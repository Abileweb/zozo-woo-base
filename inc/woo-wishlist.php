<?php 

add_action( 'woocommerce_before_shop_loop_item_title', 'zozowishlist_woocommerce_before_shop_loop_item_title_end', 10 );
function zozowishlist_woocommerce_before_shop_loop_item_title_end(){
	global $product;
	$id = $product->get_id();	
	$fav_stat = Zozo_Woo_Favourite::zozoFavouriteIPVerify( $id, true );
	$fav_class = $fav_stat == '1' ? ' theme-color' : '';
	echo '<a href="#" class="zozo-woo-favourite-trigger'. esc_attr( $fav_class ) .'" data-product-id="'. esc_attr( $id ) .'"><span class="ti-heart"></span></a>';
}

add_action( 'woocommerce_before_shop_loop_item', 'zozo_woocommerce_template_loop_product_link_open', 10 );
function zozo_woocommerce_template_loop_product_link_open(){
	global $product;
	$id = $product->get_id();	
	$fav_stat = Zozo_Woo_Favourite::zozoFavouriteIPVerify( $id, true );
	if( $fav_stat == '1' ) echo '<span class="zozo-product-favoured"><i class="ti-heart"></i></span>';
}


$sticky_wishlist = zozowoobase_major_settings::zozowoobase_get( 'sticky-wishlist-opt' );
if( $sticky_wishlist ){
	add_action( 'wp_footer', 'zozo_enable_sticky_wishlist', 10 );
	function zozo_enable_sticky_wishlist(){
		echo do_shortcode('[zozo_sticky_wishlist]');
	}
}

class Zozo_Woo_Favourite {
        
	private static $favourite_key = 'zozo_woo_favourite_ip';
	private static $user_fav_key = 'zozo_user_favourite_products';
	
	public static function zozoFavouriteIPVerify( $postID, $veryfiy = false ){
		
		if ( !is_user_logged_in() ) return 0;
		
		$current_user = wp_get_current_user();
		$current_user_id = $current_user->ID;
		
		// Retrieve post votes IPs
		$fav_ids = get_user_meta( $current_user_id, self::$user_fav_key, true );
		$fav_stat = 1;
		
		if( $veryfiy ){
			$fav_stat = 0;
			if( isset( $fav_ids ) && is_array( $fav_ids ) ){
				if( ( $key = array_search( $postID, $fav_ids ) ) !== false ) {
					$fav_stat = 1;
				}
			}
		}else{
			if( isset( $fav_ids ) && is_array( $fav_ids ) ){
				if( ( $key = array_search( $postID, $fav_ids ) ) !== false ) {
					unset( $fav_ids[$key] );
					self::zozoUserFavCheck( $postID, $current_user_id, 'remove' );
					$fav_stat = 0;
				}else{
					array_push( $fav_ids, $postID );		
					self::zozoUserFavCheck( $postID, $current_user_id, 'add' );
				}
			}else{
				$fav_ids = array( $postID );
				self::zozoUserFavCheck( $postID, $current_user_id, 'add' );
			}

			$updated = update_user_meta( $current_user_id, self::$user_fav_key, $fav_ids );
			
			$fav_ids = get_user_meta( $current_user_id, self::$user_fav_key, true );
			$fav_count = !empty( $fav_ids ) && is_array( $fav_ids ) ? count( $fav_ids ) : 0;
			$fav_stat = $fav_stat ? 'fav' : 'unfav';
			return json_encode( array( 'stat' => $fav_stat, 'count' => $fav_count, 'mini_wishlist_count' => $fav_count, 'mini_wishlist' => self::zozoMiniFavouriteProducts() ) );

		}
		 
		return $fav_stat;
	}
	
	public static function zozoUserFavCheck( $postID, $current_user_id, $stat = 'add' ){
		$fav_ids = get_user_meta( $current_user_id, self::$user_fav_key, true );
		if( !empty( $fav_ids ) && is_array( $fav_ids ) ){
			if( ( $key = array_search( $postID, $fav_ids ) ) !== false ) {
				if( $stat == 'remove' ) unset( $fav_ids[$key] );
			}else{
				if( $stat == 'add' ) array_push( $fav_ids, $postID );
			}
			$updated = update_user_meta( $current_user_id, self::$user_fav_key, $fav_ids );
		}else{
			if( $stat == 'add' ) update_user_meta( $current_user_id, self::$user_fav_key, array( $postID ) );
		}
	}

	public static function zozoMetaFavouriteCheck()	{
		
		if ( !is_user_logged_in() ) return 0;
		
		// Check for nonce security
		$nonce = $_POST['nonce'];  
		if ( ! wp_verify_nonce( $nonce, 'zozo-wishlist-%!@)(^' ) ) wp_die( esc_html__( 'Busted', 'zozo-woo-base' ) );
			
		$postID = isset( $_POST['post_id'] ) ? esc_attr( $_POST['post_id'] ) : '';
		
		if( $postID != '' ){
			$fav_stat = self::zozoFavouriteIPVerify( $postID );
			echo $fav_stat;		
		}
		wp_die();
	}
	
