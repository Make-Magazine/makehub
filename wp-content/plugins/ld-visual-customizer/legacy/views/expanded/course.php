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
lds_shortcodes_enqueue_scripts();
?>

<?php
/**
 * Display course status
 */
?>

<?php if ( $logged_in ) : ?>
	<span id="learndash_course_status">
		<b><?php printf( _x( '%s Status:', 'Course Status Label', 'lds_skins' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?></b> <?php echo $course_status; ?>
		<br />
	</span>
	<br />

	<?php
		/**
		 * Filter to add custom content after the Course Status section of the Course template output.
		 * @since 2.3
		 * See https://bitbucket.org/snippets/learndash/7oe9K for example use of this filter.
		 */
		echo apply_filters('ld_after_course_status_template_container', '', learndash_course_status_idx( $course_status ), $course_id, $user_id );
	?>

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


<?php if ( isset($materials) && !empty(trim($materials)) ) : ?>
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
        ?>
		<?php if ( ! empty( $lessons ) ) : ?>

            <div id="lds-shortcode" class="lds-course-list-style-expanded">

                <div class="lds-expanded-course-item <?php if( isset($class) ) echo esc_attr($class); ?>">

                    <div class="lds-expanded-course-lesson-list lds-expanded-section">
						<p><strong><?php echo LearnDash_Custom_Label::get_label( 'lessons' ) ?></strong></p>
						<ul>
							<?php foreach( $lessons as $lesson ): ?>
								<li>
									<a class="lds-content-status-<?php echo esc_attr( $lesson['status'] ); ?>" href="<?php echo esc_attr( learndash_get_step_permalink( $lesson['post']->ID ) ); ?>">

                                        <?php
                                        /**
                                         * Custom icon or fallback
                                         * @var [type]
                                         */
                                        $icon = ldvc_get_content_icon( $lesson['post']->ID );
										echo '<span class="lds-ec-icon fa ' . esc_attr($icon) . '"></span>' . esc_html($lesson['post']->post_title); ?>

                                        <?php
                                        /**
                                         * Custom information like the duration and time
                                         */
                                         if( get_post_meta( $lesson['post']->ID, '_lds_short_description', true ) ) echo '<span class="lds-ec-description">' . get_post_meta( $lesson['post']->ID, '_lds_short_description', true ) . '</span>';

                                         if( get_post_meta( $lesson['post']->ID, '_lds_duration', true ) ) echo '<span class="lds-ec-duration standalone"><i class="fa fa-clock-o"></i> ' . get_post_meta( $lesson['post']->ID, '_lds_duration', true ) . '</span>';

                                        /**
                                         * If this is a drip lesson, output the following
                                         */
                                        if ( ! empty( $lesson['lesson_access_from'] ) ) : ?>
                                            <span class="lds-ec-notavailable">
                                                <i class="fa fa-calendar"></i>
                                                <?php echo sprintf( __( 'Available on: %s ', 'lds_skins' ), learndash_adjust_date_time_display( $lesson['lesson_access_from'] ) ); ?>
                                            </span>
                                        <?php endif; ?>
									</a>
                                    <?php
                                    /**
                                     * Populate the lesson topics
                                     * @var [type]
                                     */
                                    $topics = @$lesson_topics[ $lesson['post']->ID ];

                                    if ( ! empty( $topics ) ) : ?>
    									<ul class="lds-expanded-topic-list">
    										<?php include(  ldvc_get_template_part('partials/expanded-topics.php' ) ); ?>
    									</ul>
    								<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
                    </div>

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
					} ?>

                    <?php endif; ?>



                    <?php if ( ! empty( $quizzes ) ) include( ldvc_get_template_part('partials/expanded-quizes.php') ); ?>

                </div> <!--/.lds-expanded-list -->
            </div> <!--/#lds-shortcode-->
	</div>
	<?php endif;
endif;
