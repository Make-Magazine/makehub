<?php
// Options
$date_format = get_option('date_format');
$time_format = get_option('time_format');

if (have_posts()) :
    // allow other stuff
    do_action('AHEE__espresso_grid_template_template__before_loop');
    ?>
    <div class="event-view-btns">
        <a href="/maker-campus/events-list" title="List View"><i class="fas fa-th-list"></i></a>
        <a href="/maker-campus/event-calendar" title="Calendar View"><i class="fas fa-calendar-alt"></i></a>
    </div>
    <div id="mainwrapper" class="espresso-grid-wrapper">
        <div class="espresso-grid-revised">
            <?php
            // Start the Loop.
            while (have_posts()) : the_post();
                // Include the post TYPE-specific template for the content.
                global $post;

                //Create the event link
                $external_url = $post->EE_Event->external_url();
                $registration_url = !empty($external_url) ? $post->EE_Event->external_url() : $post->EE_Event->get_permalink();
                $feature_image_url = $post->EE_Event->feature_image_url('grid-cropped');

                if (!isset($default_image) || $default_image == '') {
                    $default_image = EE_GRID_TEMPLATE_URL . '/images/default.jpg';
                }

                $image = !empty($feature_image_url) ? $feature_image_url : $default_image;

                $datetimes = EEM_Datetime::instance()->get_datetimes_for_event_ordered_by_start_time($post->ID, $show_expired, false, 1);
                $date_count = count(EEM_Datetime::instance()->get_all_event_dates($post->ID));

                $event = EEH_Event_View::get_event($post->ID);
                $tickets = array();
                if ($event instanceof EE_Event) {
                    $tickets = $event->tickets();
                }
                $tickets_expired = array();

                $datetime = end($datetimes);

                // check how many tickets have expired. if that is equal to the total tickets, skip that event
                foreach ($tickets as $ticket) {
                    if ($ticket->ticket_status() == "TKE") {
                        $tickets_expired[] = "TKE";
                    }
                }
                if (count($tickets) == count($tickets_expired)) {
                    continue;
                }

                if ($datetime instanceof EE_Datetime) {
                    $startmonth = $datetime->start_date('M');
                    $startday = $datetime->start_date('j');
                    $timeData = $datetime->start_date('Y-m-d');
                    $timerange = $datetime->time_range('g:i a');
                    ?>

                    <a href="<?php echo $registration_url; ?>" class="ee_grid_box_revised item">
                        <div class="event-image-wrapper">
                            <img src="<?php echo $image; ?>" alt="" />
                        </div>
                        <div onclick="" class="event-info">
                            <div class="event-date">
                                <time class="event-date-time" datetime="<?php echo $timeData; ?>">
                                    <span class="event-start-month"><?php echo $startmonth; ?></span>
                                    <span class="event-start-day"><?php echo $startday; ?></span>
                                </time>
                            </div>
                            <div class="event-details">
                                <h3 class="event-title title">
                                    <?php echo $post->post_title; ?>
                                </h3>
                                <?php /* if(get_field('custom_schedule_details', $post->ID)) { ?>
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
                                  } */ ?>
                                <!--<div class="event-time">
                                <?php //echo $timerange; ?> Pacific
                                </div>-->

                            </div>
                        </div>

                        <!-- <div class="event-purchase">
                           <p class="price"><?php //echo event_ticket_prices($event);  ?></p>
                        </div>-->
                    </a>

                    <?php
                }
            endwhile;
            ?>
            <a href="/submit-event" class="btn universal-btn submit-event-btn">Submit Your Program</a>
        </div>
        <div class="events-grid-sidebar">
            <?php dynamic_sidebar('event_grid_sidebar'); ?>
            <?php            
            /*
            echo do_shortcode('[products columns="1" orderby="popularity" class="experiences" category="experiences"]');
            $count = get_term_by('name', "experiences", 'product_cat')->count;
            if ($count != 0) {
                ?>
                <a href="/product-category/experiences/">
                    <i class="fas fa-angle-double-right"></i> See More Experiences
                </a>
            <?php } else { ?>
                <a href="https://makershed.com" class="btn universal-btn" style="height:auto;padding:15px 10px;">
                    Checkout Maker Shed for Kits and More!
                </a>
    <?php } */?>
        </div>
    </div>
    <?php
    // allow moar other stuff
    do_action('AHEE__espresso_grid_template_template__after_loop');

else :
    // If no content, include the "No posts found" template.
    espresso_get_template_part('content', 'none');

endif;