	public static function zozoUserFavouriteProducts( $atts ){
		$current_user = wp_get_current_user();
		$current_user_id = $current_user->ID;
		$fav_ids = get_user_meta( $current_user_id, self::$user_fav_key, true );
		$output = '';
		if( !empty( $fav_ids ) && is_array( $fav_ids ) ){
			$args = array(
				'post_type' => 'product',
				'posts_per_page' => -1,
				'post__in'=> $fav_ids
			);
			$the_query = new WP_Query( $args ); 
			if ( $the_query->have_posts() ) : 
				$output .= '<table class="table table-borderless zozo-wishlist-table">';
					while ( $the_query->have_posts() ) : $the_query->the_post(); 
						global $product;
						$thumb = '';
						$title = get_the_title();
						$price = '<span class="price">'. $product->get_price_html() .'</span>';
						$add_to_cart = '<a href="' . get_permalink( $the_query->post->ID ) . '" data-product_id="'. esc_attr( $the_query->post->ID ) .'" data-product_sku="'. esc_attr( $the_query->post->sku ) .'" class="button add_to_cart_button ajax_add_to_cart zozo_ajax_add_to_cart" aria-label="'. esc_attr( get_the_title() ) .'" rel="nofollow">' . __( 'Add to Cart', 'zozo-woo-base' ) . '</a>';
						if (has_post_thumbnail( $the_query->post->ID )) $thumb = get_the_post_thumbnail( $the_query->post->ID, 'thumbnail' );
						else $thumb = '<img src="'. woocommerce_placeholder_img_src() .'" alt="Placeholder" width="300px" height="300px" />';
						$output .= '<tr><td>'. $thumb .'</td><td>'. $title .'</td><td>'. $price .'</td>';
						$output .= '<td>'. $add_to_cart .'</td>';
						$output .= '<td><a href="#" class="zozo-wishlist-remove" data-product-id="'. esc_attr( $the_query->post->ID ) .'"><span class="ti-close"></span></a></td>';
						$output .= '</tr>';
					endwhile;  wp_reset_postdata();	
				$output .= '</table>';	
			endif; 
		}else{
			$output .= '<p class="lead">'. esc_html__( '!No wishlist items exists.', 'zozo-woo-base' ) .'</p>';
		}
		
		return $output;
	}
	
	public static function zozoWishlistRemove(){
		
		if ( !is_user_logged_in() ) return 0;
		
		// Check for nonce security
		$nonce = $_POST['nonce'];  
		if ( ! wp_verify_nonce( $nonce, 'zozo-wishlist-{}@@%^@' ) ) wp_die( esc_html__( 'Busted', 'zozo-woo-base' ) );
			
		$product_id = isset( $_POST['product_id'] ) ? esc_attr( $_POST['product_id'] ) : '';
		$current_user = wp_get_current_user();
		$current_user_id = $current_user->ID;
		
		if( $product_id != '' ){
			$fav_ids = get_user_meta( $current_user_id, self::$user_fav_key, true );
			if( !empty( $fav_ids ) && is_array( $fav_ids ) ){
				if( ( $key = array_search( $product_id, $fav_ids ) ) !== false ) {
					unset( $fav_ids[$key] );
					$updated = update_user_meta( $current_user_id, self::$user_fav_key, $fav_ids );
				}
			}
		}
		
		$fav_ids = get_user_meta( $current_user_id, self::$user_fav_key, true );
		$fav_count = !empty( $fav_ids ) && is_array( $fav_ids ) ? count( $fav_ids ) : 0;
		$fav_stat = $fav_stat ? 'fav' : 'unfav';
		echo json_encode( array( 'stat' => $fav_stat, 'count' => $fav_count, 'mini_wishlist_count' => $fav_count, 'mini_wishlist' => self::zozoMiniFavouriteProducts() ) );
		
		wp_die();
	}
	
	public static function zozoMiniWishlist( $atts ){
		$count = 0;
		$woo_out = '';
		
		if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return '';
		$wishlist_page_id = zozowoobase_major_settings::zozowoobase_get( 'wishlist-page-id' );
		$wishlist_dash_link = get_permalink( $wishlist_page_id );
		
		$current_user = wp_get_current_user();
		$current_user_id = $current_user->ID;
		$fav_ids = get_user_meta( $current_user_id, 'zozo_user_favourite_products', true );
		
		if( !empty( $fav_ids ) && is_array( $fav_ids ) ) $count = count( $fav_ids );
		$woo_out .= '<div class="mini-wishlist-dropdown">';
			$woo_out .= '<a href="'. esc_url( $wishlist_dash_link ) .'" class="mini-wishlist-item"><i class="ti-heart"></i>';
				$woo_out .= '<span class="woo-icon-count zozo-wishlist-items-count">'. esc_html( $count ) .'</span>';
			$woo_out .= '</a>';
			$woo_out .= '<ul class="wishlist-dropdown-menu">'. self::zozoMiniFavouriteProducts() .'</ul>';
		$woo_out .= '</div>';

		return $woo_out;
	}
		
