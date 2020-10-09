<?php

// After the gravity view is updated, we want to update the created post associated with it. 
// SO FAR, THIS IS UPDATING THE TITLE, CONTENT, FEATURED IMAGE, AND TEXT ACF FIELDS... needs work for taxonomies
add_action('gravityview/edit_entry/after_update', 'gravityview_event_update', 10, 4);

function gravityview_event_update($form, $entry_id, $entry_object) {    
    error_log('gravityview_event_update');
    $entry = $entry_object->entry;

    $post_id = $entry["post_id"];
    
    //calculate start and end date 
    $start_date = date_create($entry['4'] . ' ' . $entry['5']);
    $end_date = date_create($entry['4'] . ' ' . $entry['7']);
    
    $organizerData = event_organizer($entry);
    
    //update event
    $post_data = array(
        'ID' => $post_id,
        'post_title' => $entry['1'],
        'post_content' => $entry['2'],
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
    wp_update_post($post_data);

    event_post_meta($entry, $form, $post_id);
    update_organizer_data($entry, $organizerData, $post_id);
    
    //update reoccuring event TBD
    //update ticketing TBD          

    update_event_acf($entry, $form, $post_id);
}
