<?php
/**
 * Created by PhpStorm.
 * User: Jack Bui
 * Date: 11/16/2015
 * Time: 11:27 AM
 */
class FRE_Credit_Users extends AE_Base{
    public static $instance;
    public $secureCode;
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
     *
     */
    public  function __construct(){

    }
    /**
      * init
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function init(){
        $this->add_filter( 'ae_pack_post_types', 'fre_credit_pack_post_type' );
        $this->add_action( 'ae_select_process_payment', 'fre_credit_process_payment', 10, 2 );
        $this->add_action( 'save_post', 'fre_credit_cash_approved', 10, 2 );
        $this->add_action('wp_ajax_fre-withdraw-sync', 'withdraw');
        $this->add_action('wp_ajax_fre-credit-get-balance-info', 'getBlanceInfo');
        $this->add_action('wp_ajax_fre-credit-get-profile-info', 'getProfileInfo');
        $this->add_action('wp_ajax_fre-credit-update-email-paypal', 'updatePaypalCredit');
        $this->add_action('wp_ajax_fre-credit-update-bank', 'updateBank');
        $this->add_action('wp_ajax_fre-credit-request-secure-code', 'requestSecureCode');
    }

    /**
      * Get data of user
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author ThanhTu
      */
    public function getProfileInfo(){
        global $user_ID, $ae_post_factory, $current_user;
        $ae_users  = AE_Users::get_instance();
        $user_data = $ae_users->convert($current_user->data);
        $user_data->email_paypal = get_user_meta($user_ID, 'email-paypal-credit', true);
        $user_data->banking_info = get_user_meta($user_ID, 'bank-info-credit', true);
        wp_send_json(array(
            'success'=> true,
            'data' => $user_data,
            'msg'=> __('Successful!', ET_DOMAIN),
        ));
    }
    
