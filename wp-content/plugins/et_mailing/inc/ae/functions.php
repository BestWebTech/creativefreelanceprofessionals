<?php
/**
 * return core url
 */
if (!function_exists('ae_get_url')) {
    function ae_get_url() {
        return plugins_url('', __FILE__);
    }
}

/**
 * return core path
 */
if (!function_exists('ae_get_path')) {
    function ae_get_path() {
        return dirname(__FILE__);
    }
}

/**
 * ae get template part
 * @param $slug
 * @param String $name
 * @version 1.0
 */
if (!function_exists('ae_get_template_part')) {
    function ae_get_template_part($slug, $name) {
        $template = '';
        
        // Look in yourtheme/slug-name.php and yourtheme/woocommerce/slug-name.php
        if ($name) {
            $template = locate_template(array(
                "/includes/aecore/template/{$slug}-{$name}.php",
                "{$slug}-{$name}.php"
            ));
        }
        
        // Get default slug-name.php
        if (!$template && $name && file_exists(ae_get_path() . "/template/{$slug}-{$name}.php")) {
            $template = ae_get_path() . "/template/{$slug}-{$name}.php";
        }
        
        // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/woocommerce/slug.php
        if (!$template) {
            $template = locate_template(array(
                "{$slug}.php",
                ae_get_path() . "{$slug}.php"
            ));
        }
        
        // Allow 3rd party plugin filter template file from their plugin
        $template = apply_filters('ae_get_template_part', $template, $slug, $name);
        
        if ($template) {
            load_template($template, false);
        }
    }
}

/**
 * check user capability
 * @param $cap user cap
 * @return bool
 * @author Dakachi
 */
if (!function_exists('ae_user_can')) {
    function  ae_user_can( $cap )
    {
        return current_user_can( $cap );
    }
}
/**
 * get current user role  
 * @param int $user_ID The user ID you want to get role
 * @since v1.1
 * @return string $role current user role or null
 * @author Dakachi
 */
if (!function_exists('ae_user_role')) {
    function ae_user_role( $user_ID = '' )
    {
        // get user id 's role
        if ( $user_ID != '' ) {
            $user_info = get_userdata( $user_ID );
        } else {
            // get current user role
            global $current_user;
            $user_info = $current_user;
        }
        // if user exist
        if ( $user_info ) {
            $roles = $user_info->roles;

            return array_pop( $roles );
        }

        // user not exist or not logged in
        return '';
    }
}
/**
 * get theme option
 * @param $name the name of option
 * @return $option_value
 * @author Dakachi
 */
if (!function_exists('ae_get_option')) {
    function ae_get_option( $name, $default = FALSE )
    {
        $option = AE_Options::get_instance();

        return ( $option->$name != '' ) ? $option->$name : $default;
    }
}
/**
 * update theme option, if option not exist create new
 * @param $name the name of option
 * @param $name the name of option
 * @return void
 * @author Dakachi
 */
if (!function_exists('ae_update_option')) {
    function ae_update_option ( $name, $new_value )
    {
        if ( !current_user_can( 'manage_options' ) )
            return;
        $option = AE_Options::get_instance();

        return $option->update_option( $name, $new_value );
    }
}
if (!function_exists('ae_set_option')) {
    function ae_set_option ( $name, $new_value )
    {
        if ( !current_user_can( 'manage_options' ) )
            return;
        $option = AE_Options::get_instance();

        return $option->update_option( $name, $new_value );
    }
}
/*
 * return array wp_editor config
*/
if (!function_exists('ae_editor_settings')) {
    function ae_editor_settings ()
    {
        return apply_filters( 'ae_editor_settings', array (
            'quicktags'     => FALSE,
            'media_buttons' => FALSE,
            'wpautop'       => FALSE,

            //'tabindex'    =>  '2',
            'teeny'         => TRUE,
            'tinymce'       => array (
                'content_css'                       => ae_get_url().'/assets/css/tinyeditor-content.css',
                'height'                            => 250,
                'editor_class'                      => 'input-item',
                'autoresize_min_height'             => 250,
                'autoresize_max_height'             => 550,
                'theme_advanced_buttons1'           => 'bold,|,italic,|,underline,|,bullist,numlist,|,link,unlink,|,wp_fullscreen',
                'theme_advanced_buttons2'           => '',
                'theme_advanced_buttons3'           => '',
                'theme_advanced_statusbar_location' => 'none',
                'theme_advanced_resizing'           => TRUE,
                'setup'                             => "function(ed){
                ed.onChange.add(function(ed, l) {
                    var content = ed.getContent();
                    if(ed.isDirty() || content === '' ){
                        ed.save();
                        jQuery(ed.getElement()).blur(); // trigger change event for textarea
                    }

                });

                // We set a tabindex value to the iframe instead of the initial textarea
                ed.onInit.add(function() {
                    var editorId = ed.editorId,
                        textarea = jQuery('#'+editorId);
                    jQuery('#'+editorId+'_ifr').attr('tabindex', textarea.attr('tabindex'));
                    textarea.attr('tabindex', null);
                });
            }"
            )
        ) );
    }
}
if(!function_exists("ce_teeny_mce_buttons")) {
    add_filter( 'teeny_mce_buttons', 'ce_teeny_mce_buttons' );
    function ce_teeny_mce_buttons ( $buttons )
    {
        return array (
            'format',
            'bold',
            'italic',
            'underline',
            'bullist',
            'numlist',
            'link',
            'unlink'
        );
    }
}
if(!function_exists("ce_tinymce_add_plugins")) {
    function ce_tinymce_add_plugins ( $plugin_array )
    {

        // $autoresize = get_template_directory_uri() . '/js/lib/tiny_mce/plugins/autoresize/editor_plugin.js';

        // //$plugin_array['feimage'] = $feimage;
        // if(!is_admin())
        //     $plugin_array['autoresize'] = $autoresize;

        //$plugin_array['etHeading']    = $et_heading;
        //$plugin_array['wordcount']    = $wordcount;

        return $plugin_array;
    }

    add_filter( 'mce_external_plugins', 'ce_tinymce_add_plugins' );
}