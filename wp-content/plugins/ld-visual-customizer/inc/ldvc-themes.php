<?php
function lds_get_themes_scaffolding() {

    $themes = apply_filters( 'ldvc_theme_slugs', array(
        'default',
        'modern',
        'sunny',
        'rustic',
        'playful',
        'minimal',
        'sleek'
    ) );

    $theme_settings = array();

    foreach( $themes as $theme ) {
        if( function_exists( 'lds_theme_' . $theme ) ) {
            $theme_settings[$theme] = call_user_func( 'lds_theme_' . $theme );
        }
    }

    $theme_settings['reset'] = lds_reset_settings();

    return $theme_settings;

}

function lds_theme_default() {
    return apply_filters( 'ldvc_default_theme_settings', array(
        'assets' => array(
            'styles'    =>  false,
            'scripts'   =>  false
        ),
        'controls' => array()
    ) );
}

function lds_theme_modern() {

    return apply_filters( 'ldvc_modern_theme_settings', array(
        'assets'    =>  array(
            'styles'    =>  array(
                'modern' =>  LDS_URL . '/assets/css/themes/modern.css',
            )
        ),
        'controls'  => array(
            // Primary Colors
            'lds_primary_color'     =>  '#23c6c8',
            'lds_secondary_color'   =>  '#59cd90',
            'lds_alert_color'       =>  '#2f4050',
            // Headings
            'lds_heading_bg'    =>  '#2f4050',
            'lds_heading_txt'   =>  '#ffffff',
            'lds_sub_heading_bg' => '#495A6A',
            'lds_sub_heading_txt' => '#ffffff',
            // Border + Radius + Spacing
            'lds_content_list_border_size'      => '0',
            'lds_content_list_border_radius'    => '0',
            'lds_status_border_radius'          => '3',
            'lds_button_border_radius'          => '0',
            'lds_pagination_border-radius'      => '3',
            'lds_content_item_border'           => 'transparent',
            'lds_content_list_spacing'          => '0',
            // Additional colors
            'lds_expanded_content_bg'   =>  '#f2f5f7',
            'lds_tertiary_bg_color'     =>  '#f2f5f7',
            'lds_tertiary_txt_color'    =>  '#686e71',
            'lds_content_item_bg'       =>  '#f6f6f7',
            'lds_item_txt'              =>  '#2f4050',
            // Icons
            /*
            'lds_sample_icon'           =>  'fa-asterisk',
            'lds_calendar_icon'         =>  'fa-calendar-o',
            'lds_content_icon'          =>  'fa-file-text',
            'lds_assignment_icon'       =>  'fa-files-o',
            'lds_download_icon'         =>  'fa-cloud-download',
            'lds_materials_icon'        =>  'fa-cubes',
            */
        ),
    ) );

}


function lds_theme_minimal() {

    return apply_filters( 'ldvc_minimal_theme_settings', array(
        'assets'    =>  array(
            'styles'    =>  array(
                'classic' =>  LDS_URL . '/assets/css/themes/minimal.css',
            )
        ),
        'controls'  => array(
            // Primary Colors
            'lds_primary_color'     =>  '',
            'lds_secondary_color'   =>  '',
            'lds_alert_color'       =>  '',
            // Headings
            'lds_heading_txt'   =>  '#556270',
            // Border + Radius + Spacing
            'lds_content_list_border_size'      => '0',
            'lds_content_list_border_radius'    => '0',
            'lds_status_border_radius'          => '0',
            'lds_button_border_radius'          => '0',
            'lds_content_item_border'           => '#dddddd',
            'lds_content_list_spacing'          => '0',
            // Additional colors
            'lds_expanded_content_bg'   =>  '#F0F0F0',
            'lds_tertiary_bg_color'     =>  'transparent',
            'lds_tertiary_txt_color'    =>  '#666',
            'lds_content_item_bg'       =>  '#ffffff',
            'lds_item_txt'              =>  '#556270',
            /* Icons
            'lds_complete_icon'         =>  'fa-asterisk',
            'lds_sample_icon'           =>  'fa-eye',
            'lds_calendar_icon'         =>  'fa-calendar-o',
            'lds_content_icon'          =>  'fa-file-text',
            'lds_assignment_icon'       =>  'fa-files-o', */
        ),
    ) );

}


