<?php

//disable the default post creation
add_filter( 'gform_disable_post_creation_7', 'disable_post_creation', 10, 3 );
function disable_post_creation( $is_disabled, $form, $entry ) {
    return true;
}

// Create event with ticket
add_action( 'gform_after_submission_7', 'create_event', 10, 2 );
function create_event($entry, $form) {
	global $wpdb;
	
	$tags = GFAPI::get_field($form, 50);
	$tagArray = array();
    if ($tags->type == 'checkbox') {
        // Get a comma separated list of checkboxes checked
        $checked = $tags->get_value_export($entry);
        // Convert to array.
        $tagArray = explode(', ', $checked);    
    }
	$start_date = date_create($entry['4'] .' ' . $entry['5']);
	$end_date = date_create($entry['4'] .' ' . $entry['7']);

	$organizerData = array(
		'Organizer' => $entry['116.3'] . " " . $entry['116.6'],
		'Email' => wp_get_current_user()->user_email,
		'Website' => $entry['128']
	);
	
	// pull the id of the last organizer with the submitter's email address so we don't create a duplicate
	$existingOrganizer = $wpdb->get_var('
	SELECT post_id 
	FROM '.$wpdb->prefix.'postmeta 
	WHERE meta_key = "_OrganizerEmail" and meta_value = "'.$organizerData['Email'].'" 
	order by post_id DESC limit 1');
	if($existingOrganizer) {
		$organizerData['ID'] = $existingOrganizer;
	}
	
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
	$post_id = tribe_create_event( $event_args );
	
	// update social media fields for the event organizer
	$organizer_id = tribe_get_organizer_id($post_id);
	$socialField = GFAPI::get_field($form, 127);
	$socialLinks = explode(', ', $socialField->get_value_export($entry));
	$num = 1;
	$repeater = [];
	foreach ($socialLinks as $value) {
		$repeater[] = array("field_5f7e086a4a5a3" => $value);
		$num++;
	}
	update_field("social_links", $repeater, $organizer_id); 
	
	// Upload featured image to Organizer page
	set_post_thumbnail(get_page_by_title($organizerData['Organizer'], 'OBJECT', 'tribe_organizer'), get_attachment_id_from_url($entry['118'])); 
	
	// Set the arguments for the recurring event.
	if($entry['100'] == "no") {
		$recurrence_data = array(
			'recurrence' => array(
				'rules' => array(
					array(
						'type'                  => 'Every Year',
						'end-type'              => 'Never',
						'end'                   => '',
						'end-count'             => '',
						'EventStartDate'        => $start_date,
						'EventEndDate'          => $end_date,
						'custom'                => array(),
						'occurrence-count-text' => 'events',
					),
				),
			),
		);

		// Instantiate and set it in motion.
		$recurrence_meta  = new \Tribe__Events__Pro__Recurrence__Meta();
		$recurrence_meta->updateRecurrenceMeta( $post_id, $recurrence_data );
	}
	
	// Set the taxonomies
	wp_set_object_terms( $post_id, $entry['12'], 'tribe_events_cat' );
	wp_set_object_terms( $post_id, $tagArray, 'post_tag' );
	
	// Set the featured Image
	set_post_thumbnail($post_id, get_attachment_id_from_url($entry['9']));
	
	//field mapping - ** note - upload fields don't work here. use post creation feed for that **
    //0 indicie = gravity form field id
    //1 indicie = acf field name/event meta fields
	//2 indicie (optional) = acf field key or subfield key (for repeaters)
    $field_mapping = array(
        array('4', 'preferred_start_date'),
        array('5', 'preferred_start_time'),
        array('6', 'preferred_end_date'),
        array('7', 'preferred_end_time'),
        array('96', 'alternative_start_date'),
        array('97', 'alternative_start_time'),
        array('96', 'alternative_end_time'),
        array('99', 'alternative_end_date'),   
        array('124', 'schedule_exclusions'),   
		array('31', 'image_1'),
		array('32', 'image_2'),
		array('33', 'image_3'),
		array('54', 'image_4'),
		array('55', 'image_5'),
		array('56', 'image_6'),
		array('123', 'promo_videos', 'field_5f7cd1ffdd06a'),
        array('19', 'about'),
		array('119', 'short_description'),
        array('73', 'audience', 'field_5f35a5f833a04'),
        array('57', 'location'),
        array('72', 'materials', 'field_5f7b4abb07cab'),
        array('78', 'kit_required'),
        array('79', 'kit_price_included'),
        array('80', 'kit_supplier'),
        array('111', 'other_kit_supplier'),
		array('120', 'kit_shipping_time'),
        array('82', 'kit_url'),
        array('122', 'wish_list_urls', 'field_5f7cc93ab762c'),
        array('87', 'prior_hosted_event'),
        array('88', 'hosted_live_stream'),        
        array('89', 'video_conferencing', 'field_5f60f9bfa1d1e'),        
        array('90', 'other_video_conferencing'),
        array('91', 'prev_session_links'),
        array('92', 'comfort_level'),
        array('93', 'technical_setup'),
        array('108', 'basic_skills'),
        array('109', 'skills_taught'),
    );
    
    //update the acf fields with the submitted values from the form
    foreach($field_mapping as $field){
        $fieldID    = $field[0];
        $meta_field = $field[1];
		$field_key = (isset($field[2])?$field[2]:'');
		$fieldData = GFAPI::get_field($form, $fieldID);
        if(isset($entry[$fieldID])){
			if ($fieldData->type == 'post_custom_field' && $fieldData->inputType == 'list' || $fieldData->type == 'list') {
				$listArray = explode(', ', $fieldData->get_value_export($entry));
				$num = 1;
				$repeater = [];
				foreach ($listArray as $value) {
					$repeater[] = array($field_key => $value);
					$num++;
				}
				update_field($meta_field, $repeater, $post_id);
			} else if(strpos($meta_field, 'image') !== false) {
				 update_post_meta($post_id, $meta_field, get_attachment_id_from_url($entry[$fieldID])); // this should hopefully use the attachment id
			} else {
				 //error_log('updating image ACF field '.$meta_field. ' with GF field '.$fieldID . ' with value '.$entry[$fieldID]);
				 update_post_meta($post_id, $meta_field, $entry[$fieldID]);
			}
        }
		// entry field id is not set in time for checkboxes, apparently
		if ($fieldData->type == 'post_custom_field' && $fieldData->inputType == 'checkbox' || $fieldData->type == 'checkbox') { 
			$checked = $fieldData->get_value_export($entry);
			$values = explode(', ', $checked);   
			update_field($field_key, $values, $post_id); 
		}
    }

    // create ticket for event // CHANGE TO WOOCOMMERCE AFTER PURCHASING EVENTS PLUS PLUGIN
    //$api = Tribe__Tickets__Commerce__PayPal__Main::get_instance();
	$api = Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance();
    $ticket = new Tribe__Tickets__Ticket_Object();
    $ticket->name = "Ticket - " . $post_id;
    $ticket->description = (isset($entry['42'])?$entry['42']:'');
    $ticket->price = (isset($entry['37'])?$entry['37']:'');
    $ticket->capacity = (isset($entry['106'])?$entry['106']:'999');
    $ticket->start_date = (isset($entry['45'])?$entry['45']:'');
    $ticket->start_time = (isset($entry['46'])?$entry['46']:'');
    $ticket->end_date = (isset($entry['47'])?$entry['47']:'');
    $ticket->end_time = (isset($entry['48'])?$entry['48']:'');

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
	
	//set the post id
    $wpdb->update($wpdb->prefix.'gf_entry',array('post_id'=>$post_id),array('id'=>$entry['id']));
}
