<?php
/**
 * Plugin Name: Sale Splash
 * Plugin URI: http://muskonur.com/
 * Description: A campaign, discount, and marketing plugin for WordPress sites using WooCommerce.
 * Version: 1.0
 * Author: Mustafa Konur
 * Author URI: http://muskonur.com/
 * Text Domain: sale-splash
 * Domain Path: /languages
 */

// Prevent direct access to the file.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Enqueue styles for the plugin.
function sale_splash_enqueue_styles() {
    wp_enqueue_style( 'sale-splash-style', plugin_dir_url( __FILE__ ) . 'assets/style.css' );
}
add_action( 'wp_enqueue_scripts', 'sale_splash_enqueue_styles' );

// Define plugin path and URL constants.
define( 'SALE_SPLASH_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'SALE_SPLASH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include required files for the plugin.
require_once( SALE_SPLASH_PLUGIN_PATH . 'includes/admin/admin-menu.php' );
require_once( SALE_SPLASH_PLUGIN_PATH . 'includes/woocommerce.php' );

// Register activation and deactivation hooks for the plugin.
register_activation_hook( __FILE__, 'sale_splash_activate' );
register_deactivation_hook( __FILE__, 'sale_splash_deactivate' );

// Function to run on plugin activation.
function sale_splash_activate() {
    // Code to execute on activation goes here.
}

// Function to run on plugin deactivation.
function sale_splash_deactivate() {
    // Code to execute on deactivation goes here.
}
