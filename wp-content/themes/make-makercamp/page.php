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
 */
get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <?php
        if (have_posts()) :

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
