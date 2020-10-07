<?php

//require_once( ABSPATH . 'wp-content/plugins/event-tickets/src/Tribe/Tickets.php');

//disable the default post creation
add_filter( 'gform_disable_post_creation_7', 'disable_post_creation', 10, 3 );
function disable_post_creation( $is_disabled, $form, $entry ) {
    return true;
}

// Create event with ticket
add_action('gravityview/duplicate-entry/duplicated', 'duplicate_entry', 10, 2);
function duplicate_entry($duplicated_entry, $entry){
    error_log('duplicate_entry with form id '.$duplicated_entry['form_id']);
    $form = GFAPI::get_form($duplicated_entry['form_id']);
    create_event($duplicated_entry, $form);
}
add_action('gform_after_submission_7', 'create_event', 10, 2);

function create_event($entry, $form) {
    error_log('create event function');
    $tags = GFAPI::get_field($form, 50);

    $tagArray = array();
    if ($tags->type == 'checkbox') {
        // Get a comma separated list of checkboxes checked
        $checked = $tags->get_value_export($entry);
        // Convert to array.
        $tagArray = explode(', ', $checked);
    }
    $start_date = date_create($entry['4'] . ' ' . $entry['5']);
    $end_date   = date_create($entry['4'] . ' ' . $entry['7']);

    $organizerData = array(
        'Organizer' => $entry['116.3'] . " " . $entry['116.6'],
        'Email' => $entry['115']
    );
    $user = get_current_user_id();
    
    $event_args = array(
        'post_author' => $user,
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

    // Set the arguments for the recurring event.
    if ($entry['100'] == "no") {
        $recurrence_data = array(
            'recurrence' => array(
                'rules' => array(
                    array(
                        'type' => 'Every Year',
                        'end-type' => 'Never',
                        'end' => '',
                        'end-count' => '',
                        'EventStartDate' => $start_date,
                        'EventEndDate' => $end_date,
                        'custom' => array(),
                        'occurrence-count-text' => 'events',
                    ),
                ),
            ),
        );

        // Instantiate and set it in motion.
        $recurrence_meta = new \Tribe__Events__Pro__Recurrence__Meta();
        $recurrence_meta->updateRecurrenceMeta($post_id, $recurrence_data);
    }

    // Upload featured image to Organizer page
    set_post_thumbnail(get_page_by_title($organizerData['Organizer'], 'OBJECT', 'tribe_organizer'), get_attachment_id_from_url($entry['118']));

    // Set the taxonomies
    wp_set_object_terms($post_id, $entry['12'], 'tribe_events_cat');
    wp_set_object_terms($post_id, $tagArray, 'post_tag');

    // Set the featured Image
    set_post_thumbnail($post_id, get_attachment_id_from_url($entry['9']));

    //field mapping - ** note - upload fields don't work here. use post creation feed for that **
    //0 indicie = gravity form field id
    //1 indicie = acf field name/event meta fields
    //1 indicie (optional) = acf field key or subfield key (for repeaters)
    $field_mapping = array(
        array('4', 'preferred_start_date'),
        array('5', 'preferred_start_time'),
        array('6', 'preferred_end_date'),
        array('7', 'preferred_end_time'),
        array('96', 'alternative_start_date'),
        array('97', 'alternative_start_time'),
        array('96', 'alternative_end_time'),
        array('99', 'alternative_end_date'),
        array('31', 'image_1'),
        array('32', 'image_2'),
        array('33', 'image_3'),
        array('54', 'image_4'),
        array('55', 'image_5'),
        array('56', 'image_6'),
        array('19', 'about'),
        array('119', 'short_description'),
        array('73', 'audience', 'field_5f35a5f833a04'),
        array('57', 'location'),
        array('72', 'materials', 'field_5f7b4abb07cab'),
        array('78', 'kit_required'),
        array('79', 'kit_price_included'),
        array('80', 'kit_supplier'),
        array('111', 'other_kit_supplier'),
        array('82', 'kit_url'),
        array('83', 'amazon_url'),
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
        }
        // entry field id is not set in time for checkboxes, apparently
        if ($fieldData->type == 'post_custom_field' && $fieldData->inputType == 'checkbox' || $fieldData->type == 'checkbox') {            
            $checked = $fieldData->get_value_export($entry);
            $values = explode(', ', $checked);
            update_field($field_key, $values, $post_id);
        }
    }

    // create ticket for event // CHANGE TO WOOCOMMERCE AFTER PURCHASING EVENTS PLUS PLUGIN
    //$api = Tribe__Tickets__Commerce__PayPal__Main::get_instance();
    $api = Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance();
    $ticket = new Tribe__Tickets__Ticket_Object();
    $ticket->name = "Ticket";
    $ticket->description = (isset($entry['42']) ? $entry['42'] : '');
    $ticket->price = (isset($entry['37']) ? $entry['37'] : '');
    $ticket->capacity = (isset($entry['43']) ? $entry['43'] : '');
    $ticket->start_date = (isset($entry['45']) ? $entry['45'] : '');
    $ticket->start_time = (isset($entry['46']) ? $entry['46'] : '');
    $ticket->end_date = (isset($entry['47']) ? $entry['47'] : '');
    $ticket->end_time = (isset($entry['48']) ? $entry['48'] : '');

    // Save the ticket
    $ticket->ID = $api->save_ticket($post_id, $ticket, array(
        'ticket_name' => $ticket->name,
        'ticket_price' => $ticket->price,
        'ticket_description' => $ticket->description,
        //'start_date' => $ticket->start_date,
        //'start_time' => $ticket->start_time,
        //'end_date' => $ticket->end_date,
        //'end_time' => $ticket->end_time,
        // none of these work
        'event_capacity' => $ticket->capacity,
        'capacity' => $ticket->capacity,
        'stock' => $ticket->capacity,
        'tribe_ticket' => [
            'mode' => 'global',
            'event_capacity' => $ticket->capacity,
            'capacity' => $ticket->capacity
        ],
    ));
    
    //update the entry with the event post id     
    global $wpdb;
    $wpdb->update( $wpdb->prefix.'gf_entry', array( 'post_id' => $post_id),array('ID'=>$entry['id']));    
}

