<?php
function get_user_ids_leader_can_message( $user_id = null ) {

	if( $user_id == null ) {
		$cuser = wp_get_current_user();
		$user_id = $cuser->ID;
	}

	$user_list = array();

	//checks if user is a group leder
	$group_leader = ldms_is_group_leader( $user_id );

	$cuser = wp_get_current_user();

	if( current_user_can('manage_options') ) {

		$users = get_users();
		$cuser = wp_get_current_user();

		foreach( $users as $user ) {
			if( $user->ID == $cuser->ID ) continue;
			$user_list[] = $user->ID;
		}

		return apply_filters( 'get_user_ids_leader_can_message', array_unique($user_list) );

	} elseif( $group_leader ){
		// get ids of groups the user is a leader of
		$groups = learndash_get_administrators_group_ids( $user_id );
		$user_list = array();

		foreach($groups as $group_id){

			$user_list = array_merge($user_list, learndash_get_groups_user_ids( $group_id ) );
			$user_list = array_merge($user_list, learndash_get_groups_administrator_ids( $group_id ) );

		}

		return apply_filters( 'get_user_ids_leader_can_message', array_unique($user_list) );

	} else {

		$groups = learndash_get_users_group_ids( $user_id );
		$user_list  = array();

		foreach( $groups as $group_id ){
			$user_list = array_merge($user_list, learndash_get_groups_administrator_ids( $group_id ) );
		}

		return apply_filters( 'get_user_ids_leader_can_message', array_unique($user_list) );

	}

	// TODO: ldms_users_can_start
	//
	$settings = get_option('ldms_private_sessions_settings');

	if( isset($settings['ldms_users_can_start']) ) {
		foreach( $settings['ldms_users_can_start'] as $role => $value ) {

			if( $value != 'true' ) {
				continue;
			}

			if( in_array( $role, (array) $cuser->roles ) ) {
				$groups = learndash_get_users_group_ids( $user_id );
				foreach($groups as $group_id){

					$user_list = array_merge($user_list, learndash_get_groups_administrator_ids( $group_id ) );

				}
			}
		}
	}

	return $user_list;
}


add_action( 'wp_ajax_nopriv_ldms_create_message', 'ldms_must_be_logged_in' );
function ldms_must_be_logged_in() {
   esc_html_e( "You must log in to create a message", "ldmessenger" );
   die();
}

add_action( 'template_redirect', 'ldms_check_for_new_message' );
function ldms_check_for_new_message() {

	if( isset( $_POST['ldms-send-to'] ) ) {

		$post_id = ldms_create_message();

		if( $post_id ) {
			wp_redirect( get_permalink($post_id) . '?success=true' );
			die();
		}

	}

}

add_action( 'wp_ajax_ldms_create_message', 'ldms_create_message' );
function ldms_create_message() {
	$result = __('Something went wrong on our end. Please try again and if the issue continues contact the system administor','ldmessenger');

	$message 		= isset( $_REQUEST['ldms-contents'] ) ? wp_kses_post( $_REQUEST['ldms-contents'] ) : '';
	$send_to_id 	= isset( $_REQUEST['ldms-send-to'] ) ? intval( $_REQUEST['ldms-send-to'] ) : '';
	$sender_id 		= isset( $_REQUEST['ldms-sender'] ) ? intval( $_REQUEST['ldms-sender'] ) : '';
	$session_title  = isset( $_REQUEST['ldms-session-title'] ) ? esc_attr( $_REQUEST['ldms-session-title'] ) : '';

	$send_to_user 	= get_user_by( 'id', $send_to_id );
	$sender_user 	= get_user_by( 'id', $sender_id );

	if ( !isset( $_REQUEST['nonce'] ) && !wp_verify_nonce( $_REQUEST['nonce'], "lmds_message_nonce" ) ) {
		exit("No naughty business please");
	}


	$attached_users = array( $send_to_id , $sender_id );

	$post_id = ldms_create_message_cpt($send_to_user, $session_title , $message, $attached_users, $sender_id);

	if( $post_id ){

		/**
		 * Send and e-mail
		 * @var string
		 */
		$result = '<h3>'.__('Session Started', 'ldmessenger').'</h3>';
		$result .= '<p><a href="'.get_permalink( $post_id ).'">'.__('View Session', 'ldmessenger' ).'</a></p>';

		$email_sent = ldms_send_email_notification($send_to_user, $message, get_permalink( $post_id), $sender_user);

		if( $email_sent ){
			$result .= '<p>'.__( 'Email notification sent', 'ldmessenger' ).'</p>';
		} else{
			$result .= '<p>'.__( 'There was an issue notifying the recipient', 'ldmessenger' ) . $email_sent . '</p>';
		}

		/**
		 * Add an unread message meta key
		 */
		 add_user_meta( $send_to_user, '_ldms_unread_message', $post_id );

		 return $post_id;

	}

	return false;

	/*
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		$result = json_encode($result);
		echo $result;
	}
	else {
		header("Location: ".$_SERVER["HTTP_REFERER"]);
	}

	die(); */

}

