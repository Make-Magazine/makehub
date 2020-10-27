<?php

add_filter('gform_notification_events', 'custom_notification_event');

function custom_notification_event($events) {
    $events['accepted_event_occur_48_hours'] = __('Event Occuring In 2 days');
    $events['accepted_event_occur_seven_days'] = __('Event Occuring In 7 days');
    $events['after_event'] = __('After Event');
    $events['send_manually'] = __('Send Manually');
    $events['maker_updated_exhibit'] = __('Maker Updated Entry', 'gravityforms');
    return $events;
}

//cron job triggers this check for any accepted entries where the event is occuring in the next 48 hours. 
function trigger_notificatons() {
    global $wpdb;
    ///////////////////////////////////////////////
    /*           48 HOURS BEFORE EVENT           */
    ///////////////////////////////////////////////
    $sql = 'SELECT post_id '
            . 'FROM  ' . $wpdb->prefix . 'postmeta '
            . 'left outer join ' . $wpdb->prefix . 'posts posts on (posts.id = post_id) '
            . 'WHERE  meta_key LIKE "_EventStartDate" AND '
            . '       meta_value like CONCAT("%",CURDATE() + INTERVAL 2 DAY,"%") and post_status = "publish"';
    //trigger notificaton
    build_send_notifications('accepted_event_occur_48_hours', $sql);


    ///////////////////////////////////////////////
    /*           7 days BEFORE EVENT             */
    ///////////////////////////////////////////////
    $sql = 'SELECT post_id '
            . 'FROM  ' . $wpdb->prefix . 'postmeta '
            . 'left outer join ' . $wpdb->prefix . 'posts posts on (posts.id = post_id) '
            . 'WHERE  meta_key LIKE "_EventStartDate" AND '
            . '       meta_value like CONCAT("%",CURDATE() + INTERVAL 7 DAY,"%") and post_status = "publish"';
    //trigger notificaton
    build_send_notifications('accepted_event_occur_48_hours', $sql);

    ///////////////////////////////////////////////
    /*                AFTER EVENT                */
    ///////////////////////////////////////////////
    $sql = 'SELECT post_id '
            . 'FROM  ' . $wpdb->prefix . 'postmeta '
            . 'left outer join ' . $wpdb->prefix . 'posts posts on (posts.id = post_id) '
            . 'WHERE  meta_key LIKE "_EventEndDate" AND '
            . '       meta_value like CONCAT("%",CURDATE() + INTERVAL 1 DAY,"%") and post_status = "publish"';
    //trigger notificaton
    build_send_notifications('accepted_event_occur_48_hours', $sql);
}

function build_send_notifications($event, $sql) {
    $events = $wpdb->get_results($sql);
    foreach ($events as $event) {
        //find associated entry    
        $entry_id = $wpdb->get_var('select id from ' . $wpdb->prefix . 'gf_entry where post_id = ' . $event->post_id);
        if ($entry_id != '') {
            $entry = GFAPI::get_entry($entry_id);
            $form = GFAPI::get_form($entry['form_id']);

            //trigger notificaton            
            $notifications_to_send = GFCommon::get_notifications_to_send($event, $form, $entry);
            foreach ($notifications_to_send as $notification) {
                if ($notification['isActive']) {
                    if (strpos($notification['to'], "{{attendee_list}}") !== false) {
                        $notification['to'] = str_replace('{{attendee_list}}', implode(',', get_event_attendee_emails($event->post_id)), $notification['to']);
                    }
                    GFCommon::send_notification($notification, $form, $entry);
                }
            }
        }
    }
}


// trigger an email to admin when an entry is updated via gravity view
add_action('gform_after_update_entry', 'update_entry', 10, 3);

function update_entry($form, $entry_id, $orig_entry = array()) {
    //log update in error log
    /*$current_user = wp_get_current_user();
    $message = 'Entry ' . $entry_id . ' updated by ' . $current_user->user_email;
    error_log($message);*/
        
    //get updated entry
    $updatedEntry = GFAPI::get_entry(esc_attr($entry_id));

    //check for updates and trigger maker update notification    
    $notifications_to_send = GFCommon::get_notifications_to_send('maker_updated_exhibit', $form, $updatedEntry);
    foreach ($notifications_to_send as $notification) {
        if ($notification['isActive']) {
            $text = $notification['message'];
            $notification['message'] = gf_entry_changed_fields($text, $entry_id, $orig_entry, $updatedEntry, $form);
            //error_log($notification['message'] );
            GFCommon::send_notification($notification, $form, $updatedEntry);
        }
    }
}

function gf_entry_changed_fields($text, $entry_id, $orig_entry, $updatedEntry, $form) {
  $entry_id = (isset($lead['id'])?$lead['id']:'');

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

   //if there are changes to the record, send them to the admin
   if (!empty($updates)) {
      $current_user = wp_get_current_user();
      $message  = '';
      $message .= 'The following changes were made:<br><br>';

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
                 . '<td><b>' . $update['fieldLabel'] . '<br>Field ID: '. $update['field_id'] .'</b></td>'
                 . '<td style="border: thin solid grey; background-color: beige; padding: 10px;">' . $update['field_before'] . '</td>'
                 . '<td style="border: thin solid grey; background-color: #C3FBB2; padding: 10px;">' . $update['field_after'] . '</td></tr>';
      }
      $message .= '</tbody>';
      $message .= '</table>';
      

      $text = str_replace('{entry_changed_fields}', $message, $text);
   }
   
     //end update entry changed fields
  }
  return $text;
}  