<div class="top-bar-messages-container">
	<div class="top-bar-messages">
		<span class="no-<?php echo messages_get_unread_count(); ?>"><?php echo messages_get_unread_count(); ?></span></div>

		<div class="top-bar-messages-menu-container">
				<div class="arrow"></div>
				<div class="top-bar-messages-menu">
				<?php
				if ( bp_has_message_threads('max=3&user_id=' . get_current_user_id() . '') ) : ?>

				  <ul>
 				  <?php while ( bp_message_threads() ) : bp_message_thread(); ?>

				    <li class="<?php if(bp_message_thread_has_unread()) { ?>unread<?php } ?>">
				    <div class="avatar"><?php bp_message_thread_avatar() ?></div>
				    <div class="message-content">
				      <div class="message-content-details"><?php bp_message_thread_from() ?> <?php bp_message_thread_last_post_date() ?></div>
				      <a href="<?php bp_message_thread_view_link() ?>" class="message-title"><?php bp_message_thread_subject() ?></a>
				      <p><?php bp_message_thread_excerpt() ?></p>
				    </div>

				    <div class="clear"></div>
				    </li>
				  <?php endwhile; ?>
				  <li class="view-all"><a href="<?php echo bp_loggedin_user_domain() ?><?php esc_attr_e('messages', 'onecommunity'); ?>"><?php esc_attr_e('View all messages', 'onecommunity'); ?></a></li>
				  </ul>

				<?php else: ?>

				  <div id="message" class="info">
 				   <p><?php esc_attr_e('There are no messages to display.', 'onecommunity'); ?></p>
				  </div>

				<?php endif; ?>
				</div>

		</div><!-- top-bar-messages-menu-container -->
</div><!-- top-bar-messages-container -->