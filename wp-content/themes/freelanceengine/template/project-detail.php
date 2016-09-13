<?php
/**
 * The template for displaying project detail heading, author info and action button
 */
global $wp_query, $ae_post_factory, $post, $user_ID;
$post_object    = $ae_post_factory->get(PROJECT);
$convert = $project = $post_object->current_post;

$et_expired_date    = $convert->et_expired_date;
$bid_accepted       = $convert->accepted;
$project_status     = $convert->post_status;
$profile_id         = get_user_meta($post->post_author,'user_profile_id', true);

$currency           = ae_get_option('content_currency',array('align' => 'left', 'code' => 'USD', 'icon' => '$'));

?>




<div class="col-md-12">
	<div class="tab-content-project">
    	<!-- Title -->
    	<div class="row title-tab-project">
            <div class="col-lg-2 col-md-3 col-sm-2 col-xs-7">
                <span><?php _e("PROJECT TITLE", ET_DOMAIN); ?></span>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 hidden-xs">
                <span><?php _e("BY", ET_DOMAIN); ?></span>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-3 hidden-sm hidden-md hidden-xs">
                <span><?php _e("POSTED DATE", ET_DOMAIN); ?></span>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs">
                <span><?php _e("BUDGET", ET_DOMAIN); ?></span>
            </div>
			  <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs">
                <span><?php _e("Project Task Codes", ET_DOMAIN); ?></span>
            </div>
        </div>
        <!-- Title / End -->
        <!-- Content project -->
        <div class="single-projects">
            <div class="project type-project project-item">
                <div class="row">
                    <div class="col-lg-2 col-md-3 col-sm-2 col-xs-7">
                        <a href="<?php echo get_author_posts_url( $post->post_author ); ?>" class="avatar-author-project-item">
                            <?php echo get_avatar( $post->post_author, 35,true, get_the_title($profile_id) ); ?>
                        </a>
                        <h1 class="content-title-project-item"><?php the_title();?></h1>
                    </div>
                     <div class="col-lg-2 col-md-2 col-sm-2 hidden-xs">
                      	<span class="author-link-project-item"><?php the_author_posts_link();?></span>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 hidden-sm hidden-md hidden-xs">
                         <span  class="time-post-project-item"><?php the_date(); ?></span>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-2 hidden-xs">
                        <span class="budget-project-item"> <?php echo $convert->budget; ?></span>
                    </div>
					 <div class="col-lg-1 col-md-1 col-sm-2 hidden-xs">
                        <span class="budget-project-item"> <?php $postId= get_the_ID(); echo $postId; ?></span>
                    </div>
					<div class="col-lg-2 col-md-2 col-sm-3 hidden-xs">
				
					

