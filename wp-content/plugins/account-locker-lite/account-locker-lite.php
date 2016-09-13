<?php

/**
 * Plugin Name: Account Locker Lite
 * Plugin URI: http://gewora.net
 * Description: Allows the administrator to lock/ban specific accounts without deleting them.
 * Version: 1.0.0
 * Author: Gewora.net
 * Author URI: http://gewora.net
 */

/*======================================================================*\
|| #################################################################### ||
|| #             This file is part of Account Locker Lite             # ||
|| # ---------------------------------------------------------------- # ||
|| #         Copyright © 2014 Gewora.net. All Rights Reserved.        # ||
|| # This file may not be redistributed in whole or significant part  # ||
|| #                                                                  # ||
|| #            For more license information contact Gewora           # ||
|| #            and/or read the license details which have            # ||
|| #                  been included in this product                   # ||
|| #                                                                  # ||
|| # You are NOT allowed to modify/remove this copyright information  # ||
|| #                                                                  # ||
|| #              Any infringement of this copyright will             # ||
|| #               result in legal action by the holder               # ||
|| #                                                                  # ||
|| # -------------------- http://www.gewora.net --------------------- # ||
|| #################################################################### ||
\*======================================================================*/

if(!defined('ABSPATH')) exit('restricted access');


# Define constants
define('GEWORA_ACCOUNT_LOCKER_LITE_WORDPRESS_PLUGIN_BASE_URL', plugins_url('',  __FILE__ ));
define('GEWORA_ACCOUNT_LOCKER_LITE_WORDPRESS_PLUGIN_BASE_PATH', plugin_dir_path( __FILE__ ));
define('GEWORA_ACCOUNT_LOCKER_LITE_WORDPRESS_PLUGIN_NAMESPACE', 'AccountLockerPro');

/*
|--------------------------------------------------------------------------
| Start the autoloader
|--------------------------------------------------------------------------
|
| Write the important stuff into the session
|+
*/

require_once(GEWORA_ACCOUNT_LOCKER_LITE_WORDPRESS_PLUGIN_BASE_PATH . 'lib/Gewora/Autoloader.php');

/*
|--------------------------------------------------------------------------
| Start the plugin classes
|--------------------------------------------------------------------------
|
| Start our classes
|
*/

# Setup class (used on plugin installation/uninstalling
use Gewora\AccountLockerLite\Setup;
$Setup = new Setup();

# Main class
use Gewora\AccountLockerLite\Gewora;
$Setup = new Gewora();
