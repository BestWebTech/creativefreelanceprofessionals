<?php

/**
 * @project ae_mailing
 * @author  nguyenvanduocit
 * @date    01/29/2015
 */
class AEM_Module_Mandrill extends AEM_Module_Base
{
    private $client;

    function __construct ()
    {
        $this->slug = 'mandrill';
    }

    /**
     * Script to send "send test email" request
     */
    function enqueue_scripts ()
    {
        wp_enqueue_style( 'mandrill_backend_style', plugins_url( "/inc/module/mandrill/css/style.css", AEM_PLUGIN_FILE ) );
    }

    /**
     * This function using for override pluggable wp_mail
     *
     * @param string|array $to Array or comma-separated list of email addresses to send message.
     * @param              $subject
     * @param              $message
     * @param string       $headers
     * @param array        $attachments
     *
     * @param bool         $isClickTracking
     * @param bool         $isOpenTracking
     * @param bool         $isDomainTracking
     * @param array        $tags
     * @param array        $cc
     * @param array        $bcc
     * @param datetime     $deliverytime
     * @param bool         $isImportant
     * @param string       $signing_domain
     * @param string       $subaccount
     *
     * @return AEM_Module_mandrill_Client|bool|WP_Error
     */
    function wp_mail (
        $to,
        $subject,
        $message,
        $headers = NULL,
        $attachments = array (),
        $isClickTracking = NULL,
        $isOpenTracking = NULL,
        $isDomainTracking = NULL,
        $tags = NULL,
        $cc = NULL,
        $bcc = NULL,
        $deliverytime = NULL,
        $isImportant = FALSE,
        $signing_domain = 'via',
        $subaccount = NULL )
    {
        try {
            $client = $this->get_client();
            if ( !is_wp_error( $client ) ) {
                # Make the call to the client.
                $postData = $this->generate_postdata(
                    array (
                        'to'               => $to,
                        'subject'          => $subject,
                        'message'          => $message,
                        'headers'          => $headers,
                        'attachments'      => $attachments,
                        'isClickTracking'  => $isClickTracking,
                        'isOpenTracking'   => $isOpenTracking,
                        'isDomainTracking' => $isDomainTracking,
                        'isImportant'      => $isImportant,
                        'signing_domain'   => $signing_domain,
                        'deliverytime'     => $deliverytime,
                        'subaccount'       => $subaccount,
                        'cc'               => $cc,
                        'bcc'              => $bcc,
                        'tags'             => $tags,
                    )
                );
                /**
                 * Send email
                 */

                $result = $client->messages->send( $postData, NULL, NULL, $deliverytime );
                $result = $result[0];
                if ( in_array( $result['status'], array ( 'rejected', 'invalid' ) ) ) {
                    return new WP_Error( "aem_mandrill_error", $result['reject_reason'] );
                } else {
                    return TRUE;
                }
            }

            return $client;
        } catch ( Exception $ex ) {
            //throw $ex;
            return new WP_Error( "mandrill_send_error", $ex->getMessage() );
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
            'subject'     => $args['subject'],
            'text'        => $args['message'],
            'isImportant' => $args['isImportant'],
            'auto_text'   => TRUE,
            'auto_html'   => TRUE,
        );

        //From email
        if ( !$is_force && isset( $header['from_name'] ) ) {
            $emailData["from_email"] = $header['from_email'];
        } else {
            $emailData["from_email"] = AEM_Option()->get_from_email();
        }
        $emailData["from_email"] = apply_filters( 'wp_mail_from', $emailData["from_email"] );

        //From name
        if ( !$is_force && isset( $header['from_name'] ) ) {
            $emailData["from_name"] = $header['from_name'];
        } else {
            $emailData["from_name"] = AEM_Option()->get_from_name();
        }
        $emailData["from_name"] = apply_filters( 'wp_mail_from_name', $emailData["from_name"] );

        //to
        $emailData['to'] = array ();

        if ( !is_array( $args['to'] ) ) {
            $args['to'] = explode( ',', $args['to'] );
        }

        foreach ( (array) $args['to'] as $recipient ) {
            try {
                // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
                $recipient_name = '';
                if ( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) ) {
                    if ( count( $matches ) == 3 ) {
                        $recipient_name = $matches[1];
                        $recipient = $matches[2];
                    }
                }
                $emailData['to'][] = array (
                    'email' => $recipient,
                    'name'  => $recipient_name,
                    'type'  => 'to'
                );
            } catch ( phpmailerException $e ) {
                continue;
            }

        }

