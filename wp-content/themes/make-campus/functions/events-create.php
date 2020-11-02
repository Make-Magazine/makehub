<?php

//disable the default post creation
add_filter('gform_disable_post_creation_7', 'disable_post_creation', 10, 3);

function disable_post_creation($is_disabled, $form, $entry) {
    return true;
}

// Create event with ticket
add_action('gform_after_submission_7', 'create_event', 10, 2);

function create_event($entry, $form) {
    global $wpdb;
    //error_log('create_event');
    //calculate start and end date 
    $start_date = date_create($entry['4'] . ' ' . $entry['5']);
    $end_date = date_create($entry['4'] . ' ' . $entry['7']);
    $end_recurring = date_create($entry['129'] . ' ' . $entry['7']);

    // set organizer information
    $organizerData = event_organizer($entry);

    $event_args = array(
        'post_title' => $entry['1'],
        'post_content' => $entry['2'],
        'post_status' => 'pending',
        'post_type' => 'tribe_events',
        'EventStartDate' => $entry['4'],
        'EventEndDate' => $entry['4'],
        'EventStartHour' => $start_date->format('h'),
        'EventStartMinute' => $start_date->format('i'),
        'EventStartMeridian' => $start_date->format('A'),
        'EventEndHour' => $end_date->format('h'),
        'EventEndMinute' => $end_date->format('i'),
        'EventEndMeridian' => $end_date->format('A'),
        'Organizer' => $organizerData
    );
    $post_id = tribe_create_event($event_args);

    update_post_meta($post_id, '_EventTimezone', $entry['131']);

    // this will update the organizer name and website as well, but never the email
    update_organizer_data($entry, $form, $organizerData, $post_id);

    // update taxonomies, featured image, etc
    event_post_meta($entry, $form, $post_id);

    // If they want a recurring event, we'll fake that
    if ($entry['100'] == "no") {
        event_recurrence_update($entry, $post_id, $start_date, $end_date, $end_recurring);
    }

    // Set the ACF data
    update_event_acf($entry, $form, $post_id);

    // Create/update the tickets for the event
    update_ticket_data($entry, $post_id);

    //set the post id
    $wpdb->update($wpdb->prefix . 'gf_entry', array('post_id' => $post_id), array('id' => $entry['id']));
}
