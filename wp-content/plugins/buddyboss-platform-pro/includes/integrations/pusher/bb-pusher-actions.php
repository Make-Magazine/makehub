<?php
/**
 * Pusher integration actions
 *
 * @package BuddyBossPro\Pusher
 * @since   2.1.6
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'bb_pro_pusher_enqueue_scripts_and_styles', 99 );
add_action( 'login_head', 'bb_pro_pusher_enqueue_scripts_and_styles', 99 );

add_action( 'bp_before_message_thread_reply', 'bb_pro_pusher_messages_typing_html', 999 );

// fire an event after thread delete.
add_action( 'bp_messages_thread_before_delete', 'bb_pro_pusher_message_delete_thread', 999, 1 );

// fire an event after member suspended.
add_action( 'bp_suspend_hide_user', 'bb_pro_pusher_user_suspended', 99, 1 );

// fire an event after member unsuspended.
add_action( 'bp_suspend_unhide_user', 'bb_pro_pusher_user_unsuspended', 9, 1 );

// fire an event after member block.
add_action( 'bp_moderation_after_save', 'bb_pro_pusher_moderation_after_save', 999 );

// fire an event after member unblock.
add_action( 'bb_moderation_after_delete', 'bb_pro_pusher_moderation_after_delete', 999 );

// fire an event after member connection setting changed.
add_action( 'update_option_bp-force-friendship-to-message', 'bb_pro_pusher_member_connection_after_update', 10, 2 );

// fire an event when member send connection request.
add_action( 'friends_friendship_requested', 'bb_pro_pusher_member_connection_requested', 10, 3 );

// fire an event when member withdrawn connection request.
add_action( 'friends_friendship_withdrawn', 'bb_pro_pusher_member_withdrawn_connection_request', 10, 2 );
add_action( 'friends_friendship_post_delete', 'bb_pro_pusher_friends_remove_friend', 10, 2 );

// fire an event when member accepted connection request.
add_action( 'friends_friendship_accepted', 'bb_pro_pusher_member_accepted_friendship', 10, 3 );

// fire an event when member rejected connection request.
add_action( 'friends_friendship_rejected', 'bb_pro_pusher_member_rejected_friendship', 10, 2 );

// fire an event after message access control settings changed.
add_filter( 'update_option_' . bb_access_control_send_message_key(), 'bb_pro_pusher_message_access_control_after_update', 10, 2 );

// Fire and event when component has been updated.
add_action( 'update_option_bp-active-components', 'bb_pro_pusher_active_components', 999, 3 );

// Fire and event when group message has been disabled from: BuddyBoss → Settings → Groups.
add_action( 'update_option_bp-disable-group-messages', 'bb_pro_pusher_disabled_group_messages', 999, 3 );

// Fire an event when group settings has been updated.
add_action( 'updated_group_meta', 'bb_pro_pusher_group_settings_update', 10, 4 );

// Fire an event when user deleted.
add_action( 'wpmu_delete_user', 'bb_pro_pusher_on_user_delete' );
add_action( 'delete_user', 'bb_pro_pusher_on_user_delete' );

add_action( 'messages_message_after_save', 'bb_pro_pusher_messages_message_new_thread_save', 999, 1 );
add_action( 'messages_message_sent', 'bb_pro_pusher_messages_message_new_message_save', 99, 1 );

// Fire an event for the group delete.
add_action( 'groups_before_delete_group', 'bb_pro_pusher_groups_before_delete_group', 99, 1 );

add_action( 'bp_rest_settings_get_items', 'bb_pro_pusher_rest_settings', 99, 1 );

// Fire an events for the group member banned/unbanned.
add_action( 'bb_group_messages_banned_member', 'bb_pro_pusher_group_messages_banned_member', 10, 3 );
add_action( 'bb_group_messages_unbanned_member', 'bb_pro_pusher_group_messages_unbanned_member', 10, 3 );

// Fire an events for the group member joined/left first time.
add_action( 'groups_join_group', 'bb_pro_pusher_group_messages_member_joined', 10, 2 );
add_action( 'groups_leave_group', 'bb_pro_pusher_group_messages_member_left', 10, 2 );
add_action( 'groups_remove_member', 'bb_pro_pusher_group_messages_member_left', 10, 2 );

add_action( 'wp_ajax_bb_pusher_update_message_content', 'bb_pusher_update_message_content' );
add_action( 'wp_ajax_bb_pusher_update_message_content', 'bb_pusher_update_message_content' );


add_action( 'bp_rest_messages_create_item', 'bb_pusher_rest_create_message', 10, 4 );

// Fire and event when group message has been disabled from: BuddyBoss → Integration → Pusher → Pusher.
add_action( 'update_option_bb-pusher-enabled-features', 'bb_pro_pusher_disabled_pusher_settings', 999, 3 );

// For and event when group messages has been deleted.
add_action( 'bp_messages_thread_messages_after_update', 'bb_pro_pusher_deleted_thread_messages', 999, 1 );

/**
 * Enqueue scripts and styles.
 *
 * @since 2.1.6
 */
