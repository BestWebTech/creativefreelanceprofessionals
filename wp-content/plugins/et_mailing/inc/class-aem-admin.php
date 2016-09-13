<?php

/**
 * @project et_mailing
 * @author  nguyenvanduocit
 * @date    02/03/2015
 */
require_once AEM_PLUGIN_PATH.'/inc/ae/appengine.php';

class AEM_Admin extends AE_Base
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
        /**
         * admin setup
         */
        $this->add_action( 'init', 'admin_setup' );
        /**
         * set default options
         */
        $this->add_action( 'admin_head', 'admin_custom_css' );
    }

    function admin_custom_css ()
    {
        ?>
        <style type="text/css">
            .custom-icon {
                margin: 10px;
            }

            .custom-icon input {
                width: 80%;
            }
        </style>
    <?php
    }

    function admin_setup ()
    {
        $pages = array ();

        $pre_page = array ();
        $pre_page = apply_filters( 'ae_admin_menu_pages', $pre_page );

        $root_slug = "et-mailing";
        $is_root_slug_used = FALSE; //check if any page used root slug

        if ( count( $pre_page ) > 0 ) {
            $root_slug = $pre_page[0]['args']['slug'];
        }

        $options = AEM_Option::get_instance();

        foreach ( $pre_page as $page ) {
            //Get section of $page
            $sections = $page['container']->get_sections();
            //Foreach all section, add section to single page
            foreach ( $sections as $section ) {

                $arg = $section->get_args();

                $container = new AE_container( array (
                    'class' => $arg['class'],
                    'id'    => 'settings',
                ), $section, $options );

                $pages[] = array (
                    'args'      => array (
                        'parent_slug' => $root_slug,
                        'page_title'  => $arg['title'],
                        'menu_title'  => $arg['title'],
                        'cap'         => 'administrator',
                        'slug'        => ( $is_root_slug_used ? $arg['id'] : $root_slug ), //first page must have root slug
                        'icon'        => $arg['icon'],
                        'desc'        => ''
                    ),
                    'container' => $container
                );
                $is_root_slug_used = TRUE;
            }
        }
        /**
         * add menu page
         */

        $menu_arg = array (
            'page_title' => __( 'Mailing setings', AEM_DOMAIN ),
            'menu_title' => __( 'Mailing', AEM_DOMAIN ),
            'cap'        => 'administrator',
            'slug'       => $root_slug,
            'icon_url'   => '',
            'pos'        => 3
        );
        $this->admin_menu = new AE_Menu( $pages, $menu_arg );

        foreach ( $pages as $key => $page ) {
            new AE_Submenu( $page, $pages );
        }
    }
}