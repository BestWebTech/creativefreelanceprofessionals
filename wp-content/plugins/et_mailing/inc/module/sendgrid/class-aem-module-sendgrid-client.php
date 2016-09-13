<?php
/**
 * @project ae_mailing
 * @author  nguyenvanduocit
 * @date    01/30/2015
 */
require_once AEM_PLUGIN_PATH.'/inc/module/sendgrid/lib/vendor/autoload.php';
class AEM_Module_Sendgrid_Client extends SendGrid
{
    public function __construct ( $api_user, $api_key)
    {
        $options = array('turn_off_ssl_verification'=>true);
        parent::__construct($api_user, $api_key,$options);
    }
}