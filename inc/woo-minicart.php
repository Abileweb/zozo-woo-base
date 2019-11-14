<?php 

function zozo_find_matching_product_variation_ID( $product_id, $attributes ){	
	return (new \WC_Product_Data_Store_CPT())->find_matching_product_variation(
		new \WC_Product($product_id),
		$attributes
	);
}

//Add to cart button text change to icon
function zozo_woocommerce_loop_add_to_cart_link( $string, $product, $args ){
	
	global $woocommerce, $product, $post;
	
	if( $product->is_type( 'variable' ) ){
		$all_variations = $product->get_available_variations();
		$variations = array();
		
		foreach( $all_variations as $variation ) {
			// Get selected value.
			if( isset( $variation['attributes']['attribute_pa_color'] ) ) {
				$selected_key = 'attribute_pa_color';
				$variations['pa_color'] = $product->get_variation_default_attribute( 'pa_color' ); // WPCS: input var ok, CSRF ok, sanitization ok.
			}
			if( isset( $variation['attributes']['attribute_pa_size'] ) ) {
				$selected_key = 'attribute_pa_size';
				$variations['pa_size'] = $product->get_variation_default_attribute( 'pa_size' ); // WPCS: input var ok, CSRF ok, sanitization ok.
			}
		}
		
		$args['attributes']['data-variations'] = json_encode( $variations );
	}
	
	return apply_filters( 'zozo_woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
		sprintf( '<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
			esc_url( $product->add_to_cart_url() ),
			esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
			esc_attr( isset( $args['class'] ) ? 'zozo-ajax-add-to-cart' : '' ), //str_replace( "button", "", $args['class'] ) //add_to_cart_button ajax_add_to_cart
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			'<span class="ti-shopping-cart"></span>'
		),
	$product, $args );
}
add_filter( 'woocommerce_loop_add_to_cart_link', 'zozo_woocommerce_loop_add_to_cart_link', 10, 3 );

// Woo cart items
function zozo_cart_items(){
	$empty_cart = '<li class="cart-item"><p class="no-cart-items">'. apply_filters( 'zozo_woo_mini_cart_empty', esc_html__('No items in cart', 'zozo-woo-base') ) .'</p></li>';
	if ( WC()->cart->get_cart_contents_count() == 0 ) return $empty_cart;
	ob_start();
	
	$shop_page_url = get_permalink( wc_get_page_id( 'cart' ) );
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
	?>
		<li class="cart-item" data-product-id="<?php echo esc_attr( $product_id ); ?>">
		<?php
			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
		?>
			<div class="product-thumbnail">
				<?php
					$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
					if ( ! $product_permalink ) {
						echo ( ''. $thumbnail );
					} else {
						printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
					}
				?>
			</div>
			<div class="product-name" data-title="<?php esc_attr_e( 'Product', 'zozo-woo-base' ); ?>">
				<?php echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_title() ), $cart_item, $cart_item_key ); ?>
				<p>
					<span><?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?> &#9747; <?php echo esc_attr( $cart_item['quantity'] ); ?></span>
				</p>
			</div>
			<div class="product-remove">
				<?php
					echo 
					sprintf(
						'<a href="%s" class="remove-cart-item" title="%s" data-product_id="%s" data-product_sku="%s"><i class="ti-trash"></i></a>',
						esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
						__( 'Remove this item', 'zozo-woo-base' ),
						esc_attr( $product_id ),
						esc_attr( $_product->get_sku() )
					);
				?>
			</div>
		<?php
			}//if
		?>
		</li>
		<?php
		}//foreach
	?>
	<li class="mini-view-cart"><a href="<?php echo esc_url( $shop_page_url ); ?>" title="<?php esc_attr_e('Cart', 'zozo-woo-base'); ?>"><?php esc_html_e('View Cart', 'zozo-woo-base'); ?></a></li>
	<?php 
	$out = ob_get_clean();
	return $out;
}

/**
 * Ensure cart contents update when products are added to the cart via AJAX
 */
