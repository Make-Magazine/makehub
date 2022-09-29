<?php
//set default avatar thumb dimensions
define ( 'BP_AVATAR_THUMB_WIDTH', 80 );
define ( 'BP_AVATAR_THUMB_HEIGHT', 80 );

// if we want some random page to behave like a buddy press page (e.g. the blog pages)
function set_displayed_user($user_id) {
    global $bp;
    $bp->displayed_user->id = $user_id;
    $bp->displayed_user->domain = bp_core_get_user_domain($bp->displayed_user->id);
    $bp->displayed_user->userdata = bp_core_get_core_userdata($bp->displayed_user->id);
    $bp->displayed_user->fullname = bp_core_get_user_displayname($bp->displayed_user->id);
}

//remmove the blog from profile tabs
function remove_profile_nav() {
    global $bp;
    bp_core_remove_nav_item('blog');
}
add_action('bp_init', 'remove_profile_nav');

//add_filter('wp_nav_menu_objects', 'ad_filter_menu', 10, 2);
function ad_filter_menu($sorted_menu_objects, $args) {
    //check if current user is a facilitator
    global $current_user;
    $current_user = wp_get_current_user();
    $userEmail = (string) $current_user->user_email;
	if (class_exists(EEM_Person::class)) {
	    $person = EEM_Person::instance()->get_one([['PER_email' => $userEmail]]);

	    //if they are not a facilitator, remove the facilitator portal from the drop down
	    if (isset($args->menu->slug)
	        && ($args->menu->slug == 'profile-dropdown' || $args->menu->slug == 'buddy-panel')
	        && !$person) {
	        foreach ($sorted_menu_objects as $key => $menu_object) {
	            //look for "edit-submission" in the url
	            $pos = strpos($menu_object->url, "edit-submission");
	            if ($pos !== false) {
	                unset($sorted_menu_objects[$key]);
	                break;
	            }
	        }
	        global $bp;
	        bp_core_remove_nav_item('facilitator-portal');
	    }
	    return $sorted_menu_objects;
	}
}

// overwrite the recommended dimensions for the cover image
function bp_custom_get_cover_image_dimensions( $wh, $settings, $component ) {
	if ( 'xprofile' === $component || 'groups' === $component ) {
		return array(
			'width'  => 1300,
			'height' => 225,
		);
	}
	return $wh;
}
add_filter( 'bp_attachments_get_cover_image_dimensions', 'bp_custom_get_cover_image_dimensions', 10, 4 );

// if we have the group name as a token, we probably want the group.url as well
function add_group_url_email_token( $formatted_tokens, $tokens, $obj ) {
	if ( isset( $formatted_tokens['group.name'] ) ) {
		$group_id = BP_Groups_Group::group_exists( sanitize_title( $formatted_tokens['group.name'] ) );
		$formatted_tokens['group.url']  = get_site_url().'/wp-login.php?redirect_to='.bp_get_group_permalink( groups_get_group( $group_id ) );
	}
	return $formatted_tokens;
}
add_filter( 'bp_email_set_tokens', 'add_group_url_email_token', 11, 3  );

//add 'facilitator' to body class if the logged in user is a facilitator
add_filter( 'body_class','my_body_classes' );
function my_body_classes( $classes ) {
//check if current user is a facilitator
    global $current_user;
    $current_user = wp_get_current_user();
    $userEmail = (string) $current_user->user_email;
	if (class_exists(EEM_Person::class)) {
	    $person = EEM_Person::instance()->get_one([['PER_email' => $userEmail]]);
	    if($person){
	        $classes[] = 'facilitator-user';
	    }
	}
    return $classes;
}

// if a user somehow gets access to a hidden group url that they aren't a member of, redirect them to the main groups page
function redirect_nongroup_member() {
	if( strpos($_SERVER['REQUEST_URI'], "/groups/") !== false && strlen($_SERVER['REQUEST_URI']) > 8 ) {
		if(groups_get_group(bp_get_current_group_id())->status == NULL){
			wp_safe_redirect( NETWORK_HOME_URL . "/groups" );
			exit;
		}
	}
}
//add_action( 'wp', 'redirect_nongroup_member' );


