jQuery(document).ready(function( $ ){


	$('#yuzo_related_post_disabled_counter').change(function() {
			if($(this).is(":checked")) {
					$( ".ilencontentwrapelements #box_e,.ilencontentwrapelements #box_g" ).fadeOut( 300, function() {
							//$(this).css("display","none");
					});
					//$(this).parent().parent().parent().addClass("active_fb");
			}else{
					$( ".ilencontentwrapelements #box_e,.ilencontentwrapelements #box_g" ).fadeIn( "slow", function() {
							$(this).css("display","block");
					});
			}
	});

	if($('#yuzo_related_post_disabled_counter').is(":checked")) {
			$( ".ilencontentwrapelements #box_e,.ilencontentwrapelements #box_g" ).fadeOut( 300, function() {
					//$(this).css("display","none");
			});
			//$('#wp_social_popup_button_fb').parent().parent().parent().removeClass("active_fb");

	}else{
			$( ".ilencontentwrapelements #box_e,.ilencontentwrapelements #box_g" ).fadeIn( "slow", function() {
					$(this).css("display","block");
			});
			//$('#wp_social_popup_button_fb').parent().parent().parent().addClass("active_fb");
	}



	$('#yuzo_related_post_exclude_category_-1').change(function() {
		if($(this).is(":checked")) {
			$( ".ilencontentwrapelements #box_d .IF_class_exclude_category_related" ).fadeOut( 300, function() {
				//$(this).css("display","none");
			});
		//$(this).parent().parent().parent().addClass("active_fb");
		}else{
			$( ".ilencontentwrapelements #box_d .IF_class_exclude_category_related" ).fadeIn( "slow", function() {
				$(this).css("display","block");
			});
		}
  	});

  	if($('#yuzo_related_post_exclude_category_-1').is(":checked")) {
			$( ".ilencontentwrapelements #box_d .IF_class_exclude_category_related" ).fadeOut( 300, function() {
				//$(this).css("display","none");
			});
		//$(this).parent().parent().parent().addClass("active_fb");
		}else{
			$( ".ilencontentwrapelements #box_d .IF_class_exclude_category_related" ).fadeIn( "slow", function() {
				$(this).css("display","block");
			});
		}





	$(".class_yuzo_meta_custom").css("display","none");
	$(".class_yuzo_meta_custom").css("background","#F9F9F9");
	

	$("#yuzo_related_post_meta_views").on('change',function(){

		if( $(this).val() == 'other' ){

			$(".class_yuzo_meta_custom").css("display","block");

		}else{

			$(".class_yuzo_meta_custom").css("display","none");

		}

	});


	$("#yuzo_related_post_related_to").on('change',function(){

		if( $(this).val() == '5' ){

			$(".class_order_by").css("display","none");
			$(".class_order").css("display","none");
			$(".class_order_by_taxonomias").css("display","block");
			

		} else {

			$(".class_order_by").css("display","block");
			$(".class_order").css("display","block");
			$(".class_order_by_taxonomias").css("display","none");

		}

	});


 
	$('.yuzo_style_chosse #yuzo_related_post_style_img_2').on("click", function(event){
		$("#yuzo_related_post_height_image").val(85);
		$("#yuzo_related_post_font_size").val(14);
		$("#yuzo_related_post_text2_length").val(150);
	});

	$('.yuzo_style_chosse #yuzo_related_post_style_img_1').on("click", function(event){
		$("#yuzo_related_post_height_image").val(145);
		$("#yuzo_related_post_font_size").val(13);
		$("#yuzo_related_post_text2_length").val(0);
	});

	if( $("#yuzo_related_post_meta_views").val() == 'other' ){
		$(".class_yuzo_meta_custom").css("display","block");
		$(".class_yuzo_meta_custom").css("background","rgb(249, 249, 249)");
	}



	if( $("#yuzo_related_post_related_to").val() == 5 ){

		$(".class_order_by").css("display","none");
		$(".class_order").css("display","none");
		$(".class_order_by_taxonomias").css("display","block");
		

	} else {

		$(".class_order_by").css("display","block");
		$(".class_order").css("display","block");
		$(".class_order_by_taxonomias").css("display","none");

	}


	// manipulate select theme
	$('.yuzo_chosse_theme_select select').appendTo('#chosse_yuzo_theme');
	$('.yuzo_chosse_theme_select').remove();
	



});

