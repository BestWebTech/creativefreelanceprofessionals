<?php
/**
 * @project ae_mailing
 * @author  nguyenvanduocit
 * @date    01/30/2015
 */
function AEM ()
{
    return AEM_Mailing::instance();
}

/**
 * Get setting page instance
 * Call this function in after_setup_theme only, b/c it need to check if current theme is ET's theme.
 *
 * @return AEM_ET_Setting_Page
 */
function AEM_Admin ()
{
    //Check wherever current site is using ET's Themes
    if ( !class_exists( 'AE_Base' ) ) {
        //if not using ET's theme, we create new ET
        AEM_Admin::instance();
    }
    if(class_exists( 'QA_Admin' )){
        require_once AEM_PLUGIN_PATH.'/inc/ae/appengine.php';
    }
    return AEM_Setting_Page::instance();
}

/**
 * Get option
 *
 * @param      $name
 * @param bool $default
 *
 * @return bool|mixed
 */
function aem_get_option ( $name, $default = FALSE )
{
    $option = AEM_Option();
    return ( $option->$name != '' ) ? $option->$name : $default;
}
function aem_set_option ( $name, $value = FALSE )
{
    $option = AEM_Option();
    $option->$name = $value;
    $option->save();
}

/**
 * Get option instace
 *
 * @return AEM_Option|null
 */
function AEM_Option ()
{
    return AEM_Option::instance();
}