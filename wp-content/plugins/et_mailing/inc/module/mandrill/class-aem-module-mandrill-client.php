<?php
/**
 * @project ae_mailing
 * @author  nguyenvanduocit
 * @date    01/30/2015
 */
require_once AEM_PLUGIN_PATH.'/inc/module/mandrill/lib/vendor/autoload.php';
class AEM_Module_Mandrill_Client extends Mandrill
{
    public function __construct ( $apiKey)
    {
        parent::__construct($apiKey);
    }
}