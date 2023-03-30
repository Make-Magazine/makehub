<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package BuddyBoss_Theme
 */
get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <?php
        if (have_posts()) :

            do_action(THEME_HOOK_PREFIX . '_template_parts_content_top');

            //while (have_posts()) :
                the_post();
                // Are there custom panels to display?
                if (have_rows('content_panels')) {
                    // loop through the rows of data
                    echo '<div class="customPanels">';
                    while (have_rows('content_panels')) {
                        the_row();
                        $row_layout = get_row_layout();
                        echo dispLayout($row_layout);
                    }
                    echo '</div>';
                }

                do_action(THEME_HOOK_PREFIX . '_single_template_part_content', 'page');

            //endwhile; // End of the loop.
        else :
            get_template_part('template-parts/content', 'none');
            ?>

        <?php endif; ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
if (is_search()) {
    get_sidebar('search');
} else {
    get_sidebar('page');
}
?>

<?php
get_footer();