add_action( 'wp_ajax_ldms_delete_message', 'ldms_delete_message' );
add_action( 'wp_ajax_nopriv_ldms_delete_message', 'ldms_delete_message' );
function ldms_delete_message() {

    $message_id    = $_POST[ 'message_id' ];
    $cuser      = wp_get_current_user();



	if ( !isset( $_POST['nonce'] ) && !wp_verify_nonce( $_POST['nonce'], "ldms-delete-nonce" ) ) {
		exit("No naughty business please");
	}

    if( ( $cuser->ID != get_post_field( 'post_author', $note_id ) ) && ( !current_user_can( 'delete_session' ) ) ) {
        return false;
        wp_die();
    }

    $deleted = wp_delete_post( $message_id );

    // Delete the notification if unread
    delete_user_meta( $cuser->ID, '_ldms_unread_message', $post_id );

    if($deleted){
    	wp_send_json_success( array( 'success' => true, 'data' => $message_id ) );
    }else{
	    wp_send_json_error( array( 'success' => false, 'data' => 'You do not have permission to delete Conversations' ) );
    }

    die();

}

add_filter( 'document_title_parts', 'ldms_obfuscate_doc_title', 999, 2 );
function ldms_obfuscate_doc_title( $title_parts ) {

	if( get_post_type() !== 'ldms_message' ) {
		return $title_parts;
	}

	$user = wp_get_current_user();

	if( !is_user_logged_in() ) {
	 	$title_parts['title'] = __( 'Please login to continue', 'ldmessanger' );
	} elseif( !ldms_user_can_view_thread($user->ID) ) {
		$title_parts['title'] = __( 'You do not have access', 'ldmessanger' );
	}

	return $title_parts;

}

add_filter( 'the_title', 'ldms_obfuscate_title', 999, 2 );
function ldms_obfuscate_title( $title, $id = null ) {

	if( get_post_type($id) !== 'ldms_message' ) {
		return $title;
	}

	$user = wp_get_current_user();

	if( !is_user_logged_in() ) {
		return __( 'Please login to continue', 'ldmessanger' );
	} elseif( !ldms_user_can_view_thread($user->ID) ) {
		return __( 'You do not have access', 'ldmessanger' );
	}

	return $title;

}

function ldms_create_message_cpt($send_to_user, $session_title, $message, $attached_users, $sender_id) {

    $title = __( 'Message to ', 'ldmessenger' ) . $send_to_user->display_name;

    if( $session_title != '' ){
	   $title = $session_title;
    }

	// $title = hash( 'md5', $title, true );

    $content =  $message;

    $post_id = wp_insert_post( array(
        'post_type'        => 'ldms_message',
        'post_title'        => $title,
		'post_name'			=> uniqid(),
        'post_content'      => $message,
        'post_status'       => 'publish',
        'post_author'       => $sender_id
    ) );

    if ( $post_id != 0 ){
	    update_post_meta( $post_id, 'session_title', $session_title );
	    update_post_meta( $post_id, 'attached_users', $attached_users );
	    update_post_meta( $post_id, 'sent_to', intval( $send_to_user->ID ) );
		add_user_meta( $send_to_user->ID, '_ldms_unread_message', $post_id );
        return $post_id;
    }else {
       return false;
    }
}

function ldms_display_user_dropdown($user_id){

	$users = get_user_ids_leader_can_message($user_id);

	foreach($users as $user_id){
		$user = get_user_by('id', $user_id);
		echo '<option value="' . esc_attr($user->ID) . '">' . esc_html( $user->display_name ) . '</option>';
	}

}

function ldms_user_has_messages( $user_id = NULL ) {

	$cuser 		= wp_get_current_user();
	$user_id 	= ( $user_id == NULL ? $cuser->ID : $user_id );

	$args = apply_filters( 'ldms_user_has_messages', array(
		'post_type'   		=> 'ldms_message',
		'post_status'   	=> 'publish',
		'posts_per_page'	=>	'1',
		'meta_query' => array(
			array(
				'key'     => 'attached_users',
				'value'   => serialize( $user_id ),
				'compare' => 'LIKE',
			),
		),
	) );

	$messages 		= new WP_Query($args);
	$has_messages 	= $messages->have_posts();

	wp_reset_postdata(); wp_reset_query();

	return $has_messages;

}

