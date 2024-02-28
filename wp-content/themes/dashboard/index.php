<?php get_header();  ?>

<div class="clear"></div>

<div class="container">

	<div class="row">

		<div class="content col-md-8">

			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<article <?php post_class(); ?>>
			
					<?php the_content(); ?>

					<div class="clear"></div>

				</article>

			<?php endwhile; ?>
			
			<?php endif; ?>

		</div><!--Content-->
		

	</div>

</div><!--Container-->

<?php get_footer(); ?>
