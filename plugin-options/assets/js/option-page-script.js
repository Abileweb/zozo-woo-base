/*
 * Zozo woo base addon sctipts
 */ 

(function( $ ) {

	"use strict";
	
	var custom_uploader;
	
	$( document ).ready(function() {
		
		$(document).find(".zwb-section-fields .switch-checkbox").on( "change", function() {
			var stat = $(this).is(":checked") ? 1 : 0;
			$(this).prev('input').val(stat);
		});
		
		$(".zwb-admin-wrap .handlediv").on( "click", function(){
			$(this).parent(".postbox").toggleClass("closed");			
		});
		
		$('.zwb-upload-image-button').click(function(e) {
			
			e.preventDefault();
			
			var cur_ele = $(this);
			
			//If the uploader object has already been created, reopen the dialog
			if (custom_uploader) {
				custom_uploader.open();
				return;
			}

			//Extend the wp.media object
			custom_uploader = wp.media.frames.file_frame = wp.media({
				title: 'Choose Image',
				button: {
					text: 'Choose Image'
				},
				multiple: false
			});

			//When a file is selected, grab the URL and set it as the text field's value
			custom_uploader.on('select', function() {
				//console.log(custom_uploader.state().get('selection').toJSON());
				var attachment = custom_uploader.state().get('selection').first().toJSON();
				cur_ele.prev(".zwb-upload-image-val").val(attachment.url);
				cur_ele.parent(".field-set-right").prepend('<img src="'+ attachment.url +'" alt="" class="zwb-uploaded-image" />');
			});

			//Open the uploader dialog
			custom_uploader.open();

		});
		
		$('.zwb-remove-image-button').click(function(e) {
			e.preventDefault();
			var cur_parent = $(this).parent(".field-set-right");
			cur_parent.find("img").remove();
			cur_parent.find(".zwb-upload-image-val").val('');
		});
		
		$('.zwb-color-field').each(function(){
			$(this).wpColorPicker();
		});
		
		if( $(".zwb-tab").length ){
			
			var cur_ele = $( ".zwb-tab" );			
			$(".tablinks-list > .tablinks:first-child").addClass("active");
			cur_ele.find(".tablinks").click(function() {	
				$(this).parent(".tablinks-list").children("li").removeClass("active");
				$(this).addClass("active");
				cur_ele.find(".tabcontent:not(.tab-hide)").each(function( index ) {
					$(this).addClass("tab-hide");
				});			
				
				$( '#' + $(this).attr("data-id") ).removeClass("tab-hide");
			});
			
			$(".zwb-tab .switch-checkbox").on( "change", function(){
				if($(this).is(":checked")) {
					$(this).prev("input").val(1);
				}else{
					$(this).prev("input").val(0);
				}
			});
		}
		
		if( $(".zwb-submit.button").length ){
			$(".zwb-submit.button").on( "click", function() {
				var confirm_stat = confirm(zwb_ajax_var.confirm_str);
				if( confirm_stat == true ){
					$("#zwb-form-wrapper").submit();
				}
				return false;
			});
		}

		if( $("#zozo_woo_addons_nonce").length ){

			$(".wp-admin .zozo-woo-feature-details > a.addon-process-trigger").on( "click", function() {
			
				var addon_key = $(this).attr("data-key");
				var addon_stat = $(this).attr("data-stat");
				var cur_nonce = $("#zozo_woo_addons_nonce").val();

				$.ajax({
					type: 'post',
					url: ajaxurl,
					data: { 
						action: "zozo_woo_addons_update", 
						nonce: cur_nonce,
						key: addon_key,
						stat: addon_stat
					},
					success: function(data){
						window.location.reload();			
					},
					error: function(xhr, status, error) {
						window.location.reload();	
					}
				});

				return false;

			});
		}

		/* Custom Reqiured Field */
		if( $( ".zwb-section-fields .field-set[data-required]" ).length ){
			$( ".zwb-section-fields .field-set[data-required]" ).hide();
			$( ".zwb-section-fields .field-set[data-required]" ).each(function( index ) {
				var hidden_ele = this;
				var req_field = '#'+ $(this).attr('data-required');
				var req_val = $(this).attr('data-required-value');
				var req_condition = $(this).attr('data-required-condition');
				var req_selected = $( req_field ).find(":selected").val();
				if( req_condition == '!=' ){
					$( req_field ).change(function() {
						req_selected = $( this ).find(":selected").val();
						if( req_selected != req_val ){
							$(hidden_ele).show();
						}else{
							if( $( hidden_ele ).find('select').length ){
								var t_val = $(hidden_ele).find('select').attr('id');
								$(hidden_ele).find('select').prop('selectedIndex',0);
								$(hidden_ele).parents('.zwb-section-fields').find('.field-set').filter('[data-req="'+ t_val +'"]').hide();
							}
							$(hidden_ele).hide();
						}
					});
				}else{
					$( req_field ).change(function() {
						req_selected = $( this ).find(":selected").val();
						if( req_selected == req_val ){
							$(hidden_ele).show();
						}else{
							if( $( hidden_ele ).find('select').length ){
								var t_val = $(hidden_ele).find('select').attr('id');
								$(hidden_ele).find('select').prop('selectedIndex',0);
								$(hidden_ele).parents('.zwb-section-fields').find('.field-set').filter('[data-req="'+ t_val +'"]').hide();
							}
							$(hidden_ele).hide();
						}
					});
				}
				
			});
		}
		
	});	
	
})( jQuery );