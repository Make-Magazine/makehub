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
					<?php if(get_field('website')) { ?>
						<div class="host-email">
							<i class="fas fa-link"></i>
							<a href="<?php echo get_field('website'); ?>" target="_blank"><?php echo get_field('website'); ?></a>
						</div>
					<?php } ?>
					<?php
						$social_links = get_field('social_links', $person->ID());
						if($social_links) { ?>
							<span class="social-links">
							<b>See more of <?php echo get_the_title(); ?> at:</b>
							<?php foreach ($social_links as $link) {
								if ($link['social_link'] != '') {
									echo '<a href="' . $link['social_link'] . '" target="_blank">*</a>';
								}
							} ?>
							</span>
						<?php }
					?>
				</div>
				<div class="host-bio">
					<?php
						$content = get_field('facilitator_info');
						$postwithbreaks = wpautop( $content, true );
						echo $postwithbreaks;
					?>
				</div>

			</div>

			<script src="https://make.activehosted.com/f/embed.php?id=29" type="text/javascript" charset="utf-8"></script>
			<a href="" onclick="jQuery('._form-wrapper').show();jQuery('#formBtn').hide(); return false;" id="formBtn" class="btn universal-btn-reversed">Keep me informed of upcoming sessions</a>

			<div class='host-events'>
				<?php
					$events = EEH_People_View::get_events_for_person();

					?>
					<div class="eea-people-addon-person-events-container">
						<?php if ( $events ) : ?>
						<h2>Events <?php echo get_the_title(); ?> is involved with:</h2>
						<?php foreach ( $events as $type => $event ) : // type here is the person type ?>
							<div class="eea-people-addon-people-type-container">
								<?php /* <h4 class="eea-people-addon-people-type-label"><?php echo $type; ?></h4> */ ?>
								<div class="events-list">
									<?php $event = array_reverse($event); // reverse this order to show upcoming first
									foreach ( $event as $evt ) {
										$date = $evt->first_datetime();

										$timestamp = strtotime(date('Y-m-d H:i:s', strtotime($date->start_date())));
										$dateFormat = date('D <\b/>j<\/b/>', strtotime($date->start_date()));
										$starttime = date('F j, Y', strtotime($date->start_date())) . " @ " . date('g:i a', strtotime($date->start_time()));

										$endtime = date('g:i a', strtotime($date->end_time()));

										$isPast = ( $timestamp > time() ) ? true : false;

										$return = '	<article id="post-' . $evt->ID() . '" class="'. esc_attr( implode( ' ', get_post_class() ) )  .'">
											 			<div class="event-image">
														  	<div class="event-truncated-date">' . $dateFormat . '</div>
													   		<a href="' . get_permalink($evt->ID())  . '">
														 			<img src="' . get_the_post_thumbnail_url( $evt->ID(), 'thumbnail' ) . '" />
													   		</a>
												 		</div>
										 				<div class="event-info">
												   			<div class="event-date">'. $starttime . ' - ' . $endtime . ' Pacific</div>
											   				<h3 class="event-title">
													 				<a href="' . get_permalink($evt->ID()) . '">' . get_the_title($evt->ID()) . '</a>
												   			</h3>
												   			<div class="event-description">' . get_field('short_description', $evt->ID()) . '</div>';
															if($isPast == true) {
																$return .= '<div class="event-prices">
																				<a href="' . get_permalink($evt->ID()) . '/#tickets" class="btn universal-btn" target="_blank">Get Tickets</a>'
																				. event_ticket_prices($evt) .
																			'</div>';
															}
										$return .= 		'</div>
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
