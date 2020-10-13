<?php

//schedule cron job to be ran every night at midnight
// register activation hook 
register_activation_hook( __FILE__, 'gf_notif_activation' );

// function for activation hook
function gf_notif() {
    // check if scheduled hook exists
    if ( !wp_next_scheduled( 'trigger_gf_notification' )) {
         // Schedules a hook
         // time() - the first time of an event to run ( UNIX timestamp format )
         // 'hourly' - recurrence ('hourly', 'twicedaily', 'daily' ) 
         // 'my_event' - the name of an action hook to execute. 
         wp_schedule_event( time(), 'hourly', 'trigger_gf_notification' );
    }
}

add_action( 'trigger_gf_notification', 'trigger_48_hour_notificatons' );

// register deactivation hook 
register_deactivation_hook(__FILE__, 'gf_notif_deactivation');

// function for deactivation hook
function gf_notif_deactivation() {
    // clear scheduled hook
    wp_clear_scheduled_hook( 'trigger_gf_notification' );
}
