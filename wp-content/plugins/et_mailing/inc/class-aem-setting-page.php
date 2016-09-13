<?php

/**
 * @project ae_mailing
 * @author  nguyenvanduocit
 * @date    01/29/2015
 */
class AEM_Setting_Page
{
    static $_instance;

    public static function instance ()
    {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    function __construct ()
    {
        add_filter( 'ae_admin_menu_pages', array ( $this, 'add_admin_menu_pages' ) );
        $this->init_hook();
    }

    function init_hook ()
    {
        add_action( 'wp_ajax_aem_test_email', array ( $this, 'sen_test_email' ) );
        add_action( 'admin_enqueue_scripts', array ( $this, "enqueue_scripts" ) );
    }
    function sen_test_email(){
        //$mailer = AE_Mailing::get_instance();
        if ( isset( $_POST["test_email"] ) ) {
            try {
                $message = "This is the test email.";
                $send_result = wp_mail( $_POST["test_email"], __( "AE_Mail Test mail", AEM_DOMAIN ), $message );
                //Test with ET's themes
                //AE_Mailing::get_instance()->confirmed_mail(1);
                if(is_wp_error($send_result)){
                    wp_send_json( array ( 'sucess' => FALSE, 'message' => $send_result->get_error_message() ) );
                }
                else {
                    wp_send_json( array ( 'sucess' => TRUE, 'message' => __( "Message enqueued.", AEM_DOMAIN ) ) );
                }
            } catch ( Exception $ex ) {
                wp_send_json( array ( 'sucess' => FALSE, 'message' => $ex->getMessage() ) );
            }
        } else {
            wp_send_json( array ( 'sucess' => FALSE, 'message' => __( "Test email is not provided ! Please fill the text box above.", AEM_DOMAIN ) ) );
        }
    }
    function enqueue_scripts($hook){
        wp_enqueue_script( 'aem_backend_script', plugins_url( "/js/setting.js", AEM_PLUGIN_FILE ), array ( "appengine" ), NULL, TRUE );
        $modules = AEM()->module_factory()->get_modules();
        foreach ( $modules as $module ) {
            $module->enqueue_scripts();
        }
    }
    /**
     * Add admin menu in ET's themes
     *
     * @param $pages
     */
    public function add_admin_menu_pages ( $pages )
    {
        $options = AEM_Option::get_instance();

        $modules = AEM()->module_factory()->get_modules();

        $temp = array ();

        $general_section = $this->get_general_section();

        $temp[] = new AE_section( $general_section['args'], $general_section['groups'], $options );
        foreach($modules as $module){
            $section = $module->get_setting_section();
            $temp[] = new AE_section( $section['args'], $section['groups'], $options );
        }

        $mailing_container = new AE_container( array (
            'class' => 'mailing-settings',
            'id'    => 'settings',
        ), $temp, $options );

        $pages[] = array (
            'args'      => array (
                'parent_slug' => 'et-overview',
                'page_title'  => __( 'Mailing', AEM_DOMAIN ),
                'menu_title'  => __( 'Mailing', AEM_DOMAIN ),
                'cap'         => 'administrator',
                'slug'        => 'aem-settings',
                'icon'        => 'M',
                'desc'        => __( "Third-party email service management.", AEM_DOMAIN )
            ),
            'container' => $mailing_container
        );

        return $pages;
    }

    function get_general_section ()
    {
        $sections = array (
            'args'   => array (
                'title' => __( "General settings", AEM_DOMAIN ),
                'id'    => 'aem-general-settings',
                'icon'  => "Y",
                'class' => ''
            ),
            'groups' => array (
                array (
                    'args'   => array (
                        'title' => __( "From Email", AEM_DOMAIN ),
                        'id'    => 'aem-from_email',
                        'desc'  => __( "The email name you want to display in email header", AEM_DOMAIN ),
                        'class' => ''
                    ),
                    'fields' => array (
                        array (
                            'id'    => 'from_email',
                            'type'  => 'text',
                            'title' => __( "From email ", AEM_DOMAIN ),
                            'name'  => 'aem_from_email',
                            'class' => ''
                        ),
                    ),
                ), array (
                    'args'   => array (
                        'title' => __( "From name", AEM_DOMAIN ),
                        'id'    => 'aem-from_name',
                        'desc'  => __( "The name you want to display in email header", AEM_DOMAIN ),
                        'class' => ''
                    ),
                    'fields' => array (
                        array (
                            'id'    => 'from_name',
                            'type'  => 'text',
                            'title' => __( "From name ", AEM_DOMAIN ),
                            'name'  => 'aem_from_name',
                            'class' => ''
                        ),
                    ),
                ),
                array (
                    'args'   => array (
                        'title' => __( "Force header", AEM_DOMAIN ),
                        'id'    => 'aem-force-header',
                        'desc'  => __( "If enable, all email send by all plugin, theme will be change to the infor above", AEM_DOMAIN ),
                        'class' => ''
                    ),
                    'fields' => array (
                        array (
                            'id'    => 'force_header',
                            'type'  => 'switch',
                            'title' => __( "Force header ", AEM_DOMAIN ),
                            'name'  => 'aem_force_header',
                            'class' => ''
                        ),
                    ),
                ),
                array(
                    'args' => array(
                        'title' => __("Service to use", AEM_DOMAIN) ,
                        'id' => 'select_email_service',
                        'class' => '',
                        'desc' => __("Select a service you want to use.", AEM_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'eam_current_service',
                            'type' => 'select',
                            'data' => AEM()->module_factory()->get_module_names(),
                            'title' => __("Available service", AEM_DOMAIN) ,
                            'name' => 'eam_current_service',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __("Select a service", AEM_DOMAIN),
                            'lable' => __("Available service", AEM_DOMAIN)
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Send test email", AEM_DOMAIN) ,
                        'id' => 'send_test_email',
                        'class' => '',
                        'desc' => __("Click to test email.", AEM_DOMAIN)
                    ) ,

                    'fields' => array(
                        array (
                            'id'    => 'test_email',
                            'type'  => 'text',
                            'title' => __( "Test email", AEM_DOMAIN ),
                            'name'  => 'aem_test_email',
                            'class' => ''
                        ),
                        array (
                            'id'    => 'send_text_email',
                            'text'  => "Send Test Email",
                            'type'  => 'Custom_Type_Button',
                            'name'  => 'aem_send_test_email',
                            'class' => 'bg-grey-button button btn-button'
                        )
                    )
                ),

                array (
                    'args'   => array (
                        'title' => __( "Click tracking", AEM_DOMAIN ),
                        'id'    => 'aem-click-tracking',
                        'class' => '',
                        'desc'  => __( "Track link click on email", AEM_DOMAIN )
                    ),

                    'fields' => array (
                        array (
                            'id'    => 'click_tracking',
                            'type'  => 'switch',
                            'title' => __( "Click tracking ", AEM_DOMAIN ),
                            'name'  => 'aem_click_tracking',
                            'class' => ''
                        ),
                    ),
                ),
                array (
                    'args'   => array (
                        'title' => __( "Open tracking", AEM_DOMAIN ),
                        'id'    => 'aem-open-tracking',
                        'class' => '',
                        'desc'  => __( "Open click", AEM_DOMAIN )
                    ),

                    'fields' => array (
                        array (
                            'id'    => 'click_tracking',
                            'type'  => 'switch',
                            'title' => __( "Open tracking ", AEM_DOMAIN ),
                            'name'  => 'aem_open_tracking',
                            'class' => ''
                        ),
                    ),
                )
            )
        );

        return $sections;
    }
}