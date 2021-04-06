<?php

//duplicate entry
add_action('gravityview/duplicate-entry/duplicated', 'duplicate_entry', 10, 2);

function duplicate_entry($duplicated_entry, $entry) {
    error_log('duplicate_entry with form id ' . $duplicated_entry['form_id']);
    $form = GFAPI::get_form($duplicated_entry['form_id']);
    create_event($duplicated_entry, $form);
}

function update_event_acf($entry, $form, $post_id) {
    //field mapping - ** note - upload fields don't work here. use post creation feed for that **
    //0 indicie = gravity form field id
    //1 indicie = acf field name/event meta fields
    //2 indicie (optional) = acf field key or subfield key (for repeaters)
    $field_mapping = array(
        array('4', 'preferred_start_date'),
        array('5', 'preferred_start_time'),
        array('129', 'preferred_end_date'),
        array('7', 'preferred_end_time'),
        array('96', 'alternative_start_date'),
        array('97', 'alternative_start_time'),
        array('96', 'alternative_end_time'),
        array('99', 'alternative_end_date'),
        array('124', 'schedule_exclusions'),
        array('154', 'custom_schedule_details'),
        array('140', 'image_1'),
        array('141', 'image_2'),
        array('142', 'image_3'),
        array('143', 'image_4'),
        array('144', 'image_5'),
        array('145', 'image_6'),
        array('123', 'promo_videos', 'field_5f7cd1ffdd06a'),
        array('119', 'short_description'),
        // array('73', 'audience', 'field_5f35a5f833a04'),
        array('57', 'location'),
        array('153', 'materials'),
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
        array('91', 'prev_session_links'),
        array('92', 'comfort_level'),
        array('93', 'technical_setup'),
        array('108', 'basic_skills'),
        array('109', 'skills_taught'),
        array('148', 'public_email'),
        array('152', 'attendee_communication_email'),
        array('135', 'webinar_link'),
        array('43', 'min_participants')
    );
    //update the acf fields with the submitted values from the form
    foreach ($field_mapping as $field) {
        $fieldID = $field[0];
        $meta_field = $field[1];
        $field_key = (isset($field[2]) ? $field[2] : '');
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
                update_post_meta($post_id, $meta_field, attachment_url_to_postid($entry[$fieldID])); // this should hopefully use the attachment id                
            } else {
                update_post_meta($post_id, $meta_field, $entry[$fieldID]);
            }
        }
        // checkboxes are set with a decimal point for each selection so theisset in entry doesn't work
        if (isset($fieldData->type)) {
            if ($fieldData->type == 'checkbox' || ($fieldData->type == 'post_custom_field' && $fieldData->inputType == 'checkbox')) {
                $checked = $fieldData->get_value_export($entry);
                $values = explode(', ', $checked);
                update_field($field_key, $values, $post_id);
            }
        }
    }
}

// for fields we want to use as filters, map them to an event custom field rather than an acf
function update_event_additional_fields($entry, $form, $post_id) {
    $ageData = GFAPI::get_field($form, 73);
    // checkboxes are set with a decimal point for each selection so theisset in entry doesn't work
    if (isset($ageData->type)) {
        if ($ageData->type == 'checkbox' || ($ageData->type == 'post_custom_field' && $ageData->inputType == 'checkbox')) {
            $checked = str_replace(", ", "|", $ageData->get_value_export($entry, $post_id, true));
            //having to use these damn custom names stops this from being very extendable/dynamic
            update_post_meta($post_id, "_ecp_custom_3", $checked);
        }
    }
}

function event_post_meta($entry, $form, $post_id) {
    $tagArray = array();
    foreach ($entry as $key => $value) {
        if (strpos($key, "50.") === 0) {
            $tagArray[] = $value;
        }
    }
    // Set the taxonomies    
    wp_set_object_terms($post_id, $entry['12'], 'tribe_events_cat'); //program type
    wp_set_object_terms($post_id, $tagArray, 'post_tag');  //program theme
    
    // Set the featured Image
    set_post_thumbnail($post_id, attachment_url_to_postid($entry['9']));
}

function update_organizer_data($entry, $form, $personID, $parameter_array) {
    $userSocial         = getFieldByParam('user_social', $parameter_array, $entry); //this is a serialized field
    $userWebsite        = getFieldByParam('user_website', $parameter_array, $entry);
    $facilitator_info   = getFieldByParam('user-bio', $parameter_array, $entry);
    
    $socialLinks = unserialize($userSocial); //TBD need to find more secure way of doing this to avoid code injection
    
    $repeater = array();
    foreach ($socialLinks as $value) {
        $repeater[] = array("field_5f7e086a4a5a3" => $value);
    }
    // update ACF fields for the event organizer    
    update_field("social_links", $repeater, $personID);
    update_field("website", $userWebsite, $personID);
    update_field("facilitator_info", $facilitator_info, $personID);        
}