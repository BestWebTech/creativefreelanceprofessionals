<?php

/**
 * @author Tong Quang Dat
 * @copyright 2015
 * @package AppEngine Payment
 */

/*
Plugin Name: AE Sagepay
Plugin URI: http://enginethemes.com/
Description: Integrates the Sagepay payment gateway to your DirectoryEngine site Freelanceengine
Version: 1.1
Author: enginethemes
Author URI: http://enginethemes.com/
License: GPLv2
Text Domain: enginetheme
*/
function ae_sagepage_require_plugin(){
    require_once dirname(__FILE__) . '/update.php';
}
add_action('after_setup_theme', 'ae_sagepage_require_plugin');
/**
 * include lib sagepay
 */
include_once dirname(__FILE__) . '/lib/sagepay.php';

//setup admin option
add_filter('ae_admin_menu_pages', 'ae_sagepay_add_settings');
function ae_sagepay_add_settings($pages) {
    $sections = array();
    $options = AE_Options::get_instance();

    // $api_link = " <a class='find-out-more' target='_blank' href='https://dashboard.paymill.com/account/apikeys' >" . __("Find out more", ET_DOMAIN) . " <span class='icon' data-icon='i' ></span></a>";


    /**
     * ae fields settings
     */
    $sections = array(
        'args' => array(
            'title' => __("Sagepay Settings", ET_DOMAIN) ,
            'id' => 'sagepay_field',
            'icon' => 'F',
            'class' => ''
        ) ,

        'groups' => array(
            array(
                'args' => array(
                    'title' => __("Sagepay gateway", ET_DOMAIN) ,
                    'id' => 'sagepay-gateway',
                    'class' => '',
                    'desc' => __('FOR TEST MODE <br/><b>Vendor name: protxross <br/> Encrypt pass : TPjs72eMz5qBnaTa</b>', ET_DOMAIN) ,
                    'name' => 'sagepay_setting'
                ) ,
                'fields' => array(
                    array(
                        'id' => 'title-name',
                        'type' => 'text',
                        'label' => __("Title", ET_DOMAIN) ,
                        'name' => 'title',
                        'class' => '',
                        'default' => 'Sagepay'
                    ) ,
                    array(
                        'id' => 'desc-name',
                        'type' => 'text',
                        'label' => __("Description", ET_DOMAIN) ,
                        'name' => 'desc',
                        'class' => '',
                        'default' => 'Send your payment to our Sagepay gateway'
                    ) ,
                    array(
                        'id' => 'vendor-name',
                        'type' => 'text',
                        'label' => __("Vendor Name", ET_DOMAIN) ,
                        'name' => 'vendor_name',
                        'class' => ''
                    ) ,
                    array(
                        'id' => 'encrypt_pass',
                        'type' => 'text',
                        'label' => __('Encrypt Password') ,
                        'name' => 'encrypt_pass',
                        'class' => ''
                    )
                )
            )
        )
    );

    $temp = new AE_section($sections['args'], $sections['groups'], $options);

    $sagepay_setting = new AE_container(array(
        'class' => 'field-settings',
        'id' => 'settings',
    ) , $temp, $options);

    $pages[] = array(
        'args' => array(
            'parent_slug' => 'et-overview',
            'page_title' => __('Sagepay gateway', ET_DOMAIN) ,
            'menu_title' => __('SAGEPAY', ET_DOMAIN) ,
            'cap' => 'administrator',
            'slug' => 'ae_sagepay',
            'icon' => '$',
            'icon_class' => 'fa fa-money',
            'desc' => __("Integrate the Sagepay payment gateway to your site", ET_DOMAIN)
        ) ,
        'container' => $sagepay_setting
    );

    return $pages;
}

/**
 * init template modal
 */
add_action('wp_footer', 'init_template');
function init_template() {
    $is_page_submit = apply_filters( 'ae_is_page_submit', is_page_template('page-post-place.php') || is_page_template('page-submit-project.php') );
    if ($is_page_submit) {
        include_once dirname(__FILE__) . '/form-template.php';
    }
}

/**
 * init script
 */
