<?php
/**
 * Template name: Login Required
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Maker Camp Theme
 */
get_header();
?>

<div id="primary" class="content-area bb-grid-cell">
	<main id="main" class="site-main">
		<?php 
		if( is_user_logged_in() ) {

			if ( have_posts() ) :

				do_action( THEME_HOOK_PREFIX . '_template_parts_content_top' );

				while ( have_posts() ) :
					the_post();
					do_action( THEME_HOOK_PREFIX . '_single_template_part_content', 'page' );
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

				endwhile; // End of the loop.
			else :
				get_template_part( 'template-parts/content', 'none' );

			endif;
		
		} else { // if not logged in ?>
			<div class="logged-out-wrapper">
				<div class="logged-out-message"><?php echo get_theme_mod( 'logged_out_message' ); ?></div>
			</div>
		<?php } ?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
