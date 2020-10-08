<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function update_event_acf($entry, $form, $post_id) {
    //field mapping - ** note - upload fields don't work here. use post creation feed for that **
    //0 indicie = gravity form field id
    //1 indicie = acf field name/event meta fields
    //2 indicie (optional) = acf field key or subfield key (for repeaters)
    $field_mapping = array(
        array('4', 'preferred_start_date'),
        array('5', 'preferred_start_time'),
        array('4', 'preferred_end_date'),
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
                update_post_meta($post_id, $meta_field, get_attachment_id_from_url($entry[$fieldID])); // this should hopefully use the attachment id
            } else {
                //error_log('updating image ACF field '.$meta_field. ' with GF field '.$fieldID . ' with value '.$entry[$fieldID]);
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