    /**
      * Update banking information
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author ThanhTu
      */
    public function updateBank(){
        global $user_ID;
        $request = $_REQUEST;
        $resp = array(
            'success'=> false,
            'msg'=> __('Please enter a valid secure code!', ET_DOMAIN)
        );
        $result = $this->checkSecureCode($user_ID, $request['secure_core']);
        if( !$result ){
            wp_send_json($resp);
        }

        $bank_info = array(
            'benficial_owner' => $request['benficial_owner'],
            'account_number' => $request['account_number'],
            'banking_information' => $request['banking_information'],
        );

        $result = update_user_meta($user_ID, 'bank-info-credit', $bank_info);
        
        if($result){
          wp_send_json(array(
                  'success'=> true,
                  'data' => $result,
                  'msg'=> __('Update successful!', ET_DOMAIN),
              ));
        }else{
          wp_send_json(array(
              'success'=> false,
              'msg'=> __('Error!', ET_DOMAIN),
          ));
        }

    }
    /**
      * Update Email Paypal in Credit
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author ThanhTu
      */
    public function updatePaypalCredit(){
        global $user_ID;
        $request = $_REQUEST;
        $resp = array(
            'success'=> false,
            'msg'=> __('Please enter a valid secure code!', ET_DOMAIN)
        );
        $result = $this->checkSecureCode($user_ID, $request['secure_core']);
        if( !$result ){
            wp_send_json($resp);
        }
        
        $result = update_user_meta($user_ID, 'email-paypal-credit', $request['paypal']);
        if($result){
          wp_send_json(array(
                  'success'=> true,
                  'data' => $result,
                  'msg'=> __('Update successful!', ET_DOMAIN),
              ));
        }else{
          wp_send_json(array(
              'success'=> false,
              'msg'=> __('Error!', ET_DOMAIN),
          ));
        }
    }
    /**
      * set user wallet
      *
      * @param integer $user_id;
      * @param FRE_Credit_Wallet $fre_user_wallet
      * @param string $type is freezable and available
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function setUserWallet($user_id, $fre_user_wallet, $type = 'available'){
        if( null == $fre_user_wallet || empty($fre_user_wallet) ){
            $fre_user_wallet = new FRE_Credit_Wallet();
        }
        if( $user_id ) {
            if( $type == 'freezable' ) {
                update_user_meta($user_id, 'fre_user_wallet_freezable', $fre_user_wallet);
            }
            else{
                update_user_meta($user_id, 'fre_user_wallet', $fre_user_wallet);
            }
        }
    }
    /**
      * get user wallet
      *
      * @param integer $user_id
     * @param string $type is freezable and available
      * @return FRE_Credit_Wallet user's wallet
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function getUserWallet($user_id, $type = 'available' ){
        if( $type == 'freezable' ){
            $user_wallet = get_user_meta($user_id, 'fre_user_wallet_freezable', true);
        }
        else{
            $user_wallet = get_user_meta($user_id, 'fre_user_wallet', true);
        }
        if( empty($user_wallet) ){
            $user_wallet = new FRE_Credit_Wallet();
        }
        return $user_wallet;
    }
    /**
      * update user balance only
      *
      * @param integer $user_id
      * @param $balance
      * @param string $type is freezable and available
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function updateUserBalance($user_id, $balance, $type = 'available'){
        $user_wallet = $this->getUserWallet($user_id, $type);
        $user_wallet->setBalance($balance);
        $this->setUserWallet($user_id, $user_wallet, $type);
    }
    /**
      * check user balance
      *
      * @param integer $user_id
      * @param FRE_Credit_Wallet $number
      * @param string $type is freezable and available
      * @return float if user wallet is smaller user $number
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function checkBalance( $user_id, $number, $type = 'available'  ){
        if( $type == 'freezable' ) {
            $user_wallet = $this->getUserWallet($user_id, 'freezable');
        }
        else{
            $user_wallet = $this->getUserWallet($user_id);
        }
        if( empty($user_wallet) ){
            $user_wallet = new FRE_Credit_Wallet();
        }
        if( null == $number || empty($number) ){
            $number = new FRE_Credit_Wallet();
        }
        $credit_exchange = FRE_Credit_Currency_Exchange::getInstance();
        $num = $credit_exchange->exchange($number->balance, $number->currency, $user_wallet->currency);
        return (float)($user_wallet->balance - $num);
    }
    /**
     * Generate secure code
     *
     * @param integer number of code
     * @return string secureCode
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function generateSecureCode( $number = 6 ){
        return wp_generate_password($number);
    }
    /**
      * set secure code for current user
      *
     * @param integer $user_id
      * @param string $code
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function setSecureCode($user_id, $code ){
        if( null == $code || empty($code) ){
            $code = '';
        }
        $this->secureCode = md5($code);
        update_user_meta($user_id, 'fre_credit_secure_code', $this->secureCode);
    }
    /**
      * get secureCode
      *
      * @param integer $user_id
      * @return string secure code after md5
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function getSecureCode($user_id){
        $secure_code = get_user_meta($user_id, 'fre_credit_secure_code', true);
        return $secure_code;
    }
    /**
      * Check user's secure code
      *
      * @param integer $user_id
      * @param string $code
      * @return boolean true if this is user's secure code
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function checkSecureCode($user_id, $code){
        $secureCode = $this->getSecureCode($user_id);
        if( $secureCode == md5($code) ){
            return true;
        }
        return false;
    }
    /**
      * charge
      *
      * @param array $charge_obj
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function charge($charge_obj = array()){
        global $user_ID;
        $default = array(
            'amount' => 0,
            'currency' => fre_credit_get_payment_currency(),
            'customer' => $user_ID,
            'history_type'=> 'charge',
            'status'=> 'completed'
        );
        $charge_obj = wp_parse_args($charge_obj, $default);
        $user_wallet = FRE_Credit_Users()->getUserWallet($charge_obj['customer']);
        $number = FRE_Credit_Currency_Exchange()->exchange($charge_obj['amount'], $charge_obj['currency'], $user_wallet->currency);
        $wallet = new FRE_Credit_Wallet($number, $user_wallet->currency);
        $result = FRE_Credit_Users()->checkBalance($charge_obj['customer'], $wallet);
        if( $result >= 0 ){
            $this->updateUserBalance($user_ID, $result);
            $froze_balance = FRE_Credit_Users()->getUserWallet($user_ID, 'freezable');
            $froze_balance->balance +=  $wallet->balance;
            $this->updateUserBalance($user_ID, $froze_balance->balance, 'freezable');
            $charge_id = FRE_Credit_History()->saveHistory($charge_obj);
            $response = array(
                'success'=> true,
                'msg'=> __("Payment success!", ET_DOMAIN),
                'id'=> $charge_id
            );
        }
        else{
            $response = array(
                'success'=> false,
                'msg'=> __("You don't have enough money in your wallet!", ET_DOMAIN)
            );
        }
        return $response;
    }
    /**
      * transfer money
      *
      * @param array $transfer_obj
      * @return array $response
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function transfer( $transfer_obj ){
        $default = array(
            "amount" => 0, // amount in cents
            "currency" => fre_credit_get_payment_currency(),
            "destination" => '',
            "source_transaction" => '',
            "commission_fee"=> 0,
            "statement_descriptor" => ''
        );
        $transfer_obj = wp_parse_args($transfer_obj, $default);
        $user_wallet = FRE_Credit_Users()->getUserWallet($transfer_obj['destination']);
        $number_transfer = FRE_Credit_Currency_Exchange()->exchange($transfer_obj['amount'], $transfer_obj['currency'], $user_wallet->currency);
        $number_charge = FRE_Credit_Currency_Exchange()->exchange((float)$transfer_obj['source_transaction']->amount, $transfer_obj['currency'], $user_wallet->currency);
        $wallet = new FRE_Credit_Wallet( $number_charge, $transfer_obj['currency'] );
        $charge = $transfer_obj['source_transaction'];
        if( $charge && !empty($charge) ) {
            $result = FRE_Credit_Users()->checkBalance($charge->post_author, $wallet, 'freezable');
            $result_wallet = new FRE_Credit_Wallet($result);
            if( $result < 0 ){
                $response = array(
                    'success'=> false,
                    'msg'=> __("You don't have enough money in you wallet!", ET_DOMAIN)
                );
                return $response;
            }
            FRE_Credit_Users()->setUserWallet($charge->post_author, $result_wallet, 'freezable');
            $user_wallet->balance += ( $number_transfer - $transfer_obj['commission_fee'] );
            FRE_Credit_Users()->setUserWallet($transfer_obj['destination'], $user_wallet);
            $transfer_obj['history_type'] = 'transfer';
            $transfer_obj['status'] = 'completed';
            FRE_Credit_History()->saveHistory($transfer_obj);
            $response = array(
                'success'=> true,
                'msg'=> __("Success!", ET_DOMAIN)
            );
        }
        else{
            $response = array(
                'success'=> false,
                'msg'=> __("There isn't any charge for this transferring!", ET_DOMAIN)
            );
        }
        return $response;
    }
    /**
      * refund money
      *
      * @param integer $charge_id
      * @return array $response
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function refund( $charge_id ){
        $charge = FRE_Credit_History()->retrieveHistory($charge_id);
        if( $charge && !empty($charge) ){
            $user_froze_wallet = FRE_Credit_Users()->getUserWallet($charge->post_author, 'freezable');
            $user_wallet = FRE_Credit_Users()->getUserWallet($charge->post_author);
            $wallet = new FRE_Credit_Wallet($charge->amount, $charge->currency);
            $charge_exchange = FRE_Credit_Currency_Exchange()->exchange((float)$charge->amount, $charge->currency, $user_wallet->currency);
            $result = FRE_Credit_Users()->checkBalance($charge->post_author, $wallet, 'freezable');
            if( $result > 0 ){
                $user_froze_wallet->balance = $result;
                FRE_Credit_Users()->setUserWallet($charge->post_author, $user_froze_wallet, 'freezable');
                $user_wallet->balance += $charge_exchange;
                FRE_Credit_Users()->setUserWallet($charge->post_author, $user_wallet);
                $refund_obj = array(
                    'amount'=> $charge->amount,
                    'currency'=>$charge->currency,
                    'history_type'=> 'refund',
                    'status'=> 'completed'
                    );
                FRE_Credit_History()->saveHistory($refund_obj);
                $response = array(
                    'success'=> true,
                    'msg'=> __("Refund success!", ET_DOMAIN)
                );
            }
            else{
                $response = array(
                    'success'=> false,
                    'msg'=> __("There isn't any froze money for this refund!", ET_DOMAIN)
                );
            }
        }
        else{
            $response = array(
                'success'=> false,
                'msg'=> __("There isn't any charge for this refund!", ET_DOMAIN)
            );
        }
        return $response;
    }
    /**
      * deposit
      *
      * @param array $payment_return
     * @param array $data
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function fre_credit_process_payment($payment_return, $data ){
        extract($data);
        global $user_ID, $current_user;
        if (!$payment_return['ACK']) return false;
        $order_pay = $data['order']->get_order_data();
        if( isset($payment_return['payment_status']) ){
            $packs = AE_Package::get_instance();
            $sku = $order_pay['payment_package'];
            $pack = $packs->get_pack($sku, 'fre_credit_plan');
            if($pack != false){              
                $admin_email = fre_credit_get_admin_email();
                $subject = sprintf(__('%s just deposit on your website', ET_DOMAIN), $current_user->data->user_nicename );
                $message = fre_credit_get_deposit_email_content($pack->et_number_posts);
                $this->mailNotification($admin_email, $subject, $message, array('user_id'=>$user_ID) );
                $default = array(
                    "amount" => $pack->et_number_posts, // amount in cents
                    "currency" => fre_credit_get_payment_currency(),
                    "destination" => '',
                    "source_transaction" => '',
                    "commission_fee"=> 0,
                    "statement_descriptor" => '',
                    "history_type"=> 'deposit',
                    "post_title"=> 'Deposited',
                    "payment" => $payment_return['payment']
                );
                
                if( $payment_return['payment_status'] == 'Completed' && $payment_return['payment'] != 'cash' ){
                    if( isset( $pack->et_number_posts ) && (float)$pack->et_number_posts > 0 ){
                        $this->deposit($user_ID, $pack->et_number_posts);
                        $default['status'] = 'completed';
                        FRE_Credit_History()->saveHistory($default);
                        $payment_return['bid_msg'] = sprintf( __("You've successfully purchased %d .", ET_DOMAIN), $pack->et_number_posts);
                        return $payment_return;
                    }
                }
                if( $payment_return['payment'] == 'cash' ){
                    $default['status'] = 'pending';
                    $history_id = FRE_Credit_History()->saveHistory($default);
                    update_post_meta($order_pay['ID'], 'fre_credit_deposit_history', $history_id);
                }
            }
        }
        return $payment_return;
    }
    /**
      * deposit
      *
      * @param integer $user_id
      * @param float $number
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function deposit($user_id, $number){
        $user_wallet = $this->getUserWallet($user_id);
        $wallet = fre_credit_convert_wallet($number);
        $number = FRE_Credit_Currency_Exchange()->convertToUserCurrency($user_id, $wallet);
        $user_wallet->balance = $number + $user_wallet->balance;
        FRE_Credit_Users()->setUserWallet($user_id, $user_wallet);
    }
    /**
      * package post type
      *
      * @param array $pack_post_type
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function fre_credit_pack_post_type( $pack_post_type ){
        return wp_parse_args( array( 'fre_credit_plan' ), $pack_post_type );
    }
    /**
      * approve cash
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function fre_credit_cash_approved( $post_ID, $post )
    {
        global $user_ID;
        if( current_user_can('manage_options') ){
            if( $post->post_type == 'order' && $post->post_status == 'publish' ){
                $order = new AE_Order($post_ID);
                $order_pay = $order->get_order_data();
                if( isset($order_pay['payment'] ) && $order_pay['payment'] == 'cash' ){
                    $sku = $order_pay['payment_package'];
                    $packs = AE_Package::get_instance();
                    $pack = $packs->get_pack($sku, 'fre_credit_plan');
                    if( isset( $pack->et_number_posts ) && (int)$pack->et_number_posts > 0 ){
                        $this->deposit($post->post_author, (float)$pack->et_number_posts);
                        $history_id = get_post_meta($order_pay['ID'], 'fre_credit_deposit_history', true);
                        if( $history_id ){
                            $history = FRE_Credit_History()->retrieveHistory($history_id);
                            $history->history_status = 'completed';
                            update_post_meta($history->ID, 'history_status', 'completed');
                            update_post_meta($history->ID, 'user_balance', fre_price_format(FRE_Credit_Users()->getUserWallet($user_ID)->balance));
                        }
                    }
                }
            }
        }
    }
    /**
      * withdraw function
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function withdraw(){
        global $user_ID, $current_user;
        $request = $_REQUEST;
        $default = array(
            'amount'=> '',
            'payment_info'=> '',
            'secureCode'=> ''
        );
        $request = wp_parse_args($request, $default);
        $resp = array(
            'success'=> false,
            'msg'=> __('Please enter a valid secure code!', ET_DOMAIN)
        );
        $result = $this->checkSecureCode($user_ID, $request['secureCode']);
        if( !$result ){
            wp_send_json($resp);
        }
        $resp = array(
            'success'=> false,
            'msg'=> __('Please enter a valid amount and payment information!', ET_DOMAIN)
        );
        if( empty($request['amount']) || empty($request['payment_info']) ){
            wp_send_json($resp);
        }
        $resp = array(
            'success'=> false,
            'msg'=> __('Please enter a number greater than minimum withdraw', ET_DOMAIN)
        );
        $min = ae_get_option('fre_credit_minimum_withdraw', 0);
        if( (float)$request['amount'] < (float)$min ){
            wp_send_json($resp);
        }
        $wallet = fre_credit_convert_wallet($request['amount']);
        $result = $this->checkBalance($user_ID, $wallet );
        $resp = array(
            'success'=> false,
            'msg'=> __("You don't have enough money in your wallet!", ET_DOMAIN)
        );
        if( $result < 0 ){
            wp_send_json($resp);
        }
        $user_wallet = $this->getUserWallet($user_ID);
        $charge_obj = array(
            'amount' => (float)$request['amount'],
            'currency' => $user_wallet->currency,
            'customer' => $user_ID,
            'status'=> 'pending',
            'post_title'=> __('withdrew',ET_DOMAIN),
            'history_type'=> 'withdraw',
            'payment' => $request['payment_method']
        );
        $charge = $this->charge($charge_obj);
        if( !$charge['success'] ){
            wp_send_json($charge);
        }
        $post_title = sprintf(__('%s sent a request to withdraw %s ', ET_DOMAIN), $current_user->data->display_name, fre_price_format($request['amount']) );
        $content = $request['payment_info']. '<br>';
        if(!empty($request['payment_method'])){
            $payment_method = get_user_meta($user_ID, $request['payment_method'], true);
            if(!empty($payment_method)){
                $content .= __('<b>Payment Information</b><br>');
                if($request['payment_method'] == 'email-paypal-credit'){
                    $content .= sprintf(__('Paypal Account: %s', ET_DOMAIN),  $payment_method);
                }elseif($request['payment_method'] == 'bank-info-credit'){
                    $content .= sprintf(__('Benficial Owner: %s<br>', ET_DOMAIN), $payment_method['benficial_owner'] );
                    $content .= sprintf(__('Account number:  %s<br>', ET_DOMAIN), $payment_method['account_number'] );
                    $content .= sprintf(__('Banking Information: %s<br>', ET_DOMAIN), $payment_method['banking_information'] );
                }
            }
        }

        $withdraw = array(
            'post_title'=> $post_title,
            'post_type'=> 'fre_credit_withdraw',
            'post_status'=> 'pending',
            'post_content'=> $content,
            'post_author'=> $user_ID
        );
        
        $post = wp_insert_post($withdraw);
        if( $post ){
            update_post_meta($post, 'amount', $request['amount']);
            update_post_meta($post, 'currency', $user_wallet->currency);
            update_post_meta($post, 'charge_id', $charge['id']);
            $admin_email = fre_credit_get_admin_email();
            $subject = sprintf(__('User %s  has sent a withdraw request', ET_DOMAIN), $current_user->data->user_nicename );
            $message = fre_credit_get_withdraw_email_content($request['amount'], $content);
            $this->mailNotification($admin_email, $subject, $message, array('user_id'=>$user_ID) );
            wp_send_json(array(
                'success'=> true,
                'msg'=> __('Send request successful!', ET_DOMAIN),
                'data'=> fre_credit_balance_info($user_ID)
            ));
        }
        else{
            wp_send_json(array(
                'success'=> false,
                'msg'=> __('Send request failed!', ET_DOMAIN)
            ));
        }
    }
    /**
      * get user's balance information
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function getBlanceInfo(){
        global $user_ID;
        $result = fre_credit_balance_info($user_ID);
        wp_send_json($result);
    }
    /**
      * email notification
      *
      * @param array/string $user_email,
      * @param string $subject
      * @param string $message
      * @param array $filter
      * @return bool $result
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function mailNotification( $user_email, $subject, $message, $filter = array() ){
        $ae_mailing = AE_Mailing::get_instance();
        $result = $ae_mailing->wp_mail($user_email, $subject, $message, $filter);
        return $result;
    }
    /**
      * request SecureCode
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function requestSecureCode(){
        global $user_ID, $current_user;
        if( $user_ID ){
            $resp = array(
                'success'=> false,
                'msg'=> __('Please wait a minute to request a new secure code!', ET_DOMAIN )
            );
            if( !$this->checkTimeToRequestCode(time()) ){
                wp_send_json($resp);
            }
            $pass = $this->generateSecureCode(8);
            $this->setSecureCode($user_ID, $pass);
            $subject = sprintf(__('New secure code on %s', ET_DOMAIN), get_bloginfo('name'));
            $message = fre_credit_request_secure_code_mail_content($pass);
            $result = $this->mailNotification($current_user->data->user_email, $subject, $message, array('user_id'=>$user_ID) );
            $resp = array(
                'success'=> false,
                'msg'=> __('Your request to generate a secure code is failed!', ET_DOMAIN )
            );
            if( $result ){
                $resp = array(
                    'success'=> true,
                    'msg'=> __('You secure code is sent to your email!', ET_DOMAIN )
                );
                update_user_meta($user_ID, 'fre_credit_update_secure_code_time', time());
                wp_send_json($resp);
            }
            wp_send_json($resp);
        }
    }
    /**
      * check time to request secure code
      *
      * @param integer current time
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function checkTimeToRequestCode($current_time){
        global $user_ID;
        $time = get_user_meta($user_ID, 'fre_credit_update_secure_code_time', true);
        if( $time ){
            $dis = (int)$current_time - (int)$time;
            $time_check = ae_get_option('fre_credit_time_request_code', 120);
            if( $dis > $time_check ){
                return true;
            }
            return false;
        }
        else{
            return true;
        }
    }
}