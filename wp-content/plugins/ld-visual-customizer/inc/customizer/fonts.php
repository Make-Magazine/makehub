<?php
/**
 * Font Sizes
 * @var [type]
 */

 $wp_customize->add_section( 'lds_visual_customizer_fonts', array(
     'title'     => __( 'Fonts', 'lds_skins' ),
     'priority'  => 35,
     'panel'     => 'lds_visual_customizer'
 ) );

 $sizes = apply_filters( 'lds_font_sizes', array(
     '---'	=>	'---',
     ''		=>	'Default',
     '8'		=>	'8px',
     '9'		=>	'9px',
     '10'	=>	'10px',
     '11'	=>	'11px',
     '12'	=>	'12px',
     '14'	=>	'14px',
     '16'	=>	'16px',
     '18'	=>	'18px',
     '20'	=>	'20px',
     '24'	=>	'24px',
     '28'	=>	'28px',
     '32'	=>	'32px',
     '38'	=>	'38px',
     '42'	=>	'42px',
     '48'	=>	'48px',
     '64'	=>	'64px',
     '72'	=>	'72px',
     '92'	=>	'92px',
 ) );

 $font_settings = array(
     'lds_font_family'  => array(
        'default'       => '',
        'type'          => 'option',
        'transport'     => 'refresh',
        'capability'    => 'edit_theme_options'
     ),
     'lds_heading_font_family'  => array(
        'default'       => '',
        'type'          => 'option',
        'transport'     => 'refresh',
        'capability'    => 'edit_theme_options'
     ),
     'lds_font_size_inherit' => array(
         'default'        => '',
         'type'           => 'option',
         'transport'      => 'refresh',
         'capability'     => 'edit_theme_options',
     ),
     'lds_base_font_size' => array(
          'default'      =>   '',
          'type'         =>   'option',
          'transport'    =>   'refresh',
          'capability'   =>   'edit_theme_options'
     ),
     'lds_heading_font_size' => array(
         'default'        => '',
         'type'           => 'option',
         'transport'      => 'refresh',
         'capability'     => 'edit_theme_options',
     ),
     'lds_section_heading_font_size' => array(
         'default'        => '',
         'type'           => 'option',
         'transport'      => 'refresh',
         'capability'     => 'edit_theme_options',
     ),
     'lds_item_font_size'  =>  array(
         'default'        => '',
         'type'           => 'option',
         'transport'      => 'refresh',
         'capability'     => 'edit_theme_options',
     ),
     'lds_item_attribute_font_size' => array(
         'default'        => '',
         'type'           => 'option',
         'transport'      => 'refresh',
         'capability'     => 'edit_theme_options',
     ),
     'lds_widget_heading_font_size' => array(
         'default'        => '',
         'type'           => 'option',
         'transport'      => 'refresh',
         'capability'     => 'edit_theme_options',
     ),
     'lds_widget_section_heading_font_size' => array(
         'default'        => '',
         'type'           => 'option',
         'transport'      => 'refresh',
         'capability'     => 'edit_theme_options',
     ),
     'lds_widget_item_attributes_font_size' => array(
         'default'        => '',
         'type'           => 'option',
         'transport'      => 'refresh',
         'capability'     => 'edit_theme_options',
     ),
     'lds_widget_item_font_size' => array(
         'default'        => '',
         'type'           => 'option',
         'transport'      => 'refresh',
         'capability'     => 'edit_theme_options',
     ),
     'lds_widget_sub_item_font_size' => array(
         'default'        => '',
         'type'           => 'option',
         'transport'      => 'refresh',
         'capability'     => 'edit_theme_options',
     ),
     'lds_button_font_size' => array(
         'default'        => '',
         'type'           => 'option',
         'transport'      => 'refresh',
         'capability'     => 'edit_theme_options',
    ),
    'lds_focus_nav_course_title' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
    ),
    'lds_focus_nav_section_heading' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_focus_nav_lesson_title' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
    ),
    'lds_focus_nav_sub_title' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
   ),
   'lds_focus_page_title' => array(
       'default'        => '',
       'type'           => 'option',
       'transport'      => 'refresh',
       'capability'     => 'edit_theme_options',
    ),
    'lds_focus_body_size' => array(
        'default'        => '',
        'type'           => 'option',
        'transport'      => 'refresh',
        'capability'     => 'edit_theme_options',
     ),
     'lds_focus_nav_meta' => array(
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
 foreach( $font_settings as $slug => $options ) {
     $wp_customize->add_setting( $slug, $options );
 }

$font_families = ldvc_get_google_font_familes();

$font_family_controls = apply_filters( 'lds_font_family_controls', array(
    'lds_font_family' => array(
        'label'      => __( 'Base font', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_fonts',
        'settings'   => 'lds_font_family',
        'type'       => 'select',
        'choices'    => array(
            ''  => __( 'Theme Default', 'lds_skins' )
        )
    ),
    'lds_heading_font_family' => array(
        'label'      => __( 'Heading font', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_fonts',
        'settings'   => 'lds_heading_font_family',
        'type'       => 'select',
        'choices'    => array(
            ''  => __( 'Theme Default', 'lds_skins' )
        )
    ),
) );

$font_choices = array();

if( !empty($font_families) && is_array($font_families->items) ) {
     foreach( $font_families->items as $font ) {
          $font_choices = array_merge( $font_choices, array( $font->family => $font->family ) );
     }
}

$font_family_controls['lds_font_family']['choices'] = array_merge( $font_family_controls['lds_font_family']['choices'], $font_choices );
$font_family_controls['lds_heading_font_family']['choices'] = array_merge( $font_family_controls['lds_font_family']['choices'], $font_choices );

foreach( $font_family_controls as $slug => $options ) {

    $wp_customize->add_control( new WP_Customize_Control(
        $wp_customize,
        $slug,
        $options
    ) );

}

 $font_controls = apply_filters( 'lds_capi_font_ccontrols', array(
      'lds_base_font_size' => array(
         'label'      => __( 'Base font size', 'lds_skins' ),
         'section'    => 'lds_visual_customizer_fonts',
         'settings'   => 'lds_base_font_size',
         'min'        => 0,
         'max'        => 200,
         'step'       => 1,
      ),
     'lds_heading_font_size' => array(
         'label'      => __( 'Heading font size', 'lds_skins' ),
         'section'    => 'lds_visual_customizer_fonts',
         'settings'   => 'lds_heading_font_size',
         'min'        => 0,
         'max'        => 200,
         'step'       => 1,
     ),
     'lds_section_heading_font_size' => array(
         'label'      => __( 'Section heading font size', 'lds_skins' ),
         'section'    => 'lds_visual_customizer_fonts',
         'settings'   => 'lds_section_heading_font_size',
         'min'        => 0,
         'max'        => 200,
         'step'       => 1,
     ),
     'lds_item_font_size' => array(
         "label"     => __( 'Lesson / Topic / Quiz title font size', 'lds_skins' ),
         "section"    => "lds_visual_customizer_fonts",
         "settings"   => "lds_item_font_size",
         'min'        => 0,
         'max'        => 200,
         'step'       => 1,
     ),
     'lds_item_attribute_font_size' => array(
        'label'      => __( 'Lesson meta font size', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_fonts',
        'settings'   => 'lds_item_attribute_font_size',
        'min'        => 0,
        'max'        => 200,
        'step'       => 1,
     ),
     'lds_widget_heading_font_size' => array(
        'label'      => __( 'Widget: Heading font size', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_fonts',
        'settings'   => 'lds_widget_heading_font_size',
        'min'        => 0,
        'max'        => 200,
        'step'       => 1,
     ),
     'lds_widget_section_heading_font_size' => array(
        'label'      => __( 'Widget: Section heading font size', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_fonts',
        'settings'   => 'lds_widget_section_heading_font_size',
        'min'        => 0,
        'max'        => 200,
        'step'       => 1,
     ),
     'lds_widget_item_font_size' => array(
        'label'      => __( 'Widget: Lesson / Topic font size', 'lds_skins' ),
        'section'    => 'lds_visual_customizer_fonts',
        'settings'   => 'lds_widget_item_font_size',
        'min'        => 0,
        'max'        => 200,
        'step'       => 1,
    ),
    'lds_widget_item_attributes_font_size' => array(
       'label'      => __( 'Widget: Lesson meta size', 'lds_skins' ),
       'section'    => 'lds_visual_customizer_fonts',
       'settings'   => 'lds_widget_item_attributes_font_size',
       'min'        => 0,
       'max'        => 200,
       'step'       => 1,
   ),
    'lds_widget_sub_item_font_size' => array(
       'label'      => __( 'Widget: Sub item font size', 'lds_skins' ),
       'section'    => 'lds_visual_customizer_fonts',
       'settings'   => 'lds_widget_sub_item_font_size',
       'min'        => 0,
       'max'        => 200,
       'step'       => 1,
   ),
   'lds_button_font_size' => array(
      'label'      => __( 'Button font size', 'lds_skins' ),
      'section'    => 'lds_visual_customizer_fonts',
      'settings'   => 'lds_button_font_size',
      'min'        => 0,
      'max'        => 200,
      'step'       => 1,
     ),
     'lds_focus_nav_course_title' => array(
          'label'      => __( 'Focus Mode Sidebar: Course Title', 'lds_skins' ),
          'section'    => 'lds_visual_customizer_fonts',
          'settings'   => 'lds_focus_nav_course_title',
          'min'        => 0,
          'max'        => 200,
          'step'       => 1,
     ),
     'lds_focus_nav_section_heading' => array(
          'label'      => __( 'Focus Mode Sidebar: Section Heading', 'lds_skins' ),
          'section'    => 'lds_visual_customizer_fonts',
          'settings'   => 'lds_focus_nav_section_heading',
          'min'        => 0,
          'max'        => 200,
          'step'       => 1,
     ),
     'lds_focus_nav_lesson_title' => array(
          'label'      => __( 'Focus Mode Sidebar: Lesson Title', 'lds_skins' ),
          'section'    => 'lds_visual_customizer_fonts',
          'settings'   => 'lds_focus_nav_lesson_title',
          'min'        => 0,
          'max'        => 200,
          'step'       => 1,
     ),
     'lds_focus_nav_meta' => array(
          'label'      => __( 'Focus Mode Sidebar: Lesson Meta', 'lds_skins' ),
          'section'    => 'lds_visual_customizer_fonts',
          'settings'   => 'lds_focus_nav_meta',
          'min'        => 0,
          'max'        => 200,
          'step'       => 1,
     ),
     'lds_focus_nav_sub_title' => array(
          'label'      => __( 'Focus Mode Sidebar: Topic / Quiz Title', 'lds_skins' ),
          'section'    => 'lds_visual_customizer_fonts',
          'settings'   => 'lds_focus_nav_sub_title',
          'min'        => 0,
          'max'        => 200,
          'step'       => 1,
     ),
     'lds_focus_page_title' => array(
          'label'      => __( 'Focus Mode: Page Title Size', 'lds_skins' ),
          'section'    => 'lds_visual_customizer_fonts',
          'settings'   => 'lds_focus_page_title',
          'min'        => 0,
          'max'        => 200,
          'step'       => 1,
     ),
     'lds_focus_body_size' => array(
          'label'      => __( 'Focus Mode: Body Font Size', 'lds_skins' ),
          'section'    => 'lds_visual_customizer_fonts',
          'settings'   => 'lds_focus_body_size',
          'min'        => 0,
          'max'        => 200,
          'step'       => 1,
     ),
 ) );


 foreach( $font_controls as $slug => $options ) {

     $wp_customize->add_control( new LDVC_Customize_Range( $wp_customize, $slug, $options ) );

 }
