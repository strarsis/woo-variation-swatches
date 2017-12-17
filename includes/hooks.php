<?php
	defined( 'ABSPATH' ) or die( 'Keep Quit' );
	
	add_filter( 'product_attributes_type_selector', 'fvs_product_attributes_types' );
	
	add_action( 'admin_init', 'fvs_add_product_taxonomy_meta' );
	
	add_action( 'woocommerce_product_option_terms', 'fvs_product_option_terms', 10, 2 );
	
	add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'fvs_variation_attribute_options_html', 200, 2 );
	