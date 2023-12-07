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


add_action('bp_init', 'setup_group_nav');
//add tabs to the group nav for schedule and materials
function setup_group_nav() {
    global $bp;
    /* Add some group subnav items */
    $user_access = false;
    $group_link = '';
    if (bp_is_active('groups') && !empty($bp->groups->current_group)) {
        $group_type = bp_groups_get_group_type($bp->groups->current_group->id);
        if ($group_type == 'maker-campus') {
            $group_link = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
            $user_access = $bp->groups->current_group->user_has_access;
            bp_core_new_subnav_item(array(
                'name' => __('Event Info', 'custom'),
                'slug' => 'event-info',
                'parent_url' => $group_link,
                'parent_slug' => $bp->groups->current_group->slug,
                'screen_function' => 'bp_group_event_info',
                'position' => 50,
                'user_has_access' => $user_access,
                'item_css_id' => 'event-info'
            ));
        }
    }
}

function bp_group_event_info() {
    add_action('bp_template_title', 'group_event_info_screen_title');
    add_action('bp_template_content', 'group_event_info_screen_content');

    $templates = array('groups/single/plugins.php', 'plugin-template.php');
    if (strstr(locate_template($templates), 'groups/single/plugins.php')) {
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'groups/single/plugins'));
    } else {
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'plugin-template'));
    }
}

function group_event_info_screen_title() {
    echo get_the_title();
}


function bb_group_redirect(){
	// if someone tries to access a group by id, redirect them to the proper url
	if(preg_match('/^\/groups\/[0-9]*\/$/', $_SERVER['REQUEST_URI'])) {  
		$path = $_SERVER['REQUEST_URI'];
		$path_array = array_filter(explode('/', $path));
		$group = groups_get_group( array( 'group_id' => end($path_array) ) );
		$slug = $group->slug;
		wp_redirect( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/groups/" . $slug );
		exit();
	}
}
add_action( 'template_redirect', 'bb_group_redirect' );

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