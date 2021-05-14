<div id="learndash_course_content" class="learndash">

    <h4 id="learndash_course_content_title"><?php printf( _x( '%s Content', 'Course Content Label', 'lds_skins' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?></h4>

    <?php
    /**
     * Display lesson list
     */
    if ( ! empty( $lessons ) ) : ?>

        <?php if ( $has_topics ) : ?>
            <div class="expand_collapse">
                <a href="#" onClick='jQuery("#learndash_post_<?php echo $course_id; ?> .learndash_topic_dots").slideDown(); return false;'><?php _e( 'Expand All', 'lds_skins' ); ?></a> | <a href="#" onClick='jQuery("#learndash_post_<?php echo esc_attr( $course_id ); ?> .learndash_topic_dots").slideUp(); return false;'><?php _e( 'Collapse All', 'lds_skins' ); ?></a>
            </div>
        <?php endif; ?>

        <div id="learndash_lessons">

            <div id="lesson_heading">
                <span><?php echo LearnDash_Custom_Label::get_label( 'lessons' ) ?></span>
                <span class="right"><?php _e( 'Status', 'lds_skins' ); ?></span>
            </div>

            <div id="lessons_list" class="lds-enhanced-lesson_list">

                <?php foreach ( $lessons as $lesson ) : ?>
                    <div class='post-<?php echo esc_attr( $lesson['post']->ID ); ?> <?php echo esc_attr( $lesson['sample'] ); ?>'>

                        <div class="list-count">
                            <?php echo $lesson['sno']; ?>
                        </div>

                        <h4>
                            <a class='<?php echo esc_attr( $lesson['status'] ); ?>' href='<?php echo esc_attr( $lesson['permalink'] ); ?>'>
                                <?php
                                echo $lesson['post']->post_title;

                                $lds_post_id = $lesson['post']->ID;
                                include( ldvc_get_template_part('partials/enhanced-meta.php') ); ?>
                            </a>
                            <?php
                            /**
                             * Not available message for drip feeding lessons
                             */
                            ?>
                            <?php if ( ! empty( $lesson['lesson_access_from'] ) ) : ?>
                                <small class="notavailable_message">
                                    <?php echo sprintf( __( 'Available on: %s ', 'lds_skins' ), learndash_adjust_date_time_display( $lesson['lesson_access_from'] ) ); ?>

                                </small>
                            <?php endif; ?>


                            <?php
                            /**
                             * Lesson Topics
                             */
                            ?>
                            <?php $topics = @$lesson_topics[ $lesson['post']->ID ]; ?>

                            <?php if ( ! empty( $topics ) ) : ?>
                                <div id='learndash_topic_dots-<?php echo esc_attr( $lesson['post']->ID ); ?>' class="learndash_topic_dots type-list">
                                    <ul>
                                        <?php $odd_class = ''; ?>
                                        <?php foreach ( $topics as $key => $topic ) : ?>
                                            <?php $odd_class = empty( $odd_class ) ? 'nth-of-type-odd' : ''; ?>
                                            <?php $completed_class = empty( $topic->completed ) ? 'topic-notcompleted':'topic-completed'; ?>
                                            <li class='<?php echo esc_attr( $odd_class ); ?>'>
                                                <span class="topic_item">
                                                    <a class='<?php echo esc_attr( $completed_class ); ?>' href='<?php echo esc_attr( get_permalink( $topic->ID ) ); ?>' title='<?php echo esc_attr( $topic->post_title ); ?>'>
                                                        <span>
                                                            <?php
                                                            echo $topic->post_title;

                                                            $lds_post_id = $topic->ID;
                                                            include( ldvc_get_template_part('partials/enhanced-meta.php') ); ?>
                                                        </span>
                                                    </a>
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                        </h4>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    <?php endif; ?>
