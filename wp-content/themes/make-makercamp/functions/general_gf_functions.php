<?php
//=============================================
// Return field ID number based on the
// the Parameter Name for a specific form
//=============================================
function get_value_by_label($key, $form, $entry = array()) {
   $return = array();
   if(!isset($form['fields'])){
       error_log('error in get_value_by_label in wp-content/themes/make-campus/functions/general_gf_functions.php');
       error_log('$key='.$key);
       error_log('Entry');
       error_log(print_r($entry,true));
       error_log('Form');
       error_log(print_r($form,true));
   }
   foreach ($form['fields'] as &$field) {
      $lead_key = $field['inputName'];
      if ($lead_key == $key) {
         //is this a checkbox field?
         if ($field['type'] == 'checkbox') {
            $retArray = array();

            foreach ($field['inputs'] as $input) {
               if (isset($entry[$input['id']]) && $entry[$input['id']] == $input['label']) {
                  $retArray[] = array('id' => $input['id'], 'value' => $input['label']);
               }
            }
            $return = $retArray;
         } else {
            $return['id'] = $field['id'];
            if (!empty($entry)) {
               $return['value'] = $entry[$field['id']];
            } else {
               $return['value'] = '';
            }
         }
         return $return;
      }
   }
   return '';
}


function mf_update_entry_field( $entry_id, $input_id, $value ) {
	global $wpdb;

	$entry = GFAPI::get_entry( $entry_id );
	if ( is_wp_error( $entry ) ) {
		return $entry;
	}

	$form = GFAPI::get_form( $entry['form_id'] );
	if ( ! $form ) {
		return false;
	}

	$field = GFFormsModel::get_field( $form, $input_id );

	$lead_detail_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}gf_entry_meta WHERE entry_id=%d AND  CAST(meta_key AS CHAR) ='%s' order by id DESC limit 1", $entry_id, $input_id ) );

	$result = true;
  $result = GFFormsModel::update_lead_field_value( $form, $entry, $field, $lead_detail_id, $input_id, $value );

	return $result;
}

//change save and continue retention from 30 days to 90 days
add_filter( 'gform_incomplete_submissions_expiration_days', 'gwp_days', 1, 10 );
function gwp_days( $expiration_days ) {
    // change this value
    $expiration_days = 90;
    return $expiration_days;
}
