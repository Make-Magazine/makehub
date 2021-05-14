<?php

lds_shortcodes_enqueue_scripts();

global $post;

$c      = 1;
$i      = 0;

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

if ( @$lesson_progression_enabled && ! @$previous_lesson_completed ) : ?>
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
<?php endif;

if ( $show_content ): ?>

	<?php if ( ( isset( $materials ) ) && ( !empty( $materials ) ) ) : ?>
		<div id="learndash_lesson_materials" class="learndash_lesson_materials">
			<h4><?php printf( esc_html_x( '%s Materials', 'Lesson Materials Label', 'lds_skins' ), LearnDash_Custom_Label::get_label( 'lesson' ) ); ?></h4>
			<p><?php echo $materials; ?></p>
		</div>
	<?php endif; ?>

	<div class="learndash_content"><?php echo $content; ?></div>

    <div id="lds-shortcode" class="lds-container-fluid lds-course-list-style-banner u-lds-flush-margins">

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

        <?php if ( ! empty( $topics ) ) : ?>
            <div class="l-lds-grid-row">
                <div class="m-lds-header">
                    <h2><?php printf( _x( '%s %s', 'Lesson Topics Label', 'lds_skins'), LearnDash_Custom_Label::get_label( 'lesson' ), LearnDash_Custom_Label::get_label( 'topics' ) ); ?></h2>
                </div>
                <div class="lds-row">
                    <?php
                    foreach ( $topics as $key => $topic ) :

                        $meta = array(
                            'content_type'  =>  get_post_meta( $topic->ID, '_lds_content_type', true ),
                            'duration'      =>  get_post_meta( $topic->ID, '_lds_duration', true ),
                            'description'   =>  get_post_meta( $topic->ID, '_lds_short_description', true ),
                            'icon'          =>  ldvc_get_content_icon( $topic->ID ),
                            'status'		=>	( $topic->completed == 0 ? 'notcompleted' : 'completed' ),
                            'post_id'       =>  $topic->ID,
                            'type'          =>  'lesson'
                        );

                        // Append the status to the class
                        $class .= ' status-' . $meta['status'];

                        if( $i % $cols == 0 && $i > 1 ) echo '</div><div class="lds-row">';
                        ?>
                        <div class="lds-course-item <?php echo esc_attr( $class ); ?>">
        					<?php
							include( ldvc_get_template_part('grid-banner/partials/item-banner.php') );
        					// Get banner image
        					?>

        					<h3 class="m-lds-grid-title">
        						<a href="<?php echo esc_attr( learndash_get_step_permalink($topic->ID) ); ?>">
        							<?php echo esc_html( $c . '. ' . get_the_title($topic->ID) ); ?>
        						</a>
        					</h3>

        					<?php
							include( ldvc_get_template_part('grid-banner/partials/item-meta.php') ); ?>

        					<div class="lds-entry-content">

        						<?php /* if ( ! empty( $lesson['lesson_access_from'] ) ) : ?>
        							<small class="notavailable_message">
        								<?php echo sprintf( __( 'Available on: %s ', 'lds_skins' ), learndash_adjust_date_time_display( $lesson['lesson_access_from'] ) ); ?>
        							</small>
        						<?php endif; */ ?>

        						<?php if( $meta['description'] ) echo wpautop( $meta['description'] ); ?>

                            </div>
                        </div>
                    <?php $i++; $c++; endforeach; ?>
                </div>
            </div> <!--/.lds-grid-row-->
        <?php endif; ?>

        <?php if ( ! empty( $quizzes ) ): ?>
            <div class="l-lds-grid-row">
                <div class="m-lds-header">
                    <h2><?php echo esc_html( LearnDash_Custom_Label::get_label( 'quizzes' ) ); ?></h2>
                </div>
                <?php include( ldvc_get_template_part('grid-banner/partials/item-quizes.php') ); ?>
            </div>
        <?php endif; ?>



        <?php
        /**
         * Display Mark Complete Button
         */
        if ( $all_quizzes_completed && $logged_in ) : ?>
            <?php echo '<p>' . learndash_mark_complete( $post ) . '</p>'; ?>
        <?php endif; ?>

        </div> <!--/#lds-shortcode-->

<?php endif; ?>


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
