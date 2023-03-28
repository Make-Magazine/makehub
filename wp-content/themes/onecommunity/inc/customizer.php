<?php
/**
 * Customizer functionality
 */

function onecommunity_logo() {
    add_theme_support( 'custom-logo', array(
        'width'       => 260,
        'height'      => 70,
        'flex-width' => true,
        'flex-height' => true,
    ) );
}
add_action('after_setup_theme', 'onecommunity_logo');


add_action( 'customize_register' , 'onecommunity_theme_options' );

function onecommunity_theme_options( $wp_customize ) {

/////////////////////////////////////////////////////////////////////////////////////////////

$wp_customize->add_section( 'onecommunity_general_section' , array(
    'title'       => 'General',
    'priority'    => 1,
    'description' => 'Change general settings.',
) );


$wp_customize->add_setting( 'onecommunity_activity_columns', array(
        'default' => 2,
        'sanitize_callback' => 'sanitize_text_field'
    ) );

$wp_customize->add_control( 'onecommunity_activity_columns', array(
    'type'     => 'select',
    'choices'  => array(
            '2'  => '2 columns',
            '3' => '3 columns'
    ),
    'priority' => 1,
    'section'  => 'onecommunity_general_section',
    'settings'  => 'onecommunity_activity_columns',
    'label'    => 'Number of columns on the activity page',
) );


$wp_customize->add_setting( 'onecommunity_profile_columns', array(
        'default' => 2,
        'sanitize_callback' => 'sanitize_text_field'
    ) );

$wp_customize->add_control( 'onecommunity_profile_columns', array(
    'type'     => 'select',
    'choices'  => array(
            '2'  => '2 columns',
            '3' => '3 columns'
    ),
    'priority' => 1,
    'section'  => 'onecommunity_general_section',
    'settings'  => 'onecommunity_profile_columns',
    'label'    => 'Number of columns on the member profile',
) );


$wp_customize->add_setting( 'onecommunity_single_group_columns', array(
        'default' => 2,
        'sanitize_callback' => 'sanitize_text_field'
    ) );

$wp_customize->add_control( 'onecommunity_single_group_columns', array(
    'type'     => 'select',
    'choices'  => array(
            '2'  => '2 columns',
            '3' => '3 columns'
    ),
    'priority' => 1,
    'section'  => 'onecommunity_general_section',
    'settings'  => 'onecommunity_single_group_columns',
    'label'    => 'Number of columns on the single group page',
) );



$wp_customize->add_setting( 'onecommunity_bp_groups_blog_enable', array(
        'default' => false,
        'sanitize_callback' => 'sanitize_text_field'
    ) );

$wp_customize->add_control( 'onecommunity_bp_groups_blog_enable', array(
    'type'     => 'checkbox',
    'priority' => 1,
    'section'  => 'onecommunity_general_section',
    'settings'  => 'onecommunity_bp_groups_blog_enable',
    'label'    => 'Enable the blog posts tab inside the buddypress groups and create new taxonomy (you can create the new blog groups under Posts -> BP Group Post, slug should be the same like the ID of BP group you want assign the posts)',
) );


$wp_customize->add_setting( 'onecommunity_bp_groups_blog_position_hierarchy', array(
    'default' => 10,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_bp_groups_blog_position_hierarchy', array(
    'type'     => 'number',
    'priority' => 1,
    'section'  => 'onecommunity_general_section',
    'settings'  => 'onecommunity_bp_groups_blog_position_hierarchy',
    'label'    => 'BP Groups blog tab position hierarchy',
) );


$wp_customize->add_setting( 'onecommunity_bp_user_blog_enable', array(
        'default' => false,
        'sanitize_callback' => 'sanitize_text_field'
    ) );

$wp_customize->add_control( 'onecommunity_bp_user_blog_enable', array(
    'type'     => 'checkbox',
    'priority' => 1,
    'section'  => 'onecommunity_general_section',
    'settings'  => 'onecommunity_bp_user_blog_enable',
    'label'    => 'Enable the blog posts tab on the buddypress user profile',
) );


$wp_customize->add_setting( 'onecommunity_bp_user_blog_position_hierarchy', array(
    'default' => 10,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_bp_user_blog_position_hierarchy', array(
    'type'     => 'number',
    'priority' => 1,
    'section'  => 'onecommunity_general_section',
    'settings'  => 'onecommunity_bp_user_blog_position_hierarchy',
    'label'    => 'BP User blog tab position hierarchy',
) );


$wp_customize->add_setting( 'onecommunity_preloader_enable', array(
        'default' => false,
        'sanitize_callback' => 'sanitize_text_field'
    ) );

$wp_customize->add_control( 'onecommunity_preloader_enable', array(
    'type'     => 'checkbox',
    'priority' => 1,
    'section'  => 'onecommunity_general_section',
    'settings'  => 'onecommunity_preloader_enable',
    'label'    => 'Enable preloader',
) );


$wp_customize->add_setting( 'onecommunity_sidenav_enable', array(
        'default' => true,   
        'sanitize_callback' => 'sanitize_text_field'
    ) );

$wp_customize->add_control( 'onecommunity_sidenav_enable', array(
    'type'     => 'checkbox',
    'priority' => 1,
    'section'  => 'onecommunity_general_section',
    'settings'  => 'onecommunity_sidenav_enable',
    'label'    => 'Show side panel with members',
) );


$wp_customize->add_setting( 'onecommunity_sidenav_featured_member_enable', array(
        'default' => true,   
        'sanitize_callback' => 'sanitize_text_field'
    ) );

$wp_customize->add_control( 'onecommunity_sidenav_featured_member_enable', array(
    'type'     => 'checkbox',
    'priority' => 1,
    'section'  => 'onecommunity_general_section',
    'settings'  => 'onecommunity_sidenav_featured_member_enable',
    'label'    => 'Show featured member on the side panel',
) );


$wp_customize->add_setting( 'onecommunity_login_popup_frontpage_enable', array(
        'default' => false,   
        'sanitize_callback' => 'sanitize_text_field'
    ) );

$wp_customize->add_control( 'onecommunity_login_popup_frontpage_enable', array(
    'type'     => 'checkbox',
    'priority' => 1,
    'section'  => 'onecommunity_general_section',
    'settings'  => 'onecommunity_login_popup_frontpage_enable',
    'label'    => 'Enable login popup on the frontpage',
) );


$wp_customize->add_setting( 'onecommunity_blog_breadcrumb_title' , array(
    'default' => "News",
    'sanitize_callback' => 'sanitize_text_field',
) );

$wp_customize->add_control( 'onecommunity_blog_breadcrumb_title', array(
    'type'     => 'text',
    'priority' => 1,
    'section'  => 'onecommunity_general_section',
    'settings'  => 'onecommunity_blog_breadcrumb_title',
    'label'    => 'Breadcrumb title of the blog posts listing page',
) );


$wp_customize->add_setting( 'onecommunity_blog_slug' , array(
    'default' => "news",
    'sanitize_callback' => 'sanitize_text_field',
) );

$wp_customize->add_control( 'onecommunity_blog_slug', array(
    'type'     => 'text',
    'priority' => 1,
    'section'  => 'onecommunity_general_section',
    'settings'  => 'onecommunity_blog_slug',
    'label'    => 'Breadcrumb slug of the blog posts listing page',
) );


$wp_customize->add_setting( 'onecommunity_shortcodes_date_format' , array(
    'default' => "l, M j",
    'sanitize_callback' => 'sanitize_text_field',
) );

$wp_customize->add_control( 'onecommunity_shortcodes_date_format', array(
    'type'     => 'text',
    'priority' => 1,
    'section'  => 'onecommunity_general_section',
    'settings'  => 'onecommunity_shortcodes_date_format',
    'label'    => 'Date format for shortcodes',
) );


/////////////////////////////////////////////////////////////////////////////////////////////


$wp_customize->add_section( 'onecommunity_colors_section' , array(
    'title'       => 'Colors',
    'priority'    => 99,
    'description' => 'Change colors of theme.',
));


$wp_customize->add_setting( 'onecommunity_main_color',
    array(
    'default' => '#ff9801',
    'sanitize_callback' => 'sanitize_hex_color'
));

$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize, 'onecommunity_main_color', array(
    'label'      => 'Main color',
    'priority' => 1,
    'section'  => 'onecommunity_colors_section',
    'settings' => 'onecommunity_main_color',
)));



