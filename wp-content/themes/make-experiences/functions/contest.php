<?php

// make order of projects library random
add_filter( 'posts_orderby', 'randomise_with_pagination' );
function randomise_with_pagination( $orderby ) {
	if( is_page("Amazing Maker Awards Contestants") ) { // Amazing Maker Awards Contestants is the title for the AMA gallery
	  	// Reset seed on load of initial archive page
		if( ! get_query_var( 'paged' ) || get_query_var( 'paged' ) == 0 || get_query_var( 'paged' ) == 1 ) {
			if( isset( $_SESSION['seed'] ) ) {
				unset( $_SESSION['seed'] );
			}
		}
		// Get seed from session variable if it exists
		$seed = false;
		if( isset( $_SESSION['seed'] ) ) {
			$seed = $_SESSION['seed'];
		}
    	// Set new seed if none exists
    	if ( ! $seed ) {
      		$seed = rand();
      		$_SESSION['seed'] = $seed;
    	}
    	// Update ORDER BY clause to use seed
    	$orderby = 'RAND(' . $seed . ')';
	}
	return $orderby;
}
