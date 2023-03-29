<?php 
/*
Template Name: Default page with sidebar
*/
get_header();
?>
	<section class="wrapper">
		<main id="content" class="<?php if( !is_active_sidebar('sidebar-pages') ) { ?>no-sidebar<?php } ?>">

			<?php 
			$body_classes = get_body_class();
			while ( have_posts() ) : the_post(); 
				if(!in_array('learnpress-page', $body_classes)) { ?>
				    <h1 class="page-title"><?php
						$thetitle = get_the_title();
						$getlength = strlen($thetitle);
						$thelength = 35;
						echo substr($thetitle, 0, $thelength);
						if ($getlength > $thelength) echo "...";
				?>	</h1> <?php
				}

				if(!in_array('learnpress-page', $body_classes)) {
					if ( has_post_thumbnail() ) { ?>
						<div class="thumbnail">
							<?php
							the_post_thumbnail('post-thumbnail-2');
							dd_the_post_thumbnail_caption();
					} else {
						// no thumbnail
					}
				}
				?>

				<div class="clear"></div>

				<article>
					<?php
					the_content();

					wp_link_pages( array(
						'before'      => '<div class="page-links"><span class="page-links-title">' . esc_attr__( 'Pages:', 'onecommunity' ) . '</span>',
						'after'       => '</div>',
						'link_before' => '<span>',
						'link_after'  => '</span>',
						'pagelink'    => '<span class="screen-reader-text">' . esc_attr__( 'Page', 'onecommunity' ) . ' </span>%',
						'separator'   => '<span class="screen-reader-text">, </span>',
					) );
					?>
				</article>

				<?php
				if ( class_exists( 'LearnPress' ) ) {
					if ( !learn_press_is_course_tag() ) {
						if ( comments_open() || get_comments_number() ) {
							comments_template();
						}
					}
				} else {
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
				}

		// End of the loop.
		endwhile;
		?>

	</main><!-- content -->

	<?php if( is_active_sidebar('sidebar-pages') ) { ?>

		<?php if(in_array('learnpress-page', $body_classes)) { ?>

			<aside id="sidebar" class="sidebar">

				<?php
				$transient = get_transient( 'onecommunity_sidebar_learnpress' );
				if ( false === $transient || !get_theme_mod( 'onecommunity_transient_sidebar_learnpress_enable', 0 ) == 1 ) {
				ob_start();

				if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-learnpress')) : endif;

				$sidebar = ob_get_clean();
				print_r( $sidebar );
				
				if ( get_theme_mod( 'onecommunity_transient_sidebar_learnpress_enable', 0 ) == 1 ) {
					set_transient( 'onecommunity_sidebar_learnpress', $sidebar, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_transient_sidebar_learnpress_expiration', 1440 ) );
				}

				} else {
					echo '<!-- Transient onecommunity_sidebar_learnpress ('.get_theme_mod( 'onecommunity_transient_sidebar_learnpress_expiration', 1440 ).' min) -->';
					print_r( $transient );
				}
				?>

			</aside><!--sidebar ends-->

		<?php } else {
			get_sidebar();
		} ?>

		<div id="sidebar-spacer"></div>

	<?php } ?>


</section><!-- .wrapper -->
<?php get_footer(); ?>
