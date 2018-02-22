<?php
	/**
	 * Plugin Name: WooCommerce Variation Swatches
	 * Plugin URI: https://wordpress.org/plugins/woo-variation-swatches/
	 * Description: WooCommerce Product Variation Swatches
	 * Author: Emran Ahmed
	 * Version: 1.0.10
	 * Domain Path: /languages
	 * Requires at least: 4.8
	 * Tested up to: 4.9
	 * WC requires at least: 3.2.0
	 * WC tested up to: 3.2.6
	 * Text Domain: woo-variation-swatches
	 * Author URI: https://getwooplugins.com/
	 */
	
	defined( 'ABSPATH' ) or die( 'Keep Silent' );
	
	if ( ! class_exists( 'Woo_Variation_Swatches' ) ):
		
		final class Woo_Variation_Swatches {
			
			protected $_version = '1.0.10';
			
			protected static $_instance = NULL;
			private          $_settings_api;
			
			public static function instance() {
				if ( is_null( self::$_instance ) ) {
					self::$_instance = new self();
				}
				
				return self::$_instance;
			}
			
			public function __construct() {
				$this->constants();
				$this->includes();
				$this->hooks();
				do_action( 'woo_variation_swatches_loaded', $this );
			}
			
			public function constants() {
				$this->define( 'WVS_PLUGIN_VERSION', esc_attr( $this->_version ) );
				$this->define( 'WVS_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
				$this->define( 'WVS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
				
				$this->define( 'WVS_PLUGIN_INCLUDE_PATH', trailingslashit( plugin_dir_path( __FILE__ ) . 'includes' ) );
				$this->define( 'WVS_PLUGIN_TEMPLATES_PATH', trailingslashit( plugin_dir_path( __FILE__ ) . 'templates' ) );
				$this->define( 'WVS_PLUGIN_TEMPLATES_URI', trailingslashit( plugin_dir_url( __FILE__ ) . 'templates' ) );
				
				$this->define( 'WVS_PLUGIN_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );
				$this->define( 'WVS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
				$this->define( 'WVS_PLUGIN_FILE', __FILE__ );
				$this->define( 'WVS_IMAGES_URI', trailingslashit( plugin_dir_url( __FILE__ ) . 'images' ) );
				$this->define( 'WVS_ASSETS_URI', trailingslashit( plugin_dir_url( __FILE__ ) . 'assets' ) );
			}
			
			public function includes() {
				if ( $this->is_required_php_version() ) {
					require_once $this->include_path( 'class-wvs-settings-api.php' );
					require_once $this->include_path( 'class-wvs-term-meta.php' );
					require_once $this->include_path( 'functions.php' );
					require_once $this->include_path( 'hooks.php' );
				}
			}
			
			public function define( $name, $value, $case_insensitive = FALSE ) {
				if ( ! defined( $name ) ) {
					define( $name, $value, $case_insensitive );
				}
			}
			
			public function include_path( $file ) {
				$file = ltrim( $file, '/' );
				
				return WVS_PLUGIN_INCLUDE_PATH . $file;
			}
			
			public function hooks() {
				add_action( 'init', array( $this, 'language' ) );
				add_action( 'admin_notices', array( $this, 'php_requirement_notice' ) );
				add_action( 'admin_notices', array( $this, 'wc_requirement_notice' ) );
				add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
				
				if ( $this->is_required_php_version() ) {
					add_action( 'init', array( $this, 'settings_api' ), 5 );
					add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
					add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
					add_filter( 'body_class', array( $this, 'body_class' ) );
				}
			}
			
			public function body_class( $classes ) {
				array_push( $classes, 'woo-variation-swatches' );
				if ( wp_is_mobile() ) {
					array_push( $classes, 'woo-variation-swatches-on-mobile' );
				}
				array_push( $classes, sprintf( 'woo-variation-swatches-style-%s', $this->get_option( 'style' ) ) );
				array_push( $classes, sprintf( 'woo-variation-swatches-tooltip-%s', $this->get_option( 'tooltip' ) ? 'enabled' : 'disabled' ) );
				array_push( $classes, sprintf( 'woo-variation-swatches-stylesheet-%s', $this->get_option( 'stylesheet' ) ? 'enabled' : 'disabled' ) );
				
				return $classes;
			}
			
			public function enqueue_scripts() {
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				wp_enqueue_script( 'woo-variation-swatches', $this->assets_uri( "/js/frontend{$suffix}.js" ), array( 'jquery' ), $this->version(), TRUE );
				
				if ( $this->get_option( 'stylesheet' ) ) {
					wp_enqueue_style( 'woo-variation-swatches', $this->assets_uri( "/css/frontend{$suffix}.css" ), array(), $this->version() );
				}
				
				if ( $this->get_option( 'tooltip' ) ) {
					wp_enqueue_style( 'woo-variation-swatches-tooltip', $this->assets_uri( "/css/frontend-tooltip{$suffix}.css" ), array(), $this->version() );
				}
			}
			
			public function admin_enqueue_scripts() {
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				wp_enqueue_script( 'form-field-dependency', $this->assets_uri( "/js/form-field-dependency{$suffix}.js" ), array( 'jquery' ), $this->version(), TRUE );
				wp_enqueue_script( 'woo-variation-swatches-admin', $this->assets_uri( "/js/admin{$suffix}.js" ), array( 'jquery' ), $this->version(), TRUE );
				wp_enqueue_style( 'woo-variation-swatches-admin', $this->assets_uri( "/css/admin{$suffix}.css" ), array(), $this->version() );
				
				// wp_enqueue_script( 'selectWoo' );
				// wp_enqueue_style( 'select2' );
				
				wp_localize_script( 'woo-variation-swatches-admin', 'WVSPluginObject', array(
					'media_title'  => esc_html__( 'Choose an Image', 'woo-variation-swatches' ),
					'button_title' => esc_html__( 'Use Image', 'woo-variation-swatches' ),
					'add_media'    => esc_html__( 'Add Media', 'woo-variation-swatches' ),
					'ajaxurl'      => esc_url( admin_url( 'admin-ajax.php', 'relative' ) ),
					'nonce'        => wp_create_nonce( 'wvs_plugin_nonce' ),
				) );
			}
			
			public function settings_api() {
				$this->_settings_api = new WVS_Settings_API( $this );
				
				return $this->_settings_api;
			}
			
			public function add_setting( $tab_id, $tab_title, $tab_sections, $active = FALSE ) {
				// Example:
				
				// fn(tab_id, tab_title, [
				//    [
				//     'id'=>'',
				//     'title'=>'',
				//     'desc'=>'',
				//     'fields'=>[
				//        [
				//         'id'=>'',
				//         'type'=>'',
				//         'title'=>'',
				//         'desc'=>'',
				//         'value'=>''
				//      ]
				//    ] // fields end
				//  ]
				//], active ? true | false)
				
				add_filter( 'wvs_settings', function ( $fields ) use ( $tab_id, $tab_title, $tab_sections, $active ) {
					array_push( $fields, array(
						'id'       => $tab_id,
						'title'    => esc_html( $tab_title ),
						'active'   => $active,
						'sections' => $tab_sections
					) );
					
					return $fields;
				} );
			}
			
			public function get_option( $id ) {
				return $this->_settings_api->get_option( $id );
			}
			
			public function add_term_meta( $taxonomy, $post_type, $fields ) {
				return new WVS_Term_Meta( $taxonomy, $post_type, $fields );
			}
			
			public function plugin_row_meta( $links, $file ) {
				if ( $file == $this->basename() ) {
					$review_url                  = "https://wordpress.org/support/plugin/woo-variation-swatches/reviews/?rate=5#new-post";
					$row_meta[ 'documentation' ] = '<a href="' . esc_url( apply_filters( 'wvs_documentation_url', '#' ) ) . '" title="' . esc_attr( esc_html__( 'View Documentation', 'woo-variation-swatches' ) ) . '">' . esc_html__( 'Documentation', 'woo-variation-swatches' ) . '</a>';
					$row_meta[ 'support' ]       = '<a href="' . esc_url( apply_filters( 'wvs_support_url', 'https://wordpress.org/support/plugin/woo-variation-swatches/' ) ) . '" title="' . esc_attr( esc_html__( 'Support', 'woo-variation-swatches' ) ) . '">' . esc_html__( 'Support', 'woo-variation-swatches' ) . '</a>';
					$row_meta[ 'rating' ]        = sprintf( '<a target="_blank" href="%1$s">%3$s</a> <span class="gwp-rate-stars"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><a xlink:href="%1$s" title="%2$s" target="_blank"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></a></svg><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><a xlink:href="%1$s" title="%2$s" target="_blank"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></a></svg><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><a xlink:href="%1$s" title="%2$s" target="_blank"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></a></svg><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><a xlink:href="%1$s" title="%2$s" target="_blank"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></a></svg><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><a xlink:href="%1$s" title="%2$s" target="_blank"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></a></svg></span>', esc_url( $review_url ), esc_html__( 'Review', 'woo-variation-swatches' ), esc_html__( 'Please Rate Us', 'woo-variation-swatches' ) );
					
					return array_merge( $links, $row_meta );
				}
				
				return (array) $links;
			}
			
			public function plugin_action_links( $links ) {
				$action_links = array(//    'settings' => '<a href="' . admin_url( 'admin.php?page=upb-settings' ) . '" title="' . esc_attr__( 'View Settings', 'woo-variation-swatches' ) . '">' . esc_html__( 'Settings', 'woo-variation-swatches' ) . '</a>',
				);
				
				return array_merge( $action_links, $links );
			}
			
			public function get_theme_name() {
				return wp_get_theme()->get( 'Name' );
			}
			
			public function get_theme_version() {
				return wp_get_theme()->get( 'Version' );
			}
			
			public function is_required_php_version() {
				return version_compare( PHP_VERSION, '5.6.0', '>=' );
			}
			
			public function php_requirement_notice() {
				if ( ! $this->is_required_php_version() ) {
					$class   = 'notice notice-error';
					$text    = esc_html__( 'Please check PHP version requirement.', 'woo-variation-swatches' );
					$link    = esc_url( 'https://docs.woocommerce.com/document/server-requirements/' );
					$message = wp_kses( __( "It's required to use latest version of PHP to use <strong>WooCommerce Variation Swatches</strong>.", 'woo-variation-swatches' ), array( 'strong' => array() ) );
					
					printf( '<div class="%1$s"><p>%2$s <a target="_blank" href="%3$s">%4$s</a></p></div>', $class, $message, $link, $text );
				}
			}
			
			public function wc_requirement_notice() {
				if ( ! $this->is_wc_active() ) {
					$class = 'notice notice-error';
					
					$text    = esc_html__( 'WooCommerce', 'woo-variation-swatches' );
					$link    = esc_url( 'https://wordpress.org/plugins/woocommerce/' );
					$message = wp_kses( __( "<strong>WooCommerce Variation Swatches</strong> is an add-on of ", 'woo-variation-swatches' ), array( 'strong' => array() ) );
					
					printf( '<div class="%1$s"><p>%2$s <a target="_blank" href="%3$s"><strong>%4$s</strong></a></p></div>', $class, $message, $link, $text );
				}
			}
			
			public function language() {
				load_plugin_textdomain( 'woo-variation-swatches', FALSE, trailingslashit( WVS_PLUGIN_DIRNAME ) . 'languages' );
			}
			
			public function is_wc_active() {
				return in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) );
			}
			
			public function basename() {
				return WVS_PLUGIN_BASENAME;
			}
			
			public function dirname() {
				return WVS_PLUGIN_DIRNAME;
			}
			
			public function version() {
				return WVS_PLUGIN_VERSION;
			}
			
			public function plugin_path() {
				return untrailingslashit( plugin_dir_path( __FILE__ ) );
			}
			
			public function plugin_uri() {
				return untrailingslashit( plugins_url( '/', __FILE__ ) );
			}
			
			public function images_uri( $file ) {
				$file = ltrim( $file, '/' );
				
				return WVS_IMAGES_URI . $file;
			}
			
			public function assets_uri( $file ) {
				$file = ltrim( $file, '/' );
				
				return WVS_ASSETS_URI . $file;
			}
			
			public function template_override_dir() {
				return apply_filters( 'wvs_override_dir', 'woo-variation-swatches' );
			}
			
			public function template_path() {
				return apply_filters( 'wvs_template_path', untrailingslashit( $this->plugin_path() ) . '/templates' );
			}
			
			public function template_uri() {
				return apply_filters( 'wvs_template_uri', untrailingslashit( $this->plugin_uri() ) . '/templates' );
			}
			
			public function locate_template( $template_name, $third_party_path = FALSE ) {
				
				$template_name = ltrim( $template_name, '/' );
				$template_path = $this->template_override_dir();
				$default_path  = $this->template_path();
				
				if ( $third_party_path && is_string( $third_party_path ) ) {
					$default_path = untrailingslashit( $third_party_path );
				}
				
				// Look within passed path within the theme - this is priority.
				$template = locate_template( array(
					                             trailingslashit( $template_path ) . trim( $template_name ),
					                             'wvs-template-' . trim( $template_name )
				                             ) );
				
				// Get default template/
				if ( empty( $template ) ) {
					$template = trailingslashit( $default_path ) . trim( $template_name );
				}
				
				// Return what we found.
				return apply_filters( 'wvs_locate_template', $template, $template_name, $template_path );
			}
			
			public function get_template( $template_name, $template_args = array(), $third_party_path = FALSE ) {
				
				$template_name = ltrim( $template_name, '/' );
				
				$located = apply_filters( 'wvs_get_template', $this->locate_template( $template_name, $third_party_path ) );
				
				do_action( 'wvs_before_get_template', $template_name, $template_args );
				
				extract( $template_args );
				
				if ( file_exists( $located ) ) {
					include $located;
				} else {
					trigger_error( sprintf( esc_html__( 'WooCommerce Variation Swatches Plugin try to load "%s" but template "%s" was not found.', 'woo-variation-swatches' ), $located, $template_name ), E_USER_WARNING );
				}
				
				do_action( 'wvs_after_get_template', $template_name, $template_args );
			}
			
			public function get_theme_file_path( $file, $third_party_path = FALSE ) {
				
				$file         = ltrim( $file, '/' );
				$template_dir = $this->template_override_dir();
				$default_path = $this->template_path();
				
				if ( $third_party_path && is_string( $third_party_path ) ) {
					$default_path = untrailingslashit( $third_party_path );
				}
				
				// @TODO: Use get_theme_file_path
				if ( file_exists( get_stylesheet_directory() . '/' . $template_dir . '/' . $file ) ) {
					$path = get_stylesheet_directory() . '/' . $template_dir . '/' . $file;
				} elseif ( file_exists( get_template_directory() . '/' . $template_dir . '/' . $file ) ) {
					$path = get_template_directory() . '/' . $template_dir . '/' . $file;
				} else {
					$path = trailingslashit( $default_path ) . $file;
				}
				
				return apply_filters( 'wvs_get_theme_file_path', $path, $file );
			}
			
			public function get_theme_file_uri( $file, $third_party_uri = FALSE ) {
				
				$file         = ltrim( $file, '/' );
				$template_dir = $this->template_override_dir();
				$default_uri  = $this->template_uri();
				
				if ( $third_party_uri && is_string( $third_party_uri ) ) {
					$default_uri = untrailingslashit( $third_party_uri );
				}
				
				// @TODO: Use get_theme_file_uri
				if ( file_exists( get_stylesheet_directory() . '/' . $template_dir . '/' . $file ) ) {
					$uri = get_stylesheet_directory_uri() . '/' . $template_dir . '/' . $file;
				} elseif ( file_exists( get_template_directory() . '/' . $template_dir . '/' . $file ) ) {
					$uri = get_template_directory_uri() . '/' . $template_dir . '/' . $file;
				} else {
					$uri = trailingslashit( $default_uri ) . $file;
				}
				
				return apply_filters( 'wvs_get_theme_file_uri', $uri, $file );
			}
		}
		
		function woo_variation_swatches() {
			return Woo_Variation_Swatches::instance();
		}
		
		add_action( 'plugins_loaded', 'woo_variation_swatches' );
	endif;