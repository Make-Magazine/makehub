<?php
/* **************************************************** */
/* Create a list of randomized gravatars                */
/* **************************************************** */

add_filter( 'pre_option_avatar_default', 'make_default_avatar' );
 
function make_default_avatar ( $value ){
  $wp_upload_dir = function_exists('wp_get_upload_dir') ? wp_get_upload_dir() : wp_upload_dir();
  $avatar_dir = get_stylesheet_directory_uri() . '/images/default-avatars/';
  return $avatar_dir . "custom-avatar" . rand( 0 , 10 ).'.jpg';
}

function buddydev_set_default_avatar( $value ) {
  $wp_upload_dir = function_exists('wp_get_upload_dir') ? wp_get_upload_dir() : wp_upload_dir();
  $avatar_dir = get_stylesheet_directory_uri() . '/images/default-avatars/';
  return $avatar_dir . "custom-avatar" . rand( 0 , 10 ).'.jpg';
}
add_filter( 'bp_core_avatar_default',   'buddydev_set_default_avatar' );
