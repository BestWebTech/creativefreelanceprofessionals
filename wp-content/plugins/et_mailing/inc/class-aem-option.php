<?php

/**
 * @project ae_mailing
 * @author  nguyenvanduocit
 * @date    02/02/2015
 */
if(!class_exists("AE_Options")){
    require_once AEM_PLUGIN_PATH."/inc/ae/class-options.php";
}
if(!class_exists('AEM_Option')) {
    class AEM_Option extends AE_Options
    {
        private static $_instance;

        public static function instance ()
        {
            if ( self::$_instance == NULL ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        public function __construct ()
        {
            parent::__construct( "et_options" );
        }

        public function get_from_email ()
        {
            $from_email = aem_get_option( 'aem_from_email', NULL );
            if ( NULL === $from_email ) {
                $from_email = get_option( 'admin_email' );
            }

            return apply_filters( "aem_from_email", $from_email );
        }

        public function get_from_name ()
        {
            $from_email = aem_get_option( 'aem_from_name', NULL );
            if ( NULL === $from_email ) {
                $from_email = get_option( 'blogname' );
            }

            return apply_filters( "aem_from_name", $from_email );
        }

        public function is_click_tracking ()
        {
            $is_click_tracking = aem_get_option( 'aem_click_tracking', FALSE );

            return apply_filters( "aem_click_tracking", $is_click_tracking );
        }

        public function is_open_tracking ()
        {
            $is_click_tracking = aem_get_option( 'aem_open_tracking', FALSE );

            return apply_filters( "aem_open_tracking", $is_click_tracking );
        }
    }
}