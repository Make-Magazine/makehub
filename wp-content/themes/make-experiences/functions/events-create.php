<?php

//We do not want gravity forms creating the post, we will be doing that ourselves
add_filter('gform_disable_post_creation_7', 'disable_post_creation', 10, 3);

function disable_post_creation($is_disabled, $form, $entry) {
    return true;
}

// Create event with ticket
add_action('gform_after_submission_7', 'create_event', 10, 2);

function create_event($entry, $form) {
    //let's get some basic info for the event
    //pull field by variable name
    //Schedule is stored in a nested form
    var_dump($entry);
    
    //Start Date
    $date       = date_create($entry['4'] . ' ' . $entry['5']); 
    $start_date = date_format($date,"Y-m-d"). 'T'.date_format($date,"H:i:s"); //date format needs to be 2021-03-25T18:00:00    
    
    //End Date
    $date     = date_create($entry['4'] . ' ' . $entry['7']);
    $end_date = date_format($date,"Y-m-d"). 'T'.date_format($date,"H:i:s"); //date format needs to be 2021-03-25T18:00:00
            
    //Create the event
    $rest_endpoint = '/ee/v4.8.36/events';
    $body_params = array('EVT_name' => $entry[1],
        'EVT_desc' => $entry[2],
        'EVT_short_desc' => $entry[119],
        'EVT_wp_user' => 3128,
        'status' => "pending"
    );
    $data = int_rest_req($rest_endpoint, $body_params);
    //was the event created?
    if (!empty($data['EVT_ID'])) {        
        $event_id = $data['EVT_ID'];

        //next set the date/time of the event
        $rest_endpoint = '/ee/v4.8.36/datetimes';
        $body_params = array('EVT_ID' => $event_id,
                            'DTT_name' => $entry['1'],
                            'DTT_EVT_start' => $start_date, //"Start time/date of Event - this is in the timezone of the site."
                            'DTT_EVT_end' => $end_date, //"End time/date of Event - this in the timezone of the site."
                            'DTT_reg_limit' => $entry['106'], //"Registration Limit for this time"
        );
        $data = int_rest_req($rest_endpoint, $body_params);


        //was the datetime created?
        if (!empty($data['DTT_ID'])) {
            $dateTimeID = $data['DTT_ID'];

            //now let's create the ticket            
            $rest_endpoint = '/ee/v4.8.36/tickets';
            $body_params = array('TKT_name' => "Ticket - " . $entry['1'],
                'TKT_description' => (isset($entry['42']) ? $entry['42'] : ''),
                'TKT_price' => (isset($entry['37']) ? $entry['37'] : 0),
                'TKT_min' => (isset($entry['43']) ? $entry['43'] : ''),
                'TKT_max' => (isset($entry['106']) ? $entry['106'] : ''),
                'TKT_qty' => (isset($entry['106']) ? $entry['106'] : ''), //"Quantity of this ticket that is available"
                'TKT_required' => true);
            $data = int_rest_req($rest_endpoint, $body_params);

            //was the ticket created?
            if (!empty($data['TKT_ID'])) {                
                $ticketID = $data['TKT_ID'];

                //then relate the datetime with the ticket
                $rest_endpoint = '/ee/v4.8.36/datetime_tickets';
                $body_params = array('DTT_ID' => $dateTimeID, 'TKT_ID' => $ticketID);
                $data = int_rest_req($rest_endpoint, $body_params);

                //now create a price object       
                $rest_endpoint = '/ee/v4.8.36/prices';
                $body_params = array('PRT_ID' => 1, 'PRC_amount' => (isset($entry['37']) ? $entry['37'] : 0));
                $data = int_rest_req($rest_endpoint, $body_params);

                //was price object created?
                if (!empty($data['PRC_ID'])) {
                    $priceID = $data['PRC_ID'];
                    
                    //associate that price object to the ticket                    
                    $rest_endpoint = '/ee/v4.8.36/ticket_prices';
                    $body_params = array('TKT_ID' => $ticketID, 'PRC_ID' => $priceID);
                    $data = int_rest_req($rest_endpoint, $body_params);
                }
            }
        }

        //check if user exists
        //if they do, assign that user to this event
        //if they do not, add user
        //then assign that uer to this event
    }else{
        //event was not created - TBD need a fallback here
        error_log('Error in creating the event for entry '. $entry('ID'));
        return;
    }
    
    /*
     * Now that the event is created, let's transfer data from the entry to the event
     */
    // update taxonomies, featured image, etc
    event_post_meta($entry, $form, $event_id);
    // Set the ACF data
    update_event_acf($entry, $form, $event_id);
    // Set event custom fields for filtering
    update_event_additional_fields($entry, $form, $event_id);
}

/*  This function performs the internal rest requests to Event Espresso */
function int_rest_req($rest_endpoint, $body_params, $type='POST') {
    $request = new WP_REST_Request($type, $rest_endpoint);
    $request->set_body_params($body_params);

    $response = rest_do_request($request);
    $server = rest_get_server();
    $data = $server->response_to_data($response, false);                
    
    if ( $response->is_error() ) {
        echo 'Error in call to WPI Rest API endpoint ('.$rest_endpoint.')<br/>';
        var_dump($data);
    }    
    return $data;
}
