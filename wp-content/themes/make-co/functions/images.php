<?php
/* **************************************************** */
/* Create a list of randomized gravatars                */
/* **************************************************** */

/*
$wp_upload_dir = function_exists('wp_get_upload_dir') ? wp_get_upload_dir() : wp_upload_dir();

$avatar_list = glob("wp-content/uploads/default-avatars/*.jpg");
error_log(print_r($avatar_list, TRUE));


add_filter( 'avatar_defaults', 'make_avatars' );
function make_avatars ($avatar_defaults) {
	$myavatar = $avatar_list[0];
	$avatar_defaults[glob("wp-content/uploads/default-avatars/*.jpg")] = "Default Gravatar";
	return $avatar_defaults;
}
*/

/*
$wp_upload_dir = function_exists('wp_get_upload_dir') ? wp_get_upload_dir() : wp_upload_dir();
$avatar_dir = $upload_dir['base_url'] . '/default-avatars/';

error_log(get_stylesheet_directory_uri() . '/images/default-avatars/');
*/

add_filter( 'pre_option_avatar_default', 'make_default_avatar' );
 
function make_default_avatar ( $value ){
  $wp_upload_dir = function_exists('wp_get_upload_dir') ? wp_get_upload_dir() : wp_upload_dir();
  $avatar_dir = get_stylesheet_directory_uri() . '/images/default-avatars/';
  return $avatar_dir . "custom-avatar" . rand( 0 , 10 ).'.jpg';
}
