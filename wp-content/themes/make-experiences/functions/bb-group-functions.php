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
        //maker camp
        if ($group_type == 'maker-camp') {
            $group_link = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
            $user_access = $bp->groups->current_group->user_has_access;
            bp_core_new_subnav_item(array(
                'name' => __('Camp Hub', 'custom'),
                'slug' => 'camp-hub',
                'parent_url' => $group_link,
                'parent_slug' => $bp->groups->current_group->slug,
                'screen_function' => 'bp_group_camp_hub',
                'position' => 50,
                'user_has_access' => $user_access,
                'item_css_id' => 'camp-hub'
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


//makercamp group hub info
function bp_group_camp_hub() {
    add_action('bp_template_title', 'group_camp_hub_screen_title');
    add_action('bp_template_content', 'group_camp_hub_screen_content');

    $templates = array('groups/single/plugins.php', 'plugin-template.php');
    if (strstr(locate_template($templates), 'groups/single/plugins.php')) {
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'groups/single/plugins'));
    } else {
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'plugin-template'));
    }
}

function group_camp_hub_screen_title() {
    echo get_the_title();
}

function group_camp_hub_screen_content() {    
   //makercamp.make.co home page post id is 7594
    switch_to_blog(7);         //switch to makercamp blog

    //output the page content for post 7594
    echo apply_filters( 'the_content', get_post_field('post_content', 7594 ) );
    switch_to_blog(1); //switch back to main site
}