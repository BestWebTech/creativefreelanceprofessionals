<?php
/**
 * Created by PhpStorm.
 * User: Jack Bui
 * Date: 11/16/2015
 * Time: 3:51 PM
 */
class FRE_Credit_Employer extends FRE_Credit_Users{
    public static $instance;
    /**
     * getInstance method
     *
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * the constructor of this class
     *
     */
    public  function __construct(){
        //-----------------------------
    }
    /**
      * unit function of this class
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function init(){
        //filter gateway to payment method
        $this->add_filter( 'ae_support_gateway', 'fre_credit_support' );
        $this->add_action('after_payment_list', 'fre_credit_render_button');
        $this->add_action('wp_footer', 'fre_credit_add_modal');
        $this->add_filter('ae_setup_payment', 'fre_credit_setup_payment', 10, 3);
        $this->add_filter( 'ae_process_payment', 'fre_credit_process_payment', 10 ,2 );
    }
    /**
      * add credit gateway
      *
      * @param array $gateways
      * @return array $gateways
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function fre_credit_support($gateways){
        $gateways['frecredit'] = 'frecredit';
        return $gateways;
    }
    /**
      * render button payment
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function fre_credit_render_button(){
        $page = ae_get_option('fre_credit_deposit_page_slug', false);
        if( !$page || !is_page($page) ) {
            fre_credit_template_payToSubmitProject_button();
        }
    }
    /**
      * add modal html
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function fre_credit_add_modal(){
        include_once dirname(__FILE__) . '/template/form-template.php';
    }
    /**
      * submit payment
      *
      * @param array $order
      * @return array $response
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function fre_submit_project_payment($order){
        global $user_ID;
        $ae_package = AE_Package::get_instance();
        $response = array(
            'success'=> false,
            'msg'=> __("Please select a package plan!", ET_DOMAIN)
        );
        $package = $ae_package->get_pack($order['payment_package'], 'pack');
        if( $package ){
            $wallet = fre_credit_convert_wallet($package->et_price);
            $result = FRE_Credit_Users()->checkBalance($user_ID, $wallet);
            if( $result >= 0 ){
                $this->updateUserBalance($user_ID, $result);
                $response = array(
                    'success'=> true,
                    'msg'=> __("Payment success!", ET_DOMAIN)
                );
            }
            else{
                $response = array(
                    'success'=> false,
                    'msg'=> __("You don't have enough money in your wallet!", ET_DOMAIN)
                );
            }
        }
        return $response;
    }
    /**
      * filter setup payment
      *
      * @param array $response
     * @param string $paymentType
     * @param array $order
      * @return array $response
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function fre_credit_setup_payment($response, $paymentType, $order){
        if ($paymentType == 'FRECREDIT') {
            $resp = array(
                'success' => false,
                'paymentType' => $paymentType,
                'msg' => __('Please enter a valid secure code!', ET_DOMAIN)
            );
            global $user_ID;
            if(!isset($_REQUEST['secureCode']) || empty($_REQUEST['secureCode'])){
                return $resp;
            }
            else{
                $flag = $this->checkSecureCode($user_ID, $_REQUEST['secureCode']);
                if( !$flag ){
                    return $resp;
                }
            }
            $order_pay = $order->generate_data_to_pay();

            $result = $this->fre_submit_project_payment($order_pay);
            if( $result['success'] ){
                $id = time();
                $token = md5($id);
                $order->set_payment_code($token);
                $order->set_payer_id($id);
                $order->update_order();
                $returnURL = et_get_page_link('process-payment', array(
                    'paymentType' => 'frecredit',
                    'token' => $token
                ));
                $response = array(
                    'success' => true,
                    'data' => array(
                        'url' => $returnURL
                    ) ,
                    'paymentType' => 'frecredit'
                );
                $history_obj = array(
                    "amount" => (float)$order_pay['total'], // amount in cents
                    "currency" => fre_credit_get_payment_currency(),
                    "destination" => '',
                    'commission_fee' => 0,
                    "statement_descriptor" => __(" to post a project", ET_DOMAIN),
                    'source_transaction' => '',
                    'post_title'=> __('Paid', ET_DOMAIN),
                    'history_type'=> 'charge',
                    'payment' => $_REQUEST['ID']
                );
                
                $history_obj['status'] = 'recieved';
                FRE_Credit_History()->saveHistory($history_obj);
            }
            else{
                $response = array(
                    'success' => false,
                    'paymentType' => $paymentType,
                    'msg' => $result['msg']
                );
            }
        }
        return $response;
    }
    /**
      * filter process payment
      *
      * @param void
      * @return array $paymentReturn
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function fre_credit_process_payment( $payment_return, $data ){
        $payment_type = $data['payment_type'];
        $order = $data['order'];
        if( $payment_type == 'frecredit') {
            if( isset($_REQUEST['token']) &&  $_REQUEST['token'] == $order->get_payment_code() ) {
                $payment_return	=	array (
                    'ACK' 			=> true,
                    'payment'		=>	'FRE-CREDIT',
                    'payment_status' =>'Completed'

                );
                $order->set_status ('publish');
                $order->update_order();
                update_post_meta($data['ad_id'], 'status', 'completed');
            } else {
                $payment_return	=	array (
                    'ACK' 			=> false,
                    'payment'		=>	'FRE-CREDIT',
                    'payment_status' =>'Completed',
                    'msg' 	=> __('FrE credit payment method false.', ET_DOMAIN)

                );
                update_post_meta($order['order_id'], 'status', 'failed');
            }
        }
        return $payment_return;
    }

}