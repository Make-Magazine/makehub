<?php

//We do not want gravity forms creating the post, we will be doing that ourselves
add_filter('gform_disable_post_creation_7', 'disable_post_creation', 10, 3);

function disable_post_creation($is_disabled, $form, $entry) {
    return true;
}

// Create event with ticket
add_action('gform_after_submission_7', 'create_event', 10, 2);

function create_event($entry, $form) {
    //get current user info     
    //global $current_user;
    $current_user = get_user_by('id', $entry['created_by']);
    $userID = $entry['created_by'];
    $userEmail = (string) $current_user->user_email;

    //find all fields set with a parameter name 
    $parameter_array = find_field_by_parameter($form);

    //first create the event
    $eventName = getFieldByParam('event-name', $parameter_array, $entry); //event-name
    $longDescription = getFieldByParam('long-description', $parameter_array, $entry); //long-description
    $shortDescription = getFieldByParam('short-description', $parameter_array, $entry); //short_description

    $currDateTime = date(DATE_ATOM, mktime(0, 0, 0, 7, 1, 2000));
    $event = EE_Event::new_instance(
                    array('EVT_name' => $eventName,
                        'EVT_desc' => $longDescription,
                        'EVT_short_desc' => $shortDescription,
                        'EVT_wp_user' => $userID,
                        'status' => "pending",
                        'EVT_visible_on' => $currDateTime,
                        'EVT_slug' => slugify($eventName)
    ));
    $event->save();
    $eventID = $event->ID();

    //set the post id
    global $wpdb;
    $wpdb->update($wpdb->prefix . 'gf_entry', array('post_id' => $eventID), array('id' => $entry['id']));
    $entry['post_id'] = $eventID;    
    
    // assign basic questions to primary registrant    
    $eQgroup = EE_Event_Question_Group::new_instance(
                    array('EVT_ID' => $eventID,
                        'QSG_ID' => 3,
                        'EQG_primary' => 1
    ));
    $eQgroup->save();    
    $event->_add_relation_to($eQgroup, 'Event_Question_Group'); //link the question group
    
    // assign personal questions to additional registrant    
    $eQgroup = EE_Event_Question_Group::new_instance(
                    array('EVT_ID' => $eventID,
                        'QSG_ID' => 1,
                        'EQG_additional' => 1
    ));
    $eQgroup->save();    
    $event->_add_relation_to($eQgroup, 'Event_Question_Group'); //link the question group
    
    //set ticket schedue
    setSchedTicket($parameter_array, $entry, $eventID);

    $userBio = getFieldByParam('user-bio', $parameter_array, $entry);    
    $userFname = getFieldByParam('user-fname', $parameter_array, $entry);
    $userLname = getFieldByParam('user-lname', $parameter_array, $entry);

    //check if facilitator exists
    $person = EEM_Person::instance()->get_one([['PER_email' => $userEmail]]);

    if ($person) {
        $personID = $person->ID();
        //update bio fname and lname if changed
        updatePerson($parameter_array, $entry, $person);
    } else { //if they do not exist, add user
        $person = EE_Person::new_instance(array(
                    "PER_full_name" => $userFname . ' ' . $userLname,
                    "PER_bio" => $userBio,
                    "PER_fname" => $userFname,
                    "PER_lname" => $userLname,
                    "PER_email" => $userEmail
        ));
        $person->save();
        $personID = $person->ID();
    }

    // set person image
    set_post_thumbnail(get_post($personID), attachment_url_to_postid($entry['118'])); //user image is in field 118 of the submitted entry
    //assign that user to this event
    $per_post = EE_Person_Post::new_instance(array('PER_ID' => $personID, 'OBJ_ID' => $eventID, 'PT_ID' => '124')); //124 is the people type of facilitator
    $per_post->save();

    //now lets look for additional hosts
    // this will update the organizer social, website, and facilitator info
    update_organizer_data($entry, $form, $personID, $parameter_array);

    /*
     * Now that the event is created, let's transfer data from the entry to the event
     */
    event_post_meta($entry, $form, $eventID, $parameter_array); // update taxonomies, featured image, etc    
    update_event_acf($entry, $form, $eventID, $parameter_array); // Set the ACF data    
    
}
