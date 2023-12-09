<?php
/*
 * Template name: Search Page
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Maker Camp Theme
 */

get_header();
$searchQuery = isset($_GET['_sft_ld_lesson_category']) ? $_GET['_sft_ld_lesson_category'] : '';
?>

<div id="primary" class="content-area bb-grid-cell">
	<main id="main" class="site-main search-results-page">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-12">
					<h1>Maker Camp Project Library</h1>
					<?php dynamic_sidebar('sidebar-left'); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-12">
					<?php if($searchQuery) { ?>
						<h2>Search Results for: <?php echo $searchQuery ?></h2>
					<?php } ?>
					<?php if ( have_posts() ) :
						while ( have_posts() ) :
							the_post();
							the_content();

						endwhile; // End of the loop.
					endif; ?>
				</div>
			</div>
		</div>
	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
