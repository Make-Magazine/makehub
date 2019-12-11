<?php
//add new Notification event of manual to allow users to set up manual notifications
add_filter( 'gform_notification_events', 'add_event' );
function add_event( $notification_events ) {
    $notification_events['manual']                = __( 'Send Manually', 'gravityforms' );
    return $notification_events;
}
