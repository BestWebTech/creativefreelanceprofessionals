<?php 
/**
 * Stripe escrow setting 
 * @param Array $groups seting of payment gateways
 */
if( !function_exists( 'fre_escrow_payment_gateway_stripe_setting' ) ){
    function fre_escrow_payment_gateway_stripe_setting($groups){	
    	$groups[] = array(
                    'args' => array(
                        'title' => __("Stripe API", ET_DOMAIN) ,
                        'id' => 'use-escrow-stripe',
                        'class' => '',
                        'name' => 'escrow_stripe_api',
                        // 'desc' => __("Your Paypal Adaptive API", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'use_stripe_escrow',
                            'type' => 'switch',
                            'title' => __("use stripe escrow", ET_DOMAIN) ,
                            'name' => 'use_stripe_escrow',
                            'class' => ''
                        ),
                        array(
                            'id' => 'client_secret',
                            'type' => 'text',
                            //'title' => __("Your paypal API username", ET_DOMAIN) ,
                            'name' => 'client_secret',
                            'label' => __("Your Stripe client secret key", ET_DOMAIN) ,
                            'class' => ''
                        ),
                        array(
                            'id' => 'client_public',
                            'type' => 'text',
                            //'title' => __("Your paypal API username", ET_DOMAIN) ,
                            'name' => 'client_public',
                            'label' => __("Your Stripe client public key", ET_DOMAIN) ,
                            'class' => ''
                        ),
                        array(
                            'id' => 'client_id',
                            'type' => 'text',
                            //'title' => __("Your paypal API username", ET_DOMAIN) ,
                            'name' => 'client_id',
                            'label' => __("Your Stripe client id", ET_DOMAIN) ,
                            'class' => ''
                        ),
                        array(
                            'id' => 'stripe_fee',
                            'type' => 'select',
                            'title' => __("Stripe fees", ET_DOMAIN) ,
                            'label' => __("Stripe fees", ET_DOMAIN) ,
                            'name' => 'stripe_fee',
                            'class' => '',
                            'data' => array(
                                // 'SENDER' => __("Sender pays all fees", ET_DOMAIN) ,
                                'PRIMARYRECEIVER' => __("Primary receiver pays all fees", ET_DOMAIN),
                                'EACHRECEIVER' => __("Each receiver pays their own fee", ET_DOMAIN),
                                'SECONDARYONLY' => __("Secondary receivers pay all fees", ET_DOMAIN)
                            )
                        )
                    )                  
                );
    	return $groups;
    }
}
add_filter( 'fre_escrow_payment_gateway_settings', 'fre_escrow_payment_gateway_stripe_setting' );