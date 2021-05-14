<?php
$wp_customize->add_section( 'lds_styling_scheme', array(
    'title'         => __( 'Templates &amp; Themes', 'lds_skins' ),
    'description'   =>   __( 'Customize the layout and overall appearance of LearnDash.', 'lds_skins' ),
    'priority'  => 35,
    'panel'     => 'lds_visual_customizer'
) );

/**
 * Settings for the themes & template settings
 * @var array
 */
$theme_settings = array(
    'lds_listing_style' => array(
        'default'        => 'default',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_skin'  =>  array(
        'default'        => 'default',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_grid_columns'  =>  array(
        'default'        => '2',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_focus_theme'  =>   array(
        'type'          =>  'option',
        'transport'     =>  'refresh',
        'capability'    =>  'edit_theme_options'
   ),
    'lds_primary_color' => array(
        'default'             => '',
        'type'                => 'option',
        'transport'           => 'refresh',
        'capability'          => 'edit_theme_options',
        'sanitize_callback'   =>  'lds_update_theme_color'
    ),
    'lds_secondary_color' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
        'sanitize_callback'   =>  'lds_update_theme_color'
    ),
    'lds_alert_color' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
        'sanitize_callback'   =>  'lds_update_theme_color'
    ),
    'lds_tertiary_txt_color' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_tertiary_bg_color' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
);

/**
 * Register the settings
 * @var [type]
 */
foreach( $theme_settings as $slug => $options ) {
    $wp_customize->add_setting( $slug, $options );
}

$customize_controls = apply_filters( 'lds_capi_theme_ccontrols', array(
    'lds_listing_style' => array(
        'label'      => __( 'Template', 'lds_skins' ),
        'section'    => 'lds_styling_scheme',
        'settings'   => 'lds_listing_style',
        'description' => __( 'Use the default LearnDash layout or select a custom layout.', 'lds_skins' ),
        'type'       => 'select',
        'choices'   =>  array(
            'default'		=>	__( 'Default', 'lds_skins' ),
            'expanded'		=>	__( 'Outline', 'lds_skins' ),
            'grid-banner'	=>	__( 'Grid', 'lds_skins' ),
            'stacked'         =>  __( 'Stacked', 'lds_skins' ),
        )
    ),
    'lds_grid_columns' => array(
        'label'      => __( 'Number of Columns', 'lds_skins' ),
        'section'    => 'lds_styling_scheme',
        'settings'   => 'lds_grid_columns',
        'type'       => 'select',
        'choices'   =>  array(
            '2'		=>	'2',
            '3'		=>	'3',
            '4'		=>	'4',
        )
    ),
    'lds_skin' => array(
        "label"      => __("Base Skin", "learndash-skins"),
        "section"    => "lds_styling_scheme",
        'description' => __( 'Select a predefined theme for quick styling!', 'lds_skins' ),
        "settings"   => "lds_skin",
        'type'       => 'select',
        'choices'    => array(
            'default'   => __( 'Default', 'lds_skins' ),
            'modern'    => __( 'Modern', 'lds_skins' ),
            'sleek'     => __( 'Sleek', 'lds_skins' ),
            'minimal'   => __( 'Minimal', 'lds_skins' ),
            'rustic'    => __( 'Rustic', 'lds_skins' ),
            'playful'   => __( 'Playful', 'lds_skins' ),
            'sunny'     => __( 'Sunny', 'lds_skins' ),
        ),
    ),
    'lds_focus_theme' => array(
        "label"      => __("Focus Mode Skin", "learndash-skins"),
        "section"    => "lds_styling_scheme",
        "settings"   => "lds_focus_theme",
        'type'       => 'select',
        'choices'    => array(
            'default'   => __( 'Default', 'lds_skins' ),
            'minimal'    => __( 'Minimal', 'lds_skins' ),
            'sidebar'    =>   __( 'Sidebar only', 'lds_skins' )
            /// 'sleek'     => __( 'Web App', 'lds_skins' ),
            // 'vibrant'   => __( 'Vibrant', 'lds_skins' ),
        ),
    ),
) );

foreach( $customize_controls as $slug => $options ) {

    $wp_customize->add_control( new WP_Customize_Control(
        $wp_customize,
        $slug,
        $options
    ) );

}

$template_color_controls = apply_filters( 'lds_visual_customizer_color_controls', array(
    'lds_primary_color' =>  array(
        'label'               =>  __( 'Primary theme color', 'lds_skins' ),
        'section'             =>  'lds_styling_scheme',
        'settings'            =>  'lds_primary_color',
    ),
    'lds_secondary_color' =>  array(
        'label'     =>  __( 'Secondary theme color', 'lds_skins' ),
        'section'   =>  'lds_styling_scheme',
        'settings'  =>  'lds_secondary_color'
    ),
    'lds_alert_color' =>  array(
        'label'     =>  __( 'Alert color', 'lds_skins' ),
        'section'   =>  'lds_styling_scheme',
        'settings'  =>  'lds_alert_color'
    ),
    'lds_tertiary_txt_color' => array(
        'label'      => __( 'Tertiary text color', 'lds_skins' ),
        'description'   =>  __( 'Dark gray by default', 'lds_skins' ),
        'section'    => 'lds_styling_scheme',
        'settings'   => 'lds_tertiary_txt_color',
    ),
    'lds_tertiary_bg_color' => array(
        'label'      => __( 'Tertiary background color', 'lds_skins' ),
        'description' => __( 'Light gray by default', 'lds_skins' ),
        'section'    => 'lds_styling_scheme',
        'settings'   => 'lds_tertiary_bg_color',
    ),
) );

foreach( $template_color_controls as $slug => $control ) {
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $slug, $control ) );
}
