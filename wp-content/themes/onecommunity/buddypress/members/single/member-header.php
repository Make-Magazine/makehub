<?php
/**
 * BuddyPress - Users Header
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

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


<!-- ********************************* MOBILE ***********************************- -->
<div id="item-header-mobile">

	<h2><a href="<?php bp_displayed_user_link(); ?>"><?php bp_displayed_user_fullname(); ?></a></h2>

	<div id="item-header-details">
	<?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
		<span class="user-nicename">@<?php bp_displayed_user_mentionname(); ?></span><br>
	<?php endif; ?>
	<?php bp_nouveau_member_meta(); ?>
	<?php if ( shortcode_exists( 'gamipress_points' ) ) { ?> <br><span class="profile-balance"><?php echo do_shortcode( '[gamipress_points type="all" thumbnail="no" label="yes" inline="yes" layout="none" user_id="' . bp_displayed_user_id() . '"]' ); ?></span><?php } ?>
	</div>

	<?php if ( shortcode_exists( 'gamipress_achievements' ) ) { ?><?php echo do_shortcode( '[gamipress_achievements type="all" filter="no" search="no" title="no" points_awarded="no" excerpt="no" times_earned="no" steps="no" toggle="no" unlock_button="unlock_button" layout="none" current_user="no" user_id="' . bp_displayed_user_id() . '"]' ); ?><?php } ?>

</div><!-- #item-header-mobile -->
<!-- ********************************* MOBILE ***********************************- -->


<div id="item-header-content">

	<h2><a href="<?php bp_displayed_user_link(); ?>"><?php bp_displayed_user_fullname(); ?></a></h2>

	<div id="item-header-details">
	<?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
		<span class="user-nicename">@<?php bp_displayed_user_mentionname(); ?></span> &bull; 
	<?php endif; ?>
	<?php bp_nouveau_member_meta(); ?>

	<?php if ( shortcode_exists( 'gamipress_points' ) ) { ?> &bull; <span class="profile-balance"><?php echo do_shortcode( '[gamipress_points type="all" thumbnail="no" label="yes" inline="yes" layout="none" user_id="' . bp_displayed_user_id() . '"]' ); ?></span><?php } ?>
	
	</div>

	<div id="latest-update">
		<?php bp_activity_latest_update( bp_displayed_user_id() ); ?>
	</div>

	<?php bp_nouveau_member_hook( 'before', 'header_meta' ); ?>


	<?php bp_nouveau_member_header_buttons( array( 'container_classes' => array( 'member-header-actions' ) ) ); ?>

	<?php if ( shortcode_exists( 'gamipress_achievements' ) ) { ?><?php echo do_shortcode( '[gamipress_achievements type="all" filter="no" search="no" title="no" points_awarded="no" excerpt="no" times_earned="no" steps="no" toggle="no" unlock_button="unlock_button" layout="none" current_user="no" user_id="' . bp_displayed_user_id() . '"]' ); ?><?php } ?>

	<?php if ( !is_user_logged_in() ) { ?>

	<div class="member-header-actions action">
		<button class="generic-button login-popup-action"><?php _e( 'Add Friend', 'buddypress' ); ?></button>
		<button class="generic-button login-popup-action"><?php _e( 'Private Message', 'buddypress' ); ?></button>
	</div>

<?php } ?>

</div><!-- #item-header-content -->