function bb_pro_pusher_enqueue_scripts_and_styles() {
	if ( ! bbp_pro_is_license_valid() || ! bb_pusher_is_enabled() ) {
		return;
	}

	if ( false === bb_pusher_is_feature_enabled( 'live-messaging' ) || ! is_user_logged_in() ) {
		return;
	}

	$is_msg_dropdown = false;
	if ( function_exists( 'buddyboss_theme_get_option' ) ) {
		$is_mob_on  = (bool) buddyboss_theme_get_option( 'mobile_component_opt_multi_checkbox', 'mobile_messages' ) && bp_is_active( 'messages' );
		$is_desk_on = (bool) buddyboss_theme_get_option( 'desktop_component_opt_multi_checkbox', 'desktop_messages' ) && bp_is_active( 'messages' );
		if ( $is_mob_on || $is_desk_on ) {
			$is_msg_dropdown = true;
		}
	}

	if ( ! $is_msg_dropdown && ! bp_is_user_messages() ) {
		return;
	}

	$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script( 'bb-pro-pusher-js-lib', 'https://js.pusher.com/7.0/pusher-with-encryption.min.js', array(), bb_platform_pro()->version, false );
	wp_enqueue_script( 'bb-pro-deparam-js', trailingslashit( bb_platform_pro()->plugin_url ) . 'assets/js/vendor/jquery-deparam.js', array(), bb_platform_pro()->version, false );
	wp_enqueue_script( 'bb-pro-pusher-auth-js', bb_pusher_integration_url( '/assets/js/pusher-auth.js' ), array( 'bb-pro-pusher-js-lib', 'wp-i18n', 'bb-pro-deparam-js' ), bb_platform_pro()->version, false );
	wp_enqueue_script( 'bb-pro-pusher-js', bb_pusher_integration_url( '/assets/js/bb-pusher' . $min . '.js' ), array( 'bb-pro-pusher-js-lib', 'wp-i18n', 'bb-pro-deparam-js', 'bb-pro-pusher-auth-js' ), bb_platform_pro()->version, false );
	wp_set_script_translations( 'bb-pro-pusher-js', 'buddyboss-pro' );

	$user_id   = bp_loggedin_user_id();
	$user_hash = bb_pusher_get_user_hash( $user_id );
	$user_data = array(
		'user_id'      => $user_id,
		'display_name' => bp_core_get_user_displayname( $user_id ),
		'user_link'    => bp_core_get_user_domain( $user_id ),
		'user_avatar'  => bp_core_fetch_avatar(
			array(
				'item_id' => $user_id,
				'object'  => 'user',
				'type'    => 'thumb',
				'html'    => false,
			)
		),
	);

	$hash_key        = bb_pusher_hash_key();
	$user_thread_ids = array();

	if ( is_user_logged_in() && bb_pusher_is_feature_enabled( 'live-messaging' ) && bp_is_active( 'messages' ) ) {

		$results = BP_Messages_Thread::get_threads_for_user(
			array(
				'fields'    => 'ids',
				'user_id'   => bp_loggedin_user_id(),
				'is_hidden' => true,
			)
		);

		if ( ! empty( $results ) ) {
			array_walk(
				$results['threads'],
				function ( &$id, $key ) use ( &$user_thread_ids ) {
					$user_thread_ids[ bb_pusher_string_hash( $id ) ] = $id;
				}
			);
		}
	}

	$data = array(
		'ajax_url'                     => bp_core_ajax_url(),
		'is_messages_component_active' => (int) bp_is_active( 'messages' ),
		'loggedin_user_name'           => ( is_user_logged_in() ? bp_core_get_user_displayname( bp_loggedin_user_id() ) : '' ),
		'loggedin_user_id'             => bp_loggedin_user_id(),
		'is_admin'                     => bp_current_user_can( 'bp_moderate' ) ? 'yes' : 'no',
		'home_url'                     => get_home_url(),
		'auth_endpoint'                => get_home_url() . '/wp-json/' . bp_rest_namespace() . '/' . bp_rest_version() . '/pusher/auth',
		'user_data'                    => wp_json_encode( $user_data ),
		'sender_avatar'                => esc_url(
			bp_core_fetch_avatar(
				array(
					'item_id' => bp_loggedin_user_id(),
					'object'  => 'user',
					'type'    => 'thumb',
					'width'   => 32,
					'height'  => 32,
					'html'    => false,
				)
			)
		),
		'sender_link'                  => bp_core_get_userlink( bp_loggedin_user_id(), false, true ),
		'display_date'                 => __( 'a second ago', 'buddyboss-pro' ),
		'app_key'                      => bb_pusher_app_key(),
		'app_cluster'                  => bb_pusher_cluster(),
		'global_private'               => 'private-bb-pro-global',
		'bb_pro_user_friends'          => ( bp_is_active( 'friends' ) ? bp_get_friend_ids( $user_id ) : '' ),
		'is_live_messaging_enabled'    => ( false === bb_pusher_is_feature_enabled( 'live-messaging' ) ? 'off' : 'on' ),
		'bb_pro_pusher_thread_ids'     => $user_thread_ids,
		'cancel_text'                  => __( 'Cancel', 'buddyboss-pro' ),
		'not_delivered_text'           => __( 'Not delivered', 'buddyboss-pro' ),
		'try_again_text'               => __( 'Try again', 'buddyboss-pro' ),
		'sending_text'                 => __( 'Sending...', 'buddyboss-pro' ),
		'video_default_url'            => ( function_exists( 'bb_get_video_default_placeholder_image' ) && ! empty( bb_get_video_default_placeholder_image() ) ? bb_get_video_default_placeholder_image() : '' ),
		'i18n'                         => array(
			'conversion_delete'        => __( 'The conversation with %s has been deleted.', 'buddyboss-pro' ), //phpcs:ignore
			'thread_left'              => __( 'You have left ', 'buddyboss-pro' ),
			'group_banned'             => __( 'You are banned in ', 'buddyboss-pro' ),
			'typing'                   => __( ' are typing...', 'buddyboss-pro' ),
			'single_typing'            => __( ' is typing...', 'buddyboss-pro' ),
			'two_typing'               => __( ' members are typing...', 'buddyboss-pro' ),
			'multiple_typing'          => __( ' others are typing...', 'buddyboss-pro' ),
			'separator'                => __( ' and ', 'buddyboss-pro' ),
			'group_message_disabled'   => __( 'Group messages have been disabled by a site administrator.', 'buddyboss-pro' ),
			'deleted'                  => __( ' was deleted.', 'buddyboss-pro' ),
			'disabled_private_message' => __( 'Private messages have been disabled by a site administrator.', 'buddyboss-pro' ),
			'remove_from_group'        => __( 'You have removed from group ', 'buddyboss-pro' ),
		),
		'blocked_users_ids'            => array(),
		'suspended_users_ids'          => array(),
		'is_blocked_by_users'          => array(),
		'alien_hash'                   => $user_hash,
		'hash'                         => $hash_key, // @todo don't show if possible.
		'presence_thread'              => '',
		'content_size_limit'           => bb_pusher_get_content_size_limit(),
		'group_threads'                => array(),
	);

	if ( bp_is_active( 'messages' ) && bp_is_user_messages() ) {
		$data['current_thread_id'] = (int) bp_action_variable( 0 );
		$data['current_thread']    = array();
		$data['thread_channel']    = 'private-bb-message-thread-' . $data['current_thread_id'];
	}

	if ( bp_is_active( 'messages' ) && bp_is_active( 'groups' ) && function_exists( 'bp_disable_group_messages' ) && true === bp_disable_group_messages() ) {
		// Determine groups of user.
		$groups = groups_get_groups(
			array(
				'fields'      => 'ids',
				'per_page'    => - 1,
				'user_id'     => bp_loggedin_user_id(),
				'show_hidden' => true,
				'meta_query'  => array( // phpcs:ignore
					'relation' => 'AND',
					array(
						'key'     => 'group_message_thread',
						'compare' => 'EXISTS',
					),
				),
			),
		);

		$group_ids = ( isset( $groups['groups'] ) ? $groups['groups'] : array() );

		$group_threads = array();
		if ( ! empty( $group_ids ) ) {
			array_walk(
				$group_ids,
				function ( &$group_id, $key ) use ( &$group_threads ) {
					$thread_id = (int) groups_get_groupmeta( (int) $group_id, 'group_message_thread' );
					if ( ! empty( $thread_id ) ) {
						$group_threads[ bb_pusher_string_hash( $thread_id ) ] = $thread_id;
					}
				}
			);
		}
		$data['group_threads'] = $group_threads;

	}

	if ( bp_is_active( 'groups' ) && bp_is_group_single() && bp_is_group_messages() && 'public-message' === bb_get_group_current_messages_tab() ) {
		$data['current_thread_id'] = groups_get_groupmeta( (int) bp_get_current_group_id(), 'group_message_thread' );
	}

	if ( bp_is_active( 'moderation' ) ) {
		$blocked_members = bp_moderation_get(
			array(
				'user_id'           => get_current_user_id(),
				'per_page'          => 0,
				'in_types'          => BP_Moderation_Members::$moderation_type,
				'update_meta_cache' => true,
			)
		);

		$blocked_members_ids = ( ! empty( $blocked_members['moderations'] ) ? array_column( $blocked_members['moderations'], 'item_id' ) : array() );

		if ( ! empty( $blocked_members_ids ) ) {
			array_walk(
				$blocked_members_ids,
				function ( &$id, $key ) use ( &$data ) {
					$avatar = bp_core_fetch_avatar(
						array(
							'item_id' => $id,
							'object'  => 'user',
							'type'    => 'thumb',
							'width'   => BP_AVATAR_THUMB_WIDTH,
							'height'  => BP_AVATAR_THUMB_HEIGHT,
							'html'    => false,
						)
					);

					$data['blocked_users_ids'][ bb_pusher_get_user_hash( $id ) ] = array(
						'id'                 => $id,
						'blocked_user_name'  => bb_moderation_has_blocked_label( bp_core_get_user_displayname( $id ), $id ),
						'blocked_avatar_url' => bb_moderation_has_blocked_avatar( $avatar, $id ),
					);
				}
			);
		}

		$data['blocked_message_text'] = '<p class="blocked">' . esc_html__( 'This content has been hidden as you have blocked this member.', 'buddyboss-pro' ) . '</p>';

		$suspended_users = BP_Moderation::get(
			array(
				'per_page'          => 0,
				'hidden'            => 1,
				'fields'            => 'ids',
				'in_types'          => BP_Moderation_Members::$moderation_type,
				'update_meta_cache' => true,
			)
		);

		$suspended_users_ids = ( ! empty( $suspended_users['moderations'] ) ? array_column( $suspended_users['moderations'], 'item_id' ) : array() );

		if ( ! empty( $suspended_users_ids ) ) {
			array_walk(
				$suspended_users_ids,
				function ( &$id, $key ) use ( &$data ) {
					$data['suspended_users_ids'][ bb_pusher_get_user_hash( $id ) ] = $id;
				}
			);
		}

		$data['suspended_avatar']       = bb_moderation_is_suspended_avatar();
		$data['suspended_user_name']    = bb_moderation_is_suspended_label();
		$data['suspended_message_text'] = esc_html__( 'This content has been hidden from site admin.', 'buddyboss-pro' );

		// Is blocked by members.
		$is_blocked_by_members = bb_moderation_get_blocked_by_user_ids( get_current_user_id() );
		if ( ! empty( $is_blocked_by_members ) ) {
			foreach ( $is_blocked_by_members as $id ) {
				$avatar = bp_core_fetch_avatar(
					array(
						'item_id' => $id,
						'object'  => 'user',
						'type'    => 'thumb',
						'width'   => BP_AVATAR_THUMB_WIDTH,
						'height'  => BP_AVATAR_THUMB_HEIGHT,
						'html'    => false,
					)
				);

				$data['is_blocked_by_users'][ bb_pusher_get_user_hash( $id ) ] = array(
					'id'                 => $id,
					'blocked_user_name'  => bb_moderation_is_blocked_label( bp_core_get_user_displayname( $id ), $id ),
					'blocked_avatar_url' => bb_moderation_is_blocked_avatar( $avatar, $id ),
				);
			}
		}
	}

	wp_localize_script(
		'bb-pro-pusher-js',
		'bb_pusher_vars',
		$data
	);

}

/**
 * Add the html for the someone is typing html before message form.
 *
 * @since 2.1.6
 *
 * @return void
 */
