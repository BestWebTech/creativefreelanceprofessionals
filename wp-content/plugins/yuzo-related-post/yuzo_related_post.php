<?php
/*
Plugin Name: Related Posts Yuzo
Plugin URI: https://wordpress.org/plugins/yuzo-related-post/
Description: Related posts so easy and fast
Tags: related posts,related post,related content,popular posts,last post, most views, widget,related page,content,associate page, associate post
Version: 5.12.51
Author: iLen
Author URI: http://ilentheme.com
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd =_s-xclick&hosted_button_id=MSRAUBMB5BZFU
*/








// live as if it were the last day of your life
if ( !class_exists('yuzo_related_post') ) {



require_once 'assets/ilenframework/assets/lib/utils.php'; // get utils
require_once 'assets/functions/options.php'; // get options plugins
if(!defined('YUZO_PATH')) define('YUZO_PATH', plugin_dir_path( __FILE__ ));



class yuzo_related_post extends yuzo_related_post_make{


	var $plugin_options = null;


	function __construct(){

		parent::__construct(); // configuration general

		global $yuzo_options;

		// get option plugin ;)
		$yuzo_options = $this->utils->IF_get_option( $this->parameter['name_option'] );
		$this->plugin_options = $yuzo_options;


		// if disabled hits
		if( isset($yuzo_options->disabled_counter) && $yuzo_options->disabled_counter ){
			null;
		}else{
			// ajax nonce for count visits in cache plugin
			if(  ((defined( 'WP_CACHE' ) && WP_CACHE) ||  defined('WPFC_MAIN_PATH') ) && !is_user_logged_in() ){
				add_action( 'wp_enqueue_scripts',  array( &$this, 'wp_yuzo_postview_cache_count_enqueue') );
				add_action( 'wp_ajax_nopriv_yuzo-plus-views', array( &$this, 'hits_ajax' ) );
				add_action( 'wp_ajax_yuzo-plus-views', array( &$this, 'hits_ajax' ) );
			}else{
				// count normal
				add_action('wp_head',array( &$this,'hits'), 12 );
			}

		}
		

		if( is_admin() ){
 
			// when active plugin refirect to page
			add_action('admin_menu',  array( &$this,'yuzo_menu_welcome') );

			add_action( 'admin_enqueue_scripts', array( &$this,'script_and_style_admin' ) );

			if( isset($yuzo_options->disabled_counter) && $yuzo_options->disabled_counter ){
				null;
			}else{
				if( isset($yuzo_options->show_columns_dashboard) && $yuzo_options->show_columns_dashboard ){

					//Hooks a function to a specific filter action.
					//applied to the list of columns to print on the manage posts screen.
					add_filter('manage_posts_columns', array( &$this,'yuzo_post_column_views') );

					//Hooks a function to a specific action. 
					//allows you to add custom columns to the list post/custom post type pages.
					//'10' default: specify the function's priority.
					//and '2' is the number of the functions' arguments.
					add_action('manage_posts_custom_column', array( &$this,'yuzo_post_custom_column_views'),10,2 );


					// Add labels of hits
					add_action( 'admin_head',  array( &$this,'add_labes_hits_tablelist' ) );

				}
			}
			

			// when active plugin verify db
			//register_activation_hook( __FILE__ , array( &$this,'yuzo_install_db' ) );
			add_action( 'admin_head',  array( &$this,'yuzo_install_db' ) );
			//add_action( 'admin_head',  'yuzo_redirect_welcome_upgrade' );
			add_action( 'admin_footer',   array( &$this,'show_popup_message' ) );
			// when active plugin redirect
			add_action( 'activated_plugin', array( &$this,'yuzo_redirect_welcome' ) );
			//add_action('plugins_loaded', array(&$this, 'yuzo_redirect_welcome'), 10 , 2 );
			//register_update_hook( __FILE__ , array( &$this,'yuzo_redirect_welcome_upgrade' ) );

			// load functions ajax in admin
			add_action( 'admin_enqueue_scripts', 'add_ajax_javascript_file' );
			add_action( 'wp_ajax_ajax_delete_yuzo_data_admin', 'ajax_delete_yuzo_data_admin' );

			// delete transient each 'save'
			if( isset($_POST["save_options"]) && $_POST["save_options"] == 1 ){
				$this->delete_transient_for_save();	
			}
			



		}elseif( ! is_admin() ) {

			if( isset($yuzo_options->disabled_counter) && $yuzo_options->disabled_counter ){
				null;
			}else{
				add_shortcode( 'yuzo_views' , array( &$this,'yuzo_shortcode' ) );
			}

			add_shortcode( 'yuzo_related', array( &$this,'yuzo_shortcode_related' ) );


			if( isset($yuzo_options->automatically_append) &&  $yuzo_options->automatically_append =='1' ){
				add_action('the_content',array( &$this,'create_post_related'),10 );
			}

			// add scripts & styles
			add_action( 'wp_enqueue_scripts', array( &$this,'script_and_style_front' ) );

			// add custom css
			add_action( 'wp_footer', array( &$this,'add_custom_css' ) );
	 
		}
 
	}

 
 

// MAKE HTML of PLUGIN
function create_post_related( $content ){

	global $post,$yuzo_options,$wp_query,$if_utils;  
	$orig_post = $post;


	// verify
	if( self::only_specific_post() == false ) return $content;


	$style          = "";
	$script         = "";
	$transient_name = "yuzo_query_cache_".$post->ID;
	$cacheTime      = 20; // minutes
	$rebuilt_query  = isset($yuzo_options->transient) && $yuzo_options->transient?false:true; //false;

	if( ( is_archive() || is_search() ) && ( isset($yuzo_options->no_show_archive_page) && $yuzo_options->no_show_archive_page ) ){ return $content; };

	$meta_yuzo = get_post_meta( $post->ID, $this->parameter['name_option']."_metabox" );

 	// validate if Yuzo is disabled in the post
	if( isset($meta_yuzo[0]['yuzo_disabled_related']) && $meta_yuzo[0]['yuzo_disabled_related'] ){
		return $content;
	}

	// validate no appear in IDs
	if( isset($yuzo_options->no_appear) && $yuzo_options->no_appear ){
		// if exclude IDs
		$no_ids = explode(",",$yuzo_options->no_appear);
		if( in_array( $post->ID, $no_ids  ) ){

			return $content;

		}
	}

	// validate show in feed or no
	if( is_feed() && (( ! isset($yuzo_options->show_feed) || ! $yuzo_options->show_feed)) ){
		return $content;
	}


	// if active Only home page
	if( (isset($yuzo_options->show_only_home) && $yuzo_options->show_only_home) && (!is_home() || !is_front_page() ) ) return $content;



	// verify cache query
	$the_query_yuzo = null;
	if(  isset($yuzo_options->transient) && $yuzo_options->transient ){
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		if( false === ($the_query_yuzo = get_transient($transient_name) ) || ( current_user_can( 'manage_options' )  && !isset($_GET['P3_NOCACHE']) )  ){
			$rebuilt_query  = true;
		}
	}

	// order image post
	$order_image = "DESC";
	if( isset($yuzo_options->image_order) ){
		$order_image = $yuzo_options->image_order;
	}

	//var_dump( $wp_query );
	$_html = "<!-- Begin Yuzo -->";
	$_html .= "<div class='yuzo_related_post style-$yuzo_options->style'  data-version='{$this->parameter["version"]}'>";
	if( $wp_query->post_count != 0 ){ // if have result in loop post

		$__in_post   = null; // for array
		$post_in     = null;
		$post_not_in = null;
		$have_manual_post = false; // true = if have / false = no have
		$break_query_post = false; // true = If validation for query_post breaks then this variable is 'true' indicates that maximum placed in the post manual if necessary.


		if( $rebuilt_query ){



			// include post from admin posts
			$__in_post   = null; // for array
			$post_in     = null;
			$post_not_in = null;
			if( isset($meta_yuzo[0]['yuzo_include_post']) && $meta_yuzo[0]['yuzo_include_post'] ){
				$__in_post = explode("|",$meta_yuzo[0]['yuzo_include_post']);
   
				foreach ($__in_post as $in_post_key => $in_post_value) {
					if( $in_post_value ){
						$have_manual_post = true;
						$post_in[] = (int)$in_post_value;
						$post_not_in[] = (int)$in_post_value;
					}
				}
			}






			// verify type page
			$post_type = get_post_type( $post->ID );
			if( $post_type ){
				if( ! in_array($post_type, (array)$yuzo_options->post_type ) ){
					if( !$have_manual_post ){
						return $content;
					}else{
						$break_query_post = true;
					}
				}
			}





			// Categories on which related thumbnails will appear
			$object_current_categories_of_post = get_the_category($post->ID);
			$array_current_categories_of_post = null;
			if( $object_current_categories_of_post ){ foreach($object_current_categories_of_post as $value_cat) $array_current_categories_of_post[] = (string)$value_cat->term_id; }

			if( isset($yuzo_options->categories) && $yuzo_options->categories ){
				if( !in_array("-1",$yuzo_options->categories) ){
					$containsSearch = array_intersect($yuzo_options->categories, $array_current_categories_of_post);
					if( ! $containsSearch ){
						return $content;
					}
				}
			}






			// Exclude category and set categories
			$array_categories_to_show = array();
			$string_categories_to_show = null;
			if( ! $break_query_post ){
				// exclude category force
				if( isset($yuzo_options->exclude_category) && is_array($yuzo_options->exclude_category) ){

					if( !in_array("-1",$yuzo_options->exclude_category) ){
						if( is_array($array_current_categories_of_post) ){
							$array_categories_to_show = array_diff($array_current_categories_of_post,$yuzo_options->exclude_category);	
						}
						
					}else{
						// categories to show
						$array_categories_to_show = $array_current_categories_of_post;
					}
					/*$containsSearch = array_intersect($yuzo_options->exclude_category, $array_current_categories_of_post);
					if( $containsSearch ){
						$array_categories_to_show = 
					}*/
					if( is_array($array_categories_to_show) ){
						$string_categories_to_show = implode(",",$array_categories_to_show);	
					}
					

				}else{
					if( is_array($array_current_categories_of_post) ){
						$string_categories_to_show = implode(",",$array_current_categories_of_post);						
					}
				}


			}







			/*if( ! $break_query_post ){
				$category_plugin = null;
				$array_categorias = $yuzo_options->categories; // (categies to show)
				$categories =  get_the_category($post->ID);
				if($categories){
					foreach ($categories as $key_ => $value_) {
						$category_plugin[]=(string)$value_->cat_ID;
					}
				}
				if( is_array($array_categorias) && is_array($category_plugin) ){
					if( ! in_array( '-1',$array_categorias ) ){
						$bFound = (count(array_intersect($category_plugin, $array_categorias))) ? true : false; 
						if( $bFound == false ){

							if( !$have_manual_post ){
								return $content;
							}else{
								$break_query_post = true;
							}
						}
					}
				}
			}*/






			// is show home
			if( isset($yuzo_options->show_home) && !$yuzo_options->show_home  && is_home() ){
				return $content;
			}elseif( !isset($yuzo_options->show_home) && !$yuzo_options->show_home && is_home()){
				return $content;
			}






			// Excluding related categories
			if( ! $break_query_post ){
				$array_no_category=array();
				$string_no_category="";
				if( isset($yuzo_options->exclude_category_related) && is_array($yuzo_options->exclude_category) ){
					if( ! in_array( '-1',$yuzo_options->exclude_category ) ){    
						foreach ($yuzo_options->exclude_category as $ce_key => $ce_value) {
							$array_no_category[]=$ce_value;
						}
					}
				}
			}
			

 










			// Related Based in
			if( ! $break_query_post  ){
				$tag_ids     = array();
				$string_cate = "";
				$string_tags = "";
				$post__in    = null;

				$string_order_by = $yuzo_options->order_by;
				$string_order    = $yuzo_options->order;

				if( $yuzo_options->related_to == '3' ){
				  $tags = wp_get_post_tags($post->ID);
					if ($tags) {  
						$tag_ids = array();
						foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;
					}
			 
					//$string_tags =  $tag_ids;
					$category_plugin = null;
					$string_cate = $category_plugin;
				}elseif( $yuzo_options->related_to == '1' ){

					$tags = wp_get_post_tags($post->ID);
	
					if ($tags) {  
						$tag_ids = array();  
						foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;
					}else{

						if( !$have_manual_post ){
							return $content;
						}else{
							$break_query_post = true;
						}

					}

					// if exclude tags
					/*if( isset($yuzo_options->exclude_tag) && $yuzo_options->exclude_tag ){
						$no_tags = explode(",",$yuzo_options->exclude_tag);
						$no_tag_values = array();
						foreach ($no_tags as $no_tags_key => $no_tags_value_) {
							$term = get_term_by('name', $no_tags_value_, 'post_tag');
							$no_tag_values[] = $term->term_id;
						}
						//var_dump( $tag_ids );
						//var_dump( $no_tag_values );
						//$tag_ids = array_diff($tag_ids, $no_tag_values);
					}*/
					
					//$string_tags =  implode(",",$tag_ids);
					//$string_tags =  $tag_ids;

				}elseif( $yuzo_options->related_to == '2' ){
					$category_plugin = null;
					$string_cate = $category_plugin;

				}elseif( $yuzo_options->related_to == '4' ){

					$string_order_by = 'rand';

				}elseif( $yuzo_options->related_to == '5' ){ // Taxonomies 

					$post__in = self::taxomy_real();
					$string_order = "";
					$string_order_by = 'post__in';

				}


				// validate exclude post current
				$post__not_in[] = (int)$post->ID;
				// exclude post from admin posts
				if( isset($meta_yuzo[0]['yuzo_exclude_post']) &&  $meta_yuzo[0]['yuzo_exclude_post'] ){
					$__ex_post = null;
					$__ex_post = explode("|",$meta_yuzo[0]['yuzo_exclude_post']);
					foreach ($__ex_post as $ex_post_key => $ex_post_value) {
						if( $ex_post_value ){
							$post__not_in[] = (int)$ex_post_value;
						}
					}

				}


			}
			



			//Set number of post
			//if( ! $break_query_post  ){
			$number_post = 4; // need it value for manual post without query_post
			if( is_home() ){
				$number_post = (int)$yuzo_options->display_post_home;
			}else{
				$number_post = (int)$yuzo_options->display_post;
			}
			








			// Main query
			if( ! $break_query_post  ){
				$args = array(
					'showposts'           =>  $number_post,
					//'post_type'           => (array)$yuzo_options->post_type,
					'post_status'         => 'publish',
					'ignore_sticky_posts' => 1,
					'orderby'             => $string_order_by,
					'order'               => $string_order,
					'cat'				  => $string_categories_to_show,
					//'category__in'        => $string_cate,
					'category__not_in'    => $array_no_category,
				);
 
				// validate if only show 1 type
				if( isset($yuzo_options->show_only_type) && $yuzo_options->show_only_type ){
					$args['post_type'] = (array)$post_type;
				}

				// validate if post related custom
				if( is_array($post__in) ){
					if( is_array($post_in) ){
						foreach ($post_in as $post_in_key => $post_in_value) {
							if( ! in_array($post_in_value,$post__in) ){
								$post__in[] = $post_in_value;
							}
						}
						//$post__in = array_merge(array_diff($post__in, $post__in_temp), array_diff($post__in_temp, $post__in));
					}
					if( is_array($post_not_in) ){
						foreach ($post_not_in as $post_not_in_key => $post_not_in_value) {
							$post__not_in[] = $post_not_in_value;
						}
					}
					//$args['post__in'] =  array_diff( $post__in , $post__not_in);
					//var_dump( $post__in );
					//var_dump( $post__not_in );

					// this validate is if no post exclude / include
					if( !$post_in && !$post_not_in ){
						$args['post__in'] = array_diff($post__in,$post__not_in);
						//$args['post__in'] = array_merge(array_diff($post__in, $post__not_in), array_diff($post__not_in, $post__in));
					}else{
						$args['post__in'] = array_merge(array_diff($post__in, $post__not_in), array_diff($post__not_in, $post__in));    
					}

					$args['posts_per_page'] = -1;
					
					//var_dump($args['post__in']);
				}else{
					if( is_array($post_not_in) ){
						foreach ($post_not_in as $post_not_in_key => $post_not_in_value) {
							$post__not_in[] = $post_not_in_value;
						}
					}
					$args['post__not_in'] =  (array)$post__not_in;
				}

				// validate exclude IDs in related
				if( isset($yuzo_options->exclude_id) && $yuzo_options->exclude_id ){
					$new_no_in_post = explode(",",$yuzo_options->exclude_id);
					if( isset( $args['post__not_in'] ) && $args['post__not_in'] ){
						
						if( is_array( $new_no_in_post ) ){
							foreach($new_no_in_post as $posts_no_in2 => $post_no_in2) {
								array_push($args['post__not_in'], $post_no_in2);
							}
						}

					}else{
						if( is_array( $new_no_in_post ) ){
							$args['post__not_in'] = array();
							foreach($new_no_in_post as $posts_no_in2 => $post_no_in2) {
								array_push($args['post__not_in'], $post_no_in2);
							}
						}
					}
				}


				
				if(  isset($tag_ids) && $tag_ids ){
					$args['tag__in'] =  $tag_ids;
				}


				// validate if tags exclude custom
				if( isset($yuzo_options->exclude_tag) && $yuzo_options->exclude_tag ){
					// if exclude tags
					$no_tags = explode(",",$yuzo_options->exclude_tag);
					$no_tag_values = array();
					foreach ($no_tags as $no_tags_key => $no_tags_value_) {
						$term = get_term_by('name', $no_tags_value_, 'post_tag');
						if( $term ){
							$no_tag_values[] = $term->term_id;
						}
					}

					$args['tag__not_in'] =  $no_tag_values;
				}

			}


			// validate final Related Based
			if( $yuzo_options->related_to == '1' || $yuzo_options->related_to == '4' || $yuzo_options->related_to == '5'){
				unset($args['cat']);
			}


		} // rebuilt query
        
        
        
        // validate real post to show
        /*$posts_to_show = get_posts($args);
        if($posts_to_show){
            foreach($posts_to_show as $post_to_s) : setup_postdata($post_to_s);
                
            endforeach;    
        }*/
        

        

		//var_dump( $args );
		// cache query
		if( $rebuilt_query && ! $break_query_post ){
			$the_query_yuzo = new WP_Query( $args );

			if(  isset($yuzo_options->transient) && $yuzo_options->transient ){
				set_transient( $transient_name , $the_query_yuzo, 60 * $cacheTime );
			}

		}else{
			//$the_query_yuzo = new WP_Query( null );
			$the_query_yuzo = $the_query_yuzo;
		}

		$my_array_views = array();
		$metabox_add_post_first = 1;

		$counter_manual_post = is_array($post_in)?count($post_in):0;
        //echo "contador|";
        //var_dump( $counter_manual_post );
        //var_dump($metabox_add_post_first);
		//var_dump( $counter_manual_post >= $metabox_add_post_first );
		//if( have_posts() && $wp_query->post_count != 0 ){
		// The Loop
		if ( ($the_query_yuzo->have_posts() && $wp_query->post_count != 0) || $counter_manual_post >= $metabox_add_post_first ) {

			// set transitions
			$css_transitions = null;
			if(  isset($yuzo_options->bg_color_hover_transitions) && $yuzo_options->bg_color_hover_transitions ){
				$css_transitions = " -webkit-transition: background {$yuzo_options->bg_color_hover_transitions}s linear; -moz-transition: background {$yuzo_options->bg_color_hover_transitions}s linear; -o-transition: background {$yuzo_options->bg_color_hover_transitions}s linear; transition: background {$yuzo_options->bg_color_hover_transitions}s linear;";
			}

			
			// margin related
			$css_margin = null;
			if( isset($yuzo_options->related_margin) && $yuzo_options->related_margin ){
				$css_margin = " margin: {$yuzo_options->related_margin->top}px  {$yuzo_options->related_margin->right}px  {$yuzo_options->related_margin->bottom}px  {$yuzo_options->related_margin->left}px; ";
			}

			// padding related
			$css_padding = null;
			if( isset($yuzo_options->related_padding) && $yuzo_options->related_padding ){
				$css_padding = " padding: {$yuzo_options->related_padding->top}px  {$yuzo_options->related_padding->right}px  {$yuzo_options->related_padding->bottom}px  {$yuzo_options->related_padding->left}px; ";
			}

			// effects visual
			$css_effects = self::effects();
			$css_shine_effect1="";
			$css_shine_effect2="";
			if( isset($yuzo_options->effect_related) && $yuzo_options->effect_related == 'shine' ){
				$css_shine_effect1=" ilen_shine ";
				$css_shine_effect2=" <span class='shine-effect'></span> ";
			}

			// background size
			$css_background_size = "";
			if( !isset($yuzo_options->background_size) ){
				$css_background_size = 'cover';
			}elseif( isset($yuzo_options->background_size) && $yuzo_options->background_size == 'cover' ){
				$css_background_size = 'cover';
			}elseif( isset($yuzo_options->background_size) && $yuzo_options->background_size == 'contain' ){
				$css_background_size = 'contain';
			}


			// box shadow
			$class_box_shadow = "";
			if( isset($yuzo_options->box_shadow) && $yuzo_options->box_shadow ){
				$class_box_shadow = "box_shadow_related";
			}


			// type image
			$width="";
			$height="";
			/*if( !isset($yuzo_options->type_image) || !$yuzo_options->type_image ){
				$width = ( (int)$yuzo_options->height_image * 15 / 100 ) + (int)$yuzo_options->height_image;
				$height =  (int)$yuzo_options->height_image - ( (int)$yuzo_options->height_image * 20 / 100 );
			}else*/if( isset($yuzo_options->type_image)  && $yuzo_options->type_image ){

				if( $yuzo_options->type_image == 'rectangular' ){
					$width =  ( (int)$yuzo_options->height_image * 15 / 100 ) + (int)$yuzo_options->height_image;
					$height = (int)$yuzo_options->height_image - ( (int)$yuzo_options->height_image * 20 / 100 ) ;
				}elseif( $yuzo_options->type_image == 'square'  ){
					$width =  (int)$yuzo_options->height_image;
					$height = ((int)$yuzo_options->height_image);
				}elseif( $yuzo_options->type_image == 'full-rectangular' ){
					$width =  ( (int)$yuzo_options->height_image * 70 / 100 ) + (int)$yuzo_options->height_image;
					$height = (int)$yuzo_options->height_image - ( (int)$yuzo_options->height_image * 20 / 100 ) ;
				}elseif( $yuzo_options->type_image == 'full-vertical' ){
					$width =  (int)$yuzo_options->height_image;
					$height = isset($yuzo_options->height_full) && $yuzo_options->height_full?  (int)$yuzo_options->height_full: ( (int)$yuzo_options->height_image * 40 / 100 ) + (int)$yuzo_options->height_image ;
				}

			}

			// border radius
			$css_border = null;
			if( isset($yuzo_options->thumbnail_border_radius) && $yuzo_options->thumbnail_border_radius ){
				$css_border = " border-radius: {$yuzo_options->thumbnail_border_radius}% ";

				if(  $yuzo_options->thumbnail_border_radius == 50 ){
					$css_border = " border-radius: {$yuzo_options->thumbnail_border_radius}%; margin:0 auto; width:".$height."px;";
				}
			}


			// target link
			$target_link = null;
			if( isset($yuzo_options->target_link)  && $yuzo_options->target_link ){
				$target_link = "target='_blank'";
			}

			// title center
			$css_title_center = null;
			if( isset($yuzo_options->title_center)  && $yuzo_options->title_center ){
				$css_title_center = "text-align:center;";
			}

			// rel='nofollow'
			$rel_link = "";
			if( isset($yuzo_options->rel_nofollow) && $yuzo_options->rel_nofollow ){
				$rel_link = "rel='nofollow'";    
			}
			

	 
			$count = 1;
			if( isset($yuzo_options->top_text) && $yuzo_options->top_text ){
				$_html .= "<div class='yuzo_clearfixed yuzo__title'>". $this->IF_setHtml( $yuzo_options->top_text ) ."</div>";
			}

			// set colors text and title
			$css_text_color="";$css_text_color_hover="";
			$css_title_color="";$css_title_color_hover="";
			if( isset( $yuzo_options->text_color->color ) && $yuzo_options->text_color->color ){ $css_text_color=$yuzo_options->text_color->color; }
			if( isset( $yuzo_options->text_color->hover ) && $yuzo_options->text_color->hover ){ $css_text_color_hover=$yuzo_options->text_color->hover; }
			if( isset( $yuzo_options->title_color->color ) && $yuzo_options->title_color->color ){ $css_title_color=$yuzo_options->title_color->color; }
			if( isset( $yuzo_options->title_color->hover ) && $yuzo_options->title_color->hover ){ $css_title_color_hover=$yuzo_options->title_color->hover; }


			//while ( have_posts() ) : the_post();
			$_html                  .="<div class='yuzo_wraps'>";
			$post_count_manual      = $counter_manual_post;
			$post_into_manual       = 0; // each that in post manual
			$post_into_manual_fecth = false;
			$post_into_count        = 0;
			$post_i 				= 0; // counter post show, effective
            
            
			while ( $the_query_yuzo->have_posts() || $counter_manual_post >= $metabox_add_post_first ) :
				// $myquery->found_posts
				// $the_query_yuzo->request; // view query sql
				//  START custom first post
				// link: http://wordpress.stackexchange.com/questions/76877/include-a-specific-post-to-the-query-posts-and-remove-it-if-it-is-already-in-the
                // validate if num post = manual num post for break loop
                if( $post_count_manual == 0 ){
                    $the_query_yuzo->the_post();
                    //$post = get_post();
                }else if( $post_count_manual >= $metabox_add_post_first ){
                    if( $post_id = $post_in[$metabox_add_post_first-1] ){
                        //$the_query_yuzo->the_post();
                        $post_into_manual++;
                        if( $post_into_manual > $number_post ){ break; }
                        $post = get_post( $post_id );
                    }elseif($the_query_yuzo->have_posts() && $wp_query->post_count != 0){
						$the_query_yuzo->the_post(); // END custom first post
					}
                }elseif( $the_query_yuzo->have_posts() ){

                    if( $post_into_manual >= $number_post || $number_post  <= $post_i  ){  break; }
                    if( $post_into_manual > 0 && $post_into_manual_fecth == false){
                    	//var_dump($the_query_yuzo->found_posts);
                    	//echo "|num:".($post_into_manual + 1)."|query found:$the_query_yuzo->found_posts";
                        /*for($yj = 1; $yj <= ( $post_into_manual + 1 ); $yj++){
                        	echo 10;
                            $post_into_manual_fecth = true;
                            $the_query_yuzo->the_post();
                            $post = get_post();
                            //var_dump($post);
                        }*/
                        $post_into_count = $post_into_manual ;
                        //for($yj = 1; $yj <= $the_query_yuzo->found_posts && $post_into_count < $number_post ; $yj++){
                        for($yj = 1; $yj <= $the_query_yuzo->found_posts && $yj <= $post_into_manual ; $yj++){
                        	$post_into_count++;
                            $post_into_manual_fecth = true;
                            $the_query_yuzo->the_post();
                            //$post = get_post();
                            //var_dump($post);
                        }
                    }else{
                        $the_query_yuzo->the_post();
                    }
				}else{
				    break;
				}
                $metabox_add_post_first++;
                $post_i++;
 






				$my_array_views = self::getViewsPost_to_yuzo();
				$bold_title = "";
				$text2_extract = "";
				if( $yuzo_options->title_bold =='1'){
					$bold_title = "font-weight:bold;";
				}





				// validate text to show
				$text_to_show = null;
				if( (int)$yuzo_options->text2_length > 0 ){

					if( ! isset($yuzo_options->text_show) ){
						$text_to_show = $post->post_content;
					}elseif( isset($yuzo_options->text_show) && $yuzo_options->text_show == 1 ){
						$text_to_show = $post->post_content;//get_the_content();
					}elseif( isset($yuzo_options->text_show) && $yuzo_options->text_show == 2 ){
						$text_to_show = $post->post_excerpt;
					}


					$text2_extract = '<span class="yuzo_text" style="font-size:'.((int)$yuzo_options->font_size - 4).'px;" >'.$if_utils->IF_setHtml( $if_utils->IF_cut_text( $text_to_show , (int)$yuzo_options->text2_length, TRUE ) ).'</span>';

				}



	 
				if( $yuzo_options->style == 1 ){

					// counter
					if( isset($my_array_views['top']) && $my_array_views['top'] ){
						$my_array_views['top'] = '<div>'.$my_array_views['top'].'</div>';
					}
					if( isset($my_array_views['bottom']) && $my_array_views['bottom'] ){
						$my_array_views['bottom'] = '<div>'.$my_array_views['bottom'].'</div>';
					}

					if( $yuzo_options->type_image == 'full-vertical' ){
						$size_css = " $width $height ";
					}else{
						$size_css = $yuzo_options->background_size;
					}

						$image = $this->IF_get_image(  $yuzo_options->thumbnail_size, $yuzo_options->default_image, get_the_ID(), $order_image );
						  $_html .= '
						  <div class="relatedthumb relatedpost-'.get_the_ID().' '.$class_box_shadow.'" style="width:'.$width.'px;float:left;overflow:hidden;'.$css_title_center.'">  
							  
							  <a '.$rel_link.' href="'.get_permalink().'" '.$target_link.' >
									  <div class="yuzo-img-wrap '.$css_shine_effect1.'" style="/*width: '.$width.'px;height:'.$height.'px;*/">
										'.$css_shine_effect2.'
										<div class="yuzo-img" style="background:url(\''.$image['src'].'\') 50% 50% no-repeat;width: '.$width.'px;;max-width:100%;height:'.$height.'px;margin-bottom: 5px;background-size: '.$size_css.'; '.$css_border.'"></div>
									  </div>
									  '.$my_array_views['top'].'
								   <span class="yuzo__text--title" style="font-size:'.$yuzo_options->font_size.'px;'.$bold_title.'">'.$if_utils->IF_setHtml( $if_utils->IF_cut_text( get_the_title(), $yuzo_options->text_length , true ) ).'</span>
							  '.$text2_extract .'
							  '.$my_array_views['bottom'].'
							  </a>

						  </div>';
					  $style="<style>
								.yuzo_related_post img{width:".$width."px !important; height:{$height}px !important;}
								.yuzo_related_post .relatedthumb{line-height:".((int)$yuzo_options->font_size +2 )."px;background:{$yuzo_options->bg_color->color} !important;color:{$css_text_color}!important;}
								.yuzo_related_post .relatedthumb:hover{background:{$yuzo_options->bg_color->hover} !important;$css_transitions;color:{$css_text_color_hover}!important;}
								.yuzo_related_post .relatedthumb a{color:{$css_title_color}!important;}
								.yuzo_related_post .relatedthumb a:hover{ color:$css_title_color_hover}!important;}
								.yuzo_related_post .relatedthumb:hover a{ color:{$css_title_color_hover}!important;}
								.yuzo_related_post .relatedthumb:hover .yuzo__text--title{ color:{$css_title_color_hover}!important;}
								.yuzo_related_post .yuzo_text, .yuzo_related_post .yuzo_views_post {color:{$css_text_color}!important;}
								.yuzo_related_post .relatedthumb:hover .yuzo_text, .yuzo_related_post:hover .yuzo_views_post {color:{$css_text_color_hover}!important;}
								.yuzo_related_post .relatedthumb{ $css_margin $css_padding }
								$css_effects
								</style>";

						if ( ! isset($yuzo_options->yuzo_conflict) || ! $yuzo_options->yuzo_conflict ) {
						  $script="<script>
						  jQuery(document).ready(function( $ ){
							jQuery('.yuzo_related_post .yuzo_wraps').equalizer({ columns : '> div' });
						   });
						  </script>";
						}

				}elseif( $yuzo_options->style == 2 ){
						$image = $this->IF_get_image(  $yuzo_options->thumbnail_size, $yuzo_options->default_image, get_the_ID(), $order_image );
						$_html .= '
						<div class="relatedthumb yuzo-list  '.$class_box_shadow.'" style="'.$css_title_center.'"  >  
						  <a '.$rel_link.' href="'.get_permalink().'" class="image-list" '.$target_link.' >
						  <div class="yuzo-img-wrap '.$css_shine_effect1.'" style="width: '.$width.'px;height:'.$height.'px;">
									'.$css_shine_effect2.'
									 <div class="yuzo-img" style="background:url(\''.$image['src'].'\') 50% 50% no-repeat;width: '.$width.'px;height:'.$height.'px;margin-bottom: 5px;background-size:  '.$css_background_size.'; '.$css_border.' "></div>
						  </div>
						  </a>
						  <a '.$rel_link.' class="link-list yuzo__text--title" href="'.get_permalink().'" style="font-size:'.$yuzo_options->font_size.'px;'.$bold_title.';line-height:'.( (int)$yuzo_options->font_size + 8).'px;">'.$my_array_views['top'].' '.$if_utils->IF_setHtml( $if_utils->IF_cut_text( get_the_title(), $yuzo_options->text_length , true ) ).'  '.$my_array_views['bottom'].'</a>
								'.$text2_extract .'
						   
						</div>';
							$style="<style>
						.yuzo_related_post .relatedthumb { background:{$yuzo_options->bg_color->color} !important;$css_transitions;color:{$css_text_color}!important; }
						.yuzo_related_post .relatedthumb:hover{background:{$yuzo_options->bg_color->hover} !important;color:{$css_text_color_hover}!important;}
						.yuzo_related_post .yuzo_text, .yuzo_related_post .yuzo_views_post {color:{$css_text_color}!important;}
						.yuzo_related_post .relatedthumb:hover .yuzo_text, .yuzo_related_post:hover .yuzo_views_post {color:{$css_text_color_hover}!important;}
						.yuzo_related_post .relatedthumb a{color:{$css_title_color}!important;}
						.yuzo_related_post .relatedthumb a:hover{color:{$css_title_color_hover}!important;}
						.yuzo_related_post .relatedthumb:hover a{ color:{$css_title_color_hover}!important;}
						.yuzo_related_post .relatedthumb{ $css_margin $css_padding }

						$css_effects
						</style>";
						
				}elseif( $yuzo_options->style == 3 ){
					//$image = IF_get_image(  $yuzo_options->thumbnail_size, $yuzo_options->default_image );
					$_html .= '
					<div class="relatedthumb yuzo-list"  >  
						<a '.$rel_link.' class="link-list yuzo__text--title" href="'.get_permalink().'"  '.$target_link.'  style="font-size:'.$yuzo_options->font_size.'px;'.$bold_title.';line-height:'.( (int)$yuzo_options->font_size + 8).'px;">'.$my_array_views['top'].' '.$if_utils->IF_setHtml( $if_utils->IF_cut_text( get_the_title(), $yuzo_options->text_length , true ) ).' '.$my_array_views['bottom'].'</a>
							  '.$text2_extract .'
					</div>';
					$style="<style>
							.yuzo_related_post .relatedthumb{background:{$yuzo_options->bg_color->color} !important;$css_transitions;color:{$css_text_color}!important;}
							.yuzo_related_post .relatedthumb:hover{background:{$yuzo_options->bg_color->hover} !important;$css_transitions;color:{$css_text_color_hover}!important;}
							.yuzo_related_post .yuzo_text, .yuzo_related_post .yuzo_views_post {color:{$css_text_color}!important;}
							.yuzo_related_post .relatedthumb:hover .yuzo_text, .yuzo_related_post:hover .yuzo_views_post {color:{$css_text_color_hover}!important;}
							.yuzo_related_post .relatedthumb a{color:{$css_title_color}!important;}
							.yuzo_related_post .relatedthumb a:hover{color:{$css_title_color_hover}!important;}
							.yuzo_related_post .relatedthumb:hover a{ color:{$css_title_color_hover}!important;}
							.yuzo_related_post .relatedthumb{ $css_margin $css_padding }
							</style>";
									
				}elseif( $yuzo_options->style == 4 ){
					//$image = IF_get_image(  $yuzo_options->thumbnail_size, $yuzo_options->default_image );
					$_html .= '
					<div class="relatedthumb yuzo-list-color color-'.$count.'"  >  
						<a '.$rel_link.' class="link-list yuzo__text--title" href="'.get_permalink().'"  '.$target_link.'  style="font-size:'.$yuzo_options->font_size.'px;'.$bold_title.';line-height:'.( (int)$yuzo_options->font_size + 8).'px;">'.$my_array_views['top'].' '.$if_utils->IF_setHtml( $if_utils->IF_cut_text( get_the_title(), $yuzo_options->text_length , true ) ).' '.$my_array_views['bottom'].'</a>
							  '.$text2_extract .'
					</div>';
					$style="<style>
						.yuzo_related_post .yuzo_text {color:{$yuzo_options->text_color->color};}
						.yuzo_related_post .relatedthumb a{color:{$yuzo_options->title_color->color};}
					</style>";
					
				}
				$count++;
			endwhile;
			$_html .= '</div> <!-- end wrap -->';

		}

		// Reset Post Data
		if( $the_query_yuzo->have_posts() && $wp_query->post_count != 0 ){
			wp_reset_postdata();
		}

	}else{
	  
		if( $yuzo_options->display_random  ){

			$args = array(
					'posts_per_page'   => (int)$yuzo_options->display_post,
					'post_type'        => (array)$yuzo_options->post_type,
					'post_status'      => 'publish',
					'ignore_sticky_posts ' => 1,
					'orderby'          => 'rand'
				   );

			$metabox_add_post_first = 0;

			// validate post current
			$post__not_in[] = $post->ID;
			$args['post__not_in'] = $post__not_in;


			//query_posts( $args ); 
			$the_query_yuzo = new WP_Query( $args );


				// set transitions
			$css_transitions = null;
			if(  isset($yuzo_options->bg_color_hover_transitions) && $yuzo_options->bg_color_hover_transitions ){
				$css_transitions = " -webkit-transition: background {$yuzo_options->bg_color_hover_transitions}s linear; -moz-transition: background {$yuzo_options->bg_color_hover_transitions}s linear; -o-transition: background {$yuzo_options->bg_color_hover_transitions}s linear; transition: background {$yuzo_options->bg_color_hover_transitions}s linear;";
			}

			// border radius
			$css_border = null;
			if( isset($yuzo_options->thumbnail_border_radius) && $yuzo_options->thumbnail_border_radius ){
				$css_border = " border-radius: {$yuzo_options->thumbnail_border_radius}% ";

				if(  $yuzo_options->thumbnail_border_radius == 50 ){
					$css_border = " border-radius: {$yuzo_options->thumbnail_border_radius}%; margin:0 auto; width:".$height."px;";
				}
			}

			// margin related
			$css_margin = null;
			if( isset($yuzo_options->related_margin) && $yuzo_options->related_margin ){
				$css_margin = " margin: {$yuzo_options->related_margin->top}px  {$yuzo_options->related_margin->right}px  {$yuzo_options->related_margin->bottom}px  {$yuzo_options->related_margin->left}px; ";
			}

			// padding related
			$css_padding = null;
			if( isset($yuzo_options->related_padding) && $yuzo_options->related_padding ){
				$css_padding = " padding: {$yuzo_options->related_padding->top}px  {$yuzo_options->related_padding->right}px  {$yuzo_options->related_padding->bottom}px  {$yuzo_options->related_padding->left}px; ";
			}

			// effects visual
			$css_effects = self::effects();
			$css_shine_effect1="";
			$css_shine_effect2="";
			if( isset($yuzo_options->effect_related) && $yuzo_options->effect_related == 'shine' ){
				$css_shine_effect1=" ilen_shine ";
				$css_shine_effect2=" <span class='shine-effect'></span> ";
			}

			// background size
			$css_background_size = "";
			if( !isset($yuzo_options->background_size) ){
				$css_background_size = 'cover';
			}elseif( isset($yuzo_options->background_size) && $yuzo_options->background_size == 'cover' ){
				$css_background_size = 'cover';
			}elseif( isset($yuzo_options->background_size) && $yuzo_options->background_size == 'contain' ){
				$css_background_size = 'contain';
			}


			// box shadow
			$class_box_shadow = "";
			if( isset($yuzo_options->box_shadow) && $yuzo_options->box_shadow ){
				$class_box_shadow = "box_shadow_related";
			}


			// type image
			$width="";
			$height="";
			if( !isset($yuzo_options->type_image) || !$yuzo_options->type_image ){
				$width = ((int)$yuzo_options->height_image+15);
				$height = ((int)$yuzo_options->height_image-20);
			}elseif( isset($yuzo_options->type_image)  && $yuzo_options->type_image ){

				if( $yuzo_options->type_image == 'rectangular' ){
					$width = ((int)$yuzo_options->height_image+15);
					$height = ((int)$yuzo_options->height_image-20);
				}elseif( $yuzo_options->type_image == 'square'  ){
					$width = ((int)$yuzo_options->height_image);
					$height = ((int)$yuzo_options->height_image);
				}

			}

			// target link
			$target_link = null;
			if( isset($yuzo_options->target_link)  && $yuzo_options->target_link ){
				$target_link = "target='_blank'";
			}

			// title center
			$css_title_center = null;
			if( isset($yuzo_options->title_center)  && $yuzo_options->title_center ){
				$css_title_center = "text-align:center;";
			}

			// rel='nofollow'
			$rel_link = "";
			if( isset($yuzo_options->rel_nofollow) && $yuzo_options->rel_nofollow ){
				$rel_link = "rel='nofollow'";    
			}
			

	 
			$count = 1;
			if( isset($yuzo_options->top_text) && $yuzo_options->top_text ){
				$_html .= "<div class='yuzo_clearfixed yuzo__title yuzo__title'>". $this->IF_setHtml( $yuzo_options->top_text ) ."</div>";
			}

			// set colors text and title
			$css_text_color="";$css_text_color_hover="";
			$css_title_color="";$css_title_color_hover="";
			if( isset( $yuzo_options->text_color->color ) && $yuzo_options->text_color->color ){ $css_text_color=$yuzo_options->text_color->color; }
			if( isset( $yuzo_options->text_color->hover ) && $yuzo_options->text_color->hover ){ $css_text_color_hover=$yuzo_options->text_color->hover; }
			if( isset( $yuzo_options->title_color->color ) && $yuzo_options->title_color->color ){ $css_title_color=$yuzo_options->title_color->color; }
			if( isset( $yuzo_options->title_color->hover ) && $yuzo_options->title_color->hover ){ $css_title_color_hover=$yuzo_options->title_color->hover; }




			//while ( have_posts() ) : the_post();

			while ( $the_query_yuzo->have_posts()  ) :

				// $the_query_yuzo->request; // view query sql
				// START custom first post
				// link: http://wordpress.stackexchange.com/questions/76877/include-a-specific-post-to-the-query-posts-and-remove-it-if-it-is-already-in-the
				if ( isset($post_in) && count($post_in) >  $metabox_add_post_first ) {
					$post_id = $post_in[$metabox_add_post_first]; // This is the ID of the first post to be displayed on slider
					$the_query_yuzo->the_post(); // END custom first post
					$post = get_post( $post_id );
				}else{
					//the_post(); // END custom first post
					$the_query_yuzo->the_post(); // END custom first post
				}
				$metabox_add_post_first++;

				




				$my_array_views = self::getViewsPost_to_yuzo();
				$bold_title = "";
				$text2_extract = "";
				if( $yuzo_options->title_bold =='1'){
					$bold_title = "font-weight:bold;";
				}


				// validate text to show
				$text_to_show = null;
				if( (int)$yuzo_options->text2_length > 0 ){

					if( ! isset($yuzo_options->text_show) ){
						$text_to_show = $post->post_content;
					}elseif( isset($yuzo_options->text_show) && $yuzo_options->text_show == 1 ){
						$text_to_show = $post->post_content; //get_the_content();
					}elseif( isset($yuzo_options->text_show) && $yuzo_options->text_show == 2 ){
						$text_to_show = $post->post_excerpt;
					}


					$text2_extract = '<span class="yuzo_text" style="font-size:'.((int)$yuzo_options->font_size - 4).'px;" >'.$if_utils->IF_setHtml( $if_utils->IF_cut_text( $text_to_show , (int)$yuzo_options->text2_length, TRUE ) ).'</span>';

				}



	 
				if( $yuzo_options->style == 1 ){

					// counter
					if( isset($my_array_views['top']) && $my_array_views['top'] ){
						$my_array_views['top'] = '<div>'.$my_array_views['top'].'</div>';
					}
					if( isset($my_array_views['bottom']) && $my_array_views['bottom'] ){
						$my_array_views['bottom'] = '<div>'.$my_array_views['bottom'].'</div>';
					}

						$image = $this->IF_get_image(  $yuzo_options->thumbnail_size, $yuzo_options->default_image, get_the_ID(), $order_image );
						  $_html .= '
						  <div class="relatedthumb '.$class_box_shadow.'" style="width:'.$width.'px;float:left;overflow:hidden;'.$css_title_center.'">  
							  
							  <a '.$rel_link.' href="'.get_permalink().'" '.$target_link.' >
									  <div class="yuzo-img-wrap '.$css_shine_effect1.'" style="width: '.$width.'px;height:'.$height.'px;">
										'.$css_shine_effect2.'
										<div class="yuzo-img" style="background:url(\''.$image['src'].'\') 50% 50% no-repeat;width: '.$width.'px;height:'.$height.'px;margin-bottom: 5px;background-size: '.$css_background_size.'; '.$css_border.'"></div>
									  </div>
									  '.$my_array_views['top'].'
								   <span class="yuzo__text--title" style="font-size:'.$yuzo_options->font_size.'px;'.$bold_title.'">'.$if_utils->IF_setHtml( $if_utils->IF_cut_text( get_the_title(), $yuzo_options->text_length , true ) ).'</span>
							  '.$text2_extract .'
							  '.$my_array_views['bottom'].'
							  </a>

						  </div>';
					  $style="<style>
								.yuzo_related_post img{width:".((int)$yuzo_options->height_image + 10 )."px !important; height:{$yuzo_options->height_image}px !important;}
								.yuzo_related_post .relatedthumb{line-height:".((int)$yuzo_options->font_size +2 )."px;background:{$yuzo_options->bg_color->color} !important;color:{$css_text_color}!important;}
								.yuzo_related_post .relatedthumb:hover{background:{$yuzo_options->bg_color->hover} !important;$css_transitions;color:{$css_text_color_hover}!important;}
								.yuzo_related_post .relatedthumb a{color:{$css_title_color}!important;}
								.yuzo_related_post .relatedthumb a:hover{ color:$css_title_color_hover}!important;}
								.yuzo_related_post .relatedthumb:hover a{ color:{$css_title_color_hover}!important;}
								.yuzo_related_post .yuzo_text {color:{$css_text_color}!important;}
								.yuzo_related_post .relatedthumb:hover .yuzo_text {color:{$css_text_color_hover}!important;}
								.yuzo_related_post .relatedthumb{ $css_margin $css_padding }
								$css_effects
								</style>";

						if ( ! isset($yuzo_options->yuzo_conflict) || ! $yuzo_options->yuzo_conflict ) {
						  $script="<script>
						  jQuery(document).ready(function( $ ){
							//jQuery('.yuzo_related_post').equalizer({ overflow : 'relatedthumb' });
							jQuery('.yuzo_related_post .yuzo_wraps').equalizer({ columns : '> div' });
						   })
						  </script>";
						}

				}elseif( $yuzo_options->style == 2 ){
						$image = $this->IF_get_image(  $yuzo_options->thumbnail_size, $yuzo_options->default_image, get_the_ID(), $order_image );
						$_html .= '
						<div class="relatedthumb yuzo-list  '.$class_box_shadow.'" style="'.$css_title_center.'"  >  
						  <a '.$rel_link.' href="'.get_permalink().'" class="image-list" '.$target_link.' >
						  <div class="yuzo-img-wrap '.$css_shine_effect1.'" style="width: '.$width.'px;height:'.$height.'px;">
									'.$css_shine_effect2.'
									 <div class="yuzo-img" style="background:url(\''.$image['src'].'\') 50% 50% no-repeat;width: '.((int)$yuzo_options->height_image+20).'px;height:'.$height.'px;margin-bottom: 5px;background-size:  '.$css_background_size.'; '.$css_border.' "></div>
						  </div>
						  </a>
						  <a '.$rel_link.' class="link-list yuzo__text--title" href="'.get_permalink().'" style="font-size:'.$yuzo_options->font_size.'px;'.$bold_title.';line-height:'.( (int)$yuzo_options->font_size + 8).'px;">'.$my_array_views['top'].' '.$if_utils->IF_setHtml( $if_utils->IF_cut_text( get_the_title(), $yuzo_options->text_length , true ) ).'  '.$my_array_views['bottom'].'</a>
								'.$text2_extract .'
						   
						</div>';
							$style="<style>
						.yuzo_related_post .relatedthumb { background:{$yuzo_options->bg_color->color} !important;$css_transitions;color:{$css_text_color}!important; }
						.yuzo_related_post .relatedthumb:hover{background:{$yuzo_options->bg_color->hover} !important;color:{$css_text_color_hover}!important;}
						.yuzo_related_post .yuzo_text {color:{$css_text_color}!important;}
						.yuzo_related_post .relatedthumb:hover .yuzo_text {color:{$css_text_color_hover}!important;}
						.yuzo_related_post .relatedthumb a{color:{$css_title_color}!important;}
						.yuzo_related_post .relatedthumb a:hover{color:{$css_title_color_hover}!important;}
						.yuzo_related_post .relatedthumb:hover a{ color:{$css_title_color_hover}!important;}
						.yuzo_related_post .relatedthumb{ $css_margin $css_padding }
						$css_effects
						</style>";
						
				}elseif( $yuzo_options->style == 3 ){
					//$image = IF_get_image(  $yuzo_options->thumbnail_size, $yuzo_options->default_image );
					$_html .= '
					<div class="relatedthumb yuzo-list .yuzo__text--title"  >  
						<a '.$rel_link.' class="link-list yuzo__text--title" href="'.get_permalink().'"  '.$target_link.'  style="font-size:'.$yuzo_options->font_size.'px;'.$bold_title.';line-height:'.( (int)$yuzo_options->font_size + 8).'px;">'.$my_array_views['top'].' '.$if_utils->IF_setHtml( $if_utils->IF_cut_text( get_the_title(), $yuzo_options->text_length , true ) ).' '.$my_array_views['bottom'].'</a>
							  '.$text2_extract .'
					</div>';
					$style="<style>
							.yuzo_related_post .relatedthumb{background:{$yuzo_options->bg_color->color} !important;$css_transitions;color:{$css_text_color}!important;}
							.yuzo_related_post .relatedthumb:hover{background:{$yuzo_options->bg_color->hover} !important;$css_transitions;color:{$css_text_color_hover}!important;}
							.yuzo_related_post .yuzo_text {color:{$css_text_color}!important;}
							.yuzo_related_post .relatedthumb:hover .yuzo_text {color:{$css_text_color_hover}!important;}
							.yuzo_related_post .relatedthumb a{color:{$css_title_color}!important;}
							.yuzo_related_post .relatedthumb a:hover{color:{$css_title_color_hover}!important;}
							.yuzo_related_post .relatedthumb:hover a{ color:{$css_title_color_hover}!important;}
							.yuzo_related_post .relatedthumb{ $css_margin $css_padding }
							</style>";
									
				}elseif( $yuzo_options->style == 4 ){
					//$image = IF_get_image(  $yuzo_options->thumbnail_size, $yuzo_options->default_image );
					$_html .= '
					<div class="relatedthumb yuzo-list-color color-'.$count.'"  >  
						<a '.$rel_link.' class="link-list yuzo__text--title" href="'.get_permalink().'"  '.$target_link.'  style="font-size:'.$yuzo_options->font_size.'px;'.$bold_title.';line-height:'.( (int)$yuzo_options->font_size + 8).'px;">'.$my_array_views['top'].' '.$if_utils->IF_setHtml( $if_utils->IF_cut_text( get_the_title(), $yuzo_options->text_length , true ) ).' '.$my_array_views['bottom'].'</a>
							  '.$text2_extract .'
					</div>';
					$style="<style>
						.yuzo_related_post .yuzo_text {color:{$yuzo_options->text_color};}
						.yuzo_related_post .relatedthumb a{color:{$yuzo_options->title_color};}
					</style>";
					
				}
				$count++;
			endwhile;

			// Reset Post Data
			if( $the_query_yuzo->have_posts() && $wp_query->post_count != 0 ){
				wp_reset_postdata();
			}
		}

	}
	  
	$_html .= "\n</div> $style $script <!-- End Yuzo :) -->";
	  
	$post = $orig_post;  
	
	// Reset Query
	//wp_reset_query(); 

  
	return $content.$_html;
  }

  
  function script_and_style_admin(){

	global $pagenow;


	//if( isset($_GET["page"]) &&  $_GET["page"] == $this->parameter["id_menu"] ){
	wp_enqueue_script( 'admin-js-'.$this->parameter["name_option"], plugins_url('/assets/js/admin.js',__FILE__), array( 'jquery' ), $this->parameter['version'], true );
	//}

	//if( (isset($_GET["page"]) &&  $_GET["page"] == $this->parameter["id_menu"]) || $pagenow == 'widgets.php' ){
	// Register styles
	wp_register_style( 'admin-css-'.$this->parameter["name_option"], plugins_url('/assets/css/admin.css',__FILE__),'all',$this->parameter['version'] );
	// Enqueue styles
	wp_enqueue_style( 'admin-css-'.$this->parameter["name_option"] );


	if( isset($_GET["page"]) && $_GET["page"] == "yuzo-welcome" ){
		wp_enqueue_script( 'admin-js-welcome-'.$this->parameter["name_option"], plugins_url('/assets/ilenframework/assets/js/wow.min.js',__FILE__), array( 'jquery' ), $this->parameter['version'], true );
		// Register styles
		wp_register_style( 'admin-css-welcome-1-'.$this->parameter["name_option"], plugins_url('/assets/ilenframework/assets/css/animate.css',__FILE__),'all',$this->parameter['version'] );
		// Enqueue styles
		wp_enqueue_style( 'admin-css-welcome-1-'.$this->parameter["name_option"] );

		// Register styles
		wp_register_style( 'admin-css-welcome-2-'.$this->parameter["name_option"], plugins_url('/assets/ilenframework/assets/css/font-awesome.css',__FILE__),'all',$this->parameter['version'] );
		// Enqueue styles
		wp_enqueue_style( 'admin-css-welcome-2-'.$this->parameter["name_option"] );
	}
 
  }


  function script_and_style_front(){

	global $yuzo_options;

	// Register styles
	wp_register_style( 'front-css-'.$this->parameter["name_option"], plugins_url('/assets/css/style.css',__FILE__),'all',$this->parameter['version'] );
	// Enqueue styles
	wp_enqueue_style( 'front-css-'.$this->parameter["name_option"] );


	if ( ! isset($yuzo_options->yuzo_conflict) || ! $yuzo_options->yuzo_conflict ) {
		wp_enqueue_script( 'front-js-equalizer-'.$this->parameter["name_option"], plugins_url('/assets/js/jquery.equalizer.js',__FILE__), array( 'jquery' ), $this->parameter['version'], true );
	}

	// RTL!
	if( is_rtl() ){

	  // Register styles
	  wp_register_style( 'front-css-rtl-'.$this->parameter["name_option"], plugins_url('/assets/css/rtl.css',__FILE__),'all',$this->parameter['version'] );
	  // Enqueue styles
	  wp_enqueue_style( 'front-css-rtl-'.$this->parameter["name_option"] );

	}

  }


