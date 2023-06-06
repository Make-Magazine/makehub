<div class="user-top-menu">
	<div id="user-top-menu-expander"></div>

		<div class="user-top-menu-container">
				<div class="arrow"></div>
					<ul>
						<li><a href="<?php echo bp_loggedin_user_domain(); ?><?php esc_attr_e('activity', 'onecommunity'); ?>"><?php esc_attr_e('Activity', 'onecommunity'); ?></a></li>
						<li><a href="<?php echo bp_loggedin_user_domain(); ?><?php esc_attr_e('profile', 'onecommunity'); ?>"><?php esc_attr_e('Profile', 'onecommunity'); ?></a></li>
						<?php if ( bp_is_active( 'notifications' ) ) { ?><li><a href="<?php echo bp_loggedin_user_domain(); ?><?php esc_attr_e('notifications', 'onecommunity'); ?>"><?php esc_attr_e('Notifications', 'onecommunity'); ?></a> <?php $count = bp_notifications_get_unread_notification_count( bp_loggedin_user_id() ); if($count > 0) { echo '<span>' . $count . '</span>'; } ?></li><?php } ?>
						<?php if ( bp_is_active( 'messages' ) ) { ?><li><a href="<?php echo bp_loggedin_user_domain(); ?><?php esc_attr_e('messages', 'onecommunity'); ?>"><?php esc_attr_e('Messages', 'onecommunity'); ?></a> <span><?php $count = bp_get_total_unread_messages_count( bp_loggedin_user_id() ); if($count > 0) { echo '<span>' . $count . '</span>'; } ?></li><?php } ?>
						<?php if ( bp_is_active( 'friends' ) ) { ?><li><a href="<?php echo bp_loggedin_user_domain(); ?><?php esc_attr_e('friends', 'onecommunity'); ?>"><?php esc_attr_e('Friend Requests', 'onecommunity'); ?></a> <?php $count = bp_friend_get_total_requests_count( bp_loggedin_user_id() ); if($count > 0) { echo '<span>' . $count . '</span>'; } ?></li><?php } ?>
						<?php if ( bp_is_active( 'groups' ) ) { ?><li><a href="<?php echo bp_loggedin_user_domain(); ?><?php esc_attr_e('groups', 'onecommunity'); ?>"><?php esc_attr_e('Group Invites', 'onecommunity'); ?></a> <span><?php $groups = groups_get_invites_for_user(bp_loggedin_user_id()); $groupsInviteCount = $groups['total']; if($count > 0) { echo '<span>' . $groupsInviteCount . '</span>'; } ?></li><?php } ?>
						<li><a href="<?php echo bp_loggedin_user_domain(); ?><?php esc_attr_e('forums', 'onecommunity'); ?>"><?php esc_attr_e('Forums', 'onecommunity'); ?></a></li>
						<?php if ( get_theme_mod( 'onecommunity_bp_user_blog_enable', false ) == true ) { ?> <li><a href="<?php echo home_url(); ?>/wp-admin/post-new.php"><?php esc_attr_e('Add a New Post', 'onecommunity'); ?></a></li> <?php } ?>
						<li><a href="<?php echo bp_loggedin_user_domain(); ?><?php esc_attr_e('settings', 'onecommunity'); ?>"><?php esc_attr_e('Settings', 'onecommunity'); ?></a></li>
						<li><b><a href="<?php echo wp_logout_url( home_url() ) ?>"><?php esc_attr_e('Log Out', 'onecommunity'); ?></a></b></li>
					</ul>
		</div>

</div>

<div class="user-top logged-in">

	<a class="top-bar-avatar" href="<?php echo bp_loggedin_user_domain() ?>">
	<?php bp_loggedin_user_avatar( 'type=thumb&width=32&height=32' ) ?>
	</a>
	<span class="top-bar-username"><?php esc_attr_e('Hello', 'onecommunity'); ?><br>
	<a href="<?php echo bp_loggedin_user_domain() ?>"><?php $theusername = bp_core_get_user_displayname( bp_loggedin_user_id() ); $getlength = strlen($theusername); $thelength = 15; echo mb_substr($theusername, 0, $thelength, 'UTF-8'); if ($getlength > $thelength) echo "..."; ?></a>!</span>

</div><!-- user-top logged-in -->