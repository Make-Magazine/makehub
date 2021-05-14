<?php
/**
 *  Custom Icons
 *
 */ ?>

<style type="text/css">

    <?php
    $icon_resets = array(
        'complete_icon' => array(
            'option'    =>  'lds_complete_icon',
            'selector'  =>  '.ld-icon.ld-icon-checkmark:not(.fa)::before'
        ),
        'quiz_icon'     => array(
            'option'    =>  'lds_quiz_icon',
            'selector'  =>  '.ld-icon.ld-icon-quiz:not(.fa)::before'
        ),
        'calendar_icon' =>  array(
            'option'    =>  'lds_calendar_icon',
            'selector'  =>  '.ld-icon.ld-icon-calendar:not(.fa)::before'
        ),
        'sample_icon' =>    array(
            'option'    =>  'lds_sample_icon',
            'selector'  =>  '.ld-icon.ld-icon-unlocked:not(.fa)::before',
        ),
        'arrow_right'  =>  array(
            'option'    =>  'lds_arrow_right_icon',
            'selector'  =>  '.ld-icon.ld-icon-arrow-right:not(.fa)::before',
        ),
        'arrow_left'  =>  array(
            'option'    =>  'lds_arrow_left_icon',
            'selector'  =>  '.ld-icon.ld-icon-arrow-left:not(.fa)::before',
        ),
        'content_icon' => array(
            'option'    =>  'lds_content_icon',
            'selector'  =>  '.ld-icon.ld-icon-content:not(.fa)::before'
        ),
        'materials_icon'    =>  array(
            'option'    =>  'lds_materials_icon',
            'selector'  =>  '.ld-icon.ld-icon-materials:not(.fa)::before',
        ),
        'certificate_icon'  =>  array(
            'option'    =>  'lds_certificate_icon',
            'selector'  =>  '.ld-icon.ld-icon-certificate:not(.fa)::before'
        ),
        'arrow_down'  =>  array(
            'option'    =>  'lds_arrow_down_icon',
            'selector'  =>  '.ld-icon.ld-icon-arrow-down:not(.fa)::before',
        ),
        'assignment'    =>  array(
            'option'    =>  'lds_assignment_icon',
            'selector'  =>  '.ld-icon.ld-icon-assignment:not(.fa)::before'
        ),
        'download'    =>  array(
            'option'    =>  'lds_download_icon',
            'selector'  =>  '.ld-icon.ld-icon-download:not(.fa)::before'
        ),
        'comments'    =>  array(
            'option'    =>  'lds_comments_icon',
            'selector'  =>  '.ld-icon.ld-icon-comments:not(.fa)::before'
        ),
        'alert'    =>  array(
            'option'    =>  'lds_alert_icon',
            'selector'  =>  '.ld-icon.ld-icon-alert:not(.fa)::before'
        ),
    );

    foreach( $icon_resets as $slug => $details ) {

        $option = get_option( $details['option'] );

        if( $option && $option != '' ) {
            echo $details['selector'] . '{
                content: "";
            }';
        }

    }

    $icon_size = get_option('lds_icon_font_size');
    if( lds_in_use($icon_size) && $icon_size !== 'default' ): ?>
          .learndash-wrapper .ld-status-icon .ld-icon {
               font-size: <?php echo $icon_size; ?>px !important;
          }
    <?php endif; ?>


</style>

<?php
