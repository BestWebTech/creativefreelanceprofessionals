<?php
	$options = AE_Options::get_instance();
    // save this setting to theme options
    $website_logo = $options->site_logo;
?>
<style type="text/css">
	#stripe_modal .modal-header .close  {
		width: 30px;
		height: 30px;
	}
</style>
<div class="modal fade modal-fre-credit" id="fre_credit_modal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php if( !function_exists('et_load_mobile') || !et_load_mobile() ) { ?>
			<div class="modal-header">
				<button  style="z-index:1000;" data-dismiss="modal" class="close">×</button>
				<div class="info slogan">
	      			<h4 class="modal-title"><span class="plan_name">{$plan_name}</span></h4>
	      			<span class="plan_desc">{$plan_description}</span>
	    		</div>
			</div>
		<?php } ?>
			<div class="modal-body">

				<form class="modal-form" id="submit_fre_credit_form" novalidate="novalidate" autocomplete="on" data-ajax="false">
					<div class="content clearfix">
						<div class="form-group">
							<div class="controls">
								<div class="form-item-left">
									<label>
										<?php _e('Your secure code:', ET_DOMAIN);?>
									</label>
									<div class="controls fld-wrap" id="">
										<input tabindex="20" id="fre_credit_secure_code" type="password" size="20" name="fre_credit_secure_code"  class="bg-default-input not_empty" placeholder="" required />
									</div>
								</div>
								<div class="clearfix"></div>
							</div>
						</div>
						<div class="footer form-group font-quicksand">
						<div class="button">
							<button class="btn  btn-primary" type="submit"  id="submit_fre_credit"> <?php _e('PAY TO YOUR BALANCE',ET_DOMAIN);?> </button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="modal-close"></div>
</div>
</div>