function yuzo_extract_title(  $text = "",  $length = 30 ){

  $excert  = trim( $text );
  if( strlen( $excert  ) > (int)$length )
	$title = substr( $excert , 0 , (int)$length )."...";
  else
	$title = substr( $excert , 0 , (int)$length );

  return $title;

}


function effects(){

   global $yuzo_options;

   $css_effects = null;
   if( isset( $yuzo_options->effect_related ) ){

	  if( $yuzo_options->effect_related == 'enlarge' ){
		/* link: http://santyweb.blogspot.com/2011/06/css-agrandar-imagenes-o-texto-al-pasar.html */
		$css_effects = ".yuzo_related_post .relatedthumb{
display:block!important;
-webkit-transition:-webkit-transform 0.3s ease-out!important;
-moz-transition:-moz-transform 0.3s ease-out!important;
-o-transition:-o-transform 0.3s ease-out!important;
-ms-transition:-ms-transform 0.3s ease-out!important;
transition:transform 0.3s ease-out!important;
}
.yuzo_related_post .relatedthumb:hover{
-moz-transform: scale(1.1);
-webkit-transform: scale(1.1);
-o-transform: scale(1.1);
-ms-transform: scale(1.1);
transform: scale(1.1)
}
.yuzo_related_post{
	overflow:inherit!important;
}";
	  }elseif( $yuzo_options->effect_related == 'zoom_icon_link' ){
		/* link: http://santyweb.blogspot.com/2011/06/css-agrandar-imagenes-o-texto-al-pasar.html */
		$css_effects = ".yuzo_related_post .relatedthumb .yuzo-img{
  -webkit-transition:all 0.3s ease-out;
  -moz-transition:all 0.3s ease-out;
  -o-transition:all 0.3s ease-out;
  -ms-transition:all 0.3s ease-out;
  transition:all 0.3s ease-out;
}
.yuzo_related_post .relatedthumb .yuzo-img-wrap{
  overflow:hidden;
  background: url(".$this->parameter['theme_imagen']."/link-overlay.png) no-repeat center;
}
.yuzo_related_post .relatedthumb:hover .yuzo-img {
  opacity: 0.7;
  -webkit-transform: scale(1.2);
  transform: scale(1.2);
}";
	  }
	  elseif( $yuzo_options->effect_related == 'shine' ){
		$css_effects = ".yuzo_related_post .relatedthumb{}";
	  }


	  
	  
   }


   return $css_effects;


}