function bb_pro_pusher_messages_typing_html() {
	?>
	<div class="bb-pusher-typing-indicator bp-hide">
		<div class="bb-pusher-typing-indicator-inner">
			<div class="bb-pusher-typing-indicator-text">
				<span class="bb-pusher-typing-indicator-text-inner">
					<?php esc_html_e( 'Typing...', 'buddyboss-pro' ); ?>
				</span>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Fire an event when thread was deleted.
 *
 * @since 2.1.6
 *
 * @param int $thread_id Thread id.
 *
 * @return void
 *
 * @throws \GuzzleHttp\Exception\GuzzleException Client Exception.
 * @throws \Pusher\ApiErrorException Pusher API Error.
 * @throws \Pusher\PusherException Pusher Exception.
 */
function bb_pro_pusher_message_delete_thread( $thread_id ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	$thread_recipients = BP_Messages_Thread::get_recipients_for_thread( (int) $thread_id );
	$recipients        = array();

	if ( ! empty( $thread_recipients ) ) {
		foreach ( $thread_recipients as $recepient ) {
			$recipients[ bb_pusher_get_user_hash( $recepient->user_id ) ] = array(
				'user_id'            => bb_pusher_get_user_hash( $recepient->user_id ),
				'name'               => bp_core_get_user_displayname( $recepient->user_id ),
				'inbox_unread_count' => messages_get_unread_count( $recepient->user_id ),
			);
		}
	}

	$is_group_message_thread = bb_messages_is_group_thread( (int) $thread_id );

	$event_data = array(
		'thread_id'  => bb_pusher_string_hash( $thread_id ),
		'recipients' => $recipients,
	);

	if ( true === (bool) $is_group_message_thread && bp_is_active( 'groups' ) ) {
		$first_message    = BP_Messages_Thread::get_first_message( $thread_id );
		$first_message_id = ( ! empty( $first_message ) ? $first_message->id : false );
		$group_id         = ( isset( $first_message_id ) ) ? (int) bp_messages_get_meta( $first_message_id, 'group_id', true ) : 0;
		if ( $group_id ) {
			$event_data['group_name'] = bp_get_group_name( groups_get_group( $group_id ) );
		}
	}

	$bb_pusher = bb_pusher();
	if ( null !== $bb_pusher ) {
		bb_pusher_trigger_event( $bb_pusher, 'private-bb-message-thread-' . $thread_id, 'client-bb-pro-thread-delete', $event_data );
	}
}

/**
 * Fire an event when user was suspended.
 *
 * @since 2.1.6
 *
 * @param int $user_id User id.
 *
 * @return void
 *
 * @throws \GuzzleHttp\Exception\GuzzleException Client Exception.
 * @throws \Pusher\ApiErrorException Pusher API Error.
 * @throws \Pusher\PusherException Pusher Exception.
 */
function bb_pro_pusher_user_suspended( $user_id ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	if (
		BP_Core_Suspend::check_hidden_content( $user_id, 'user' ) ||
		BP_Core_Suspend::check_suspended_content( $user_id, 'user' )
	) {
		$results = BP_Messages_Thread::get_threads_for_user(
			array(
				'fields'    => 'ids',
				'user_id'   => $user_id,
				'is_hidden' => true,
			)
		);

		$thread_ids = array();
		if ( ! empty( $results ) ) {
			array_walk(
				$results['threads'],
				function ( &$id, $key ) use ( &$thread_ids ) {
					$thread_ids[] = bb_pusher_string_hash( $id );
				}
			);
		}

		$channel = 'private-bb-pro-global';

		$event_data = array(
			'user_id'    => bb_pusher_get_user_hash( $user_id ),
			'thread_ids' => $thread_ids,
		);

		$bb_pusher = bb_pusher();
		if ( null !== $bb_pusher ) {
			bb_pusher_trigger_event( $bb_pusher, $channel, 'client-bb-pro-member-suspended', $event_data );
		}
	}
}

/**
 * Fire an event when user was unsuspended.
 *
 * @since 2.1.6
 *
 * @param int $user_id User id.
 *
 * @return void
 *
 * @throws \GuzzleHttp\Exception\GuzzleException Client Exception.
 * @throws \Pusher\ApiErrorException Pusher API Error.
 * @throws \Pusher\PusherException Pusher Exception.
 */
function bb_pro_pusher_user_unsuspended( $user_id ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	if (
		BP_Core_Suspend::check_hidden_content( $user_id, 'user' ) ||
		BP_Core_Suspend::check_suspended_content( $user_id, 'user' )
	) {
		$results = BP_Messages_Thread::get_threads_for_user(
			array(
				'fields'    => 'ids',
				'user_id'   => $user_id,
				'is_hidden' => true,
			)
		);

		$thread_ids = array();
		if ( ! empty( $results ) ) {
			array_walk(
				$results['threads'],
				function ( &$id, $key ) use ( &$thread_ids ) {
					$thread_ids[] = bb_pusher_string_hash( $id );
				}
			);
		}

		$channel = 'private-bb-pro-global';

		$event_data = array(
			'user_id'    => bb_pusher_get_user_hash( $user_id ),
			'thread_ids' => $thread_ids,
		);

		$bb_pusher = bb_pusher();
		if ( null !== $bb_pusher ) {
			bb_pusher_trigger_event( $bb_pusher, $channel, 'client-bb-pro-member-unsuspended', $event_data );
		}
	}
}

/**
 * Fire a pusher event for the member block.
 *
 * @since 2.1.6
 *
 * @param BP_Moderation $moderation Object of moderation data.
 *
 * @return void
 */
function bb_pro_pusher_moderation_after_save( $moderation ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	if ( 'user' === $moderation->item_type ) {
		$creator_id = $moderation->user_id;
		$blocked_id = $moderation->item_id;

		$results = BP_Messages_Thread::get_threads_for_user(
			array(
				'fields'    => 'ids',
				'user_id'   => $blocked_id,
				'is_hidden' => true,
			)
		);

		$thread_ids = array();
		if ( ! empty( $results ) ) {
			array_walk(
				$results['threads'],
				function ( &$id, $key ) use ( &$thread_ids ) {
					$thread_ids[] = bb_pusher_string_hash( $id );
				}
			);
		}

		$channels = array(
			'private-bb-user-' . $creator_id,
			'private-bb-user-' . $blocked_id,
		);

		$event_data = array(
			'creator_id' => $creator_id,
			'blocked_id' => $blocked_id,
			'thread_ids' => $thread_ids,
		);

		$bb_pusher = bb_pusher();
		if ( null !== $bb_pusher ) {
			bb_pusher_trigger_event( $bb_pusher, $channels, 'client-bb-pro-member-blocked', $event_data );
		}
	}
}

/**
 * Fire a pusher event for the member block.
 *
 * @since 2.1.6
 *
 * @param BP_Moderation $moderation Object of moderation data.
 *
 * @return void
 */
function bb_pro_pusher_moderation_after_delete( $moderation ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	if ( 'user' === $moderation->item_type ) {
		$creator_id   = $moderation->user_id;
		$unblocked_id = $moderation->item_id;

		$results = BP_Messages_Thread::get_threads_for_user(
			array(
				'fields'    => 'ids',
				'user_id'   => $unblocked_id,
				'is_hidden' => true,
			)
		);

		$thread_ids = array();
		if ( ! empty( $results ) ) {
			array_walk(
				$results['threads'],
				function ( &$id, $key ) use ( &$thread_ids ) {
					$thread_ids[] = bb_pusher_string_hash( $id );
				}
			);
		}

		$channels = array(
			'private-bb-user-' . $creator_id,
			'private-bb-user-' . $unblocked_id,
		);

		$event_data = array(
			'creator_id'   => $creator_id,
			'unblocked_id' => $unblocked_id,
			'thread_ids'   => $thread_ids,
		);

		$bb_pusher = bb_pusher();
		if ( null !== $bb_pusher ) {
			bb_pusher_trigger_event( $bb_pusher, $channels, 'client-bb-pro-member-unblocked', $event_data );
		}
	}
}

/**
 * Fire a pusher event when the member connection setting updated.
 *
 * @since 2.1.6
 *
 * @param mixed $old_value The old option value.
 * @param mixed $new_value The new option value.
 *
 * @return void
 */
function bb_pro_pusher_member_connection_after_update( $old_value, $new_value ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	if ( $old_value !== $new_value ) {
		$channel    = 'private-bb-pro-global';
		$event_data = array(
			'is_force_friendship_to_message' => bp_force_friendship_to_message(),
		);

		$bb_pusher = bb_pusher();
		if ( null !== $bb_pusher ) {
			bb_pusher_trigger_event( $bb_pusher, $channel, 'client-bb-pro-message-is-connected', $event_data );
		}
	}
}

/**
 * Fire a pusher event when the member send connection request.
 *
 * @since 2.1.6
 *
 * @param int $friendship_id     ID of the pending friendship connection.
 * @param int $initiator_user_id ID of the friendship initiator.
 * @param int $friend_user_id    ID of the friend user.
 *
 * @return void
 */
function bb_pro_pusher_member_connection_requested( $friendship_id, $initiator_user_id, $friend_user_id ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	$channel    = 'private-bb-user-' . $friend_user_id;
	$event_name = 'client-bb-pro-member-connection-requested';

	bb_pro_comman_connection_triggers( $initiator_user_id, $friend_user_id, $initiator_user_id, $channel, $event_name );

	$channel = 'private-bb-user-' . $initiator_user_id;
	bb_pro_comman_connection_triggers( $friend_user_id, $initiator_user_id, $friend_user_id, $channel, $event_name );

}

/**
 * Fire a pusher event when the member withdrawn connection request.
 *
 * @since 2.1.6
 *
 * @param int                   $friendship_id ID of the friendship.
 * @param BP_Friends_Friendship $friendship    Friendship object. Passed by reference.
 *
 * @return void
 */
function bb_pro_pusher_member_withdrawn_connection_request( $friendship_id, $friendship ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	if ( isset( $friendship->initiator_user_id, $friendship->friend_user_id ) ) {
		$initiator_user_id = $friendship->initiator_user_id;
		$friend_user_id    = $friendship->friend_user_id;

		$channel    = 'private-bb-user-' . $friend_user_id;
		$event_name = 'client-bb-pro-member-withdrawn-connection-request';

		bb_pro_comman_connection_triggers( $initiator_user_id, $friend_user_id, $initiator_user_id, $channel, $event_name );

		$channel = 'private-bb-user-' . $initiator_user_id;
		bb_pro_comman_connection_triggers( $friend_user_id, $initiator_user_id, $friend_user_id, $channel, $event_name );

	}
}

/**
 * Fire a pusher event when the member accepted connection request.
 *
 * @since 2.1.6
 *
 * @param int $friendship_id     ID of the pending friendship connection.
 * @param int $initiator_user_id ID of the friendship initiator.
 * @param int $friend_user_id    ID of the friend user.
 *
 * @return void
 */
function bb_pro_pusher_member_accepted_friendship( $friendship_id, $initiator_user_id, $friend_user_id ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	$channel    = 'private-bb-user-' . $initiator_user_id;
	$event_name = 'client-bb-pro-member-accepted-connection-request';

	bb_pro_comman_connection_triggers( $friend_user_id, $friend_user_id, $initiator_user_id, $channel, $event_name );

	$channel = 'private-bb-user-' . $friend_user_id;
	bb_pro_comman_connection_triggers( $friend_user_id, $friend_user_id, $initiator_user_id, $channel, $event_name );
}

/**
 * Fire a pusher event when the member rejected connection request.
 *
 * @since 2.1.6
 *
 * @param int                   $friendship_id ID of the friendship.
 * @param BP_Friends_Friendship $friendship    Friendship object. Passed by reference.
 *
 * @return void
 */
function bb_pro_pusher_member_rejected_friendship( $friendship_id, $friendship ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	if ( isset( $friendship->initiator_user_id, $friendship->friend_user_id ) ) {
		$initiator_user_id = $friendship->initiator_user_id;
		$friend_user_id    = $friendship->friend_user_id;

		$channel    = 'private-bb-user-' . $friend_user_id;
		$event_name = 'client-bb-pro-member-rejected-connection-request';

		bb_pro_comman_connection_triggers( $friend_user_id, $friend_user_id, $initiator_user_id, $channel, $event_name );

		$channel = 'private-bb-user-' . $initiator_user_id;
		bb_pro_comman_connection_triggers( $initiator_user_id, $friend_user_id, $initiator_user_id, $channel, $event_name );

	}

}

/**
 * Fire a pusher event when the message access control settings updated.
 *
 * @since 2.1.6
 *
 * @param mixed $old_value The old option value.
 * @param mixed $new_value The new option value.
 *
 * @return void
 */
function bb_pro_pusher_message_access_control_after_update( $old_value, $new_value ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	$is_changed = false;
	if ( isset( $old_value['access-control-type'] ) && ! isset( $new_value['access-control-type'] ) ) {
		$is_changed = true;
	} elseif ( ! isset( $old_value['access-control-type'] ) && isset( $new_value['access-control-type'] ) ) {
		$is_changed = true;
	} elseif ( isset( $old_value, $new_value ) ) {

		if ( isset( $old_value['access-control-type'] ) && isset( $new_value['access-control-type'] ) && ( ! empty( $old_value['access-control-type'] ) || ! empty( $new_value['access-control-type'] ) ) && $old_value['access-control-type'] !== $new_value['access-control-type'] ) {
			$is_changed = true;
		} elseif ( isset( $old_value['access-control-options'] ) && isset( $new_value['access-control-options'] ) && ( ! empty( $old_value['access-control-options'] ) || ! empty( $new_value['access-control-options'] ) ) ) {

			if ( ! empty( array_merge( array_diff( $old_value['access-control-options'], $new_value['access-control-options'] ), array_diff( $new_value['access-control-options'], $old_value['access-control-options'] ) ) ) ) {
				$is_changed = true;
			} else {
				foreach ( $new_value['access-control-options'] as $option ) {
					$key = 'access-control-' . $option . '-options';
					if ( $old_value[ $key ] !== $new_value[ $key ] ) {
						$is_changed = true;
						break;
					}
				}
			}
		}
	}

	if ( $is_changed ) {
		$channel    = 'private-bb-pro-global';
		$event_name = 'client-bb-pro-message-access-control-update';
		$event_data = array();

		$bb_pusher = bb_pusher();
		if ( null !== $bb_pusher ) {
			bb_pusher_trigger_event( $bb_pusher, $channel, $event_name, $event_data );
		}
	}
}

/**
 * Fire the event for the component has been updated.
 *
 * @since 2.1.6
 *
 * @param array  $old_value Old values of array.
 * @param array  $value     New values of the array.
 * @param string $option    Option key Name.
 */
function bb_pro_pusher_active_components( $old_value, $value, $option ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled()
	) {
		return;
	}

	$channel    = 'private-bb-pro-global';
	$event_name = 'client-bb-pro-active-components';
	$event_data = array(
		'previous' => $old_value,
		'updated'  => $value,
	);

	$bb_pusher = bb_pusher();
	if ( null !== $bb_pusher ) {
		bb_pusher_trigger_event( $bb_pusher, $channel, $event_name, $event_data );
	}
}

