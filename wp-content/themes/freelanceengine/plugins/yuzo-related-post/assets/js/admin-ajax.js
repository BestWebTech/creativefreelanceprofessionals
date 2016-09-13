 
function delete_transient(button){
	
	jQuery(button).children().addClass("ajax");
	var data = {
		'action': 'ajax_delete_yuzo_data_admin',
		'yuzo_actions': 'transient'
	};

	jQuery.post(ajaxurl, data, function(response) {
		alert('Ok! The transient Yuzo have been removed successfully.');
		jQuery(button).children().removeClass("ajax");
	});

}


function delete_meta_yuzo_ok(button){
	jQuery(button).children().addClass("ajax");
	var data = {
		'action': 'ajax_delete_yuzo_data_admin',
		'yuzo_actions': 'meta'
	};

	jQuery.post(ajaxurl, data, function(response) {
		alert("Ok! Meta 'yuzo_views' was removed successfully.");
		jQuery(button).children().removeClass("ajax");
	});

}
 