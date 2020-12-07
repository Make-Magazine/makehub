<?php

function determine_staging() {
   // Display a message if on staging or development
    $homeurl = get_home_url();    
    if(strpos($homeurl, '.test') || strpos($homeurl, '.local')){    
        echo '<div class="stagingMsg" style="display: block; text-align: center; background: red; font-size: large;color: white; position: fixed; right 0px; z-index: 9999;">Local Site</div>';
    }elseif(strpos($homeurl, 'experiencestage.make.co') || strpos($homeurl, 'campusdev.make.co')){            
        echo '<div class="stagingMsg" style="display: block; text-align: center; background: red; font-size: large;color: white; position: fixed; right 0px; z-index: 9999;">Testing Environment</div>';
    }    
}

add_action('admin_head', 'determine_staging');
add_action('wp_head', 'determine_staging');

/*
 * Function to change the TO email for all outgoing Gravity Form emails
 */
add_filter('gform_notification', 'change_email_to', 10, 3);

function change_email_to($notification, $form, $entry) {
   $homeurl = get_home_url();
   // Check for our stage and dev sites
   if(strpos($homeurl, 'experiencestage.make.co') || strpos($homeurl, 'campusdev.make.co')){
      $notification['subject'] .= 'Email from Campus Testing - '.$notification['subject'];
      $notification['toType'] = 'email';
      $notification['to'] = 'webmaster@make.co,siana@make.co';
      $notification['from'] = 'staging@make.co';
      if (isset($notification['bcc'])) $notification['bcc'] = '';
   } elseif (strpos($homeurl, '.test') !== false||strpos($homeurl, '.local') !== false) {
      // Check for local sites
      if (defined('MF_OVERRITE_EMAIL')) {
         $notification['toType'] = 'email';
         $notification['to'] = MF_OVERRITE_EMAIL;
         if (isset($notification['bcc'])) $notification['bcc'] = '';
      }
   }
   
   return $notification;
   
}