/**
 * Fire the event when group messages has been disabled by admin.
 *
 * @since 2.1.6
 *
 * @param array  $old_value Old values of array.
 * @param array  $value     New values of the array.
 * @param string $option    Option key Name.
 */
function bb_pro_pusher_disabled_group_messages( $old_value, $value, $option ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	$event_name = 'client-bb-pro-update-group-messages';
	$event_data = array(
		'previous' => $old_value,
		'updated'  => $value,
	);

	// Determine groups of user.
	$groups = groups_get_groups(
		array(
			'fields'      => 'ids',
			'per_page'    => - 1,
			'show_hidden' => true,
			'meta_query'  => array( // phpcs:ignore
				'relation' => 'AND',
				array(
					'key'     => 'group_message_thread',
					'compare' => 'EXISTS',
				),
			),
		),
	);

	$group_ids        = ( isset( $groups['groups'] ) ? $groups['groups'] : array() );
	$threads_channels = array();
	if ( ! empty( $group_ids ) ) {

		foreach ( $group_ids as $group_id ) {
			$thread_id = groups_get_groupmeta( $group_id, 'group_message_thread' );
			if ( ! empty( $thread_id ) ) {
				$threads_channels[] = 'private-bb-message-thread-' . $thread_id;
			}
		}
	}

	$bb_pusher = bb_pusher();
	if ( null !== $bb_pusher ) {
		bb_pusher_trigger_event( $bb_pusher, $threads_channels, $event_name, $event_data );
	}
}

/**
 * Fire an event when group settings has been updated.
 *
 * @since 2.1.6
 *
 * @param int    $meta_id     ID of updated metadata entry.
 * @param int    $object_id   ID of the object metadata is for.
 * @param string $meta_key    Metadata key.
 * @param mixed  $meta_value Metadata value.
 */
function bb_pro_pusher_group_settings_update( $meta_id, $object_id, $meta_key, $meta_value ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	if ( 'message_status' !== $meta_key ) {
		return;
	}

	if ( ! bp_is_active( 'groups' ) ) {
		return;
	}

	$members = groups_get_group_members(
		array(
			'group_id' => $object_id,
		)
	);

	$member_ids = array();
	if ( ! empty( $members['members'] ) ) {
		$member_ids = wp_parse_id_list( array_column( $members['members'], 'ID' ) );

		$member_ids = array_map(
			function ( $user_id ) {
				return bb_pusher_get_user_hash( $user_id );
			},
			$member_ids
		);
	}

	$group_thread = (int) groups_get_groupmeta( $object_id, 'group_message_thread' );
	$channel      = 'private-bb-message-thread-' . $group_thread;
	$event_data   = array(
		'thread_id'  => bb_pusher_string_hash( $group_thread ),
		'updated'    => $meta_value,
		'member_ids' => $member_ids,
	);

	$bb_pusher = bb_pusher();
	if ( null !== $bb_pusher ) {
		bb_pusher_trigger_event( $bb_pusher, $channel, 'client-bb-pro-group-setting-update', $event_data );
	}
}

/**
 * Fire an event when user deleted.
 *
 * @since 2.1.6
 *
 * @param int $user_id ID of the user who is about to be deleted.
 */
function bb_pro_pusher_on_user_delete( $user_id ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	$results = BP_Messages_Thread::get_threads_for_user(
		array(
			'fields'    => 'ids',
			'user_id'   => $user_id,
			'is_hidden' => true,
		)
	);

	$thread_ids = array();
	if ( ! empty( $results ) ) {
		array_walk(
			$results['threads'],
			function ( &$id, $key ) use ( &$thread_ids ) {
				$thread_ids[] = bb_pusher_string_hash( $id );
			}
		);
	}

	$channel    = 'private-bb-pro-global';
	$event_data = array(
		'user_id'    => bb_pusher_get_user_hash( $user_id ),
		'thread_ids' => $thread_ids,
	);

	$bb_pusher = bb_pusher();
	if ( null !== $bb_pusher ) {
		bb_pusher_trigger_event( $bb_pusher, $channel, 'client-bb-pro-member-deleted', $event_data );
	}
}

/**
 * Fire pusher event when a new thread create.
 *
 * @since 2.1.6
 *
 * @param object $message Message object.
 */
function bb_pro_pusher_messages_message_new_thread_save( $message ) {

	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' ) ||
		! did_action( 'messages_message_new_thread_save' )
	) {
		return;
	}

	$channels       = array();
	$recipients     = array();
	$thread         = new BP_Messages_Thread( $message->thread_id );
	$all_recipients = $thread->get_recipients();

	if ( ! empty( $all_recipients ) ) {
		foreach ( (array) $all_recipients as $recipient ) {
			$recipients[ bb_pusher_get_user_hash( $recipient->user_id ) ] = bb_pusher_get_user_hash( $recipient->user_id );
			$channels[] = 'private-bb-user-' . $recipient->user_id;
		}
	}

	$event_data = array(
		'thread_id'  => bb_pusher_string_hash( $message->thread_id ),
		'recipients' => $recipients,
	);

	$bb_pusher = bb_pusher();
	if ( null !== $bb_pusher && ! empty( $channels ) ) {
		bb_pusher_trigger_event( $bb_pusher, $channels, 'client-bb-pro-new-thread-create', $event_data );
	}

}

/**
 * Fire pusher event when a new message create from group.
 *
 * @since 2.1.6
 *
 * @param object $message Message object.
 */
function bb_pro_pusher_messages_message_new_message_save( $message ) {

	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	$message_from   = bp_messages_get_meta( $message->id, 'message_from', true ); // group.
	$message_action = bp_messages_get_meta( $message->id, 'thread_action', true ); // group.

	if (
		'group' !== $message_from ||
		'messages_send_reply' === $message_action ||
		( bb_is_rest() && strpos( $GLOBALS['wp']->query_vars['rest_route'], 'buddyboss/v1/messages' ) !== false ) ||
		did_action( 'groups_join_group' ) ||
		did_action( 'groups_accept_invite' ) ||
		did_action( 'groups_banned_member' ) ||
		did_action( 'groups_ban_member' ) ||
		did_action( 'groups_unban_member' ) ||
		did_action( 'groups_membership_accepted' ) ||
		did_action( 'groups_leave_group' ) ||
		did_action( 'groups_remove_member' )
	) {
		return;
	}

	$recipients     = array();
	$channels       = array();
	$thread         = new BP_Messages_Thread( $message->thread_id );
	$all_recipients = $thread->get_recipients();

	if ( ! empty( $all_recipients ) ) {
		foreach ( (array) $all_recipients as $recipient ) {
			$recipients[ bb_pusher_get_user_hash( $recipient->user_id ) ] = bb_pusher_get_user_hash( $recipient->user_id );
			$channels[] = 'private-user-' . $recipient->user_id;
		}
	}

	$event_data = array(
		'thread_id'  => bb_pusher_string_hash( $message->thread_id ),
		'recipients' => $recipients,
	);

	$bb_pusher = bb_pusher();
	if ( null !== $bb_pusher ) {
		bb_pusher_trigger_event( $bb_pusher, $channels, 'client-bb-pro-new-group-message', $event_data );
	}

}

/**
 * Fire an event before group delete.
 *
 * @param int $group_id Group id.
 *
 * @return void
 */
function bb_pro_pusher_groups_before_delete_group( $group_id ) {
	$group_thread = (int) groups_get_groupmeta( (int) $group_id, 'group_message_thread' );
	$group_name   = bp_get_group_name( groups_get_group( $group_id ) );

	if ( $group_thread > 0 ) {
		add_action(
			'groups_delete_group',
			function ( $group_id ) use ( $group_thread, $group_name ) {
				$channel    = 'private-bb-message-thread-' . $group_thread;
				$event_data = array(
					'thread_id'  => bb_pusher_string_hash( $group_thread ),
					'group_name' => $group_name,
				);

				$bb_pusher = bb_pusher();
				if ( null !== $bb_pusher ) {
					bb_pusher_trigger_event( $bb_pusher, $channel, 'client-bb-pro-group-thread-deleted', $event_data );
				}
			},
			99,
			1
		);
	}
}

/**
 * Added settings param for the rest API.
 *
 * @since 2.1.6
 *
 * @param WP_REST_Response $response The response data.
 *
 * @return void
 */
function bb_pro_pusher_rest_settings( $response ) {
	$data = $response->get_data();

	if ( isset( $data['platform'] ) && bb_pusher_is_enabled() && bbp_pro_is_license_valid() ) {
		$data['platform']['pusher_app_key']     = bb_pusher_app_key();
		$data['platform']['pusher_app_cluster'] = bb_pusher_cluster();
		$pusher_features                        = array_keys( bb_get_pusher_features() );
		$data['platform']['pusher_features']    = array();
		if ( ! empty( $pusher_features ) ) {
			foreach ( $pusher_features as $key ) {
				$data['platform']['pusher_features'][ $key ] = bb_pusher_is_feature_enabled( $key );
			}
		}
	}

	$response->set_data( $data );
}

/**
 * Events fired when user banned for the group message.
 *
 * @since 2.1.6
 *
 * @param int $thread_id Thread id.
 * @param int $user_id   User id.
 * @param int $group_id  Group id.
 *
 * @return void
 */
