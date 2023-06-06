<?php
/**
 * BuddyPress - Members Loop
 *
 * @since 3.0.0
 * @version 3.0.0
 */

bp_nouveau_before_loop(); ?>

<?php if ( bp_get_current_member_type() ) : ?>
	<p class="current-member-type"><?php bp_current_member_type_message(); ?></p>
<?php endif; ?>

<?php if ( bp_has_members( bp_ajax_querystring( 'members' ) . '&per_page=12' ) ) : ?>

	<ul id="members-list" class="<?php bp_nouveau_loop_classes(); ?>">

	<?php while ( bp_members() ) : bp_the_member(); ?>

		<li <?php bp_member_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php bp_member_user_id(); ?>" data-bp-item-component="members">
			<div class="list-wrap">

				<div class="item-avatar">
					<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar( bp_nouveau_avatar_args() ); ?></a>
				</div>

				<div class="item">

					<div class="item-block">

						<h2 class="list-title member-name">
							<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?> <span class="online-status" title="<?php bp_nouveau_member_meta(); ?>"></span></a>
						</h2>


						<?php $profile_field = bp_get_member_profile_data( 'field=Location' );
						if( $profile_field ) { ?>
						<div class="member-location">
							<?php echo esc_attr( $profile_field ) ?>
						</div>
						<?php } ?>

					</div>

					<?php if ( bp_get_member_latest_update() && ! bp_nouveau_loop_is_grid() ) : ?>
					<div class="user-update">
						<p class="update"> <?php bp_member_latest_update('length=68'); ?></p>
					</div>
					<?php endif; ?>

					<?php bp_nouveau_members_loop_buttons(
							array(
								'container'      => 'ul',
								'button_element' => 'button',
							)
						);
					?>

				</div><!-- // .item -->



			</div>
		</li>

	<?php endwhile; ?>

	</ul>


	<?php bp_nouveau_pagination( 'bottom' ); ?>

<?php
else :

	bp_nouveau_user_feedback( 'members-loop-none' );

endif;
?>

<?php bp_nouveau_after_loop(); ?>
