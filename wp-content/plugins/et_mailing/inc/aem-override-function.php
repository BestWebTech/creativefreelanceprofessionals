<?php
/**
 * @project et_mailing
 * @author  nguyenvanduocit
 * @date    02/03/2015
 */

/**
 * Contains all override function.
 */

/**
 * @param        $to
 * @param        $subject
 * @param        $message
 * @param string $headers
 * @param array  $attachments
 *
 * @return bool
 */
if ( !function_exists( 'wp_mail' ) ) {
    function wp_mail ( $to, $subject, $message, $headers = '', $attachments = array () )
    {
        $atts = apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) );
        $send_result = AEM()->module_factory()->get_current_module()->wp_mail( $atts['to'], $atts['subject'], $atts['message'], $atts['headers'], $atts['attachments'] );

        if ( !$send_result ) {
            $send_result = @mail( $to, $subject, $message, $headers );
        }

        return $send_result;
    }
}