<?php
/**
 * BuddyPress - Blogs Directory
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>


<div id="page-header">

<section class="wrapper">

	<div class="breadcrumbs">
	<?php esc_attr_e('You are here:', 'onecommunity'); ?> <a href="<?php echo home_url(); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <span class="current"><?php the_title(); ?></span>
	</div>

	<h1 class="page-title half"><?php the_title(); ?></h1>

	<?php bp_nouveau_before_blogs_directory_content(); ?>

	<?php if ( ! bp_nouveau_is_object_nav_in_sidebar() ) : ?>

		<?php bp_get_template_part( 'common/nav/directory-nav' ); ?>

	<?php endif; ?>

</section><!-- .wrapper -->

</div>


<section class="wrapper">

	<div class="screen-content">

	<?php bp_get_template_part( 'common/search-and-filters-bar' ); ?>

		<div id="blogs-dir-list" class="blogs dir-list" data-bp-list="blogs">
			<div id="bp-ajax-loader"><?php bp_nouveau_user_feedback( 'directory-blogs-loading' ); ?></div>
		</div><!-- #blogs-dir-list -->

		<?php bp_nouveau_after_blogs_directory_content(); ?>
	</div><!-- // .screen-content -->

</section>