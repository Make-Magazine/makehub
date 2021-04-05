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
				$html .= '<span class="dashicons dashicons-clock"></span><span class="ee-event-datetimes-li-timerange">' . $datetime->time_range( $time_format ) . '</span>';
				$datetime_description = $datetime->description();
				$html .= ! empty( $datetime_description ) ? ' - ' . $datetime_description : '';
				$html = apply_filters( 'FHEE__espresso_list_of_event_dates__datetime_html', $html, $datetime );
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
