<?php
//TODO : move to symphony classloader
if ( !class_exists( 'AEM_Autoload' ) ) {

    /**
     * Auto load core class and module class
     *
     * Class WFA_Autoload
     * @author nguyenvanduocit
     */
    class AEM_Autoload
    {
        /**
         * @param $class bane
         */
        public static function autoload ( $class )
        {
            $path = NULL;
            $class = strtolower( $class );
            $file = 'class-'.str_replace( '_', '-', $class ).'.php';
            //base module
            if ( ( $class == 'aem_module_base' ) || ( $class == 'aem_module_factory' ) ) {
                $path = AEM_PLUGIN_PATH."/inc/module/";
            } //module class
            elseif ( strpos( $class, 'aem_module_' ) === 0 ) {

                $last_dash = strripos( $class, '_' );

                if ( $last_dash > 10 ) {
                    $path = AEM_PLUGIN_PATH."/inc/module/".trailingslashit( substr( str_replace( '_', '-', $class ), 11, $last_dash - 11 ) );
                } else {
                    $path = AEM_PLUGIN_PATH."/inc/module/".trailingslashit( substr( str_replace( '_', '-', $class ), 11 ) );
                }
            } elseif ( strpos( $class, 'ae_custom_type_' ) === 0 ) {
                $path = AEM_PLUGIN_PATH."/inc/custom_fields/";
            } elseif ( strpos( $class, 'aem_' ) === 0 ) {
                $path = AEM_PLUGIN_PATH."/inc/";
            } else {
                return;
            }
            if ( $path && is_readable( $path.$file ) ) {
                include_once( $path.$file );
                return;
            }
        }
    }

    // register class autoloader
    spl_autoload_register( array ( 'AEM_Autoload', 'autoload' ) );

}

