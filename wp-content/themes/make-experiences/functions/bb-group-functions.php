<?php

add_action('bp_init', 'setup_group_nav');

//add tabs to the group nav for schedule and materials
function setup_group_nav() {
    global $bp;
    /* Add some group subnav items */
    $user_access = false;
    $group_link = '';
    if (bp_is_active('groups') && !empty($bp->groups->current_group)) {
        $group_type = bp_groups_get_group_type($bp->groups->current_group->id);
        $group_link = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
        if ($group_type == 'maker-campus') {
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
        /*
        // custom page
        if( groups_get_groupmeta( $bp->groups->current_group->id, 'landing_hub_blog_id', true ) &&
            groups_get_groupmeta( $bp->groups->current_group->id, 'landing_hub_post_id', true )) {
                $user_access = $bp->groups->current_group->user_has_access;
                bp_core_new_subnav_item(array(
                    'name' => __('Group Hub', 'custom'),
                    'slug' => 'group-hub',
                    'parent_url' => $group_link,
                    'parent_slug' => $bp->groups->current_group->slug,
                    'screen_function' => 'bp_group_custom_hub',
                    'position' => 1,
                    'user_has_access' => $user_access,
                    'item_css_id' => 'group-hub'
                ));
        }*/
    }
}

// use this to set specific default tabs for specific groups
function custom_group_default_tabs($default_tab) {
    if (class_exists('BP_Group_Extension')) :
        $group = groups_get_current_group();
        $group_type = bp_groups_get_group_type($group->id);
        global $bp;
        if (empty($group)) {
            return $default_tab;
        }
        /*if( groups_get_groupmeta( $bp->groups->current_group->id, 'landing_hub_blog_id', true ) &&
            groups_get_groupmeta( $bp->groups->current_group->id, 'landing_hub_post_id', true )) {
            $default_tab = 'group-hub';
        }*/
    endif; // end if ( class_exists( 'BP_Group_Extension' ) )
    return $default_tab;
}

add_filter('bp_groups_default_extension', 'custom_group_default_tabs');

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
        $return .= '<div>' . get_field("materials", $event_id) . '</div>';
        $return .= '</div>';
    }

    echo $return;
}

function bb_group_redirect() {
    // if someone tries to access a group by id, redirect them to the proper url
    if (preg_match('/^\/groups\/[0-9]*\/$/', $_SERVER['REQUEST_URI'])) {
        $path = $_SERVER['REQUEST_URI'];
        $path_array = array_filter(explode('/', $path));
        $group = groups_get_group(array('group_id' => end($path_array)));
        $slug = $group->slug;
        wp_redirect((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/groups/" . $slug);
        exit();
    }
}

add_action('template_redirect', 'bb_group_redirect');

//CUSTOM group hub info
function bp_group_custom_hub() {
    add_action('bp_template_title', 'group_custom_hub_screen_title');
    add_action('bp_template_content', 'group_custom_hub_screen_content');

    $templates = array('groups/single/plugins.php', 'plugin-template.php');
    if (strstr(locate_template($templates), 'groups/single/plugins.php')) {
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'groups/single/plugins'));
    } else {
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'plugin-template'));
    }
}

function group_custom_hub_screen_title() {
    echo get_the_title();
}

function group_custom_hub_screen_content() {
    global $bp;
    $postid= groups_get_groupmeta( $bp->groups->current_group->id, 'landing_hub_post_id', true );
    $blogid = groups_get_groupmeta( $bp->groups->current_group->id, 'landing_hub_blog_id', true );
    switch_to_blog($blogid);
        $contentElementor = "";
        if (class_exists("\\Elementor\\Plugin")) {
            $pluginElementor = \Elementor\Plugin::instance();
            $contentElementor = $pluginElementor->frontend->get_builder_content($postid);
        }
        echo $contentElementor;
    restore_current_blog();
}

//rename group tabs
function bp_rename_group_tabs() {
    global $bp;

    if (bp_is_group()) {
        $bp->groups->nav->edit_nav( array('name' =>  'Activity' ),'activity', bp_current_item() );
        $bp->groups->nav->edit_nav( array('name' =>  'Settings' ),'notifications', bp_current_item() );
    }
}

add_action( 'bp_actions', 'bp_rename_group_tabs', 999 );