function ldms_get_user_messages( $user_id = NULL, $search = NULL, $status = 'active' ) {

	$cuser 		= wp_get_current_user();
	$user_id 	= ( $user_id == NULL ? $cuser->ID : $user_id );
	$paged		= ( get_query_var('paged') ? get_query_var('paged') : 1 );

	$sessions = array(
		'stared'	=>	false,
		'normal'	=>	false
	);

	$stared_sessions = get_user_meta( $cuser->ID, 'stared_sessions', false );

	if( $stared_sessions && $paged == 1 ) {

		$args = apply_filters( 'ldms_get_user_stared_messages', array(
			'post_type'			=>	'ldms_message',
			'post_status' 		=> 'publish',
			'posts_per_page' 	=> -1,
			'post__in'			=>	$stared_sessions,
			'tax_query'		=> array(
				array(
					'taxonomy'	=>	'ldms_status',
					'field'		=>	'slug',
					'terms'		=>	'archived',
					'operator'	=>	'NOT IN'
				)
			)
		) );

		if( $status == 'archived' ) {
			$args['tax_query'][0]['operator'] = 'IN';
		}

		$sessions['stared'] = new WP_Query($args);
	}

	$args = array(
		'post_type'   	=> 'ldms_message',
		'post_status'   => 'publish',
		'paged'			=> $paged,
		'post__not_in'	=> $stared_sessions,
		'meta_query' => array(
			array(
				'key'     => 'attached_users',
				'value'   => serialize( $user_id ),
				'compare' => 'LIKE',
			),
		),
		'orderby'		=>	'modified',
		'tax_query'		=> array(
			array(
				'taxonomy'	=>	'ldms_status',
				'field'		=>	'slug',
				'terms'		=>	'archived',
				'operator'	=>	'NOT IN'
			)
		)
	);

	if( $search ) {
		$args['s'] = esc_attr( $search );
	}

	if( $status == 'archived' ) {
		$args['tax_query'][0]['operator'] = 'IN';
	}

	$args = apply_filters( 'ldms_get_user_messages', $args );

	$sessions['normal'] = new WP_Query($args);

	return $sessions;

}

/**
 * Get the users unread message count.
 *
 * Stores a metakey of _ldms_unread_message in the users meta table with the post_id
 * @param  [int] $user_id User ID, optional
 * @return [int]          count
 */
function ldms_get_user_unread_message_count( $user_id = NULL ) {

	$cuser 		= wp_get_current_user();
	$user_id 	= ( $user_id == NULL ? $cuser->ID : $user_id );

	return intval( count(get_user_meta( $user_id, '_ldms_unread_message', false )) );

}


add_filter( 'ldms_get_user_messages', 'ldps_show_all_user_messages' );
function ldps_show_all_user_messages( $args ) {

	$settings 	= get_option('ldms_private_sessions_settings');
	$roles 		= array_keys($settings['ldms_user_moderators']);

    $user = wp_get_current_user();

    foreach( $roles as $role ) {
        if( in_array( $role, (array) $user->roles ) ) {
			unset($args['meta_query']);
        }
    }

    return $args;


}

add_filter( 'ldms_user_can_view_thread', 'ldps_show_individual_message_to_users', 10, 2 );
function ldps_show_individual_message_to_users( $value, $user_id ) {

    $user  = wp_get_current_user();

	$settings 	= get_option('ldms_private_sessions_settings');
	$roles 		= array_keys($settings['ldms_user_moderators']);

    foreach( $roles as $role ) {
        if( in_array( $role, (array) $user->roles ) ) {
            return true;
        }
    }

    return $value;

}

function ldms_is_moderator() {

	$user  = wp_get_current_user();

	$settings 	= get_option('ldms_private_sessions_settings');
	$roles 		= array_keys($settings['ldms_user_moderators']);

	foreach( $roles as $role ) {
		if( in_array( $role, (array) $user->roles ) ) {
			return true;
		}
	}


	return false;

}

function ldms_enable_title(){

	$enable_title = false;

	$settings = get_option( 'ldms_private_sessions_settings', array() );

	if( $settings['ldms_enable_title'] === 'yes'){
		$enable_title = true;
	}

	return apply_filters( 'ldms_enable_title', $enable_title );
}

