<?php
/**
 * Make - Base Theme
 *
 * This file adds the landing page template to the Make - Base Theme.
 *
 * Template Name: Page Full Width
 *
 * @package Make - Base
 * @author  Make Community
 * @license GPL-2.0-or-later
 * @link    https://make.co/
 */

add_filter( 'body_class', 'make_base_add_body_class' );
/**
 * Adds landing page body class.
 *
 * @since 1.0.0
 *
 * @param array $classes Original body classes.
 * @return array Modified body classes.
 */
function make_base_add_body_class( $classes ) {

	$classes[] = 'landing-page';
	return $classes;

}

// Removes Skip Links.
remove_action( 'genesis_before_header', 'genesis_skip_links', 5 );

add_action( 'wp_enqueue_scripts', 'make_base_dequeue_skip_links' );
/**
 * Dequeues Skip Links Script.
 *
 * @since 1.0.0
 */
function make_base_dequeue_skip_links() {

	wp_dequeue_script( 'skip-links' );

}

// Forces full width content layout.
add_filter( 'genesis_site_layout', '__genesis_return_full_width_content' );

// Removes site header elements.
remove_action( 'genesis_header', 'genesis_header_markup_open', 5 );
remove_action( 'genesis_header', 'genesis_do_header' );
remove_action( 'genesis_header', 'genesis_header_markup_close', 15 );

// Removes navigation.
remove_theme_support( 'genesis-menus' );

// Removes breadcrumbs.
remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );

// Removes footer widgets.
remove_action( 'genesis_before_footer', 'genesis_footer_widget_areas' );

// Removes site footer elements.
remove_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
remove_action( 'genesis_footer', 'genesis_do_footer' );
remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );

// Runs our custom loop instead of the Genesis Loop
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'makebase_panels_loop' );

function makebase_panels_loop() { ?>
<div class="container-fluid content-panels">
   <?php if(have_posts()) : while(have_posts()) : the_post(); ?>
	
	<div class="container-fluid"> 
      <?php the_content(); ?>
   </div>
	
	<?php endwhile; ?>
   <?php endif; ?>
	
</div>
<?php }
genesis();


