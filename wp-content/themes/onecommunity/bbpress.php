<?php get_header(); ?>

<section class="wrapper">

<main id="content" class="bbpress-page">

	<div class="breadcrumbs">
	<?php bbp_breadcrumb(); ?>
	</div>

	<h1 class="page-title"><?php
		$thetitle = get_the_title();
		$getlength = strlen($thetitle);
		$thelength = 100;
		echo substr($thetitle, 0, $thelength);
		if ($getlength > $thelength) echo "...";
	?></h1>

		<?php
		// Start the loop.
		while ( have_posts() ) : the_post();

			// Include the page content template.
			get_template_part( 'template-parts/content', 'page' );

			// End of the loop.
		endwhile;
		?>

</main><!-- .content -->

<?php get_sidebar(); ?>

<div id="sidebar-spacer"></div>

</section><!-- .wrapper -->
<?php get_footer(); ?>