$wp_customize->add_setting( 'onecommunity_main_color_hover',
    array(
    'default' => '#d37d00',
    'sanitize_callback' => 'sanitize_hex_color'
));

$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize, 'onecommunity_main_color_hover', array(
    'label'      => 'Main hover color',
    'priority' => 1,
    'section'  => 'onecommunity_colors_section',
    'settings' => 'onecommunity_main_color_hover',
)));




$wp_customize->add_setting( 'onecommunity_unselected_tabs_bg',
    array(
    'default' => '#d1d1d1',
    'sanitize_callback' => 'sanitize_hex_color'
));

$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize, 'onecommunity_unselected_tabs_bg', array(
    'label'      => 'Unselected tabs background color',
    'priority' => 1,
    'section'  => 'onecommunity_colors_section',
    'settings' => 'onecommunity_unselected_tabs_bg',
)));


$wp_customize->add_setting( 'onecommunity_subnav_bg_selected',
    array(
    'default' => '#cdcdcd',
    'sanitize_callback' => 'sanitize_hex_color'
));

$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize, 'onecommunity_subnav_bg_selected', array(
    'label'      => 'Selected sub tabs background color',
    'priority' => 1,
    'section'  => 'onecommunity_colors_section',
    'settings' => 'onecommunity_subnav_bg_selected',
)));



$wp_customize->add_setting( 'onecommunity_button_bg',
    array(
    'default' => '#ff9801',
    'sanitize_callback' => 'sanitize_hex_color'
));

$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize, 'onecommunity_button_bg', array(
    'label'      => 'General button background color',
    'priority' => 1,
    'section'  => 'onecommunity_colors_section',
    'settings' => 'onecommunity_button_bg',
) ) );



$wp_customize->add_setting( 'onecommunity_cover_bg' , array(
    'default' => "linear-gradient(0deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.6) 11%, rgba(0,0,0,0) 100%)",
    'sanitize_callback' => 'esc_attr',
) );

