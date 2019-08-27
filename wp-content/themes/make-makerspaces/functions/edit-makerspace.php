<?php

// This code modifies what text is displayed to the users when there are no entries for them to edit
add_filter( 'gravityview/template/text/no_entries', 'modify_gravityview_no_entries_text', 10, 3 );

/**
 * Modify the text displayed when there are no entries. (Requires GravityView 2.0 or newer)
 * @param string $existing_text The existing "No Entries" text
 * @param bool $is_search  Is the current page a search result, or just a multiple entries screen?
 * @param \GV\Template_Context $context The context.
 */
function modify_gravityview_no_entries_text( $existing_text, $is_search = false, $context = null ) {
	$return = $existing_text."<br /><a href='/register'>Add a new Makerspace</a>";
		
	return $return;
}

//MF custom merge tags
add_filter('gform_custom_merge_tags', 'mf_custom_merge_tags', 10, 4);


function mf_custom_merge_tags($merge_tags, $form_id, $fields, $element_id) {
   $merge_tags[] = array('label' => 'Entry Changed Fields', 'tag' => '{entry_changed_fields}');
    
   return $merge_tags;
}

function mf_entry_changed_fields($text, $entry_id, $orig_entry, $updatedEntry, $form) {
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
      $message .= 'The makerspace was updated by ' . $current_user->user_email . '. <br><br>'
               . 'The following changes were made:<br><br>';

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

//add new Notification event of - send confirmation letter and maker cancelled exhibit
add_filter( 'gform_notification_events', 'add_event' );
function add_event( $notification_events ) {
   $notification_events['maker_updated_exhibit'] = __( 'Maker Updated Entry', 'gravityforms' );
   $notification_events['manual']                = __( 'Send Manually', 'gravityforms' );
   return $notification_events;
}

// trigger an email to admin when an entry is updated via gravity view
add_action('gform_after_update_entry', 'update_entry', 10, 3);

function update_entry($form, $entry_id, $orig_entry=array()) {
   $current_user = wp_get_current_user();
   $message = 'Entry ' . $entry_id . ' updated by '. $current_user->user_email;
   //error_log($message);
   
   //Create a note of the entry change.
	$results = mf_add_note($entry_id, $message);
   
   //get updated entry
   $updatedEntry = GFAPI::get_entry(esc_attr($entry_id));
   
   //check for updates     
   //Handle notifications for acceptance
   $notifications_to_send = GFCommon::get_notifications_to_send( 'maker_updated_exhibit', $form, $updatedEntry );
   foreach ( $notifications_to_send as $notification ) {
      if($notification['isActive']){
         $text = $notification['message'];
         $notification['message'] = mf_entry_changed_fields($text,$entry_id, $orig_entry, $updatedEntry, $form);        
         //error_log($notification['message'] );
         GFCommon::send_notification( $notification, $form, $updatedEntry );
      }
   }
}

// add gravity form edit capability to the subscriber role
function add_theme_caps() {
    // gets the author role
    $role = get_role( 'subscriber' );

    // This only works, because it accesses the class instance.
    // would allow the author to edit others' posts for current theme only
    $role->add_cap( 'gravityforms_edit_entries' ); 
}
add_action( 'admin_init', 'add_theme_caps');

/*
 * Add a single note
 */
function mf_add_note($leadid,$notetext){
	global $current_user;
	$user_data = get_userdata( $current_user->ID );
   RGFormsModel::add_note( $leadid, $current_user->ID, $user_data->display_name, $notetext );
}