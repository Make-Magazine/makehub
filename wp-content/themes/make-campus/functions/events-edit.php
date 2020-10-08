<?php

// After the gravity view is updated, we want to update the created post associated with it. 
// SO FAR, THIS IS UPDATING THE TITLE, CONTENT, FEATURED IMAGE, AND TEXT ACF FIELDS... needs work for taxonomies
add_action('gravityview/edit_entry/after_update', 'gravityview_event_update', 10, 4);

function gravityview_event_update($form, $entry_id, $entry_object) {    
    error_log('gravityview_event_update');
    $entry = $entry_object->entry;

    $post_id = $entry["post_id"];

    //get tags
    $tags = GFAPI::get_field($form, 50);

    //
    $tagArray = array();
    if ($tags->type == 'checkbox') {
        // Get a comma separated list of checkboxes checked
        $checked = $tags->get_value_export($entry);
        // Convert to array.
        $tagArray = explode(', ', $checked);
    }

    //start and end date 
    $start_date = date_create($entry['4'] . ' ' . $entry['5']);
    $end_date = date_create($entry['4'] . ' ' . $entry['7']);

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
    );
    wp_update_post($post_data);

    // Set the taxonomies    
    wp_set_object_terms($post_id, $entry['12'], 'tribe_events_cat'); //program type
    wp_set_object_terms($post_id, $tagArray, 'post_tag');  //program theme
    //update organizer TBD
    //update reoccuring event TBD
    //update ticketing TBD
    // Upload featured image to Organizer page
    //set_post_thumbnail(get_page_by_title($organizerData['Organizer'], 'OBJECT', 'tribe_organizer'), get_attachment_id_from_url($entry['118']));
    // Set the featured Image
    set_post_thumbnail($post_id, get_attachment_id_from_url($entry['9']));

    update_event_acf($entry, $form, $post_id);

}
