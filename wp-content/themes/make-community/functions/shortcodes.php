<?php

// Get Members count
function show_number_of_members() {
	$output = '<div class="stat member-count">
                <h3>' . bp_get_total_site_member_count() . '+</h3>
                <h4>Registered Members</h4>
		       </div>';
	return $output;
}
add_shortcode('member_count', 'show_number_of_members');

// Get Groups count
function show_number_of_groups() {
	$output = '<div class="stat group-count">
                <h3>' . bp_get_total_group_count() . '+</h3>
                <h4>Registered Groups</h4>
		       </div>';
	return $output;
}
add_shortcode('group_count', 'show_number_of_groups');

// Get Upcoming Events count
function show_number_of_upcoming_events() {
    $events = EM_Events::get(array('scope'=>'future'));
    $events_count = count($events);
	$output = '<div class="stat event-count">
                <h3>' . $events_count . '+</h3>
                <h4>Registered Events</h4>
		       </div>';
	return $output;
}
add_shortcode('event_count', 'show_number_of_upcoming_events');