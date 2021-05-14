<?php
$quiz_completed         = learndash_is_quiz_complete( get_current_user_id(), $quiz['post']->ID );
$completed_class        = empty( $quiz_completed ) ? 'topic-notcompleted' : 'topic-completed';
$current_topic_class    = ($quiz['post']->ID == $post->ID) ? 'learndash-current-menu-item' : '';

if ( !empty( $current_topic_class ) ) $lesson_topic_child_item_active = true; ?>

<li class="quiz-item <?php echo $current_topic_class ?>">
    <a class='lds-content-status-<?php echo esc_attr( $quiz['status'] ) . ' ' . esc_attr( $current_topic_class ); ?>' href='<?php echo esc_attr( $quiz['permalink'] ); ?>'>
        <span class="lds-ec-icon fa fa-question"></span> <?php echo esc_html( $quiz['post']->post_title ); ?>
        <?php
        if( get_post_meta( $quiz['post']->ID, '_lds_short_description', true ) ) echo '<span class="lds-ec-description">' . get_post_meta( $quiz['post']->ID, '_lds_short_description', true ) . '</span>';

        if( get_post_meta( $quiz['post']->ID, '_lds_duration', true ) ) echo '<span class="lds-ec-duration standalone"><i class="fa fa-clock-o"></i> ' . get_post_meta( $quiz['post']->ID, '_lds_duration', true ) . '</span>'; ?>
    </a>
</li>
