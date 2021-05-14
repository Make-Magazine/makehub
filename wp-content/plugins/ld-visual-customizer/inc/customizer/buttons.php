<?php
$wp_customize->add_section( 'lds_visual_customizer_buttons', array(
   'title'     => __( 'Buttons', 'lds_skins' ),
   'priority'  => 35,
   'panel'     => 'lds_visual_customizer'
) );

$button_settings = apply_filters( 'lds_visual_customizer_color_buttons', array(
   'lds_clear_btn'     =>  array(
        'default'       =>  '',
        'type'          =>  'option',
        'transport'     =>  'refresh',
        'capability'    =>  'edit_theme_options'
   ),
   'lds_status_border_radius' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
   ),
   'lds_button_bg' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
   ),
   'lds_button_border_radius' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
   ),
   'lds_pagination_border_radius' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
   ),
   'lds_button_txt' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
   ),
   'lds_complete_button_bg' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
   ),
   'lds_complete_button_txt' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
   ),
) );

foreach( $button_settings as $slug => $options ) {
   $wp_customize->add_setting( $slug, $options );
}

$button_controls = apply_filters( 'lds_visual_customizer_button_controls', array(
   'lds_button_border_radius' => array(
        'label'      => __( 'Button Border Radius', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_buttons',
        'settings'   => 'lds_button_border_radius',
        'min'        => 0,
        'max'        => 50,
        'step'       => 1,
        'default'    => 25,
   ),
) );

foreach( $button_controls as $slug => $control ) {
   $wp_customize->add_control( new LDVC_Customize_Range( $wp_customize, $slug, $control ) );
}

$button_color_controls = apply_filters( 'lds_visual_customizer_button_color_controls', array(
   'lds_button_txt' => array(
        'label'      => __( 'Standard: Button Text', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_buttons',
        'settings'   => 'lds_button_txt',
   ),
   'lds_button_bg' => array(
        'label'      => __( 'Standard: Button Background', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_buttons',
        'settings'   => 'lds_button_bg',
   ),
   'lds_complete_button_bg' => array(
        'label'      => __( 'Complete: Button Background', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_buttons',
        'settings'   => 'lds_complete_button_bg',
   ),
   'lds_complete_button_txt' => array(
        'label'      => __( 'Complete: Button Text', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_buttons',
        'settings'   => 'lds_complete_button_txt',
   ),
) );

foreach( $button_color_controls as $slug => $control ) {
   $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $slug, $control ) );
}
