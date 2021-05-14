<?php
$q = 0; ?>

<div class="lds-expanded-course-lesson-list lds-expanded-section">
    <p><strong><?php echo esc_html( LearnDash_Custom_Label::get_label('quizzes') ); ?></strong></p>
    <ul>
        <?php foreach( $quizzes as $quiz ) : ?>
            <li>
                <a class='lds-content-status-<?php echo esc_attr( $quiz['status'] ); ?>' href='<?php echo esc_attr( $quiz['permalink'] ); ?>'>
                    <span class="lds-ec-icon fa fa-question"></span> <?php echo esc_html( $quiz['post']->post_title ); ?>
                    <?php
                    if( get_post_meta( $quiz['post']->ID, '_lds_short_description', true ) ) echo '<span class="lds-ec-description">' . get_post_meta( $quiz['post']->ID, '_lds_short_description', true ) . '</span>';

                    if( get_post_meta( $quiz['post']->ID, '_lds_duration', true ) ) echo '<span class="lds-ec-duration standalone"><i class="fa fa-clock-o"></i> ' . get_post_meta( $quiz['post']->ID, '_lds_duration', true ) . '</span>'; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
