<style type="text/css">
    <?php
    if( get_option('lds_content_list_hide_item_counts') && get_option('lds_content_list_hide_item_counts') == 1 ): ?>
        .learndash-wrapper .ld-item-list-item .ld-item-component,
        .learndash-wrapper .ld-item-list-item .ld-sep {
            display: none;
        }
    <?php
    endif;
    if( get_option('lds_content_list_hide_lesson_expand') && get_option('lds_content_list_hide_lesson_expand') == 1 ): ?>
        .learndash-wrapper .ld-lesson-item-preview .ld-expand-button,
        .learndash-wrapper .ld-item-list-item .ld-expand-button {
            display: none;
        }
    <?php endif;
    if( get_option('lds_content_list_hide_expand_all') && get_option('lds_content_list_hide_expand_all') == 1 ): ?>
        .learndash-wrapper .ld-course-navigation-actions .ld-expand-button,
        .learndash-wrapper .ld-item-list-actions .ld-expand-button {
            display: none;
        }
    <?php endif;
    if( get_option('lds_hide_breadcrumbs') && get_option('lds_hide_breadcrumbs') == 1 ): ?>
        .learndash-wrapper .ld-breadcrumbs,
        .learndash-wrapper .ld-course-status.ld-course-status-enrolled {
            display: none;
        }
    <?php
    endif;
    if( get_option('lds_hide_last_activity') && get_option('lds_hide_last_activity') == 1 ): ?>
        .learndash-wrapper .ld-progress .ld-progress-steps {
            display: none;
        }
    <?php endif;
    $progress_steps = get_option('lds_hide_progress_steps');
    if( $progress_steps && $progress_steps == 1 ): ?>
    .learndash-wrapper .ld-progress .ld-progress-stats .ld-progress-steps {
        display: none;
    }
    <?php endif;
    $lesson_progress_stats = get_option('lds_hide_lesson_progress_stats');
    if( $lesson_progress_stats && $lesson_progress_stats == 1 ): ?>
        .learndash-wrapper .ld-topic-list.ld-table-list .ld-table-list-lesson-details .ld-lesson-list-progress,
        .learndash-wrapper .ld-topic-list.ld-table-list .ld-table-list-lesson-details .ld-lesson-list-steps {
            display: none;
        }
    <?php
    endif;

    $profile_points = get_option('lds_hide_profile_points');
    if( $profile_points && $profile_points == 1 ): ?>
        .learndash-wrapper .ld-profile-stat-points,
        .learndash-wrapper .ld-table-list-column-points {
            display: none;
        }
    <?php
    endif; ?>
</style>