function get_attachment_id_from_url($attachment_url) {
    global $wpdb;
    $attachment_id = false;
    // Get the upload directory paths
    $upload_dir_paths = wp_upload_dir();
    // Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
    if (false !== strpos($attachment_url, $upload_dir_paths['baseurl'])) {
        // If this is the URL of an auto-generated thumbnail, get the URL of the original image
        $attachment_url = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url);
        // Remove the upload path base directory from the attachment URL
        $attachment_url = str_replace($upload_dir_paths['baseurl'] . '/', '', $attachment_url);
        // Finally, run a custom database query to get the attachment ID from the modified attachment URL
        $attachment_id = $wpdb->get_var($wpdb->prepare("SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url));
    }
    return $attachment_id;
}

function getOrganizerObject($organizerID) {
    $organizerArray = tribe_get_organizers();
    $organizerObj = new stdClass();
    foreach ($organizerArray as $organizerKey) {
        if ($organizerKey->ID == $organizerID) {
            $organizerObj = $organizerKey;
        }
    }
    return $organizerObj;
}

function get_event_attendees($event_id) {
    $attendee_list = Tribe__Tickets__Tickets::get_event_attendees($event_id);
    return $attendee_list;
}

// After the gravity view is updated, we want to update the created post associated with it. 
// SO FAR, THIS IS UPDATING THE TITLE, CONTENT, FEATURED IMAGE, AND TEXT ACF FIELDS... needs work for taxonomies
add_action('gravityview/edit_entry/after_update', 'gravityview_event_update', 10, 4);

function gravityview_event_update($form, $entry_id, $gv_entry_obj) {
    error_log('gravityview_event_update');
    $entry = $gv_entry_obj->entry;
    error_log(print_r($entry, TRUE));
    $post_id = $entry["post_id"];
    //update event
    $post_data = array(
        'ID' => $post_id,
        'post_title' => $entry['1'],
        'post_content' => $entry['2'],        
    );
    wp_update_post($post_data);
    
//update acf
    

    /*
    //'post_category' => gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Experience Type"),
        //'tags_input' => gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Category Tags")
        /*'EventStartDate' => $entry['4'],
        'EventEndDate' => $entry['4'],
        'EventStartHour' => $start_date->format('h'),
        'EventStartMinute' => $start_date->format('i'),
        'EventStartMeridian' => $start_date->format('A'),
        'EventEndHour' => $end_date->format('h'),
        'EventEndMinute' => $end_date->format('i'),
        'EventEndMeridian' => $end_date->format('A'),
        'Organizer' => $organizerData
    
    //error_log(print_r(gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Experience Type"), TRUE));
    //error_log(print_r(gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Category Tags"), TRUE));
    //error_log("Featured Image: " . print_r(gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Featured Image")), TRUE);
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $media = media_sideload_image(gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Featured Image"), $post_id);
    if (!empty($media) && !is_wp_error($media)) {
        $args = array(
            'post_type' => 'attachment',
            'posts_per_page' => -1,
            'post_status' => 'any',
            'post_parent' => $post_id
        );
        $attachments = get_posts($args);
        if (isset($attachments) && is_array($attachments)) {
            foreach ($attachments as $attachment) {
                $image = wp_get_attachment_image_src($attachment->ID, 'full');
                // determine if in the $media image we created, the string of the URL exists
                if (strpos($media, $image[0]) !== false) {
                    // if so, we found our image. set it as thumbnail
                    set_post_thumbnail($post_id, $attachment->ID);
                    break;
                }
            }
        }
    }
    // Not sure how else to update all the fields but to mention the by name
    update_field("about", gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "About You"), $post_id);
    update_field("location", gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Location"), $post_id);
    update_field("materials", gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Experience Materials"), $post_id);
     
     */
}

// rather than use potentially changing field ids, look up by label
function gf_get_value_by_label($form, $entry, $label) {
    foreach ($form['fields'] as $field) {
        $lead_key = $field->label;
        if (strToLower($lead_key) == strToLower($label)) {
            return $entry[$field->id];
        }
    }
    return false;
}

add_filter('acf/load_value/type=checkbox', function($value, $post_id, $field) {
    // Value should be an array, not a string
    if (is_string($value)) {
        $value = get_post_meta($post_id, $field['name'], false);
    }
    return $value;
}, 10, 3);


add_filter('gform_ajax_spinner_url', 'spinner_url', 10, 2);

function spinner_url($image_src, $form) {
    return "/wp-content/universal-assets/v1/images/makey-spinner.gif";
}

function smartTruncate($string, $limit, $break = ".", $pad = "...") {
    // return with no change if string is shorter than $limit
    if (strlen($string) <= $limit)
        return $string;
    // is $break present between $limit and the end of the string?
    if (false !== ($breakpoint = strpos($string, $break, $limit))) {
        if ($breakpoint < strlen($string) - 1) {
            $string = substr($string, 0, $breakpoint) . $pad;
        }
    }

    return $string;
}


