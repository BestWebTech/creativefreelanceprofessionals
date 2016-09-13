jQuery(document).ready(function ($) {

	$(".component_list_categories .check_all").on("change",function(){

		if( $(this).prop('checked') ){

			$(this).parent().parent(".component_list_categories").children(".checked_hidden").css("display","none");

		}else{

			$(this).parent().parent(".component_list_categories").children(".checked_hidden").css("display","block");		

		}

	});

});