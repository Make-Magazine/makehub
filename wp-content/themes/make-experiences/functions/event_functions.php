<?php

//duplicate entry
add_action('gravityview/duplicate-entry/duplicated', 'duplicate_entry', 10, 2);

function duplicate_entry($duplicated_entry, $entry) {
    error_log('duplicate_entry with form id ' . $duplicated_entry['form_id']);
    $form = GFAPI::get_form($duplicated_entry['form_id']);
    create_event($duplicated_entry, $form);
}

function update_event_acf($entry, $form, $post_id, $parameterArray) {
    //field mapping - ** note - upload fields don't work here. use post creation feed for that **
    //0 indicie = gravity form field id
    //1 indicie = acf field name/event meta fields
    //2 indicie (optional) = acf field key or subfield key (for repeaters)
    //can't set parameter names on image fields, so we have touse the field ids
    $field_mapping = array(
        array('140', 'image_1'),
        array('141', 'image_2'),
        array('142', 'image_3'),
        array('143', 'image_4'),
        array('144', 'image_5'),
        array('145', 'image_6'),
        array('', 'promo_videos', 'field_5f7cd1ffdd06a'),
        array('', 'short_description'),
        array('', 'audience', 'field_5f35a5f833a04'),
        array('', 'location'),
        array('', 'materials'),
        array('', 'kit_required'),
        array('', 'kit_price_included'),
        array('', 'kit_supplier'),
        array('', 'other_kit_supplier'),
        array('', 'kit_shipping_time'),
        array('', 'kit_url'),
        array('', 'wish_list_urls', 'field_5f7cc93ab762c'),
        array('', 'prior_hosted_event'),
        array('', 'hosted_live_stream'),
        array('', 'video_conferencing', 'field_5f60f9bfa1d1e'),
        array('', 'prev_session_links'),
        array('', 'comfort_level'),
        array('', 'technical_setup'),
        array('', 'basic_skills'),
        array('', 'skills_taught'),
        array('', 'public_email'),
        array('', 'attendee_communication_email'),
        array('', 'webinar_link')
    );
   
    //update the acf fields with the submitted values from the form
    foreach ($field_mapping as $field) {
        $fieldID = 0;
        if ($field[0] == '') {
            //determine field id by parameter name
            $paramName = $field[1];
            
            if (isset($parameterArray[$paramName])) {
                $fieldInfo = $parameterArray[$paramName];
                if (isset($fieldInfo)) {
                    $fieldID = (string) $fieldInfo->id;
                }
            }
        } else {
            $fieldID = $field[0];
        }
        
        $meta_field = $field[1];
        echo '$meta_field='.$meta_field.'<br/>';
        if($meta_field =='materials'){
            echo '$fieldID='.$fieldID.'<br/>';
            echo 'field type = '.$fieldData->type.'<br/>';
            echo 'materials = '.$entry[$fieldID].'<br/>';
        }
        
        $field_key = (isset($field[2]) ? $field[2] : '');
        $fieldData = GFAPI::get_field($form, $fieldID);

        if ($fieldID!=0 && isset($entry[$fieldID])) {
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
                //update_post_meta($post_id, $meta_field, $entry[$fieldID]);
                update_field($meta_field, $entry[$fieldID], $post_id);
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

function event_post_meta($entry, $form, $post_id, $parameter_array) {
    // Set the taxonomies       
    $expType = getFieldByParam('exp-type', $parameter_array, $entry);
    $expCats = getFieldByParam('exp-cats', $parameter_array, $entry);

    wp_set_object_terms($post_id, $expType, 'event_types'); //program type    
    wp_set_object_terms($post_id, $expCats, 'espresso_event_categories');  //event Categories
    // Set the featured Image
    set_post_thumbnail($post_id, attachment_url_to_postid($entry['9']));
}

function update_organizer_data($entry, $form, $personID, $parameter_array) {
    $userSocial = getFieldByParam('user_social', $parameter_array, $entry); //this is a serialized field
    $userWebsite = getFieldByParam('user_website', $parameter_array, $entry);
    $facilitator_info = getFieldByParam('user-bio', $parameter_array, $entry);

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
