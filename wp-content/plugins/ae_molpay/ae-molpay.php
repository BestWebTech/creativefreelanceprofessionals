<?php
/**
 * @package AppEngine MOLPay
 */

/*
Plugin Name: AE MOLPay
Plugin URI: http://enginethemes.com/
Description: Integrates the MOLPay payment gateway to your Directory, Freelance site
Version: 1.0
Author: enginethemes
Author URI: http://enginethemes.com/
License: GPLv2
Text Domain: enginetheme
*/
//require_once dirname(__FILE__) . '/update.php';

add_filter('ae_admin_menu_pages','ae_molpay_add_settings');
function ae_molpay_add_settings($pages){
	$sections = array();
	$options = AE_Options::get_instance();

	/**
	 * ae fields settings
	 */
	$sections = array(
		'args' => array(
			'title' => __("MOLPay API", ET_DOMAIN) ,
			'id' => 'meta_field',
			'icon' => 'F',
			'class' => ''
		) ,

		'groups' => array(
			array(
				'args' => array(
					'title' => __("Merchaint ID", ET_DOMAIN) ,
					'id' => 'secret-key',
					'class' => '',
					'desc' => __('The MOLPay API by providing one of your API Merchaint ID in the request.', ET_DOMAIN),
					'name' => 'molpay'
				) ,
				'fields' => array(
					array(
						'id' => 'merchaint_id',
						'type' => 'text',
						'label' => __("Merchaint ID", ET_DOMAIN) ,
						'name' => 'merchaint_id',
						'class' => ''
					) ,
					array(
						'id' => 'verify_key',
						'type' => 'text',
						'label' => __('Verify Key', ET_DOMAIN),
						'name'  => 'verify_key',
						'class' => ''
					)
				)
			)
		)
	);

	$temp = new AE_section($sections['args'], $sections['groups'], $options);

	$molpay_setting = new AE_container(array(
		'class' => 'field-settings',
		'id' => 'settings',
	) , $temp, $options);

	$pages[] = array(
		'args' => array(
			'parent_slug' => 'et-overview',
			'page_title' => __('MOLPay', ET_DOMAIN) ,
			'menu_title' => __('MOLPAY', ET_DOMAIN) ,
			'cap' => 'administrator',
			'slug' => 'ae-molpay',
			'icon' => '$',
			'desc' => __("Integrate the MOLPay payment gateway to your site", ET_DOMAIN)
		) ,
		'container' => $molpay_setting
	);
	return $pages;
}


add_filter( 'ae_support_gateway', 'ae_molpay_add' );
function ae_molpay_add($gateways){
	$gateways['molpay'] = 'Molpay';
	return $gateways;
}

add_action('after_payment_list', 'ae_molpay_render_button');
function ae_molpay_render_button() {
	$molpay = ae_get_option('molpay');
	if(!$molpay['merchaint_id'])
		return false;
?>
	<li>
		<span class="title-plan select-payment" data-type="molpay">
			<?php _e("MOLPay", ET_DOMAIN); ?>
			<span><?php _e("Send your payment via MOLPay.", ET_DOMAIN); ?></span>
		</span>
		<a href="#" class="btn btn-submit-price-plan select-payment" data-type="molpay"><?php _e("Select", ET_DOMAIN); ?></a>
	</li>
<?php
}


add_filter('ae_setup_payment', 'ae_molpay_setup_payment', 10, 4	);
function ae_molpay_setup_payment($response, $paymentType, $order) {
	if( $paymentType == 'MOLPAY') {

		$molpay = ae_get_option('molpay');
		$order_pay = $order->generate_data_to_pay();
		$merchaint_id = $molpay['merchaint_id'];

		$orderId = $order_pay['product_id'];
		$amount = $order_pay['total'];
		$currency = $order_pay['currencyCodeType'];

		try{

			$returnURL = 'https://www.onlinepayment.com.my/MOLPay/pay/'.$merchaint_id.'/alipay.php';

			$returnURL .= '?amount='.$amount;
			$returnURL .= '&orderid='.$orderId;
			$returnURL .= '&currency='.$currency;

			$status_url = et_get_page_link('process-payment', array(
			 'paymentType' => 'molpay'
			));

			$order->update_order();

			$response = array(
				'success' => true,
				'data' => array(
					'url' => $returnURL,
					'ACK' => true,
				) ,
				'paymentType' => 'molpay'
			);
		}
		catch(Exception $e) {
			$value  =   $e->getJsonBody();
			$response = array(
				'success' => false,
				'msg' => $value['error']['message'],
				'paymentType' => 'molpay'
			);
		}
	}
	return $response;
}



add_filter( 'ae_process_payment', 'ae_molpay_process_payment', 10 ,2 );
function ae_molpay_process_payment ( $payment_return, $data) {
	$payment_type = $data['payment_type'];
	if( $payment_type == 'molpay') {
		$order = $data['order'];
		$molpay_key = ae_get_option('molpay');
		$result = $order->get_order_data();

		// Verify key MOLPay
		$vkey = $molpay_key['verify_key'];

		$tranID = $_POST['tranID'];
		$orderid = $_POST['orderid'];
		$status = $_POST['status'];
		$domain = $_POST['domain'];
		$amount = $_POST['amount'];
		$currency = $_POST['currency'];
		$appcode = $_POST['appcode'];
		$paydate = $_POST['paydate'];
		$skey = $_POST['skey'];

		//To verify the data integrity sending by MOLPay
		$key0 = md5( $tranID.$orderid.$status.$domain.$amount.$currency );
		$key1 = md5($paydate.$domain.$key0.$appcode.$vkey);


		if($skey != $key1)
		{
			$status = -1;
		}
		if($status == "00" && $result['currency'] == $currency && $order->get_total() == $amount )
		{
			$payment_return	= array (
				'ACK' 			=> true,
				'payment'		=>	'molpay',
				'payment_status' =>'Completed'
			);
			$order->set_status ('publish');
			$order->update_order();
		}else{
			$payment_return =   array (
				'ACK'           => false,
				'payment'       =>  'molpay',
				'payment_status' =>'Completed',
				'msg'   => __('MOLPay payment method false.', ET_DOMAIN)

			);
		}
	}
	return $payment_return;
}