<?php
ob_start();
session_start();
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
define('BUSINESS_PLUGIN_PATH', WP_PLUGIN_DIR . '/business-idea/');
define('BUSINESS_PLUGIN_URL', plugins_url( '', __FILE__ ).'/');

function business_idea_install_db() {
	global $wpdb;
	$sql1="CREATE TABLE IF NOT EXISTS `".$wpdb->base_prefix."business_idea` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `full_name` varchar(300) DEFAULT NULL,
			  `user_email` VARCHAR( 300 ) NOT NULL,
			  `start_up_name` varchar(300) DEFAULT NULL,
			  `idea` text DEFAULT NULL,
			  `attachment` text DEFAULT NULL,
			  `budget` FLOAT(12,2) NULL DEFAULT '0.00',
			  `youtube_video` VARCHAR(300) NULL,
			  `vimeo_video` VARCHAR(300) NULL,
			  `status` int(2) DEFAULT '0',
			  `created` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
	dbDelta($sql1);
}


function business_idea_on_uninstall()
{
	global $wpdb;
	if (!current_user_can( 'activate_plugins' ) )
        return;  
	if ( __FILE__ != WP_UNINSTALL_PLUGIN )
	$sql1="DROP TABLE IF EXISTS ".$wpdb->base_prefix."business_idea;";
	$wpdb->query($sql1);	
	return;
}

function business_idea_on_activation()
{
	global $wpdb;	
	business_idea_install_db();
}


register_uninstall_hook(BUSINESS_PLUGIN_PATH.'business-idea.php', 'business_idea_on_uninstall' );
register_activation_hook(BUSINESS_PLUGIN_PATH.'business-idea.php', 'business_idea_on_activation' );


?>
