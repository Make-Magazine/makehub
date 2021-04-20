<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package BuddyBoss_Theme
 */

global $post;
$person = EEH_People_View::get_person();

get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">
			<div class="host-wrapper">
				<div class="host-photo">
					<?php echo get_the_post_thumbnail(); ?>	
				</div>
				
				<div class="host-meta">
					<h1 class="host-title"><?php echo get_the_title(); ?></h1>

					<div class="host-email">
						<i class="fas fa-link"></i>
						<a href="<?php echo get_field('website'); ?>" target="_blank"><?php echo get_field('website'); ?></a>
					</div>
					<?php 
						$social_links = get_field('social_links', $person->ID());
						if($social_links) { ?>
							<span class="social-links">
							<b>See more of <?php echo get_the_title(); ?> at:</b>
							<?php foreach ($social_links as $link) {
								if ($link['social_link'] != '') {
									echo '<a href="' . $link['social_link'] . ' target="_blank">*</a>';
								}
							} ?>
							</span>
						<?php }
					?>

					<div class="host-bio">
						<?php
							$content = get_field('facilitator_info');
							$postwithbreaks = wpautop( $content, true );
							echo $postwithbreaks;
						?>	
					</div>
				</div>

			</div>

			<div class='host-events'>
				<?php
					$events = EEH_People_View::get_events_for_person();
				
					?>
					<div class="eea-people-addon-person-events-container">
						<?php if ( $events ) : ?>
						<h3>Events <?php echo get_the_title(); ?> is involved with:</h3>
						<?php foreach ( $events as $type => $event ) : // type here is the person type ?>
							<div class="eea-people-addon-people-type-container">
								<?php /* <h4 class="eea-people-addon-people-type-label"><?php echo $type; ?></h4> */ ?>
								<div class="events-list">
									<?php $event = array_reverse($event); // reverse this order to show upcoming first
									foreach ( $event as $evt ) {
										$date = $evt->first_datetime(); 
										$dateFormat = date('D <\b/>j<\/b/>', strtotime($date->start_date()));
										$startime = date('F j, Y @ g:i a', strtotime($date->start_date()));
										$endime = date('g:i a', strtotime($date->end_time()));

										$return = '<article id="post-' . $evt->ID() . '" '. esc_attr( implode( ' ', get_post_class() ) )  .'>
													 <div class="event-truncated-date">' . $dateFormat . '</div>
													 <div class="event-image">
													   <a href="' . get_permalink($evt->ID())  . '">
														 <img src="' . get_the_post_thumbnail_url( $evt->ID(), 'thumbnail' ) . '" />
													   </a>
													 </div>
													 <div class="event-info">
													   <div class="event-date">'. $startime . ' - ' . $endime . ' Pacific</div>
													   <h3 class="event-title">
														 <a href="' . get_permalink($evt->ID()) . '">' . get_the_title($evt->ID()) . '</a>
													   </h3>
													   <div class="event-description">' . get_field('short_description', $evt->ID()) . '</div>
													   <div class="event-prices">';
															$return .= event_ticket_prices($evt) . 
													  '</div>
													 </div>
												   </article>';
			
										echo $return;

									} ?>
								</div>
							</div>
						<?php endforeach; ?>
						<?php endif; ?>
					</div>
			</div>
		</main><!-- #main -->
	</div><!-- #primary -->


<?php
get_footer();
