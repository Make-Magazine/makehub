<?php
nt_frontend_note_assets();

$current_user 	= get_current_user_id();
$global_output	= '';
$courses 	    = ld_get_mycourses( $current_user );
$def_args 	    = array(
	'post_type'		=>	'coursenote',
	'posts_per_page'	=>	-1,
	'author__in'		=>	$current_user,
	'meta_key'		=>	'nt-note-current-lessson-id',
	'fields'			=>	'ids'
);

if( empty( $courses ) ) {
	return '<p>' . __( 'You\'re not signed up for any courses.', 'sfwd-lms' ) . '</p>';
}  ?>

<div id="learndash_course_content">

    <?php
    foreach( $courses as $course_id ):


    	$args  = array_merge( $def_args, array( 'meta_value' => $course_id ) );
    	$notes = get_posts( $args );


        $args = array(
            'post_type'     =>  'sfwd-courses',
            'post_status'   =>  'publish',
            'post__in'      =>  $course_id
        );

        $course = new WP_Query($args);

        while( $course->have_posts() ): $course->the_post();

            $lessons    = learndash_get_lesson_list( $course_id );
            $has_topics = true; ?>

                <h4 id="learndash_course_content_title"><?php the_title(); ?></h4>

                <?php
                /**
                 * Display lesson list
                 */
                if ( ! empty( $lessons ) ) : ?>

                    <div id="learndash_lessons">
                        <div id="lesson_heading">
            				<span><?php echo LearnDash_Custom_Label::get_label( 'lessons' ) ?></span>
            				<span class="right"><?php _e( 'Status', 'learndash' ); ?></span>
    				    </div>
                        <div id="lessons_list">
                            <?php foreach ( $lessons as $lesson ) : ?>
                                <div class='post-<?php echo esc_attr( $lesson['post']->ID ); ?> <?php echo esc_attr( $lesson['sample'] ); ?>'>

                                    <div class="list-count">
                                        <?php echo $lesson['sno']; ?>
                                    </div>
                                    <h4>
        								<a class='<?php echo esc_attr( $lesson['status'] ); ?>' href='<?php echo esc_attr( $lesson['permalink'] ); ?>'>
                                            <?php echo $lesson['post']->post_title; ?>
                                        </a>
                                        <?php
                                        /**
                                         * Not available message for drip feeding lessons
                                         */
                                        ?>
        								<?php if ( ! empty( $lesson['lesson_access_from'] ) ) : ?>
        									<small class="notavailable_message">
        										<?php echo sprintf( __( 'Available on: %s ', 'learndash' ), learndash_adjust_date_time_display( $lesson['lesson_access_from'] ) ); ?>

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
        															<span><?php echo $topic->post_title; ?></span>
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
                        </div> <!--/#lessons_list-->
                    </div> <!--/#learndash_lessons-->
                <?php endif; ?>
            <?php endwhile;
        endforeach; ?>
    </div>
<?php
