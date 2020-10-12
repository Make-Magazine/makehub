<?php
    
//disable the default post creation
add_filter('gform_disable_post_creation_7', 'disable_post_creation', 10, 3);

function disable_post_creation($is_disabled, $form, $entry) {
    return true;
}

// Create event with ticket
add_action('gform_after_submission_7', 'create_event', 10, 2);

function create_event($entry, $form) {
    global $wpdb;
    
    //calculate start and end date 
    $start_date = date_create($entry['4'] . ' ' . $entry['5']);
    $end_date = date_create($entry['129'] . ' ' . $entry['7']);
    
    // set organizer information
    $organizerData = event_organizer($entry);

    $event_args = array(
        'post_title' => $entry['1'],
        'post_content' => $entry['2'],
        'post_status' => 'pending',
        'post_type' => 'tribe_events',
        'EventStartDate' => $entry['4'],
        'EventEndDate' => $entry['4'],
        'EventStartHour' => $start_date->format('h'),
        'EventStartMinute' => $start_date->format('i'),
        'EventStartMeridian' => $start_date->format('A'),
        'EventEndHour' => $end_date->format('h'),
        'EventEndMinute' => $end_date->format('i'),
        'EventEndMeridian' => $end_date->format('A'),
        'Organizer' => $organizerData
    );
    $post_id = tribe_create_event($event_args);
	
	/* Leaving this for now
	$startingTZ = get_post_meta( $post_id, '_EventTimezone', true );
	error_log(print_r($startingTZ, TRUE)); // outputs America/Los_Angeles
	$startingDateTime = get_post_meta( $post_id, '_EventStartDateUTC', true );
	error_log(print_r($startingDateTime, TRUE)); // output format 2020-10-31 16:00:00
	
	update_post_meta( $event->ID, '_EventTimezone', 'America/New_York');
    update_post_meta( $event->ID, '_EventTimezoneAbbr', 'EDT');
	*/
	
    update_organizer_data($entry, $form, $organizerData, $post_id);
	// update taxonomies, featured image, etc
    event_post_meta($entry, $form, $post_id);

    // Set the arguments for the recurring event.
    if ($entry['100'] == "no") {
        $recurrence_data = array(
            'recurrence' => array(
                'rules' => array(
                    array(
                        'type' => 'Every Week',
                        'end-type' => 'on',
                        'end' => '',
                        //'end-count' => '',
                        'EventStartDate' => $start_date,
                        'EventEndDate' => $end_date,
                        'custom' => array(),
                        'occurrence-count-text' => 'events',
                    ),
                ),
            ),
        );
		//error_log(print_r($recurrence_data, TRUE));
        $recurrence_meta = new \Tribe__Events__Pro__Recurrence__Meta();
        $recurrence_meta->updateRecurrenceMeta($post_id, $recurrence_data);
    }
	
    update_event_acf($entry, $form, $post_id);

    // create ticket for event // CHANGE TO WOOCOMMERCE AFTER PURCHASING EVENTS PLUS PLUGIN
    //$api = Tribe__Tickets__Commerce__PayPal__Main::get_instance();
    $api = Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance();
    $ticket = new Tribe__Tickets__Ticket_Object();
    $ticket->name = "Ticket - " . $post_id;
    $ticket->description = (isset($entry['42']) ? $entry['42'] : '');
    $ticket->price = (isset($entry['37']) ? $entry['37'] : '');
    $ticket->capacity = (isset($entry['106']) ? $entry['106'] : '999');
    $ticket->start_date = (isset($entry['45']) ? $entry['45'] : '');
    $ticket->start_time = (isset($entry['46']) ? $entry['46'] : '');
    $ticket->end_date = (isset($entry['47']) ? $entry['47'] : '');
    $ticket->end_time = (isset($entry['48']) ? $entry['48'] : '');

    // Save the ticket
    $ticket->ID = $api->save_ticket($post_id, $ticket, array(
        'ticket_name' => $ticket->name,
        'ticket_price' => $ticket->price,
        'ticket_description' => $ticket->description,
            //'start_date' => $ticket->start_date,
            //'start_time' => $ticket->start_time,
            //'end_date' => $ticket->end_date,
            //'end_time' => $ticket->end_time,
    ));
    tribe_tickets_update_capacity($ticket->ID, $ticket->capacity);
	update_post_meta( $ticket->ID, '_stock', $ticket->capacity ); 

    //set the post id
    $wpdb->update($wpdb->prefix . 'gf_entry', array('post_id' => $post_id), array('id' => $entry['id']));
}
