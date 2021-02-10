<?php
/**
 * Make - Base Theme
 *
 * This file adds the Customizer additions to the Make - Base Theme.
 *
 * @package Make - Base
 * @author  Make Community
 * @license GPL-2.0-or-later
 * @link    https://make.co/
 */

add_action( 'customize_register', 'make_base_customizer_register' );
/**
 * Registers settings and controls with the Customizer.
 *
 * @since 2.2.3
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
function make_base_customizer_register( $wp_customize ) {

	$wp_customize->add_setting(
		'make_base_link_color',
		array(
			'default'           => make_base_customizer_get_default_link_color(),
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'make_base_link_color',
			array(
				'description' => __( 'Change the color of post info links, hover color of linked titles, hover color of menu items, and more.', 'make-base' ),
				'label'       => __( 'Link Color', 'make-base' ),
				'section'     => 'colors',
				'settings'    => 'make_base_link_color',
			)
		)
	);

	$wp_customize->add_setting(
		'make_base_accent_color',
		array(
			'default'           => make_base_customizer_get_default_accent_color(),
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'make_base_accent_color',
			array(
				'description' => __( 'Change the default hover color for button links, the menu button, and submit buttons. This setting does not apply to buttons created with the Buttons block.', 'make-base' ),
				'label'       => __( 'Accent Color', 'make-base' ),
				'section'     => 'colors',
				'settings'    => 'make_base_accent_color',
			)
		)
	);

	$wp_customize->add_setting(
		'make_base_logo_width',
		array(
			'default'           => 350,
			'sanitize_callback' => 'absint',
		)
	);

	// Add a control for the logo size.
	$wp_customize->add_control(
		'make_base_logo_width',
		array(
			'label'       => __( 'Logo Width', 'make-base' ),
			'description' => __( 'The maximum width of the logo in pixels.', 'make-base' ),
			'priority'    => 9,
			'section'     => 'title_tagline',
			'settings'    => 'make_base_logo_width',
			'type'        => 'number',
			'input_attrs' => array(
				'min' => 100,
			),

		)
	);

}
