<?php
/**
 * Private message setting
 * @param Array $pages setting of payment gateways
 */

if( !function_exists('fre_credit_menu_credit' ) ){
    function fre_credit_menu_credit( $pages ){
        $options = AE_Options::get_instance();

        $section = array(
            'args' => array(
                'title' => __("Credit Settings", ET_DOMAIN) ,
                'id' => 'credit-settings',
                'icon' => 'y',
                'class' => ''
            ) ,
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Use credit system", ET_DOMAIN) ,
                        'id' => 'use-credit-system',
                        'class' => '',
                        'desc' => __("Enabling this will allow users use credit system on site.", ET_DOMAIN)
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'user_credit_system',
                            'type' => 'switch',
                            'title' => __("Use credit system", ET_DOMAIN) ,
                            'name' => 'user_credit_system',
                            'class' => ''
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Prevent access to deposit page", ET_DOMAIN) ,
                        'id' => 'prevent-deposit-page',
                        'class' => '',
                        'desc' => __("Enabling this will prevent users access to deposit page on site.", ET_DOMAIN)
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'prevent_deposit_page',
                            'type' => 'switch',
                            'title' => __("Prevent access to deposit page", ET_DOMAIN) ,
                            'name' => 'prevent_deposit_page',
                            'class' => ''
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Deposit page slug", ET_DOMAIN) ,
                        'id' => 'fre_credit_deposit_page',
                        'class' => '',
                        'desc' => __("Set page slug to deposit page.", ET_DOMAIN)
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'fre_credit_deposit_page_slug',
                            'type' => 'text',
                            'title' => __("Set page slug to deposit page.", ET_DOMAIN) ,
                            'name' => 'fre_credit_deposit_page_slug',
                            'placeholder' => __("eg:deposit", ET_DOMAIN) ,
                            'class' => '',
                            'default'=> 0
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Admin email who will receive notification", ET_DOMAIN) ,
                        'id' => 'fre_credit_admin_email',
                        'class' => '',
                        'desc' => __("Set admin email who will receive notification", ET_DOMAIN)
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'fre_credit_admin_emails',
                            'type' => 'text',
                            'title' => __("Set admin email who will receive notification.", ET_DOMAIN) ,
                            'name' => 'fre_credit_admin_emails',
                            'placeholder' => __("abc@example.com, cde@example.com ", ET_DOMAIN) ,
                            'class' => '',
                            'default'=> 0
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Minimum money in each withdraw process", ET_DOMAIN) ,
                        'id' => 'fre_credit_minimum_withdraw_title',
                        'class' => '',
                        'desc' => __("Minimum money in each withdraw process", ET_DOMAIN)
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'fre_credit_minimum_withdraw',
                            'type' => 'text',
                            'title' => __("Set minimum money in each withdraw process.", ET_DOMAIN) ,
                            'name' => 'fre_credit_minimum_withdraw',
                            'placeholder' => __("0", ET_DOMAIN) ,
                            'class' => '',
                            'default'=> 0
                        )
                    )
                ),
                array(
                    'type' => 'list',
                    'args' => array(
                        'title' => __("Credit Plans", ET_DOMAIN) ,
                        'id' => 'list-credit-package',
                        'class' => 'list-credit-package',
                        'desc' => '',
                        'name' => 'fre_credit_plan',
                        'custom_field' => 'fre_credit_plan'
                    ) ,
                    'fields' => array(
                        'form' => dirname(__FILE__).'/admin-template/credit-plan-form.php',
                        'form_js' => dirname(__FILE__).'/admin-template/credit-plan-form-js.php',
                        'js_template' => dirname(__FILE__).'/admin-template/credit-plan-js-item.php',
                        'template' => dirname(__FILE__).'/admin-template/credit-plan-item.php',
                        'fullpath'=>true
                    )
                ),
                 array(
                     'args' => array(
                         'title' => __("Mail Template", ET_DOMAIN) ,
                         'id' => 'private-message-mail-description-group',
                         'class' => '',
                         'name' => ''
                     ) ,
                     'fields' => array(
                         array(
                             'id' => 'mail-description',
                             'type' => 'desc',
                             'title' => __("Mail description here", ET_DOMAIN) ,
                             'text' => __("Email templates for new message. You can use placeholders to include some specific content.", ET_DOMAIN) . '<a class="icon btn-template-help payment" data-icon="?" href="#" title="View more details"></a>' . '<div class="cont-template-help payment-setting">
                                                     [user_login],[display_name],[user_email] : ' . __("user's details you want to send mail", ET_DOMAIN) . '<br />
                                                     [dashboard] : ' . __("member dashboard url ", ET_DOMAIN) . '<br />
                                                     [title], [link], [excerpt],[desc], [author] : ' . __("project title, link, details, author", ET_DOMAIN) . ' <br />
                                                     [activate_url] : ' . __("activate link is require for user to renew their pass", ET_DOMAIN) . ' <br />
                                                     [site_url],[blogname],[admin_email] : ' . __(" site info, admin email", ET_DOMAIN) . '
                                                     [project_list] : ' . __("list projects employer send to freelancer when invite him to join", ET_DOMAIN) . '
                                                 </div>',
                             'class' => '',
                             'name' => 'mail_description'
                         )
                     )
                 ) ,
                 array(
                     'args' => array(
                         'title' => __("Deposit notification email template", ET_DOMAIN) ,
                         'id' => 'fre-credit-deposit-mail',
                         'class' => '',
                         'name' => '',
                         'desc' => __("Send to admin when there is a new deposit from users.", ET_DOMAIN),
                         'toggle' => false
                     ) ,
                     'fields' => array(
                         array(
                             'id' => 'fre_credit_deposit_mail_template',
                             'type' => 'editor',
                             'title' => __("Deposit notification email template", ET_DOMAIN) ,
                             'name' => 'fre_credit_deposit_mail_template',
                             'class' => '',
                             'reset' => 1
                         )
                     )
                 ),
                array(
                    'args' => array(
                        'title' => __("Request secure code email template", ET_DOMAIN) ,
                        'id' => 'fre-credit-request-secure-mail',
                        'class' => '',
                        'name' => '',
                        'desc' => __("Send to users their secure code when they request one.", ET_DOMAIN),
                        'toggle' => false
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'fre_credit_request_secure_mail_template',
                            'type' => 'editor',
                            'title' => __("Request secure code email template.", ET_DOMAIN) ,
                            'name' => 'fre_credit_request_secure_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Withdraw notification email template", ET_DOMAIN) ,
                        'id' => 'fre-credit-withdraw-mail',
                        'class' => '',
                        'name' => '',
                        'desc' => __("Send to admin when there is a new withdraw request from users.", ET_DOMAIN),
                        'toggle' => false
                    ) ,
                    'fields' => array(
                        array(
                            'id' => '
                            ',
                            'type' => 'editor',
                            'title' => __("Withdraw notification email template", ET_DOMAIN) ,
                            'name' => 'fre_credit_withdraw_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
            )
        );
        
        $temp = new AE_section($section['args'], $section['groups'], $options);
        
        $orderlist = new AE_container(array(
            'class' => 'credit-settings',
            'id' => 'settings',
        ) , $temp, $options);
        $pages[] = array(
            'args' => array(
                'parent_slug' => 'et-overview',
                'page_title' => __('Credit system', ET_DOMAIN) ,
                'menu_title' => __('Credit system', ET_DOMAIN) ,
                'cap' => 'administrator',
                'slug' => 'fre-credit',
                'icon' => 'M',
                'desc' => __("Bridging the gap between Employers and Freelancers", ET_DOMAIN)
            ) ,
            'container' => $orderlist
        );
        /**
         * order list view
         */
        $withdrawList = new AE_WithdrawList(array());
        $pages['withdraws'] = array(
            'args' => array(
                'parent_slug' => 'et-overview',
                'page_title' => __('Withdraws', ET_DOMAIN) ,
                'menu_title' => __('WITHDRAWS', ET_DOMAIN) ,
                'cap' => 'administrator',
                'slug' => 'et-withdraws',
                'icon' => '%',
                'desc' => __("Overview of all withdraws", ET_DOMAIN)
            ) ,
            'container' => $withdrawList
        );
        return $pages;
    }
}
add_filter('ae_admin_menu_pages', 'fre_credit_menu_credit');
/**
  * add default template to setting page
  * @param array $default
  * @return array $default
  * @since 1.0
  * @package FREELANCEENGINE
  * @category PRIVATE MESSAGE
  * @author Jack Bui
  */
