<?php

//This filter declaration targets form 4 - field 9 - the last digit is the column in the list field
add_filter('gform_column_input_4_9_1', 'set_month_select', 10, 6);
add_filter('gform_column_input_4_9_2', 'set_day_select', 10, 6);
add_filter('gform_column_input_content_4_9_3', 'set_time_field_type', 10, 6);
add_filter('gform_column_input_content_4_9_4', 'set_time_field_type', 10, 6);

//This filter declaration targets form 4 - field 11 - the last digit is the column in the list field
add_filter('gform_column_input_4_11_1', 'set_month_select', 10, 6);
add_filter('gform_column_input_4_11_2', 'set_day_select', 10, 6);
add_filter('gform_column_input_content_4_11_3', 'set_time_field_type', 10, 6);
add_filter('gform_column_input_content_4_11_4', 'set_time_field_type', 10, 6);

add_filter( 'gform_field_validation_4_9', 'validate_time', 10, 4 );
add_filter( 'gform_field_validation_4_11', 'validate_time', 10, 4 );

//reformat field as month selector
function set_month_select( $input_info, $field, $column, $value, $form_id ) {
    return array(
		'type' => 'select',
		  'choices' => array(
			  array( 'text' => 'January', 'value' => 1),
			  array( 'text' => 'February', 'value' => 2),
			  array( 'text' => 'March', 'value' => 3),
			  array( 'text' => 'April', 'value' => 4),
			  array( 'text' => 'May', 'value' => 5),
			  array( 'text' => 'June', 'value' => 6),
			  array( 'text' => 'July', 'value' => 7),
			  array( 'text' => 'August', 'value' => 8),
			  array( 'text' => 'September', 'value' => 9),
			  array( 'text' => 'October', 'value' => 10),
			  array( 'text' => 'November', 'value' => 11),
			  array( 'text' => 'December', 'value' => 12),
		  ),
	  );
}

//reformat field as day selector
function set_day_select( $input_info, $field, $column, $value, $form_id ) {
    return array(
		'type' => 'select',
		'choices' => array(
			array( 'text' => 'Saturday', 'value' => 'Saturday'),
			array( 'text' => 'Thursday', 'value' => 'Thursday'),
		),
	);
}


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

	if(!$value) { $value = "12 : 00 PM"; } // if we aren't seeing a set value, set it noon as default

    $new_input = '<input type="text" name="' . $input_field_name . '" value="'.$value.'" ' . $tabindex . ' class="time timepicker">';

    return $new_input;
}


// make sure the start time is before the end time
function validate_time($result, $value, $form, $field) {
	// loop through all instances of the field
	foreach ($value as $row => $array) {
		$startTime = strtotime(str_replace(' ', '', $array['Start Time']));
		$endTime   = strtotime(str_replace(' ', '', $array['End Time']));
		if ( $startTime > $endTime  ) {
			$result['is_valid'] = false;
			$result['message']  = 'End Time must be greater than start time';
		}
	}
	return $result;
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
    if(is_admin()){
        return $value;
    }
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

/* If the person filling out this form is an existing facilitator, populate the preview image */
add_filter( 'gform_field_content_1_118', 'set_facilitator_img', 10, 5 );
function set_facilitator_img($input, $field, $value, $lead_id, $form_id){
    if(is_admin()){
        return $input;
    }
    //check if facilitator exists
    global $current_user;
    $facilitator_img ='';

    $current_user = wp_get_current_user();
    $userEmail = (string) $current_user->user_email;
    $form = gfapi::get_form($form_id);
    $person = EEM_Person::instance()->get_one([['PER_email' => $userEmail]]);
    if ($person) {
        $person_id = $person->ID();

        //populate the image field
        foreach ($form["fields"] as &$field) {
            if ($field["id"] == 118) {
                $facilitator_img = get_the_post_thumbnail_url($person_id);
            }
        }
    }

    $input .= '<div id="preview_input_1_118"> '
            . '  This is the current image we have for you. If you would like to update it, please click \'Choose File\'<br/>'
            .   '<div class="preview_img-wrapper" style="background-image: url('.$facilitator_img.');"></div>'
           . '</div>';
    return $input;
}

add_filter( 'gform_pre_render', 'gw_conditional_requirement' );
add_filter( 'gform_pre_validation', 'gw_conditional_requirement' );
/* If the person filling out this form is an existing facilitator, populate the preview image */
function gw_conditional_requirement( $form ) {
    //Form 1 only
    if ( $form['id'] != 1 ) {
       return $form;
    }

    global $current_user;
    $current_user = wp_get_current_user();
    $userEmail = (string) $current_user->user_email;

    foreach ( $form['fields'] as &$field ) {
        if ( $field->id == 118 ) {
            $field->isRequired = false;
        }
    }
    return $form;
}

//allow event preview if you are the facilitator, whatever role you might have (e.g. subscriber )
function facilitator_preview_post( $posts ) {
	if( isset($_GET['post_type']) && isset($_GET['p']) ) {
		if($_GET['post_type'] == "espresso_events" && !empty($posts)){
			$current_user_id = get_current_user_id();
			$author_id= $posts[0]->post_author;
			if($current_user_id == $author_id)
			    $posts[0]->post_status = 'publish';
		}
	}
    return $posts;
}
add_filter( 'posts_results', 'facilitator_preview_post', 10, 2 );
