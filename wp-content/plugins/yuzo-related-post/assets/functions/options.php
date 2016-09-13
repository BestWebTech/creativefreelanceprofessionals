<?php
/**
 * Options Plugin
 * Make configutarion
*/

if ( !class_exists('yuzo_related_post_make') ) {

class yuzo_related_post_make extends IF_utils{

		public $parameter       = array();
		public $options         = array();
		public $components      = array();
		public $utils           = null;

	function getHeaderPlugin(){

		//code 
		return array(            'id'             =>'yuzo_related_post_id',
								 'id_menu'        =>'yuzo-related-post',
								 'name'           =>'Yuzo - Related Posts',
								 'name_long'      =>'Yuzo - Related Posts',
								 'name_option'    =>'yuzo_related_post',
								 'name_plugin_url'=>'yuzo-related-post',
								 'descripcion'    =>'Gets the related post on your blog with any design characteristics.',

								 'version'        =>'5.12.51',
								 'db_version'     =>'1.4',
								 'present_version'=>'1.6', // html
								 'popup_version'  =>array('id'=>'0.155','second_close'=>'6','html'=>'<div class="IF_popup_message"  > <div id="message" class="overlay"> <div class="popup"> <div class="header_popup" > <span class="time_countdown"></span> <h2>News</h2> <a class="close" href="#">&times;</a> </div><div class="featured_image"> <span class="ilen_shine" style="display:inline-block;width:772px;height:327px;"> <span class="shine-effect" style=""></span> <div style="background:url(http://i.imgur.com/RNI4iBd.jpg) no-repeat;max-width:100%;height:300px;background-position-y: -92px;background-size: cover;"></div> </span> </div><div class="content" style="margin-top:343px;"> <p style="text-align:center;margin-top: 3px;"><a target="_blank" href="http://support.ilentheme.com/forums/topic/yuzo-pro-before-long/" class="ibtn"><span><i class="el el-fire"></i></span>In a few days  will be released Yuzo PRO, You are ready?</a></p> </div></div><!--<h1>Popup/Modal Windows without JavaScript</h1> <div class="box"> <a class="button" href="#message">Let me Pop up</a> </div>--> <a class="button" id="IF_popup_button_active" href="#message" style="display:none">Let me Pop up</a><style>.IF_popup_message .ilen_shine .shine-effect{width: 145%!important;}.IF_popup_message .ilen_shine:hover .shine-effect{opacity: 1; top: -100%; left: -30%; height: 300%;}  .IF_popup_message  .popup .ibtn{ background: #0D94E2;background: #0D94E2;color: #fff!important;text-shadow:0px 1px 0px #1f1ce2; } .IF_popup_message  .popup .ibtn span{ background: #0D94E2;color: #60c8ff;!important }  .IF_popup_message .overlay:target .popup {top: 50%;opacity: 1;position: absolute;left: 50%;margin-top: -216px;margin-left: -386px;}</style><div>'),
								 
								 'url'            =>'',
								 'logo'           =>'<i class="el el-fire"></i>', // or image .jpg,png
								 'logo_text'      =>'', // alt of image
								 'slogan'         =>'', // powered by <a href="">iLenTheme</a>
								 'url_framework'  =>plugins_url()."/yuzo-related-post/assets/ilenframework",
								 'theme_imagen'   =>plugins_url()."/yuzo-related-post/assets/images",
								 'languages'      =>plugins_url()."/yuzo-related-post/assets/languages",
								 'default_image'  =>plugins_url()."/yuzo-related-post/assets/ilenframework/assets/images/default.png",
								 //'twitter'        => 'https://twitter.com/intent/tweet?text=View this awesome plugin WP;url=http://bit.ly/1rLUvBM&amp;via=iLenElFuerte',
								 'twitter'        =>'',
								 'wp_review'      =>'http://wordpress.org/support/view/plugin-reviews/yuzo-related-post?filter=5',
								 'wp_support'     =>'http://support.ilentheme.com/forums/forum/plugins/yuzo-related-post/',
								 'link_donate'    =>'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=MSRAUBMB5BZFU',
								 'type'           =>'plugin',
								 'method'         =>'free',
								 'themeadmin'     =>'fresh',
								 'metabox'        =>1,
								 'scripts_admin'  =>array('page'        => array('yuzo-related-post' => array('nouislider','date','jquery_ui_reset','list_categories','tag','enhancing_code')),
														  //'edit.php'    => array('select2','date','range2'),
														  //'post.php'    => array('select2','date','range2'),
														  'post_type'   => array('page' => array('select2','jquery_ui_reset'),
																				 'post' => array('select2','jquery_ui_reset')),
														  'widgets'     => array('select2','nouislider','jtumbler','tag','check_list'),
														)
					);
	}

	function getOptionsPlugin(){

		global $if_utils;
		//$this->yuzo_parameter = $this->parameter;
		global ${'tabs_plugin_' . $this->parameter['name_option']};


		// get options
		$yuzo_options_temp = $if_utils->IF_get_option( $this->parameter['name_option'] );
		if(!$yuzo_options_temp){
			$yuzo_options_temp = new stdClass;
			$yuzo_options_temp->css_and_style = null;
		}


		${'tabs_plugin_' . $this->parameter['name_option']} = array();
		${'tabs_plugin_' . $this->parameter['name_option']}['tab01']=array('id'=>'tab01','name'=>'Main Settings','icon'=>'<i class="fa fa-circle-o"></i>','width'=>'200'); 
		${'tabs_plugin_' . $this->parameter['name_option']}['tab02']=array('id'=>'tab02','name'=>'Styling','icon'=>'<i class="fa fa-pencil"></i>','width'=>'200'); // ,'fix'=>1
		${'tabs_plugin_' . $this->parameter['name_option']}['tab03']=array('id'=>'tab03','name'=>'Productivity','icon'=>'<i class="fa fa-eye"></i>','width'=>'200'); 
		

		// get category for exclude
		/*$categories = '';
		$categories = get_categories();
		$categories_array = array();
		if($categories){
			foreach ($categories as $cats_key => $cats_value) {
				if($cats_value && is_object($cats_value->cat_ID) && is_object($cats_value->name)) {
					$categories_array[]=array('value'=>$cats_value->cat_ID,'id'=>$this->parameter['name_option'].'_exca','text'=>$cats_value->name,'help'=>'');
				}
			}
		}*/

		return array('a'=>array(                'title'      => __('Basic',$this->parameter['name_option']), 
												'title_large'=> __('Basic',$this->parameter['name_option']), 
												'description'=> '', 
												'icon'       => 'fa fa-circle-o',
												'tab'        => 'tab01',

												'options'    => array(  
																		 
																		array(  'title' =>__('Top Text',$this->parameter['name_option']),
																				'help'  =>__('Title top of related',$this->parameter['name_option']),
																				'type'  =>'text',
																				'value' =>'<h3>Related Post</h3>',
																				'id'    =>$this->parameter['name_option'].'_'.'top_text',
																				'name'  =>$this->parameter['name_option'].'_'.'top_text',
																				'class' =>'',
																				'row'   =>array('a','b')),


																		array(  'title' =>__('Number of  similar Post to display',$this->parameter['name_option']),
																				'help'  =>__('Number Post',$this->parameter['name_option']),
																				'type'  =>'select',
																				'value' =>4,
																				'items' =>array(1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',6=>'6',7=>'7',8=>'8',9=>'9',10=>'10',11=>'11',12=>'12',13=>'13',14=>'14',15=>'15',16=>'16',17=>'17',18=>'18',19=>'19',20=>'20',25=>'25',30=>'30',35=>'35',40=>'40',45=>'45',50=>'50'),
																				'id'    =>$this->parameter['name_option'].'_'.'display_post',
																				'name'  =>$this->parameter['name_option'].'_'.'display_post',
																				'class' =>'',
																				'row'   =>array('a','b')),
																		),
					),
					'b'=>array(                'title'      => __('Custom styling',$this->parameter['name_option']), 
											   'title_large'=> __('',$this->parameter['name_option']), 
											   'description'=> '',  
											   'icon'       => '',
											   'tab'        => 'tab02',

												'options'    => array( 

																		array(  'title' =>__('Choose your style',$this->parameter['name_option']),
																				'help'  =>'Yuzo shows you 4 different styles to show related posts.',
																				'type'  =>'radio_image',
																				'value' =>1,
																				'items' =>array(    

																									array('value'=>2,
																										  'text' =>'Vertical',
																										  'image'=>$this->parameter['theme_imagen'].'/2-.png'),

																									array('value'=>1,
																										  'text' =>'Horizontal',
																										  'image'=>$this->parameter['theme_imagen'].'/1-.png'),

																									array('value'=>3,
																										  'text' =>'List',
																										  'image'=>$this->parameter['theme_imagen'].'/3-.png'),

																									array('value'=>4,
																										  'text' =>'Experimental',
																										  'image'=>$this->parameter['theme_imagen'].'/4-.png'),
	 
																								),

																				'id'    =>$this->parameter['name_option'].'_'.'style',
																				'name'  =>$this->parameter['name_option'].'_'.'style',
																				'class' =>'yuzo_style_chosse',
																				'row'   =>array('a','c')),


																		array(  'title' =>__('Thumbnail size',$this->parameter['name_option']),
																				'help'  =>__('Image size',$this->parameter['name_option']),
																				'type'  =>'select',
																				'value' =>'thumbnail',
																				'items' =>array('thumbnail'=>'Thumbnail','medium'=>'Medium','full'=>'Full'),
																				'id'    =>$this->parameter['name_option'].'_'.'thumbnail_size',
																				'name'  =>$this->parameter['name_option'].'_'.'thumbnail_size',
																				'class' =>'',
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Background size',$this->parameter['name_option']),
																				'help'  =>__('<code>Cover:</code> The recommended since adjusts the picture for all related post are well aligned and looks exactly.<br /><br /> <code>Contain:</code>Fits both high and wide to display the image completely, but if the radius ratio is more high than the width of the other related post is not displayed aligned with respect.',$this->parameter['name_option']),
																				'type'  =>'select',
																				'value' =>'cover',
																				'items' =>array('cover'=>'Cover','contain'=>'Contain'),
																				'id'    =>$this->parameter['name_option'].'_'.'background_size',
																				'name'  =>$this->parameter['name_option'].'_'.'background_size',
																				'class' =>'',
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Height & Width image',$this->parameter['name_option']),
																				'help'  =>__('in px',$this->parameter['name_option']),
																				'type'  =>'text',
																				'value' =>'110',
																				'id'    =>$this->parameter['name_option'].'_'.'height_image',
																				'name'  =>$this->parameter['name_option'].'_'.'height_image',
																				'class' =>'',
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Image type',$this->parameter['name_option']),
																				'help'  =>__('You can choose from rectangle and square as your way of seeing',$this->parameter['name_option']),
																				'type'  =>'select',
																				'value' =>'rectangular',
																				'items' =>array('rectangular'=>'Rectangle ','square'=>'Square','full-rectangular'=>'Full Rectangle','full-vertical'=>'Full Vertical'),
																				'id'    =>$this->parameter['name_option'].'_'.'type_image',
																				'name'  =>$this->parameter['name_option'].'_'.'type_image',
																				'class' =>'',
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Specify the Height',$this->parameter['name_option']),
																				'help'  =>__('in px',$this->parameter['name_option']),
																				'type'  =>'text',
																				'value' =>'',
																				'id'    =>$this->parameter['name_option'].'_'.'height_full',
																				'name'  =>$this->parameter['name_option'].'_'.'height_full',
																				'class' =>'_class_height_of_full_vertical',
																				'style' =>'background:#FFFFCA;',
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Image order',$this->parameter['name_option']),
																				'help'  =>__('If you have multiple attachment images you can use this option to display the first or last image placed in your post.',$this->parameter['name_option']),
																				'type'  =>'select',
																				'value' =>'DESC',
																				'items' =>array('DESC'=>'Last image','ASC'=>'First image'),
																				'id'    =>$this->parameter['name_option'].'_'.'image_order',
																				'name'  =>$this->parameter['name_option'].'_'.'image_order',
																				'class' =>'',
																				'row'   =>array('a','b')),
	 
																		array(  'title' =>__('Background Color',$this->parameter['name_option']),
																				'help'  =>'Selected background color and mouse hover, you can also leave blank', 
																				'type'  =>'color_hover',
																				'value' =>array('color'=>'','hover'=>'#fcfcf4'),
																				'id'    =>$this->parameter['name_option'].'_'.'bg_color',
																				'name'  =>$this->parameter['name_option'].'_'.'bg_color', 
																				'class' =>'', 
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Hover transitions',$this->parameter['name_option']),
																				'help'  =>__('Effect transitions background when mouse over in related<br />You can enter values such as: 0.2, 0.5 , 0.8, 1, 1.5 , 2,3, etc..',$this->parameter['name_option']),
																				'type'  =>'text',
																				'value' =>'0.2',
																				'id'    =>$this->parameter['name_option'].'_'.'bg_color_hover_transitions',
																				'name'  =>$this->parameter['name_option'].'_'.'bg_color_hover_transitions',
																				'class' =>'',
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Thumbnail Border Radio',$this->parameter['name_option']),
																				'help'  =>__('Select the percentage of border for the image (%)<br/> 0=square - 50=circle',$this->parameter['name_option']),
																				'type'  =>'range2',
																				'value' =>'',
																				'id'  =>$this->parameter['name_option']. '_'. 'thumbnail_border_radius',
																				'name'  =>$this->parameter['name_option']. '_'. 'thumbnail_border_radius',
																				'min' =>0,
																				'max' =>50,
																				'step'  =>1,
																				'value' =>0,
																				'class' =>'',
																				'color' => 1,
																				'row' => array('a','b')),

																		array(  'title' =>__('Margin',$this->parameter['name_option']),
																				'help'  =>__('Place the related margin on each post',$this->parameter['name_option']),
																				'type'  =>'input_4',
																				'value' =>array('top'=>0,'right'=>0,'bottom'=>0,'left'=>0),
																				'id'  =>$this->parameter['name_option']. '_'. 'related_margin',
																				'name'  =>$this->parameter['name_option']. '_'. 'related_margin',
																				'min' =>0,
																				'max' =>50,
																				'step'  =>1,
																				'class' =>'',
																				'row' =>array('a','b')),

																		array(  'title'         =>__('Padding',$this->parameter['name_option']),
																				'help'          =>__('Place the related padding on each post',$this->parameter['name_option']),
																				'type'          =>'input_4',
																				'value'         =>array('top'=>5,'right'=>5,'bottom'=>5,'left'=>5),
																				'id'            =>$this->parameter['name_option']. '_'. 'related_padding',
																				'name'          =>$this->parameter['name_option']. '_'. 'related_padding',
																				'min'           =>0,
																				'max'           =>50,
																				'step'          =>1,
																				'class'         =>'',
																				'row'           =>array('a','b')),


																		array(  'title' =>__('Shadow Box:',$this->parameter['name_option']), //title section
																				'help'  =>'Adding a Shadow Box around each post related (Only vertical and horizontal style)',
																				'type'  =>'checkbox', //type input configuration
																				'value' =>'0', //default
																				'value_check'=>1, // value
																				'id'    =>$this->parameter['name_option'].'_'.'box_shadow', //id
																				'name'  =>$this->parameter['name_option'].'_'.'box_shadow', //name
																				'class' =>'', //class
																				'row'   =>array('a','b')),


																				

															),
												),
					'c'=>array(                'title'      => __('Medium',$this->parameter['name_option']), 
															   'title_large'=> __('',$this->parameter['name_option']), 
															   'description'=> '',  
															   'icon'       => '',
															   'tab'        => 'tab01',

																'options'    => array( 


																						 array(  'title' =>__('Post Type:',$this->parameter['name_option']), //title section
																								  'help'  =>__('Type related post where is displayed',$this->parameter['name_option']), //descripcion section
																								  'type'  =>'checkbox', 
																								  'value' =>array('post'),
																								  'value_check'=>array('post'),
																								  'display'   =>'types_post', 
																								  'id'    =>$this->parameter['name_option'].'_'.'post_type', //id
																								  'name'  =>$this->parameter['name_option'].'_'.'post_type', //name
																								  'class' =>'', //class
																								  'row'   =>array('a','b')),


																						  array(  'title' =>__('Show in Home:',$this->parameter['name_option']), //title section
																								  'help'  =>'Displays related post in the home? (only if template allows)',
																								  'type'  =>'checkbox', //type input configuration
																								  'value' =>'0', // default
																								  'value_check'=>1, // value data
																								  'id'    =>$this->parameter['name_option'].'_'.'show_home',  
																								  'name'  =>$this->parameter['name_option'].'_'.'show_home',  
																								  'class' =>'', //class
																								  'row'   =>array('a','b')),

																						array(  'title' =>__('No show in archives pages:',$this->parameter['name_option']), //title section
																								  'help'  =>'Displays related post in the category page, search page, author page, date page, etc... (only if template allows)',
																								  'type'  =>'checkbox', //type input configuration
																								  'value' =>'1', // default
																								  'value_check'=>1, // value data
																								  'id'    =>$this->parameter['name_option'].'_'.'no_show_archive_page',  
																								  'name'  =>$this->parameter['name_option'].'_'.'no_show_archive_page',  
																								  'class' =>'', //class
																								  'row'   =>array('a','b')),
					   
					   

																						  array(  'title' =>__('Default image URL',$this->parameter['name_option']),
																								  'help'  =>__('Default image in case there is no image in the post',$this->parameter['name_option']),
																								  'type'  =>'upload',
																								  'value' =>$this->parameter['theme_imagen'].'/default.png',
																								  'id'    =>$this->parameter['name_option'].'_'.'default_image',
																								  'name'  =>$this->parameter['name_option'].'_'.'default_image',
																								  'class' =>'',
																								  'row'   =>array('a','b')),

					   
																						   array(  'title' =>__('Related to',$this->parameter['name_option']),
																								  'help'  =>__('Related Post based',$this->parameter['name_option']),
																								  'type'  =>'select',
																								  'value' =>1,
																								  'items' =>array(1=>__('Tags',$this->parameter['name_option']),
																												  2=>__('Category',$this->parameter['name_option']),
																												  3=>__('Tags & Category',$this->parameter['name_option']),
																												  4=>__('Random',$this->parameter['name_option']),
																												  5=>__('Taxonomies',$this->parameter['name_option']),
																												  ),
																								  'id'    =>$this->parameter['name_option'].'_'.'related_to',
																								  'name'  =>$this->parameter['name_option'].'_'.'related_to',
																								  'class' =>'',
																								  'row'   =>array('a','b')),


																						  array(  'title' =>__('Order by',$this->parameter['name_option']),
																								  'help'  =>__('Multiple criteria systems',$this->parameter['name_option']),
																								  'type'  =>'select',
																								  'value' =>'rand',
																								  'items' =>array('none'          =>__('None',$this->parameter['name_option']),
																												  'ID'            =>__('ID',$this->parameter['name_option']),
																												  'author'        =>__('Author',$this->parameter['name_option']),
																												  'title'         =>__('Title',$this->parameter['name_option']),
																												  'name'          =>__('Name',$this->parameter['name_option']),
																												  'date'          =>__('Date',$this->parameter['name_option']),
																												  'modified'      =>__('Modified',$this->parameter['name_option']),
																												  'rand'          =>__('Rand',$this->parameter['name_option']),
																												  'comment_count' =>__('Comment Count',$this->parameter['name_option'])
																												),
																								  'id'    =>$this->parameter['name_option'].'_'.'order_by',
																								  'name'  =>$this->parameter['name_option'].'_'.'order_by',
																								  'class' =>'class_order_by',
																								  'row'   =>array('a','b')),


																						  array(  'title' =>__('Order',$this->parameter['name_option']),
																								  'help'  =>'',
																								  'type'  =>'select',
																								  'value' =>'DESC',
																								  'items' =>array('DESC'=>'Descendant','ASC'=>'Ascendant'),
																								  'id'    =>$this->parameter['name_option'].'_'.'order',
																								  'name'  =>$this->parameter['name_option'].'_'.'order',
																								  'class' =>'class_order',
																								  'row'   =>array('a','b')),

																						  array(  'title' =>__('Order by',$this->parameter['name_option']),
																								  'help'  =>__('By via taxonomies, this might be more relevant in the algorithm to search for related real.s',$this->parameter['name_option']),
																								  'type'  =>'select',
																								  'value' =>'',
																								  'items' =>array('related_scores_high__speedy'             =>__('Related Scores : High  ( Speedy )',$this->parameter['name_option']),
																												  ''                                        =>__('Related Scores : High  /  Date Published : New  ( Default setting )',$this->parameter['name_option']),
																												  'related_scores_high__date_published_old' =>__('Related Scores : High  /  Date Published : Old',$this->parameter['name_option']),
																												  'related_scores_low__date_published_new'  =>__('Related Scores : Low  /  Date Published : New',$this->parameter['name_option']),
																												  'related_scores_low__date_published_old'  =>__('Related Scores : Low  /  Date Published : Old',$this->parameter['name_option']),
																												  'related_scores_high__date_modified_new'  =>__('Related Scores : High  /  Date Modified : New',$this->parameter['name_option']),
																												  'related_scores_high__date_modified_old'  =>__('Related Scores : High  /  Date Modified : Old',$this->parameter['name_option']),
																												  'related_scores_low__date_modified_new'   =>__('Related Scores : Low  /  Date Modified : New',$this->parameter['name_option']),
																												  'related_scores_low__date_modified_old'   =>__('Related Scores : Low  /  Date Modified : Old',$this->parameter['name_option']),
																												  
																												  'date_published_new'                      =>__('Date Published : New',$this->parameter['name_option']),
																												  'date_published_old'                      =>__('Date Published : Old',$this->parameter['name_option']),
																												  'date_modified_new'                       =>__('Date Modified : New',$this->parameter['name_option']),
																												  'date_modified_old'                       =>__('Date Modified : Old',$this->parameter['name_option']),
																												),
																								  'id'    =>$this->parameter['name_option'].'_'.'order_by_taxonomias',
																								  'name'  =>$this->parameter['name_option'].'_'.'order_by_taxonomias',
																								  'class' =>'class_order_by_taxonomias',
																								  'row'   =>array('a','b')),

																							array(  'title' =>__('Show on feed:',$this->parameter['name_option']), //title section
																								  'help'  =>'Displays related post in the feed/rss',
																								  'type'  =>'checkbox', //type input configuration
																								  'value' =>'0', // default
																								  'value_check'=>1, // value data
																								  'id'    =>$this->parameter['name_option'].'_'.'show_feed',  
																								  'name'  =>$this->parameter['name_option'].'_'.'show_feed',  
																								  'class' =>'', //class
																								  'row'   =>array('a','b')),

																							array(  'title' =>__('Target link:',$this->parameter['name_option']), //title section
																								  'help'  =>'when clicking the related post open in a tab (target="_blank")',
																								  'type'  =>'checkbox', //type input configuration
																								  'value' =>'0', // default
																								  'value_check'=>1, // value data
																								  'id'    =>$this->parameter['name_option'].'_'.'target_link',  
																								  'name'  =>$this->parameter['name_option'].'_'.'target_link',  
																								  'class' =>'', //class
																								  'row'   =>array('a','b')),

																							array('title' =>__('rel=nofollow?:',$this->parameter['name_option']), //title section
																								  'help'  =>'If you enable this option yuzo related links will not be tracked by search engines.',
																								  'type'  =>'checkbox', //type input configuration
																								  'value' =>'0', // default
																								  'value_check'=>1, // value data
																								  'id'    =>$this->parameter['name_option'].'_'.'rel_nofollow',  
																								  'name'  =>$this->parameter['name_option'].'_'.'rel_nofollow',  
																								  'class' =>'', //class
																								  'row'   =>array('a','b')),
					 
																				)
															),
					'd'=>array(                'title'      => __('Advanced',$this->parameter['name_option']), 
											   'title_large'=> __('',$this->parameter['name_option']), 
											   'description'=> '',  
											   'icon'       => '',
											   'tab'        => 'tab01',

												'options'    => array( 


																		array(  'title' =>__('Automatically append to the post content:',$this->parameter['name_option']), //title section
																				'help'  =>'Or use <code>&lt;?php if ( function_exists( "get_yuzo_related_posts" ) ) { get_yuzo_related_posts(); } ?&gt;</code> in the Loop', //descripcion section
																				'type'  =>'checkbox', //type input configuration
																				'value' =>'1', //value
																				'value_check'=>1,
																				'id'    =>$this->parameter['name_option'].'_'.'automatically_append', //id
																				'name'  =>$this->parameter['name_option'].'_'.'automatically_append', //name
																				'class' =>'', //class
																				'row'   =>array('a','b')),

																		
																		array(  'title' =>__('Show ONLY Home:',$this->parameter['name_option']), //title section
																				'help'  =>'This option allows only appears on the home page related, will not be displayed anywhere else.',
																				'type'  =>'checkbox', 
																				'value' =>'0', // default
																				'value_check'=>1, // value data
																				'id'    =>$this->parameter['name_option'].'_'.'show_only_home',  
																				'name'  =>$this->parameter['name_option'].'_'.'show_only_home',  
																				'class' =>'', 
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Show only the same type:',$this->parameter['name_option']), //title section
																				'help'  =>'If you enable this option displays related posts Yuzo only the type of publication, example: If you are in a "type page" display related but only "page".',
																				'type'  =>'checkbox', 
																				'value' =>'0', // default
																				'value_check'=>1, // value data
																				'id'    =>$this->parameter['name_option'].'_'.'show_only_type',  
																				'name'  =>$this->parameter['name_option'].'_'.'show_only_type',  
																				'class' =>'', 
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Number of  similar Post to display for Home',$this->parameter['name_option']),
																				'help'  =>__('Number Post for Home',$this->parameter['name_option']),
																				'type'  =>'select',
																				'value' =>'4',
																				'items' =>array(1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',6=>'6',7=>'7',8=>'8',9=>'9',10=>'10',11=>'11',12=>'12',13=>'13',14=>'14',15=>'15',16=>'16',17=>'17',18=>'18',19=>'19',20=>'20',25=>'25',30=>'30',35=>'35',40=>'40',45=>'45',50=>'50'),
																				'id'    =>$this->parameter['name_option'].'_'.'display_post_home',
																				'name'  =>$this->parameter['name_option'].'_'.'display_post_home',
																				'class' =>'',
																				'row'   =>array('a','b')),


																		array(  'title' =>__('Categories on which related thumbnails will appear:',$this->parameter['name_option']), //title section
																				'help'  =>'',

																				'type'  =>'component_list_categories',
																				'text_first_select' => __('All',$this->parameter['name_option']),

																				'value' =>array('-1'),
																				'value_check'=>0,
																				'id'    =>$this->parameter['name_option'].'_'.'categories', //id
																				'name'  =>$this->parameter['name_option'].'_'.'categories', //name
																				'class' =>'',
																				'row'   =>array('a','b')),

																		
																		array(  'title' =>__('Exclude category:',$this->parameter['name_option']), //title section
																				'help'  =>'Select a category that does not want to be displayed as relationship in post.',

																				'type'  =>'component_list_categories',
																				'text_first_select' => __('None',$this->parameter['name_option']),

																				'value' =>array('-1'),
																				'value_check'=>0,
																				'id'    =>$this->parameter['name_option'].'_'.'exclude_category', //id
																				'name'  =>$this->parameter['name_option'].'_'.'exclude_category', //name
																				'class' =>'',
																				'row'   =>array('a','b')),


																		array(  'title' =>__('Excluding related categories:',$this->parameter['name_option']), //title section
																				'help'  =>'If you enable this option, the categories you chose will not be displayed and that they are related to it.',
																				'type'  =>'checkbox', 
																				'value' =>'1', // default
																				'value_check'=>1, // value data
																				'id'    =>$this->parameter['name_option'].'_'.'exclude_category_related',  
																				'name'  =>$this->parameter['name_option'].'_'.'exclude_category_related',  
																				'class' =>'IF_class_exclude_category_related', 
																				'row'   =>array('a','b')),


																		array(  'title' =>__('Exclude by tag:',$this->parameter['name_option']), //title section
																				'help'  =>'Write the tag separated by a comma which you do not want to be related to the post (drag drop and case sensitive tag).',
																				'type'  =>'tag',
																				'value' =>'',
																				'id'    =>$this->parameter['name_option'].'_'.'exclude_tag', //id
																				'name'  =>$this->parameter['name_option'].'_'.'exclude_tag', //name
																				'class' =>'',
																				'placeholder' => 'Write the tags...',
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Exclude by ID:',$this->parameter['name_option']), //title section
																				'help'  =>'Write the IDs separated by a comma which you do not want to be related to the post.',
																				'type'  =>'tag',
																				'value' =>'',
																				'id'    =>$this->parameter['name_option'].'_'.'exclude_id', //id
																				'name'  =>$this->parameter['name_option'].'_'.'exclude_id', //name
																				'class' =>'',
																				'placeholder' => 'Write the IDs...',
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Not appear inside:',$this->parameter['name_option']), //title section
																				'help'  =>'Write for ID separated by commas "," Posts you want to Yuzo not appear (You can also disable within the Post, it appears to clear Yuzo at that specific post)',
																				'type'  =>'tag',
																				'value' =>'',
																				'id'    =>$this->parameter['name_option'].'_'.'no_appear', //id
																				'name'  =>$this->parameter['name_option'].'_'.'no_appear', //name
																				'class' =>'',
																				'placeholder' => 'Write the IDs...',
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Show only post specific:',$this->parameter['name_option']), //title section
																				'help'  =>'Place the id of the post you just want to leave the "related post", by placing the plugin not show id post linking except whatever you put your.',
																				'type'  =>'tag',
																				'value' =>'',
																				'id'    =>$this->parameter['name_option'].'_'.'only_in_post', //id
																				'name'  =>$this->parameter['name_option'].'_'.'only_in_post', //name
																				'class' =>'',
																				'placeholder' => 'Write the ID...',
																				'row'   =>array('a','b')),

	 
																		array(  'title' =>__('If there is no related post, display random post?',$this->parameter['name_option']), //title section
																				'help'  =>'only if use <code>&lt;?php if ( function_exists( "get_yuzo_related_posts" ) ) { get_yuzo_related_posts(); } ?&gt;</code> in the Loop', //descripcion section
																				'type'  =>'checkbox', 
																				'value' =>'1',
																				'value_check'=>'1',
																				'id'    =>$this->parameter['name_option'].'_'.'display_random',
																				'name'  =>$this->parameter['name_option'].'_'.'display_random',
																				'class' =>'',
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Do not display properly. JS conflicts',$this->parameter['name_option']), //title section
																				'help'  =>'If not well visualized Related Post Yuzo possibly has a conflict with some other plugin Javascript, you can enable this option to remove the Yuzo js. This plugin will work normally.', //descripcion section
																				'type'  =>'checkbox', 
																				'value' =>'0',
																				'value_check'=>'1',
																				'id'    =>$this->parameter['name_option'].'_'.'yuzo_conflict',
																				'name'  =>$this->parameter['name_option'].'_'.'yuzo_conflict',
																				'class' =>'',
																				'row'   =>array('a','b')),

																)
											),
					'e0'=>array(                'title'      => __('Magnificent views counter',$this->parameter['name_option']), 
											   'title_large'=> __('',$this->parameter['name_option']), 
											   'description'=> '',  
											   'icon'       => '',
											   'tab'        => 'tab03',

												'options'    => array( 

																	array(  'title' =>__('Disabled',$this->parameter['name_option']),
																			'help'  =>__('If you enable this option, Yuzo stop counting visits by post to your wordpress site.',$this->parameter['name_option']),
																			'type'  =>'checkbox', 
																			'value' =>'0',
																			'value_check'=>'1',
																			'id'    =>$this->parameter['name_option'].'_'.'disabled_counter',
																			'name'  =>$this->parameter['name_option'].'_'.'disabled_counter',
																			'class' =>'',
																			'row'   =>array('a','b')),
	 
																)
											),
					'e'=>array(                'title'      => __('Dashboard (Post)',$this->parameter['name_option']), 
											   'title_large'=> __('',$this->parameter['name_option']), 
											   'description'=> '',  
											   'icon'       => '',
											   'tab'        => 'tab03',

												'options'    => array( 


																		array(  'title' =>__('View visits in administration',$this->parameter['name_option']),
																				'help'  =>__('Show a column in the Post Manager where it shows the number of visits per Post.',$this->parameter['name_option']),
																				'type'  =>'checkbox', 
																				'value' =>'1',
																				'value_check'=>'1',
																				'id'    =>$this->parameter['name_option'].'_'.'show_columns_dashboard',
																				'name'  =>$this->parameter['name_option'].'_'.'show_columns_dashboard',
																				'class' =>'',
																				'row'   =>array('a','b')),
	 
																)
											),
					
					'g'=>array(                'title'      => __('Show visits in related post',$this->parameter['name_option']), 
											   'title_large'=> __('',$this->parameter['name_option']), 
											   'description'=> '',  
											   'icon'       => '',
											   'tab'        => 'tab03',

												'options'    => array( 


																		array(  'title' =>__('Show',$this->parameter['name_option']),
																				'help'  =>__('Show related posts.',$this->parameter['name_option']),
																				'type'  =>'checkbox', 
																				'value' =>'0',
																				'value_check'=>'1',
																				'id'    =>$this->parameter['name_option'].'_'.'show_in_related_post',
																				'name'  =>$this->parameter['name_option'].'_'.'show_in_related_post',
																				'class' =>'',
																				'row'   =>array('a','b')),


																		array(  'title' =>__('Meta view',$this->parameter['name_option']),
																				'help'  =>__('You can use the meta "Yuzo Views" or other meta you use to visit the counter.',$this->parameter['name_option']),
																				'type'  =>'select',
																				'value' =>'yuzo-views',
																				'items' =>array('yuzo-views'=>'Yuzo views','other'=>'Other Meta'),
																				'id'    =>$this->parameter['name_option'].'_'.'meta_views',
																				'name'  =>$this->parameter['name_option'].'_'.'meta_views',
																				'class' =>'',
																				'row'   =>array('a','b')),


																		array(  'title' =>__('Use another Meta visit counter',$this->parameter['name_option']),
																				'help'  =>__("Enter the Meta (key) you're using the hit counter for your post, if you do not know anything about this best selected above 'Yuzo views'<br />If you use the 'Popular Posts WordPress' plugin, then put the key <code>popularposts</code>",$this->parameter['name_option']),
																				'type'  =>'text',
																				'value' =>'',
																				'id'    =>$this->parameter['name_option'].'_'.'meta_views_custom',
																				'name'  =>$this->parameter['name_option'].'_'.'meta_views_custom',
																				'class' =>'class_yuzo_meta_custom',
																				'row'   =>array('a','b')),
	 

																		array(  'title' =>__('Position',$this->parameter['name_option']),
																				'help'  =>__('Choose where you want to display the counter.',$this->parameter['name_option']),
																				'type'  =>'select',
																				'value' =>'show-views-bottom',
																				'items' =>array('show-views-top'=>'Before title','show-views-bottom'=>'After title'),
																				'id'    =>$this->parameter['name_option'].'_'.'show_in_related_post_position',
																				'name'  =>$this->parameter['name_option'].'_'.'show_in_related_post_position',
																				'class' =>'',
																				'row'   =>array('a','b')),

																		
																		array(  'title' =>__('Text views',$this->parameter['name_option']),
																				'help'  =>__('Set the text to be displayed to identify visits, if you leave it empty display an icon of an eye.',$this->parameter['name_option']),
																				'type'  =>'text',
																				'value' =>'views',
																				'id'    =>$this->parameter['name_option'].'_'.'show_in_related_post_text',
																				'name'  =>$this->parameter['name_option'].'_'.'show_in_related_post_text',
																				'class' =>'',
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Format thousands',$this->parameter['name_option']),
																				'help'  =>__('Select between 2 formats that you can identify thousands in the hit counter by Post.',$this->parameter['name_option']),
																				'type'  =>'select',
																				'items' =>array(''=>__('none',$this->parameter['name_option']),','=>__(',',$this->parameter['name_option']),'.'=>__('.',$this->parameter['name_option'])),
																				'value' =>'views',
																				'id'    =>$this->parameter['name_option'].'_'.'format_count',
																				'name'  =>$this->parameter['name_option'].'_'.'format_count',
																				'class' =>'',
																				'row'   =>array('a','b')),

																		array(  'title' =>__('1000 to 1K',$this->parameter['name_option']),
																				'help'  =>__('Cut the hit counter as social networks',$this->parameter['name_option']),
																				'type'  =>'checkbox', 
																				'value' =>'0',
																				'value_check'=>'1',
																				'id'    =>$this->parameter['name_option'].'_'.'cut_hit',
																				'name'  =>$this->parameter['name_option'].'_'.'cut_hit',
																				'class' =>'',
																				'row'   =>array('a','b')),
	 
																)
											),
					'f'=>array(                'title'      => __('Widgets',$this->parameter['name_option']), 
											   'title_large'=> __('',$this->parameter['name_option']), 
											   'description'=> '',  
											   'icon'       => '',
											   'tab'        => 'tab03',

												'options'    => array( 
																		array(  'title' =>__('Active Yuzo Widget',$this->parameter['name_option']),
																				'help'  =>__('If you select this option, activate the powerful Yuzo Widget.',$this->parameter['name_option']),
																				'type'  =>'checkbox', 
																				'value' =>'1',
																				'value_check'=>'1',
																				'id'    =>$this->parameter['name_option'].'_'.'active_widget',
																				'name'  =>$this->parameter['name_option'].'_'.'active_widget',
																				'class' =>'',
																				'row'   =>array('a','b')),
															  ),
					),
					'ff'=>array(                'title'      => __('Metabox',$this->parameter['name_option']), 
											   'title_large'=> __('',$this->parameter['name_option']), 
											   'description'=> '',  
											   'icon'       => '',
											   'tab'        => 'tab03',

												'options'    => array( 
																		array(  'title' =>__('Disabled Metabox',$this->parameter['name_option']),
																				'help'  =>__('if you have many problems with this. (This option requires PHP 5.3+ stable)',$this->parameter['name_option']),
																				'type'  =>'checkbox', 
																				'value' =>'0',
																				'value_check'=>'1',
																				'id'    =>$this->parameter['name_option'].'_'.'disabled_metabox',
																				'name'  =>$this->parameter['name_option'].'_'.'disabled_metabox',
																				'class' =>'',
																				'row'   =>array('a','b')),
															  ),
					),
					'k'=>array(                'title'      => __('Faster',$this->parameter['name_option']), 
											   'title_large'=> __('',$this->parameter['name_option']), 
											   'description'=> '',  
											   'icon'       => '',
											   'tab'        => 'tab03',

												'options'    => array(

																		array(  'title' =>__('',$this->parameter['name_option']),
																				'help'  =>__('',$this->parameter['name_option']),
																				'type'  =>'html', 
																				'html1' =>'',
																				'html2' =>"You can use the 'transient' to Yuzo is extremely fast, with this you can save a lot of queries to the database with the same result, it emphasized that the beacons are increased but these are refreshed every 20 minutes. For the administrator is counted in real time.
																				<div class='text_emphasis custom' style='/*margin: 10px 0;background: rgba(253, 255, 121, 0.35);color: #736D6D;padding: 2px 5px;font-style: italic;*/'><strong style='font-weight:bolder;'>NOTE: </strong> <br />1) Use this option only and only if you do not use Cache plugins.<br />2) It not recommended to use this option when doing tests on localhost. <br />3) This option is recommended for use with high-traffic web sites and not to use cache plugin. </div>",
																				'id'    =>$this->parameter['name_option'].'_'.'yuzo_transient_html',
																				'name'  =>$this->parameter['name_option'].'_'.'yuzo_transient_html',
																				'class' =>'yuzo_message_html_transient',
																				'row'   =>array('a','c')),


																		array(  'title' =>__('Use transient?:',$this->parameter['name_option']), //title section
																				'help'  =>'With this you can cache the query to display the content faster, rather than asking each time the database with this option you can make Yuzo much faster.',
																				'type'  =>'checkbox', //type input configuration
																				'value' =>'0', //value
																				'value_check'=>1,
																				'id'    =>$this->parameter['name_option'].'_'.'transient', //id
																				'name'  =>$this->parameter['name_option'].'_'.'transient', //name
																				'class' =>'', //class
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Clear transient database:',$this->parameter['name_option']),  
																				'help'  =>"You can remove the 'transient cache' of the database manually, just press this button.",
																				'type'  =>'button',
																				'value' =>'javascript:void(0);', // URL
																				'onclick'=>"return delete_transient(this);",
																				'text_button'=>'Delete Transient',
																				'id'    =>$this->parameter['name_option'].'_'.'delete_transient', 
																				'name'  =>$this->parameter['name_option'].'_'.'delete_transient',  
																				'class' =>'',
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Clear old Meta_Key:',$this->parameter['name_option']),  
																				'help'  =>"If you have had previous to Yuzo 4.0 version, you can remove the meta_key 'yuzo_views' this goal was formerly created but now the hit counter does with its own table of Yuzo, eliminates this meta_key because it will not work in the future.",
																				'type'  =>'button',
																				'value' =>'javascript:void(0);', // URL
																				'onclick'=>"return delete_meta_yuzo_ok(this);",
																				'text_button'=>'Delete Meta Key',
																				'id'    =>$this->parameter['name_option'].'_'.'delete_meta', 
																				'name'  =>$this->parameter['name_option'].'_'.'delete_meta',  
																				'class' =>'',
																				'row'   =>array('a','b')),

																			)
											),
					
				   'f0'=>array(                'title'      => __('Shortcodes Related',$this->parameter['name_option']), 
											   'title_large'=> __('',$this->parameter['name_option']), 
											   'description'=> '',  
											   'icon'       => '',
											   'tab'        => 'tab03',

												'options'    => array( 
																		array(  'title' =>__('',$this->parameter['name_option']),
																				'help'  =>__('',$this->parameter['name_option']),
																				'type'  =>'html', 
																				'html1' =>'',
																				'html2' =>'You can put the  <code>[yuzo_related]</code> or for template <code>&lt;?php echo do_shortcode( "[yuzo_related]" ); ?&gt;</code><br />
																						  With this option you can put related via a shortcode.',
																				'id'    =>$this->parameter['name_option'].'_'.'yuzo_html_shortcode_related',
																				'name'  =>$this->parameter['name_option'].'_'.'yuzo_html_shortcode_related',
																				'class' =>'yuzo_message_html_related',
																				'row'   =>array('a','c')),
															  ),
					),
					'f1'=>array(               'title'      => __('Shortcodes counters (hit)',$this->parameter['name_option']), 
											   'title_large'=> __('',$this->parameter['name_option']), 
											   'description'=> '',  
											   'icon'       => '',
											   'tab'        => 'tab03',

												'options'    => array( 
																		array(  'title' =>__('',$this->parameter['name_option']),
																				'help'  =>__('',$this->parameter['name_option']),
																				'type'  =>'html', 
																				'html1' =>'',
																				'html2' =>'You can put the  <code>[yuzo_views]</code> or for template <code>&lt;?php echo do_shortcode( "[yuzo_views]" ); ?&gt;</code><br />
																						  With this option you can put the hit counter anywhere via a shortcode.<br />
																						  And if you want to display the counter of a specific post you can use this <code>[yuzo_views id=123]</code>',
																				'id'    =>$this->parameter['name_option'].'_'.'yuzo_html_shortcode',
																				'name'  =>$this->parameter['name_option'].'_'.'yuzo_html_shortcode',
																				'class' =>'yuzo_message_html',
																				'row'   =>array('a','c')),
															  ),
					),
					
					'h'=>array(                'title'      => __('Template',$this->parameter['name_option']), 
											   'title_large'=> __('',$this->parameter['name_option']), 
											   'description'=> '',  
											   'icon'       => '',
											   'tab'        => 'tab03',

												'options'    => array( 


																		array(  'title' =>__('',$this->parameter['name_option']),
																				'help'  =>__('',$this->parameter['name_option']),
																				'type'  =>'html', 
																				'html1' =>'',
																				'html2' =>'You can put the <br /><code>&lt;?php if ( function_exists( "get_Yuzo_Views" ) ) { echo get_Yuzo_Views(); } ?&gt;</code><br />
																						  function to get the total view of your post, that you can put in your 
																						  <code>single.php</code>,
																						  <code>page.php</code>,
																						  <code>loop.php</code>,
																						  <code>your-template.php</code> file anywhere you 
																						  want and with the personalization you want since this function will get the number of visits this Post. <br /><br />
																						  <div><p  style="text-align:center"><strong style="font-weight:bold;">The best WordPress Related Post ;)</strong></p><p style="text-align:center;maring:0 auto;"><span class="ilen_shine" style="display:inline-block;width:114px;height:51px;"><span class="shine-effect"></span><img  src="'.$this->parameter['theme_imagen'].'/wordpress-and-love.png" /></span></p></div>',
																				'id'    =>$this->parameter['name_option'].'_'.'yuzo_get_views_html',
																				'name'  =>$this->parameter['name_option'].'_'.'yuzo_get_views_html',
																				'class' =>'yuzo_message_html',
																				'row'   =>array('a','c')),
	 
																)
											),
					'i'=>array(                'title'      => __('Effect on related',$this->parameter['name_option']), 
											   'title_large'=> __('',$this->parameter['name_option']), 
											   'description'=> '',  
											   'icon'       => '',
											   'tab'        => 'tab02',

												'options'    => array( 
																		array(  'title' =>__('Choose effect',$this->parameter['name_option']),
																				'help'  =>__('Yuzo has many visual effects with respect to the image when the mouse is over the related.',$this->parameter['name_option']),
																				'type'  =>'select',
																				'value' =>'none',
																				'items' =>array(
																								'none'           =>__('None',$this->parameter['name_option']),
																								'enlarge'        =>__('Enlarge related',$this->parameter['name_option']),
																								'zoom_icon_link' =>__('Zoom + Icons link + Opacity',$this->parameter['name_option']),
																								'shine'          =>__('Shine',$this->parameter['name_option']),
																							   ),
																				'id'    =>$this->parameter['name_option'].'_'.'effect_related',
																				'name'  =>$this->parameter['name_option'].'_'.'effect_related',
																				'class' =>'',
																				'row'   =>array('a','b')),
															  ),
					),
					'j'=>array(                'title'      => __('Text',$this->parameter['name_option']), 
											   'title_large'=> __('',$this->parameter['name_option']), 
											   'description'=> '',  
											   'icon'       => '',
											   'tab'        => 'tab02',

												'options'    => array( 


																		array(  'title' =>__('Font size',$this->parameter['name_option']),
																				'help'  =>__('Font size of title related',$this->parameter['name_option']),
																				'type'  =>'select',
																				'value' =>13,
																				'items' =>array(9=>'9',10=>'10',11=>'11',12=>'12',13=>'13',14=>'14',15=>'15',16=>'16',17=>'17',18=>'18',19=>'19',20=>'20'),
																				'id'    =>$this->parameter['name_option'].'_'.'font_size',
																				'name'  =>$this->parameter['name_option'].'_'.'font_size',
																				'class' =>'',
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Title length',$this->parameter['name_option']),
																				'help'  =>__('Number of characters to be shown in the title',$this->parameter['name_option']),
																				'type'  =>'text',
																				'value' =>'50',
																				'id'    =>$this->parameter['name_option'].'_'.'text_length',
																				'name'  =>$this->parameter['name_option'].'_'.'text_length',
																				'class' =>'',
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Title Bold:',$this->parameter['name_option']), //title section
																				'help'  =>'Title font weight',
																				'type'  =>'checkbox', //type input configuration
																				'value' =>'0', //value
																				'value_check'=>1,
																				'id'    =>$this->parameter['name_option'].'_'.'title_bold', //id
																				'name'  =>$this->parameter['name_option'].'_'.'title_bold', //name
																				'class' =>'', //class
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Title color',$this->parameter['name_option']),
																				'help'  =>__('Selected title color, you can also leave blank',$this->parameter['name_option']),
																				'type'  =>'color_hover',
																				'value' =>'',
																				'id'  =>$this->parameter['name_option']. '_'. 'title_color',
																				'name'  =>$this->parameter['name_option']. '_'. 'title_color',
																				'class' =>'',
																				'row' =>array('a','b')),

																		array(  'title' =>__('Text Color',$this->parameter['name_option']),
																				'help'  =>'Selected text color and mouse hover, you can also leave blank', 
																				'type'  =>'color_hover',
																				'value' =>array('color'=>'','hover'=>''),
																				'id'    =>$this->parameter['name_option'].'_'.'text_color',
																				'name'  =>$this->parameter['name_option'].'_'.'text_color', 
																				'class' =>'', 
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Text length',$this->parameter['name_option']),
																				'help'  =>__('Number of text lettering post',$this->parameter['name_option']),
																				'type'  =>'text',
																				'value' =>'0',
																				'id'    =>$this->parameter['name_option'].'_'.'text2_length',
																				'name'  =>$this->parameter['name_option'].'_'.'text2_length',
																				'class' =>'',
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Text to display',$this->parameter['name_option']),
																				'help'  =>__('You can choose from the first text of the article or else the extract of the article',$this->parameter['name_option']),
																				'type'  =>'select',
																				'value' =>'none',
																				'items' =>array(
																								'1'        =>__('Text in the article begins',$this->parameter['name_option']),
																								'2'        =>__('Excerpt from article',$this->parameter['name_option']),
																							   ),
																				'id'    =>$this->parameter['name_option'].'_'.'text_show',
																				'name'  =>$this->parameter['name_option'].'_'.'text_show',
																				'class' =>'',
																				'row'   =>array('a','b')),
																		
																		array(  'title' =>__('Text center:',$this->parameter['name_option']), //title section
																				'help'  =>'Place your title text centered',
																				'type'  =>'checkbox', //type input configuration
																				'value' =>'0', //default
																				'value_check'=>1, // value
																				'id'    =>$this->parameter['name_option'].'_'.'title_center', //id
																				'name'  =>$this->parameter['name_option'].'_'.'title_center', //name
																				'class' =>'', //class
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Theme',$this->parameter['name_option']),
																				'help'  =>__('Select the theme made to choose one to your liking',$this->parameter['name_option']),
																				'type'  =>'select',
																				'value' =>'default',
																				'items' =>array('default'=>'My Custom theme css','magazine-alfa'=>'Magazine Alfa'),
																				'id'    =>$this->parameter['name_option'].'_'.'theme',
																				'name'  =>$this->parameter['name_option'].'_'.'theme',
																				'class' =>'yuzo_chosse_theme_select',
																				'row'   =>array('a','b')),

																		array(  'title' =>__('Add custom css or chosse theme <span id="chosse_yuzo_theme"></span>',$this->parameter['name_option']),
																				'help'  =>__('1) At the moment there is one theme and is "Magazine Alfa"<br />
2) If you want to edit a theme you can do it is copy and paste the option to "My Custom theme css" code of theme and there you can edit<br />
3) In future versions will come new "themes", if you want to add a theme send me the link to the page on my site support to simulate the theme for you.',$this->parameter['name_option']),
																				'type'  =>'component_enhancing_code',
																				'lineNumbers' =>'false',
																				'value' =>".yuzo_related_post{}\n.yuzo_related_post .relatedthumb{}",
																				//'mini_callback' => 'editor_bubble_seo_preview.setValue( jQuery("#ilen_seo_preview").html() );',
																				'id'    =>$this->parameter['name_option'].'_'.'css_and_style',
																				'name'  =>$this->parameter['name_option'].'_'.'css_and_style',
																				'class' =>'yuzo_my_custom_theme',
																				'row'   =>array('a','c')),

																		array(  'title' =>__('',$this->parameter['name_option']),
																				'help'  =>__('',$this->parameter['name_option']),
																				'type'  =>'html', 
																				'html1' =>'',
																				'html2' =>"<span id='yuzo_css_default'>".isset($yuzo_options_temp->css_and_style) && $yuzo_options_temp->css_and_style?nl2br($yuzo_options_temp->css_and_style):''."</span>",
																				'id'    =>$this->parameter['name_option'].'_'.'yuzo_html_custom_css',
																				'name'  =>$this->parameter['name_option'].'_'.'yuzo_html_custom_css',
																				'class' =>'',
																				'style' =>'style="display:none;"',
																				'row'   =>array('a','c')),

																		array(  'title' =>__('',$this->parameter['name_option']),
																				'help'  =>__('',$this->parameter['name_option']),
																				'type'  =>'html', 
																				'html1' =>'',
																				'html2' =>"<span id='yuzo_css_magazine-alfa'>".nl2br(".yuzo_wraps{
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
 }")."</span>",
																				'id'    =>$this->parameter['name_option'].'_'.'yuzo_css_theme_magazine-alfa',
																				'name'  =>$this->parameter['name_option'].'_'.'yuzo_css_theme_magazine-alfa',
																				'class' =>'',
																				'style' =>'style="display:none;"',
																				'row'   =>array('a','c')),
	 
																)
											),
								'last_update'=>time(),


				);
		
	}





	function parameters(){
		$this->parameter = self::getHeaderPlugin();
	}

	function myoptions_build(){
		
		$this->options = self::getOptionsPlugin();

		return $this->options;
		
	}

	function use_components(){
		//code 
		
		$this->components = array('list_categories');

	}

	function configuration_plugin(){
		// set parameter 
		self::parameters();

		// my configuration 
		self::myoptions_build();

		// my component to use
		self::use_components();
	}

	function __construct(){

		global $if_utils;

		$this->utils = $if_utils;

		if( is_admin() )
			self::configuration_plugin();
		else
			self::parameters();

	}


}
}
?>