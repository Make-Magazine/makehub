<?php
/*
 * Template name: No Sidebar
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 */

get_header();
?>

<div id="primary" class="content-area bb-grid-cell">
	<main id="main" class="site-main">

		<?php if ( have_posts() ) :

			while ( have_posts() ) :
				the_post();

			endwhile; // End of the loop. d
			?>

		<?php endif; ?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
