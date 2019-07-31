<?php
/**
 * Make - Community Theme
 *
 * This file adds the Customizer additions to the Make - Community Theme.
 *
 * @package Make - Co
 * @author  Maker Media
 * @license GPL-2.0-or-later
 * @link    https://makermedia.com/
 */

add_action( 'customize_register', 'make_co_customizer_register' );
/**
 * Registers settings and controls with the Customizer.
 *
 * @since 2.2.3
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
function make_co_customizer_register( $wp_customize ) {

	$wp_customize->add_setting(
		'make_co_link_color',
		array(
			'default'           => make_co_customizer_get_default_link_color(),
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'make_co_link_color',
			array(
				'description' => __( 'Change the color of post info links, hover color of linked titles, hover color of menu items, and more.', 'make-co' ),
				'label'       => __( 'Link Color', 'make-co' ),
				'section'     => 'colors',
				'settings'    => 'make_co_link_color',
			)
		)
	);

	$wp_customize->add_setting(
		'make_co_accent_color',
		array(
			'default'           => make_co_customizer_get_default_accent_color(),
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'make_co_accent_color',
			array(
				'description' => __( 'Change the default hover color for button links, the menu button, and submit buttons. This setting does not apply to buttons created with the Buttons block.', 'make-co' ),
				'label'       => __( 'Accent Color', 'make-co' ),
				'section'     => 'colors',
				'settings'    => 'make_co_accent_color',
			)
		)
	);

	$wp_customize->add_setting(
		'make_co_logo_width',
		array(
			'default'           => 350,
			'sanitize_callback' => 'absint',
		)
	);

	// Add a control for the logo size.
	$wp_customize->add_control(
		'make_co_logo_width',
		array(
			'label'       => __( 'Logo Width', 'make-co' ),
			'description' => __( 'The maximum width of the logo in pixels.', 'make-co' ),
			'priority'    => 9,
			'section'     => 'title_tagline',
			'settings'    => 'make_co_logo_width',
			'type'        => 'number',
			'input_attrs' => array(
				'min' => 100,
			),

		)
	);

}
