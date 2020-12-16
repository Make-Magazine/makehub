<?php
/**
 * Single Event Template
 * A single event. This displays the event title, description, meta, and
 * optionally, the Google map for the event.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/single-event.php
 *
 * @package TribeEventsCalendar
 * @version 4.6.19
 *
 */
if (!defined('ABSPATH')) {
    die('-1');
}

$events_label_singular = tribe_get_event_label_singular();
$events_label_plural = tribe_get_event_label_plural();

$event_id = get_the_ID();

// Build an array of all images associated with the post to create a gallery out of
$post_image_ids = array();
array_push($post_image_ids, get_post_thumbnail_id());
for ($x = 1; $x < 7; $x++) {
    if (get_field("image_" . $x)) {
        // if fresh from the form, this comes in as an array, but for some reason once edited it comes in as a number
        if (is_array(get_field("image_" . $x))) {
            array_push($post_image_ids, get_field("image_" . $x)["ID"]);
        } else {
            array_push($post_image_ids, get_field("image_" . $x));
        }
    }
}
$post_image_ids_string = implode(', ', $post_image_ids);

$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
?>

<div id="tribe-events-content" class="tribe-events-single">
    <p class="tribe-events-back">
        <a class="universal-btn" href="<?php echo esc_url(tribe_get_events_link()); ?>"> <?php printf('&laquo; ' . esc_html_x('All %s', '%s Events plural label', 'the-events-calendar'), $events_label_plural); ?></a>
    </p>
    <div class="tribe-events-image-gallery">
        <?php
        echo do_shortcode('[gallery ids="' . $post_image_ids_string . '" size="small" order="DESC" orderby="ID"]');
        if (count($post_image_ids) != 1) {
            ?>
            <a id="showAllGallery" class="universal-btn" href="javascript:void(jQuery('.psgal .msnry_item:first-of-type a').click())">View All Images</a>
        <?php } ?>
    </div>

    <div class="tribe-events-header tribe-clearfix">
        <?php
        the_title('<h1 class="tribe-events-single-event-title">', '</h1>');
        echo tribe_events_event_schedule_details($event_id, '<h2>', '</h2>');

        if (tribe_events_has_tickets()) {
            $cost = tribe_get_cost();
            if ($cost != 0 && $cost != 'Free') {
                ?>
                <span class="tribe-events-cost">&nbsp;-&nbsp;<?php echo($formatter->formatCurrency($cost, 'USD') ); ?></span>
                <?php
            }
        }
        ?>
    </div>

    <!-- Notices -->
    <?php tribe_the_notices() ?>

    <?php while (have_posts()) : the_post(); ?>
        <div id="post-<?php the_ID(); ?>" <?php post_class(); ?> class="container">
            <div class="row">
                <div class="col-md-8 col-sm-12 col-xs-12 event-info">
                    <!-- Event content -->
                    <?php do_action('tribe_events_single_event_before_the_content') ?>
                    <div class="event-cat">
                        <?php echo tribe_get_event_categories($event_id); ?>
                    </div>
                    <?php if (tribe_get_event_categories($event_id) == "In-Person" && get_field('location')) { ?>
                        <div class="tribe-events-single-location tribe-events-content">
                            <h3>Location:</h3> 
                            <?php echo get_field('location') ?>
                        </div>
                    <?php } ?>
                    <div class="tribe-events-single-event-description tribe-events-content">
                        <h3>What You'll Do:</h3> 
                        <?php the_content(); ?>
                    </div>
                    <?php if (get_field('basic_skills')) { ?>
                        <div class="tribe-events-single-skill-level tribe-events-content">
                            <h3>Skill Level:</h3> 
                            <?php echo get_field('basic_skills') ?>
                        </div>
                    <?php } ?>
                    <?php if (get_field('skills_taught')) { ?>
                        <div class="tribe-events-single-skills-taught tribe-events-content">
                            <h3>Skills you will learn:</h3> 
                            <?php echo get_field('skills_taught') ?>
                        </div>
                    <?php } ?>
                    <?php if (get_field('kit_required') == "Yes") { ?>
                        <div class="tribe-events-single-kit tribe-events-content">
                            <h3>A kit is required for this program:</h3> 
                            <?php
                            if (get_field('kit_price_included') == "yes") {
                                echo " and is included in the ticket price";
                                echo " and will be supplied by ";
                                if (get_field("kit_supplier") == "other") {
                                    echo get_field("other_kit_supplier");
                                } else {
                                    echo get_field("kit_supplier");
                                }
                            }
                            if (get_field('kit_price_included') == "no") {
                                echo "<p><a class='btn btn-blue-universal' href='" . get_field("kit_url") . "'  target='_blank'>Get Kit Here</a>";
                            }
                            ?>
                        </div>
                    <?php } ?>
                    <?php if (get_field('materials') ) { ?>
                        <div class="tribe-events-single-event-materials tribe-events-content">
                            <h3>What You'll Need:</h3> 
                            <div class="materials-list">
                                <?php echo get_field('materials'); ?>
                            </div>
                            <?php if (get_field('wish_list_urls') && get_field('wish_list_urls')[0]['wish_list'] != '') { ?>
                                <h3>Wishlist Links: </h3>
                                <ul>
                                    <?php
                                    foreach (get_field('wish_list_urls') as $wishlist) {
                                        echo '<li><a href="' . $wishlist['wish_list'] . '" target="_blank">' . $wishlist['wish_list'] . '</a></li>';
                                    }
                                    ?>
                                </ul>
                            <?php } ?>
                        </div>
                        <?php
                    }
                    if (get_field('promo_videos') && get_field('promo_videos')[0]['video'] != '') {
                        ?>
                        <div class="tribe-events-single-promo-videos tribe-events-content">
                            <h3>Videos: </h3>

                            <?php
                            foreach (get_field('promo_videos') as $video) {
                                $project_video = $video['video'];
                                if (strpos($project_video, "youtube") > 0 || strpos($project_video, "vimeo") > 0 || strpos($project_video, "youtu.be") > 0) {
                                    $dispVideo = str_replace('//vimeo.com', '//player.vimeo.com/video', $project_video);
                                    //youtube has two type of url formats we need to look for and change
                                    $videoID = parse_yturl($dispVideo);
                                    if ($videoID != false) {
                                        $dispVideo = 'https://www.youtube.com/embed/' . $videoID;
                                    }
                                    ?>
                                    <div class="entry-video">
                                        <div class="embed-youtube">
                                            <iframe class="lazyload" src="<?php echo $dispVideo ?>" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    echo "<p><a href='" . $video['video'] . "' target='_blank'>" . $video['video'] . "</a></p>";
                                }
                            }
                            ?>

                        </div>
                    <?php } ?>
                    <!-- .tribe-events-single-event-description -->
					<div class="event-author">
                        <h3>About the Facilitator:</h3> 
                        <?php 
							$organizer = tribe_get_organizer_ids($event_id);
							if(get_field('facilitator_info', $organizer[0])) {
								echo nl2br(get_field('facilitator_info', $organizer[0]));
							} else { // until all the fields can be manually copied over, default to the old field
								echo nl2br(get_field('about')); 
							}
						?>
                        <br /><br />
                        <?php
                        // Include organizer meta if appropriate
                        if (tribe_has_organizer()) {
                            tribe_get_template_part('modules/meta/organizer');
                        }
                        ?>

                    </div>

                    <?php
                    // ATTENDEES Section
                    $userList = get_event_attendees($event_id);
                    if (array_search(wp_get_current_user()->user_email, array_column($userList, 'purchaser_email')) !== false) {
                        ?>
                        <hr />
                        <h3>Attendee Resources:</h3> 
                        <div class="tribe-events-single-conference-link tribe-events-content">
                            <h3>Program Conference Link:</h3> 
                            <?php if (get_field('webinar_link')) { ?>
                                <a href="<?php echo get_field('webinar_link'); ?>" target="_blank" class="btn universal-btn">Program Stream</a>
                            <?php } else { ?>
                                COMING SOON
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php do_action('tribe_events_single_event_after_the_content') ?>
                </div>
                <div class="col-md-4 col-sm-12 col-xs-12 event-meta">
                    <div class="event-meta-sticky">
                        <!-- Event meta -->
                        <?php do_action('tribe_events_single_event_before_the_meta') ?>
                        <?php tribe_get_template_part('modules/meta'); ?>
                        <?php do_action('tribe_events_single_event_after_the_meta') ?>
                    </div>
                </div>
            </div>
        </div> <!-- #post-x -->

        <?php if (get_post_type() == Tribe__Events__Main::POSTTYPE && tribe_get_option('showComments', false)) comments_template() ?>
    <?php endwhile; ?>

    <!-- Event footer -->
    <div id="tribe-events-footer">
        <!-- Navigation -->
        <nav class="tribe-events-nav-pagination" aria-label="<?php printf(esc_html__('%s Navigation', 'the-events-calendar'), $events_label_singular); ?>">
            <ul class="tribe-events-sub-nav">
                <li class="tribe-events-nav-previous universal-btn"><?php echo tribe_the_prev_event_link('<i class="fas fa-angle-double-left"></i> %title%') ?></li>
                <li class="tribe-events-nav-next universal-btn"><?php echo tribe_the_next_event_link('%title% <i class="fas fa-angle-double-right"></i>') ?></li>
            </ul>
            <!-- .tribe-events-sub-nav -->
        </nav>
    </div>
    <!-- #tribe-events-footer -->

</div><!-- #tribe-events-content -->