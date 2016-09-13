<?php

class AEM_Mailing
{
    static $_instance = NULL;
    private $module_factory = NULL;
    private $option = NULL;

    public static function instance ()
    {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    function init ()
    {
        $this->init_hook();
    }

    function module_factory ()
    {
        if ( is_null( $this->module_factory ) ) {
            $this->module_factory = new AEM_Module_Factory();
        }

        return $this->module_factory;
    }

    /*
     * Init hook
     */
    function init_hook ()
    {
        //Advoid conflic with theme use appengine
        add_action( "after_setup_theme", array ( $this, "init_module_hook" ) );
    }

    /**
     * Call current init_hook() to init their hook
     */
    function init_module_hook ()
    {
        $module = AEM()->module_factory()->get_current_module();
        if ( $module ) {
            $module->init_hook();
        }
    }
}