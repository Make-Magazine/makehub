<?php

// After the gravity view is updated, we want to update the created post associated with it. 

add_action('gform_after_update_entry', 'gravityview_event_update', 998, 3);

//function gravityview_event_update($form, $entry_id, $entry_object = '') {
function gravityview_event_update($form, $entry_id, $orig_entry = array()) {
    //let's get the entry id and event id
    $entry = GFAPI::get_entry($entry_id);
    
    $event_id = $entry["post_id"];
    $event_status = get_post_status($event_id);
    
    //if the event is not published,  update event name, description, short description, ticket/schedule information
    if ($event_status != 'publish') {
        //find all fields set with a parameter name 
        $parameter_array = find_field_by_parameter($form);

        //update the event
        $eventName = getFieldByParam('event-name', $parameter_array, $entry); //event-name
        $longDescription = getFieldByParam('long-description', $parameter_array, $entry); //long-description
        $shortDescription = getFieldByParam('short-description', $parameter_array, $entry); //short_description

        $event_values = array(
            'EVT_name'     => $eventName,
            'EVT_desc'     => $longDescription,
            'EVT_short_desc' => $shortDescription
        );
        // update event
        $success = EEM_Event::instance()->update_by_ID($event_values, $event_id);
        

        //delete all schedule/tickets and then re-add to get all changes
        $event = EEM_Event::instance()->get_one_by_ID($event_id);
        $datetimes = $event->get_many_related('Datetime');
        foreach ($datetimes as $datetime) {
            $event->_remove_relation_to($datetime, 'Datetime');
            $tickets = $datetime->get_many_related('Ticket');
            foreach ($tickets as $ticket) {
                $ticket->_remove_relation_to($datetime, 'Datetime');
                $ticket->delete_related_permanently('Price');
                $ticket->delete_permanently();
            }
            $datetime->delete();
        }
        //now let's re-add the schedule
        setSchedTicket($parameter_array, $entry, $event_id);
    }

    event_post_meta($entry, $form, $event_id, $parameter_array); // update taxonomies, featured image, etc    
    update_event_acf($entry, $form, $event_id, $parameter_array); // Set the ACF data        
}

// trigger an email to when an entry is updated via gravity view
//add_action('gform_after_update_entry', 'send_update_entry_notification', 999, 3);
function send_update_entry_notification($form, $entry_id, $orig_entry = array()) {
    //We do not want to trigger this email if the edit is being done by an admin
    if (!current_user_can('administrator')) {
        //get updated entry
        $updatedEntry = GFAPI::get_entry(esc_attr($entry_id));

        //check for updates and trigger maker update notification    
        $notifications_to_send = GFCommon::get_notifications_to_send('maker_updated_exhibit', $form, $updatedEntry);
        foreach ($notifications_to_send as $notification) {
            if ($notification['isActive']) {
                $text = $notification['message'];
                $notification['message'] = gf_entry_changed_fields($text, $entry_id, $orig_entry, $updatedEntry, $form);

                GFCommon::send_notification($notification, $form, $updatedEntry);
            }
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
        } else {
            $message .= 'No changes made';
            $text = str_replace('{entry_changed_fields}', $message, $text);
        }

        //end update entry changed fields
    }

    return $text;
}

add_filter('gform_entry_field_value', function ( $value, $field, $entry, $form ) {
    $classes = array(
        'GF_Field_Checkbox',
        'GF_Field_MultiSelect',
        'GF_Field_Radio',
        'GF_Field_Select',
    );

    foreach ($classes as $class) {
        if ($field instanceof $class) {
            $value = $field->get_value_entry_detail(RGFormsModel::get_lead_field_value($entry, $field), $currency = '', $use_text = true, $format = 'html');
            break;
        }
    }

    return $value;
}, 10, 4);
