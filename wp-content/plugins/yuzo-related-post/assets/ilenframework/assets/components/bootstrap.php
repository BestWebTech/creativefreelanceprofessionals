<?php
/**
* Add bootstrap style and js for wp-admin
*
* @package ilentheme
* @since 1.5 core
* @date 21/09/2014
*
*/

 // Add actions
/*add_action('admin_enqueue_scripts', 'ilenframework_add_scripts_admin_bootstrap' );

// Enqueue Script Bootstrap
function ilenframework_add_scripts_admin_bootstrap(){
  global $IF_CONFIG;


  // core
  wp_enqueue_script( 'ilentheme-js-bootstrap-'.$IF_CONFIG->parameter['id'], $IF_CONFIG->parameter['url_framework'] . '/assets/js/bootstrap.min.js', array( 'jquery','jquery-ui-core'), '', true );
  wp_register_style( 'ilentheme-style-bootstrap-'.$IF_CONFIG->parameter['id'],  $IF_CONFIG->parameter['url_framework'] . '/assets/css/bootstrap.min.css' );

  // datetimepicker
  wp_enqueue_script( 'ilentheme-js-bootstrap-moment-'.$IF_CONFIG->parameter['id'], $IF_CONFIG->parameter['url_framework'] . '/assets/js/moment.min.js', array( 'jquery'), '', true );
  wp_enqueue_script( 'ilentheme-js-bootstrap-datetimepicker-'.$IF_CONFIG->parameter['id'], $IF_CONFIG->parameter['url_framework'] . '/assets/js/bootstrap-datetimepicker.min.js', array( 'jquery'), '', true );
  //wp_register_style( 'ilentheme-style-bootstrap-dt-'.$IF_CONFIG->parameter['id'],  'http://www.malot.fr/bootstrap-datetimepicker/bootstrap-datetimepicker/css/bootstrap-datetimepicker.css' );

  wp_enqueue_style(  'ilentheme-style-bootstrap-'.$IF_CONFIG->parameter['id'] );
}*/
?>