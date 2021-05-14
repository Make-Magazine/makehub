<?php
/**
 * Displays a lesson.
 *
 * Available Variables:
 *
 * $course_id 		: (int) ID of the course
 * $course 		: (object) Post object of the course
 * $course_settings : (array) Settings specific to current course
 * $course_status 	: Course Status
 * $has_access 	: User has access to course or is enrolled.
 *
 * $courses_options : Options/Settings as configured on Course Options page
 * $lessons_options : Options/Settings as configured on Lessons Options page
 * $quizzes_options : Options/Settings as configured on Quiz Options page
 *
 * $user_id 		: (object) Current User ID
 * $logged_in 		: (true/false) User is logged in
 * $current_user 	: (object) Currently logged in user object
 *
 * $quizzes 		: (array) Quizzes Array
 * $post 			: (object) The lesson post object
 * $topics 		: (array) Array of Topics in the current lesson
 * $all_quizzes_completed : (true/false) User has completed all quizzes on the lesson Or, there are no quizzes.
 * $lesson_progression_enabled 	: (true/false)
 * $show_content	: (true/false) true if lesson progression is disabled or if previous lesson is completed.
 * $previous_lesson_completed 	: (true/false) true if previous lesson is completed
 * $lesson_settings : Settings specific to the current lesson.
 *
 * @since 2.1.0
 *
 * @package LearnDash\Lesson
 */
lds_shortcodes_enqueue_scripts();
?>
<?php if ( @$lesson_progression_enabled && ! @$previous_lesson_completed ) : ?>
	<span id="learndash_complete_prev_lesson">
	<?php
		$previous_item = learndash_get_previous( $post );
		if ( ( !empty( $previous_item ) ) && ( $previous_item instanceof WP_Post ) ) {
			if ( $previous_item->post_type == 'sfwd-quiz') {
				echo sprintf( _x( 'Please go back and complete the previous <a class="learndash-link-previous-incomplete" href="%s">%s</a>.', 'placeholders: quiz URL, quiz label', 'lds_skins' ), get_permalink( $previous_item->ID ), LearnDash_Custom_Label::label_to_lower('quiz') );

			} else if ( $previous_item->post_type == 'sfwd-topic') {
				echo sprintf( _x( 'Please go back and complete the previous <a class="learndash-link-previous-incomplete" href="%s">%s</a>.', 'placeholders: topic URL, topic label', 'lds_skins' ), get_permalink( $previous_item->ID ), LearnDash_Custom_Label::label_to_lower('topic') );
			} else {
				echo sprintf( _x( 'Please go back and complete the previous <a class="learndash-link-previous-incomplete" href="%s">%s</a>.', 'placeholders: lesson URL, lesson label', 'lds_skins' ), get_permalink( $previous_item->ID ), LearnDash_Custom_Label::label_to_lower('lesson') );
			}

		} else {
			echo sprintf( _x( 'Please go back and complete the previous %s.', 'placeholder lesson', 'lds_skins' ), LearnDash_Custom_Label::label_to_lower('lesson') );
		}
	?>
	</span><br />
	<?php add_filter( 'comments_array', 'learndash_remove_comments', 1, 2 ); ?>
<?php endif; ?>

<?php if ( $show_content ) : ?>

	<?php if ( ( isset( $materials ) ) && ( !empty( $materials ) ) ) : ?>
		<div id="learndash_lesson_materials" class="learndash_lesson_materials">
			<h4><?php printf( esc_html_x( '%s Materials', 'Lesson Materials Label', 'lds_skins' ), LearnDash_Custom_Label::get_label( 'lesson' ) ); ?></h4>
			<p><?php echo $materials; ?></p>
		</div>
	<?php endif; ?>

	<div class="learndash_content"><?php echo $content; ?></div>

	<?php
    /**
     * Lesson Topics
     */
    ?>
		<div id="lds-shortcode" class="lds-course-list-style-expanded">
			<div class="lds-expanded-course-item <?php echo esc_attr($class); ?>">

				<?php if ( ! empty( $topics ) ) : ?>
				<div class="lds-expanded-course-lesson-list lds-expanded-section">
					<p><strong><?php printf( _x( '%s %s', 'Lesson Topics Label', 'lds_skins'), LearnDash_Custom_Label::get_label( 'lesson' ), LearnDash_Custom_Label::get_label( 'topics' ) ); ?></strong></p>
					<ul>
						<?php include( ldvc_get_template_part('partials/expanded-topics.php') ); ?>
					</ul>
				</div>
				<?php endif; ?>

				<?php if ( ! empty( $quizzes ) ) include( ldvc_get_template_part('partials/expanded-quizes.php') ); ?>

				<?php
			    /**
			     * Display Lesson Assignments
			     */
			    ?>
				<?php if ( ( lesson_hasassignments( $post ) ) && ( !empty( $user_id ) ) ) : ?>
					<?php
						$ret = SFWD_LMS::get_template(
								'learndash_lesson_assignment_uploads_list.php',
								array(
									'course_step_post' => $post,
									'user_id' => $user_id
								)
							);
						echo $ret;
					?>

				<?php endif; ?>

			</div>
		</div> <!--/#lds-shortcode-wrapper-->


	<?php
    /**
     * Display Mark Complete Button
     */
    ?>
	<?php if ( $all_quizzes_completed && $logged_in ) : ?>
		<br />
        <?php echo learndash_mark_complete( $post ); ?>
	<?php endif; ?>

<?php endif; ?>

<br />

<?php
$ret = SFWD_LMS::get_template(
		'learndash_course_steps_navigation.php',
		array(
			'course_id' => $course_id,
			'course_step_post' => $post,
			'user_id' => $user_id,
			'course_settings' => isset( $course_settings ) ? $course_settings : array()
		)
	);
echo $ret;
