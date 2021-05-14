<?php
$stats = lds_get_user_stats( $lessons, $lesson_topics, $quizzes );
if( ( $course_status == 'In Progress' || $course_status == 'completed' ) && get_option( 'lds_show_leaderboard', 'yes' ) == 'yes' ): ?>
<div id="learndash_enhanced_course_header">

    <hgroup>
        <p class="lds-enhanced-course-status"><?php echo esc_html($course_status); ?></p>
        <h2><?php echo LearnDash_Custom_Label::get_label( 'course' ) ?> <?php esc_html_e( 'Status', 'sfwd-lms' ); ?></h2>
    </hgroup>

    <ul class="lds_stats">
        <?php
        foreach ($stats as $label => $values):
            if( $values['total'] == 0 ) continue;
            ?>
            <li>
                <span><?php echo esc_html( $values['completed'] . '/' . $values['total'] ); ?></span>
                <strong><?php echo esc_html($values['title']); ?></strong>
            </li>
        <?php endforeach; ?>
    </ul>

</div>
<?php endif; ?>
