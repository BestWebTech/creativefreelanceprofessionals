<?php

/**
 * ======================================================================
 * LICENSE: This file is subject to the terms and conditions defined in *
 * file 'license.txt', which is part of this source code package.       *
 * ======================================================================
 */

/**
 * Backend contact/hire manager
 * 
 * @package AAM
 * @author Vasyl Martyniuk <vasyl@vasyltech.com>
 */
class AAM_Backend_Feature_Contact extends AAM_Backend_Feature_Abstract {
    
    /**
     * @inheritdoc
     */
    public static function getAccessOption() {
        return 'feature.contact.capability';
    }
    
    /**
     * @inheritdoc
     */
    public static function getTemplate() {
        return 'contact.phtml';
    }
    
    /**
     * Update the extension
     * 
     * @return string
     * 
     * @access public
     */
    public function update() {
        $extension = AAM_Core_Request::post('extension');
        
        $list = AAM_Core_API::getOption('aam-extension-license', array());
        if (isset($list[$extension])) {
            $response = $this->install($list[$extension]);
        } else {
            $response = json_encode(array(
                'status' => 'failure', 
                'error'  => __('License key is missing.', AAM_KEY)
            ));
        }
        
        return $response;
    }
    
    /**
     * Register Contact/Hire feature
     * 
     * @return void
     * 
     * @access public
     */
    public static function register() {
        $cap = AAM_Core_Config::get(self::getAccessOption(), 'administrator');
        
        AAM_Backend_Feature::registerFeature((object) array(
            'uid'        => 'contact',
            'position'   => 9999,
            'title'      => __('Contact Us', AAM_KEY),
            'capability' => $cap,
            'subjects'   => array(
                'AAM_Core_Subject_Role', 
                'AAM_Core_Subject_User', 
                'AAM_Core_Subject_Visitor'
            ),
            'view'       => __CLASS__
        ));
    }

}