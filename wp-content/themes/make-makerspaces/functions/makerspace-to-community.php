<?php

add_action('gform_entry_created', 'makerspace_to_community', 10, 2);
add_action('gform_after_update_entry', 'makerspace_to_community', 10, 2);

/*
 * Triggered when a new entry is created or a current one is updated for form 5 Makerspace Directory
 */

function makerspace_to_community($entry, $form) {
    $email = $entry[141];
    
    //If user already exists then assign ID and update the account.    
    $userdata = array('user_email'=>$email, 'user_url'=>$entry[2], 'display_name'=>$entry[1], 'user_login'=>$email);
    
    //Check whether the user already exist or not
    $user_details = get_user_by( 'email', $userdata['user_email'] );
    if ($user_details) {
        //TBD - update if email,  website, or name changes

        $userdata['ID'] = $user_details->data->ID;

        $user_id = wp_update_user($userdata);
        $custom_send_email_flag = 0;
    } else { //no, add them
        
        $user_id = wp_insert_user($userdata);
        $custom_send_email_flag = 1;
    }

    GLOBAL $wpdb;
    $gf_field_arr = array();
    //set $bpmeta array with submitted data
    $sql = "select * from wp_ms_bp_xref";
    $results = $wpdb->get_results($sql);
    foreach ($results as $row) {
        $gf_field_arr[$row->gf_field_id] = $row->bp_field_id;
    }
    //loop thru form object extract field from array
    foreach ($form['fields'] as $field) {
        $field_id = $field->id;
        //is this one of the fields we are looking for?
        if (isset($gf_field_arr[$field_id])) {
            //what type is this?
            if ($field['type'] == 'checkbox') {
                if (isset($field['inputs']) && !empty($field['inputs'])) {
                    $bp_field_ar = array();
                    
                    foreach ($field['inputs'] as $choice) {
                        if (isset($entry[$choice['id']])) {
                            $bp_field_arr[] = $entry[$choice['id']];
                        }
                    }

                    $bp_field_arr = array_filter($bp_field_arr, function( $item ) {
                        return !empty($item[0]);
                    });
                    $bpmeta[$gf_field_arr[$field_id]] = $bp_field_arr;
                }
            } else {
                if (isset($entry[$field_id])) {
                    $bpmeta[$gf_field_arr[$field_id]] = $entry[$field_id];
                }
            }
        }
    }    
    
    //Upload user avatar if image is set
    if ($bpmeta['avatar']) {
        $image_dir = AVATARS . '/' . $user_id;
        mkdir($image_dir, 0777);
        $current_time = time();
        $destination_bpfull = $image_dir . '/' . $current_time . '-bpfull.jpg';
        $destination_bpthumb = $image_dir . '/' . $current_time . '-bpthumb.jpg';

        if (array_key_exists('avatar', $usermeta)) {
            $usermeta['avatar'] = str_replace(' ', '%20', $bpmeta['avatar']);
            $bpfull = $bpthumb = wp_get_image_editor($bpmeta['avatar']);

            // Handle 404 avatar url
            if (!is_wp_error($bpfull)) {
                $bpfull->resize(150, 150, true);
                $bpfull->save($destination_bpfull);
                $bpthumb->resize(50, 50, true);
                $bpthumb->save($destination_bpthumb);
            }
        }
    }

    //xprofile field visibility
    //Get the BP extra fields id name name
    $bp_xprofile_fields = $bp_xprofile_fields_with_default_value = array();

    $bp_extra_fields = $wpdb->get_results('SELECT id, type, name FROM wp_bp_xprofile_fields');

    $bpxfwdv_sql = 'SELECT name
                    FROM wp_bp_xprofile_fields
                    WHERE type
                    IN ("checkbox", "multiselectbox", "selectbox", "radio")
                    AND parent_id=0';
    $bp_xprofile_fields_with_default_value = $wpdb->get_col($bpxfwdv_sql);

    // Get xprofile field visibility
    $xprofile_visibility_sql = 'SELECT object_id, meta_value FROM wp_bp_xprofile_meta WHERE meta_key = "default_visibility"';
    $bp_fields_visibility = $wpdb->get_results($xprofile_visibility_sql);
    $xprofile_fields_visibility = array(1 => 'public');

    foreach ((array) $bp_fields_visibility as $bp_field_visibility) {
        $xprofile_fields_visibility[$bp_field_visibility->object_id] = $bp_field_visibility->meta_value;
    }

    //Create an array of BP fields   
    foreach ($bp_extra_fields as $value) {
        $bp_xprofile_fields[$value->id] = $value->name;
        $bp_fields_type[$value->id] = $value->type;
    }
        
    //echo '$bp_fields_type';
    //var_dump($bp_fields_type);        
        
    // Insert xprofile field visibility state for user level.
    update_user_meta($user_id, 'bp_xprofile_visibility_levels', $xprofile_fields_visibility);

    if (isset($bpmeta)) {
        //Added an entry in user_meta table for current user meta key is last_activity
        //TBD - figure out how to do this 
        bp_update_user_last_activity($user_id, date('Y-m-d H:i:s'));
         
        foreach ($bpmeta as $bpmetakeyid => $bpmetavalue) {
            $current_field_type = $bp_fields_type[$bpmetakeyid];
            if ('image' === $current_field_type || $current_field_type == 'file') {
                $sql = 'SELECT id FROM wp_bp_xprofile_data WHERE field_id = ' . $bpmetakeyid . ' AND user_id = ' . $user_id;
                $result = $wpdb->get_var($sql);
                $date = date('Y-m-d G:i:s');
                if ('' == $result) {
                    $sql = "insert into wp_bp_xprofile_data (`field_id`,`user_id`,`value`, `last_updated`) VALUES($bpmetakeyid, $user_id, '$bpmetavalue', '$date')";
                } else {
                    $sql = 'UPDATE wp_bp_xprofile_data SET value = "' . $bpmetavalue . '", last_updated = "' . $date . '" WHERE id = ' . $result . ' AND field_id = ' . $bpmetakeyid . ' AND user_id = ' . $user_id;
                }
                $wpdb->query($sql);
            } else {
                xprofile_set_field_data($bpmetakeyid, $user_id, $bpmetavalue);
            }
        }
    }

    // If no error, let's update the user meta too!
    
    if ($usermeta) {
        if (array_key_exists('bp_member_types', $usermeta)) {
            $bp_member_types = $usermeta['bp_member_types'];
            unset($usermeta['bp_member_types']);
            $bp_member_types_arr = explode('::', $bp_member_types);
            foreach ($bp_member_types_arr as $type) {
                // Set the member type of user $user_id to $type.
                $member_type = bp_set_member_type($user_id, $type);
            }
        }

        foreach ($usermeta as $metakey => $metavalue) {
            $metavalue = maybe_unserialize($metavalue);
            update_user_meta($user_id, $metakey, $metavalue);
        }
    }
}
