<?php

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

// for logged in users, change the default tab to their dashboard page
function bp_set_dashboard_for_me() {
    $default = 'dashboard';
    if (bp_is_my_profile()) {
        if ($default && defined('BP_PLATFORM_VERSION')) {
            add_filter('bp_member_default_component', function () use ( $default ) {
                return $default;
            });
        } elseif ($default && !defined('BP_DEFAULT_COMPONENT')) {
            define('BP_DEFAULT_COMPONENT', $default);
            buddypress()->active_components[$default] = 1;
        }
    }
}
add_action('bp_setup_globals', 'bp_set_dashboard_for_me');

add_filter('wp_nav_menu_objects', 'ad_filter_menu', 10, 2);
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
