<?php 
	$options = AE_Options::get_instance();
    // save this setting to theme options
    $website_logo = $options->site_logo;
?>
<style type="text/css">
	#payu_modal .modal-header .close  {
		width: 30px;
		height: 30px;
        color:#fff;
	}
    .plan_desc{
        color:#428BCA;
    }
    #payu_email{
        height:41px;
        width:100%;
        padding:0 15px
    }
    
</style>
<div class="modal fade modal-sagepay form_modal_style" id="sagepay_modal" aria-hidden="true">
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
				
				<form class="modal-form" id="sagepay_form" action="#" method="POST" autocomplete="on">
                
					<div class="content clearfix">		
						<div class="form-group">
							<div class="controls">
                                <div class="row">    
    								<div class="form-field col-xs-12">
    									<label>
    										<?php _e('First name:*', ET_DOMAIN);?>
    									</label>
    									<div class="controls fld-wrap" >
    										<input  tabindex="20" id="sagepay_firstname" name="sagepay_firstname" type="text" size="20"   class="form-control bg-default-input not_empty required" placeholder="Jonh" />
    									</div>
    								</div>
                                    <div class="form-field col-xs-12">
    									<label>
    										<?php _e('Last name:*', ET_DOMAIN);?>
    									</label>
    									<div class="controls fld-wrap" >
    										<input  tabindex="20" id="sagepay_lastname" name="sagepay_lastname" type="text" size="20"  class="form-control bg-default-input not_empty" placeholder="Smith" />
    									</div>
    								</div>
                                    <div class="form-field col-xs-12">
    									<label>
    										<?php _e('Address:*', ET_DOMAIN);?>
    									</label>
    									<div class="controls fld-wrap" >
    										<input  tabindex="20" id="sagepay_billingadress" name="sagepay_billingadress" type="text" size="20"  class="form-control bg-default-input not_empty" placeholder="" />
    									</div>
    								</div>
                                    <div class="form-field col-xs-12">
    									<label>
    										<?php _e('City:*', ET_DOMAIN);?>
    									</label>
    									<div class="controls fld-wrap" >
    										<input  tabindex="20" id="sagepay_billingcity" name="sagepay_billingcity" type="text" size="20"  class="form-control bg-default-input not_empty" placeholder="" />
    									</div>
    								</div>
                                    <div class="form-field col-xs-12">
    									<label>
    										<?php _e('Post/Zip Code:', ET_DOMAIN);?>
    									</label>
    									<div class="controls fld-wrap" >
                                            <input tabindex="20" id="sagepay_postcode" name="sagepay_postcode" type="text" size="20"  class="form-control bg-default-input not_empty" placeholder=""  />
    									</div>
    								</div>
                                    <div class="form-field col-xs-12">
    									<label>
    										<?php _e('Country:*', ET_DOMAIN);?>
    									</label>
    									<div class="controls fld-wrap" >
                                            <select class="form-control" id="sagepay_country" name="sagepay_country">
        										<script type="text/javascript" >
                                                    document.write(getCountryOptionsListHtml("<?php echo htmlentities('GB'); ?>"));
                                                </script>
                                            </select>
    									</div>
    								</div>
                                    <div class="form-field col-xs-12">
    									<label>
    										<?php _e('State Code (U.S. only):', ET_DOMAIN);?>
    									</label>
    									<div class="controls fld-wrap" >
                                            <select class="form-control" aria-describedby="helpBlock" id="sagepay_state">
        										<script type="text/javascript" >
                                                    document.write(getUsStateOptionsListHtml("<?php echo htmlentities(''); ?>"));
                                                </script>
                                            </select>  
                                            <span id="helpBlock" class="help-block">(* for U.S. customers only)</span>     
    									</div>
    								</div>
                                </div>
							</div>
						</div>	
					</div>
					<div class="footer form-group font-quicksand">
						<div class="button">  
							<button type="submit" class="btn  btn-primary" id="button_sagepay" ><?php _e('PAY NOW WITH SAGEPAY GETWAY',ET_DOMAIN); ?> </button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="modal-close"></div>
</div>
<div style="display: none; height: 0; width:0;">
     <form method="post" action="#" id="sagepay_hidden_form">
        <input type="hidden" name="Vendor" />
        <input type="hidden" name="VPSProtocol" value="3.00"/>
        <input type="hidden" name="TxType" value="PAYMENT"/>
        <input type="hidden" name="Crypt"/>
        <button type="submit" class="btn " id="submit_sagepay" >Submit </button>
     </form>
</div>