$wp_customize->add_control( 'onecommunity_cover_bg', array(
    'type'     => 'text',
    'priority' => 1,
    'section'  => 'onecommunity_colors_section',
    'settings'  => 'onecommunity_cover_bg',
    'label'    => 'Cover background gradient (BP Groups and User Profiles)',
) );


/////////////////////////////////////////////////////////////////////////////////////////////

$wp_customize->add_section( 'onecommunity_footer_section' , array(
    'title'       => 'Footer',
    'priority'    => 99,
    'description' => 'Change footer settings.',
) );


$wp_customize->add_setting( 'onecommunity_footer_columns_enable', array(
        'default' => false,
        'sanitize_callback' => 'sanitize_text_field'
    ) );

$wp_customize->add_control( 'onecommunity_footer_columns_enable', array(
    'type'     => 'checkbox',
    'priority' => 1,
    'section'  => 'onecommunity_footer_section',
    'settings'  => 'onecommunity_footer_columns_enable',
    'label'    => 'Enable footer columns',
) );


$wp_customize->add_setting( 'onecommunity_footer_logo_enable', array(
        'default' => true,
        'sanitize_callback' => 'sanitize_text_field'
    ) );

$wp_customize->add_control( 'onecommunity_footer_logo_enable', array(
    'type'     => 'checkbox',
    'priority' => 1,
    'section'  => 'onecommunity_footer_section',
    'settings'  => 'onecommunity_footer_logo_enable',
    'label'    => 'Footer logo enable',
) );


$wp_customize->add_setting( 'onecommunity_footer_logo' , array(
    'sanitize_callback' => 'esc_url_raw',
) );

$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'onecommunity_footer_logo', array(
    'label'    => 'Footer logo (260px x 70px)',
    'priority' => 1,
    'section'  => 'onecommunity_footer_section',
    'settings'  => 'onecommunity_footer_logo',
) ) );


$wp_customize->add_setting( 'onecommunity_footer_info_enable', array(
        'default' => true,
        'sanitize_callback' => 'sanitize_text_field'
    ) );

$wp_customize->add_control( 'onecommunity_footer_info_enable', array(
    'type'     => 'checkbox',
    'priority' => 1,
    'section'  => 'onecommunity_footer_section',
    'settings'  => 'onecommunity_footer_info_enable',
    'label'    => 'Footer side info enable.',
) );


$wp_customize->add_setting( 'onecommunity_footer_info' , array(
    'default' => "A social network service in a box. Get your community to the higher level. It's easy with OneCommunity.",
    'sanitize_callback' => 'esc_attr',
) );

$wp_customize->add_control( 'onecommunity_footer_info', array(
    'type'     => 'textarea',
    'priority' => 2,
    'section'  => 'onecommunity_footer_section',
    'settings'  => 'onecommunity_footer_info',
    'label'    => 'Footer site info:',
) );


$wp_customize->add_setting( 'onecommunity_footer_menu_1_title' , array(
    'default' => "Informations",
    'sanitize_callback' => 'esc_attr',
) );

$wp_customize->add_control( 'onecommunity_footer_menu_1_title', array(
    'type'     => 'text',
    'priority' => 2,
    'section'  => 'onecommunity_footer_section',
    'settings'  => 'onecommunity_footer_menu_1_title',
    'label'    => 'Footer menu 1 title',
) );


$wp_customize->add_setting( 'onecommunity_footer_menu_2_title' , array(
    'default' => "Legal",
    'sanitize_callback' => 'esc_attr',
) );

$wp_customize->add_control( 'onecommunity_footer_menu_2_title', array(
    'type'     => 'text',
    'priority' => 2,
    'section'  => 'onecommunity_footer_section',
    'settings'  => 'onecommunity_footer_menu_2_title',
    'label'    => 'Footer menu 2 title',
) );


$wp_customize->add_setting( 'onecommunity_footer_social_title' , array(
    'default' => "Follow Us",
    'sanitize_callback' => 'esc_attr',
) );

$wp_customize->add_control( 'onecommunity_footer_social_title', array(
    'type'     => 'text',
    'priority' => 2,
    'section'  => 'onecommunity_footer_section',
    'settings'  => 'onecommunity_footer_social_title',
    'label'    => 'Social icons section title',
) );


$wp_customize->add_setting( 'onecommunity_social_1' , array(
    'sanitize_callback' => 'esc_attr',
) );

$wp_customize->add_control( 'onecommunity_social_1', array(
    'type'     => 'checkbox',
    'priority' => 2,
    'section'  => 'onecommunity_footer_section',
    'settings'  => 'onecommunity_social_1',
    'label'    => 'Disable Facebook icon',
) );


$wp_customize->add_setting( 'onecommunity_social_1_link' , array(
    'default' => '//facebook.com',
    'sanitize_callback' => 'esc_url_raw',
) );

$wp_customize->add_control( 'onecommunity_social_1_link', array(
    'type'     => 'text',
    'priority' => 2,
    'section'  => 'onecommunity_footer_section',
    'settings'  => 'onecommunity_social_1_link',
    'label'    => 'Facebook link',
) );


$wp_customize->add_setting( 'onecommunity_social_2' , array(
    'sanitize_callback' => 'esc_attr',
) );


