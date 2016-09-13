<?php

/**
 * @project ae_mailing
 * @author  nguyenvanduocit
 * @date    01/29/2015
 */
class AEM_Module_Sendgrid extends AEM_Module_Base
{
    private $client;

    function __construct ()
    {
        $this->slug = 'sendgrid';
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
     * @return AEM_Module_sendgrid_Client|bool|WP_Error
     */
    function wp_mail (
        $to,
        $subject,
        $message,
        $headers = NULL,
        $attachments = array (),
        $cc = NULL,
        $bcc = NULL )
    {
        try {
            $client = $this->get_client();
            if ( !is_wp_error( $client ) ) {
                # Make the call to the client.
                $postData = $this->generate_postdata(
                    array (
                        'to'          => $to,
                        'subject'     => $subject,
                        'message'     => $message,
                        'headers'     => $headers,
                        'attachments' => $attachments,
                        'cc'          => $cc,
                        'bcc'         => $bcc,
                    )
                );
                /**
                 * Send email
                 */
                $result = $client->send( $postData );
                if ( $result->message == 'error' ) {
                    return new WP_Error( "aem_sendgrid_error", implode( ".", $result->errors ) );
                } else {
                    return TRUE;
                }
            }

            return $client;
        } catch ( Exception $ex ) {
            //throw $ex;
            return new WP_Error( "sendgrid_send_error", $ex->getMessage() );
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
        $emailData = new SendGrid\Email();
        $is_force = aem_get_option( 'aem_force_header', FALSE );
        $header = $this->process_header( $args['headers'] );
        /**
         * generate postFile
         */
        $emailData->setSubject( $args['subject'] );
        $emailData->setText( $args['subject'] );

        //From email
        if ( !$is_force && isset( $header['from_name'] ) ) {
            $from_email = $header['from_email'];
        } else {
            $from_email = AEM_Option()->get_from_email();
        }
        $from_email = apply_filters( 'wp_mail_from', $from_email );
        $emailData->setFrom( $from_email );

        //From name
        if ( !$is_force && isset( $header['from_name'] ) ) {
            $from_name = $header['from_name'];
        } else {
            $from_name = AEM_Option()->get_from_name();
        }
        $from_name = apply_filters( 'wp_mail_from_name', $from_name );
        $emailData->setFromName( $from_name );

        //to

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
                $emailData->addTo( $recipient, $recipient_name );
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
                    $emailData->addCc( $recipient );
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
                    $emailData->addBcc( $recipient );
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
            $emailData->setHtml( $args['message'] );
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
                        $emailData->addHeader( $tmp[0], $tmp[1] );
                    }
                }
            }
        }

        //$args['attachments'] = array ( plugin_dir_path( AEM_PLUGIN_FILE )."/inc/ae/assets/img/slider.png" );
        if ( !empty( $args['attachments'] ) ) {
            $emailData->setAttachments( $args['attachments'] );
        }

        $postData = apply_filters( "aem_postdata", $emailData );

        return $postData;
    }

    /**
     * Get sendgrid client
     *
     * @return AEM_Module_sendgrid_Client
     */
    function get_client ()
    {
        if ( is_null( $this->client ) ) {
            $username = aem_get_option( 'sendgrid_username' );
            $password = aem_get_option( 'sendgrid_password' );
            //check apikey
            if ( FALSE == $username ) {
                return new WP_Error( "sendgrid_username_missing", "Please enter sendgrid username to using sendgrid service." );
            }
            if ( FALSE == $password ) {
                return new WP_Error( "sendgrid_password_missing", "Please enter sendgrid password to using sendgrid service." );
            }
            $this->client = new AEM_Module_Sendgrid_Client( $username, $password );
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
                'title' => __( "SendGrid", AEM_DOMAIN ),
                'id'    => 'sendgrid-settings',
                'icon'  => "-",
                'class' => 'sendgrid'
            ),
            'groups' => array (
                array (
                    'args'   => array (
                        'title' => __( "Sendgrid Username", AEM_DOMAIN ),
                        'id'    => 'sendgrid-username',
                        'class' => '',
                        'desc'  => __( "Enable this api to send email with sendgrid service.", AEM_DOMAIN )
                    ),

                    'fields' => array (
                        array (
                            'id'          => 'sendgrid_username',
                            'type'        => 'text',
                            'required'    => TRUE,
                            'title'       => __( "Username ", AEM_DOMAIN ),
                            'label'       => __( "Username ", AEM_DOMAIN ),
                            'name'        => 'sendgrid_username',
                            'placeholder' => __( "SendGrid username", AEM_DOMAIN ),
                            'class'       => ''
                        ), array (
                            'id'          => 'sendgrid_password',
                            'type'        => 'text',
                            'required'    => TRUE,
                            'title'       => __( "Password ", AEM_DOMAIN ),
                            'label'       => __( "Password ", AEM_DOMAIN ),
                            'name'        => 'sendgrid_password',
                            'placeholder' => __( "SendGrid password", AEM_DOMAIN ),
                            'class'       => ''
                        ),
                    ),
                ),
            )
        );

        return $sections;
    }
}