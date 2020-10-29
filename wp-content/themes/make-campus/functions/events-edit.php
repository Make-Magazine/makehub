<?php
// After the gravity view is updated, we want to update the created post associated with it. 
// SO FAR, THIS IS UPDATING THE TITLE, CONTENT, FEATURED IMAGE, AND TEXT ACF FIELDS... needs work for taxonomies
add_action('gravityview/edit_entry/after_update', 'gravityview_event_update', 10, 4);

function gravityview_event_update($form, $entry_id, $entry_object = '') {
    if ($entry_object == '')
        return;
    
    //error_log('gravityview_event_update');
    $entry = $entry_object->entry;
                
    $post_id = $entry["post_id"];

    //calculate start and end date 
    $start_date = date_create($entry['4'] . ' ' . $entry['5']);
    $end_date   = date_create($entry['4'] . ' ' . $entry['7']);
    $end_recurring = date_create($entry['129'] . ' ' . $entry['7']);

    $organizerData = event_organizer($entry);

    //update event
    $post_data = array(
        'ID' => $post_id,
        'post_title' => $entry['1'],
        'post_content' => $entry['2'],        
        'Organizer' => $organizerData
    );
    //error_log(print_r($post_data, TRUE));
    wp_update_post($post_data);
    
    //update start and end dates
    update_post_meta($post_id, '_EventStartDate',$start_date->format('Y-m-d H:i:s'));
    update_post_meta($post_id, '_EventEndDate',$end_date->format('Y-m-d H:i:s'));

    //update timezone    
    update_post_meta($post_id, '_EventTimezone', $entry['131']);

    // update taxonomies, featured image, etc
    event_post_meta($entry, $form, $post_id);

    //update organizer (not email or name)
    update_organizer_data($entry, $form, $organizerData, $post_id);

    // If they want a recurring event, we can do that
    if ($entry['100'] == "no") {
        event_recurrence_update($entry, $post_id, $start_date, $end_date, $end_recurring);
    }
        
    // Set the ACF data
    update_event_acf($entry, $form, $post_id);        
}

// trigger an email to admin when an entry is updated via gravity view
add_action('gform_after_update_entry', 'send_update_entry_notification', 10, 3);
function send_update_entry_notification($form, $entry_id, $orig_entry=array()) {    
    //get updated entry
    $updatedEntry = GFAPI::get_entry(esc_attr($entry_id));
    
    //check for updates and trigger maker update notification    
    $notifications_to_send = GFCommon::get_notifications_to_send('maker_updated_exhibit', $form, $updatedEntry);
    foreach ($notifications_to_send as $notification) {
        if ($notification['isActive']) {    
            $text = $notification['message'];
            $notification['message'] = gf_entry_changed_fields($text,$entry_id, $orig_entry, $updatedEntry, $form);    
            
            GFCommon::send_notification($notification, $form, $updatedEntry);
        }
    }
}

function gf_entry_changed_fields($text, $entry_id, $orig_entry, $updatedEntry, $form) {
    //Entry Changed Fields
    if (strpos($text, '{entry_changed_fields}') !== false) {
        $updates = array();
        foreach ($form['fields'] as $field) {
            //send notification after entry is updated in maker admin
            $input_id = $field->id;
            
            //if field type is checkbox we need to compare each of the inputs for changes
            $inputs = $field->get_entry_inputs();
            
            if (is_array($inputs)) {
                foreach ($inputs as $input) {
                    $input_id = $input['id'];
                    $origField = (isset($orig_entry[$input_id]) ? $orig_entry[$input_id] : '');
                    $updatedField = (isset($updatedEntry[$input_id]) ? $updatedEntry[$input_id] : '');
                    $fieldLabel = ($field['adminLabel'] != '' ? $field['adminLabel'] : $field['label']);
                    if ($origField != $updatedField) {
                        //update field id
                        $updates[] = array(
                            'field_id' => $input_id,
                            'field_before' => $origField,
                            'field_after' => $updatedField,
                            'fieldLabel' => $fieldLabel);
                    }
                }
            } else {                
                $origField = (isset($orig_entry[$input_id]) ? $orig_entry[$input_id] : '');
                $updatedField = (isset($updatedEntry[$input_id]) ? $updatedEntry[$input_id] : '');
                             
                $fieldLabel = ($field['adminLabel'] != '' ? $field['adminLabel'] : $field['label']);
                if ($origField != $updatedField) {
                    //update field id
                    $updates[] = array('field_id' => $input_id,
                        'field_before' => $origField,
                        'field_after' => $updatedField,
                        'fieldLabel' => $fieldLabel);
                }
            }
        }
                
        $message = 'The following changes were made:<br><br>';
        
        //if there are changes to the record, send them to the admin
        if (!empty($updates)) {                        
            // Build  table of changed items
            $message .= '<table width="100%">'
                    . ' <thead>'
                    . ' <tr>'
                    . '    <td width="20%">&nbsp;</td>'
                    . '    <td width="40%"><strong>Before</strong></td>'
                    . '    <td width="40%"><strong>After</strong></td>'
                    . ' </tr></thead>';

            $message .= '<tbody>';
            //process updates
            foreach ($updates as $update) {
                $message .= '<tr>'
                        . '<td><b>' . $update['fieldLabel'] . '<br>Field ID: ' . $update['field_id'] . '</b></td>'
                        . '<td style="border: thin solid grey; background-color: beige; padding: 10px;">' . $update['field_before'] . '</td>'
                        . '<td style="border: thin solid grey; background-color: #C3FBB2; padding: 10px;">' . $update['field_after'] . '</td></tr>';
            }
            $message .= '</tbody>';
            $message .= '</table>';

            $text = str_replace('{entry_changed_fields}', $message, $text);
        }else{
            $message .= 'No changes made';
            $text = str_replace('{entry_changed_fields}', $message, $text);
        }

        //end update entry changed fields
    }
    
    return $text;
}