function hits( $content ="" ){

	global $wpdb;

	if(!is_singular()) return;


	$post_ID = get_the_ID();

	$table_name = $wpdb->prefix . 'yuzoviews';

	if( ! $wpdb->query("UPDATE $table_name  SET views =views+1,last_viewed = '".date("Y-m-d H:m:i")."',modified=".time()." WHERE post_id = $post_ID") ){

		@$wpdb->query("insert into $table_name values(0,$post_ID,1,'".date("Y-m-d H:m:i")."',".time().")");

	}

	return $content;

}

function hits_ajax( $content = "" ){

	if(!$_GET['is_singular']) return;

	$post_ID = (int)$_GET["postviews_id"];

	global $wpdb;

	$table_name = $wpdb->prefix . 'yuzoviews';

	if( ! $wpdb->query("UPDATE $table_name  SET views =views+1,last_viewed = '".date("Y-m-d H:m:i")."',modified=".time()." WHERE post_id = $post_ID") ){

		@$wpdb->query("insert into $table_name values(0,$post_ID,1,'".date("Y-m-d H:m:i")."',".time().")");

	}

	return $content;

	wp_die();

}








/*********************************CODE-3********************************************
* @Author: Boutros AbiChedid 
* @Date:   January 16, 2012
* @Websites: http://bacsoftwareconsulting.com/ ; http://blueoliveonline.com/
* @Description: Adds a Non-Sortable 'Views' Columnn to the Post Tab in WP dashboard.
* This code requires CODE-1(and CODE-2) as a prerequesite.
* Code is browser and JavaScript independent.
* @Tested on: WordPress version 3.2.1
***********************************************************************************/

