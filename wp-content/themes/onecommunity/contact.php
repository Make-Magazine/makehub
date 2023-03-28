<?php
/*
Template Name: Contact Form
*/
?>

<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<section class="wrapper">

<main id="content">

	<div class="breadcrumbs">
	<a href="<?php echo home_url(); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <span class="current"><?php the_title(); ?></span>
	</div>

	<h1 class="page-title"><?php
		$thetitle = get_the_title();
		$getlength = strlen($thetitle);
		$thelength = 35;
		echo substr($thetitle, 0, $thelength);
		if ($getlength > $thelength) echo "...";
	?></h1>


	<?php

			// Include the page content template.
			get_template_part( 'template-parts/content', 'page' );

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}

			// End of the loop.
		endwhile;
		?>

	</main><!-- content -->

<aside id="sidebar" class="sidebar">
	<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-contact')) : ?><?php endif; ?>
</aside><!--sidebar ends-->

<div id="sidebar-spacer"></div>

<div class="clear"></div>

</section><!-- wrapper -->

<?php get_footer(); ?>