if( ! function_exists( 'wp_new_user_notification' ) ) {
	/**
	 * Email login credentials to a newly-registered user.
	 *
	 * A new user registration notification is also sent to admin email.
	 *
	 * @since BuddyPress 2.0.0
	 * @since BuddyPress 4.3.0 The `$plaintext_pass` parameter was changed to `$notify`.
	 * @since BuddyPress 4.3.1 The `$plaintext_pass` parameter was deprecated. `$notify` added as a third parameter.
	 * @since BuddyPress 4.6.0 The `$notify` parameter accepts 'user' for sending notification only to the user created.
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 * @global PasswordHash $wp_hasher Portable PHP password hashing framework instance.
	 *
	 * @param int    $user_id    User ID.
	 * @param null   $deprecated Not used (argument deprecated).
	 * @param string $notify     Optional. Type of notification that should happen. Accepts 'admin' or an empty
	 *                           string (admin only), 'user', or 'both' (admin and user). Default empty.
	 */
	function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {
		if ( $deprecated !== null ) {
			_deprecated_argument( __FUNCTION__, '4.3.1' );
		}

		global $wpdb, $wp_hasher;
		$user = get_userdata( $user_id );

		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

		if ( 'user' !== $notify ) {
			$switched_locale = switch_to_locale( get_locale() );

			/* translators: %s: site title */
			$message = '<p>' . sprintf( __( 'New user registration on your site %s:', 'buddyboss' ), $blogname ) . '</p>';
			/* translators: %s: user login */
			$message .= '<p>' . sprintf( __( 'Username: <b>%s</b>', 'buddyboss' ), $user->user_login ) . '</p>';
			/* translators: %s: user email address */
			$message .= '<p>' . sprintf( __( 'Email: <b>%s</b>', 'buddyboss' ), $user->user_email ) . '</p>';

			$wp_new_user_notification_email_admin = array(
				'to'      => get_option( 'admin_email' ),
				/* translators: Password change notification email subject. %s: Site title */
				'subject' => __( '[%s] New User Registration', 'buddyboss' ),
				'message' => $message,
				'headers' => '',
			);

			/**
			 * Filters the contents of the new user notification email sent to the site admin.
			 *
			 * @since BuddyPress 4.9.0
			 *
			 * @param array   $wp_new_user_notification_email {
			 *     Used to build wp_mail().
			 *
			 *     @type string $to      The intended recipient - site admin email address.
			 *     @type string $subject The subject of the email.
			 *     @type string $message The body of the email.
			 *     @type string $headers The headers of the email.
			 * }
			 * @param WP_User $user     User object for new user.
			 * @param string  $blogname The site title.
			 */
			$wp_new_user_notification_email_admin = apply_filters( 'wp_new_user_notification_email_admin', $wp_new_user_notification_email_admin, $user, $blogname );

			add_filter( 'wp_mail_content_type', 'bp_email_set_content_type' );

			$wp_new_user_notification_email_admin['message'] = bp_email_core_wp_get_template( $wp_new_user_notification_email_admin['message'], $user );

			@wp_mail(
				$wp_new_user_notification_email_admin['to'],
				wp_specialchars_decode( sprintf( $wp_new_user_notification_email_admin['subject'], $blogname ) ),
				$wp_new_user_notification_email_admin['message'],
				$wp_new_user_notification_email_admin['headers']
			);

			remove_filter( 'wp_mail_content_type', 'bp_email_set_content_type' );

			if ( $switched_locale ) {
				restore_previous_locale();
			}
		}

		// `$deprecated was pre-4.3 `$plaintext_pass`. An empty `$plaintext_pass` didn't sent a user notification.
		if ( 'admin' === $notify || ( empty( $deprecated ) && empty( $notify ) ) ) {
			return;
		}

		// Generate something random for a password reset key.
		$key = wp_generate_password( 20, false );

		/** This action is documented in wp-login.php */
		do_action( 'retrieve_password_key', $user->user_login, $key );

		// Now insert the key, hashed, into the DB.
		if ( empty( $wp_hasher ) ) {
			require_once ABSPATH . WPINC . '/class-phpass.php';
			$wp_hasher = new PasswordHash( 8, true );
		}
		$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
		$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

		$switched_locale = switch_to_locale( get_user_locale( $user ) );

		/* translators: %s: user login */
		$message  = '<p><b>Please set your password and login to access courses, events, directories and more.</b></p>';
		$message  = '<p>' . sprintf( __( 'Username: %s', 'buddyboss' ), $user->user_login ) . '</p>';
		$message .= '<p>' . sprintf( __( 'To set your password <a href="%s">Click here</a>.', 'buddyboss' ), network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user->user_login ), 'login' ) ) . '</p>';
		$message .= wp_login_url();

		$wp_new_user_notification_email = array(
			'to'      => $user->user_email,
			/* translators: Password change notification email subject. %s: Site title */
			'subject' => __( '[%s] Your username and password info', 'buddyboss' ),
			'message' => $message,
			'headers' => '',
		);

		/**
		 * Filters the contents of the new user notification email sent to the new user.
		 *
		 * @since BuddyPress 4.9.0
		 *
		 * @param array   $wp_new_user_notification_email {
		 *     Used to build wp_mail().
		 *
		 *     @type string $to      The intended recipient - New user email address.
		 *     @type string $subject The subject of the email.
		 *     @type string $message The body of the email.
		 *     @type string $headers The headers of the email.
		 * }
		 * @param WP_User $user     User object for new user.
		 * @param string  $blogname The site title.
		 */
		$wp_new_user_notification_email = apply_filters( 'wp_new_user_notification_email', $wp_new_user_notification_email, $user, $blogname );

		add_filter( 'wp_mail_content_type', 'bp_email_set_content_type' );

		$wp_new_user_notification_email['message'] = bp_email_core_wp_get_template( $wp_new_user_notification_email['message'], $user );

		wp_mail(
			$wp_new_user_notification_email['to'],
			wp_specialchars_decode( sprintf( $wp_new_user_notification_email['subject'], $blogname ) ),
			$wp_new_user_notification_email['message'],
			$wp_new_user_notification_email['headers']
		);

		remove_filter( 'wp_mail_content_type', 'bp_email_set_content_type' );

		if ( $switched_locale ) {
			restore_previous_locale();
		}
	}
}

