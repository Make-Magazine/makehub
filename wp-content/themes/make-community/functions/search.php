<?php

// make order of search and filter default to random
add_filter( 'posts_orderby', 'randomise_with_pagination' );
function randomise_with_pagination( $orderby ) {
	$post = is_singular() ? get_queried_object() : false;
	if ( ! empty($post) && is_a($post, 'WP_Post') ) {
		if( is_page_template('page-search.php') && $_GET['sort_order'] != "id+desc" ) { // Amazing Maker Awards Contestants is the title for the AMA gallery
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
	}
	return $orderby;
}
