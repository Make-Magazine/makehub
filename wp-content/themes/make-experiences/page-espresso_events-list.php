<?php
/**
 * Template Name: Event List pages
 */
get_header();
// make sure $attributes is an array
$attributes = array_merge(
        // defaults
        array(
            'template_file' => 'espresso-grid-template.template.php', //Default template file
            //'title' => NULL,
            'limit' => 10,
            //'css_class' => NULL,
            'show_expired' => false,
            'month' => null,
            'category_slug' => null,
            'order_by' => 'start_date',
            'sort' => 'ASC',
        //'show_featured' => '0',
        //'table_header' => '1'
        ),
        (array) $attributes
);
// the following get sanitized/whitelisted in EEH_Event_Query
$custom_sanitization = array(
    'category_slug' => 'skip_sanitization',
    'show_expired' => 'skip_sanitization',
    'order_by' => 'skip_sanitization',
    'month' => 'skip_sanitization',
    'sort' => 'skip_sanitization',
);
if (method_exists('\EES_Shortcode', 'sanitize_attributes')) {
    $attributes = \EES_Shortcode::sanitize_attributes($attributes, $custom_sanitization);
} else {
    $attributes = $this->sanitize_the_attributes($attributes, $custom_sanitization);
}
global $wp_query;
$wp_query = new EE_Grid_Template_Query($attributes);
?>
<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php
        if (have_posts()) :
            ?>
            <header class="page-header">
                <h1 class="page-title">Upcoming Maker Campus Events</h1>
            </header><!-- .page-header -->
            <?php
            // allow other stuff
            do_action('AHEE__espresso_grid_template_template__before_loop');
            ?>
            <div class="events-list">
                <?php
                // Start the Loop.
                while (have_posts()) : the_post();
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
                        $dateFormat = $datetime->start_date('D <\b/>j<\/b/>');
                    }

                    $eventDetails = '';
                    if (get_field('custom_schedule_details', $event->ID())) {
                        $eventDetails = '<div class="event-time-desc">' . get_field('custom_schedule_details', $event->ID()) . '</div>';
                    } else {
                        if ($date_count > 1) {
                            if ($ticket_count == 1) {
                                $eventDetails = '<div class="event-time-desc">' . $date_count . ' sessions starting on  ' . $startmonth . " " . $startday . '</div>';
                            } else {
                                $eventDetails = '<div class="event-time-desc">Schedules Vary</div>';
                            }
                        }
                    }
                    ?>
                    <article id="post-<?php echo $event->ID(); ?>" <?php esc_attr(implode(' ', get_post_class())); ?>>
                        <div class="event-truncated-date"><?php echo $dateFormat; ?></div>
                        <div class="event-image">
                            <a href="<?php echo $registration_url; ?>">
                                <img src="<?php echo $image; ?>" />
                            </a>
                        </div>
                        <div class="event-info">
                            <div class="event-date"><?php echo $timerange; ?> Pacific</div>
                            <h3 class="event-title">
                                <a href="<?php echo $registration_url; ?>"><?php echo get_the_title($event->ID()); ?></a>
                            </h3>
                            <div class="event-description truncated"><?php echo get_field('short_description', $event->ID()); ?></div>
                            <?php
                            if ($eventDetails != '') {
                                echo $eventDetails;
                            }
                            ?>
                            <div class="event-prices">
                                <a href="<?php echo $registration_url; ?>#tickets" class="btn universal-btn">Get Tickets</a>
                                <?php echo event_ticket_prices($event); ?>
                            </div> <!-- end .event-prices -->
                        </div> <!-- end .event-info -->
                    </article>                    
                    <?php
                endwhile;
                ?>
            </div>
            <?php
            espresso_pagination();
            // allow moar other stuff
            do_action('AHEE__archive_espresso_events_template__after_loop');
        else :
            // If no content, include the "No posts found" template.
            espresso_get_template_part('content', 'none');
        endif;
        ?>
        <hr />
        Have questions or comments? Email us at <a href="mailto:makercampus@make.co">makercampus@make.co</a>
    </main><!-- #main -->
</div><!-- #primary -->

<?php
wp_reset_query();
wp_reset_postdata();
?>

<div id="secondary" class="widget_text widget-area sm-grid-1-1">
    <?php dynamic_sidebar('event_listing_sidebar'); ?>
</div>
<?php
get_footer();
