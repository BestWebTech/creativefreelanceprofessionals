<?php

/**
 * @project ae_mailing
 * @author  nguyenvanduocit
 * @date    01/29/2015
 */
class AEM_Module_Mailgun extends AEM_Module_Base
{
    private $client;

    function __construct ()
    {
        $this->slug = 'mailgun';
    }


    /**
     * This function using for override pluggable wp_mail
     *
     * @param        $to
     * @param        $subject
     * @param        $message
     * @param string $headers
     * @param array  $attachments
     *
     * @throws Exception
     */
    function wp_mail (
        $to,
        $subject,
        $message,
        $headers = NULL,
        $attachments = array (),
        $isClickTracking = NULL,
        $isOpenTracking = NULL,
        $tags = NULL,
        $custom_var = NULL,
        $cc = NULL,
        $bcc = NULL,
        $deliverytime = NULL )
    {
        try {
            $mgClient = $this->get_client();
            if ( !is_wp_error( $mgClient ) ) {
                # Make the call to the client.
                $postData = $this->generate_postdata(
                    array (
                        'to'              => $to,
                        'subject'         => $subject,
                        'message'         => $message,
                        'headers'         => $headers,
                        'attachments'     => $attachments,
                        'isClickTracking' => $isClickTracking,
                        'isOpenTracking'  => $isOpenTracking,
                        'deliverytime'    => $deliverytime,
                        'tags'            => $tags,
                        'cc'              => $cc,
                        'bcc'             => $bcc,
                        'custom_var'      => $custom_var
                    )
                );
                /**
                 * Send email
                 */
                $result = $mgClient->enqueueMessage( $postData['emailData'], $postData['fileData'] );

                return $result;
            }

            return $mgClient;
        } catch ( Exception $ex ) {
            //throw $ex;
            return FALSE;
        }
    }

    /**
     * Repair data for send message
     *
     * @param array $args
     *
     * @return array
     */
    function generate_postdata ( $args = array () )
    {
        $is_force = aem_get_option( 'aem_force_header', FALSE );
        $header = $this->process_header( $args['headers'] );
        /**
         * generate postFile
         */
        $emailData = array (
            'to'      => $args['to'],
            'subject' => $args['subject'],
            'text'    => $args['message'],
        );
        //From email
        if ( !$is_force && isset( $header['from_name'] ) ) {
            $from_email = $header['from_email'];
        } else {
            $from_email = AEM_Option()->get_from_email();
        }
        $from_email = apply_filters( 'wp_mail_from', $from_email );

        //From name
        if ( !$is_force && isset( $header['from_name'] ) ) {
            $from_name = $header['from_name'];
        } else {
            $from_name = AEM_Option()->get_from_name();
        }
        $from_name = apply_filters( 'wp_mail_from_name', $from_name );

        $emailData['from'] = sprintf( '%1$s<%2$s>', $from_name, $from_email );

        //cc
        if ( !empty( $header['cc'] ) ) {
            $header['cc'] = implode( ",", $header['cc'] );
            //header cc not empty
            if ( empty( $args['cc'] ) ) {
                //arg cc empty, override agr cc
                $emailData['cc'] = $header['cc'];
            } else {
                //merge them
                $emailData['cc'] = trim( $args['cc'], ',' ).",".trim( $header['cc'], ',' );
            }

        }

        //bcc
        if ( !empty( $header['bcc'] ) ) {
            $header['bcc'] = implode( ",", $header['bcc'] );
            //header bcc not empty
            if ( empty( $args['bcc'] ) ) {
                //arg bcc empty, override agr bcc
                $emailData['bcc'] = $header['bcc'];
            } else {
                //merge them
                $emailData['bcc'] = trim( $args['bcc'], ',' ).",".trim( $header['bcc'], ',' );
            }

        }


        if ( !isset( $header['content_type'] ) ) {
            $content_type = 'text/plain';
        } else {
            $content_type = $header['content_type'];
        }

        $content_type = apply_filters( 'wp_mail_content_type', $content_type );

        if ( $content_type == "text/html" ) {
            $emailData['html'] = $args['message'];
        }

        /**
         * Extras data
         */
        $extras = array ();
        //Click tracking
        if ( isset( $args['isClickTracking'] ) ) {
            $extras['o:tracking-clicks'] = $args['isTracking'];
        } else {
            $extras['o:tracking-clicks'] = AEM_Option()->is_click_tracking();
        }
        //open tracking
        if ( isset( $args['isOpenTracking'] ) ) {
            $extras['o:tracking-opens'] = $args['isOpenTracking'];
        } else {
            $extras['o:tracking-opens'] = AEM_Option()->is_open_tracking();
        }
        //General tracking
        $extras['o:tracking'] = TRUE;
        //schedule time
        if ( isset( $args['deliverytime'] ) ) {
            $extras['o:deliverytime'] = $args['deliverytime'];
        }
        //tagging
        if ( isset( $args['tags'] ) ) {
            if ( !is_array( $args['tags'] ) ) {
                $args['tags'] = (array) $args['tags'];
            }
            $extras['o:tag'] = $args['tags'];
        } else {
            $extras['o:tag'] = explode( ",", aem_get_option( "mailgun_tag", NULL ) );
        }
        //campaign id
        if ( isset( $args['campaignId'] ) ) {
            $extras['o:campaign'] = $args['campaignId'];
        } else {
            $extras['o:campaign'] = aem_get_option( "mailgun_campaignid", NULL );
        }

        //custom header
        if ( isset( $args['headers'] ) ) {
            //extract header line
            $header_arr = explode( "\r\n", $args['headers'] );
            foreach ( $header_arr as $header ) {
                //extract header key:value
                $tmp = explode( ':', $header );
                if ( count( $tmp ) > 1 ) {
                    if ( $tmp[1] != NULL ) {
                        if ( strtolower( $tmp[0] ) == 'from' ) {
                            if ( aem_get_option( 'aem_force_header' ) ) {
                                continue;
                            }
                        }
                        $extras['h:'.$tmp[0]] = $tmp[1];
                    }
                }
            }
        }
        //combine
        $emailData = array_merge( $emailData, $extras );
        /**
         * Generate post file
         */
        $fileData = array ();
        if ( !empty( $args['attachments'] ) ) {
            $fileData = array (
                'attachment' => $args['attachments']
            );
        }
        /**
         * combine all
         */
        $postData = array (
            'emailData' => $emailData,
            'fileData'  => $fileData
        );

        $postData = apply_filters( "aem_postdata", $postData );

        return $postData;
    }

