<?php

// override for the wp email function on stage/dev sites
add_filter('wp_mail', 'change_email_for_wp', 10, 2);

function change_email_for_wp($email) {
    $homeurl = get_home_url();

    //check for our stage, dev or local sites
    if (strpos($homeurl, 'wpengine.com') !== false || strpos($homeurl, 'makehub.local') !== false || strpos($homeurl, 'makehub.test') !== false) {
        /*$toEmail = str_replace(' ', '', $email['to']); //remove spaces
        $toEmailArr = explode(",", $toEmail); //put all to emails in an array
        $newTo = array('webmaster@make.co', 'dan@make.co', 'siana@make.co');
        foreach ($toEmailArr as $checkEmail) {
            $pos = strpos($checkEmail, '@make.co', -7);

            //if this email is from @make.co keep it.
            if ($pos === false) {
                $newTo[] = $checkEmail;
            }
        }*/

        $email['subject'] = 'Redirect Email sent to ' . $email['to'] . ' - ' . $email['subject'];
        //$email['to'] = implode(",", $newTo);
        error_log($email['headers']);
		$email['headers'][] = 'cc: ""';
		if(strpos($homeurl, 'makehub.local') !== false) {
			$email['to'] = 'rio@make.co';
		} else if(strpos($homeurl, 'makehub.test') !== false) {
			$email['to'] = 'alicia@make.co';
		} else {
        	$email['to'] = 'webmaster@make.co, dan@make.co, siana@make.co';
		}
    }

    return ($email);
}
