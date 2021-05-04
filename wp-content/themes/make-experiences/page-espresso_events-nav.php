<?php
/**
 * Template Name: Events Top Navigation
 */

get_header();

?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">
		
		<?php if ( have_posts() ) : ?>
				<div class="event-view-btns">
					<a href="/maker-campus" title="Grid View"><i class="fas fa-th-large"></i></a>
					<a href="/maker-campus/event-list" title="List View"><i class="fas fa-th-list"></i></a>
				</div>
		<?php
			do_action( THEME_HOOK_PREFIX . '_template_parts_content_top' );

			while ( have_posts() ) :
				the_post();
				do_action( THEME_HOOK_PREFIX . '_single_template_part_content', 'page' );

			endwhile; // End of the loop.
		else :
			get_template_part( 'template-parts/content', 'none' );
			?>

		<?php endif; ?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