//Gets the  number of Post Views to be used later.
function yuzo_get_PostViews($post_ID, $count_key = '',$format = true){

	global $yuzo_options;
	$transient_name      = "yuzo_view_cache_$post_ID";
	$cacheTime           = 20; // minutes
	$the_cache_yuzo_view = isset($yuzo_options->transient) && $yuzo_options->transient?false:true; //false;


	// verify cache query
	if(  isset($yuzo_options->transient) && $yuzo_options->transient ){
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		if( false === ($count = get_transient($transient_name) ) || ( current_user_can( 'manage_options' )  && !isset($_GET['P3_NOCACHE']) )  ){
			$the_cache_yuzo_view  = true;
		}
	}
 
	if( $the_cache_yuzo_view == true ){

		$count = 0;
		if( isset($yuzo_options->meta_views) && $yuzo_options->meta_views == 'other' ){
			$count_key = isset($yuzo_options->meta_views_custom)? $yuzo_options->meta_views_custom:'';
		}


		//Returns values of the custom field with the specified key from the specified post.
		if( $count_key != 'popularposts' && !$count_key ){
			$count = self::yuzo_get_views($post_ID); // default yuzo
		}elseif( $count_key == 'popularposts'  ){ // if use 'WordPress Popular Posts'
			$count = self::wpp_get_views( $post_ID );
		}else{
			$count = get_post_meta($post_ID, $count_key , true); // other custom views
		}

		if(  isset($yuzo_options->transient) && $yuzo_options->transient ){
			set_transient( $transient_name , $count, 60 * $cacheTime); // set cache
		}
 

	}else{
		$count = $count;
	}

	// format
	

	//cut counter
	if( $format ){
		if( isset( $yuzo_options->cut_hit ) && $yuzo_options->cut_hit ){
			$count = $this->cut_counter($count);
		}elseif( isset($yuzo_options->format_count) && $yuzo_options->format_count ){
			$count = number_format( (int)$count, 0, '', "$yuzo_options->format_count");
		}
	}

 
	return $count;
}


   
//Function that Adds a 'Views' Column to your Posts tab in WordPress Dashboard.
function yuzo_post_column_views($newcolumn){
	//Retrieves the translated string, if translation exists, and assign it to the 'default' array.
	$newcolumn['yuzo_post_views'] = __('Views',$this->parameter['name_option']);
	return $newcolumn;
}



