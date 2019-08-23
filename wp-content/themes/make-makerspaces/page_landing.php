<?php

/**
 * Template Name: Landing
 *
 * @package Make - Co
 * @author  Make Community
 * @license GPL-2.0-or-later
 * @link    https://make.co/
 */
// Add our custom loop
add_action('genesis_loop', 'spaces_map_loop');

function spaces_map_loop() {
    echo 'you are in the loop';
    /*
    $args = array(
        'category_name' => 'genesis-office-hours', // replace with your category slug
        'orderby' => 'post_date',
        'order' => 'DESC',
        'posts_per_page' => '12', // overrides posts per page in theme settings
    );
    $loop = new WP_Query($args);
    if ($loop->have_posts()) {
        // loop through posts
        while ($loop->have_posts()): $loop->the_post();
            $video_id = esc_attr(genesis_get_custom_field('cd_youtube_id'));
            $video_thumbnail = '<img src="http://img.youtube.com/vi/' . $video_id . '/0.jpg" alt="" />';
            $video_link = 'http://www.youtube.com/watch?v=' . $video_id;
            echo '<div class="one-third first">';
            echo '<a href="' . $video_link . '" target="_blank">' . $video_thumbnail . '</a>';
            echo '</div>';
            echo '<div class="two-thirds">';
            echo '<h4>' . get_the_title() . '</h4>';
            echo '<p>' . get_the_date() . '</p>';
            echo '<p><a href="' . $video_link . '" target="_blank">Watch It</a> | <a href="' . get_permalink() . '" target="_blank">Show Notes</a></p>';
            echo '</div>';
        endwhile;
    }
    wp_reset_postdata();*/
}

//* Run the Genesis loop
genesis();
