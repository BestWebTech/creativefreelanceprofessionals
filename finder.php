<?php ini_set('max_execution_time', 300); ?>
<html>
<head>
<script Language="javaScript">
<!-- 
function Blank_TextField_Validator()
{
// Check the value of the element named text_name from the form named text_form
if (findform.val.value == "")
{
  // If null display and alert box
   document.write('<p style="color:red; font-size:18px; font-weight:bold;">Please fill search box First !</p>');
  // Place the cursor on the field for revision
   text_form.text_name.focus();
  // return false to stop further processing
   return (false);
}
// If text_name is not null continue processing
return (true);
}
-->
</script>
<title>Find String</title>
<style type="text/css">
.result{float:left; width:900px; height:auto; border:1px #000000 solid;}
</style>
</head>
<body>
<div style="float:left; width:100%;">
  <div style="float:left; width:150px;color:#0000FF; font-size:18px; font-weight:bold; margin:13px;">Find code:</div>
  <div style="float:left; width:250px;">
    <form action="#" method="post" style="float: left;
width: 1000px;
height: 45px;
" name="findform" onSubmit="return Blank_TextField_Validator()">
      <input type="text" id="val" name="val" style="float: left;
width: 400px;
height: 45px;
border: 1px solid green; color:#0000FF; font-size:18px; font-weight:bold;" />
      <input type="submit" value="Search" style="float: left;
width: 100px;
height: 45px;
border: 1px solid #069; color:#0000FF; font-size:22px; font-weight:bold;" />
    </form>
  </div>
</div><br />
<?php 
error_reporting(0);
// Most hosting services will have a time limit on how long a php script can run, typically 30 seconds.
// On large sites with a lot of files this sript may not be able to find and check all files within the time limit.
// If you get a time out error you can try overideing the default time limits by removeing the // in the front of these two lines. 

// ini_set('max_execution_time', '0');
// ini_set('set_time_limit', '0');


find_files('.');
   
function find_files($seed)
{
    if(! is_dir($seed)) return false;
  $files = array();
  $dirs = array($seed);
  while(NULL !== ($dir = array_pop($dirs)))
        {
        if($dh = opendir($dir))
          {
            while( false !== ($file = readdir($dh)))
              {
                if($file == '.' || $file == '..') continue;
                  $path = $dir . '/' . $file;
                  if(is_dir($path)) {    $dirs[] = $path; }
                               
//  the line below tells the script to only check the content of files with a .php extension.
//  the if{} statement says if you "match" php[\d]?  at the end of the file name then check the contents
//  of the file.  The [\d]? part means also match if there is a digit \d such as .php4 in the file extension
            
                 //    else { if(preg_match('/\/*\.php[\d]?$/i', $path)) { check_files($path); }}
                               
// 07/26/2011  Based on some recent Pharma hacks I have changed the default to check php, js and txt files
                                else { 
								 
								if ( 'POST' == $_SERVER['REQUEST_METHOD'] ){
									/*
									if($_POST['val']=='')
									{
										echo '<p style="color:red; font-size:18px; font-weight:bold;">Please fill search box First !</p>';
									}
									else
									{*/
										 if(preg_match('/^.*\.(php[\d]?|js|txt)$/i', $path)) 
											{ 
												check_files($path); 
											
											}
									//}
									
								
									
								}
					} 
                               
//  if you would like to check other (all) file types you can comment out/un-comment and or modify 
//  the following lines as needed.  You can only have one of the else{} statements un-commented.
//  The first example contains a lengthy OR (the | means OR) statement, the part inside the (),
//  (php[\d]?|htm|html|shtml|js|asp|aspx) You can add/remove filetypes by modifying this part
//  (php[\d]?|htm|html|shtml) will only check .php, .htm, .html, .shtml  files.
                                 
                 //      else { if(preg_match('/^.*\.(php[\d]?|htm|html|shtml|js|asp|aspx)$/i', $path)) { check_files($path); }} 
                           
//  In the next else{} statement there is no if{}, no checking of the file extension every file will be checked.
                           
                 //      else { check_files($path); }   //  will check all file types for the code
                           
                            }
            closedir($dh);
}}}


function check_files($this_file)
{
// the varaiable $str_to_find contains the string to search for inside the single quotes.
//  if you want to search for other strings replace base64_decode with the string you want to search for.
        
        $str_to_find=$_POST['val']; // the string(code/text) to search for 
    
        if(!($content = file_get_contents($this_file))) { echo("<p>Could not check $this_file</p>\n"); }
    else { if(stristr($content, $str_to_find)) { echo("<p>$this_file -> contains $str_to_find</p>  <p><a href='".substr($this_file,1)."' target='_blank'>Open File</a></p>\n"); }}
	
    unset($content); 
}
?>

                          
                                
</body>
</html>