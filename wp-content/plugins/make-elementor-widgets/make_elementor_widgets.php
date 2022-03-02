<?php
/**
 * Plugin Name: Make: Elementor Widgets
 * Description: This plugin adds some common Make widgets including: Makershed purchases, Upcoming Maker Faires, Make Projects (most recent projects), Shopify (shows a list of products for sale), Fancy RSS(rss with image)
 * Version:     1.0.0
 * Author:      Alicia Williams
 * Text Domain: elementor-make-widget
 *
 * Elementor tested up to: 3.5.0
 * Elementor Pro tested up to: 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register oEmbed Widget.
 *
 * Include widget file and register widget class.
 *
 * @since 1.0.0
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 * @return void
 */
function register_shed_purchases_widget( $widgets_manager ) {
  error_log('function register_shed_purchases_widget');
	require_once( __DIR__ . '/widgets/shed-purchases-widget.php' );
	$widgets_manager->register( new \Elementor_mShedPurch_Widget() );
}
add_action( 'elementor/widgets/register', 'register_shed_purchases_widget' );

/* Add new Make: category for our widgets */
function add_elementor_widget_categories( $elements_manager ) {
  error_log('function add_elementor_widget_categories');
	$elements_manager->add_category(
		'make-category',
		[
			'title' => esc_html__( 'Make:', 'elementor-make-widget' ),
			'icon' => 'fa fa-plug',
		]
	);
}
add_action( 'elementor/elements/categories_registered', 'add_elementor_widget_categories' );
