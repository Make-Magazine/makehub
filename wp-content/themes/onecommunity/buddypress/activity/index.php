<?php
/**
 * BuddyPress Activity templates
 *
 * @since 2.3.0
 * @version 3.0.0
 */
?>

<section class="wrapper<?php if ( get_theme_mod( 'onecommunity_activity_columns', 0 ) == 3 ) { ?> layout-col-3<?php } ?>">

<?php if ( get_theme_mod( 'onecommunity_activity_columns', 0 ) == 3 ) { ?>

<button id="left-sidebar-trigger"></button>

<aside id="left-sidebar" class="sidebar sidebar-small">
	<button id="left-sidebar-close"></button>

	<?php
	$transient = get_transient( 'onecommunity_sidebar_left' );
	if ( false === $transient OR !get_theme_mod( 'onecommunity_transient_sidebar_left_enable', 0 ) == 1 ) {
	ob_start();
	 if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-left')) : ?><?php endif;

	$sidebar_left = ob_get_clean();
	print_r( $sidebar_left );

	if ( get_theme_mod( 'onecommunity_transient_sidebar_left_enable', 0 ) == 1 ) {
		set_transient( 'onecommunity_sidebar_left', $sidebar_left, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_transient_sidebar_left_expiration', 20 ) );
	}

	} else {
		echo '<!-- Transient onecommunity_sidebar_left ('.get_theme_mod( 'onecommunity_transient_sidebar_left_expiration', 20 ).' min) -->';
		print_r( $transient );
	}
	?>

</aside>

<div id="sidebar-spacer-left"></div>

<?php } ?>

<main id="content">

	<div class="breadcrumbs">
	<?php esc_attr_e('You are here:', 'onecommunity'); ?> <a href="<?php echo home_url(); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <span class="current"><?php the_title(); ?></span>
	</div>

	<?php bp_nouveau_before_activity_directory_content(); ?>

	<?php if ( is_user_logged_in() ) : ?>

		<?php bp_get_template_part( 'activity/post-form' ); ?>

	<?php endif; ?>

	<?php bp_nouveau_template_notices(); ?>

	<?php if ( ! bp_nouveau_is_object_nav_in_sidebar() ) : ?>

		<?php bp_get_template_part( 'common/nav/directory-nav' ); ?>

	<?php endif; ?>

	<div class="screen-content">

		<?php bp_get_template_part( 'common/search-and-filters-bar' ); ?>

		<?php bp_nouveau_activity_hook( 'before_directory', 'list' ); ?>

		<div id="activity-stream" class="activity" data-bp-list="activity">

				<div id="bp-ajax-loader"><?php bp_nouveau_user_feedback( 'directory-activity-loading' ); ?></div>

		</div><!-- .activity -->

		<?php bp_nouveau_after_activity_directory_content(); ?>

	</div><!-- // .screen-content -->

</main><!-- content -->

<?php get_sidebar(); ?>

<div id="sidebar-spacer"></div>

</section><!-- wrapper -->
