jQuery(document).ready( function($) {
 
	jQuery('.ilentheme-options #tabs,.ilenplugin-options #tabs')
	  .tabs({  show: function(event, ui) {

		var lastOpenedPanel = $(this).data("lastOpenedPanel");

		if (!$(this).data("topPositionTab")) {
		    $(this).data("topPositionTab", $(ui.panel).position().top)
		}         

		//Dont use the builtin fx effects. This will fade in/out both tabs, we dont want that
		//Fadein the new tab yourself            
		$(ui.panel).hide().fadeIn(25);

		if (lastOpenedPanel) {

		    // 1. Show the previous opened tab by removing the jQuery UI class
		    // 2. Make the tab temporary position:absolute so the two tabs will overlap
		    // 3. Set topposition so they will overlap if you go from tab 1 to tab 0
		    // 4. Remove position:absolute after animation
		    lastOpenedPanel
			  .toggleClass("ui-tabs-hide")
			  .css("position", "absolute")
			  .css("top", $(this).data("topPositionTab") + "px")
			  .fadeOut(25, function() {
				$(this)
				.css("position", "");
			  });

		}

		//Saving the last tab has been opened
		$(this).data("lastOpenedPanel", $(ui.panel));

	  } } )
	  .addClass('ilencontentwrapelements ui-helper-clearfix');

    jQuery('.ilenmetabox-options #tabs')
	  .tabs()
	  .addClass('ilencontentwrapelements ui-helper-clearfix');



    //Custom
    // =save theme options
    jQuery(".ilentheme-options .btn_save,.ilenplugin-options .btn_save").on("click",function(event){
	  event.preventDefault();
	  jQuery( this ).find("i").addClass("spinInfinite");
	  ilenvalidatorsubmit();
	  document.frmsave.submit();
    });
 

    // for plugin
    jQuery(".ilenplugin-options .btn_reset, .ilentheme-options .btn_reset").on("click",function(event){
	  event.preventDefault();
	  if( confirm( jQuery(this).attr("data-me") ) ){
		jQuery( this ).find("i").addClass("spinInfinite");
		document.frmreset.submit();
	  }
    });
    // plugin donate
    /*jQuery(".ilenplugin-options .btn_donate").on("click",function(event){
	  event.preventDefault();
	  document.frm_donate.submit();
    });*/
    // end -> ONLY THEME 'plugin-fresh.css'



    var formfield;
    // upload file >3.5
    jQuery('.ilentheme-options .upload_image_button,.ilentheme-options .upload_image_button_complete, .ilenplugin-options .upload_image_button,.ilenplugin-options .upload_image_button_complete').on("click",function( event ){  

			event.preventDefault();
			formfield = jQuery(this).prev().attr('id');
			var button_this = jQuery(this);

		    var custom_uploader = wp.media({
			  title: jQuery(button_this).attr('data-title'),
			  button: {
				text: jQuery(button_this).attr('data-button-set')
			  },
			  multiple: false  // Set this to true to allow multiple files to be selected
		    })
		    .on('select', function( event ) {


			  var attachment = custom_uploader.state().get('selection').first().toJSON();

			  jQuery("#"+formfield).val(attachment.url);
		    if( jQuery(button_this).hasClass("upload_image_button_complete") ){
    
			  jQuery(button_this).parent().next(".preview").empty().append("<span class='admin_delete_image_upload admin_delete_image_upload_complete'>✕</span><div class='preview_opacity_bg' style='top: 0;left: 0;height: 100%;width: 100%;position: absolute;'></div>");
			  jQuery(button_this).parent().next(".preview").css("background-image","url("+attachment.url+")");
			  jQuery(button_this).parent().next(".preview").css("width","100%");
			  jQuery(button_this).parent().next(".preview").css("height","450px");
			  jQuery(button_this).parent().prev().children("input").val(attachment.url);

			  // delete upload clear input
			  jQuery(".ilentheme-options .admin_delete_image_upload_complete,.ilenplugin-options .admin_delete_image_upload_complete").on("click",function(){
 
				jQuery(this).parent().parent().find('.theme_src_upload').val('');
				jQuery(this).parent().css("background-image","url()");
				jQuery(this).parent().css("height","20px");
				jQuery(this).fadeOut(300);

			  });

			  
		    }else{
			  jQuery(button_this).parent().find(".preview").empty().append("<img src='"+attachment.url+"' /><span class='admin_delete_image_upload admin_delete_image_upload_normal'>✕</span>");
			  // delete upload clear input
			  jQuery(".ilentheme-options .admin_delete_image_upload_normal,.ilenplugin-options .admin_delete_image_upload_normal").on("click",function(){
				jQuery(this).parent().parent().find('.theme_src_upload').val('');
				jQuery(this).prev().fadeOut(300);
				jQuery(this).fadeOut(300);
			  });
		    }
		    })
		    .open();



	});

    jQuery('.ilenplugin-options .upload_image_default,.ilentheme-options .upload_image_default').on('click',function(){
	  jQuery(this).prev().prev().val( jQuery(this).attr("image-default") );
	  jQuery(this).parent().find(".preview").html("<img src='"+jQuery(this).attr("image-default")+"' /><span class='admin_delete_image_upload admin_delete_image_upload_normal'>✕</span>");
    });

    // end upload >3.5


	// upload file old
	var button_this;
    var orig_send_to_editor = window.send_to_editor;
	jQuery('.ilentheme-options .upload_image_button_old,.ilenplugin-options .upload_image_button_old').on("click",function( event ){ 
		 button_this = jQuery(this); 
		 formfield = jQuery(this).prev().attr('id');
		 tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');

	  window.send_to_editor = function(html) {
		 imgurl = jQuery('img',html).attr('src');
		 jQuery("#"+formfield).val(imgurl);
		 jQuery(button_this).parent().find(".preview").html("<img src='"+imgurl+"' /><span class='admin_delete_image_upload'></span>");
		 tb_remove();

		 window.send_to_editor = orig_send_to_editor;
	  }

		 return false;
	});
	
	// end upload file old


	// delete upload clear input
	jQuery(".ilentheme-options .admin_delete_image_upload_normal,.ilenplugin-options .admin_delete_image_upload_normal").on("click",function(){
	    jQuery(this).parent().parent().find('.theme_src_upload').val('');
	    jQuery(this).prev().fadeOut(300);
	    jQuery(this).fadeOut(300);
	});
    jQuery(".ilentheme-options .admin_delete_image_upload_complete, .ilenplugin-options .admin_delete_image_upload_complete").on("click",function(){
	  jQuery(this).parent().parent().find('.theme_src_upload').val('');
	  jQuery(this).parent().css("background-image","url()");
	  jQuery(this).parent().css("height","20px");
	  jQuery(this).fadeOut(300);
    });

    // select2 change event in background_complete
    jQuery(".ilentheme-options .background_complete .select2_background_complete, .ilenplugin-options .background_complete .select2_background_complete").on("change",function(){
	  jQuery(this).parent().parent().find(".preview").css(jQuery(this).attr('data-attribute'),jQuery(this).val());
    });
    



	// select radio image (active)
	jQuery(".ilentheme-options .radio_image_selection,.ilenplugin-options .radio_image_selection").on("click",function( event){

		event.preventDefault();
		var class_ref;
		var img_obj;

		class_ref = jQuery(this).attr("data-id");
		img_obj = jQuery(this);

		jQuery("."+class_ref).each(function(){
			jQuery(this).removeClass("active");
		});

		jQuery(img_obj).addClass("active");
		jQuery(img_obj).next().attr("checked","checked");

	});

    // select radio bg pattern (active)
    jQuery(".item_pattern_bg").on("click",function( event){

	  event.preventDefault();
	  var class_ref;
	  var img_obj;
	  var obj_wrap;
	  var obj_wrap_class;

	  obj_wrap_class = jQuery(this).parent().parent().attr("class");
	  obj_wrap = jQuery(this).parent().parent();
	  //class_ref = jQuery(this).attr("data-id");
	  img_obj = jQuery(this);
	  //alert("."+obj_wrap_class+" .item_pattern_bg");
	  jQuery( "."+obj_wrap_class+" .item_pattern_bg" ).each(function(){
		jQuery(this).removeClass("active");
		jQuery(this).next().attr('checked', false);

	  });

	  jQuery(img_obj).addClass("active");
	  jQuery(img_obj).next().attr("checked","checked");

    });

 

	// set input an colorpicker
	jQuery('.ilentheme-options .theme_color_picker, .ilenplugin-options .theme_color_picker, .ilenmetabox-options .theme_color_picker').wpColorPicker({

		change: function(event, ui) {
			// event = standard jQuery event, produced by whichever control was changed.
			// ui = standard jQuery UI object, with a color member containing a Color.js object

			// change the headline color
			if( jQuery(this).parent().parent().next('.theme_color_picker_value').length ){

				jQuery(this).parent().parent().next('.theme_color_picker_value').val( ui.color.toString() );
				jQuery(this).parent().parent().parent().next().next().next().next().next().next().next().next().next().next().next().find(".preview_opacity_bg").css("background-color", ui.color.toString() );

			}

			if( jQuery(this).parent().parent().next('.theme_color_picker_value_font').length ){

				jQuery(this).parent().parent().parent().find('.ifonts_preview').css("color",ui.color.toString());

			}

    	}

    });

   

	// if exists div class 'mesaggebox' delete element whth effect
	if ( jQuery('.ilentheme-options div.messagebox').length ) {

		setTimeout(function() {
		    jQuery('.ilentheme-options div.messagebox').slideUp(1000, function(){
			    jQuery(this).remove();
			});
		 }, 2000);

	}




    // Background custom & pattern
    jQuery(".switch-label").on("click",function(){

	  var opt =  jQuery(this).prev().attr("value");
	  var obj1 =  jQuery(this).parent().parent().find(".custom_bg_wrap");
	  var obj2 =  jQuery(this).parent().parent().find(".pattern_bg_wrap");
	  jQuery(obj1).css("display","none");
	  jQuery(obj2).css("display","none");
	  //jQuery().fadeOut( 1500, function() {

		//jQuery(obj).css("display","none");

		var obj_move;
		if( opt == "2"){
		    //obj_move = jQuery(this).parent().parent().find(".custom_bg_wrap");
		    obj1.css("display","block");    
		}else if( opt = "1" ){
		    //obj_move = jQuery(this).parent().parent().find(".pattern_bg_wrap");
		    obj2.css("display","block");
		   
		}
	  //});
	  
	  

    });



    // Fix color picker in background_complete
    jQuery(".ilentheme-options .background_complete .wp-color-result, .ilenplugin-options .background_complete .wp-color-result").on("click",function(){

	  if( jQuery(this).hasClass('wp-picker-open') )
		jQuery(this).next().next('.wp-picker-holder').css({
		  'display'         : 'block',
		  'overflow'        : 'hidden',
		  'margin-bottom'   : '20px'
		});
	  else
		jQuery(this).next().next('.wp-picker-holder').css({
		  'display'         : 'inherit',
		  'overflow'        : 'inherit',
		  'margin-bottom'   : '0'
		});

    });
    


    function ilenvalidatorsubmit(){
	  var $ = jQuery;
	  // validate Select2 Multiple
	  var myselect2_before = $("._select2_mulpliple");
	  var myselect2 = myselect2_before.next();
	  if( myselect2_before.length ){

		/** 
		* @link http://jsfiddle.net/DYpU8/4/
		**/
		$( myselect2_before ).each(function() {
		    var newselect2 = $(this).next();
		    var select2parent = $(this).parent();
		    // 'data' brings the unordered list, while val does not
		    var data = newselect2.select2('data');
		    
		    // Push each item into an array
		    var finalResult = [];
		    for( item in newselect2.select2('data') ) {
			  finalResult.push(data[item].id);
		    };
		    
		    // Display the result with a comma
		    $(this).parent().find('._input_hidden_select2').val( finalResult.join(',') );

		});

	  }

    }


	// Widget additional field (MORE) // only load page (no save, no update widget)
	$( ".ilenwidget-more--button" ).each(function() {
		$( this ).off('click').on('click', function() {
			$( this ).toggleClass( "active_ilen_widget_more" );
			$( this ).next().toggleClass( "active_ilen_widget_more" );
		});
	});
	

    // Animation Panel Once (remove animation)
    var MyRemoveAnimationPostBox = function( object_click ){
		var id_container = String(jQuery( object_click ).attr("id"));
		id_container = id_container.substr(id_container.length - 1);
		id_container = padLeft(id_container,2);
		jQuery("#tab"+id_container+" .postbox").removeClass('animation_postbox_once');
		jQuery("#tab01 .postbox").removeClass('animation_postbox_once');
    }
    jQuery(".ilenplugin-options .animation_once").on("click",function(){
		setTimeout( MyRemoveAnimationPostBox, 3000,  jQuery(this) );
    });

    

    
    // link: http://wordpress.stackexchange.com/questions/120272/color-picker-showing-twice-when-widget-added-to-sidebar
    // other reference: https://make.wordpress.org/core/2014/04/17/live-widget-previews-widget-management-in-the-customizer-in-wordpress-3-9/
    /*( function( $ ){
	  function initColorPicker( widget ) {
		widget.find( '.color-picker' ).wpColorPicker( {
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
		$( '#widgets-right .widget:has(.color-picker)' ).each( function () {
			  initColorPicker( $( this ) );                                                   
		} );
	  } );
    }( jQuery ) );*/



    // OTHER
    /*jQuery( document ).on( 'widget-added', function(event, widget){
	  console.log( jQuery(widget[0]).find(".widget-id").val() );
	  jQuery( "#widget-"+jQuery(widget[0]).find(".widget-id").val()+"-savewidget" ).click();
    });
    */
   

   // GOOGLE FONTS
   if( jQuery('.select--gfonts__family').length ){
		jQuery('.select--gfonts__family').on("change", function(e) { 
			previewFontFamily( jQuery(this),jQuery(this).parent().next().children('select') ); 
		});
   }

   previewFontFamily = function(elem, elem_variant) {
		var font = $(elem).val();
		getFontVariants(font,elem,elem_variant);
	};

	getFontVariants = function(font,elem,elem_variant) {

			var select_variants = $(elem_variant);
			var variant_array = [];
			//alert( font + ":"+$(elem).val());
			
			jQuery.ajax({
				url: ajaxurl,//"http://localhost/ntest/3/wp-admin/admin-ajax.php", //ajaxurl,
				type:"GET",
				data: {
					'action' : 'get_google_font_variants',
					'font_family' : font
				},
				success: function(data_fonts) {

					select_variants.find("option").remove();
					var is_selected = null;
	    			for(i = 0; i < data_fonts.length; ++i) {
							if( 'normal' == data_fonts[i]) { 
								is_selected = "selected"; 
							} else { 
								is_selected = ""; 
							}
							select_variants.append('<option '+is_selected+' value="'+data_fonts[i]+'" >'+data_fonts[i]+'</option>');
							variant_array.push(data_fonts[i]);
	    			}
	    			select_variants.next('input').val(variant_array.join(','));
					/*WebFont.load({
						google: {
							families: [font+':'+variant_array.join()]
						},
						loading: function() {
							preview.css("opacity", 0);
						},
						fontactive: function(family, desc) {
							preview.css('font-family', '"'+font+'"').css("opacity", 1);
						}
					});*/

					//variants.trigger("change").trigger("liszt:updated");
    		},
			error: function (xhr, ajaxOptions, thrownError) {
				//alert(xhr.responseText);
				jQuery(".update-nag").html(xhr.responseText);
			}
    	});
      
	};



	function update_preview_gf( objetc_select_fonts, variants_fonts ){

		var font_family  = objetc_select_fonts.find('.ifonts_family select').val();
		var font_variant = objetc_select_fonts.find('.ifonts_variants select').val();
		if(objetc_select_fonts.find('.ifonts_size select').length){
			var font_size    = objetc_select_fonts.find('.ifonts_size select').val();
		}
		

		// get variants
		var text_variants = "";
		if( variants_fonts != 'normal' ){
			
			var variants_array = [];
			var i = 0;
			objetc_select_fonts.find('.ifonts_variants select').each(function() {
			    variants_array[i] = jQuery(this).val();
			    i++;
			});
			console.log(variants_array);
			text_variants = variants_array.join(',');

		}

		

		addGoogleFont( font_family , text_variants);

		objetc_select_fonts.children('.ifonts_preview').css("font-family",font_family);
		if(font_size){
			objetc_select_fonts.children('.ifonts_preview').css("font-size",font_size);
			objetc_select_fonts.children('.ifonts_preview').css("line-height",(parseInt(font_size.replace("px", "")) + 10) + "px" );
		}

	}

 
	jQuery(".select--gfonts__family,.select--gfonts__size").on("change",function(){

		update_preview_gf( jQuery(this).parent().parent() , 'normal' );

	});

	jQuery(".select--gfonts__variant").on("change",function(){

		update_preview_gf( jQuery(this).parent().parent() , jQuery(this).val() );
		jQuery(this).parent().parent().children('.ifonts_preview').css("font-weight", jQuery(this).val() );

	});


	jQuery(".input_check_light_dark").on("change",function(){

		if( jQuery(this).parent().parent().find('input:checkbox:checked').length > 0 ){
			jQuery(this).parent().parent().find('.ifonts_preview').css('background-color','#393937');
		}else{
			jQuery(this).parent().parent().find('.ifonts_preview').css('background-color','transparent');
		}


	});
 
 
});


