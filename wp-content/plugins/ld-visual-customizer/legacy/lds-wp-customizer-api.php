<?php
add_action( 'customize_register', 'lds_use_theme_customizer_api' );
function lds_use_theme_customizer_api( $wp_customize ) {

    lds_create_range_field();

    $wp_customize->add_panel( 'lds_visual_customizer', array(
        'priority'      =>  100,
        'capability'        =>  'edit_theme_options',
        'theme_supports'    =>  '',
        'title'             =>  __( 'LearnDash Styling', 'lds_skins' ),
        'description'       =>  __( 'Control the LearnDash Styling', 'lds_skins' )
    ) );

    $wp_customize->add_section( 'lds_styling_scheme', array(
        'title'     => __( 'Templates &amp; Themes', 'lds_skins' ),
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
            'default'        => 'default',
            'type'           => 'option',
            'transport'      => 'refresh',
            'capability'     => 'edit_theme_options',
        ),
        'lds_icon_style' => array(
            'default'        => 'default',
            'type'           => 'option',
            'transport'      => 'refresh',
            'capability'     => 'edit_theme_options',
        ),
        'lds_show_leaderboard' => array(
            'default'        => 'no',
            'type'           => 'option',
            'transport'      => 'refresh',
            'capability'     => 'edit_theme_options',
        ),
        'lds_animation' => array(
            'default'        => 'no',
            'type'           => 'option',
            'transport'      => 'refresh',
            'capability'     => 'edit_theme_options',
        )
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
            'type'       => 'select',
            'choices'   =>  array(
                'default'		=>	__( 'LearnDash Default', 'lds_skins' ),
                'enhanced'		=>	__( 'LearnDash Enhanced', 'lds_skins' ),
                'expanded'		=>	__( 'Expanded Style', 'lds_skins' ),
                'grid-banner'	=>	__( 'Grid with banners', 'lds_skins' ),
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
			"settings"   => "lds_skin",
            'type'       => 'select',
            'choices'    => array(
                'default'   => __( 'Default', 'lds_skins' ),
                'modern'    => __( 'Modern', 'lds_skins' ),
                'classic'   => __( 'Classic', 'lds_skins' ),
                'rustic'    => __( 'Rustic', 'lds_skins' ),
                'playful'   => __( 'Playful', 'lds_skins' ),
                'upscale'   => __( 'Upscale', 'lds_skins' ),
            ),
        ),
        'lds_icon_style' => array(
            'label'      => __( 'Icon Style', 'lds_skins' ),
			'section'    => 'lds_styling_scheme',
			'settings'   => 'lds_icon_style',
            'type'       => 'select',
            'choices'    => array(
                'default'   => __( 'Default', 'lds_skins' ),
                'modern'    => __( 'Modern', 'lds_skins' ),
                'minimal'   => __( 'Minimal', 'lds_skins' ),
                'chunky'    => __( 'Chunky', 'lds_skins' ),
                'playful'   => __( 'Playful', 'lds_skins' ),
                'circles'   => __( 'Circles', 'lds_skins' )
            ),
        )
    ) );

    foreach( $customize_controls as $slug => $options ) {

        $wp_customize->add_control( new WP_Customize_Control(
            $wp_customize,
            $slug,
            $options
        ) );

    }

    /**
     * Font Sizes
     * @var [type]
     */

     $wp_customize->add_section( 'lds_visual_customizer_fonts', array(
         'title'     => __( 'Font Sizes', 'lds_skins' ),
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
         'lds_table_heading_font_size' => array(
             'default'        => '',
             'type'           => 'option',
             'transport'      => 'refresh',
             'capability'     => 'edit_theme_options',
         ),
         'lds_table_row_font_size'  =>  array(
             'default'        => '',
             'type'           => 'option',
             'transport'      => 'refresh',
             'capability'     => 'edit_theme_options',
         ),
         'lds_table_sub_row_font_size' => array(
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
         'lds_widget_text_font_size' => array(
             'default'        => '',
             'type'           => 'option',
             'transport'      => 'refresh',
             'capability'     => 'edit_theme_options',
         )
     );

     /**
      * Register the settings
      * @var [type]
      */
     foreach( $font_settings as $slug => $options ) {
         $wp_customize->add_setting( $slug, $options );
     }

     $font_controls = apply_filters( 'lds_capi_font_ccontrols', array(
         'lds_table_heading_font_size' => array(
             'label'      => __( 'Table / Primary Heading', 'lds_skins' ),
             'section'    => 'lds_visual_customizer_fonts',
             'settings'   => 'lds_table_heading_font_size',
             'min'        => 0,
             'max'        => 200,
             'step'       => 1,
         ),
         'lds_table_row_font_size' => array(
             "label"     => __( "Table Row", "lds_skins" ),
 			 "section"    => "lds_visual_customizer_fonts",
 			 "settings"   => "lds_table_row_font_size",
             'min'        => 0,
             'max'        => 200,
             'step'       => 1,
         ),
         'lds_table_sub_row_font_size' => array(
            'label'      => __( 'Table Sub Row', 'lds_skins' ),
 			'section'    => 'lds_visual_customizer_fonts',
 			'settings'   => 'lds_table_sub_row_font_size',
            'min'        => 0,
            'max'        => 200,
            'step'       => 1,
         ),
         'lds_widget_heading_font_size' => array(
            'label'      => __( 'Widget Heading', 'lds_skins' ),
 			'section'    => 'lds_visual_customizer_fonts',
 			'settings'   => 'lds_widget_heading_font_size',
            'min'        => 0,
            'max'        => 200,
            'step'       => 1,
         ),
         'lds_widget_text_font_size' => array(
            'label'      => __( 'Widget Text', 'lds_skins' ),
 			'section'    => 'lds_visual_customizer_fonts',
 			'settings'   => 'lds_widget_text_font_size',
            'min'        => 0,
            'max'        => 200,
            'step'       => 1,
         )
     ) );

     foreach( $font_controls as $slug => $options ) {

         $wp_customize->add_control( new LDVC_Customize_Range( $wp_customize, $slug, $options ) );


     }

    $wp_customize->add_section( 'lds_visual_customizer_colors', array(
        'title'     => __( 'Course Content Lists', 'lds_skins' ),
        'priority'  => 35,
        'panel'     => 'lds_visual_customizer'
    ) );

    $color_settings = apply_filters( 'lds_visual_customizer_color_settings', array(
        'lds_heading_bg' => array(
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

    foreach( $color_settings as $slug => $options ) {
        $wp_customize->add_setting( $slug, $options );
    }

    $color_controls = apply_filters( 'lds_visual_customizer_color_controls', array(
        'lds_heading_bg' => array(
            'label'      => __( 'Table Heading Background', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_colors',
            'settings'   => 'lds_heading_bg',
        ),
        'lds_heading_txt' => array(
            'label'      => __( 'Table / Primary Heading Text', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_colors',
            'settings'   => 'lds_heading_txt',
        ),
        'lds_row_txt' => array(
            'label'      => __( 'Table Row / Primary Text', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_colors',
            'settings'   => 'lds_row_txt',
        ),
        'lds_row_bg' => array(
            'label'      => __( 'Table Row Background', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_colors',
            'settings'   => 'lds_row_bg',
        ),
        'lds_row_bg_alt' => array(
            'label'      => __( 'Table Row Background (alt)', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_colors',
            'settings'   => 'lds_row_bg_alt',
        ),
        'lds_sub_row_txt' => array(
            'label'      => __( 'Table Sub Row Text', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_colors',
            'settings'   => 'lds_sub_row_txt',
        ),
        'lds_sub_row_bg' => array(
            'label'      => __( 'Table Sub Row Background', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_colors',
            'settings'   => 'lds_sub_row_bg',
        ),
        'lds_sub_row_alt' => array(
            'label'      => __( 'Table Sub Row Background (alt)', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_colors',
            'settings'   => 'lds_sub_row_bg_alt',
        ),
        'lds_progress' => array(
            'label'      => __( 'Progress Bar', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_colors',
            'settings'   => 'lds_progress',
        ),
        'lds_checkbox_complete' => array(
            'label'      => __( 'Complete Checkmark', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_colors',
            'settings'   => 'lds_checkbox_complete',
        ),
        'lds_checkbox_incomplete' => array(
            'label'      => __( 'Incomplete Checkmark', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_colors',
            'settings'   => 'lds_checkbox_incomplete',
        ),
        'lds_arrow_complete' => array(
            'label'      => __( 'Complete Arrow', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_colors',
            'settings'   => 'lds_arrow_complete',
        ),
        'lds_arrow_incomplete' => array(
            'label'      => __( 'Incomplete Arrow', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_colors',
            'settings'   => 'lds_arrow_incomplete',
        ),
    ) );

    foreach( $color_controls as $slug => $control ) {
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $slug, $control ) );
    }

    $wp_customize->add_section( 'lds_visual_customizer_buttons', array(
        'title'     => __( 'Buttons', 'lds_skins' ),
        'priority'  => 35,
        'panel'     => 'lds_visual_customizer'
    ) );

    $button_settings = apply_filters( 'lds_visual_customizer_color_buttons', array(
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
        ),
    ) );

    foreach( $button_controls as $slug => $control ) {
        $wp_customize->add_control( new LDVC_Customize_Range( $wp_customize, $slug, $control ) );
    }


    $button_color_controls = apply_filters( 'lds_visual_customizer_button_color_controls', array(
        'lds_button_txt' => array(
            'label'      => __( 'Default Button Text', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_buttons',
            'settings'   => 'lds_button_txt',
        ),
        'lds_button_bg' => array(
            'label'      => __( 'Default Button Background', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_buttons',
            'settings'   => 'lds_button_bg',
        ),
        'lds_complete_button_bg' => array(
            'label'      => __( 'Complete Button Background', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_buttons',
            'settings'   => 'lds_complete_button_bg',
        ),
        'lds_complete_button_txt' => array(
            'label'      => __( 'Complete Button Text', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_buttons',
            'settings'   => 'lds_complete_button_txt',
        ),
    ) );

    foreach( $button_color_controls as $slug => $control ) {
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $slug, $control ) );
    }

    $wp_customize->add_section( 'lds_visual_customizer_widgets', array(
        'title'     => __( 'Widgets', 'lds_skins' ),
        'priority'  => 35,
        'panel'     => 'lds_visual_customizer'
    ) );

    $widget_settings = apply_filters( 'lds_visual_customizer_widget_colors', array(
        'lds_widget_txt' => array(
            'default'        => '',
            'type'           => 'option',
            'transport'      => 'refresh',
            'capability'     => 'edit_theme_options',
        ),
        'lds_widget_bg' => array(
            'default'        => '',
            'type'           => 'option',
            'transport'      => 'refresh',
            'capability'     => 'edit_theme_options',
        ),
        'lds_widget_header_txt' => array(
            'default'        => '',
            'type'           => 'option',
            'transport'      => 'refresh',
            'capability'     => 'edit_theme_options',
        ),
        'lds_widget_header_bg' => array(
            'default'        => '',
            'type'           => 'option',
            'transport'      => 'refresh',
            'capability'     => 'edit_theme_options',
        ),
        'lds_widget_links'  => array(
            'default'        => '',
            'type'           => 'option',
            'transport'      => 'refresh',
            'capability'     => 'edit_theme_options',
        ),
    ) );

    foreach( $widget_settings as $slug => $options ) {
        $wp_customize->add_setting( $slug, $options );
    }

    $widget_color_controls = apply_filters( 'lds_visual_customizer_widget_color_controls', array(
        'lds_widget_txt' => array(
            'label'      => __( 'Widget Text', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_widgets',
            'settings'   => 'lds_widget_txt',
        ),
        'lds_widget_bg' => array(
            'label'      => __( 'Widget Background', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_widgets',
            'settings'   => 'lds_widget_bg',
        ),
        'lds_widget_header_txt' => array(
            'label'      => __( 'Widget Header Text', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_widgets',
            'settings'   => 'lds_widget_header_txt',
        ),
        'lds_widget_header_bg' => array(
            'label'      => __( 'Widget Header Background', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_widgets',
            'settings'   => 'lds_widget_header_bg',
        ),
        'lds_widget_links' => array(
            'label'      => __( 'Widget Links', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_widgets',
            'settings'   => 'lds_widget_links',
        ),
    ) );

    foreach( $widget_color_controls as $slug => $control ) {
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $slug, $control ) );
    }

    $wp_customize->add_section( 'lds_visual_customizer_quiz', array(
        'title'     => __( 'Quiz Colors', 'lds_skins' ),
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
        'lds_quiz_txt' => array(
            'default'        => '',
            'type'           => 'option',
            'transport'      => 'refresh',
            'capability'     => 'edit_theme_options',
        ),
        'lds_quiz_border_color' => array(
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
        'lds_quiz_txt' => array(
            'label'      => __( 'Quiz Question Text', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_quiz',
            'settings'   => 'lds_quiz_txt',
        ),
        'lds_quiz_border_color' => array(
            'label'      => __( 'Quiz Question Border Color', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_quiz',
            'settings'   => 'lds_quiz_border_color',
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

    return $wp_customize;

}

function lds_create_range_field() {

    if( class_exists( 'WP_Customize_Control' ) ) {
    	class LDVC_Customize_Range extends WP_Customize_Control {
    		public $type = 'range';

            public function __construct( $manager, $id, $args = array() ) {
                parent::__construct( $manager, $id, $args );
                $defaults = array(
                    'min' => 0,
                    'max' => 10,
                    'step' => 1
                );
                $args = wp_parse_args( $args, $defaults );

                $this->min = $args['min'];
                $this->max = $args['max'];
                $this->step = $args['step'];
            }

    		public function render_content() {
    		?>
    		<label>
    			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
    			<input class='range-slider' min="<?php echo $this->min ?>" max="<?php echo $this->max ?>" step="<?php echo $this->step ?>" type='range' <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>" oninput="jQuery(this).next('input').val( jQuery(this).val() )">
                <input class="lds-range-input" onKeyUp="jQuery(this).prev('input').val( jQuery(this).val() )" type='text' value='<?php echo esc_attr( $this->value() ); ?>'>

    		</label>
    		<?php
    		}
    	}
    }

}

add_action( 'customize_controls_enqueue_scripts', 'lds_customizer_preview' );
function lds_customizer_preview() {

    wp_register_style( 'lds-customizer-preview', LDS_URL . '/assets/css/admin/customizer.css'  );
    wp_enqueue_style( 'lds-customizer-preview' );

    wp_register_script( 'lds-customizer-fields', LDS_URL . '/assets/js/lds-admin.js' );
    wp_enqueue_script( 'lds-customizer-fields' );

}
