<?php

$wp_customize->add_section( 'lds_visual_customizer_quiz', array(
    'title'     => __( 'Quiz', 'lds_skins' ),
    'priority'  => 35,
    'panel'     => 'lds_visual_customizer'
) );

$quiz_color_settings = apply_filters( 'lds_visual_customizer_quiz_settings', array(
    'lds_quiz_bg' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_quiz_border' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
    ),
    'lds_quiz_txt' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_quiz_correct_txt' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_quiz_correct_bg' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_quiz_incorrect_txt' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_quiz_incorrect_bg' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_quiz_border_radius' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
    ),
) );

foreach( $quiz_color_settings as $slug => $options ) {
    $wp_customize->add_setting( $slug, $options );
}

$quiz_color_controls = apply_filters( 'lds_visual_customizer_quiz_color_controls', array(
    'lds_quiz_bg' => array(
        'label'      => __( 'Quiz Question Background', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_quiz',
        'settings'   => 'lds_quiz_bg',
    ),
    'lds_quiz_border' => array(
       'label'      => __( 'Quiz Question Border', 'lds_skins' ),
       'section'    => 'lds_visual_customizer_quiz',
       'settings'   => 'lds_quiz_border',
    ),
    'lds_quiz_txt' => array(
        'label'      => __( 'Quiz Question Text', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_quiz',
        'settings'   => 'lds_quiz_txt',
    ),
    'lds_quiz_correct_txt' => array(
        'label'      => __( 'Quiz Question Correct Text', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_quiz',
        'settings'   => 'lds_quiz_correct_txt',
    ),
    'lds_quiz_correct_bg' => array(
        'label'      => __( 'Quiz Question Correct Background', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_quiz',
        'settings'   => 'lds_quiz_correct_bg',
    ),
    'lds_quiz_incorrect_txt' => array(
        'label'      => __( 'Quiz Question Incorrect Text', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_quiz',
        'settings'   => 'lds_quiz_incorrect_txt',
    ),
    'lds_quiz_incorrect_bg' => array(
        'label'      => __( 'Quiz Question Incorrect Background', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_quiz',
        'settings'   => 'lds_quiz_incorrect_bg',
    ),
) );

foreach( $quiz_color_controls as $slug => $control ) {
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $slug, $control ) );
}

$quiz_slider_controls = apply_filters( 'lds_visual_customizer_quiz_slider_controls', array(
     'lds_quiz_border_radius' => array(
       'label'      => __( 'Quiz Question Border Radius', 'lds_skins' ),
       'section'    => 'lds_visual_customizer_quiz',
       'settings'   => 'lds_quiz_border_radius',
       'min'        => 0,
       'max'        => 100,
       'step'       => 1,
     ),
) );

foreach( $quiz_slider_controls as $slug => $options ) {

    $wp_customize->add_control( new LDVC_Customize_Range( $wp_customize, $slug, $options ) );

}
