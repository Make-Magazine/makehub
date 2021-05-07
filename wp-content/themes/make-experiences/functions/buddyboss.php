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

    $person = EEM_Person::instance()->get_one([['PER_email' => $userEmail]]);

    //if they are not a facilitator, remove the facilitator portal from the drop down
    if ($args->menu->slug == 'profile-dropdown' && !$person) {
        foreach ($sorted_menu_objects as $key => $menu_object) {
            //look for "edit-submission" in the url
            $pos = strpos($menu_object->url, "edit-submission");
            if ($pos !== false) {
                unset($sorted_menu_objects[$key]);
                break;
            }
        }
    }
    return $sorted_menu_objects;
}

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
                'item_css_id' => 'custom'
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

function group_event_info_screen_content() {
    global $bp;

    //get event by pulling group id
    global $wpdb;
    $sql = 'SELECT post_id  FROM `wp_postmeta` WHERE `meta_key` LIKE "group_id" and meta_value="' . $bp->groups->current_group->id . '" limit 1';
    $event_id = $wpdb->get_var($sql);

    $return = '';
    if ($event_id) {
        $event = EEM_Event::instance()->get_one_by_ID($event_id);
        $tickets = $event->tickets();
        
        $return = '<div id="tickets"> <h4>Tickets & Times</h4>';
        foreach ($tickets as $ticket) {
            $return .= '<div class="ticket-detail">
                        <div class="ticket-detail-name"><b>' . $ticket->name() . '</b></div>
                        <ul>';
            $dates = $ticket->datetimes();
            foreach ($dates as $date) {
                $return .= '<li>' . date_format(new DateTime($date->start_date()), 'M j, Y') . ' ' . $date->start_time() . ' - ' . $date->end_time() . '<span class="small">(Pacific)</span></li>';
            }
            $return .= '    </ul>'
                    . '</div>';                        
        }
        $return .= '</div>';
        $return .= '<div id="materials"> <h4>Materials</h4>';
        $return .= '<div>'.get_field("materials", $event_id).'</div>';
        $return .= '</div>';
    }

    echo $return;
}
