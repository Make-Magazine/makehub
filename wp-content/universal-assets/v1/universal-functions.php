<?php

if (!defined('ABSPATH'))
    define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-load.php');

// Add that navwalker for the custom menus
require_once('lib/wp_bootstrap_navwalker.php');

function set_ajax_params(){
	//pull the style.css to retrieve the version
	$file = ABSPATH . 'wp-content/universal-assets/v1/package.json';
	// get the file contents, assuming the file to be readable (and exist)
	$contents = file_get_contents($file);
	if($contents){
		$pkg_json = json_decode($contents);
	}
	$my_version = isset($pkg_json->version)?$pkg_json->version:'1.1';

	### UNIVERSAL STYLES ###
	wp_enqueue_style( 'bootstrap', content_url() . '/universal-assets/v1/css/bootstrap-noglyphicons.min.css' );
	wp_enqueue_style('universal-firstload.css', content_url() . '/universal-assets/v1/css/universal-firstload.min.css', array(), $my_version);
	wp_enqueue_style('universal.css', content_url() . '/universal-assets/v1/css/universal.min.css', array(), $my_version);

	//auth0
	wp_enqueue_script('auth0', 'https://cdn.auth0.com/js/auth0/9.3.1/auth0.min.js', array(), false, true);
	wp_enqueue_script('universal-auth0', content_url() . '/universal-assets/v1/js/min/universal-auth0.min.js', array('auth0'), $my_version, true);
	wp_enqueue_script('universal', content_url() . '/universal-assets/v1/js/min/universal.min.js', array('auth0'), $my_version, true);

	$membershipType = "";
	if(is_user_logged_in()) {
	 	$user = wp_get_current_user();
		$membershipType = checkMakeCoMems($user);
	}

	$user_image =
		bp_core_fetch_avatar (
			array(  'item_id' => $user->ID, // id of user for desired avatar
					'object'  =>'user',
					'type'    => 'thumb',
					'html'    => FALSE     // FALSE = return url, TRUE (default) = return img html
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

// GET THE AVATAR FROM THE ROOT BLOG FOR NON BUDDYBOSS THEMES
function nfm_bp_avatar_upload_path_correct($path){
	if ( is_multisite() ){
		$path = ABSPATH . 'wp-content/uploads/';
	}
	return $path;
}
add_filter('bp_core_avatar_upload_path', 'nfm_bp_avatar_upload_path_correct', 1);

function nfm_bp_avatar_upload_url_correct($url){
	if ( is_multisite() ){
		$url = get_blog_option( BP_ROOT_BLOG, 'siteurl' ) . '/wp-content/uploads';
	}
	return $url;
}
add_filter('bp_core_avatar_url', 'nfm_bp_avatar_upload_url_correct', 1);

// Include all function files in the make-experiences/functions directory:
$function_files = glob(dirname(__FILE__) .'/functions/*.php');

$count=0;
foreach ($function_files as $file) {
	include_once $file;
}