function lds_theme_sunny() {

    return apply_filters( 'ldvc_sunny_theme_settings', array(
        'assets'    =>  array(
            'styles'    =>  array(
                'classic' =>  LDS_URL . '/assets/css/themes/sunny.css',
            )
        ),
        'controls'  => array(
            // Primary Colors
            'lds_primary_color'     =>  '#69D2E7',
            'lds_secondary_color'   =>  '#FA6900',
            'lds_alert_color'       =>  '#F38630',
            // Headings
            'lds_heading_txt'   =>  '#333333',
            // Border + Radius + Spacing
            'lds_content_list_border_size'      => '5',
            'lds_content_list_border_radius'    => '8',
            'lds_status_border_radius'          => '4',
            'lds_button_border_radius'          => '8',
            'lds_content_item_border'           => '#69D2E7',
            'lds_content_list_spacing'          => '',
            // Additional colors
            'lds_expanded_content_bg'   =>  '#effaf9',
            'lds_tertiary_bg_color'     =>  '#effaf9',
            'lds_tertiary_txt_color'    =>  '#A7DBD8',
            'lds_content_item_bg'       =>  '#ffffff',
            'lds_item_txt'              =>  '#666666',
            /*
            'lds_complete_icon'         =>  'fa-asterisk',
            'lds_sample_icon'           =>  'fa-eye',
            'lds_calendar_icon'         =>  'fa-calendar-o',
            'lds_content_icon'          =>  'fa-file-text',
            'lds_assignment_icon'       =>  'fa-files-o',
            'lds_quiz_icon'             =>  'fa-cloud',
            'lds_arrow_down_icon'       =>  'fa-arrow-circle-down',
            'lds_arrow_up_icon'         =>  'fa-arrow-circle-up',
            'lds_arrow_right_icon'      =>  'fa-arrow-circle-right',
            'lds_arrow_left_icon'       =>  'fa-arrow-circle-left' */
        ),
    ) );

}

function lds_theme_sleek() {

    return apply_filters( 'ldvc_sleek_theme_settings', array(
        'assets'    =>  array(
            'styles'    =>  array(
                'classic' =>  LDS_URL . '/assets/css/themes/sleek.css',
            )
        ),
        'controls'  => array(
            // Primary Colors
            'lds_primary_color'     =>  '',
            'lds_secondary_color'   =>  '',
            'lds_alert_color'       =>  '',
            // Headings
            'lds_heading_txt'   =>  '#fff',
            'lds_heading_bg'    =>  '#37bc9b',
            'lds_sub_heading_bg' => '#1EA382',
            'lds_sub_heading_txt' => '#ffffff',
            'lds_section_heading_font_size' =>  '12',
            // Border + Radius + Spacing
            'lds_content_list_border_size'      => '0',
            'lds_content_list_border_radius'    => '3',
            'lds_status_border_radius'          => '3',
            'lds_button_border_radius'          => '3',
            'lds_content_item_border'           => 'transparent',
            'lds_content_list_spacing'          => '0',
            // Additional colors
            'lds_expanded_content_bg'   =>  '#fafafa',
            'lds_tertiary_bg_color'     =>  '#fff',
            'lds_tertiary_txt_color'    =>  '#656565',
            'lds_content_item_bg'       =>  '#fff',
            'lds_item_txt'              =>  '#3d3723',
            /*
            'lds_complete_icon'         =>  'fa-asterisk',
            'lds_sample_icon'           =>  'fa-eye',
            'lds_calendar_icon'         =>  'fa-calendar-o',
            'lds_content_icon'          =>  'fa-file-text',
            'lds_assignment_icon'       =>  'fa-files-o', */
        ),
    ) );

}