function bb_pro_pusher_group_messages_banned_member( $thread_id, $user_id, $group_id ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	$channel   = 'private-bb-message-thread-' . $thread_id;
	$bb_pusher = bb_pusher();

	$event_data = array(
		'action'    => 'group_message_group_ban',
		'thread_id' => bb_pusher_string_hash( $thread_id ),
		'sender_id' => $user_id,
	);

	if ( null !== $bb_pusher ) {
		bb_pusher_trigger_event( $bb_pusher, $channel, 'client-bb-pro-group-message-group-update-notify', $event_data );
	}
}

/**
 * Events fired when user unbanned for the group message.
 *
 * @since 2.1.6
 *
 * @param int $thread_id Thread id.
 * @param int $user_id   User id.
 * @param int $group_id  Group id.
 *
 * @return void
 */
function bb_pro_pusher_group_messages_unbanned_member( $thread_id, $user_id, $group_id ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	$channels  = array( 'private-bb-message-thread-' . $thread_id, 'private-user-' . $user_id );
	$bb_pusher = bb_pusher();

	$event_data = array(
		'action'    => 'group_message_group_un_ban',
		'thread_id' => bb_pusher_string_hash( $thread_id ),
		'sender_id' => $user_id,
	);

	if ( null !== $bb_pusher ) {
		bb_pusher_trigger_event( $bb_pusher, $channels, 'client-bb-pro-group-message-group-update-notify', $event_data );
	}
}

/**
 * Events fired when user joined the group.
 *
 * @since 2.1.6
 *
 * @param int $group_id Group id.
 * @param int $user_id  User id.
 *
 * @return void
 */
function bb_pro_pusher_group_messages_member_joined( $group_id, $user_id ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	$group_thread = (int) groups_get_groupmeta( (int) $group_id, 'group_message_thread' );

	if ( $group_thread > 0 ) {

		$channels  = array( 'private-bb-message-thread-' . $group_thread, 'private-bb-user-' . $user_id );
		$bb_pusher = bb_pusher();

		$event_data = array(
			'action'    => 'group_message_group_joined',
			'thread_id' => bb_pusher_string_hash( $group_thread ),
			'sender_id' => $user_id,
		);

		if ( null !== $bb_pusher ) {
			bb_pusher_trigger_event( $bb_pusher, $channels, 'client-bb-pro-group-message-group-update-notify', $event_data );
		}
	}

}

/**
 * Events fired when user left the group.
 *
 * @since 2.1.6
 *
 * @param int $group_id Group id.
 * @param int $user_id  User id.
 *
 * @return void
 */
function bb_pro_pusher_group_messages_member_left( $group_id, $user_id ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	$action = 'group_message_group_left';
	if ( 'groups_remove_member' === current_filter() ) {
		$action = 'groups_remove_member';
	}

	$group_thread = (int) groups_get_groupmeta( (int) $group_id, 'group_message_thread' );

	if ( $group_thread > 0 ) {

		$channel   = 'private-bb-message-thread-' . $group_thread;
		$bb_pusher = bb_pusher();

		$event_data = array(
			'action'    => $action,
			'thread_id' => bb_pusher_string_hash( $group_thread ),
			'sender_id' => $user_id,
		);

		if ( null !== $bb_pusher ) {
			bb_pusher_trigger_event( $bb_pusher, $channel, 'client-bb-pro-group-message-group-update-notify', $event_data );
		}
	}

}

/**
 * Common function to fire connections related to events.
 *
 * @since 2.1.6
 *
 * @param int    $user_id           User id to find the thread.
 * @param int    $friend_user_id    Friend user id.
 * @param int    $initiator_user_id Initiator user id.
 * @param string $channel           Channel name.
 * @param string $event_name        Event name.
 */
function bb_pro_comman_connection_triggers( $user_id, $friend_user_id, $initiator_user_id, $channel, $event_name ) {

	// Get friend user threads.
	$results = BP_Messages_Thread::get_threads_for_user(
		array(
			'fields'    => 'ids',
			'user_id'   => $user_id,
			'is_hidden' => true,
		)
	);

	$thread_ids = array();
	if ( ! empty( $results ) ) {
		array_walk(
			$results['threads'],
			function ( &$id, $key ) use ( &$thread_ids ) {
				$thread_ids[] = bb_pusher_string_hash( $id );
			}
		);
	}

	$event_data = array(
		'initiator_user_id' => $initiator_user_id,
		'friend_user_id'    => $friend_user_id,
		'thread_ids'        => $thread_ids,
	);

	$bb_pusher = bb_pusher();
	if ( null !== $bb_pusher ) {
		bb_pusher_trigger_event( $bb_pusher, $channel, $event_name, $event_data );
	}

}

/**
 * AJAX callback to updated message information.
 *
 * @since 2.1.6
 *
 * @return void
 */
