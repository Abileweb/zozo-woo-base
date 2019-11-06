<?php 

//Shop page title
function zozo_filter_woocommerce_show_page_title() { 
	if( is_shop() ){
		return false;
	}
};
$shop_pagetitle = zozowoobase_major_settings::zozowoobase_get('woo-shop-pagetitle');
if( !$shop_pagetitle ) add_filter( 'woocommerce_show_page_title', 'zozo_filter_woocommerce_show_page_title', 10, 2 ); 

//Product thumbnail columns
function zozo_woocommerce_product_thumbnails_columns( $cols ) { 
	return 1;
};
add_filter( 'woocommerce_product_thumbnails_columns', 'zozo_woocommerce_product_thumbnails_columns' );

add_action('init', 'woocommerce_sort_by_columns_fun');
function woocommerce_sort_by_columns_fun() {
	if (isset($_POST['woocommerce-sort-by-columns'])) {
		setcookie('shop_pageResults', $_POST['woocommerce-sort-by-columns'], time()+1209600 );
	}
}

add_action( 'after_setup_theme', 'zozo_woocommerce_support' );
function zozo_woocommerce_support() {
    add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}

remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
add_action( 'woocommerce_before_shop_loop_item', 'zozowoobase_woocommerce_template_loop_product_link_open', 10 );
function zozowoobase_woocommerce_template_loop_product_link_open(){
	echo '<div class="loop-product-wrap">';
}

add_action('woocommerce_before_shop_loop_item_title',  'zozowoobase_woocommerce_before_shop_loop_item_title_start', 5 );
function zozowoobase_woocommerce_before_shop_loop_item_title_start(){
 echo '<div class="woo-thumb-wrap">';
}
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 15 );

//Product thumbnail with link
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail_custom', 10 );
function woocommerce_template_loop_product_thumbnail_custom(){
	echo '<a href="'. esc_url( get_permalink() ) .'" class="zozo-product-link">';
		echo woocommerce_template_loop_product_thumbnail();
	echo '</a>';
	echo '<div class="zozo-woo-buttons-pack">';
}

add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail_custom_close', 15 );
function woocommerce_template_loop_product_thumbnail_custom_close(){
	echo '</div><!-- .zozo-woo-buttons-pack -->';
}

add_action('woocommerce_before_shop_loop_item_title',  'zozowoobase_woocommerce_before_shop_loop_item_title_end', 20 );
function zozowoobase_woocommerce_before_shop_loop_item_title_end(){
 echo '</div><!-- .woo-thumb-wrap -->';
}

add_action( 'woocommerce_after_shop_loop_item', 'zozowoobase_woocommerce_template_loop_product_link_close', 5 );
function zozowoobase_woocommerce_template_loop_product_link_close(){
 echo '</div><!-- .loop-product-wrap -->';
}

function zozo_woo_set_columns($columns){
	$woo_col = 4;
	if ( is_product_category() || is_product_tag() ) {
		$woo_col = zozowoobase_major_settings::zozowoobase_get('woo-shop-archive-columns');
	}else {
		$woo_col = zozowoobase_major_settings::zozowoobase_get('woo-shop-columns');
	}
	return $woo_col;
}
add_filter('loop_shop_columns','zozo_woo_set_columns');

// now we set our cookie if we need to
function zozo_loop_shop_per_page( $count ) {
	if( isset($_POST['woocommerce-sort-by-columns'] ) ) {
		$count = $_POST['woocommerce-sort-by-columns'];	
	}elseif( isset($_COOKIE['shop_pageResults'] ) ) { // if normal page load with cookie
		$count = $_COOKIE['shop_pageResults'];
	}else{
		$shop_ppp = zozowoobase_major_settings::zozowoobase_get('woo-shop-ppp');
		$count = $shop_ppp ? $shop_ppp : 9;
	}
  // else normal page load and no cookie
  return $count;
}
add_filter('loop_shop_per_page','zozo_loop_shop_per_page');

function zozo_woocommerce_catalog_page_ordering() {
	$def_count = '';
	if (isset($_POST['woocommerce-sort-by-columns'])) {
		$count = $_POST['woocommerce-sort-by-columns'];	
	}elseif (isset($_COOKIE['shop_pageResults'])) { // if normal page load with cookie
		$count = $_COOKIE['shop_pageResults'];
	}else{
		$shop_ppp = zozowoobase_major_settings::zozowoobase_get('woo-shop-ppp');
		$count = $def_count = $shop_ppp ? $shop_ppp : 9;
	}?>
	
	<form action="" method="POST" name="results">
		<select name="woocommerce-sort-by-columns" id="woocommerce-sort-by-columns" class="sortby" onchange="this.form.submit()">
			<?php
				$shopCatalog_orderby = apply_filters('woocommerce_sortby_page', array(
					$def_count       => esc_html__('Default', 'zozo-woo-addon'),
					'6'    => esc_html__('6 per page', 'zozo-woo-addon'),
					'12'    => esc_html__('12 per page', 'zozo-woo-addon'),
					'24'        => esc_html__('24 per page', 'zozo-woo-addon'),
					'36'        => esc_html__('36 per page', 'zozo-woo-addon'),
					'48'        => esc_html__('48 per page', 'zozo-woo-addon'),
					'64'        => esc_html__('64 per page', 'zozo-woo-addon'),
				));
				
				foreach ( $shopCatalog_orderby as $sort_id => $sort_name ){
					echo '<option value="' . $sort_id . '" ' . ( $count == $sort_id ? 'selected="selected"' : '' ) . ' >' . $sort_name . '</option>';
				}
			?>
		</select>
	</form>
<?php
} 
add_action( 'woocommerce_before_shop_loop', 'zozo_woocommerce_catalog_page_ordering', 20 );