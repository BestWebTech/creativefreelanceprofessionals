<?php
/**
 * Pack Functions and Utils
 * iLenFramework 
 * @package ilentheme
 */
if ( !class_exists('IF_utils') ) {
class IF_utils{



/* FUNCTION GET IMAGE POST */
/*
// Thumbnail (default 150px x 150px max)
// Medium resolution (default 300px x 300px max)
// Large resolution (default 640px x 640px max)
// Full resolution (original size uploaded)
*/
function get_ALTImage($ID){
  return get_post_meta( $ID  , '_wp_attachment_image_alt', true);
}

/* get image for src image in post // get original size  */
function IF_catch_that_image( $post_id=null ) {

	global $post;

	$_post = null;
	if( is_object( $post ) && !is_admin() ){

		$post_id = $post;

	}elseif( is_admin() ) {

		$post_id = get_post($post_id);

	}

	if( $post_id ){
		$first_img = array();
		$matches   = array();
		$output    = '';
	 
		
	 
		ob_start();
		ob_end_clean();
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_id->post_content, $matches);

		if( isset($matches[1][0]) ){
		$first_img['src'] = $matches[1][0];
		}

		$first_img['alt'] = '';

		return $first_img;
	}else{
		return null;
	}

}

/* get featured image */
function IF_get_featured_image( $size = "medium", $post_id=null ){

	global $post;

   
	/*if( is_object( $post ) && !is_admin() ){

		$post_id = $post->ID;

	}elseif( is_admin() ) {

		$post_id = $post_id;

	}*/
 

	$url = array();
	if ( has_post_thumbnail($post_id) ) { // check if the post has a Post Thumbnail assigned to it.

		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), $size );
		$url['alt'] = $this->get_ALTImage( $post_id );
		$url['src'] = $thumb['0'];
		
	}


	return $url; 
}


/* get attachment image */
function IF_get_image_post_attachment( $size = "medium", $post_id=null, $order = 'DESC' ){
 
	$image = array();
	$args  = array(
	   'post_type' => 'attachment',
	   'numberposts' => 5,
	   'post_parent' => $post_id,
	   'order' => $order, 
	);


	$args = apply_filters( 'yuzo_attachment_query' , $args );

	$image['alt'] = $this->get_ALTImage($post_id);
	$attachments = get_posts( $args );
	//wp_reset_postdata();
	//var_dump( count($attachments) );exit;
	if ( $attachments ) {
		foreach ( $attachments as $attachment ) {
		   $_array_img = wp_get_attachment_image_src( $attachment->ID , $size );
		   $image['src']=$_array_img[0];
		   return $image;
		}
	}

	return $image;
}





/* get default imagen */
function IF_get_image_default2( $default_src="" ){
	$image = array();
	$image['alt']='';
	$image['src']= $default_src;

	return $image;

}



function IF_get_image( $size = 'medium' , $default = '', $post_id=null, $order = 'DESC' ) {

	$img = array();
	$img = $this->IF_get_featured_image($size,$post_id);

	if( isset($img['src']) ){
		return $img;
	}

	$img = $this->IF_get_image_post_attachment($size, $post_id, $order);

	if( isset($img['src']) ){
		return $img;
	}

	$img = $this->IF_catch_that_image( $post_id );

	if( isset($img['src']) ){
		return $img;
	}else{
		return $this->IF_get_image_default2( $default );
	}


}
/* END FUNCTION GET IMAGE POST */

