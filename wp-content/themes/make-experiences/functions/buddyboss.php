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
	if(bp_is_my_profile()) {
		if ( $default && defined( 'BP_PLATFORM_VERSION' ) ) {
			add_filter( 'bp_member_default_component', function () use ( $default ) {
				return $default;
			} );
		} elseif ( $default && ! defined( 'BP_DEFAULT_COMPONENT' ) ) {
			define( 'BP_DEFAULT_COMPONENT', $default );
			buddypress()->active_components[ $default ] = 1;
		}
	}
}
add_action( 'bp_setup_globals', 'bp_set_dashboard_for_me' );