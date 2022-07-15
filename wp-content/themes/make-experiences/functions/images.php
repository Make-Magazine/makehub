<?php
// Jetpack webp exceptions
function jetpack_exceptions() {
  if ( is_page( array(11626, 2625, 13115) ) ) {
    add_filter( 'jetpack_photon_skip_image', '__return_true' );
  }
}

add_action( 'wp', 'jetpack_exceptions' );

////////////////////////////////////////////////////////////////////
// Use Jetpack Photon if it exists, else use original photo
////////////////////////////////////////////////////////////////////

function get_resized_remote_image_url($url, $width, $height, $escape = true) {
    if (class_exists('Jetpack') && Jetpack::is_module_active('photon')) {
        $width = (int) $width;
        $height = (int) $height;
        // Photon doesn't support redirects, so help it out by doing http://foobar.wordpress.com/files/ to http://foobar.files.wordpress.com/
        if (function_exists('new_file_urls'))
            $url = new_file_urls($url);

        $thumburl = jetpack_photon_url($url, array(
            'resize' => array($width, $height),
            'strip' => 'all',
        ));
        return ($escape) ? esc_url($thumburl) : $thumburl;
    } else {
        return $url;
    }
}

function get_first_image_url($html) {
    if (preg_match('/<img.+?src="(.+?)"/', $html, $matches)) {
        return $matches[1];
    } else
        return get_stylesheet_directory_uri() . "/images/default-related-article.jpg";
}

function new_image_sizes() {
    add_image_size('grid-cropped', 300, 300, true);
    add_image_size('medium-large', 600, 600);
}
add_action('after_setup_theme', 'new_image_sizes');

// allow gutenberg full width images
function make_experiences_setup() {
  add_theme_support( 'align-wide' );
}
add_action( 'after_setup_theme', 'make_experiences_setup' );