function bb_pusher_update_message_content() {
	if ( ! bp_is_post_request() ) {
		return;
	}

	$thread_id = filter_input( INPUT_POST, 'thread_id', FILTER_SANITIZE_NUMBER_INT );
	$new_reply = filter_input( INPUT_POST, 'message_id', FILTER_SANITIZE_NUMBER_INT );
	$hash      = filter_input( INPUT_POST, 'hash', FILTER_SANITIZE_STRING );

	// Get the message by pretending we're in the message loop.
	global $thread_template, $media_template, $document_template, $video_template;

	$bp           = buddypress();
	$reset_action = $bp->current_action;

	// Override bp_current_action().
	$bp->current_action = 'view';

	bp_thread_has_messages(
		array(
			'thread_id' => $thread_id,
		)
	);

	$messages = BP_Messages_Message::get(
		array(
			'include'         => array( $new_reply ),
			'include_threads' => array( $thread_id ),
			'per_page'        => 1,
		)
	);

	// Set current message to current key.
	$thread_template->current_message = - 1;

	// Now manually iterate message like we're in the loop.
	bp_thread_the_message();

	// Manually call oEmbed
	// this is needed because we're not at the beginning of the loop.
	bp_messages_embed();

	if ( ! empty( $messages ) && ! empty( $messages['messages'] ) ) {
		$thread_template->message = current( $messages['messages'] );
	}

	$excerpt = wp_trim_words( wp_strip_all_tags( bb_get_the_thread_message_excerpt() ) );
	if ( empty( $excerpt ) ) {
		if ( bp_is_active( 'media' ) && bp_is_messages_media_support_enabled() ) {
			$media_ids = bp_messages_get_meta( bp_get_the_thread_message_id(), 'bp_media_ids', true );

			if ( ! empty( $media_ids ) ) {
				$media_ids = explode( ',', $media_ids );
				if ( count( $media_ids ) < 2 ) {
					$excerpt = __( 'sent a photo', 'buddyboss-pro' );
				} else {
					$excerpt = __( 'sent some photos', 'buddyboss-pro' );
				}
			}
		}

		if ( bp_is_active( 'media' ) && bp_is_messages_video_support_enabled() ) {
			$video_ids = bp_messages_get_meta( bp_get_the_thread_message_id(), 'bp_video_ids', true );

			if ( ! empty( $video_ids ) ) {
				$video_ids = explode( ',', $video_ids );
				if ( count( $video_ids ) < 2 ) {
					$excerpt = __( 'sent a video', 'buddyboss-pro' );
				} else {
					$excerpt = __( 'sent some videos', 'buddyboss-pro' );
				}
			}
		}

		if ( bp_is_active( 'media' ) && bp_is_messages_document_support_enabled() ) {
			$document_ids = bp_messages_get_meta( bp_get_the_thread_message_id(), 'bp_document_ids', true );

			if ( ! empty( $document_ids ) ) {
				$document_ids = explode( ',', $document_ids );
				if ( count( $document_ids ) < 2 ) {
					$excerpt = __( 'sent a document', 'buddyboss-pro' );
				} else {
					$excerpt = __( 'sent some documents', 'buddyboss-pro' );
				}
			}
		}

		if ( bp_is_active( 'media' ) && bp_is_messages_gif_support_enabled() ) {
			$gif_data = bp_messages_get_meta( bp_get_the_thread_message_id(), '_gif_data', true );

			if ( ! empty( $gif_data ) ) {
				$excerpt = __( 'sent a GIF', 'buddyboss-pro' );
			}
		}
	}

	// Output single message template part.
	$reply = array(
		'id'                => bp_get_the_thread_message_id(),
		'content'           => do_shortcode( bp_get_the_thread_message_content() ),
		'sender_id'         => bp_get_the_thread_message_sender_id(),
		'sender_name'       => esc_html( bp_get_the_thread_message_sender_name() ),
		'is_deleted'        => empty( get_userdata( bp_get_the_thread_message_sender_id() ) ) ? 1 : 0,
		'sender_link'       => bp_get_the_thread_message_sender_link(),
		'sender_is_you'     => bp_get_the_thread_message_sender_id() === bp_loggedin_user_id(),
		'sender_avatar'     => esc_url(
			bp_core_fetch_avatar(
				array(
					'item_id' => bp_get_the_thread_message_sender_id(),
					'object'  => 'user',
					'type'    => 'thumb',
					'width'   => 32,
					'height'  => 32,
					'html'    => false,
				)
			)
		),
		'date'              => bp_get_the_thread_message_date_sent() * 1000,
		'display_date'      => bb_get_the_thread_message_sent_time(),
		'display_date_list' => bb_get_thread_sent_date( $thread_template->message->date_sent ),
		'excerpt'           => $excerpt,
		'sent_date'         => ucfirst( bb_get_thread_start_date( $thread_template->message->date_sent ) ),
		'sent_split_date'   => date_i18n( 'Y-m-d', strtotime( $thread_template->message->date_sent ) ),
	);

	if ( bp_is_active( 'moderation' ) ) {
		$reply['is_user_suspended'] = bp_moderation_is_user_suspended( bp_get_the_thread_message_sender_id() );
		$reply['is_user_blocked']   = bp_moderation_is_user_blocked( bp_get_the_thread_message_sender_id() );
	}

	if ( bp_is_active( 'messages', 'star' ) ) {

		$star_link = bp_get_the_message_star_action_link(
			array(
				'message_id' => bp_get_the_thread_message_id(),
				'url_only'   => true,
			)
		);

		$reply['star_link']  = $star_link;
		$reply['is_starred'] = array_search( 'unstar', explode( '/', $star_link ), true );

	}

	if ( bp_is_active( 'media' ) && bp_is_messages_media_support_enabled() ) {
		$media_ids = bp_messages_get_meta( bp_get_the_thread_message_id(), 'bp_media_ids', true );

		if ( ! empty( $media_ids ) && bp_has_media(
			array(
				'include'  => $media_ids,
				'privacy'  => array( 'message' ),
				'order_by' => 'menu_order',
				'sort'     => 'ASC',
			)
		) ) {
			$reply['media'] = array();
			while ( bp_media() ) {
				bp_the_media();

				$reply['media'][] = array(
					'id'            => bp_get_media_id(),
					'title'         => bp_get_media_title(),
					'message_id'    => bp_get_the_thread_message_id(),
					'thread_id'     => bp_get_the_thread_id(),
					'attachment_id' => bp_get_media_attachment_id(),
					'thumbnail'     => bp_get_media_attachment_image_thumbnail(),
					'full'          => bb_get_media_photos_theatre_popup_image(),
					'meta'          => $media_template->media->attachment_data->meta,
					'privacy'       => bp_get_media_privacy(),
					'height'        => ( isset( $media_template->media->attachment_data->meta['height'] ) ? $media_template->media->attachment_data->meta['height'] : '' ),
					'width'         => ( isset( $media_template->media->attachment_data->meta['width'] ) ? $media_template->media->attachment_data->meta['width'] : '' ),
				);
			}
		}
	}

	if ( bp_is_active( 'video' ) && bp_is_messages_video_support_enabled() ) {
		$video_ids = bp_messages_get_meta( bp_get_the_thread_message_id(), 'bp_video_ids', true );

		if (
			! empty( $video_ids ) &&
			bp_has_video(
				array(
					'include'  => $video_ids,
					'privacy'  => array( 'message' ),
					'order_by' => 'menu_order',
					'sort'     => 'ASC',
				)
			)
		) {
			$reply['video'] = array();
			while ( bp_video() ) {
				bp_the_video();

				$video_html = '';
				if ( 1 === $video_template->video_count ) {
					ob_start();
					bp_get_template_part( 'video/single-video' );
					?>
					<p class="bb-video-loader"></p>
					<?php
					if ( ! empty( bp_get_video_length() ) ) {
						?>
						<p class="bb-video-duration"><?php bp_video_length(); ?></p>
						<?php
					}
					$thumbnail_url = bb_video_get_thumb_url( bp_get_video_id(), bp_get_video_attachment_id(), 'bb-video-profile-album-add-thumbnail-directory-poster-image' );

					if ( empty( $thumbnail_url ) ) {
						$thumbnail_url = bb_get_video_default_placeholder_image();
					}
					?>
					<a class="bb-open-video-theatre bb-video-cover-wrap bb-item-cover-wrap hide" data-id="<?php bp_video_id(); ?>" data-attachment-full="<?php bp_video_popup_thumb(); ?>" data-privacy="<?php bp_video_privacy(); ?>"  data-attachment-id="<?php bp_video_attachment_id(); ?>" href="#">
						<img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php bp_video_title(); ?>" />
					</a>
					<?php
					$video_html = ob_get_clean();
					$video_html = str_replace( 'video-js', 'video-js single-activity-video', $video_html );
					$video_html = str_replace( 'id="theatre-video', 'id="video', $video_html );

				}

				$reply['video'][] = array(
					'id'            => bp_get_video_id(),
					'title'         => bp_get_video_title(),
					'message_id'    => bp_get_the_thread_message_id(),
					'thread_id'     => bp_get_the_thread_id(),
					'attachment_id' => bp_get_video_attachment_id(),
					'thumbnail'     => bp_get_video_attachment_image_thumbnail(),
					'full'          => bp_get_video_attachment_image(),
					'meta'          => $video_template->video->attachment_data->meta,
					'privacy'       => bp_get_video_privacy(),
					'video_html'    => $video_html,
				);
			}
		}
	}

	if ( bp_is_active( 'media' ) && bp_is_messages_document_support_enabled() ) {
		$document_ids = bp_messages_get_meta( bp_get_the_thread_message_id(), 'bp_document_ids', true );

		if ( ! empty( $document_ids ) && bp_has_document(
			array(
				'include'  => $document_ids,
				'order_by' => 'menu_order',
				'sort'     => 'ASC',
			)
		) ) {
			$reply['document'] = array();
			while ( bp_document() ) {
				bp_the_document();

				$attachment_id         = bp_get_document_attachment_id();
				$extension             = bp_document_extension( $attachment_id );
				$svg_icon              = bp_document_svg_icon( $extension, $attachment_id );
				$svg_icon_download     = bp_document_svg_icon( 'download' );
				$download_url          = bp_document_download_link( $attachment_id, bp_get_document_id() );
				$filename              = basename( get_attached_file( $attachment_id ) );
				$size                  = bp_document_size_format( filesize( get_attached_file( $attachment_id ) ) );
				$extension_description = '';
				$extension_lists       = bp_document_extensions_list();
				$text_attachment_url   = wp_get_attachment_url( $attachment_id );
				$mirror_text           = bp_document_mirror_text( $attachment_id );
				$audio_url             = '';

				if ( ! empty( $extension_lists ) ) {
					$extension_lists = array_column( $extension_lists, 'description', 'extension' );
					$extension_name  = '.' . $extension;
					if ( ! empty( $extension_lists ) && ! empty( $extension ) && array_key_exists( $extension_name, $extension_lists ) ) {
						$extension_description = '<span class="document-extension-description">' . esc_html( $extension_lists[ $extension_name ] ) . '</span>';
					}
				}

				if ( in_array( $extension, bp_get_document_preview_music_extensions(), true ) ) {
					$audio_url = bp_document_get_preview_url( bp_get_document_id(), $attachment_id );
				}

				$output = '';
				ob_start();

				if ( in_array( $extension, bp_get_document_preview_music_extensions(), true ) ) {
					$audio_url = bp_document_get_preview_url( bp_get_document_id(), $attachment_id );
					?>
					<div class="document-audio-wrap">
						<audio controls controlsList="nodownload">
							<source src="<?php echo esc_url( $audio_url ); ?>" type="audio/mpeg">
							<?php esc_html_e( 'Your browser does not support the audio element.', 'buddyboss-pro' ); ?>
						</audio>
					</div>
					<?php
				}

				$attachment_url      = bp_document_get_preview_url( bp_get_document_id(), bp_get_document_attachment_id(), 'bb-document-pdf-preview-activity-image' );
				$full_attachment_url = bp_document_get_preview_url( bp_get_document_id(), bp_get_document_attachment_id(), 'bb-document-pdf-image-popup-image' );

				if ( $attachment_url ) {
					?>
					<div class="document-preview-wrap">
						<img src="<?php echo esc_url( $attachment_url ); ?>" alt=""/>
					</div><!-- .document-preview-wrap -->
					<?php
				}
				$sizes = is_file( get_attached_file( $attachment_id ) ) ? get_attached_file( $attachment_id ) : 0;
				if ( $sizes && filesize( $sizes ) / 1e+6 < 2 ) {
					if ( in_array( $extension, bp_get_document_preview_code_extensions(), true ) ) {
						$data      = bp_document_get_preview_text_from_attachment( $attachment_id );
						$file_data = $data['text'];
						$more_text = $data['more_text']
						?>
						<div class="document-text-wrap">
							<div class="document-text" data-extension="<?php echo esc_attr( $extension ); ?>">
								<textarea class="document-text-file-data-hidden" style="display: none;"><?php echo wp_kses_post( $file_data ); ?></textarea>
							</div>
							<div class="document-expand">
								<a href="#" class="document-expand-anchor">
									<i class="bb-icon-l bb-icon-plus document-icon-plus"></i> <?php esc_html_e( 'Click to expand', 'buddyboss-pro' ); ?>
								</a>
							</div>
						</div> <!-- .document-text-wrap -->
						<?php
						if ( true === $more_text ) {

							printf(
							/* translators: %s: download string */
								'<div class="more_text_view">%s</div>',
								sprintf(
								/* translators: %s: download url */
									wp_kses_post( 'This file was truncated for preview. Please <a href="%s">download</a> to view the full file.', 'buddyboss-pro' ),
									esc_url( $download_url )
								)
							);
						}
					}
				}

				$output .= ob_get_clean();

				$reply['document'][] = array(
					'id'                    => bp_get_document_id(),
					'title'                 => bp_get_document_title(),
					'attachment_id'         => bp_get_document_attachment_id(),
					'url'                   => $download_url,
					'extension'             => $extension,
					'svg_icon'              => $svg_icon,
					'svg_icon_download'     => $svg_icon_download,
					'filename'              => $filename,
					'size'                  => $size,
					'meta'                  => $document_template->document->attachment_data->meta,
					'download_text'         => __( 'Click to view', 'buddyboss-pro' ),
					'extension_description' => $extension_description,
					'download'              => __( 'Download', 'buddyboss-pro' ),
					'collapse'              => __( 'Collapse', 'buddyboss-pro' ),
					'copy_download_link'    => __( 'Copy Download Link', 'buddyboss-pro' ),
					'more_action'           => __( 'More actions', 'buddyboss-pro' ),
					'privacy'               => bp_get_db_document_privacy(),
					'author'                => bp_get_document_user_id(),
					'preview'               => $attachment_url,
					'full_preview'          => ( '' !== $full_attachment_url ) ? $full_attachment_url : $attachment_url,
					'msg_preview'           => $output,
					'text_preview'          => $text_attachment_url ? esc_url( $text_attachment_url ) : '',
					'mp3_preview'           => $audio_url ? $audio_url : '',
					'document_title'        => $filename ? $filename : '',
					'mirror_text'           => $mirror_text ? $mirror_text : '',
				);
			}
		}
	}

	if ( bp_is_active( 'media' ) && bp_is_messages_gif_support_enabled() ) {
		$gif_data = bp_messages_get_meta( bp_get_the_thread_message_id(), '_gif_data', true );

		if ( ! empty( $gif_data ) ) {
			$preview_url  = ( is_int( $gif_data['still'] ) ) ? wp_get_attachment_url( $gif_data['still'] ) : $gif_data['still'];
			$video_url    = ( is_int( $gif_data['mp4'] ) ) ? wp_get_attachment_url( $gif_data['mp4'] ) : $gif_data['mp4'];
			$reply['gif'] = array(
				'preview_url' => $preview_url,
				'video_url'   => $video_url,
			);
		}
	}

	$extra_content = bp_nouveau_messages_catch_hook_content(
		array(
			'beforeMeta'    => 'bp_before_message_meta',
			'afterMeta'     => 'bp_after_message_meta',
			'beforeContent' => 'bp_before_message_content',
			'afterContent'  => 'bp_after_message_content',
		)
	);

	if ( array_filter( $extra_content ) ) {
		$reply = array_merge( $reply, $extra_content );
	}

	// Clean up the loop.
	bp_thread_messages();

	// Remove the bp_current_action() override.
	$bp->current_action = $reset_action;

	wp_send_json_success(
		array(
			'messages' => array( $reply ),
			'hash'     => $hash,
		)
	);

}

