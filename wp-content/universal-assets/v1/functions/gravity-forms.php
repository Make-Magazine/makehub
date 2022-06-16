<?php
/* Turn off secure file download. This was conflicting with LargeFS on wpengine */
add_filter( 'gform_secure_file_download_location', '__return_false' );

//add new Notification event of - send confirmation letter and maker cancelled exhibit
add_filter( 'gform_notification_events', 'add_events' );
function add_events( $notification_events ) {
    $notification_events['manual']                = __( 'Send Manually', 'gravityforms' );
    return $notification_events;
}
