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
				<h1 class="page-title"><?php echo get_the_title(); ?></h1>
				<div class="event-view-btns">
					<a href="/maker-campus" class="universal-btn">Grid</a>
					<a href="/maker-campus/event-calendar" class="universal-btn">Calendar</a>
				</div>
			</header><!-- .page-header -->
        	

			<div class="events-list">

				<?php
				/* Start the Loop */
				$args = array( 'post_type' => 'espresso_events' );
				$loop = new WP_Query( $args );
				while ( $loop->have_posts() ) : $loop->the_post();
					global $post;
				
				    $date = $post->EE_Event->first_datetime(); 
				 	$dateFormat = date('D <\b/>j<\/b/>', strtotime($date->start_date()));
					$startime = date('F j, Y @ g:i a', strtotime($date->start_date()));
					$endtime = date('g:i a', strtotime($date->end_time()));
				
					// if the first date of event has passed and it's a multiday event with one ticket, skip this item in the loop
					$firstExpiredDate = EEM_Datetime::instance()->get_oldest_datetime_for_event( $post->ID, true, false, 1 )->get_i18n_datetime('DTT_EVT_start');
					$now = new DateTime("now", new DateTimeZone('America/Los_Angeles'));
					$now = strtotime($now->format('Y-m-d H:i:s'));

					if(strtotime($firstExpiredDate) < $now  && $ticket_count == 1 ) {
						continue;
					}
				
					$event = EEH_Event_View::get_event($post->ID);
					$tickets = array();
					if ($event instanceof EE_Event) {
						$tickets = $event->tickets();
					}		
					$ticket_count = count($tickets);
				
					$eventDetails = '';
				
					if(get_field('custom_schedule_details', $post->ID)) { 
						$eventDetails = '<div class="event-time-desc">' . get_field('custom_schedule_details', $post->ID) . '</div>';
				    } else {
						if($date_count > 1){ 
							if($ticket_count == 1) { 
								$eventDetails = '<div class="event-time-desc">'.$date_count.' sessions starting on  ' .$startmonth . " " . $startday . '</div>';
							} else { 
								$eventDetails = '<div class="event-time-desc">Schedules Vary</div>';
							}
						} 
				   } 
				   
					//if($datetime instanceof EE_Datetime) {

						$return = '<article id="post-' . $post->ID . '" '. esc_attr( implode( ' ', get_post_class() ) )  .'>
									 <div class="event-truncated-date">' . $dateFormat . '</div>
									 <div class="event-image">
									   <a href="' . get_permalink() . '">
										 <img src="' . get_the_post_thumbnail_url( $post->ID, 'thumbnail' ) . '" />
									   </a>
									 </div>
									 <div class="event-info">
									   <div class="event-date">'. $startime . ' - ' . $endtime . ' PST</div>
									   <h3 class="event-title">
										 <a href="' . get_permalink() . '">' . get_the_title() . '</a>
									   </h3>
									   <div class="event-description">' . get_field('short_description') . '</div>';
						if($eventDetails != '') { $return .= $eventDetails; }
						$return .=     '<div class="event-prices">';
											$return .= event_ticket_prices($post) . 
									  '</div>
									 </div>
								   </article>';

						echo $return;
					//}

				endwhile;
				?>
			</div>

			<?php
			buddyboss_pagination();

		else :
			get_template_part( 'template-parts/content', 'none' );
			?>

		<?php endif; ?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php get_sidebar(); ?>

<?php
get_footer();
