<?php

/**
 * @project ae_mailing
 * @author  nguyenvanduocit
 * @date    01/30/2015
 */
class AEM_Util
{

    public static function check_php_version ()
    {
        if ( version_compare( PHP_VERSION, '5.3.2' ) >= 0 ) {
            return TRUE;
        }

        return new WP_Error( 'check_php_version', __( "Mailing AE require a minimum version of PHP 5.3.2, but ensure tenants PHP version you are using is less than 5.3.2. Please upgrade PHP to be able to use the plugin.", AEM_DOMAIN ) );

    }

    public static function check_require ()
    {

        $php_check_result = self::check_php_version();
        if ( is_wp_error( $php_check_result ) ) {
            add_action( 'admin_notices', array ( __CLASS__, 'upgrade_php_notice' ) );

            return $php_check_result;
        }

        $option_check_result = self::check_option();
        if ( !$option_check_result ) {
            add_action( 'admin_notices', array ( __CLASS__, 'update_option_notice' ) );

            return $php_check_result;
        }

    }

    public static function check_option ()
    {
        $current_module = AEM()->module_factory()->get_current_module();
        $check_result = $current_module->check_option();

        return $check_result;
    }

    public static function update_option_notice ()
    {
        ?>
        <div class="update-nag">
            <p><?php _e( "You can't use the emails because you haven't inserted ET Mailing's API. Please insert the API to make the website work like charm.", AEM_DOMAIN ); ?></p>
        </div>
    <?php
    }

    public static function upgrade_php_notice ()
    {
        ?>
        <div class="update-nag">
            <p><?php _e( "Plugin can't work, because Mailing AE require a minimum version of PHP 5.3.2, but ensure tenants PHP version you are using is less than 5.3.2. Please upgrade PHP to be able to use the plugin.", AEM_DOMAIN ); ?></p>
        </div>
    <?php
    }
}