<?php

function makercamp_new_customizer_settings($wp_customize) {
	$wp_customize->add_setting('logged_out_message', array(
		'default' => '',
		'sanitize_callback' => '',
	) );

	$wp_customize->add_control( 'logged_out_message', array(
		'section' => 'title_tagline',
		'label' => 'Logged Out Message',
		'type' => 'textarea',
		'priority' => 16,
	) );

}
add_action('customize_register', 'makercamp_new_customizer_settings');