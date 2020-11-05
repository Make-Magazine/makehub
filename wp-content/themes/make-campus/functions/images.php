<?php

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
