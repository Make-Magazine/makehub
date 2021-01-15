<?php

/*
 * add min num of participants 
 */
//manage_tribe_events_posts_columns
add_filter('manage_tribe_events_posts_columns', 'make_events_posts_columns');

function make_events_posts_columns($columns) {
    $new_columns = array();
    
    //add Min Attendees column before Attendees in admin event listing
    foreach ($columns as $key => $value) {
        if ($key == 'tickets') {
            $new_columns['min_attendees'] = __('Min Attendees');
        }
        $new_columns[$key] = $value;
    }
    return $new_columns;
}

//manage_tribe_events_posts_custom_column
add_action('manage_tribe_events_posts_custom_column', 'make_events_column', 10, 2);

function make_events_column($column, $post_id) {
// populate min attendees with _tribe_ticket_capacity field from post
    if ('min_attendees' === $column) {
        
        $min_attendees = get_post_meta($post_id, 'min_participants', true);

        if (!$min_attendees) {
            _e('');
        } else {
            echo $min_attendees;
        }
    }
}
