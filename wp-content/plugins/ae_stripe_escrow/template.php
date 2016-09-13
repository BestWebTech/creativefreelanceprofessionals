<?php
/**
* Card form template
* @param void
* @return void
* @since 1.0
* @package AE_ESCROW
* @category STRIPE
* @author Tambh
*/
function fre_stripe_card_form(){
?>
<div class="form-group">
	<div class="controls">
		<div class="form-item">
			<label>
				<?php _e("Stripe's email:", ET_DOMAIN);?>
			</label>
			<div class="controls fld-wrap" id="">
				<input name="stripe_email" tabindex="19" id="stripe_email" type="text" size="20"  data-stripe="email" class="bg-default-input not_empty" placeholder="youremail@gmail.com" />
			</div>
		</div>
		<div class="form-item-left">
			<label>
				<?php _e('Card number:', ET_DOMAIN);?>
			</label>
			<div class="controls fld-wrap" id="">
				<input name="stripe_number" tabindex="20" id="stripe_number" type="text" size="20"  data-stripe="number" class="bg-default-input not_empty" placeholder="&#8226;&#8226;&#8226;&#8226; &nbsp; &nbsp; &nbsp; &#8226;&#8226;&#8226;&#8226; &nbsp; &nbsp; &#8226;&#8226;&#8226;&#8226; &nbsp; &nbsp; &nbsp; &#8226;&#8226;&#8226;&#8226;" />
			</div>
		</div>
		<div class="form-item-right">
		  	<label>
				<?php _e('Expiry date:', ET_DOMAIN);?>
		  	</label>
		 	<div class="stripe_date" id="">
			 	<input tabindex="22" type="text" size="4" data-stripe="exp-year" placeholder="YY"  class="bg-default-input not_empty" id="exp_year" name="exp_year" />
		      	<span> / </span>								      	
		      	<input tabindex="21" type="text" size="2" data-stripe="exp-month" placeholder="MM"  class="bg-default-input not_empty" id="exp_month" name="exp_month"/>
		 	</div>
		</div>
		<div class="clearfix"></div>
	</div>
</div> 		
<div class="form-group">

	<div class="form-item-left">
	  	<label for="name_card">
			<?php _e('Name on card:',ET_DOMAIN);?>
	  	</label>
	  	<div class="controls name_card " id="">
			<input tabindex="23" name="name_card" id="name_card"  data-stripe="name" class="bg-default-input not_empty" type="text" />
	 	</div>
	</div>

	<div class="form-item-right">
		<label>
			<?php _e('Card code:', ET_DOMAIN);?>
	  	</label>
	 	<div class="controls card-code" id="">
			<input tabindex="24" type="text" size="3"  data-stripe="cvc" class="bg-default-input not_empty input-cvc " placeholder="CVC" id="cvc" name="cvc"/>
	  	</div>
	</div>	
</div>
<?php }
/**
* Update stripe card button
* @param void
* @return void
* @since 1.0
* @package AE_ESCROW
* @category STRIPE
* @author Tambh
*/
function fre_update_stripe_button() { ?>
	<li>
		<div class="update-stripe-container">
			<a href="#" class="btn-update-stripe">
				<i class="fa fa-refresh"></i>
				<?php
				global $user_ID;
				$ae_escrow_stripe = AE_Escrow_Stripe::getInstance();
				$ae_escrow_stripe->init();
				if( $ae_escrow_stripe->ae_get_stripe_user_id($user_ID) ){
					_e('Change Stripe account', ET_DOMAIN );
				}
				else{
					_e('Update Stripe information', ET_DOMAIN);
				} ?>
			</a>
		</div>
	</li>
<?php }
/**
* Stripe card modal
* @param void
* @return void
* @since 1.0
* @package AE_ESCROW
* @category STRIPE
* @author Tambh
*/
function fre_update_stripe_info_modal(){ ?>
<div class="modal fade modal-stripe" id="stripe_escrow_modal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button  style="z-index:1000;" data-dismiss="modal" class="close">×</button>				
				<div class="info slogan">
	      			<h4 class="modal-title"><span class="plan_name"><?php _e("Update your stripe's credit card information", ET_DOMAIN); ?></span></h4>	      			
	    		</div>
			</div>		
			<div class="modal-body">				
				<form class="modal-form" id="stripe_form" novalidate="novalidate" autocomplete="on" data-ajax="false">
					<?php fre_stripe_card_form(); ?>
					<div class="footer form-group font-quicksand">
						<div class="button">  
							<button class="btn  btn-primary" type="submit"  id="submit_stripe"> <?php _e('Update',ET_DOMAIN);?> </button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php 
}
/**
* The field for users update their stripe account
* @param string $html of user escrow field
* @return string $html
* @since 1.0
* @package AE_ESCROW
* @category STRIPE
* @author Tambh
*/
function ae_stripe_recipient_field( $html ){
	$ae_escrow_stripe = AE_Escrow_Stripe::getInstance();
	$ae_escrow_stripe->init();
	global $user_ID;
	if( $ae_escrow_stripe->is_use_stripe_escrow() ) {
		ob_start();
		if( ae_user_role($user_ID) == FREELANCER ) {
			 $ae_escrow_stripe->ae_stripe_connect();
		}
		else{
			fre_update_stripe_button();
		}
		$html = ob_get_clean();
	}
	echo $html;
}
add_action('ae_escrow_stripe_user_field', 'ae_stripe_recipient_field');
/**
* stripe email field
* @param void
* @return void
* @since 1.0
* @package AE_ESCROW
* @category STRIPE
* @author Tambh
*/
function ae_stripe_email(){
	global $user_ID;
	?>
	<div class="form-group">
		<div class="form-group-control">
			<label><?php _e('Your Stripe Account', ET_DOMAIN) ?></label>
			<input type="stripe_email" class="form-control" id="stripe_email" name="stripe_email" value="<?php echo get_user_meta( $user_ID, 'stripe_email', true ); ?>" placeholder="<?php _e('Enter your Stripe email', ET_DOMAIN) ?>">
		</div>
	</div>
	<div class="clearfix"></div>
<?php }
/**
* Notification
* @param void
* @return void
* @since 1.0
* @package AE_ESCROW
* @category STRIPE
* @author Tambh
*/
function ae_stripe_escrow_notification(){ ?>
<script type="text/javascript" id="user-confirm">
	(function ($ , Views, Models) {
		$(document).ready(function(){
			var msg = "<?php _e('You updated successfully!',ET_DOMAIN); ?>";
			alert(msg);
			window.location.href = "<?php echo et_get_page_link('profile'); ?>"
		});
	})(jQuery, window.Views, window.Models);
</script>
<?php }
/**
* disable paypal field
* @param void
* @return void
* @since 1.0
* @package AE_ESCROW
* @category STRIPE
* @author Tambh
*/
add_filter('ae_escrow_recipient_field_html', 'ae_escrow_stripe_field_html');
function ae_escrow_stripe_field_html( $html ){
	$ae_escrow_stripe = AE_Escrow_Stripe::getInstance();	
	if( $ae_escrow_stripe->is_use_stripe_escrow() ){
		return '';
	}
	return $html;
}