<?php

$wp_customize->add_section( 'lds_hide', array(
    'title'     => __( 'Hide Elements', 'lds_skins' ),
    'priority'  => 35,
    'panel'     => 'lds_visual_customizer'
) );


$hide_settings = apply_filters( 'lds_hide_settings', array(


) );

/**
 * Register the settings
 * @var [type]
 */
foreach( $hide_settings as $slug => $options ) {
    $wp_customize->add_setting( $slug, $options );
}