/**
* Return post
* See the post via ajaxat
* @return $data
*
*/
function IF_get_result_post_via_ajax(){

	/*$array_value[] = array('id'=>'01','text'=>'blabla1');
	$array_value[] = array('id'=>'02','text'=>'blabla2');
	header( "Content-Type: application/json" );
	echo json_encode($array_value);
	die();
	exit();*/

	$term = (string) urldecode(stripslashes(strip_tags($_REQUEST['term'])));
	$image_default = $_REQUEST['image_default'];
	if (empty($term))
		die();

	$args = array(
		//'post_type' => $post_types,
		'post_status' => 'publish',
		'posts_per_page' => 15,
		's' => $term,
		//'fields' => 'ids'
	);

	$size = "thumbnail";

	$get_posts = get_posts($args);

	$found_posts = array();
	//$counter = 0;
	if ($get_posts) {
		foreach ($get_posts as $post) {

			$url = array();
			if( !isset( $url['src'] ) ){
				if ( has_post_thumbnail( $post->ID ) ) { // check if the post has a Post Thumbnail assigned to it.
					$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), $size);
					$url['src'] = $thumb['0'];
				}
			}

			if( !isset( $url['src'] ) ){
				$url = array();
				$args  = array(
				   'post_type' => 'attachment',
				   'numberposts' => -1,
				   'post_status' => null,
				   'post_parent' => $post->ID
				);
				$url['alt'] = $this->get_ALTImage($post->ID);
				$attachments = get_posts( $args );
				if ( $attachments ) {
					foreach ( $attachments as $attachment ) {
					   $_array_img = wp_get_attachment_image_src( $attachment->ID , $size );
					   $url['src']=$_array_img[0];
					   break;
					}
				}
			}

			if( ! isset($url['src']) ) {
				$url = array();
				$matches = array();
				$output = '';

				ob_start();
				ob_end_clean();
				$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);

				if( isset($matches[1][0]) ){
				$url['src'] = $matches[1][0];
				}

				$url['alt'] = '';
 
			}

			if(  ! isset($url['src'])  ){

				$url['src'] = $image_default;

			}

			$found_posts[] = array(  'id'   => $post->ID,
									 'text' => $this->IF_cut_text(get_the_title($post->ID),75),
									 'image'=> $url['src'] );
			//$counter++;
		}
	}
	wp_reset_postdata();
	//$response_found_posts['total'] = 10;
	//$response_found_posts['posts'] = $found_posts;
	// response output
	header( "Content-Type: application/json" );
	wp_send_json($found_posts); // http://codex.wordpress.org/Function_Reference/wp_send_json
	wp_die();  // IMPORTANT: don't forget to "exit"

}





// Class paginate [experimental]
function  IF_paginate( $total_rows,
					   $pagego,
					   $pagina,
					   $Nrecords=10,
					   $pagevar="pag",
					   $lang = ''){ 
				   
		$targetpage = "$pagego";
		$limit = $Nrecords; // TOTAL REGISTRATION PER PAGE
		$stages = 3;

		// Initial pagina num setup
		if ($pagina == 0 || !$pagina){$pagina = 1;}
		$prev = $pagina - 1;    
		$next = $pagina + 1;                            
		$lastpagina = ceil($total_rows/$limit);      
		$Lastpaginam1 = $lastpagina - 1;                    

		$paginate = '';

		if($lastpagina > 1)
		{
			$paginate .= "<div class='paginate'>";
			// Previous
			if ($pagina > 1){
				$paginate.= "<a class='' href='$targetpage&$pagevar=$prev'>".__('Previous',$lang)."</a>";
			}else{
				$paginate.= "<span class=' disabled'>".__('Previous',$lang)."</span>";   }

			// paginas  
			if ($lastpagina < 7 + ($stages * 2))    // Not enough paginas to breaking it up
			{
				for ($counter = 1; $counter <= $lastpagina; $counter++)
				{
					if ($counter == $pagina){
						$paginate.= "<span class='current'>$counter</span>";
					}else{
						$paginate.= "<a class='' href='$targetpage&$pagevar=$counter'>$counter</a>";}                   
				}
			}
			elseif($lastpagina > 5 + ($stages * 2)) // Enough paginas to hide a few?
			{
				// Beginning only hide later paginas
				if($pagina < 1 + ($stages * 2))     
				{
					for ($counter = 1; $counter < 4 + ($stages * 2); $counter++)
					{
						if ($counter == $pagina){
							$paginate.= "<span class='current'>$counter</span>";
						}else{
							$paginate.= "<a  class='' href='$targetpage&$pagevar=$counter'>$counter</a>";}                  
					}
					$paginate.= "<a href='#'>....</a>";
					$paginate.= "<a  class='' href='$targetpage&$pagevar=$Lastpaginam1'>$Lastpaginam1</a>";
					$paginate.= "<a  class='' href='$targetpage&$pagevar=$lastpagina'>$lastpagina</a>";     
				}
				// Middle hide some front and some back
				elseif($lastpagina - ($stages * 2) > $pagina && $pagina > ($stages * 2))
				{
					$paginate.= "<a class='' href='$targetpage&$pagevar=1'>1</a>";
					$paginate.= "<a class='' href='$targetpage&$pagevar=2'>2</a>";
					$paginate.= "<a href='#'>....</a>";
					for ($counter = $pagina - $stages; $counter <= $pagina + $stages; $counter++)
					{
						if ($counter == $pagina){
							$paginate.= "<span class='current'>$counter</span>";
						}else{
							$paginate.= "<a  class='' href='$targetpage&$pagevar=$counter'>$counter</a>";
						}                   
					}
					$paginate.= "<a href='#'>....</a>";
					$paginate.= "<a  class='' href='$targetpage&$pagevar=$Lastpaginam1'>$Lastpaginam1</a>";
					$paginate.= "<a  class='' href='$targetpage&$pagevar=$lastpagina'>$lastpagina</a>";     
				}
				// End only hide early paginas
				else
				{
					$paginate.= "<a class='' href='$targetpage&$pagevar=1'>1</a>";
					$paginate.= "<a class='' href='$targetpage&$pagevar=2'>2</a>";
					$paginate.= "<a href='#'>....</a>";
					for ($counter = $lastpagina - (2 + ($stages * 2)); $counter <= $lastpagina; $counter++)
					{
						if ($counter == $pagina){
							$paginate.= "<span class='' class='current'>$counter</span>";
						}else{
							$paginate.= "<a class='' href='$targetpage&$pagevar=$counter'>$counter</a>";}                   
					}
				}
			}
						
					// Next
			if ($pagina < $counter - 1){
				$paginate.= "<a class='' href='$targetpage&$pagevar=$next'>".__('Next',$lang)."</a>";
			}else{
				$paginate.= "<span class=' disabled' >".__('Next',$lang)."</span>";
				}
			// calculo
			$desc_text_end = $pagina * $Nrecords;
			$desc_text_begin = $desc_text_end - $Nrecords;
			$desc_text_begin = ($desc_text_begin==0)?1:$desc_text_begin;

			$paginate .="</div>";
	   }
	 // pagination
	  return $paginate;

	 //FIN PAGINACION // PAGINACION *******************************************************************************************************
}



