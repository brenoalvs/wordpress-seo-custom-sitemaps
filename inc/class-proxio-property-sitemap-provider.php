<?php
/**
 * Proxio Property Sitemap provider.
 *
 * @package WP_SEO_Custom_Sitemaps
 * @since   0.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'Proxio_Property_Sitemap_Provider' ) ) {
	/**
	 * Provider for Properties Sitemaps.
	 *
	 * @since  0.1.0
	 */
	class Proxio_Property_Sitemap_Provider extends Proxio_Entity_Sitemap_Provider {
		protected $entity   = 'property';
		protected $endpoint = '/listings/sitemap';
		protected $page_id  = 'property_single_page';
	}
}