function fre_credit_default_option( $default ){
    return $default;
}
add_filter( 'fre_default_setting_option', 'fre_credit_default_option' );
/**
  * add Escrow payment gateway
  *
  * @param array $groups
  * @return array $groups
  * @since 1.0
  * @package FREELANCEENGINE
  * @category FRE CREDIT
  * @author Jack Bui
  */
// var_dump(!ae_get_option('manual_transfer'));
// var_dump(!ae_get_option('use_credit_escrow'));
 
if( !function_exists('fre_escrow_payment_gateway_credit_setting') ){
    function fre_escrow_payment_gateway_credit_setting($groups){
        $groups[] = array(
            'args' => array(
                'title' => __("Credit escrow settings", ET_DOMAIN) ,
                'id' => 'use-escrow-credit',
                'class' => '',
                'name' => 'escrow_credit_settings',
                'desc' => __('Enabling this will allow you to use credit escrow system and disable all other gateways!', ET_DOMAIN)
            ) ,

            'fields' => array(
                array(
                    'id' => 'use_credit_escrow',
                    'type' => 'switch',
                    'title' => __("use credit escrow", ET_DOMAIN) ,
                    'name' => 'use_credit_escrow',
                    'class' => '',
                    'label' => __('Enabling this will allow you to use credit escrow system and disable all other gateways!', ET_DOMAIN)
                ),
                array(
                    'id' => 'credit_fee',
                    'type' => 'select',
                    'title' => __("Credit fees", ET_DOMAIN) ,
                    'label' => __("Credit fees", ET_DOMAIN) ,
                    'name' => 'credit_fee',
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
add_filter( 'fre_escrow_payment_gateway_settings', 'fre_escrow_payment_gateway_credit_setting' );
if( !function_exists('fre_credit_enable_escrow') ){
    /**
      * disable all others escrow gateways when enable credit gateway
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    function fre_credit_enable_escrow(){
        $credit_api = ae_get_option( 'escrow_credit_settings' );
        $stripe_api = ae_get_option( 'escrow_stripe_api' );
        if( $credit_api['use_credit_escrow'] && ( isset($stripe_api['use_stripe_escrow']) && $stripe_api['use_stripe_escrow']  ) ){
            $stripe_api['use_stripe_escrow'] = false;
            ae_update_option('escrow_stripe_api', $stripe_api);
        }
    }
}
add_action('ae_save_option', 'fre_credit_enable_escrow');
/**
  * default email
  *
  * @param array $mail_template
  * @return void
  * @since 1.0
  * @package FREELANCEENGINE
  * @category FRE CREDIT
  * @author Jack Bui
  */
function fre_credit_default_email($mail_template){
    $mail_template['fre_credit_deposit_mail_template'] = "<p>Hi,</p>
                                    <p>User [display_name] has been deposited [number] on your site,please check and confirm the payment.</p>
                                    <p>Sincerely,</p>
                                    <p>[blogname]</p>";
    $mail_template['fre_credit_request_secure_mail_template'] = "<p>Hi [display_name],</p>
                                    <p>You have requested a secure code.</p>
                                    <p>You can use this code for credit transaction. You should keep your code secret. </p>
                                    <p>You code: [code]</p>
                                    <p>Sincerely,</p>
                                    <p>[blogname]</p>";
    $mail_template['fre_credit_withdraw_mail_template'] = "<p>Hi,</p>
                                    <p>There is a withdraw request on your site.</p>
                                    <p>Name: [display_name]</p>
                                    <p>Amount: [amount]</p>
                                    <p>Node: [message]</p>
                                    <p>Sincerely,</p>
                                    <p>[blogname]</p>";
    return $mail_template;
}
add_filter('fre_default_setting_option', 'fre_credit_default_email');