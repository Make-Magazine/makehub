<?php

/**
 * Add "faire_list" Field
 */
function make_add_faire_list_field($fields) {

    $new_fields = array(
        'faire_list' => 'MAKE_XProfile_Field_Type_FaireList',
    );

    // Get Fields.
    $fields = array_merge($fields, $new_fields);

    return $fields;
}

add_filter('bp_xprofile_get_field_types', 'make_add_faire_list_field');