//********************************************//
//           Member type: Members             //
//********************************************//
// make each user a 'Member' user type when they successfully subscribe
add_action('mepr-event-transaction-completed', 'default_member_type', 10, 1);

function default_member_type($user_id) {
    if (!bp_get_member_type($user_id)) {
        bp_set_member_type($user_id, 'member');
    }
}

// if membership lapses, is canceled or deleted, remove the member type so they don't show up in the members directory count
add_action('mepr-event-subscription-stopped', 'remove_member_types', 10, 1); // this captures cancels and removals
add_action('mepr-transaction-expired', 'remove_member_types', 10, 1);

function remove_member_types($user_id) {
    if (bp_get_member_type($user_id) == 'member') {
        bp_set_member_type($user_id, '');
    }
}

//********************************************//
//        Makerspace related functions        //
//********************************************//
// if the membership level is makerspace/smallbusiness, add a class to the user card
function add_featured_ms_class_directory($classes) {
	foreach (CURRENT_MEMBERSHIPS as $membership) {
	    if (strpos('Makerspace', $membership) !== FALSE) {
	        $classes[] = "member-level-makerspace";
	    }
	}
    return $classes;
}

add_filter('bp_get_member_class', 'add_featured_ms_class_directory');

/**
 * To be more inclusive to Europe, we will Name and Surname rather than First Name Last Name
 */
function translate_text( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        case 'First Name:*' :
            $translated_text = __( 'Name', 'memberpress' );
            break;
		case 'Last Name:*' :
			$translated_text = __( 'Surname', 'memberpress' );
			break;
    }

    return $translated_text;
}
add_filter( 'gettext', 'translate_text', 999, 3 );