if( jQuery("#yuzo_related_post_type_image").val() == 'full-vertical' ){
	jQuery("._class_height_of_full_vertical").css("display","block");
}else{
	jQuery("._class_height_of_full_vertical").css("display","none");
}
jQuery("#yuzo_related_post_type_image").on("change",function(){
	if( jQuery(this).val() == 'full-vertical' ){
		jQuery("._class_height_of_full_vertical").css("display","block");
	}else{
		jQuery("._class_height_of_full_vertical").css("display","none");
	}
});

jQuery('#yuzo_related_post_theme').change(function() {
		if( jQuery(this).val() == "default") {
				//alert("selecciono default");
				jQuery("#code_yuzo_related_post_css_and_style").next().remove();
				var textArea2 = document.getElementById('code_yuzo_related_post_css_and_style');
				var editor2 = CodeMirror.fromTextArea(textArea2);
				editor2.getDoc().setValue( jQuery('#yuzo_css_default').text() );
		}else if( jQuery(this).val() == "magazine-alfa" ){
				jQuery("#code_yuzo_related_post_css_and_style").next().remove();
				var textArea2 = document.getElementById('code_yuzo_related_post_css_and_style');
				var editor2 = CodeMirror.fromTextArea(textArea2);
				editor2.getDoc().setValue( ".yuzo_wraps{\n \
						box-shadow: 0px 0px 8px -2px #333; \n \
						border-radius: 3px;\n \
	background: #ffffff;\n \
	padding: 10px;\n \
	background: -moz-linear-gradient(top, #ffffff 1%, #ffffff 27%, #e8e8e8 100%);\n \
	background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,#ffffff), color-stop(27%,#ffffff), color-stop(100%,#e8e8e8));\n \
	background: -webkit-linear-gradient(top, #ffffff 1%,#ffffff 27%,#e8e8e8 100%);\n \
	background: -o-linear-gradient(top, #ffffff 1%,#ffffff 27%,#e8e8e8 100%);\n \
	background: -ms-linear-gradient(top, #ffffff 1%,#ffffff 27%,#e8e8e8 100%);\n \
	background: linear-gradient(to bottom, #ffffff 1%,#ffffff 27%,#e8e8e8 100%);\n \
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e8e8e8',GradientType=0 );\n \
	height: auto!important;\n \
	float: left;\n \
	width: 98%;\n \
	margin-left: 1%;\n \
	box-sizing: border-box;\n \
	-moz-box-sizing: border-box;\n \
	-webkit-box-sizing: border-box;\n \
	-o-box-sizing: border-box;\n \
	-ms-box-sizing: border-box;\n \
}\n \
a.yuzo__text--title,\n \
.yuzo__text--title,\n \
.yuzo-list a.yuzo__text--title{\n \
	font-weight:bolder;\n \
	color:#000!important;\n \
}\n \
.yuzo__title::before{\n \
	 content: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAMAAADXqc3KAAAAA3NCSVQICAjb4U/gAAAACXBIWXMAAAphAAAKYQH8zEolAAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAAAEtQTFRF////AAAAAAAAAAAAAAAAAAAAAAAEAAADAAADAAACAAACAgACAgACAgACAgACAQABAQADAQADAQADAQACAQACAQACAQACAQACAQACjwA2xwAAABh0Uk5TAAIEIDA/QFFkeX2Ago2dvcDBwtDZ4OT+yEuE8AAAAHNJREFUKFOtkVkOgCAMREfFFRdc4f4ntWjUSG2Mie+PvswQKPAZ84QXbmbj2W1CsxItC99GIrtDgvDC3bmEmNjPCuq5ysCEVYcQE39XJRCqNIR3nAlbs0+saG4x9lEoUhITCreEixpINEDeshWarozZBa+sC18XgoSOCdYAAAAASUVORK5CYII71a91eb3cbaabc2cd8b11cc616e0253d);\n \
	 width: 32px;\n \
	 height: 32px;\n \
	 display: inline-block;\n \
	 position: relative;\n \
	 top: 6px;\n \
	 opacity: 0.6;\n \
}\n \
.yuzo__title h3,.yuzo__title{\n \
	display: inline-block;\n \
}" );
		}

});

