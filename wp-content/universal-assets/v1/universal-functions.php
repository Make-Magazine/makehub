<?php

if (!defined('ABSPATH'))
    define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-load.php');

define('AUTH0_CLIENTID', "NDw7r6YLomyGceVgG7PIt2wIhIgLNqxG");
define('AUTH0_SECRET', "4dfAi4LjuknqDkzXILwK13vARSBiZIdB-XTHAErTk7QthdRcjF-5w3-AmYfh6eUT");

// Can we load the universal scripts this way
function universal_scripts() {
    //auth0
    wp_enqueue_script('auth0', 'https://cdn.auth0.com/js/auth0/9.3.1/auth0.min.js', array(), false, true);
}
add_action('wp_enqueue_scripts', 'universal_scripts', 10, 2);

// check if user is logged in
function ajax_check_user_logged_in() {
    echo is_user_logged_in() ? 'yes' : 'no';
    die();
}
add_action('wp_ajax_is_user_logged_in', 'ajax_check_user_logged_in');
add_action('wp_ajax_nopriv_is_user_logged_in', 'ajax_check_user_logged_in');

// Add that navwalker for the custom menus
require_once('lib/wp_bootstrap_navwalker.php');

// check if user is a user, but isn't a user of the current blog, and add them if they aren't
function create_user_on_blog($user_object) {
	$blog_id = get_current_blog_id();
   	if ( $blog_id == 3 || $blog_id == 3 ) {
	    $user_id = $user_object->ID;
	    $blog_id = get_current_blog_id();
	    if ($user_id && !is_user_member_of_blog($user_id, $blog_id)) {
	        add_user_to_blog($blog_id, $user_id, "subscriber");
	    }
	}
}
add_action('auth0_before_login', 'create_user_on_blog', 10, 6);

/** Set up the Ajax WP Logout */
function MM_wordpress_logout() {
    //check_ajax_referer( 'ajax-logout-nonce', 'ajaxsecurity' );
    wp_logout();
    ob_clean(); // probably overkill for this, but good habit
    wp_send_json_success();
}
add_action('wp_ajax_mm_wplogout', 'MM_wordpress_logout');
add_action('wp_ajax_nopriv_mm_wplogout', 'MM_wordpress_logout');

/** Set up the Ajax WP Login */
function MM_WPlogin() {
    //check_ajax_referer( 'ajax-login-nonce', 'ajaxsecurity' );
    global $wpdb; // access to the database

    //use auth0 plugin to log people into wp
    $a0_plugin = new WP_Auth0_InitialSetup(WP_Auth0_Options::Instance());
    $a0_options = WP_Auth0_Options::Instance();
    $users_repo = new WP_Auth0_UsersRepo($a0_options);
    $login_manager = new WP_Auth0_LoginManager($users_repo, $a0_options);

    //get the user information passed from auth0
    $userinput = filter_input_array(INPUT_POST);
    $userinfo = (object) $userinput['auth0_userProfile'];
    $userinfo->email_verified = true;
    $access_token = filter_input(INPUT_POST, 'auth0_access_token', FILTER_SANITIZE_STRING);
    $id_token = filter_input(INPUT_POST, 'auth0_id_token', FILTER_SANITIZE_STRING);

    if ($login_manager->login_user($userinfo, $id_token, $access_token)) {
        /* $blog_id = get_current_blog_id(); // this only triggered for a user logged into another site to begin with
          $user_id = username_exists( sanitize_text_field( $userinfo->nickname ) );
          if ( $user_id && ! is_user_member_of_blog( $user_id, $blog_id ) ) {
          add_user_to_blog( $blog_id, $user_id, "subscriber" );
          } */
        wp_send_json_success();
    } else {
        error_log('Failed login');
        error_log(print_r($userinput, TRUE));
        wp_send_json_error();
    }
}
add_action('wp_ajax_mm_wplogin', 'MM_WPlogin');
add_action('wp_ajax_nopriv_mm_wplogin', 'MM_WPlogin');

// Write to the php error log by request
function make_error_log() {
    $error = filter_input(INPUT_POST, 'make_error', FILTER_SANITIZE_STRING);
    error_log(print_r($error, TRUE));
}
add_action('wp_ajax_make_error_log', 'make_error_log');
add_action('wp_ajax_nopriv_make_error_log', 'make_error_log');

function randomString() {
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
    return substr(str_shuffle($permitted_chars), 0, 10);
}

function timezone_abbr_from_name($timezone_name) {
    $dateTime = new DateTime();
    $dateTime->setTimeZone(new DateTimeZone($timezone_name));
    return $dateTime->format('T');
}
add_action('admin_bar_menu', 'toolbar_link_to_mypage', 999);

