<?php get_header(); ?>

<section class="wrapper">

<main id="content">

	<?php
	while ( have_posts() ) : the_post(); ?>

	<div class="breadcrumbs">
	<?php esc_attr_e('You are here:', 'onecommunity'); ?> <a href="<?php echo home_url(); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <span class="current"><?php the_title(); ?></span>
	</div>

	<h1 class="single-post-title"><?php the_title(); ?></h1>

	<div class="single-post-details">

	<span class="single-blog-comments"><?php comments_number('0', '1', '%'); ?></span>

	<?php
	if ( shortcode_exists( 'wp_ulike' ) ) {
		echo do_shortcode('[wp_ulike]');
	} ?>

	<div class="clear"></div>

	</div>

	<?php
	if ( has_post_thumbnail() ) { ?>
	<div class="thumbnail thumbnail-wide">
		<?php the_post_thumbnail('post-thumbnail-2');
		dd_the_post_thumbnail_caption(); ?>
	</div>
	<?php } else {
	// no thumbnail
	}
	?>

	<div class="clear"></div>

	<article class="event-location">

		<?php the_content( esc_attr__('Read more','onecommunity') );

			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . esc_attr__( 'Pages:', 'onecommunity' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . esc_attr__( 'Page', 'onecommunity' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text">, </span>',
			) );

		?>

    <div class="clear"></div>

 		<?php

			if ( is_singular( 'attachment' ) ) {
				// Parent post navigation.
				the_post_navigation( array(
					'prev_text' => _x( '<span class="meta-nav">Published in</span><span class="post-title">%title</span>', 'Parent post link', 'onecommunity' ),
				) );
			} elseif ( is_singular( 'post' ) ) {
				// Previous/next post navigation.
				the_post_navigation( array(
					'next_text' => '<span class="meta-nav" aria-hidden="true">' . esc_attr__( 'Next', 'onecommunity' ) . '</span> ' .
						'<span class="screen-reader-text">' . esc_attr__( 'Next post:', 'onecommunity' ) . '</span> ' .
						'<span class="post-title">%title</span>',
					'prev_text' => '<span class="meta-nav" aria-hidden="true">' . esc_attr__( 'Previous', 'onecommunity' ) . '</span> ' .
						'<span class="screen-reader-text">' . esc_attr__( 'Previous post:', 'onecommunity' ) . '</span> ' .
						'<span class="post-title">%title</span>',
				) );
			}

			// End of the loop.
		endwhile;
		?>

    <div class="clear"></div>

		</article>

		<?php
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
		?>

</main><!-- content -->

<div id="sidebar-spacer"></div>

<aside id="sidebar" class="sidebar">

	<?php
	$transient = get_transient( 'onecommunity_sidebar_single' );
	if ( false === $transient OR !get_theme_mod( 'onecommunity_transient_sidebar_single_enable', 0 ) == 1 ) {
	ob_start();
	if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-single')) : endif;

	$sidebar = ob_get_clean();
	print_r($sidebar);

	if ( get_theme_mod( 'onecommunity_transient_sidebar_single_enable', 0 ) == 1 ) {
		set_transient( 'onecommunity_sidebar_single', $sidebar, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_transient_sidebar_pages_expiration', 20 ) );
	}

	} else {
		echo '<!-- Transient onecommunity_sidebar_single ('.get_theme_mod( 'onecommunity_transient_sidebar_pages_expiration', 20 ).' min) -->';
		print_r( $transient );
	}
	?>

</aside><!--sidebar ends-->

</section><!-- .wrapper -->


<?php get_footer(); ?>