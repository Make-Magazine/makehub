<?php

// default images
define ( 'BP_AVATAR_DEFAULT', 'https://make.co/wp-content/universal-assets/v2/images/default-makey-big.png' );
define ( 'BP_AVATAR_DEFAULT_THUMB', 'https://make.co/wp-content/universal-assets/v2/images/default-makey-big.png' );

add_filter( 'bp_core_fetch_avatar_no_grav', '__return_true' );

// Set Buddypress emails from and reply to
add_filter( 'bp_email_set_reply_to', function( $retval ) {
    return new BP_Email_Recipient( 'make@make.co' );
} );
add_filter( 'wp_mail_from', function( $email ) {
    return 'make@make.co';
}, 10, 3 );
add_filter( 'wp_mail_from_name', function( $name ) {
    return 'Make: Community';
}, 10, 3 );

function make_update_pass ($check, $password, $hash, $user_id){
//    error_log('password is '.$password);
    return true;    
}
add_filter('check_password', 'make_update_pass', 20, 4);

add_action( 'widgets_init', 'parent_overrides', 11 );
function parent_overrides() {
    unregister_sidebar('sidebar-groups'); 
    unregister_sidebar('sidebar-groups-left');
    unregister_sidebar('sidebar-groups-cached');
}

/**
 * Remove courses and course settings from group creation.
 */
add_filter( 'get_header', function ( ) {
    if (function_exists('buddypress')) {
        $bp = buddypress();
        unset( $bp->groups->group_creation_steps['courses'] );
        unset( $bp->groups->group_creation_steps['group-course-settings'] );
    }
}, 9999 );


// Social Media Icons based on the profile user info
function member_social_extend(){
    global $bp;
	$member_id   = $bp->displayed_user->id;

	$profiles = array(
		'Twitter',
		'Facebook',
		'Discord',
		'Youtube',
		'Vimeo',
		'LinkedIn',
		'Twitch',
		'Mastodon',
		'Instagram',
        'SnapChat',
        'Github'
	);

	$profiles_data = array();

	foreach( $profiles as $profile ) {
		$profile_content = xprofile_get_field_data( $profile, $member_id );
		if ( !empty($profile_content) && $profile_content != '<a href="" rel="nofollow"></a>' ) {
			$profiles_data[ $profile ] = $profile_content;
		} 
	}
    
	if( !( empty( $profiles_data ) ) ) {
		echo '<div class="social-icons">';
		foreach( $profiles_data as $key => $value ) {
            $value = new SimpleXMLElement($value);
            $url =  $value['href'];
            if(!empty($url[0])) {
                $profile_icon = 'https://make.co/wp-content/universal-assets/v2/images/social-icons/' . sanitize_title( $key ) . '.png';
                echo '<a href="' . $url . '" title="' . $key . '" target="_blank"><img height="25px" width="25px" src="' . $profile_icon . '" alt="' . $key . '" /></a>';
            }
		}
		echo '</div>';
	}
}

add_filter( 'bp_before_member_header_meta', 'member_social_extend' );

/* Add resource tab on makercamp group */
function setup_group_nav(){
    global $bp; 
    
    $user_access = false;    
    if( bp_is_active('groups') && !empty($bp->groups->current_group) ){
        $group_link = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
        $user_access = $bp->groups->current_group->user_has_access;
        bp_core_new_subnav_item( array( 
            'name' => __( 'Resources', 'resources'),
            'slug' => 'resources', 
            'parent_url' => $group_link, 
            'parent_slug' => 'maker-camp',
            'screen_function' => 'bp_group_resources', 
            'position' => 10, 
            'user_has_access' => $user_access, 
            'item_css_id' => 'resources',
            'link' => 'https://makercamp.make.co/maker-camp-resources/'
        ));
    }
}
add_action( 'bp_init', 'setup_group_nav' );

//not needed as we are directing to a specific url instead of adding content
function bp_group_resources() {
	return;
}

// change mediapress slug
define( 'MPP_GALLERY_SLUG', 'galleries');//rename mediapress to album

function filtering_activity_default( $query ) {
    $query = $query . '&action=activity_update,created_group,new_event';
    return $query;
}
  
add_filter( 'bp_ajax_querystring', 'filtering_activity_default', 999 );
add_filter( 'bp_get_activity_show_filters_options', function ( $filters ) {
	return false;
} );

/* This is what was preventing school maker faire membership from getting assigned correctly
function redirect_after_join_group( $group_id, $user_id ) {
    $group = groups_get_group( array( 'group_id' => $group_id ) );
    bp_core_redirect( bp_get_groups_directory_permalink() . $group->slug );
}
add_action( 'groups_join_group', 'redirect_after_join_group', 9999, 2 );
add_action( 'groups_membership_accepted', 'redirect_after_join_group', 9999, 2 );
*/

/*function remove_message_count( $count, $nav_item, $displayed_nav ) {
    if ( $displayed_nav == 'personal' ) {
        if ( $nav_item->slug == 'messages' ) {
            $count = false;
        }
    }
    return $count;
}
add_filter( 'bp_nouveau_nav_has_count', 'remove_message_count', 99, 3 );*/

// we're going to set the member type to maker_space
add_action( 'init', 'set_makeco_member_type' );
function set_makeco_member_type(){
    if( isset($_GET['auth'] ) && $_GET['auth'] === 'm50667' ){ // Make sure this request is authorized by us
        if( function_exists( 'bp_set_member_type' ) ){
            $user_id = $_GET['wpid'];
            bp_set_member_type($user_id, 'maker_space', true);
        }
    }
}