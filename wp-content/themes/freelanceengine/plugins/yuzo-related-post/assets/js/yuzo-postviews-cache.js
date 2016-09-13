jQuery(document).ready(function(){


	jQuery.ajax({
		type:"GET",
		url:viewsCacheL10n.admin_ajax_url,
		data:"postviews_id="+viewsCacheL10n.post_id+"&action=yuzo-plus-views&is_singular="+viewsCacheL10n.is_singular,
		cache:!1
	});

	

});
