<?php

// override for the wp email function on stage/dev sites
add_filter('wp_mail', 'change_email_for_wp', 99, 2);

function change_email_for_wp($email) {
    $homeurl = get_home_url();

    //check for our stage, dev or local sites
    if (strpos($homeurl, 'devmakehub') !== false || strpos($homeurl, 'wpengine.com') !== false || strpos($homeurl, 'makehub.local') !== false || strpos($homeurl, 'makehub.test') !== false) {
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


/**
 * Check if Website is visible to Search Engines
 */
function wpse_check_visibility() {
  error_log('in function');
    // if the worpress site is set public AND we are on a staging or dev environment,
    //        set it to not public
    // this is to discourage search engines from crawling our dev and stage sites
    $homeurl = get_home_url();
    error_log('homeurl='.$homeurl);
    //check for our stage, dev or local sites
    if (strpos($homeurl, 'devmakehub')   !== false ||
        strpos($homeurl, 'wpengine.com') !== false) {
      error_log('we are on a dev/stage site');
      error_log('blog id is '.get_current_blog_id());
      error_log('blog public is '.get_option( 'blog_public'));
      // Public blogs have a setting of 1, private blogs are 0.
      if ( get_option( 'blog_public') != '0' ) {
          update_blog_public(1,0);
          error_log('after update, blog public is '.get_option( 'blog_public'));
      }
    }

}
add_action( 'admin_init', 'wpse_check_visibility' );
