<?php 
/**
 * Components: List Categories
 * @package ilentheme
 * 
 */



if ( !class_exists('ilenframework_component_list_category') ) {

class ilenframework_component_list_category{

	function __construct(){

		// add scripts & styles
		//add_action('admin_enqueue_scripts', array( &$this,'load_script_and_styles') );

	}

	function IF_getCategories( $args = array(), $text_first_element ){

		global $IF;
		
		$array_categories['-1'] = __( $text_first_element ,$IF->parameter['name_option'] );

		$categories = get_categories( $args );

		if( $categories ){

			foreach ($categories as $key => $value) {

				$array_categories[$value->cat_ID] = $value->name;

			}

		}

		return $array_categories;


		
	}


	function display( $id_name , $seleted_array = array(), $text_first_element="All" ){			

		//code 
		if( !is_array(  $seleted_array ) ){
			$seleted_array = array();
		}

		$list_categories = $this->IF_getCategories(array(), $text_first_element);

		$style_check = "";
		if( in_array("-1", $seleted_array ) ){

			$style_check = "style='display:none'";

		}


		foreach ($list_categories as $key2 => $value2): ?>

			<div class="row_checkbox_list <?php if( $key2 != "-1"){ echo "checked_hidden"; } ?>" <?php if( $key2 != "-1"){ echo $style_check; } ?>>
				<input  type="checkbox" <?php if( in_array( $key2  ,  $seleted_array ) ){ echo " checked='checked' ";} ?> name="<?php echo $id_name ?>[]" id="<?php echo $id_name."_". $key2 ?>" value="<?php echo $key2; ?>" class="<?php if($key2=="-1"){ echo "check_all"; } ?>" />
				<label for="<?php echo $id_name."_". $key2 ?>"><span class="ui"></span></label>
				&nbsp;<?php echo  $value2; ?> 
				<div class="help"></div>
			</div>

		<?php endforeach; 
		
	}

	// =SCRIPT & STYLES---------------------------------------------
	/*function load_script_and_styles(){
		//code 
		global $IF;
		if( is_admin()  && isset($_GET["page"]) == $IF->parameter['id_menu'] ){

			wp_enqueue_script('ilenframework-script-admin-list-category', $IF->parameter['url_framework'] . '/assets/js/list_category.js', array( 'jquery' ), '', true );

		}
	}*/






 

} // class

} // if


global $IF_COMPONENT;

$IF_COMPONENT['component_list_category'] = new ilenframework_component_list_category;

?>