//Function that Populates the 'Views' Column with the number of views count.
function yuzo_post_custom_column_views($column_name, $id){

	global $yuzo_options;

	if( isset($yuzo_options->disabled_counter) && $yuzo_options->disabled_counter ) return;
	 
	if($column_name === 'yuzo_post_views'){
		// Display the Post View Count of the current post.
		// get_the_ID() - Returns the numeric ID of the current post.

		$_html = "";
		$counter = self::yuzo_get_PostViews(get_the_ID(),'',false);
		if( $counter < 1000 ){
			$_html = "<i class='el el-fire color_flare_normal'></i> $counter";
		}elseif( $counter >= 1000 && $counter < 10000 ){
			$_html = "<i class='el el-fire  color_flare_hot'></i> $counter";
		}elseif( $counter >= 10000 && $counter < 25000 ){
			$_html = "<i class='el el-fire  color_flare_hot2'></i> $counter";
		}elseif( $counter >= 25000 && $counter < 50000 ){
			$_html = "<i class='el el-fire  color_flare_hot3'></i> $counter";
		}elseif( $counter >= 50000 ){
			$_html = "<i class='el el-fire  color_flare_hot4'></i> $counter";
		}

		echo $_html;
	}

}


function getViewsPost_to_yuzo(){

	global $yuzo_options;

	if( isset($yuzo_options->disabled_counter) && $yuzo_options->disabled_counter ) return;

	// Display Views
	$counts            = 0;
	$meta_views_top    = "";
	$meta_views_bottom = "";

	if( isset($yuzo_options->meta_views) && $yuzo_options->meta_views && isset($yuzo_options->show_in_related_post) && $yuzo_options->show_in_related_post ){

	// get count
	if( $yuzo_options->meta_views == 'yuzo-views' ){

		$counts = self::yuzo_get_PostViews( get_the_ID() );

	}elseif( $yuzo_options->meta_views == 'other' ){

	  $meta_views_custom = isset( $yuzo_options->meta_views_custom )?$yuzo_options->meta_views_custom:'';
	  $counts = self::yuzo_get_PostViews( get_the_ID(), $meta_views_custom  );

	}

	// format
	/*if( isset($yuzo_options->format_count) && $yuzo_options->format_count ){
	  $counts = number_format((int)$counts, 0, '', "$yuzo_options->format_count");
	}*/


	//get postition
	if( isset( $yuzo_options->show_in_related_post_position ) && $yuzo_options->show_in_related_post_position ){

	  $text_views = "";
	  $class_views= "";
	  if( isset(  $yuzo_options->show_in_related_post_text ) && $yuzo_options->show_in_related_post_text ){
		$text_views = $yuzo_options->show_in_related_post_text;
	  }

	  /*if( isset(  $yuzo_options->show_in_related_post_text ) && !$yuzo_options->show_in_related_post_text ){
		$class_views = 'yuzo_icon_views';
	  }*/

	  if( $yuzo_options->show_in_related_post_position == 'show-views-top' ){
		$meta_views_top = "<div class='yuzo_views_post yuzo_icon_views yuzo_icon_views__top' style='font-size:".((int)$yuzo_options->font_size - 2)."px;'>$text_views $counts</div>";
	  }elseif( $yuzo_options->show_in_related_post_position == 'show-views-bottom' ){
		$meta_views_bottom = "<div class='yuzo_views_post yuzo_icon_views yuzo_icon_views__bottom' style='font-size:".((int)$yuzo_options->font_size - 2)."px;'>$text_views $counts</div>";
	  }

	}


  }

  $array_views_yuzo_post = array();

  $array_views_yuzo_post['top'] = $meta_views_top;
  $array_views_yuzo_post['bottom'] = $meta_views_bottom;

  return $array_views_yuzo_post;

}

