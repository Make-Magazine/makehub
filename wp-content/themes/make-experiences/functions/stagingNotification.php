<?php

// override for the wp email function on stage/dev sites
add_filter('wp_mail', 'change_email_for_wp', 10, 2);

function change_email_for_wp($email) {
   $homeurl = get_home_url();
   
   //check for our stage and dev sites
   if(strpos($homeurl,'wpengine.com')!==false){            
       $email['subject'] = 'Redirect Email sent to '.$email['to'].' - '.$email['subject'];              
       $email['to'] = 'webmaster@make.co, dan@make.co, siana@make.co';
   }
   
   return ($email);
   
}
