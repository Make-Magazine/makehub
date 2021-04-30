<?php

add_action('gravityview/approve_entries/updated', 'update_entry_status', 1, 3);

function update_entry_status($entry_id, $status) {
    //$status - 1 for approved, 2 for rejected, 3 for pending
    switch ($status) {
        case '1':
            $post_status = 'publish';
            break;
        case '2':
            $post_status = 'trash';
            break;
        default:
            $post_status = 'pending';
    }
    $entry = GFAPI::get_entry($entry_id);
    if(isset($entry['post_id']) && $entry['post_id']!=''){
        $post_data = array(
            'ID' => $entry['post_id'],
            'post_status' => $post_status
        );
        wp_update_post($post_data);
    }
    
    //if the event is approved, create an event
    if ($status == 1) {
        //find all fields set with a parameter name 
        $entry = GFAPI::get_entry($entry_id);
        $form = GFAPI::get_form($entry['form_id']);
        $parameter_array = find_field_by_parameter($form);
        $shortDescription = getFieldByParam('short-description', $parameter_array, $entry); //short_description
        $eventName = getFieldByParam('event-name', $parameter_array, $entry); //event-name
        $event_id = $entry["post_id"];

        //get start date of the event
        $event = EEM_Event::instance()->get_one_by_ID($event_id);
        if ($event) {
            $date = $event->first_datetime();
            $startDt = date("M Y", strtotime($date->start_date()));
        } else {
            $startDt = '';
        }

        //add Month and year to end of group name ie. How to Make Your Dragon - May 2021
        $groupName = $eventName . ' - ' . $startDt;

        /*
         * link to event listing at least (so registrants can help promote)
         * Basic event information - dates, times
         */
        $materials = getFieldByParam('materials', $parameter_array, $entry); //materials

        $webinar_link = getFieldByParam('webinar_link', $parameter_array, $entry);
        if ($webinar_link == '')
            $webinar_link = 'Coming Soon';

        $schedule = '<ul>';
        $dates = $event->datetimes_in_chronological_order();
        foreach ($dates as $date) {
            $schedule .= '<li>' . $date->start_date() . ' ' . $date->start_time() . ' - ' . $date->end_time() . ' <span class="small">(Pacific)</span></li>';
        }
        $schedule .= '</ul>';

        //please keep this with the physical line breaks. for some reason html gets stripped from parts of this.
        //use wpautop
        $description = '<span><a href="' . $event->get_permalink() . '">' . $eventName . '</a></span>
                         <p>Webinar Link - ' . $webinar_link . '</p>' .
                $schedule .
                $materials;

        // finally, let's create a corresponding buddyboss group for the event
        $groupArgs = array(
            'creator_id' => $entry['created_by'],
            'name' => $groupName,
            'description' => $description,
            'slug' => str_replace(' ', '-', strtolower($groupName)),
            'status' => 'hidden',
            'enable_forum' => 0,
            'date_created' => bp_core_current_time()
        );
        $group_id = groups_create_group($groupArgs);

        //set the group image
        $file = get_the_post_thumbnail_url($event_id, 'full');
        $pathinfo = pathinfo($file);

        $uploads = wp_get_upload_dir();
        $uploadDir = $uploads['basedir'];
        //error_log('$group_id=' . $group_id);

        //make the necessary directories to place the images in        
        wp_mkdir_p($uploadDir . '/group-avatars/' . $group_id . '/');
        wp_mkdir_p($uploadDir . '/buddypress/groups/' . $group_id . '/cover-image/' . $group_id);

        $avatar = $uploadDir . '/group-avatars/' . $group_id . '/' . $pathinfo['filename'] . '-bpfull.' . $pathinfo['extension'];
        $bpthumb = $uploadDir . '/group-avatars/' . $group_id . '/' . $pathinfo['filename'] . '-bpthumb.' . $pathinfo['extension'];
        $bpcover = $uploadDir . '/buddypress/groups/' . $group_id . '/cover-image/' . $pathinfo['filename'] . '-bp-cover-image.' . $pathinfo['extension'];

        //get absolute path of featured image               
        $file_path = str_replace($uploads['baseurl'], $uploads['basedir'], $file);

        if (!copy($file_path, $avatar)) {
            error_log('error creating avatar for group ' . $group_id);
        }

        if (!copy($file_path, $bpthumb)) {
            error_log('error creating bpthumb for group ' . $group_id);
        }

        if (!copy($file_path, $bpcover)) {
            error_log('error creating bpcover for group ' . $group_id);
        }

        //write the new group id to event acf
        update_field('group_id', $group_id, $event_id);
        
        $userID = $entry['created_by'];
        // now, give the user a basic membership level, if they don't have one already
        $user_meta = get_user_meta($userID);

        // assign community membership
        $result = ihc_do_complete_level_assign_from_ap($userID, 14, 0, 0);          
    }
}
