<?php
//This filter declaration targets form 10 - field 9 - the last digit is the column in the list field
add_filter( 'gform_column_input_content_10_9_1', 'set_date_field_type', 10, 6 );
add_filter( 'gform_column_input_content_10_9_2', 'set_time_field_type', 10, 6 );
add_filter( 'gform_column_input_content_10_9_3', 'set_time_field_type', 10, 6 );

//This filter declaration targets form 10 - field 11 - the last digit is the column in the list field
add_filter( 'gform_column_input_content_10_11_1', 'set_date_field_type', 10, 6 );
add_filter( 'gform_column_input_content_10_11_2', 'set_time_field_type', 10, 6 );
add_filter( 'gform_column_input_content_10_11_3', 'set_time_field_type', 10, 6 );

//reformat field as date type
function set_date_field_type( $input, $input_info, $field, $text, $value, $form_id ) {
    $form = GFAPI::get_form($form_id);
    var_dump($form);
    //build field name, must match List field syntax to be processed correctly
    $input_field_name = 'input_' . $field->id . '[]';
    $input_field_id = 'input_'.$form_id.'_'.$field->id;
    $tabindex = GFCommon::get_tabindex();
    
    $new_input = '<input name="' . $input_field_name . '" '.$tabindex.' id="'.$input_field_id.'" type="text" value="'.$value.'" class="datepicker medium mdy datepicker_no_icon hasDatepicker" tabindex="100002" aria-describedby="input_10_12_date_format">'.
                 ' <span id="'.$input_field_id.'_date_format" class="screen-reader-text">Date Format: MM slash DD slash YYYY</span>';    
    return $new_input;    
}

//reformat field as time type
function set_time_field_type( $input, $input_info, $field, $text, $value, $form_id ) {    
    $tabindex = GFCommon::get_tabindex();
    $input_field_name = 'input_' . $field->id . '[]';
    $input_field_id = 'input_'.$form_id.'_'.$field->id;
    
    $new_input = '<input type="time" name="'.$input_field_name.'" value="'.$value.'" '.$tabindex.' step="900" >';    //15 minute increments
                        
    return $new_input;    
}