/**
 * Added support for message success event on rest.
 *
 * @since 2.1.6
 *
 * @param BP_Messages_Thread  $thread   Thread object.
 * @param WP_REST_Response    $response The response data.
 * @param WP_REST_Request     $request  The request sent to the API.
 * @param BP_Messages_Message $message  Message object.
 *
 * @return void
 */
function bb_pusher_rest_create_message( $thread, $response, $request, $message ) {

	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	global $thread_template, $media_template, $document_template, $video_template;

	$bb_pusher = bb_pusher();
	$data      = $response->get_data();
	$hash      = $request->get_param( 'hash' );

	if ( ! empty( $thread ) && (int) $thread->thread_id > 0 && null !== $bb_pusher && bp_is_active( 'messages' ) ) {

		$last_message_id = $message->id;

		bp_thread_has_messages(
			array(
				'thread_id' => $thread->thread_id,
				'before'    => bp_core_current_time(),
			)
		);

		$messages = BP_Messages_Message::get(
			array(
				'include'         => array( $last_message_id ),
				'include_threads' => array( $thread->thread_id ),
				'per_page'        => 1,
			)
		);

		// Set current message to current key.
		$thread_template->current_message = - 1;

		// Now manually iterate message like we're in the loop.
		bp_thread_the_message();

		// Manually call oEmbed
		// this is needed because we're not at the beginning of the loop.
		bp_messages_embed();

		if ( ! empty( $messages ) && ! empty( $messages['messages'] ) ) {
			$thread_template->message = current( $messages['messages'] );
		}

		$excerpt = wp_trim_words( wp_strip_all_tags( bb_get_the_thread_message_excerpt() ) );
		if ( empty( $excerpt ) ) {
			if ( bp_is_active( 'media' ) && bp_is_messages_media_support_enabled() ) {
				$media_ids = bp_messages_get_meta( bp_get_the_thread_message_id(), 'bp_media_ids', true );

				if ( ! empty( $media_ids ) ) {
					$media_ids = explode( ',', $media_ids );
					if ( count( $media_ids ) < 2 ) {
						$excerpt = __( 'sent a photo', 'buddyboss-pro' );
					} else {
						$excerpt = __( 'sent some photos', 'buddyboss-pro' );
					}
				}
			}

			if ( bp_is_active( 'media' ) && bp_is_messages_video_support_enabled() ) {
				$video_ids = bp_messages_get_meta( bp_get_the_thread_message_id(), 'bp_video_ids', true );

				if ( ! empty( $video_ids ) ) {
					$video_ids = explode( ',', $video_ids );
					if ( count( $video_ids ) < 2 ) {
						$excerpt = __( 'sent a video', 'buddyboss-pro' );
					} else {
						$excerpt = __( 'sent some videos', 'buddyboss-pro' );
					}
				}
			}

			if ( bp_is_active( 'media' ) && bp_is_messages_document_support_enabled() ) {
				$document_ids = bp_messages_get_meta( bp_get_the_thread_message_id(), 'bp_document_ids', true );

				if ( ! empty( $document_ids ) ) {
					$document_ids = explode( ',', $document_ids );
					if ( count( $document_ids ) < 2 ) {
						$excerpt = __( 'sent a document', 'buddyboss-pro' );
					} else {
						$excerpt = __( 'sent some documents', 'buddyboss-pro' );
					}
				}
			}

			if ( bp_is_active( 'media' ) && bp_is_messages_gif_support_enabled() ) {
				$gif_data = bp_messages_get_meta( bp_get_the_thread_message_id(), '_gif_data', true );

				if ( ! empty( $gif_data ) ) {
					$excerpt = __( 'sent a GIF', 'buddyboss-pro' );
				}
			}
		}

		// Output single message template part.
		$reply = array(
			'id'                => bp_get_the_thread_message_id(),
			'content'           => do_shortcode( bp_get_the_thread_message_content() ),
			'sender_id'         => bp_get_the_thread_message_sender_id(),
			'sender_name'       => esc_html( bp_get_the_thread_message_sender_name() ),
			'is_deleted'        => empty( get_userdata( bp_get_the_thread_message_sender_id() ) ) ? 1 : 0,
			'sender_link'       => bp_get_the_thread_message_sender_link(),
			'sender_is_you'     => bp_get_the_thread_message_sender_id() === bp_loggedin_user_id(),
			'sender_avatar'     => esc_url(
				bp_core_fetch_avatar(
					array(
						'item_id' => bp_get_the_thread_message_sender_id(),
						'object'  => 'user',
						'type'    => 'thumb',
						'width'   => 32,
						'height'  => 32,
						'html'    => false,
					)
				)
			),
			'date'              => bp_get_the_thread_message_date_sent() * 1000,
			'display_date'      => bb_get_the_thread_message_sent_time(),
			'display_date_list' => bb_get_thread_sent_date( $thread_template->message->date_sent ),
			'excerpt'           => $excerpt,
			'sent_date'         => ucfirst( bb_get_thread_start_date( $thread_template->message->date_sent ) ),
			'sent_split_date'   => date_i18n( 'Y-m-d', strtotime( $thread_template->message->date_sent ) ),
			'is_new'            => true,
		);

		if ( bp_is_active( 'moderation' ) ) {
			$reply['is_user_suspended'] = bp_moderation_is_user_suspended( bp_get_the_thread_message_sender_id() );
			$reply['is_user_blocked']   = bp_moderation_is_user_blocked( bp_get_the_thread_message_sender_id() );
		}

		if ( bp_is_active( 'media' ) && bp_is_messages_media_support_enabled() ) {
			$media_ids = bp_messages_get_meta( bp_get_the_thread_message_id(), 'bp_media_ids', true );

			if (
				! empty( $media_ids ) &&
				bp_has_media(
					array(
						'include'  => $media_ids,
						'privacy'  => array( 'message' ),
						'order_by' => 'menu_order',
						'sort'     => 'ASC',
					)
				)
			) {
				$reply['media'] = array();
				while ( bp_media() ) {
					bp_the_media();

					$reply['media'][] = array(
						'id'            => bp_get_media_id(),
						'title'         => bp_get_media_title(),
						'message_id'    => bp_get_the_thread_message_id(),
						'thread_id'     => bp_get_the_thread_id(),
						'attachment_id' => bp_get_media_attachment_id(),
						'thumbnail'     => bp_get_media_attachment_image_thumbnail(),
						'full'          => bb_get_media_photos_theatre_popup_image(),
						'meta'          => $media_template->media->attachment_data->meta,
						'privacy'       => bp_get_media_privacy(),
						'height'        => ( isset( $media_template->media->attachment_data->meta['height'] ) ? $media_template->media->attachment_data->meta['height'] : '' ),
						'width'         => ( isset( $media_template->media->attachment_data->meta['width'] ) ? $media_template->media->attachment_data->meta['width'] : '' ),
					);
				}
			}
		}

		if ( bp_is_active( 'video' ) && bp_is_messages_video_support_enabled() ) {
			$video_ids = bp_messages_get_meta( bp_get_the_thread_message_id(), 'bp_video_ids', true );

			if (
				! empty( $video_ids ) &&
				bp_has_video(
					array(
						'include'  => $video_ids,
						'privacy'  => array( 'message' ),
						'order_by' => 'menu_order',
						'sort'     => 'ASC',
					)
				)
			) {
				$reply['video'] = array();
				while ( bp_video() ) {
					bp_the_video();

					$video_html = '';
					if ( 1 === $video_template->video_count ) {
						ob_start();
						bp_get_template_part( 'video/single-video' );
						?>
						<p class="bb-video-loader"></p>
						<?php
						if ( ! empty( bp_get_video_length() ) ) {
							?>
							<p class="bb-video-duration"><?php bp_video_length(); ?></p>
							<?php
						}
						$thumbnail_url = bb_video_get_thumb_url( bp_get_video_id(), bp_get_video_attachment_id(), 'bb-video-profile-album-add-thumbnail-directory-poster-image' );

						if ( empty( $thumbnail_url ) ) {
							$thumbnail_url = bb_get_video_default_placeholder_image();
						}
						?>
						<a class="bb-open-video-theatre bb-video-cover-wrap bb-item-cover-wrap hide" data-id="<?php bp_video_id(); ?>" data-attachment-full="<?php bp_video_popup_thumb(); ?>" data-privacy="<?php bp_video_privacy(); ?>"  data-attachment-id="<?php bp_video_attachment_id(); ?>" href="#">
							<img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php bp_video_title(); ?>" />
						</a>
						<?php
						$video_html = ob_get_clean();
						$video_html = str_replace( 'video-js', 'video-js single-activity-video', $video_html );
						$video_html = str_replace( 'id="theatre-video', 'id="video', $video_html );

					}

					$reply['video'][] = array(
						'id'            => bp_get_video_id(),
						'title'         => bp_get_video_title(),
						'message_id'    => bp_get_the_thread_message_id(),
						'thread_id'     => bp_get_the_thread_id(),
						'attachment_id' => bp_get_video_attachment_id(),
						'thumbnail'     => bp_get_video_attachment_image_thumbnail(),
						'full'          => bp_get_video_attachment_image(),
						'meta'          => $video_template->video->attachment_data->meta,
						'privacy'       => bp_get_video_privacy(),
						'video_html'    => $video_html,
					);
				}
			}
		}

		if ( bp_is_active( 'media' ) && bp_is_messages_document_support_enabled() ) {
			$document_ids = bp_messages_get_meta( bp_get_the_thread_message_id(), 'bp_document_ids', true );

			if (
				! empty( $document_ids ) &&
				bp_has_document(
					array(
						'include'  => $document_ids,
						'order_by' => 'menu_order',
						'sort'     => 'ASC',
					)
				)
			) {
				$reply['document'] = array();
				while ( bp_document() ) {
					bp_the_document();

					$document_id           = bp_get_document_id();
					$attachment_id         = bp_get_document_attachment_id();
					$extension             = bp_document_extension( $attachment_id );
					$svg_icon              = bp_document_svg_icon( $extension, $attachment_id );
					$svg_icon_download     = bp_document_svg_icon( 'download' );
					$download_url          = bp_document_download_link( $attachment_id, $document_id );
					$filename              = basename( get_attached_file( $attachment_id ) );
					$size                  = bp_document_size_format( filesize( get_attached_file( $attachment_id ) ) );
					$extension_description = '';
					$extension_lists       = bp_document_extensions_list();
					$text_attachment_url   = wp_get_attachment_url( $attachment_id );
					$mirror_text           = bp_document_mirror_text( $attachment_id );
					$audio_url             = '';
					$video_url             = '';

					if ( ! empty( $extension_lists ) ) {
						$extension_lists = array_column( $extension_lists, 'description', 'extension' );
						$extension_name  = '.' . $extension;
						if ( ! empty( $extension_lists ) && ! empty( $extension ) && array_key_exists( $extension_name, $extension_lists ) ) {
							$extension_description = '<span class="document-extension-description">' . esc_html( $extension_lists[ $extension_name ] ) . '</span>';
						}
					}

					if ( in_array( $extension, bp_get_document_preview_video_extensions(), true ) ) {
						$video_url = bb_document_video_get_symlink( $document_id, true );
					}

					if ( in_array( $extension, bp_get_document_preview_music_extensions(), true ) ) {
						$audio_url = bp_document_get_preview_url( $document_id, $attachment_id );
					}

					$output = '';
					ob_start();

					if ( in_array( $extension, bp_get_document_preview_music_extensions(), true ) ) {
						$audio_url = bp_document_get_preview_url( bp_get_document_id(), $attachment_id );
						?>
						<div class="document-audio-wrap">
							<audio controls controlsList="nodownload">
								<source src="<?php echo esc_url( $audio_url ); ?>" type="audio/mpeg">
								<?php esc_html_e( 'Your browser does not support the audio element.', 'buddyboss-pro' ); ?>
							</audio>
						</div>
						<?php
					}

					$attachment_url      = bp_document_get_preview_url( $document_id, bp_get_document_attachment_id(), 'bb-document-pdf-preview-activity-image' );
					$full_attachment_url = bp_document_get_preview_url( $document_id, bp_get_document_attachment_id(), 'bb-document-pdf-image-popup-image' );

					if ( $attachment_url ) {
						?>
						<div class="document-preview-wrap">
							<img src="<?php echo esc_url( $attachment_url ); ?>" alt=""/>
						</div><!-- .document-preview-wrap -->
						<?php
					}
					$sizes = is_file( get_attached_file( $attachment_id ) ) ? get_attached_file( $attachment_id ) : 0;
					if ( $sizes && filesize( $sizes ) / 1e+6 < 2 ) {
						if ( in_array( $extension, bp_get_document_preview_code_extensions(), true ) ) {
							$data      = bp_document_get_preview_text_from_attachment( $attachment_id );
							$file_data = $data['text'];
							$more_text = $data['more_text']
							?>
							<div class="document-text-wrap">
								<div class="document-text" data-extension="<?php echo esc_attr( $extension ); ?>">
									<textarea class="document-text-file-data-hidden" style="display: none;"><?php echo wp_kses_post( $file_data ); ?></textarea>
								</div>
								<div class="document-expand">
									<a href="#" class="document-expand-anchor">
										<i class="bb-icon-l bb-icon-plus document-icon-plus"></i> <?php esc_html_e( 'Click to expand', 'buddyboss-pro' ); ?>
									</a>
								</div>
							</div> <!-- .document-text-wrap -->
							<?php
							if ( true === $more_text ) {

								printf(
								/* translators: %s: download string */
									'<div class="more_text_view">%s</div>',
									sprintf(
									/* translators: %s: download url */
										wp_kses_post( 'This file was truncated for preview. Please <a href="%s">download</a> to view the full file.', 'buddyboss-pro' ),
										esc_url( $download_url )
									)
								);
							}
						}
					}

					$output .= ob_get_clean();

					$reply['document'][] = array(
						'id'                    => bp_get_document_id(),
						'title'                 => bp_get_document_title(),
						'attachment_id'         => bp_get_document_attachment_id(),
						'url'                   => $download_url,
						'extension'             => $extension,
						'svg_icon'              => $svg_icon,
						'svg_icon_download'     => $svg_icon_download,
						'filename'              => $filename,
						'size'                  => $size,
						'meta'                  => $document_template->document->attachment_data->meta,
						'download_text'         => __( 'Click to view', 'buddyboss-pro' ),
						'extension_description' => $extension_description,
						'download'              => __( 'Download', 'buddyboss-pro' ),
						'collapse'              => __( 'Collapse', 'buddyboss-pro' ),
						'copy_download_link'    => __( 'Copy Download Link', 'buddyboss-pro' ),
						'more_action'           => __( 'More actions', 'buddyboss-pro' ),
						'privacy'               => bp_get_db_document_privacy(),
						'author'                => bp_get_document_user_id(),
						'preview'               => $attachment_url,
						'full_preview'          => ( '' !== $full_attachment_url ) ? $full_attachment_url : $attachment_url,
						'msg_preview'           => $output,
						'text_preview'          => $text_attachment_url ? esc_url( $text_attachment_url ) : '',
						'mp3_preview'           => $audio_url ? $audio_url : '',
						'document_title'        => $filename ? $filename : '',
						'mirror_text'           => $mirror_text ? $mirror_text : '',
						'video'                 => $video_url ? $video_url : '',
					);
				}
			}
		}

		if ( bp_is_active( 'media' ) && bp_is_messages_gif_support_enabled() ) {
			$gif_data = bp_messages_get_meta( bp_get_the_thread_message_id(), '_gif_data', true );

			if ( ! empty( $gif_data ) ) {
				$preview_url  = ( is_int( $gif_data['still'] ) ) ? wp_get_attachment_url( $gif_data['still'] ) : $gif_data['still'];
				$video_url    = ( is_int( $gif_data['mp4'] ) ) ? wp_get_attachment_url( $gif_data['mp4'] ) : $gif_data['mp4'];
				$reply['gif'] = array(
					'preview_url' => $preview_url,
					'video_url'   => $video_url,
				);
			}
		}

		$extra_content = bp_nouveau_messages_catch_hook_content(
			array(
				'beforeMeta'    => 'bp_before_message_meta',
				'afterMeta'     => 'bp_after_message_meta',
				'beforeContent' => 'bp_before_message_content',
				'afterContent'  => 'bp_after_message_content',
			)
		);

		if ( array_filter( $extra_content ) ) {
			$reply = array_merge( $reply, $extra_content );
		}

		$get_thread_recipients = $thread_template->thread->recipients;
		$inbox_unread_count    = apply_filters( 'thread_recipient_inbox_unread_counts', array(), $get_thread_recipients );

		// Clean up the loop.
		bp_thread_messages();

		$notify_data = array(
			'hash'                          => $hash,
			'thread_id'                     => $thread->thread_id,
			'message'                       => $reply,
			'recipient_inbox_unread_counts' => $inbox_unread_count,
			'last_message_id'               => $last_message_id,
		);

		bb_pro_pusher_trigger_chunked_event( $bb_pusher, 'private-bb-message-thread-' . $thread->thread_id, 'client-bb-pro-after-message-ajax-complete', $notify_data );
	}
}

