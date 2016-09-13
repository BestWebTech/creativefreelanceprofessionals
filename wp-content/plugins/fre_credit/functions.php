<?php
/**
 * Plugin  function
 * @param void
 * @return void
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Jack Bui
*/
if( !function_exists('FRE_Credit_Users')){
    /**
      * get instance of class FRE_Credit_Users
      *
      * @param void
      * @return FRE_Credit_Users $instance
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    function FRE_Credit_Users(){
        return FRE_Credit_Users::getInstance();
    }
}

if( !function_exists('FRE_Credit_Wallet')){
    /**
     * get instance of class FRE_Credit_Wallet
     *
     * @param void
     * @return FRE_Credit_Wallet $instance
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function FRE_Credit_Wallet(){
        return FRE_Credit_Wallet::getInstance();
    }
}
if( !function_exists('FRE_Credit_Currency')){
    /**
     * get instance of class FRE_Credit_Currency
     *
     * @param void
     * @return FRE_Credit_Currency $instance
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function FRE_Credit_Currency(){
        return FRE_Credit_Currency::getInstance();
    }
}
if( !function_exists('FRE_Credit_Currency_Exchange')){
    /**
     * get instance of class FRE_Credit_Currency_Exchange
     *
     * @param void
     * @return FRE_Credit_Currency_Exchange $instance
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function FRE_Credit_Currency_Exchange(){
        return FRE_Credit_Currency_Exchange::getInstance();
    }
}
if( !function_exists('FRE_Credit_Plan_Posttype')){
    /**
     * get instance of class FRE_Credit_Plan_Posttype
     *
     * @param void
     * @return FRE_Credit_Currency $instance
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function FRE_Credit_Plan_Posttype(){
        return FRE_Credit_Plan_Posttype::getInstance();
    }
}
if( !function_exists('FRE_Credit_Escrow')){
    /**
     * get instance of class FRE_Credit_Escrow
     *
     * @param void
     * @return FRE_Credit_Escrow $instance
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function FRE_Credit_Escrow(){
        return FRE_Credit_Escrow::getInstance();
    }
}
if( !function_exists('FRE_Credit_History')){
    /**
     * get instance of class FRE_Credit_History
     *
     * @param void
     * @return FRE_Credit_History $instance
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function FRE_Credit_History(){
        return FRE_Credit_History::getInstance();
    }
}
if( !function_exists('FRE_Credit_Withdraw')){
    /**
     * get instance of class FRE_Credit_Withdraw
     *
     * @param void
     * @return FRE_Credit_Withdraw $instance
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function FRE_Credit_Withdraw(){
        return FRE_Credit_Withdraw::getInstance();
    }
}
if( !function_exists('fre_credit_get_payment_currency') ){
    /**
     * get site payment currency
     *
     * @param void
     * @return FRE_Credit_Currency $currency
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function fre_credit_get_payment_currency(){
        $currency = ae_get_option('currency', false);
        $code = 'usd';
        $signal = '$';
        $rate_exchange = 1;
        if( $currency ){
           $code = $currency['code'];
           $signal = $currency['icon'];
        }
        if(isset($currency['rate_exchange']) ){
            $rate_exchange = $currency['rate_exchange'];
        }
        $currency = new FRE_Credit_Currency($code, $signal, true, $rate_exchange);
        return $currency;
    }

}
if( !function_exists('fre_credit_convert_wallet') ){
    /**
      * convert a number to wallet
      *
      * @param float $number
      * @return FRE_Credit_Wallet $wallet
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    function fre_credit_convert_wallet( $number = 0 ){
        if( null == $number || empty($number) ){
            $number = 0;
        }
        $currency = fre_credit_get_payment_currency();
        $wallet = new FRE_Credit_Wallet($number, $currency);
        return $wallet;
    }
}
if( !function_exists('is_use_credit_escrow') ){
    /**
     * Check if use credit escrow
     * @param void
     * @return bool true/false, true if use stripe escrow and false if don't
     * @since 1.0
     * @package AE_ESCROW
     * @category STRIPE
     * @author Jack Bui
     */
    function  is_use_credit_escrow(){
        $credit_api = ae_get_option( 'escrow_credit_settings' );
        return apply_filters( 'use_credit_escrow', $credit_api['use_credit_escrow'] );
    }
}
if( !function_exists('fre_parse_form_data') ) {
    /**
     * description
     *
     * @param string $data
     * @return array $data
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function fre_parse_form_data($data){
        $array = array();
        if( empty($data) ){
            return $array;
        }
        $data = explode('&', $data);
        foreach( $data as $key => $value ){
            $data_arr = explode('=', $value);
            $array[$data_arr['0']] = $data_arr['1'];
        }
        return $array;
    }
}
if( !function_exists('fre_credit_get_user_total_balance') ){
    /**
     * get user balance
     *
     * @param integer $user_id
     * @return FRE_Credit_Wallet $available
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function fre_credit_get_user_total_balance($user_id){
        $available = FRE_Credit_Users()->getUserWallet($user_id);
        $freezable = FRE_Credit_Users()->getUserWallet($user_id, 'freezable');
        $available->balance = $available->balance + $freezable->balance;
        return $available;
    }
}
if( !function_exists('fre_credit_balance_info') ){
    /**
     * render json about balance infor
     *
     * @param integer $user_id
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function fre_credit_balance_info($user_id){
        $total = fre_credit_get_user_total_balance($user_id);
        $available = FRE_Credit_Users()->getUserWallet($user_id);
        $freezable = FRE_Credit_Users()->getUserWallet($user_id, 'freezable');
        $minimum = ae_get_option('fre_credit_minimum_withdraw', 0);
        $balance_info = array(
            'total_text'=>  fre_price_format($total->balance),
            'available_text'=>fre_price_format($available->balance),
            'freezable_text'=> fre_price_format($freezable->balance),
            'total'=> $total,
            'available'=> $available,
            'freezable'=> $freezable,
            'min_withdraw'=> $minimum,
            'min_withdraw_text'=> fre_price_format($minimum)
        );
        return $balance_info;
    }
}
/**
  * deposit page link
  *
  * @param void
  * @return string link of deposit page
  * @since 1.0
  * @package FREELANCEENGINE
  * @category FRE CREDIT
  * @author Jack Bui
  */
function fre_credit_deposit_page_link(){
    $page = ae_get_option('fre_credit_deposit_page_slug', false);
    if( $page ){
        $link = get_permalink( get_page_by_title( $page ) );
        return $link;
    }
    return home_url();
}
/**
  * get admin email
  *
  * @param void
  * @return string $email
  * @since 1.0
  * @package FREELANCEENGINE
  * @category FRE CREDIT
  * @author Jack Bui
  */
function fre_credit_get_admin_email(){
    $email = ae_get_option('fre_credit_admin_emails', false);
    if( !$email ){
        $email = get_option('admin_email');
    }
    return apply_filters('fre_credit_admin_email', $email);
}
if( !function_exists('fre_credit_get_deposit_email_content') ) {
    /**
     * get email content
     *
     * @param integer $number
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function fre_credit_get_deposit_email_content($number)
    {
        $message = ae_get_option('fre_credit_deposit_mail_template');
        $number = fre_price_format($number);
        $message = str_ireplace('[number]', $number, $message);
        return $message;
    }
}
if( !function_exists('fre_credit_get_withdraw_email_content') ) {
    /**
     * get email content
     *
     * @param integer $amount
     * @param string $msg
     * @return $message
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function fre_credit_get_withdraw_email_content($amount, $msg)
    {
        $message = ae_get_option('fre_credit_withdraw_mail_template');
        $number = fre_price_format($amount);
        $message = str_ireplace('[amount]', $number, $message);
        $message = str_ireplace('[message]', $msg, $message);
        return $message;
    }
}
if( !function_exists('fre_credit_request_secure_code_mail_content') ) {
    /**
     * get email content
     *
     * @param string $number
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function fre_credit_request_secure_code_mail_content($code)
    {
        $message = ae_get_option('fre_credit_request_secure_mail_template');
        $message = str_ireplace('[code]', $code, $message);
        return $message;
    }
}
/**
  * check if enable option prevent access to deposit page
  *
  * @param void
  * @return void
  * @since 1.0
  * @package FREELANCEENGINE
  * @category FRE CREDIT
  * @author Jack Bui
  */
function fre_credit_redirect(){
    if( ae_get_option('prevent_deposit_page', false) ){
        $page = ae_get_option('fre_credit_deposit_page_slug', false);
        if( $page && is_page($page) ){
            if( et_load_mobile() ){
                wp_redirect(et_get_page_link('profile').'#tab_credits');
            }
            else {
                wp_redirect(et_get_page_link('profile') . '#credits');
            }
        }
    }
}
add_action('template_redirect', 'fre_credit_redirect');

/**
 * Get color/icon of transaction
 * @param $type
 * @author ThanhTu      
 * @since 1.0
 * @return Array
 */
function get_color_icon_transaction($type = 'deposit'){
    $trans_arr = array(        
        'deposit' => array('color' => 'text-blue-light', 'icon' => 'fa fa-arrow-up'),
        'withdraw' => array('color' => 'text-green-dark', 'icon' => 'fa fa-arrow-down'),
        'transfer' => array('color' => 'text-blue-light', 'icon' => 'fa fa-arrow-right'),
        'charge' => array('color' => 'text-orange-dark', 'icon' => 'fa fa-minus')
    );
    return $trans_arr[$type];
}
