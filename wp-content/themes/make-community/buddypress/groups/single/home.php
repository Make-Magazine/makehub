<section class="wrapper">

<main id="content" class="bp-group" style="width:100%;">

	<div class="breadcrumbs<?php if ( bp_group_use_cover_image_header() ) { echo " has-bp-cover"; } ?>">
		<?php esc_attr_e('You are here:', 'onecommunity'); ?> <a href="<?php echo home_url(); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <a href="<?php echo home_url(); ?>/groups"><?php esc_attr_e('Groups', 'onecommunity'); ?></a> / <span class="current"><?php the_title(); ?></span>
	</div>

<?php
if ( bp_has_groups() ) :
	while ( bp_groups() ) :
		bp_the_group();
	?>

		<?php bp_nouveau_group_hook( 'before', 'home_content' ); ?>

		<div id="item-header" role="complementary" data-bp-item-id="<?php bp_group_id(); ?>" data-bp-item-component="groups" class="groups-header single-headers<?php if ( bp_group_use_cover_image_header() ) { echo " has-bp-cover"; } ?>">

			<?php bp_nouveau_group_header_template_part(); ?>

		</div><!-- #item-header -->

		<div class="bp-wrap">

			<?php if ( ! bp_nouveau_is_object_nav_in_sidebar() ) : ?>

				<?php bp_get_template_part( 'groups/single/parts/item-nav' ); ?>

			<?php endif; ?>

			<div id="item-body" class="item-body">

				<?php bp_nouveau_group_template_part(); ?>

			</div><!-- #item-body -->

		</div><!-- // .bp-wrap -->

		<?php bp_nouveau_group_hook( 'after', 'home_content' ); ?>

	<?php endwhile; ?>

<?php
endif;
?>

</main><!-- content -->

</section><!-- .wrapper -->