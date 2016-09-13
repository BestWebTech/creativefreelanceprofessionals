<?php

/**
 * ======================================================================
 * LICENSE: This file is subject to the terms and conditions defined in *
 * file 'license.txt', which is part of this source code package.       *
 * ======================================================================
 */

return array(
    array(
        'title' => 'AAM Plus Package',
        'id' => 'AAM Plus Package',
        'type' => 'commercial',
        'cost'  => '$30',
        'currency' => 'USD',
        'description' => __('Setup access to unlimited number of posts, pages or custom post types as well as define default access to ALL posts, pages, custom post types, categories or custom taxonomies.', AAM_KEY),
        'storeURL' => 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FGAHULDEFZV4U',
        'status' => AAM_Core_Repository::getInstance()->extensionStatus('AAM Plus Package')
    ),
    array(
        'title' => 'AAM Role Filter',
        'id' => 'AAM Role Filter',
        'type' => 'commercial',
        'cost'  => '$5',
        'currency' => 'USD',
        'description' => __('More advanced user and role administration. Based on user capabilities level, filter list of roles that user can manage. Also prevent from editing, promoting or deleting higher level users.', AAM_KEY),
        'storeURL' => 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=G9V4BT3T8WJSN',
        'status' => AAM_Core_Repository::getInstance()->extensionStatus('AAM Role Filter')
    ),
    array(
        'title' => 'AAM Support',
        'id' => 'AAM Support',
        'type' => 'commercial',
        'cost'  => '$50',
        'currency' => 'USD',
        'description' => AAM_Backend_View_Helper::preparePhrase('Highest priority technical support (within 1 business day). Need help or not sure how to use AAM? We will carefully analyze your objectives, educate and help you to archive your goals with existing AAM functionality or we will put $50 toward custom development if necessary. [Otherwise money back guaranteed!]', 'strong'),
        'storeURL' => 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZM8BAAN8CE3M4',
        'status' => 'download'
    ),
    array(
        'title' => 'AAM Dev License',
        'id' => 'AAM Development License',
        'type' => 'commercial',
        'cost'  => '$150',
        'currency' => 'USD',
        'description' => __('Development license gives you an ability to download all the available extensions and use them up to 5 life domains.', AAM_KEY),
        'storeURL' => 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZX9RCWU6BTE52',
        'status' => AAM_Core_Repository::getInstance()->extensionStatus('AAM Development License')
    ),
    array(
        'title' => 'AAM Multisite',
        'id' => 'AAM Multisite',
        'type' => 'GNU',
        'license' => 'AAMMULTISITE',
        'description' => __('Convenient way to navigate between different sites in the Network Admin Panel. This extension adds additional widget to the AAM page that allows to switch between different sites.', AAM_KEY),
        'status' => AAM_Core_Repository::getInstance()->extensionStatus('AAM Multisite')
    ),
    array(
        'title' => 'AAM Post Filter',
        'id' => 'AAM Post Filter',
        'type' => 'GNU',
        'license'  => 'AAMPOSTFILTER',
        'description' => AAM_Backend_View_Helper::preparePhrase('[WARNING!] Please use with caution. This is a supportive exension for the post access option [List]. It adds additional post filtering to fix the issue with large amount of post. [Turned on caching] is strongly recommended.', 'strong', 'strong', 'strong'),
        'status' => AAM_Core_Repository::getInstance()->extensionStatus('AAM Post Filter')
    ),
    array(
        'title' => 'AAM Skeleton Extension',
        'id' => 'AAM Skeleton Extension',
        'type' => 'GNU',
        'license' => 'SKELETONEXT',
        'description' => __('Skeleton for custom AAM extension. Please find all necessary documentation inside the source code.', AAM_KEY),
        'status' => AAM_Core_Repository::getInstance()->extensionStatus('AAM Skeleton Extension')
    ),
    array(
        'title' => 'CodePinch',
        'id' => 'WP Error Fix',
        'type' => 'plugin',
        'description' => __('Our patent-pending technology provides solutions to PHP errors within hours, preventing costly maintenance time and keeping your WordPress site error.', AAM_KEY),
        'status' => AAM_Core_Repository::getInstance()->pluginStatus('WP Error Fix')
    ),
    array(
        'title' => 'User Switching',
        'id' => 'User Switching',
        'type' => 'plugin',
        'description' => __('Instant switching between user accounts in WordPress.', AAM_KEY),
        'status' => AAM_Core_Repository::getInstance()->pluginStatus('User Switching')
    )
);