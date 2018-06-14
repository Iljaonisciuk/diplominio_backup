<?php
/**
 * Plugin Name: Product Categories Designs for WooCommerce 
 * Plugin URI: http://www.wponlinesupport.com/
 * Description: Display WooCommerce product categories designs with grid and silder view.
 * Author: WP Online Support 
 * Text Domain: product-categories-designs-for-woocommerce
 * Domain Path: /languages/
 * Version: 1.1.1
 * Author URI: http://www.wponlinesupport.com/
 *
 * @package WordPress
 * @author SP Technolab
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if( !defined( 'PCDFWOO_VERSION' ) ) {
	define( 'PCDFWOO_VERSION', '1.1.1' ); // Version of plugin
}
if( !defined( 'PCDFWOO_VERSION_DIR' ) ) {
    define( 'PCDFWOO_VERSION_DIR', dirname( __FILE__ ) ); // Plugin dir
}
if( !defined( 'PCDFWOO_VERSION_URL' ) ) {
    define( 'PCDFWOO_VERSION_URL', plugin_dir_url( __FILE__ ) ); // Plugin url
}
if( !defined( 'PCDFWOO_PRODUCT_POST_TYPE' ) ) {
    define( 'PCDFWOO_PRODUCT_POST_TYPE', 'product' ); // Plugin category name
}


/**
 * Check WooCommerce is active
 *
 * @package Product Categories Designs for WooCommerce
 * @since 1.0
 */
function pcdfwoo_check_activation() {

	if ( !class_exists('WooCommerce') ) {
		// is this plugin active?
		if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
			// deactivate the plugin
	 		deactivate_plugins( plugin_basename( __FILE__ ) );
	 		// unset activation notice
	 		unset( $_GET[ 'activate' ] );
	 		// display notice
	 		add_action( 'admin_notices', 'pcdfwoo_admin_notices' );
		}
	}
}

// Check required plugin is activated or not
add_action( 'admin_init', 'pcdfwoo_check_activation' );

/**
 * Admin notices
 * 
 * @package Product Categories Designs for WooCommerce
 * @since 1.0
 */
function pcdfwoo_admin_notices() {
	
	if ( !class_exists('WooCommerce') ) {
		echo '<div class="error notice is-dismissible">';
		echo sprintf( __('<p><strong>%s</strong> recommends the following plugin to use.</p>', 'product-categories-designs-for-woocommerce'), 'Woo Product Slider and Carousel with Category' );
		echo sprintf( __('<p><strong><a href="%s" target="_blank">%s</a> </strong></p>', 'product-categories-designs-for-woocommerce'), 'https://wordpress.org/plugins/woocommerce/', 'WooCommerce' );
		echo '</div>';
	}
}

/**
 * Load the plugin after the main plugin is loaded.
 * 
 * @package Woo Product Slider and Carousel with category
 * @since 1.0.0
 */
function pcdfwoo_load_plugin() {

	// Check main plugin is active or not
	if( class_exists('WooCommerce') ) {

		/**
		 * Load Text Domain
		 * This gets the plugin ready for translation
		 * 
		 * @package Woo Product Slider and Carousel with category
		 * @since 1.0.0
		 */
		function pcdfwoo_load_textdomain() {
			load_plugin_textdomain( 'product-categories-designs-for-woocommerce', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
		}

		// Action to load plugin text domain
		add_action('plugins_loaded', 'pcdfwoo_load_textdomain');

		/**
		 * Function add some script and style
		 * 
		 * @package Woo Product Slider and Carousel with category
		 * @since 1.2.5
		 */
		function pcdfwoo_style_css() {
			
			// Slick CSS
			wp_enqueue_style( 'pcdfwoo_style',  plugin_dir_url( __FILE__ ) . 'assets/css/slick.css', array(), PCDFWOO_VERSION);

			// Registring slick slider script
			if( !wp_script_is( 'wpos-slick-jquery', 'registered' ) ) {
				wp_register_script( 'wpos-slick-jquery', PCDFWOO_VERSION_URL.'assets/js/slick.min.js', array('jquery'), PCDFWOO_VERSION, true );				
			}

			// Public script
			wp_register_script( 'pcdfwoo-public-jquery', PCDFWOO_VERSION_URL.'assets/js/public.js', array('jquery'), PCDFWOO_VERSION, true );
			wp_enqueue_script( 'pcdfwoo-public-jquery' );
		}

		// Action to add some style and script
		add_action( 'wp_enqueue_scripts', 'pcdfwoo_style_css' );

		// Including some files
		require_once( 'includes/class-shortcode.php' );	
		require_once( 'includes/class-slider-shortcode.php' );	
	}
}

// Action to load plugin after the main plugin is loaded
add_action('plugins_loaded', 'pcdfwoo_load_plugin', 15);

/**
 * Function to unique number value
 * 
 * @package Woo Product Slider and Carousel with category
 * @since 1.2.5
 */
function pcdfwoo_get_unique() {
    static $unique = 0;
    $unique++;

    return $unique;
}

/**
 * Function to get featured content column
 * 
 * @package WP Featured Content and Slider Pro
 * @since 1.0.0
 */
function pcdfwoo_column( $row = '' ) {
	if($row == 2) {
		$per_row = 6;
	} else if($row == 3) {
		$per_row = 4;	
	} else if($row == 4) {
		$per_row = 3;
	} else if($row == 1) {
		$per_row = 12;
	} else{
        $per_row = 12;
    }

    return $per_row;
}

// How it work file, Load admin files
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
    require_once( PCDFWOO_VERSION_DIR . '/includes/admin/pcdfwoo-how-it-work.php' );
}