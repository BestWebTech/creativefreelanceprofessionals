<?php
/**
 * @project ae_mailing
 * @author nguyenvanduocit
 * @date 01/30/2015
 */
class AE_Custom_Type_Button{
    function __construct( $field = array(), $value ='', $parent ) {

        //parent::__construct( $parent->sections, $parent->args );
        $this->parent = $parent;
        $this->field = $field;
        $this->value = $value;

    }

    /**
     * Field Render Function.
     *
     * Takes the vars and outputs the HTML for the field in the settings
     *
     * @since AEFramework 1.0.0
     */
    function render() {

        $readonly = isset($this->field['readonly']) ? ' readonly="readonly"' : '';
        $placeholder = (isset($this->field['placeholder']) && !is_array($this->field['placeholder'])) ? ' placeholder="' . esc_attr($this->field['placeholder']) . '" ' : '';

        $default = isset($this->field['default']) ? $this->field['default'] : '';
        $value = ($this->value) ? (esc_attr($this->value) ) : $default;

        if( isset( $this->field['label']) && $this->field['label'] !== '') {
            echo '<label for="'. $this->field['id'] .'">'. $this->field['label'] .'</label>';
        }
        echo '<button id="' . $this->field['id'] . '" name="' . $this->field['name'] .'" ' . $placeholder . '" value="' . $value . '" class="regular-text ' . $this->field['class'] . '"'.$readonly.'>'.$this->field['text'].'</button><br />';

    }//render
}