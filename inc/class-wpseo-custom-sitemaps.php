<?php
/**
 * WP SEO Custom Sitemaps setup.
 *
 * @package WP_SEO_Custom_Sitemaps
 * @since   0.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Main plugin class.
 *
 * @since 0.1.0
 */
final class WP_SEO_Custom_Sitemaps {
	/**
	 * The single instance of class.
	 *
	 * @var   WP_SEO_Custom_Sitemaps
	 * @since 0.1.0
	 */
	protected static $_instance = null;

	/**
	 * Main Instance.
	 *
	 * Ensures only one instance of plugin can be loaded.
	 *
	 * @since  0.1.0
	 * @static
	 * @return Main plugin instance.
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * WP SEO Custom Sitemaps constructor.
	 */
	protected function __construct() {
		add_filter( 'wpseo_sitemaps_providers', array( $this, 'load_provider' ) );
		add_action( 'wpseo_xmlsitemaps_config', array( $this, 'register_admin_fields' ) );
	}

	/**
	 * Loads custom sitemap provider.
	 *
	 * @param  array $providers List of sitemap providers.
	 * @return array            Filtered list of sitemap providers.
	 */
	public function load_provider( $providers ) {
		// Requires custom sitemaps provider file.
		require_once 'class-wpseo-custom-sitemap-provider.php';

		$providers[] = new WPSEO_Custom_Sitemap_Provider();

		return $providers;
	}

	/**
	 * Registers admin fields to save custom sitemap entries.
	 */
	public function register_admin_fields() {
		// Add settings fields.
	}
}

