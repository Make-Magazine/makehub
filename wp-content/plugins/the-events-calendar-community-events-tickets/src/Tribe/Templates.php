<?php
/**
 * Templating functionality for Tribe Events Calendar
 */

// don't load directly
if ( ! defined('ABSPATH') ) {
	die('-1');
}

if ( class_exists( 'Tribe__Events__Community__Tickets__Templates' ) ) {
	return;
}

/**
 * Handle views and template files.
 */
class Tribe__Events__Community__Tickets__Templates {

	function __construct() {
		add_filter( 'tribe_events_template_paths', [ $this, 'add_community_tickets_template_paths' ] );
		add_filter( 'tribe_support_registered_template_systems', [ $this, 'add_template_updates_check' ] );
	}

	/**
	 * Filter template paths to add the community plugin to the queue
	 *
	 * @param array $paths
	 * @return array $paths
	 * @author Peter Chester
	 * @since 3.1
	 */
	public function add_community_tickets_template_paths( $paths ) {
		$paths['community-tickets'] = tribe( 'community-tickets.main' )->plugin_path;
		return $paths;
	}

	/**
	 * Register Community Events Tickets with the template update checker.
	 *
	 * @param array $plugins
	 *
	 * @return array
	 */
	public function add_template_updates_check( $plugins ) {
		$plugins[ __( 'Community Events Tickets', 'tribe-events-community-tickets' ) ] = [
			Tribe__Events__Community__Tickets__Main::VERSION,
			tribe( 'community-tickets.main' )->plugin_path . 'src/views/community-tickets',
			trailingslashit( get_stylesheet_directory() ) . 'tribe-events/community-tickets',
		];

		return $plugins;
	}
}
