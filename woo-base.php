<?php 

//Remove add to cart button
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

//Remove Thumb Link
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

//Product thumbnail with link
add_action( 'woocommerce_after_shop_loop_item', 'zozowoobase_woocommerce_thumb_buttons_pack_open', 15 );
function zozowoobase_woocommerce_thumb_buttons_pack_open(){
	echo '<div class="zozo-woo-buttons-pack">';
	do_action( 'zozowoobase_woocommerce_thumb_buttons_pack' );
}

//Add to Cart Button
add_action( 'zozowoobase_woocommerce_thumb_buttons_pack', 'woocommerce_template_loop_add_to_cart', 20 );

add_action( 'zozowoobase_woocommerce_thumb_buttons_pack', 'zozowoobase_woocommerce_thumb_buttons_pack_close', 90 );
function zozowoobase_woocommerce_thumb_buttons_pack_close(){
	echo '</div><!-- .zozo-woo-buttons-pack -->';
}

//Set Woo Shop/Archive Columns
function zozo_woo_set_columns($columns){
	$woo_col = 4;
	if ( is_product_category() || is_product_tag() ) {
		$woo_col = zozowoobase_major_settings::zozowoobase_get('woo-archive-columns');
	}else {
		$woo_col = zozowoobase_major_settings::zozowoobase_get('woo-shop-columns');
	}
	return $woo_col;
}
add_filter('loop_shop_columns','zozo_woo_set_columns');

//Related Columns

add_filter( 'woocommerce_output_related_products_args', 'zozowoobase_woocommerce_related_products', 99 );
function zozowoobase_woocommerce_related_products( $args ){
	$woo_col = zozowoobase_major_settings::zozowoobase_get('woo-related-columns');
	$woo_col = $woo_col ? $woo_col : 4;
	$args['posts_per_page'] = $woo_col; // # of related products
	$args['columns'] = $woo_col; // # of columns per row
	return $args;
}

remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
add_action( 'woocommerce_shop_loop_item_title', 'zozowoobase_woocommerce_template_loop_product_title', 10 );
function zozowoobase_woocommerce_template_loop_product_title() {
	echo '<h2 class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '"><a href="'. esc_url( get_the_permalink() ) .'">' . get_the_title() . '</a></h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}