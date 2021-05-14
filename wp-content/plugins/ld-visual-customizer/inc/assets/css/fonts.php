<?php

/**
 *  Custom font Sizes
 *
 *  lds_heading_font_size
 *  lds_section_heading_font_size
 *  lds_item_font_size
 *  lds_item_attribute_font_size
 *  lds_widget_heading_font_size
 *  lds_widget_item_font_size
 */ ?>

<style type="text/css">
    <?php

    $inherit_font_size = get_option('lds_font_size_inherit');
    if( $inherit_font_size && $inherit_font_size == 1 ): ?>
        .learndash-wrapper {
            font-size: inherit;
            line-height: inherit;
        }
    <?php
    endif;

    $heading_font_size = get_option('lds_heading_font_size');

    if( $heading_font_size != '0' || !empty($heading_font_size) ): ?>

        .learndash-wrapper .ld-item-list.ld-course-list .ld-section-heading h2,
        .learndash-wrapper .ld-item-list.ld-lesson-list .ld-section-heading h2 {
            font-size: <?php echo $heading_font_size; ?>px;
        }

    <?php
    endif;

    $base_font_size = get_option('lds_base_font_size');

    if( lds_in_use($base_font_size) ): ?>
          .learndash-wrapper {
               font-size: <?php echo $base_font_size; ?>px !important;
          }
     <?php
     endif;

    $section_heading_font_size = get_option('lds_section_heading_font_size');

    if( $section_heading_font_size != '0' || !empty($section_heading_font_size) ): ?>

        .learndash-wrapper .ld-item-list.ld-course-list .ld-lesson-section-heading,
        .learndash-wrapper .ld-item-list.ld-lesson-list .ld-lesson-section-heading {
            font-size: <?php echo $section_heading_font_size; ?>px;
        }

    <?php
    endif;

    $item_font_size = get_option('lds_item_font_size');

    if( $item_font_size != '0' || !empty($item_font_size) ): ?>

        .learndash-wrapper .ld-topic-title,
        .learndash-wrapper .ld-topic-title,
        .learndash-wrapper .ld-lesson-title,
        .learndash-wrapper .ld-item-title {
            font-size: <?php echo $item_font_size; ?>px !important;
        }

    <?php
    endif;

    $item_attribute_font_size = get_option('lds_item_attribute_font_size');

    if( $item_attribute_font_size != '0' || !empty($item_attribute_font_size) ): ?>

        .learndash-wrapper .ld-item-list .ld-item-list-item .ld-item-details .ld-expand-button,
        .learndash-wrapper .ld-item-list .ld-item-list-item .ld-item-title .ld-item-components,
        .learndash-wrapper .ld-item-list .ld-item-list-item .ld-item-title .ld-item-components .ld-status {
            font-size: <?php echo $item_attribute_font_size; ?>px;
        }

    <?php
    endif;

    $widget_section_heading_font_size = get_option('lds_widget_section_heading_font_size');

    if( $widget_section_heading_font_size != '0' || !empty($widget_section_heading_font_size) ): ?>

        .learndash-wrapper .ld-course-navigation .ld-lesson-item-section-heading .ld-lesson-section-heading {
            font-size: <?php echo $widget_section_heading_font_size; ?>px !important;
            line-height: 1.4em !important;
        }

    <?php
    endif;

    $widget_item_font_size = get_option('lds_widget_item_font_size');

    if( $widget_item_font_size != '0' || !empty($widget_item_font_size) ): ?>

        .learndash-wrapper .ld-course-navigation .ld-lesson-item-preview .ld-lesson-title {
            font-size: <?php echo $widget_item_font_size; ?>px;
            line-height: 1.4em;
        }

    <?php
    endif;

    $widget_sub_item_font_size  = get_option('lds_widget_sub_item_font_size');

    if( $widget_sub_item_font_size != '0' || !empty($widget_sub_item_font_size) ): ?>

        .learndash-wrapper .ld-course-navigation .ld-table-list.ld-topic-list .ld-table-list-item {
            font-size: <?php echo $widget_sub_item_font_size; ?>px;
            line-height: 1.4em;
        }

    <?php
    endif;

    $widget_item_attributes_font_size  = get_option('lds_widget_item_attributes_font_size');

    if( $widget_item_attributes_font_size != '0' || !empty($widget_item_attributes_font_size) ): ?>

        .learndash-wrapper .ld-course-navigation .ld-lesson-item-preview .ld-expand-button {
            font-size: <?php echo $widget_item_attributes_font_size; ?>px;
            line-height: 1.4em;
        }

    <?php
    endif;

    $button_font_size  = get_option('lds_button_font_size');

    if( $button_font_size != '0' || !empty($button_font_size) ): ?>

        .learndash-wrapper .ld-pagination .ld-pages,
        .learndash-wrapper #sfwd_lms_mark_complete,
        .learndash-wrapper .ld-button,
        .learndash-wrapper .ld-expand-button {
            font-size: <?php echo $button_font_size; ?>px;
        }

    <?php
    endif;

    $base_font_family = get_option('lds_font_family');

    if( $base_font_family && $base_font_family != '' ): ?>

            .learndash-wrapper {
                font-family: "<?php echo $base_font_family; ?>";
            }

    <?php
    endif;

    $heading_font_family = get_option('lds_heading_font_family');

    if( $heading_font_family && $heading_font_family != '' ): ?>

        .learndash-wrapper .ld-item-list.ld-course-list .ld-lesson-section-heading,
        .learndash-wrapper .ld-item-list.ld-lesson-list .ld-lesson-section-heading,
        .learndash-wrapper .ld-course-navigation .ld-lesson-item-section-heading .ld-lesson-section-heading,
        .learndash-wrapper .ld-item-list.ld-course-list .ld-section-heading h2,
        .learndash-wrapper .ld-item-list.ld-lesson-list .ld-section-heading h2 {
            font-family: "<?php echo $heading_font_family; ?>";
        }

    <?php
    endif;

    /**
      * Focus Mode
      *
      **/

      // Course Title
      $focus_course_title = get_option('lds_focus_nav_course_title');

     if( $focus_course_title != '0' && !empty($focus_course_title) ): ?>
          .learndash-wrapper .ld-focus .ld-focus-sidebar .ld-course-navigation-heading h3 {
               font-size: <?php echo $focus_course_title; ?>px !important;
          }
     <?php
     endif;

     // Focus Mode: Section Heading
     $focus_section_heading = get_option('lds_focus_nav_section_heading');

     if( $focus_section_heading != '0' && !empty($focus_section_heading) ): ?>
          .learndash-wrapper .ld-focus .ld-focus-sidebar .ld-course-navigation .ld-lesson-item-section-heading .ld-lesson-section-heading {
               font-size: <?php echo $focus_section_heading; ?>px !important;
          }
     <?php
     endif;

     // Focus Mode: Lesson / Quiz title

     $focus_lesson_title = get_option('lds_focus_nav_lesson_title');

     if( $focus_lesson_title != '0' && !empty($focus_lesson_title) ): ?>
          .learndash-wrapper .ld-focus .ld-focus-sidebar .ld-lesson-item .ld-lesson-title {
               font-size: <?php echo $focus_lesson_title; ?>px !important;
          }
     <?php
     endif;

     // Focus Mode: Lesson Meta

     $focus_lesson_meta = get_option('lds_focus_nav_meta');

     if( $focus_lesson_meta != '0' && !empty($focus_lesson_meta) ): ?>
          .learndash-wrapper .ld-focus .ld-focus-sidebar .ld-expand-button {
               font-size: <?php echo $focus_lesson_meta; ?>px !important;
          }
     <?php
     endif;

     // Focus Mode: Sub-item Text

     $focus_mode_topic = get_option('lds_focus_nav_sub_title');

     if( $focus_mode_topic != '0' && !empty($focus_mode_topic) ): ?>
          .learndash-wrapper .ld-focus .ld-focus-sidebar a.ld-table-list-item-preview .ld-topic-title {
               font-size: <?php echo $focus_mode_topic; ?>px !important;
          }
     <?php
     endif;

     $focus_mode_page_title = get_option('lds_focus_page_title');

     if( $focus_mode_page_title != '0' && !empty($focus_mode_page_title) ): ?>
          .learndash-wrapper .ld-focus .ld-focus-content h1 {
               font-size: <?php echo $focus_mode_page_title; ?>px !important;
          }
     <?php
     endif;

     $focus_mode_body = get_option('lds_focus_body_size');

     if( $focus_mode_body != '0' && !empty($focus_mode_body) ): ?>
          .learndash-wrapper .ld-focus .ld-focus-content ol,
          .learndash-wrapper .ld-focus .ld-focus-content li,
          .learndash-wrapper .ld-focus .ld-focus-content p,
          .learndash-wrapper .ld-focus .ld-focus-content td,
          .learndash-wrapper .ld-focus .ld-focus-content th {
               font-size: <?php echo $focus_mode_body; ?>px;
          }
     <?php
     endif;



    ?>

</style>
