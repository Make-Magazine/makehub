<?php
function find_field_by_parameter($form) {
    $parameter_array = array(); //array of field id's and their associated parameter name
    if (isset($form['fields'])) {
        foreach ($form['fields'] as $field) {
            //paramater names are stored in a different place
            if ($field['type'] == 'name' || $field['type'] == 'address') {
                foreach ($field['inputs'] as $choice) {
                    if ($choice['name'] != '')
                        $parameter_array[$choice['name']] = $field;
                }
            }
            if ($field['allowsPrepopulate'] && $field['inputName'] != '') {
                $parameter_array[$field['inputName']] = $field;
            }
        }
    }
    return $parameter_array;
}

function getFieldByParam($paramName = '', $parameterArray = array(), $entry = array()) {
    if (isset($parameterArray[$paramName])) {
        $field = $parameterArray[$paramName];
        if (isset($field)) {
            $fieldID = $field->id;
            if ($field->type == 'name' || $field->type == 'address') {
                foreach ($field->inputs as $choice) {
                    if ($choice['name'] == $paramName) {
                        $fieldID = $choice['id'];
                    }
                }
            } elseif ($field->type == 'checkbox') {
                $fieldID = $field->id;
                $cbArray = array();
                foreach ($entry as $key => $value) {
                    if (strpos($key, $fieldID . ".") === 0) {
                        $cbArray[] = $value;
                    }
                }
                return $cbArray;
            }
            return (isset($entry[$fieldID]) ? $entry[$fieldID] : '');
        }
    }
    return '';
}

//=============================================
// Return field ID number based on the
// the Parameter Name for a specific form
//=============================================
function get_value_by_label($key, $form, $entry = array()) {
   $return = array();
   if(!isset($form['fields'])){
       error_log('error in get_value_by_label in wp-content/themes/make-community/functions/general_gf_functions.php');
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
function spinner_url($image_src, $form) {
    return "/wp-content/universal-assets/v2/images/makey-spinner.gif";
}
add_filter('gform_ajax_spinner_url', 'spinner_url', 10, 2);

//change save and continue retention from 30 days to 90 days
add_filter( 'gform_incomplete_submissions_expiration_days', 'gwp_days', 1, 10 );
function gwp_days( $expiration_days ) {
    // change this value
    $expiration_days = 90;
    return $expiration_days;
}