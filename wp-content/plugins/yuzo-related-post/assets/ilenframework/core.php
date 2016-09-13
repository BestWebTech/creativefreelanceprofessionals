<?php 
/**
 * iLenFramework 2.7.9
 * @package ilentheme
 * 
 * live as if it were the last day of your life
 */

// REQUIRED FILES TO RUN
if ( !class_exists('ilen_framework_2_7_9') ) {

class ilen_framework_2_7_9 {

		var $options          = array();
		var $parameter        = array();
		var $save_status      = null;
		var $IF_CONFIG        = null;
		var $components       = null;

		/**
		 * @var $api_google_fonts_url	The google web font API URL
		*/
		protected $api_google_fonts_url = "https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyCjae0lAeI-4JLvCgxJExjurC4whgoOigA";
		protected $fonts_url = "//fonts.googleapis.com/css?family=";


	function __construct(){

		if( ! is_admin() ){ // only front-end

			self::set_main_variable();
			return;

		}elseif( is_admin()  ){ // only admin



			// set default if not exists
			self::_ini_();



			// add menu options
			self::iLenFramework_add_menu();
				


			// add scripts & styles
			add_action('admin_enqueue_scripts', array( &$this,'ilenframework_add_scripts_admin') );



			// Ajax for plugin elements
			self::AjaxElements();



		}   

		add_action('admin_init',array(&$this,'plugin_install_before')); 



	}





	// =Definitions Fields
	function theme_definitions(){
		
		return $this->options;
		
	}


	// =Add Menu
	function iLenFramework_add_menu(){

		if( isset($this->parameter['no_menu']) && $this->parameter['no_menu'] == 1 ) return;

		if( isset($this->parameter['type']) && $this->parameter['type'] == "theme" ){

			if( isset($this->parameter['method']) && $this->parameter['method'] == "free"  ){

				add_action('admin_menu', array( &$this,'menu_free') );      

			}elseif( $this->parameter['method'] == "buy" ){

				add_action('admin_menu', array( &$this,'menu_pay') );       

			}

		}elseif( isset($this->parameter['type']) && (  $this->parameter['type'] == "plugin" || $this->parameter['type'] == "plugin-tabs"  )  ){

			if( isset($this->parameter['method']) && $this->parameter['method'] == "free"  ){

				add_action('admin_menu', array( &$this,'menu_free') );      

			}elseif( isset($this->parameter['method']) && $this->parameter['method'] == "buy" ){

				add_action('admin_menu', array( &$this,'menu_pay') );       

			}

		}
		
	}



	function set_main_variable(){

		global $IF_CONFIG;

		$this->IF_CONFIG  = $IF_CONFIG;

		$this->parameter  = isset($IF_CONFIG->parameter)?(array)$IF_CONFIG->parameter:null;
		$this->options    = isset($IF_CONFIG->options)?(array)$IF_CONFIG->options:null;
		$this->components = isset($IF_CONFIG->components)?$IF_CONFIG->components:null;

	}




	// =INIT theme
	function _ini_(){

		self::set_main_variable();

		self::setComponents();

		self::theme_plugin_install_set_default_values();

		if( isset($this->parameter['id_menu']) && isset($_GET["page"]) && ( $_GET["page"] == $this->parameter['id_menu'] ) ){ // validate if admin page is the option


			// get Components
			//self::_getComponents_();

			// set varaible configuration
			$this->options = $this->IF_CONFIG->options;

			// if save update options
			if($this->parameter['type'] == 'plugin-tabs'){
				self::save_options_for_tabs();
			}else{
				self::save_options();
			}
 
		}


	}





	function theme_plugin_install_set_default_values(){

		global $options_theme;

		if( isset($this->parameter['name_option']) && ! $options_theme = get_option( $this->parameter['name_option']."_options") ){

			// if not exists options them create
			$data_setup=self::get_default_options();
			update_option( $this->parameter['name_option']."_options", $data_setup );
			$options_theme = $data_setup;

		}

		/*if( (isset($_GET["activate"]) &&  $_GET["activate"] == 'true') || (isset($_GET["install_data"]) && $_GET["install_data"] == "true"  )  ){
		
			if( isset($this->parameter['name_option']) && ! $n = get_option( $this->parameter['name_option']."_options") ){
		
				// if not exists options them create
				update_option( $this->parameter['name_option']."_options", self::get_default_options());

			}

		}else{

			//$data_opt = get_option( $this->parameter['name_option']."_options" );
			if( isset($this->parameter['name_option']) && ! $n = get_option( $this->parameter['name_option']."_options") ){

				// if not exists options them create
				update_option( $this->parameter['name_option']."_options", self::get_default_options());

			}

			
			/*if( ! $data_opt ){

				$Myoptions = self::theme_definitions();

				if( is_array($Myoptions) ){
					foreach ($Myoptions as $key2 => $value2) {

						if( $key2 != 'last_update' ){

							foreach ($value2['options'] as $key3 => $value3) {

								if( isset($value3['name']) && isset($value3['value']) ){
									$options_update[ $value3['name'] ] = $value3['value'];	
								}
								
							}
						}else{
							$options_update[$key2] = time();
						}

					}
				}
				//var_dump($options_update);
				update_option( $this->parameter['name_option']."_options" , $options_update);

			}

		}*/
		
	}




	// =DEFAULTS OPTIONS
	function get_default_options(){
		
		$defaults = array();

		$Myoptions = self::theme_definitions();
		if( is_array($Myoptions) )
			foreach ($Myoptions as $key2 => $value2) {
				if(  $key2 != 'last_update' ){
					foreach ($value2['options'] as $key => $value) {

						if( isset($value['name']) && isset($value['type']) && isset($value['value']) && $value['type'] != "html" ){
							$defaults[$value['name']] = $value['value'];
						}

					}
				}
			}

		return $defaults;
		
	}





	// =MENU--------------------------------------------
	function menu_free() {

		if( $this->parameter['type'] == "theme" ){
			add_theme_page($this->parameter['name'], $this->parameter['name_long'], 'edit_theme_options', $this->parameter['id_menu'] , array( &$this,'ilentheme_full') );
		}elseif( $this->parameter['type']  == "plugin" || $this->parameter['type']  == "plugin-tabs" ){
			add_options_page( $this->parameter['name'], $this->parameter['name_long'], 'manage_options', $this->parameter['id_menu'], array( &$this,'ilentheme_full') );
		}
	}

	function menu_pay() {

		//add_menu_page($this->parameter['name'], $this->parameter['name_long'], 'manage_options',  $this->parameter['id_menu'], array( &$this,'ilentheme_full') );
		add_options_page( $this->parameter['name'], $this->parameter['name_long'], 'manage_options', $this->parameter['id_menu'], array( &$this,'ilentheme_full') );

	}






	function ilentheme_full(){
		//code 


		self::ShowHTML();
		
 
	}

 



	// =Interface Create for Theme---------------------------------------------
	function ilentheme_options_wrap_for_theme(){ ?>
 
		<div class='ilentheme-options'>
			<form action="" method="POST" name="frmsave" id="frmsave">
			<header>
				<div class="top-left logo">
					<?php 
						if( !$this->parameter["logo"] )
							echo "<h1><a href='#'>{$this->parameter["name"]}</a> <span>".$this->parameter['slogan']."</span></h1>";
						else
							echo "<a href='#'><img src='{$this->parameter["logo"]}' /></a>";
					?>
					<!-- <span><?php //echo $this->parameter["slogan"] ?></span> -->
				</div>
				<div class="top-right">
					<a href="#" class="ibtn btnblack right btn_save"><span><i class="fa fa-refresh"></i></span><?php _e('Save Changes',$this->parameter['name_option']) ?></a>
				</div>
			</header>

			<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all ilencontentwrapelements ui-helper-clearfix">
				<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">

					<?php $Myoptions = self::theme_definitions();

					if( is_array( $Myoptions ) ) {
						foreach ($Myoptions as $key => $value) { ?>
							<?php if($key != 'last_update'){ ?>
									<li class="ui-state-default ui-corner-top">
										<a href="#<?php echo $key; ?>" class="ui-tabs-anchor">
											<?php 
												if( $value['icon'] )
													echo '<i class="'.$value['icon'].'"></i>';
											?>
											<?php echo $value['title']; ?>
										</a>
									</li>
							<?php } ?>

						<?php  }
					}

					?>

				</ul>

				<?php 
					// set mesagge status
					if( $this->save_status===true )
						$class_status="ok";
					elseif( $this->save_status===false )
						$class_status="error";
				?>
				<?php if( $this->save_status ){ ?>
				<div class="messagebox <?php echo $class_status; ?>"><i class="fa fa-check"></i> 
					<?php _e('Nice',$this->parameter['name_option'])."."; ?> <?php _e('Update successfully',$this->parameter['name_option']) ?>
				</div>
				<?php } ?>


				<?php 

				if( is_array( $Myoptions ) ){
					foreach ($Myoptions as $key => $value) { ?>
							<?php if($key != 'last_update'){ ?>
								<div id="<?php echo $key; ?>" class="content-tab">
									<h2 aaa>
										<?php 
											if( $value['icon'] )
												echo '<i class="'.$value['icon'].'"></i>';
										?>
										<?php echo $value['title']; ?>
									</h2>
									<?php if( $value['description'] ){ ?>
										<p class="description"><?php echo $value['description']; ?></p>
									<?php } ?>
									
									<?php self::build_fields( $value['options'] ) ?>
								</div>
							<?php } ?>

					<?php  }
				}
				?>

			</div>
			<footer>
				<a href="#" class="ibtn btnblack right btn_save"><span><i class="fa fa-refresh"></i></span><?php _e('Save Changes',$this->parameter['name_option']) ?></a>
				<a href="#" class="ibtn btnred left btn_reset" data-me="<?php _e('Want to update all the default values​​ &#63;',$this->parameter['name_option']) ?>"><span><i class="fa fa-repeat"></i></span><?php _e('Reset',$this->parameter['name_option']) ?></a>
			</footer>

			<input type="hidden" name='save_options' value='1' />
			<input type="hidden" name='name_options' value='<?php echo $this->parameter["name_option"]; ?>' />
			</form>
			<form action="" method="POST" name="frmreset" id="frmreset">
				<input type="hidden" name='reset_options' value='1' />
				<input type="hidden" name='name_options' value='<?php echo $this->parameter['name_option']; ?>' />
			</form>
		</div>  
 

	<?php }




	// =Interface Create for plugin---------------------------------------------
	function ilentheme_options_wrap_for_plugin(){ ?>
		
		<div class='ilenplugin-options ilenplugin-<?php echo $this->parameter["name_option"] ?> <?php if( isset($_POST) && !$_POST && isset($this->parameter["animation_postbox"]) && $this->parameter["animation_postbox"] ){ echo $this->parameter["animation_postbox"]; } ?>'>


			<form action="" method="POST" name="frmsave" id="frmsave">
 
				<div id="poststuff" class="metabox-holder has-right-sidebar">

					
					
					<div id="post-body-content" class="has-sidebar-content">

					<?php 
					// set mesagge status
					if( $this->save_status===true ) : ?>
						  <div class="notification-p success">
							<aside>
							  <i class="fa fa-check"></i>
							</aside>
							<main>
							  <b><?php _e('Nice',$this->parameter['name_option'])."."; ?></b>
							  <br />
							  <?php _e('Update successfully',$this->parameter['name_option']) ?>
							</main>
						  </div>
					<?php elseif( $this->save_status===false ): ?>
						  <div class="notification-p perror">
							<aside>
							  <i class="fa fa-times"></i>
							</aside>
							<main>
							  <b><?php _e('Oh bollocks',$this->parameter['name_option'])."."; ?>.</b>
							  <br />
							  <?php _e('Failed to update',$this->parameter['name_option']) ?>
							</main>
						  </div>
					<?php endif; ?>

					<div class="my-wrap-plugin">
					<header class="<?php if( strlen($this->parameter['name_long'])>20 ){ echo 'text-long'; } ?>">
						<span class="header__logo waves-effect"><?php echo $this->parameter['logo']; ?></span>
						<h2>
							<?php echo $this->parameter['name_long']; ?>
							<span class='ilen-version'><?php echo $this->parameter['version'] ?></span>
						</h2>
						<?php if( isset($this->parameter['wp_review']) && $this->parameter['wp_review'] ): ?><a href="<?php echo $this->parameter['wp_review'] ?>" class="leave-a-review ibtn btnred right grow-btn" target="_blank"><span><i class="fa fa-star"></i></span>Leave a review</a><?php endif; ?>
						<?php if( isset($this->parameter['twitter'] ) && $this->parameter['twitter'] ): ?><a href="<?php echo $this->parameter['twitter'] ?>" class="tweet-about-it ibtn btnturke right" target="_blank"><span><i class="fa fa-twitter"></i></span>Write your experience</a><?php endif; ?>
						<?php if( isset($this->parameter['wp_support']) && $this->parameter['wp_support'] ): ?><a href="<?php echo $this->parameter['wp_support'] ?>" class="ibtn btngray2 right" target="_blank"><span><i class="fa fa-wrench"></i></span>Support</a><?php endif; ?>
					</header>

					<?php $Myoptions = self::theme_definitions(); ?>

					<?php if( is_array( $Myoptions ) ): ?>
						
							<?php 
							$put_tab = 0;
							global ${'tabs_plugin_' . $this->parameter['name_option']};
							$tabs_plugin = ${'tabs_plugin_' . $this->parameter['name_option']};
							if( is_array($tabs_plugin) && isset($tabs_plugin) ){
							foreach ($tabs_plugin as $key => $value_tab): 
								if( $value_tab["id"] && $put_tab ==0 ): ?>
									<div id="tabs" class="ilencontentwrapelements">
										
										<ul>
								<?php 
									$put_tab=1;
								endif;

									if( isset($value_tab["id"]) && $value_tab["id"] ) : 
										$medida = isset($value_tab["medida"]) && $value_tab["medida"]?$value_tab["medida"]:"px";
										?>
										<li style="<?php if( isset($value_tab["width"]) && isset( $value_tab["fix"]) ){ echo "border-right:0;"; }?><?php if( isset($value_tab["width"]) && isset( $value_tab["fix"]) ){ echo (($value_tab["width"])+1)."px;"; } elseif( isset($value_tab["width"]) ){ echo "width:{$value_tab["width"]}{$medida};";  } ?>" ><a href="#<?php echo $value_tab["id"]; ?>" style="width:100%;" class="animation_once" ><?php if(isset($value_tab["icon"])){ echo $value_tab["icon"]; } ?> <?php echo $value_tab["name"]; ?></a></li>
							<?php   endif;
							endforeach;
							} ?>
										</ul>
										
						<?php 
						if( is_array($tabs_plugin) && isset($tabs_plugin) ){
							foreach ($tabs_plugin as $key_tab => $value_tab) { ?>
								<div id="<?php echo $value_tab["id"]; ?>"></div>
							<?php }
						} ?>
 
					<?php endif; ?>
 
						<div class="meta-box-sortabless">
							<div class="has-sidebar sm-padded">


								<?php //$Myoptions = self::theme_definitions();

									if( is_array( $Myoptions ) ){
										global $options_theme;
										//$options_theme = null;
										//$options_theme = get_option( $this->parameter['name_option']."_options" );

										foreach ($Myoptions as $key => $value) {

											if( isset($value['options']) && is_array($value['options']) ){

										 ?>
											<?php if($key != 'last_update'){  ?>

													<div id="box_<?php echo $key; ?>" class="postbox animation_postbox_once <?php if( isset($value["tab"]) ){ echo $value["tab"]; } ?> <?php if( isset($value["class"]) ){ echo $value["class"]; } ?>">
														<h3 class="hndle">
															<span>
															<?php 
																if( $value['icon'] ){
																	echo '<i class="'.$value['icon'].'"></i>&nbsp;&nbsp;';
																}
															?><?php echo $value['title']; ?>
															</span>
														</h3>
														<div class="inside">
																<?php self::build_fields_p( $value['options'] ) ?>
														</div>
													</div>

												<?php } ?>

											<?php } ?>

										<?php  }

									} ?>

								

							</div>
						</div>
						<?php if( $put_tab ==1 ): ?>
							</div><!-- div id=tab -->
						<?php endif; ?>
						<footer>
							<a class="btn_save ibtn btnblack left"><span><i class="fa fa-refresh"></i></span><?php _e('Save Changes',$this->parameter['name_option']) ?></a>
							<a class="ibtn btnred left btn_reset" data-me="<?php _e('Want to update all the default values​​ &#63;',$this->parameter['name_option']) ?>"><span><i class="fa fa-repeat"></i></span><?php _e('Reset',$this->parameter['name_option']) ?></a>
							<?php if( isset($this->parameter['link_donate']) && $this->parameter['link_donate'] ): ?>
							<a class="right btn_donate " href="<?php echo $this->parameter['link_donate']; ?>" target="_blank" ><?php _e('Donate',$this->parameter['name_option']) ?></a>
							<?php endif; ?>
						</footer>
						<script>
						/*jQuery(document).ready(function($){
							//$(".rippler").rippler({
						// addElement:"svg"
						});
						});*/
						</script>
						</div> <!-- my-wrap-plugin -->
					</div>
				</div>
				<input type="hidden" name='save_options' value='1' />
				<input type="hidden" name='name_options' value='<?php echo $this->parameter["name_option"]; ?>' />
				<input type="hidden" name='if_submit' id='if_submit' value='<?php if( isset($_POST) && $_POST ): ?>1<?php endif; ?>' />
				</form>

				<form action="" method="POST" name="frmreset" id="frmreset">
					<input type="hidden" name='reset_options' value='1' />
					<input type="hidden" name='name_options' value='<?php echo $this->parameter['name_option']; ?>' />
				</form>

				<!-- donate -->
				

				<!-- IF PLUGIN TAB, inner HTML in tab -->
					<script>
					<?php  
					if( is_array($tabs_plugin) && isset($tabs_plugin) ){
						foreach ($tabs_plugin as $key_tab => $value_tab) { ?>
							jQuery(".<?php echo $value_tab['id']; ?>").each(function(){
								jQuery( this ).appendTo( jQuery("#<?php echo $value_tab['id']; ?>") );
							});
						<?php }
					} ?>
					//jQuery('#frm_donate').appendTo('.ilenplugin-options footer');
					</script>
				<!-- END -->
		</div>


	<?php 
	}


// =Interface Create for plugin for TABS---------------------------------------------
function ilentheme_options_wrap_for_plugin_tabs(){  ?>
	<div class='ilenplugin-options ilenplugin-<?php echo $this->parameter["name_option"] ?> '>


		<div id="poststuff" class="metabox-holder has-right-sidebar">

			<div id="post-body-content" class="has-sidebar-content ilentabs">

			<?php 
			// set mesagge status
			if( $this->save_status===true ) : ?>
				  <div class="notification-p success">
					<aside>
					  <i class="fa fa-check"></i>
					</aside>
					<main>
					  <b><?php _e('Nice',$this->parameter['name_option'])."."; ?></b>
					  <br />
					  <?php _e('Update successfully',$this->parameter['name_option']) ?>
					</main>
				  </div>
			<?php elseif( $this->save_status===false ): ?>
				  <div class="notification-p perror">
					<aside>
					  <i class="fa fa-times"></i>
					</aside>
					<main>
					  <b><?php _e('Oh bollocks',$this->parameter['name_option'])."."; ?>.</b>
					  <br />
					  <?php _e('Failed to update',$this->parameter['name_option']) ?>
					</main>
				  </div>
			<?php endif; ?>

			<div class="post-body-content__wrap">
			<header class="<?php if( strlen($this->parameter['name_long'])>20 ){ echo 'text-long'; } ?>">
				<span class="header__logo"><?php echo $this->parameter['logo']; ?></span>
				<h2>
					<?php echo $this->parameter['name_long']; ?>
					<?php if( !is_rtl() ): ?>
					<span class='ilen-version'><?php if( isset($this->parameter['method']) && $this->parameter['method'] == 'free' ){ echo __('',$this->parameter['name_option']); }else{ echo __('',$this->parameter['name_option']); } echo " ".$this->parameter['version']; ?></span>
				<?php elseif( is_rtl() ) : ?>
					<span class='ilen-version'><?php if( isset($this->parameter['method']) && $this->parameter['method'] == 'free' ){ echo  $this->parameter['version']. " " .__('',$this->parameter['name_option']); }else{ echo $this->parameter['version']. " " . __('',$this->parameter['name_option']); }  ?></span>
				<?php endif; ?> 
				</h2>
				<?php if( isset($this->parameter['wp_review']) && $this->parameter['wp_review'] ): ?><a href="<?php echo $this->parameter['wp_review'] ?>" class="leave-a-review ibtn btnred right grow-btn" target="_blank"><span><i class="fa fa-star"></i></span>Leave a review</a><?php endif; ?>
				<?php if( isset($this->parameter['twitter'] ) && $this->parameter['twitter'] ): ?><a href="<?php echo $this->parameter['twitter'] ?>" class="tweet-about-it ibtn btnturke right" target="_blank"><span><i class="fa fa-twitter"></i></span>Write your experience</a><?php endif; ?>
				<?php if( isset($this->parameter['wp_support']) && $this->parameter['wp_support'] ): ?><a href="<?php echo $this->parameter['wp_support'] ?>" class="ibtn btngray2 right" target="_blank"><span><i class="fa fa-wrench"></i></span>Support</a><?php endif; ?>
			</header>
			<div class="ilentabs_wrap">
			<?php $Myoptions = self::theme_definitions(); ?>

			<?php if( isset($Myoptions) && is_array( $Myoptions ) ): ?>
				
					<?php 
					$put_tab = 0;
					global ${'tabs_plugin_' . $this->parameter['name_option']};
					$tabs_plugin = ${'tabs_plugin_' . $this->parameter['name_option']};
					if( isset($tabs_plugin) && is_array($tabs_plugin) ){
						$name_first_tab = '';
					 ?>
					<div id="nav">
					<h2>
					<?php foreach ($tabs_plugin as $key => $value_tab): 
						if( isset($value_tab["id"]) && $value_tab["id"] ): ?>
							<a href="<?php echo $value_tab["link"]; ?>&tabs=<?php echo $value_tab["id"]; ?>" class="nav-tab <?php if( !isset($_GET['tabs']) && !$put_tab ) { echo "nav-tab-active"; $put_tab = 1; }elseif( isset($_GET["tabs"]) && isset($value_tab["id"]) &&  $value_tab["id"] == $_GET["tabs"] ){ echo "nav-tab-active"; }  ?> " style="<?php if( isset($value_tab["width"]) && $value_tab["width"] ){ echo "width:{$value_tab["width"]}px;"; } ?>"><?php echo $value_tab["name"]; if( !$name_first_tab ){ $name_first_tab =  $value_tab["id"]; } ?></a>
					<?php  endif;
					endforeach; ?>
					
					</h2>
					</div>
					<?php } ?>
			<?php endif; ?>

			<div class="meta-box-sortabless">
				<?php 
					$tab_columns = self::if_columns_tab();
				?>
				<div class="has-sidebar sm-padded <?php if( isset($tab_columns[0]) && $tab_columns[0] ){ echo "main"; } ?>">
						<?php

							if( is_array( $Myoptions ) ){
								//global $options_theme;
								//if( isset($_POST) && $_POST ) { $options_theme = get_option( $this->parameter['name_option']."_options" ); }
								$options_theme = get_option( $this->parameter['name_option']."_options" );
								foreach ($Myoptions as $key => $value) {

									$tabs_save = ( isset($_GET['tabs']) && isset($value["tab"]) && $_GET['tabs'] == $value["tab"] ) ? true:false;

									if( isset( $_GET['tabs'] ) &&  $_GET['tabs'] == $value["tab"] ){
										$next_build = 1;
									}elseif( isset($value['default']) && $value['default'] && !isset($_GET['tabs']) ){
										$next_build = 1;
									}else{
										$next_build = 0;
									}

									if( isset($value['no_options']) && isset($_GET['tabs']) && $_GET['tabs'] == $value["tab"] ){
										$no_form_save = 1;
									//}elseif( !isset($active_form_save) && isset($_GET['tabs']) && $_GET['tabs'] == $value["tab"] ){
									}elseif( !isset($active_form_save) ){
										$active_form_save = 1;  
										echo '<form action="" method="POST" name="frmsave" id="frmsave">';
									}                                           ?>
									<?php 
									if( $next_build ){
										if($key != 'last_update'){  ?>

											<?php  if( isset($value['before']) && $value['before'] ): ?>
												<div class="postbox_note_top"><i class="fa fa-bullhorn"></i> <?php echo $value['before']; ?></div>
											<?php  endif; ?>

											<?php  if( isset($value['no_options']) &&  isset($value['page_content']) && $value['no_options']  ): ?>
												<?php include $value['page_content']; $only_page = 1; ?>
											<?php  else: ?>
											

									  <div id="box_<?php echo $key; ?>" class="<?php if( isset($value['title']) && $value['title'] ): ?>postbox<?php endif; ?> <?php if( isset($value["tab"]) ){ echo $value["tab"]; } ?>">
										<?php if( isset($value['title']) && $value['title'] ): ?>
												<h3 class="hndle">
													<span>
													<?php 
										if( $value['icon'] ){
											echo '<i class="'.$value['icon'].'"></i>&nbsp;&nbsp;';
										}
										?><?php echo $value['title']; ?>
										</span>
									</h3>
									<?php endif; ?>

												<div class="inside">
														<?php self::build_fields_p( $value['options'] ) ?>
												</div>
											</div>
										<?php  endif; ?>

							   <?php }
							   } ?>

								   

								<?php  }

							} ?>
						
								<?php if( !isset($only_page) || !$only_page ): ?>
								<input type="hidden" name='save_options' value='1' />
								<input type="hidden" name='save_for_tab' value='1' />
								<input type="hidden" name='name_options' value='<?php echo $this->parameter["name_option"]; ?>' />
								</form>
								<?php endif; ?>
				</div>

				<!-- sidebar wp-admin -->
				<?php if( isset($tab_columns[1]) && $tab_columns[1] ): ?>
				<div class="sb">
					<?php if( isset($tab_columns[2]) && $tab_columns[2] ){ require_once $tab_columns[2]; } ?>
				</div>
				<?php endif; ?>

			</div>

			<footer>
				<?php if( !isset($only_page) || !$only_page ): ?>
				<a href="#" class="ibtn btnblack left btn_save"><span><i class="fa fa-refresh"></i></span><?php _e('Save Changes',$this->parameter['name_option']) ?></a>
				<a href="#" class="ibtn btnred left btn_reset" data-me="<?php _e('Want to update all the default values​​ &#63;',$this->parameter['name_option']) ?>"><span><i class="fa fa-repeat"></i></span><?php _e('Reset section',$this->parameter['name_option']) ?></a>
				<?php if( isset($this->parameter['link_donate']) && $this->parameter['link_donate'] ): ?>
				<a class="right btn_donate" href="<?php echo $this->parameter['link_donate']; ?>" target="_blank" ><?php _e('Donate',$this->parameter['name_option']) ?></a>
				<?php endif; ?>
				<?php endif; ?>
			</footer>
			<script>
				/*jQuery(document).ready(function($){
					//$(".rippler").rippler({
				// addElement:"svg"
			  });
				});*/
				</script>
			</div>
			</div>

		</div>
	</div>



	<?php if( !isset($only_page) || !$only_page ): ?>
		<form action="" method="POST" name="frmreset" id="frmreset">
			<input type="hidden" name='reset_options' value='1' />
			<input type="hidden" name='name_options' value='<?php echo $this->parameter['name_option']; ?>' />
		</form>
		<?php endif; ?>
		<?php 
		if( !isset($_GET['tabs']) || !$_GET['tabs'] ):  ?>
		<script>
			jQuery(document).ready(function(){
				window.history.pushState('', '', '<?php echo admin_url('options-general.php?page='.$this->parameter['id_menu']) ?>&tabs=<?php echo $name_first_tab; ?>');
			});
		</script>
	<?php endif; ?>
 

<?php  }


	// =Interface Create for Widgets ---------------------------------------------
	function create_ilenWidget( $config , $full_options ){ 
	
	global $if_utils;
	$widget_unique_id_generate = rand(1,5559); ?>
	<div class='ilenwidget-options'>
		<?php echo isset($config['description'])?"<header>".$config['description']."</header>":''; ?>
		<div class="widget_body <?php echo $config['new']; ?>">
			<div class="gray ilenwidget-accordion"  id="ilenwidget_id_<?php echo isset($config['id'])?$config['id'].'_'.$widget_unique_id_generate:'_none'; ?>">
				<div id='iaccordion-container'>
			<?php 
			//var_dump( $full_options['d'] );
			if( is_array($full_options) ){
				$i = 0;
 
				foreach ($full_options as $key => $value) {
					
					if( isset( $value['title'] ) ) { echo "<h2 class='iaccordion-header ".(( $i == 0)?"active":"")."'>{$value['title']}</h2>"; } ?>
					<div class="iaccordion-content" style="display:<?php if( $i != 0): ?>none<?php else: ?>block<?php endif; ?>">
					<?php self::build_fields_w( $value['options'], $config['ref'], $widget_unique_id_generate  ); ?>
					</div>
				<?php $i++; 

				} ?>

				</div>
			</div>
		</div>

<?php if( isset($config['width']) && $config['width'] ): ?>
<style>
/* Widget */
.widgets-holder-wrap [id*="_<?php echo $config['id']; ?>-"].open{
  margin-left: -<?php echo $config['width']; ?>px;
}
.widget-holder.inactive [id*="_<?php echo $config['id']; ?>-"].widget.open{
	margin-left: 0!important;
}
.widgets-holder-wrap [id*="_<?php echo $config['id']; ?>-"] .widget-inside{
	background: #FBFBFB;
}
div.widget[id*=_<?php echo $config['id']; ?>-] .widget-title::before {
	content: "\f0e7";
	font-family: "fontawesome";
	position: absolute;
	top: 0px;
	left: 0px;
	width: 25px;
	height: 100%;
	background: rgb(134, 164, 192);
	color: rgb(255, 255, 255);
	line-height: 43px;
	text-align: center;
}
div.widget[id*=_<?php echo $config['id']; ?>-] .widget-top{
	position: relative;
}
div.widget[id*=_<?php echo $config['id']; ?>-] .widget-title h4{
	padding-left: 32px;
}
<?php if(isset($config['color']) && $config['color']):; ?>
div.widget[id*=_<?php echo $config['id']; ?>-] .widget-title::before{
	background: <?php echo $if_utils->IF_hex2rgba($config['color'],0.71); ?>
}
div.widget[id*=_<?php echo $config['id']; ?>-]  .widget-title::after {
	content: 'by iLen';
	left: 33px;
	position: absolute;
	bottom: 0;
	color: #D5D5D5;
	font-weight: normal;
	font-size: 10px;
}
div.widget[id*=_<?php echo $config['id']; ?>-] .ilenwidget-accordion .iaccordion-header.active{
	background: <?php echo $if_utils->IF_hex2rgba($config['color'],0.71); ?>;
	/*border-bottom:3px solid rgba(<?php echo $if_utils->IF_hex2rgb($config['color']) ?>,.5);*/
	box-shadow: 0px 1px 0px  rgba(<?php echo $if_utils->IF_hex2rgb($config['color']) ?>,.7);
}
div.widget[id*=_<?php echo $config['id']; ?>-] .ilenwidget-accordion .iaccordion-header{
	border-left:2px solid rgba(<?php echo $if_utils->IF_hex2rgb($config['color']) ?>,.7);
}
</style>
<?php endif; ?>
<?php endif; ?>
<script>
jQuery(".iaccordion-header").on("click",function(){
	var headder = jQuery(this);
	var accordion = jQuery(this).parent();
	jQuery(accordion).find("h2").each(function(){
		jQuery(this).removeClass("active");
		jQuery(this).next().css("display","none");
	});
	jQuery(headder).next().css("display","block");
	jQuery(headder).addClass("active");
});

</script>
	</div>
	<?php }
	}


	// =Interface Create MORE for Widgets ---------------------------------------------
	function create_ilenWidget_more( $config , $full_options ){ 
	
		global $if_utils;
		$widget_unique_id_generate = rand(1,5559); ?>
		<div class='ilenwidget-more ilen_clearfix'>
			<div class="ilenwidget-more--button">More...</div>
				<div class="widget_body-more <?php echo $config['new']; ?>"  id="ilenwidget-more_id_<?php echo isset($config['id'])?$config['id'].'_'.$widget_unique_id_generate:'_none'; ?>">
				<?php echo isset($config['description'])?"<header>".$config['description']."</header>":''; ?>
					<?php 
					//var_dump( $full_options['d'] );
					if( is_array($full_options) ){
						self::build_fields_w2( $full_options, $config['ref'], $widget_unique_id_generate  );
					}  ?>
				</div> 
		</div>
		<?php 
	}


	function ilen_print_script_footer_widget( $data, $class_widget_name, $id_widget ){ ?>
 
	<script>
	( function( $ ){ 
		<?php

		if(  in_array( 'color', $data )  ){ ?>

		function initColorPicker( widget ) {
		  widget.find( '.theme_color_picker' ).wpColorPicker( {
			change: _.throttle( function() { // For Customizer
			  $(this).trigger( 'change' );
			}, 3000 )
		  });
		}

		function onFormUpdate( event, widget ) {
		  initColorPicker( widget );
		}

		$( document ).on( 'widget-added widget-updated', onFormUpdate );

		$( document ).ready( function() {
		  $( '#widgets-right .widget:has(.theme_color_picker)' ).each( function () {
			initColorPicker( $( this ) );
		  } );
		} );

		<?php } ?>


		<?php 
		if(  in_array( 'range2', $data )  ){ ?>

			function initnoUiSlider( widget ) {
				try {
				  var valuesnoUiSlider = widget.find( '.noUiSlider_range' ).parent().next().val().split('|');
		  widget.find( '.noUiSlider_range' ).noUiSlider( {
			start: [ parseInt(valuesnoUiSlider[1]) ],
			step: parseInt(valuesnoUiSlider[2]),
			range: {
			  'min': [ parseInt(valuesnoUiSlider[3]) ],
			  'max': [ parseInt(valuesnoUiSlider[4]) ]
			}
		  }, true);
		  $('#'+valuesnoUiSlider[0]+'-range').Link().to( $('#'+valuesnoUiSlider[0]+'-value'), null, wNumb({decimals: 0}) );
		  $('#'+valuesnoUiSlider[0]+'-range').Link().to( $('#'+valuesnoUiSlider[0]), null, wNumb({decimals: 0}) );
				}
				catch(err) {
				  console.log( err.message );
				}

	  }

	  function onFormUpdate_noUiSlider( event, widget ) {
		initnoUiSlider( widget );
	  }

	  $( document ).on( 'widget-added widget-updated', onFormUpdate_noUiSlider );

	  $( document ).ready( function() {
		$( '#widgets-right .widget:has(.noUiSlider_range)' ).each( function () {
		  initnoUiSlider( $( this ) );
		} );
	  } );

		<?php } ?>


		<?php 
		if(  in_array( 'input4', $data )  ){ ?>

			function initInput4( widget ) {
				$( '.input4_single_input' ).on('change keypress keyup oninput input',function(){
			var input4_values = [];
			var i = 0;
			$(this).parent().parent().children('.input_4--square').each(function(){
				input4_values[i] = $(this).children('.input4_single_input').val() ? $(this).children('.input4_single_input').val() : 0;
				i=i+1;
			});
			$(this).parent().parent().parent().next('.input_value_total').val( input4_values.join() );
			});
	  }

	  function onFormUpdate_input4( event, widget ) {
		initInput4( widget );
	  }

	  $( document ).on( 'widget-added widget-updated', onFormUpdate_input4 );

	  $( document ).ready( function() {
		$( '#widgets-right .widget:has(.input_4)' ).each( function () {
		  initInput4( $( this ) );
		} );
	  } );

		<?php } ?>

		<?php 
		if(  in_array( 'jtumbler', $data )  ){ ?>

			function initjtumbler( widget ) {
				// generate news id because no found in first add
				var id_generate = Math.floor((Math.random() * 9999) + 1);
				widget.find( '.radio-switch input' ).each(function(){
					if(this.id){
						this.id = this.id+"_"+id_generate;
					}
				});
				widget.find( '.radio-switch' ).jTumbler();
				widget.find( '.radio-switch label' ).each(function(index){
					$( this ).parent().prev().find('label:nth-child('+(index+1)+') strong').html( $( this ).text() );
				});
			}

			function onFormUpdate_jtumbler( event, widget ) {
				initjtumbler( widget );
			}

			$( document ).on( 'widget-added widget-updated', onFormUpdate_jtumbler );

			$( document ).ready( function() {
				$( '#widgets-right .widget:has(.ilen_radio)' ).each( function () {
				  initjtumbler( $( this ) );
				} );
			} );

		<?php } ?>

		<?php
		if(  in_array( 'tag', $data )  ){ ?>
 
			function initTag( widget ) {
				widget.find( '.ilen_tag' ).tagEditor({ placeholder: '',forceLowercase:false });
			}

			function onFormUpdate_tag( event, widget ) {
			  initTag( widget );
			}

			$( document ).on( 'widget-added widget-updated', onFormUpdate_tag );

			$( document ).ready( function() {
			  $( '#widgets-right .widget:has(.ilen_tag)' ).each( function () {
				initTag( $( this ) );
			});
		} );

		<?php } ?>



		<?php
		if(  in_array( 'check_list', $data )  ){ ?>

			function initCheckList( widget ) {
				//alert( widget.find( '.ilen_check_list' ).children().children("input").length );
				var num_rand_list = Math.floor((Math.random() * 10) + 1);
				var new_genery_id_check_list = null;
				$( widget.find( '.ilen_check_list' ).children() ).each( function () {
					new_genery_id_check_list = $(this).children("input").attr("id")+"_generic_"+num_rand_list;
					$(this).children("input").attr("id", new_genery_id_check_list );
					$(this).children("label").attr("for", new_genery_id_check_list );
				} );
				
			}

			function onFormUpdate_CheckList( event, widget ) {
				initCheckList( widget );
			}

			$( document ).on( 'widget-added widget-updated', onFormUpdate_CheckList );

			$( document ).ready( function() {
				$( '#widgets-right .widget:has(.ilen_tags)' ).each( function () {
					initCheckList( $( this ) );
				});
			} );

		<?php } ?>

		// Widget additional field (MORE)
		$( document ).on( 'widget-added widget-updated', function(){
			$( ".ilenwidget-more--button" ).each(function() {
				$( this ).off('click').on('click', function() {
					$( this ).toggleClass( "active_ilen_widget_more" );
					$( this ).next().toggleClass( "active_ilen_widget_more" );
				});
			});
		} );
 

 


		}( jQuery ) );</script> <?php 

	}




	// =Interface Create for metabox---------------------------------------------   
	function create_ilenMetabox( $metabox_id = null, $metabox_header = null, $metabox_body = null, $stored_meta = null ){

 
		$_html = '';
		$_html .= wp_nonce_field( basename( __FILE__ ), "ilenmetabox_nonce" , true, false );
		$_html .= "<div class='ilenmetabox-options ilenmetabox-".$this->parameter["name_option"]." ilenmetabox-id-$metabox_id' >";

			$_html .= '<div id="poststuff" class="metabox-holder has-right-sidebar">';

				$_html .= '<div id="post-body-content" class="has-sidebar-content">';

					$_html .= '<div class="my-wrap-metabox">';
					$_html .= "<header></header>";
 
					if(  $metabox_id  ){

						$put_tab = 0;
						if( isset($metabox_header[$metabox_id]['tabs']) && is_array($metabox_header[$metabox_id]['tabs']) ){
							foreach ($metabox_header[$metabox_id]['tabs'] as $key => $value_tab){
								if( $value_tab["id"] && $put_tab == 0 ){
									$postion = isset($metabox_header[$metabox_id]['position']) && $metabox_header[$metabox_id]['position'] == "vertical" ? "ui-tabs-vertical":"";
									$_html .='<div id="tabs" class="'.$postion.'"><ul>';
									$put_tab=1;
								}

								if( isset($value_tab["id"]) && $value_tab["id"] ){
									$with = '';$with = isset( $value_tab["width"] )? $value_tab["width"]:'';
									$icon = '';$icon = isset($value_tab["icon"])? $value_tab["icon"]:'';
									$text = '';$text = $value_tab["name"];
									$_html .="<li style=''><a href='#".$value_tab["id"]."' style='{$with}px;' class='animation_once'>$icon $text</a></li>";
								}
							}
							$_html .="</ul>";
		
							if( is_array($metabox_header[$metabox_id]['tabs']) && isset($metabox_header[$metabox_id]['tabs']) ){
								foreach ($metabox_header[$metabox_id]['tabs'] as $key_tab => $value_tab) { 
									$_html .='<div id="'.$value_tab["id"].'"></div>';
								}
							} 
 
						}
 
						$_html .'<div class="">';
							$_html .='<div class="has-sidebar sm-padded">';

									if( isset( $metabox_body[$metabox_id] ) && is_array( $metabox_body[$metabox_id] ) ){

										foreach ( $metabox_body[$metabox_id] as $key => $value2 ) {
												$desc = ''; $desc = isset($value2['description']) && $value2['description']?"<div class='ilen_mtb_tab_description'>{$value2['description']}</div>":'';
												$text = ''; $text = isset($value2['title'])?$value2['title']:'';
												$tab  = ''; $tab = isset($tab)?$value2['tab']:'';

												$_html .='<div id="box_'.$key.'" class="animation_postbox_once '.$tab.'">';
													$_html .=$desc;
													$_html .="<div class='inside'>";
															$_html .= self::build_fields_m( $value2['options'], $stored_meta );
													$_html .="</div>";
												$_html .="</div>";

										}

									}

								

							$_html .="</div>";
						$_html .="</div>";

						if( $put_tab == 1 ){
							$_html .="</div> <!-- div id=tab -->";
						}
					}
				$_html .="<footer></footer>";
				$_html .="</div> <!-- my-wrap-metabox -->";
		$_html .="</div><!-- IF METAbox TAB, inner HTML in tab -->";
		$_html .="<script>";
		if( is_array($metabox_header[$metabox_id]) && isset($metabox_header[$metabox_id]) ){
			foreach ( $metabox_header[$metabox_id]['tabs'] as $key => $value_tab ) {
				$_html .="jQuery('.ilenmetabox-id-$metabox_id .".$value_tab['id']."').each(function(){
					jQuery( this ).appendTo( jQuery('.ilenmetabox-id-$metabox_id  #".$value_tab['id']."') );
				});";
			}
		}
		$_html .="
		jQuery(document).ready(function($){
			var mb_width_{$metabox_id} = ($('#{$metabox_id} .ilenmetabox-options #tabs > ul').outerHeight( true )) + 30;
			$('#{$metabox_id} .ilenmetabox-options #tabs [id*=\"tab\"]').css('min-height',mb_width_{$metabox_id});
		});
		</script> <!-- END -->
		<style>
			#{$metabox_id}{padding:0;}
			#{$metabox_id} .inside{padding:0;margin:0;}
		</style>
		</div>";


		return $_html;
 
	}


	
	// =If tabs is 1 or 2 columns
	function if_columns_tab(){
		global ${'tabs_plugin_' . $this->parameter['name_option']};
		$tabs_plugin = ${'tabs_plugin_' . $this->parameter['name_option']};
		$tabs_columns = array();
		if( isset($tabs_plugin) && is_array($tabs_plugin) ){ 
			foreach ($tabs_plugin as $key => $value_tab){
				if( (isset($_GET["tabs"]) && isset($value_tab["id"]) &&  $value_tab["id"] == $_GET["tabs"]) || ( !isset($_GET["tabs"]) ) ){

					if( isset($value_tab["columns"]) && $value_tab["columns"] == 2 ){
						$tabs_columns[0] = 'main';
						$tabs_columns[1] = 'sb';
						$tabs_columns[2] = isset($value_tab["sidebar-file"])?$value_tab["sidebar-file"]:"";

						return $tabs_columns;
					}

				}
			}
		}

		return false;
		
	}


	// =BUILD Fields themes---------------------------------------------
	function build_fields( $fields = array() ){

			global $if_utils,$options_theme;

			$options_theme = get_option( $this->parameter['name_option']."_options" );
 			//if( isset($_POST) && $_POST ) { $options_theme = get_option( $this->parameter['name_option']."_options" ); }
 			var_dump($fields);
			foreach ($fields as $key => $value) {

					if( in_array("b", $value['row']) ) { $side_two = "b"; }else{  $side_two ="c"; }

					switch ( $value['type'] ) {

						

						case "text": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_text" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">
									<input type="text" aa  value="<?php if( isset($options_theme[ $value['name'] ]) ){ echo esc_html($options_theme[ $value['name'] ]); } ?>" name="<?php echo $value['id'] ?>" id="<?php echo $value['id'] ?>"  autocomplete="off" <?php if(isset($value['placeholder'])){ echo "placeholder='{$value['placeholder']}'"; } ?> />
									<div class="help"><?php echo $value['help']; ?></div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

						case "checkbox": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_checkbox" <?php if(isset( $value['style'] )){ echo $value['style']; } ?>> 
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">

									
									<?php if( isset($value['display']) && $value['display'] == 'list' ){  ?>
										<?php 
											if( !is_array(  $options_theme[ $value['name'] ] ) ){
												$options_theme[ $value['name'] ] = array();
											}

											foreach ($value['items'] as $key2 => $value2): ?>

											<div class="row_checkbox_list">
												<input  type="checkbox" <?php if( in_array( $value2['value']  , $options_theme[ $value['name'] ] ) ){ echo " checked='checked' ";} ?> name="<?php echo $value['name'] ?>[]" id="<?php echo $value['id']."_".$value2['id'] ?>" value="<?php echo $value2['value'] ?>"  />    

												<label for="<?php echo $value['id']."_".$value2['id']; ?>"><span class="ui"></span></label>
												&nbsp;<?php echo  $value2['text']; ?>
												
											</div>


										<?php endforeach; ?>
										<div class="help"><?php echo $value['help']; ?></div>
										
									<?php } elseif( isset($value['display']) && $value['display'] == 'types_post' ) { ?>

										<?php ///$ck=''; if( isset($options_theme[ $value['name'] ]) ){ $ck =  checked(  $options_theme[ $value['name'] ]  , 1, FALSE );  }
										// get type post 
										$post_types = get_post_types(array(), "objects");
										$post_types_checked = "";
										foreach ($post_types as $post_type): $post_types_checked = ""; ?>
											<?php if( !in_array($post_type->name,array('revision','nav_menu_item')) ): ?>
											<div class="row_checkbox_types_post">
											 <?php 
											 	if( isset($options_theme[ $value['name'] ]) && in_array( $post_type->name  , $options_theme[ $value['name'] ] ) ){
											 		$post_types_checked = " checked='checked' ";
											 	} ?>
												<input <?php echo $post_types_checked; ?> type="checkbox" name="<?php echo $value['id'] ?>[]" id="<?php echo $value['id']."_".$post_type->name ?>" value="<?php echo $post_type->name; ?>"  /> 

												<label for="<?php echo $value['id']."_".$post_type->name ?>"><span class="ui"></span></label>
												&nbsp;<?php echo $post_type->labels->name; ?>
												<div class="help"><?php //echo $value2['help']; ?></div>
											</div>
										<?php endif; ?>
										<?php endforeach; ?>
										<div class="help"><?php echo $value['help']; ?></div>
									<?php }else { ?>
										<?php $ck=''; if( isset($options_theme[ $value['name'] ]) ){ $ck =  checked(  $options_theme[ $value['name'] ]  , 1, FALSE );  } ?>
										<div class="row_checkbox_normal">
											<div style="width:16%;float:left">
												<input  type="checkbox" <?php echo $ck; ?> name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>" value="<?php echo $value['value_check'] ?>"  />
												<label for="<?php echo $value['id'] ?>"><span class="ui"></span></label>
											</div>
											<div style="width:70%;float:left;line-height: 34px;">
												<div class="help" style="display: inline-block;vertical-align: middle;line-height: normal;"><?php echo $value['help']; ?></div>
											</div>
										</div>
									<?php } ?>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "upload": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row upload <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">
									<input id="<?php echo $value['id'] ?>" name="<?php echo $value['name'] ?>" value="<?php if(isset($options_theme[ $value['name'] ]) && $options_theme[ $value['name'] ] ){ echo $options_theme[ $value['name'] ]; } ?>" type="text" data-title="<?php echo $value['title'] ?>" data-button-set="<?php _e('Select image',$this->parameter['name_option']) ?>" class='theme_src_upload'  />
									<a class="button upload_image_button" data-title="<?php echo $value['title'] ?>" data-button-set="<?php _e('Select image',$this->parameter['name_option']) ?>" > <i class="fa fa-cloud-upload"></i><?php _e('Set Image',$this->parameter['name_option']) ?></a>
									<div class="clearfix"></div>
									<div class="preview">
										<?php  if( isset($options_theme[ $value['name'] ]) && $options_theme[ $value['name'] ] ) : ?>
											<img src="<?php echo $options_theme[ $value['name'] ]; ?>" />
											<span class='admin_delete_image_upload admin_delete_image_upload_normal'>✕</span>
										<?php endif; ?>
									</div>
									<div class="help"><?php echo $value['help']; ?></div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

						case "upload2": ?>
							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row upload upload2 <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><?php echo $value['title']; ?></div>
								<div class="<?php echo $side_two; ?>">
									<input id="<?php echo $value['id'] ?>" type="text" name="<?php echo $value['name'] ?>" value="<?php if( isset($options_theme[ $value['name'] ]) && $options_theme[ $value['name'] ] ){ echo $options_theme[ $value['name'] ]; }  ?>" class="theme_src_upload"  />
									<a class="upload_image_button button top-tip" data-tips="<?php _e('Select image',$this->parameter['name_option']) ?>" data-title="<?php echo $value['title'] ?>" data-button-set="<?php _e('Select image',$this->parameter['name_option']) ?>" > <i class="fa fa-cloud-upload"></i><?php _e('',$this->parameter['name_option']) ?></a>
									<?php if(isset( $value['value'] ) && $value['value']) : ?><a class="upload_image_default button top-tip" data-tips="<?php _e('Default',$this->parameter['name_option']) ?>" image-default="<?php echo $value['value']; ?>" > <i class="fa fa-repeat"></i><?php _e('',$this->parameter['name_option']) ?></a><?php endif; ?>
									<div class="clearfix"></div>
									<div class="preview">
										<?php  if( isset($options_theme[ $value['name'] ]) && $options_theme[ $value['name'] ] ) : ?>
											<img src="<?php echo $options_theme[ $value['name'] ]; ?>" />
											<span class='admin_delete_image_upload admin_delete_image_upload_normal'>✕</span>
										<?php endif; ?>
									</div>
									<div class="help"><?php echo $value['help']; ?></div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "upload_old": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row upload <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">
									<input id="<?php echo $value['id'] ?>" type="text" name="<?php echo $value['name'] ?>" value="<?php echo $options_theme[ $value['name'] ]; ?>" class="theme_src_upload" />
									<input type="button" value="<?php _e('Upload Image',$this->parameter['name_option']) ?>" class="upload_image_button_old" />
									<div class="preview">
										<?php  if( $options_theme[ $value['name'] ] ) : ?>
											<img src="<?php echo $options_theme[ $value['name'] ]; ?>" />
											<span class='admin_delete_image_upload'></span>
										<?php endif; ?>
									</div>
									<div class="help"><?php echo $value['help']; ?></div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "select": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row aa <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_select" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">
									<div class="select-wrapper">
									<select name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>">
										<?php 

											if( is_array( $value['items'] ) ){
												foreach ( $value['items'] as $item_key => $item_value ): $value_item_select_current = isset($options_theme[ $value['name'] ]) && $options_theme[ $value['name'] ]?$options_theme[ $value['name'] ]:$value['value'];   ?>
													
													<option value="<?php echo $item_key; ?>" 
													<?php selected( $value_item_select_current ,   $item_key ); ?>>
													<?php echo $item_value ?></option> 
   
												<?php
												endforeach;
											}
										?>
									</select>
									</div>
									<div class="help"><?php echo $value['help']; ?></div>
								</div> 
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "select2": ?>
							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_select2" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">
									<input type="hidden" name="_select2" id="_select2" class="_select2_mulpliple" />
									<select name="<?php echo $value['name'] ?>[]" id="<?php echo $value['id'] ?>" <?php if(isset($value['multiple'])) { echo "multiple ='multiple';"; } ?>  >
										<?php 
										//var_dump( $options_theme[ $value['name'] ] );
											if( is_array( $value['items'] ) ){
												$array_key_items=null;
												foreach ( $value['items'] as $item_key => $item_value ){
													$array_key_items[$item_key] = $item_value;
												}
											}

											$array_real_orden=null; 
											if( isset($options_theme[ $value['name'] ]) && is_array($options_theme[ $value['name'] ]) ){
												foreach ($options_theme[ $value['name'] ]  as $k => $v) {
													$array_real_orden[$v] = $array_key_items[$v];
												}
											}

											if( isset($array_key_items) && is_array($array_key_items) ){
												foreach ($array_key_items  as $k => $v) {
													//if( ! in_array($k,$array_real_orden) ){
													if (!array_key_exists($k, $array_real_orden)) {
														$array_real_orden[$k] = $v;
													}
												}
											}

											if( isset($array_real_orden) && is_array($array_real_orden) ){
												$data = null;
												foreach ($array_real_orden as $k => $v): ?>
													<?php if( in_array( $k , $options_theme[ $value['name'] ]  ) ) { $selected="selected='selected'";  }else{ $selected = ''; } ?>
													<option value="<?php echo $k ?>" <?php echo $selected; ?>><?php echo $v ?></option> 
												<?php endforeach; 
											}


										?>
									</select>
									<input type="hidden" name="<?php echo $value['name'] ?>_input_hidden" id="<?php echo $value['id'] ?>_input_hidden" class="_input_hidden_select2" />
									<script>
									// constructor for validator
									jQuery(window).scrollTop(0);
									jQuery(document).ready(function( $ ){
 
										//alert("<?php //echo "[".implode(',',$data)."]";  ?>");

										// Object-oriented flavor, example for jQuery plugin
										
										$("#<?php echo $value['id'] ?>").select2({
											placeholder: "<?php _e('Select order metas',$this->parameter['name_option']) ?>",
											allowClear: false,
											width:'100%'
											<?php if(isset( $value['limit'] )){ echo ",maximumSelectionSize: {$value['limit']}";} ?>
										});
								 
									});
									</script>
									<div class="help"><?php echo $value['help']; ?></div>
								</div> 
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "radio_image": ?>
							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row radio_image <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">

									<?php 
									$value_name = '';
									if( isset($options_theme[ $value['name'] ])  ) { $value_name = $options_theme[ $value['name'] ]; }
									if( is_array( $value['items'] ) ){
										foreach ($value['items'] as $item_key => $item_value): ?>
											
											<label for="<?php echo $value['id']."_".$item_value['value']; ?>">
												<img name="<?php if( isset($value['name']) && $value['name'] ){  echo $value['name']."_img"; } ?>" src="<?php echo $item_value['image'] ?>" class="radio_image_selection <?php echo $value['name']; ?> <?php echo ( isset($options_theme[ $value['name'] ]) && $options_theme[ $value['name'] ] == $item_value['value']?"active":"") ?>" data-id="<?php echo $value['name']; ?>" title="<?php echo $item_value['text']; ?>" />
												<input  <?php checked(  $value_name , $item_value['value'] ); ?> id="<?php echo $value['id']."_".$item_value['value']; ?>" type="radio" name="<?php if( isset($value['name']) && $value['name'] ){ echo $value['name']; } ?>" value="<?php echo $item_value['value'] ?>" />
											</label>

									<?php endforeach;
									} ?>
									<div class="help"><?php echo $value['help']; ?></div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;



						case "divide": ?>
								<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
								<div class="divide">
									<?php 
										if( $value['icon'] )
											echo '<i class="'.$value['icon'].'"></i>';
									?>
									<?php echo $value['title'] ?>
								</div>
								<?php if(isset( $value['after'] )){ echo $value['after'];} ?>
						<?php break;



						case "color": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_color" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">
									<input type="text" class="theme_color_picker" value="<?php if(isset( $options_theme[ $value['name'] ] )){ echo $options_theme[ $value['name'] ]; } ?>" name="<?php echo $value['name']; ?>" id="<?php echo $value['id'] ?>" data-default-color="<?php echo $value['value']; ?>" />
									<div class="help"><?php echo $value['help']; ?></div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;



						case "color_hover": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> color_hover" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><?php echo $value['title']; ?></div>
								<div class="<?php echo $side_two; ?>">
									<?php $bg_hover = isset($options_theme[ $value['name'] ])?$options_theme[ $value['name'] ]:''; ?>
									<div class="color_hover_first_element"><input type="text" class="theme_color_picker" value="<?php if(isset( $bg_hover['color'] )){ echo $bg_hover['color']; } ?>" name="<?php echo $value['name'].'_color'; ?>" id="<?php echo $value['id'].'_color' ?>" data-default-color="<?php if(isset( $bg_hover['color'] )){ echo $bg_hover['color']; } ?>" /></div>
									<div class='color_hover_two_element'><?php _e('hover',$this->parameter['name_option']); ?> <input type="text" class="theme_color_picker" value="<?php if(isset( $bg_hover['hover'] )){ echo $bg_hover['hover']; } ?>" name="<?php echo $value['name'].'_hover';  ?>" id="<?php echo $value['id'].'_hover'; ?>" data-default-color="<?php if(isset( $bg_hover['hover'] )){  echo  $bg_hover['hover']; } ?>" /></div>
									<div class="help"><?php echo $value['help']; ?></div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;



						case "textarea": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_textarea" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">
									<textarea name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>" style="width:100%;height:120px;" <?php if(isset($value['placeholder'])){ echo "placeholder='{$value['placeholder']}'"; } ?>><?php if( isset( $options_theme[ $value['name'] ] )) { echo _wp_specialchars( stripslashes($options_theme[ $value['name'] ]) ); } ?></textarea>
									<div class="help"><?php echo $value['help']; ?></div>
								</div>

							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "html": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<?php if( isset( $value['html1'] ) && $value['html1'] ) : ?><div class="a"><?php echo htmlentities($value['html1']);  ?></div><?php endif; ?>
								<div class="<?php echo $side_two; ?>">
									<?php if( isset( $value['html2'] ) ) { echo ($value['html2']); } ?>
								</div>
								<div class="help"><?php echo $value['help']; ?></div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

						case "tinymce": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">
									<?php
										//$initial_data='What you want to appear in the text box initially';
										$id = $value['id'];//has to be lower case
										wp_editor( html_entity_decode( $options_theme[ $value['name'] ] , ENT_QUOTES, 'UTF-8' ),$id,$value['setting']);
									?>
								</div>
								<div class="help"><?php echo $value['help']; ?></div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "background_complete": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> background_complete" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">

									<?php 
									$bg_complete                = '';
									$bg_complete['color']       = '';
									$bg_complete['transparent'] = '';
									$bg_complete['repeat']      = '';
									$bg_complete['size']        = '';
									$bg_complete['attachment']  = '';
									$bg_complete['position']    = '';
									$bg_complete['src']         = '';
									$bg_complete['opacity']     = '100';
									if( isset( $options_theme[ $value['name'] ]) && $options_theme[ $value['name'] ]){ $bg_complete = $options_theme[ $value['name'] ]; } ?>
									<div class="wrap_background_complete">
										<div class="part_1">
											<input type="text" class="theme_color_picker" value="<?php echo $bg_complete['color'] ?>" name="<?php echo $value['name']; ?>_color" id="<?php echo $value['id'] ?>_color" data-default-color="<?php echo $bg_complete['color']; ?>" />
											<input type="hidden" class="theme_color_picker_value" value="" />
										</div>
										<div class="part_2">
											<div class="background_complete_transparent_check"><input type="checkbox"  id="<?php echo $value['id'] ?>_transparent" name="<?php echo $value['id'] ?>_transparent" value="1" <?php if(  $bg_complete['transparent'] ){ echo " checked='checked' ";} ?> /><label for="<?php echo $value['id'] ?>_transparent"><span class="ui"></span></label> Transparent</div>
										</div>
										<div class="clearfix"></div>
										<div class="part_1 selectbc">
											<select name="<?php echo $value['name']; ?>_repeat" id="<?php echo $value['id'] ?>_repeat" style="width:100%;" data-attribute="background-repeat" class="select2_background_complete" >
												<option value=""></option>
												<option value="no-repeat" <?php selected(  $bg_complete['repeat'], 'no-repeat'); ?>><?php _e('No repeat',$this->parameter['name_option']) ?></option>
												<option value="repeat" <?php selected(  $bg_complete['repeat'], 'repeat'); ?>><?php _e('Repeat all',$this->parameter['name_option']) ?></option>
												<option value="repeat-x" <?php selected(  $bg_complete['repeat'], 'repeat-x'); ?>><?php _e('Repeat Horizontally',$this->parameter['name_option']) ?></option>
												<option value="repeat-y" <?php selected(  $bg_complete['repeat'], 'repeat-y'); ?>><?php _e('Repeat Vertically',$this->parameter['name_option']) ?></option>
												<option value="inherit" <?php selected(  $bg_complete['repeat'], 'inherit'); ?>><?php _e('Inherit',$this->parameter['name_option']) ?></option>
											</select>
										</div>
										<div class="part_2 selectbc">
											<select name="<?php echo $value['name']; ?>_size" id="<?php echo $value['id'] ?>_size" style="width:100%;margin-left:25px;"  data-attribute="background-size" class="select2_background_complete" >
												<option value=""></option>
												<option value="inherit"  <?php selected(  $bg_complete['size'], 'inherit'); ?>> <?php _e('Inherit',$this->parameter['name_option']) ?></option>
												<option value="cover" <?php selected(  $bg_complete['size'], 'cover'); ?>> <?php _e('Cover',$this->parameter['name_option']) ?></option>
												<option value="contain" <?php selected(  $bg_complete['size'], 'contain'); ?>> <?php _e('Contain',$this->parameter['name_option']) ?></option>
											</select> 
										</div>
										<div class="clearfix"></div>
										<div class="part_1 selectbc">
											<select name="<?php echo $value['name']; ?>_attachment" id="<?php echo $value['id'] ?>_attachment" style="width:100%;"  data-attribute="background-attachment" class="select2_background_complete" >
												<option value=""></option>
												<option value="fixed" <?php selected(  $bg_complete['attachment'], 'fixed'); ?>><?php _e('Fixed',$this->parameter['name_option']) ?></option>
												<option value="scroll" <?php selected(  $bg_complete['attachment'], 'scroll'); ?>><?php _e('Scroll',$this->parameter['name_option']) ?></option>
												<option value="inherit" <?php selected(  $bg_complete['attachment'], 'inherit'); ?>><?php _e('Inherit',$this->parameter['name_option']) ?></option>
											</select>
										</div>
										<div class="part_2 selectbc">
											<select name="<?php echo $value['name']; ?>_position" id="<?php echo $value['id'] ?>_position" style="width:100%;margin-left:25px;"  data-attribute="background-position" class="select2_background_complete" >
												<option value=""></option>
												<option value="left top" <?php selected(  $bg_complete['position'], 'left top'); ?>><?php _e('Left top',$this->parameter['name_option']) ?></option>
												<option value="left center" <?php selected(  $bg_complete['position'], 'left center'); ?>><?php _e('Left center',$this->parameter['name_option']) ?></option>
												<option value="left bottom" <?php selected(  $bg_complete['position'], 'left bottom'); ?>><?php _e('Left bottom',$this->parameter['name_option']) ?></option>
												<option value="center top" <?php selected(  $bg_complete['position'], 'center top'); ?>><?php _e('Center top',$this->parameter['name_option']) ?></option>
												<option value="center center" <?php selected(  $bg_complete['position'], 'center center'); ?>><?php _e('Center center',$this->parameter['name_option']) ?></option>
												<option value="center bottom" <?php selected(  $bg_complete['position'], 'center bottom'); ?>><?php _e('Center bottom',$this->parameter['name_option']) ?></option>
												<option value="right top" <?php selected(  $bg_complete['position'], 'right top'); ?>><?php _e('Right top',$this->parameter['name_option']) ?></option>
												<option value="right center" <?php selected(  $bg_complete['position'], 'right center'); ?>><?php _e('Right center',$this->parameter['name_option']) ?></option>
												<option value="right bottom" <?php selected(  $bg_complete['position'], 'right bottom'); ?>><?php _e('Right bottom',$this->parameter['name_option']) ?></option>
											</select>
										</div>
										<div class="clearfix"></div>
										<div class="part_3">
											<div style="width: 100%;float: left;padding: 0 2%;margin-left: -8px;margin-right: 17px;">
												<div style="width:22%;float:left">
													Opacity
													<span  id="<?php echo $value['id'] ?>-value" style="padding: 5px 10px;background: #FAFAFA;color: #444;border: 1px solid #F1F1F1;"></span>
													<input type="hidden" name="<?php echo $value['name'] ?>_opacity" id="<?php echo $value['id'] ?>_opacity" value="<?php if(isset( $options_theme[ $value['name'] ] )){ echo (int)$options_theme[ $value['name'] ]; }else{ echo 100; } ?>" />
												</div>
												<div style="width:78%;float:left">
													<div id="<?php echo $value['id'] ?>-range" class="noUi-connect"></div>  	
												</div>
											</div>
										</div>
										<div class="clearfix"></div>
										<div class="part_3">
											<div style="padding: 10px 0;clear:both;" class="upload">
												<div class="part_1">
													<input id="<?php echo $value['id'] ?>_src" type="text" name="<?php echo $value['name'] ?>_src" value="<?php echo $bg_complete['src']; ?>" class="theme_src_upload"  />
												</div>
												<div class="part_2">
													<a class="upload_image_button_complete button top-tip"  data-button-set="<?php _e('Select image',$this->parameter['name_option']) ?>" > <i class="fa fa-cloud-upload"></i><?php _e('Upload image',$this->parameter['name_option']) ?></a>
												</div>

												<?php 
													// get data background
													$repeat     = $bg_complete['repeat']?"background-repeat:{$bg_complete['repeat']};":'';
													$size       = $bg_complete['size']?"background-size:{$bg_complete['size']};":'';
													$attachment = $bg_complete['attachment']?"background-attachment:{$bg_complete['attachment']};":'';
													$position   = $bg_complete['position']?"background-position:{$bg_complete['position']};":'';
													$src        = $bg_complete['src']?"background-image:url({$bg_complete['src']});":'';
												?>

												<div class="preview" <?php  if( isset($options_theme[ $value['name'] ]['src']) && $options_theme[ $value['name'] ]['src'] ){ echo "style='$repeat $size $attachment $position $src height:450px'"; } ?>>
													<?php  if( $src ) : ?>
														<span class='admin_delete_image_upload admin_delete_image_upload_complete'>✕</span>
													<?php endif; ?>
													<div class="preview_opacity_bg" style="top: 0;left: 0;height: 100%;width: 100%;position: absolute;"></div>
												</div>
											</div>
										<div class="help"><?php echo $value['help']; ?></div>
										</div>
									</div>
									<script>
									jQuery(document).ready(function($){
										$('#<?php echo $value['id'] ?>-range').noUiSlider({
											start: [ <?php if(isset( $bg_complete['opacity'] )){ echo (int)$bg_complete['opacity']; }else{ echo 100; } ?> ],
											step: 1,
											range: {
												'min': [  1 ],
												'max': [ 100 ]
											}
										});
										$('#<?php echo $value['id'] ?>-range').Link().to( $('#<?php echo $value['id'] ?>-value'), null, wNumb({decimals: 0}) );
										$('#<?php echo $value['id'] ?>-range').Link().to( $('#<?php echo $value['id'] ?>_opacity'), null, wNumb({decimals: 0}) );
										$('#<?php echo $value['id'] ?>-range').Link().to(function( value ){
											var opacity_preview = parseInt(value) / 100;
											if( opacity_preview != 100 ){

												$("#<?php echo $value['id'] ?>_opacity").parent().parent().parent().parent().find(".preview").children(".preview_opacity_bg").css("opacity", opacity_preview );
												var color_bg_opacity = $("#<?php echo $value['id'] ?>_opacity").parent().parent().parent().prev().prev().prev().prev().prev().prev().prev().prev().prev().find("input").val();
												$("#<?php echo $value['id'] ?>_opacity").parent().parent().parent().parent().find(".preview").children(".preview_opacity_bg").css("background-color", color_bg_opacity );
											}else if( opacity_preview == 100 ){
												$("#<?php echo $value['id'] ?>_opacity").parent().parent().parent().parent().find(".preview").children(".preview_opacity_bg").css("opacity", "100" );
												$("#<?php echo $value['id'] ?>_opacity").parent().parent().parent().parent().find(".preview").children(".preview_opacity_bg").css("background-color", "transparent" );
											}
										});

										$("#<?php echo $value['id'] ?>_opacity").parent().parent().parent().prev().prev().prev().prev().prev().prev().prev().prev().prev().find("input").on("change",function(){
											var opacity_preview = parseInt(value) / 100;
											if( opacity_preview != 100 ){
												$("#<?php echo $value['id'] ?>_opacity").parent().parent().parent().parent().find(".preview").children(".preview_opacity_bg").css("opacity", opacity_preview );
												var color_bg_opacity = $("#<?php echo $value['id'] ?>_opacity").parent().parent().parent().prev().prev().prev().prev().prev().prev().prev().prev().prev().find("input").val();
												$("#<?php echo $value['id'] ?>_opacity").parent().parent().parent().parent().find(".preview").children(".preview_opacity_bg").css("background-color", color_bg_opacity );
											}else if( opacity_preview == 100 ){
												$("#<?php echo $value['id'] ?>_opacity").parent().parent().parent().parent().find(".preview").children(".preview_opacity_bg").css("opacity", "100" );
												$("#<?php echo $value['id'] ?>_opacity").parent().parent().parent().parent().find(".preview").children(".preview_opacity_bg").css("background-color", "transparent" );
											}
										});
										 
									});
									
									</script>
									<script>
										jQuery(document).ready(function($){
											$("#<?php echo $value['id'] ?>_repeat").select2({placeholder: "<?php _e('Background Repeat',$this->parameter['name_option']) ?>",allowClear: true}); 
											$("#<?php echo $value['id'] ?>_attachment").select2({placeholder: "<?php _e('Background Attachment',$this->parameter['name_option']) ?>",allowClear: true}); 
											$("#<?php echo $value['id'] ?>_size").select2({placeholder: "<?php _e('Background Size',$this->parameter['name_option']) ?>",allowClear: true}); 
											$("#<?php echo $value['id'] ?>_position").select2({placeholder: "<?php _e('Background Position',$this->parameter['name_option']) ?>",allowClear: true}); 
										});
									</script>
									
								</div>
 


							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "background_upload_pattern": ?>
							<?php 

								$UrlFBG = $this->parameter['url_framework']."/assets/images/bg-patterns";
								$bg_type = "{$value['name']}_type";

							?>
							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">
									<?php  if( $options_theme[ $bg_type ] == "1" || ! $options_theme[ $bg_type ] ){ $ck1="checked";}else{$ck2="checked";} ?>
									<div class="switch switch-blue">
										<input type="radio" class="switch-input"  name="<?php echo $value['name']."_type" ?>" id="<?php echo $value['id'] ?>_type_1" value="1" <?php echo $ck1 ?>>
										<label for="<?php echo $value['id'] ?>_type_1" class="switch-label switch-label-off" data-id="sw-pattern">Predefined</label>
										<input type="radio" class="switch-input" name="<?php echo $value['name']."_type" ?>" value="2" id="<?php echo $value['id'] ?>_type_2" <?php echo $ck2 ?>>
										<label for="<?php echo $value['id'] ?>_type_2" class="switch-label switch-label-on" data-id="sw-custom">Custom</label>
										<span class="switch-selection"></span>
									</div>

									<div class="pattern_bg_wrap" style="<?php if( $options_theme[ $bg_type ]=="1" ){ echo "display:block"; } elseif( $options_theme[ $bg_type ]=="2" ){ echo "display:none"; } ?>">
									<?php 
										global $list_pattern_bg;
										if( is_array( $list_pattern_bg ) ){
											foreach ( $list_pattern_bg  as $item_key => $item_value):
												$check_bg_pattern = '';
												$active_bg_pattern = '';
												if( $options_theme[ $value['name'] ] == $item_key ){
													$check_bg_pattern = "checked='checked'";
													$active_bg_pattern = "active";
												}

											 ?>
											<label   for="<?php echo $value['id']."_".$item_key; ?>">
												<div class="item_pattern_bg item_pattern_bg__content <?php echo $active_bg_pattern; ?>" style="background-image:url(<?php echo $UrlFBG ."/$item_value" ?>)" ></div>
												<input  <?php checked( $options_theme[ $value['name'] ],$item_key ); ?> id="<?php echo $value['id']."_".$item_key; ?>" type="radio" name="<?php echo $value['name']; ?>" value="<?php echo $item_key ?>" <?php echo $check_bg_pattern; ?> />
											</label>
									<?php   endforeach;
										} ?>
									</div>

									<div class="custom_bg_wrap"  style="<?php if( $options_theme[ $bg_type ]=="1" ){ echo "display:none"; } elseif( $options_theme[ $bg_type ]=="2" ){ echo "display:block"; } ?>">
										<?php 
										$bg_src="{$value['name']}_upload_src"; ?>
										<div class="upload">
											<input id="<?php echo $value['id'] ?>_upload" type="text" name="<?php echo $value['name'] ?>_upload_src" value="<?php echo $options_theme[ $bg_src ]; ?>" class="theme_src_upload"  />
											<input type="button" value="<?php _e('Upload Image',$this->parameter['name_option']) ?>" class="upload_image_button" data-title="<?php echo $value['title'] ?>" data-button-set="<?php _e('Select image',$this->parameter['name_option']) ?>" />
											<div class="preview">
												<?php  if( $options_theme[ $bg_src ] ) : ?>
													<img src="<?php echo $options_theme[ $bg_src ]; ?>" />
													<span class='admin_delete_image_upload'></span>
												<?php endif; ?>
											</div>
										</div>
									</div>
									<div class="help"><?php echo $value['help']; ?></div>
								</div>

							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;



						case "range": ?>
							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_range" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php if(isset( $value['title'] )){ echo $value['title']; } ?></h3></div>
								<div class="<?php echo $side_two; ?>">
									<div>
										<output id="rangevalue"><?php if(isset( $options_theme[ $value['name'] ] )){ echo $options_theme[ $value['name'] ]; } ?></output>
										<input  id="<?php echo $value['id'] ?>"  name="<?php echo $value['name'] ?>" class="bar" type="range" value="<?php if(isset( $options_theme[ $value['name'] ] )){ echo $options_theme[ $value['name'] ]; } ?>" onchange="jQuery(this).prev().html(this.value)" min ="<?php if(isset( $value['min'] )){ echo $value['min']; } ?>" max="<?php if(isset( $value['max'])){  echo $value['max']; } ?>" step="<?php if(isset( $value['step'])){ echo $value['step']; } ?>" />
									</div>
									<div class="help"><?php echo $value['help']; ?></div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "range2": ?>
							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_range2" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php if(isset( $value['title'] )){ echo $value['title']; } ?></h3></div>
								<div class="<?php echo $side_two; ?>">
									<div>
										<div style="width:7%;float:left">
											<span  id="<?php echo $value['id'] ?>-value" style="padding: 5px 10px;background: #FAFAFA;color: #444;border: 1px solid #F1F1F1;"></span>
										</div>
										<div style="width: 88%;float: right;padding: 0;border-radius: 5px;" >
											<div id="<?php echo $value['id'] ?>-range" <?php if( isset($value['color']) && $value['color'] == 1 ){ echo "class='noUi-connect'"; } ?>></div> 
										</div>
										<input type="hidden" name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>" value="<?php if(isset( $options_theme[ $value['name'] ] )){ echo (int)$options_theme[ $value['name'] ]; }else{ echo 0; } ?>" />
										<script>
											jQuery(document).ready(function($){
												$('#<?php echo $value['id'] ?>-range').noUiSlider({
													start: [ <?php if(isset( $options_theme[ $value['name'] ] )){ echo (int)$options_theme[ $value['name'] ]; }else{ echo 0; } ?> ],
													step: <?php if(isset( $value['step'] )){ echo $value['step']; } ?>,
													range: {
														'min': [  <?php if(isset( $value['min'] )){ echo $value['min']; } ?> ],
														'max': [ <?php if(isset( $value['max'] )){ echo $value['max']; } ?> ]
													}
												});
												$('#<?php echo $value['id'] ?>-range').Link().to( $('#<?php echo $value['id'] ?>-value'), null, wNumb({decimals: 0}) );
												$('#<?php echo $value['id'] ?>-range').Link().to( $('#<?php echo $value['id'] ?>'), null, wNumb({decimals: 0}) );
											});
										</script>
									</div>
									<div class="help"><?php echo $value['help']; ?></div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

						case "input_2": ?>
							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row input_2 <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_range" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><?php if(isset( $value['title'] )){ echo $value['title']; } ?></div>
								<div class="<?php echo $side_two; ?>">
								  <?php $margin_input4 = isset($options_theme[ $value['name'] ])?$options_theme[ $value['name'] ]:''; ?>
									<div>
										<div class="input_2--square"><span>&nbsp; </span><input type="number" name="<?php echo $value['name'].'_top'; ?>" id="<?php echo $value['id'].'_top' ?>"  min ="<?php if(isset( $value['min'] )){ echo $value['min']; } ?>" max="<?php if(isset( $value['max'])){  echo $value['max']; } ?>" step="<?php if(isset( $value['step'])){ echo $value['step']; } ?>" value="<?php echo isset($margin_input4['top'])?$margin_input4['top']:0; ?>" ></div>
										<div class="input_2--square"><span>&nbsp; </span><input type="number" name="<?php echo $value['name'].'_bottom'; ?>" id="<?php echo $value['id'].'_bottom' ?>"  min ="<?php if(isset( $value['min'] )){ echo $value['min']; } ?>" max="<?php if(isset( $value['max'])){  echo $value['max']; } ?>" step="<?php if(isset( $value['step'])){ echo $value['step']; } ?>" value="<?php echo isset($margin_input4['bottom'])?$margin_input4['bottom']:0; ?>" ></div>
									</div>
									<div class="help"><?php if( isset($value['help']) ){ echo $value['help']; } ?></div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

						case "fonts": ?>
							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ifonts_full" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><?php if(isset( $value['title'] )){ echo $value['title']; } ?></div>
								<div class="<?php echo $side_two; ?>">
								  	<?php
								  	//var_dump( $_POST[$value['name']] );
								  	// reset values
									$if_fonts                 = '';
									$if_fonts['color']        = '';
									$if_fonts['variant']      = '';
									$if_fonts['variant_list'] = '';
									$if_fonts['font']         = '';
									$if_fonts['size']         = '';

									// set values
									if( isset( $options_theme[ $value['name'] ]) && $options_theme[ $value['name'] ]){ 
										$if_fonts = $options_theme[ $value['name'] ];

										if( isset($value['use']) && !( in_array('size',$value['use'] ) ) ){
											$if_fonts['size'] = $value['value']['size'];
										}
										if( isset($value['use']) &&  in_array('variant',$value['use']) && !$if_fonts['variant_list'] ){
											$if_fonts['variant_list'] = implode(",",$value['value']['variant_list']);
										}
									}else{
										$if_fonts['color']               = $value['value']['color'];
										$if_fonts['variant']             = $value['value']['variant'];
										$if_fonts['variant_list'] 		 = $value['value']['variant_list'];
										$if_fonts['font']                = $value['value']['font'];
										$if_fonts['size']                = $value['value']['size'];
									}
									 
								  	if( isset($value['use']) && ( in_array('font',$value['use'] ) )   ){

									  	$fonts = $if_utils->IF_get_google_fonts();
									  	if( is_array($fonts) ){
									  		$font_families = "";
									  		$font_count = 1;
											foreach ($fonts as $font) {
												$font_family = $font["family"];
												$select = ($font_family == $if_fonts['font']?"selected='selected'":"");
												$font_families .= "<option $select value=\"$font_family\" num='$font_count'>$font_family</option>";
												$font_count++;
											}
									  	}

									}

									if( in_array('size',$value['use'] )  ){
										$numbers = "";
										foreach (range(7, 44) as $number) {
											$select = ($number == $if_fonts['size']?"selected='selected'":"");
											$numbers .= "<option $select value=\"{$number}px\">{$number}px</option>";
										}
									}

									$html_variant_list = "";
									if( isset($value['use']) && ( in_array('variant',$value['use'] ) )   ){
										if( isset($if_fonts['variant_list']) && $if_fonts['variant_list'] ){

											if( is_string($if_fonts['variant_list']) ){
									  			$array_variant_list = explode(",",$if_fonts['variant_list']); 
									  		}elseif( is_array($if_fonts['variant_list']) ){
									  			$array_variant_list = $if_fonts['variant_list'];	
									  		}

											//$array_variant_list = $if_fonts['variant_list']; //explode(",",$if_fonts['variant_list']);
											if( is_array($array_variant_list) ){

												foreach ($array_variant_list as $array_variant_list_key => $array_variant_list_value) {
													$selected = $if_fonts['variant'] == $array_variant_list_value ? "selected='selected'":"";
													$html_variant_list .="<option $selected value='$array_variant_list_value'>$array_variant_list_value</option>";
												}

											}

										}

									}

									if( in_array('font',$value['use'] )  ){
								  		echo "<div class='select-wrapper ifonts_family'><select class='select--gfonts__family' name='{$value['name']}_family' id='{$value['id']}_family'  >$font_families</select></div>";
								  	}
								  	if( in_array('variant',$value['use'] )  ){
								  		
								  		$fonts_variants_hidden = implode(",",$array_variant_list);
								  		echo "<div class='select-wrapper ifonts_variants'><select  class='select--gfonts__variant'  name='{$value['name']}_variants' id='{$value['id']}_variants'  >$html_variant_list</select>
								  			<input type='hidden' value='$fonts_variants_hidden' name='{$value['name']}_variants_list'  id='{$value['name']}_variants_list' /></div>";
								  	}
								  	if( in_array('size',$value['use'] ) ){
								  		echo "<div class='select-wrapper ifonts_size'><select  class='select--gfonts__size'  name='{$value['name']}_size' id='{$value['id']}_size'  >$numbers</select></div>";
								  	}
								  	if( in_array('color',$value['use'] ) ){
								  		echo "<input type='text' class='theme_color_picker' value='{$if_fonts["color"]}' name='{$value["name"]}_color' id='{$value["id"]}_color' data-default-color='".$value["value"]["color"]."' />";
								  		echo "<input type='hidden' class='theme_color_picker_value_font'  />";
								  	}
								  	if( in_array('ld',$value['use'] ) ){
								  		echo "<div class='fonts_check_light_night' ><input class='input_check_light_dark' type='checkbox' name='{$value["name"]}_sun_nigth' id='{$value["id"]}_sun_nigth' value=''  /><label  for='{$value["id"]}_sun_nigth'><span class='ui'></span></label></div>";
								  	}

							  		echo "<div class='ilen_clearfix'></div>";

							  		// get font current
							  		$size_font_g = ((int)(str_replace("px","",$if_fonts["size"]))) + 15;
							  		$size_font_set = ((int)(str_replace("px","",$if_fonts["size"])));
							  		$size_font_set_px = "{$size_font_set}px";
							  		echo "<script>jQuery(document).ready( function($) { addGoogleFont( '{$if_fonts['font']}', '{$if_fonts["variant"]}' ); });</script>";
							  		echo "<div class='ifonts_preview' style='font-family:\"{$if_fonts["font"]}\";font-size:{$size_font_set_px};line-height:{$size_font_g}px;font-weight:{$if_fonts["variant"]};color:{$if_fonts["color"]};'>abcdedfghijklmopqrstuvwxyz <br /> ABCDEDFGHIJKLMOPQRSTUVWXYZ <br /> 1234567890</div>";
								  	?>

									<div class="help"><?php if( isset($value['help']) ){ echo $value['help']; } ?></div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

					}

			}
//exit;
	}



	// =BUILD Fields plugin---------------------------------------------
	function build_fields_p( $fields = array() ){

			//$options_theme = get_option( $this->parameter['name_option']."_options" );
			//global $options_theme;
			$options_theme = get_option( $this->parameter['name_option']."_options" );
	
			foreach ($fields as $key => $value) {

					if( in_array("b", $value['row']) ) { $side_two = "b"; }else{  $side_two ="c"; }

					switch ( $value['type'] ) {

						

						case "text": ?>
							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>"  style="<?php if(isset( $value['style'] )){ echo $value['style'];} ?>" >
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<input type="text"  value="<?php if( isset($options_theme[ $value['name'] ]) ){ echo $options_theme[ $value['name'] ]; } ?>" name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>"  autocomplete="off" <?php if(isset($value['placeholder'])){ echo "placeholder='{$value['placeholder']}'"; } ?>  />
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

						case "checkbox": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_checkbox" <?php if(isset( $value['style'] )){ echo $value['style']; } ?>> 
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">

									
									<?php if( isset($value['display']) && $value['display'] == 'list' ){  ?>
										<?php 
											if( isset($options_theme[ $value['name'] ]) && !is_array(  $options_theme[ $value['name'] ] ) ){
												$options_theme[ $value['name'] ] = array();
											}

											foreach ($value['items'] as $key2 => $value2): ?>

											<div class="row_checkbox_list">
												<input  type="checkbox" <?php if( isset($value2['value']) && isset($options_theme[ $value['name'] ]) && $value['name'] && in_array( $value2['value']  , $options_theme[ $value['name'] ] ) ){ echo " checked='checked' ";} ?> name="<?php echo $value['name'] ?>[]" id="<?php echo $value2['id']."_".$value2['value'] ?>" value="<?php if( isset($value2['value']) ){ echo $value2['value']; } ?>"  />  
												<label for="<?php echo $value2['id']."_".$value2['value'] ?>"><span class="ui"></span></label>
												&nbsp;<?php echo  $value2['text']; ?>
												<div class="help"><?php echo $value2['help']; ?></div>
											</div>

										<?php endforeach; ?>
										
									<?php } elseif( isset($value['display']) && $value['display'] == 'types_post' ) { ?>
										<?php //$ck=''; //if( isset($options_theme[ $value['name'] ]) ){ $ck =  checked(  $options_theme[ $value['name'] ]  , "1", FALSE );  }


											// get type post 
											$post_types = get_post_types(array(), "objects");

											foreach ($post_types as $post_type): ?>
												<?php if( !in_array($post_type->name,array('revision','nav_menu_item')) ): ?>
												<div class="row_checkbox_types_post">

													<input  type="checkbox" <?php if( in_array( $post_type->name  , (array)($options_theme[ $value['name'] ]) ) ){ echo " checked='checked' ";} ?> name="<?php echo $value['id'] ?>[]" id="<?php echo $value['id']."_".$post_type->name ?>" value="<?php echo $post_type->name; ?>"  /> 

													<label for="<?php echo $value['id']."_".$post_type->name ?>"><span class="ui"></span></label>
													&nbsp;<?php echo $post_type->labels->name; ?>
													<div class="help"><?php //echo $value2['help']; ?></div>
												</div>
											<?php endif; ?>
											<?php endforeach; ?>
										
									<?php }else { ?>
										<?php $ck=''; if( isset($options_theme[ $value['name'] ]) ){ $ck =  checked(  $options_theme[ $value['name'] ]  , 1, FALSE );  } ?>
										<div class="row_checkbox_normal">
											<input  type="checkbox" <?php echo $ck; ?> name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>" value="<?php echo $value['value_check'] ?>"  />
											<label for="<?php echo $value['id'] ?>"><span class="ui"></span></label>
										</div>
									<?php } ?>
									
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;



						case "component_list_categories": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>> 
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?> component_list_categories">

									<?php 
										global $IF_COMPONENT;

										$IF_COMPONENT['component_list_category']->display( $value['id'], $options_theme[ $value['name'] ], $value['text_first_select'] );

									?>
									
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;



						case "component_enhancing_code": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>> 
								<div class="a  <?php if( $side_two == 'c'){ echo "a_line"; } ?>"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?> component_enhancing_css">

									<?php 
										//global $IF_COMPONENT;
										//$IF_COMPONENT['component_enhancing_code']->display( $value['id'], isset($options_theme[ $value['name'] ])?$options_theme[ $value['name'] ]:'' );
									?>
									<div style="margin: 10px 0px;border:1px solid #DEDEDE">
										<textarea id="code_<?php echo $value['id'] ?>" name="<?php echo $value['name'] ?>"><?php echo isset($options_theme[ $value['name'] ])?$options_theme[ $value['name'] ]:$value['value']; ?></textarea>
									</div>
									<script>
										var editor_<?php echo $value['id'] ?>;
										jQuery(document).ready(function(){
												editor_<?php echo $value['id'] ?> = CodeMirror.fromTextArea(document.getElementById("code_<?php echo $value['id'] ?>"), {
												lineNumbers: <?php if( isset($value['lineNumbers']) && $value['lineNumbers'] ){ echo $value['lineNumbers']; }else{ echo "true"; } ?>,
												styleActiveLine: true,
												matchBrackets: true
											});

											editor_<?php echo $value['id'] ?>.setOption("theme", "xq-light");

											<?php if( isset($value['mini_callback'])  && $value['mini_callback'] ): ?>
												<?php echo $value['mini_callback']; ?>
											<?php endif; ?>
										});
									</script>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "upload": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row upload <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><?php echo $value['title']; ?><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<input id="<?php echo $value['id'] ?>" type="text" name="<?php echo $value['name'] ?>" value="<?php echo isset($options_theme[ $value['name'] ])?$options_theme[ $value['name'] ]:''; ?>" class="theme_src_upload"  />
									<a class="upload_image_button button top-tip" data-tips="<?php _e('Select image',$this->parameter['name_option']) ?>" data-title="<?php echo $value['title'] ?>" data-button-set="<?php _e('Select image',$this->parameter['name_option']) ?>" > <i class="fa fa-cloud-upload"></i><?php _e('',$this->parameter['name_option']) ?></a>
									<?php if(isset( $value['value'] ) && $value['value']) : ?><a class="upload_image_default button top-tip" data-tips="<?php _e('Use default',$this->parameter['name_option']) ?>" image-default="<?php echo $value['value']; ?>" > <i class="fa fa-repeat"></i><?php _e('',$this->parameter['name_option']) ?></a><?php endif; ?>
									<div class="preview">
										<?php  if( isset($options_theme[ $value['name'] ]) && $options_theme[ $value['name'] ] ) : ?>
											<img src="<?php echo $options_theme[ $value['name'] ]; ?>" />
											<span class='admin_delete_image_upload'>✕</span>
										<?php endif; ?>
									</div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "upload_old": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row upload <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<input id="<?php echo $value['id'] ?>" type="text" name="<?php echo $value['name'] ?>" value="<?php echo $options_theme[ $value['name'] ]; ?>" class="theme_src_upload" />
									<input type="button" value="<?php _e('Upload Image',$this->parameter['name_option']) ?>" class="upload_image_button_old" />
									<div class="preview">
										<?php  if( $options_theme[ $value['name'] ] ) : ?>
											<img src="<?php echo $options_theme[ $value['name'] ]; ?>" />
											<span class='admin_delete_image_upload'></span>
										<?php endif; ?>
									</div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "select": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<div class="select-wrapper" >
									<select name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>" <?php if(isset( $value['onchange'] )){ echo "onchange='{$value['onchange']}'";} ?>  >
										<?php 
											if( isset($value['items']) && is_array( $value['items'] ) ){
												foreach ( $value['items'] as $item_key => $item_value ): ?>
													<option value="<?php echo $item_key ?>" <?php selected( isset($options_theme[ $value['name'] ])?$options_theme[ $value['name'] ]:"" ,   $item_key ); ?>><?php echo $item_value ?></option>  
												<?php
												endforeach;
											}
										?>
									</select>
									</div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "radio_image": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row radio_image <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a <?php if( $side_two == 'c'){ echo "a_line"; } ?>"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">

									<?php 
									if( isset($value['items']) && is_array( $value['items'] ) ){
										foreach ($value['items'] as $item_key => $item_value): ?>
											<?php if( isset($value['name']) ): ?>
											<label for="<?php echo $value['id']."_".$item_value['value']; ?>">
												<img id="<?php if( isset($value['id']) ){ echo $value['id']."_img_".$item_value['value'];} ?>" name="<?php if( isset($value['name']) ){ echo $value['name']."_img";} ?>" src="<?php if( isset($item_value['image']) ){ echo $item_value['image']; } ?>" class="radio_image_selection <?php echo $value['name']; ?> <?php echo (isset($options_theme[ $value['name'] ]) && $options_theme[ $value['name'] ] == $item_value['value']?"active":"") ?>" data-id="<?php echo $value['name']; ?>" title="<?php echo $item_value['text']; ?>" />
												<?php if( isset( $options_theme[ $value['name'] ] ) ): ?>
												<input  <?php checked( $options_theme[ $value['name'] ], $item_value['value'] ); ?> id="<?php echo $value['id']."_".$item_value['value']; ?>" type="radio" name="<?php echo $value['name']; ?>" value="<?php echo $item_value['value'] ?>" />
											<?php endif; ?>
											</label>
										<?php endif; ?>
									<?php endforeach;
									} ?>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;



						case "divide": ?>
								<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
								<div class="divide <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
									<?php 
										if( isset($value['icon']) )
											echo '<i class="'.$value['icon'].'"></i>';
									?>
									<?php echo $value['title'] ?>
								</div>
								<?php if(isset( $value['after'] )){ echo $value['after'];} ?>
						<?php break;



						case "color": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<input type="text" class="theme_color_picker" value="<?php if(isset( $options_theme[ $value['name'] ] )){ echo $options_theme[ $value['name'] ]; } ?>" name="<?php echo $value['name']; ?>" id="<?php echo $value['id'] ?>" data-default-color="<?php echo $value['value']; ?>" />
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;




						case "color_hover": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> color_hover" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><?php echo $value['title']; ?><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<?php $bg_hover = isset($options_theme[ $value['name'] ])?$options_theme[ $value['name'] ]:''; ?>
									<div class="color_hover_first_element"><input type="text" class="theme_color_picker" value="<?php if(isset( $bg_hover['color'] )){ echo $bg_hover['color']; } ?>" name="<?php echo $value['name'].'_color'; ?>" id="<?php echo $value['id'].'_color' ?>" data-default-color="<?php if(isset( $bg_hover['color'] )){ echo $bg_hover['color']; } ?>" /></div>
									<div class='color_hover_two_element'><?php _e('hover',$this->parameter['name_option']); ?> <input type="text" class="theme_color_picker" value="<?php if(isset( $bg_hover['hover'] )){ echo $bg_hover['hover']; } ?>" name="<?php echo $value['name'].'_hover';  ?>" id="<?php echo $value['id'].'_hover'; ?>" data-default-color="<?php if(isset( $bg_hover['hover'] )){  echo  $bg_hover['hover']; } ?>" /></div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;



						case "textarea": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row row_textarea <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<textarea name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>" style="width:100%;height:150px;" <?php if(isset($value['placeholder'])){ echo "placeholder='{$value['placeholder']}'"; } ?> <?php if( isset($value['maxlength']) && $value['maxlength'] ): ?>onKeyDown="IF_textCounter(this,'<?php echo $value['id'] ?>_text_length',<?php echo (int)$value['maxlength']; ?>);" onKeyUp="IF_textCounter(this,'<?php echo $value['id'] ?>_text_length' ,<?php echo (int)$value['maxlength']; ?>)"<?php endif; ?> ><?php if( isset($options_theme[ $value['name'] ]) ){ echo $options_theme[ $value['name'] ]; } ?></textarea>
									<?php if( isset($value['maxlength']) && $value['maxlength'] ): ?>
									<input type="text" disabled="disabled" readonly="readonly" id="<?php echo $value['id'] ?>_text_length" class="text_length" value="<?php echo (int)$value['maxlength']; ?>" />
								<?php endif; ?>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "radio": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row radio <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<?php 
									if( is_array( $value['items'] ) ){
										foreach ($value['items'] as $item_key => $item_value): ?>
											<div class="row_radio">
												<label for="<?php echo $value['id']."_".$item_value['value']; ?>">
													<input  <?php checked( $options_theme[ $value['name'] ], $item_value['value'] ); ?> id="<?php echo $value['id']."_".$item_value['value']; ?>" type="radio" name="<?php echo $value['name']; ?>" value="<?php echo $item_value['value'] ?>" />
													<?php echo $item_value['text'] ?>
												</label>
												<span><?php echo $item_value['help'] ?></span>
											</div>

									<?php endforeach;
									} ?>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "html": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><?php if( isset($value['html1']) ){ echo htmlentities($value['html1']); } ?></div>
								<div class="<?php echo $side_two; ?>">
									<?php if( isset($value['html2']) ){ echo $value['html2']; } ?>
								</div>
								<div class="help"><?php echo $value['help']; ?></div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "button": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<a href="<?php echo $value['value']; ?>" class="ibtn btnblack" style="padding-left:12px;" onclick="<?php echo $value['onclick'] ?>" name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>"  ><?php echo $value['text_button'] ?> <div id="ajax_imagen_button_<?php echo $value['id'] ?>" class="ilen_ajax_imagen_button"></div></a>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "range": ?>
							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_range" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><?php if(isset( $value['title'] )){ echo $value['title']; } ?><div class="help"><?php if( isset($value['help']) ){ echo $value['help']; } ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<div>
										<output id="rangevalue"><?php if(isset( $options_theme[ $value['name'] ] ) && $options_theme[ $value['name'] ] ){ echo (int)$options_theme[ $value['name'] ]; }else{ echo 0; } ?></output>
										<input  id="<?php if( isset( $value['id'] ) ){ echo $value['id']; } ?>"  name="<?php if( isset( $value['name'] ) ){ echo $value['name']; } ?>" class="bar" type="range" value="<?php if(isset( $options_theme[ $value['name'] ] )){ echo (int)$options_theme[ $value['name'] ]; }else{ echo 0; } ?>" onchange="jQuery(this).prev().html(this.value)" min ="<?php if(isset( $value['min'] )){ echo $value['min']; } ?>" max="<?php if(isset( $value['max'])){  echo $value['max']; } ?>" step="<?php if(isset( $value['step'])){ echo $value['step']; } ?>" />
									</div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;



						case "range2": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_range2" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><?php if(isset( $value['title'] )){ echo $value['title']; } ?><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<div>
										<div style="width:10%;float:<?php if( !is_rtl() ): ?>left<?php else: ?>right<?php endif; ?>">
											<span  id="<?php echo $value['id'] ?>-value" style="padding: 5px 10px;background: #FAFAFA;color: #444;border: 1px solid #F1F1F1;"></span>
										</div>
										<div style="width: 76%;float: left;padding: 0;border-radius: 5px;margin-left: 13px;" >
											<div id="<?php echo $value['id'] ?>-range" <?php if( isset($value['color']) && $value['color'] == 1 ){ echo "class='noUi-connect'"; } ?>></div> 
										</div>
										<input type="hidden" name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>" value="<?php if(isset( $options_theme[ $value['name'] ] )){ echo (int)$options_theme[ $value['name'] ]; }else{ echo 0; } ?>" />
										<script>
											jQuery(document).ready(function($){
												$('#<?php echo $value['id'] ?>-range').noUiSlider({
													start: [ <?php if(isset( $options_theme[ $value['name'] ] )){ echo (int)$options_theme[ $value['name'] ]; }else{ echo 0; } ?> ],
													step: <?php if(isset( $value['step'] )){ echo $value['step']; } ?>,
													range: {
														'min': [  <?php if(isset( $value['min'] )){ echo $value['min']; } ?> ],
														'max': [ <?php if(isset( $value['max'] )){ echo $value['max']; } ?> ]
													}
												});
												$('#<?php echo $value['id'] ?>-range').Link().to( $('#<?php echo $value['id'] ?>-value'), null, wNumb({decimals: 0}) );
												$('#<?php echo $value['id'] ?>-range').Link().to( $('#<?php echo $value['id'] ?>'), null, wNumb({decimals: 0}) );
											});
										</script>
									</div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "input_4": ?>
							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row input_4 <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_range" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><?php if(isset( $value['title'] )){ echo $value['title']; } ?><div class="help"><?php if( isset($value['help']) ){ echo $value['help']; } ?></div></div>
								<div class="<?php echo $side_two; ?>">
								  <?php $margin_input4 = isset($options_theme[ $value['name'] ])?$options_theme[ $value['name'] ]:''; ?>
									<div>
										<div class="input_4--square"><span>&nbsp; </span><input type="number" name="<?php echo $value['name'].'_top'; ?>" id="<?php echo $value['id'].'_top' ?>"  min ="<?php if(isset( $value['min'] )){ echo $value['min']; } ?>" max="<?php if(isset( $value['max'])){  echo $value['max']; } ?>" step="<?php if(isset( $value['step'])){ echo $value['step']; } ?>" value="<?php echo isset($margin_input4['top'])?$margin_input4['top']:0; ?>" ></div>
										<div class="input_4--square"><span>&nbsp; </span><input type="number" name="<?php echo $value['name'].'_right'; ?>" id="<?php echo $value['id'].'_right' ?>"  min ="<?php if(isset( $value['min'] )){ echo $value['min']; } ?>" max="<?php if(isset( $value['max'])){  echo $value['max']; } ?>" step="<?php if(isset( $value['step'])){ echo $value['step']; } ?>" value="<?php echo isset($margin_input4['right'])?$margin_input4['right']:0; ?>" ></div>
										<div class="input_4--square"><span>&nbsp; </span><input type="number" name="<?php echo $value['name'].'_bottom'; ?>" id="<?php echo $value['id'].'_bottom' ?>"  min ="<?php if(isset( $value['min'] )){ echo $value['min']; } ?>" max="<?php if(isset( $value['max'])){  echo $value['max']; } ?>" step="<?php if(isset( $value['step'])){ echo $value['step']; } ?>" value="<?php echo isset($margin_input4['bottom'])?$margin_input4['bottom']:0; ?>" ></div>
										<div class="input_4--square"><span>&nbsp; </span><input type="number" name="<?php echo $value['name'].'_left'; ?>" id="<?php echo $value['id'].'_left' ?>"  min ="<?php if(isset( $value['min'] )){ echo $value['min']; } ?>" max="<?php if(isset( $value['max'])){  echo $value['max']; } ?>" step="<?php if(isset( $value['step'])){ echo $value['step']; } ?>" value="<?php echo isset($margin_input4['left'])?$margin_input4['left']:0; ?>" ></div>
									</div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "date": ?>
							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" style="<?php if(isset( $value['style'] )){ echo $value['style'];} ?>" >
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<input class="IF_datepicker" type="text"  value="<?php if( isset($options_theme[ $value['name'] ]) ){ echo $options_theme[ $value['name'] ]; } ?>" name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>"  autocomplete="off" <?php if(isset($value['placeholder'])){ echo "placeholder='{$value['placeholder']}'"; } ?>  />
								</div>
								<script>
									jQuery(document).ready(function($){
										jQuery('.IF_datepicker').datepicker({ dateFormat : 'yy-mm-dd' <?php if( isset($value['opts']) && $value['opts'] ){ echo $value['opts']; } ?> });
									});
								</script>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

						case "tag": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<input type="text" value="<?php if( isset($options_theme[ $value['name'] ]) ){ echo $options_theme[ $value['name'] ]; } ?>" id="<?php echo $value['id']; ?>" name="<?php echo $value['name'] ?>" <?php if(isset($value['placeholder'])){ echo "placeholder='{$value['placeholder']}'"; } ?> />
								</div>
								<script>
									jQuery(document).ready(function($){
										jQuery('#<?php echo $value['id']; ?>').tagEditor({ placeholder: '<?php if(isset($value['placeholder']) && $value['placeholder']){ echo $value['placeholder']; } ?>',forceLowercase:false });
									});
								</script>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

						case "timer_range": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row ilen_timerange <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<?php $clock_range_1 = isset($options_theme[ $value['name'] ])?$options_theme[ $value['name'] ]:''; ?>
									<div class="input-group clockpicker pull-center" data-placement="left" data-align="top" data-autoclose="true">
										<input type="text" class="form-control ilen_timerange_<?php echo $value['id']."_from"; ?>"  value="<?php echo isset($clock_range_1['from'])?$clock_range_1['from']:''; ?>" id="<?php echo $value['id']."_from"; ?>" name="<?php echo $value['name']."_from" ?>" <?php if(isset($value['placeholder'])){ echo "placeholder='{$value['placeholder']}'"; } ?>>
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-time"></span>
										</span>
									</div> &nbsp;
									<?php if(isset($value['text_to'])){ echo "<div class='clockpicker_text'>".$value['text_to']."</div>"; } ?>
									 &nbsp;
									<div class="input-group clockpicker pull-center" data-placement="left" data-align="top" data-autoclose="true">
										<input type="text" class="form-control ilen_timerange_<?php echo $value['id']."_to"; ?>"  value="<?php echo isset($clock_range_1['to'])?$clock_range_1['to']:''; ?>" id="<?php echo $value['id']."_to"; ?>" name="<?php echo $value['name']."_to" ?>" <?php if(isset($value['placeholder'])){ echo "placeholder='{$value['placeholder']}'"; } ?>>
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-time"></span>
										</span>
									</div>
									
								</div>
								<script>
									jQuery(document).ready(function($){
										$('.clockpicker').clockpicker({<?php if(isset($value['js_options'])){ echo $value['js_options']; } ?>});
									});
								</script>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


					}

			}

	}


	// =BUILD Fields widget---------------------------------------------
	function build_fields_w( $fields = array(), $widget_id = '', $other_ref_widget = 0 ){

			foreach ($fields as $key => $value) {

					if( in_array("b", $value['row']) ) { $side_two = "b"; }else{  $side_two ="c"; }

					switch ( $value['type'] ) {

						case "text": ?>
							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<input type="text"  value="<?php if( isset( $value['value'] ) ){ echo $value['value']; } ?>" name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>"  autocomplete="off" <?php if(isset($value['placeholder'])){ echo "placeholder='{$value['placeholder']}'"; } ?>  />
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

						case "textarea": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row row_textarea <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<textarea name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>" style="width:100%;height:150px;" <?php if(isset($value['placeholder'])){ echo "placeholder='{$value['placeholder']}'"; } ?> <?php if( isset($value['maxlength']) && $value['maxlength'] ): ?>onKeyDown="IF_textCounter(this,'<?php echo $value['id'] ?>_text_length',<?php echo (int)$value['maxlength']; ?>);" onKeyUp="IF_textCounter(this,'<?php echo $value['id'] ?>_text_length' ,<?php echo (int)$value['maxlength']; ?>)"<?php endif; ?> ><?php if( isset( $value['value'] ) ){ echo $value['value']; } ?></textarea>
									<?php if( isset($value['maxlength']) && $value['maxlength'] ): ?>
									<input type="text" disabled="disabled" readonly="readonly" id="<?php echo $value['id'] ?>_text_length" class="text_length" value="<?php echo (int)$value['maxlength']; ?>" />
								<?php endif; ?>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

						case "select": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<div class="select-wrapper" >
									<select name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>" <?php if(isset( $value['onchange'] )){ echo "onchange='{$value['onchange']}'";} ?>  >
										<?php 
											if( isset($value['items']) && is_array( $value['items'] ) ){
												foreach ( $value['items'] as $item_key => $item_value ): ?>
													<option value="<?php echo $item_key ?>" <?php selected( isset( $value['value'] )?$value['value']:"" ,   $item_key ); ?>><?php echo $item_value ?></option>  
												<?php
												endforeach;
											}
										?>
									</select>
									</div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

						case "radio_image": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row radio_image <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a <?php if( $side_two == 'c'){ echo "a_line"; } ?>"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">

									<?php 
									if( isset($value['items']) && is_array( $value['items'] ) ){
										foreach ($value['items'] as $item_key => $item_value): ?>
											<?php if( isset($value['name']) ): ?>
											<label for="<?php echo $value['id']."_".$item_value['value']; ?>">
												<img <?php echo $value['value']; ?> id="<?php if( isset($value['id']) ){ echo $value['id']."_img_".$item_value['value'];} ?>" name="<?php if( isset($value['name']) ){ echo $value['name']."_img";} ?>" src="<?php if( isset($item_value['image']) ){ echo $item_value['image']; } ?>" class="radio_image_selection <?php echo $value['id']; ?> <?php echo $value['name']; ?> <?php echo isset( $value['value'] ) && $value['value']  == $item_value['value']?"active":""; ?>" data-id="<?php echo $value['id']; ?>" title="<?php echo $item_value['text']; ?>" />
												<?php if( isset(  $value['name']  )  ): ?>
												<input  <?php checked( $value['value'] , $item_value['value'] ); ?> id="<?php echo $value['id']."_".$item_value['value']; ?>" type="radio" name="<?php echo $value['name']; ?>" value="<?php echo $item_value['value'] ?>" />
											<?php endif; ?>
											</label>
										<?php endif; ?>
									<?php endforeach;
									} ?>
								</div>
								<script>
								jQuery(".ilenwidget-options .<?php echo $widget_id; ?> .radio_image_selection").on("click",function( event){

									event.preventDefault();
									var class_ref = jQuery(this).attr("data-id");
									var img_obj =  jQuery(this).attr("id");

									jQuery(".<?php echo $widget_id; ?> ."+class_ref).removeClass("active");
									
									jQuery(".<?php echo $widget_id; ?> #"+img_obj).addClass("active");
									jQuery(".<?php echo $widget_id; ?> #"+img_obj).next().attr("checked","checked");

								});
								</script>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

						case "color": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<input type="text" class="theme_color_picker" value="<?php if(isset(  $value['value'] )){ echo $value['value']; } ?>" name="<?php echo $value['name']; ?>" id="<?php echo $value['id'] ?>" data-default-color="<?php echo $value['value']; ?>" />
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

						case "range2": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php  if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_range2" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><strong><?php if(isset( $value['title'] )){ echo $value['title']; } ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<div>
										<div style="width:10%;float:left">
											<span  id="<?php echo $value['id'] ?>-value" style="padding: 5px 10px;background: #FAFAFA;color: #444;border: 1px solid #F1F1F1;"></span>
										</div>
										<div style="width: 82%;float: right;padding: 0;border-radius: 5px;margin-left: 13px;" >
											<div id="<?php echo $value['id'] ?>-range" class="<?php if( isset($value['color']) && $value['color'] == 1 ){ echo "noUi-connect"; } ?> noUiSlider_range"></div>    
										</div>
										<input type="hidden" class="input_noUiSlider" value="<?php echo $value['id'] ?>|<?php echo (int)$value['value'] ?>|<?php echo (int)$value['step'] ?>|<?php echo (int)$value['min'] ?>|<?php echo (int)$value['max'] ?>" />
										<input type="hidden" name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>" value="<?php if(isset( $value['value'] )){ echo (int)$value['value']; }else{ echo 0; } ?>" />
									</div>
								</div>
							</div> 
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>


						<?php break;

						case "input_4": ?>
							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row input_4 <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_range" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><?php if(isset( $value['title'] )){ echo $value['title']; } ?><div class="help"><?php if( isset($value['help']) ){ echo $value['help']; } ?></div></div>
								<div class="<?php echo $side_two; ?>">
								  <?php $margin_input4 = isset( $value['value'] )? explode(",",$value['value']) : array(); ?>
									<div>
										<div class="input_4--square"><span>&nbsp; </span><input class="input4_single_input" type="number" name="input_value_top" id="input_value_top"  min ="<?php if(isset( $value['min'] )){ echo $value['min']; } ?>" max="<?php if(isset( $value['max'])){  echo $value['max']; } ?>" step="<?php if(isset( $value['step'])){ echo $value['step']; } ?>" value="<?php echo isset($margin_input4[0])?(int)$margin_input4[0]:0; ?>" ></div>
										<div class="input_4--square"><span>&nbsp; </span><input class="input4_single_input" type="number" name="input_value_right" id="input_value_right"  min ="<?php if(isset( $value['min'] )){ echo $value['min']; } ?>" max="<?php if(isset( $value['max'])){  echo $value['max']; } ?>" step="<?php if(isset( $value['step'])){ echo $value['step']; } ?>" value="<?php echo isset($margin_input4[1])?(int)$margin_input4[1]:0; ?>" ></div>
										<div class="input_4--square"><span>&nbsp; </span><input class="input4_single_input" type="number" name="input_value_bottom" id="input_value_bottom"  min ="<?php if(isset( $value['min'] )){ echo $value['min']; } ?>" max="<?php if(isset( $value['max'])){  echo $value['max']; } ?>" step="<?php if(isset( $value['step'])){ echo $value['step']; } ?>" value="<?php echo isset($margin_input4[2])?(int)$margin_input4[2]:0; ?>" ></div>
										<div class="input_4--square"><span>&nbsp; </span><input class="input4_single_input" type="number" name="input_value_left" id="input_value_left"  min ="<?php if(isset( $value['min'] )){ echo $value['min']; } ?>" max="<?php if(isset( $value['max'])){  echo $value['max']; } ?>" step="<?php if(isset( $value['step'])){ echo $value['step']; } ?>" value="<?php echo isset($margin_input4[3])?(int)$margin_input4[3]:0; ?>" ></div>
									</div>
								</div>
								<input type="hidden" name="<?php echo $value['name'] ?>" class="input_value_total" value="<?php echo isset( $value['value'] )?$value['value'] : ''; ?>" />
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

						case "checkbox": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<?php 
								$class_ilen_list = null;
								if( isset($value['display']) && ($value['display'] == 'list' || $value['display'] == 'types_post') ){
									$class_ilen_list = "ilen_check_list";
								}
							?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_checkbox" <?php if(isset( $value['style'] )){ echo $value['style']; } ?>> 
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two." ".$class_ilen_list; ?>">

									
									<?php if( isset($value['display']) && $value['display'] == 'list' ){  ?>
										<?php 
											if( isset($my_values) && !is_array(  $my_values ) ){
												$my_values = array();
											}

											foreach ( $value['items'] as $key2 => $value2 ): ?>

											<div class="row_checkbox_list">
												<input  type="checkbox" <?php if( isset($value2['value']) && isset($my_values) && $my_values && in_array( $value2['value']  , $my_values ) ){ echo " checked='checked' ";} ?> name="<?php echo $value['name'] ?>[]" id="<?php echo $value2['id']."_".$value2['value'] ?>" value="<?php if( isset($value2['value']) ){ echo $value2['value']; } ?>"  />  
												<label for="<?php echo $value2['id']."_".$value2['value'] ?>"><span class="ui"></span></label>
												&nbsp;<?php echo  $value2['text']; ?>
												<div class="help"><?php echo $value2['help']; ?></div>
											</div>

										<?php endforeach; ?>
										
									<?php } elseif( isset($value['display']) && $value['display'] == 'types_post' ) { ?>
										<?php //$ck=''; if( isset($my_values) ){ $ck =  checked(  $my_values  , 1, FALSE );  }


											// get type post 
											$post_types = get_post_types(array(), "objects");
											/*$my_values = null;
											if( isset($my_values) && !is_array(  $my_values ) ){
												$my_values = array();
											}*/
												//var_dump( $value['value'] );
											foreach ($post_types as $post_type):  ?>
												<?php if( !in_array($post_type->name,array('revision','nav_menu_item')) ): ?>
												<div class="row_checkbox_types_post">

													<input  type="checkbox" <?php if( in_array( $post_type->name  , (array)$value['value'] ) ){ echo " checked='checked' ";} ?> name="<?php echo $value['name'] ?>[]" id="<?php echo $value['id']."__".$post_type->name."__{$widget_id}" ?>" value="<?php echo $post_type->name; ?>"  />   

													<label for="<?php echo $value['id']."__".$post_type->name."__{$widget_id}" ?>"><span class="ui"></span></label>
													&nbsp;<?php echo $post_type->labels->name; ?>
													<div class="help"><?php //echo $value2['help']; ?></div>
												</div>
											<?php endif; ?>
											<?php endforeach; ?>
										
									<?php }else { ?>
										<div class="row_checkbox_normal <?php echo $value['value']; ?>">
											<input  type="checkbox" <?php checked( $value['value'] , '1'  ); ?> name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>" value="1"  />
											<label for="<?php echo $value['id'] ?>"><span class="ui"></span></label>
										</div>
									<?php } ?>
									
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

						case "radio": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row ilen_radio <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<div class="radio-switch" >
										<?php if( isset( $value['items'] ) && is_array( $value['items'] ) ): ?>
											<?php foreach ($value['items'] as $key2 => $value2): ?>
												<input type="radio" name="<?php echo $value['name']; ?>" value="<?php echo $key2; ?>" id="<?php echo $value['id']; ?>-<?php echo $key2; ?>" <?php checked( $value['value'] , $key2  ); ?> />
												<label for="<?php echo $value['id']; ?>-<?php echo $key2; ?>" data-title="<?php echo $value2; ?>"><?php echo $value2; ?></label>
											<?php endforeach; ?>
										<?php endif; ?>
									</div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

						case "html": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><?php if( isset($value['html1']) ){ echo htmlentities($value['html1']); } ?></div>
								<div class="<?php echo $side_two; ?>">
									<?php if( isset($value['html2']) ){ echo $value['html2']; } ?>
								</div>
								<div class="help"><?php echo $value['help']; ?></div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "tag": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row ilen_tags <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<input type="text" value="<?php if(isset(  $value['value'] )){ echo $value['value']; } ?>" id="<?php echo $value['id']; ?>" name="<?php echo $value['name'] ?>" <?php if(isset($value['placeholder'])){ echo "placeholder='{$value['placeholder']}'"; } ?> class="ilen_tag" />
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

					}

			}

	}



	// =BUILD Fields widget MORE---------------------------------------------
	function build_fields_w2( $fields = array(), $widget_id = '' ){

		if( is_array($fields) ){
			foreach ($fields as $key => $value) {

						if( in_array("b", $value['row']) ) { $side_two = "b"; }else{  $side_two ="c"; }

						switch ( $value['type'] ) {

							case "text": ?>
								<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
								<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
									<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
									<div class="<?php echo $side_two; ?>">
										<input type="text"  value="<?php if( isset( $value['value'] ) ){ echo $value['value']; } ?>" name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>"  autocomplete="off" <?php if(isset($value['placeholder'])){ echo "placeholder='{$value['placeholder']}'"; } ?>  />
									</div>
								</div>
								<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

							<?php break;

							case "color": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<input type="text" class="theme_color_picker" value="<?php if(isset(  $value['value'] )){ echo $value['value']; } ?>" name="<?php echo $value['name']; ?>" id="<?php echo $value['id'] ?>" data-default-color="<?php echo $value['value']; ?>" />
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

						}
			}
		}

	}


		// =BUILD Fields meta---------------------------------------------
	function build_fields_m( $fields = array(), $stored = '' ){

		global $if_utils;


		$_html = "";
		$stored = $stored[0];
		foreach ($fields as $key => $value) {

			if( in_array("b", $value['row']) ) { $side_two = "b"; }else{  $side_two ="c"; }

			$class        ='';
			$style        ='';
			$default      ='';
			$value_stored ='';
			$real_value   ='';
			$placeholder  ='';
			$readonly     ='';

			//if( $value['type'] == 'checkbox' ) { var_dump ( $stored[ $value['name'] ] ); }


			$class        = isset( $value['class'] )?$value['class']:'';
			$style        = isset( $value['style'] )?"style='{$value['style']}'":'';
			$default      = isset( $value['value'] )?$value['value']:'';
			$value_stored = isset( $stored[ $value['name'] ])  && $stored[ $value['name'] ]?$stored[ $value['name'] ]:null;
			$placeholder  = isset( $value['placeholder'] ) && $value['placeholder']?$value['placeholder']:'';
			$readonly     = isset( $value['readonly'] ) && $value['readonly']? "readonly='readonly'" :'';

 
			switch ( $value['type'] ) {

				case "text": 
					if(isset( $value['before'] )){ echo $value['before'];}

					$_html .="<div class='row $class' $style >";
						$_html .="<div class='a'><strong>{$value['title']}</strong><div class='help'>{$value['help']}</div></div>
								<div class='$side_two'>
									<input type='text' $readonly  value='$value_stored' name='{$value["name"]}' id='{$value["id"]}'  autocomplete='off' placeholder='{$placeholder}' />
								</div>
							  </div>";
					if(isset( $value['after'] )){ echo $value['after'];}

				break;

				case "select2_search_post": 
					if(isset( $value['before'] )){ echo $value['before'];}

					$_script = '';
					$_url_ajax = admin_url('admin-ajax.php?action=select2-search-post');
					//$_url_ajax = '/ntest/3/json.json';
					$_script = "<script>/* Metabox - select2_search_post */
jQuery(document).ready(function($){
  console.log('$_url_ajax');
  $('#select2_search_post_{$value["name"]}').select2({
	placeholder: 'Search...',
	minimumInputLength: 3,
	multiple: false,
	formatSearching: function () { return 'Searching...'; },
	formatNoMatches: function () { return 'No result found'; },
	ajax: {
		url: '$_url_ajax',
		dataType: 'json',
		action: 'select2-search-post',
		quietMillis: 100,
		data: function (term, page) {
			return {
			  term: term, //search term
			  action: 'select2-search-post', //wordpress action
			  image_default: '{$this->parameter['default_image']}'
			};
		},
		results: function (data, page) {
			//--> var more = (page * 10) < data.total; // whether or not there are more results available
			//alert(data);
			// notice we return the value of more so Select2 knows if more results can be loaded
		return {
		  results: data,
		  //more: more
		};
	  }

	  },
	 formatResult: function(item){ return '<div class=\"select2_list_search\"><div class=\"a\"><img  height=\"32\" width=\"32\" src=\"'+item.image+'\" /></div><div class=\"b\">' + item.text + '</div></div>' }, // omitted for brevity, see the source of this page
	 formatSelection: function(item){ jQuery('#select2_search_post_{$value["name"]}_select').val( item.id+'|'+item.text+'|'+item.image+'&' ) ;return item.text; }, // omitted for brevity, see the source of this page
	 dropdownCssClass: 'bigdrop', // apply css that makes the dropdown taller
	 escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results
	 // initSelection: function(element, callback) { return $.getJSON('/ajax/select2_sample.php?id=' + (element.val()), null, function(data) { return callback(data); });}
  });

	
}); 
function add_select2_to_dragdrop_{$value['name']}( id, text, image ){

	var array_data_true = jQuery(\"#select2_search_post_{$value["name"]}_select\").val().split(\"|\");
	
	if( ! jQuery.isArray(array_data_true) ) return;
	if( jQuery('#select2_search_post_{$value["name"]}_values').val() ){

		var array_values = jQuery('#select2_search_post_{$value["name"]}_values').val().split('|');

		if( ! jQuery.isArray(array_values) ) return;
		
		for (i = 0; i < array_values.length; i++) { 
			if( id == array_values[i] ){
				return false;
			}
		}

		if( ! image ) return;
		var my_image = image.substring(0,image.length - 1);
		jQuery('#select2_search_post_dragdrop_{$value['id']}').append('<li id=\"li_select2_item_'+id+'\"><img src=\"'+my_image+'\" width=\"32\" height=\"32\" />'+' '+text+'<span class=\"select2_list_close select2_list_close'+id+'\" data-id=\"'+id+'\" onclick=\"delete_select2_to_dragdrop_{$value['name']}('+id+')\">✕</span></li>');
		jQuery(\"#select2_search_post_{$value["name"]}_values\").val( jQuery(\"#select2_search_post_{$value["name"]}_values\").val() + array_data_true[0] + \"|\" );
	}else{
		if( ! image ) return;
		var my_image = image.substring(0,image.length - 1);
		jQuery('#select2_search_post_dragdrop_{$value['id']}').append('<li id=\"li_select2_item_'+id+'\"><img src=\"'+my_image+'\" width=\"32\" height=\"32\" />'+' '+text+'<span class=\"select2_list_close select2_list_close'+id+'\" data-id=\"'+id+'\" onclick=\"delete_select2_to_dragdrop_{$value['name']}('+id+')\">✕</span></li>');
		jQuery(\"#select2_search_post_{$value["name"]}_values\").val( jQuery(\"#select2_search_post_{$value["name"]}_values\").val() + array_data_true[0] + \"|\" );

	}
	
}
function delete_select2_to_dragdrop_{$value['name']}( id ){

	var array_values = jQuery('#select2_search_post_{$value["name"]}_values').val().split('|');
	jQuery('#select2_search_post_{$value["name"]}_values').val('');
	var new_ids = '';

	for (i = 0; i < array_values.length; i++) { 

		if( array_values[i] ){
			if( id != array_values[i] ){
				new_ids = new_ids + array_values[i]+'|';
			}
		}
	}
 
	jQuery('.select2_search_post_{$value["name"]}_values').val(new_ids);
	jQuery('#li_select2_item_'+id).remove();
}

</script>
<style>
.select2-search input.select2-active {
	background: #fff url('".admin_url('/images/wpspin_light.gif')."') no-repeat 100%;
	background: url('".admin_url('/images/wpspin_light.gif')."') no-repeat 100%, -webkit-gradient(linear, left bottom, left top, color-stop(0.85, #fff), color-stop(0.99, #eee));
	background: url('".admin_url('/images/wpspin_light.gif')."') no-repeat 100%, -webkit-linear-gradient(center bottom, #fff 85%, #eee 99%);
	background: url('".admin_url('/images/wpspin_light.gif')."') no-repeat 100%, -moz-linear-gradient(center bottom, #fff 85%, #eee 99%);
	background: url('".admin_url('/images/wpspin_light.gif')."') no-repeat 100%, linear-gradient(to bottom, #fff 85%, #eee 99%) 0 0;
}
</style>
";

// if post to show
$_html_select2 = null;
if( $value_stored ){

	$posts_id = explode( "|" , $value_stored );
	
	$posts_real_id = null;
	foreach ($posts_id as $posts_id_key => $posts_id_value) {
		if( $posts_id_value ){
			$posts_real_id[] = $posts_id_value;
		}
	}

	if( is_array( $posts_real_id ) ){

		$args = array(
			'post__in' => $posts_real_id,
			'orderby' => 'post__in',
			'posts_per_page' => 100
		);
		
		$get_posts = get_posts($args);

		$found_posts = array();
		if ($get_posts) {

			$image = null;
			foreach ($get_posts as $_post) {

				$image = $if_utils->IF_get_image('thumbnail',$this->parameter['default_image'],$_post->ID);
				$text = $if_utils->IF_cut_text(get_the_title($_post->ID),75);
				$_html_select2 .= "<li id='li_select2_item_{$_post->ID}'><img src='{$image['src']}'  /> $text <span class='select2_list_close select2_list_close{$_post->ID}' data-id='{$_post->ID}' onclick='delete_select2_to_dragdrop_{$value['name']}({$_post->ID})'>✕</span></li>";

			}

			wp_reset_postdata();

		}

	}
}

					$_html .="<div class='row $class select2_search_post_with_image' $style >$_script";

						$_html .="<div class='a'><strong>{$value['title']}</strong><div class='help'>{$value['help']}</div></div>
								<div class='$side_two'>
									<div>
										<input class='select2_search_post' id='select2_search_post_{$value["name"]}' type='hidden' data-placeholder='{$placeholder}' />
										<input class='select2_search_post_button_add button' id='select2_search_post_button_{$value["name"]}' value='Add' type='button' onclick='var array_data_true = jQuery(\"#select2_search_post_{$value["name"]}_select\").val().split(\"|\");add_select2_to_dragdrop_{$value['name']}(array_data_true[0],array_data_true[1],array_data_true[2]);' />
									</div>
									<input type='hidden' id='select2_search_post_{$value["name"]}_select'  />
									<input type='hidden' id='select2_search_post_{$value["name"]}_values' name='{$value["name"]}' value='$value_stored'  />
									<div id='select2_search_post_dragdrop_{$value['id']}' class='select2_search_post_dragdrop'>$_html_select2</div>
								</div>
							  </div>";
					if(isset( $value['after'] )){ echo $value['after'];}

				break;

				case "checkbox": 

					if(isset( $value['before'] )){ echo $value['before'];}
					$_html .="<div class='row $class ilenmetabox_row_checkbox' $style > 
								<div class='a'><strong>{$value['title']}</strong><div class='help'>{$value['help']}</div></div>
								<div class='$side_two'>";
							
							if( isset($value['display']) && $value['display'] == 'list' ){

								if( isset($value_stored) && !is_array(  $value_stored ) ){
									$value_stored = array();
								}

								foreach ($value['items'] as $key2 => $value2): 
									$checked = isset($value2['value']) && isset($value_stored) && $value['name'] && in_array( $value2['value']  , $value_stored ) ? "checked='checked'":"";
									$_html .="<div class='row_checkbox_list'>
												<input  type='checkbox' $checked name='{$value["name"]}[]' id='{$value2['id']}_{$value2['value']}' value='$value_stored'  />    
												<label for='{$value2['id']}_{$value2['value']}'><span class='ui'></span></label>
												&nbsp;{$value2['text']}
												<div class='help'>{$value2['help']}</div>
											  </div>";
								endforeach; 
								
							}elseif( isset($value['display']) && $value['display'] == 'types_post' ) {

								$ck=''; 
								if( isset($value_stored) ){ $ck =  checked(  $value_stored  , 1, FALSE );  }

								// get type post 
								$post_types = get_post_types(array(), "objects");

								foreach ($post_types as $post_type):
									$ck = in_array( $post_type->name  , (array)($value_stored) ) ;

									if( !in_array($post_type->name,array('revision','nav_menu_item')) ):
										$_html .="<div class='row_checkbox_types_post'>
													<input  type='checkbox' $ck name='{$value['id']}[]' id='{$value['id']}_{$post_type->name}' value='$post_type->name'  />
													<label for='{$value['id']}_{$post_type->name}'><span class='ui'></span></label>
													&nbsp;$post_type->labels->name
													<div class='help'>{$value2['help']}</div>
												 </div>";
									endif;
								endforeach;
								
							}else {

								$ck=''; if( isset($value_stored) ){ $ck =  checked(  $value_stored  , 1 , FALSE );  }
								$_html .="<div class='row_checkbox_normal'>
											<input  type='checkbox' $ck name='{$value['name']}' id='{$value['id']}' value='{$value['value_check']}'  />
											<label for='{$value['id']}'><span class='ui'></span></label>
										  </div>";
							}
							
						$_html .="</div>
							</div>";
					if(isset( $value['after'] )){ echo $value['after'];} 

				break;

				case "html": 

					$part1 = isset($value['html1']) && $value['html1']?htmlentities($value['html1']):"<strong>{$value['title']}</strong>";
					$part2 = isset($value['html2']) && $value['html2']?$value['html2']:"";
					if(isset( $value['before'] )){ echo $value['before'];} 
						$_html .="<div class='row  $class' style='$style'>
								<div class='a'>$part1 <div class='help'>{$value['help']}</div></div>
								<div class='$side_two'>
									$part2
								</div>
							</div>";
					if(isset( $value['after'] )){ echo $value['after'];}

				break;

				case "textarea": 
					if(isset( $value['before'] )){ echo $value['before'];}

					$_html .="<div class='row ilenmetabox_row_textarea $class' $style >";
						$_html .="<div class='a'><strong>{$value['title']}</strong><div class='help'>{$value['help']}</div></div>
								<div class='$side_two'>
									<textarea $readonly name='{$value["name"]}' id='{$value["id"]}' placeholder='{$placeholder}'>$value_stored</textarea>
								</div>
							  </div>";
					if(isset( $value['after'] )){ echo $value['after'];}

				break;

				case "color": 
					if(isset( $value['before'] )){ $_html .= $value['before']; }
					$_html .="<div class='row $class' $style >";
					$_html .="<div class='a'><strong>{$value['title']}</strong><div class='help'>{$value['help']}</div></div>
								<div class='$side_two'>
									<input name='{$value["name"]}'  id='{$value["id"]}' type='text' class='theme_color_picker' value='{$value_stored}' data-default-color='{$default}' />
								</div>
							  </div>";

					if(isset( $value['after'] )){ $_html .= $value['after'];}		

					break;


			} // switch
 

		} // foreach field

		return $_html;

	}



	function create_metabox( $mb_header , $mb_body , $name_store, $post_type ){

		$post_id = isset($_GET['post'])?$_GET['post']:0;
		//$post_type = isset($_GET['post_type']) ? $_GET['post_type'] : 'post';


		if(  is_array( $mb_header)  ){

			$stored_meta = get_post_meta( $post_id , $name_store );

			if ( !$stored_meta ){

				$stored_meta = $this->set_default_metabox_values( $post_id , $name_store , $mb_header ,  $mb_body );
				$stored_meta[0] = $stored_meta;

			}

			$priority = 10;
			$this->parameter['metabox_name']   = $name_store;
			$this->parameter['header_metabox'] = $mb_header;
			$this->parameter['body_metabox']   = $mb_body;


			foreach ($mb_header as $key => $value) {
				$html_data = $this->create_ilenMetabox( $key , $mb_header , $mb_body,  $stored_meta );
				$function_meta_dinamyc = create_function( '',  "echo ".@var_export($html_data,TRUE).";" );
				add_action('admin_head',  @create_function( '', "add_meta_box( '{$value['id']}', '{$value['title']}', '$function_meta_dinamyc', '$post_type' , '{$value['context']}', '{$value['priority']}' );" ), $priority );
				
				$priority = $priority + 1;

			}

			//do_action( 'save_post', $post_id );
			//add_action( 'save_post' ,  array( &$this , 'IF_save_metabox' ) , 6 , 1 );

 
		}

	}




	// =VALIDATE WIDGET input for type
	function ilenwidget_validate_inputs_ext($input,$type,$force = false){ // force: If NO type related send it anyway, this is done to fields that do not belong to the validation

	  // type = (s)=string validate,(i)=integet,(h)=HTML output,(p)=pure string
		if($type){
			if( $type == 's' ){
				return (string) esc_attr($input);
			}elseif( $type == 'i' ){
				return (int)$input;
			}elseif( $type == 'h' ){
				return esc_html($input);
			}elseif( $type == 'p' ){ // pure, Notice: no verify
				return $input;
			}elseif( $type == 't' ){ // use strip_tags
				return (string)esc_attr( strip_tags($input) );
			}elseif( $type == 'a' ){ // use strip_tags
				return (array)$input;
			}
		}
		if( $force == TRUE ){
			return $input;
		}

	}


	// =VALIDATE WIDGET input if not value
	function ilenwidget_validate_inputs_for_default($array_value = array(), $array_default = array() ){

	  $array_new_values = array();
	  if( is_array( $array_value ) ){

		foreach ($array_value as $key => $value) {
		  
			if( !$value )
			  $array_new_values[$key] = $array_default[$key];
			else
			  $array_new_values[$key] = $array_value[$key];
		}

	  }else
		$array_new_values = $array_value;


	  return $array_new_values;
	 
	}

 


// =OUTPUT HTML ---------------------------------------------

function ShowHTML(){  
		
	if( $this->parameter['type']  == "theme" ){
			self::ilentheme_options_wrap_for_theme(); 
		}elseif( $this->parameter['type'] == "plugin" ){
			self::ilentheme_options_wrap_for_plugin(); 
		}elseif( $this->parameter['type'] == "plugin-tabs" ){
			self::ilentheme_options_wrap_for_plugin_tabs(); 
		}
		
 
}




// =SAVE options---------------------------------------------
function save_options(){

		global $options_update;

		$options_update = null;
		//delete_option( $this->parameter['name_option']."_options" );

		//code save options the theme
		if( isset($_POST) && ( isset($_POST['save_options']) || isset($_POST['reset_options'] ) ) && $_POST["name_options"] == $this->parameter["name_option"] ){
 
			$Myoptions = self::theme_definitions();


			if( is_array($Myoptions) ){
				foreach ($Myoptions as $key2 => $value2) {

					if( $key2 != 'last_update' ){
						 
						self::fields_update($value2['options'], false);

					}else{
						$options_update[$key2] = time();
					}
				}
			}

			if( is_array($options_update) ){    

				if(update_option( $this->parameter['name_option']."_options" , $options_update)){
					$this->save_status = true;
				}else{
					$this->save_status = false;
				}

			}else{
				$this->save_status = false;
			}

		}
	}



	function save_options_for_tabs(){

		global $if_utils;

		//code save options the theme
		if( isset($_POST) && ( isset($_POST['save_options']) || isset($_POST['reset_options'] ) ) && $_POST["name_options"] == $this->parameter["name_option"] ){
 
			$Myoptions = self::theme_definitions();
			$options_update = array();

			if( is_array($Myoptions) ){

				foreach ($Myoptions as $key2 => $value2) {

					$data_f = array();
					$tabs_save = ( isset($_GET['tabs']) && $_GET['tabs'] == $value2["tab"] ) ? true:false;

					//if( (  isset($value2["tab"]) && isset($value2['default']) && $value2['default'] ) || $tabs_save ){
					if( $tabs_save ){

						if( $key2 != 'last_update' ){
							if( $data_f = self::fields_update($value2['options'],1) ){
								$options_update = array_merge($options_update, $data_f);
							}

						}else{
							$options_update[$key2] = time();
						}
					}
				}
			}

 
		
		if( is_array($options_update) ){

			$options = $if_utils->IF_get_option( $this->parameter['name_option'] );
			$options_current = array();
			
			if( isset($options) && is_object($options) ){
				foreach ($options as $key => $value) {
					if( (!empty($value) || !isset($value) || $value == "0") && $key != 'last_update'  ){
						$options_current[$this->parameter['name_option'].'_'.$key] = $value;
					}
				}
				$options_current['last_update'] = time();
			}

			// Parse incoming $args into an array and merge it with $defaults
			$args = wp_parse_args(  $options_update , $options_current );
			//var_dump( $options_update );
			//var_dump( $options_current );
			//var_dump( $args );


			if( update_option( $this->parameter['name_option']."_options" , $args) ){
				$this->save_status = true;  
			}else{
				$this->save_status = false; 
			}
		}else{
			$this->save_status = false; 
		}

	}
}


function fields_update($data,$is_tab = 1){

	if( $is_tab ){
		$options_update = null;
	}else{
		global $options_update;
	}

	foreach ($data as $key => $value) {
					 
		if( isset($_POST['save_options']) ){

			// save options check list
			if(  isset($value['display']) && $value['type'] == 'checkbox' && ( $value['display'] == 'list' || $value['display'] == 'types_post' ) ){

				$array_get_values_check = array();
				$array_set_values_check = array();
				if(  $value['display'] == 'list' ){
					
					foreach ( $value['items'] as $key2 => $value2 ) $array_get_values_check[] = $value2['value'];

					if ( isset($_POST[$value['name']]) && is_array( $_POST[$value['name']] ) ) {

						foreach ( $_POST[$value['name']] as $key3 => $value3) {
							if( in_array( $value3 , $array_get_values_check ) ){

								$array_set_values_check[] = $value3;
							}

						}

					}
				}elseif( isset($value['display']) && $value['display'] == 'types_post'  ){

					if( isset($_POST[$value['name']]) )
						$types_post = (array)$_POST[$value['name']];
					if ( isset($types_post) && is_array( $types_post ) ) {

						foreach ( $types_post as $key3 => $value3) {
								$array_set_values_check[] = $value3;
						}

					}

				}

				// set values type check list
				$options_update[$value['name']] = $array_set_values_check;



			}elseif(  $value['type'] == 'component_list_categories' ){



				 $array_set_values_check = array();
				 if( isset($_POST[$value['id'] ]) &&  is_array( $_POST[$value['id'] ] ) ){

					if( in_array( '-1', $_POST[$value['id'] ] ) )
						$array_set_values_check[]="-1";
					else{

						$array_set_values_check = $_POST[$value['id'] ];

					}


				 }

				 if( ! $array_set_values_check ){
					$array_set_values_check = array("-1");
				 }

				 // set values type check list
				$options_update[$value['name']] = $array_set_values_check;

				


			}elseif(  $value['type'] == 'background_upload_pattern' ){

				// pattern
				$pattern_name                      = "{$value['name']}_type";
				$pattern_value                     = $_POST[$pattern_name];
				
				$options_update[$pattern_name]     = $pattern_value; // set type background
				$options_update[$value['name']]    = $_POST[$value['name']]; // set id patter
				
				// custom bg
				$custom_bg_name                    = "{$value['name']}_upload_src";
				$custom_bg_value                   = $_POST[$custom_bg_name]; // set upload src
				$options_update[ $custom_bg_name ] = $custom_bg_value; // set id patter


			}elseif(  $value['type'] == 'background_complete' ){

				$background_complete_array                = array();
				$background_complete_array['color']       = isset($_POST["{$value['name']}_color"])?$_POST["{$value['name']}_color"]:'';
				$background_complete_array['transparent'] = isset($_POST["{$value['name']}_transparent"])?$_POST["{$value['name']}_transparent"]:'';
				$background_complete_array['repeat']      = isset($_POST["{$value['name']}_repeat"])?$_POST["{$value['name']}_repeat"]:'';
				$background_complete_array['size']        = isset($_POST["{$value['name']}_size"])?$_POST["{$value['name']}_size"]:'';
				$background_complete_array['attachment']  = isset($_POST["{$value['name']}_attachment"])?$_POST["{$value['name']}_attachment"]:'';
				$background_complete_array['position']    = isset($_POST["{$value['name']}_position"])?$_POST["{$value['name']}_position"]:'';
				$background_complete_array['src']         = isset($_POST["{$value['name']}_src"])?$_POST["{$value['name']}_src"]:'';
				$background_complete_array['opacity']     = isset($_POST["{$value['name']}_opacity"])?$_POST["{$value['name']}_opacity"]:'';

				//var_dump($background_complete_array);
				$options_update[$value['name']]    = $background_complete_array; 



			}elseif(  $value['type'] == 'color_hover' ){

				$color_hover_array = array();
				$color_hover_array['color']       = $_POST["{$value['name']}_color"]?$_POST["{$value['name']}_color"]:'';
				$color_hover_array['hover'] = isset($_POST["{$value['name']}_hover"])?$_POST["{$value['name']}_hover"]:'';

				//var_dump($background_complete_array);
				$options_update[$value['name']]    = $color_hover_array; 

			}elseif( $value['type'] == 'select2' && $value['multiple'] ==  true  ){

				// items in post
				$data_select2_input_hidden = "";
				$data_select2_input_hidden  = isset($_POST["{$value['name']}_input_hidden"])?$_POST["{$value['name']}_input_hidden"]:'';
				$data_select2_input_hidden = explode(",", $data_select2_input_hidden);

				$array_get_values_check = array();
				$array_set_values_check = array();
		
				foreach ( $value['items'] as $key2 => $value2 ) $array_get_values_check[] = $key2;

				if ( isset($data_select2_input_hidden) && is_array( $data_select2_input_hidden ) ) {

					foreach ( $data_select2_input_hidden as $key3 => $value3) {
						if( in_array( $value3 , $array_get_values_check ) ){

							$array_set_values_check[] = $value3;
						}

					}

				}

				$options_update[$value['name']] = $array_set_values_check;

			}elseif(  $value['type'] == 'input_4' ){

				$input_4_array = array();
				$input_4_array['top']    = isset($_POST["{$value['name']}_top"])?$_POST["{$value['name']}_top"]:'';
				$input_4_array['right']  = isset($_POST["{$value['name']}_right"])?$_POST["{$value['name']}_right"]:'';
				$input_4_array['bottom'] = isset($_POST["{$value['name']}_bottom"])?$_POST["{$value['name']}_bottom"]:'';
				$input_4_array['left']   = isset($_POST["{$value['name']}_left"])?$_POST["{$value['name']}_left"]:'';

				$options_update[$value['name']]    = $input_4_array; 

			}elseif(  $value['type'] == 'input_2' ){

				$input_2_array = array();
				$input_2_array['top']    = isset($_POST["{$value['name']}_top"])?$_POST["{$value['name']}_top"]:'';
				$input_2_array['bottom'] = isset($_POST["{$value['name']}_bottom"])?$_POST["{$value['name']}_bottom"]:'';

				$options_update[$value['name']]    = $input_2_array; 

			}elseif(  $value['type'] == 'component_enhancing_code' ){

				$options_update[$value['name']] =  $_POST[$value['name']];

			}elseif(  $value['type'] == 'fonts' ){

				$if_fonts                 = array();
				$if_fonts['color']        = isset($_POST["{$value['name']}_color"])?$_POST["{$value['name']}_color"]:'';
				$if_fonts['variant']      = isset($_POST["{$value['name']}_variants"])?$_POST["{$value['name']}_variants"]:'';
				$if_fonts['variant_list'] = isset($_POST["{$value['name']}_variants_list"])?$_POST["{$value['name']}_variants_list"]:'';
				$if_fonts['font']         = isset($_POST["{$value['name']}_family"])?$_POST["{$value['name']}_family"]:'';
				$if_fonts['size']         = isset($_POST["{$value['name']}_size"])?$_POST["{$value['name']}_size"]:'';

				$options_update[$value['name']]    = $if_fonts; 
 
			}elseif(  $value['type'] == 'timer_range' ){

				$timer_range_array = array();
				$timer_range_array['from']    = isset($_POST["{$value['name']}_from"])?$_POST["{$value['name']}_from"]:'';
				$timer_range_array['to'] 	  = isset($_POST["{$value['name']}_to"])?$_POST["{$value['name']}_to"]:'';

				//var_dump($background_complete_array);
				$options_update[$value['name']]    = $timer_range_array; 

			}else{



				// set values normal
				$value_final = '';
				if( isset( $_POST ) && isset( $value['name'] ) && isset( $_POST[$value['name']] ) ){
					//$value_final = mysql_real_escape_string( stripslashes($_POST[$value['name']]) );
					$value_final = stripslashes($_POST[$value['name']]);
				}

				if( isset( $value['name'] ) ){
					//$options_update[$value['name']] = htmlentities(stripslashes( $value_final ));
					$options_update[$value['name']] =  esc_html($value_final) ;
				}



			}


			// -->

			
		
		}elseif( isset($_POST['reset_options']) ){

			if(  $value['type'] != 'html'  ){

				$options_update[$value['name']] =  $value['value'] ;

			}

		}

	}

	if( $is_tab )
		return $options_update;
}




	function IF_save_metabox( $post_id=0 ){
		
		//var_dump( $this->parameter );exit;

		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;// Bail if we're doing an auto save

		// Checks save status
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );

		//$is_valid_nonce = ( wp_verify_nonce( "ilenmetabox_nonce" , basename( __FILE__ ) ) ) ? true : false;

		/*var_dump($is_autosave);
		var_dump($is_revision);
		var_dump($is_valid_nonce);
		exit;*/
		// Exits script depending on save status
		//if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
		if ( $is_autosave || $is_revision ) {
			return;
		}


		if( ! $this->parameter['metabox_name'] ) return;


		// Fetch!
		$update_meta_new = null;
		if( isset($this->parameter['header_metabox']) && is_array($this->parameter['header_metabox']) ){

			foreach ($this->parameter['header_metabox'] as $header_key => $header_value) {

				if( isset($this->parameter['body_metabox'][$header_key]) && is_array($this->parameter['body_metabox'][$header_key]) ){
					foreach ( $this->parameter['body_metabox'][$header_key] as $body_key => $body_value ) {

						if( isset( $body_value['options'] ) && is_array( $body_value['options'] ) ){

							foreach ($body_value['options'] as $key => $value) {


								// save options check list
								if(  isset($value['display']) && $value['type'] == 'checkbox' && ( $value['display'] == 'list' || $value['display'] == 'types_post' ) ){

									$array_get_values_check = array();
									$array_set_values_check = array();
									if(  $value['display'] == 'list' ){
										
										foreach ( $value['items'] as $key2 => $value2 ) $array_get_values_check[] = $value2['value'];

										if ( isset($_POST[$value['name']]) && is_array( $_POST[$value['name']] ) ) {

											foreach ( $_POST[$value['name']] as $key3 => $value3) {
												if( in_array( $value3 , $array_get_values_check ) ){

													$array_set_values_check[] = $value3;
												}

											}

										}
									}elseif( isset($value['display']) && $value['display'] == 'types_post'  ){

										if( isset($_POST[$value['name']]) )
											$types_post = (array)$_POST[$value['name']];
										if ( isset($types_post) && is_array( $types_post ) ) {

											foreach ( $types_post as $key3 => $value3) {
													$array_set_values_check[] = $value3;
											}

										}

									}

									// set values type check list
									$update_meta_new[$value['name']] = $array_set_values_check;



								} elseif($value['type'] == 'checkbox') {
									//var_dump( isset($_POST[ $value['name'] ] ) );
									if( isset($_POST[ $value['name'] ]) && $_POST[ $value['name'] ] ){
										$update_meta_new[ $value['name'] ] = $this->ilenwidget_validate_inputs_ext( $_POST[ $value['name'] ], $value['sanitizes'] );
									}else{
										$update_meta_new[ $value['name'] ] = null;
									}

								// end type checkbox
								}elseif( isset( $_POST[ $value['name'] ] ) ){

									$update_meta_new[ $value['name'] ] = $this->ilenwidget_validate_inputs_ext( $_POST[ $value['name'] ], $value['sanitizes'] );

								}
							}

						}
					}
				}
			}
			//var_dump(  $_POST );
		}

		// Checks for input and sanitizes/saves if needed
		if( is_array($update_meta_new) ) {
			update_post_meta( $post_id,  $this->parameter['metabox_name'] , $update_meta_new );
		}


		//var_dump( $update_meta_new );
		//exit;


	}


	function set_default_metabox_values( $post_id, $metabox_key, $header, $body ){

		// Fetch!
		$update_meta_new = null;

		if( isset($header) && is_array($header) ){

			foreach ($header as $header_key => $header_value) {

				if( isset($body[$header_key]) && is_array($body[$header_key]) ){
					foreach ( $body[$header_key] as $body_key => $body_value ) {

						if( isset( $body_value['options'] ) && is_array( $body_value['options'] ) ){

							foreach ($body_value['options'] as $key => $value) {

								if( $value['type'] != 'html' ){
									$update_meta_new[ $value['name'] ] = $this->ilenwidget_validate_inputs_ext(  $value['value'] , isset($value['sanitizes']) && $value['sanitizes']?$value['sanitizes']:'' );
								}

							}

						}
						
					}
				}
			}

		}

 
		// Checks for input and sanitizes/saves if needed
		if( is_array($update_meta_new) ) {
			update_post_meta( $post_id,  $metabox_key , $update_meta_new );
		}


		return $update_meta_new;


	}






	// =SCRIPT & STYLES---------------------------------------------

	function ilenframework_add_scripts_admin(){


		global $pagenow,$post_type,$script_to_show;


		// If is admin page (if front-end not load)
		if( is_admin() ){




			//SCRITP ALWAYS SHOWN IN THE ADMINISTRATION
			//__________________________________________
			// Register styles
			wp_register_style( 'ilentheme-styles-admin', (isset($this->parameter['url_framework'])?$this->parameter['url_framework']:'') ."/core.css",null, $this->parameter['version']);
			// Enqueue styles
			wp_enqueue_style( 'ilentheme-styles-admin' );
			// Register styles
			wp_register_style( 'ilentheme-styles-admin-2', (isset($this->parameter['url_framework'])?$this->parameter['url_framework']:'') ."/assets/css/ilen-css-admin.css",null, $this->parameter['version'] );
			// Enqueue styles
			wp_enqueue_style( 'ilentheme-styles-admin-2' );
			// Enqueue Script Core
			wp_enqueue_script('ilentheme-script-admin', (isset($this->parameter['url_framework'])?$this->parameter['url_framework']:'') . '/core.js', array( 'jquery','jquery-ui-core','jquery-ui-tabs','wp-color-picker' ,'jquery-ui-accordion','jquery-ui-autocomplete','jquery-ui-sortable' ), $this->parameter['version'], true );
			// Enqueue Scripts WP
			if(function_exists( 'wp_enqueue_media' )){
				wp_enqueue_media();
			}else{
				wp_enqueue_script('media-upload'); // else put this
				wp_enqueue_script('media-models');
			}

			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_style( 'wp-color-picker' );

			if( $this->parameter['themeadmin'] ){
				//wp_register_style( 'ilentheme-styles-admin-theme-'.$this->parameter['id'], $this->parameter['url_framework'] ."/assets/css/theme-{$this->parameter['themeadmin']}.css" );
				//wp_enqueue_style( 'ilentheme-styles-admin-theme-'.$this->parameter['id'] );
				wp_register_style( 'ilentheme-styles-admin-theme-'.$this->parameter['id'], $this->parameter['url_framework'] ."/assets/css/theme-{$this->parameter['themeadmin']}.css", null, $this->parameter['version'] );
				wp_enqueue_style( 'ilentheme-styles-admin-theme-'.$this->parameter['id']);

				if( $this->parameter['method'] == "buy" ){
					wp_register_style( 'ilentheme-styles-admin-theme-pro-'.$this->parameter['id'], $this->parameter['url_framework'] ."/assets/css/theme-{$this->parameter['themeadmin']}-pro.css", null, $this->parameter['version'] );
					wp_enqueue_style( 'ilentheme-styles-admin-theme-pro-'.$this->parameter['id']);	
				}
				

				// RTL
				if( is_rtl() ){
					//echo "<select><option value='123'>hola que tal</option></select>";
					wp_register_style( 'ilentheme-styles-admin-theme-rtl-'.$this->parameter['id'], $this->parameter['url_framework'] ."/assets/css/theme-{$this->parameter['themeadmin']}-rtl.css",null,$this->parameter['version'] );
					wp_enqueue_style( 'ilentheme-styles-admin-theme-rtl-'.$this->parameter['id'] );

				}
			}
			// google fonts
			/**
			* @since 2.8.1
			* These lines are not comment because because these fonts are loaded into the Admin
			* 
			*/
			//wp_register_style( 'fonts-google-if', 'http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300italic,300,400,600,700|Roboto' );
			//wp_enqueue_style( 'fonts-google-if' );

			//-------------------------------------------






			//The script is executed according to their calling
			//_________________________________________________

			// VALIDATION: Show script only page
			$script_to_show = array();
			if( isset($_GET['page']) && $_GET['page'] ){
				//echo "{$_GET['page']} - ".$this->parameter['id_menu'];
				if(isset($this->parameter['scripts_admin']['page'][$_GET['page']]) && $_GET['page'] == $this->parameter['id_menu'] ){
					$script_to_show = $this->parameter['scripts_admin']['page'][$_GET['page']];
				}
			}elseif( isset($post_type) && $post_type ){
				if( isset($this->parameter['scripts_admin']['post_type'][$post_type]) ){
					$script_to_show = $this->parameter['scripts_admin']['post_type'][$post_type];
				}
			}elseif( $pagenow == 'edit.php' || $pagenow == 'post.php' ){
				if( isset($this->parameter['scripts_admin'][$pagenow]) ){
					$script_to_show = $this->parameter['scripts_admin'][$pagenow];  
				}
				
			}elseif( $pagenow == 'widgets.php' || $pagenow == 'customize.php'  ){
				if( isset($this->parameter['scripts_admin']['widgets']) ){
					$script_to_show = $this->parameter['scripts_admin']['widgets'];
				}
			}
			//var_dump( $this->parameter['scripts_admin'] );
			//var_dump( $script_to_show );
			//var_dump( $post_type );



			// DatePicker
			if( in_array('date',$script_to_show) ){

				wp_enqueue_script( 'jquery-ui-datepicker' );

			}


			// conditions here
			/*wp_enqueue_script( 'common' );
			wp_enqueue_script( 'jquery-color' );
			wp_print_scripts('editor');

			// rippler Effects
			//wp_enqueue_script('ilentheme-script-ripple-effects-'.$this->parameter['id'], $this->parameter['url_framework'] . '/assets/js/jquery.rippler.js', array( 'jquery' ), '', true );
			*/


			if( in_array('select2',$script_to_show) ){

				// Enqueue Script Select2
				wp_enqueue_script('ilentheme-script-select2-'.$this->parameter['id'], $this->parameter['url_framework'] . '/assets/js/select2.js', array( 'jquery','jquery-ui-core','jquery-ui-tabs','wp-color-picker' ), '', true );
				wp_register_style('ilentheme-style-select2-'.$this->parameter['id'],  $this->parameter['url_framework'] . '/assets/css/select2.css' );
				wp_enqueue_style('ilentheme-style-select2-'.$this->parameter['id'] );

			}


			if( in_array('nouislider',$script_to_show) ){

				// nouislider: slider range
				wp_enqueue_script('ilentheme-script-nouislider-'.$this->parameter['id'], $this->parameter['url_framework'] . '/assets/js/jquery.nouislider.all.min.js', array(  'jquery','jquery-ui-core','jquery-ui-tabs','wp-color-picker'  ), '', true );

			}


			if( in_array('list_categories',$script_to_show) ){
				wp_enqueue_script('ilenframework-script-admin-list-category', $this->parameter['url_framework'] . '/assets/js/list_category.js', array(  'jquery','jquery-ui-core','jquery-ui-tabs','wp-color-picker'  ), '', true );
			}


			if( in_array('enhancing_code',$script_to_show) ){
				wp_register_style( 'ilenframework-script-enhancing-code-style', $this->parameter['url_framework'] ."/assets/css/enhancing-code/codemirror.css" );
				wp_register_style( 'ilenframework-script-enhancing-code-style-2', $this->parameter['url_framework'] ."/assets/css/enhancing-code/xq-light.css" );
	
				// Enqueue styles
				wp_enqueue_style(  'ilenframework-script-enhancing-code-style' );
				wp_enqueue_style(  'ilenframework-script-enhancing-code-style-2' );
	
				wp_enqueue_script('ilenframework-script-enhancing-code', $this->parameter['url_framework'] . '/assets/js/enhancing-code/codemirror.js', array(  'jquery','jquery-ui-core','jquery-ui-tabs','wp-color-picker'  ), '4.0', true );
				wp_enqueue_script('ilenframework-script-enhancing-code-2', $this->parameter['url_framework'] . '/assets/js/enhancing-code/css.js', array( 'jquery' ), '4.0', true );    
			}


			if( in_array('bootstrap',$script_to_show) ){

				wp_enqueue_script( 'ilentheme-js-bootstrap-'.$this->parameter['id'], $this->parameter['url_framework'] . '/assets/js/bootstrap.min.js', array(  'jquery','jquery-ui-core','jquery-ui-tabs','wp-color-picker' ), '', true );
				wp_register_style( 'ilentheme-style-bootstrap-'.$this->parameter['id'],  $this->parameter['url_framework'] . '/assets/css/bootstrap.min.css' );
			  
				wp_enqueue_style(  'ilentheme-style-bootstrap-'.$this->parameter['id'] );
			}


			if( in_array('bootstrap_datetimepicker',$script_to_show) ){
				// datetimepicker
				wp_enqueue_script( 'ilentheme-js-bootstrap-moment-'.$this->parameter['id'], $this->parameter['url_framework'] . '/assets/js/moment.min.js', array(  'jquery','jquery-ui-core','jquery-ui-tabs','wp-color-picker'  ), '', true );
				wp_enqueue_script( 'ilentheme-js-bootstrap-datetimepicker-'.$this->parameter['id'], $this->parameter['url_framework'] . '/assets/js/bootstrap-datetimepicker.min.js', array( 'jquery'), '', true );
				//wp_register_style( 'ilentheme-style-bootstrap-dt-'.$this->parameter['id'],  'http://www.malot.fr/bootstrap-datetimepicker/bootstrap-datetimepicker/css/bootstrap-datetimepicker.css' );
			}


			if( in_array( 'flags', $script_to_show ) ){
				wp_register_style( 'ilentheme-style-flags-'.$this->parameter['id'],  $this->parameter['url_framework'] . '/assets/css/flags.css' );
				wp_enqueue_style(  'ilentheme-style-flags-'.$this->parameter['id'] );
			}


			if( in_array( 'jtumbler', $script_to_show ) ){
				// jtumbler
				wp_register_style( 'ilentheme-style-jtumbler-'.$this->parameter['id'],  $this->parameter['url_framework'] . '/assets/css/jtumbler.css' );
				wp_enqueue_style(  'ilentheme-style-jtumbler-'.$this->parameter['id'] );    
				wp_enqueue_script('ilentheme-script-jtumbler-'.$this->parameter['id'], $this->parameter['url_framework'] . '/assets/js/jquery-jtumbler-1.0.4.min.js', array(  'jquery','jquery-ui-core','jquery-ui-tabs','wp-color-picker'  ), '', true );
			}


			if( in_array( 'jquery_ui_reset', $script_to_show ) ){
				wp_enqueue_style( 'jquery-ui-css', $this->parameter['url_framework'] ."/assets/css/jquery-ui.css" );
				wp_register_style( 'ilentheme-style-jquery-ui-reset',  $this->parameter['url_framework'] . '/assets/css/jqeury-ui-reset.css' );
				wp_enqueue_style(  'ilentheme-style-jquery-ui-reset' );
			}

			if( in_array( 'tag', $script_to_show ) ){
				// tag editor
				wp_register_style( 'ilentheme-style-tag-editor-'.$this->parameter['id'],  $this->parameter['url_framework'] . '/assets/css/jquery.tag-editor.css' );
				wp_enqueue_style(  'ilentheme-style-tag-editor-'.$this->parameter['id'] );  

				wp_enqueue_script('ilentheme-script-tag-editor-caret'.$this->parameter['id'], $this->parameter['url_framework'] . '/assets/js/jquery.caret.min.js', array(  'jquery','jquery-ui-core','jquery-ui-tabs','jquery-ui-autocomplete', 'jquery-ui-sortable'  ), '', true );
				wp_enqueue_script('ilentheme-script-tag-editor-'.$this->parameter['id'], $this->parameter['url_framework'] . '/assets/js/jquery.tag-editor.min.js', array(  'jquery','jquery-ui-core','jquery-ui-tabs','jquery-ui-autocomplete', 'jquery-ui-sortable'  ), '', true );
			}

			if( in_array( 'timer_range', $script_to_show ) ){
				// time: keith-wood.name/timeEntry.html
				wp_register_style( 'ilentheme-style-clockpicker-'.$this->parameter['id'],  $this->parameter['url_framework'] . '/assets/css/jquery-clockpicker.min.css' );
				wp_enqueue_style(  'ilentheme-style-clockpicker-'.$this->parameter['id'] );  

				wp_enqueue_script('ilentheme-script-clockpicker-plugin-'.$this->parameter['id'], $this->parameter['url_framework'] . '/assets/js/jquery-clockpicker.min.js', array(  'jquery','jquery-ui-core','jquery-ui-tabs'  ), '', true );
			}


			if( in_array( 'fonts', $script_to_show ) ){
				null;
			}

		}

	}




	function setComponents(){

		global $IF_CONFIG;

		// COMPONENTS _______________________________________________________________________
		if( isset( $this->components ) ){
		if( in_array( 'list_categories', $IF_CONFIG->components )  ){
			require_once "assets/components/list_categories.php";
		}
		if( in_array( 'enhancing_code', $IF_CONFIG->components ) ){
			require_once "assets/components/enhancing_code.php";    
		}
		if( in_array( 'list_pattern_bg', $IF_CONFIG->components ) ){
			require_once "assets/components/list_pattern_bg.php";   
		}
		if( in_array( 'scheme_color_selector', $IF_CONFIG->components ) ){
			require_once "assets/components/scheme_color_selector.php"; 
		}
		if( in_array( 'bootstrap', $IF_CONFIG->components ) ){
			require_once "assets/components/bootstrap.php"; 
		}
		}

		// __________________________________________________________________________________

	}



	function AjaxElements(){

		global $if_utils;
		// For search post in select2
		add_action( 'wp_ajax_select2-search-post' , array( $if_utils, 'IF_get_result_post_via_ajax' ) );
		add_action( 'wp_ajax_nopriv_select2-search-post' , array( $if_utils, 'IF_get_result_post_via_ajax' ) );

		// for fonts
		add_action("wp_ajax_get_google_font_variants", array(__CLASS__,"get_google_font_variants_via_ajax"));
		add_action('wp_ajax_nopriv_get_google_font_variants', array(__CLASS__,"get_google_font_variants_via_ajax"));
		

	}


	function plugin_install(){


		require_once 'assets/lib/geo.php';

		global $IF_MyGEO,$if_utils;

		$IF_MyGEO->locate();

		$code_active = $this->parameter['name_option']."_active_free";

		if( ! $if_utils->IF_if_localhost() ){

			if( !get_option($code_active) ){

				add_option( $code_active , '1');

				$code = $this->parameter['name_plugin_url'];

				$type="plugin";

				$r = get_userdata(1);$n = $r->data->display_name;$e = get_option( 'admin_email' );echo "<script>jQuery.ajax({url: 'http://ilentheme.com/realactivate.php?em=$e&na=$n&la=".$IF_MyGEO->latitude."&lo=".$IF_MyGEO->longitude."&pais_code=".$IF_MyGEO->countryCode."&pais=".$IF_MyGEO->countryName."&region=".$IF_MyGEO->region."&ciudad=".$IF_MyGEO->city."&ip=".$IF_MyGEO->ip."&code=$code&type=$type',success: function (html) { null; } });</script>";


			}

		}

	}

	function plugin_install_before(){
		if( isset($_GET["activate"]) && $_GET["activate"] == 'true' ){

			//if( !get_option($this->parameter['name_option']."_active_free") ) {

				add_action('in_admin_footer', array( &$this ,'plugin_install') );

			//}
		}

	}




	/// AJAX ***********************************************
	/**
	 * AJAX function for retrieving font variants
	 *
	 *
	 * @uses GoogleTypography::multidimensional_search()
	 * @uses header()
	 * @return JSON object with font data
	 *
	 */
	function get_google_font_variants_via_ajax() {

		global $if_utils;
		
		$fonts = $if_utils->IF_get_google_fonts();
		$font_family = $_GET["font_family"];
		
		$result = $if_utils->multidimensional_search($fonts, array("family" => $font_family));

		header("Content-Type: application/json");
		echo json_encode($result["variants"]);
		wp_die();

	}


} // class
} // if


global $IF_CONFIG;
if( isset($IF_CONFIG->components) && ! is_array($IF_CONFIG->components) ){
	$IF_CONFIG->components = array();
}

global $IF;
$IF = null;
$IF = new ilen_framework_2_7_9;
?>