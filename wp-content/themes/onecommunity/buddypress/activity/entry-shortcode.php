<?php
/**
 * BuddyPress - Activity Stream (Single Item)
 *
 * This template is used by activity-loop.php and AJAX functions to show
 * each activity.
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

/**
 * Fires before the display of an activity entry.
 *
 * @since 1.2.0
 */
do_action( 'bp_before_activity_entry' ); ?>

<li class="<?php bp_activity_css_class(); ?> parent" id="activity-<?php bp_activity_id(); ?>">

	<div class="activity-content">

	<div class="activity-header-container">


	<div class="activity-avatar">
		<a href="<?php bp_activity_user_link(); ?>">
			<?php bp_activity_avatar(); ?>
		</a>
	</div>	

	<div class="activity-header">
			<?php bp_activity_action(); ?>
	</div>

	</div><!-- .activity-header-container -->

		<?php if ( bp_activity_has_content() ) : ?>
			<div class="activity-inner">
				<?php bp_activity_content_body(); ?>
			</div>
		<?php endif; ?>

		<div class="shortcode-activity-meta">			
			<span class="comments-count"><?php bp_activity_comment_count(); ?></span>
			<?php do_action( 'bp_activity_entry_meta' ); ?>
		</div>

		<?php

		/**
		 * Fires after the display of an activity entry content.
		 *
		 * @since 1.2.0
		 */
		do_action( 'bp_activity_entry_content' ); ?>

	</div>

	<div class="clear"></div>

</li>

<?php

/**
 * Fires after the display of an activity entry.
 *
 * @since 1.2.0
 */
do_action( 'bp_after_activity_entry' ); ?>