// load google fonts and put in header 
function addGoogleFont(FontName, variants_fonts) {

	var get_variants_font = "";
	if(variants_fonts){
		get_variants_font = ":"+variants_fonts;
	}

	jQuery("head").append("<link href='https://fonts.googleapis.com/css?family=" + FontName + get_variants_font + "' rel='stylesheet' type='text/css'>");
}


// link: http://jsfiddle.net/67XDq/7/
function IF_textCounter(field, cnt, maxlimit) {         
    var cntfield = document.getElementById(cnt) 
     if (field.value.length > maxlimit) // if too long...trim it!
	  field.value = field.value.substring(0, maxlimit);
	  // otherwise, update 'characters left' counter
	  else
	  cntfield.value = maxlimit - field.value.length;
}

// link: 
function padLeft(nr, n, str){
    return Array(n-String(nr).length+1).join(str||'0')+nr;
}


// link: http://stackoverflow.com/questions/542938/how-do-i-get-the-number-of-days-between-two-dates-in-javascript#comment12872595_544429
function IF_mydiff(date1,date2,interval) {
    var second=1000, minute=second*60, hour=minute*60, day=hour*24, week=day*7;
    date1 = new Date(date1);
    date2 = new Date(date2);
    var timediff = date2 - date1;
    if (isNaN(timediff)) return NaN;
    switch (interval) {
	  case "years": return date2.getFullYear() - date1.getFullYear();
	  case "months": return (
		( date2.getFullYear() * 12 + date2.getMonth() )
		-
		( date1.getFullYear() * 12 + date1.getMonth() )
	  );
	  case "weeks"  : return Math.floor(timediff / week);
	  case "days"   : return Math.floor(timediff / day); 
	  case "hours"  : return Math.floor(timediff / hour); 
	  case "minutes": return Math.floor(timediff / minute);
	  case "seconds": return Math.floor(timediff / second);
	  default: return undefined;
    }
}


