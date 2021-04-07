<?php

//This filter declaration targets form 10 - field 9 - the last digit is the column in the list field
add_filter('gform_column_input_content_10_9_1', 'set_date_field_type', 10, 6);
add_filter('gform_column_input_content_10_9_2', 'set_time_field_type', 10, 6);
add_filter('gform_column_input_content_10_9_3', 'set_time_field_type', 10, 6);

//This filter declaration targets form 10 - field 11 - the last digit is the column in the list field
add_filter('gform_column_input_content_10_11_1', 'set_date_field_type', 10, 6);
add_filter('gform_column_input_content_10_11_2', 'set_time_field_type', 10, 6);
add_filter('gform_column_input_content_10_11_3', 'set_time_field_type', 10, 6);

//reformat field as date type
function set_date_field_type($input, $input_info, $field, $text, $value, $form_id) {
    //build field name, must match List field syntax to be processed correctly
    $input_field_name = 'input_' . $field->id . '[]';
    $input_field_id = 'input_' . $form_id . '_' . $field->id;
    $tabindex = GFCommon::get_tabindex();

    $new_input = '<input name="' . $input_field_name . '" id="' . $input_field_name . '" ' . $tabindex . ' type="date" placeholder="mm-dd-yyyy" onKeyDown="numbersAndDashes()" value="' . $value . '" class="datepicker medium mdy datepicker_no_icon hasDatepicker" aria-describedby="input_10_12_date_format">' .
            ' <span id="' . $input_field_id . '_date_format" class="screen-reader-text">Date Format: MM slash DD slash YYYY</span>';
    return $new_input;
}

//reformat field as time type
function set_time_field_type($input, $input_info, $field, $text, $value, $form_id) {
    $tabindex = GFCommon::get_tabindex();
    $input_field_name = 'input_' . $field->id . '[]';
    $input_field_id = $field->id . "_" . str_replace(" ", "_", strtolower($text));
	
    $new_input = '<input type="text" name="' . $input_field_name . '" value="' . $value . '" ' . $tabindex . ' class="time ui-timepicker-input">';

    return $new_input;
}

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

/* this field will prepoulate gravity form fields based on the set parameter name */
add_filter('gform_field_value', 'set_field_values', 10, 3);

function set_field_values($value, $field, $name) {
    $values = array();
    if (!empty($name)) {
        //check if facilitator exists
        global $current_user;
        $current_user = wp_get_current_user();
        $userEmail = (string) $current_user->user_email;

        $person = EEM_Person::instance()->get_one([['PER_email' => $userEmail]]);
        if ($person) {
            $post_id = $person->ID();
            $user_website = get_field("website", $post_id);
            $user_social = get_field("social_links", $post_id);

            $values = array(
                'user-fname' => $person->fname(),
                'user-lname' => $person->lname(),
                'user_website' => $user_website,
                'user_social' => $user_social,
                'user-bio' => $person->get('PER_bio'),
            );
        }
    }

    return isset($values[$name]) ? $values[$name] : $value;
}

add_filter('gform_pre_render_7', 'GF_prepopulate_profile_photo');

//TBD this code does not work as defaultValue is not a valid field for photo
function GF_prepopulate_profile_photo($form) {
    //check if facilitator exists
    global $current_user;
    $current_user = wp_get_current_user();
    $userEmail = (string) $current_user->user_email;

    $person = EEM_Person::instance()->get_one([['PER_email' => $userEmail]]);
    if ($person) {
        $person_id = $person->ID();

        //populate the image field
        foreach ($form["fields"] as &$field) {
            if ($field["id"] == 118) {
                $field["defaultValue"] = get_the_post_thumbnail_url($person_id);
            }
        }
    }
    return $form;
}

//update the person record
function updatePerson($parameter_array, $entry, $person) {
    $userBio = getFieldByParam('user-bio', $parameter_array, $entry);
    $userFname = getFieldByParam('user-fname', $parameter_array, $entry);
    $userLname = getFieldByParam('user-lname', $parameter_array, $entry);
    $userFullName = $userFname . ' ' . $userLname;
    $currBio = $person->get('PER_bio');

    if ($userFname != $person->fname())
        $person->set_fname($userFname);

    if ($userLname != $person->lname())
        $person->set_lname($userLname);

    if ($userBio != $currBio)
        $person->set('PER_bio', $userBio);

    if ($userFullName != $person->get('PER_full_name'))
        $person->set('PER_full_name', $userFname . ' ' . $userLname);
    $person->save();
}
