<?php
/* **************************************************** */
/* Create a list of randomized gravatars                */
/* **************************************************** */

add_filter( 'pre_option_avatar_default', 'make_default_avatar' );
function make_default_avatar ( $value ){
  $avatar_dir = get_stylesheet_directory_uri() . '/images/default-avatars/';
  return $avatar_dir . "custom-avatar" . rand( 0 , 10 ).'.jpg';
}
/*
function buddydev_set_default_avatar( $value ) {
  $avatar_dir = get_stylesheet_directory_uri() . '/images/default-avatars/';
  return $avatar_dir . "custom-avatar" . rand( 0 , 10 ).'.jpg';
}
add_filter( 'bp_core_avatar_default',   'buddydev_set_default_avatar' );
*/

// Register the Cover Image feature for Users profiles, this probably only occurs when registering new users or groups
function bp_default_register_feature() {
    $components = array( 'groups', 'xprofile');
 
    // Define the feature's settings
    $cover_image_settings = array(
        'name'     => 'cover_image', // feature name
        'settings' => array(
            'components'   => $components,
            'width'        => 940,
            'height'       => 225,
            'callback'     => 'bp_default_cover_image',
            'theme_handle' => 'bp-default-main',
        ),
    );
    // Register the feature for your theme according to the defined settings.
    bp_set_theme_compat_feature( bp_get_theme_compat_id(), $cover_image_settings );
}
add_action( 'bp_after_setup_theme', 'bp_default_register_feature' );

function set_default_cover_image( $settings = array() ) {
	$cover_dir = get_stylesheet_directory_uri() . '/images/default-cover-images/';
   $settings['default_cover'] = $cover_dir . "default-cover-image" . rand( 0 , 0 ).'.jpg';
   return $settings;
}
add_filter( 'bp_before_xprofile_cover_image_settings_parse_args', 'set_default_cover_image', 10, 1 );