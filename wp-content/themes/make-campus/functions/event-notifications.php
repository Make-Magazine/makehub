<?php

add_filter('gform_notification_events', 'custom_notification_event');

function custom_notification_event($events) {
    $events['accepted_event_occur_48_hours'] = __('Event Occuring Within 48 Hours');
	$events['after_event'] = __('After Event');
    $events['send_manually'] = __( 'Send Manually' );
    return $events;
}

//cron job triggers this check for any accepted entries where the event is occuring in the next 48 hours. 
function trigger_notificatons() {
    global $wpdb;
    ///////////////////////////////////////////////
	/*           48 HOURS BEFORE EVENT           */
	///////////////////////////////////////////////
    $sql = 'SELECT post_id '
            . 'FROM  ' . $wpdb->prefix . 'postmeta '
            . 'left outer join ' . $wpdb->prefix . 'posts posts on (posts.id = post_id) '
            . 'WHERE  meta_key LIKE "_EventStartDate" AND '
            . '       meta_value like CONCAT("%",CURDATE() + INTERVAL 2 DAY,"%") and post_status = "publish"';
    $events = $wpdb->get_results($sql);
    foreach ($events as $event) {
        //find associated entry    
        $entry_id = $wpdb->get_var('select id from ' . $wpdb->prefix . 'gf_entry where post_id = ' . $event->post_id);
        if ($entry_id != '') {            
            $entry = GFAPI::get_entry($entry_id);            
            $form = GFAPI::get_form($entry['form_id']);

            //trigger notificaton
            // error_log('sending 48 hour notification for entry '.$entry_id);
            $notifications_to_send = GFCommon::get_notifications_to_send('accepted_event_occur_48_hours', $form, $entry);
			foreach ($notifications_to_send as $notification) {
				if(strpos($notification['to'], "{{attendee_list}}") !== false){
					$attendeeEmailList = str_replace('{{attendee_list}}', implode(',', get_event_attendee_emails($event->post_id)), $notification['to']);
					$notification['to'] = $attendeeEmailList;
				}
				GFCommon::send_notification($notification, $form, $entry);
			}
        }
	}
	///////////////////////////////////////////////
	/*                AFTER EVENT                */
	///////////////////////////////////////////////
    $sql = 'SELECT post_id '
            . 'FROM  ' . $wpdb->prefix . 'postmeta '
            . 'left outer join ' . $wpdb->prefix . 'posts posts on (posts.id = post_id) '
            . 'WHERE  meta_key LIKE "_EventEndDate" AND '
			. '       meta_value like CONCAT("%",CURDATE() + INTERVAL 1 DAY,"%") and post_status = "publish"';
    $events = $wpdb->get_results($sql);
    foreach ($events as $event) {
        //find associated entry    
        $entry_id = $wpdb->get_var('select id from ' . $wpdb->prefix . 'gf_entry where post_id = ' . $event->post_id);
        if ($entry_id != '') {            
            $entry = GFAPI::get_entry($entry_id);            
            $form = GFAPI::get_form($entry['form_id']);

            //trigger notificaton
            // error_log('sending 48 hour notification for entry '.$entry_id);
            $notifications_to_send = GFCommon::get_notifications_to_send('accepted_event_occur_48_hours', $form, $entry);
			foreach ($notifications_to_send as $notification) {
				if(strpos($notification['to'], "{{attendee_list}}") !== false){
					$attendeeEmailList = str_replace('{{attendee_list}}', implode(',', get_event_attendee_emails($event->post_id)), $notification['to']);
					$notification['to'] = $attendeeEmailList;
				}
				GFCommon::send_notification($notification, $form, $entry);
			}
        }
    }
}
