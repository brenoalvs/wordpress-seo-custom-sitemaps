<?php
/**
 * Proxio Office Sitemap provider.
 *
 * @package WP_SEO_Custom_Sitemaps
 * @since   0.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'Proxio_Office_Sitemap_Provider' ) ) {
	/**
	 * Provider for Offices Sitemaps.
	 *
	 * @since  0.1.0
	 */
	class Proxio_Office_Sitemap_Provider extends Proxio_Entity_Sitemap_Provider {
		protected $entity   = 'offices';
		protected $endpoint = '/companies/sitemap?entityTypes=Office';
		protected $page_id  = 'office_single_page';
	}
}
