<?php
	/**
	 * Plugin Name: Woo Variation Swatches
	 * Plugin URI: https://wordpress.org/plugins/woo-variation-swatches/
	 * Description: WooCommerce Product Variation Swatches
	 * Author: Emran Ahmed
	 * Version: 1.0.0
	 * Domain Path: /languages
	 * Requires at least: 4.8
	 * Tested up to: 4.9
	 * WC requires at least: 3.2.0
	 * WC tested up to: 3.2.5
	 * Text Domain: woo-variation-swatches
	 * Author URI: https://getwooplugins.com/
	 */
	
	defined( 'ABSPATH' ) or die( 'Keep Silent' );
	
	if ( ! class_exists( 'Woo_Variation_Swatches' ) ):
		
		class Woo_Variation_Swatches {
			
			protected $_version = '1.0.0';
			
			protected static $_instance = NULL;
			
			public static function init() {
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
					require_once $this->include_path( 'template-functions.php' );
					require_once $this->include_path( 'functions.php' );
					require_once $this->include_path( 'hooks.php' );
					require_once $this->include_path( 'settings.php' );
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
				add_action( 'admin_notices', array( $this, 'php_requirement_notice' ) );
				add_action( 'admin_notices', array( $this, 'wc_requirement_notice' ) );
				add_action( 'init', array( $this, 'language' ) );
				add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			}
			
			public function enqueue_scripts() {
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				wp_enqueue_script( 'woo-variation-swatches', $this->assets_uri( "/js/frontend{$suffix}.js" ), array( 'jquery' ), $this->version(), TRUE );
				wp_enqueue_style( 'woo-variation-swatches', $this->assets_uri( "/css/frontend{$suffix}.css" ), array(), $this->version() );
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
				//])
				
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
			
			public function add_term_meta( $taxonomy, $post_type, $fields ) {
				return new WVS_Term_Meta( $taxonomy, $post_type, $fields );
			}
			
			public function plugin_row_meta( $links, $file ) {
				if ( $file == $this->basename() ) {
					$row_meta = array(
						'documentation' => '<a href="' . esc_url( apply_filters( 'wvs_documentation_url', '#' ) ) . '" title="' . esc_attr( esc_html__( 'View Documentation', 'woo-variation-swatches' ) ) . '">' . esc_html__( 'Documentation', 'woo-variation-swatches' ) . '</a>',
						'support'       => '<a href="' . esc_url( apply_filters( 'wvs_support_url', 'https://wordpress.org/support/plugin/woo-variation-swatches/' ) ) . '" title="' . esc_attr( esc_html__( 'Support', 'woo-variation-swatches' ) ) . '">' . esc_html__( 'Support', 'woo-variation-swatches' ) . '</a>',
					);
					
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
					$message = wp_kses( __( "It's required to use latest version of PHP to use <strong>Woo Variation Swatches</strong>.", 'woo-variation-swatches' ), array( 'strong' => array() ) );
					
					printf( '<div class="%1$s"><p>%2$s <a target="_blank" href="%3$s">%4$s</a></p></div>', $class, $message, $link, $text );
				}
			}
			
			public function wc_requirement_notice() {
				if ( ! $this->is_wc_active() ) {
					$class = 'notice notice-error';
					
					$text    = esc_html__( 'WooCommerce', 'woo-variation-swatches' );
					$link    = esc_url( 'https://wordpress.org/plugins/woocommerce/' );
					$message = wp_kses( __( "<strong>Woo Variation Switcher</strong> is an add-on of ", 'woo-variation-swatches' ), array( 'strong' => array() ) );
					
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
		}
		
		function woo_variation_swatches() {
			return Woo_Variation_Swatches::init();
		}
		
		add_action( 'plugins_loaded', 'woo_variation_swatches' );
	endif;