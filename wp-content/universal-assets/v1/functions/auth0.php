<?php
define('AUTH0_CLIENTID', "J8vRwQOxIEkUOAlxaThb3YBq0ts6Tj0k");
define('AUTH0_SECRET', "-B_rlU963aS6LbkcxaulJ-qUG8N3ULQ6idiUea3tMEfbHc5OPZMECkP5akU3oqHk");

// don't just use the auth0 email field for the wpuser name
add_filter( 'auth0_use_management_api_for_userinfo', '__return_false', 101 );

// check if user is logged in
function ajax_check_user_logged_in() {
    echo is_user_logged_in() ? 'yes' : 'no';
    die();
}
add_action('wp_ajax_is_user_logged_in', 'ajax_check_user_logged_in');
add_action('wp_ajax_nopriv_is_user_logged_in', 'ajax_check_user_logged_in');

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
