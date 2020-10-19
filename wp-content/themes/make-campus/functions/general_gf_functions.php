<?php
//=============================================
// Return field ID number based on the
// the Parameter Name for a specific form
//=============================================
function get_value_by_label($key, $form, $entry = array()) {
   $return = array();
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
