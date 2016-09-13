<?php

/**
 * @project ae_mailing
 * @author  nguyenvanduocit
 * @date    01/29/2015
 */
abstract class AEM_Module_Base
{
    protected $slug = NULL;

    abstract function wp_mail ( $to, $subject, $message, $headers = '', $attachments = array () );

    abstract function get_setting_section ();

    function init_hook ()
    {
    }

    function enqueue_scripts ()
    {
    }

    function check_option ()
    {
        $sessions = $this->get_setting_section();
        $groups = $sessions['groups'];
        foreach ( $groups as $group ) {
            foreach ( $group["fields"] as $field ) {
                if( isset($field['required']) && $field['required'] == true) {
                    $field_value = aem_get_option( $field['name'], FALSE );
                    if ( $field_value == FALSE ) {
                        return FALSE;
                    }
                }
            }
        }

        return TRUE;
    }

    protected function process_header ( $headers )
    {
        if ( empty( $headers ) ) {
            $header_data = array ();
        } else {
            if ( !is_array( $headers ) ) {
                // Explode the headers out, so this function can take both
                // string headers and an array of headers.
                $tempheaders = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
            } else {
                $tempheaders = $headers;
            }
            $header_data['cc'] = array ();
            $header_data['bcc'] = array ();
            // If it's actually got contents
            if ( !empty( $tempheaders ) ) {
                // Iterate through the raw headers
                foreach ( (array) $tempheaders as $header ) {
                    if ( strpos( $header, ':' ) === FALSE ) {
                        if ( FALSE !== stripos( $header, 'boundary=' ) ) {
                            $parts = preg_split( '/boundary=/i', trim( $header ) );
                            $boundary = trim( str_replace( array ( "'", '"' ), '', $parts[1] ) );
                        }
                        continue;
                    }
                    // Explode them out
                    list( $name, $content ) = explode( ':', trim( $header ), 2 );
                    // Cleanup crew
                    $name = trim( $name );
                    $content = trim( $content );
                    switch ( strtolower( $name ) ) {
                        // Mainly for legacy -- process a From: header if it's there
                        case 'from':
                            if ( strpos( $content, '<' ) !== FALSE ) {
                                // So... making my life hard again?
                                $from_name = substr( $content, 0, strpos( $content, '<' ) - 1 );
                                $from_name = str_replace( '"', '', $from_name );
                                $header_data['from_name'] = trim( $from_name );
                                $from_email = substr( $content, strpos( $content, '<' ) + 1 );
                                $from_email = str_replace( '>', '', $from_email );
                                $header_data['from_email'] = trim( $from_email );
                            } else {
                                $header_data['from_email'] = trim( $content );
                            }
                            break;
                        case 'content-type':
                            if ( strpos( $content, ';' ) !== FALSE ) {
                                list( $type, $charset ) = explode( ';', $content );
                                $header_data['content_type'] = trim( $type );
                                if ( FALSE !== stripos( $charset, 'charset=' ) ) {
                                    $header_data['charset'] = trim( str_replace( array ( 'charset=', '"' ), '', $charset ) );
                                } elseif ( FALSE !== stripos( $charset, 'boundary=' ) ) {
                                    $header_data['boundary'] = trim( str_replace( array ( 'BOUNDARY=', 'boundary=', '"' ), '', $charset ) );
                                    $header_data['charset'] = '';
                                }
                            } else {
                                $header_data['content_type'] = trim( $content );
                            }
                            break;
                        case 'cc':
                            $header_data['cc'] = array_merge( (array) $header_data['cc'], explode( ',', $content ) );
                            break;
                        case 'bcc':
                            $header_data['bcc'] = array_merge( (array) $header_data['bcc'], explode( ',', $content ) );
                            break;
                        default:
                            // Add it to our grand headers array
                            $header_data[trim( $name )] = trim( $content );
                            break;
                    }
                }
            }
        }
        return $header_data;
    }
}