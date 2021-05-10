<?php

// Overwrite the event dates display
function espresso_list_of_event_dates( $EVT_ID = 0, $date_format = '', $time_format = '', $echo = TRUE, $show_expired = NULL, $format = TRUE,  $limit = NULL ) {
	$date_format = ! empty( $date_format ) ? $date_format : get_option( 'date_format' );
	$time_format = ! empty( $time_format ) ? $time_format : get_option( 'time_format' );
	$date_format = apply_filters( 'FHEE__espresso_list_of_event_dates__date_format', $date_format );
	$time_format = apply_filters( 'FHEE__espresso_list_of_event_dates__time_format', $time_format );
	$datetimes = EEH_Event_View::get_all_date_obj( $EVT_ID, $show_expired, FALSE, $limit );
	if ( ! $format ) {
		return apply_filters( 'FHEE__espresso_list_of_event_dates__datetimes', $datetimes );
	}
	//d( $datetimes );
	if ( is_array( $datetimes ) && ! empty( $datetimes )) {
		global $post;
		$html = $format ? '<ul id="ee-event-datetimes-ul-' . $post->ID . '" class="ee-event-datetimes-ul ee-clearfix">' : '';
		foreach ( $datetimes as $datetime ) {
			if ( $datetime instanceof EE_Datetime ) {
				$html .= '<li id="ee-event-datetimes-li-' . $datetime->ID();
				$html .= '" class="ee-event-datetimes-li ee-event-datetimes-li-' . $datetime->get_active_status() . '">';
				$datetime_name = $datetime->name();
				$html .= ! empty( $datetime_name ) ? '<h3>' . $datetime_name . '</h3>' : '';
				$html .= '<span class="dashicons dashicons-calendar"></span><span class="ee-event-datetimes-li-daterange">' . $datetime->date_range( $date_format ) . '</span>';
				$html .= '<span class="dashicons dashicons-clock"></span><span class="ee-event-datetimes-li-timerange">' . $datetime->time_range( $time_format ) . ' Pacific</span>';
				$datetime_description = $datetime->description();
				$html .= ! empty( $datetime_description ) ? ' - ' . $datetime_description : '';
				//$html = apply_filters( 'FHEE__espresso_list_of_event_dates__datetime_html', $html, $datetime );
				$html .= '</li>';
			}
		}
		$html .= $format ? '</ul>' : '';
	} else {
		$html = $format ?  '<p><span class="dashicons dashicons-marker pink-text"></span>' . esc_html__( 'There are no upcoming dates for this event.', 'event_espresso' ) . '</p><br/>' : '';
	}
	if ( $echo ) {
		echo $html;
		return '';
	}
	return $html;
}

function event_ticket_prices($post) {
	// grab array of EE_Ticket objects for event
	$tickets = EEH_Event_View::event_tickets_available( $post->ID() );
	$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
	if ( is_array( $tickets ) && count($tickets) > 1 ) {
		foreach($tickets as $ticket => $element) {
			$tickets[$ticket] = $tickets[$ticket]->ticket_price();
		}
	}
	sort($tickets, SORT_NUMERIC);
	$ticket_price = 'Tickets Not Available';
	if ( is_array( $tickets ) && count($tickets) > 1 ) {
		foreach($tickets as $ticket => $element) {
			reset($tickets);
			if ($ticket === key($tickets))
				$ticket_price = $formatter->formatCurrency($tickets[$ticket], 'USD');
				if(trim($ticket_price) == "$0.00"){
					$ticket_price = 'FREE';
				}
			end($tickets);
			if ( $ticket === key($tickets) && $formatter->formatCurrency($tickets[$ticket], 'USD') != $ticket_price ) {
				$ticket_price .= " - " . $formatter->formatCurrency($tickets[$ticket], 'USD');
			}
		}
	} else if (count($tickets) > 0) {
		$ticket_price = $formatter->formatCurrency($tickets[0]->ticket_price(), 'USD');
		if(trim($ticket_price) == "$0.00"){
			$ticket_price = 'FREE';
		}
	}
	return $ticket_price;
}

/**
 * Add the Event Type taxonomy to event espresso
 *
 */
