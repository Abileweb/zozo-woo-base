<?php

//'required'	=> array( 'woo-shop-sidebar-option', '!=', 'none' )

//General
zozo_woo_base_framework_options::zozo_woo_set_section( array(
	'title'      => esc_html__( 'General Settings', 'zozo-woo-addon' ),
	'id'         => 'zozo-woo-section-general',
	'fields'	 => array(
		array(
			'id'		=> 'woo-shop-ppp',
			'type'		=> 'text',
			'title'		=> esc_html__( 'Number of Products Per Shop Page', 'zozo-woo-addon' ),
			'desc'		=> esc_html__( 'Enter products per page for shop page. Example 9', 'zozo-woo-addon' ),
			'default'	=> '9'
		),
		array(
			'id'		=> 'woo-shop-columns',
			'type'		=> 'select',
			'title'		=> esc_html__( 'Shop Page Product Columns', 'zozo-woo-addon' ),
			'desc'		=> esc_html__( 'You can choose shop page product columns.', 'zozo-woo-addon' ),
			'default'	=> '3',
			'options'	=> array(
				'2'		=> esc_html__( '2 Columns', 'zozo-woo-addon' ),
				'3'		=> esc_html__( '3 Columns', 'zozo-woo-addon' ),
				'4'		=> esc_html__( '4 Columns', 'zozo-woo-addon' ),
				'5'		=> esc_html__( '5 Columns', 'zozo-woo-addon' )
			)
		),
		array(
			'id'		=> 'woo-archive-columns',
			'type'		=> 'select',
			'title'		=> esc_html__( 'Archive Page Product Columns', 'zozo-woo-addon' ),
			'desc'		=> esc_html__( 'You can choose archive page product columns.', 'zozo-woo-addon' ),
			'default'	=> '3',
			'options'	=> array(
				'2'		=> esc_html__( '2 Columns', 'zozo-woo-addon' ),
				'3'		=> esc_html__( '3 Columns', 'zozo-woo-addon' ),
				'4'		=> esc_html__( '4 Columns', 'zozo-woo-addon' ),
				'5'		=> esc_html__( '5 Columns', 'zozo-woo-addon' )
			)
		),
		array(
			'id'		=> 'woo-related-columns',
			'type'		=> 'select',
			'title'		=> esc_html__( 'Related Product Columns', 'zozo-woo-addon' ),
			'desc'		=> esc_html__( 'You can choose related product columns.', 'zozo-woo-addon' ),
			'default'	=> '4',
			'options'	=> array(
				'2'		=> esc_html__( '2 Columns', 'zozo-woo-addon' ),
				'3'		=> esc_html__( '3 Columns', 'zozo-woo-addon' ),
				'4'		=> esc_html__( '4 Columns', 'zozo-woo-addon' ),
				'5'		=> esc_html__( '5 Columns', 'zozo-woo-addon' )
			)
		),
	)
) );

//Mini Cart
zozo_woo_base_framework_options::zozo_woo_set_section( array(
	'title'      => esc_html__( 'Mini Cart Settings', 'zozo-woo-addon' ),
	'id'         => 'zozo-woo-section-minicart',
	'fields'	 => array(
		array(
			'id'		=> 'sticky-minicart-opt',
			'type'		=> 'switch',
			'title'		=> esc_html__( 'Sticky Mini Cart Option', 'zozo-woo-addon' ),
			'desc' => esc_html__( 'Enable/Disable sticky minicart for your site. You can place mini cart shortcode wherever you want. [zozo_mini_cart]', 'zozo-woo-addon' ),
			'default'	=> 0,
			'1'       	=> esc_html__( 'Enable', 'zozo-woo-addon' ),
			'0'			=> esc_html__( 'Disable', 'zozo-woo-addon' ),
		),
	)
) );

//Wishlist
zozo_woo_base_framework_options::zozo_woo_set_section( array(
	'title'      => esc_html__( 'Wishlist Settings', 'zozo-woo-addon' ),
	'id'         => 'zozo-woo-section-wishlist',
	'fields'	 => array(
		array(
			'id'		=> 'wishlist-page-id',
			'type'		=> 'select',
			'title'		=> esc_html__( 'Wishlist Page', 'zozo-woo-addon' ),
			'desc'		=> esc_html__( 'Choose wishlist page for show wishlist table. You can place wishlist shortcode wherever you want. [zozo_user_wishlist]', 'zozo-woo-addon' ),
			'get'		=> 'pages'
		),
		array(
			'id'		=> 'sticky-wishlist-opt',
			'type'		=> 'switch',
			'title'		=> esc_html__( 'Sticky Wishlist Option', 'zozo-woo-addon' ),
			'desc' => esc_html__( 'Enable/Disable sticky wishlist for your site. You can place mini wishlist shortcode wherever you want. [zozo_mini_wishlist]', 'zozo-woo-addon' ),
			'default'	=> 0,
			'1'       	=> esc_html__( 'Enable', 'zozo-woo-addon' ),
			'0'			=> esc_html__( 'Disable', 'zozo-woo-addon' ),
		),
	)
) );