<?php

	$features_txt_arr = array(
		'more'			=> esc_html__( 'More', 'zozo-woo-addon' ),
		'activate'		=> esc_html__( 'Activate', 'zozo-woo-addon' ),
		'deactivate'	=> esc_html__( 'Deactivate', 'zozo-woo-addon' ),
		'preview'		=> esc_html__( 'Preview', 'zozo-woo-addon' )
	);

	$features_title_arr = array(
		'compare'		=> esc_html__( 'Product Compare', 'zozo-woo-addon' ),
		'deal'			=> esc_html__( 'Deal Products', 'zozo-woo-addon' ),
		'member'		=> esc_html__( 'Member', 'zozo-woo-addon' ),
		'notification'	=> esc_html__( 'Notification', 'zozo-woo-addon' ),
		'quickview'		=> esc_html__( 'Quick View', 'zozo-woo-addon' ),
		'ajaxsearch'	=> esc_html__( 'Ajax Search', 'zozo-woo-addon' ),
		'variation'		=> esc_html__( 'Variation', 'zozo-woo-addon' )
	);

	$pro_stat = 0; $features = ''; $pro_version = ''; $update_req = false;

	if( in_array( 'zozo-woo-pro/index.php', get_option( 'active_plugins' ) ) ){
		$pro_stat = 1;
	}

	if( $pro_stat ) {
		$features = get_option('zozo_woo_pro_act_features');
		if( empty( $features ) || !is_array( $features ) ){
			$features = array( 
				'woo-compare'		=> true, 
				'woo-deal'			=> true, 
				'woo-member'		=> true, 
				'woo-notifier'		=> true, 
				'woo-quickview'		=> true, 
				'woo-search'		=> true, 
				'woo-variation'		=> true
			);
		}
		if ( is_admin() ) {
			if( ! function_exists('get_plugin_data') ){
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			$plugin_data = get_plugin_data( ABSPATH . 'wp-content/plugins/zozo-woo-pro/index.php' );
			$pro_version = isset( $plugin_data['Version'] ) ? $plugin_data['Version'] : '1.0';
			$update_req = $pro_version < zozowoobase_major_settings::$pro_version ? true : false;
		}
	}



?>

<?php wp_nonce_field( 'zozo_woo_addons_update*(!$KJ&*', 'zozo_woo_addons_nonce' ); ?>
<?php wp_nonce_field( 'zozo_woo_pro_update*(!$sJ&*', 'zozo_woo_pro_update_nonce' ); ?>
<?php if( $update_req ) : ?>
	<p class="zozo-woo-update-req">Update required <span class="zozo-woo-pro-version">v1.1</span> <a href="#" class="button-primary zozo-woo-update-trigger">Update</a></p>
<?php endif; ?>

<div class="zozo-woo-features-wrap">

	<div class="zozo-woo-features-inner">
		<div class="zozo-woo-feature-details box-shadow-3">
			<div class="zozo-woo-features-title">
				<h4><?php echo esc_html( $features_title_arr['compare'] ); ?></h4>
			</div>
			<div class="zozo-woo-features-img">
				<img src="<?php echo esc_url( ZOZO_WOO_BASE_URL . 'plugin-options/assets/images/compare.png' ); ?>" alt="<?php echo esc_html( $features_title_arr['compare'] ); ?>" />
			</div>
			<div class="zozo-woo-features-content">
				<p>Add Social Meta data to your site to deliver closer.. <a href="#"><?php echo esc_html( $features_txt_arr['more'] ); ?></a></p>
			</div>
			<?php
				$btn_txt = ''; $addon_stat = 0; $btn_url = '#'; $btn_class = '';
				if( $pro_stat ) {
					if( isset( $features['woo-compare'] ) && $features['woo-compare'] == true ){
						$btn_txt = $features_txt_arr['deactivate'];
						$addon_stat = 1;
					}else{
						$btn_txt = $features_txt_arr['activate'];
					}
					$btn_class = ' addon-process-trigger';
				}else{
					$btn_txt = $features_txt_arr['preview'];
					$btn_url = 'https://zozothemes.com/';
				}
			?>
			<a href="<?php echo esc_url( $btn_url ); ?>" target="_blank" class="button-primary<?php echo esc_attr( $btn_class ); ?>" data-key="woo-compare" data-stat="<?php echo esc_attr( $addon_stat ); ?>"><?php echo esc_html( $btn_txt ); ?></a>
		</div>
	</div><!-- .zozo-woo-features-inner -->

	<div class="zozo-woo-features-inner">
		<div class="zozo-woo-feature-details box-shadow-3">
			<div class="zozo-woo-features-title">
				<h4><?php echo esc_html( $features_title_arr['deal'] ); ?></h4>
			</div>
			<div class="zozo-woo-features-img">
				<img src="<?php echo esc_url( ZOZO_WOO_BASE_URL . 'plugin-options/assets/images/deal.png' ); ?>" alt="<?php echo esc_html( $features_title_arr['deal'] ); ?>" />
			</div>
			<div class="zozo-woo-features-content">
				<p>Add Social Meta data to your site to deliver closer.. <a href="#"><?php echo esc_html( $features_txt_arr['more'] ); ?></a></p>
			</div>
			<?php
				$btn_txt = ''; $addon_stat = 0; $btn_url = '#'; $btn_class = '';
				if( $pro_stat ) {
					if( isset( $features['woo-deal'] ) && $features['woo-deal'] == true ){
						$btn_txt = $features_txt_arr['deactivate'];
						$addon_stat = 1;
					}else{
						$btn_txt = $features_txt_arr['activate'];
					}
					$btn_class = ' addon-process-trigger';
				}else{
					$btn_txt = $features_txt_arr['preview'];
					$btn_url = 'https://zozothemes.com/';
				}
			?>
			<a href="<?php echo esc_url( $btn_url ); ?>" target="_blank" class="button-primary<?php echo esc_attr( $btn_class ); ?>" data-key="woo-deal" data-stat="<?php echo esc_attr( $addon_stat ); ?>"><?php echo esc_html( $btn_txt ); ?></a>
		</div>
	</div><!-- .zozo-woo-features-inner -->

	<div class="zozo-woo-features-inner">
		<div class="zozo-woo-feature-details box-shadow-3">
			<div class="zozo-woo-features-title">
				<h4><?php echo esc_html( $features_title_arr['member'] ); ?></h4>
			</div>
			<div class="zozo-woo-features-img">
				<img src="<?php echo esc_url( ZOZO_WOO_BASE_URL . 'plugin-options/assets/images/member.png' ); ?>" alt="<?php echo esc_html( $features_title_arr['member'] ); ?>" />
			</div>
			<div class="zozo-woo-features-content">
				<p>Add Social Meta data to your site to deliver closer.. <a href="#"><?php echo esc_html( $features_txt_arr['more'] ); ?></a></p>
			</div>
			<?php
				$btn_txt = ''; $addon_stat = 0; $btn_url = '#'; $btn_class = '';
				if( $pro_stat ) {
					if( isset( $features['woo-member'] ) && $features['woo-member'] == true ){
						$btn_txt = $features_txt_arr['deactivate'];
						$addon_stat = 1;
					}else{
						$btn_txt = $features_txt_arr['activate'];
					}
					$btn_class = ' addon-process-trigger';
				}else{
					$btn_txt = $features_txt_arr['preview'];
					$btn_url = 'https://zozothemes.com/';
				}
			?>
			<a href="<?php echo esc_url( $btn_url ); ?>" target="_blank" class="button-primary<?php echo esc_attr( $btn_class ); ?>" data-key="woo-member" data-stat="<?php echo esc_attr( $addon_stat ); ?>"><?php echo esc_html( $btn_txt ); ?></a>
		</div>
	</div><!-- .zozo-woo-features-inner -->

	<div class="zozo-woo-features-inner">
		<div class="zozo-woo-feature-details box-shadow-3">
			<div class="zozo-woo-features-title">
				<h4><?php echo esc_html( $features_title_arr['notification'] ); ?></h4>
			</div>
			<div class="zozo-woo-features-img">
				<img src="<?php echo esc_url( ZOZO_WOO_BASE_URL . 'plugin-options/assets/images/notification.png' ); ?>" alt="<?php echo esc_html( $features_title_arr['notification'] ); ?>" />
			</div>
			<div class="zozo-woo-features-content">
				<p>Add Social Meta data to your site to deliver closer.. <a href="#"><?php echo esc_html( $features_txt_arr['more'] ); ?></a></p>
			</div>
			<?php
				$btn_txt = ''; $addon_stat = 0; $btn_url = '#'; $btn_class = '';
				if( $pro_stat ) {
					if( isset( $features['woo-notifier'] ) && $features['woo-notifier'] == true ){
						$btn_txt = $features_txt_arr['deactivate'];
						$addon_stat = 1;
					}else{
						$btn_txt = $features_txt_arr['activate'];
					}					
					$btn_class = ' addon-process-trigger';
				}else{
					$btn_txt = $features_txt_arr['preview'];
					$btn_url = 'https://zozothemes.com/';
				}
			?>
			<a href="<?php echo esc_url( $btn_url ); ?>" target="_blank" class="button-primary<?php echo esc_attr( $btn_class ); ?>" data-key="woo-notifier" data-stat="<?php echo esc_attr( $addon_stat ); ?>"><?php echo esc_html( $btn_txt ); ?></a>
		</div>
	</div><!-- .zozo-woo-features-inner -->

	<div class="zozo-woo-features-inner">
		<div class="zozo-woo-feature-details box-shadow-3">
			<div class="zozo-woo-features-title">
				<h4><?php echo esc_html( $features_title_arr['quickview'] ); ?></h4>
			</div>
			<div class="zozo-woo-features-img">
				<img src="<?php echo esc_url( ZOZO_WOO_BASE_URL . 'plugin-options/assets/images/quick-view.png' ); ?>" alt="<?php echo esc_html( $features_title_arr['quickview'] ); ?>" />
			</div>
			<div class="zozo-woo-features-content">
				<p>Add Social Meta data to your site to deliver closer.. <a href="#"><?php echo esc_html( $features_txt_arr['more'] ); ?></a></p>
			</div>
			<?php
				$btn_txt = ''; $addon_stat = 0; $btn_url = '#'; $btn_class = '';
				if( $pro_stat ) {
					if( isset( $features['woo-quickview'] ) && $features['woo-quickview'] == true ){
						$btn_txt = $features_txt_arr['deactivate'];
						$addon_stat = 1;
					}else{
						$btn_txt = $features_txt_arr['activate'];
					}					
					$btn_class = ' addon-process-trigger';
				}else{
					$btn_txt = $features_txt_arr['preview'];
					$btn_url = 'https://zozothemes.com/';
				}
			?>
			<a href="<?php echo esc_url( $btn_url ); ?>" target="_blank" class="button-primary<?php echo esc_attr( $btn_class ); ?>" data-key="woo-quickview" data-stat="<?php echo esc_attr( $addon_stat ); ?>"><?php echo esc_html( $btn_txt ); ?></a>
		</div>
	</div><!-- .zozo-woo-features-inner -->

	<div class="zozo-woo-features-inner">
		<div class="zozo-woo-feature-details box-shadow-3">
			<div class="zozo-woo-features-title">
				<h4><?php echo esc_html( $features_title_arr['ajaxsearch'] ); ?></h4>
			</div>
			<div class="zozo-woo-features-img">
				<img src="<?php echo esc_url( ZOZO_WOO_BASE_URL . 'plugin-options/assets/images/search.png' ); ?>" alt="<?php echo esc_html( $features_title_arr['ajaxsearch'] ); ?>" />
			</div>
			<div class="zozo-woo-features-content">
				<p>Add Social Meta data to your site to deliver closer.. <a href="#"><?php echo esc_html( $features_txt_arr['more'] ); ?></a></p>
			</div>
			<?php
				$btn_txt = ''; $addon_stat = 0; $btn_url = '#'; $btn_class = '';
				if( $pro_stat ) {
					if( isset( $features['woo-search'] ) && $features['woo-search'] == true ){
						$btn_txt = $features_txt_arr['deactivate'];
						$addon_stat = 1;
					}else{
						$btn_txt = $features_txt_arr['activate'];
					}					
					$btn_class = ' addon-process-trigger';
				}else{
					$btn_txt = $features_txt_arr['preview'];
					$btn_url = 'https://zozothemes.com/';
				}
			?>
			<a href="<?php echo esc_url( $btn_url ); ?>" target="_blank" class="button-primary<?php echo esc_attr( $btn_class ); ?>" data-key="woo-search" data-stat="<?php echo esc_attr( $addon_stat ); ?>"><?php echo esc_html( $btn_txt ); ?></a>
		</div>
	</div><!-- .zozo-woo-features-inner -->

	<div class="zozo-woo-features-inner">
		<div class="zozo-woo-feature-details box-shadow-3">
			<div class="zozo-woo-features-title">
				<h4><?php echo esc_html( $features_title_arr['variation'] ); ?></h4>
			</div>
			<div class="zozo-woo-features-img">
				<img src="<?php echo esc_url( ZOZO_WOO_BASE_URL . 'plugin-options/assets/images/variation.png' ); ?>" alt="<?php echo esc_html( $features_title_arr['variation'] ); ?>" />
			</div>
			<div class="zozo-woo-features-content">
				<p>Add Social Meta data to your site to deliver closer.. <a href="#"><?php echo esc_html( $features_txt_arr['more'] ); ?></a></p>
			</div>
			<?php
				$btn_txt = ''; $addon_stat = 0; $btn_url = '#'; $btn_class = '';
				if( $pro_stat ) {
					if( isset( $features['woo-variation'] ) && $features['woo-variation'] == true ){
						$btn_txt = $features_txt_arr['deactivate'];
						$addon_stat = 1;
					}else{
						$btn_txt = $features_txt_arr['activate'];
					}
					$btn_class = ' addon-process-trigger';
				}else{
					$btn_txt = $features_txt_arr['preview'];
					$btn_url = 'https://zozothemes.com/';
				}
			?>
			<a href="<?php echo esc_url( $btn_url ); ?>" target="_blank" class="button-primary<?php echo esc_attr( $btn_class ); ?>" data-key="woo-variation" data-stat="<?php echo esc_attr( $addon_stat ); ?>"><?php echo esc_html( $btn_txt ); ?></a>
		</div>
	</div><!-- .zozo-woo-features-inner -->

</div><!-- .postbox-container -->