function IF_get_option( $subject ){
 
	require_once(ABSPATH . 'wp-includes/pluggable.php');

	$transient_name = "cache_".$subject;
	$cacheTime = 10; // Time in minutes between updates.

	if( function_exists('bbp_is_user_keymaster') ){ // fix problem bbpress

		//if(  false === ($data = get_transient($transient_name) ) || ( bbp_is_user_keymaster() && !isset($_GET['P3_NOCACHE']) )  ){
	 
			$new_array = array();
			$new_data = get_option( $subject."_options" );

			if( is_array( $new_data ) ){
				foreach ($new_data as $key => $value) {
					$new_array[ str_replace($subject.'_', '', $key) ] = $value;
				}
			}

			$data = json_decode (json_encode ($new_array), FALSE);

			//set_transient( $transient_name , $data, MINUTE_IN_SECONDS * $cacheTime);

			return $data;

		/*} else {

			return $data;

		}*/
	}else{

		//if(  false === ($data = get_transient($transient_name) ) || ( current_user_can( 'manage_options' ) && !isset($_GET['P3_NOCACHE']) )  ){
	 
			$new_array = array();
			$new_data = get_option( $subject."_options" );

			if( is_array( $new_data ) ){
				foreach ($new_data as $key => $value) {
					$new_array[ str_replace($subject.'_', '', $key) ] = $value;
				}
			}

			$data = json_decode (json_encode ($new_array), FALSE);

			//set_transient( $transient_name , $data, MINUTE_IN_SECONDS * $cacheTime);

			return $data;

		/*} else {

			return $data;

		}*/


	}

		
	
}




function IF_getyoutubeID( $url ){
	$matches = null;
	preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);    
	if( isset($matches[1]) && $matches[1] )
		return (string)$matches[1];
}


/*
*  @see http://stackoverflow.com/posts/2068371/revisions
*/ 
function IF_getyoutubeThumbnail( $id_youtube ){
	

	return "https://img.youtube.com/vi/$id_youtube/hqdefault.jpg";
	
}



/**
* set string in html characteres, UTF-8
*/
function IF_setHtml( $s ){

	return html_entity_decode( stripslashes($s), ENT_QUOTES, 'UTF-8' );

}





/**
* Convert Hex Color to RGB
* @link http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/
*/

function IF_hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
	  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
	  $r = hexdec(substr($hex,0,2));
	  $g = hexdec(substr($hex,2,2));
	  $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   return implode(",", $rgb); // returns the rgb values separated by commas
   //return $rgb; // returns an array with the rgb values
}



/**
* Convert hexdec color string to rgb(a) string
* @link http://mekshq.com/how-to-convert-hexadecimal-color-code-to-rgb-or-rgba-using-php/
*/
function IF_hex2rgba($color, $opacity = false, $to_array = false) {

	$default = 'rgb(0,0,0)';

	//Return default if no color provided
	if(empty($color))
		  return $default; 

	//Sanitize $color if "#" is provided 
		if ($color[0] == '#' ) {
			$color = substr( $color, 1 );
		}

		//Check if color has 6 or 3 characters and get values
		if (strlen($color) == 6) {
				$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
				$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
				return $default;
		}

		//Convert hexadec to rgb
		$rgb =  array_map('hexdec', $hex);

		//Check if opacity is set(rgba or rgb)
		if($opacity){
			if(abs($opacity) > 1)
				$opacity = 1.0;
			$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
		}elseif( $to_array == true ){
			$output = $rgb;
		}else {
			$output = 'rgb('.implode(",",$rgb).')';
		}
		//Return rgb(a) color string
		return $output;
}

