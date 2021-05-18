<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

add_filter( 'AHEE__EE_Registration__set_status__to_approved', 'attendee_approved', 4 );
//attendee registration approved
function attendee_approved( $registration) {            
    $attendeeID = $registration->attendee_ID();
    $eventID = $registration->event_ID();
    $attendee = EEM_Attendee::instance()->get_one_by_ID($attendeeID);
    
    //get the user information for the attendee
    $attendeeEmail = $attendee->email();
    $user = get_user_by('email', $attendeeEmail);
    
    if(!$user) {
        //create a user
        $username = strstr($attendeeEmail, '@', true); //first try username being the first part of the email
        if(username_exists( $username )){  //username exists try something else
            $count=1;
            $exists = true;
            while($exists){
                $username = $username.$count;
                if(!username_exists($username)){
                    $exists = false;
                }
                $count++;
            }
        }
        
        //generate random password, create user, send email        
        $random_password = wp_generate_password( 12, false );
        $user_id = wp_create_user( $username, $random_password, $attendeeEmail );

		$subject = 'Welcome to Maker Campus on Make: Community.';
		$my_groups = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/members/me/groups/";
		$message = 'Hello '.$attendee->fname().',<br /><br />'
                        . 'Thank you for registering for an upcoming Maker Campus program.  Included with the event ticket is a free membership to Make: Community. This is where you will find the event information, resources and community.  Please login to access Make: Community and your Maker Campus <a href="'. $my_groups .'">event groups</a>.'
                        . '<br /><br />
<b>Username:</b> ' . $username . '<br />
<b>Temporary Password:</b> ' . $random_password;
		$headers = array('Content-Type: text/html; charset=ISO-8859-1','From: Make: Community <make@make.co>');
		wp_mail( $attendeeEmail, $subject, $message, $headers );

    }else{        
        $user_id = $user->ID;
    }
    
    //add wp_EE_Attendee_ID usermeta
    //is wp_EE_Attendee_ID set?
    $havemeta = get_user_meta($user_id, 'wp_EE_Attendee_ID', true); 
    if(!$havemeta){
        $attendeeID = $attendee->get('ATT_ID');
        add_user_meta($user_id,'wp_EE_Attendee_ID',$attendeeID);
    }else{
       error_log('$havemeta='.$havemeta); 
    }
        
    // give them a free membership    
    $result = ihc_do_complete_level_assign_from_ap($user_id, 14, 0, 0);
    
    //add them to the event group    
    $group_id = get_field('group_id', $eventID);
    
    groups_join_group( $group_id, $user_id);
    
    return $registration;
}

add_filter( 'FHEE__thank_you_page_overview_template__order_conf_desc', 'confirmation_page_text', 4 );
function confirmation_page_text($order_conf_desc){    
    $order_conf_desc = 'Your registration has been successfully processed. '.                        
                        'As part of the Maker Campus experience, all registered attendees have been given a free membership to Make: Community. '.
                        'This membership provides attendees with a central hub for the workshop; material list, online webinar access, group access to connect with the facilitator, attendees, and more!  Make: Community is a great place to connect with others and find making activities online and at your local makerspace.<br/><br/>'.                      
                        'Attendees, check your email for your registration confirmation and login instructions to access your event information and benefit from the full Maker Campus experience. '.
                        'Click the button below to view / download / print a full description of your purchases and registration information.<br/><br   />';
    $order_conf_desc .=   (is_user_logged_in()?'<a class="ee-button ee-roundish indented-text big-text" href="/my/groups/">View Event Group</a>':'');
    return $order_conf_desc;
}

add_filter('FHEE__EED_Multi_Event_Registration__return_to_events_list_btn_txt','change_return_to_event_text',1);
function change_return_to_event_text($text){
    $text = 'Return to Event';
    return $text;
}