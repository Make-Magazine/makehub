<?php
/* **************************************************** */
/* Create a list of randomized gravatars                */
/* **************************************************** */

add_filter( 'pre_option_avatar_default', 'make_default_avatar' );
function make_default_avatar ( $value ){
  $avatar_dir = get_stylesheet_directory_uri() . '/images/default-avatars/';
  return $avatar_dir . "custom-avatar" . rand( 0 , 10 ).'.jpg';
}

function buddydev_set_default_avatar( $value ) {
  $avatar_dir = get_stylesheet_directory_uri() . '/images/default-avatars/';
  return $avatar_dir . "custom-avatar" . rand( 0 , 10 ).'.jpg';
}
add_filter( 'bp_core_avatar_default',   'buddydev_set_default_avatar' );

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
add_filter( 'bp_before_members_cover_image_settings_parse_args', 'set_default_cover_image', 10, 1 );

/**
 * Returns the URL to an image resized and cropped or fitted to the given dimensions.
 *
 * You can use this image URL directly -- it's cached and such by our servers.
 * Please use this function to generate the URL rather than doing it yourself as
 * this function uses staticize_subdomain() makes it serve off our CDN network.
 *
 * Somewhat contrary to the function's name, it can be used for ANY image URL, hosted by us or not.
 * So even though it says "remote", you can use it for attachments hosted by us, etc.
 *
 * @link http://vip.wordpress.com/documentation/image-resizing-and-cropping/ Image Resizing And Cropping
 * @param string $url The raw URL to the image (URLs that redirect are currently not supported with the exception of http://foobar.wordpress.com/files/ type URLs)
 * @param int $width The desired width of the final image
 * @param int $height The desired height of the final image
 * @param string $type Either 'resize' or 'fit'
 * @param bool $escape Optional. If true (the default), the URL will be run through esc_url(). Set this to false if you need the raw URL.
 * @return string
 */

function photon_image_url( $url, $width, $height, $type, $escape = true ) {
	if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'photon' ) ) {
		$width = (int) $width;
		$height = (int) $height;
		$thumburl = jetpack_photon_url($url, array(
			$type => array($width, $height),
			'strip' => 'all',
		));
		return ( $escape ) ? esc_url( $thumburl ) : $thumburl;
	}
}
