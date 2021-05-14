<?php
$wp_customize->add_section( 'lds_visual_customizer_focus_mode', array(
    'title'     => __( 'Focus Mode', 'lds_skins' ),
    'priority'  => 35,
    'panel'     => 'lds_visual_customizer'
) );

$focus_settings = apply_filters( 'lds_visual_customizer_focus_settings', array(
    'lds_focus_content_width' => array(
        'default'             => '',
        'type'                => 'option',
        'transport'           => 'refresh',
        'capability'          => 'edit_theme_options',
    ),
    'lds_welcome_message_name' => array(
        'default'             => '',
        'type'                => 'option',
        'transport'           => 'refresh',
        'capability'          => 'edit_theme_options',
    ),
    'lds_focus_hide_welcome'  => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_focus_hide_avatar'  => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_focus_hide_top_progress_bar'  => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_focus_hide_course_home'  => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_focus_hide_content_footer'  => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_focus_sidebar_bg'  => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_focus_sidebar_course_title_bg'  => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_focus_sidebar_course_title'  => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_focus_sidebar_navigation_title'  => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_focus_sidebar_navigation_accent'  => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_focus_sidebar_border_color'  => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
    ),
    'lds_focus_sidebar_section_heading_bg'  => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
    ),
    'lds_focus_sidebar_section_heading'  => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
    ),
    'lds_focus_sidebar_navigation_topic'  => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
    ),
    'lds_focus_sidebar_navigation_topic_bg'  => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
    ),
    'lds_focus_sidebar_navigation_background'  => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
    ),
) );

$focus_setting_fields = apply_filters( 'ldvc_focus_mode_ranges', array(
    'lds_welcome_message_name' => array(
       'label'      => __( 'Welcome message name', 'lds_skins' ),
       'section'    => 'lds_visual_customizer_focus_mode',
       'settings'   => 'lds_welcome_message_name',
       'type'       => 'select',
       'choices'   =>  array(
            'default'   =>	__( 'Default (display name)', 'lds_skins' ),
            'first'	    =>	__( 'First name', 'lds_skins' ),
            'full'      =>    __( 'Full name', 'lds_skins' ),
            'username'  =>	__( 'Username', 'lds_skins' ),
            'none'      =>	__( 'No name', 'lds_skins' ),
       ),
    ),
    'lds_focus_hide_welcome' => array(
        'label'      => __( 'Hide user welcome message', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_focus_mode',
        'settings'   => 'lds_focus_hide_welcome',
        'type'       => 'checkbox',
    ),
    'lds_focus_hide_avatar' => array(
        'label'      => __( 'Hide user avatar and secondary menu', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_focus_mode',
        'settings'   => 'lds_focus_hide_avatar',
        'type'       => 'checkbox',
    ),
    'lds_focus_hide_top_progress_bar' => array(
        'label'      => __( 'Hide progress bar in header', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_focus_mode',
        'settings'   => 'lds_focus_hide_top_progress_bar',
        'type'       => 'checkbox',
    ),
    'lds_focus_hide_course_home' => array(
        'label'      => __( 'Hide course home icon', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_focus_mode',
        'settings'   => 'lds_focus_hide_course_home',
        'type'       => 'checkbox',
    ),
    'lds_focus_hide_content_footer' => array(
        'label'      => __( 'Hide footer previous / next links', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_focus_mode',
        'settings'   => 'lds_focus_hide_content_footer',
        'type'       => 'checkbox',
    ),
) );

foreach( $focus_settings as $slug => $options ) {
    $wp_customize->add_setting( $slug, $options );
}


foreach( $focus_setting_fields as $slug => $options ) {

    $wp_customize->add_control( new WP_Customize_Control(
        $wp_customize,
        $slug,
        $options
    ) );

}

$focus_colors = apply_filters( 'ldvc_focus_mode_color_settings', array(
     'lds_focus_sidebar_bg'  =>  array(
          'label'               =>  __( 'Sidebar: Background', 'lds_skins' ),
          'section'             =>  'lds_visual_customizer_focus_mode',
          'settings'            =>  'lds_focus_sidebar_bg',
     ),
     'lds_focus_sidebar_navigation_accent' =>  array(
          'label'     =>  __( 'Sidebar: Accent', 'lds_skins' ),
          'section'   =>  'lds_visual_customizer_focus_mode',
          'settings'  =>  'lds_focus_sidebar_navigation_accent'
     ),
     'lds_focus_sidebar_border_color' =>  array(
          'label'     =>  __( 'Sidebar: Border', 'lds_skins' ),
          'section'   =>  'lds_visual_customizer_focus_mode',
          'settings'  =>  'lds_focus_sidebar_border_color'
     ),
     'lds_focus_sidebar_course_title_bg' =>  array(
          'label'     =>  __( 'Sidebar: Course title background', 'lds_skins' ),
          'section'   =>  'lds_visual_customizer_focus_mode',
          'settings'  =>  'lds_focus_sidebar_course_title_bg'
     ),
     'lds_focus_sidebar_course_title' =>  array(
          'label'     =>  __( 'Sidebar: Course title text', 'lds_skins' ),
          'section'   =>  'lds_visual_customizer_focus_mode',
          'settings'  =>  'lds_focus_sidebar_course_title'
     ),
     'lds_focus_sidebar_section_heading_bg' =>  array(
          'label'     =>  __( 'Sidebar: Section heading background', 'lds_skins' ),
          'section'   =>  'lds_visual_customizer_focus_mode',
          'settings'  =>  'lds_focus_sidebar_section_heading_bg'
     ),
     'lds_focus_sidebar_section_heading' =>  array(
          'label'     =>  __( 'Sidebar: Section heading text', 'lds_skins' ),
          'section'   =>  'lds_visual_customizer_focus_mode',
          'settings'  =>  'lds_focus_sidebar_section_heading'
     ),
     'lds_focus_sidebar_navigation_title' =>  array(
          'label'     =>  __( 'Sidebar: Lesson text', 'lds_skins' ),
          'section'   =>  'lds_visual_customizer_focus_mode',
          'settings'  =>  'lds_focus_sidebar_navigation_title'
     ),
     'lds_focus_sidebar_navigation_background' =>  array(
          'label'     =>  __( 'Sidebar: Lesson background', 'lds_skins' ),
          'section'   =>  'lds_visual_customizer_focus_mode',
          'settings'  =>  'lds_focus_sidebar_navigation_background'
     ),
     'lds_focus_sidebar_navigation_topic_bg' =>  array(
          'label'     =>  __( 'Sidebar: Nested topic / quiz background', 'lds_skins' ),
          'section'   =>  'lds_visual_customizer_focus_mode',
          'settings'  =>  'lds_focus_sidebar_navigation_topic_bg'
     ),
     'lds_focus_sidebar_navigation_topic' =>  array(
          'label'     =>  __( 'Sidebar: Nested topic / quiz text', 'lds_skins' ),
          'section'   =>  'lds_visual_customizer_focus_mode',
          'settings'  =>  'lds_focus_sidebar_navigation_topic'
     ),
) );


foreach( $focus_colors as $slug => $control ) {
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $slug, $control ) );
}
