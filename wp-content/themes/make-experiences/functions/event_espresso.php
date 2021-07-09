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

function events_sidebar() {

    register_sidebar( array(
        'id'            => 'event_grid_sidebar',
        'name'          => 'Event Grid Sidebar',
    ) );

}
add_action( 'widgets_init', 'events_sidebar' );

//default the admin menu link to sort by newest event first
add_action ( 'admin_menu', 'ee_filter_ee_events_orderyby_datetime', 99 );
function ee_filter_ee_events_orderyby_datetime() {
    // call global submenu item
    global $submenu;
    // edit main link for events
    $submenu['espresso_events'][0][2] = 'admin.php?page=espresso_events&orderby=Datetime.DTT_EVT_start&order=desc';
}

// hide events tagged as 'hidden'
function ee_filter_pre_get_posts( $query ) {
	if ( isset($query->query['post_type']) ) {
		if( $query->query['post_type'] == 'espresso_events' ) {
			$query->set( 'tax_query', array(
					array(
						'taxonomy' => 'post_tag',
						'field' => 'term_id',
						'terms' => get_tag_ID('hidden'),
						'operator' => 'NOT IN'
					)
				) 
			);
		}
	}
}
add_action( 'pre_get_posts', 'ee_filter_pre_get_posts' );