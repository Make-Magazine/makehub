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

wp_enqueue_script('lds-global'); ?>


<?php
/**
 * Display course status
 */

if ( $logged_in ) : ?>
	<p id="learndash_course_status">
		<strong><?php printf( _x( '%s Status:', 'Course Status Label', 'lds_skins' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?></strong> <?php echo $course_status; ?>
	</p>

	<?php
	/**
	 * Filter to add custom content after the Course Status section of the Course template output.
	 * @since 2.3
	 * See https://bitbucket.org/snippets/learndash/7oe9K for example use of this filter.
	 */
	echo apply_filters('ld_after_course_status_template_container', '', learndash_course_status_idx( $course_status ), $course_id, $user_id );

	if ( ! empty( $course_certficate_link ) ) : ?>
		<div id="learndash_course_certificate">
			<p><a href='<?php echo esc_attr( $course_certficate_link ); ?>' class="btn-blue" target="_blank"><?php echo apply_filters('ld_certificate_link_label', __( 'PRINT YOUR CERTIFICATE', 'lds_skins' ), $user_id, $post->ID ); ?></a></p>
		</div>
	<?php
	endif;
endif;

// The primary content
echo '<div class="learndash_content">' . $content . '</div>';

if ( ! $has_access ):

	/**
	 * Filter to add custom content before the Course Payment Button.
	 *
	 * @since 2.5.8
	 */
	do_action( 'learndash-course-payment-buttons-before', $course_id, $user_id );

	echo learndash_payment_buttons( $post );

	/**
	 * Filter to add custom content after the Course Payment Button.
	 *
	 * @since 2.5.8
	 */
	do_action( 'learndash-course-payment-buttons-after', $course_id, $user_id );
endif;

if ( isset($materials) && !empty($materials) ) : ?>
	<div id="learndash_course_materials">
		<h4><?php printf( _x( '%s Materials', 'Course Materials Label', 'lds_skins' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?></h4>
		<p><?php echo $materials; ?></p>
	</div>
<?php
endif;

if ( $has_course_content ) :

	$show_course_content = true;
	if ( !$has_access ) :
		if ( $course_meta['sfwd-courses_course_disable_content_table'] == 'on' ) :
			$show_course_content = false;
		endif;
	endif;

	if ( $show_course_content ) : ?>

		<div id="learndash_course_content">

			<h4 id="learndash_course_content_title"><?php printf( _x( '%s Content', 'Course Content Label', 'lds_skins' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?></h4>

			<?php
	        /**
	         * Display lesson list
	         */
	        if ( ! empty( $lessons ) ) :

				// Setup variables
				$i = 0;
				$c = 1;

				$cols = intval(get_option( 'lds_grid_columns', 2 ));

				switch( $cols ) {
					case(2):
						$class = 'lds-col-md-6';
						break;
					case(3):
						$class = 'lds-col-md-4';
						break;
					case(4):
						$class = 'lds-col-md-3';
						break;
				}

				// Enqueue the necissary shortcode scripts
				lds_shortcodes_enqueue_scripts(); ?>

				<div id="lds-shortcode" class="lds-container-fluid lds-course-list-style-banner u-lds-flush-margins">

					<div class="lds-row">
						<div class="lds-col-md-12 m-lds-header">
							<h2><?php echo LearnDash_Custom_Label::get_label( 'lessons' ) ?></h2>
						</div>
					</div>

			    	<div class="lds-row">

					<?php
					foreach ( $lessons as $lesson ) :

						// $topics = @$lesson_topics[ $lesson['post']->ID ];

						$meta 	= array(
						    'content_type'  =>  get_post_meta( $lesson['post']->ID, '_lds_content_type', true ),
						    'duration'      =>  get_post_meta( $lesson['post']->ID, '_lds_duration', true ),
						    'description'   =>  get_post_meta( $lesson['post']->ID, '_lds_short_description', true ),
						    'icon'          =>  ldvc_get_content_icon( $lesson['post']->ID ),
							'status'		=>	$lesson['status'],
							'post_id'		=>	$lesson['post']->ID,
							'type'			=> 'course'
						);
						$topics = @$lesson_topics[ $lesson['post']->ID ];

						// Append the status to the class
						$class .= ' status-' . $meta['status'];

 						if( $i % $cols == 0 && $i > 1 ) echo '</div><div class="lds-row">';
						?>
						<div class="lds-course-item <?php echo esc_attr( $class ); ?>">

							<?php

							// Get banner image
							include( ldvc_get_template_part('grid-banner/partials/item-banner.php') ); ?>

							<h3 class="m-lds-grid-title">
								<a href="<?php echo esc_attr( learndash_get_step_permalink( $lesson['post']->ID, $course_id ) ); ?>">
									<?php
									echo '<span class="m-lds-grid-title-count">' . esc_html($c) . '.</span> ' . esc_html($lesson['post']->post_title); ?>
								</a>
							</h3>

							<?php
							include( ldvc_get_template_part('grid-banner/partials/item-meta.php') ); ?>

							<div class="lds-entry-content">

								<?php if ( ! empty( $lesson['lesson_access_from'] ) ) : ?>
									<small class="notavailable_message">
										<i class="fa fa-calendar"></i> <?php echo sprintf( __( 'Available on: %s ', 'lds_skins' ), learndash_adjust_date_time_display( $lesson['lesson_access_from'] ) ); ?>
									</small>
								<?php endif; ?>

								<?php
								if( get_post_meta( $lesson['post']->ID, '_lds_short_description' ) ):
									echo wpautop( get_post_meta( $lesson['post']->ID, '_lds_short_description', true ) );
								endif; ?>

								<?php
								if ( ! empty($topics) ) : ?>
									<div class="m-lds-item-row">
										<h4>
											<a href="#" class="js-lds-show-topics m-lds-toggle">
												<i class="fa fa-chevron-circle-down"></i>
												<?php echo esc_html( count($topics) . ' ' . __( 'Topics', 'lds_skins'  ) ); ?>
											</a>
										</h4>
										<div class="m-lds-topics-list u-lds-hide">
											<?php
											foreach( $topics as $topic ):
												$meta = array(
													'content_type'  =>  get_post_meta( $topic->ID, '_lds_content_type', true ),
													'duration'      =>  get_post_meta( $topic->ID, '_lds_duration', true ),
													'description'   =>  get_post_meta( $topic->ID, '_lds_short_description', true ),
													'icon'          =>  ldvc_get_content_icon( $topic->ID ),
													'status'		=>	( $topic->completed == 0 ? 'notcompleted' : 'completed' ),
													'type'			=>	'lesson',
												);
												?>
												<div class="m-lds-topic-item">
													<p>
														<strong>
															<a href="<?php echo esc_attr( learndash_get_step_permalink($topic->ID) ); ?>">
																<?php
																echo esc_html( get_the_title($topic->ID) );
																?>
															</a>
														</strong>
													</p>
													<?php
													include( ldvc_get_template_part('grid-banner/partials/item-meta.php') ); ?>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								<?php
							endif;  ?>

							</div> <!--/.lds-entry-content-->

							<?php
							switch( $lesson['status'] ) {
								case( 'complete' ):
									$label = __( 'Revisit Lesson', 'lds_skins' );
									break;
								default:
									$label = __( 'Start Lesson', 'lds_skins' );
									break;
							}

							if( $lesson['status'] != 'notavailable' ): ?>
								<p><a href="<?php echo esc_attr( learndash_get_step_permalink($lesson['post']->ID) ); ?>" class="lds-button lds-button-primary"><?php echo esc_html( $label ); ?> <i class="fa fa-angle-right"></i></a></p>
							<?php endif; ?>

						</div> <!--/.lds-course-item-->
			        	<?php
						$i++;
						$c++;
					endforeach;  ?>
				</div> <!--/.lds-row-->
				<?php

		        if ( ! empty( $quizzes ) ): ?>
		            <div class="l-lds-grid-row">
		                <div class="m-lds-header">
		                    <h2><?php echo esc_html( LearnDash_Custom_Label::get_label( 'quizzes' ) ); ?></h2>
		                </div>
		                <div class="lds-row">
		                    <?php include( ldvc_get_template_part('grid-banner/partials/item-quizes.php') ); ?>
		                </div>
		            </div>
		        <?php endif; ?>
			</div> <!--/#lds-shortcode-->
		<?php endif; ?>
	</div> <!--/#learndash_course_content-->
<?php endif;
endif; ?>
