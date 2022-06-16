<?php
function set_ajax_params(){
  //pull the style.css to retrieve the version
  $file = ABSPATH . 'wp-content/universal-assets/v1/package.json';
  // get the file contents, assuming the file to be readable (and exist)
  $contents = file_get_contents($file);
  if($contents){
    $pkg_json = json_decode($contents);
  }
  $my_version = isset($pkg_json->version)?$pkg_json->version:'1.1';

  //auth0
  wp_enqueue_script('auth0', 'https://cdn.auth0.com/js/auth0/9.3.1/auth0.min.js', array(), false, true);
  wp_enqueue_script('universal', content_url() . '/universal-assets/v1/js/min/universal.min.js', array('auth0'), $my_version, true);

  $user = wp_get_current_user();
  $membershipType = checkMakeCoMems($user);

  $user_image =
        bp_core_fetch_avatar (
            array(  'item_id' => $user->ID, // id of user for desired avatar
                    'object'=>'user',
                    'type'    => 'thumb',
                    'html'   => FALSE     // FALSE = return url, TRUE (default) = return img html
            )
        );

  $last_name  = get_user_meta( $user->ID, 'last_name', true );
  $first_name = get_user_meta( $user->ID, 'first_name', true );

  //set the ajax parameters
  wp_localize_script('universal', 'ajax_object',
          array(
              'ajax_url' => admin_url('admin-ajax.php'),
              'home_url' => get_home_url(),
              'logout_nonce' => wp_create_nonce('ajax-logout-nonce'),
              'wp_user_email' => $user->user_email,
              'wp_user_nicename' => $first_name.' '.$last_name,
              'wp_user_avatar' => $user_image,
              'wp_user_memlevel' => $membershipType
          )
  );
}

add_action('wp_enqueue_scripts', 'set_ajax_params', 9999);

function randomString() {
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
    return substr(str_shuffle($permitted_chars), 0, 10);
}

function timezone_abbr_from_name($timezone_name) {
    $dateTime = new DateTime();
    $dateTime->setTimeZone(new DateTimeZone($timezone_name));
    return $dateTime->format('T');
}

function get_tag_ID($tag_name) {
	$tag = get_term_by('name', $tag_name, 'post_tag');
	if ($tag) {
		return $tag->term_id;
	} else {
		return 0;
	}
}

/**
 *  Case in-sensitive array_search() with partial matches
 */
 function array_find($needle, array $haystack) {
   foreach ($haystack as $key => $value) {
      if (false !== stripos($value, $needle)) {
           return $key;
       }
   }
   return false;
 }