function zozo_header_add_to_cart_fragment( $fragments ) {
    $mini_cart = $sticky_cart = $cart_count = '';
	$count = WC()->cart->cart_contents_count;
	$cart_url = wc_get_cart_url();
	$cart_items = zozo_cart_items();
	
	$cart_count .= '<span class="woo-icon-count zozo-cart-items-count">'. esc_html( $count ) .'</span>';	
	
	//Mini Cart 
	$mini_cart .= '<ul class="cart-dropdown-menu">';
		$mini_cart .= $cart_items;
	$mini_cart .= '</ul>';
	
	//Sticky Cart 
	$sticky_cart .= '<ul class="zozo-sticky-cart">';
		$sticky_cart .= $cart_items;
	$sticky_cart .= '</ul>';
	
	$fragments['ul.cart-dropdown-menu'] = $mini_cart;
	$fragments['ul.zozo-sticky-cart'] = $sticky_cart;
	$fragments['span.zozo-cart-items-count'] = $cart_count;
     
    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'zozo_header_add_to_cart_fragment' );

// Ajax add to mini cart
function zozo_add_to_mini_cart() {
		
	$nonce = $_POST['nonce'];  
	if ( ! wp_verify_nonce( $nonce, 'zozo-add-to-cart' ) ) wp_die( esc_html__( 'Busted', 'zozo-woo-base' ) );
	
	ob_start();

	if ( ! isset( $_POST['product_id'] ) ) {
		return;
	}

	$product_id        = apply_filters( 'zozo_woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
	$product           = wc_get_product( $product_id );
	$quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_POST['quantity'] ) );
	$passed_validation = apply_filters( 'zozo_woocommerce_add_to_cart_validation', true, $product_id, $quantity );
	$product_status    = get_post_status( $product_id );
	$variation_id      = 0;
	$variation         = array();

	if ( $product && 'variable' === $product->get_type() ) {

		if( isset( $_POST['variation'] ) && is_array( $_POST['variation'] ) ){
			$color_stat = isset( $_POST['variation']['pa_color'] ) ? true : false;
			$size_stat = isset( $_POST['variation']['pa_size'] ) ? true : false;
			$color_value = isset( $_POST['variation']['pa_color'] ) ? $_POST['variation']['pa_color'] : '';
			$size_value = isset( $_POST['variation']['pa_size'] ) ? $_POST['variation']['pa_size'] : '';
			if( $color_stat ) $product_varations['attribute_pa_color'] = $color_value;
			if( $size_stat ) $product_varations['attribute_pa_size'] = $size_value;

			$current_attrs = '';
			if( $color_stat && $size_stat ){
				$current_attrs = 'both';
			}elseif( $color_stat ){
				$current_attrs = 'color';
			}elseif( $size_stat ){
				$current_attrs = 'size';
			}
			
			if( !empty( $current_attrs ) ){
				$available_variations = $product->get_available_variations();
				if( isset( $available_variations ) ){
					$final_color = '';
					$final_size = '';
					foreach( $available_variations as $available_variation ){
						
						$cur_color = isset( $available_variation['attributes']['attribute_pa_color'] ) ? $available_variation['attributes']['attribute_pa_color'] : '';
						$cur_size = isset( $available_variation['attributes']['attribute_pa_size'] ) ? $available_variation['attributes']['attribute_pa_size'] : '';
						
						if( $current_attrs == 'both' ){
							if( $cur_color == $color_value && $cur_size == $size_value ){
								$product_varations =  $available_variation['attributes'];
							}
						}elseif( $current_attrs == 'color' ){
							if( $cur_color == $color_value ){
								$product_varations =  $available_variation['attributes'];
							}
						}elseif( $current_attrs == 'size' ){
							if( $cur_size == $size_value ){
								$product_varations =  $available_variation['attributes'];
							}
						}
						
					}

				}
			}

		}
		
		$variation_id = zozo_find_matching_product_variation_ID( $product_id, $product_varations );			
		$variation_id = !empty( $variation_id ) ? esc_attr( $variation_id ) : 0;
		$product_id   = $product->get_parent_id();
		$variation    = isset( $_POST['variation'] ) && is_array( $_POST['variation'] ) ? $_POST['variation'] : $product->get_variation_attributes();
		
	}

	if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) && 'publish' === $product_status ) {
		
		$data = array();
		$data["status"] = 1;
		$data["mini_cart"] = zozo_cart_items();
		$data["sticky_cart"] = $data["mini_cart"];
		$data["cart_count"] = WC()->cart->cart_contents_count;
		
		wp_send_json( $data );

	} else {

		// If there was an error adding to the cart, redirect to the product page to show any errors.
		$data = array(
			'error'       => true,
			'product_url' => apply_filters( 'zozo_woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
		);

		wp_send_json( $data );
	}
	
	wp_die();
}
add_action( 'wp_ajax_zozo_add_to_cart', 'zozo_add_to_mini_cart' );
add_action( 'wp_ajax_nopriv_zozo_add_to_cart', 'zozo_add_to_mini_cart' );

