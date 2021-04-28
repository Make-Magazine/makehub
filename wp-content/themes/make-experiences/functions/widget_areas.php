<?php
/*-----------------------------------------------------------------------------------------------------------------------*/
/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function experiences_widgets_init() {
	/**
	 * Register archive top widget area
	 *
	 * @since 1.0.0
	 */
	register_sidebar( array(
		'name'          => esc_html__( 'Event Listing Sidebar', 'experiences' ),
		'id'            => 'event_listing_sidebar',
		'description'   => esc_html__( 'Add widgets here.', 'experiences' ),
		'before_widget' => '<div id="secondary" class="widget-area sm-grid-1-1">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="mx-block-title">',
		'after_title'   => '</h4>',
	) );

}
add_action( 'widgets_init', 'experiences_widgets_init' );