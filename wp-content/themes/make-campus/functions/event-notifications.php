<?php

add_filter( 'gform_notification_events', 'custom_notification_event' );
function custom_notification_event( $events ) {
    //$events['event_approved'] = __( 'Event Entry Approved' );
    //$events['event_rejected'] = __( 'Event Entry Rejected' );
    return $events;
} 
