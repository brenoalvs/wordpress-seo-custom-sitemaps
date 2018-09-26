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
		register_activation_hook( WP_SEO_CUSTOM_SITEMAPS, array( $this, 'activation' ) );
		register_deactivation_hook( WP_SEO_CUSTOM_SITEMAPS, array( $this, 'deactivation' ) );

		add_filter( 'wpseo_sitemaps_providers', array( $this, 'load_provider' ) );
		add_action( 'plugins_loaded', array( $this, 'custom_sitemap_xls' ), 20 );

		add_filter( 'wpseo_typecount_join', array( $this, 'remove_translations_from_sitemap' ) );
		add_filter( 'wpseo_posts_join', array( $this, 'remove_translations_from_sitemap' ) );
	}

	public function activation() {
		if ( ! wp_next_scheduled( 'proxio_sitemaps_update' ) ) {
			wp_schedule_event( time(), 'daily', 'proxio_sitemaps_update' );
		}
	}

	public function deactivation() {
		wp_clear_scheduled_hook( 'proxio_sitemaps_update' );
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

		// Abstract class for entities sitemaps.
		require_once 'abstract-class-proxio-entity-sitemap-provider.php';

		// Entities sitemaps.
		require_once 'class-proxio-property-sitemap-provider.php';
		require_once 'class-proxio-agent-sitemap-provider.php';
		require_once 'class-proxio-office-sitemap-provider.php';
		require_once 'class-proxio-team-sitemap-provider.php';

		$providers[] = new WPSEO_Custom_Sitemap_Provider();
		$providers[] = new Proxio_Property_Sitemap_Provider();
		$providers[] = new Proxio_Agent_Sitemap_Provider();
		$providers[] = new Proxio_Office_Sitemap_Provider();
		$providers[] = new Proxio_Team_Sitemap_Provider();

		return $providers;
	}

	/**
	 * Registers admin fields to save custom sitemap entries.
	 */
	public function custom_sitemap_xls() {
		global $wpseo_sitemaps;

		if ( is_a( $wpseo_sitemaps, 'WPSEO_Sitemaps' ) ) {
			$stylesheet_url = plugin_dir_url( WP_SEO_CUSTOM_SITEMAPS ) . 'inc/main-sitemap.xsl';
			$wpseo_sitemaps->renderer->set_stylesheet( '<?xml-stylesheet type="text/xsl" href="' . esc_url( $stylesheet_url ) . '"?>' );
		}
	}

	/**
	 * Removes translations posts from queries for sitemaps.
	 *
	 * Used to generate sitemaps only with default language.
	 *
	 * @param  string $join JOIN clause for fetch all posts, empty by default.
	 * @return string       JOIN clause for fetch only default language posts.
	 */
	public function remove_translations_from_sitemap( $join ) {
		global $wpdb;

		$language = proxio_get_language_code();

		return "INNER JOIN {$wpdb->prefix}icl_translations
			ON wp_posts.ID = {$wpdb->prefix}icl_translations.element_id
			AND {$wpdb->prefix}icl_translations.language_code = '{$language}'
		";
	}
}

