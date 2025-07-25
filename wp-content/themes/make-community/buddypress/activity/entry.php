<?php
/**
 * BuddyPress - Activity Stream (Single Item)
 *
 * This template is used by activity-loop.php and AJAX functions to show
 * each activity.
 *
 * @since 3.0.0
 * @version 3.0.0
 */

bp_nouveau_activity_hook( 'before', 'entry' ); ?>

<li class="<?php bp_activity_css_class(); ?>" id="activity-<?php bp_activity_id(); ?>" <?php bp_nouveau_activity_data_attribute_id(); ?> data-bp-timestamp="<?php bp_nouveau_activity_timestamp(); ?>">

	<div class="activity-avatar item-avatar">

		<a href="<?php bp_activity_user_link(); ?>">

			<?php bp_activity_avatar( array( 'type' => 'thumb' ) ); ?>

		</a>

	</div>

	<div class="activity-header"><?php bp_activity_action(); ?></div>

	<div class="clear"></div>
		<div class="activity-content">

			<?php if ( bp_nouveau_activity_has_content() ) : ?>

				<div class="activity-inner"><?php bp_get_template_part( 'activity/type-parts/content',  bp_activity_type_part() ); ?></div>

			<?php endif; ?>
			
			<?php if(current_user_can('mepr-active','rules:11718')){  ?>
				<?php bp_nouveau_activity_entry_buttons(); ?>
			<?php } ?>

		</div>

	<?php bp_nouveau_activity_hook( 'before', 'entry_comments' ); ?>

	<?php if ( bp_activity_get_comment_count() || ( is_user_logged_in() && ( bp_activity_can_comment() || bp_is_single_activity() ) ) ) : ?>

		<div class="activity-comments">

			<?php bp_activity_comments(); ?>
			<?php if(current_user_can('mepr-active','rules:11718')){  ?>
				<?php bp_nouveau_activity_comment_form(); ?>
			<?php } ?>

		</div>

	<?php endif; ?>

	<?php bp_nouveau_activity_hook( 'after', 'entry_comments' ); ?>

</li>

<?php
bp_nouveau_activity_hook( 'after', 'entry' );
