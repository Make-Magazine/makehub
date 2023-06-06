<div class="top-bar-notifications-container">
	<div class="top-bar-notifications">
		<span class="no-<?php echo bp_notifications_get_unread_notification_count( bp_loggedin_user_id() ); ?>"><?php echo bp_notifications_get_unread_notification_count( bp_loggedin_user_id() ); ?></span></div>

  			<div class="notifications-list-container">
				<div class="arrow"></div>
					<ul class="notifications-list">

						<?php $notifications = bp_notifications_get_notifications_for_user( bp_loggedin_user_id() );

						if ( $notifications ) {
						$counter = 0;
						for ( $i = 0, $count = count( $notifications ); $i < $count; ++$i ) { ?>
							<li class="notification"><?php echo wp_kses( $notifications[$i], array( 'a' => array( 'href' => array(), 'title' => array() ) ) ); ?></li>
							<?php $counter++;
							}
						} else {
							echo '<li>' . esc_attr__('You have no unread notifications', 'onecommunity') . '</li>';
						}
						?>

						<li class="view-all"><a href="<?php echo bp_loggedin_user_domain() ?><?php esc_attr_e('notifications', 'onecommunity'); ?>"><?php esc_attr_e('Manage', 'onecommunity'); ?></a></li>

					</ul>
	</div><!-- notifications-list-container -->
</div><!-- top-bar-notifications-container -->