<?php
/**
 * Proxio Agent Sitemap provider.
 *
 * @package WP_SEO_Custom_Sitemaps
 * @since   0.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'Proxio_Agent_Sitemap_Provider' ) ) {
	/**
	 * Provider for Agents Sitemaps.
	 *
	 * @since  0.1.0
	 */
	class Proxio_Agent_Sitemap_Provider extends Proxio_Entity_Sitemap_Provider {
		protected $entity   = 'agent';
		protected $endpoint = '/users/sitemap?entityTypes=Agent';
		protected $page_id  = 'agent_single_page';
	}
}
