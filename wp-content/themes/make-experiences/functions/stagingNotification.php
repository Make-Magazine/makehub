<?php

// override for the wp email function on stage/dev sites
add_filter('wp_mail', 'change_email_for_wp', 10, 2);

function change_email_for_wp($email) {
    $homeurl = get_home_url();

    //check for our stage, dev or local sites
    if (strpos($homeurl, 'wpengine.com') !== false || strpos($homeurl, 'makehub.local') !== false || strpos($homeurl, 'makehub.test') !== false) {
        $email['headers']  = "Content-Type: text/html; charset=ISO-8859-1";
        $email['subject']  = 'Redirect Email sent to ' . $email['to'] . ' - ' . $email['subject'];
        $email['message'] .= '<br />This email is redirected from: '.$homeurl;
		    if(strpos($homeurl, 'makehub.local') !== false) {
            $email['to'] = 'rio@make.co';
    		} else if(strpos($homeurl, 'makehub.test') !== false) {
    			  $email['to'] = 'alicia@make.co';
    		} else {
          	$email['to'] = 'webmaster@make.co, dan@make.co';
    		}
    }

    return ($email);
}
