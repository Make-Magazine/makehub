<?php
/**
 * Learn based off Monochrome Pro.
 *
 * This file adds the Customizer additions to the Learn Theme.
 *
 * @package Learn
 * @author  Maker Media
 * @license GPL-2.0+
 * @link    https://github.com/Make-Magazine/makehub
 */

add_action( 'customize_register', 'learn_customizer_register' );
/**
 * Register settings and controls with the Customizer.
 *
 * @since 1.0.0
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
function learn_customizer_register( $wp_customize ) {

	$images = apply_filters( 'learn_images', array( '1', '3' ) );

	$wp_customize->add_section( 'learn_theme_options', array(
		'description' => __( 'Personalize the Learn theme with these available options.', 'learn' ),
		'title'       => __( 'Theme Options', 'learn' ),
		'priority'    => 30,
	) );

	$wp_customize->add_section( 'learn-settings', array(
		'description' => __( 'Use the included default images or personalize your site by uploading your own images.<br /><br />The default images are <strong>1600 pixels wide and 800 pixels tall</strong>.', 'learn' ),
		'title'       => __( 'Front Page Background Images', 'learn' ),
		'priority'    => 35,
	) );

	foreach( $images as $key => $image ) {

		// Add setting for front page background images.
		$wp_customize->add_setting( $image .'-learn-image', array(
			'default'           => sprintf( '%s/images/bg-%s.jpg', get_stylesheet_directory_uri(), $image ),
			'sanitize_callback' => 'esc_url_raw',
			'type'              => 'option',
		) );

		// Add control for front page background images.
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $image .'-learn-image', array(
			'label'    => sprintf( __( 'Featured Section %s Image:', 'learn' ), $image ),
			'section'  => 'learn-settings',
			'settings' => $image .'-learn-image',
			'priority' => $key + 1,
		) ) );

	}

	// Add setting for link color.
	$wp_customize->add_setting(
		'learn_link_color',
		array(
			'default'           => learn_customizer_get_default_link_color(),
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	// Add control for link color.
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'learn_link_color',
			array(
				'description' => __( 'Change the default color for hovers for linked titles, menu links, entry meta links, and more.', 'learn' ),
				'label'       => __( 'Link Color', 'learn' ),
				'section'     => 'colors',
				'settings'    => 'learn_link_color',
			)
		)
	);

	// Add setting for accent color.
	$wp_customize->add_setting(
		'learn_accent_color',
		array(
			'default'           => learn_customizer_get_default_accent_color(),
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	// Add control for accent color.
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'learn_accent_color',
			array(
				'description' => __( 'Change the default color for button hovers.', 'learn' ),
				'label'       => __( 'Accent Color', 'learn' ),
				'section'     => 'colors',
				'settings'    => 'learn_accent_color',
			)
		)
	);

	// Add setting for footer start color.
	$wp_customize->add_setting(
		'learn_footer_start_color',
		array(
			'default'           => learn_customizer_get_default_footer_start_color(),
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	// Add control for footer start color.
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'learn_footer_start_color',
			array(
				'description' => __( 'Change the default color for start of footer gradient.', 'learn' ),
				'label'       => __( 'Footer Start Color', 'learn' ),
				'section'     => 'colors',
				'settings'    => 'learn_footer_start_color',
			)
		)
	);

	// Add setting for footer end color.
	$wp_customize->add_setting(
		'learn_footer_end_color',
		array(
			'default'           => learn_customizer_get_default_footer_end_color(),
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	// Add control for footer end color.
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'learn_footer_end_color',
			array(
				'description' => __( 'Change the default color for end of footer gradient.', 'learn' ),
				'label'       => __( 'Footer End Color', 'learn' ),
				'section'     => 'colors',
				'settings'    => 'learn_footer_end_color',
			)
		)
	);

	// Add control for search option.
	$wp_customize->add_setting(
		'learn_header_search',
		array(
			'default'           => learn_customizer_get_default_search_setting(),
			'sanitize_callback' => 'absint',
		)
	);

	// Add setting for search option.
	$wp_customize->add_control(
		'learn_header_search',
		array(
			'label'       => __( 'Show Menu Search Icon?', 'learn' ),
			'description' => __( 'Check the box to show a search icon in the menu.', 'learn' ),
			'section'     => 'learn_theme_options',
			'type'        => 'checkbox',
			'settings'    => 'learn_header_search',
		)
	);

}
