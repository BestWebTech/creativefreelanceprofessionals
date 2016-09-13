<?php
	$options = AE_Options::get_instance();
    // save this setting to theme options
    $website_logo = $options->site_logo;
?>
<style type="text/css">
	#pin_modal .modal-header .close  {
		width: 30px;
		height: 30px;
	}
</style>
<div class="modal fade modal-pin" id="pin_modal" aria-hidden="true">
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

				<form class="modal-form" id="pin_form" novalidate="novalidate" autocomplete="on" data-ajax="false">
					<div class="content clearfix">
						<div class="form-group alert_area" style="display:none">
							<div class="alert alert-danger" role="alert">
								<ul class="response_error">
								</ul>
							</div>
						</div>
						<div class="form-group">
							<div class="controls">
								<div class="form-item-left">
									<label>
										<?php _e('Card number:', ET_DOMAIN);?>
									</label>
									<div class="controls fld-wrap" id="">
										<input tabindex="20" id="cc-number" type="text" value="5520000000000000" size="20"  data-pin="number" class="bg-default-input not_empty" placeholder="&#8226;&#8226;&#8226;&#8226; &nbsp; &nbsp; &nbsp; &#8226;&#8226;&#8226;&#8226; &nbsp; &nbsp; &#8226;&#8226;&#8226;&#8226; &nbsp; &nbsp; &nbsp; &#8226;&#8226;&#8226;&#8226;" />
									</div>
								</div>
								<div class="form-item-right">
								  	<label>
										<?php _e('Expiry date:', ET_DOMAIN);?>
								  	</label>
								 	<div class="pin_date" id="">
									 	<input tabindex="22" type="text" value="5" size="4" data-pin="exp-year" placeholder="MM"  class="bg-default-input not_empty" id="cc-expiry-month"/>
								      	<span> / </span>
								      	<input tabindex="21" type="text" value="16" size="2" data-pin="exp-month" placeholder="YY"  class="bg-default-input not_empty" id="cc-expiry-year"/>
								 	</div>
								</div>
								<div class="clearfix"></div>
							</div>
						</div>


						<div class="form-group">

							<div class="form-item-left">
							  	<label>
									<?php _e('Name on card:',ET_DOMAIN);?>
							  	</label>
							  	<div class="controls name_card " id="">
									<input tabindex="23" name="" id="cc-name"  value="Roland C Robot" data-pin="name" class="bg-default-input not_empty" type="text" />
							 	</div>
							</div>

							<div class="form-item-right">
								<label>
									<?php _e('Card code:', ET_DOMAIN);?>
							  	</label>
							 	<div class="controls card-code" id="">
									<input tabindex="24" type="text" size="3" value="123" data-pin="cvc" class="bg-default-input not_empty input-cvc " placeholder="CVC" id="cc-cvc" />
							  	</div>
							</div>
						</div>
						<div class="form-group">

							<div class="form-item-left">
							  	<label>
									<?php _e('Address line:',ET_DOMAIN);?>
							  	</label>
							  	<div class="controls add_line " id="">
									<input tabindex="23" name="" id="address-line1" value="gfda"  data-pin="address" class="bg-default-input not_empty" type="text" />
							 	</div>
							</div>

						</div>
						<div class="form-group">

							<div class="form-item-left">
							  	<label>
									<?php _e('City:',ET_DOMAIN);?>
							  	</label>
							  	<div class="controls name_card " id="">
									<input tabindex="23" name="" id="address-city" value="bd" data-pin="city" class="bg-default-input not_empty" type="text" />
							 	</div>
							</div>

							<div class="form-item-right">
								<label>
									<?php _e('State:', ET_DOMAIN);?>
							  	</label>
							 	<div class="controls card-code" id="">
									<input tabindex="24" type="text" size="3" value="db" data-pin="state" class="bg-default-input not_empty input-cvc " placeholder="CVC" id="address-state" />
							  	</div>
							</div>

						</div>
						<div class="form-group">

							<div class="form-item-left">
							  	<label>
									<?php _e('Country:',ET_DOMAIN);?>
							  	</label>
							  	<div class="controls name_card " id="">
									<select class="form-control-pin" id="address-country" name="address-country">
										<script type="text/javascript" >
                                            document.write(getCountryOptionsListHtml("<?php echo htmlentities('GB'); ?>"));
                                        </script>
                                    </select>
							 	</div>
							</div>


						</div>
						<div class="form-group">
							<div class="form-item-left">
								<label>
									<?php _e('Postcode:', ET_DOMAIN);?>
							  	</label>
							 	<div class="controls card-code" id="">
									<input tabindex="24" type="text" size="3" value="2600" data-pin="postcode" class="bg-default-input not_empty input-cvc " placeholder="CVC" id="address-postcode" />
							  	</div>
							</div>
						</div>




					</div>
					<div class="footer form-group font-quicksand">
						<div class="button">
							<button class="btn  btn-primary" type="submit"  id="submit_pin"> <?php _e('PAY NOW',ET_DOMAIN);?> </button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="modal-close"></div>
</div>