<?php
function ldms_user_can_view_thread($user = NULL){

	$user_id = 0;

	if ( ( is_numeric( $user ) ) && ( !empty( $user ) ) ) {
		$user_id = $user;
	} else if ($user instanceof WP_User) {
		$user_id = $user->ID;
	} else {
		$user_id = get_current_user_id();
	}
	$user_ids = get_post_meta(get_the_id(), 'attached_users', true);
	if( is_array( $user_ids) ){
		if( in_array ( $user_id ,$user_ids )){
			return apply_filters( 'ldms_user_can_view_thread', true, $user_id );
		}
	}

	return apply_filters( 'ldms_user_can_view_thread', false, $user_id );
}


function ldms_user_in_a_thread($user = NULL){

	$user_id = 0;
	$return = false;

	if ( ( is_numeric( $user ) ) && ( !empty( $user ) ) ) {
		$user_id = $user;
	} else if ($user instanceof WP_User) {
		$user_id = $user->ID;
	} else {
		$user_id = get_current_user_id();
	}

	$args = array(
		'post_type'		=>	'ldms_message',
		'post_status'		=>	'publish',
		'posts_per_page'	=>	1,
		'meta_query' => array(
			array(
				'key'     => 'attached_users',
				'value'   => serialize( $user_id ),
				'compare' => 'LIKE',
			),
		),
	);

	$messages = new WP_Query( $args );

	if( $messages->have_posts() ) {
		$return = true;
	}

	wp_reset_postdata(); wp_reset_query();

	return $return;
	
}


function ldms_is_group_leader($user = NULL){

	if( !function_exists('learndash_get_administrators_group_ids') ) {
		if( current_user_can('manage_options') ) return true;
		return false;
	}

	$user_id = 0;

	if ( ( is_numeric( $user ) ) && ( !empty( $user ) ) ) {
		$user_id = $user;
	} else if ($user instanceof WP_User) {
		$user_id = $user->ID;
	} else {
		$user_id = get_current_user_id();
	}

	$admin_groups = learndash_get_administrators_group_ids( $user_id );
	$ok = ! empty( $admin_groups ) && is_array( $admin_groups ) && ! empty( $admin_groups[0] );
	return $ok;
}


//check is user id is attacahed to message
function ldms_user_can_view($user = 0){
	global $post;


	$user_id = 0;

	if ( ( is_numeric( $user ) ) && ( !empty( $user ) ) ) {
		$user_id = $user;
	} else if ($user instanceof WP_User) {
		$user_id = $user->ID;
	} else {
		$user_id = get_current_user_id();
	}


	if( function_exists( 'learndash_is_group_leader_user' ) ) {

		if( ldms_user_in_a_thread() || ldms_is_group_leader() || learndash_is_group_leader_user() || learndash_is_admin_user() || ldms_is_moderator() ){
			return true;
		}

	} else {

		if( ldms_user_in_a_thread() || current_user_can('manage_options') ){
			return true;
		}

	}

	return false;
}

// set users last login date
function ldms_user_last_login( $user_login, $user ) {
    update_user_meta( $user->ID, 'last_login', time() );
}
add_action( 'wp_login', 'ldms_user_last_login', 10, 2 );


//get last login
function ldms_get_last_login($user_id) {
	$last_login = get_user_meta($user_id, 'last_login');
	return $last_login;
}
