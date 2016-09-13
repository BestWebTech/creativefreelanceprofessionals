<?php

/**
 * @project ae_mailing
 * @author  nguyenvanduocit
 * @date    01/30/2015
 */

class AEM_Module_Factory
{
    private $modules = array ();

    function get_current_module ()
    {
        $current_module = aem_get_option( 'eam_current_service', 0 );
        $modules = $this->get_module_names();
        if ( isset( $modules[$current_module] ) ) {
            return $this->get_module( $modules[$current_module] );
        } else {
            return FALSE;
        }
    }

    function get_modules ()
    {
        $modules = $this->get_module_names();
        foreach ( $modules as $module_name ) {
            $this->get_module( $module_name );
        }

        return $this->modules;
    }

    function get_module_names ()
    {
        $modules = array (
            "Mailgun",
            "Mandrill",
            "Sendgrid"
        );
        $modules = apply_filters( "aem_get_module_names", $modules );

        return $modules;
    }

    function get_module ( $module_name )
    {
        if ( !isset( $this->modules[$module_name] ) ) {

            $module_object = $this->get_module_class( $module_name );
            if ( NULL !== $module_object ) {
                $this->modules[$module_name] = $module_object;
            }

            return $module_object;
        }

        return $this->modules[$module_name];
    }

    function get_module_class ( $module_name )
    {
        $class_name = sprintf( "AEM_Module_%s", $module_name );
        $class_name = apply_filters( "aem_get_module_class", $class_name, $module_name );
        if ( class_exists( $class_name ) ) {
            return new $class_name;
        }

        return false;
    }
}