<?php
// After the gravity view is updated, we want to update the created post associated with it. 

add_action('gform_after_update_entry', 'gravityview_event_update', 998, 3);

//function gravityview_event_update($form, $entry_id, $entry_object = '') {
function gravityview_event_update($form, $entry_id, $orig_entry=array()) {        
    $entry = GFAPI::get_entry($entry_id);                
    $post_id = $entry["post_id"];
    //check event status 
    $post_status = get_post_status($post_id);
                        
    
    //Update event - event name(1), long desc(2), short description(119)    
    $rest_endpoint = '/ee/v4.8.36/events/'.$post_id;
    $body_params = array('EVT_name' => $entry[1],
        'EVT_desc' => $entry[2],
        'EVT_short_desc' => $entry[119]
    );        
    $data = int_rest_req($rest_endpoint, $body_params, 'PUT');
    
    //Post-Approval: They should be able to edit everything but start and end date times, number of sessions, and ticket price. 
    if($post_status!='publish'){                
        //Start Date
        $date       = date_create($entry['4'] . ' ' . $entry['5']); 
        $start_date = date_format($date,"Y-m-d"). 'T'.date_format($date,"H:i:s"); //date format needs to be 2021-03-25T18:00:00    

        //End Date
        $date     = date_create($entry['4'] . ' ' . $entry['7']);
        $end_date = date_format($date,"Y-m-d"). 'T'.date_format($date,"H:i:s"); //date format needs to be 2021-03-25T18:00:00
        
        //Update Date/Time object - event name(1), start date, end date, registration limit(106)
        //find the date/time object for this event
        $rest_endpoint = '/ee/v4.8.36/events/2774/datetimes';
        $body_params = array();        
        $data = int_rest_req($rest_endpoint, $body_params, 'GET');  
        
        //loop through each date time and update accordingly
        //next set the date/time of the event
        $rest_endpoint = '/ee/v4.8.36/datetimes';
        $body_params = array('EVT_ID' => $event_id,
                            'DTT_name' => $entry['1'],
                            'DTT_EVT_start' => $start_date, //"Start time/date of Event - this is in the timezone of the site."
                            'DTT_EVT_end' => $end_date, //"End time/date of Event - this in the timezone of the site."
                            'DTT_reg_limit' => $entry['106'], //"Registration Limit for this time"
        );
        $data = int_rest_req($rest_endpoint, $body_params);
        
        //update ticket object - Ticket Name(?), Ticket Price(37), Ticket Minimum(43), ticket maximum(106), ticket available quantity(106)
        $rest_endpoint = '/ee/v4.8.36/tickets';
        $body_params = array('TKT_name' => "Ticket - " . $entry['1'],
            'TKT_description' => (isset($entry['42']) ? $entry['42'] : ''),
            'TKT_price' => (isset($entry['37']) ? $entry['37'] : 0),
            'TKT_min' => (isset($entry['43']) ? $entry['43'] : ''),
            'TKT_max' => (isset($entry['106']) ? $entry['106'] : ''),
            'TKT_qty' => (isset($entry['106']) ? $entry['106'] : ''), //"Quantity of this ticket that is available"
            'TKT_required' => true);
        $data = int_rest_req($rest_endpoint, $body_params);    
        
        //update prices object - Ticket Price(37)
    }
    $organizerData = event_organizer($entry);

    //update event
    $post_data = array(
        'ID' => $post_id,
        'post_title' => $entry['1'],
        'post_content' => $entry['2'],        
        'Organizer' => $organizerData,
    );
    //error_log(print_r($post_data, TRUE));
    wp_update_post($post_data);
    
    // update taxonomies, featured image, etc
    event_post_meta($entry, $form, $post_id);
               
    // Set the ACF data
    update_event_acf($entry, $form, $post_id);    
    // Set event custom fields for filtering
    update_event_additional_fields($entry, $form, $post_id);
}

// trigger an email to when an entry is updated via gravity view
add_action('gform_after_update_entry', 'send_update_entry_notification', 999, 3);
function send_update_entry_notification($form, $entry_id, $orig_entry=array()) {    
    //TBD only do this if the user updating is not an admin user
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
add_filter( 'gform_entry_field_value', function ( $value, $field, $entry, $form ) {
    $classes = array(
        'GF_Field_Checkbox',
        'GF_Field_MultiSelect',
        'GF_Field_Radio',
        'GF_Field_Select',
    );
 
    foreach ( $classes as $class ) {
        if ( $field instanceof $class ) {
            $value = $field->get_value_entry_detail( RGFormsModel::get_lead_field_value( $entry, $field ), $currency = '', $use_text = true, $format = 'html' );
            break;
        }
    }
 
    return $value;
}, 10, 4 );