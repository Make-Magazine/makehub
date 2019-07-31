<?php
/**
 * Make - Community Theme
 *
 * This file adds the landing page template to the Make - Community Theme.
 *
 * Template Name: Landing
 *
 * @package Make - Co
 * @author  Maker Media
 * @license GPL-2.0-or-later
 * @link    https://makermedia.com/
 */

add_filter( 'body_class', 'make_co_add_body_class' );
/**
 * Adds landing page body class.
 *
 * @since 1.0.0
 *
 * @param array $classes Original body classes.
 * @return array Modified body classes.
 */
function make_co_add_body_class( $classes ) {

	$classes[] = 'landing-page';
	return $classes;

}

// Removes Skip Links.
remove_action( 'genesis_before_header', 'genesis_skip_links', 5 );

add_action( 'wp_enqueue_scripts', 'make_co_dequeue_skip_links' );
/**
 * Dequeues Skip Links Script.
 *
 * @since 1.0.0
 */
function make_co_dequeue_skip_links() {

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
add_action( 'genesis_loop', 'learn_panels_loop' );

function learn_panels_loop() { ?>
<div class="container-fluid content-panels">
   <?php if(have_posts()) : while(have_posts()) : the_post(); ?>
	
	<?php 
	/* <div class="container"> If we wanted to display any straight content, which I see no need for at this time
      <?php the_content(); ?>
   </div> */ 
	?>
	
   <?php
    // check if the flexible content field has rows of data
    if( have_rows('content_panels')) {
      // loop through the rows of data
      while ( have_rows('content_panels') ) {
        the_row();
        $row_layout = get_row_layout();
		  //error_log($row_layout);
        echo dispLayout($row_layout);
      }
    }
   ?>
	
	<?php endwhile; ?>
   <?php endif; ?>
	
</div>
<?php }
genesis();


