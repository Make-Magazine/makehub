<?php
if( defined('LEARNDASH_COURSE_GRID_VERSION') ) {

    $wp_customize->add_section( 'lds_visual_customizer_course_grid', array(
        'title'     => __( 'Course Grid Add-on', 'lds_skins' ),
        'description' => __( 'Customize the appearance of the LearnDash Course Grid Add-on.', 'lds_skins' ),
        'priority'  => 35,
        'panel'     => 'lds_visual_customizer'
    ) );

    $cg_settings = apply_filters( 'ldvc_cg_settings', array(
        'lds_cg_item_border_radius' => array(
            'default'        => '',
            'type'           => 'option',
            'transport'      => 'refresh',
            'capability'     => 'edit_theme_options',
        ),
        'lds_cg_item_border_size' => array(
            'default'        => '',
            'type'           => 'option',
            'transport'      => 'refresh',
            'capability'     => 'edit_theme_options',
        ),
        'lds_cg_item_padding' => array(
            'default'        => '',
            'type'           => 'option',
            'transport'      => 'refresh',
            'capability'     => 'edit_theme_options',
        ),
        'lds_cg_item_background' => array(
            'default'       =>  '',
            'type'          => 'option',
            'transport'     =>  'refresh',
            'capability'    =>  'edit_theme_options'
        ),
        'lds_cg_item_border_color' => array(
            'default'       =>  '',
            'type'          => 'option',
            'transport'     =>  'refresh',
            'capability'    =>  'edit_theme_options'
        ),
        'lds_cg_heading_txt_size' => array(
             'default'       =>  '',
             'type'          => 'option',
             'transport'     =>  'refresh',
             'capability'    =>  'edit_theme_options'
        ),
        'lds_cg_body_txt_size' => array(
             'default'       =>  '',
             'type'          => 'option',
             'transport'     =>  'refresh',
             'capability'    =>  'edit_theme_options'
        ),
        'lds_cg_btn_txt_size' => array(
             'default'       =>  '',
             'type'          => 'option',
             'transport'     =>  'refresh',
             'capability'    =>  'edit_theme_options'
        ),
        'lds_cg_ribbon_text' => array(
            'default'       =>  '',
            'type'          => 'option',
            'transport'     =>  'refresh',
            'capability'    =>  'edit_theme_options'
        ),
        'lds_cg_ribbon_background' => array(
            'default'       =>  '',
            'type'          => 'option',
            'transport'     =>  'refresh',
            'capability'    =>  'edit_theme_options'
        ),
        'lds_cg_ribbon_free_text' => array(
            'default'       =>  '',
            'type'          => 'option',
            'transport'     =>  'refresh',
            'capability'    =>  'edit_theme_options'
        ),
        'lds_cg_ribbon_free_background' => array(
            'default'       =>  '',
            'type'          => 'option',
            'transport'     =>  'refresh',
            'capability'    =>  'edit_theme_options'
        ),
        'lds_cg_ribbon_enrolled_text' => array(
            'default'       =>  '',
            'type'          => 'option',
            'transport'     =>  'refresh',
            'capability'    =>  'edit_theme_options'
        ),
        'lds_cg_ribbon_enrolled_bg' => array(
            'default'       =>  '',
            'type'          => 'option',
            'transport'     =>  'refresh',
            'capability'    =>  'edit_theme_options'
        ),
        'lds_cg_button_bg' => array(
            'default'       =>  '',
            'type'          => 'option',
            'transport'     =>  'refresh',
            'capability'    =>  'edit_theme_options'
        ),
        'lds_cg_button_txt' => array(
            'default'       =>  '',
            'type'          => 'option',
            'transport'     =>  'refresh',
            'capability'    =>  'edit_theme_options'
        ),
        'lds_cg_item_drop_shadow' => array(
            'default'        => '',
            'type'           => 'option',
            'transport'      => 'refresh',
            'capability'     => 'edit_theme_options',
        ),
        'lds_cg_hover_effect' => array(
            'default'        => '',
            'type'           => 'option',
            'transport'      => 'refresh',
            'capability'     => 'edit_theme_options',
        ),
        'lds_cg_ribbon_style' => array(
            'default'        => '',
            'type'           => 'option',
            'transport'      => 'refresh',
            'capability'     => 'edit_theme_options',
        ),
    ) );

    foreach( $cg_settings as $slug => $options ) {
        $wp_customize->add_setting( $slug, $options );
    }

    $cg_range_settings = apply_filters( 'ldvc_cg_ranges', array(
        'lds_cg_item_border_radius' => array(
            'label'      => __( 'Card border radius', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_course_grid',
            'settings'   => 'lds_cg_item_border_radius',
            'min'        => 0,
            'max'        => 50,
            'step'       => 1,
            'default'    => 12,
        ),
        'lds_cg_item_border_size' => array(
            'label'      => __( 'Card border size', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_course_grid',
            'settings'   => 'lds_cg_item_border_size',
            'min'        => 0,
            'max'        => 50,
            'step'       => 1,
            'default'    => 3,
        ),
        'lds_cg_item_padding' => array(
            'label'      => __( 'Card padding', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_course_grid',
            'settings'   => 'lds_cg_item_padding',
            'min'        => 0,
            'max'        => 50,
            'step'       => 1,
            'default'    => 20
        ),
        'lds_cg_heading_txt_size' => array(
             'label'      => __( 'Heading Text Size', 'lds_skins' ),
             'section'    => 'lds_visual_customizer_course_grid',
             'settings'   => 'lds_cg_heading_txt_size',
             'min'        => 0,
             'max'        => 100,
             'step'       => 1,
             'default'    => 20,
        ),
        'lds_cg_body_txt_size' => array(
             'label'      => __( 'Body Text Size', 'lds_skins' ),
             'section'    => 'lds_visual_customizer_course_grid',
             'settings'   => 'lds_cg_body_txt_size',
             'min'        => 0,
             'max'        => 100,
             'step'       => 1,
             'default'    => 20,
        ),
        'lds_cg_btn_txt_size' => array(
             'label'      => __( 'Button Text Size', 'lds_skins' ),
             'section'    => 'lds_visual_customizer_course_grid',
             'settings'   => 'lds_cg_btn_txt_size',
             'min'        => 0,
             'max'        => 100,
             'step'       => 1,
             'default'    => 20,
        ),
    ) );

    foreach( $cg_range_settings as $slug => $control ) {
        $wp_customize->add_control( new LDVC_Customize_Range( $wp_customize, $slug, $control ) );
    }

    $cg_ribbon_style = array(
         'lds_cg_ribbon_style' => array(
            'label'      => __( 'Ribbon Style', 'lds_skins' ),
            'section'    => 'lds_visual_customizer_course_grid',
            'settings'   => 'lds_cg_ribbon_style',
            'type'       => 'select',
            'choices'   =>  array(
                'default'	    =>	__( 'Default', 'lds_skins' ),
                'modern'		    =>	__( 'Modern', 'lds_skins' ),
                'band'            =>    __( 'Band', 'lds_skins' ),
                'overlay'	    =>	__( 'Overlay', 'lds_skins' ),
                'tab'	         =>	__( 'Tab', 'lds_skins' ),
            ),
       ) );

       foreach( $cg_ribbon_style as $slug => $options ) {

          $wp_customize->add_control( new WP_Customize_Control(
              $wp_customize,
              $slug,
              $options
          ) );

      }

    $cg_color_pickers = apply_filters( 'ldvc_cg_colors', array(
        'lds_cg_item_background' => array(
            'label'     => __( 'Card background', 'lds_skins' ),
            'section'   =>  'lds_visual_customizer_course_grid',
            'settings'  =>  'lds_cg_item_background',
        ),
        'lds_cg_item_border_color' => array(
            'label'     => __( 'Card border color', 'lds_skins' ),
            'section'   =>  'lds_visual_customizer_course_grid',
            'settings'  =>  'lds_cg_item_border_color',
        ),
        'lds_cg_ribbon_text' => array(
            'label'     => __( 'Ribbon Text', 'lds_skins' ),
            'section'   =>  'lds_visual_customizer_course_grid',
            'settings'  =>  'lds_cg_ribbon_text',
        ),
        'lds_cg_ribbon_background' => array(
            'label'     => __( 'Ribbon Background', 'lds_skins' ),
            'section'   =>  'lds_visual_customizer_course_grid',
            'settings'  =>  'lds_cg_ribbon_background',
        ),
        'lds_cg_ribbon_free_text' => array(
            'label'     => __( 'Free: Ribbon Text', 'lds_skins' ),
            'section'   =>  'lds_visual_customizer_course_grid',
            'settings'  =>  'lds_cg_ribbon_free_text',
        ),
        'lds_cg_ribbon_free_background' => array(
            'label'     => __( 'Free: Ribbon Background', 'lds_skins' ),
            'section'   =>  'lds_visual_customizer_course_grid',
            'settings'  =>  'lds_cg_ribbon_free_background',
        ),
        'lds_cg_ribbon_enrolled_text' => array(
            'label'     => __( 'Enrolled: Ribbon Text', 'lds_skins' ),
            'section'   =>  'lds_visual_customizer_course_grid',
            'settings'  =>  'lds_cg_ribbon_enrolled_text',
        ),
        'lds_cg_ribbon_enrolled_bg' => array(
            'label'     => __( 'Enrolled: Ribbon Background', 'lds_skins' ),
            'section'   =>  'lds_visual_customizer_course_grid',
            'settings'  =>  'lds_cg_ribbon_enrolled_bg',
        ),
        'lds_cg_button_bg' => array(
            'label'     => __( 'Button Background', 'lds_skins' ),
            'section'   =>  'lds_visual_customizer_course_grid',
            'settings'  =>  'lds_cg_button_bg',
        ),
        'lds_cg_button_txt' => array(
            'label'     => __( 'Button Text', 'lds_skins' ),
            'section'   =>  'lds_visual_customizer_course_grid',
            'settings'  =>  'lds_cg_button_txt',
        ),
    ) );

    foreach( $cg_color_pickers as $slug => $control ) {
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $slug, $control ) );
    }

    $cg_card_settings = apply_filters( 'ldvc_cg_hover_effects', array(
            'lds_cg_item_drop_shadow' => array(
                'label'      => __( 'Drop shadow', 'lds_skins' ),
                'section'    => 'lds_visual_customizer_course_grid',
                'settings'   => 'lds_cg_item_drop_shadow',
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
            'lds_cg_hover_effect' => array(
                'label'      => __( 'Hover effect', 'lds_skins' ),
                'section'    => 'lds_visual_customizer_course_grid',
                'settings'   => 'lds_cg_hover_effect',
                'type'       => 'select',
                'choices'   =>  array(
                    'none'		    =>	__( 'None', 'lds_skins' ),
                    'highlight'		=>	__( 'Highlight', 'lds_skins' ),
                    'elevate'       =>  __( 'Elevate', 'lds_skins' ),
                    'flip'	        =>	__( 'Flip', 'lds_skins' ),
                    'reverse'	    =>	__( 'Reverse out', 'lds_skins' ),
                ),
            ),
        ) );


    foreach( $cg_card_settings as $slug => $options ) {

        $wp_customize->add_control( new WP_Customize_Control(
            $wp_customize,
            $slug,
            $options
        ) );

    }

}
