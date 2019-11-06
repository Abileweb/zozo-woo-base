<?php

/**
 * Zozo Woo Addon Plugin Options
 * @since 1.0.0
 */
final class zozo_woo_base_plugin_options {
	
	private static $_instance = null;
	
	public static $zozo_options = null;

	public static $possibility_arr = null;
	
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function __construct() {
		$this->init();
	}
	
	public function init() {
		
		self::$zozo_options = get_option('zozo_woo_addon_options');
		
		//Plugin options scripts
		$possibility_arr = array( 'zozo-woo-addon', 'zozo-pro-features', 'zozo-pro-settings' );
		if( isset( $_GET['page'] ) && in_array( $_GET['page'], $possibility_arr ) ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_options_enqueue_admin_script' ) );
		}
		
		//Plugin options framework
		$this->zozo_woo_addon_framework();
		
		//Call admin script
		add_action( 'admin_menu', array( $this, 'admin_menu_making' ) );	
	}
	
	public function admin_options_enqueue_admin_script(){
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker');
		wp_enqueue_script( 'wp-color-picker');
		wp_enqueue_style( 'zozo-woo-admin-styles', ZOZO_WOO_BASE_URL . 'plugin-options/assets/css/admin-style.css', array(), '1.0' );
		wp_enqueue_script( 'zozo-woo-admin-options', ZOZO_WOO_BASE_URL . 'plugin-options/assets/js/option-page-script.js', array( 'jquery' ), '1.0' );
		//Localize Script
		wp_localize_script( 'zozo-woo-admin-options', 'zwb_ajax_var', array(
			'confirm_str' => esc_html( 'Are you sure to save?', 'zozo-woo-addon' )
		));
	}
	
	public static function zozo_woo_addon_options( $field_name ){
		return isset( self::$zozo_options[$field_name] ) ? self::$zozo_options[$field_name] : '';
	}
	
	public function zozo_woo_addon_framework(){
		require_once( ZOZO_WOO_BASE_DIR . 'plugin-options/framework.php' );
		zozo_woo_base_framework_options::$opt_name = 'zozo_woo_addon_options';
	}
	
	public function admin_menu_making(){
		
		//Create admin menu page
		add_menu_page( 
			esc_html__( 'Zozo Woo Addon', 'zozo-woo-addon' ),
			esc_html__( 'Zozo Woo Addon', 'zozo-woo-addon' ),
			'manage_options',
			'zozo-woo-addon', 
			array( $this, 'zozo_woo_admin_page_output' ),
			'dashicons-networking',
			6
		);
		
		//Create admin sub menu page
		add_submenu_page(
			'zozo-woo-addon',
			esc_html__( 'Pro Features', 'zozo-woo-addon' ),
			esc_html__( 'Pro Features', 'zozo-woo-addon' ),
			'manage_options',
			'zozo-pro-features',
			array( $this, 'zozo_woo_features_page_output' )
		);
		
		//Change first submenu name
		global $submenu;
		$submenu['zozo-woo-addon'][0][0] = esc_html__( 'General Settings', 'zozo-woo-addon' );
	}

	public function zozo_woo_features_page_output() { 
		?>
		<div class="zwb-admin-wrap">
		
			<div class="zwb-head">
				<h1><?php esc_html_e( 'Feature Manager', 'zozo-woo-addon' ); ?></h1>
				<div class="notice">
					<p class="pa-title-sub"><?php esc_html_e( 'Thank you for using Zozo Woo Addon Plugin. User can find doctors, clinics &#38; other healthcare providers in any place. Search by specialties, localities and more. This plugin has been developed by', 'zozo-woo-addon' ); ?> <strong><?php esc_html_e( 'zozothemes', 'zozo-woo-addon' ); ?></strong></p>
				</div>
			</div><!-- .zwb-head -->			
			
			<div class="zwb-content">
				<div class="metabox-holder">
					<?php
						//Admin Page Content
						require_once( ZOZO_WOO_BASE_DIR . 'plugin-options/inc/admin-page-content.php' );
					?>
					<div class="postbox-container">
						<div class="zwb-admin-section meta-box-sortables ui-sortable">
							<div class="postbox">
								<button type="button" class="handlediv"><span class="toggle-indicator" aria-hidden="true"></span></button>
								<h2 class="hndle ui-sortable-handle"><span><?php esc_html_e( 'Feature Manager', 'zozo-woo-addon' ) ?></span></h2>
								<div class="inside">
										
								</div>
							</div>
						</div><!-- .zwb-admin-section -->
					</div><!-- .postbox-container -->
				</div><!-- .metabox-holder -->
			</div><!-- .zwb-content -->
			
		</div><!-- .zwb-admin-wrap -->
		<?php
	}
	
	public function zozo_woo_admin_page_output(){
		?>
		<div class="zwb-admin-wrap">
		
			<div class="zwb-head">
				<h1><?php esc_html_e( 'Zozo Woo Addon', 'zozo-woo-addon' ); ?></h1>
				<div class="notice">
					<p class="pa-title-sub"><?php esc_html_e( 'Thank you for using Zozo Woo Addon Plugin. User can find doctors, clinics &#38; other healthcare providers in any place. Search by specialties, localities and more. This plugin has been developed by', 'zozo-woo-addon' ); ?> <strong><?php esc_html_e( 'zozothemes', 'zozo-woo-addon' ); ?></strong></p>
				</div>
			</div><!-- .zwb-head -->			
			
			<div class="zwb-content">
				<div class="metabox-holder">
					<?php
						//Admin Page Content
						require_once( ZOZO_WOO_BASE_DIR . 'plugin-options/inc/admin-page-content.php' );
					?>
					<div class="postbox-container">
						<form method="post" action="#" enctype="multipart/form-data" class="zwb-form-wrapper">
							<?php 
			
								if ( isset( $_POST['save_zozo_woo_options'] ) && wp_verify_nonce( $_POST['save_zozo_woo_options'], 'zozo_woo_base_framework_options' ) ) {
									update_option( 'zozo_woo_addon_options', $_POST['zozo_woo_addon_options'] );
								}
								
								//Get updated theme option
								zozo_woo_base_framework_options::$zozo_woo_addon_options = get_option('zozo_woo_addon_options');
								
								//Plugin config
								require_once( ZOZO_WOO_BASE_DIR . 'plugin-options/config.php' );
								
							?>
							<?php wp_nonce_field( 'zozo_woo_base_framework_options', 'save_zozo_woo_options' ); ?>
							<input name="submit" type="submit" class="button-primary" value="<?php esc_html_e( 'Update Options', 'zozo-woo-addon' ); ?>">
						</form>
					</div><!-- .postbox-container -->
				</div><!-- .metabox-holder -->
			</div><!-- .zwb-content -->
			
		</div><!-- .zwb-admin-wrap -->
		<?php
	}
}

zozo_woo_base_plugin_options::instance();