/**
* MIX COLORS
* @link https://gist.github.com/andrewrcollins/4570993
* @since 2.7.5 It was commented that causes problems with versions of PHP.
* @since 2.7.7 reactivate.
*/
/*function IF_mix_colors($color_1 = array(0, 0, 0), $color_2 = array(0, 0, 0), $weight = 0.5){
	$f = @function($x) use ($weight) { return $weight * $x; };
	$g = @function($x) use ($weight) { return (1 - $weight) * $x; };
	$h = @function($x, $y) { return round($x + $y); };
	return @array_map($h, array_map($f, $color_1), array_map($g, $color_2));
}

function IF_rgb2hex($rgb = array(0, 0, 0)){
	$f = @function($x) { return str_pad(dechex($x), 2, "0", STR_PAD_LEFT); };
	return "#" . implode("", array_map($f, $rgb));
}

function IF_tint($color, $weight = 0.5){
	$t = $color;
	if(is_string($color)) $t = $this->IF_hex2rgba($color,false,true);
	$u = $this->IF_mix_colors($t, array(255, 255, 255), $weight);
	if(is_string($color)) return $this->IF_rgb2hex($u);
	return $u;
}*/



/**
* Correctly determine if date string is a valid date in that format
* @link http://stackoverflow.com/questions/19271381/correctly-determine-if-date-string-is-a-valid-date-in-that-format#comment37720734_19271434
*/
function IF_isDateFormat( $date ){
	$d = DateTime::createFromFormat('Y-m-d', $date);
	return $d && $d->format('Y-m-d') == $date;
}


/**
* Date Diff
* @link http://php.net/manual/es/function.date-diff.php
*/
function IF_dateDifference( $date_1 , $date_2 , $differenceFormat = '%r%a' ){
	$datetime1 = date_create($date_1);
	$datetime2 = date_create($date_2);
	
	$interval = date_diff($datetime1, $datetime2);
	
	return $interval->format($differenceFormat);
}




/**
* Return without shortcode text
* @param String $text (text to replace)
* @return $new_text
* 
* @since 2.0
* @since 2.7.4 modified preg_replace
* @since 2.7.9 modified preg_replace
*/
function IF_removeShortCode( $text ){
	
	//$new_text = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '',  $text );
	//$new_text = preg_replace( "~(?:\[/?)[^/\]]+/?\]~s", '',  $text );
	
	/**
	 * 
	 * @link http://endlessgeek.com/2014/02/wordpress-strip-shortcodes-excerpts/
	 */
	$new_text = preg_replace( '/\[[^\]]+\]/', '',  $text );

	return $new_text;
}



/**
* Return text cut
* Use strip_tags for text with and without HTML format
* @return $new_txt
*
*/
function IF_cut_text(  $text = "",  $length = 30, $strip_tags = false ){

	$new_txt = $this->IF_removeShortCode( $text );
	//$new_txt = strip_shortcodes( $new_txt );
	$new_txt  = trim( $new_txt );

	if( $strip_tags == true ){
		$new_txt = strip_tags($new_txt);
	}else{
		$new_txt = $new_txt;
	}
  
	if( strlen( $new_txt  ) > (int)$length ){
		$new_txt = mb_substr( $new_txt , 0 , (int)$length )."...";
	}else{
		$new_txt = mb_substr( $new_txt , 0 , (int)$length );
	}

	// @link https://wordpress.org/support/topic/stripping-shortcodes-keeping-the-content?replies=16
	//$exclude_codes = 'shortcode_to_keep_1|keep_this_shortcode|another_shortcode_to_keep';
	//$the_content = get_the_content();
	//$the_content= preg_replace("~(?:\[/?)(?!(?:$exclude_codes))[^/\]]+/?\]~s", '', $the_content);  # strip shortcodes, keep shortcode content

	return $new_txt;

}



/**
* Return true/false
* Detect if document index is localhost
* @return boolean
*
*/
function IF_if_localhost(){

	if (substr($_SERVER['REMOTE_ADDR'], 0, 4) == '127.'
			|| $_SERVER['REMOTE_ADDR'] == '::1') {
		return true;
	}else{
		return;
	}

}

