<?php
/*
 * Template name: No Sidebar
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package BuddyBoss_Theme
 */

get_header();
?>

<div id="primary" class="content-area bb-grid-cell">
	<main id="main" class="site-main">

		<?php if ( have_posts() ) :

			while ( have_posts() ) :
				the_post();

			endwhile; // End of the loop.
		else :
			get_template_part( 'template-parts/content', 'none' );
			?>

		<?php endif; ?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
