<?php
/**
 * BuddyPress - Activity Stream (Single Item)
 *
 * This template is used by activity-loop.php and AJAX functions to show
 * each activity.
 */

/**
 * Fires before the display of an activity entry.
 *
 * @since 1.2.0
 */
do_action( 'bp_before_activity_entry' ); ?>

<li class="<?php bp_activity_css_class(); ?>" data-effect="fadeInDown" id="activity-<?php bp_activity_id(); ?>">

	<?php do_action( 'bp_before_activity_entry_content' ); ?>
	
	<div class="activity-content">

		<div class="activity-header">

			<?php do_action( 'bp_before_activity_entry_header' ); ?>
			
			<div class="activity-avatar">
				<a href="<?php bp_activity_user_link(); ?>">
					<?php bp_activity_avatar(); ?>
				</a>
			</div>

			<div class="activity-head"><?php bp_activity_action( array( 'no_timestamp' => true ) );?><div class="yz-timestamp-area"><?php echo yz_get_activity_time_stamp_meta(); ?></div></div>

		</div>
		
		
		<?php //bp_activity_content_body(); // if this is a custom post id, serve the feeatured image and title
			 $custom_post_id = bp_get_activity_secondary_item_id();
			 if ($custom_post_id) { ?>
		      <div class="activity-inner"> <?php
				  if (has_post_thumbnail( $custom_post_id ) ) {
						$theimg = wp_get_attachment_image_src( get_post_thumbnail_id( $custom_post_id ), 'large' ); ?>
						<div class="img_container"><a href="<?php echo get_post_permalink($custom_post_id); ?>"> <img style="thumbnail" style="width:100%;" src="<?php echo $theimg[0]; ?>"></a></div>
						<p>
				  			<?php echo get_post_field('post_title', $custom_post_id); ?>
						</p>
						<a href="<?php echo get_post_permalink($custom_post_id); ?>" class="btn universal-btn">Read More</a>
					</div>
				  <?php } ?>
			 <?php }else{
				 bp_activity_content_body();
			 } ?>

		<?php

		/**
		 * Fires after the display of an activity entry content.
		 *
		 * @since 1.2.0
		 */
		do_action( 'bp_activity_entry_content' ); ?>		

		<div class="activity-meta <?php if ( ! yz_display_wall_post_meta() ) { echo 'yz-empty-wall-post-meta'; } ?>" data-activity-id="<?php echo bp_get_activity_id(); ?>">

			<?php if ( bp_get_activity_type() == 'activity_comment' && is_user_logged_in() ) : ?>

				<a href="<?php bp_activity_thread_permalink(); ?>" class="button view bp-secondary-action"><?php _e( 'View Conversation', 'youzer' ); ?></a>

			<?php endif; ?>

			<?php if ( is_user_logged_in() ) : ?>

				<?php if ( bp_activity_can_favorite() ) : ?>
					<?php echo yz_get_post_like_button(); ?>
				<?php endif; ?>
            <?php if ($custom_post_id) { ?>
					<a href="<?php echo(get_post_permalink($custom_post_id)); ?>#commentform" class="button acomment-reply">Comment</a>
			   <?php }else { ?>
			      <?php if ( bp_activity_can_comment() ) : ?>
						<a href="<?php bp_activity_comment_link(); ?>" class="button acomment-reply bp-primary-action" id="acomment-comment-<?php bp_activity_id(); ?>"><?php yz_wall_get_comment_button_title() ?></a>
					<?php endif; ?>

					<?php do_action( 'bp_activity_after_comment_button' ); ?>

					<?php if ( bp_activity_user_can_delete() ) bp_activity_delete_link(); ?>
			   <?php } ?>

				<?php

				/**
				 * Fires at the end of the activity entry meta data area.
				 *
				 * @since 1.2.0
				 */
				do_action( 'bp_activity_entry_meta' ); ?>

			<?php endif; ?>

			<?php do_action( 'bp_activity_entry_meta_non_logged_in', bp_get_activity_id() ); ?>

		</div>

		<?php // endif; ?>
	
	</div>

	<?php

	/**
	 * Fires before the display of the activity entry comments.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_before_activity_entry_comments' ); ?>

	<?php if ( ( bp_activity_get_comment_count() || bp_activity_can_comment() ) || bp_is_single_activity() ) : ?>

		<div class="activity-comments">

			<?php bp_activity_comments(); ?>

			<?php if ( is_user_logged_in() && bp_activity_can_comment() ) : ?>

				<form action="<?php bp_activity_comment_form_action(); ?>" method="post" id="ac-form-<?php bp_activity_id(); ?>" class="ac-form"<?php bp_activity_comment_form_nojs_display(); ?>>
					<div class="ac-reply-content">
						<div class="ac-textarea">
							<label for="ac-input-<?php bp_activity_id(); ?>" class="bp-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'Comment', 'youzer' );
							?></label>
							<textarea id="ac-input-<?php bp_activity_id(); ?>" class="ac-input bp-suggestions" name="ac_input_<?php bp_activity_id(); ?>" placeholder="<?php _e( 'Write a Comment ...', 'youzer' ); ?>"></textarea>
						</div>
						<div class="yz-send-comment"><i class="fas fa-paper-plane"></i></button>
						<!-- <a href="#" class="ac-reply-cancel"><?php _e( 'Cancel', 'youzer' ); ?></a> -->
						<input type="hidden" name="comment_form_id" value="<?php bp_activity_id(); ?>" />
					</div>

					<?php

					/**
					 * Fires after the activity entry comment form.
					 *
					 * @since 1.5.0
					 */
					do_action( 'bp_activity_entry_comments' ); ?>

					<?php wp_nonce_field( 'new_activity_comment', '_wpnonce_new_activity_comment' ); ?>

				</form>

			<?php endif; ?>

		</div>

	<?php endif; ?>

	<?php

	/**
	 * Fires after the display of the activity entry comments.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_after_activity_entry_comments' ); ?>

</li>

<?php

/**
 * Fires after the display of an activity entry.
 *
 * @since 1.2.0
 */
do_action( 'bp_after_activity_entry' ); ?>
