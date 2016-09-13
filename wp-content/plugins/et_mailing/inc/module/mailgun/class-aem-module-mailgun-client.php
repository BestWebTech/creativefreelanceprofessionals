<?php
/**
 * @project ae_mailing
 * @author  nguyenvanduocit
 * @date    01/30/2015
 */
require_once AEM_PLUGIN_PATH.'/inc/module/mailgun/lib/vendor/autoload.php';
use Mailgun\Mailgun;

class AEM_Module_Mailgun_Client extends Mailgun
{
    public function __construct ( $apiKey = NULL, $domain = "")
    {
        parent::__construct($apiKey);
        $this->workingDomain = $domain;
    }

    public function enqueueMessage( $postData, $postFiles = array () )
    {
        return parent::sendMessage( $this->workingDomain, $postData, $postFiles );
    }
}