<?php
/*
Plugin Name: AppEngine
Plugin URI: www.enginethemes.com
Description: Easy implement a front form, and publish a web application
Version: 1.1
Author: Engine Themes team
Author URI: www.enginethemes.com
License: GPL2
*/
if ( !class_exists( 'AppEngine' ) ) {
    if ( !defined( 'ET_DOMAIN' ) ) {
        define( 'ET_DOMAIN', 'et_domain' );
    }
    require_once dirname( __FILE__ ).'/bootstrap.php';
}