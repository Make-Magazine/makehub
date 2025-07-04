<?php
/* This function checks if the entry-id set on the form is valid
 * If it is, then it compares the entered email to see if it matches the previous
 * one used on the entry. if it all passes, then they can move to the next step
 * otherwise it returns errors
 */

add_filter('gform_validation', 'gf_custom_validation');

function gf_custom_validation($validation_result) {
    $form = $validation_result['form'];

    // determine if entry-id and contact-email id's are in the submitted form
    // and what their field id's are
    $entryID = get_value_by_label('entry-id', $form);
    $contact_email = get_value_by_label('contact-email', $form);

    //make sure we are in the right form
    if (!empty($entryID) && !empty($contact_email)) {
        $entryid = rgpost('input_' . $entryID['id']);
        $sub_email = rgpost('input_' . $contact_email['id']);

        //check if entry-id is valid
        $entry = GFAPI::get_entry($entryid);

        if (is_array($entry) && $entry['status'] == 'active') {
          $field = get_value_by_label('contact-email', $form, $entry);
          $contactEmail = $field['value'];
          //finding Field with ID of 1 and marking it as failed validation
          foreach ($form['fields'] as &$field) {
              if ($field->id == $contact_email['id']) {     //contact_email
                  //pull contact email from original entry
                  //$contactEmail = (isset($entry['132']) ? $entry['132'] : '');
                  if (trim(strtolower($sub_email)) != trim(strtolower($contactEmail))) {
                      // set the form validation to false
                      $validation_result['is_valid'] = false;
                      $field->failed_validation = true;
                      $field->validation_message = 'Email does not match email on the event';
                  }
              }
          }
        } else {
            // set the form validation to false
            $validation_result['is_valid'] = false;
            //finding Field with ID of 1 and marking it as failed validation
            foreach ($form['fields'] as &$field) {
                if ($field->id == $entryID['id']) {
                    // set the form validation to false
                    $validation_result['is_valid'] = false;
                    $field->failed_validation = true;
                    $field->validation_message = 'Invalid Project ID';
                    break;
                }
            }
        }
    }
    //Assign modified $form object back to the validation result
    $validation_result['form'] = $form;
    return $validation_result;
}

add_filter( 'gform_pre_render', 'make_populate_fields',99 ); //all forms

/*
 * this logic is for all subsequent pages of 'linked forms'
 * It will take the entry id that was submitted on page one and use that
 *    to pull in various data from the original form submission
 */

function make_populate_fields($form) {
    if (!class_exists('GFFormDisplay')) {
        return $form;
    }

    $jqueryVal = '';
    //this is a 2-page form with the data from page one being displayed in an html field on following pages
    $current_page = GFFormDisplay::get_current_page($form['id']);

    if ($current_page > 1) {
        //find the submitted entry id
        $return = get_value_by_label('entry-id', $form, array());
        $entry_id = rgpost('input_' . $return['id']);

        //is entry id set?
        if ($entry_id != '') {
            //pull the original entry
            $entry = GFAPI::get_entry($entry_id); //original entry ID
            $form_id = $form['id']; //current form

            //find the submitted original entry id
            foreach ($form['fields'] as &$field) {
                $parmName = '';
                $value = '';
                switch ($field->type) {
                    //parameter name is stored in a different place
                    case 'name':
                    case 'address':
                        foreach ($field->inputs as $key => $input) {
                            if ($input['name'] != '') {
                                $parmName = $input['name'];
                                $pos = strpos($parmName, 'field-');
                                if ($pos !== false) { //populate by field ID?
                                    $field_id = str_replace("field-", "", $input['name']);
                                    $field->inputs[$key]['defaultValue'] = $entry[$field_id];
                                    $jqueryVal .= "jQuery('#input_" . $form_id . "_" . str_replace('.', '_', $field_id) . "').val('" . $entry[$field_id] . "');";
                                }
                            }
                        }
                        break;
                }

                if (isset($field->inputName) && $field->inputName != '') {
                    $parmName = $field->inputName;

                    //check for 'field-' to see if the value should be populated by original entry field data
                    $pos = strpos($parmName, 'field-');

                    //populate field using field id's from original form
                    if ($pos !== false) { //populate by field ID?
                        //strip the 'field-' from the parameter name to get the field number
                        $field_id = str_replace("field-", "", $parmName);
                        $fieldType = $field->type;
                        switch ($fieldType) {
                            case 'name':
                                foreach ($field->inputs as &$input) {  //loop thru name inputs
                                    if (isset($input['name']) && $input['name'] != '') {  //check if parameter name is set
                                        $pos = strpos($input['name'], 'field-');
                                        if ($pos !== false) { //is it requesting to be set by field id?
                                            //strip the 'field-' from the parameter name to get the field number
                                            $field_id = str_replace("field-", "", $input['name']);
                                            $input['content'] = (isset($entry[$field_id]) ? $entry[$field_id] : '');
                                            ;
                                        }
                                    }
                                }
                                break;
                            case 'checkbox':
                                //find which fields are set
                                foreach ($field->inputs as $key => $input) {
                                    //need to get the decimal indicator from the input in order to set the field id
                                    if (($pos = strpos($input['id'], ".")) !== FALSE) {
                                        $decPos = substr($input['id'], $pos + 1);
                                    }
                                    $fieldNum = $field_id . '.' . $decPos;
                                    //check if field is set in the entry
                                    if (!empty($entry[$fieldNum])) {
                                        if ($field->choices[$key]['value'] == $entry[$fieldNum]) {
                                            $field->choices[$key]['isSelected'] = true;
                                            $jqueryVal .= "jQuery( '#choice_" . $form_id . "_" . str_replace('.', '_', $input['id']) . "' ).prop( 'checked', true );";
                                        }
                                    }
                                }

                                break;
                            default:
                                $field->defaultValue = (isset($entry[$field_id]) ? $entry[$field_id] : "");
                                break;
                        }
                    } else { //populate by specific parameter name
                        //populate fields
                        $fieldIDarr = array(
                            'project-name' => 116,
                            'entry-id' => $entry_id);

                        //find the project name for submitted entry-id
                        if (isset($fieldIDarr[$parmName])) {
                            if ($parmName == 'plans-type') {
                                $planstypevalues = array();
                                for ($i = 1; $i <= 6; $i++) {
                                    if (isset($entry['55.' . $i]) && !empty($entry['55.' . $i])) {
                                        $planstypevalues[] = $entry['55.' . $i];
                                    }
                                }
                                $value = implode(',', $planstypevalues);
                            } elseif ($parmName == 'entry-id') {
                                $value = $entry_id;
                            } else {
                                if(isset($fieldIDarr[$parmName]))
                                    $value = $entry[$fieldIDarr[$parmName]];
                            }
                        }

                        $field->defaultValue = $value;
                    }
                }
            }
        }
    } //end check current page

    if ($jqueryVal != '') {
        ?>
        <script>
            jQuery(document).ready(function () {
        <?php echo $jqueryVal; ?>
            });
        </script>
        <?php
    }

    return $form;
}