### Function: Show 1k instead of 1,000
function cut_counter($input){

	global $yuzo_options;

	if( !isset($yuzo_options->format_count) || !$yuzo_options->format_count ){
		$yuzo_options->format_count = ".";
	}

	if($input < 1000){
		return $input;
	}elseif($input > 1000000 ){
		  return number_format($input/1000000,1,"$yuzo_options->format_count","") . "M";
	}else{
		return number_format($input/1000,1,"$yuzo_options->format_count","") . "k";
	}
  
}



### Function: Calculate Post Views With WP_CACHE Enabled
function wp_yuzo_postview_cache_count_enqueue() {

  global $post;
  
  //if( !defined( 'WP_CACHE' ) || !WP_CACHE )
	//return;
  
  if ( !wp_is_post_revision( $post ) && ( is_single() || is_page() ) ) {

	  wp_enqueue_script( 'wp-yuzo-postviews-cache', plugin_dir_url( __FILE__ ) . 'assets/js/yuzo-postviews-cache.js' , array( 'jquery' ), $this->parameter['version'] , true );
	  wp_localize_script( 'wp-yuzo-postviews-cache', 'viewsCacheL10n', array( 'admin_ajax_url' => admin_url( 'admin-ajax.php' ), 'post_id' => intval( $post->ID ), 'is_singular' => is_singular()?'1':''  ) );
	
  }
}