function register_taxonomy_event_type() {
	$labels = array(
		'name' => _x( 'Event Types', 'event_types' ),
		'singular_name' => _x( 'Event Type', 'event_types' ),
		'search_items' => _x( 'Search Event Types', 'event_types' ),
		'popular_items' => _x( 'Popular Event Types', 'event_types' ),
		'all_items' => _x( 'All Event Types', 'event_types' ),
		'parent_item' => _x( 'Parent Event Type', 'event_types' ),
		'parent_item_colon' => _x( 'Parent Event Type:', 'event_types' ),
		'edit_item' => _x( 'Edit Event Type', 'event_types' ),
		'update_item' => _x( 'Update Event Types', 'event_types' ),
		'add_new_item' => _x( 'Add New Event Type', 'event_types' ),
		'new_item_name' => _x( 'New Event Types', 'event_types' ),
		'separate_items_with_commas' => _x( 'Separate Event Types with commas', 'event_types' ),
		'add_or_remove_items' => _x( 'Add or remove Event Types', 'event_types' ),
		'choose_from_most_used' => _x( 'Choose from most used Event Types', 'event_types' ),
		'menu_name' => _x( 'Event Types', 'event_types' ),
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_in_nav_menus' => true,
		'show_ui' => true,
		'show_tagcloud' => true,
		'hierarchical' => true,
		'rewrite' => true,
		'query_var' => true,
		'show_in_rest' => true
	);

	register_taxonomy( 'event_types', array('espresso_events'), $args );
}
add_action( 'init', 'register_taxonomy_event_type' );

// Disable the default archive page
add_filter(
    'FHEE__EE_Register_CPTs__get_CPTs__cpts',
    'ee_remove_event_cpt_archive'
);
function ee_remove_event_cpt_archive( $cpt_registry_array ) {
    if ( isset( $cpt_registry_array['espresso_events'] ) ) {
        $cpt_registry_array['espresso_events']['args']['has_archive'] = false;
    }
    return $cpt_registry_array;
}

add_filter( 'FHEE__EE_Ticket_Selector__display_ticket_selector_submit__btn_text', 'ee_mer_change_cart_button', 11 );
function ee_mer_change_cart_button( $text ) {    
    return 'Get Tickets';
}

add_filter( 'AHEE__EE_Registration__set_status__to_approved', 'attendee_approved', 4 );
//attendee registration approved
function attendee_approved( $registration) {            
    $attendeeID = $registration->attendee_ID();
    $eventID = $registration->event_ID();
    $attendee = EEM_Attendee::instance()->get_one_by_ID($attendeeID);
    
    //get the user information for the attendee
    $attendeeEmail = $attendee->email();
    $user = get_user_by('email', $attendeeEmail);
    
    if(!$user) {
        //create a user
        $username = strstr($attendeeEmail, '@', true); //first try username being the first part of the email
        if(username_exists( $username )){  //username exists try something else
            $count=1;
            $exists = true;
            while($exists){
                $username = $username.$count;
                if(!username_exists($username)){
                    $exists = false;
                }
                $count++;
            }
        }
        
        //generate random password, create user, send email        
        $random_password = wp_generate_password( 12, false );
        $user_id = wp_create_user( $username, $random_password, $attendeeEmail );
        wp_new_user_notification( $user_id, '','user');
        //add wp_EE_Attendee_ID usermeta
        $attendeeID = $attendee->get('ATT_ID');
        add_user_meta($user_id,'wp_EE_Attendee_ID',$attendeeID);
    }else{        
        $user_id = $user->ID;
    }
     
    // give them a free membership    
    $result = ihc_do_complete_level_assign_from_ap($user_id, 14, 0, 0);
    
    //add them to the event group    
    $group_id = get_field('group_id', $eventID);
    
    groups_join_group( $group_id, $user_id);
    
    return $registration;
}

add_filter( 'FHEE__thank_you_page_overview_template__order_conf_desc', 'confirmation_page_text', 4 );
function confirmation_page_text($order_conf_desc){    
    $order_conf_desc = 'Your registration has been successfully processed. '.                        
                        'As part of the Maker Campus experience, all registered attendees have been given a free membership to Make: Community. '.
                        'This membership provides attendees with a central hub for the workshop; material list, online webinar access, group access to connect with the facilitator, attendees, and more!  Make: Community is a great place to connect with others and find making activities online and at your local makerspace.<br/><br/>'.                      
                        'Attendees, check your email for your registration confirmation and login instructions to access your event information and benefit from the full Maker Campus experience. '.
                        'Click the button below to view / download / print a full description of your purchases and registration information.<br/><br   />';
    $order_conf_desc .=   (is_user_logged_in()?'<a class="ee-button ee-roundish indented-text big-text" href="/my/groups/">View Event Group</a>':'');
    return $order_conf_desc;
}

add_filter('FHEE__EED_Multi_Event_Registration__return_to_events_list_btn_txt','change_return_to_event_text',1);
function change_return_to_event_text($text){
    $text = 'Return to Event';
    return $text;
}