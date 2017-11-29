<?php
	/**
	 * Plugin Name: Flatsome Variation Swatches
	 * Plugin URI: https://wordpress.org/plugins/flatsome-variation-swatches/
	 * Description: Woocommerce Product Variation Switcher for flatsome theme
	 * Author: Emran Ahmed
	 * Version: 1.0.0
	 * Domain Path: /languages
	 * Requires at least: 4.8
	 * Tested up to: 4.9
	 * WC requires at least: 3.2.0
	 * WC tested up to: 3.2.5
	 * Text Domain: flatsome-variation-swatches
	 * Author URI: https://getwooplugins.com/
	 */
	
	defined( 'ABSPATH' ) or die( 'Keep Silent' );
	
	if ( ! class_exists( 'Flatsome_Variation_Swatches' ) ):
		
		class Flatsome_Variation_Swatches {
			
			protected        $_version  = '1.0.0';
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
				do_action( 'flatsome_variation_swatches_loaded', $this );
			}
			
			public function constants() {
				
				define( 'FVS_PLUGIN_VERSION', esc_attr( $this->_version ) );
				define( 'FVS_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
				define( 'FVS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
				
				define( 'FVS_PLUGIN_INCLUDE_PATH', trailingslashit( plugin_dir_path( __FILE__ ) . 'includes' ) );
				define( 'FVS_PLUGIN_TEMPLATES_PATH', trailingslashit( plugin_dir_path( __FILE__ ) . 'templates' ) );
				define( 'FVS_PLUGIN_TEMPLATES_URI', trailingslashit( plugin_dir_url( __FILE__ ) . 'templates' ) );
				
				define( 'FVS_PLUGIN_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );
				define( 'FVS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
				define( 'FVS_PLUGIN_FILE', __FILE__ );
			}
			
			public function includes() {
				if ( ! $this->is_required_php_version() ) {
					$this->include_path( 'class-fvs-settings-api.php' );
				}
			}
			
			public function include_path( $file ) {
				$file = ltrim( $file, '/' );
				
				return FVS_PLUGIN_INCLUDE_PATH . $file;
			}
			
			public function hooks() {
				add_action( 'admin_notices', array( $this, 'php_requirement_notice' ) );
				add_action( 'admin_notices', array( $this, 'wc_requirement_notice' ) );
				add_action( 'admin_notices', array( $this, 'flatsome_requirement_notice' ) );
				add_action( 'init', array( $this, 'language' ) );
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
					$text    = esc_html__( 'Please check PHP version requirement.', 'flatsome-variation-swatches' );
					$link    = esc_url( 'https://docs.woocommerce.com/document/server-requirements/' );
					$message = wp_kses( __( "It's required to use latest version of PHP to use <strong>Flatsome Variation Switcher</strong>.", 'flatsome-variation-swatches' ), array( 'strong' => array() ) );
					
					printf( '<div class="%1$s"><p>%2$s <a target="_blank" href="%3$s">%4$s</a></p></div>', $class, $message, $link, $text );
				}
			}
			
			public function wc_requirement_notice() {
				if ( ! $this->is_wc_active() ) {
					$class = 'notice notice-error';
					
					$text    = esc_html__( 'WooCommerce', 'flatsome-variation-swatches' );
					$link    = esc_url( 'https://wordpress.org/plugins/woocommerce/' );
					$message = wp_kses( __( "<strong>Flatsome Variation Switcher</strong> is an add-on of ", 'flatsome-variation-swatches' ), array( 'strong' => array() ) );
					
					printf( '<div class="%1$s"><p>%2$s <a target="_blank" href="%3$s"><strong>%4$s</strong></a></p></div>', $class, $message, $link, $text );
				}
			}
			
			public function flatsome_requirement_notice() {
				if ( ! $this->is_flatsome_active() ) {
					$class = 'notice notice-error';
					
					$text    = esc_html__( 'Flatsome', 'flatsome-variation-swatches' );
					$link    = esc_url( 'http://flatsome3.uxthemes.com/' );
					$message = wp_kses( __( "<strong>Flatsome Variation Switcher</strong> is an add-on of ", 'flatsome-variation-swatches' ), array( 'strong' => array() ) );
					
					printf( '<div class="%1$s"><p>%2$s <a target="_blank" href="%3$s"><strong>%4$s</strong></a></p></div>', $class, $message, $link, $text );
				}
			}
			
			public function language() {
				load_plugin_textdomain( 'flatsome-variation-swatches', FALSE, trailingslashit( FVS_PLUGIN_DIRNAME ) . 'languages' );
			}
			
			public function is_wc_active() {
				return in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) );
			}
			
			public function is_flatsome_active() {
				return get_template() === 'flatsome';
			}
			
			public function basename() {
				return FVS_PLUGIN_BASENAME;
			}
			
			public function version() {
				return FVS_PLUGIN_VERSION;
			}
			
			public function plugin_path() {
				return untrailingslashit( plugin_dir_path( __FILE__ ) );
			}
			
			public function plugin_uri() {
				return untrailingslashit( plugins_url( '/', __FILE__ ) );
			}
		}
		
		function flatsome_variation_swatches() {
			return Flatsome_Variation_Swatches::init();
		}
		
		add_action( 'plugins_loaded', 'flatsome_variation_swatches' );
	endif;