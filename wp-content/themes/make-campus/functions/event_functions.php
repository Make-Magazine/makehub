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
        array('4', 'preferred_end_date'),
        array('7', 'preferred_end_time'),
        array('96', 'alternative_start_date'),
        array('97', 'alternative_start_time'),
        array('96', 'alternative_end_time'),
        array('99', 'alternative_end_date'),
        array('124', 'schedule_exclusions'),
        array('140', 'image_1'),
        array('141', 'image_2'),
        array('142', 'image_3'),
        array('143', 'image_4'),
        array('144', 'image_5'),
        array('145', 'image_6'),
        array('123', 'promo_videos', 'field_5f7cd1ffdd06a'),
        array('19', 'about'),
        array('119', 'short_description'),
        array('73', 'audience', 'field_5f35a5f833a04'),
        array('57', 'location'),
        array('139', 'materials', 'field_5f7b4abb07cab'),
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

function event_organizer($entry) {
    global $wpdb;

    $organizerData = array(
        'Organizer' => $entry['116.3'] . " " . $entry['116.6'],
        'Email' => wp_get_current_user()->user_email,
        'Website' => $entry['128']
    );

    // pull the id of the last organizer with the submitter's email address so we don't create a duplicate
    $existingOrganizer = $wpdb->get_var('
		SELECT post_id 
		FROM ' . $wpdb->prefix . 'postmeta 
		WHERE meta_key = "_OrganizerEmail" and meta_value = "' . $organizerData['Email'] . '" 
		order by post_id DESC limit 1');
    if ($existingOrganizer) {
        $organizerData = array();
        $organizerData['OrganizerID'] = $existingOrganizer;
    }

    return $organizerData;
}

function update_organizer_data($entry, $form, $organizerData, $post_id) {
    // update social media fields for the event organizer
    $organizer_id = tribe_get_organizer_id($post_id);
    $socialField = GFAPI::get_field($form, 127);
    $socialLinks = explode(', ', $socialField->get_value_export($entry));
    $num = 1;
    $repeater = [];
    foreach ($socialLinks as $value) {
        $repeater[] = array("field_5f7e086a4a5a3" => $value);
        $num++;
    }
    update_field("social_links", $repeater, $organizer_id);

    $organizerArgs = array("Website" => $entry['128']);
    tribe_update_organizer($organizer_id, $organizerArgs);

    // Upload featured image to Organizer page
    set_post_thumbnail(get_post($organizer_id, 'OBJECT', 'tribe_organizer'), get_attachment_id_from_url($entry['118']));
}

function update_ticket_data($entry, $post_id) {
    $api = Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance();
    $ticket = new Tribe__Tickets__Ticket_Object();
    $ticket->name = "Ticket - " . $entry['1'];
    $ticket->description = (isset($entry['42']) ? $entry['42'] : '');
    $ticket->price = (isset($entry['37']) ? $entry['37'] : '');
    $ticket->capacity = (isset($entry['106']) ? $entry['106'] : '');
    // these would be used if we wanted to limit the time tickets were on sale
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
    ));

    if ($ticket->capacity == 0 || $ticket->capacity == '') {
        $ticket->capacity = -1;
        $woo_stock = 99999;
    } else {
        $woo_stock = $ticket->capacity;
    }
    
    tribe_tickets_update_capacity($ticket->ID, $ticket->capacity);

    update_post_meta($ticket->ID, '_stock', $woo_stock);
    update_post_meta($ticket->ID, '_stock_status', "instock"); //because tickets were showing up with stock, but still the outofstock flag in woocommerce
}

function event_post_meta($entry, $form, $post_id) {
    $tags = GFAPI::get_field($form, 50);
    $tagArray = array();
    if ($tags->type == 'checkbox') {
        // Get a comma separated list of checkboxes checked
        $checked = $tags->get_value_export($entry);
        // Convert to array.
        $tagArray = explode(', ', $checked);
    }
    // Set the taxonomies    
    wp_set_object_terms($post_id, $entry['12'], 'tribe_events_cat'); //program type
    wp_set_object_terms($post_id, $tagArray, 'post_tag');  //program theme
    // Set the featured Image
    set_post_thumbnail($post_id, get_attachment_id_from_url($entry['9']));
}

function event_recurrence_update($entry, $post_id, $start_date, $end_date, $end_recurring) {
    $recurrence_type = $entry['130'];
    $end_count = $end_recurring->diff($start_date)->days;
    if ($recurrence_type == "Every Week") {
        $end_count = floor($end_count / 7) + 1;
    } else if ($recurrence_type == "Every Month") {
        $end_count = countMonths($entry['4'], $entry['129']);
    }
    $recurrence_data = array(
        'recurrence' => array(
            'rules' => array(
                array(
                    'type' => $entry['130'],
                    'end-type' => 'on',
                    'end' => $end_recurring->format('Y-m-d H:i:s'), // this is the date the series should end on, but does nothing
                    'end-count' => $end_count, // this is what is actually ending the series
                    'EventStartDate' => $start_date->format('Y-m-d H:i:s'),
                    'EventEndDate' => $end_date->format('Y-m-d H:i:s'), // this is just for the end of the first occurence of the event
                ),
            ),
        ),
    );
    update_field("number_of_sessions", $end_count . " / " . strtolower($recurrence_type), $post_id);
    $recurrence_meta = new Tribe__Events__Pro__Recurrence__Meta();
    $recurrence_meta->updateRecurrenceMeta($post_id, $recurrence_data);
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

function get_event_attendees($event_id) {
    $attendee_list = Tribe__Tickets__Tickets::get_event_attendees($event_id);
    return $attendee_list;
}

function get_event_attendee_emails($event_id) {
    $attendees_data = get_event_attendees($event_id);
    $attendees_emails = array();
    foreach ($attendees_data as $data) {
        $attendees_emails[] = $data['purchaser_email'];
    }
    return $attendees_emails;
}

function countMonths($date1, $date2) {
    $begin = new DateTime($date1);
    $end = new DateTime($date2);
    $end = $end->modify('+1 month');

    $interval = DateInterval::createFromDateString('1 month');

    $period = new DatePeriod($begin, $interval, $end);
    $counter = iterator_count($period);

    return $counter;
}

//open organizer in a new tab
add_filter('tribe_get_event_organizer_link_target', 'set_callback_blank', 10, 3);

function set_callback_blank($target, $url, $post_id) {
    return '_blank';
}

// Prevents Next/Prev pages from being loaded via Ajax if photo view is the homepage
function tribe_prevent_ajax_paging() {
    if (is_front_page() || is_home()) {
        echo "<script>
				jQuery(document).ready(function(){
					jQuery( '.blog .tribe-events-c-top-bar__nav-link--prev, .blog .tribe-events-c-top-bar__nav-link--next, .blog .tribe-events-c-nav__next, .blog .tribe-events-c-nav__prev' ).unbind();
					jQuery( '.archive .tribe-events-c-top-bar__nav-link--prev, .archive .tribe-events-c-top-bar__nav-link--next, .archive .tribe-events-c-nav__next, .archive .tribe-events-c-nav__prev' ).unbind();
				});
			  </script>";
    }
}

add_action('wp_footer', 'tribe_prevent_ajax_paging', 99);