/**
* Return String modify
* Insert string at specified position (for string)
* Example: $oldstring = $args['before_widget'];
*          $okok = insert_text_middel($oldstring, "<aside ", "style='background:black;'");
* @return String
*
*/
function IF_insert_text_middel($string, $keyword, $body) {
   return substr_replace($string, PHP_EOL . $body, strpos($string, $keyword) + strlen($keyword), 0);
}





/**
 * Function for retrieving and saving fonts from Google
 *
 *
 * @uses get_transient()
 * @uses set_transient()
 * @uses wp_remote_get()
 * @uses wp_remote_retrieve_body()
 * @uses json_decode()
 * @return JSON object with font data
 *
 */
function IF_get_google_fonts() {

	if( $this->IF_if_localhost() ){
		$api_google_fonts_url = get_template_directory_uri()."/framework/ilenframework/assets/lib/webfonts.json";
	}else{
		$api_google_fonts_url = "https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyCjae0lAeI-4JLvCgxJExjurC4whgoOigA";
	}
	
	$fonts_url = "//fonts.googleapis.com/css?family=";
	$fonts = get_transient("ilen_google_typography_fonts"); 

	if (false === $fonts)   {
		//$request = wp_remote_get( $api_google_fonts_url );
		$request = wp_remote_get( $api_google_fonts_url  );

		if(is_wp_error($request)) {

		   $error_message = $request->get_error_message();

		   echo "Something went wrong: $error_message";

		} else {

			$json = wp_remote_retrieve_body($request);

			$data = json_decode($json, TRUE);

			$items = $data["items"];
			
			$i = 0;
			
			foreach ($items as $item) {
				
				$i++;
				
				$variants = array();
				foreach ($item['variants'] as $variant) {
					if(!stripos($variant, "italic") && $variant != "italic") {
						if($variant == "regular") {
							$variants[] = "normal";
						} else {
							$variants[] = $variant;
						}
					}
				}

				$fonts[] = array("uid" => $i, "family" => $item["family"], "variants" => $variants);

			}
			
			set_transient("ilen_google_typography_fonts", $fonts, 60 * 60 * 24);
		}

	}

	return $fonts;
}



/**
 * Function for searching array of fonts
 *
 *
 * @return JSON object with font data
 *
 */
function multidimensional_search($parents, $searched) {
  if(empty($searched) || empty($parents)) {
    return false;
  }

  foreach($parents as $key => $value) {
    $exists = true;
    foreach ($searched as $skey => $svalue) {
      $exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue);
    }
    if($exists){ return $parents[$key]; }
  }

  return false;
}


/**
 * Build the frontend CSS to apply to wp_head()
 *
 *
 */
function build_frontend_fonts( $selector, $font, $size = null, $variant = null, $color = null, $add_style_tag = false ) {

	$font_styles = "";
	$frontend    = "";
	if( isset($selector) && $selector != "") {

		$font_styles .= $selector . '{ ';
		$font_styles .= 'font-family: "' . $font . '"!important; ';
		if( $variant ){
			$font_styles .= 'font-weight: ' . $variant . '!important; ';
		}
		if($size!=null) {
			$font_styles .= 'font-size: ' . $size . '!important; ';
		}
		if($color!=null) {
			$font_styles .= 'color: ' . $color . '!important; ';
		}
		$font_styles .= " }"; // \n

		if( $add_style_tag ) { $frontend = "\n<style type=\"text/css\">\n"; }
		$frontend .= $font_styles;
		if( $add_style_tag ) { $frontend .= "</style>\n"; }

		return $frontend;

	}

}


/**
 * Build the frontend CSS manual
 */
function build_frontend_css($selector, $property, $put_tag = false){

	$build_css = null;
	$build_css = $selector."{ $property }";

	if(  $put_tag == true ){
		$build_css = "<style>$build_css</style>";
	}

	return $build_css;

}


/**
 * Replace all accents for their equivalents without them
 *
 * @param $string
 *  string chain to clean
 *
 * @return $string
 *  string chain
 */
function clean_string($string){
    //Esta parte se encarga de eliminar cualquier caracter extraño
    $string = str_replace(
        array("\\", "¨", "º", "-", "~",
             "#", "@", "|", "!", "\"",
             "·", "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "`", "]",
             "+", "}", "{", "¨", "´",
             ">", "<", ";", ",", ":",
             ".", " "),
        '',
        $string
    );
    
    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );
 
    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );
 
    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );
 
    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );
 
    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );
 
    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C'),
        $string
    );

    $string = trim($string);
    return $string;
}





} // end class

global $if_utils;
$if_utils = new IF_utils;
} // end if



?>