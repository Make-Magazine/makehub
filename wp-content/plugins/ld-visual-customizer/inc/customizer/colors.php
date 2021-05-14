<?php

$wp_customize->add_section( 'lds_visual_customizer_colors', array(
    'title'     => __( 'Global Colors', 'lds_skins' ),
    'priority'  => 35,
    'panel'     => 'lds_visual_customizer'
) );

$color_settings = apply_filters( 'lds_visual_customizer_color_settings', array(

   
) );

foreach( $color_settings as $slug => $options ) {
    $wp_customize->add_setting( $slug, $options );
}
