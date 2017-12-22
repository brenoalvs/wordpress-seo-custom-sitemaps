<?php
/**
 * Proxio Team Sitemap provider.
 *
 * @package WP_SEO_Custom_Sitemaps
 * @since   0.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'Proxio_Team_Sitemap_Provider' ) ) {
	/**
	 * Provider for Teams Sitemaps.
	 *
	 * @since  0.1.0
	 */
	class Proxio_Team_Sitemap_Provider extends Proxio_Entity_Sitemap_Provider {
		protected $entity   = 'team';
		protected $endpoint = '/companies/sitemap?entityTypes=Team';
		protected $page_id  = 'team_single_page';
	}
}
