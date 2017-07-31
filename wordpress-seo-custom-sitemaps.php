<?php
/**
 * Plugin Name:     Custom Sitemaps for Yoast SEO
 * Plugin URI:      https://wordpress.org/plugins/wordpress-seo-custom-sitemaps
 * Description:     Custom sitemaps extension for Yoast SEO plugin
 * Author:          Breno Alves
 * Author URI:      https://github.com/brenoalvs
 * Text Domain:     wp-seo-custom-sitemaps
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         WP_SEO_Custom_Sitemaps
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

// Define WP_SEO_CUSTOM_SITEMAPS.
if ( ! defined( 'WP_SEO_CUSTOM_SITEMAPS' ) ) {
	define( 'WP_SEO_CUSTOM_SITEMAPS', __FILE__ );
}

// Includes the main plugin class.
if ( ! class_exists( 'WP_SEO_Custom_Sitemaps' ) ) {
	include_once dirname( __FILE__ ) . '/inc/class-wpseo-custom-sitemaps.php';
}

/**
 * Main instance of custom sitemaps extension.
 *
 * Return the main instance of plugin to prevent use of globals.
 *
 * @return WP_SEO_Custom_Sitemaps
 */
function wp_seo_custom_sitemaps() {
	return WP_SEO_Custom_Sitemaps::get_instance();
}

$GLOBALS['wp_seo_custom_sitemaps'] = wp_seo_custom_sitemaps();
