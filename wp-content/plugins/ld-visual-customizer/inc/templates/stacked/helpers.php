<?php

add_action( 'learndash-lesson-row-title-before', 'lds_stacked_lesson_thumbnail', 10, 2 );
function lds_stacked_lesson_thumbnail( $lesson_id, $course_id ) {

    include( lds_get_template_part( 'stacked/lesson/partials/thumbnail.php' ) );

}

add_action( 'learndash-quiz-row-status-before', 'lds_stacked_topic_thumbnail', 10, 2 );
add_action( 'learndash-topic-row-status-before', 'lds_stacked_topic_thumbnail', 10, 2 );
function lds_stacked_topic_thumbnail( $topic_id, $course_id ) {

    include( lds_get_template_part( 'stacked/topic/partials/thumbnail.php' ) );

}


add_action( 'wp_enqueue_scripts', 'lds_stacked_custom_styles', 900 );
function lds_stacked_custom_styles() {

    ob_start();

    $item_drop_shadow  = get_option('lds_content_list_drop_shadow');
    $item_border_color = get_option('lds_content_item_border');
    $item_border_size  = strval(get_option('lds_content_list_border_size'));
    $border_radius     = strval(get_option('lds_content_list_border_radius'));
    ?>

    .learndash-wrapper.lds-template-stacked .ld-lesson-topic-list .ld-table-list-item {
        overflow: hidden;
        <?php
        if( $border_radius == '0' || !empty($border_radius) ):
            echo 'border-radius: ' . $border_radius . 'px !important;';
        endif;
        if( $item_border_color && $item_border_color != '' ):
            echo 'border-color:' . $item_border_color . ' !important;';
        endif;
        if( $item_border_size == 0 || !empty($item_border_size) ):
            echo 'border-width:' . $item_border_size . 'px !important;';
        endif;
        switch($item_drop_shadow):
            case('light'): ?>
                box-shadow: 0 0 15px rgba(0,0,0,0.05);
                <?php
                break;
            case('light-plus'): ?>
                box-shadow: 0 0 15px rgba(0,0,0,0.1);
                <?php
                break;
            case('medium'): ?>
                box-shadow: 3px 5px 35px rgba(0,0,0,0.05);
                <?php
                break;
            case('medium-plus'): ?>
                box-shadow: 3px 5px 35px rgba(0,0,0,0.1);
                <?php
                break;
            case('heavy'): ?>
                box-shadow: 3px 5px 35px rgba(0,0,0,0.1);
                <?php
                break;
            case('heavy-plus'): ?>
                box-shadow: 3px 5px 40px rgba(0,0,0,0.15);
                <?php
                break;
            endswitch; ?>
    }

    <?php
    if( $border_radius == '0' || !empty($border_radius) ): ?>
        .learndash-wrapper .lds-grid-banners-thumbnail {
            border-radius: <?php echo $border_radius . 'px ' . $border_radius . 'px '; ?> 0 0;
        }
        .learndash-wrapper .ld-item-list-items .ld-item-list-item .ld-table-list-footer {
            border-radius: 0 0 <?php echo $border_radius . 'px ' . $border_radius . 'px '; ?>;
        }
    <?php
    endif;

    $primary_color = get_option( 'lds_primary_color' );
    if( $primary_color && $primary_color != '' ): ?>
        .learndash-wrapper .lds-grid-banners-thumbnail {
            background-color: <?php echo $primary_color; ?> !important;
        }
    <?php
    endif;

    $custom_css = ob_get_clean();

    wp_add_inline_style( 'learndash-front', $custom_css );

}
