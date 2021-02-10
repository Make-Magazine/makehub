<?php

/**
 * Make - Community Theme
 *
 * This file adds the landing page template to the Make - Community Theme.
 *
 * Template Name: Landing
 *
 * @package Make - Co
 * @author  Make Community
 * @license GPL-2.0-or-later
 * @link    https://make.co/
 */
function custom_home_page_setup() {
    // Remove posts.
    //remove_action( 'Genesis_loop', 'Genesis_do_loop' );
    ?>
    <div class = "container-fluid content-panels">
    <?php if (have_posts()) : while (have_posts()) : the_post();
            // check if the flexible content field has rows of data
            if (have_rows('content_panels')) {
                // loop through the rows of data
                while (have_rows('content_panels')) {
                    the_row();
                    $row_layout = get_row_layout();
                    //error_log($row_layout);
                    echo dispLayout($row_layout);
                }
            }
            ?>

        <?php endwhile; ?>
    <?php endif; ?>

    </div>
    <?php
}

add_action('Genesis_meta', 'custom_home_page_setup');
Genesis();
