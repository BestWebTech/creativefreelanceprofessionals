<?php
/**
 *
 */

$payment_type			= get_query_var( 'paymentType' );

$session	=	et_read_session ();
//processs payment
if ($payment_type == 'paypaladaptive') {
	$payment_return = fre_process_escrow($payment_type , $session );
}else{
	$payment_return = ae_process_payment($payment_type , $session );
}

$ad_id		=	$session['ad_id'];

et_get_mobile_header();

global $ad , $payment_return;

$payment_return	=	wp_parse_args( $payment_return, array('ACK' => false, 'payment_status' => '' ));
extract( $payment_return );
if($session['ad_id'])
	$ad	=	get_post( $session['ad_id'] );
else
	$ad	=	false;

?>
<div class="container">
	<!-- block control  -->
	<div class="row block-posts" id="post-control">
		<div class="col-md-12 posts-container" id="posts_control">
			<div class="blog-wrapper post-item single">
			    <div class="row">
			        <div class="col-md-12 col-xs-12">
			            <div class="blog-content">
			                <h2 class="title-blog">
			                	<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
			                		<?php the_title(); ?>
			                	</a>
			                </h2>
			            </div>
			        </div>
			    </div>
			</div>
		</div><!-- SINGLE TITLE + CATEGORY -->

		<div class="clearfix"></div>

		<div class="col-md-12 col-xs-12 blog-content-wrapper">
			<div class="blog-content">
				<?php
					if ( $payment_type == 'paypaladaptive' ) {
						$permalink	=	get_permalink( $ad->ID );
						if( isset($ACK) && $ACK  ) {
							$permalink = add_query_arg(array('workspace' => 1), $permalink );
							$workspace = '<a href="'.$permalink.'">'.get_the_title($ad->ID).'</a>';
							printf(__( 'You have successfully sent the money. Now you can start working on project %s .' , ET_DOMAIN ), $workspace);
							/**
							 * template payment success
							*/
							// redirect to workspace

						 } else {
						 	// redirect to project place
							_e( 'Accept bid fail !' , ET_DOMAIN );
							// echo $payment_return['msg'];
						}
					}else{
						// if( ( isset($ACK) && $ACK ) || (isset($test_mode) && $test_mode) ) {
						if( ( isset($ACK) && $ACK ) ) {
							if($ad) :
								$permalink	=	get_permalink( $ad->ID );
							else:
								$permalink = home_url();
							endif;
							/**
							 * template payment success
							*/
							get_template_part( 'template/payment' , 'success' );
						 } else {
							if($ad)
								$permalink	=	et_get_page_link('submit-project', array( 'id' => $ad->ID ));
							else
								$permalink	=	et_get_page_link('submit-project');
							/**
							 * template payment fail
							*/
							get_template_part( 'template/payment' , 'fail' );
						}
					}
						// clear session
						et_destroy_session();

						?>
				<br/>
				</br/>
			</div>
		</div><!-- SINGLE CONTENT -->

        <div class="clearfix"></div>
	</div>
	<!--// block control  -->
</div>
<!-- Page Blog / End -->
<script type="text/javascript">
  	jQuery(document).ready (function () {
  		var $count_down	=	jQuery('.count_down');
		setTimeout (function () {
			window.location = '<?php echo $permalink ?>';
		}, 10000 );
		setInterval (function () {
			if($count_down.length >  0) {
				var i	=	 $count_down.html();
				$count_down.html(parseInt(i) -1 );
			}
		}, 1000 );
  	});
</script>

<?php
et_get_mobile_footer();