/**
 * Fire the event for the pusher settings has been updated.
 *
 * @since 2.1.6
 *
 * @param array  $old_value Old values of array.
 * @param array  $value     New values of the array.
 * @param string $option    Option key Name.
 */
function bb_pro_pusher_disabled_pusher_settings( $old_value, $value, $option ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled()
	) {
		return;
	}

	$is_event_trigger = false;

	if ( isset( $old_value['live-messaging'] ) && isset( $value['live-messaging'] ) ) {
		if ( $old_value['live-messaging'] !== $value['live-messaging'] ) {
			$is_event_trigger = true;
		}
	} else {
		if ( isset( $old_value['live-messaging'] ) && ! isset( $value['live-messaging'] ) ) {
			$is_event_trigger = true;
		} elseif ( ! isset( $old_value['live-messaging'] ) && isset( $value['live-messaging'] ) ) {
			$is_event_trigger = true;
		}
	}

	if ( $is_event_trigger ) {
		$channel    = 'private-bb-pro-global';
		$event_name = 'client-bb-pro-pusher-settings-change';
		$event_data = array(
			'old'     => $old_value,
			'updated' => $value,
		);

		$bb_pusher = bb_pusher();
		if ( null !== $bb_pusher ) {
			bb_pusher_trigger_event( $bb_pusher, $channel, $event_name, $event_data );
		}
	}
}

/**
 * Fire a pusher event when the member withdrawn connection request.
 *
 * @since 2.1.6
 *
 * @param int $initiator_user_id ID of the initiator.
 * @param int $friend_user_id    ID of the friend.
 *
 * @return void
 */
function bb_pro_pusher_friends_remove_friend( $initiator_user_id, $friend_user_id ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' )
	) {
		return;
	}

	if ( isset( $initiator_user_id, $friend_user_id ) ) {

		$channel    = 'private-bb-user-' . $friend_user_id;
		$event_name = 'client-bb-pro-member-withdrawn-connection-request';
		bb_pro_comman_connection_triggers( $initiator_user_id, $friend_user_id, $initiator_user_id, $channel, $event_name );

		$channel = 'private-bb-user-' . $initiator_user_id;
		bb_pro_comman_connection_triggers( $friend_user_id, $initiator_user_id, $friend_user_id, $channel, $event_name );

	}
}

/**
 * Fire an event when delete the thread messages.
 *
 * @since BuddyBoss 2.1.6
 *
 * @param int $thread_id ID of the thread being deleted.
 *
 * @return void
 */
function bb_pro_pusher_deleted_thread_messages( $thread_id ) {
	if (
		! bbp_pro_is_license_valid() ||
		! bb_pusher_is_enabled() ||
		! bb_pusher_is_feature_enabled( 'live-messaging' ) ||
		empty( $thread_id )
	) {
		return;
	}

    // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	BP_Messages_Thread::$noCache = true;

	$event_data = array(
		'thread_id'     => $thread_id,
		'thread_exists' => messages_is_valid_thread( $thread_id ),
	);
	$bb_pusher  = bb_pusher();
	if ( null !== $bb_pusher ) {
		bb_pusher_trigger_event( $bb_pusher, 'private-bb-message-thread-' . $thread_id, 'client-bb-pro-thread-delete-message', $event_data );
	}
}
