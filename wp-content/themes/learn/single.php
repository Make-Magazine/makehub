<?php
/**
 * Learn based off of Monochrome Pro.
 *
 * This file adds the single post template to the Learn Theme.
 *
 * @package Learn
 * @author  Maker Media
 * @license GPL-2.0+
 * @link    https://github.com/Make-Magazine/makehub
 */

// Add body class if post has featured image.
add_filter( 'body_class', 'learn_body_class_post' );
function learn_body_class_post( $classes ) {

	if ( has_post_thumbnail() ) {
		$classes[] = 'featured-image';
	}

	return $classes;

}

// Enqueue Backestretch scripts.
add_action( 'wp_enqueue_scripts', 'learn_enqueue_backstretch_post' );
function learn_enqueue_backstretch_post() {

	if ( has_post_thumbnail() ) {

		wp_register_script( 'learn-backstretch', get_stylesheet_directory_uri() . '/js/backstretch.js', array( 'jquery' ), '1.0.0', true );
		wp_register_script( 'learn-backstretch-set', get_stylesheet_directory_uri() . '/js/backstretch-set.js', array( 'jquery', 'learn-backstretch' ), '1.0.0', true );

	}

}

// Run functions if post has featured image and full-width content layout.
add_action( 'genesis_before', 'learn_setup_full_width' );
function learn_setup_full_width() {

	$run = genesis_site_layout() === 'full-width-content' && has_post_thumbnail();

	if ( ! $run ) {
		return;
	}

	// Localize Backstretch script.
	add_action( 'genesis_after', 'learn_set_background_image_post' );
	function learn_set_background_image_post() {

		wp_enqueue_script( 'learn-backstretch' );
		wp_enqueue_script( 'learn-backstretch-set' );

		$image = array( 'src' => has_post_thumbnail() ? genesis_get_image( array( 'format' => 'url' ) ) : '' );
		wp_localize_script( 'learn-backstretch-set', 'BackStretchImg', $image );

	}

	// Hook entry background area.
	add_action( 'genesis_after_header', 'learn_entry_background_post' );
	function learn_entry_background_post() {

		echo '<div class="entry-background"></div>';

	}

	// Output Gravatar before entry title.
	add_action( 'genesis_entry_header', 'learn_gravatar_post', 7 );
	function learn_gravatar_post() {

		echo '<div class="entry-avatar">';
		echo get_avatar( get_the_author_meta( 'user_email' ), 110 );
		echo '</div>';

	}

}

// Add entry meta in entry footer.
add_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
add_action( 'genesis_entry_footer', 'genesis_post_meta' );
add_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );

// Run the Genesis loop.
genesis();
