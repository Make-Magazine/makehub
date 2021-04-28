<?php
// Options
$date_format = get_option( 'date_format' );
$time_format = get_option( 'time_format' );
$temp_month = '';
$reg_button_text = !isset($button_text) ? __('Register Now!', 'event_espresso') : $button_text;
$alt_button_text = !isset($alt_button_text) ? __('View Details', 'event_espresso') : $alt_button_text;//For alternate registration pages

if ( have_posts() ) :
	// allow other stuff
	do_action( 'AHEE__espresso_grid_template_template__before_loop' );
	?>
	<div class="event-view-btns">
		<a href="/maker-campus/events-list" class="universal-btn">List</a>
		<a href="/maker-campus/event-calendar" class="universal-btn">Calendar</a>
	</div>
	<div id="mainwrapper" class="espresso-grid-wrapper">
		<div class="espresso-grid-revised">
		<?php
		// Start the Loop.
		while ( have_posts() ) : the_post();
			// Include the post TYPE-specific template for the content.
			global $post;

			//Create the event link
			$external_url 		= $post->EE_Event->external_url();
			$button_text		= !empty($external_url) ? $alt_button_text : $reg_button_text;
			$registration_url 	= !empty($external_url) ? $post->EE_Event->external_url() : $post->EE_Event->get_permalink();
			$feature_image_url	= $post->EE_Event->feature_image_url('grid-cropped');

			if(!isset($default_image) || $default_image == '') {
				$default_image = EE_GRID_TEMPLATE_URL .'/images/default.jpg';
			}

			$image = !empty($feature_image_url) ? $feature_image_url : $default_image;

			$datetimes = EEM_Datetime::instance()->get_datetimes_for_event_ordered_by_start_time( $post->ID, $show_expired, false, 1 );
			$date_count = count(EEM_Datetime::instance()->get_all_event_dates( $post->ID ));

			$event = EEH_Event_View::get_event($post->ID);
			$tickets = array();
			if ($event instanceof EE_Event) {
				$tickets = $event->tickets();
			}		
			$ticket_count = count($tickets);

			$datetime = end( $datetimes );
			
			// if the first date of event has passed and it's a multiday event with one ticket, skip this item in the loop
			$firstExpiredDate = EEM_Datetime::instance()->get_oldest_datetime_for_event( $post->ID, true, false, 1 )->get_i18n_datetime('DTT_EVT_start');
			if(strtotime($firstExpiredDate) < time() && $ticket_count == 1 ) {
				continue;
			}
			
			if ($datetime instanceof EE_Datetime) {
				$startmonth = $datetime->start_date('M');
				$startday = $datetime->start_date('j');
				$timeData = $datetime->start_date('Y-m-d');
				$timerange = $datetime->time_range('g:i a');
			?>

			<div class="ee_grid_box_revised item">
				<div class="event-image-wrapper">
					<a href="<?php echo $registration_url; ?>">
						<img src="<?php echo $image; ?>" alt="" />
					</a>
				</div>
				<div onclick="" class="event-info">
					<div class="event-date">
						<time class="event-date-time" datetime="<?php echo $timeData; ?>">
							<span class="event-start-month"><?php echo $startmonth; ?></span>
							<span class="event-start-day"><?php echo $startday; ?></span>
						</time>
					</div>
					<div class="event-details">
						<div class="event-time">
							<?php echo $timerange; ?> Pacific
						</div>
						<h3 class="event-title title">
							<a href="<?php echo $registration_url; ?>">
								<?php echo $post->post_title; ?>
							</a>
						</h3>
					</div>
				</div>
				<?php if(get_field('custom_schedule_details', $post->ID)) { ?>
						<div class="event-time-desc">
							<?php echo get_field('custom_schedule_details', $post->ID); ?>
						</div>
				<?php } else {
						  if($date_count > 1){ 
							if($ticket_count == 1) { ?>
								<div class="event-time-desc">
									<?php 
										echo $date_count; ?> sessions starting on <?php echo $startmonth . " " . $startday; 
									?>
								</div>
					<?php 	} else { ?>
								<div class="event-time-desc">Schedules Vary</div>
					<?php 	}
						  } 
					   }  ?>
				<div class="event-purchase">
					<?php echo '<a class="btn universal-btn" id="a_register_link-' . $post->ID .'" href="' . $registration_url . '">' . $button_text . '</a>'; ?>
					<p class="price"><?php echo event_ticket_prices($event); ?></p>
				</div>
			</div>

			<?php
			}
		endwhile; ?>
		</div>
		<div class="events-grid-sidebar">
			<?php echo do_shortcode('[products columns="1" orderby="popularity" class="experiences" category="experiences"]'); ?>
			<a href="/product-category/experiences/">
				<i class="fas fa-angle-double-right"></i> See More Experiences
			</a>
		</div>
	</div>
	<?php
	// allow moar other stuff
	do_action( 'AHEE__espresso_grid_template_template__after_loop' );

else :
	// If no content, include the "No posts found" template.
	espresso_get_template_part( 'content', 'none' );

endif;
