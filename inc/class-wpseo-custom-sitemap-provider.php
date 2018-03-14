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
			return 'general' === $type;
		}

		public function get_entries( $current_page = '', $max_entries = '' ) {
			$sitemap_links = array();
			$sitemap_page  = Proxio()->get_option( 'sitemap_page' );
			$content       = apply_filters( 'the_content', get_post_field( 'post_content', $sitemap_page ) );
			$dom           = new DOMDocument();

			// Avoid display of HTML parse warnings.
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

			$entities = array(
				'users'     => Proxio()->get_option( 'agent_archive_page' ),
				'listings'  => Proxio()->get_option( 'property_archive_page' ),
				'companies' => Proxio()->get_option( 'office_archive_page' ),
			);

			$uploads  = wp_upload_dir();
			$jsons    = glob( $uploads['basedir'] . '/json/*_en.json' );

			foreach ( $jsons as $json ) {
				$filename = substr( basename( $json ), 0, strpos( basename( $json ), '.json' ) );
				$content  = json_decode( file_get_contents( $json ) );
				$parts    = explode( '-', $filename );

				if ( isset( $parts[1] ) ) {
					$base_url = trailingslashit( get_permalink( $entities[ $parts[1] ] ) . $parts[0] );

					foreach ( $content as $location ) {
						if ( isset( $location->value ) ) {
							$sitemap_links[] = array(
								'loc' => trailingslashit( $base_url . $location->value ),
							);
						}
					}
				}
			}

			if ( ! empty( $current_page ) && ! empty( $max_entries ) ) {
				$page_links = array();

				for ( $counter = $current_page * $max_entries - $max_entries; $counter < $max_entries * $current_page; $counter++ ) {
					if ( isset( $sitemap_links[ $counter ] ) ) {
						$page_links[] = $sitemap_links[ $counter ];
					} else {
						break;
					}
				}

				$sitemap_links = $page_links;
			}

			return $sitemap_links;
		}

		/**
		 * Get set of sitemaps index link data.
		 *
		 * @param int $max_entries Entries per sitemap.
		 *
		 * @return array
		 */
		public function get_index_links( $max_entries ) {
			$index         = array();
			$entries       = $this->get_entries();
			$entries_pages = array_chunk( $entries, $max_entries );

			foreach ( $entries_pages as $key => $page ) {
				$page = 0 === $key ? '' : $key + 1;

				$index[] = array(
					'loc'     => WPSEO_Sitemaps_Router::get_base_url( 'general-sitemap' . $page . '.xml' ),
					'lastmod' => '@' . time(), // @ for explicit timestamp format
				);
			}

			return $index;
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
			return $this->get_entries( $current_page, $max_entries );
		}
	}
}
