<?php
/**
 * Displays content of course
 *
 * Available Variables:
 * $course_id 		: (int) ID of the course
 * $course 		: (object) Post object of the course
 * $course_settings : (array) Settings specific to current course
 *
 * $courses_options : Options/Settings as configured on Course Options page
 * $lessons_options : Options/Settings as configured on Lessons Options page
 * $quizzes_options : Options/Settings as configured on Quiz Options page
 *
 * $user_id 		: Current User ID
 * $logged_in 		: User is logged in
 * $current_user 	: (object) Currently logged in user object
 *
 * $course_status 	: Course Status
 * $has_access 	: User has access to course or is enrolled.
 * $has_course_content		: Course has course content
 * $lessons 		: Lessons Array
 * $quizzes 		: Quizzes Array
 * $lesson_progression_enabled 	: (true/false)
 *
 * @since 2.1.0
 *
 * @package LearnDash\Course
 */
?>

<?php
/**
 * Show Course Status
 */
?>
<?php if ( $has_course_content ) : ?>
	<div id='lds-course-content'>
		<h4 id='learndash_course_content_title'><?php printf( _x( '%s Content', 'Course Content Label', 'lds_skins' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?></h4>

		<?php /* Show Lesson List */ ?>
		<?php if ( ! empty( $lessons ) ) : ?>

			<div id='lds-grid-lessons'>

				<div id='lesson_heading'>
					<span><?php echo LearnDash_Custom_Label::get_label( 'lessons' ) ?></span>
				</div>

				<div id='lds-grid-list'>
                    <div class='lds-row'>

					<?php
                    $i      = 0;
                    $class  = ( $cols == 2 ? 'lds-col-md-6' : 'lds-col-md-4' );
                    foreach ( $lessons as $lesson ) :
                        if( $i % $cols == 0 && $i > 1 ) echo '</div><div class="lds-row">'; ?>
                            <div id='post-<?php echo $lesson['post']->ID; ?>' class='lds-lesson-item <?php echo esc_attr( $lesson['sample'] ) . ' ' . $class; ?>'>

                                <div class='lds-lesson-icon'>
                                    <p class='lds-lesson-count'><?php echo esc_html( $lesson['sno'] ); ?></p>
                                </div>

                                <h4><a class='<?php echo $lesson['status']; ?>' href='<?php echo $lesson['permalink']?>'><?php echo $lesson['post']->post_title; ?></a></h4>

                                <div class='lds-lesson-excerpt'>
                                    <?php // TODO: Gotta pull out the excerpt from $lesson['post']->ID
                                    // the_excerpt(); ?>
                                </div>

								<?php  /* Not available message for drip feeding lessons */ ?>
								<?php if ( ! empty( $lesson['lesson_access_from'] ) ) : ?>
									<small class='notavailable_message'>
										<?php echo sprintf( __( 'Available on: %s ', 'lds_skins' ), learndash_adjust_date_time_display($lesson['lesson_access_from'] ) ); ?>
									</small>
								<?php endif; ?>

								<?php /* Lesson Topis */ ?>
								<?php $topics = @$lesson_topics[ $lesson['post']->ID ]; ?>

								<?php if ( ! empty( $topics ) ) : ?>

                                    <h5><?php esc_html_e( 'Topics', 'lds_skins' ); ?></h5>

                                    <ul id='lds-lesson-topics-<?php echo $lesson['post']->ID; ?>' class='lds-lesson-topics-list'>
                                        <?php foreach ( $topics as $key => $topic ) :
                                            // TODO: Pull in font awesome icons from kit
                                            $completed_class = empty( $topic->completed ) ? 'topic-notcompleted' : 'topic-completed'; ?>
                                            <li>
                                                <a class='<?php echo esc_attr( $completed_class ); ?>' href='<?php echo esc_attr( get_permalink( $topic->ID ) ); ?>' title='<?php echo $topic->post_title; ?>'>
                                                    <span><?php echo $topic->post_title; ?></span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>

                                <?php endif; ?>

						</div>
					<?php endforeach; ?>
                    </div>
				</div>
			</div>
		<?php endif; ?>


		<?php /* Show Quiz List */	?>
		<?php if ( ! empty( $quizzes ) ) : ?>
			<div id='learndash_quizzes'>
				<div id='quiz_heading'>
					<span><?php echo LearnDash_Custom_Label::get_label( 'quizzes' ); ?></span><span class='right'><?php _e( 'Status', 'lds_skins' ); ?></span>
				</div>
				<div id='quiz_list'>
				<?php foreach ( $quizzes as $quiz ) : ?>
					<div id='post-<?php echo $quiz['post']->ID; ?>' class='<?php echo $quiz['sample'];?>'>
						<div class='list-count'><?php echo $quiz['sno']; ?></div>
						<h4>
							<a class='<?php echo $quiz['status']; ?>' href='<?php echo $quiz['permalink']?>'><?php echo $quiz['post']->post_title; ?></a>
						</h4>
					</div>
				<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

	</div>
<?php endif; ?>
