<?php
/**
 * Learn based off of Monochrome Pro.
 *
 * This file adds the pricing page template to the Learn Theme.
 *
 * Template Name: Pricing
 *
 * @package Learn
 * @author  Maker Media
 * @license GPL-2.0+
 * @link    https://github.com/Make-Magazine/makehub
 */

// Add pricing page body class to the head.
add_filter( 'body_class', 'learn_add_body_class' );
function learn_add_body_class( $classes ) {

	$classes[] = 'pricing-page';

	return $classes;

}

// Force full width content layout.
add_filter( 'genesis_site_layout', '__genesis_return_full_width_content' );

// Run the Genesis loop.
genesis();