$wp_customize->add_control( 'onecommunity_social_2', array(
    'type'     => 'checkbox',
    'priority' => 2,
    'section'  => 'onecommunity_footer_section',
    'settings'  => 'onecommunity_social_2',
    'label'    => 'Disable YouTube icon',
) );


$wp_customize->add_setting( 'onecommunity_social_2_link' , array(
    'default' => '//youtube.com',
    'sanitize_callback' => 'esc_url_raw',
) );

$wp_customize->add_control( 'onecommunity_social_2_link', array(
    'type'     => 'url',
    'priority' => 2,
    'section'  => 'onecommunity_footer_section',
    'settings'  => 'onecommunity_social_2_link',
    'label'    => 'Your YouTube channel link',
) );



$wp_customize->add_setting( 'onecommunity_social_3' , array(
    'sanitize_callback' => 'esc_attr',
) );


$wp_customize->add_control( 'onecommunity_social_3', array(
    'type'     => 'checkbox',
    'priority' => 2,
    'section'  => 'onecommunity_footer_section',
    'settings'  => 'onecommunity_social_3',
    'label'    => 'Disable Twitter icon',
) );


$wp_customize->add_setting( 'onecommunity_social_3_link' , array(
    'default' => '//twitter.com',
    'sanitize_callback' => 'esc_url_raw',
) );

$wp_customize->add_control( 'onecommunity_social_3_link', array(
    'type'     => 'url',
    'priority' => 2,
    'section'  => 'onecommunity_footer_section',
    'settings'  => 'onecommunity_social_3_link',
    'label'    => 'Your Twitter profile link',
) );


$wp_customize->add_setting( 'onecommunity_social_4' , array(
    'sanitize_callback' => 'esc_attr',
) );

$wp_customize->add_control( 'onecommunity_social_4', array(
    'type'     => 'checkbox',
    'priority' => 2,
    'section'  => 'onecommunity_footer_section',
    'settings'  => 'onecommunity_social_4',
    'label'    => 'Disable RSS icon',
) );


$wp_customize->add_setting( 'onecommunity_social_5' , array(
    'sanitize_callback' => 'esc_attr',
) );

$wp_customize->add_control( 'onecommunity_social_5', array(
    'type'     => 'checkbox',
    'priority' => 2,
    'section'  => 'onecommunity_footer_section',
    'settings'  => 'onecommunity_social_5',
    'label'    => 'Disable Instagram icon',
) );


$wp_customize->add_setting( 'onecommunity_social_5_link' , array(
    'default' => '//instagram.com',
    'sanitize_callback' => 'esc_url_raw',
) );

$wp_customize->add_control( 'onecommunity_social_5_link', array(
    'type'     => 'url',
    'priority' => 2,
    'section'  => 'onecommunity_footer_section',
    'settings'  => 'onecommunity_social_5_link',
    'label'    => 'Your Instagram profile link',
) );


$wp_customize->add_setting( 'onecommunity_footer_copyright' , array(
    'default' => "All rights reserved. No parts of this site may be copied without our written permission.",
    'sanitize_callback' => 'esc_attr',
) );

$wp_customize->add_control( 'onecommunity_footer_copyright', array(
    'type'     => 'textarea',
    'priority' => 2,
    'section'  => 'onecommunity_footer_section',
    'settings'  => 'onecommunity_footer_copyright',
    'label'    => 'Footer copyright info',
) );


/////////////////////////////////////////////////////////////////////////////////////////////


$wp_customize->add_section( 'onecommunity_font_section' , array(
    'title'       => 'Fonts',
    'priority'    => 99,
    'description' => 'Change fonts',

) );


$wp_customize->add_setting( 'onecommunity_font_name' , array(
    'default' => 'Nunito',
    'sanitize_callback' => 'esc_attr',
) );

$wp_customize->add_control( 'onecommunity_font_name', array(
    'type'     => 'text',
    'priority' => 1,
    'section'  => 'onecommunity_font_section',
    'settings'  => 'onecommunity_font_name',
    'label'    => 'Name of the font:',
) );



$wp_customize->add_setting( 'onecommunity_font_url' , array(
    'default' => '//fonts.googleapis.com/css?family=Nunito:400,400i,600,700,800,900&display=swap',
    'sanitize_callback' => 'esc_url_raw',
) );

$wp_customize->add_control( 'onecommunity_font_url', array(
    'type'     => 'url',
    'priority' => 1,
    'section'  => 'onecommunity_font_section',
    'settings'  => 'onecommunity_font_url',
    'label'    => 'URL of the font:',
) );


$wp_customize->add_setting( 'onecommunity_font_2_enable', array(
        'sanitize_callback' => 'sanitize_text_field'
    ) );

$wp_customize->add_control( 'onecommunity_font_2_enable', array(
    'type'     => 'checkbox',
    'priority' => 1,
    'section'  => 'onecommunity_font_section',
    'settings'  => 'onecommunity_font_2_enable',
    'label'    => 'Enable the 2nd font if you want to use a different font for content.',
) );


$wp_customize->add_setting( 'onecommunity_font_name_2' , array(
    'sanitize_callback' => 'esc_attr'
) );

