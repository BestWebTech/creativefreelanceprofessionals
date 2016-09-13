<?php
/**
 * Plugin  template
 * @param void
 * @return void
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Jack Bui
*/

if( !function_exists('fre_credit_template_payToSubmitProject_button') ){
    /**
      * html template for pay to submit project button
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    function fre_credit_template_payToSubmitProject_button(){ ?>
        <li class="fre-credit-payment-onsite">
        <span class="title-plan fre-credit-payment" data-type="frecredit">
            <?php
            _e("Your balance", ET_DOMAIN); ?>
            <span><?php
                _e("Send the payment by using your balance", ET_DOMAIN); ?></span>
        </span>
            <a href="#" class="btn btn-submit-price-plan other-payment btn-fre-credit-payment" data-type="frecredit"><?php
                _e("Select", ET_DOMAIN); ?></a>
        </li>
<?php }
}
if( !function_exists('fre_credit_deposit_template') ){
    /**
      * html template for deposit page
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    function fre_credit_deposit_template(){
        if( et_load_mobile() ){
            include dirname(__FILE__) . '/template/fre-credit-deposit.php';
        }
        else {
            include dirname(__FILE__) . '/template/fre-credit-deposit.php';
        }
    }
}
add_shortcode( 'fre_credit_deposit', 'fre_credit_deposit_template' );
if( !function_exists('fre_credit_secure_code_field')) {
    /**
     * secure code field
     *
     * @param void
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function fre_credit_secure_code_field(){ ?>
        <div class="form-group">
            <div class="controls">
                <div class="form-item-left">
                    <label>
                        <?php _e('Enter your secure code:', ET_DOMAIN);?>
                    </label>
                    <div class="controls fld-wrap" id="">
                        <input tabindex="20" id="fre_credit_secure_code" type="password" size="20" name="fre_credit_secure_code"  class="bg-default-input not_empty" placeholder="" required />
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
   <?php }
}
if( !function_exists('fre_credit_add_profile_tab') ){
    /**
      * add tab credit to profile page
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    function fre_credit_add_profile_tab(){ ?>
        <li>
            <a href="#credits" role="tab" data-toggle="tab">
                <?php _e('Credits', ET_DOMAIN) ?>
            </a>
        </li>
   <?php }
}
add_action( 'fre_profile_tabs', 'fre_credit_add_profile_tab');
if( !function_exists('fre_credit_add_profile_tab_content') ){
    /**
      * credit tab content
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    function fre_credit_add_profile_tab_content(){
        global $user_ID;
        $user_role = ae_user_role($user_ID);
        $total = fre_credit_get_user_total_balance($user_ID);
        $available = FRE_Credit_Users()->getUserWallet($user_ID);
        $freezable = FRE_Credit_Users()->getUserWallet($user_ID, 'freezable');

        $email_paypal = get_user_meta($user_ID, 'email-paypal-credit', true);
        $banking_info = get_user_meta($user_ID, 'bank-info-credit', true);

        if( $user_role == 'freelancer' ) {
            $tooltip_frozen = __('Your withdraw request is waiting for admin approval.', ET_DOMAIN); 
        }elseif( $user_role == 'employer' ){
            $tooltip_frozen = __('Your project commission & fee have been sent to admin under Escrow system. Otherwise, your withdraw request is waiting for admin approval.', ET_DOMAIN);
        }else{
            $tooltip_frozen = '';
        }

        ?>
        <div class="tab-pane fade tabs-credits" id="credits">
            <!-- Infomation Account Credit-->
            <div class="changelog balance">
                <div class="bar-title">
                    <p class="title"><?php _e('Payment Method', ET_DOMAIN ); ?></p>
                </div>
                <div>
                    <span class="email-credit available"><?php _e('Paypal Account:', ET_DOMAIN)?></span>
                    <?php if(empty($email_paypal)){?>
                        <a href="#" class="btn-edit-email-credit" data-toggle="modal" data-target="#" >
                            <?php _e('Update your Paypal account information', ET_DOMAIN);?>
                        </a>
                    <?php }else{ ?>
                        <span class="budget"><?php echo $email_paypal?></span>
                        <a href="#" class="btn-edit-email-credit" data-toggle="modal" data-target="#" >
                            <?php _e('Change', ET_DOMAIN);?>
                        </a>
                    <?php } ?>
                </div>
                <div>
                    <span class="email-bank available"><?php _e('Banking Information:', ET_DOMAIN)?></span>
                    <?php if(empty($banking_info)){?>
                        <a href="#" class="btn-update-bank" data-toggle="modal" data-target="#" >
                            <?php _e('Update your Banking account information', ET_DOMAIN);?>
                        </a>
                    <?php }else{ ?>
                        <span class="budget"><?php echo $banking_info['benficial_owner']?></span>
                        <a href="#" class="btn-update-bank" data-toggle="modal" data-target="#" >
                            <?php _e('Change', ET_DOMAIN);?>
                        </a>
                    <?php } ?>
                </div>
            </div>
            <!-- Infomation Account Credit-->
            
            <div class="balance">
                <div class="balance-credit">
                    <span class="credit"><?php _e('Credit balance', ET_DOMAIN) ?></span>
                    <span class="text-blue-light buget fre_credit_total_text"><?php echo fre_price_format($total->balance) ?></span>
                </div>
                <div class="balance-available">
                    <span class="available"><?php _e('Available balance', ET_DOMAIN); ?></span>
                    <span class="text-green-dark buget fre_credit_available_text"><?php echo fre_price_format($available->balance) ?></span>
                </div>
                <div class="balance-frozen">
                    <span class="frozen"><?php _e('Frozen balance', ET_DOMAIN) ?> 
                      <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="top" title="<?php echo $tooltip_frozen; ?>">
                          <i class="fa fa-info-circle"></i>
                      </button>
                      </span>
                    <span class="buget fre_credit_freezable_text"><?php echo fre_price_format($freezable->balance) ?></span>
                </div>
                <div class="button-functions">
                    <button class="btn-sumary btn-add blue-light box-shadow-button-blue" onClick="window.location.href='<?php echo fre_credit_deposit_page_link() ?>'"><i class="fa fa-plus-circle"></i></button>
                    <button class="btn-sumary btn-withdraw green-dark box-shadow-button-green btn-withdraw-action" data-toggle="modal" data-target="#"><i class="fa fa-arrow-down"></i><?php _e('Withdraw', ET_DOMAIN); ?></button>
                </div>
            </div>
            <div class="changelog fre-credit-history-wrapper">
                <div class="bar-title">
                    <p class="title"><?php _e('Credits changelog', ET_DOMAIN ); ?></p>
                    <div class="function-filter">
                        <div class="value">
                            <span class="dropbox"><?php _e('All transaction', ET_DOMAIN); ?> <i class="fa fa-angle-down"></i></span>
                            <ul class="list-value hide fre-credit-history-filter">
                                <li><a href="#" class="text-blue-light" data-value=""><i class="fa fa-arrow-up"></i><?php _e('All Transaction', ET_DOMAIN); ?></a></li>
                                <li><a href="#" class="text-blue-light" data-value="deposit"><i class="fa fa-arrow-up"></i><?php _e('Deposit', ET_DOMAIN); ?></a></li>
                                <li><a href="#" class="text-green-dark" data-value="withdraw"><i class="fa fa-arrow-down"></i><?php _e('Withdraw', ET_DOMAIN); ?></a></li>
                                <li><a href="#" class="text-blue-light" data-value="transfer"><i class="fa fa-arrow-right"></i><?php _e('Receive', ET_DOMAIN); ?></a></li>
                                <li><a href="#" class="text-orange-dark" data-value="charge"><i class="fa fa-minus"></i><?php _e('Paid', ET_DOMAIN); ?></a></li>
                            </ul>
                        </div>
                        <div class="date">
                            <span><?php _e('From', ET_DOMAIN); ?></span>
                            <input type='text' class="" id='fre_credit_from' placeholder="--/--/----"/>
                            <span><?php _e('to', ET_DOMAIN); ?></span>
                            <input type='text' class="" id='fre_credit_to'  placeholder="--/--/----"/>
                        </div>
                    </div>
                </div>

                <div class="changelog-list">
                    <ul class="list-histories">
                        <?php
                            global $post,$wp_query, $ae_post_factory;
                            $args = array(
                                'post_type'=> 'fre_credit_history',
                                'post_status'=> 'publish',
                                'paged'=>1,
                                'author'=> $user_ID
                            );
                            $new_query = new WP_Query($args);
                            $post_data = array();
                            if( $new_query->have_posts() ):
                                while( $new_query->have_posts() ):
                                    $new_query->the_post();
                                    $his_obj = $ae_post_factory->get('fre_credit_history');
                                    $convert = $his_obj->convert($post);
                                    $post_data[]= $convert;
                                    include dirname(__FILE__) . '/template/fre-credit-history-item.php';
                                endwhile;
                            else:
                                _e("There isn't any transaction!", ET_DOMAIN);
                            endif;?>
                    </ul>
                    <div class="col-md-12">
                        <div class="paginations-wrapper">
                            <?php
                            ae_pagination($new_query, get_query_var('paged'), 'page');
                            ?>
                        </div>
                    </div>
                    <?php echo '<script type="data/json" class="fre_credit_history_data" >' . json_encode($post_data) . '</script>'; ?>
                </div>
            </div>
        </div>
 <?php  }
}
//add_action( 'fre_profile_tabs_on_mobile', 'fre_credit_add_profile_tab_on_mobile');
add_action( 'fre_profile_tab_content', 'fre_credit_add_profile_tab_content');
if( !function_exists('fre_credit_add_profile_tab_on_mobile') ){
    /**
      * add tab credit on mobile version
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    function fre_credit_add_profile_tab_on_mobile(){ ?>
        <li>
            <a href="<?php echo et_get_page_link('profile'); ?>#tab_credits" class="link-menu-nav">
                <?php _e('Credits', ET_DOMAIN) ?>
            </a>
        </li>
        <li>
            <a href="#" class="request-secure-code">
                <i class="fa fa-key"></i>
                <?php
                global $user_ID;
                if( !FRE_Credit_Users()->getSecureCode($user_ID) ) {
                    _e("Request a new Secure Code", ET_DOMAIN);
                }
                else{
                    _e("Reset Secure Code", ET_DOMAIN);
                }
                ?>
            </a>
        </li>
<?php }
}
add_action( 'fre_profile_tabs_on_mobile', 'fre_credit_add_profile_tab_on_mobile');
if( !function_exists('fre_credit_add_profile_mobile_tab_content') ){
    /**
      * add credit content to mobile version
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    function fre_credit_add_profile_mobile_tab_content(){
        global $user_ID;
        $user_role = ae_user_role($user_ID);
        $total = fre_credit_get_user_total_balance($user_ID);
        $available = FRE_Credit_Users()->getUserWallet($user_ID);
        $freezable = FRE_Credit_Users()->getUserWallet($user_ID, 'freezable');

        $email_paypal = get_user_meta($user_ID, 'email-paypal-credit', true);
        $banking_info = get_user_meta($user_ID, 'bank-info-credit', true);

        if( $user_role == 'freelancer' ) {
            $tooltip_frozen = __('Your withdraw request is waiting for admin approval.', ET_DOMAIN); 
        }elseif( $user_role == 'employer' ){
            $tooltip_frozen = __('Your project commission & fee have been sent to admin under Escrow system. Otherwise, your withdraw request is waiting for admin approval.', ET_DOMAIN);
        }else{
            $tooltip_frozen = '';
        }
        ?>
        <div class="tabs-credits tab-profile mobile-tab-profile" id="tab_credits">

            <!-- Infomation Account Credit-->
            <!-- <div class="changelog balance"> -->
                <div class="top-bar">
                  <span class="text-package "><?php _e('Payment Method', ET_DOMAIN ); ?></span>
                </div>
                <div class="balance">
                <div>
                    <span class="email-credit available"><?php _e('Paypal Account:', ET_DOMAIN)?></span>
                    <?php if(empty($email_paypal)){?>
                        <a href="#" class="btn-edit-email-credit" data-toggle="modal" data-target="#" >
                            <?php _e('Update your Paypal account information', ET_DOMAIN);?>
                        </a>
                    <?php }else{ ?>
                        <span class="budget"><?php echo $email_paypal?></span>
                        <a href="#" class="btn-edit-email-credit" data-toggle="modal" data-target="#" >
                            <?php _e('Change', ET_DOMAIN);?>
                        </a>
                    <?php } ?>
                </div>
                <div>
                    <span class="email-bank available"><?php _e('Banking Information:', ET_DOMAIN)?></span>
                    <?php if(empty($banking_info)){?>
                        <a href="#" class="btn-update-bank" data-toggle="modal" data-target="#" >
                            <?php _e('Update your Banking account information', ET_DOMAIN);?>
                        </a>
                    <?php }else{ ?>
                        <span class="budget"><?php echo $banking_info['benficial_owner']?></span>
                        <a href="#" class="btn-update-bank" data-toggle="modal" data-target="#" >
                            <?php _e('Change', ET_DOMAIN);?>
                        </a>
                    <?php } ?>
                </div>
            </div>
            <!-- Infomation Account Credit-->
            <div class="top-bar">
                <span class="text-package "><?php _e('Credit balance', ET_DOMAIN); ?></span>
                <span class="text-price fre_credit_total_text"><?php echo fre_price_format($total->balance) ?></span>
            </div>
            <div class="balance">
                <div class="row">
                    <div class="col-xs-6 available">
                        <p><?php _e('Available balance', ET_DOMAIN ) ?></p>
                        <p class="price fre_credit_available_text"><?php echo fre_price_format($available->balance) ?></p>
                    </div>
                    <div class="col-xs-6 frozen">
                        <p><?php _e('Frozen balance', ET_DOMAIN); ?></p>
                        <p class="price fre_credit_freezable_text"><?php echo fre_price_format($freezable->balance) ?></p>
                    </div>
                </div>
            </div>
            <div class="button-functions">
                <a href="<?php echo fre_credit_deposit_page_link() ?>" class="btn-recharge"><?php _e('Recharge', ET_DOMAIN); ?></a>
                <a href="#" class="btn-withdraw btn-withdraw-action"><?php _e('Withdraw', ET_DOMAIN); ?></a>
            </div>
            <div class="change-log fre-credit-history-wrapper">
                <div class="top-bar">
                    <span class="text-package"><?php _e('Credit changelog', ET_DOMAIN); ?></span>
                </div>
                <div class="value">
                    <span class="dropbox"><?php _e('All transaction', ET_DOMAIN); ?> <i class="fa fa-angle-down"></i></span>
                    <ul class="list-value hide fre-credit-history-filter">
                        <li><a href="#" class="text-blue-light" data-value=""><i class="fa fa-arrow-up"></i><?php _e('All Transaction', ET_DOMAIN); ?></a></li>
                        <li><a href="#" class="text-blue-light" data-value="deposit"><i class="fa fa-arrow-up"></i><?php _e('Deposit', ET_DOMAIN); ?></a></li>
                        <li><a href="#" class="text-green-dark" data-value="withdraw"><i class="fa fa-arrow-down"></i><?php _e('Withdraw', ET_DOMAIN); ?></a></li>
                        <li><a href="#" class="text-blue-light" data-value="transfer"><i class="fa fa-arrow-right"></i><?php _e('Receive', ET_DOMAIN); ?></a></li>
                        <li><a href="#" class="text-orange-dark" data-value="charge"><i class="fa fa-minus"></i><?php _e('Paid', ET_DOMAIN); ?></a></li>
                    </ul>
                </div>
                <div class="line-white"></div>
                <div class="date">
                    <span><?php _e('From', ET_DOMAIN); ?></span>
                    <input type='text' class="" id='fre_credit_from' />
                    <span><?php _e('to', ET_DOMAIN); ?></span>
                    <input type='text' class="" id='fre_credit_to' />
                </div>
                <div class="change-log-list">
                    <ul class="list-histories">
                        <?php
                        global $post,$wp_query, $ae_post_factory;
                        $args = array(
                            'post_type'=> 'fre_credit_history',
                            'post_status'=> 'publish',
                            'paged'=>1,
                            'author'=> $user_ID
                        );
                        $new_query = new WP_Query($args);
                        $post_data = array();
                        if( $new_query->have_posts() ):
                            while( $new_query->have_posts() ):
                                $new_query->the_post();
                                $his_obj = $ae_post_factory->get('fre_credit_history');
                                $convert = $his_obj->convert($post);
                                $post_data[]= $convert;
                                include dirname(__FILE__) . '/template/fre-credit-history-item.php';
                            endwhile;
                        else:
                            _e("There isn't any transaction!", ET_DOMAIN);
                        endif;?>
                    </ul>
                    <div class="col-md-12">
                        <div class="paginations-wrapper">
                            <?php
                            ae_pagination($new_query, get_query_var('paged'), 'load');
                            ?>
                        </div>
                    </div>
                    <?php echo '<script type="data/json" class="fre_credit_history_data" >' . json_encode($post_data) . '</script>'; ?>
                </div>
            </div>
        </div>

<?php }
}
add_action('fre_profile_mobile_tab_content', 'fre_credit_add_profile_mobile_tab_content');
if( !function_exists('fre_credit_modal_withdraw') ){
    /**
      * modal withdraw
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    function fre_credit_modal_withdraw(){ ?>
        <!--Show modal-->
        <div class="modal fade withdraw" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                        <h4 class="modal-title small-modal-title" id="myModalLabel"><?php _e('Credit withdraw', ET_DOMAIN);?></h4>
                    </div>
                    <div class="modal-body">
                        <form id="fre_credit_withdraw_form" >
                            <div class="balance-withdraw">
                                <div class="current">
                                    <span><?php _e('Current total credit', ET_DOMAIN) ; ?></span>
                                    <span class="price text-blue-light fre_credit_total"><?php echo  fre_price_format(0); ?></span>
                                </div>
                                <div class="available">
                                    <span><?php _e('Available credit', ET_DOMAIN); ?></span>
                                    <span class="price text-green-dark fre_credit_available"><?php echo  fre_price_format(0); ?></span>
                                </div>
                                <div class="frozen">
                                    <span><?php _e('Frozen credit', ET_DOMAIN); ?></span>
                                    <span class="price fre_credit_freezable"><?php echo  fre_price_format(0); ?></span>
                                </div>
                            </div>
                            <div class="amount-withdraw">
                                <p class="title"><?php _e('Withdraw amount', ET_DOMAIN); ?> </p>
                                <p class="price-amount"><?php _e('Minimum amount is', ET_DOMAIN); ?> <span class="fre_credit_min_withdraw"><?php fre_price_format(0)?></span></p>
                                <div class="input-amount">
                                    <input type="number" name="amount" required  value="">
                                </div>
                            </div>
                            <div class="payment">
                                <?php 
                                    global $user_ID;
                                    $email_paypal = get_user_meta($user_ID, 'email-paypal-credit', true);
                                    $banking_info = get_user_meta($user_ID, 'bank-info-credit', true);
                                    if(empty($email_paypal) || empty($banking_info)){
                                ?>
                                    <label><?php _e('Payment information', ET_DOMAIN); ?></label>
                                    <textarea name="payment_info"></textarea>
                                <?php }else{ ?>
                                    <label><?php _e('Payment method', ET_DOMAIN); ?></label>
                                    <select name="payment_method" required style="float: right;width: 123px;">
                                        <?php 
                                            if(!empty($email_paypal)){
                                              echo "<option value='email-paypal-credit'>".__('Paypal', ET_DOMAIN)."</option>";
                                            }
                                            if(!empty($banking_info)){
                                              echo "<option value='bank-info-credit'>".__('Bank', ET_DOMAIN)."</option>";
                                            }
                                        ?>
                                    </select>
                                    <br>
                                    <label><?php _e('Note', ET_DOMAIN); ?></label>
                                    <textarea name="payment_info"></textarea>
                                <?php } ?>
                            </div>
                            <div class="amount-withdraw security-code">
                                <p class="title"><?php _e('Security code', ET_DOMAIN); ?></p>
                                <p class="price-amount"><?php _e('Please enter the code that we provided you when register.', ET_DOMAIN); ?></p>
                                <div class="input-amount">
                                    <input type="password" name="secureCode" value="">
                                </div>
                            </div>
                            <button type="submit" class="btn-sumary blue-light box-shadow-button-blue btn-submit"><?php _e('Submit', ET_DOMAIN); ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<?php }
}

if(!function_exists('fre_credit_modal_update_paypal')){
    /**
      * modal edit Email Paypal
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author ThanhTu
      */
    function fre_credit_modal_update_paypal(){
?>
        <div class="modal fade email_paypal" id="modalEditPaypal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                        <h4 class="modal-title small-modal-title" id="myModalLabel"><?php _e('Update Paypal account information', ET_DOMAIN);?></h4>
                    </div>
                    <div class="modal-body">
                        <form id="fre_credit_edit_paypal_form" >
                          <div class="form-group">
                              <label for="credit_email_paypal"><?php _e('Paypal Account', ET_DOMAIN) ?></label>
                              <input type="text" class="form-control" id="email_paypal" name="email_paypal" placeholder="<?php _e('Enter your email paypal', ET_DOMAIN) ?>">
                          </div>
                          <div class="form-group">
                              <label for="verify_secure_code"><?php _e('Secure Code', ET_DOMAIN) ?></label>
                              <input type="text" class="form-control" id="secure_code" name="secure_code" placeholder="<?php _e('Enter secure code', ET_DOMAIN) ?>">
                          </div>  
                          <button type="submit" class="btn-sumary blue-light box-shadow-button-blue btn-submit">
                              <?php _e('Update', ET_DOMAIN); ?>
                          </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
}

if(!function_exists('fre_credit_modal_update_bank')){
    /**
      * modal edit Email Paypal
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author ThanhTu
      */
    function fre_credit_modal_update_bank(){
?>
        <div class="modal fade email_paypal" id="modalUpdateBank" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                        <h4 class="modal-title small-modal-title" id="myModalLabel"><?php _e('Update Banking Information', ET_DOMAIN);?></h4>
                    </div>
                    <div class="modal-body">
                        <form id="fre_credit_updat_bank_form" >
                          <div class="form-group">
                              <label for="benficial_owner"><?php _e('Benficial Owner', ET_DOMAIN) ?></label>
                              <input type="text" class="form-control" id="benficial_owner" name="benficial_owner" >
                          </div>
                          <div class="form-group">
                              <label for="account_number"><?php _e('Account number', ET_DOMAIN) ?></label>
                              <input type="text" class="form-control" id="account_number" name="account_number">
                          </div>  
                          <div class="form-group">
                              <label for="banking_information"><?php _e('Banking Information', ET_DOMAIN) ?></label>
                              <textarea id="banking_information" name="banking_information"></textarea>
                          </div>    
                          <div class="form-group">
                              <label for="secure_code"><?php _e('Secure Code', ET_DOMAIN) ?></label>
                              <input type="text" class="form-control" id="secure_code" name="secure_code" placeholder="<?php _e('Enter secure code', ET_DOMAIN) ?>">
                          </div>                       
                          <button type="submit" class="btn-sumary blue-light box-shadow-button-blue btn-submit">
                              <?php _e('Update', ET_DOMAIN); ?>
                          </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
}

if( !function_exists('fre_credit_add_template') ){
    /**
      * add template to footer
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    function fre_credit_add_template(){
        fre_credit_modal_update_bank();
        fre_credit_modal_update_paypal();
        fre_credit_modal_withdraw();
        include_once dirname(__FILE__) . '/template/fre-credit-history-item-js.php';
    }
}
add_action('wp_footer', 'fre_credit_add_template');
if( !function_exists('fre_credit_add_request_secure_code') ) {
    /**
     * add secure code
     *
     * @param void
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function fre_credit_add_request_secure_code(){ ?>
        <li>
            <a href="#" class="request-secure-code">
                <i class="fa fa-key"></i>
                <?php
                    global $user_ID;
                    if( !FRE_Credit_Users()->getSecureCode($user_ID) ) {
                        _e("Request a new Secure Code", ET_DOMAIN);
                    }
                    else{
                        _e("Reset Secure Code", ET_DOMAIN);
                    }
                ?>
            </a>
        </li>

<?php     }

}
add_action('fre-profile-after-list-setting', 'fre_credit_add_request_secure_code');