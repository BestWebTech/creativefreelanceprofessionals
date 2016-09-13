<?php
/**
 * Stripe escrow class
 */
class AE_Escrow_Stripe extends AE_Base
{
    private static $instance;
    public $client_id;
    public $client_secret;
    public $client_public;
    public $token_uri;
    public $authorize_uri;
    public $redirect_uri;
    /**
     * getInstance method
     *
     */
    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * The constructor
     *
     * @since 1.0
     * @author Tambh
     */
    private function __construct() {
    }
    /**
     * Init for class AE_Escrow_Stripe
     * @param void
     * @return void
     * @since 1.0
     * @package AE_ESCROW
     * @category STRIPE
     * @author Tambh
     */
    public function init(){
        require_once dirname(__FILE__) . '/init.php';
        $stripe_api = ae_get_option('escrow_stripe_api');
        $this->client_id = isset($stripe_api['client_id']) ? $stripe_api['client_id'] : 'ca_6haoa1xY6ECo3GG6MU1zo9yeuF6DviYz';
        $this->client_secret = isset($stripe_api['client_secret']) ? $stripe_api['client_secret'] : 'sk_test_Q1YPkPqgUbUloB0ZC9eE8KhL';
        $this->client_public = isset($stripe_api['client_public']) ? $stripe_api['client_public'] : ' pk_test_Sl5wiqSBuabUX5QndfTX5Bzn';
        $this->token_uri = 'https://connect.stripe.com/oauth/token';
        $this->authorize_uri = 'https://connect.stripe.com/oauth/authorize';
        $this->redirect_uri = et_get_page_link('process-accept-bid').'/?paymentType=stripe';
        $this->init_ajax();
    }
    /**
     * Put all ajax function here
     * @param void
     * @return void
     * @since 1.0
     * @package AE_ESCROW
     * @category STRIPE
     * @author Tambh
     */
    public function init_ajax(){
        $this->add_action( 'wp_footer', 'ae_stripe_escrow_template' );
        $this->add_ajax( 'fre-stripe-escrow-customer', 'ae_create_stripe_customer' );
        $this->add_action( 'ae_escrow_payment_gateway', 'ae_escrow_stripe_payment_gateway' );
        $this->add_action('fre_finish_escrow', 'ae_escrow_stripe_finish', 10, 2);
        $this->add_filter('fre_process_escrow', 'ae_escrow_stripe_process', 10, 3 );
        $this->add_action('ae_escrow_execute', 'ae_stripe_escrow_execute', 10, 2);
        $this->add_action('ae_escrow_refund', 'ae_stripe_escrow_refund', 10, 2);
//        $this->add_filter('ae_accept_bid_infor', 'ae_accept_bid_infor_filter');
    }
    /**
     * Connect to a stripe account
     * @param void
     * @return void
     * @since 1.0
     * @package AE_ESCROW
     * @category STRIPE
     * @author Tambh
     */
    public function ae_stripe_connect(){
        global $user_ID;
        if ( isset($_GET['code']) ) {
            $code = $_GET['code'];
            $token_request_body = array(
                'client_secret' => $this->client_secret,
                'grant_type' => 'authorization_code',
                'client_id' => $this->client_id,
                'code' =>  $code
            );
            $req = curl_init();
            curl_setopt($req, CURLOPT_URL, $this->token_uri);
            curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($req, CURLOPT_POST, true );
            curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));
            $respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
            $resp = json_decode(curl_exec($req), true);
            $stripe_user_id = $resp['stripe_user_id'];
            $this->ae_update_stripe_user_id( $user_ID, $stripe_user_id );
            ae_stripe_escrow_notification();
            curl_close($req);
        }
        else if (isset($_GET['error'])) { // Error
            echo $_GET['error_description'];
        }
        $authorize_request_body = array(
            'response_type' => 'code',
            'scope' => 'read_write',
            'client_id' => $this->client_id
        );
        $url = $this->authorize_uri . '?' . http_build_query($authorize_request_body);
        $text = __('Connect with Stripe', ET_DOMAIN);
        if( $this->ae_get_stripe_user_id($user_ID) ){
            $text = __('Reconnect with Stripe', ET_DOMAIN);
        }
        $html = "<li><div class='update-stripe-container'>";
        $html .= "<a class='' href='".$url."'><i class='fa fa-external-link'></i>".$text."</a>";
        $html .= "</div></li>";
        echo $html;
    }
    /**
     * Check if use stripe escrow
     * @param void
     * @return bool true/false, true if use stripe escrow and false if don't
     * @since 1.0
     * @package AE_ESCROW
     * @category STRIPE
     * @author Tambh
     */
    public function  is_use_stripe_escrow(){
        $stripe_api = ae_get_option( 'escrow_stripe_api' );
        return apply_filters( 'use_stripe_escrow', $stripe_api['use_stripe_escrow'] );
    }
    /**
     * Create a stripe customer
     * @param void
     * @return void
     * @since 1.0
     * @package AE_ESCROW
     * @category STRIPE
     * @author Tambh
     */
    public function ae_create_stripe_customer(){
        try {
            global $user_ID;
            if( !isset($_POST['token']) || !isset($_POST['stripe_email']) ){
                $response = array(
                    'success' => false,
                    'msg' => __('Update failed!', ET_DOMAIN)
                );
                wp_send_json($response);
            }
            $token = $_POST['token'];
            $stripe_email = $_POST['stripe_email'];
            \Stripe\Stripe::setApiKey($this->client_secret);
            $stripe_object = array(
                'card'=> $token,
                'email'=> $stripe_email
            );
            $customer_obj = wp_parse_args( array(
                'description' => 'Customer from ' . home_url()
            ), $stripe_object );
            $customer = \Stripe\Customer::create($customer_obj );
            $stripe_user_id = $customer->id;
//                $charge_obj = array(
//                    'amount' => 99999999,
//                    'currency' => 'usd',
//                    'source' => $token
//                );
//                $charge = \Stripe\Charge::create( $charge_obj );
//                var_dump($charge);

            if( $stripe_user_id ){
                $this->ae_update_stripe_user_id( $user_ID, $stripe_user_id );
            }
            else{
                $response = array(
                    'success' => false,
                    'msg' => __('Update failed!', ET_DOMAIN)
                );
            }
            $response = array(
                'success' => true,
                'msg' => __('You updated successfully!',ET_DOMAIN)
            );

        }
        catch( Exception $ex ){
            $value  =   $ex->getJsonBody();
            $response = array(
                'success' => false,
                'msg' => $value['error']['message'] );
        }
        wp_send_json( $response );

    }
    /**
      * Include all template were used for stripe escrow
      * @param void
      * @return void
      * @since 1.0
      * @package AE_ESCROW
      * @category STRIPE
      * @author Tambh
     */
    public function ae_stripe_escrow_template(){
        fre_update_stripe_info_modal();
    }
    /**
      * Get stripe public key* @param void
      * @return string $public_key
      * @since 1.0
      * @package AE_ESCROW
      * @category STRIPE
      * @author Tambh
    */
    public function ae_get_stripe_public_key(){
        return apply_filters( 'ae_stripe_public_key', $this->client_public );
    }
    /**
    * Get stripe customer id of  a Employer
    * @param integer $user_id
    * @return string of customer id
    * @since 1.0
    * @package AE_ESCROW
    * @category STRIPE
    * @author Tambh
    */
    public function ae_get_stripe_user_id( $user_id = null ){
        $stripe_user_id = '';
        if( null != $user_id ){
            $stripe_user_id = get_user_meta( $user_id, 'ae_stripe_user_id', true);
        }
        return apply_filters( 'ae_stripe_user_id', $stripe_user_id );
    }
    /**
    * Update stripe user id
    * @param integer $user_id
    * @param string $stripe_user_id
    * @return void
    * @since 1.0
    * @package AE_ESCROW
    * @category STRIPE
    * @author Tambh
    */
    public function ae_update_stripe_user_id( $user_id = null, $stripe_user_id = null ){
        if( null != $user_id && null != $stripe_user_id  ){
            update_user_meta( $user_id, 'ae_stripe_user_id', $stripe_user_id );
        }
    }
    /**
    * Execute Escrow by Stripe gateway
    * @param array $escrow_data
    * @return void
    * @since 1.0
    * @package AE_ESCROW
    * @category STRIPE
    * @author Tambh
    */
    public function ae_escrow_stripe_payment_gateway( $escrow_data ){
        global $user_ID;
        try {
            $escrow_data['customer'] = $this->ae_get_stripe_user_id($user_ID);
            $escrow_data['recipient'] = $this->ae_get_stripe_user_id( $escrow_data['bid_author'] );
            $charge_obj = array(
                'amount' => (float)$escrow_data['total'] * 100,
                'currency' => $escrow_data['currency'],
                'customer' => $escrow_data['customer'],
            );
            $bid_id = $escrow_data['bid_id'];
            $bid = get_post($bid_id);
            $charge = $this->ae_stripe_charge($charge_obj);
            $order_post = array(
                'post_type' => 'fre_order',
                'post_status' => 'pending',
                'post_parent' => $bid_id,
                'post_author' => $user_ID,
                'post_title' => 'Pay for accept bid',
                'post_content' => 'Pay for accept bid ' . $bid_id
            );
            if ( $charge && isset($charge->id)) {
                $order_id = wp_insert_post($order_post);
                update_post_meta($order_id, 'fre_paykey', $charge->id);
                update_post_meta($order_id, 'gateway', 'stripe');

                update_post_meta($bid_id, 'fre_bid_order', $order_id);
                update_post_meta($bid_id, 'commission_fee', $escrow_data['commission_fee']);
                update_post_meta($bid_id, 'payer_of_commission', $escrow_data['payer_of_commission']);
                update_post_meta($bid_id, 'fre_paykey', $charge->id);

                et_write_session('payKey', $charge->id);
                et_write_session('order_id', $order_id);
                et_write_session('bid_id', $bid_id);
                et_write_session('ad_id', $bid->post_parent);
                $response = array(
                    'success' => true,
                    'msg'=> 'Success!',
                    'redirect_url' => $this->redirect_uri
                );
                wp_send_json($response);
            }
            else {
                wp_send_json(array(
                    'success' => false,
                    'msg' => __('charge failed', ET_DOMAIN)
                ));
            }
        }
        catch( Exception $ex ){
            $value  =   $ex->getJsonBody();
            $response = array(
                'success' => false,
                'msg' => $value['error']['message'] );
        }
        exit;
    }
    /**
    * Stripe transfer process
    * @param array $transfer_obj 
    * @return object $transfer
    * @since 1.0
    * @package AE_ESCROW
    * @category STRIPE
    * @author Tambh
    */
    public function ae_stripe_transfer( $transfer_obj ){
        \Stripe\Stripe::setApiKey( $this->client_secret );
        $stripe_fee_payer = $this->ae_get_stripe_fee_payer();
        $amount = (float)$transfer_obj['amount'];
        $stripe_fee = $amount*0.029 + 30;
        if( $stripe_fee_payer == 'PRIMARYRECEIVER' ){
            $amount  = $stripe_fee + $amount;
        }
        $application_fee = (float)$transfer_obj['application_fee'];
        if( $stripe_fee_payer == 'SECONDARYONLY' ){
            $application_fee = $application_fee + $stripe_fee;
        }
        // Create a transfer to the bank account associated with your Stripe account
        $transfer_obj = array(
            "amount" => (float)$amount, // amount in cents
            "currency" => $transfer_obj['currency'],
            "destination" => $transfer_obj['destination'],
            'application_fee' => (int)$application_fee,
            "statement_descriptor" => "Freelance escrow");
        $transfer = \Stripe\Transfer::create($transfer_obj);
        return $transfer;

    }
    /**
    * Stripe transfer revert
    * @param string $transfer_id
    * @return object $re reversals
    * @since 1.0
    * @package AE_ESCROW
    * @category STRIPE
    * @author Tambh
    */
    public function ae_stripe_reversals( $transfer_id ){
        \Stripe\Stripe::setApiKey($this->client_id);
        $tr = \Stripe\Transfer::retrieve($transfer_id);
        $re = $tr->reversals->create();
        return $re;
    }
    /**
    * Charge money from customer when they acept bid
$    *@param array $charge_obj
    * @return object $charge
    * @since 1.0
    * @package AE_ESCROW
    * @category STRIPE
    * @author Tambh
    */
    public function ae_stripe_charge( $charge_obj ){
        \Stripe\Stripe::setApiKey($this->client_secret);
        $charge = \Stripe\Charge::create($charge_obj);
        return $charge;
    }
    /**
    * Refund money
    * @param string $charge_id
    * @return object $refund
    * @since 1.0
    * @package AE_ESCROW
    * @category STRIPE
    * @author Tambh
    */
    public function ae_stripe_refund( $charge_id ){
        \Stripe\Stripe::setApiKey($this->client_secret);
        $ch = \Stripe\Charge::retrieve( $charge_id );
        $re = $ch->refunds->create();
        return $re;
    }
    /**
    * Transfer money to freelancer when employer finish their project
    * @param integer $project_id the project's id that employer finished
     * @param $bid_id_accepted
    * @return void
    * @since 1.0
    * @package AE_ESCROW
    * @category STRIPE
    * @author Tambh
    */
    public function ae_escrow_stripe_finish( $project_id, $bid_id_accepted ){
        if ( $this->is_use_stripe_escrow() ) {
            // execute payment and send money to freelancer
            $charge_id = get_post_meta($bid_id_accepted, 'fre_paykey', true);
            if ( $charge_id ) {
                $charge = $this->ae_stripe_retrieve_charge( $charge_id );
                if ( $charge ) {
                    $bid = get_post($bid_id_accepted);
                    $destination = '';
                    $bid_budget = $charge->amount;
                    if( $bid && !empty($bid)){
                        $destination = $this->ae_get_stripe_user_id($bid->post_author);
                        $bid_budget = get_post_meta( $bid_id_accepted, 'bid_budget', true );
                        $payer_of_commission = get_post_meta( $bid_id_accepted, '$payer_of_commission', true );
                        if( $payer_of_commission != 'project_owner' ) {
                            $commission_fee = get_post_meta($bid_id_accepted, 'commission_fee', true);
                        }
                        else{
                            $commission_fee = 0;
                        }
                    }
                    $transfer_obj = array(
                        "amount" => (float)$bid_budget*100, // amount in cents
                        "currency" => $charge->currency,
                        "destination" => $destination,
                        'application_fee' => (int)$commission_fee*100,
                        "statement_descriptor" => __("Freelance escrow", ET_DOMAIN)
                    );
                    $transfer = $this->ae_stripe_transfer( $transfer_obj );
                    if( $transfer ) {
                        $order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
                        if ($order) {
                            wp_update_post(array(
                                'ID' => $order,
                                'post_status' => 'finish'
                            ));
                            $mail = Fre_Mailing::get_instance();
                            $mail->alert_transfer_money($project_id, $bid_id_accepted);
                        }
                    }
                }
            }
            else {
                $mail = Fre_Mailing::get_instance();
                $mail->alert_transfer_money($project_id, $bid_id_accepted);
            }
        }
    }
    /**
    * Retrieve a charge
    * @param string $charge_id
    * @return object $charge or false if there isn't any charge
    * @since 1.0
    * @package AE_ESCROW
    * @category STRIPE
    * @author Tambh
    */
    public function ae_stripe_retrieve_charge( $charge_id ){
        \Stripe\Stripe::setApiKey( $this->client_secret );
        $charge = \Stripe\Charge::retrieve($charge_id);
        if( isset($charge->status)  && $charge->status == 'succeeded'){
            return $charge;
        }
        return false;
    }
    /**
    * Process payment accept bid
    * @param array $payment_return
     * @param string $payment_type
     * @param array $data
    * @return array $payment_return
    * @since 1.0
    * @package AE_ESCROW
    * @category STRIPE
    * @author Tambh
    */
    public function ae_escrow_stripe_process( $payment_return, $payment_type, $data ){
        if ($payment_type == 'stripe') {
            $response = $this->ae_stripe_retrieve_charge($data['payKey']);
            $payment_return['payment_status'] = $response->paid;
            if ($response->status == 'succeeded') {
                $payment_return['ACK'] = true;
                    wp_update_post(array(
                        'ID' => $data['order_id'],
                        'post_status' => 'publish'
                    ));
                    // assign project
                    $bid_action = Fre_BidAction::get_instance();
                    $bid_action->assign_project($data['bid_id']);
            }
            if (strtoupper($response->responseEnvelope->ack) == 'FAILURE') {
                $payment_return['msg'] = $response->error[0]->message;
            }
        }
        return $payment_return;
    }
    /**
    * Refund escrow by stripe
     * @param interger $project_id
     *@param integer $bid_id_accepted
    * @return void
    * @since 1.0
    * @package AE_ESCROW
    * @category STRIPE
    * @author Tambh
    */
    public function ae_stripe_escrow_refund( $project_id, $bid_id_accepted ){
        $pay_key = get_post_meta($bid_id_accepted, 'fre_paykey', true);
        $re = $this->ae_stripe_refund($pay_key);
        if( $re ){
            $order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
            if ($order) {
                wp_update_post(array(
                    'ID' => $order,
                    'post_status' => 'refund'
                ));
            }
            wp_update_post(array(
                'ID' => $project_id,
                'post_status' => 'disputed'
            ));
            $mail = Fre_Mailing::get_instance();
            $mail->refund($project_id, $bid_id_accepted);
            // send json back
            wp_send_json(array(
                'success' => true,
                'msg' => __("Send payment successful.", ET_DOMAIN) ,
                'data' =>__('Success', ET_DOMAIN)
            ));
        }
        else {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Refund failed!', ET_DOMAIN)
            ));
        }
    }
    /**
     * Refund escrow by stripe
     * @param $project_id
     *@param integer $bid_id_accepted
     * @return void
     * @since 1.0
     * @package AE_ESCROW
     * @category STRIPE
     * @author Tambh
     */
    public function ae_stripe_escrow_execute( $project_id, $bid_id_accepted ){
        $charge_id = get_post_meta($bid_id_accepted, 'fre_paykey', true);
        $charge = $this->ae_stripe_retrieve_charge( $charge_id );
        if ( $charge ) {
            $bid = get_post($bid_id_accepted);
            $destination = '';
            $bid_budget = $charge->amount;
            if( $bid && !empty($bid)){
                $destination = $this->ae_get_stripe_user_id($bid->post_author);
                $bid_budget = get_post_meta( $bid_id_accepted, 'bid_budget', true );
                $payer_of_commission = get_post_meta( $bid_id_accepted, '$payer_of_commission', true );
                if( $payer_of_commission != 'project_owner' ) {
                    $commission_fee = get_post_meta($bid_id_accepted, 'commission_fee', true);
                }
                else{
                    $commission_fee = 0;
                }
            }
            $transfer_obj = array(
                "amount" => (float)$bid_budget*100, // amount in cents
                "currency" => $charge->currency,
                "destination" => $destination,
                'application_fee' => $commission_fee,
                "statement_descriptor" => "Freelance escrow"
            );
            $transfer = $this->ae_stripe_transfer( $transfer_obj );
            if( $transfer ) {
                $order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
                if ($order) {
                    wp_update_post(array(
                        'ID' => $order,
                        'post_status' => 'completed'
                    ));
                }

                // success update project status
                wp_update_post(array(
                    'ID' => $project_id,
                    'post_status' => 'disputed'
                ));
                // send mail
                $mail = Fre_Mailing::get_instance();
                $mail->execute($project_id, $bid_id_accepted);

                wp_send_json(array(
                    'success' => true,
                    'msg' => __("Send payment successful.", ET_DOMAIN)
                ));
            }
            else {
                wp_send_json(array(
                    'success' => false,
                    'msg' => __('Send payment failed', ET_DOMAIN)
                ));
            }
        }
        else {
            wp_send_json(array(
                'success' => false,
                'msg' => __("Invalid charge.", ET_DOMAIN)
            ));
        }


    }
    /**
    * Get the stripe user who will pay the fee
    * @param void
    * @return string $fee_payer
    * @since 1.0
    * @package AE_ESCROW
    * @category STRIPE
    * @author Tambh
    */
    public function ae_get_stripe_fee_payer(){
        $stripe_api = ae_get_option('escrow_stripe_api');
        $fee_payer = $stripe_api['stripe_fee']? $stripe_api['stripe_fee']: 'EACHRECEIVER' ;
        return $fee_payer;
    }

}
