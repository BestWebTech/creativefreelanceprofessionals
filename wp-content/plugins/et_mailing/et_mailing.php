<?php

/*
Plugin Name: ET Mailing
Plugin URI: https://www.enginethemes.com/extensions/
Description: This plugin allows you to use Third-party service to send email, avoid spam filters.
Version: 1.0.0
Author: EngineTheme
Developer : Duoc Nguyen Van
Author URI: http://enginethemes.com
*/
define( "AEM_PLUGIN_FILE", __FILE__ );
define( "AEM_PLUGIN_PATH", dirname( AEM_PLUGIN_FILE ) );
define( 'AEM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'AEM_VERSION', "1.0.0" );
define( 'AEM_DOMAIN', "et_mailing" );

require_once AEM_PLUGIN_PATH.'/update.php';
require_once AEM_PLUGIN_PATH.'/inc/class-autoload.php';
require_once AEM_PLUGIN_PATH.'/inc/aem-functions.php';
require_once AEM_PLUGIN_PATH.'/inc/class-aem-util.php';

if ( !is_wp_error( AEM_Util::check_require() ) ) {

    require_once AEM_PLUGIN_PATH.'/inc/aem-override-function.php';
    /**
     * Initiate main object
     */
    AEM()->init();
    /*
     * Check if in administrator area
     */
    if ( is_admin()) {
        /*
         * Do action after theme setup to check if ET Theme is activated
         */
        add_action( "after_setup_theme", "load_admin_page" );
    }
}

function load_admin_page ()
{
    //Advoid redilace theme core
    /**
     * Do action in administration area
     */
    AEM_Admin();
}