<?php
/**
 * Template Name: Event List pages
 */

get_header();

?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">
		
		<?php if ( have_posts() ) : ?>
			<header class="page-header">
				<h1 class="page-title">Upcoming Maker Campus Events</h1>
			</header><!-- .page-header -->
      
			<div class="events-list">
				<?php
				$where = array(
					'Datetime.DTT_EVT_start' => array( '>=', current_time( 'mysql' )),
					'status' => 'publish',
				);
				// run the query
				if ( class_exists( 'EE_Registry' ) ) :
					$events = EE_Registry::instance()->load_model( 'Event' )->get_all( array(
						$where,
						'order_by' => 'Datetime.DTT_EVT_start',
						'order' => 'ASC',
						'group_by' => 'EVT_ID'
					));
					foreach ( $events as $event ) {
						$date = $event->first_datetime(); 
						$dateFormat = date('D <\b/>j<\/b/>', strtotime($date->start_date()));
						$startmonth = date('F', strtotime($date->start_date()));
						$startday = date('j', strtotime($date->start_date()));
						$startime = date('F j, Y @ g:i a', strtotime($date->start_date()));
						$endtime = date('g:i a', strtotime($date->end_time()));
						
						$date_count = count(EEM_Datetime::instance()->get_all_event_dates( $event->ID() ));

						$tickets = array();
						if ($event instanceof EE_Event) {
							$tickets = $event->tickets();
						}		
						$ticket_count = count($tickets);

						$eventDetails = '';

						if(get_field('custom_schedule_details', $event->ID())) { 
							$eventDetails = '<div class="event-time-desc">' . get_field('custom_schedule_details', $event->ID()) . '</div>';
						} else {
							if($date_count > 1){ 
								if($ticket_count == 1) { 
									$eventDetails = '<div class="event-time-desc">'.$date_count.' sessions starting on  ' .$startmonth . " " . $startday . '</div>';
								} else { 
									$eventDetails = '<div class="event-time-desc">Schedules Vary</div>';
								}
							} 
						} 

						$return = '<article id="post-' . $event->ID() . '" '. esc_attr( implode( ' ', get_post_class() ) )  .'>
									 <div class="event-truncated-date">' . $dateFormat . '</div>
									 <div class="event-image">
									   <a href="' . get_permalink($event->ID()) . '">
										 <img src="' . get_the_post_thumbnail_url( $event->ID(), 'thumbnail' ) . '" />
									   </a>
									 </div>
									 <div class="event-info">
									   <div class="event-date">'. $startime . ' - ' . $endtime . ' Pacific</div>
									   <h3 class="event-title">
										 <a href="' . get_permalink($event->ID()) . '">' . get_the_title($event->ID()) . '</a>
									   </h3>
									   <div class="event-description">' . get_field('short_description', $event->ID()) . '</div>';
						if($eventDetails != '') { $return .= $eventDetails; }
						$return .=     '<div class="event-prices">
											<a href="' . get_permalink($event->ID()) . '#tickets" class="btn universal-btn">Get Tickets</a>';
											$return .= event_ticket_prices($event) . 
									  '</div>
									 </div>
								   </article>';
						// if the first date of event has passed and it's a multiday event with one ticket, skip this item in the loop
						$firstExpiredDate = EEM_Datetime::instance()->get_oldest_datetime_for_event( $event->ID(), true, false, 1 )->start();
						$now = new DateTime("now", new DateTimeZone('America/Los_Angeles'));
						$now = $now->format('Y-m-d H:i:s');
						if(date('Y-m-d H:i:s', $firstExpiredDate) < $now  && $ticket_count == 1 ) {
							$return = '';
						}
						echo $return;

					}
				endif; 
				?>
			</div>

			<?php
			espresso_pagination();
			// allow moar other stuff
			do_action('AHEE__archive_espresso_events_template__after_loop');

		else :
			get_template_part( 'template-parts/content', 'none' );
			?>

		<?php endif; ?>
		<hr />
		Have questions or comments – email us at <a href="mailto:makercampus@make.co">makercampus@make.co</a>
	</main><!-- #main -->
</div><!-- #primary -->
<div id="secondary" class="widget_text widget-area sm-grid-1-1">
	<?php dynamic_sidebar('event_listing_sidebar'); ?>
</div>
<?php
get_footer();
