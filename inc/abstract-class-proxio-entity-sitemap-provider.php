<?php
/**
 * Proxio Entity Sitemap provider.
 *
 * @package WP_SEO_Custom_Sitemaps
 * @since   0.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'Proxio_Entity_Sitemap_Provider' ) ) {

	/**
	 * Sitemap Provider for Proxio Entities.
	 *
	 * Downloads a CSV dictionary file daily from Proxio API for a given
	 * entity and generate sitemap entries for each slug within this dictionary.
	 *
	 * @since  0.1.0
	 */
	abstract class Proxio_Entity_Sitemap_Provider implements WPSEO_Sitemap_Provider {
		/**
		 * Sitemap Entity.
		 *
		 * @var string
		 */
		protected $entity;

		/**
		 * Sitemap Endpoint.
		 *
		 * Must starts with "/" e.g.: /listings/sitemap
		 *
		 * @var string
		 */
		protected $endpoint;

		/**
		 * Base page ID to generate sitemap links.
		 *
		 * @var string
		 */
		protected $page_id;

		/**
		 * Initialization.
		 *
		 * Attachs download_entity_file hook to prepare all data to be manipuled by sitemap provider.
		 */
		public function __construct() {
			add_action( 'proxio_sitemaps_update', array( $this, 'download_entity_file' ) );
		}

		/**
		 * Downloads slugs dictionary file from API.
		 *
		 * @return void
		 */
		public function download_entity_file() {
			$uploads_dir  = wp_upload_dir();
			$filepath     = trailingslashit( $uploads_dir['basedir'] ) . $this->entity . '.csv';

			$api_url      = Proxio()->get_option( 'api_url' );
			$api_key      = Proxio()->get_option( 'api_key' );

			$download_url = add_query_arg( 'api_key', $api_key, untrailingslashit( $api_url ) . $this->endpoint );
			$download     = download_url( $download_url, 300 );

			if ( ! is_wp_error( $download ) ) {
				copy( $download, $filepath );
				@unlink( $download );
			}
		}

		/**
		 * Get entries from sitemap file.
		 *
		 * Open downloaded csv file and read line by line avoiding memory leak issues.
		 *
		 * @param  int $current_page Current page of the sitemap.
		 * @param  int $max_entries  Entries per sitemap.
		 * @return array             List of entries.
		 */
		private function get_entries( $current_page = 1, $max_entries ) {
			$sitemap_links = array();
			$uploads_dir   = wp_upload_dir();
			$filepath      = trailingslashit( $uploads_dir['basedir'] ) . $this->entity . '.csv';

			if ( file_exists( $filepath ) ) {
				$handle    = fopen( $filepath, 'r' );
				$skip      = $max_entries * $current_page - $max_entries;
				$take      = $max_entries;
				$skipcount = 0;
				$linecount = 0;

				if ( 0 < $skip ) {
					while ( ! feof( $handle ) && $skipcount < $skip ) {
						$line = fgets( $handle );
						$skipcount++;
					}
				}

				while ( ! feof( $handle ) ) {
					if ( $linecount >= $take ) {
						break;
					}

					$line     = fgets( $handle );
					$slug     = rtrim( $line );
					$base_url = trailingslashit( get_permalink( get_proxio_page_id( $this->page_id ) ) );

					$sitemap_links[] = array(
						'loc' => $base_url . $slug,
					);

					$linecount++;
				}

				fclose( $handle );
			}

			return $sitemap_links;
		}

		private function get_entries_count() {
			$count       = 0;
			$uploads_dir = wp_upload_dir();
			$filepath    = trailingslashit( $uploads_dir['basedir'] ) . $this->entity . '.csv';

			$handle = fopen( $filepath, 'r' );

			while ( ! feof( $handle ) ) {
				$line = fgets( $handle );
				$count++;
			}

			fclose( $handle );

			return $count;
		}

		/**
		 * Check if provider supports given item type.
		 *
		 * @param string $type Type string to check for.
		 *
		 * @return boolean
		 */
		public function handles_type( $type ) {
			return $this->entity === $type;
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
			$entries       = $this->get_entries_count();
			$entries_pages = round( $entries / $max_entries );

			for ( $page = 0; $page < $entries_pages; $page++ ) {
				$key = 0 === $page ? '' : $page + 1;

				$index[] = array(
					'loc'     => WPSEO_Sitemaps_Router::get_base_url( $this->entity . '-sitemap' . $key . '.xml' ),
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
