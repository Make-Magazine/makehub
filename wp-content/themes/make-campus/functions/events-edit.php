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
    error_log( $post_id. ' program type '. $entry['12']);
    wp_set_object_terms($post_id, $entry['12'], 'tribe_events_cat'); //program type
    wp_set_object_terms($post_id, $tagArray, 'post_tag');  //program theme
    
    //update organizer TBD
    //update reoccuring event TBD
    //update ticketing TBD
    
    // Upload featured image to Organizer page
    //set_post_thumbnail(get_page_by_title($organizerData['Organizer'], 'OBJECT', 'tribe_organizer'), get_attachment_id_from_url($entry['118']));
    // Set the featured Image
    set_post_thumbnail($post_id, get_attachment_id_from_url($entry['9']));
    
    //update acf
    //field mapping - ** note - upload fields don't work here. use post creation feed for that **
    //0 indicie = gravity form field id
    //1 indicie = acf field name/event meta fields
    //2 indicie (optional) = acf field key or subfield key (for repeaters)
    $field_mapping = array(
        array('4', 'preferred_start_date'),
        array('5', 'preferred_start_time'),
        array('6', 'preferred_end_date'),
        array('7', 'preferred_end_time'),
        array('96', 'alternative_start_date'),
        array('97', 'alternative_start_time'),
        array('96', 'alternative_end_time'),
        array('99', 'alternative_end_date'),
        array('124', 'schedule_exclusions'),
        array('31', 'image_1'),
        array('32', 'image_2'),
        array('33', 'image_3'),
        array('54', 'image_4'),
        array('55', 'image_5'),
        array('56', 'image_6'),
        array('123', 'promo_videos', 'field_5f7cd1ffdd06a'),
        array('19', 'about'),
        array('119', 'short_description'),
        array('73', 'audience', 'field_5f35a5f833a04'),
        array('57', 'location'),
        array('72', 'materials', 'field_5f7b4abb07cab'),
        array('78', 'kit_required'),
        array('79', 'kit_price_included'),
        array('80', 'kit_supplier'),
        array('111', 'other_kit_supplier'),
        array('120', 'kit_shipping_time'),
        array('82', 'kit_url'),
        array('122', 'wish_list_urls', 'field_5f7cc93ab762c'),
        array('87', 'prior_hosted_event'),
        array('88', 'hosted_live_stream'),
        array('89', 'video_conferencing', 'field_5f60f9bfa1d1e'),
        array('90', 'other_video_conferencing'),
        array('91', 'prev_session_links'),
        array('92', 'comfort_level'),
        array('93', 'technical_setup'),
        array('108', 'basic_skills'),
        array('109', 'skills_taught'),
    );

    //update the acf fields with the submitted values from the form
    foreach ($field_mapping as $field) {
        $fieldID = $field[0];
        $meta_field = $field[1];
        $field_key = (isset($field[2])?$field[2]:'');
        $fieldData = GFAPI::get_field($form, $fieldID);
        if (isset($entry[$fieldID])) {
            if ($fieldData->type == 'post_custom_field' && $fieldData->inputType == 'list' || $fieldData->type == 'list') {
                $listArray = explode(', ', $fieldData->get_value_export($entry));
                $num = 1;
                $repeater = [];
                foreach ($listArray as $value) {
                    $repeater[] = array($field_key => $value);
                    $num++;
                }
                update_field($meta_field, $repeater, $post_id);
            } else if (strpos($meta_field, 'image') !== false) {
                update_post_meta($post_id, $meta_field, get_attachment_id_from_url($entry[$fieldID])); // this should hopefully use the attachment id
            } else {
                //error_log('updating image ACF field '.$meta_field. ' with GF field '.$fieldID . ' with value '.$entry[$fieldID]);
                update_post_meta($post_id, $meta_field, $entry[$fieldID]);
            }
            // entry field id is not set in time for checkboxes, apparently
            if ($fieldData->type == 'post_custom_field' && $fieldData->inputType == 'checkbox' || $fieldData->type == 'checkbox') {
                $checked = $fieldData->get_value_export($entry);
                $values = explode(', ', $checked);
                update_field($field_key, $values, $post_id);
            }
        }        
    }      
}