        //cc
        if ( !empty( $header['cc'] ) ) {
            //header cc not empty
            if ( empty( $args['cc'] ) ) {
                //arg cc empty, override agr cc
                $args['cc'] = $header['cc'];
            } else {
                //arg cc not empty
                if ( !is_array( $args['cc'] ) ) {
                    $args['cc'] = explode( ',', $args['cc'] );
                }
                //merge them
                $args['cc'] = array_merge( $header['cc'] );
            }

        }
        if ( !empty( $args['cc'] ) ) {

            foreach ( (array) $args['cc'] as $recipient ) {
                try {
                    // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
                    $recipient_name = '';
                    if ( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) ) {
                        if ( count( $matches ) == 3 ) {
                            $recipient_name = $matches[1];
                            $recipient = $matches[2];
                        }
                    }
                    $emailData['to'][] = array (
                        'email' => $recipient,
                        'name'  => $recipient_name,
                        'type'  => 'cc'
                    );
                } catch ( phpmailerException $e ) {
                    continue;
                }
            }

        }
        //bcc
        if ( !empty( $header['bcc'] ) ) {
            //header bcc not empty
            if ( empty( $args['bcc'] ) ) {
                //arg bcc empty, override agr cc
                $args['bcc'] = $header['bcc'];
            } else {
                //arg bcc not empty
                if ( !is_array( $args['bcc'] ) ) {
                    $args['bcc'] = explode( ',', $args['bcc'] );
                }
                //merge them
                $args['bcc'] = array_merge( $header['bcc'] );
            }

        }
        if ( !empty( $args['bcc'] ) ) {
            foreach ( (array) $args['bcc'] as $recipient ) {
                try {
                    // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
                    $recipient_name = '';
                    if ( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) ) {
                        if ( count( $matches ) == 3 ) {
                            $recipient_name = $matches[1];
                            $recipient = $matches[2];
                        }
                    }
                    $emailData['to'][] = array (
                        'email' => $recipient,
                        'name'  => $recipient_name,
                        'type'  => 'bcc'
                    );
                } catch ( Exception $e ) {
                    continue;
                }
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
            $extras['track_clicks'] = $args['isTracking'];
        } else {
            $extras['track_clicks'] = AEM_Option()->is_click_tracking();
        }
        //open tracking
        if ( isset( $args['isOpenTracking'] ) ) {
            $extras['track_opens'] = $args['isOpenTracking'];
        } else {
            $extras['track_opens'] = AEM_Option()->is_open_tracking();
        }
        //Domain tracking
        if ( isset( $args['isDomainTracking'] ) ) {
            $extras['track_domain'] = $args['isDomainTracking'];
        } else {
            $extras['track_domain'] = aem_get_option( 'mandrill_domain_tracking' );
        }
        //signing domain
        if ( isset( $args['signing_domain'] ) ) {
            $extras['signing_domain'] = $args['signing_domain'];
        } else {
            $extras['signing_domain'] = aem_get_option( 'mandrill_signing_domain' );
        }
        //tagging
        $extras['tags'] = explode( ",", aem_get_option( "mandrill_tag", "" ) );
        if ( isset( $args['tags'] ) ) {
            if ( !is_array( $args['tags'] ) ) {
                $args['tags'] = explode( ",", $args['tags'] );
            }
            $extras['tags'] = array_merge( $extras['tags'], (array) $args['tags'] );
        }
        //campaign id
        if ( isset( $args['subaccount'] ) ) {
            $extras['subaccount'] = $args['subaccount'];
        } else {
            $extras['subaccount'] = aem_get_option( "mandrill_subaccount", NULL );
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
                        $extras['headers'][$tmp[0]] = $tmp[1];
                    }
                }
            }
        }

        $fileData = array ();
        //$args['attachments'] = array(plugin_dir_path( AEM_PLUGIN_FILE )."/inc/ae/assets/img/slider.png");
        if ( !empty( $args['attachments'] ) ) {
            foreach ( $args['attachments'] as $attachment ) {
                $file_content = file_get_contents( $attachment );

                $fileData[] = array (
                    //'type' => 'text/plain',
                    'name'    => basename( $attachment ),
                    'content' => base64_encode( $file_content )
                );
            }
            $emailData['attachments'] = $fileData;
        }
        //combine
        $emailData = array_merge( $emailData, $extras );

        $postData = apply_filters( "aem_postdata", $emailData );

        return $postData;
    }

    /**
     * Get mandrill client
     *
     * @return AEM_Module_mandrill_Client
     */
    function get_client ()
    {
        if ( is_null( $this->client ) ) {
            $apiKey = aem_get_option( 'mandrill_apiKey' );
            //check apikey
            if ( FALSE == $apiKey ) {
                return new WP_Error( "mandrill_api_missing", "Please enter mandrill API to using mandrill service." );
            }
            $this->client = new AEM_Module_Mandrill_Client( $apiKey );
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
                'title' => __( "Mandrill", AEM_DOMAIN ),
                'id'    => 'mandrill-settings',
                'icon'  => "-",
                'class' => 'mandrill'
            ),
            'groups' => array (
                array (
                    'args'   => array (
                        'title' => __( "Mandrill API", AEM_DOMAIN ),
                        'id'    => 'mandrill-api',
                        'class' => '',
                        'desc'  => __( "Enable this api to send email with mandrill service.", AEM_DOMAIN )
                    ),

                    'fields' => array (
                        array (
                            'id'          => 'mandrill_apiKey',
                            'type'        => 'text',
                            'required'    => TRUE,
                            'title'       => __( "API Key ", AEM_DOMAIN ),
                            'name'        => 'mandrill_apiKey',
                            'placeholder' => __( "mandrill api key", AEM_DOMAIN ),
                            'class'       => ''
                        ),
                    ),
                ),
                array (
                    'args'   => array (
                        'title' => __( "Domain tracking", AEM_DOMAIN ),
                        'id'    => 'mandrill-domaintracking',
                        'class' => '',
                        'desc'  => __( "A custom domain to use for tracking opens and clicks instead of mandrillapp.com.", AEM_DOMAIN )
                    ),

                    'fields' => array (
                        array (
                            'id'    => 'domain_tarcking',
                            'type'  => 'switch',
                            'title' => __( "Domain tracking ", AEM_DOMAIN ),
                            'name'  => 'mandrill_domain_tracking',
                            'class' => ''
                        ),
                    ),
                ),
                array (
                    'args'   => array (
                        'title' => __( "Signing domain", AEM_DOMAIN ),
                        'id'    => 'mandrill-signing_domain',
                        'class' => '',
                        'desc'  => __( "A custom domain to use for SPF/DKIM signing instead of mandrill (for \"via\" or \"on behalf of\" in email clients)", AEM_DOMAIN )
                    ),

                    'fields' => array (
                        array (
                            'id'    => 'signing_domain',
                            'type'  => 'text',
                            'title' => __( "Signing domain ", AEM_DOMAIN ),
                            'name'  => 'mandrill_signing_domain',
                            'class' => ''
                        ),
                    ),
                ),
                array (
                    'args'   => array (
                        'title' => __( "Subaccount id", AEM_DOMAIN ),
                        'id'    => 'mandrill-subaccount',
                        'class' => '',
                        'desc'  => __( "The unique id of a subaccount for this message.", AEM_DOMAIN )
                    ),

                    'fields' => array (
                        array (
                            'id'    => 'subaccount',
                            'type'  => 'text',
                            'title' => __( "Subaccount ID ", AEM_DOMAIN ),
                            'name'  => 'mandrill_subaccount',
                            'class' => ''
                        ),
                    ),
                ),
                array (
                    'args'   => array (
                        'title' => __( "Tag", AEM_DOMAIN ),
                        'id'    => 'mandrill-tag',
                        'class' => '',
                        'desc'  => __( "If added, this tag will exist on every outbound message. Statistics will be populated in the mandrill Control Panel. Use a comma to define multiple tags..", AEM_DOMAIN )
                    ),

                    'fields' => array (
                        array (
                            'id'    => 'tag',
                            'type'  => 'text',
                            'title' => __( "Tag ", AEM_DOMAIN ),
                            'name'  => 'mandrill_tag',
                            'class' => ''
                        ),
                    ),
                )
            )
        );

        return $sections;
    }
}