$wp_customize->add_control( 'onecommunity_font_name_2', array(
    'type'     => 'text',
    'priority' => 1,
    'section'  => 'onecommunity_font_section',
    'settings'  => 'onecommunity_font_name_2',
    'label'    => 'Name of the 2nd font:',
) );


$wp_customize->add_setting( 'onecommunity_font_url_2' , array(
    'default' => 'https://fonts.googleapis.com/',
    'sanitize_callback' => 'esc_url_raw',
) );

$wp_customize->add_control( 'onecommunity_font_url_2', array(
    'type'     => 'url',
    'priority' => 1,
    'section'  => 'onecommunity_font_section',
    'settings'  => 'onecommunity_font_url_2',
    'label'    => 'URL of the 2nd font:',
) );

/////////////////////////////////////////////////////////////////////////////////////////////

$wp_customize->add_section( 'onecommunity_intranet_section' , array(
    'title'       => 'Intranet',
    'priority'    => 99,
    'description' => '',
) );


$wp_customize->add_setting( 'onecommunity_intranet_enable', array(
        'sanitize_callback' => 'sanitize_text_field'
    ) );

$wp_customize->add_control( 'onecommunity_intranet_enable', array(
    'type'     => 'checkbox',
    'priority' => 1,
    'section'  => 'onecommunity_intranet_section',
    'settings'  => 'onecommunity_intranet_enable',
    'label'    => 'Enable intranet. Your entire website will be available for registered members only. Guests can browse pages with following slugs: login, register, intranet, recovery. You should create landing page for guests with the intranet slug.',
) );


$wp_customize->add_setting( 'onecommunity_intranet_pages', array(
        'default' => 'login,register,intranet,recovery',
        'sanitize_callback' => 'esc_attr',
    ) );

$wp_customize->add_control( 'onecommunity_intranet_pages', array(
    'type'     => 'text',
    'priority' => 1,
    'section'  => 'onecommunity_intranet_section',
    'settings'  => 'onecommunity_intranet_pages',
    'label'    => 'Put slugs of pages available for guests:',
) );

$wp_customize->add_setting( 'onecommunity_intranet_landing', array(
        'default' => 'intranet',
        'sanitize_callback' => 'esc_attr',
    ) );

$wp_customize->add_control( 'onecommunity_intranet_landing', array(
    'type'     => 'text',
    'priority' => 1,
    'section'  => 'onecommunity_intranet_section',
    'settings'  => 'onecommunity_intranet_landing',
    'label'    => 'Slug of the landing page:',
) );

/////////////////////////////////////////////////////////////////////////////////////////////

$wp_customize->add_section( 'onecommunity_live_notifications' , array(
    'title'       => 'Live notifications',
    'priority'    => 99,
    'description' => 'Enable Live Notifications',
) );



$wp_customize->add_setting( 'onecommunity_live_notifications_enable', array(
        'sanitize_callback' => 'sanitize_text_field'
    ) );

$wp_customize->add_control( 'onecommunity_live_notifications_enable', array(
    'type'     => 'checkbox',
    'priority' => 1,
    'section'  => 'onecommunity_live_notifications',
    'settings'  => 'onecommunity_live_notifications_enable',
    'label'    => 'Enable live notifications. Please keep in mind it will increase your server resources usage if you have many online visitors.',
) );



$wp_customize->add_setting( 'onecommunity_live_notifications_interval', array(
        'default' => 15,
        'sanitize_callback' => 'sanitize_text_field'
    ) );

$wp_customize->add_control( 'onecommunity_live_notifications_interval', array(
    'type'     => 'select',
    'choices'  => array(
            '15'  => '15 seconds',
            '30' => '30 seconds',
            '45'  => '45 seconds',
            '60' => '60 seconds',
            '75' => '75 seconds',
            '90' => '90 seconds',
            '120' => '120 seconds'
    ),
    'priority' => 1,
    'section'  => 'onecommunity_live_notifications',
    'settings'  => 'onecommunity_live_notifications_interval',
    'label'    => 'Heartbeat API time interval',
) );


/////////////////////////////////////////////////////////////////////////////////////////////

$wp_customize->add_section( 'onecommunity_dark_mode' , array(
    'title'       => 'Dark Mode',
    'priority'    => 99,
    'description' => '',
) );



$wp_customize->add_setting( 'onecommunity_dark_mode_enable' , array(
    'sanitize_callback' => 'esc_url_raw',
    'default' => false
) );


$wp_customize->add_control( 'onecommunity_dark_mode_enable', array(
    'type'     => 'checkbox',
    'priority' => 1,
    'section'  => 'onecommunity_dark_mode',
    'settings'  => 'onecommunity_dark_mode_enable',
    'label'    => 'Enable dark mode',
) );


$wp_customize->add_setting( 'onecommunity_dark_mode_logo' , array(
    'sanitize_callback' => 'esc_url_raw',
) );

$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'onecommunity_dark_mode_logo', array(
    'label'    => 'Logo visible under dark mode (260px x 70px)',
    'priority' => 1,
    'section'  => 'onecommunity_dark_mode',
    'settings'  => 'onecommunity_dark_mode_logo',
) ) );



$wp_customize->add_setting( 'onecommunity_footer_logo_dark_mode' , array(
    'sanitize_callback' => 'esc_url_raw',
) );