add_action('wp_print_scripts', 'add_scripts');
function add_scripts() {
    if (is_page_template('page-post-place.php') || is_page_template('page-submit-project.php')) {
        wp_enqueue_script('ae_sagepay', plugin_dir_url(__FILE__) . 'assets/sagepay.js', array(
            'underscore',
            'backbone',
            'appengine'
        ) , '1.0', true);
        wp_enqueue_script('ae_country', plugin_dir_url(__FILE__) . 'assets/country.js', '', '1.0');
        wp_enqueue_script('ae_state', plugin_dir_url(__FILE__) . 'assets/state.js', '', '1.0');
        wp_localize_script('ae_sagepay', 'ae_sagepay', array(
            'currency' => ae_get_option('currency') ,
            'empty_field_msg' => __('Please! Insert first name.', ET_DOMAIN) ,
            'incorect_email' => __('The email invalid.', ET_DOMAIN) ,
            'empty_phone' => __('Please! Insert phone number.', ET_DOMAIN) ,
            'not_emty' => __('Please! Insert request fields', ET_DOMAIN) ,
            'not_emty_state' => __('Sorry, the following problems were found: Billing State Code (U.S. only) is illegal', ET_DOMAIN) ,
        ));
    }
}
add_action('ae_payment_script', 'ae_sage_add_scripts');
function ae_sage_add_scripts() {
    wp_enqueue_script('ae_sagepay', plugin_dir_url(__FILE__) . 'assets/sagepay.js', array(
        'underscore',
        'backbone',
        'appengine'
    ) , '1.0', true);
    wp_enqueue_script('ae_country', plugin_dir_url(__FILE__) . 'assets/country.js', '', '1.0');
    wp_enqueue_script('ae_state', plugin_dir_url(__FILE__) . 'assets/state.js', '', '1.0');
    wp_localize_script('ae_sagepay', 'ae_sagepay', array(
        'currency' => ae_get_option('currency') ,
        'empty_field_msg' => __('Please! Insert first name.', ET_DOMAIN) ,
        'incorect_email' => __('The email invalid.', ET_DOMAIN) ,
        'empty_phone' => __('Please! Insert phone number.', ET_DOMAIN) ,
        'not_emty' => __('Please! Insert request fields', ET_DOMAIN) ,
        'not_emty_state' => __('Sorry, the following problems were found: Billing State Code (U.S. only) is illegal', ET_DOMAIN) ,
    ));
}
/**
 * Render button
 */
add_action('after_payment_list', 'ae_sagepay_render_button');
function ae_sagepay_render_button() {

    $sagepay_setting = ae_get_option('sagepay_setting');
    if (!$sagepay_setting['vendor_name'] || !$sagepay_setting['encrypt_pass']) return false;

    $title = ($sagepay_setting['title']) ? $sagepay_setting['title'] : 'Sagepay';
    $desc = ($sagepay_setting['desc']) ? $sagepay_setting['desc'] : 'Send your payment to our Sagepay gateway';
?>
    <li>
        <span class="title-plan sagepay-payment" data-type="sagepay">
            <?php echo $title; ?>
            <span><?php echo $desc; ?></span>
        </span>
        <a href="#" class="btn btn-submit-price-plan other-payment" data-type="sagepay"><?php
    _e("Select", ET_DOMAIN); ?></a>
    <div class="clearfix"></div>
    </li>

<?php
}

/**
 * setup payment
 * $vendortxcode;
 */
