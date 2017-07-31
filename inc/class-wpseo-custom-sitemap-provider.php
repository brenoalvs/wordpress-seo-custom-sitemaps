<?php
/**
 * Custom sitemap provider.
 *
 * @package WP_SEO_Custom_Sitemaps
 * @since   0.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'WPSEO_Custom_Sitemap_Provider' ) ) {
	/**
	 * Provider for custom entries on sitemap.
	 *
	 * @since  0.1.0
	 */
	class WPSEO_Custom_Sitemap_Provider implements WPSEO_Sitemap_Provider {
		/**
		 * Check if provider supports given item type.
		 *
		 * @param string $type Type string to check for.
		 *
		 * @return boolean
		 */
		public function handles_type( $type ) {
			return 'custom' === $type;
		}

		/**
		 * Get set of sitemaps index link data.
		 *
		 * @param int $max_entries Entries per sitemap.
		 *
		 * @return array
		 */
		public function get_index_links( $max_entries ) {
			// Used to generate multiple sitemaps as pagination.
			return array(
				array(
					'loc'     => WPSEO_Sitemaps_Router::get_base_url( 'custom-sitemap.xml' ),
					'lastmod' => '@' . time(), // @ for explicit timestamp format
				),
			);
		}

		/**
		 * Get set of sitemap link data.
		 *
		 * @see https://www.sitemaps.org/protocol.html#xmlTagDefinitions Sitemaps tag definitions.
		 *
		 * @param string $type         Sitemap type.
		 * @param int    $max_entries  Entries per sitemap.
		 * @param int    $current_page Current page of the sitemap.
		 *
		 * @return array
		 */
		public function get_sitemap_links( $type, $max_entries, $current_page ) {
			// Array feed by settings.
			return array(
				array(
					'loc' => 'http://wordpress.dev/custom-link',
					'mod' => date( DATE_W3C, time() ),
					'chf' => 'daily',
					'pri' => 1,
				),
			);
		}
	}
}
