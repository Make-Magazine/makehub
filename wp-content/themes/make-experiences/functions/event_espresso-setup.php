<?php
/**
 * Add the Event Type taxonomy to event espresso
 *

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
/*add_filter('FHEE__EE_Register_CPTs__get_CPTs__cpts', 'ee_remove_event_cpt_archive');
function ee_remove_event_cpt_archive( $cpt_registry_array ) {
    if ( isset( $cpt_registry_array['espresso_events'] ) ) {
        $cpt_registry_array['espresso_events']['args']['has_archive'] = false;
    }
    return $cpt_registry_array;
}

add_filter( 'FHEE__EE_Ticket_Selector__display_ticket_selector_submit__btn_text', 'ee_mer_change_cart_button', 11 );
function ee_mer_change_cart_button( $text ) {
    return 'Get Tickets';
}*/
