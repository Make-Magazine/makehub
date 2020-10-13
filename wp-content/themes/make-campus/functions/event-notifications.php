<?php

add_filter('gform_notification_events', 'custom_notification_event');

function custom_notification_event($events) {
    $events['accepted_event_occur_48_hours'] = __('Event Occuring Within 48 Hours');
    //$events['event_rejected'] = __( 'Event Entry Rejected' );
    return $events;
}

//cron job triggers this check for any accepted entries where the event is occuring in the next 48 hours. 
function trigger_48_hour_notificatons() {
    global $wpdb;
    //find all accepted entries where start date is 2 days from now
    $sql = 'SELECT post_id '
            . 'FROM  ' . $wpdb->prefix . 'postmeta '
            . 'left outer ' . $wpdb->prefix . 'posts posts on (posts.id = post_id) '
            . 'WHERE  meta_key LIKE "_EventStartDate" AND '
            . '       meta_value like CONCAT("%",CURDATE() + INTERVAL 2 DAY,"%") and post_status = "publish"';
    $events = $wpdb->get_results($sql);
    foreach ($events as $event) {
        //find associated entry    
        $entry_id = $wpdb->get_var('select id from ' . $wpdb->prefix . 'gf_entry where post_id = ' . $event['post_id']);
        $entry = GFAPI::get_entry($entry_id);
        $form = GFAPI::get_form($entry['form_id']);

        //trigger notificaton
        $notifications_to_send = GFCommon::get_notifications_to_send('accepted_event_occur_48_hours', $form, $lead);
        foreach ($notifications_to_send as $notification) {
            if ($notification['isActive']) {
                GFCommon::send_notification($notification, $form, $lead);
            }
        }
    }
}

//schedule cron job to be ran every night at midnight
// register activation hook 
register_activation_hook( __FILE__, 'example_activation' );

// function for activation hook
function example_activation() {
    // check if scheduled hook exists
    if ( !wp_next_scheduled( 'my_event' )) {
         // Schedules a hook
         // time() - the first time of an event to run ( UNIX timestamp format )
         // 'hourly' - recurrence ('hourly', 'twicedaily', 'daily' ) 
         // 'my_event' - the name of an action hook to execute. 
         wp_schedule_event( time(), 'hourly', 'my_event' );
    }
}

add_action( 'my_event', 'do_this_hourly' );

// the code of your hourly event
function do_this_hourly() {
   // put your code here
}

// register deactivation hook 
register_deactivation_hook(__FILE__, 'example_deactivation');

// function for deactivation hook
function example_deactivation() {
    // clear scheduled hook
    wp_clear_scheduled_hook( 'my_event' );
}