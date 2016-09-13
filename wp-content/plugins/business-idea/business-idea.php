<?php
/*
Plugin Name: Business Idea
Description: Business Idea Plugin
Author: Govind Kumawat (Vertax Web Technology)	
Author http://vertaxwebtechnology.com/
Version: 1.0.0


*/


include(dirname(__FILE__).'/business-idea-install.php' );

function business_admin_menu() {
	global $wpdb;
		
	if ( function_exists('add_object_page') ) {
		add_object_page(__('Business Start Ups','business-idea'), __('Business Start Ups','business-idea'), 'manage_options', 'business-startup-welcome', 'business_idea_admin_view_welcome_page','');
	} else {
		add_menu_page(__('Business Ideas','business-idea'), __('Business Ideas','business-idea'), 'manage_options', 'business-idea-welcome', 'business_idea_admin_view_welcome_page','' );
	}
	@add_submenu_page('view-start-ups', __('View Start Ups','business-idea'), false, 'manage_options', 'business-startup-view', 'business_idea_render_view_page');	

}


function business_idea_admin_view_welcome_page()
{
	include(dirname(__FILE__).'/admin/list.php' );

}



function business_idea_render_view_page() {
	
	include(dirname(__FILE__).'/admin/view.php' );
}


function wp_business_idea_form_shortcode()
{
	include(dirname(__FILE__).'/front/form.php' );
}


/**
 * Register style sheet.
 */
function register_plugin_styles() {
	wp_register_style( 'business-idea',  plugins_url('assets/css/style.css', __FILE__) , '', false, 'all' );
	wp_enqueue_style( 'business-idea' );
}
/**
 * Proper way to enqueue scripts 
 */
function register_plugin_scripts() {
	//wp_enqueue_style( 'style-name', get_stylesheet_uri() );
	//wp_enqueue_script( 'jquery-idea',plugins_url('assets/js/jquery.js', __FILE__), array(), '1.11.1', true );
	//wp_enqueue_script( 'jquery.validate',plugins_url('assets/js/jquery.validate.js', __FILE__), array(), '1.0.0', true );
	wp_enqueue_script( 'validate',plugins_url('assets/js/validate.js', __FILE__), array(), '1.0.0', true );
}
// Register style sheet.
add_action( 'wp_enqueue_scripts', 'register_plugin_styles' );
// Register javascript.
add_action( 'wp_enqueue_scripts', 'register_plugin_scripts' );
// Register Shortcode for business idea form.
add_shortcode('wp_business_idea_form','wp_business_idea_form_shortcode');
//add_shortcode('wp_fssnet_payment_status','wp_fssnet_payment_status_shortcode');
// Register for add menu in admin
add_action('admin_menu', 'business_admin_menu');
// Register for view Business idea Page
//add_action( 'admin_menu', 'business_idea_register_admin_page' );
