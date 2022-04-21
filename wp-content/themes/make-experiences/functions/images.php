<?php

// Jetpack webp exceptions
function jetpack_exceptions() {
  if ( is_page( 11626 ) ) {
    add_filter( 'jetpack_photon_skip_image', '__return_true' );
  }
}

add_action( 'wp', 'jetpack_exceptions' );
