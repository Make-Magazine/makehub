<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package _makerspaces
 */

//* Remove primary sidebar
remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );

// add a nice little container class to our main content
add_filter( 'genesis_attr_content', 'themeprefix_primary_nav_id' );
function themeprefix_primary_nav_id( $attributes ) {
	$attributes['class'] .= ' container-fluid inner-container';
	return $attributes;
}
//* Run the Genesis loop
genesis();