	public static function zozoStickyWishlist( $atts ){
		$count = 0;
		$woo_out = '';
		
		if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return '';
		$wishlist_page_id = zozowoobase_major_settings::zozowoobase_get( 'wishlist-page-id' );
		$wishlist_dash_link = get_permalink( $wishlist_page_id );
		
		$current_user = wp_get_current_user();
		$current_user_id = $current_user->ID;
		$fav_ids = get_user_meta( $current_user_id, 'zozo_user_favourite_products', true );
		
		if( !empty( $fav_ids ) && is_array( $fav_ids ) ) $count = count( $fav_ids );
		$woo_out .= '<div class="zozo-sticky-wishlist-wrap">';
			$woo_out .= '<a href="'. esc_url( $wishlist_dash_link ) .'" class="zozo-sticky-wishlist-close"><i class="ti-heart"></i>';
				$woo_out .= '<span class="woo-icon-count zozo-wishlist-items-count">'. esc_html( $count ) .'</span>';
			$woo_out .= '</a>';
			$woo_out .= '<ul class="zozo-sticky-wishlist">'. self::zozoMiniFavouriteProducts() .'</ul>';
		$woo_out .= '</div>';
		return $woo_out;
	}
	
	public static function zozoMiniFavouriteProducts(){
		
		$wishlist_page_id = zozowoobase_major_settings::zozowoobase_get( 'wishlist-page-id' );
		$wishlist_dash_link = get_permalink( $wishlist_page_id );
		
		$current_user = wp_get_current_user();
		$current_user_id = $current_user->ID;
		$fav_ids = get_user_meta( $current_user_id, self::$user_fav_key, true );
		$empty_wishlist = '<li class="wishlist-item"><p class="no-wishlist-items">'. apply_filters( 'zozo_woo_mini_wishlist_empty', esc_html__('!No wishlist items exists.', 'zozo-woo-base') ) .'</p></li>';
		if( empty( $fav_ids ) || !is_array( $fav_ids ) ) return $empty_wishlist;
		
		$args = array(
			'post_type' => 'product',
			'posts_per_page' => -1,
			'post__in'=> $fav_ids
		);
		$the_query = new WP_Query( $args ); 
		
		ob_start();
		
		if ( $the_query->have_posts() ) : 
			while ( $the_query->have_posts() ) : $the_query->the_post(); 
				global $product;
				$product_id = $the_query->post->ID;

				if ( $product && $product->exists() ) {
					$product_permalink = $product->is_visible() ? $product->get_permalink( $product_id ) : ''; ?>
				
					<li class="wishlist-item" data-product-id="<?php echo esc_attr( $product_id ); ?>">
						<div class="product-thumbnail">
							<?php
								$thumbnail = $product->get_image();
								if ( ! $product_permalink ) {
									echo ( ''. $thumbnail );
								} else {
									printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
								}
							?>
						</div>
						<div class="product-name" data-title="<?php esc_attr_e( 'Product', 'zozo-woo-base' ); ?>">
							<?php echo sprintf( '<a href="%s" title="%s">%s</a>', esc_url( $product_permalink ), esc_attr( $product->get_title() ), $product->get_title() ); ?>
							<p>
								<span><?php echo $product->get_price_html(); ?>
							</p>
						</div>
						<div class="product-remove">
							<?php
								echo 
								sprintf(
									'<a href="#" class="remove-wishlist-item" title="%s" data-product-id="%s"><span class="ti-trash"></span></a>',
									esc_html__( 'Remove this item', 'zozo-woo-base' ),
									esc_attr( $product_id )
								);
							?>
						</div>
					</li>
				<?php
				}//if product exists
			
			endwhile;  wp_reset_postdata();	
			?>
			<li class="mini-view-wishlist"><a href="<?php echo esc_url( $wishlist_dash_link ); ?>" title="<?php esc_attr_e('Wishlist', 'zozo-woo-base'); ?>"><?php esc_html_e('View Wishlist', 'zozo-woo-base'); ?></a></li><?php
		endif;
		
		$output = ob_get_clean();
		
		return $output;
	}

}

//Wishlist Shortcode
add_shortcode( 'zozo_user_wishlist', array( 'Zozo_Woo_Favourite', 'zozoUserFavouriteProducts' ) );
add_shortcode( 'zozo_mini_wishlist', array( 'Zozo_Woo_Favourite', 'zozoMiniWishlist' ) );
add_shortcode( 'zozo_sticky_wishlist', array( 'Zozo_Woo_Favourite', 'zozoStickyWishlist' ) );

//Product Favourite
add_action( 'wp_ajax_woo_fav_act', array( 'Zozo_Woo_Favourite', 'zozoMetaFavouriteCheck' ) );
add_action( 'wp_ajax_nopriv_woo_fav_act', array( 'Zozo_Woo_Favourite', 'zozoMetaFavouriteCheck' ) );

//Wishlist Remove
add_action( 'wp_ajax_zozo_wishlist_remove', array( 'Zozo_Woo_Favourite', 'zozoWishlistRemove' ) );
add_action( 'wp_ajax_nopriv_zozo_wishlist_remove', array( 'Zozo_Woo_Favourite', 'zozoWishlistRemove' ) );