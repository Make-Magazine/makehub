<?php

//duplicate entry
add_action('gravityview/duplicate-entry/duplicated', 'duplicate_entry', 10, 2);

function duplicate_entry($duplicated_entry, $entry) {
    error_log('duplicate_entry with form id ' . $duplicated_entry['form_id']);
    $form = GFAPI::get_form($duplicated_entry['form_id']);
    create_event($duplicated_entry, $form);
}

function update_event_acf($entry, $form, $post_id, $parameterArray) {
    //field mapping - ** note - upload fields don't work here. use post creation feed for that **
    //0 indicie = gravity form field id
    //1 indicie = acf field name/event meta fields
    //2 indicie (optional) = acf field key or subfield key (for repeaters)
    //can't set parameter names on image fields, so we have touse the field ids
	// TBD automaticallly map if acf field name exists?
    $field_mapping = array(
        array('140', 'image_1'),
        array('141', 'image_2'),
        array('142', 'image_3'),
        array('143', 'image_4'),
        array('144', 'image_5'),
        array('145', 'image_6'),
        array('150', 'about'),
        array('', 'promo_videos', 'field_5f7cd1ffdd06a'),
        array('', 'short_description'),
        array('', 'audience', 'field_5f35a5f833a04'),
        array('', 'location'),
        array('', 'materials'),
        array('', 'kit_required'),
        array('', 'kit_price_included'),
        array('', 'kit_supplier'),
        array('', 'other_kit_supplier'),
        array('', 'kit_shipping_time'),
        array('', 'kit_url'),
        array('', 'wish_list_urls', 'field_5f7cc93ab762c'),
        array('', 'prior_hosted_event'),
        array('', 'hosted_live_stream'),
        array('', 'video_conferencing', 'field_5f60f9bfa1d1e'),
        array('', 'prev_session_links'),
        array('', 'comfort_level'),
        array('', 'technical_setup'),
        array('', 'basic_skills'),
        array('', 'skills_taught'),
        array('', 'public_email'),
        array('', 'attendee_communication_email'),
        array('', 'webinar_link'),
        array('', 'program_expertise'),
        array('', 'custom_schedule_details')
    );

    //update the acf fields with the submitted values from the form
    foreach ($field_mapping as $field) {
        $fieldID = 0;
        if ($field[0] == '') {
            //determine field id by parameter name
            $paramName = $field[1];

            if (isset($parameterArray[$paramName])) {
                $fieldInfo = $parameterArray[$paramName];
                if (isset($fieldInfo)) {
                    $fieldID = (string) $fieldInfo->id;
                }
            }
        } else {
            $fieldID = $field[0];
        }

        $meta_field = $field[1];
        $field_key = (isset($field[2]) ? $field[2] : '');
        $fieldData = GFAPI::get_field($form, $fieldID);

        if ($fieldID != 0 && isset($entry[$fieldID])) {
            if ($fieldData->type == 'post_custom_field' && $fieldData->inputType == 'list' || $fieldData->type == 'list') {
                $listArray = explode(', ', $fieldData->get_value_export($entry));
                $num = 1;
                $repeater = [];
                foreach ($listArray as $value) {
                    $repeater[] = array($field_key => $value);
                    $num++;
                }
                update_field($meta_field, $repeater, $post_id);
            } else if (strpos($meta_field, 'image') !== false) {
                update_post_meta($post_id, $meta_field, attachment_url_to_postid($entry[$fieldID])); // this should hopefully use the attachment id                
            } else {
                //update_post_meta($post_id, $meta_field, $entry[$fieldID]);
                update_field($meta_field, $entry[$fieldID], $post_id);
            }
        }
        // checkboxes are set with a decimal point for each selection so theisset in entry doesn't work
        if (isset($fieldData->type)) {
            if ($fieldData->type == 'checkbox' || ($fieldData->type == 'post_custom_field' && $fieldData->inputType == 'checkbox')) {
                $checked = $fieldData->get_value_export($entry);
                $values = explode(', ', $checked);
                update_field($field_key, $values, $post_id);
            }
        }
    }
}

function event_post_meta($entry, $form, $post_id, $parameter_array) {
    // Set the taxonomies       
    $expType = getFieldByParam('exp-type', $parameter_array, $entry);
    $expCats = getFieldByParam('exp-cats', $parameter_array, $entry);

    wp_set_object_terms($post_id, $expType, 'event_types'); //program type    
    wp_set_object_terms($post_id, $expCats, 'espresso_event_categories');  //event Categories
    // Set the featured Image
    set_post_thumbnail($post_id, attachment_url_to_postid($entry['9']));
}

function update_organizer_data($entry, $form, $personID, $parameter_array) {
    $userSocial = getFieldByParam('user_social', $parameter_array, $entry); //this is a serialized field
    $userWebsite = getFieldByParam('user_website', $parameter_array, $entry);
    $facilitator_info = getFieldByParam('user-bio', $parameter_array, $entry);
    $facilitator_info = strip_tags(htmlspecialchars_decode($facilitator_info));

    $socialLinks = unserialize($userSocial); //TBD need to find more secure way of doing this to avoid code injection

    $repeater = array();
    if (is_array($socialLinks)) {
        foreach ($socialLinks as $value) {
            $repeater[] = array("field_5f7e086a4a5a3" => $value);
        }
    }
    // update ACF fields for the event organizer    
    update_field("social_links", $repeater, $personID);
    update_field("website", $userWebsite, $personID);
    update_field("facilitator_info", $facilitator_info, $personID);
}

