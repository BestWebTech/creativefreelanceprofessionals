
<div class="container">
<?php
include_once BUSINESS_PLUGIN_PATH."classes/settings.class.php";
$business_idea_setting = new business_idea_setting();
global $post;

	if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] === 'POST' ) && isset($_SERVER['CONTENT_LENGTH']) && (empty($_POST))){
		$max_post_size = ini_get('post_max_size');
		$content_length = $_SERVER['CONTENT_LENGTH'] / 1024 / 1024;
		if ($content_length > $max_post_size ) {
			print "<div class='error-alert'><strong>Error </strong>" .
				sprintf(__('It appears you tried to upload %d MiB of data but the PHP post_max_size is %d MiB.', 'csa-slplus'),$content_length,$max_post_size) .'<br/>'.__( 'Try increasing the post_max_size setting in your php.ini file.' , 'csa-slplus' ).'</div><br>';
				echo "<a class='idea_return_button' href=".get_permalink()." title='Return'>Return</a><br><br><br>";
			die;
		}
	}
if($_POST['submit']){
	$aVals = array();
	$aVals = $_POST['val'];
	if ( ! function_exists( 'wp_handle_upload' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}
	$fileArray = array();
	foreach($_FILES as $file){
		$uploadedfile = $file;
		$upload_overrides = array( 'test_form' => false );
		$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
		if (isset($movefile['error'])) {
		//if ( $movefile && !isset( $movefile['error'] ) ) {
			//echo "File is valid, and was successfully uploaded.\n";
			//return file url(url) and file path(file) and file type(type) in array
		/*} else {*/
			/**
			 * Error generated by _wp_handle_upload()
			 * @see _wp_handle_upload() in wp-admin/includes/file.php
			 */
			echo "<div class='error-alert'><strong>Error </strong>".$movefile['error']."</div><br>";
			echo "<a class='idea_return_button' href=".get_permalink()." title='Return'>Return</a><br><br><br>";
			die;
		}
		$fileArray[] = $movefile['url'];
	}
	$aVals['attachment'] = implode(',',$fileArray);
	if($business_idea_setting->add($aVals)){
		 echo "<div class='success-alert'><strong>Success </strong>Your Idea Successfully Submit We will touch you shortly</div><br>";
		 echo "<a class='idea_return_button' href=".get_permalink()." title='Return'>Return</a><br><br><br>";exit;		
	}
	
}


?>

	<form name="form" action="" id="business_idea_form" method="post" enctype="multipart/form-data">	
        <div id="idea-formm">
        	<div class="idea-formtable">
        		<div class="idea-lable-text">
        			<label for="full_name">Full Name <small class="required">*</small></label>
        		</div>
        		<div class="idea-input-box">
        			<input type="text" name="val[full_name]" id="full_name" class="input" value="" placeholder="Full Name" size="20">
		        </div>
        	</div>
        	<div class="idea-formtable">
        		<div class="idea-lable-text">
        			<label for="user_email">E-mail <small class="required">*</small></label>
        		</div>
        		<div class="idea-input-box idea_email">
        			<input type="text" name="val[user_email]" id="user_email" class="input" value="" placeholder="abc@xyz.com" size="25" >
		        </div>
        	</div>
            <div class="idea-formtable">
        		<div class="idea-lable-text">
        			<label for="start_up_name">Start Up Name <small class="required">*</small></label>
        		</div>
        		<div class="idea-input-box">
        			<input type="text" name="val[start_up_name]" id="start_up_name" class="input" value="" placeholder="Start Up Name" size="20">
		        </div>
        	</div>
        	<div class="idea-formtable">
        		<div class="idea-lable-text">
        			<label for="your_idea">Your Idea <small class="required">*</small></label>
        		</div>
        		<div class="idea-input-box">
                	<textarea placeholder="Share your idea" id="your_idea" rows="5" name="val[idea]" id="idea"></textarea>
		        </div>
        	</div>
		  	<div class="idea-formtable">
        		<div class="idea-lable-text">
        			<label for="budget">Budget <small class="required">*</small></label>
        		</div>
        		<div class="idea-input-box">
                	<input type="text" name="val[budget]" id="budget" class="input" value="" placeholder="0.00" size="20">
                    <div class="min_box"><small>Enter Your Business Idea Budget</small></div>
		        </div>
        	</div>
		  	<div class="idea-formtable">
        		<div class="idea-lable-text">
        			<label for="youtube_video"> Add YouTube Video</label>
        		</div>
        		<div class="idea-input-box">
                	<input type="text" name="val[youtube_video]" id="youtube_video" class="input" value="" placeholder="Youtube video Url" size="20">
                    <div class="min_box"><small>Enter Your Youtube video Url</small></div>
		        </div>
        	</div>
		  	<div class="idea-formtable">
        		<div class="idea-lable-text">
        			<label for="vimeo_video"> Add Vimeo Video</label>
        		</div>
        		<div class="idea-input-box">
                	<input type="text" name="val[vimeo_video]" id="vimeo_video" class="input" value="" placeholder="Vimeo Video Url" size="20">
                    <div class="min_box"><small>Enter Your Vimeo video Url</small></div>
		        </div>
        	</div>
           
            <div class="idea-formtable">
        		<div class="idea-lable-text">
        			<label for="upload_document">Upload Documents</label>
        		</div>
        		<div class="idea-input-box">
					<div class="input_fields_wrap">
                    	<div><input type="file" name="upload_document0" id="upload_document"/></div>
					</div>
                     <div class="min_box"><small>Upload File Limit 5 <?php /*?>Max Size (<?php echo ini_get('post_max_size'); ?>)<?php */?></small></div>
                    <a href="#" class="add_field_button">Add More Fields</a>                   
		        </div>
        	</div>
             <div class="idea-formtable">
        		<div class="idea-lable-text">
                 <small class="required">*</small> Required Fields.
        		</div>
        		<div class="idea-input-box">
                	<input type="submit" name="submit" id="idea_submit" value="submit"  />
                   
		        </div>
        	</div>
        <!-- Custom fields in Registration form ends -->
        <br class="clear">
        </div>
	</form>
	
</div>
