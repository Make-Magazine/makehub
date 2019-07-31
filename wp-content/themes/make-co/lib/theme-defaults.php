<?php
/**
 * Make - Community Theme
 *
 * This file adds the default theme settings to the Make - Community Theme.
 *
 * @package Make - Co
 * @author  Maker Media
 * @license GPL-2.0-or-later
 * @link    https://makermedia.com/
 */

add_filter( 'genesis_theme_settings_defaults', 'make_co_theme_defaults' );
/**
 * Updates theme settings on reset.
 *
 * @since 2.2.3
 *
 * @param array $defaults Original theme settings defaults.
 * @return array Modified defaults.
 */
function make_co_theme_defaults( $defaults ) {

	$defaults['blog_cat_num']              = 6;
	$defaults['breadcrumb_front_page']     = 0;
	$defaults['content_archive']           = 'full';
	$defaults['content_archive_limit']     = 0;
	$defaults['content_archive_thumbnail'] = 0;
	$defaults['posts_nav']                 = 'numeric';
	$defaults['site_layout']               = 'content-sidebar';

	return $defaults;

}

add_action( 'after_switch_theme', 'make_co_theme_setting_defaults' );
/**
 * Updates theme settings on activation.
 *
 * @since 2.2.3
 */
function make_co_theme_setting_defaults() {

	if ( function_exists( 'genesis_update_settings' ) ) {

		genesis_update_settings(
			array(
				'blog_cat_num'              => 6,
				'breadcrumb_front_page'     => 0,
				'content_archive'           => 'full',
				'content_archive_limit'     => 0,
				'content_archive_thumbnail' => 0,
				'posts_nav'                 => 'numeric',
				'site_layout'               => 'content-sidebar',
			)
		);

	}

	update_option( 'posts_per_page', 6 );

}

add_filter( 'simple_social_default_styles', 'make_co_social_default_styles' );
/**
 * Set Simple Social Icon defaults.
 *
 * @since 1.0.0
 *
 * @param array $defaults Social style defaults.
 * @return array Modified social style defaults.
 */
function make_co_social_default_styles( $defaults ) {

	$args = array(
		'alignment'              => 'alignleft',
		'background_color'       => '#f5f5f5',
		'background_color_hover' => '#333333',
		'border_radius'          => 3,
		'border_width'           => 0,
		'icon_color'             => '#333333',
		'icon_color_hover'       => '#ffffff',
		'size'                   => 40,
	);

	$args = wp_parse_args( $args, $defaults );

	return $args;

}
