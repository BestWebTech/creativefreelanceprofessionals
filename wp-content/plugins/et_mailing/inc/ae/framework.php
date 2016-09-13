<?php
if ( !class_exists( 'AppEngine' ) ):
    class AppEngine extends AE_Base
    {

        public function __construct ()
        {

            /**
             * add script appengine
             */
            $this->add_action( 'admin_enqueue_scripts', 'print_scripts' );
            if ( isset( $_REQUEST['page'] ) ) {
                $this->add_action( 'admin_print_footer_scripts', 'override_template_setting', 200 );
            }
        }

        /**
         * register base script
         */
        public function print_scripts ()
        {

            $this->add_existed_script( 'jquery' );

            $this->register_script( 'bootstrap', ae_get_url().'/assets/js/bootstrap.min.js', array (
                'jquery'
            ), null, TRUE );
            $this->register_script('jquery-validator', ae_get_url() . '/assets/js/jquery.validate.min.js', 'jquery');
            $this->register_script('marionette', ae_get_url() . '/assets/js/marionette.js', array(
                'jquery',
                'backbone',
                'underscore',
            ) , true);
            // ae core js appengine
            $this->register_script( 'appengine', ae_get_url().'/assets/js/appengine.js', array (
                'jquery',
                'underscore',
                'backbone',
                'marionette',
                'plupload',
            ), TRUE );


            // Loads the Internet Explorer specific stylesheet.
            if ( !is_admin() ) {
                $this->register_style( 'bootstrap', ae_get_url().'/assets/css/bootstrap.min.css', array (), '3.0' );
            }
        }

        /**
         * add script to footer override underscore templateSettings, localize validator message
         */
        function override_template_setting ()
        {
            ?>
            <!-- override underscore template settings -->
            <script type="text/javascript">
                _.templateSettings = {
                    evaluate: /\<\#(.+?)\#\>/g,
                    interpolate: /\{\{=(.+?)\}\}/g,
                    escape: /\{\{-(.+?)\}\}/g
                };
            </script>
        <?php

        }
    }

    global $et_appengine;
    $et_appengine = new AppEngine();
endif;