function taxomy_real(){

	global $yuzo_options,$wpdb;
	$ID_post = get_the_ID();
	$args = "SELECT term_taxonomy_id FROM {$wpdb->term_relationships} WHERE object_id = " . $ID_post;

	$term_taxonomy_ids = $wpdb->get_col( "$args" );
	if ( !$term_taxonomy_ids ) { return; }
	$term_taxonomy_ids_str = implode( ",", $term_taxonomy_ids );

	$object_ids = array();
	$object_ids = $wpdb->get_col( "SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ( {$term_taxonomy_ids_str} ) " );
	if ( !$object_ids ) { return; }

	$object_ids = array_count_values( $object_ids );

	arsort( $object_ids );

	$order_by = isset($yuzo_options->order_by_taxonomias)?$yuzo_options->order_by_taxonomias:'';
	$array_id_post_return = null;
	if ( $order_by == "related_scores_high__speedy" ) {
	$count = 1;
	foreach ( $object_ids as $object_id => $relevancy_score ) {
		$related_post = $wpdb->get_row( "SELECT ID FROM {$wpdb->posts} WHERE ID = {$object_id} AND post_status = 'publish'" );
		if ( $related_post ) {
			$array_id_post_return[] = $related_post->ID;
			if ( $count++ >= ( (int)$yuzo_options->display_post + 1 ) ) {
				break;
			}
		}
		}
	} else {
	$relevancy_scores = array();
	$post_ids = array();
	$post_date = array();
	$post_modified = array();
	foreach ( $object_ids as $object_id => $relevancy_score ) {
	  $related_post = $wpdb->get_row( "SELECT ID, post_date, post_modified FROM {$wpdb->posts} WHERE ID = {$object_id} AND  post_status = 'publish'" );
	  if ( $related_post ) {
		array_push( $relevancy_scores, $relevancy_score );
		array_push( $post_ids, $related_post->ID );
		array_push( $post_date, $related_post->post_date );
		array_push( $post_modified, $related_post->post_modified );
	  }
	}
	if ( $post_ids ) {  
	  if ( $order_by == "related_scores_high__date_published_old" ){
		array_multisort( $relevancy_scores, SORT_DESC, $post_date, SORT_ASC, $post_ids, SORT_ASC, $post_modified, SORT_ASC );
	  } elseif ( $order_by == "related_scores_low__date_published_new" ) {
		array_multisort( $relevancy_scores, SORT_ASC, $post_date, SORT_DESC, $post_ids, SORT_DESC, $post_modified, SORT_DESC );
	  } elseif ( $order_by == "related_scores_low__date_published_old" ) {
		array_multisort( $relevancy_scores, SORT_ASC, $post_date, SORT_ASC, $post_ids, SORT_ASC, $post_modified, SORT_ASC );
	  } elseif ( $order_by == "related_scores_high__date_modified_new" ) {
		array_multisort( $relevancy_scores, SORT_DESC, $post_modified, SORT_DESC, $post_date, SORT_DESC, $post_ids, SORT_DESC );
	  } elseif ( $order_by == "related_scores_high__date_modified_old" ) {
		array_multisort( $relevancy_scores, SORT_DESC, $post_modified, SORT_ASC, $post_date, SORT_ASC, $post_ids, SORT_ASC );
	  } elseif ( $order_by == "related_scores_low__date_modified_new" ) {
		array_multisort( $relevancy_scores, SORT_ASC, $post_modified, SORT_DESC, $post_date, SORT_DESC, $post_ids, SORT_DESC );
	  } elseif ( $order_by == "related_scores_low__date_modified_old" ) {
		array_multisort( $relevancy_scores, SORT_ASC, $post_modified, SORT_ASC, $post_date, SORT_ASC, $post_ids, SORT_ASC );
	  } elseif ( $order_by == "date_published_new" ) {
		array_multisort( $post_date, SORT_DESC, $post_ids, SORT_DESC, $post_modified, SORT_DESC, $relevancy_scores, SORT_DESC );
	  } elseif ( $order_by == "date_published_old" ) {
		array_multisort( $post_date, SORT_ASC, $post_ids, SORT_ASC, $post_modified, SORT_ASC, $relevancy_scores, SORT_DESC );
	  } elseif ( $order_by == "date_modified_new" ) {
		array_multisort( $post_modified, SORT_DESC, $post_date, SORT_DESC, $post_ids, SORT_DESC, $relevancy_scores, SORT_DESC );
	  } elseif ( $order_by == "date_modified_old" ) {
		array_multisort( $post_modified, SORT_ASC, $post_date, SORT_ASC, $post_ids, SORT_ASC, $relevancy_scores, SORT_DESC );
	  } else {
		array_multisort( $relevancy_scores, SORT_DESC, $post_date, SORT_DESC, $post_ids, SORT_DESC, $post_modified, SORT_DESC );
	  }
	  $count = 1;
	  foreach ( $post_ids as $key => $post_id ) {
	  	if( $ID_post != $post_id ){
	  		$array_id_post_return[] = (int)$post_id;
			if ( $count++ >= ((int)$yuzo_options->display_post + 3 ) ) {
			  break;
			}	
	  	}
		
	  }
	}
	}


  return $array_id_post_return;
}


/**
 * WordPress Popular Posts template tags for use in themes.
 * ( This function is copied from the original plugin to take the hit counter based on this plugin. )
 */
/**
 * Template tag - gets views count.
 *
 * @since   2.0.3
 * @global  object  wpdb
 * @param   int     id
 * @return  string
 */
function wpp_get_views($id = NULL) {

	global $wpdb;
	$table_name = $wpdb->prefix . "popularposts";

	$query = "SELECT pageviews FROM {$table_name}data WHERE postid = '{$id}'";
	$result = $wpdb->get_var($query);

	if ( !$result ) {
		return "0";
	}

	return $result;
}


function yuzo_get_views($id = NULL) {

	global $wpdb;
	$table_name = $wpdb->prefix . "yuzoviews";

	$query = "SELECT views FROM {$table_name} WHERE post_id = $id";
	$result = $wpdb->get_var($query);

	if ( !$result ) {
		return "0";
	}

	return $result;
}



function add_labes_hits_tablelist() {
	global $post_type,$yuzo_options;
	$labels_counter = null;

	if( isset($yuzo_options->disabled_counter) && $yuzo_options->disabled_counter ) return;

	if( $post_type == 'post' ){ 

		$labels_counter .= "<div class='yuzo_label_counter_wrap'> ";
			$labels_counter .= "<div  class='yuzo_label_counter_item top-tip' data-tips='< 1000'> <i class='el el-fire color_flare_normal'></i></div>";
			$labels_counter .= "<div  class='yuzo_label_counter_item top-tip' data-tips='>= 1000 < 10000'><i class='el el-fire color_flare_hot'></i></div>";
			$labels_counter .= "<div  class='yuzo_label_counter_item top-tip' data-tips='>= 10000 < 25000'><i class='el el-fire color_flare_hot2'></i></div>";
			$labels_counter .= "<div  class='yuzo_label_counter_item top-tip' data-tips='>= 25000 < 50000'><i class='el el-fire color_flare_hot3'></i></div>";
			$labels_counter .= "<div  class='yuzo_label_counter_item top-tip' data-tips='>= 50000'><i class='el el-fire color_flare_hot4'></i></div>";
		$labels_counter .= "</div>";

	?>

		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('.tablenav.bottom')
				.children(".bulkactions")
				.append("<?php echo $labels_counter; ?>");
 
			$('#yuzo_post_views-hide').on('change',function () {

				if ($(this).is(':checked') == true) {
					$(".yuzo_label_counter_wrap").css("display","inline-block");
				}else{
					$(".yuzo_label_counter_wrap").css("display","none");
				}

			});

			if ($('#yuzo_post_views-hide').is(':checked') == true) {
				$(".yuzo_label_counter_wrap").css("display","inline-block");
			}else{
				$(".yuzo_label_counter_wrap").css("display","none");
			}
		});
		</script>

	<?php }

}



/**
* @see http://codex.wordpress.org/Creating_Tables_with_Plugins
*/
function yuzo_install_db(){

	global $wpdb;
	global $ivl_db_version;

	$ivl_db_version = $this->parameter["db_version"];
	$table_name     = $wpdb->prefix . 'yuzoviews';
	$prefix         = $wpdb->prefix;
	$installed_ver  = get_option( $this->parameter["name_option"].'_db_version' );
	$add_table_db   = false;

	if ( $installed_ver != $ivl_db_version ) {

		/*
		 * We'll set the default character set and collation for this table.
		 * If we don't do this, some characters could end up being converted 
		 * to just ?'s when saved in our table.
		 */

		$charset_collate = '';

		if ( ! empty( $wpdb->charset ) ) {
		  $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		}

		if ( ! empty( $wpdb->collate ) ) {
		  $charset_collate .= " COLLATE {$wpdb->collate}";
		}

		$sql = "CREATE TABLE $table_name (
					ID int(11) NOT NULL AUTO_INCREMENT,
					post_id int(15) NOT NULL,
					views int(14) NOT NULL,
					last_viewed datetime NOT NULL,
					modified int(12) NULL,
				UNIQUE KEY ID (ID)
		) $charset_collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		@dbDelta( $sql );

		if( ! $installed_ver ){
			add_option( $this->parameter["name_option"].'_db_version', $ivl_db_version );
		}else{
			update_option( $this->parameter["name_option"].'_db_version', $ivl_db_version );
		}

		$add_table_db = true;
	}


	if( $add_table_db ){

		// Validate if exitis metadata 'yuzo_views' (custom fields) [ONLY OLD VERSION < 4.0]
		$sql_validate = "SELECT COUNT( * ) total
						FROM {$prefix}posts AS wposts
						WHERE post_type =  'post'
						AND EXISTS (

						SELECT * 
						FROM {$prefix}postmeta
						WHERE wposts.ID = post_id
						AND meta_key =  'yuzo_views'
						)
						ORDER BY post_date DESC";


		if( $wpdb->get_row(  $sql_validate )->total > 0 ){

			$remove_meta = 0;

			//$args = array( 'fields' => 'ids' );

			$sql_fetch = "SELECT ID
						FROM {$prefix}posts AS wposts
						WHERE post_type =  'post'
						AND EXISTS (

						SELECT * 
						FROM {$prefix}postmeta
						WHERE wposts.ID = post_id
						AND meta_key =  'yuzo_views'
						)
						ORDER BY post_date DESC";

			$all_post_ids = $wpdb->get_results($sql_fetch);

			//$all_post_ids = get_posts( $args );

			if( $all_post_ids ){
				foreach ( $all_post_ids as $post_id ) {

					$count = null;

					if( ! $wpdb->get_row( "select 1 from $table_name WHERE post_id = $post_id->ID" ) ){

						if( $count = get_post_meta( $post_id->ID, 'yuzo_views' , true) ){

							if( @$wpdb->query("insert into $table_name values(0,$post_id->ID,$count,'".date("Y-m-d H:m:i")."',".time().")" ) ){
								$remove_meta = 1;
							}

						}

					}

				}
			}

			//$meta_type  = 'post';
			//$user_id    = 0; // This will be ignored, since we are deleting for all users.
			//$meta_key   = 'yuzo_views';
			//$meta_value = ''; // Also ignored. The meta will be deleted regardless of value.
			//$delete_all = true;
			//delete_metadata( $meta_type, $user_id, $meta_key, $meta_value, $delete_all );
			/*if($remove_meta){
				delete_post_meta_by_key( 'yuzo_views' );
			}*/

		}
	}


	


}










function yuzo_menu_welcome(){

	// Welcome Page
	add_submenu_page(null, __(''), __(''), 'manage_options', 'yuzo-welcome', array(&$this, 'generateWelcomePage'));

}

function generateWelcomePage() { 
	require_once YUZO_PATH.'assets/pages/welcome.php'; // include our welcome page
} 

function yuzo_redirect_welcome( $plugin ) {

	$present_version  = get_option( $this->parameter["name_option"].'_present_version' );
	if( isset($this->parameter["present_version"]) && $plugin == plugin_basename( __FILE__ ) && $present_version != $this->parameter["present_version"] ) {

		update_option( $this->parameter["name_option"].'_present_version' , $this->parameter["present_version"] );
		update_option( $this->parameter["name_option"].'_date_yuzo' , time() );
		exit( wp_redirect( admin_url( 'options-general.php?page=yuzo-welcome&install_data=true' ) ) );

	}

}





