( function( $ ) {
	
	"use strict";
		
	// Clear and store values in local storage
	if (typeof(Storage) !== "undefined"){
		localStorage.removeItem('mini_wishlist_count');
		localStorage.removeItem('mini_wishlist');
		$( window ).on( 'storage onstorage', function ( e ) {
			var mini_wishlist = localStorage.getItem( 'mini_wishlist' );
			var mini_wishlist_count = localStorage.getItem( 'mini_wishlist_count' );
			if( mini_wishlist_count ) $(document).find("span.zozo-wishlist-items-count").text(mini_wishlist_count);
			if( mini_wishlist )  $(document).find("ul.wishlist-dropdown-menu, ul.zozo-sticky-wishlist").html(mini_wishlist);
			localStorage.removeItem( 'mini_wishlist' );
			localStorage.removeItem( 'mini_wishlist_count' );
		});
	}
		
	$(document).ready(function(){

		/* Mini cart addon scripts */

		// Sticky cart close click
		$(document).find("body .zozo-sticky-cart-wrap .zozo-sticky-cart-close").on( "click", function(e) {
			$(document).find(".zozo-sticky-cart-wrap").toggleClass("active");
			return false;
		});
		
		// Mini cart/Sticky cart remove item
		if( $( document ).find('.mini-cart-item').length || $( document ).find(".zozo-sticky-cart").length ){
			
			$( document ).on( 'click', '.remove-cart-item', function(){
				
				var cur_ele = $(this);
				cur_ele.addClass("loading");
				var product_id = cur_ele.attr("data-product_id");
				
				$.ajax({
					type: 'post',
					dataType: 'json',
					url: zozowoobase_ajax_var.admin_ajax_url,
					data: { 
						action: "zozo_product_remove", 
						nonce: zozowoobase_ajax_var.remove_from_cart,
						product_id: product_id
					},
					success: function(data){
						
						if( data['status'] == 1 ){
							if( data['mini_cart'] ){
								$(document).find('.mini-cart-dropdown li.cart-item[data-product-id="'+ product_id +'"]').fadeOut( 350, function(){
									$(document).find(".mini-cart-dropdown ul.cart-dropdown-menu").html( data['mini_cart'] );
									$(document).find(".mini-cart-dropdown .woo-icon-count").text( data['cart_count'] );
								});
								
							}
							if( data['sticky_cart'] ){
								$(document).find('.zozo-sticky-cart li.cart-item[data-product-id="'+ product_id +'"]').fadeOut( 350, function(){
									$(document).find(".zozo-sticky-cart-wrap ul.zozo-sticky-cart").html( data['sticky_cart'] );
									$(document).find(".zozo-sticky-cart-wrap .woo-icon-count").text( data['cart_count'] );
								});
							}
														
							$( document.body ).trigger( 'wc_fragment_refresh' );
							console.log("wc fragment refreshed");
						}
						
						cur_ele.removeClass("loading");
						
					},
					error: function(xhr, status, error) {
						cur_ele.removeClass("loading");
					}
				});
				return false;
			});	
			
		}		
		
		// Ajax add to cart
		$( document ).on( 'click', "a.zozo-ajax-add-to-cart", function( event) {
			
			if( $("body.page-template-zozo-wishlist").length ){
				$(this).parents("tr").find("a.zozo-wishlist-remove").trigger("click");
			}
			
			var cur_ele = $(this);
			cur_ele.addClass("loading");
			var product_id = $(this).attr("data-product_id");
			var variations = cur_ele.data("variations") ? cur_ele.data("variations") : '';
			
			$.ajax({
				type: 'post',
				dataType: 'json',
				url: zozowoobase_ajax_var.admin_ajax_url,
				data: { 
					action: "zozo_add_to_cart",
					product_id: product_id,
					variation: variations,
					nonce: zozowoobase_ajax_var.add_to_cart
				},success: function(data){					
					cur_ele.removeClass("loading");					
					if( data['error'] == true ){
						var not_avail_msg = zozowoobase_ajax_var.product_not_available;
						if( cur_ele.parents("li.product").hasClass("product-type-variable") ){
							not_avail_msg = zozowoobase_ajax_var.variation_not_available;
						}
						var err_html = '<div class="variation-not-available-wrap"><span class="ti-close"></span><div class="variation-not-inner">'+ not_avail_msg +'</div></div>';
						cur_ele.parents("li.product").append(err_html);
					}else{					
						if( data['status'] == 1 ){							
							if( data['mini_cart'] ){
								$(document).find(".mini-cart-dropdown ul.cart-dropdown-menu").html( data['mini_cart'] );
								$(document).find(".mini-cart-item .woo-icon-count").text( data['cart_count'] );
							}
							if( data['sticky_cart'] ){
								$(document).find(".zozo-sticky-cart-wrap ul.zozo-sticky-cart").html( data['sticky_cart'] );
								$(document).find(".zozo-sticky-cart-wrap .woo-icon-count").text( data['cart_count'] );
								if( !$(document).find(".zozo-sticky-cart-wrap").hasClass("active") ) $(document).find(".zozo-sticky-cart-wrap").addClass("active");
							}							
							$( document.body ).trigger( 'wc_fragment_refresh' );
							console.log("wc fragment refreshed");
						}						
					}					
				},error: function(xhr, status, error) {
					cur_ele.removeClass("loading");
				}
			});
			
			return false;
			
		});
		
		/* Wishlist addon scripts */
		
		// Sticky wishlist close click
		$(document).find("body .zozo-sticky-wishlist-close").on( "click", function(e) {
			$(document).find(".zozo-sticky-wishlist-wrap").toggleClass("active");
			return false;
		});	

		// Product wishlist trigger
		$( document ).on( 'click', ".zozo-woo-favourite-trigger", function( event) {
			
			var cur_a = $(this);
			var product_id = cur_a.attr("data-product-id");
			
			if( zozowoobase_ajax_var.user_logged == 0 ){
				if( $('.zozo-login-parent').length ){
					$('.zozo-login-parent').toggleClass('login-open');
				}else{
					window.location.href = zozowoobase_ajax_var.woo_user_page;
				}
				return false;
			}
			
			if( product_id ){
				
				cur_a.addClass("loading");
				$.ajax({
					type: "post",
					dataType: "json",
					url: zozowoobase_ajax_var.admin_ajax_url,
					data: "action=woo_fav_act&nonce="+ zozowoobase_ajax_var.wishlist_nonce +"&post_id="+product_id,
					success: function(res){
						
						if( res == 0 ){
							if( $('.zozo-login-parent').length ) $('.zozo-login-parent').toggleClass('login-open');
						}else{
						
							if( res['stat'] == 'fav' ){
								cur_a.addClass("theme-color");
								if( !cur_a.parents("li.product").find(".zozo-product-favoured").length ){
									cur_a.parents("li.product").prepend('<span class="zozo-product-favoured"><i class="ti-heart"></i></span>');
								}
							}else{
								cur_a.removeClass("theme-color");
								if( cur_a.parents("li.product").find(".zozo-product-favoured").length ){
									cur_a.parents("li.product").find(".zozo-product-favoured").remove();
								}
							}
						
							if( $.isNumeric( res['count'] ) ){
								if( $(document).find("a.mini-wishlist-item").length ){
									if( $(document).find("span.zozo-wishlist-items-count").length ){
										$(document).find("span.zozo-wishlist-items-count").text(res['count']);
									}else{
										$(document).find("a.mini-wishlist-item").append('<span class="span.zozo-wishlist-items-count">'+ res['count'] +'</span>');
									}
								}
								localStorage.setItem( 'mini_wishlist_count', res['mini_wishlist_count'] );
							}
							
							if( res['mini_wishlist'] ){								
								if( !$(document).find(".zozo-sticky-wishlist-wrap").hasClass("active") ) $(document).find(".zozo-sticky-wishlist-wrap").addClass("active");
								$(document).find("ul.wishlist-dropdown-menu, ul.zozo-sticky-wishlist").html(res['mini_wishlist']);							
								if (typeof(Storage) !== "undefined"){
									localStorage.setItem( 'mini_wishlist', res['mini_wishlist'] );
								}
							}
							
							
						}
						
						cur_a.removeClass("loading");
						
					},
					error: function (jqXHR, exception) {
						cur_a.removeClass("loading");
						console.log(jqXHR);
					}
				});
			}
			
			return false;
		});
		
		// Remove wishlist row
		$( document ).on( 'click', "a.zozo-wishlist-remove, a.remove-wishlist-item", function( event) {
			
			var cur_a = $(this);
			var product_id = cur_a.attr("data-product-id");
			
			if( product_id ){
				
				cur_a.addClass("loading");
				$.ajax({
					type: "post",
					dataType: 'json',
					url: zozowoobase_ajax_var.admin_ajax_url,
					data: "action=zozo_wishlist_remove&nonce="+zozowoobase_ajax_var.wishlist_remove+"&product_id="+product_id,
					success: function(res){
						
						if( $(document).find("li.post-" + product_id ).length ) $(document).find("li.post-" + product_id + " span.zozo-product-favoured" ).remove();
						
						if( cur_a.hasClass("remove-wishlist-item") ){
							cur_a.parents("li.wishlist-item").fadeOut( 350, function() {
								cur_a.parents("li.wishlist-item").remove();
							});
						}else{
							cur_a.parents("tr").fadeOut( 350, function() {
								cur_a.parents("tr").remove();
							});
						}
												
						if( $.isNumeric( res['mini_wishlist_count'] ) ){
							if( $(document).find("span.zozo-wishlist-items-count").length ){
								$(document).find("span.zozo-wishlist-items-count").text(res['mini_wishlist_count']);
							}else{
								$(document).find("a.mini-wishlist-item").append('<span class="span.zozo-wishlist-items-count">'+ res['mini_wishlist_count'] +'</span>');
							}
							localStorage.setItem( 'mini_wishlist_count', res['mini_wishlist_count'] );
						}
						
						if( res['mini_wishlist'] ){		
							setTimeout(function(){ 					
								$(document).find("ul.wishlist-dropdown-menu, ul.zozo-sticky-wishlist").html(res['mini_wishlist']);							
								if (typeof(Storage) !== "undefined"){
									localStorage.setItem( 'mini_wishlist', res['mini_wishlist'] );								
								}
							}, 350 );
						}
						
					},
					error: function (jqXHR, exception) {
						console.log(jqXHR);
					}
				});
			}
			
			return false;
		});

	});		
		
} )( jQuery );