<?php
/**
 * @author Quang Dat
 * @copyright 2015
 * @package AppEngine Payment
 */
/*
Plugin Name: AE PayFast
Plugin URI: http://enginethemes.com/
Description: Integrates the PayFast payment gateway to your DirectoryEngine, FreelanceEngine site
Version: 1.1
Author: EngineThemes
Author URI: http://enginethemes.com/
License: GPLv2
Text Domain: enginetheme
*/
function ae_payfast_require_plugin_file(){
   // require_once dirname(__FILE__) . '/update.php';
}
require_once dirname(__FILE__) . '/lib.php';
add_action('after_setup_theme', 'ae_payfast_require_plugin_file');
//setup admin option
// setup page for setting, add filter for payf setting
add_filter('ae_admin_menu_pages', 'ae_payf_add_settings');
function ae_payf_add_settings($pages) {
    $sections = array();
    $options = AE_Options::get_instance();
    /**
     * ae fields settings
     */
    $sections = array(
        'args' => array(
            'title' => __("PayFast API", ET_DOMAIN) ,
            'id' => 'payf_field',
            'icon' => 'F',
            'class' => ''
        ) ,

        'groups' => array(
            array(
                'args' => array(
                    'title' => __("PayFast API", ET_DOMAIN) ,
                    'id' => 'payf-secret-key',
                    'class' => '',
                    'desc' => '',
                    'name' => 'payf'
                ) ,
                'fields' => array(
                    array(
                        // 10000100
                        'id' => 'merchant_id',
                        'type' => 'text',
                        'label' => __("Merchant ID", ET_DOMAIN) ,
                        'name' => 'merchant_id',
                        'class' => ''
                    ) ,
                    array(
                        'id' => 'merchant_key',
                        'type' => 'text',
                        'label' => __("Merchant Key", ET_DOMAIN) ,
                        'name' => 'merchant_key',
                        'class' => ''
                    ),
                    array(
                        'id' => 'salt_passphrase',
                        'type'=>'text',
                        'label' => 'Salt Passphrase',
                        'name' =>'salt_passphrase',
                        'class'=>''
                    )
                )
            )
        )
    );
    // khoi tao section
    $temp = new AE_section($sections['args'], $sections['groups'], $options);

    // call field appengine AE_container in Appengine/Fields/Container.php
    $payf_setting = new AE_container(array(
        'class' => 'field-settings',
        'id' => 'settings',
    ) , $temp, $options);
    // Create infomation in this pages the same setting
    // Hien thi thong tin giong nhu setting phan menu trong admin
    // Dua toan bo thong tin vao trong args
    // Chuan bi du lieu truyen di
    $pages[] = array(
        'args' => array(
            'parent_slug' => 'et-overview',
            'page_title' => __('PayFast', ET_DOMAIN) ,
            'menu_title' => __('PAYFAST', ET_DOMAIN) ,
            'cap' => 'administrator',
            'slug' => 'ae-payf',
            'icon' => '$',
            'icon_class' => 'fa fa-inr',
            'desc' => __("Integrate the PayFast payment gateway to your site", ET_DOMAIN)
        ) ,
        'container' => $payf_setting
    );

    return $pages;
}

// add support gateway

add_filter('ae_support_gateway', 'ae_payf_add_support');
function ae_payf_add_support($gateways) {
    $gateways['payf'] = 'PayFast';
    return $gateways;
}

//add button front end
add_action('after_payment_list', 'ae_payf_render_button');
function ae_payf_render_button() {
    $payf_info = ae_get_option('payf');
    if($payf_info['merchant_id'] == "" || $payf_info['merchant_key'] == "" ){
        return false;
    }
?>
    <li>
        <span class="title-plan payf-payment" data-type="payf">
            <?php
    _e("PayFast", ET_DOMAIN); ?>
            <span><?php
    _e("Send your payment to our PayFast account", ET_DOMAIN); ?></span>
        </span>
        <a href="#" id="" class="btn btn-submit-price-plan select-payment" data-type="payf"><?php
    _e("Select", ET_DOMAIN); ?></a>

    </li>

<?php
    
    //end if
}

// setup thanh toan

