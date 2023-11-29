<?php get_header(); ?>

<section class="wrapper">

<main id="content" class="<?php if( !is_active_sidebar('sidebar-single') && !is_buddypress() ) { ?>no-sidebar<?php } ?>">

	<?php
	while ( have_posts() ) : the_post(); ?>

	<h1 class="single-post-title"><?php the_title(); ?></h1>

	<?php
	if ( shortcode_exists( 'wp_ulike' ) ) {
		echo do_shortcode('[wp_ulike]');
	} ?>

	<div class="clear"></div>



	<?php
	if ( has_post_thumbnail() ) { ?>
	<div class="thumbnail">
		<?php the_post_thumbnail('post-thumbnail-2');
		dd_the_post_thumbnail_caption(); ?>
	</div>
	<?php } else {
	// no thumbnail
	}
	?>

	<div class="clear"></div>

	<article>

		<?php 
		$mepr_user = new MeprUser( get_current_user_id() );
		if ($mepr_user->is_active()) {
			the_content( esc_attr__('Read more','onecommunity') );
		} else { ?>
				<div class="excerpt-wrapper">
			<?php
				the_excerpt();
			?>
				</div>
				<div class="paywall-message">
					<span>To read the Full Story</span>
					<a href="/join" class="btn universal-btn">Subscribe</a>
					<a href="/wp-login.php" class="btn universal-btn login-btn">Sign In</a>
				</div>
				<div class="paywall-wrapper">
					<h4 class="paywall-header">Continue reading this article as a Community user</h4>
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/cropped-make-community-logo.png" width="250px" height="105px" />
					<!--<div class="paywall-text">or become a Premium User to get access to exclusive videos, archived magazine content and more.</div>-->
					<a href="/join" class="btn universal-btn">Learn More</a>
				</div>
			<?php
		}

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
		if ($mepr_user->is_active()) {
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
		}
		?>

</main><!-- content -->

<?php if( is_active_sidebar('sidebar-single') ) { ?>
	
<div id="sidebar-spacer"></div>

<aside id="sidebar" class="sidebar">

	<?php
	$transient = get_transient( 'onecommunity_sidebar_single' );
	if ( false === $transient || !get_theme_mod( 'onecommunity_transient_sidebar_single_enable', 0 ) == 1 ) {
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

<?php } ?>

</section><!-- .wrapper -->



<?php get_footer(); ?>