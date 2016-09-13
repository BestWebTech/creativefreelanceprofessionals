<?php namespace Gewora\Autoloader;
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

function GeworaAutoload_AccountLockerLite ($classname) {
    // if the class where already loaded. should not happen
    if (class_exists($classname)) {
        return true;
    }

    $path = str_replace(
            array('_', '\\'),
            '/',
            $classname
        ) . '.php';

    $fullpath = str_replace('lib/Gewora', 'lib', plugin_dir_path( __FILE__ )) . $path;
    $fullpath = str_replace('Gewora/Gewora', 'Gewora', $fullpath);


    if(file_exists($fullpath)) {
        include_once $fullpath;
        return true;
    }

    return false;
}

spl_autoload_register(__NAMESPACE__ . '\GeworaAutoload_AccountLockerLite');