add_filter('ae_setup_payment', 'ae_setup_sagepay', 10, 3);
function ae_setup_sagepay($response, $paymentType, $order) {
    if ($paymentType == 'SAGEPAY') {

        /**
         * init setting
         */
        $order_pay = $order->generate_data_to_pay();
        $order_id = $order_pay['ID'];
        $curency = ae_get_option('currency');
        $request = $_REQUEST;
        $sagepay_setting = ae_get_option('sagepay_setting');
        $testmode = ET_Payment::get_payment_test_mode();
        $link = "https://live.sagepay.com/gateway/service/vspform-register.vsp";
        if ($testmode) {
            $link = 'https://test.sagepay.com/gateway/service/vspform-register.vsp ';
        }

        /**
         * init lib sagepay
         */
        $sage_init = new SagepayUtil;
        $sage_common = new SagepayCommon;

        if ($request['sagepay_lastname'] || $request['sagepay_firstname'] || $request['sagepay_billingadress']) {
            global $current_user; $user_email;
            get_currentuserinfo();

            $products = array_values($order_pay['products']);

            /**
             * declare varible for Crypt
             */

            $vendortxcode = $sage_common->vendorTxCode($order_id, 'PAYMENT', $sagepay_setting['vendor_name']);
            $amount = $order_pay['total'];
            $curency_code = $curency['code'];
            $description = $products[0]['NAME'];
            $customer_email = $current_user->user_email;

            // $admin_email    = $user_email;
            $surname = $request['sagepay_lastname'];
            $firstname = $request['sagepay_firstname'];
            $address = $request['sagepay_billingadress'];
            $city = $request['sagepay_billingcity'];
            $country = $request['sagepay_country'];
            $state = $request['sagepay_state'];
            $encrypt_pass = $sagepay_setting['encrypt_pass'];
             //'TPjs72eMz5qBnaTa';
            $postcode = $request['sagepay_postcode'];
            $successURL = et_get_page_link('process-payment', array(
                'paymentType' => 'sagepay'
            ));
            $failureURL = et_get_page_link('process-payment', array(
                'paymentType' => 'sagepay'
            ));

            $string = 'VendorTxCode=' . $vendortxcode . '&Amount=' . $amount . '&Currency=' . $curency_code . '&Description=' . $description . '&CustomerEMail=' . $customer_email . '&VendorEMail=&SendEMail=1&BillingSurname=' . $surname . '&BillingFirstnames=' . $firstname . '&BillingAddress1=' . $address . '&BillingCity=' . $city . '&BillingPostCode=' . $postcode . '&BillingCountry=' . $country . '&ApplyAVSCV2=0&Apply3DSecure=0&BillingState=' . $state . '&customerEmail=' . $customer_email . '&DeliveryFirstnames=' . $firstname . '&DeliverySurname=' . $surname . '&DeliveryAddress1=' . $address . '&DeliveryCity=' . $city . '&DeliveryCountry=' . $country . '&DeliveryState=' . $state . '&DeliveryPostCode=' . $postcode . '&SuccessURL=' . $successURL . '&FailureURL=' . $failureURL . '';

            /**
             * Encrypt order details using base64 and the secret key from the settings.
             */
            $Crypt = $sage_init->encryptAes($string, $encrypt_pass);

            $response = array(
                'success' => true,
                'link' => $link,
                'vendor' => $sagepay_setting['vendor_name'],
                'crypt' => $Crypt,
                'products' => $products,
            );
        } else {
            $response = array(
                'success' => false,
                'mes' => __('Request fields is empty.', ET_DOMAIN) ,
            );
        }
    }
    return $response;
}
/**
 * process sagepay return
 * @param Array $payment_return
 * @param Array $data
 *                - order data
 *                - payment_type
 * @since 1.0
 * @author Dat
 */
add_filter('ae_process_payment', 'ae_sagepay_process_payment', 10, 2);
function ae_sagepay_process_payment($payment_return, $data) {

    //init class, varible
    $sage_init = new SagepayUtil;
    $sage_common = new SagepayCommon;
    $sagepay_setting = ae_get_option('sagepay_setting');

    //$encrypt_pass   = 'TPjs72eMz5qBnaTa';
    $payment_type = $data['payment_type'];
    $order = $data['order'];

    if ($payment_type == 'sagepay') {
        $crypt = $_REQUEST['crypt'];

        /**
         * Decrypt and cover to array
         */
        $decrypt = $sage_init->decryptAes($crypt, $sagepay_setting['encrypt_pass']);
        $decryptArr = $sage_init->queryStringToArray($decrypt);

        if (!$decrypt && !empty($decryptArr) && ($decryptArr['Status'] != 'NOTAUTHED' || $decryptArr['Status'] != "OK")) {
            $payment_return = array(
                'ACK' => false,
                'payment' => 'sagepay',
                'payment_status' => 'fail',
                'msg' => __('Invalid crypt.', ET_DOMAIN)
            );
        } elseif ($decryptArr['Status'] == 'NOTAUTHED' || $decryptArr['Status'] == "OK") {
            $payment_return = array(
                'ACK' => true,
                'payment' => 'sagepay',
                'payment_status' => 'Completed',

                //'decrypt' => $decrypt

            );
            $order->set_status('publish');
            $order->update_order();
        }
    }
    return $payment_return;
}
?>