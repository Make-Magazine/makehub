<?php get_header(); ?>

<div class="clear"></div>
<?php if( get_field('hero_image') ) { ?>
	<header class="hero">
		<section class="container-fluid full-width-div header-hero" style="background-image:url('<?php echo get_field('hero_image')['url'] ?>');">  
			<div class="hero-wrapper">
				<h1><?php the_title(); ?></h1>
				<div class="separator"></div>
				<?php if( get_field('subheader') ) { ?>
					<h2><?php echo get_field('subheader'); ?></h2>
				<?php } ?>
			</div>
		</section>
	</header>
<?php } else { ?>
	<h1><?php the_title(); ?></h1>
<?php } ?>

<div class="container-fluid">
	<div class="row">
		<div class="content col-md-12">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?>>
					<?php the_content(); ?>
				</article>
			<?php endwhile; ?>			
			<?php else: ?>
				<?php get_404_template(); ?>
			<?php endif; ?>
		</div><!--Content-->
	</div>
</div><!--Container-->

<?php if( get_field('scroll_to_title_button') == TRUE ) { ?>
	<button id="scrollToTop"><i class="fas fa-caret-up"></i></button>
<?php } ?>

<?php get_footer(); ?>