$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'onecommunity_footer_logo_dark_mode', array(
    'label'    => 'Footer logo visible under dark mode (260px x 70px)',
    'priority' => 1,
    'section'  => 'onecommunity_dark_mode',
    'settings'  => 'onecommunity_footer_logo_dark_mode',
) ) );


$wp_customize->add_setting( 'onecommunity_dark_mode_bg',
    array(
    'default' => '#1e1a1b',
    'sanitize_callback' => 'sanitize_hex_color'
));

$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize, 'onecommunity_dark_mode_bg', array(
    'label'      => 'Main background color',
    'priority' => 1,
    'section'  => 'onecommunity_dark_mode',
    'settings' => 'onecommunity_dark_mode_bg',
)));


$wp_customize->add_setting( 'onecommunity_dark_mode_bg_lighter',
    array(
    'default' => '#282627',
    'sanitize_callback' => 'sanitize_hex_color'
));

$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize, 'onecommunity_dark_mode_bg_lighter', array(
    'label'      => 'Lighter background color',
    'priority' => 1,
    'section'  => 'onecommunity_dark_mode',
    'settings' => 'onecommunity_dark_mode_bg_lighter',
)));


$wp_customize->add_setting( 'onecommunity_dark_mode_bg_darker',
    array(
    'default' => '#110f0f',
    'sanitize_callback' => 'sanitize_hex_color'
));

$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize, 'onecommunity_dark_mode_bg_darker', array(
    'label'      => 'Darker background color',
    'priority' => 1,
    'section'  => 'onecommunity_dark_mode',
    'settings' => 'onecommunity_dark_mode_bg_darker',
)));


$wp_customize->add_setting( 'onecommunity_dark_mode_bg_forms',
    array(
    'default' => '#212121',
    'sanitize_callback' => 'sanitize_hex_color'
));

$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize, 'onecommunity_dark_mode_bg_forms', array(
    'label'      => 'Forms background color',
    'priority' => 1,
    'section'  => 'onecommunity_dark_mode',
    'settings' => 'onecommunity_dark_mode_bg_forms',
)));


$wp_customize->add_setting( 'onecommunity_dark_mode_background_1_row',
    array(
    'default' => '#252122',
    'sanitize_callback' => 'sanitize_hex_color'
));

$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize, 'onecommunity_dark_mode_background_1_row', array(
    'label'      => 'Borders color (darker)',
    'priority' => 1,
    'section'  => 'onecommunity_dark_mode',
    'settings' => 'onecommunity_dark_mode_background_1_row',
)));



$wp_customize->add_setting( 'onecommunity_dark_mode_font_color',
    array(
    'default' => '#f0f0f0',
    'sanitize_callback' => 'sanitize_hex_color'
));

$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize, 'onecommunity_dark_mode_font_color', array(
    'label'      => 'Font color',
    'priority' => 1,
    'section'  => 'onecommunity_dark_mode',
    'settings' => 'onecommunity_dark_mode_font_color',
)));


$wp_customize->add_setting( 'onecommunity_dark_mode_font_color_darker',
    array(
    'default' => '#848484',
    'sanitize_callback' => 'sanitize_hex_color'
));

$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize, 'onecommunity_dark_mode_font_color_darker', array(
    'label'      => 'Font color (darker, e.g. links)',
    'priority' => 1,
    'section'  => 'onecommunity_dark_mode',
    'settings' => 'onecommunity_dark_mode_font_color_darker',
)));


$wp_customize->add_setting( 'onecommunity_dark_mode_borders_color',
    array(
    'default' => '#282627',
    'sanitize_callback' => 'sanitize_hex_color'
));

$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize, 'onecommunity_dark_mode_borders_color', array(
    'label'      => 'Borders color',
    'priority' => 1,
    'section'  => 'onecommunity_dark_mode',
    'settings' => 'onecommunity_dark_mode_borders_color',
)));


$wp_customize->add_setting( 'onecommunity_dark_mode_borders_color_darker',
    array(
    'default' => '#151415',
    'sanitize_callback' => 'sanitize_hex_color'
));

$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize, 'onecommunity_dark_mode_borders_color_darker', array(
    'label'      => 'Borders color (darker)',
    'priority' => 1,
    'section'  => 'onecommunity_dark_mode',
    'settings' => 'onecommunity_dark_mode_borders_color_darker',
)));



/////////////////////////////////////////////////////////////////////////////////////////////

$wp_customize->add_section( 'onecommunity_transients_section' , array(
    'title'       => 'Transients (fragment cache)',
    'priority'    => 99,
    'description' => '',
) );


$wp_customize->add_setting( 'onecommunity_transient_header_menu_enable', array(
        'default' => 0,
        'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_header_menu_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_header_menu_enable',
    'label'    => 'Enable transients for the header menu',
) );


$wp_customize->add_setting( 'onecommunity_transient_header_menu_expiration', array(
    'default' => 10080,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_header_menu_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_header_menu_expiration',
    'label'    => 'Header menu cache expiration time in minutes:',
) );



$wp_customize->add_setting( 'onecommunity_transient_footer_enable', array(
        'default' => 0,
        'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_footer_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_footer_enable',
    'label'    => 'Enable transients for the footer',
) );


