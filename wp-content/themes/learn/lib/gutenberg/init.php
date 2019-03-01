<?php
/**
 * Gutenberg theme support.
 *
 * @package Learn
 * @author  Maker Media
 * @license GPL-2.0+
 * @link    https://github.com/Make-Magazine/makehub
 */

add_action( 'wp_enqueue_scripts', 'learn_enqueue_gutenberg_frontend_styles' );
/**
 * Enqueues Gutenberg front-end styles.
 *
 * @since 2.7.0
 */
function learn_enqueue_gutenberg_frontend_styles() {

	wp_enqueue_style(
		'learn-gutenberg',
		get_stylesheet_directory_uri() . '/lib/gutenberg/front-end.css',
		array( 'learn' )
	);

}

add_action( 'enqueue_block_editor_assets', 'learn_block_editor_styles' );
/**
 * Enqueues Gutenberg admin editor fonts and styles.
 *
 * @since 2.7.0
 */
function learn_block_editor_styles() {

	wp_enqueue_style(
		'learn-gutenberg-fonts',
		'https://fonts.googleapis.com/css?family=Muli:200,300,300i,400,400i,600,600i|Open+Sans+Condensed:300',
		array()
	);

}

// Add support for editor styles.
add_theme_support( 'editor-styles' );

// Enqueue editor styles.
add_editor_style( '/lib/gutenberg/style-editor.css' );

// Adds support for block alignments.
add_theme_support( 'align-wide' );

// Make media embeds responsive.
add_theme_support( 'responsive-embeds' );

// Adds support for editor font sizes.
add_theme_support(
	'editor-font-sizes',
	array(
		array(
			'name'      => __( 'Small', 'learn' ),
			'shortName' => __( 'S', 'learn' ),
			'size'      => 14,
			'slug'      => 'small',
		),
		array(
			'name'      => __( 'Normal', 'learn' ),
			'shortName' => __( 'M', 'learn' ),
			'size'      => 18,
			'slug'      => 'normal',
		),
		array(
			'name'      => __( 'Large', 'learn' ),
			'shortName' => __( 'L', 'learn' ),
			'size'      => 22,
			'slug'      => 'large',
		),
		array(
			'name'      => __( 'Larger', 'learn' ),
			'shortName' => __( 'XL', 'learn' ),
			'size'      => 26,
			'slug'      => 'larger',
		),
	)
);

require_once get_stylesheet_directory() . '/lib/gutenberg/inline-styles.php';

add_theme_support(
	'editor-color-palette',
	array(
		array(
			'name'  => __( 'Custom color', 'learn' ),
			'slug'  => 'custom',
			'color' => get_theme_mod( 'learn_link_color', learn_customizer_get_default_link_color() ),
		),
		array(
			'name'  => __( 'Accent color', 'learn' ),
			'slug'  => 'accent',
			'color' => get_theme_mod( 'learn_accent_color', learn_customizer_get_default_accent_color() ),
		),
	)
);

add_action( 'after_setup_theme', 'learn_content_width', 0 );
/**
 * Set content width to match the “wide” Gutenberg block width.
 */
function learn__content_width() {

	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- See https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/924
	$GLOBALS['content_width'] = apply_filters( 'learn_content_width', 1062 );

}
