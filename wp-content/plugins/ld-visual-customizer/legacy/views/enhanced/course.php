<?php
/**
 * Displays a course
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
 * $materials 		: Course Materials
 * $has_course_content		: Course has course content
 * $lessons 		: Lessons Array
 * $quizzes 		: Quizzes Array
 * $lesson_progression_enabled 	: (true/false)
 * $has_topics		: (true/false)
 * $lesson_topics	: (array) lessons topics
 *
 * @since 2.1.0
 *
 * @package LearnDash\Course
 */
?>

<?php
/**
 * Display course status
 */
?>
<?php if ( $logged_in ) :

	/**
	 * Custom Course Header
	 */
	include( ldvc_get_template_part('enhanced/course-header.php') );

	/**
	 * Filter to add custom content after the Course Status section of the Course template output.
	 * @since 2.3
	 * See https://bitbucket.org/snippets/learndash/7oe9K for example use of this filter.
	 */
	echo apply_filters('ld_after_course_status_template_container', '', learndash_course_status_idx( $course_status ), $course_id, $user_id ); ?>

	<?php  if ( ! empty( $course_certficate_link ) ) : ?>
		<div id="learndash_course_certificate">
			<a href='<?php echo esc_attr( $course_certficate_link ); ?>' class="btn-blue" target="_blank"><?php echo apply_filters('ld_certificate_link_label', __( 'PRINT YOUR CERTIFICATE', 'lds_skins' ), $user_id, $post->ID ); ?></a>
		</div>
		<br />
	<?php endif; ?>
<?php endif; ?>

<?php echo $content; ?>

<?php if ( ! $has_access ) : ?>
	<?php
	/**
	 * Filter to add custom content before the Course Payment Button.
	 *
	 * @since 2.5.8
	 */
	do_action( 'learndash-course-payment-buttons-before', $course_id, $user_id );
	?>
	<?php echo learndash_payment_buttons( $post ); ?>
	<?php
	/**
	 * Filter to add custom content after the Course Payment Button.
	 *
	 * @since 2.5.8
	 */
	do_action( 'learndash-course-payment-buttons-after', $course_id, $user_id );
	?>
<?php endif; ?>


<?php
if ( isset($materials) && !empty(trim($materials)) ) : ?>
	<div id="learndash_course_materials">
		<h4><?php printf( _x( '%s Materials', 'Course Materials Label', 'lds_skins' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?></h4>
		<p><?php echo $materials; ?></p>
	</div>
<?php endif; ?>

<?php if ( $has_course_content ) : ?>
	<?php
		$show_course_content = true;
		if ( !$has_access ) :
			if ( $course_meta['sfwd-courses_course_disable_content_table'] == 'on' ) :
				$show_course_content = false;
			endif;
		endif;

		if ( $show_course_content ) :
			?>

	<div id="learndash_course_content">

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

				<div id="lessons_list" class="lds-enhanced-lesson_list ldvc-enhanced-list">

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
		<?php
			global $course_lessons_results;
			if ( isset( $course_lessons_results['pager'] ) ) {
				echo SFWD_LMS::get_template(
					'learndash_pager.php',
					array(
					'pager_results' => $course_lessons_results['pager'],
					'pager_context' => 'course_lessons'
					)
				);
			}
		?>
		<?php endif; ?>

		<?php
			if ( ( isset( $course_lessons_results['pager'] ) ) && ( !empty( $course_lessons_results['pager'] ) ) ) {
				if ( $course_lessons_results['pager']['paged'] == $course_lessons_results['pager']['total_pages'] ) {
					$show_course_quizzes = true;
				} else {
					$show_course_quizzes = false;
				}
			} else {
				$show_course_quizzes = true;
			}
		?>
		<?php
        /**
         * Display quiz list
         */
        ?>
		<?php if ( ! empty( $quizzes ) ) : ?>
			<div id="learndash_quizzes">
				<div id="quiz_heading">
						<span><?php echo LearnDash_Custom_Label::get_label( 'quizzes' ) ?></span><span class="right"><?php _e( 'Status', 'lds_skins' ); ?></span>
				</div>
				<div id="quiz_list">

					<?php foreach( $quizzes as $quiz ) : ?>
						<div id='post-<?php echo esc_attr( $quiz['post']->ID ); ?>' class='<?php echo esc_attr( $quiz['sample'] ); ?>'>
							<div class="list-count"><?php echo $quiz['sno']; ?></div>
							<h4>
                                <a class='<?php echo esc_attr( $quiz['status'] ); ?>' href='<?php echo esc_attr( $quiz['permalink'] ); ?>'>
									<?php echo $quiz['post']->post_title;

									$lds_post_id = $quiz['post']->ID;
									include( ldvc_get_template_part('partials/enhanced-meta.php') );?>
								</a>
                            </h4>
						</div>
					<?php endforeach; ?>

				</div>
			</div>
		<?php endif; ?>

	</div>
		<?php endif; ?>
