<?php
/**
 *  Custom Icons
 *
 */ ?>

<script>

    jQuery(document).ready(function($) {

        function lds_init_custom_icons() {

            console.log('running custom icons!');

            <?php

            $custom_icons = array(
                'complete_icon' => array(
                    'option'    =>  'lds_complete_icon',
                    'selector'  =>  '.ld-icon.ld-icon-checkmark'
                ),
                'quiz_icon'     => array(
                    'option'    =>  'lds_quiz_icon',
                    'selector'  =>  '.ld-icon.ld-icon-quiz'
                ),
                'calendar_icon' =>  array(
                    'option'    =>  'lds_calendar_icon',
                    'selector'  =>  '.ld-icon.ld-icon-calendar'
                ),
                'sample_icon' =>    array(
                    'option'    =>  'lds_sample_icon',
                    'selector'  =>  '.ld-icon.ld-icon-unlocked',
                ),
                'arrow_right'  =>  array(
                    'option'    =>  'lds_arrow_right_icon',
                    'selector'  =>  '.ld-icon.ld-icon-arrow-right',
                ),
                'arrow_down'  =>  array(
                    'option'    =>  'lds_arrow_down_icon',
                    'selector'  =>  '.ld-icon.ld-icon-arrow-down',
                ),
                'arrow_left'  =>  array(
                    'option'    =>  'lds_arrow_left_icon',
                    'selector'  =>  '.ld-icon.ld-icon-arrow-left',
                ),
                'content_icon' => array(
                    'option'    =>  'lds_content_icon',
                    'selector'  =>  '.ld-icon.ld-icon-content'
                ),
                'materials_icon'    =>  array(
                    'option'    =>  'lds_materials_icon',
                    'selector'  =>  '.ld-icon.ld-icon-materials',
                ),
                'certificate_icon'  =>  array(
                    'option'    =>  'lds_certificate_icon',
                    'selector'  =>  '.ld-icon.ld-icon-certificate'
                ),
                'assignment'    =>  array(
                    'option'    =>  'lds_assignment_icon',
                    'selector'  =>  '.ld-icon.ld-icon-assignment'
                ),
                'download'    =>  array(
                    'option'    =>  'lds_download_icon',
                    'selector'  =>  '.ld-icon.ld-icon-download'
                ),
                'comments'    =>  array(
                    'option'    =>  'lds_comments_icon',
                    'selector'  =>  '.ld-icon.ld-icon-comments'
                ),
                'alert'    =>  array(
                    'option'    =>  'lds_alert_icon',
                    'selector'  =>  '.ld-icon.ld-icon-alert'
                ),
            );

            foreach( $custom_icons as $slug => $details ) {

                $icon = get_option( $details['option'] );

                if( $icon && $icon != '' ) { ?>
                    $('<?php echo $details['selector']; ?>').addClass('fa <?php echo $icon; ?> ');
                <?php
                }

            } ?>

        }

        lds_init_custom_icons();

        $('body').on( 'ld_has_paginated', function() {
            lds_init_custom_icons();
        });

    });

</script>

<?php
