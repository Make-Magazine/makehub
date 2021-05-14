<div id="lds-shortcode" class="lds-widget lds-course-list-style-expanded">
    <div class="lds-expanded-course-item">
        <div class="lds-expanded-course-lesson-list lds-expanded-section">

            <?php
            global $post;

            if ( $post->post_type == 'sfwd-topic' || $post->post_type == 'sfwd-quiz')
        			$lesson_id = learndash_get_setting( $post, 'lesson');
    		else
    			$lesson_id = $post->ID;

    		if ( ! empty( $lessons) ):
                    lds_shortcodes_enqueue_scripts(); ?>
                    <ul class="lds-expanded-navigation-widget">
                    <?php
        			foreach( $lessons as $course_lesson ):

        				$current_topic_ids = '';
        				$topics =  learndash_topic_dots( $course_lesson['post']->ID, false, 'array' );

        				if ( ( isset( $widget_instance['show_lesson_quizzes'] ) ) && ( $widget_instance['show_lesson_quizzes'] == true ) ) {
        					$lesson_quiz_list = learndash_get_lesson_quiz_list( $course_lesson['post']->ID, get_current_user_id() );
        				} else {
        					$lesson_quiz_list = array();
        				}

        				$is_current_lesson  = ( $lesson_id == $course_lesson['post']->ID );
                        $lesson_list_class  = '';
        				// $lesson_list_class          = ( $is_current_lesson ) ? 'active' : 'inactive';
        				// $list_arrow_class           = (( $is_current_lesson ) && (( !empty( $topics ) ) || ( !empty($lesson_quiz_list ) ))) ? 'expand' : 'collapse';

        				if ($lesson_id == $course_lesson['post']->ID)
        					$lesson_list_class .= ' learndash-current-menu-ancestor ';

        				$lesson_topic_child_item_active = false;
        				?>
                        <li class="<?php echo esc_attr($lesson_list_class); ?>">
                            <a class="lds-content-status-<?php echo esc_attr( $course_lesson['status'] ); ?>" href="<?php echo esc_attr( get_the_permalink( $course_lesson['post']->ID ) ); ?>">
                                <?php
                                /**
                                 * Custom icon or fallback
                                 * @var [type]
                                 */
                                 $icon = ldvc_get_content_icon( $course_lesson['post']->ID );
                                 echo '<span class="lds-ec-icon fa ' . esc_attr($icon) . '"></span>' . esc_html($course_lesson['post']->post_title);

                                /**
                                 * Custom information like the duration
                                 */
                                 if( get_post_meta( $course_lesson['post']->ID, '_lds_short_description', true ) ) echo '<span class="lds-ec-description">' . get_post_meta( $course_lesson['post']->ID, '_lds_short_description', true ) . '</span>';

                                 if( get_post_meta( $course_lesson['post']->ID, '_lds_duration', true ) ) echo '<span class="lds-ec-duration standalone"><i class="fa fa-clock-o"></i> ' . get_post_meta( $course_lesson['post']->ID, '_lds_duration', true ) . '</span>';

                                /**
                                 * If this is a drip lesson, output the following
                                 */
                                if ( ! empty( $course_lesson['lesson_access_from'] ) ) : ?>
                                    <span class="lds-ec-notavailable">
                                        <i class="fa fa-calendar"></i>
                                        <?php echo sprintf( __( 'Available on: %s ', 'lds_skins' ), learndash_adjust_date_time_display( $course_lesson['lesson_access_from'] ) ); ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                            <?php if ( ! empty($topics) ): ?>
                                <ul class="lds-expanded-topic-list">
                                    <?php
                                    foreach ( $topics as $key => $topic ):
                                        $completed_class = empty( $topic->completed ) ? 'lds-content-status-notcompleted':'lds-content-status-completed'; ?>
                                        <li>
                                            <a class='<?php echo esc_attr( $completed_class ); ?>' href='<?php echo esc_attr( get_permalink( $topic->ID ) ); ?>' title='<?php echo esc_attr( $topic->post_title ); ?>'>

                                                <?php
                                                $icon = ( !empty( get_post_meta( $topic->ID, '_lds_content_type', true ) ) ? get_post_meta( $topic->ID, '_lds_content_type', true ) : 'fa-star-o' );
                                                echo '<span class="lds-ec-icon fa ' . esc_attr($icon) . '"></span>' . esc_html($topic->post_title);

                                                $lds_post_id = $topic->ID;

                                                if( get_post_meta( $topic->ID, '_lds_short_description', true ) ) echo '<span class="lds-ec-description">' . get_post_meta( $topic->ID, '_lds_short_description', true ) . '</span>';
                                                if( get_post_meta( $topic->ID, '_lds_duration', true ) ) echo '<span class="lds-ec-duration standalone"><i class="fa fa-clock-o"></i> ' . get_post_meta( $topic->ID, '_lds_duration', true ) . '</span>'; ?>

                                            </a>
                                            <?php
                                            /**
                                             * Show quizes for topics if set /  enabled
                                             */
											if ( isset( $widget_instance['show_topic_quizzes'] ) && $widget_instance['show_topic_quizzes'] == true ):

													$topic_quiz_list = learndash_get_lesson_quiz_list( $topic->ID, get_current_user_id() );

													if ( !empty($topic_quiz_list) ) {
                                                        foreach ( $topic_quiz_list as $quiz ) include( ldvc_get_template_part('widgets/partials/course-navigation-expanded-quiz.php') );
                                                    }
											endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif;
                            if ( isset( $widget_instance['show_lesson_quizzes'] ) && $widget_instance['show_lesson_quizzes'] == true ):

                                $lesson_quiz_list  = learndash_get_lesson_quiz_list( $course_lesson['post']->ID, get_current_user_id() );

                                if( !empty($lesson_quiz_list) ): ?>
                                    <ul class="lds-expanded-nested-quizes lds-expanded-topic-list">
                                        <?php foreach( $lesson_quiz_list as $quiz ) include( ldvc_get_template_part('widgets/partials/course-navigation-expanded-quiz.php') ); ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                    <?php endif;
                    endforeach; ?>
                </ul>
            <?php endif;
            /**
             * Are course quizes show?
             */
			if ( ( isset( $widget_instance['show_course_quizzes'] ) ) && ( $widget_instance['show_course_quizzes'] == true ) ):

                $course_quiz_list = learndash_get_course_quiz_list( $course_id, get_current_user_id() );
	            if ( !empty($course_quiz_list) ): ?>
                <p><strong><?php echo esc_html(LearnDash_Custom_Label::get_label( 'quizzes' )); ?></strong></p>
                <ul id="learndash-course-quiz-list-<?php echo $course_id ?>" class="learndash-course-quiz-list learndash_navigation_lesson_topics_list lds_expanded_course_quiz_list">
                    <?php foreach ( $course_quiz_list as $quiz ) include( ldvc_get_template_part('widgets/partials/course-navigation-expanded-quiz.php') ); ?>
				</ul>
                <?php endif;
            endif;

            if ( $post->ID != $course->ID ): ?>
            <div class="widget_course_return">
                <p><?php esc_html_e( 'Return to', 'lds_skins' ); ?>
                <a href='<?php echo esc_attr( get_permalink( $course_id ) ); ?>'><?php echo $course->post_title; ?></a></p>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
