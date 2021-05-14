<?php
$hide_elements = array(
    'welcome'   =>  array(
        'option'    =>  get_option('lds_focus_hide_welcome'),
        'selector'  =>  '.ld-user-welcome-text',
    ),
    'avatar'    =>  array(
        'option'    => get_option('lds_focus_hide_avatar'),
        'selector'  => '.ld-profile-avatar'
    ),
    'progress'  =>  array(
        'option'     =>  get_option('lds_focus_hide_top_progress_bar'),
        'selector'  =>  '.ld-focus-header .ld-progress-bar'
    ),
    'course_home'   =>  array(
        'option' =>  get_option('lds_focus_hide_course_home'),
        'selector'  =>  '.ld-course-navigation-heading .ld-icon-content'
    ),
    'content_footer_nav'  =>    array(
        'option' =>  get_option('lds_focus_hide_content_footer'),
        'selector' => '.ld-focus-content .ld-content-actions'
    )
);

foreach( $hide_elements as $element ) {

    if( isset($element['option']) && $element['option'] == 1 ) { ?>

        <?php echo $element['selector']; ?> {
            display: none;
        }

    <?php
    }
}

$colors = array(
     'sidebar_bg'   =>   array(
          'option'  =>   'lds_focus_sidebar_bg',
          'selector' =>  '.learndash-wrapper .ld-focus .ld-focus-sidebar',
          'style'   =>   'background-color',
     ),
     'course_title_bg'    =>   array(
          'option'  =>   'lds_focus_sidebar_course_title_bg',
          'selector' =>  '.learndash-wrapper .ld-focus .ld-focus-sidebar .ld-course-navigation-heading, .learndash-wrapper .ld-focus .ld-focus-sidebar .ld-focus-sidebar-trigger',
          'style'   =>   'background-color',
     ),
     'course_title'    =>   array(
          'option'  =>   'lds_focus_sidebar_course_title',
          'selector' =>  '.learndash-wrapper .ld-focus .ld-focus-sidebar .ld-course-navigation-heading h3 a, .learndash-wrapper .ld-focus .ld-focus-sidebar .ld-focus-sidebar-trigger',
          'style'   =>   'color',
     ),
     'navigation_title'    =>   array(
          'option'  =>   'lds_focus_sidebar_navigation_title',
          'selector' =>  '.learndash-wrapper .ld-course-navigation .ld-lesson-item-preview .ld-lesson-title, .learndash-wrapper .ld-focus .ld-focus-sidebar .ld-lesson-item-preview .ld-status-icon',
          'style'   =>   'color',
     ),
     'navigation_background'    =>   array(
          'option'  =>   'lds_focus_sidebar_navigation_background',
          'selector' =>  '.learndash-wrapper .ld-focus .ld-focus-sidebar .ld-course-navigation .ld-lesson-item',
          'style'   =>   'background-color',
     ),
     'navigation_accent'    =>   array(
          'option'  =>   'lds_focus_sidebar_navigation_accent',
          'selector' =>  '.learndash-wrapper .ld-focus .ld-focus-sidebar .ld-primary-color, .learndash-wrapper .ld-focus .ld-focus-sidebar a.ld-table-list-item-preview .ld-status-icon.ld-quiz-complete',
          'style'   =>   'color',
     ),
     'navigation_accent_bg'    =>   array(
          'option'  =>   'lds_focus_sidebar_navigation_accent',
          'selector' =>  '.learndash-wrapper .ld-focus .ld-focus-sidebar .ld-primary-background, .learndash-wrapper .ld-focus .ld-focus-sidebar .ld-status-complete',
          'style'   =>   'background-color',
     ),
     'navigation_incomplete_border-color'    =>   array(
          'option'  =>   'lds_focus_sidebar_navigation_title',
          'selector' =>  '.learndash-wrapper .ld-focus .ld-focus-sidebar .ld-course-navigation .ld-lesson-item.ld-is-current-lesson .ld-lesson-item-preview-heading .ld-status-incomplete, .learndash-wrapper .ld-course-navigation .ld-lesson-item.ld-is-current-lesson .ld-lesson-title .ld-status-incomplete',
          'style'   =>   'border-color',
     ),
     'navigation_border'    =>   array(
          'option'  =>   'lds_focus_sidebar_border_color',
          'selector' =>  '.learndash-wrapper .ld-focus .ld-focus-sidebar .ld-course-navigation .ld-lesson-item',
          'style'   =>   'border-color',
     ),
     'section_heading_bg'    =>   array(
          'option'  =>   'lds_focus_sidebar_section_heading_bg',
          'selector' =>  '.learndash-wrapper .ld-focus .ld-focus-sidebar .ld-course-navigation .ld-lesson-item-section-heading',
          'style'   =>   'background-color',
     ),
     'section_heading'    =>   array(
          'option'  =>   'lds_focus_sidebar_section_heading',
          'selector' =>  '.learndash-wrapper .ld-focus .ld-focus-sidebar .ld-course-navigation .ld-lesson-item-section-heading .ld-lesson-section-heading',
          'style'   =>   'color',
     ),
     'navigation_topic_bg'    =>   array(
          'option'  =>   'lds_focus_sidebar_navigation_topic_bg',
          'selector' =>  '.learndash-wrapper .ld-focus .ld-focus-sidebar .ld-course-navigation .ld-lesson-item .ld-table-list, .learndash-wrapper .ld-focus .ld-focus-sidebar .ld-course-navigation .ld-lesson-item .ld-table-list::before',
          'style'   =>   'background-color',
     ),
     'navigation_topic'    =>   array(
          'option'  =>   'lds_focus_sidebar_navigation_topic',
          'selector' =>  '.learndash-wrapper .ld-focus .ld-focus-sidebar a.ld-table-list-item-preview .ld-topic-title, .learndash-wrapper .ld-focus .ld-focus-sidebar a.ld-table-list-item-preview .ld-status-icon',
          'style'   =>   'color',
     ),
);

$primary_color = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Theme_LD30', 'color_primary' );

foreach( $colors as $color ) {

     $value = get_option($color['option']);

     if( $value && !empty($value) ) {
          echo $color['selector'] . '{ ' . $color['style'] . ':' . $value . ' !important; }';
     }

} ?>

.ld-focus.ld-focus-sidebar-collapsed .ld-focus-sidebar-trigger .ld-icon-arrow-right {
     background-color: <?php echo $primary_color; ?> !important;
}
