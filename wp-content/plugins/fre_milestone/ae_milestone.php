<?php
/*
	Plugin Name: FrE Milestone
	Plugin URI: http://enginethemes.com/
	Description: FrE Milestone is a FreelanceEngine extension that helps the employer can create project’s road. Employer create milestones for each project to work with freelancer.
	Version: 1.0
	Author: EngineThemes
	Author URI: http://enginethemes.com/
	License: GPLv2
	Text Domain: enginetheme
*/

/**
 * When deactive plugin
 * @param void
 * @return void
 *
 * @since 1.0
 * @package FREELANCEENGINE
 * @category AE MILESTONE
 * @author tatthien
 */
register_deactivation_hook( __FILE__, "ae_milestone_deactivate");

function ae_milestone_deactivate() {
    //Delete options
    $option = AE_Options::get_instance();
    $option->__unset( 'max_milestone' );
}

/**
 * Include requirement files after setup theme
 * @param void
 * @return void
 *
 * @since 1.0
 * @package FREELANCEENGINE
 * @category AE MILESTONE
 * @author tatthien
 */
function require_plugin_files() {
	require_once dirname( __FILE__ ) . '/settings.php';
	require_once dirname( __FILE__ ) . '/template.php';
	require_once dirname( __FILE__ ) . '/functions.php';
	require_once dirname( __FILE__ ) . '/class-milestone-posttype.php';
	require_once dirname( __FILE__ ) . '/class-milestone-actions.php';
	require_once dirname( __FILE__ ) . '/update.php';

	if( !defined( 'ET_DOMAIN' ) ) {
		define( 'ET_DOMAIN', 'enginetheme' );
	}

	define( 'MILESTONE_DIR_URL', plugin_dir_url( __FILE__ ) );
	define( 'MAX_MILESTONE', ae_get_option( 'max_milestone', 5 ) );

	$ae_milestone_posttype = AE_Milestone_Posttype::getInstance();
	$ae_milestone_posttype->init();

	$ae_milestone_actions = AE_Milestone_Actions::getInstance();
	$ae_milestone_actions->init();
}

add_action( 'after_setup_theme', 'require_plugin_files' );

/**
 * Enqueue for milestone plugin
 * @param void
 * @return void
 *
 * @since 1.0
 * @package FREELANCEENGINE
 * @category AE MILESTONE
 * @author tatthien
 */
function ae_plugin_equeue_scripts() {
	wp_enqueue_style( 'ae-milestone-style', plugin_dir_url( __FILE__ ) . '/assets/css/ae-milestone.css', array(), '1.0' );

	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script( 'ae-milestone', plugin_dir_url( __FILE__ ) . '/assets/js/ae-milestone.js', array(
		'jquery',
		'underscore',
		'backbone',
		'appengine'
	), '1.0', true );

	wp_localize_script( 'ae-milestone', 'ae_ms_localize', array(
		'max_milestone' => MAX_MILESTONE,
		'remove_milestone_confirm' => __( 'Are you sure to delete this?', ET_DOMAIN )
	) );
}

add_action( 'wp_enqueue_scripts', 'ae_plugin_equeue_scripts' );
