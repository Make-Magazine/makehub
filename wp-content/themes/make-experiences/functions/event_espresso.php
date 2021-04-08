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