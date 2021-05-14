<?php
$wp_customize->add_section( 'lds_icons', array(
    'title'     => __( 'Icons', 'lds_skins' ),
    'priority'  => 35,
    'panel'     => 'lds_visual_customizer'
) );

/**
 * Settings for the themes & template settings
 * @var array
 */
$icon_settings = apply_filters( 'ldvc_icon_settings', array(
     'lds_icon_font_size' => array(
         'default'        => '',
         'type'           => 'option',
         'transport'      => 'refresh',
         'capability'     => 'edit_theme_options',
     ),
    'lds_complete_icon' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_quiz_icon'  =>  array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_sample_icon'  =>  array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_calendar_icon' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_arrow_down_icon' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_arrow_right_icon' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_arrow_left_icon' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_content_icon' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_assignment_icon' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_materials_icon' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_certificate_icon' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_download_icon' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    )
    ,
    'lds_comments_icon' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_alert_icon' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    )
) );

/**
 * Register the settings
 * @var [type]
 */
foreach( $icon_settings as $slug => $options ) {
    $wp_customize->add_setting( $slug, $options );
}

$icon_font_controls = apply_filters( 'lds_capi_icon_font_controls', array(
     'lds_icon_font_size' => array(
        'label'      => __( 'Icon size', 'lds_skins' ),
        'section'    => 'lds_icons',
        'settings'   => 'lds_icon_font_size',
        'type'       => 'select',
        'choices'   =>  array(
            'default'	=>	__( 'Default', 'lds_skins' ),
            '8'         =>   __( 'Tiny', 'lds_skins' ),
            '12'		=>	__( 'Small', 'lds_skins' ),
            '16'	=>	__( 'Medium', 'lds_skins' ),
            '20'    =>   __( 'Large', 'lds_skins' ),
            '26'    =>   __( 'X-Large', 'lds_skins' ),
        )
    ),
) );

foreach( $icon_font_controls as $slug => $options ) {

    $wp_customize->add_control( new WP_Customize_Control(
        $wp_customize,
        $slug,
        $options
    ) );

}

$icon_controls = apply_filters( 'ldvc_icon_controls', array(
    'lds_complete_icon' => array(
        'label'      => __( 'Complete', 'lds_skins' ),
        'section'    => 'lds_icons',
        'settings'   => 'lds_complete_icon',
        'type'       => 'fontawesome',
    ),
    'lds_quiz_icon' => array(
        'label'      => __( 'Quiz', 'lds_skins' ),
        'section'    => 'lds_icons',
        'settings'   => 'lds_quiz_icon',
        'type'       => 'fontawesome',
    ),
    'lds_sample_icon' => array(
        'label'      => __( 'Sample', 'lds_skins' ),
        'section'    => 'lds_icons',
        'settings'   => 'lds_sample_icon',
        'type'       => 'fontawesome',
    ),
    'lds_calendar_icon' => array(
        'label'      => __( 'Calendar', 'lds_skins' ),
        'section'    => 'lds_icons',
        'settings'   => 'lds_calendar_icon',
        'type'       => 'fontawesome',
    ),
    'lds_arrow_up_icon' => array(
        'label'      => __( 'Arrow up', 'lds_skins' ),
        'section'    => 'lds_icons',
        'settings'   => 'lds_arrow_up_icon',
        'type'       => 'fontawesome',
    ),
    'lds_arrow_down_icon' => array(
        'label'      => __( 'Arrow down', 'lds_skins' ),
        'section'    => 'lds_icons',
        'settings'   => 'lds_arrow_down_icon',
        'type'       => 'fontawesome',
    ),
    'lds_arrow_left_icon' => array(
        'label'      => __( 'Arrow left', 'lds_skins' ),
        'section'    => 'lds_icons',
        'settings'   => 'lds_arrow_left_icon',
        'type'       => 'fontawesome',
    ),

    'lds_arrow_right_icon' => array(
        'label'      => __( 'Arrow right', 'lds_skins' ),
        'section'    => 'lds_icons',
        'settings'   => 'lds_arrow_right_icon',
        'type'       => 'fontawesome',
    ),
    'lds_content_icon' => array(
        'label'      => __( 'Content', 'lds_skins' ),
        'section'    => 'lds_icons',
        'settings'   => 'lds_content_icon',
        'type'       => 'fontawesome',
    ),
    'lds_assignment_icon' => array(
        'label'      => __( 'Assignment', 'lds_skins' ),
        'section'    => 'lds_icons',
        'settings'   => 'lds_assignment_icon',
        'type'       => 'fontawesome',
    ),
    'lds_download_icon' => array(
        'label'      => __( 'Download', 'lds_skins' ),
        'section'    => 'lds_icons',
        'settings'   => 'lds_download_icon',
        'type'       => 'fontawesome',
    ),
    'lds_materials_icon' => array(
        'label'      => __( 'Materials', 'lds_skins' ),
        'section'    => 'lds_icons',
        'settings'   => 'lds_materials_icon',
        'type'       => 'fontawesome',
    ),
    'lds_certificate_icon' => array(
        'label'      => __( 'Certificate', 'lds_skins' ),
        'section'    => 'lds_icons',
        'settings'   => 'lds_certificate_icon',
        'type'       => 'fontawesome',
    ),
    'lds_comments_icon' => array(
        'label'      => __( 'Comments', 'lds_skins' ),
        'section'    => 'lds_icons',
        'settings'   => 'lds_comments_icon',
        'type'       => 'fontawesome',
    ),
    'lds_comments_icon' => array(
        'label'      => __( 'Comments', 'lds_skins' ),
        'section'    => 'lds_icons',
        'settings'   => 'lds_comments_icon',
        'type'       => 'fontawesome',
    ),
    'lds_alert_icon' => array(
        'label'      => __( 'Alert', 'lds_skins' ),
        'section'    => 'lds_icons',
        'settings'   => 'lds_comments_icon',
        'type'       => 'fontawesome',
    ),
) );

foreach( $icon_controls as $slug => $options ) {

    $wp_customize->add_control( new LDVC_Customize_Iconpicker_Control(
        $wp_customize,
        $slug,
        $options
    ) );

}
