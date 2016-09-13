<?php
/**
 * Class AE_container
 * create a elements container , it can contain anything
 * @author Dakachi
*/
if(! class_exists('AE_container')) {
    Class AE_container
    {
        /**
         * Field Constructor.
         *
         * @param array $params
         * - html tag
         * - id
         * - name
         * - class
         * - title
         * @param array $sections
         * @param       $parent
         *
         * @since AEFramework 1.0.0
         */
        function __construct ( $params = array (), $sections, $parent )
        {

            //parent::__construct( $parent->sections, $parent->args );
            $this->parent = $parent;
            $this->field = $params;
            $this->sections = $sections;

        }
        function get_sections(){
            return $this->sections;
        }
        /**
         * render container element
         */
        function render ()
        {

            $sections = $this->sections;
            echo '<div class="et-main-content '.$this->field['class'].'" id="'.$this->field['id'].'" >';
            // render menu if have  more then 1 section
            if ( is_array( $sections ) ) {
                /**
                 * render section menus
                 */
                echo '<div class="et-main-left"><ul class="et-menu-content inner-menu">';
                $first = TRUE;
                foreach ( $sections as $key => $section ) {
                    $section->render_menu( $first );
                    $first = FALSE;
                }
                echo '</ul></div>';

                echo '<div class="settings-content">';
                $first = TRUE;
                foreach ( $sections as $key => $section ) {
                    $section->render( $first );
                    $first = FALSE;
                }

                echo '</div>';

            } else {
                echo '<div class="one-column">';
                $sections->render( TRUE );
                echo '</div>';
            }

            echo '</div>';
        }

    }
}