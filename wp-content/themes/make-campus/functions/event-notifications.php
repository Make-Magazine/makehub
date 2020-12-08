<?php

add_filter('gform_notification_events', 'custom_notification_event');

function custom_notification_event($events) {
    $events['accepted_event_occur_48_hours'] = __('Event Occuring In 2 days');
    $events['accepted_event_occur_seven_days'] = __('Event Occuring In 7 days');
    $events['accepted_event_occur_fourteen_days'] = __('Event Occuring In 14 days');
    $events['accepted_event_occur_twenty_days'] = __('Event Occuring In 20 days');
    $events['after_event'] = __('After Event');
    $events['send_manually'] = __('Send Manually');
    $events['maker_updated_exhibit'] = __('Maker Updated Entry', 'gravityforms');
    return $events;
}

//cron job triggers this check for any accepted entries where the event is occuring in the next 48 hours. 
function trigger_notificatons() {
    global $wpdb;
    $interval_arr = array(
                        array(2,'accepted_event_occur_48_hours'),
                        array(7,'accepted_event_occur_seven_days'),
                        array(14,'accepted_event_occur_fourteen_days'),
                        array(20,'accepted_event_occur_twenty_days')
                    );
    //loop through intervals and trigger notifications
    foreach($interval_arr as $interval){
        $days = $interval[0];
        $notification = $interval[1];
        $sql = 'SELECT post_id '
                . 'FROM  ' . $wpdb->prefix . 'postmeta '
                . 'left outer join ' . $wpdb->prefix . 'posts posts on (posts.id = post_id) '
                . 'WHERE  meta_key LIKE "_EventStartDate" AND '
                . '       meta_value like CONCAT("%",CURDATE() + INTERVAL '+$days+' DAY,"%") and post_status = "publish"';
        //trigger notificaton
        build_send_notifications($notification, $sql);
    }
    
    ///////////////////////////////////////////////
    /*                AFTER EVENT                */
    ///////////////////////////////////////////////
    $sql = 'SELECT post_id '
            . 'FROM  ' . $wpdb->prefix . 'postmeta '
            . 'left outer join ' . $wpdb->prefix . 'posts posts on (posts.id = post_id) '
            . 'WHERE  meta_key LIKE "_EventEndDate" AND '
            . '       meta_value like CONCAT("%",CURDATE() + INTERVAL 1 DAY,"%") and post_status = "publish"';
    //trigger notificaton
    build_send_notifications('accepted_event_occur_48_hours', $sql);
}

function build_send_notifications($event, $sql) {
    global $wpdb;
    $events = $wpdb->get_results($sql);
    foreach ($events as $event) {
        //find associated entry    
        $entry_id = $wpdb->get_var('select id from ' . $wpdb->prefix . 'gf_entry where post_id = ' . $event->post_id);
        if ($entry_id != '') {
            $entry = GFAPI::get_entry($entry_id);
            $form = GFAPI::get_form($entry['form_id']);

            //trigger notificaton            
            $notifications_to_send = GFCommon::get_notifications_to_send($event, $form, $entry);
            foreach ($notifications_to_send as $notification) {
                if ($notification['isActive']) {
                    if (strpos($notification['to'], "{{attendee_list}}") !== false) {
                        $notification['to'] = str_replace('{{attendee_list}}', implode(',', get_event_attendee_emails($event->post_id)), $notification['to']);
                    }
                    GFCommon::send_notification($notification, $form, $entry);
                }
            }
        }
    }
}