add_action('learndash_delete_user_data','ldms_delete_user_data',10,1);

function ldms_delete_user_data($user_id){

	if( !$user_id ) {
		return;
	}

	$sessions = get_posts( array( 'author' => $user_id, 'post_type' => 'ldms_message' ) );

	foreach($sessions as $session){
		wp_delete_post( $session->ID , true );
	}

	$messages = get_posts( array( 'user_id' => $user_id, 'post_type' => 'ldms_message' ) );

	foreach($messages as $message){
		wp_delete_post( $message->comment_ID , true );
	}


}

function ldms_user_data_eraser( $email_address, $page = 1 ) {

	$number 	= 500; // Limit us to avoid timing out
	$page 	= (int) $page;

	$user = get_user_by( 'email', $email_address );

	if( !$user ) {
		return array( 'items_removed' => 0,
			'items_retained' => false, // always false in this example
			'messages' => array( __( 'No user found with this email address', 'ldmessanger' ) ), // no messages in this example
			'done' => true,
		);
	}

	$args = array(
		'post_type'   		=> 'ldms_message',
		'post_status'   	=> 'publish',
		'posts_per_page'	=>	$number,
		'paged'				=>	$page,
		'meta_query' => array(
			array(
				'key'     => 'attached_users',
				'value'   => serialize( $user->ID ),
				'compare' => 'LIKE',
			),
		),
	);

	$sessions = get_posts($args);

	if( !$sessions || empty($sessions) ) {
		return array( 'items_removed' => 0,
			'items_retained' => false, // always false in this example
			'messages' => array( __( 'No private sessions for this user', 'ldmessanger' ) ), // no messages in this example
			'done' => true,
		);
	}

	$items_removed = false;

	foreach( $sessions as $session ) {
		wp_delete_post( $session->ID, true );
		$items_removed = true;
	}

	// Tell core if we have more sessions to work on still
	$done = count( $sessions ) < $number; return array( 'items_removed' => $items_removed,
	'items_retained' => false, // always false
	'messages' => array( __('Private sessions removed', 'ldmessanger' ) ),
	'done' => $done,
	);

}

function ldms_user_data_eraser_callback( $erasers ) {
  $erasers['learndash-private-sessions'] = array(
    'eraser_friendly_name' => __( 'Private Sessions Plugin' ),
    'callback'             => 'ldms_user_data_eraser',
    );
  return $erasers;
}

add_filter( 'wp_privacy_personal_data_erasers', 'ldms_user_data_eraser_callback', 10 );

add_action( 'admin_init', 'ldms_test_data_export' );
function ldms_test_data_export() {

	$data = ldms_user_data_exporter( 'test@user.com' );

}

function ldms_user_data_exporter( $email_address, $page = 1 ) {

	$number = 500; // Limit us to avoid timing out
	$page = (int) $page;

	$user = get_user_by( 'email', $email_address );

	if( !$user ) {
		return array(
			'data' => null,
			'done' => true,
		);
	}

	$args = array(
		'post_type'   		=> 'ldms_message',
		'post_status'   	=> 'publish',
		'posts_per_page'	=>	$number,
		'paged'				=>	$page,
		'meta_query' => array(
			array(
				'key'     => 'attached_users',
				'value'   => serialize( $user->ID ),
				'compare' => 'LIKE',
			),
		),
	);

	$sessions = get_posts($args);

	if( !$sessions || empty($sessions) ) {
		return array(
		  	'data' => null,
		  	'done' => true,
		);
	}

	$export_items = array();

	foreach( $sessions as $session ) {

		$item_id 	 = 'private-session-' . $session->ID;
		$group_id 	 = 'private-sessions';
		$group_label = __( 'Private Sessions', 'ldmessanger' );

		// DATA

		$sent_to_id 	= get_post_meta( $session->ID, 'sent_to', true);
		$session_title 	= get_post_meta( $session->ID, 'session_title', true);

		if( empty($session_title) ) {
			$session_title = __( 'Session with ' . get_the_author_meta( 'display_name', $sent_to_id ) );
		}

		$author	 = $session->post_author;

		$args = array(
			'post_id' => $session->ID
		);

		$comments 	= get_comments($args);

		$start_date = get_the_date( get_option('date_format') . ' ' . get_option('time_format'), $session->ID );

		if( !empty($comments) ){
			$last_comment 	= end($comments);
			$last_responded = get_comment_date( get_option('date_format') . ' ' . get_option('time_format'), $last_comment );
		}else{
			$last_responded = get_the_date( get_option('date_format') . ' ' . get_option('time_format'), $session->ID );
		}

		$data = array(
			array(
				'name' => __( 'Session Title', 'ldmessanger' ),
				'value' => $session_title
			),
			array(
				'name' => __( 'Session Author', 'ldmessanger' ),
				'value' => get_the_author_meta( 'display_name', $author ),
			),
			array(
				'name' => __( 'Session Recipient', 'ldmessanger' ),
				'value' => get_the_author_meta( 'display_name', $sent_to_id ),
			),
			array(
				'name'  => __( 'Start Date', 'ldmessanger' ),
				'value' => $start_date
			),
			array(
			 	'name'  => __( 'Last Response', 'ldmessanger' ),
			 	'value' => $last_responded
				),
			array(
				'name'  => __( 'Total Messages', 'ldmessanger' ),
				'value' => count($comments)
			),
			array(
				'name'	=>	__( 'Initial Message', 'ldmessanger' ),
				'value'	=>	$session->post_content
			)
		);


		if( $comments ) {

			$i = 1;

			foreach( (array) $comments as $comment ) {
				$data[] = array(
					'name'		=> __( 'Response ' . $i ),
					'value'		=>  '<strong>' . $comment->comment_author . ':</strong> ' . $comment->comment_content,
				);
				$i++;
			}

		}

		$export_items[] = array(
		  'group_id' 	=> $group_id,
		  'group_label' => $group_label,
		  'item_id' 	=> $item_id,
		  'data' 		=> $data,
		);

	}

	// Tell core if we have more comments to work on still
	$done = count( $sessions ) < $number;

	return array(
		'data' => $export_items,
		'done' => $done,
	);

}