add_filter('ae_setup_payment', 'ae_payf_setup_payment', 10, 3);
function ae_payf_setup_payment($response, $paymentType, $order) {
    global $current_user;
        // kiem tra payment gateway
    if ($paymentType == 'PAYF') {
        $test_mode = ET_Payment::get_payment_test_mode();
        $payf_url = 'https://www.payfast.co.za/eng/process/?';
        if ($test_mode) {
            $payf_url = 'https://sandbox.payfast.co.za/eng/process/?';
        }
        //get info order
        $order_pay = $order->generate_data_to_pay();
        $orderId = $order_pay['product_id'];
        $amount = $order_pay['total'];
        $currency = $order_pay['currencyCodeType'];
        $pakage_info = array_pop($order_pay['products']);
        $pakage_name = $pakage_info['NAME'];
        $payf_info = ae_get_option('payf');

        //get link callback
        $return_url = et_get_page_link('process-payment', array(
                        'paymentType' => 'payf',
                        'return' => "1"
                    )) ;
        $cancel_url = et_get_page_link('process-payment', array(
                        'paymentType' => 'payf',
                        'return' => "2"
                    )) ;
        $notify_url = et_get_page_link('process-payment', array(
                        'paymentType' => 'payf',
                        'post_back' => "2",
                        'id_order' => $order_pay['ID']
                    )) ;
        $data = array(
            "merchant_id" => $payf_info['merchant_id'],
            "merchant_key" => $payf_info['merchant_key'],
            "return_url" => $return_url,
            "cancel_url" => $return_url,
            "notify_url" => $notify_url,
            "m_payment_id" => $orderId,
            "amount" => $amount,
            "item_name" => $pakage_name,
            );
        $pfOutput ="";
        foreach( $data as $key => $val )
        {
          if(!empty($val))
          {
            $pfOutput .= $key .'='. urlencode( trim( $val ) ) .'&';
          }
        }
      // Remove last ampersand
      $getString = substr( $pfOutput, 0, -1 );

      if( isset( $payf_info['salt_passphrase'] ) )
      {
          $getString .= '&passphrase='.$payf_info['salt_passphrase'];
      }
      //MD5 string used to generate the signature
      $signature = md5($getString);
      $pfOutput .= 'signature='.$signature;
      $outurl = $payf_url . $pfOutput;
        if ($payf_info['merchant_id'] || $payf_info['merchant_key']) {
            $response = array(
                // get gia tri chuan bi submit
                'success' => true,
                'data' => array(
                    'url' => $outurl,
                    'ACK' => true,
                ) ,
                'paymentType' => 'PAYF'
            );



        } else {
            $response = array(
                'success' => false,
                'data' => array(
                    'url' => site_url('post-place') ,
                    'ACK' => false
                )
            );
        }
    }
    return $response;
}

add_filter('ae_process_payment', 'ae_payf_process_payment', 10, 2);
function ae_payf_process_payment($payment_return, $data) {
    $paymenttype = $data['payment_type'];
    $order = $data['order'];
    if($paymenttype == 'payf' && isset($_GET['post_back'])){
       $id_order = $_GET['id_order'];
        $validate = validate_signature();
        validate_host();
        
        update_post_meta($id_order, 'payf_status',$validate['payment_status']);
         
        exit();
    }if ($paymenttype == 'payf' && isset($_GET['return']) && $_GET['return'] == '1' ) {
       $status = get_post_meta ( $data['order_id'],  'payf_status', true);
    switch ($status) {
        case 'COMPLETE':
            $payment_return = array(
                'ACK' => true,
                'payment' => 'payf',
                'payment_status' => 'complete'
                );
                $order->set_status('publish');
                $order->update_order();
            break;
        case 'FAILED':
            $payment_return = array(
                'ACK' => false,
                'payment' => 'payf',
                'payment_status' => 'complete'
                );
            break;
        case 'PENDING':
            $payment_return = array(
                'ACK' => false,
                'payment' => 'payf',
                'payment_status' => 'complete'
                );
            break;
        default:
            $payment_return = array(
                'ACK' => false,
                'payment' => 'payf',
                'payment_status' => 'fail'
                );
            break;
      }

    } if ($paymenttype == 'payf' && isset($_GET['return']) && $_GET['return'] == '2' ) {
        $payment_return = array(
                'ACK' => false,
                'payment' => 'payf',
                'payment_status' => 'fail'
                );
    }
    return $payment_return;
}
/**
 * hook to add translate string to plugins
 * @param Array $entries Array of translate entries
 * @since 1.0
 * @author Dakachi
 */
add_filter( 'et_get_translate_string', 'ae_payf_add_translate_string' );
function ae_payf_add_translate_string ($entries) {
    $lang_path = dirname(__FILE__).'/lang/default.po';
    if(file_exists($lang_path)) {
        $pot        =   new PO();
        $pot->import_from_file($lang_path, true );

        return  array_merge($entries, $pot->entries);
    }
    return $entries;
}