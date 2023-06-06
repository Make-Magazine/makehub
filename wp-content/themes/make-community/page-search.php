<?php
/*
 * Template name: Search Page
 */

get_header();
?>

<div id="primary" class="content-area bb-grid-cell">
	<main id="main" class="site-main search-results-page">
		<div class="container-fluid">
				<?php if ( have_posts() ) :
						the_content();
					?>

				<?php endif; ?>
		</div>
	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