function show_popup_message(){

	global $wpdb,$YUZO_CORE,$first_present1,$first_present2;
  
	/*var_dump($first_present1);
	var_dump($first_present2);*/
	if( $first_present1 == true || (isset($_GET["page"]) && $_GET["page"]  != "yuzo-related-post") ){ return; }
	$version_new     = $this->parameter["popup_version"]['id'];
	$version_current = get_option( $this->parameter["name_option"].'_popup_message' );
	update_option( $this->parameter["name_option"].'_popup_message' , $version_new  );
	/*var_dump($version_new);
	var_dump($version_current);
	var_dump($version_new != $version_current &&  isset($_GET["page"]) && $_GET["page"] == "yuzo-related-post" &&  $first_present2 == true);*/
	if ( $version_new != $version_current &&  isset($_GET["page"]) && $_GET["page"] == "yuzo-related-post" &&  $first_present2 == true ) {

		echo $this->parameter["popup_version"]['html'];
		echo '<script>
jQuery(document).ready( function($) {
	 jQuery( jQuery(".IF_popup_message") ).detach().appendTo( ".ilenplugin-options" );
	 setTimeout(function(){  
		 document.getElementById("IF_popup_button_active").click();
	     jQuery(".time_countdown").IF_countDown({
	        startNumber: '.$this->parameter["popup_version"]['second_close'].',
	        callBack: function(me) {
	            //jQuery(me).text("All done! This is where you give the reward!").css("color", "#090");
	            jQuery(me).parent().find(".close").css("display","block");
	            jQuery(me).remove();
	        }
	    });
		}, 1500);
});
</script>';

		//update_option( $this->parameter["name_option"].'_popup_message' , $version_new );
	}

	// 
}





function add_custom_css(){

	global $yuzo_options;

	if( $yuzo_options->style == 1 || $yuzo_options->style == 2 ){

		if( isset($yuzo_options->css_and_style) && $yuzo_options->css_and_style && isset($yuzo_options->theme) && $yuzo_options->theme == 'default' ){
			echo ("<style scoped>{$yuzo_options->css_and_style}</style>");
		}elseif( isset($yuzo_options->theme) && $yuzo_options->theme == 'magazine-alfa' ){
			echo addslashes("<style>.yuzo_wraps{
   box-shadow: 0px 0px 8px -2px #333; 
   border-radius: 3px;
   background: #ffffff;
   padding: 10px;
   background: -moz-linear-gradient(top, #ffffff 1%, #ffffff 27%, #e8e8e8 100%);
   background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,#ffffff), color-stop(27%,#ffffff), color-stop(100%,#e8e8e8));
   background: -webkit-linear-gradient(top, #ffffff 1%,#ffffff 27%,#e8e8e8 100%);
   background: -o-linear-gradient(top, #ffffff 1%,#ffffff 27%,#e8e8e8 100%);
   background: -ms-linear-gradient(top, #ffffff 1%,#ffffff 27%,#e8e8e8 100%);
   background: linear-gradient(to bottom, #ffffff 1%,#ffffff 27%,#e8e8e8 100%);
   filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e8e8e8',GradientType=0 );
   height: auto!important;
   float: left;
   width: 98%;
   margin-left: 1%;
   box-sizing: border-box;
   -moz-box-sizing: border-box;
   -webkit-box-sizing: border-box;
   -o-box-sizing: border-box;
   -ms-box-sizing: border-box;
 }
 a.yuzo__text--title,
 .yuzo__text--title,
 .yuzo-list a.yuzo__text--title{
   font-weight:bolder;
   color:#000!important;
 }
 .yuzo__title::before{
    content: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAMAAADXqc3KAAAAA3NCSVQICAjb4U/gAAAACXBIWXMAAAphAAAKYQH8zEolAAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAAAEtQTFRF////AAAAAAAAAAAAAAAAAAAAAAAEAAADAAADAAACAAACAgACAgACAgACAgACAQABAQADAQADAQADAQACAQACAQACAQACAQACAQACjwA2xwAAABh0Uk5TAAIEIDA/QFFkeX2Ago2dvcDBwtDZ4OT+yEuE8AAAAHNJREFUKFOtkVkOgCAMREfFFRdc4f4ntWjUSG2Mie+PvswQKPAZ84QXbmbj2W1CsxItC99GIrtDgvDC3bmEmNjPCuq5ysCEVYcQE39XJRCqNIR3nAlbs0+saG4x9lEoUhITCreEixpINEDeshWarozZBa+sC18XgoSOCdYAAAAASUVORK5CYII71a91eb3cbaabc2cd8b11cc616e0253d);
    width: 32px;
    height: 32px;
    display: inline-block;
    position: relative;
    top: 6px;
    opacity: 0.6;
 }
 .yuzo__title h3,.yuzo__title{
   display: inline-block;
 }</style>");
		}
		

	}

}


function yuzo_shortcode( $atts, $content = null ){

	global $post;
 
	extract(shortcode_atts(array(
		'id'    => $post->ID
	), $atts));

	if( isset($id) && $id ){

		return '<span class="yuzo-view-shortcode">'.self::yuzo_get_PostViews( $id ).'</span>';

	}

}


function yuzo_shortcode_related( $atts, $content = null ){

	return self::create_post_related( $content );

}



function only_specific_post(){

	global $yuzo_options,$post;

	$only_post = array();
	// validate if tags exclude custom
	if( isset($yuzo_options->only_in_post) && $yuzo_options->only_in_post ){

		//  verify
		$only_post = explode(",",$yuzo_options->only_in_post);

		if( in_array( $post->ID, $only_post  ) ){
			return true;
		}else{
			return false;
		}

	}else{
		return true;
	}



}


function delete_transient_for_save(){

	global $wpdb;

	$wpdb->query(  "DELETE FROM $wpdb->options WHERE option_name LIKE '%\_transient_yuzo_view\_%'" );
	$wpdb->query(  "DELETE FROM $wpdb->options WHERE option_name LIKE '%\_transient_yuzo_widget\_%'" );
	$wpdb->query(  "DELETE FROM $wpdb->options WHERE option_name LIKE '%\_transient_yuzo_query\_%'" );
	$wpdb->query(  "DELETE FROM $wpdb->options WHERE option_name LIKE '%\_transient_timeout_yuzo_view\_%'" );
	$wpdb->query(  "DELETE FROM $wpdb->options WHERE option_name LIKE '%\_transient_timeout_yuzo_widget\_%'" );
	$wpdb->query(  "DELETE FROM $wpdb->options WHERE option_name LIKE '%\_transient_timeout_yuzo_query\_%'" );
}




} // end class
} // end if

 
global $IF_CONFIG, $yuzo_options, $YUZO_CORE;
unset($IF_CONFIG);
$IF_CONFIG = null;
$IF_CONFIG = $YUZO_CORE = new yuzo_related_post;
require_once "assets/ilenframework/core.php";



if( isset($yuzo_options->active_widget) && $yuzo_options->active_widget ){
	require_once "assets/functions/widget.php";
}


if( !isset( $yuzo_options->disabled_metabox ) || !$yuzo_options->disabled_metabox ){
	require_once "assets/functions/metabox.php";    
}


function get_yuzo_related_posts($content=""){
  
  global $IF_CONFIG;

  echo $IF_CONFIG->create_post_related($content);
  
}



function get_Yuzo_Views(){

  global $IF_CONFIG;

  if( isset($IF_CONFIG->plugin_options->meta_views) && $IF_CONFIG->plugin_options->meta_views ){

	// get count
	if( $IF_CONFIG->plugin_options->meta_views == 'yuzo-views' ){

	  $counts = $IF_CONFIG->yuzo_get_PostViews( get_the_ID() );

	}elseif( $IF_CONFIG->plugin_options->meta_views == 'other' ){

	  $meta_views_custom = isset( $IF_CONFIG->plugin_options->meta_views_custom )?$IF_CONFIG->plugin_options->meta_views_custom:'';
	  $counts = $IF_CONFIG->yuzo_get_PostViews( get_the_ID(), $meta_views_custom  );

	}
  }

  return (int)($counts);

}



function yuzo_redirect_welcome_upgrade() {
	//echo 11;
	global $IF_CONFIG,$first_present1,$first_present2;
	$first_present1 = false;
	$first_present2 = false;
	$present_version  = get_option($IF_CONFIG->parameter["name_option"].'_present_version' );

	if( ! get_option($IF_CONFIG->parameter["name_option"].'_date_yuzo' ) ){
		update_option( $IF_CONFIG->parameter["name_option"].'_date_yuzo' , time() );		
	}

	if( isset($IF_CONFIG->parameter["present_version"]) && $present_version != $IF_CONFIG->parameter["present_version"] ) {
		$first_present1 = true;
		update_option( $IF_CONFIG->parameter["name_option"].'_present_version' , $IF_CONFIG->parameter["present_version"] );
		//sleep(5);
		exit( wp_redirect( admin_url( 'options-general.php?page=yuzo-welcome&tab=new&install_data=true' ) ) );
	}elseif( $present_version == $IF_CONFIG->parameter["present_version"] ){
		$first_present2 = true;
	}

	//return true;

}



/* AJAX DELETE METADATA AND TRANSIENT */
function add_ajax_javascript_file(){
	wp_enqueue_script( 'yuzo_ajax_custom_script_admin', plugin_dir_url( __FILE__ ) . 'assets/js/admin-ajax.js', array('jquery') );
}


function ajax_delete_yuzo_data_admin() {

	global $wpdb;

    if( !empty($_POST['yuzo_actions']) && $_POST['yuzo_actions'] == "meta" ){

		$meta_type  = 'post';
		$user_id    = 0; // This will be ignored, since we are deleting for all users.
		$meta_key   = 'yuzo_views';
		$meta_value = ''; // Also ignored. The meta will be deleted regardless of value.
		$delete_all = true;
		//delete_metadata( $meta_type, $user_id, $meta_key, $meta_value, $delete_all );
		//if($remove_meta){
		delete_post_meta_by_key( 'yuzo_views' );
        
    }elseif( !empty($_POST['yuzo_actions']) && $_POST['yuzo_actions'] == "transient"  ){

		$wpdb->query(  "DELETE FROM $wpdb->options WHERE option_name LIKE '%\_transient_yuzo_view\_%'" );
		$wpdb->query(  "DELETE FROM $wpdb->options WHERE option_name LIKE '%\_transient_yuzo_widget\_%'" );
		$wpdb->query(  "DELETE FROM $wpdb->options WHERE option_name LIKE '%\_transient_yuzo_query\_%'" );
		$wpdb->query(  "DELETE FROM $wpdb->options WHERE option_name LIKE '%\_transient_timeout_yuzo_view\_%'" );
		$wpdb->query(  "DELETE FROM $wpdb->options WHERE option_name LIKE '%\_transient_timeout_yuzo_widget\_%'" );
		$wpdb->query(  "DELETE FROM $wpdb->options WHERE option_name LIKE '%\_transient_timeout_yuzo_query\_%'" );

    }

    die();
}
 


if( is_admin() ){
	//if( isset($_GET["page"]) && $_GET["page"] == "yuzo-related-post"  ){
		yuzo_redirect_welcome_upgrade();
	//}
}


?>