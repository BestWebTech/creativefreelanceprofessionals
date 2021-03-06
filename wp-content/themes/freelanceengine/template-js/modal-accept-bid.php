<div class="modal fade" id="acceptance_project" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<i class="fa fa-times"></i>
				</button>
				<h4 class="modal-title">
					<?php _e("Bid acceptance", ET_DOMAIN) ?>
				</h4>
			</div>
			<div class="modal-body">
				<form role="form" id="escrow_bid" class="">
					<div class="escrow-info">
		            	<!-- bid info content here -->
	                </div>
	                <?php  do_action('fre_after_accept_bid_infor'); ?>
					<div class="form-group">
	                    <button type="submit" class="btn-submit btn-sumary btn-sub-create">
	                        <?php _e('Accept Bid', ET_DOMAIN) ?>
	                    </button>
	                </div>
	            </form>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog login -->
</div><!-- /.modal -->
<!-- MODAL BID acceptance PROJECT-->
<script type="text/template" id="bid-info-template">
	<label style="line-height:2.5;"><?php _e( 'You are about to accept this bid for' , ET_DOMAIN ); ?></label>
	<p class="bid-currency"><strong class="text-green-dark">{{=budget}}</strong><strong class="color-green"><i class="fa fa-check"></i></strong></p>
	<br>
	<label style="line-height:2.5;"><?php _e( 'You have to pay' , ET_DOMAIN ); ?><br></label>
	<p class="text-credit-small">
		<span class="info-bid"><?php _e( 'Budget' , ET_DOMAIN ); ?></span>
		<strong>{{= budget }}</strong>
	</p>
	<# if(commission){ #>
	<p class="text-credit-small"><span class="info-bid"><?php _e( 'Commission' , ET_DOMAIN ); ?></span>
		<strong style="color: #1faf67;">{{= commission }}</strong>
	</p>
	<# } #>
	<p class="text-credit-small"><span class="info-bid"><?php _e( 'Total' , ET_DOMAIN ); ?></span>
		<strong class="text-orange-dark">{{=total}}</strong>
	</p>
	<br>
</script>