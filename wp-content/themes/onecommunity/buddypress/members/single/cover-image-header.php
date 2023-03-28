<?php
/**
 * BuddyPress - Users Cover Image Header
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<?php // Get the Cover Image
    $cover_url = bp_attachments_get_attachment('url', array(
        'object_dir' => 'members',
        'item_id' => bp_displayed_user_id(),
    ));

    if (!$cover_url) {
        $cover_url = get_template_directory_uri().'/buddypress/img/cover-user.jpg';
    }
?>

<div id="cover" style="background-image:url('<?php echo esc_url($cover_url); ?>');">

	<?php
	//if ( bp_is_active( 'activity' ) {
		$member_update = bp_get_activity_latest_update( bp_displayed_user_id() );
		if ( $member_update ) { ?>
		<div id="latest-update">
			<?php print_r( $member_update ); ?>
		</div>
		<?php 
		}
	//} 
	?>


<div id="item-header-wrappper">

	<div id="item-header-avatar">
		<a href="<?php bp_displayed_user_link(); ?>">
			<?php bp_displayed_user_avatar( 'type=full' ); ?>
		</a>

		<?php if ( shortcode_exists( 'gamipress_user_rank' ) ) { ?>
		<div class="item-header-badges">
			<?php echo do_shortcode( '[gamipress_user_rank type="general-rank" prev_rank="no" title="no" link="no" excerpt="no" toggle="no" requirements="no" unlock_button="no" layout="none" current_rank="yes" next_rank="no" current_user="no" user_id="' . bp_displayed_user_id() . '"]' ); ?>
		</div>
		<?php } ?>

	</div><!-- #item-header-avatar -->


	<div id="item-header-content">

	<?php if ( shortcode_exists( 'gamipress_achievements' ) ) { ?><?php echo do_shortcode( '[gamipress_achievements type="all" filter="no" search="no" title="no" points_awarded="no" excerpt="no" times_earned="no" steps="no" toggle="no" unlock_button="unlock_button" layout="none" current_user="no" user_id="' . bp_displayed_user_id() . '"]' ); ?><?php } ?>

	<h2><a href="<?php bp_displayed_user_link(); ?>"><?php bp_displayed_user_fullname(); ?></a></h2>

	<div id="item-header-details">

		<?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
			<span class="user-nicename">@<?php bp_displayed_user_mentionname(); ?></span> &bull; 
		<?php endif; ?>
		<?php bp_nouveau_member_meta(); ?>

		<?php if ( shortcode_exists( 'gamipress_points' ) ) { ?> &bull; <span class="profile-balance"><?php echo do_shortcode( '[gamipress_points type="all" thumbnail="no" label="yes" inline="yes" layout="none" user_id="' . bp_displayed_user_id() . '"]' ); ?></span><?php } ?>

	</div>

	<?php bp_nouveau_member_hook( 'before', 'header_meta' ); ?>

	<?php bp_nouveau_member_header_buttons( array( 'container_classes' => array( 'member-header-actions' ) ) ); ?>

	<?php if ( !is_user_logged_in() ) { ?>

	<div class="member-header-actions action">
		<button class="generic-button login-popup-action"><?php _e( 'Add Friend', 'buddypress' ); ?></button>
		<button class="generic-button login-popup-action"><?php _e( 'Private Message', 'buddypress' ); ?></button>
	</div>

	<?php } ?>

	</div><!-- #item-header-content -->

</div><!-- #item-header-wrapper -->

</div><!-- #cover -->
