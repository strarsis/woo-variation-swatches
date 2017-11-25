<?php
	/**
	 * Plugin Name: Flatsome Variation Switcher
	 * Plugin URI: https://wordpress.org/plugins/flatsome-variation-switcher/
	 * Description: Woocommerce Product Variation Switcher for flatsome
	 * Author: Emran Ahmed
	 * Version: 1.0.0
	 * Domain Path: /languages
	 * Requires at least: 4.8
	 * Tested up to: 4.9
	 * WC requires at least: 3.2.0
	 * WC tested up to: 3.2.5
	 * Text Domain: flatsome-variation-switcher
	 * Author URI: https://getwooplugins.com/
	 */
	
	defined( 'ABSPATH' ) or die( 'Keep Silent' );
	
	if ( ! class_exists( 'Flatsome_Variation_Switcher' ) ):
		
		class Flatsome_Variation_Switcher {
			
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
				do_action( 'flatsome_variation_switcher_loaded', $this );
			}
			
			public function constants() {
				define( 'FVS_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
				define( 'FVS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
				
				define( 'FVS_PLUGIN_ASSETS_URI', trailingslashit( plugin_dir_url( __FILE__ ) . 'assets' ) );
				define( 'FVS_PLUGIN_VENDOR_URI', trailingslashit( plugin_dir_url( __FILE__ ) . 'vendor' ) );
				
				define( 'FVS_PLUGIN_INCLUDE_PATH', trailingslashit( plugin_dir_path( __FILE__ ) . 'includes' ) );
				define( 'FVS_PLUGIN_TEMPLATES_PATH', trailingslashit( plugin_dir_path( __FILE__ ) . 'templates' ) );
				define( 'FVS_PLUGIN_TEMPLATES_URI', trailingslashit( plugin_dir_url( __FILE__ ) . 'templates' ) );
				
				define( 'FVS_PLUGIN_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );
				define( 'FVS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
				define( 'FVS_PLUGIN_FILE', __FILE__ );
			}
			
			public function includes() {
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
			
			public function has_required_php_version() {
				return version_compare( PHP_VERSION, '5.6.0', '>' );
			}
			
			public function php_requirement_notice() {
				if ( ! $this->has_required_php_version() ) {
					$class   = 'notice notice-error';
					$text    = esc_html__( 'Please check PHP version requirement.', 'flatsome-variation-switcher' );
					$link    = esc_url( 'https://docs.woocommerce.com/document/server-requirements/' );
					$message = wp_kses( __( "It's required to use latest version of PHP to use <strong>Flatsome Variation Switcher</strong>.", 'flatsome-variation-switcher' ), array( 'strong' => array() ) );
					
					printf( '<div class="%1$s"><p>%2$s <a target="_blank" href="%3$s">%4$s</a></p></div>', $class, $message, $link, $text );
				}
			}
			
			public function wc_requirement_notice() {
				if ( ! $this->is_wc_active() ) {
					$class = 'notice notice-error';
					
					$text    = esc_html__( 'WooCommerce', 'flatsome-variation-switcher' );
					$link    = esc_url( 'https://wordpress.org/plugins/woocommerce/' );
					$message = wp_kses( __( "<strong>Flatsome Variation Switcher</strong> is an add-on of ", 'flatsome-variation-switcher' ), array( 'strong' => array() ) );
					
					printf( '<div class="%1$s"><p>%2$s <a target="_blank" href="%3$s"><strong>%4$s</strong></a></p></div>', $class, $message, $link, $text );
				}
			}
			
			public function flatsome_requirement_notice() {
				if ( ! $this->is_flatsome_active() ) {
					$class = 'notice notice-error';
					
					$text    = esc_html__( 'Flatsome', 'flatsome-variation-switcher' );
					$link    = esc_url( 'http://flatsome3.uxthemes.com/' );
					$message = wp_kses( __( "<strong>Flatsome Variation Switcher</strong> is an add-on of ", 'flatsome-variation-switcher' ), array( 'strong' => array() ) );
					
					printf( '<div class="%1$s"><p>%2$s <a target="_blank" href="%3$s"><strong>%4$s</strong></a></p></div>', $class, $message, $link, $text );
				}
			}
			
			public function language() {
				load_plugin_textdomain( 'flatsome-variation-switcher', FALSE, trailingslashit( FVS_PLUGIN_DIRNAME ) . 'languages' );
			}
			
			public function is_wc_active() {
				return in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) );
			}
			
			public function is_flatsome_active() {
				return get_template() === 'flatsome';
			}
		}
		
		function Flatsome_Variation_Switcher() {
			return Flatsome_Variation_Switcher::init();
		}
		
		add_action( 'plugins_loaded', 'Flatsome_Variation_Switcher' );
	endif;