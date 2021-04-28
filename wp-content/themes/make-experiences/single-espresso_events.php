<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package BuddyBoss_Theme
 */
global $post;
$post_id = get_the_ID();
$url = '';

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

global $current_user;
$current__user = wp_get_current_user();;

//get the users email
$user_email = $current__user->user_email;
$user_slug = $current__user->user_nicename;

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
		<p class="events-back">
			<a class="universal-btn" href="/maker-campus"> « All Events</a>
		</p>
        <h1 class="entry-title"><?php echo( get_the_title() . espresso_event_status_banner() ); ?></h1>
        <?php if (has_post_thumbnail()) { ?>
            <div class="gallery-wrapper">
                <?php
                echo do_shortcode('[gallery ids="' . $post_image_ids_string . '" size="medium-large" order="DESC" orderby="ID"]');
                if (count($post_image_ids) != 1) {
                    ?>
                    <a id="showAllGallery" class="universal-btn" href="javascript:void(jQuery('.psgal .msnry_item:first-of-type a').click())"><i class="fas fa-images"></i></a>
                <?php } ?>
            </div>
        <?php } ?>
        <div class="entry-content">
            <div class="event-datetimes">
                <?php echo espresso_list_of_event_dates(); ?>
            </div>
            <div class="event-content container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <?php
                        if (class_exists('ESSB_Plugin_Options')) {
                            $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                            echo do_shortcode('[easy-social-share buttons="facebook,pinterest,reddit,twitter,linkedin,love,more" morebutton_icon="dots" morebutton="2" counters="yes" counter_pos="after" total_counter_pos="hidden" animation="essb_icon_animation6" style="icon" fullwidth="yes" template="6" postid="' . $post_id . '" url="' . $url . '" text="' . get_the_title() . '"]');
                        }
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class='event-main-content col-md-7 col-sm-12 col-xs-12'>
                        <?php if(get_the_terms( $post, 'event_types' )) { ?>
                                <div class="event-cat">
                                        <?php $event_type = get_the_terms( $post, 'event_types' )[0]; ?>
                                        <a href="/event_types/<?php echo $event_type->slug; ?>">
                                                <?php echo $event_type->name; ?>
                                        </a>
                                </div>
                                <?php if ($event_type->name == "In-Person" && get_field('location')) { ?>
                                        <div class="event-location event-content-item">
                                                <h4>Location:</h4> 
                                                <?php echo get_field('location') ?>
                                        </div>
                                <?php
                                }
                        }
                        // ATTENDEES Section
                        $userList = EEM_Attendee::instance()->get_all();
                        if (array_search($user_email, array_column($userList, 'purchaser_email')) !== false) {
                            ?>
                            <hr />
                            <h4 style="margin-top:0px;">Attendee Resources:</h4> 
                            <div class="single-conference-link tribe-events-content" style="border-bottom: 0px;">
                                <?php if (get_field('webinar_link')) { ?>
                                    <a href="<?php echo get_field('webinar_link'); ?>" target="_blank" class="btn universal-btn">Online Event Link</a>
                                <?php } else { ?>
                                    COMING SOON
                                <?php } ?>
                            </div>
                            <a href="/members/<?php echo $user_slug; ?>/dashboard" class="btn universal-btn">Access Your Tickets</a>
                        <?php } ?>
                        <div class="event-description event-content-item">
                            <h4>What You'll Do:</h4> 
                            <?php echo apply_filters('the_content', $post->post_content); ?>
                        </div>
                        <?php if (get_field('basic_skills')) { ?>
                            <div class="event-skill-level event-content-item">
                                <h4>Skill Level:</h4> 
                                <?php echo get_field('basic_skills'); ?>
                            </div>
                        <?php } ?>
                        <?php if (get_field('skills_taught')) { ?>
                            <div class="event-skills-taught event-content-item">
                                <h4>Skills you will learn:</h4> 
                                <?php echo html_entity_decode(get_field('skills_taught')); ?>
                            </div>
                        <?php } ?>
                        <?php if (get_field('kit_required') == "Yes") { ?>
                            <div class="event-kit event-content-item">
                                <h4>A kit is required for this program:</h4> 
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
                        <?php if (get_field('materials')) { ?>
                            <div class="event-materials event-content-item">
                                <h4>What You'll Need:</h4> 
                                <div class="materials-list">
                                    <?php echo html_entity_decode(get_field('materials')); ?>
                                </div>
                                <?php if (get_field('wish_list_urls') && get_field('wish_list_urls')[0]['wish_list'] != '') { ?>
                                    <h4>Wishlist Links: </h4>
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
                            <div class="event-promo-videos event-content-item">
                                <h4>Videos: </h4>

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
                        <?php }
                        if (get_field('program_expertise')) {
                            ?>
                            <div class="event-host event-content-item">
                                <h4>About your Host(s):</h4> 
                                <?php echo html_entity_decode(get_field('program_expertise')); ?>
                            </div>
                            <?php
                        }
                        get_template_part('template-parts/content-espresso_events-people', 'page');
                        ?>
                    </div>

                    <div class='event-sidebar-content col-md-5 col-sm-12 col-xs-12'>
                        <div class="event-sidebar-item" id="tickets">
                            <h3>Tickets</h3>
                            <?php echo do_shortcode("[ESPRESSO_TICKET_SELECTOR event_id=" . $post->ID . "]"); ?>                            
                        </div>
                        <div class="event-sidebar-item">
                            <h3>Details</h3>                            
                            <div class="event-sidebar-field event-date">
                                <b>Dates:</b>
                                    <?php
                                    $event = EEM_Event::instance()->get_one_by_ID($post->ID);          
                                    $tickets = $event->tickets();   
                                    foreach($tickets as $ticket){ ?>
										<div class="ticket-detail">
                                        	<div class="ticket-detail-name"><?php echo $ticket->name(); ?></div>
                                        	<ul>
											<?php $dates = $ticket->datetimes();
												  foreach ($dates as $date) { ?>
													<li>
														<?php echo $date->start_date() . ' ' . $date->start_time() . ' - ' . $date->end_time(); ?> <span class="small">(Pacific)</span> 
												    </li>
											<?php }
											?>                                    
                                            </ul>
										</div>
                                    <?php } ?>
                                
                            </div>
                            <div class="event-sidebar-field event-cost">
                                <b>Cost: </b>
    							<span class="price-item"><?php echo event_ticket_prices($event); ?></span>
                            </div>
                            <?php
                            // Age ranges
                            if (get_field('audience')) {
                                ?>
                                <div class="event-sidebar-field event-age">
                                    <b>Age Range:</b>
                                    <?php foreach (get_field('audience') as $age) { 
										$ageValues = get_field_object('audience')['choices'];
									?>
                                        <span class='age-item'><?php echo $ageValues[$age]; ?></span>
                                <?php } ?>
                                </div>
                            <?php
                            }
                            $categories = get_the_terms($post->ID, 'espresso_event_categories');
                            if ($categories) {
                                ?>
                                <div class="event-sidebar-field event-categories">
                                    <b>Event Categories:</b>
                                    <div class="event-categories-wrap">
                                        <?php foreach ($categories as $category) { ?>
                                            <a href="/event-category/<?php echo $category->slug; ?>">
                                    <?php echo $category->name; ?>
                                            </a>
                                <?php } ?>
                                    </div>
                                </div>
                             <?php }
                            ?>

                            Have questions or comments – email us at <a href="mailto:makercampus@make.co">makercampus@make.co</a>
                            <br /><br />
                        </div>
                                <?php 
                                $relevents = get_field('events');
                                if ($relevents && is_singular(array('espresso_events'))) { ?>
                            <div class="related-events">
                                <h3 class="event-venues-h3 ee-event-h3">Related Events</h3>
                                <ul>
                                            <?php foreach ($relevents as $relevent): ?>
                                        <li>
                                            <a href="<?php echo get_permalink($relevent->ID); ?>">
                                        <?php echo get_the_title($relevent->ID); ?>
                                            </a>
                                        </li>
                            <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        Have questions or comments – email us at <a href="mailto:makercampus@make.co">makercampus@make.co</a>
    </main><!-- #main -->
</div><!-- #primary -->


<?php
get_footer();
