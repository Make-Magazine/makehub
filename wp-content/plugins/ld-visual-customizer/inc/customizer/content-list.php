<?php
$wp_customize->add_section( 'lds_visual_customizer_content_list', array(
    'title'     => __( 'Course Content Listings', 'lds_skins' ),
    'description'   =>   __( 'Adjusts the styling of the listing of course content like lessons, topisc, and quizzes.', 'lds_skins' ),
    'priority'  => 35,
    'panel'     => 'lds_visual_customizer'
) );

$content_list_settings = apply_filters( 'lds_visual_customizer_content_list_settings', array(
    'lds_content_list_border_size' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_content_list_border_radius' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_content_list_spacing' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_content_list_padding' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_content_list_drop_shadow' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_content_list_hide_item_counts' => array(
        'default'       =>  '',
        'type'          => 'option',
        'transport'     =>  'refresh',
        'capability'    =>  'edit_theme_options'
    ),
    'lds_content_list_hide_expand_all' => array(
        'default'       =>  '',
        'type'          => 'option',
        'transport'     =>  'refresh',
        'capability'    =>  'edit_theme_options'
    ),
    'lds_content_list_hide_lesson_expand' => array(
        'default'       =>  '',
        'type'          => 'option',
        'transport'     =>  'refresh',
        'capability'    =>  'edit_theme_options'
    ),
    'lds_content_list_hover_effect' => array(
        'default'       =>  '',
        'type'          => 'option',
        'transport'     =>  'refresh',
        'capability'    =>  'edit_theme_options'
   ),
   'lds_content_item_bg' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_expanded_content_bg' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_content_item_border' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_heading_txt' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_heading_bg'    => array(
       'default'       =>  '',
       'type'          =>  'option',
       'transport'     =>  'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_sub_heading_txt' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_sub_heading_bg'    => array(
       'default'       =>  '',
       'type'          =>  'option',
       'transport'     =>  'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_item_txt' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_row_txt' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_row_bg' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_row_bg_alt' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_sub_row_txt' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_sub_row_bg' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_sub_row_bg_alt' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_progress' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_checkbox_complete' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_checkbox_incomplete' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_arrow_complete' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
   'lds_arrow_incomplete' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
   ),
) );

/**
 * Register the settings
 * @var [type]
 */
foreach( $content_list_settings as $slug => $options ) {
    $wp_customize->add_setting( $slug, $options );
}

$content_list_ranges = apply_filters( 'ldvc_content_list_ranges', array(
    'lds_content_list_border_radius' => array(
        'label'      => __( 'Lesson / topic item border radius', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_content_list',
        'settings'   => 'lds_content_list_border_radius',
        'min'        => 0,
        'max'        => 50,
        'step'       => 1,
        'default'    => 12,
    ),
    'lds_content_list_border_size' => array(
        'label'      => __( 'Lesson / topic item border size', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_content_list',
        'settings'   => 'lds_content_list_border_size',
        'min'        => 0,
        'max'        => 50,
        'step'       => 1,
        'default'    => 3,
    ),
    'lds_content_list_padding' => array(
        'label'      => __( 'Lesson / topic item padding', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_content_list',
        'settings'   => 'lds_content_list_padding',
        'min'        => 0,
        'max'        => 50,
        'step'       => 1,
        'default'    => 20
    ),
    'lds_content_list_spacing' => array(
        'label'      => __( 'Margin below content items', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_content_list',
        'settings'   => 'lds_content_list_spacing',
        'min'        => 0,
        'max'        => 50,
        'step'       => 1,
        'default'    => 20,
    ),
) );

foreach( $content_list_ranges as $slug => $control ) {
    $wp_customize->add_control( new LDVC_Customize_Range( $wp_customize, $slug, $control ) );
}

$color_controls = apply_filters( 'lds_visual_customizer_color_controls', array(
    'lds_item_txt' => array(
        'label'      => __( 'Content item text', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_content_list',
        'settings'   => 'lds_item_txt',
    ),
    'lds_content_item_bg' => array(
        'label'      => __( 'Content item background', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_content_list',
        'settings'   => 'lds_content_item_bg',
    ),
    'lds_expanded_content_bg' => array(
        'label'      => __( 'Expanded content background', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_content_list',
        'settings'   => 'lds_expanded_content_bg',
    ),
    'lds_content_item_border' => array(
        'label'      => __( 'Content item border', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_content_list',
        'settings'   => 'lds_content_item_border',
    ),
    'lds_heading_txt' => array(
        'label'      => __( 'Content heading text', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_content_list',
        'settings'   => 'lds_heading_txt',
    ),
    'lds_heading_bg' => array(
        'label'      => __( 'Content heading background', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_content_list',
        'settings'   => 'lds_heading_bg',
    ),
    'lds_sub_heading_txt' => array(
        'label'      => __( 'Section heading text', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_content_list',
        'settings'   => 'lds_sub_heading_txt',
    ),
    'lds_sub_heading_bg' => array(
        'label'      => __( 'Section headings background', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_content_list',
        'settings'   => 'lds_sub_heading_bg',
    ),
) );

foreach( $color_controls as $slug => $control ) {
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $slug, $control ) );
}


$content_list_settings = apply_filters( 'ldvc_content_list_settings', array(
    'lds_content_list_drop_shadow' => array(
        'label'      => __( 'Drop shadow', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_content_list',
        'settings'   => 'lds_content_list_drop_shadow',
        'type'       => 'select',
        'choices'   =>  array(
            'none'		    =>	__( 'None', 'lds_skins' ),
            'light'		    =>	__( 'Light', 'lds_skins' ),
            'light-plus'    =>  __( 'Light +', 'lds_skins' ),
            'medium'	    =>	__( 'Medium', 'lds_skins' ),
            'medium-plus'	=>	__( 'Medium +', 'lds_skins' ),
            'heavy'	        =>	__( 'Heavy', 'lds_skins' ),
            'heavy-plus'	=>	__( 'Heavy +', 'lds_skins' ),
        ),
    ),
     'lds_content_list_hide_item_counts' => array(
        'label'      => __( 'Hide topic/quiz counts', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_content_list',
        'settings'   => 'lds_content_list_hide_item_counts',
        'type'       => 'checkbox',
     ),
     'lds_content_list_hide_lesson_expand' => array(
        'label'      => __( 'Hide lesson expand button', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_content_list',
        'settings'   => 'lds_content_list_hide_lesson_expand',
        'type'       => 'checkbox',
     ),
     'lds_content_list_hide_expand_all' => array(
        'label'      => __( 'Hide expand all button', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_content_list',
        'settings'   => 'lds_content_list_hide_expand_all',
        'type'       => 'checkbox',
     )
) );




foreach( $content_list_settings as $slug => $options ) {

    $wp_customize->add_control( new WP_Customize_Control(
        $wp_customize,
        $slug,
        $options
    ) );

}