    /**
     * Get mailgun client
     *
     * @return AEM_Module_Mailgun_Client
     */
    function get_client ()
    {
        if ( is_null( $this->client ) ) {
            $apiKey = aem_get_option( 'mailgun_apiKey' );
            $apiDomain = aem_get_option( 'mailgun_domain' );
            //check apikey
            if ( FALSE == $apiKey ) {
                return new WP_Error( "mailgun_api_missing", "Please enter Mailgun API to using Mailgun service." );
            }
            //check
            if ( FALSE == $apiDomain ) {
                return new WP_Error( "mailgun_domain_missing", "Please enter Mailgun domain to using Mailgun service." );
            }
            $this->client = new AEM_Module_Mailgun_Client( $apiKey, $apiDomain );
        }

        return $this->client;
    }

    /**
     * Get data togenerate setting in admin dashboard
     *
     * @return array
     */
    function get_setting_section ()
    {
        $sections = array (
            'args'   => array (
                'title' => __( "Mailgun", AEM_DOMAIN ),
                'id'    => 'mailgun-settings',
                'icon'  => "-",
                'class' => ''
            ),
            'groups' => array (
                array (
                    'args'   => array (
                        'title' => __( "Mailgun API", AEM_DOMAIN ),
                        'id'    => 'mailgun-api',
                        'class' => '',
                        'desc'  => __( "Enable this api to send email with MailGun service.", AEM_DOMAIN )
                    ),

                    'fields' => array (
                        array (
                            'id'          => 'mailgin_domain',
                            'type'        => 'text',
                            'required'    => TRUE,
                            'title'       => __( "Domain ", AEM_DOMAIN ),
                            'label'       => __( "Domain ", AEM_DOMAIN ),
                            'name'        => 'mailgun_domain',
                            'placeholder' => __( "Mailgun domain", AEM_DOMAIN ),
                            'class'       => ''
                        ),
                        array (
                            'id'          => 'mailgin_apiKey',
                            'type'        => 'text',
                            'required'    => TRUE,
                            'title'       => __( "API Key ", AEM_DOMAIN ),
                            'label'       => __( "API Key ", AEM_DOMAIN ),
                            'name'        => 'mailgun_apiKey',
                            'placeholder' => __( "Mailgun api key", AEM_DOMAIN ),
                            'class'       => ''
                        )
                    ),
                ),
                array (
                    'args'   => array (
                        'title' => __( "Campaign ID", AEM_DOMAIN ),
                        'id'    => 'mailgun-campaignid',
                        'class' => '',
                        'desc'  => __( "If added, this campaign will exist on every outbound message. Statistics will be populated in the Mailgun Control Panel. Use a comma to define multiple campaigns.", AEM_DOMAIN )
                    ),

                    'fields' => array (
                        array (
                            'id'    => 'campaign_id',
                            'type'  => 'text',
                            'title' => __( "Campaign id ", AEM_DOMAIN ),
                            'name'  => 'mailgun_campaignid',
                            'class' => ''
                        ),
                    ),
                ),
                array (
                    'args'   => array (
                        'title' => __( "Tag", AEM_DOMAIN ),
                        'id'    => 'mailgun-tag',
                        'class' => '',
                        'desc'  => __( "If added, this tag will exist on every outbound message. Statistics will be populated in the Mailgun Control Panel. Use a comma to define multiple tags..", AEM_DOMAIN )
                    ),

                    'fields' => array (
                        array (
                            'id'    => 'tag',
                            'type'  => 'text',
                            'title' => __( "Tag ", AEM_DOMAIN ),
                            'name'  => 'mailgun_tag',
                            'class' => ''
                        ),
                    ),
                )
            )
        );

        return $sections;
    }
}