// link: http://phpjs.org/functions/in_array/
function in_array(needle, haystack, argStrict) {
  //  discuss at: http://phpjs.org/functions/in_array/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: vlado houba
  // improved by: Jonas Sciangula Street (Joni2Back)
  //    input by: Billy
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //   example 1: in_array('van', ['Kevin', 'van', 'Zonneveld']);
  //   returns 1: true
  //   example 2: in_array('vlado', {0: 'Kevin', vlado: 'van', 1: 'Zonneveld'});
  //   returns 2: false
  //   example 3: in_array(1, ['1', '2', '3']);
  //   example 3: in_array(1, ['1', '2', '3'], false);
  //   returns 3: true
  //   returns 3: true
  //   example 4: in_array(1, ['1', '2', '3'], true);
  //   returns 4: false

  var key = '',
    strict = !! argStrict;

  //we prevent the double check (strict && arr[key] === ndl) || (!strict && arr[key] == ndl)
  //in just one for, in order to improve the performance 
  //deciding wich type of comparation will do before walk array
  if (strict) {
    for (key in haystack) {
	if (haystack[key] === needle) {
	  return true;
	}
    }
  } else {
    for (key in haystack) {
	if (haystack[key] == needle) {
	  return true;
	}
    }
  }

  return false;
}

jQuery.fn.IF_countDown = function(settings,to) {
    settings = jQuery.extend({
        startFontSize: "12px",
        endFontSize: "12px",
        duration: 1000,
        startNumber: 10,
        endNumber: 0,
        callBack: function() { }
    }, settings);
    return this.each(function() {
        
        //where do we start?
        if(!to && to != settings.endNumber) { to = settings.startNumber; }
        
        //set the countdown to the starting number
        jQuery(this).text(to).css("fontSize",settings.startFontSize);
        
        //loopage
        jQuery(this).animate({
            fontSize: settings.endFontSize
        }, settings.duration, "", function() {
            if(to > settings.endNumber + 1) {
                jQuery(this).css("fontSize", settings.startFontSize).text(to - 1).IF_countDown(settings, to - 1);
            }
            else {
                settings.callBack(this);
            }
        });
                
    });
};