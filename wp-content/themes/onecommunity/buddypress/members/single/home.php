<?php
/**
 * BuddyPress - Members Home
 *
 * @since   1.0.0
 * @version 3.0.0
 */
?>


<section class="wrapper">

<main id="content" class="bp-profile">

	<div class="breadcrumbs<?php if ( bp_displayed_user_use_cover_image_header() ) { echo " has-bp-cover"; } ?>">
		<?php esc_attr_e('You are here:', 'onecommunity'); ?> <a href="<?php echo home_url(); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <a href="<?php echo home_url(); ?>/members"><?php esc_attr_e('Members', 'onecommunity'); ?></a> / <span class="current"><?php the_title(); ?></span>
	</div>

	<?php bp_nouveau_member_hook( 'before', 'home_content' ); ?>

	<div id="item-header" role="complementary" data-bp-item-id="<?php echo esc_attr( bp_displayed_user_id() ); ?>" data-bp-item-component="members" class="users-header single-headers<?php if ( bp_group_use_cover_image_header() ) { echo " has-bp-cover"; } ?>">

		<?php bp_nouveau_member_header_template_part(); ?>

	</div><!-- #item-header -->

	<div class="clear"></div>

	<div class="bp-wrap">

		<?php if ( ! bp_nouveau_is_object_nav_in_sidebar() ) : ?>

				<?php bp_get_template_part( 'members/single/parts/item-nav' ); ?>

		<?php endif; ?>


		<div class="<?php if ( get_theme_mod( 'onecommunity_profile_columns', 0 ) == 3 ) { ?>layout-col-3<?php } ?>">

		<?php if ( get_theme_mod( 'onecommunity_profile_columns', 0 ) == 3 ) { ?>

			<button id="left-sidebar-trigger"></button>
			<?php bp_get_template_part( 'members/single/member-sidebar-left' ); ?>

		<?php } ?>

			<div id="item-body" class="item-body">

				<?php bp_nouveau_member_template_part(); ?>

			</div><!-- #item-body -->

		</div><!--layout-col-3 -->

	</div><!-- // .bp-wrap -->

	<?php bp_nouveau_member_hook( 'after', 'home_content' ); ?>

</main><!-- content -->

<?php bp_get_template_part( 'members/single/profile-sidebar' ); ?>

</section><!-- .wrapper -->