function lds_theme_rustic() {

    return apply_filters( 'ldvc_rustic_theme_settings', array(
        'assets'    =>  array(
            'styles'    =>  array(
                'classic' =>  LDS_URL . '/assets/css/themes/rustic.css',
            )
        ),
        'controls'  => array(
            // Primary Colors
            'lds_primary_color'     =>  '#99B2B7',
            'lds_secondary_color'   =>  '#7A6A53',
            'lds_alert_color'       =>  '#EDC951',
            // Headings
            'lds_heading_txt'   =>  '#99B2B7',
            // Border + Radius + Spacing
            'lds_content_list_border_size'      => '0',
            'lds_content_list_border_radius'    => '3',
            'lds_status_border_radius'          => '3',
            'lds_button_border_radius'          => '3',
            'lds_content_item_border'           => '',
            'lds_content_list_spacing'          => '',
            // Additional colors
            'lds_expanded_content_bg'   =>  '#f0f4f2',
            'lds_tertiary_bg_color'     =>  '#f0f4f2',
            'lds_tertiary_txt_color'    =>  '#99B2B7',
            'lds_content_item_bg'       =>  '#f6f4f0',
            'lds_item_txt'              =>  '#7A6A53',
            /*
            'lds_complete_icon'         =>  'fa-star',
            'lds_sample_icon'           =>  'fa-bolt',
            'lds_calendar_icon'         =>  'fa-calendar-o',
            'lds_quiz_icon'             =>  'fa-cloud',
            'lds_arrow_down_icon'       =>  'fa-arrow-down',
            'lds_arrow_up_icon'         =>  'fa-arrow-up',
            'lds_arrow_right_icon'      =>  'fa-arrow-right',
            'lds_arrow_left_icon'       =>  'fa-arrow-left' */
        ),
    ) );

}


function lds_theme_playful() {

    return apply_filters( 'ldvc_playful_theme_settings', array(
        'assets'    =>  array(
            'styles'    =>  array(
                'classic' =>  LDS_URL . '/assets/css/themes/playful.css',
            )
        ),
        'controls'  => array(
            // Primary Colors
            'lds_primary_color'     =>  '#00A8C6',
            'lds_secondary_color'   =>  '#AEE239',
            'lds_alert_color'       =>  '#E84A5F',
            // Headings
            'lds_heading_txt'           =>  '#00A8C6',
            'lds_heading_font_family'   =>  'Permanent Marker',
            'lds_font_family'           =>  'Architects Daughter',
            // Border + Radius + Spacing
            'lds_content_list_border_size'      => '3',
            'lds_content_list_border_radius'    => '12',
            'lds_status_border_radius'          => '8',
            'lds_button_border_radius'          => '12',
            'lds_content_item_border'           => '#00A8C6',
            'lds_content_list_spacing'          => '',
            // Additional colors
            'lds_expanded_content_bg'   =>  'transparent',
            'lds_tertiary_bg_color'     =>  '#f3feff',
            'lds_tertiary_txt_color'    =>  '#40C0CB',
            'lds_content_item_bg'       =>  '#ffffff',
            'lds_item_txt'              =>  '#00A8C6',
            /*
            'lds_complete_icon'         =>  'fa-thumbs-up',
            'lds_sample_icon'           =>  'fa-hand-o-right',
            'lds_quiz_icon'             =>  'fa-graduation-cap',
            'lds_arrow_down_icon'       =>  'fa-hand-o-down',
            'lds_arrow_up_icon'         =>  'fa-hand-o-up',
            'lds_arrow_right_icon'      =>  'fa-hand-o-right',
            'lds_arrow_left_icon'       =>  'fa-hand-o-left' */
        ),
    ) );

}
