<?php
/*
Plugin Name: FrE Credit
Plugin URI: http://enginethemes.com/
Description: Integrates the credit system with your FreelanceEngine site
Version: 1.0
Author: enginethemes
Author URI: http://enginethemes.com/
License: GPLv2
Text Domain: enginetheme
*/
/**
 * init email template when active plugin
 * @param void
 * @return void
 * @since 1.0
 * @package FREELANCEENGINE
 * @category FRE CREDIT
 * @author Jack Bui
 */
function fre_credit_activate(){

}
register_activation_hook( __FILE__, 'fre_credit_activate' );
/**
* Run this plugin after setup theme
* @param void
* @return void
* @since 1.0
* @package AE_ESCROW
* @category FRE CREDIT
* @author Jack Bui
*/
function fre_credit_require_plugin_file()
{
    if(!class_exists('AE_Base') ){
        return ;
    }
    add_action( 'wp_enqueue_scripts', 'fre_credit_enqueue_scripts' );
    require_once dirname(__FILE__) . '/class-credit-withdraw.php';
    require_once dirname(__FILE__) . '/container-withdraws.php';
    require_once dirname(__FILE__) . '/settings.php';
    $fre_withdraw = FRE_Credit_Withdraw::getInstance();
    $fre_withdraw->init();
    if( !ae_get_option('user_credit_system', false) ){
        return;
    }
    require_once dirname(__FILE__) . '/template.php';
    require_once dirname(__FILE__) . '/functions.php';
    require_once dirname(__FILE__) . '/class-credit-plans.php';
    require_once dirname(__FILE__) . '/class-credit-history.php';
    require_once dirname(__FILE__) . '/class-credit-currency-exchange.php';
    require_once dirname(__FILE__) . '/class-credit-currency.php';
    require_once dirname(__FILE__) . '/class-credit-wallet.php';
    require_once dirname(__FILE__) . '/class-credit-users.php';
    require_once dirname(__FILE__) . '/class-credit-employer.php';
    require_once dirname(__FILE__) . '/class-credit-escrow.php';
    require_once dirname(__FILE__) . '/update.php';
    if( !defined ( 'ET_DOMAIN' ) ){
        define( 'ET_DOMAIN', 'enginetheme' );
    }
    $fre_credit_employer = FRE_Credit_Employer::getInstance();
    $fre_credit_employer->init();
    FRE_Credit_Plan_Posttype()->init();
    FRE_Credit_History()->init();
    FRE_Credit_Users()->init();
    FRE_Credit_Escrow()->init();
}
add_action('after_setup_theme', 'fre_credit_require_plugin_file');
/**
* Enqueue script for FRE CREDIT
* @param void
* @return void
* @since 1.0
* @package FREELANCEENGINE
* @category FRE CREDIT
* @author Jack Bui
*/
function fre_credit_enqueue_scripts(){
    $page = ae_get_option('fre_credit_deposit_page_slug', false);
    if( $page && is_page($page) ){
        do_action('ae_payment_script');
    }
    wp_enqueue_style('fre_credit_css', plugin_dir_url(__FILE__) . 'assets/fre_credit_plugincss.css', array(), '1.0');
    wp_enqueue_script('fre_credit_js', plugin_dir_url(__FILE__) . 'assets/fre_credit_pluginjs.js', array(
        'underscore',
        'backbone',
        'appengine',
        'front'
    ), '1.0', true);
    wp_localize_script('fre_credit_js', 'fre_credit_globals', array(
        'currency' => ae_get_option('currency')
    ));
}
/**
  * enqueue script for admin page
  *
  * @param void
  * @return void
  * @since 1.0
  * @package FREELANCEENGINE
  * @category FRE CREDIT
  * @author Jack Bui
  */
function fre_credit_admin_enqueue_script($hook) {
//    if ( 'edit.php' != $hook ) {
//        return;
//    }
    if( is_super_admin() ){
        wp_enqueue_script('fre_credit_admin_js', plugin_dir_url(__FILE__) . 'assets/fre_credit_admin_pluginjs.js', array(
            'underscore',
            'backbone',
            'appengine'
        ), '1.0', true);
    }
}
add_action( 'admin_enqueue_scripts', 'fre_credit_admin_enqueue_script' );
/**
 * hook to add translate string to plugins
 *
 * @param Array $entries Array of translate entries
 * @return Array $entries
 * @since 1.0
 * @author Dakachi
 */
function fre_credit_add_translate_string ($entries) {
    $lang_path = dirname(__FILE__).'/lang/default.po';
    if(file_exists($lang_path)) {
        $pot        =   new PO();
        $pot->import_from_file($lang_path);
        return  array_merge($entries, $pot->entries);
    }
    return $entries;
}
add_filter( 'et_get_translate_string', 'fre_credit_add_translate_string' );



/**
 * filter array package of credit plans
 *
 * @param Array $request
 * @return Array $request
 * @since 1.0
 * @author ThanhTu
 */
function fre_credit_filter_plan($request){

    $request['et_number_posts'] = $request['et_price'];
    return $request;
}
add_filter( 'ae_filter_pack_fre_credit_plan', 'fre_credit_filter_plan' );