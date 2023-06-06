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

	<?php bp_nouveau_before_groups_directory_content(); ?>

	<?php bp_nouveau_template_notices(); ?>

	<div class="screen-content">

	<?php bp_get_template_part( 'common/search-and-filters-bar' ); ?>

	<div class="clear"></div>

		<div id="groups-dir-list" class="groups dir-list" data-bp-list="groups">
			<div id="bp-ajax-loader"><?php bp_nouveau_user_feedback( 'directory-groups-loading' ); ?></div>
		</div><!-- #groups-dir-list -->

	<?php bp_nouveau_after_groups_directory_content(); ?>
	</div><!-- // .screen-content -->

</section><!-- .wrapper -->