$wp_customize->add_setting( 'onecommunity_transient_footer_expiration', array(
    'default' => 10080,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_footer_expirationn', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_footer_expiration',
    'label'    => 'Footer cache expiration time in minutes:',
) );



$wp_customize->add_setting( 'onecommunity_transient_sidenav_enable', array(
        'default' => 0,
        'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_sidenav_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_sidenav_enable',
    'label'    => 'Enable transients for the right sidenav',
) );


$wp_customize->add_setting( 'onecommunity_transient_sidenav_expiration', array(
    'default' => 1440,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_sidenav_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_sidenav_expiration',
    'label'    => 'Right sidenav cache expiration time in minutes:',
) );




$wp_customize->add_setting( 'onecommunity_transient_sidebar_pages_enable', array(
        'default' => 0,
        'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_sidebar_pages_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_sidebar_pages_enable',
    'label'    => 'Enable transients for the pages sidebar',
) );


$wp_customize->add_setting( 'onecommunity_transient_sidebar_pages_expiration', array(
    'default' => 20,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_sidebar_pages_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_sidebar_pages_expiration',
    'label'    => 'Pages sidebar cache expiration time in minutes:',
) );



$wp_customize->add_setting( 'onecommunity_transient_sidebar_blog_enable', array(
        'default' => 0,
        'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_sidebar_blog_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_sidebar_blog_enable',
    'label'    => 'Enable transients for the blog sidebar',
) );


$wp_customize->add_setting( 'onecommunity_transient_sidebar_blog_expiration', array(
    'default' => 20,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_sidebar_blog_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_sidebar_blog_expiration',
    'label'    => 'Blog sidebar cache expiration time in minutes:',
) );




$wp_customize->add_setting( 'onecommunity_transient_sidebar_shop_enable', array(
        'default' => 0,
        'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_sidebar_shop_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_sidebar_shop_enable',
    'label'    => 'Enable transients for the shop sidebar',
) );


$wp_customize->add_setting( 'onecommunity_transient_sidebar_shop_expiration', array(
    'default' => 4320,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_sidebar_shop_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_sidebar_shop_expiration',
    'label'    => 'Shop sidebar cache expiration time in minutes:',
) );




$wp_customize->add_setting( 'onecommunity_sidebar_left_enable', array(
        'default' => 0,
        'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_sidebar_left_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_sidebar_left_enable',
    'label'    => 'Enable transients for the left sidebar',
) );

$wp_customize->add_setting( 'onecommunity_transient_sidebar_left_expiration', array(
    'default' => 20,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_sidebar_left_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_sidebar_left_expiration',
    'label'    => 'Left sidebar cache expiration time in minutes:',
) );





$wp_customize->add_setting( 'onecommunity_transient_sidebar_learnpress_enable', array(
        'default' => 0,
        'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_sidebar_learnpress_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_sidebar_learnpress_enable',
    'label'    => 'Enable transients for the LearnPress sidebar',
) );


$wp_customize->add_setting( 'onecommunity_transient_sidebar_learnpress_expiration', array(
    'default' => 1440,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_sidebar_learnpress_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_sidebar_learnpress_expiration',
    'label'    => 'LearnPress sidebar cache expiration time in minutes:',
) );



$wp_customize->add_setting( 'onecommunity_transient_sidebar_left_enable', array(
        'default' => 0,
        'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_sidebar_left_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_sidebar_left_enable',
    'label'    => 'Enable transients for the single groups left sidebar',
) );

$wp_customize->add_setting( 'onecommunity_transient_sidebar_left_groups_expiration', array(
    'default' => 20,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_sidebar_left_groups_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_sidebar_left_groups_expiration',
    'label'    => 'BuddyPres groups left sidebar cache expiration time in minutes:',
) );



$wp_customize->add_setting( 'onecommunity_sidebar_groups_cached_enable', array(
        'default' => 0,
        'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_sidebar_groups_cached_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_sidebar_groups_cached_enable',
    'label'    => 'Enable transients for the single groups sidebar',
) );

$wp_customize->add_setting( 'onecommunity_sidebar_groups_cached_expiration', array(
    'default' => 20,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_sidebar_groups_cached_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_sidebar_groups_cached_expiration',
    'label'    => 'BuddyPress groups sidebar cache expiration time in minutes:',
) );



$wp_customize->add_setting( 'onecommunity_sidebar_left_user_enable', array(
        'default' => 0,
        'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_sidebar_left_user_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_sidebar_left_user_enable',
    'label'    => 'Enable transients for the single user profile left sidebar',
) );

$wp_customize->add_setting( 'onecommunity_transient_sidebar_left_user_expiration', array(
    'default' => 20,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_sidebar_left_user_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_sidebar_left_user_expiration',
    'label'    => 'BuddyPress user profile left sidebar cache expiration time in minutes:',
) );



$wp_customize->add_setting( 'onecommunity_sidebar_profiles_cached_enable', array(
        'default' => 0,
        'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_sidebar_profiles_cached_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_sidebar_profiles_cached_enable',
    'label'    => 'Enable transients for the single user profile sidebar',
) );