<?php
$currentUserId= get_current_user_id() ;
$UserMeta= get_user_meta( $currentUserId , '_save_postids', true );
$currentSavePostIds =unserialize($UserMeta);


	if(!empty($currentSavePostIds) && in_array($postId,$currentSavePostIds)){
	echo 'Saved job' ;
}
else{
	 $nonce = wp_create_nonce("my_user_vote_nonce");
	$link = admin_url('admin-ajax.php?action=my_user_vote&post_id='.$post->ID.'&nonce='.$nonce);
    echo '<a class="user_vote" data-nonce="' . $nonce . '" data-post_id="' . $post->ID . '" href="' . $link . '">Save Job</a>';
  
}
?>
					<div id="vote_counter"></div>
					</div>
					
                    <div class="col-lg-1 col-md-2 text-right col-sm-2 col-xs-3" style="padding:0; margin:0;">
                    
					<?php
                    if(current_user_can( 'manage_options' ) && $project_status != 'close') {
                        get_template_part( 'template/admin', 'project-control' );
                    }elseif( !$user_ID && $project_status == 'publish'){ ?>
                        <a href="#"  class="btn btn-apply-project-item btn-login-trigger" ><?php  _e('Bid',ET_DOMAIN);?></a>
                    <?php } else {
                        $role = ae_user_role();
                        switch ($project_status) {
                            case 'publish':
                                if( ( fre_share_role() || $role == FREELANCER ) && $user_ID != $project->post_author ){
                                    $has_bid = fre_has_bid( get_the_ID() );
                                    if( $has_bid ) {
                                        ?>
                                        <a rel="<?php echo $project->ID;?>" href="#" id="<?php echo $has_bid;?>" title= "<?php _e('Cancel this bidding',ET_DOMAIN); ?>"  class="btn btn-apply-project-item btn-del-project" >
                                            <?php  _e('Cancel',ET_DOMAIN);?>
                                        </a>
                                    <?php
                                    } else{
                                        $target = '#modal_bid';
                                        $href = '#';
                                        if( !can_user_bid( $user_ID ) ){
                                            $target = '';
                                            $href = et_get_page_link('upgrade-account');
                                        }
                                        ?>
                                        <a href="<?php echo $href; ?>"  class="btn btn-apply-project-item btn-project-status" data-toggle="modal" data-target="<?php echo $target; ?>">
                                            <?php  _e('Bid ',ET_DOMAIN);?>
                                        </a>
                                    <?php }
                                }else { ?>
                                    <a href="#" id="<?php the_ID();?>"  class="btn btn-apply-project-item" ><?php  _e('Opening',ET_DOMAIN);?></a>
                                    <?php
                                }
                                break;
                            case 'close':
                                if( (int)$project->post_author == $user_ID){ ?>

                                    <a title="<?php  _e('Finish',ET_DOMAIN);?>" href="#" id="<?php the_ID();?>"   class="btn btn-apply-project-item btn-project-status btn-complete-project" >
                                        <?php  _e('Finish',ET_DOMAIN);?>
                                    </a>
                                    <a title="<?php _e('Close',ET_DOMAIN);?>" href="#" id="<?php the_ID();?>"   class="btn btn-apply-project-item btn-project-status btn-close-project" >
                                        <?php _e('Close',ET_DOMAIN);?>
                                    </a>
                                    <?php
                                }else{
                                    $bid_accepted_author = get_post_field( 'post_author', $bid_accepted);
                                    if($bid_accepted_author == $user_ID) {
                                ?>
                                    <a title="<?php  _e('Discontinue',ET_DOMAIN);?>" href="#" id="<?php the_ID();?>"   class="btn btn-apply-project-item btn-project-status btn-quit-project" >
                                        <?php  _e('Discontinue',ET_DOMAIN);?>
                                    </a>
                                <?php }
                                }
                                break;
                            case 'complete' :
                                $freelan_id  = (int)get_post_field('post_author',$project->accepted);

                                $comment = get_comments( array('status'=> 'approve', 'type' => 'fre_review', 'post_id'=> get_the_ID() ) );

                                if( $user_ID == $freelan_id && empty( $comment ) ){ ?>
                                    <a href="#" id="<?php the_ID();?>"   class="btn btn-apply-project-item btn-project-status btn-complete-project" ><?php  _e('Review & Complete',ET_DOMAIN);?></a>
                                    <?php
                                } else { ?>

                                <a href="#" id="<?php the_ID();?>"   class="btn btn-apply-project-item" ><?php  _e('Completed',ET_DOMAIN);?></a>
                                <?php
                                }
                                break;
                            default:
                                $text_status =   array( 'pending'   => __('Pending',ET_DOMAIN),
                                                        'draft'     => __('Draft',ET_DOMAIN),
                                                        'archive'   => __('Draft',ET_DOMAIN),
                                                        'reject'    => __('Reject', ET_DOMAIN),
                                                        'trash'     => __('Trash', ET_DOMAIN),
                                                        'close'     => __('Working', ET_DOMAIN),
                                                        'complete'  => __('Completed', ET_DOMAIN),
                                                        );
                                if(isset($text_status[$project_status])){ ?>
                                    <a href="#"  class="btn btn-apply-project-item" ><?php  echo isset($text_status[$convert->post_status]) ? $text_status[$convert->post_status] : ''; ;?></a>
                                    <?php
                                }
                                break;
                        }
                    }
                    ?>
                    </div>
                </div>
            </div>
		
                <?php if( Fre_ReportForm::AccessReport()
                        && ($post->post_status == 'disputing' || $post->post_status == 'disputed')
                        && !isset($_REQUEST['workspace'])
                    ) { ?>
                    <div class="workplace-container">
                        <?php get_template_part('template/project', 'report') ?>
                    </div>
                <?php }else if( isset($_REQUEST['workspace']) && $_REQUEST['workspace'] ) { ?>
                    <div class="workplace-container">
                        <?php get_template_part('template/project', 'workspaces') ?>
                    </div>
                <?php }else {
                    get_template_part('template/project-detail' , 'info');
                    get_template_part('template/project-detail' , 'content');
                } ?>
				
            </div>
			<section class="section-wrapper  section-project-home tab-project-home vs-tab-project-home " id="best_web_tech001">
            <div class="list-project-wrapper">
                <div class="row">
                    <div class="col-md-12">
                        <div class="tab-content-project">
                            <div class="row title-tab-project">
                                <div class="col-md-5 col-sm-5 col-xs-7">
                                    <span>PROJECT TITLE</span>
                                </div>
                                <div class="col-md-2 col-sm-3 hidden-xs">
                                    <span>BY</span>
                                </div>
                                <div class="col-md-2 col-sm-2 hidden-sm hidden-xs">
                                    <span>POSTED DATE</span>
                                </div>
                                <div class="col-md-1 col-sm-2 hidden-xs">
                                    <span>BUDGET</span>
                                </div>
                            </div>
		<?php 
		///////////09-sep-shivam/////////
	$args = array(
	'posts_per_page'   => 5,
	'orderby'          => 'date',
	'order'            => 'DESC',
	'post_type'        => 'project',
	'post_status'      => 'publish',
	'suppress_filters' => true 
);
		$posts_array = get_posts($args );
		foreach($posts_array as $postArray){
		$showPost= (array)$postArray;
		//echo $showPost['[guid]'].'</br>';
		//echo $showPost['post_author'].'</br>';
		$postAuthor = get_userdata( $showPost['post_author'] );
		$data = (array)$postAuthor;
		$newData = (array)$data['data'];
		
		?>
                            <!-- Tab panes -->
                            
                            <div class="tab-pane fade in active tab-project-home">
                                                                <ul class="list-project project-list-container">
                                <li class="project-item post-700 project type-project status-publish format-standard hentry project_category-web-design project_category-web-programing project_category-website-project-management project_category-website-qa">
	<div class="row">
    	<div class="col-md-5 col-sm-5 col-xs-7 text-ellipsis pd-r-30">
        	<a href="<?php echo $showPost['guid']; ?>" class="title-project">
               <?php
			   	$thumb_id = $showPost['post_author'];
				$siteUrl =	get_site_url();
		$url = get_user_meta($thumb_id,'et_avatar_url',true);
		 
		//echo $url;die;
		if(!empty($url))
		{ ?>
			<img alt='' src="<?php echo $url; ?>" class='avatar avatar-35 photo avatar-default' height='35' width='35'> </a>
	<?php 	}
	else{ ?>
	<img alt='' src='   <?php echo get_template_directory_uri(); ?>/img/919cdfee6f192f51eecc6ba193de8f15.png ' class='avatar avatar-35 photo avatar-default' height='35' width='35'>            </a>
		<?php }
		?>
			 <a href="<?php echo $showPost['guid']; ?>" title="  <?php echo $showPost['post_title']; ?>" class="project-item-title">
               <?php echo $showPost['post_title']; ?></a>
		 
			</div>
        <div class="col-md-2 col-sm-3 hidden-xs">
                        <span>
                <a href="<?php echo $showPost['guid']; ?>" title="Posts by" rel="author">
				 <?php 	$postAuthor = get_userdata( $showPost['post_author'] );
					$data = (array)$postAuthor;
					$newData = (array)$data['data']; 
					echo $newData['display_name']; ?>	</a>
					</span>
        </div>
        <div class="col-md-2 col-sm-2 hidden-sm hidden-xs">
             <span>
                <?php echo date('F j, Y', strtotime($showPost['post_modified'])); ?>           </span>
        </div>

        <div class="col-md-1 col-sm-2 col-xs-4 hidden-xs">
            <span class="budget-project">
               <?php $bidAverage= get_post_meta( $showPost['ID'],'bid_average',true );
			   if(empty($bidAverage)){
				echo "$0.00";
				 }
			   else{
				echo "$".$bidAverage;
			   }
			   ?>            </span>
        </div>
        <div class="col-md-2 col-sm-2 col-xs-5">
                        <p class="wrapper-btn">
                <a href="<?php echo $showPost['guid']; ?>" class="btn-sumary btn-apply-project">
                    Apply   </a>
            </p>
                    </div>
    </div>
</li>
 </ul>

  </div>
 </div>
    
		
		<?php 	} ?><!--  End  foreach loop -->
		</div>
                    <div class="col-md-12">
