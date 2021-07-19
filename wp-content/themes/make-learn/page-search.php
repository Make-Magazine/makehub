<?php
/*
 * Template name: Search Page
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Maker Camp Theme
 */

get_header();
$searchQuery = isset($_GET['_sf_s']) ? $_GET['_sf_s'] : '';
?>

<div id="primary" class="content-area bb-grid-cell">
	<main id="main" class="site-main search-results-page">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-8">
					<h1>Search Results for: <?php echo $searchQuery ?></h1>
					<?php if ( have_posts() ) :

						do_action( THEME_HOOK_PREFIX . '_template_parts_content_top' );

						while ( have_posts() ) :
							the_post();
							do_action( THEME_HOOK_PREFIX . '_single_template_part_content', 'page' );

						endwhile; // End of the loop.
					else :
						get_template_part( 'template-parts/content', 'none' );
						?>

					<?php endif; ?>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-4">
					<?php dynamic_sidebar('search'); ?>
				</div>
			</div>
		</div>
	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
