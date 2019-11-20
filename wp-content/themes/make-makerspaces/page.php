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

//* Add landing body class to the head

//* Force full width content layout
add_filter( 'genesis_site_layout', '__genesis_return_full_width_content' );
//* Remove navigation
remove_action( 'genesis_after_header', 'genesis_do_nav', 15 );
remove_action( 'genesis_footer', 'genesis_do_subnav', 7 );
//* Remove site footer widgets
remove_action( 'genesis_before_footer', 'genesis_footer_widget_areas' );
//* Remove site footer elements
remove_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
remove_action( 'genesis_footer', 'genesis_do_footer' );
remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );
//* Run the Genesis loop
genesis();