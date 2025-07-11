<div id="page-header">
<section class="wrapper">

	<div class="breadcrumbs">
		<?php esc_attr_e('You are here:', 'onecommunity'); ?> <a href="<?php echo home_url(); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <span class="current"><?php the_title(); ?></span>
	</div>

	<h1 class="page-title half"><?php the_title(); ?></h1>

	<?php bp_get_template_part( 'common/nav/directory-nav' ); ?>

</section><!-- .wrapper -->
</div>

<section class="wrapper">

	<?php bp_nouveau_before_members_directory_content(); ?>

	<div class="screen-content">

	<?php bp_get_template_part( 'common/search-and-filters-bar' ); ?>

	<div class="clear"></div>

		<div id="members-dir-list" class="members dir-list" data-bp-list="members">
			<div id="bp-ajax-loader"><?php bp_nouveau_user_feedback( 'directory-members-loading' ); ?></div>
		</div><!-- #members-dir-list -->

		<?php bp_nouveau_after_members_directory_content(); ?>
	</div><!-- // .screen-content -->

</section><!-- .wrapper -->