function toolbar_link_to_mypage($wp_admin_bar) {
    $args = [
        'id' => 'wp-submit-asana-bug',
        'title' => '<span class="wp-menu-image dashicons-before dashicons-buddicons-replies"></span>' . 'Report a Bug',
        'meta' => array('target' => '_blank'),
        'href' => 'https://form.asana.com/?hash=936d55d2283dea9fe2382a75e80722675681f3881416d93f7f75e8a4941c6d47&id=1149238253861292',
    ];
    $wp_admin_bar->add_menu($args);
}

function get_tag_ID($tag_name) {
	$tag = get_term_by('name', $tag_name, 'post_tag');
	if ($tag) {
		return $tag->term_id;
	} else {
		return 0;
	}
}

function add_universal_body_classes($classes) {
    if ( defined('EVENT_ESPRESSO_VERSION') ){
        $classes[] = "event-espresso";
        return $classes;
    }
}
add_filter('body_class', 'add_universal_body_classes');

// don't just use the auth0 email field for the wpuser name
add_filter( 'auth0_use_management_api_for_userinfo', '__return_false', 101 );


/**
 * Eliminate some of the default admin list columns that squish the title
 */
 add_action( 'current_screen', function( $screen ) {
 	if ( ! isset( $screen->id ) ) return;
 	add_filter( "manage_{$screen->id}_columns", 'remove_default_columns', 99 );
 } );

 function remove_default_columns( $columns ) {
	unset($columns['essb_shares'], $columns['essb_shareinfo']);
 	return $columns;
 }


/* This allows us to send elementor styled pages to other blogs */
add_action("rest_api_init", function () {
    register_rest_route(
        "elementor/v1"
        , "/pages/(?P<id>\d+)/contentElementor"
        , [
            "methods" => "GET",
            'permission_callback' => '__return_true',
            "callback" => function (\WP_REST_Request $req) {

            $contentElementor = "";

            if (class_exists("\\Elementor\\Plugin")) {
                $post_ID = $req->get_param("id");

                $pluginElementor = \Elementor\Plugin::instance();
                $contentElementor = $pluginElementor->frontend->get_builder_content($post_ID);
            }


            return $contentElementor;
            },
            ]
        );
});

// alphabetize menu items
function sort_admin_menu() {
	if(is_admin()) {
	    global $menu;
	    // alphabetize submenu items
		if($menu) {
		    usort( $menu, function ( $a, $b ) {
		        if(isset($a['5']) && $a[5]!='menu-dashboard'){
		          // format of a submenu item is [ 'My Item', 'read', 'manage-my-items', 'My Item' ]
		          return strcasecmp( strip_tags($a[0]), strip_tags($b[0]) );
		        }
		    } );
		    //remove separators
		    $menu = array_filter($menu, function($item) {
		        return $item[0] != '';
		    });
		}
	}
}
add_action('admin_init','sort_admin_menu');

add_action('elementor/widgets/widgets_registered', function( $widget_manager ){
	$widget_manager->unregister_widget_type('form');
	$widget_manager->unregister_widget_type('uael-table-of-contents');
	$widget_manager->unregister_widget_type('uael-registration-form');
	$widget_manager->unregister_widget_type('uael-login-form');
	$widget_manager->unregister_widget_type('wp-widget-members-widget-login');
	$widget_manager->unregister_widget_type('uael-gf-styler');
}, 15);

//curl functionality
function basicCurl($url, $headers = null) {
    $ch = curl_init();
    //curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
    //curl_setopt($ch, CURLOPT_STDERR, $verbose = fopen('php://temp', 'rw+'));
    curl_setopt($ch, CURLOPT_URL, $url);
    if ($headers != null) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

	  if (strpos(CURRENT_URL, '.local') > -1 || strpos(CURRENT_URL, '.test') > -1 ) { // wpengine local environments
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $data = curl_exec($ch);

    //echo "Verbose information:\n", !rewind($verbose), stream_get_contents($verbose), "\n";
    curl_close($ch);
    return $data;
}

function postCurl($url, $headers = null, $datastring = null,$type="POST") {
	$ch = curl_init($url);

	if (strpos(CURRENT_URL, '.local') > -1  || strpos(CURRENT_URL, '.test') > -1) { // wpengine local environments
	  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	}

  //curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
  //curl_setopt($ch, CURLOPT_STDERR, $verbose = fopen('php://temp', 'rw+'));

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);

	if($datastring != null) {
		curl_setopt($ch, CURLOPT_POSTFIELDS, $datastring);
	}

	if ($headers != null) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	}

	$response = curl_exec($ch);

  //echo "Verbose information:\n", !rewind($verbose), stream_get_contents($verbose), "\n";
	if(curl_errno($ch)){
	  throw new Exception(curl_error($ch));
	}

	curl_close($ch);
  return $response;
}

