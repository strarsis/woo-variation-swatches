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
				do_action( 'flatsome_variation_swatches_loaded', $this );
			}
			
			public function constants() {
				
				$this->define( 'FVS_PLUGIN_VERSION', esc_attr( $this->_version ) );
				$this->define( 'FVS_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
				$this->define( 'FVS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
				
				$this->define( 'FVS_PLUGIN_INCLUDE_PATH', trailingslashit( plugin_dir_path( __FILE__ ) . 'includes' ) );
				$this->define( 'FVS_PLUGIN_TEMPLATES_PATH', trailingslashit( plugin_dir_path( __FILE__ ) . 'templates' ) );
				$this->define( 'FVS_PLUGIN_TEMPLATES_URI', trailingslashit( plugin_dir_url( __FILE__ ) . 'templates' ) );
				
				$this->define( 'FVS_PLUGIN_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );
				$this->define( 'FVS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
				$this->define( 'FVS_PLUGIN_FILE', __FILE__ );
				$this->define( 'FVS_IMAGES_URI', trailingslashit( plugin_dir_url( __FILE__ ) . 'images' ) );
				$this->define( 'FVS_ASSETS_URI', trailingslashit( plugin_dir_url( __FILE__ ) . 'assets' ) );
			}
			
			public function includes() {
				if ( $this->is_required_php_version() ) {
					require_once $this->include_path( 'class-fvs-settings-api.php' );
					require_once $this->include_path( 'class-fvs-term-meta.php' );
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
				
				return FVS_PLUGIN_INCLUDE_PATH . $file;
			}
			
			public function hooks() {
				add_action( 'admin_notices', array( $this, 'php_requirement_notice' ) );
				add_action( 'admin_notices', array( $this, 'wc_requirement_notice' ) );
				add_action( 'admin_notices', array( $this, 'flatsome_requirement_notice' ) );
				add_action( 'init', array( $this, 'language' ) );
				add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			}
			
			public function enqueue_scripts() {
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				wp_enqueue_script( 'flatsome-variation-swatches', flatsome_variation_swatches()->assets_uri( "/js/frontend{$suffix}.js" ), array( 'jquery' ), $this->version(), TRUE );
				wp_enqueue_style( 'flatsome-variation-swatches', flatsome_variation_swatches()->assets_uri( "/css/frontend{$suffix}.css" ), array(), $this->version() );
			}
			
			public function admin_enqueue_scripts() {
				
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				
				wp_enqueue_script( 'form-field-dependency', flatsome_variation_swatches()->assets_uri( "/js/form-field-dependency{$suffix}.js" ), array( 'jquery' ), $this->version(), TRUE );
				wp_enqueue_script( 'flatsome-variation-swatches-admin', flatsome_variation_swatches()->assets_uri( "/js/admin{$suffix}.js" ), array( 'jquery' ), $this->version(), TRUE );
				wp_enqueue_style( 'flatsome-variation-swatches-admin', flatsome_variation_swatches()->assets_uri( "/css/admin{$suffix}.css" ), array(), $this->version() );
				
				wp_enqueue_script( 'selectWoo' );
				wp_enqueue_style( 'select2' );
				
				wp_localize_script( 'flatsome-variation-swatches-admin', 'FVSPluginObject', array(
					'media_title'  => esc_html__( 'Choose an Image', 'flatsome-variation-swatches' ),
					'button_title' => esc_html__( 'Use Image', 'flatsome-variation-swatches' ),
					'add_media'    => esc_html__( 'Add Media', 'flatsome-variation-swatches' ),
					'ajaxurl'      => esc_url( admin_url( 'admin-ajax.php', 'relative' ) ),
					'nonce'        => wp_create_nonce( 'fvs_plugin_nonce' ),
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
				
				add_filter( 'fvs_settings', function ( $fields ) use ( $tab_id, $tab_title, $tab_sections, $active ) {
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
				return new FVS_Term_Meta( $taxonomy, $post_type, $fields );
			}
			
			public function plugin_row_meta( $links, $file ) {
				if ( $file == FVS_PLUGIN_BASENAME ) {
					$row_meta = array(
						'documentation' => '<a href="' . esc_url( apply_filters( 'fvs_documentation_url', 'https://docs.themehippo.com/' ) ) . '" title="' . esc_attr( esc_html__( 'View Documentation', 'ultimate-page-builder' ) ) . '">' . esc_html__( 'Documentation', 'flatsome-variation-swatches' ) . '</a>',
						'support'       => '<a href="' . esc_url( apply_filters( 'fvs_support_url', 'https://wordpress.org/support/plugin/flatsome-variation-swatches/' ) ) . '" title="' . esc_attr( esc_html__( 'Support', 'ultimate-page-builder' ) ) . '">' . esc_html__( 'Support', 'flatsome-variation-swatches' ) . '</a>',
					);
					
					return array_merge( $links, $row_meta );
				}
				
				return (array) $links;
			}
			
			public function plugin_action_links( $links ) {
				$action_links = array(//    'settings' => '<a href="' . admin_url( 'admin.php?page=upb-settings' ) . '" title="' . esc_attr__( 'View Settings', 'ultimate-page-builder' ) . '">' . esc_html__( 'Settings', 'ultimate-page-builder' ) . '</a>',
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
			
			public function dirname() {
				return FVS_PLUGIN_DIRNAME;
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
			
			public function images_uri( $file ) {
				$file = ltrim( $file, '/' );
				
				return FVS_IMAGES_URI . $file;
			}
			
			public function assets_uri( $file ) {
				$file = ltrim( $file, '/' );
				
				return FVS_ASSETS_URI . $file;
			}
			
			public function template_override_dir() {
				return apply_filters( 'fvs_override_dir', 'flatsome-variation-swatches' );
			}
			
			public function template_path() {
				return apply_filters( 'fvs_template_path', untrailingslashit( $this->plugin_path() ) . '/templates' );
			}
			
			public function template_uri() {
				return apply_filters( 'fvs_template_uri', untrailingslashit( $this->plugin_uri() ) . '/templates' );
			}
		}
		
		function flatsome_variation_swatches() {
			return Flatsome_Variation_Swatches::init();
		}
		
		add_action( 'plugins_loaded', 'flatsome_variation_swatches' );
	endif;