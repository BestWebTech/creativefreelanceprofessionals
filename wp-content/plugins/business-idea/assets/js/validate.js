jQuery(document).ready(function() {
	jQuery('#idea_submit').on('click',function(){
		var fullname = jQuery('#full_name').val();
		var email = jQuery('#user_email').val();
		var startupname = jQuery('#start_up_name').val();
		var your_idea = jQuery('#your_idea').val();
		var budget = jQuery('#budget').val();
		var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/; 
		if(fullname == "" )
		{
			alert( "Please provide your name!" );
			jQuery('#full_name').focus() ;
			return false;
		}
		if(email == "")
		{
			alert( "Please provide your Email!" );
			jQuery('#user_email').focus() ;
			return false;
		}
		else if(!email.match(mailformat)){
			alert("You have entered an invalid email address!"); 
			jQuery('#user_email').focus() ;
			return false;	
		}
		if(startupname == ""){
			alert("Please provide your start up name!"); 
			jQuery('#start_up_name').focus() ;
			return false;
		}
		if(your_idea == ""){
			alert("Please provide your Your Business Idea!"); 
			jQuery('#your_idea').focus() ;
			return false;
		}
		if(budget == ""){
			alert("Please provide your Business Idea Budget!"); 
			jQuery('#budget').focus() ;
			return false;
		}
		
		if(fullname != "" || email != "" || startupname != "" || your_idea != "" || budget != ""){
			return true;
		}
	})

});
jQuery(document).ready(function() {
    var max_fields      = 5; //maximum input boxes allowed
    var wrapper         = jQuery(".input_fields_wrap"); //Fields wrapper
    var add_button      = jQuery(".add_field_button"); //Add button ID
    var x = 1; //initlal text box count
    jQuery(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            jQuery(wrapper).append('<div><br /><input type="file" name="upload_document'+x+'"/><a href="#" class="remove_field">Remove</a></div>');
			//add input box
			x++; //text box increment
        }else{
			alert('Upload File Limit 5');
		}
    });
    jQuery(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); jQuery(this).parent('div').remove(); x--;
    })
});