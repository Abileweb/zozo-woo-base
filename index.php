<?php 
/*
	Plugin Name: Zozo Woo Base
	Plugin URI: https://zozothemes.com/
	Description: Zozo Woo Base is a plugin which displays the email subscription form when a product is out of stock and send mail notofication when back in stock.
	Version: 1.0
	Author: zozothemes
	Author URI: https://zozothemes.com/
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ))  ) ){
	return;
}

define( 'ZOZO_WOO_BASE_DIR', plugin_dir_path( __FILE__ ) );
define( 'ZOZO_WOO_BASE_URL', plugin_dir_url( __FILE__ ) );

load_plugin_textdomain( 'zozo-woo-base', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

if( ! class_exists('zozowoobase_major_settings') ) {
	
	class zozowoobase_major_settings {
		
		private static $_instance = null;
		
		public static $plugin_version = null;
		
		public function __construct() {
			
			//Set plugin version
			$this->zozowoobase_set_plugin_version();
			
			//Woocommerce template path
			add_filter( 'woocommerce_locate_template', [ $this, 'zozowoobase_woo_addon_plugin_template' ], 1, 3 );
			
			//Woocommerce theme support
			add_action( 'after_setup_theme', [ $this, 'zozo_woocommerce_support' ] );
			
			//Woo base scripts/styles
			add_action( 'wp_enqueue_scripts', [ $this, 'zozowoobase_scripts' ] );
			
			//Woo base functions
			$this->zozo_woo_base_connectivity();
			
		}
		
		public function zozowoobase_set_plugin_version() {
			if ( is_admin() ) {
				if( ! function_exists('get_plugin_data') ){
					require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				}
				$plugin_data = get_plugin_data( ZOZO_WOO_BASE_DIR . 'index.php' );
				self::$plugin_version = isset( $plugin_data['Version'] ) ? $plugin_data['Version'] : '1.0';
			}
		}	

		public function zozowoobase_woo_addon_plugin_template( $template, $template_name, $template_path ) {
			global $woocommerce;
			$_template = $template;
			if ( ! $template_path ) 
			$template_path = $woocommerce->template_url;

			$plugin_path  = untrailingslashit( plugin_dir_path( __FILE__ ) )  . '/woocommerce/';
			
			if( file_exists( $plugin_path . $template_name ) ){
				$template = $plugin_path . $template_name;
				if( ! $template )
					$template = locate_template(
						array(
							$template_path . $template_name,
							$template_name
						)
					);
			}

			if( ! $template && file_exists( $plugin_path . $template_name ) )
			$template = $plugin_path . $template_name;

			if ( ! $template )
			$template = $_template;

			return $template;
		}			
		
		function zozo_woocommerce_support() {
			add_theme_support( 'woocommerce' );
			add_theme_support( 'wc-product-gallery-zoom' );
			add_theme_support( 'wc-product-gallery-lightbox' );
			add_theme_support( 'wc-product-gallery-slider' );
		}
		
		public function zozowoobase_scripts(){
			$user_logged = is_user_logged_in() ? 1 : 0;
			$localize_things = array(
				'admin_ajax_url' 	=> esc_url( admin_url('admin-ajax.php') ),
				'woo_user_page' 	=> get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ),
				'user_logged'		=> $user_logged,
				'wishlist_nonce'	=> wp_create_nonce( 'zozo-wishlist-%!@)(^' ),
				'wishlist_remove'	=> wp_create_nonce( 'zozo-wishlist-{}@@%^@' ),
				'add_to_cart' 				=> wp_create_nonce('zozo-add-to-cart'),
				'remove_from_cart' 			=> wp_create_nonce('zozo-remove-from-cart(*$#'),
				'variation_not_available'	=> esc_html__( '!Sorry. Variation not available.', 'zozo-woo-base' ),
				'product_not_available'		=> esc_html__( '!Sorry. Product not available.', 'zozo-woo-base' )
			);
			
			//Styles
			wp_enqueue_style( 'themify-icons', ZOZO_WOO_BASE_URL . 'assets/css/themify-icons.css', array(), '1.0' );
			wp_enqueue_style( 'zozowoobase', ZOZO_WOO_BASE_URL . 'assets/css/style.css', array(), '1.0' );
			
			//Scripts
			wp_enqueue_script( 'zozowoobase', ZOZO_WOO_BASE_URL . 'assets/js/custom.js',  array( 'jquery', 'jquery-ui-core' ), '1.0', true );
			wp_localize_script('zozowoobase', 'zozowoobase_ajax_var', $localize_things );			
		}
		
		public static function zozowoobase_get( $field ){ 
			$default_options = array(
				'woo-shop-pagetitle'		=> false,
				'woo-shop-ppp'				=> 9,
				'woo-shop-columns'			=> 3,
				'woo-shop-archive-columns'	=> 3,
				'sticky-minicart-opt'		=> 0,
				'wishlist-page-id'			=> '',
				'sticky-wishlist-opt'		=> 0,
			);
			$options = get_option('zozo_woo_addon_options');
			$options = !empty( $options ) ? $options : $default_options;
			
			return isset( $options[$field] ) ? $options[$field] : '';
		}
		
		public function zozo_woo_base_connectivity(){
			
			//Woo base admin page
			require_once ZOZO_WOO_BASE_DIR . "plugin-options/options.php";
			
			//Woo base functions
			require_once ZOZO_WOO_BASE_DIR . "woo-base.php";
			
			//Woo minicart functions
			require_once ZOZO_WOO_BASE_DIR . "inc/woo-minicart.php";
			
			//Woo wishlist
			require_once ZOZO_WOO_BASE_DIR . "inc/woo-wishlist.php";
		}
		
		public static function get_instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
		
	}

}
zozowoobase_major_settings::get_instance();