function update_sched_ticket_acf($schedArray, $eventID) {
    //acf field - tickets_scheduling (repeater)
    /*
      ticket_name
      ticket_price
      ticket_description
      minimum_num_tickets
      maximum_num_of_tickets
      preferred_schedule
      alternate_schedule
     */
    update_field('field_606e135e03fa2', $schedArray, $eventID);
}

function setSchedTicket($parameter_array, $entry, $eventID) {
    /* Event Date/Time and Tickets
     *      Ticket and schedule information is set in a nested form
     *      Need to get nested form ID and then loop through the nested form information 
     */
    $timeZone = getFieldByParam('timezone', $parameter_array, $entry);

    //pull nested form to get submitted schedule/ticket
    if (isset($parameter_array['nested-form'])) {
        $nstFormID = (isset($parameter_array['nested-form']['gpnfForm']) ? $parameter_array['nested-form']['gpnfForm'] : '10');
        $nstForm = GFAPI::get_form($nstFormID);

        //get the list of entry id's for the nested form
        $nstEntryIDs = $entry[$parameter_array['nested-form']['id']];

        $nstEntryArr = explode(",", $nstEntryIDs);
        $schedArray = array();
        foreach ($nstEntryArr as $nstEntryID) {
            $nst_entry = GFAPI::get_entry($nstEntryID);
            $nest_parameter_arr = find_field_by_parameter($nstForm); //find all fields with paramater names set in nested form
            //Ticket Information        
            $value = getFieldByParam('ticket-name', $nest_parameter_arr, $nst_entry);
            $ticketName = (!empty($value) ? $value : 'Ticket - ' . $entry[1]); //if ticket name not given, default ticket name to 'Ticket - Event Name'

            $ticketPrice = getFieldByParam('ticket-price', $nest_parameter_arr, $nst_entry);
            $ticketDesc = getFieldByParam('ticket-desc', $nest_parameter_arr, $nst_entry);
            $ticketMin = getFieldByParam('ticket-min', $nest_parameter_arr, $nst_entry);
            $ticketMax = getFieldByParam('ticket-max', $nest_parameter_arr, $nst_entry);
            $schedDesc = getFieldByParam('sched-desc', $nest_parameter_arr, $nst_entry);

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

            //TBD - Note we need to do something more secure here to avoid code injection
            $prefSched = unserialize($prefSchedSer);

            //create tickets
            $preferred_schedule = array();
            foreach ($prefSched as $sched) {
                //Start Date
                $date = date_create($sched['Date'] . ' ' . $sched['Start Time']);
                $start_date = new DateTime(date_format($date, "Y-m-d") . 'T' . date_format($date, "H:i:s"), new DateTimeZone($timeZone));

                //End Date
                $date = date_create($sched['Date'] . ' ' . $sched['End Time']);
                $end_date = new DateTime(date_format($date, "Y-m-d") . 'T' . date_format($date, "H:i:s"), new DateTimeZone($timeZone));

                //create the date/time instance
                $d = EE_Datetime::new_instance(
                                array('EVT_ID' => $eventID, 'DTT_name' => $schedDesc, 'DTT_EVT_start' => $start_date,
                                    'DTT_EVT_end' => $end_date, 'DTT_reg_limit' => $ticketMax));

                $d->save();
                $tkt->_add_relation_to($d, 'Datetime'); //link the datetime and the ticket instances
                //set the preferred schedule for the ACF
                $preferred_schedule[] = array('date' => $sched['Date'], 'start_time' => $sched['Start Time'], 'end_time' => $sched['End Time']);
                                
                //update the ticket end date with the start of the event
                $event = EEM_Event::instance()->get_one_by_ID($eventID);
                $date = $event->first_datetime();       
                echo 'ticket end date should be '.$date->start_date().'<br/>';
                $tkt->set('TKT_end_date', $date->start_date());
                $tkt->save();
                
            }

            //set alternate schedule
            $alternate_schedule = array();
            $altSched = unserialize($altSchedSer);

            foreach ($altSched as $sched) {
                //set the preferred schedule for the ACF
                $alternate_schedule[] = array('date' => $sched['Date'], 'start_time' => $sched['Start Time'], 'end_time' => $sched['End Time']);
            }

            $schedArray[] = array('ticket_name' => $ticketName,
                'ticket_price' => $ticketPrice,
                'ticket_description' => $ticketDesc,
                'min_num_tickets' => $ticketMin,
                'max_num_tickets' => $ticketMax,
                'schedule_description' => $schedDesc,
                'preferred_schedule' => $preferred_schedule,
                'alternate_schedule' => $alternate_schedule);
        }
                        
        //set ACF schedule and Tickets info
        update_sched_ticket_acf($schedArray, $eventID);
    }
}
