<?php
$wp_customize->add_section( 'lds_visual_customizer_elements', array(
   'title'     => __( 'Interface Elements', 'lds_skins' ),
   'description'    =>   __( 'Customize the appearance of LearnDash elements like breadcrumbs, alerts, status bubbles, and what is displayed.', 'lds_skins' ),
   'priority'  => 35,
   'panel'     => 'lds_visual_customizer'
) );


$element_settings = apply_filters( 'lds_visual_customizer_elements', array(
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
   'lds_pagination_border_radius' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
   ),
   'lds_hide_breadcrumbs'  => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_hide_last_activity'  => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_hide_progress_steps'  => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_hide_lesson_progress_stats'  => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_hide_profile_points'  => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
) );

foreach( $element_settings as $slug => $options ) {
   $wp_customize->add_setting( $slug, $options );
}


$element_border_radius = apply_filters( 'lds_visual_customizer_element_controls', array(
   'lds_status_border_radius' => array(
        'label'      => __( 'Status Border Radius', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_elements',
        'settings'   => 'lds_status_border_radius',
        'min'        => 0,
        'max'        => 50,
        'step'       => 1,
        'default'    => 25,
   ),
   'lds_pagination_border_radius' => array(
       'label'      => __( 'Pagination Border Radius', 'lds_skins' ),
       'section'    => 'lds_visual_customizer_elements',
       'settings'   => 'lds_pagination_border_radius',
       'min'        => 0,
       'max'        => 50,
       'step'       => 1,
       'default'    => 25,
 ),
) );

foreach( $element_border_radius as $slug => $control ) {
   $wp_customize->add_control( new LDVC_Customize_Range( $wp_customize, $slug, $control ) );
}


$hide_controls = apply_filters( 'ldvc_hide_controls', array(
    'lds_hide_breadcrumbs' => array(
        'label'      => __( 'Hide breadcrumbs', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_elements',
        'settings'   => 'lds_hide_breadcrumbs',
        'type'       => 'checkbox',
    ),
    'lds_hide_last_activity' => array(
        'label'      => __( 'Hide last activity', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_elements',
        'settings'   => 'lds_hide_last_activity',
        'type'       => 'checkbox',
    ),
    'lds_hide_progress_steps' => array(
        'label'      => __( 'Hide X/X steps complete', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_elements',
        'settings'   => 'lds_hide_progress_steps',
        'type'       => 'checkbox',
    ),
    'lds_hide_lesson_progress_stats' => array(
        'label'      => __( 'Hide lesson progress stats (% complete, X/X steps)', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_elements',
        'settings'   => 'lds_hide_lesson_progress_stats',
        'type'       => 'checkbox',
    ),
    'lds_hide_profile_points' => array(
        'label'      => __( 'Hide points on LearnDash profile shortcode', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_elements',
        'settings'   => 'lds_hide_profile_points',
        'type'       => 'checkbox',
    ),
) );


foreach( $hide_controls as $slug => $options ) {

    $wp_customize->add_control( new WP_Customize_Control(
        $wp_customize,
        $slug,
        $options
    ) );

}