/* Used to update linked fields back to original entry */
function updLinked_fields($form, $origEntryID) {
    //Loop thru form fields and look for parameter names of 'field-*'
    //  These are set to update original entry fields
    foreach ($form['fields'] as $field) {
        //find parameter name
        $parmName = '';
        switch ($field->type) {
            //parameter name is stored in a different place
            case 'name':
            case 'address':
                foreach ($field->inputs as $key => $input) {
                    if ($input['name'] != '') {
                        $parmName = $input['name'];
                        $pos = strpos($parmName, 'field-');
                        if ($pos !== false) { //populate by field ID?
                            $field_id = str_replace("field-", "", $input['name']);
                        }
                    }
                }
                break;
        }

        if ($parmName == '' && $field->inputName != '') {
            $parmName = $field->inputName;
        }

        if ($parmName != '') {
            /* Now that we have the parameter name, check if it contains 'field-' */
            $pos = strpos($parmName, 'field-');

            if ($pos !== false) {
                //find the field ID passed to update the linked entry
                $updField = str_replace("field-", "", $parmName);  //strip the 'field-' from the parameter name to get the field number
                //  Do not update values from read only fields
                if (!$field->gwreadonly_enable) {
                    //multiple field options to update
                    if ($field->type == 'checkbox') {
                        foreach ($field->inputs as $input) {
                            $updField = $input['id'];
                            $inputID = str_replace(".", "_", $updField);
                            /*
                             * if the field is set, update with submitted  value
                             *  else, update with blanks
                             */
                            $updValue = (isset($_POST['input_' . $inputID]) ? $_POST['input_' . $inputID] : '');
                            mf_update_entry_field($origEntryID, $updField, stripslashes($updValue));
                        }
                    } else {
                        //find submitted value
                        $updValue = (isset($_POST['input_' . $field['id']]) ? $_POST['input_' . $field['id']] : '');
                        mf_update_entry_field($origEntryID, $updField, stripslashes($updValue));

                        //update the message to attendees in event (need to find a more generic way of doing this
                        if($updField==147){
                            $entry = GFAPI::get_entry($origEntryID);
                            $event_id = $entry["post_id"];
                            update_field('message_to_attendees', $updValue, $event_id);
                        }
                    }
                }
            }
        }
    } //end foreach loop
}

//when a linked form is submitted, find the initial formid based on entry id
// and add the fields from the linked form to that original entry
add_action('gform_after_update_entry', 'update_linked_data_pre', 10, 3);  // $form,$entry_id,$orig_entry=array()

function update_linked_data_pre($form, $entry_id, $orig_entry = array()) {
    $entry = GFAPI::get_entry(esc_attr($entry_id));
    update_linked_data($entry, $form);
}

//add_action('gform_after_submission', 'update_linked_data', 10, 2); //$entry, $form
function update_linked_data($entry, $form) {
    // update meta
    $updateEntryID = get_value_by_label('entry-id', $form, $entry);
    if (isset($updateEntryID['value'])) {
        gform_update_meta($entry['id'], 'entry_id', $updateEntryID['value']);
        updLinked_fields($form, $updateEntryID['value']);
    }
}
