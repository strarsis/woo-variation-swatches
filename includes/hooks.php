<?php
	defined( 'ABSPATH' ) or die( 'Keep Quit' );
	
	add_filter( 'product_attributes_type_selector', 'wvs_product_attributes_types' );
	
	add_action( 'init', 'wvs_settings', 2 );
	
	add_action( 'admin_init', 'wvs_add_product_taxonomy_meta' );
	
	add_action( 'woocommerce_product_option_terms', 'wvs_product_option_terms', 10, 2 );
	
	add_filter( 'woocommerce_ajax_variation_threshold', 'wvs_ajax_variation_threshold', 20 );
	
	add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'wvs_variation_attribute_options_html', 200, 2 );