/* This function will check if user is a premium member, non member or eligible for upgrade */
function checkForUpgrade($memberArray) {
  $membershipType = '';
  if(isset($memberArray->active_memberships)) {
    //create an array of memberships using the title field
    $memArray = array_column($memberArray->active_memberships, 'title');

    if(!empty($memArray)){
      //look for the needle in any part of the title field in the multi level array
      if(array_find('premium', $memArray, 'title') !== false ||
         array_find('multi-seat', $memArray, 'title') !== false ||
         array_find('school maker faire', $memArray, 'title') !== false){
        //Premium Membership
        $membershipType = "premium";
      }else{
        //free membership, upgrade now
        $membershipType = "upgrade";
      }
    }
  }else{
    $membershipType = "none";
  }
  return $membershipType;
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

 /* Used to set the memberpress headers for the api call */
 function setMemPressHeaders($datastring = null) {
 	$headers = array();
 	$headers[] = 'MEMBERPRESS-API-KEY: apXPTMEf4O'; // Your API KEY from MemberPress Developer Tools Here -- 0n8p2YkomO for local apXPTMEf4O for prod
 	$headers[] = 'Content-Type: application/json';
 	if($datastring){
 		$headers[] = 'Content-Length: ' . strlen($datastring);
 	}
 	return $headers;
 }

function set_ajax_params(){
  $my_version = '1.1'; //default
  //pull the style.css to retrieve the version
  $file = ABSPATH . 'wp-content/universal-assets/v1/style.css';
  $searchfor = 'Version:';

  // get the file contents, assuming the file to be readable (and exist)
  $contents = file_get_contents($file);
  // escape special characters in the query
  $pattern = preg_quote($searchfor, '/');
  // finalise the regular expression, matching the whole line
  $pattern = "/^.*$pattern.*\$/m";
  // search, and store all matching occurences in $matches
  if(preg_match_all($pattern, $contents, $matches)){
    $my_version = str_replace($searchfor, '', implode("\n", $matches[0]));
    $my_version = str_replace(' ', '', $my_version);
  }

  wp_enqueue_script('universal', content_url() . '/universal-assets/v1/js/min/universal.min.js', array(), $my_version, true);

  $user = wp_get_current_user();

  $headers = setMemPressHeaders();
  $memberInfo = basicCurl("https://make.co/wp-json/mp/v1/members/".$user->ID, $headers);
  $memberArray = json_decode($memberInfo);
  $membershipType = checkForUpgrade($memberArray);

  $user_image =
        bp_core_fetch_avatar (
            array(  'item_id' => $user->ID, // id of user for desired avatar
                    'type'    => 'full',
                    'html'   => FALSE     // FALSE = return url, TRUE (default) = return img html
            )
        );

  //set the ajax parameters
  wp_localize_script('universal', 'ajax_object',
          array(
              'ajax_url' => admin_url('admin-ajax.php'),
              'home_url' => get_home_url(),
              'logout_nonce' => wp_create_nonce('ajax-logout-nonce'),
              'wp_user_email' => $user->user_email,
              'wp_user_nicename' => $user->user_nicename,
              'wp_user_avatar' => $user_image,
              'wp_user_memlevel' => $membershipType
          )
  );
}

add_action('wp_enqueue_scripts', 'set_ajax_params', 9999);

function set_universal_asset_constants() {
	if (isset($_SERVER['HTTPS']) &&
	    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
	    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
	    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
	  		$protocol = 'https://';
	} else {
		$protocol = 'http://';
	}
  // Set the important bits as CONSTANTS that can easily be used elsewhere
  define('CURRENT_URL', $protocol . $_SERVER['HTTP_HOST']);
  define('CURRENT_POSTID', url_to_postid( CURRENT_URL . $_SERVER[ 'REQUEST_URI' ]));

	// Decide if user can upgrade
  $user = wp_get_current_user();
  $headers = setMemPressHeaders();
  $memberInfo = basicCurl("https://make.co/wp-json/mp/v1/members/".$user->ID, $headers);
  $memberArray = json_decode($memberInfo);
  $membershipType = checkForUpgrade($memberArray);

  $canUpgrade = ($membershipType=='upgrade' ? TRUE:FALSE);
  $hasMembership = ($membershipType=='none' ? TRUE:FALSE);
  $currentMemberships = array_column($memberArray->active_memberships, 'title');

	define('CURRENT_MEMBERSHIPS', $currentMemberships);
	define('IS_MEMBER', $hasMembership);
	define('CAN_UPGRADE', $canUpgrade);
}

set_universal_asset_constants();