function ldms_plugin_data_exporter_callback( $exporters ) {
  $exporters['learndash-private-sessions'] = array(
    'exporter_friendly_name' => __( 'Private Sessions Data' ),
    'callback' => 'ldms_user_data_exporter',
  );
  return $exporters;
}

add_filter( 'wp_privacy_personal_data_exporters', 'ldms_plugin_data_exporter_callback', 10 );

function ldms_can_start_session() {

	if( ldms_is_group_leader() ) {
		return true;
	}

	if( current_user_can('start_session') ) {
		return true;
	}

	$cuser = wp_get_current_user();

	$groups 	= learndash_get_users_group_ids( $cuser->ID );
	$settings 	= get_option( 'ldms_private_sessions_settings', array() );

	if( $groups && !empty($groups) && isset($settings['ldms_groups_can_start']) && !empty($settings['ldms_groups_can_start']) ) {
		foreach( $settings['ldms_groups_can_start'] as $group_id => $value ) {

			if( in_array( $group_id, $groups) && $value == 'true' ) {
				return true;
			}

		}
	}

	return false;

}

add_action( 'wp_ajax_ldms_archive_message', 'ldms_archive_message' );
function ldms_archive_message() {

	$cuser = wp_get_current_user();

	if( !current_user_can('delete_session') ) {
		wp_send_json_error( array( 'success' => false, 'message' => __( 'You don\'t have permission to archive this message', 'ldmessager' ) ) );
	}

	$post_id = ( isset($_GET['post_id']) ? $_GET['post_id'] : false );
	$status  = ( isset($_GET['status']) ? $_GET['status'] : false );

	switch( $status ) {
		case( 'archive' ):
			wp_set_post_terms( $post_id, 'archived', 'ldms_status', true );
			break;
		case( 'unarchive' ):
			wp_remove_object_terms( $post_id, 'archived', 'ldms_status' );
			break;
	}

	do_action( 'ldms_after_archive_actions', $post_id, $status, $cuser );

	wp_send_json_success();

}

add_action( 'wp_ajax_ldms_star_message', 'ldms_star_message' );
function ldms_star_message() {

	$post_id = ( isset($_GET['post_id']) ? $_GET['post_id'] : false );
	$status = ( isset($_GET['status']) ? $_GET['status'] : false );

	$cuser = wp_get_current_user();

	if( !$post_id || !$status ) {
		wp_send_json_error( array( $post_id, $status) );
	}

	$stared_sessions = get_user_meta( $cuser->ID, 'stared_sessions', false );

	if( $status == 'unstared' ) {
		add_user_meta( $cuser->ID, 'stared_sessions', $post_id );
	} else {
		delete_user_meta( $cuser->ID, 'stared_sessions', $post_id );
	}

	wp_send_json_success( array( $post_id, $status, $stared_sessions ) );

}

function ldms_get_current_url() {

	$uri = $_SERVER['REQUEST_URI'];
	$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	return $url;

}
