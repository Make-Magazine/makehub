<?php

class Tribe__Events__Community__Tickets__Route__Sales_Report extends Tribe__Events__Community__Tickets__Route__Abstract_Route {
	/**
	 * Route slug
	 * @var string
	 */
	public $route_slug = 'view-sales-report-route';

	/**
	 * Route suffix
	 * @var string
	 */
	public $route_suffix = '/(\d+/?)$';

	/**
	 * Route query vars
	 * @var array
	 */
	public $route_query_vars = [
		'tribe_community_event_id' => 1,
	];

	/**
	 * Page arguments
	 * @var array
	 */
	public $page_args = [
		'tribe_community_event_id',
	];

	/**
	 * constructor
	 */
	public function __construct( $router, $shortcode = false ) {

		if ( true === $shortcode ) {
			return;
		}

		$community_events = Tribe__Events__Community__Main::instance();
		$community_events->rewriteSlugs['sales'] = sanitize_title( __( 'sales', 'tribe-events-community-tickets' ) );

		$title = __( 'Sales Report', 'tribe-events-community-tickets' );
		$title = apply_filters( 'tribe_community_tickets_sales_report_title', $title );
		$title = apply_filters_deprecated(
			'tribe_ct_sales_report_title',
			[ $title ],
			'4.6.3',
			'tribe_community_tickets_sales_report_title',
			'The filter "tribe_ct_sales_report_title" has been renamed to "tribe_community_tickets_sales_report_title" to match plugin namespacing.'
		);

		$this->title = $title;

		parent::__construct( $router );
	}

	/**
	 * Handles the rendering of the route
	 *
	 * @param mixed $event_id
	 * @param bool  $shortcode
	 *
	 * @return string $output
	 */
	public function callback( $event_id = null, $shortcode = false ) {
		$community_tickets = tribe( 'community-tickets.main' );
		if ( ! $shortcode ) {
			$community_tickets->require_login( $event_id );
		}
		$community_tickets->register_resources();

		$community_events = Tribe__Events__Community__Main::instance();
		add_filter( 'tribe_events_current_view_template', [ $community_events, 'default_template_placeholder' ] );
		wp_enqueue_style( 'list-tables' );
		tribe_asset_enqueue_group( 'events-styles' );
		tribe_asset_enqueue_group( 'event-tickets-admin' );
		tribe_asset_enqueue_group( 'event-tickets-plus-admin' );

		$event_tickets_resources_url = plugins_url( 'event-tickets/src/resources' );
		wp_enqueue_style( 'tickets-report-css', $event_tickets_resources_url . '/css/tickets-report.css', [], Tribe__Tickets__Main::instance()->css_version() );
		wp_enqueue_style( 'tickets-report-print-css', $event_tickets_resources_url . '/css/tickets-report-print.css', [], Tribe__Tickets__Main::instance()->css_version(), 'print' );
		wp_enqueue_script( 'tickets-attendees-js', $event_tickets_resources_url . '/js/tickets-attendees.js', [ 'jquery' ], Tribe__Tickets__Main::instance()->js_version() );
		tribe_asset_enqueue( 'events-community-tickets-shortcodes-css' );

		// the attendees report requires that the event ID be placed in $_GET['event_id']
		$_GET['event_id'] = $event_id;

		$GLOBALS['hook_suffix'] = null;

		include_once ABSPATH . '/wp-admin/includes/screen.php';
		include_once ABSPATH . '/wp-admin/includes/template.php';

		// starting with WordPress 4.4, these two classes were split out to their own files
		if ( ! class_exists( 'WP_Screen' ) ) {
			include_once ABSPATH . '/wp-admin/includes/class-wp-screen.php';
		}

		if ( ! class_exists( 'WP_List_Table' ) ) {
			include_once ABSPATH . '/wp-admin/includes/class-wp-list-table.php';
		}

		add_filter( 'body_class', [ $this, 'remove_body_classes' ], 10, 2 );

		$community_events->removeFilters();

		add_filter( 'tribe_tickets_attendees_show_view_title', '__return_false' );
		add_filter( 'tribe_tickets_order_report_show_title', '__return_false' );

		ob_start();
		?>
		<div class="table-menu-wrapper">
			<a href="<?php echo esc_url( tribe_community_events_list_events_link() ); ?>" class="button">
				<?php echo sprintf( __( 'My %s', 'tribe-events-community' ), tribe_get_event_label_plural() ); ?>
			</a>
			<?php do_action( 'tribe_community_tickets_sales_report_nav' ); ?>
			<?php do_action_deprecated(
			'tribe_ct_sales_report_nav',
			[],
			'4.6.3',
			'tribe_community_tickets_sales_report_nav',
			'The action "tribe_ct_sales_report_nav" has been renamed to "tribe_community_tickets_sales_report_nav" to match plugin namespacing.'
		); ?>
		</div>
		<?php
		$orders_report = Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance()->orders_report();
		$orders_report->orders_page_screen_setup();

		if ( $shortcode ) {
			add_filter( 'tribe_tickets_plus_order_pagination', [ tribe( 'community-tickets.shortcodes' ), 'orders_per_page' ] );
		}

		$orders_report->orders_page_inside();

		tribe_asset_enqueue( 'events-community-tickets-css' );
		tribe_asset_enqueue( 'events-community-tickets-js' );
		wp_enqueue_script( 'list-table' );
		wp_enqueue_script( 'common' );
		$output = ob_get_clean();

		$output = '<div id="tribe-events-report">' . $output . '</div>';

		return $output;
	}

	/**
	 * Returns paths for routes
	 *
	 * @param $suffix string Value gets appended to the end of the path upon return
	 *
	 * @return string
	 */
	public function path( $suffix = null ) {
		$community_events = Tribe__Events__Community__Main::instance();

		$path = $community_events->getCommunityRewriteSlug() . '/' . $community_events->rewriteSlugs['sales'] . '/' . $community_events->rewriteSlugs['event'];
		$path .= $suffix;

		return $path;
	}//end path

	/**
	 * return the sales report link
	 */
	public function url( $post_id ) {
		$url = Tribe__Events__Community__Main::instance()->getUrl( 'sales', $post_id, null, Tribe__Events__Main::POSTTYPE );
		$url = apply_filters( 'tribe_community_tickets_sales_report_url', $url );
		$url = apply_filters_deprecated(
			'tribe_ct_sales_report_url',
			[ $url ],
			'4.6.3',
			'tribe_community_tickets_sales_report_url',
			'The filter "tribe_ct_sales_report_url" has been renamed to "tribe_community_tickets_sales_report_url" to match plugin namespacing.'
		);

		return $url;
	}//end url
}
