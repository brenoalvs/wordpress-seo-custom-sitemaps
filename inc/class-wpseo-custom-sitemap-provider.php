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
			$sitemap_links = array();
			$sitemap_page  = Proxio()->get_option( 'sitemap_page' );
			$content       = apply_filters( 'the_content', get_post_field( 'post_content', $sitemap_page ) );
			$dom           = new DOMDocument();

			libxml_use_internal_errors( true );
			$html = $dom->loadHTML( $content, LIBXML_NOERROR | LIBXML_ERR_NONE );
			libxml_clear_errors();

			if ( $html ) {
				$links = $dom->getElementsByTagName( 'a' );

				foreach ( $links as $link ) {
					$sitemap_links[] = array(
						'loc' => esc_url( $link->getAttribute( 'href' ) ),
					);
				}
			}

			return $sitemap_links;
		}
	}
}