<script type="data/json" class="postdata">[{"post_parent":0,"post_title":"best test 01","post_name":"best-test-01","post_content":"<p>best test 01<\/p>\n<div id=\"wp-ulike-700\" class=\"wpulike wpulike-default\"><div class=\"counter\"><a data-ulike-id=\"700\" data-ulike-type=\"likeThis\" data-ulike-status=\"1\" class=\"wp_ulike_btn text\">Like<\/a><span class=\"count-box\">2+<\/span><\/div><\/div> <br \/><p style=\"margin-top:5px\"> Users who have LIKED this post:<\/p> <ul class=\"tiles\"><li><a class=\"user-tooltip\" title=\"Dharmraj Nagar\"><img alt='avatar' src='http:\/\/1.gravatar.com\/avatar\/185aef70d956dd493dfb6ec42f660239?s=32&amp;d=mm&amp;r=G' class='avatar avatar-32 photo avatar-default' height='32' width='32' \/><\/a><\/li><li><a class=\"user-tooltip\" title=\"James Smith\"><img alt='avatar' src='http:\/\/192.168.1.100\/projects\/creativefreelanceprofessionals\/wp-content\/uploads\/2016\/08\/club-user-150x150.png' class='avatar avatar-32 photo avatar-default' height='32' width='32' \/><\/a><\/li><\/ul>","post_excerpt":"best test 01 Like2+ Users who have LIKED this post:","post_author":"59","post_status":"publish","ID":700,"post_type":"project","comment_count":"","guid":"http:\/\/192.168.1.100\/projects\/creativefreelanceprofessionals\/?post_type=project&#038;p=700","status_text":"ACTIVE","post_date":"September 6, 2016","skill":[],"tax_input":{"skill":[],"project_category":[{"term_id":64,"name":"Web Design","slug":"web-design","term_group":0,"term_taxonomy_id":64,"taxonomy":"project_category","description":"","parent":0,"count":15,"filter":"raw"},{"term_id":65,"name":"Web Programing","slug":"web-programing","term_group":0,"term_taxonomy_id":65,"taxonomy":"project_category","description":"","parent":0,"count":22,"filter":"raw"},{"term_id":69,"name":"Website Project Management","slug":"website-project-management","term_group":0,"term_taxonomy_id":69,"taxonomy":"project_category","description":"","parent":0,"count":6,"filter":"raw"},{"term_id":70,"name":"Website QA","slug":"website-qa","term_group":0,"term_taxonomy_id":70,"taxonomy":"project_category","description":"","parent":0,"count":7,"filter":"raw"}],"project_type":[]},"project_category":[64,65,69,70],"project_type":[],"address":"","avatar":"","post_count":"","et_featured":"0","et_expired_date":"2016-09-30 00:00:00","et_budget":"","deadline":"","total_bids":"","bid_average":0,"accepted":"","et_payment_package":"001","post_views":"12","id":700,"permalink":"http:\/\/192.168.1.100\/projects\/creativefreelanceprofessionals\/project\/best-test-01\/","unfiltered_content":"best test 01","the_post_thumnail":"","featured_image":"","the_post_thumbnail":"","et_avatar":"<img alt='' src='http:\/\/1.gravatar.com\/avatar\/919cdfee6f192f51eecc6ba193de8f15?s=35&amp;d=mm&amp;r=G' class='avatar avatar-35 photo avatar-default' height='35' width='35' \/>","author_url":"http:\/\/192.168.1.100\/projects\/creativefreelanceprofessionals\/author\/jyoti-sharmabestwebtech-in\/","author_name":"jyoti.sharma@bestwebtech.in","budget":"$0.00","bid_budget_text":"$0.00","rating_score":0,"project_comment":"","et_carousels":[],"current_user_bid":false,"posted_by":"Posted by jyoti.sharma@bestwebtech.in"},{"post_parent":0,"post_title":"Design a Website Mockup","post_name":"design-a-website-mockup-2","post_content":"<p class=\"span8 margin-0 margin-b10\">\n<p>Project Description:<\/p>\n<p>Looking for a talented HTML5 + Bootstrap3 designer to make easy changes to existing template layout.<\/p>\n<p>Your job will be to review what we have now, and to create a simplified version of the HTML5\/CSS code &#8230; simplified meaning that we want to make the User Interface easy as possible. There are only a handful of pages that need to be redesigned.<br \/>\nWe will provide the existing html5 code , screenshots and simple instructions.<\/p>\n<p>You will need to provide the html5\/css\/bootstrap3 code in a zip archive upon completion.<\/p>\n<p>Please contact us if you are available to help this week. Please confirm that you are familiar with boostrap3.<\/p>\n<div id=\"wp-ulike-396\" class=\"wpulike wpulike-default\"><div class=\"counter\"><a data-ulike-id=\"396\" data-ulike-type=\"likeThis\" data-ulike-status=\"1\" class=\"wp_ulike_btn text\">Like<\/a><span class=\"count-box\">0<\/span><\/div><\/div>","post_excerpt":"Project Description: Looking for a talented HTML5 + Bootstrap3 designer to make easy changes to existing template layout. Your job&hellip;","post_author":"1","post_status":"publish","ID":396,"post_type":"project","comment_count":"","guid":"http:\/\/enginethemes.com\/demo\/c1\/?post_type=project&amp;p=396","status_text":"ACTIVE","post_date":"November 17, 2014","skill":[11,26,29],"tax_input":{"skill":[{"term_id":11,"name":"bootstrap","slug":"bootstrap","term_group":0,"term_taxonomy_id":11,"taxonomy":"skill","description":"","parent":0,"count":1,"filter":"raw"},{"term_id":26,"name":"Graphic Design","slug":"graphic-design","term_group":0,"term_taxonomy_id":26,"taxonomy":"skill","description":"","parent":0,"count":4,"filter":"raw"},{"term_id":29,"name":"HTML","slug":"html","term_group":0,"term_taxonomy_id":29,"taxonomy":"skill","description":"","parent":0,"count":11,"filter":"raw"}],"project_category":[{"term_id":64,"name":"Web Design","slug":"web-design","term_group":0,"term_taxonomy_id":64,"taxonomy":"project_category","description":"","parent":0,"count":15,"filter":"raw"},{"term_id":65,"name":"Web Programing","slug":"web-programing","term_group":0,"term_taxonomy_id":65,"taxonomy":"project_category","description":"","parent":0,"count":22,"filter":"raw"}],"project_type":[{"term_id":23,"name":"Full time","slug":"full-time","term_group":0,"term_taxonomy_id":23,"taxonomy":"project_type","description":"","parent":0,"count":19,"filter":"raw"}]},"project_category":[64,65],"project_type":[23],"address":"","avatar":"","post_count":"","et_featured":"0","et_expired_date":"2115-01-17 07:50:49","et_budget":"150","deadline":"","total_bids":"2","bid_average":100,"accepted":"","et_payment_package":"002","post_views":"23","id":396,"permalink":"http:\/\/192.168.1.100\/projects\/creativefreelanceprofessionals\/project\/design-a-website-mockup-2\/","unfiltered_content":"<p class=\"span8 margin-0 margin-b10\"><\/p>\nProject Description:\n\nLooking for a talented HTML5 + Bootstrap3 designer to make easy changes to existing template layout.\n\nYour job will be to review what we have now, and to create a simplified version of the HTML5\/CSS code ... simplified meaning that we want to make the User Interface easy as possible. There are only a handful of pages that need to be redesigned.\nWe will provide the existing html5 code , screenshots and simple instructions.\n\nYou will need to provide the html5\/css\/bootstrap3 code in a zip archive upon completion.\n\nPlease contact us if you are available to help this week. Please confirm that you are familiar with boostrap3.","the_post_thumnail":"","featured_image":"","the_post_thumbnail":"","et_avatar":"<img alt='' src='http:\/\/0.gravatar.com\/avatar\/89cf7075a85010f017f51c62768e17ec?s=35&amp;d=mm&amp;r=G' class='avatar avatar-35 photo avatar-default' height='35' width='35' \/>","author_url":"http:\/\/192.168.1.100\/projects\/creativefreelanceprofessionals\/author\/admin\/","author_name":"admin","budget":"$150.00","bid_budget_text":"$0.00","rating_score":0,"project_comment":"","et_carousels":[],"current_user_bid":false,"posted_by":"Posted by admin"},{"post_parent":0,"post_title":"Build a Website I can give you the main code to start with and then i need a cms for it as well.","post_name":"build-a-website-i-can-give-you-the-main-code-to-start-with-and-then-i-need-a-cms-for-it-as-well","post_content":"<p>I have the code for a site with a full screen flip like a book http:\/\/bit.ly\/1oJY7a4.<br \/>\nI have some of the code for this custom version of the flip site http:\/\/bit.ly\/1q6YLyf.<br \/>\nHere is what i did so far but i need you to redo it properly. http:\/\/bit.ly\/1sWE2sQ<\/p>\n<p>I need to copy some of the features and make my own custom version of it.<br \/>\nI have a general idea of what i need and will be happy to get ideas from you as well.<\/p>\n<p>besides for setting up the front end I need a back end so i can easily edit content, and on some pages i want to remove all the default content divs we create and replace it with new html code, and have an easy way of inserting the corresponding CSS and JS to the files they belong in.<\/p>\n<p>Since this site is really one page, I need each page to load with Ajax (iframe might be an option but i dont think it works well) so its not overloaded with content, but it would probably need some kind of timing so its not slow on load or jittery.<\/p>\n<p>I need to add some keyboard shortcuts in addition to the right and left arrow keys for flipping.<\/p>\n<p>In the back end I need an easy way to add or remove pages, and when they are added or removed the corresponding index, or nav buttons should also be added or removed, as well as a thumbnail shortcut so we can browse through thumbnails of each page quickly so we can select what we want.<\/p>\n<p>If you want this job, you must respond with a detailed idea of how you will achieve this for the front and back end.<br \/>\nHow long each part of the project would take.<br \/>\nHow much you charge for fixed price.<br \/>\nHow much you charge per hour.<br \/>\nI need a GMAIL address, NOT SKYPE!<br \/>\nHow many hours are you available each day.<br \/>\nBetween the available hours you must always respond to email and messages.<br \/>\nI do not pay to start the job, I will only pay after a milestone where you can prove you are doing a good job, keeping good communication, and are doing it in a timely manner.<\/p>\n<p>If you do not agree to the terms of communication and payment, and don&#8217;t give a detailed response to how you will do it, the cost and time, (don&#8217;t tell me we will discuss! first respond, and then we will discuss!) DON&#8217;T BOTHER BIDDING!!!!.<\/p>\n<div id=\"wp-ulike-394\" class=\"wpulike wpulike-default\"><div class=\"counter\"><a data-ulike-id=\"394\" data-ulike-type=\"likeThis\" data-ulike-status=\"1\" class=\"wp_ulike_btn text\">Like<\/a><span class=\"count-box\">0<\/span><\/div><\/div>","post_excerpt":"I have the code for a site with a full screen flip like a book http:\/\/bit.ly\/1oJY7a4. I have some of&hellip;","post_author":"1","post_status":"publish","ID":394,"post_type":"project","comment_count":"","guid":"http:\/\/enginethemes.com\/demo\/c1\/?post_type=project&amp;p=394","status_text":"ACTIVE","post_date":"November 17, 2014","skill":[3,16,29,34,36],"tax_input":{"skill":[{"term_id":3,"name":"AJAX","slug":"ajax","term_group":0,"term_taxonomy_id":3,"taxonomy":"skill","description":"","parent":0,"count":3,"filter":"raw"},{"term_id":16,"name":"CSS","slug":"css","term_group":0,"term_taxonomy_id":16,"taxonomy":"skill","description":"","parent":0,"count":8,"filter":"raw"},{"term_id":29,"name":"HTML","slug":"html","term_group":0,"term_taxonomy_id":29,"taxonomy":"skill","description":"","parent":0,"count":11,"filter":"raw"},{"term_id":34,"name":"Javascript","slug":"javascript","term_group":0,"term_taxonomy_id":34,"taxonomy":"skill","description":"","parent":0,"count":9,"filter":"raw"},{"term_id":36,"name":"Jquery","slug":"jquery","term_group":0,"term_taxonomy_id":36,"taxonomy":"skill","description":"","parent":0,"count":2,"filter":"raw"}],"project_category":[{"term_id":64,"name":"Web Design","slug":"web-design","term_group":0,"term_taxonomy_id":64,"taxonomy":"project_category","description":"","parent":0,"count":15,"filter":"raw"},{"term_id":65,"name":"Web Programing","slug":"web-programing","term_group":0,"term_taxonomy_id":65,"taxonomy":"project_category","description":"","parent":0,"count":22,"filter":"raw"}],"project_type":[{"term_id":23,"name":"Full time","slug":"full-time","term_group":0,"term_taxonomy_id":23,"taxonomy":"project_type","description":"","parent":0,"count":19,"filter":"raw"}]},"project_category":[64,65],"project_type":[23],"address":"","avatar":"","post_count":"","et_featured":"0","et_expired_date":"2115-01-17 07:50:49","et_budget":"600","deadline":"","total_bids":"3","bid_average":566.66666666667,"accepted":"","et_payment_package":"002","post_views":"12","id":394,"permalink":"http:\/\/192.168.1.100\/projects\/creativefreelanceprofessionals\/project\/build-a-website-i-can-give-you-the-main-code-to-start-with-and-then-i-need-a-cms-for-it-as-well\/","unfiltered_content":"I have the code for a site with a full screen flip like a book http:\/\/bit.ly\/1oJY7a4.\nI have some of the code for this custom version of the flip site http:\/\/bit.ly\/1q6YLyf.\nHere is what i did so far but i need you to redo it properly. http:\/\/bit.ly\/1sWE2sQ\n\nI need to copy some of the features and make my own custom version of it.\nI have a general idea of what i need and will be happy to get ideas from you as well.\n\nbesides for setting up the front end I need a back end so i can easily edit content, and on some pages i want to remove all the default content divs we create and replace it with new html code, and have an easy way of inserting the corresponding CSS and JS to the files they belong in.\n\nSince this site is really one page, I need each page to load with Ajax (iframe might be an option but i dont think it works well) so its not overloaded with content, but it would probably need some kind of timing so its not slow on load or jittery.\n\nI need to add some keyboard shortcuts in addition to the right and left arrow keys for flipping.\n\nIn the back end I need an easy way to add or remove pages, and when they are added or removed the corresponding index, or nav buttons should also be added or removed, as well as a thumbnail shortcut so we can browse through thumbnails of each page quickly so we can select what we want.\n\nIf you want this job, you must respond with a detailed idea of how you will achieve this for the front and back end.\nHow long each part of the project would take.\nHow much you charge for fixed price.\nHow much you charge per hour.\nI need a GMAIL address, NOT SKYPE!\nHow many hours are you available each day.\nBetween the available hours you must always respond to email and messages.\nI do not pay to start the job, I will only pay after a milestone where you can prove you are doing a good job, keeping good communication, and are doing it in a timely manner.\n\nIf you do not agree to the terms of communication and payment, and don't give a detailed response to how you will do it, the cost and time, (don't tell me we will discuss! first respond, and then we will discuss!) DON'T BOTHER BIDDING!!!!.","the_post_thumnail":"","featured_image":"","the_post_thumbnail":"","et_avatar":"<img alt='' src='http:\/\/0.gravatar.com\/avatar\/89cf7075a85010f017f51c62768e17ec?s=35&amp;d=mm&amp;r=G' class='avatar avatar-35 photo avatar-default' height='35' width='35' \/>","author_url":"http:\/\/192.168.1.100\/projects\/creativefreelanceprofessionals\/author\/admin\/","author_name":"admin","budget":"$600.00","bid_budget_text":"$0.00","rating_score":0,"project_comment":"","et_carousels":[],"current_user_bid":false,"posted_by":"Posted by admin"},{"post_parent":0,"post_title":"Build a Website","post_name":"build-a-website-4","post_content":"<p>We want to build a layout editor. The editor must be a browser based, HTML5 editor built for the sole purpose of editing brochures, flyers and multi-page documents. The editor runs as a full GUI in a browser tab. The editor will be used to edit a document in the cloud. The document will contain text frames, text, images, tables, shapes, layers, pages and master pages.<\/p>\n<p>When the publishing document is saved, it should be saved on the server, in a JSON format. The editor will also save thumbnails of a few pages to show them for quick retrieval by the end user.<\/p>\n<p>We would like the technologies used to adhere to standards. We will provide a simple coding standards document.<\/p>\n<p>The backend is already coded and this project is only for the frontend (browser based editor). JSON is used as the communication between the client and the server.<\/p>\n<p>We would like the developer to be accessible during Central Standard Time and be available on skype at least 3 times a week for a minimum of 1 hour. Please read the attached document and post questions<\/p>\n<div id=\"wp-ulike-392\" class=\"wpulike wpulike-default\"><div class=\"counter\"><a data-ulike-id=\"392\" data-ulike-type=\"likeThis\" data-ulike-status=\"1\" class=\"wp_ulike_btn text\">Like<\/a><span class=\"count-box\">0<\/span><\/div><\/div>","post_excerpt":"We want to build a layout editor. The editor must be a browser based, HTML5 editor built for the sole&hellip;","post_author":"1","post_status":"publish","ID":392,"post_type":"project","comment_count":"","guid":"http:\/\/enginethemes.com\/demo\/c1\/?post_type=project&amp;p=392","status_text":"ACTIVE","post_date":"November 17, 2014","skill":[16,29,34,66],"tax_input":{"skill":[{"term_id":16,"name":"CSS","slug":"css","term_group":0,"term_taxonomy_id":16,"taxonomy":"skill","description":"","parent":0,"count":8,"filter":"raw"},{"term_id":29,"name":"HTML","slug":"html","term_group":0,"term_taxonomy_id":29,"taxonomy":"skill","description":"","parent":0,"count":11,"filter":"raw"},{"term_id":34,"name":"Javascript","slug":"javascript","term_group":0,"term_taxonomy_id":34,"taxonomy":"skill","description":"","parent":0,"count":9,"filter":"raw"},{"term_id":66,"name":"Website Design","slug":"website-design","term_group":0,"term_taxonomy_id":66,"taxonomy":"skill","description":"","parent":0,"count":8,"filter":"raw"}],"project_category":[{"term_id":64,"name":"Web Design","slug":"web-design","term_group":0,"term_taxonomy_id":64,"taxonomy":"project_category","description":"","parent":0,"count":15,"filter":"raw"},{"term_id":65,"name":"Web Programing","slug":"web-programing","term_group":0,"term_taxonomy_id":65,"taxonomy":"project_category","description":"","parent":0,"count":22,"filter":"raw"}],"project_type":[{"term_id":23,"name":"Full time","slug":"full-time","term_group":0,"term_taxonomy_id":23,"taxonomy":"project_type","description":"","parent":0,"count":19,"filter":"raw"}]},"project_category":[64,65],"project_type":[23],"address":"","avatar":"","post_count":"","et_featured":"0","et_expired_date":"2115-01-17 07:50:49","et_budget":"2000","deadline":"","total_bids":"1","bid_average":1800,"accepted":"","et_payment_package":"002","post_views":"7","id":392,"permalink":"http:\/\/192.168.1.100\/projects\/creativefreelanceprofessionals\/project\/build-a-website-4\/","unfiltered_content":"We want to build a layout editor. The editor must be a browser based, HTML5 editor built for the sole purpose of editing brochures, flyers and multi-page documents. The editor runs as a full GUI in a browser tab. The editor will be used to edit a document in the cloud. The document will contain text frames, text, images, tables, shapes, layers, pages and master pages.\n\nWhen the publishing document is saved, it should be saved on the server, in a JSON format. The editor will also save thumbnails of a few pages to show them for quick retrieval by the end user.\n\nWe would like the technologies used to adhere to standards. We will provide a simple coding standards document.\n\nThe backend is already coded and this project is only for the frontend (browser based editor). JSON is used as the communication between the client and the server.\n\nWe would like the developer to be accessible during Central Standard Time and be available on skype at least 3 times a week for a minimum of 1 hour. Please read the attached document and post questions","the_post_thumnail":"","featured_image":"","the_post_thumbnail":"","et_avatar":"<img alt='' src='http:\/\/0.gravatar.com\/avatar\/89cf7075a85010f017f51c62768e17ec?s=35&amp;d=mm&amp;r=G' class='avatar avatar-35 photo avatar-default' height='35' width='35' \/>","author_url":"http:\/\/192.168.1.100\/projects\/creativefreelanceprofessionals\/author\/admin\/","author_name":"admin","budget":"$2,000.00","bid_budget_text":"$0.00","rating_score":0,"project_comment":"","et_carousels":[],"current_user_bid":false,"posted_by":"Posted by admin"},{"post_parent":0,"post_title":"2 simple similler psd to responsive on bootstrap","post_name":"2-simple-similler-psd-to-responsive-on-bootstrap","post_content":"<p>i have very simple psd design just need to convert in responsive on bootstrap!!!<\/p>\n<p>i need responsive in only 2 hours from now its so simple also on home page need slider !!!<\/p>\n<p>budget is only $10<\/p>\n<div id=\"wp-ulike-390\" class=\"wpulike wpulike-default\"><div class=\"counter\"><a data-ulike-id=\"390\" data-ulike-type=\"likeThis\" data-ulike-status=\"1\" class=\"wp_ulike_btn text\">Like<\/a><span class=\"count-box\">0<\/span><\/div><\/div>","post_excerpt":"i have very simple psd design just need to convert in responsive on bootstrap!!! i need responsive in only 2&hellip;","post_author":"1","post_status":"publish","ID":390,"post_type":"project","comment_count":"","guid":"http:\/\/enginethemes.com\/demo\/c1\/?post_type=project&amp;p=390","status_text":"ACTIVE","post_date":"November 17, 2014","skill":[26,29,30,66],"tax_input":{"skill":[{"term_id":26,"name":"Graphic Design","slug":"graphic-design","term_group":0,"term_taxonomy_id":26,"taxonomy":"skill","description":"","parent":0,"count":4,"filter":"raw"},{"term_id":29,"name":"HTML","slug":"html","term_group":0,"term_taxonomy_id":29,"taxonomy":"skill","description":"","parent":0,"count":11,"filter":"raw"},{"term_id":30,"name":"HTML5","slug":"html5","term_group":0,"term_taxonomy_id":30,"taxonomy":"skill","description":"","parent":0,"count":9,"filter":"raw"},{"term_id":66,"name":"Website Design","slug":"website-design","term_group":0,"term_taxonomy_id":66,"taxonomy":"skill","description":"","parent":0,"count":8,"filter":"raw"}],"project_category":[{"term_id":64,"name":"Web Design","slug":"web-design","term_group":0,"term_taxonomy_id":64,"taxonomy":"project_category","description":"","parent":0,"count":15,"filter":"raw"}],"project_type":[{"term_id":23,"name":"Full time","slug":"full-time","term_group":0,"term_taxonomy_id":23,"taxonomy":"project_type","description":"","parent":0,"count":19,"filter":"raw"}]},"project_category":[64],"project_type":[23],"address":"","avatar":"","post_count":"","et_featured":"0","et_expired_date":"2115-01-17 07:50:48","et_budget":"40","deadline":"","total_bids":"1","bid_average":30,"accepted":"","et_payment_package":"002","post_views":"8","id":390,"permalink":"http:\/\/192.168.1.100\/projects\/creativefreelanceprofessionals\/project\/2-simple-similler-psd-to-responsive-on-bootstrap\/","unfiltered_content":"i have very simple psd design just need to convert in responsive on bootstrap!!!\n\ni need responsive in only 2 hours from now its so simple also on home page need slider !!!\n\nbudget is only $10","the_post_thumnail":"","featured_image":"","the_post_thumbnail":"","et_avatar":"<img alt='' src='http:\/\/0.gravatar.com\/avatar\/89cf7075a85010f017f51c62768e17ec?s=35&amp;d=mm&amp;r=G' class='avatar avatar-35 photo avatar-default' height='35' width='35' \/>","author_url":"http:\/\/192.168.1.100\/projects\/creativefreelanceprofessionals\/author\/admin\/","author_name":"admin","budget":"$40.00","bid_budget_text":"$0.00","rating_score":0,"project_comment":"","et_carousels":[],"current_user_bid":false,"posted_by":"Posted by admin"},{"post_parent":0,"post_title":"SilverlightApp convert to HTML5","post_name":"silverlightapp-convert-to-html5","post_content":"<p>This is a repost of the project &#8211; the existing webapp has been deployed to the web in the final Silverlight version.<\/p>\n<p>The existing Webapp is built on a Microsoft SQL backend and the frontend is Silverlight. The pattern used binding to the Silverlight frontend is MVVM. We want to move away from Silverlight and move over to HTML or HTML5 to increase our reach into the market segment.<\/p>\n<p>The current WebApplication architecture consist of:<br \/>\nEntity Framework and linq to entity framework, RIA services, Silverlight and MVVM patterns.<br \/>\nWe make use of an opensource framework to assit with WPF and Silverlight patterns and UI\u2019s \u2013 Caliburn (http:\/\/caliburn.codeplex.com).<br \/>\nOther thirdparty elements are opensource DLL\u2019s for integration into Facebook, Twitter, Google.<\/p>\n<p>This project is to redevelop the frontend in HTML5 without affecting the backend development.<br \/>\nThe existing application : www.acio.co.za<\/p>\n<div id=\"wp-ulike-519\" class=\"wpulike wpulike-default\"><div class=\"counter\"><a data-ulike-id=\"519\" data-ulike-type=\"likeThis\" data-ulike-status=\"1\" class=\"wp_ulike_btn text\">Like<\/a><span class=\"count-box\">0<\/span><\/div><\/div>","post_excerpt":"This is a repost of the project &#8211; the existing webapp has been deployed to the web in the final&hellip;","post_author":"1","post_status":"publish","ID":519,"post_type":"project","comment_count":"","guid":"http:\/\/enginethemes.com\/demo\/c1\/?post_type=project&amp;p=388","status_text":"ACTIVE","post_date":"November 17, 2014","skill":[30,34],"tax_input":{"skill":[{"term_id":30,"name":"HTML5","slug":"html5","term_group":0,"term_taxonomy_id":30,"taxonomy":"skill","description":"","parent":0,"count":9,"filter":"raw"},{"term_id":34,"name":"Javascript","slug":"javascript","term_group":0,"term_taxonomy_id":34,"taxonomy":"skill","description":"","parent":0,"count":9,"filter":"raw"}],"project_category":[{"term_id":64,"name":"Web Design","slug":"web-design","term_group":0,"term_taxonomy_id":64,"taxonomy":"project_category","description":"","parent":0,"count":15,"filter":"raw"}],"project_type":[{"term_id":23,"name":"Full time","slug":"full-time","term_group":0,"term_taxonomy_id":23,"taxonomy":"project_type","description":"","parent":0,"count":19,"filter":"raw"}]},"project_category":[64],"project_type":[23],"address":"","avatar":"","post_count":"","et_featured":"0","et_expired_date":"2115-01-17 07:50:50","et_budget":"2200","deadline":"","total_bids":"","bid_average":0,"accepted":"","et_payment_package":"002","post_views":"6","id":519,"permalink":"http:\/\/192.168.1.100\/projects\/creativefreelanceprofessionals\/project\/silverlightapp-convert-to-html5\/","unfiltered_content":"This is a repost of the project - the existing webapp has been deployed to the web in the final Silverlight version.\n\nThe existing Webapp is built on a Microsoft SQL backend and the frontend is Silverlight. The pattern used binding to the Silverlight frontend is MVVM. We want to move away from Silverlight and move over to HTML or HTML5 to increase our reach into the market segment.\n\nThe current WebApplication architecture consist of:\nEntity Framework and linq to entity framework, RIA services, Silverlight and MVVM patterns.\nWe make use of an opensource framework to assit with WPF and Silverlight patterns and UI\u2019s \u2013 Caliburn (http:\/\/caliburn.codeplex.com).\nOther thirdparty elements are opensource DLL\u2019s for integration into Facebook, Twitter, Google.\n\nThis project is to redevelop the frontend in HTML5 without affecting the backend development.\nThe existing application : www.acio.co.za","the_post_thumnail":"","featured_image":"","the_post_thumbnail":"","et_avatar":"<img alt='' src='http:\/\/0.gravatar.com\/avatar\/89cf7075a85010f017f51c62768e17ec?s=35&amp;d=mm&amp;r=G' class='avatar avatar-35 photo avatar-default' height='35' width='35' \/>","author_url":"http:\/\/192.168.1.100\/projects\/creativefreelanceprofessionals\/author\/admin\/","author_name":"admin","budget":"$2,200.00","bid_budget_text":"$0.00","rating_score":0,"project_comment":"","et_carousels":[],"current_user_bid":false,"posted_by":"Posted by admin"},{"post_parent":0,"post_title":"make PDF to HTML for mailchimp1","post_name":"make-pdf-to-html-for-mailchimp1","post_content":"<p>I want to make pdf to HTML for mailchimp the HTML is compatible woth mailchimp. want to work done today<\/p>\n<div id=\"wp-ulike-518\" class=\"wpulike wpulike-default\"><div class=\"counter\"><a data-ulike-id=\"518\" data-ulike-type=\"likeThis\" data-ulike-status=\"1\" class=\"wp_ulike_btn text\">Like<\/a><span class=\"count-box\">0<\/span><\/div><\/div>","post_excerpt":"I want to make pdf to HTML for mailchimp the HTML is compatible woth mailchimp. want to work done today&hellip;","post_author":"1","post_status":"publish","ID":518,"post_type":"project","comment_count":"","guid":"http:\/\/enginethemes.com\/demo\/c1\/?post_type=project&amp;p=385","status_text":"ACTIVE","post_date":"November 17, 2014","skill":[17,30,66],"tax_input":{"skill":[{"term_id":17,"name":"CSS3","slug":"css3","term_group":0,"term_taxonomy_id":17,"taxonomy":"skill","description":"","parent":0,"count":2,"filter":"raw"},{"term_id":30,"name":"HTML5","slug":"html5","term_group":0,"term_taxonomy_id":30,"taxonomy":"skill","description":"","parent":0,"count":9,"filter":"raw"},{"term_id":66,"name":"Website Design","slug":"website-design","term_group":0,"term_taxonomy_id":66,"taxonomy":"skill","description":"","parent":0,"count":8,"filter":"raw"}],"project_category":[{"term_id":64,"name":"Web Design","slug":"web-design","term_group":0,"term_taxonomy_id":64,"taxonomy":"project_category","description":"","parent":0,"count":15,"filter":"raw"}],"project_type":[{"term_id":23,"name":"Full time","slug":"full-time","term_group":0,"term_taxonomy_id":23,"taxonomy":"project_type","description":"","parent":0,"count":19,"filter":"raw"}]},"project_category":[64],"project_type":[23],"address":"","avatar":"","post_count":"","et_featured":"0","et_expired_date":"2115-01-17 07:50:48","et_budget":"20","deadline":"","total_bids":"","bid_average":0,"accepted":"","et_payment_package":"002","post_views":"8","id":518,"permalink":"http:\/\/192.168.1.100\/projects\/creativefreelanceprofessionals\/project\/make-pdf-to-html-for-mailchimp1\/","unfiltered_content":"I want to make pdf to HTML for mailchimp the HTML is compatible woth mailchimp. want to work done today","the_post_thumnail":"","featured_image":"","the_post_thumbnail":"","et_avatar":"<img alt='' src='http:\/\/0.gravatar.com\/avatar\/89cf7075a85010f017f51c62768e17ec?s=35&amp;d=mm&amp;r=G' class='avatar avatar-35 photo avatar-default' height='35' width='35' \/>","author_url":"http:\/\/192.168.1.100\/projects\/creativefreelanceprofessionals\/author\/admin\/","author_name":"admin","budget":"$20.00","bid_budget_text":"$0.00","rating_score":0,"project_comment":"","et_carousels":[],"current_user_bid":false,"posted_by":"Posted by admin"},{"post_parent":0,"post_title":"HTML5, CSS3 developer","post_name":"html5-css3-developer","post_content":"<p>We are looking for a developer , who good in HTML5, CSS3 development to convert 6-8 pages in to HTML.<br \/>\nFind some of the features we looking for\u2026<br \/>\n\u2022 Bootstrap responsive layout.<br \/>\n\u2022 HTML5 Validated<br \/>\n\u2022 All major browser compatible<br \/>\n\u2022 Site pre-loader<br \/>\n\u2022 Well commented for future reference<\/p>\n<div id=\"wp-ulike-517\" class=\"wpulike wpulike-default\"><div class=\"counter\"><a data-ulike-id=\"517\" data-ulike-type=\"likeThis\" data-ulike-status=\"1\" class=\"wp_ulike_btn text\">Like<\/a><span class=\"count-box\">0<\/span><\/div><\/div>","post_excerpt":"We are looking for a developer , who good in HTML5, CSS3 development to convert 6-8 pages in to HTML.&hellip;","post_author":"1","post_status":"publish","ID":517,"post_type":"project","comment_count":"","guid":"http:\/\/enginethemes.com\/demo\/c1\/?post_type=project&amp;p=383","status_text":"ACTIVE","post_date":"November 17, 2014","skill":[17,30,66],"tax_input":{"skill":[{"term_id":17,"name":"CSS3","slug":"css3","term_group":0,"term_taxonomy_id":17,"taxonomy":"skill","description":"","parent":0,"count":2,"filter":"raw"},{"term_id":30,"name":"HTML5","slug":"html5","term_group":0,"term_taxonomy_id":30,"taxonomy":"skill","description":"","parent":0,"count":9,"filter":"raw"},{"term_id":66,"name":"Website Design","slug":"website-design","term_group":0,"term_taxonomy_id":66,"taxonomy":"skill","description":"","parent":0,"count":8,"filter":"raw"}],"project_category":[{"term_id":64,"name":"Web Design","slug":"web-design","term_group":0,"term_taxonomy_id":64,"taxonomy":"project_category","description":"","parent":0,"count":15,"filter":"raw"},{"term_id":65,"name":"Web Programing","slug":"web-programing","term_group":0,"term_taxonomy_id":65,"taxonomy":"project_category","description":"","parent":0,"count":22,"filter":"raw"}],"project_type":[{"term_id":23,"name":"Full time","slug":"full-time","term_group":0,"term_taxonomy_id":23,"taxonomy":"project_type","description":"","parent":0,"count":19,"filter":"raw"}]},"project_category":[64,65],"project_type":[23],"address":"","avatar":"","post_count":"","et_featured":"0","et_expired_date":"2115-01-17 07:50:48","et_budget":"400","deadline":"","total_bids":"1","bid_average":300,"accepted":"","et_payment_package":"002","post_views":"8","id":517,"permalink":"http:\/\/192.168.1.100\/projects\/creativefreelanceprofessionals\/project\/html5-css3-developer\/","unfiltered_content":"We are looking for a developer , who good in HTML5, CSS3 development to convert 6-8 pages in to HTML.\nFind some of the features we looking for\u2026\n\u2022 Bootstrap responsive layout.\n\u2022 HTML5 Validated\n\u2022 All major browser compatible\n\u2022 Site pre-loader\n\u2022 Well commented for future reference","the_post_thumnail":"","featured_image":"","the_post_thumbnail":"","et_avatar":"<img alt='' src='http:\/\/0.gravatar.com\/avatar\/89cf7075a85010f017f51c62768e17ec?s=35&amp;d=mm&amp;r=G' class='avatar avatar-35 photo avatar-default' height='35' width='35' \/>","author_url":"http:\/\/192.168.1.100\/projects\/creativefreelanceprofessionals\/author\/admin\/","author_name":"admin","budget":"$400.00","bid_budget_text":"$0.00","rating_score":0,"project_comment":"","et_carousels":[],"current_user_bid":false,"posted_by":"Posted by admin"}]</script>                    </div>

                </div>
            </div>
        </section>
        <!-- Content project / End -->
        <div class="clearfix"></div>
    </div><!-- tab-content-project !-->
</div>  <!--col-md-12 !-->
	
