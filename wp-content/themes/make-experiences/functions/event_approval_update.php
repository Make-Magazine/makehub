<?php

add_action('gravityview/approve_entries/updated', 'update_entry_status', 10, 3);

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

    $post_data = array(
        'ID' => $entry['post_id'],
        'post_status' => $post_status
    );
    wp_update_post($post_data);    
    
    	
    //check if facilitator exists
    $entry = gfapi::get_entry($entry_id);
    $current_user = get_user_by('id', $entry['created_by']);    
    $userEmail = (string) $current_user->user_email;
    $person = EEM_Person::instance()->get_one([['PER_email' => $userEmail]]);
    $personID = $person->ID();
    
    //find all fields set with a parameter name 
    $form = GFAPI::get_form($entry['form_id']);
    $parameter_array = find_field_by_parameter($form);
    $eventName = getFieldByParam('event-name', $parameter_array, $entry); //event-name
    $shortDescription = getFieldByParam('short-description', $parameter_array, $entry); //short_description
   
    // finally, let's create a corresponding buddyboss group for the event
    $groupArgs = array(
            'group_id'     => 0,
            'creator_id'   => $personID,
            'name'         => $eventName,
            'description'  => $shortDescription,
            'slug'         => str_replace(' ', '-', strtolower($eventName)),
            'status'       => 'private',
            'enable_forum' => 0,
            'date_created' => bp_core_current_time()
    );
    groups_create_group($groupArgs);
}
