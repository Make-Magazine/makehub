<?php
add_action('gform_entry_created', 'makerspace_to_community', 10, 2);
add_action('gform_after_update_entry', 'pre_makerspace_to_community', 10, 2);

function pre_makerspace_to_community($form, $entry_id) {
    $entry = GFAPI::get_entry($entry_id);
    makerspace_to_community($entry, $form);
}

/*
 * Triggered when a new entry is created or a current one is updated for form 5 Makerspace Directory
 */

function makerspace_to_community($entry, $form) {    
    $email = $entry[141];
    if ($email == '')
        return false;

        
    //Check whether the user already exist or not
    $user_details = get_user_by("email", trim($email));

    if ($user_details) { //does user exist?
        //If user already exists then assign ID and update the website and display name            
        $userdata = array('user_url' => $entry[2], 'display_name' => $entry[1]);

        $userdata['ID'] = $user_details->data->ID;
        $user_id = $user_details->data->ID;
        wp_update_user($userdata);
    } else { //no, add them      
        //come up with unique user_login, user_nicename
        $username = findUniqueUname($entry[1]); //pass makerspacename
        $userdata = array('user_email' => $email,
            'user_url' => $entry[2],
            'display_name' => $entry[1],
            'user_nicename' => $username,
            'user_login' => $username);
        $user_id = wp_insert_user($userdata);
        wp_update_user(array('ID' => $user_id, 'role' => 'subscriber'));
        add_user_to_blog(1, $user_id, 'subscriber'); //add user to main blog           
    }

    //set member type to makerspace
    switch_to_blog(1);
        $blog_id = get_current_blog_id();
        $blog_url = get_site_url( $blog_id );
        basicCurl( $blog_url . '?auth=m50667&wpid=' . get_current_user_id() );
    restore_current_blog();

    GLOBAL $wpdb;
    $gf_field_arr = array();
    //set $bpmeta array with submitted data
    $bpmeta = array();
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
                    $bp_field_arr = array();

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

        define('AVATARS', ABSPATH . 'wp-content/uploads/avatars');
        if (!file_exists(AVATARS))
            mkdir(AVATARS, 0777);
        $image_dir = AVATARS . '/' . $user_id;
        if (!file_exists($image_dir))
            mkdir($image_dir, 0777);
        $current_time = time();
        $destination_bpfull = $image_dir . '/' . $current_time . '-bpfull.jpg';
        $destination_bpthumb = $image_dir . '/' . $current_time . '-bpthumb.jpg';

        $bpmeta['avatar'] = str_replace(' ', '%20', $bpmeta['avatar']);
        $bpfull = $bpthumb = wp_get_image_editor($bpmeta['avatar']);

        // Handle 404 avatar url
        if (!is_wp_error($bpfull)) {
            $bpfull->resize(150, 150, true);
            $bpfull->save($destination_bpfull);
            $bpthumb->resize(50, 50, true);
            $bpthumb->save($destination_bpthumb);
            // And make sure it updates on the bp side
            update_user_meta($user_id, 'author_avatar', $destination_bpfull);
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

    // Insert xprofile field visibility state for user level.
    update_user_meta($user_id, 'bp_xprofile_visibility_levels', $xprofile_fields_visibility);

    /*if (isset($bpmeta)) {
        //Added an entry in user_meta table for current user meta key is last_activity
        bp_update_user_last_activity($user_id, date('Y-m-d H:i:s'));

        foreach ($bpmeta as $bpmetakeyid => $bpmetavalue) {
            $current_field_type = (isset($bp_fields_type[$bpmetakeyid]) ? $bp_fields_type[$bpmetakeyid] : '');
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
                // Is the answer to the makerspace question, 'No'? None
                if ($bpmetavalue == 'No' || $bpmetavalue == 'None') {
                    //set the value to blank as blank answers don't show in BP                    
                    $bpmetavalue = '';
                } //no, continue and update the value

                xprofile_set_field_data($bpmetakeyid, $user_id, $bpmetavalue);
            }
        }
    }*/
}

function findUniqueUname($input = '') {
    //remove special chatacters and spaces
    $input = preg_replace("/[^a-zA-Z0-9]+/", "", $input);
    if (username_exists($input)) { //if the passed username is available, return it
        return $input;
    } else {
        $user_exists = 1;
        do {
            $rnd_str = sprintf("%0d", mt_rand(1, 999999));
            $user_exists = username_exists($input . $rnd_str);
        } while ($user_exists > 0);
        return $input . $rnd_str;
    }
}
