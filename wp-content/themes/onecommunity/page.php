<?php get_header();
/*
Template Name: Default page with sidebar
*/
?>

<?php
global $wp_query;
while ( have_posts() ) : the_post(); ?>

<section class="wrapper">

<main id="content" class="<?php if( !is_active_sidebar('sidebar-pages') ) { ?>no-sidebar<?php } ?>">

	<div class="breadcrumbs">

	<?php
	$home = home_url();
	$body_classes = get_body_class();
	if(in_array('learnpress-page', $body_classes))
	{
		if ( learn_press_is_course_tag() ) {

			echo esc_attr_e('You are here:', 'onecommunity'); ?> <a href="<?php esc_url($home); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <?php

			$post = get_post( get_option('learn_press_courses_page_id') );
			echo "<a href='" . $home . '/' . $post->post_name . "'>" . $post->post_title . "</a>";
			echo " / ";
			global $wp;
			$url = home_url( $wp->request );
			$slug = get_option( "learn_press_course_tag_base" );
			$output = array_pop(explode($slug, $url));
			$output = str_replace("/", "", $output);
			echo '<span class="current">' . ucfirst($output) . '</span>';

		} elseif ( learn_press_is_course_category() ) {

			esc_attr_e('You are here:', 'onecommunity'); ?> <a href="<?php esc_url($home); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <?php

			$post = get_post( get_option('learn_press_courses_page_id') );
			echo "<a href='" . $home . '/' . $post->post_name . "'>" . $post->post_title . "</a>";
			echo " / ";
			global $wp;
			$url = home_url( $wp->request );
			$slug = get_option( "learn_press_course_category_base" );
			$output = explode($slug, $url);
			$output = array_pop($output);
			$output = str_replace("/", "", $output);
			echo '<span class="current">' . ucfirst($output) . '</span>';

 	   } else {
 	   	 do_action( 'learn-press/breadcrumbs' );
 	   }

	} else { ?>
	    <?php esc_attr_e('You are here:', 'onecommunity'); ?> <a href="<?php esc_url($home); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <span class="current"><?php the_title(); ?></span>
	<?php } ?>

	</div>

	<?php if(!in_array('learnpress-page', $body_classes)) { ?>
	    <h1 class="page-title"><?php
		$thetitle = get_the_title();
		$getlength = strlen($thetitle);
		$thelength = 35;
		echo substr($thetitle, 0, $thelength);
		if ($getlength > $thelength) echo "...";
	?></h1>
	<?php
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
