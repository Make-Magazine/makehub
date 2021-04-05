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
    global $current_user;
    $current_user = wp_get_current_user();
    $userID     = $current_user->ID;
    $userEmail  = (string) $current_user->user_email;
    
    //first create the event
    $currDateTime = date(DATE_ATOM, mktime(0, 0, 0, 7, 1, 2000));
    $event = EE_Event::new_instance(
                    array('EVT_name' => $entry[1],
                        'EVT_desc' => $entry[2],
                        'EVT_short_desc' => $entry[119],
                        'EVT_wp_user' => $userID,
                        'status' => "pending",
                        'EVT_visible_on' => $currDateTime
    ));
    $event->save();
    $eventID = $event->ID();

    /* Event Date/Time and Tickets
     *      Ticket and schedule information is set in a nested form
     *      Need to get nested form ID and then loop through the nested form information 
     */

    //pull field by variable name 
    $parameter_array = find_field_by_parameter($form);
    $timeZone = getFieldByParam('timezone', $parameter_array, $entry);

    //pull nested form to get submitted schedule/ticket
    if (isset($parameter_array['nested-form'])) {

        $nstFormID = (isset($parameter_array['nested-form']['gpnfForm']) ? $parameter_array['nested-form']['gpnfForm'] : 10);
        $nstForm = GFAPI::get_form($nstFormID);

        //get the list of entry id's for the nested form
        $nstEntryIDs = $entry[$parameter_array['nested-form']['id']];
        $nstEntryArr = explode(",", $nstEntryIDs);
        foreach ($nstEntryArr as $nstEntryID) {
            $nst_entry = GFAPI::get_entry($nstEntryID);
            $nest_parameter_arr = find_field_by_parameter($nstForm); //find all fields with paramater names set in nested form
            //Ticket Information        
            $value = getFieldByParam('ticket-name', $nest_parameter_arr, $nst_entry);
            $ticketName = (!empty($value) ? $value : 'Ticket - ' . $entry[1]); //if ticket name not given, default ticket name to 'Ticket - Event Name'

            $ticketPrice = getFieldByParam('ticket-price', $nest_parameter_arr, $nst_entry);
            $ticketDesc  = getFieldByParam('ticket-desc', $nest_parameter_arr, $nst_entry);
            $ticketMin   = getFieldByParam('ticket-min', $nest_parameter_arr, $nst_entry);
            $ticketMax   = getFieldByParam('ticket-max', $nest_parameter_arr, $nst_entry);
            $schedDesc   = getFieldByParam('sched-desc', $nest_parameter_arr, $nst_entry);
            //create the ticket instance
            $tkt = EE_Ticket::new_instance(array('TKT_name' => $ticketName,
                        'TKT_description' => $ticketDesc,
                        'TKT_price' => $ticketPrice,
                        'TKT_min' => $ticketMin,
                        'TKT_max' => $ticketMax,
                        'TKT_qty' => $ticketMax, //"Quantity of this ticket that is available"
                        'TKT_required' => true));
            $tkt->save();

            //create Price object
            $price = EE_Price::new_instance(array('PRT_ID' => 1, 'PRC_amount' => $ticketPrice));
            $price->save();
            $tkt->_add_relation_to($price, 'Price'); //link the price and ticket instances
            //Schedule Info
            $prefSchedSer = getFieldByParam('preferred-schedule', $nest_parameter_arr, $nst_entry);
            $altSchedSer = getFieldByParam('alternative-schedule', $nest_parameter_arr, $nst_entry);

            //Note we need to do something more secure here to avoid code injection
            $prefSched = unserialize($prefSchedSer);

            //create tickets
            foreach ($prefSched as $sched) {
                //TBD - do we need to convert timezone as events are saved in the timezone of the site - Pacific
                //Start Date
                $date = date_create($sched['Date'] . ' ' . $sched['Start Time']);
                $start_date = new DateTime(date_format($date, "Y-m-d") . 'T' . date_format($date, "H:i:s"), new DateTimeZone($timeZone));

                //End Date
                $date = date_create($sched['Date'] . ' ' . $sched['End Time']);
                $end_date = new DateTime(date_format($date, "Y-m-d") . 'T' . date_format($date, "H:i:s"), new DateTimeZone($timeZone));

                //create the date/time instance
                $d = EE_Datetime::new_instance(
                                array('EVT_ID' => $eventID,
                                    'DTT_name' => $schedDesc,
                                    'DTT_EVT_start' => $start_date, 'DTT_EVT_end' => $end_date, 'DTT_reg_limit' => $ticketMax));

                $d->save();
                $tkt->_add_relation_to($d, 'Datetime'); //link the datetime and the ticket instances
            }
        }
    }
    
    $userBio   = getFieldByParam('user-bio', $parameter_array, $entry);
    $userFname = getFieldByParam('user-fname', $parameter_array, $entry);
    $userLname = getFieldByParam('user-lname', $parameter_array, $entry);
    
    //check if facilitator exists
    $person = EEM_Person::instance()->get_one([['PER_email' => $userEmail]]);

    if ($person) {
        $personID = $person->ID();
        //update bio fname and lname if changed
    } else { //if they do not exist, add user
        $person = EE_Person::new_instance(array(
                    "PER_full_name" => $userFname.' '.$userLname,
                    "PER_bio" => $userBio,
                    "PER_fname" => $userFname,
                    "PER_lname" => $userLname,                    
                    "PER_email" => $userEmail
        ));
        $person->save();
        $personID = $person->ID();
    }
    
    //assign that user to this event
    $per_post = EE_Person_Post::new_instance(array('PER_ID' => $personID, 'OBJ_ID' => $eventID, 'PT_ID' => '67'));
    $per_post->save();

    /*
     * Now that the event is created, let's transfer data from the entry to the event
     */
    /*
      // update taxonomies, featured image, etc
      event_post_meta($entry, $form, $event_id);
      // Set the ACF data
      update_event_acf($entry, $form, $event_id);
      // Set event custom fields for filtering
      update_event_additional_fields($entry, $form, $event_id); */
}

/*  This function performs the internal rest requests to Event Espresso */

function int_rest_req($rest_endpoint, $body_params, $type = 'POST') {
    $request = new WP_REST_Request($type, $rest_endpoint);
    $request->set_body_params($body_params);

    $response = rest_do_request($request);
    $server = rest_get_server();
    $data = $server->response_to_data($response, false);

    if ($response->is_error()) {
        echo 'Error in call to WPI Rest API endpoint (' . $rest_endpoint . ')<br/>';
        var_dump($data);
    }
    return $data;
}