$wp_customize->add_setting( 'onecommunity_sidebar_profiles_cached_expiration', array(
    'default' => 20,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_sidebar_profiles_cached_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_sidebar_profiles_cached_expiration',
    'label'    => 'BuddyPress user profiles sidebar cache expiration time in minutes:',
) );



$wp_customize->add_setting( 'onecommunity_transient_leaderboard_small_enable', array(
        'default' => 0,
        'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_leaderboard_small_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_leaderboard_small_enable',
    'label'    => 'Enable transients for the small leaderboard',
) );


$wp_customize->add_setting( 'onecommunity_transient_leaderboard_small_expiration', array(
    'default' => 1440,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_leaderboard_small_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_leaderboard_small_expiration',
    'label'    => 'Leaderboard (small) cache expiration time in minutes:',
) );



$wp_customize->add_setting( 'onecommunity_transient_leaderboard_big_enable', array(
        'default' => 0,
        'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_leaderboard_big_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_leaderboard_big_enable',
    'label'    => 'Enable transients for the big leaderboard',
) );


$wp_customize->add_setting( 'onecommunity_transient_leaderboard_big_expiration', array(
    'default' => 1440,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_leaderboard_big_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_leaderboard_big_expiration',
    'label'    => 'Leaderboard (big) cache expiration time in minutes:',
) );




$wp_customize->add_setting( 'onecommunity_transient_most_popular_enable', array(
        'default' => 0,
        'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_most_popular_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_most_popular_enable',
    'label'    => 'Enable transients for the most popular posts shortcode',
) );


$wp_customize->add_setting( 'onecommunity_transient_most_popular_expiration', array(
    'default' => 1440,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_most_popular_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_most_popular_expiration',
    'label'    => 'Most popular posts shortcode cache expiration time in minutes:',
) );



$wp_customize->add_setting( 'onecommunity_transient_cat_posts_enable', array(
        'default' => 0,
        'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_cat_posts_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_cat_posts_enable',
    'label'    => 'Enable transients for the category posts shortcode on frontpage',
) );


$wp_customize->add_setting( 'onecommunity_transient_cat_posts_expiration', array(
    'default' => 1440,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_cat_posts_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_cat_posts_expiration',
    'label'    => 'Category posts shortcode cache expiration time in minutes:',
) );



$wp_customize->add_setting( 'onecommunity_transient_comments_enable', array(
        'default' => 0,
        'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_comments_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_comments_enable',
    'label'    => 'Enable transients for the comments shortcode',
) );


$wp_customize->add_setting( 'onecommunity_transient_comments_expiration', array(
    'default' => 20,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_comments_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_comments_expiration',
    'label'    => 'Comments shortcode cache expiration time in minutes:',
) );



$wp_customize->add_setting( 'onecommunity_transient_forum_topics_enable', array(
        'default' => 0,
        'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_forum_topics_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_forum_topics_enable',
    'label'    => 'Enable transients for the recent forum topics shortcode',
) );


$wp_customize->add_setting( 'onecommunity_transient_forum_topics_expiration', array(
    'default' => 20,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_forum_topics_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_forum_topics_expiration',
    'label'    => 'Recent forum topics shortcode cache expiration time in minutes:',
) );



$wp_customize->add_setting( 'onecommunity_transient_activity_enable', array(
        'default' => 0,
        'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_activity_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_activity_enable',
    'label'    => 'Enable transients for the activity shortcode',
) );


$wp_customize->add_setting( 'onecommunity_transient_activity_expiration', array(
    'default' => 20,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_activity_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_activity_expiration',
    'label'    => 'Activities shortcode cache expiration time in minutes:',
) );



$wp_customize->add_setting( 'onecommunity_transient_swiper_one_enable', array(
    'default' => 0,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_swiper_one_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_swiper_one_enable',
    'label'    => 'Enable transients for Swiper One',
) );


$wp_customize->add_setting( 'onecommunity_transient_swiper_one_expiration', array(
    'default' => 1440,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_swiper_one_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_swiper_one_expiration',
    'label'    => 'Swiper One cache expiration time in minutes:',
) );


$wp_customize->add_setting( 'onecommunity_transient_swiper_two_enable', array(
    'default' => 0,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_swiper_two_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_swiper_two_enable',
    'label'    => 'Enable transients for Swiper Two',
) );


$wp_customize->add_setting( 'onecommunity_transient_swiper_two_expiration', array(
    'default' => 1440,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_swiper_two_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_swiper_two_expiration',
    'label'    => 'Swiper Two cache expiration time in minutes:',
) );



$wp_customize->add_setting( 'onecommunity_transient_swiper_three_enable', array(
    'default' => 0,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_swiper_three_enable', array(
    'type'     => 'checkbox',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_swiper_three_enable',
    'label'    => 'Enable transients for Swiper Three',
) );


$wp_customize->add_setting( 'onecommunity_transient_swiper_three_expiration', array(
    'default' => 1440,
    'sanitize_callback' => 'sanitize_text_field'
) );

$wp_customize->add_control( 'onecommunity_transient_swiper_three_expiration', array(
    'type'     => 'number',
    'section'  => 'onecommunity_transients_section',
    'settings'  => 'onecommunity_transient_swiper_three_expiration',
    'label'    => 'Swiper Three cache expiration time in minutes:',
) );

/////////////////////////////////////////////////////////////////////////////////////////////


}
