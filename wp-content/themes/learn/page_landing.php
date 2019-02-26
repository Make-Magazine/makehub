<?php
/**
 * Learn based off of Monochrome Pro.
 *
 * This file adds the landing page template to the Learn Theme.
 *
 * Template Name: Landing
 *
 * @package Learn
 * @author  Maker Media
 * @license GPL-2.0+
 * @link    https://github.com/Make-Magazine/makehub
 */

// Add landing page body class to the head.
add_filter( 'body_class', 'learn_add_body_class' );
function learn_add_body_class( $classes ) {

	$classes[] = 'landing-page';

	return $classes;

}

// Remove Skip Links.
remove_action ( 'genesis_before_header', 'genesis_skip_links', 5 );

// Dequeue Skip Links script.
add_action( 'wp_enqueue_scripts', 'learn_dequeue_skip_links' );
function learn_dequeue_skip_links() {

	wp_dequeue_script( 'skip-links' );

}

// Force full width content layout.
add_filter( 'genesis_site_layout', '__genesis_return_full_width_content' );

// Remove site header elements.
remove_action( 'genesis_header', 'genesis_header_markup_open', 5 );
remove_action( 'genesis_header', 'genesis_do_header' );
remove_action( 'genesis_header', 'genesis_header_markup_close', 15 );

// Remove navigation.
remove_theme_support( 'genesis-menus' );

// Remove breadcrumbs.
remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );

// Remove before footer CTA widget area.
remove_action( 'genesis_before_footer', 'learn_before_footer_cta' );

// Remove site footer elements.
remove_action( 'genesis_after', 'genesis_footer_markup_open', 5 );
remove_action( 'genesis_after', 'learn_before_footer', 8 );
remove_action( 'genesis_after', 'genesis_do_footer' );
remove_action( 'genesis_after', 'learn_footer_menu', 12 );
remove_action( 'genesis_after', 'genesis_footer_markup_close', 15 );

// Run the Genesis loop.
genesis();
