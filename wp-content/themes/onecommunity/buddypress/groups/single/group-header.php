<?php
/**
 * BuddyPress - Groups Header
 *
 * @since 3.0.0
 * @version 3.2.0
 */
?>

<?php if ( ! bp_disable_group_avatar_uploads() ) : ?>
	<div id="item-header-avatar">
		<a href="<?php echo esc_url( bp_get_group_permalink() ); ?>" class="bp-tooltip" data-bp-tooltip="<?php echo esc_attr( bp_get_group_name() ); ?>">

			<?php bp_group_avatar(); ?>

		</a>
	</div><!-- #item-header-avatar -->
<?php endif; ?>




<div id="item-header-mobile">

<h2><?php $grouptitle = bp_get_group_name(); $getlength = strlen($grouptitle); $thelength = 22; echo mb_substr($grouptitle, 0, $thelength, 'UTF-8'); if ($getlength > $thelength) echo "..."; ?></h2>

	<div id="item-header-details">
	<?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
		<?php echo esc_html( bp_nouveau_the_group_meta( array( 'keys' => 'status' ) ); ?> <br>
	<?php endif; ?>
	Active
	<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_group_last_active( 0, array( 'relative' => false ) ) ); ?>">
		<?php
		echo esc_html(
			sprintf(
				/* translators: %s = last activity timestamp (e.g. "active 1 hour ago") */
				__( 'active %s', 'buddypress' ),
				bp_get_group_last_active()
			)
		);
		?>
	</span>

	<?php if ( bp_nouveau_group_has_meta_extra() ) : ?>
		<?php echo bp_nouveau_group_meta()->extra; ?>
	<?php endif; ?>

	</div>

</div><!-- #item-header-mobile -->




<div id="item-header-content">

	<h2><?php $grouptitle = bp_get_group_name(); $getlength = strlen($grouptitle); $thelength = 22; echo mb_substr($grouptitle, 0, $thelength, 'UTF-8'); if ($getlength > $thelength) echo "..."; ?></h2>

	<div id="item-header-details">
	<?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
		<?php echo esc_html( bp_nouveau_the_group_meta( array( 'keys' => 'status' ) ) ); ?> &bull;
	<?php endif; ?>
	Active
	<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_group_last_active( 0, array( 'relative' => false ) ) ); ?>">
		<?php
		echo esc_html(
			sprintf(
				/* translators: %s = last activity timestamp (e.g. "active 1 hour ago") */
				__( 'active %s', 'buddypress' ),
				bp_get_group_last_active()
			)
		);
		?>
	</span>

	<?php if ( bp_nouveau_group_has_meta_extra() ) : ?>
		<?php echo bp_nouveau_group_meta()->extra; ?>
	<?php endif; ?>

	</div>

	<?php bp_nouveau_group_hook( 'before', 'header_meta' ); ?>




		<?php if ( ! bp_nouveau_groups_front_page_description() && bp_nouveau_group_has_meta( 'description' ) ) : ?>
			<div class="group-description">
				<?php bp_group_description(); ?>
			</div><!-- //.group_description -->
		<?php endif; ?>

	<?php if ( is_user_logged_in() ) : ?>

		<?php bp_nouveau_group_header_buttons(); ?>

	<?php else : ?>
	<div id="groupbutton-5" class="generic-button">
		<button class="group-button login-popup-action"><?php _e( 'Join Group', 'onecommunity' ); ?></button>
	</div>
<?php endif; ?>

</div><!-- #item-header-content -->