// Ajax remove product from mini cart
function zozo_mini_cart_product_remove() {
	
	global $wpdb, $woocommerce;
	session_start();
	
	$nonce = $_POST['nonce'];  
		if ( ! wp_verify_nonce( $nonce, 'zozo-remove-from-cart(*$#' ) ) wp_die( esc_html__( 'Busted', 'zozo-woo-base' ) );
		
	$product_id = '';
	if( isset( $_POST['product_id'] ) && !empty( $_POST['product_id'] ) ) $product_id = $_POST['product_id'];
	
	if( $product_id ){
		foreach( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ){
			if( $cart_item['product_id'] == $_POST['product_id'] ){
				// Remove product in the cart using cart_item_key.
				$woocommerce->cart->remove_cart_item($cart_item_key);
			}
		}
	}
	
	$result = array();
	$result["status"] = 1;
	$result["mini_cart"] = zozo_cart_items();
	$result["sticky_cart"] = $result["mini_cart"];
	$result["cart_count"] = WC()->cart->cart_contents_count;
	
	echo json_encode( $result );
	
	wp_die();
}
add_action( 'wp_ajax_zozo_product_remove', 'zozo_mini_cart_product_remove' );
add_action( 'wp_ajax_nopriv_zozo_product_remove', 'zozo_mini_cart_product_remove' );


function zozo_mini_cart_func( $atts ) {
	
	$mini_cart = $sticky_cart = $cart_count = '';
	$count = WC()->cart->cart_contents_count;
	$cart_url = wc_get_cart_url();
	$cart_items = zozo_cart_items();
	
	$out = '<div class="mini-cart-dropdown">';
		$out .= '<a href="'. esc_url( $cart_url ) .'" class="mini-cart-item"><i class="ti-shopping-cart"></i>';
		$out .= '<span class="woo-icon-count zozo-cart-items-count">'. esc_html( $count ) .'</span></a>';	
		$out .= '<ul class="cart-dropdown-menu">';
			$out .= $cart_items;
		$out .= '</ul>';
	$out .= '</div>';
	
	return $out;
	
}
add_shortcode( 'zozo_mini_cart', 'zozo_mini_cart_func' );

function zozo_sticky_cart_func( $atts ) {
	
	$mini_cart = $sticky_cart = $cart_count = '';
	$count = WC()->cart->cart_contents_count;
	$cart_url = wc_get_cart_url();
	$cart_items = zozo_cart_items();
	
	$out = '<div class="zozo-sticky-cart-wrap">';
		$out .= '<a href="'. esc_url( $cart_url ) .'" class="zozo-sticky-cart-close"><i class="ti-shopping-cart"></i>';
		$out .= '<span class="woo-icon-count zozo-cart-items-count">'. esc_html( $count ) .'</span></a>';	
		$out .= '<ul class="zozo-sticky-cart">';
			$out .= $cart_items;
		$out .= '</ul>';
	$out .= '</div>';

	return $out;
	
}
add_shortcode( 'zozo_sticky_cart', 'zozo_sticky_cart_func' );


$sticky_minicart = zozowoobase_major_settings::zozowoobase_get( 'sticky-minicart-opt' );
if( $sticky_minicart ){
	add_action( 'wp_footer', 'zozo_enable_sticky_minicart', 10 );
	function zozo_enable_sticky_minicart(){
		echo do_shortcode('[zozo_sticky_cart]');
	}
}