jQuery(document).ready(function( $ ){

		if( jQuery("#yuzo_related_post_theme").val() == "magazine-alfa" ){
 
setTimeout(function(){
		jQuery("#yuzo_related_post_theme").val('default');
		jQuery("#yuzo_related_post_theme").val('magazine-alfa');
		jQuery("#code_yuzo_related_post_css_and_style").next().css("display","none");
				var textArea3 = document.getElementById('code_yuzo_related_post_css_and_style');
				var editor3 = CodeMirror.fromTextArea(textArea3);
				editor3.getDoc().setValue( "" );
				editor3.getDoc().setValue( ".yuzo_wraps{\n \
	box-shadow: 0px 0px 8px -2px #333; \n \
	border-radius: 3px;\n \
	background: #ffffff;\n \
	padding: 10px;\n \
	background: -moz-linear-gradient(top, #ffffff 1%, #ffffff 27%, #e8e8e8 100%);\n \
	background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,#ffffff), color-stop(27%,#ffffff), color-stop(100%,#e8e8e8));\n \
	background: -webkit-linear-gradient(top, #ffffff 1%,#ffffff 27%,#e8e8e8 100%);\n \
	background: -o-linear-gradient(top, #ffffff 1%,#ffffff 27%,#e8e8e8 100%);\n \
	background: -ms-linear-gradient(top, #ffffff 1%,#ffffff 27%,#e8e8e8 100%);\n \
	background: linear-gradient(to bottom, #ffffff 1%,#ffffff 27%,#e8e8e8 100%);\n \
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e8e8e8',GradientType=0 );\n \
	height: auto!important;\n \
	float: left;\n \
	width: 98%;\n \
	margin-left: 1%;\n \
	box-sizing: border-box;\n \
	-moz-box-sizing: border-box;\n \
	-webkit-box-sizing: border-box;\n \
	-o-box-sizing: border-box;\n \
	-ms-box-sizing: border-box;\n \
}\n \
a.yuzo__text--title,\n \
.yuzo__text--title,\n \
.yuzo-list a.yuzo__text--title{\n \
	font-weight:bolder;\n \
	color:#000!important;\n \
}\n \
.yuzo__title::before{\n \
	 content: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAMAAADXqc3KAAAAA3NCSVQICAjb4U/gAAAACXBIWXMAAAphAAAKYQH8zEolAAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAAAEtQTFRF////AAAAAAAAAAAAAAAAAAAAAAAEAAADAAADAAACAAACAgACAgACAgACAgACAQABAQADAQADAQADAQACAQACAQACAQACAQACAQACjwA2xwAAABh0Uk5TAAIEIDA/QFFkeX2Ago2dvcDBwtDZ4OT+yEuE8AAAAHNJREFUKFOtkVkOgCAMREfFFRdc4f4ntWjUSG2Mie+PvswQKPAZ84QXbmbj2W1CsxItC99GIrtDgvDC3bmEmNjPCuq5ysCEVYcQE39XJRCqNIR3nAlbs0+saG4x9lEoUhITCreEixpINEDeshWarozZBa+sC18XgoSOCdYAAAAASUVORK5CYII71a91eb3cbaabc2cd8b11cc616e0253d);\n \
	 width: 32px;\n \
	 height: 32px;\n \
	 display: inline-block;\n \
	 position: relative;\n \
	 top: 6px;\n \
	 opacity: 0.6;\n \
}\n \
.yuzo__title h3,.yuzo__title{\n \
	display: inline-block;\n \
}" ); 
		jQuery(".ilenplugin-yuzo_related_post #yuzo_related_post_theme").click();
		jQuery(".ilenplugin-yuzo_related_post #yuzo_related_post_theme").click();
		jQuery(".ilenplugin-yuzo_related_post .CodeMirror-code").click();
		jQuery(".ilenplugin-yuzo_related_post .CodeMirror-lines").click();
		jQuery(".ilenplugin-yuzo_related_post .CodeMirror").click();
		jQuery(".ilenplugin-yuzo_related_post .CodeMirror-scroll").click();
		
		
}
		, 1000);


		}



});