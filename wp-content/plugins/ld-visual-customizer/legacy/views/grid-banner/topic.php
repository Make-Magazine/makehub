<?php
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

// Enque the necissary scripts
lds_shortcodes_enqueue_scripts(); ?>

<div id="learndash_back_to_lesson">
    <a href='<?php echo esc_attr( get_permalink( $lesson_id ) ); ?>'>&larr; <?php printf( _x( 'Back to %s', 'Back to Lesson Label', 'lds_skins' ), LearnDash_Custom_Label::get_label( 'lesson' ) ); ?></a>
</div> <!--/#learndash_back_to_lesson-->

<?php if ( $lesson_progression_enabled && ! $previous_topic_completed ) : ?>

    <p>
    	<span id="learndash_complete_prev_topic">
            <?php
        	$previous_item = learndash_get_previous($post);

            if( empty($previous_item) ) $previous_item = learndash_get_previous($lesson_post);

    		if( ( !empty( $previous_item ) ) && ( $previous_item instanceof WP_Post ) ) {
    			if ( $previous_item->post_type == 'sfwd-quiz') {
    				echo sprintf( _x( 'Please go back and complete the previous <a class="learndash-link-previous-incomplete" href="%s">%s</a>.', 'placeholders: quiz URL, quiz label', 'lds_skins' ), get_permalink( $previous_item->ID ), LearnDash_Custom_Label::label_to_lower('quiz') );

    			} else if ( $previous_item->post_type == 'sfwd-topic') {
    				echo sprintf( _x( 'Please go back and complete the previous <a class="learndash-link-previous-incomplete" href="%s">%s</a>.', 'placeholders: topic URL, topic label', 'lds_skins' ), get_permalink( $previous_item->ID ), LearnDash_Custom_Label::label_to_lower('topic') );
    			} else {
    				echo sprintf( _x( 'Please go back and complete the previous <a class="learndash-link-previous-incomplete" href="%s">%s</a>.', 'placeholders: lesson URL, lesson label', 'lds_skins' ), get_permalink( $previous_item->ID ), LearnDash_Custom_Label::label_to_lower('lesson') );
    			}

    		} else {
    			echo sprintf( _x( 'Please go back and complete the previous %s.', 'placeholder lesson', 'lds_skins' ), LearnDash_Custom_Label::label_to_lower('lesson') );
    		} ?>
        </span>
    </p>

<?php elseif ( $lesson_progression_enabled && ! $previous_lesson_completed ) : ?>

	<p>
        <span id="learndash_complete_prev_lesson">
    	    <?php
    		$previous_item = learndash_get_previous( $post );

    		if ( empty($previous_item) ) $previous_item = learndash_get_previous($lesson_post);

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
    		} ?>
        </span>
    </p>

<?php endif; ?>

<?php if ( $show_content ) : ?>

    <?php if ( ( isset( $materials ) ) && ( !empty( $materials ) ) ) : ?>
		<div id="learndash_topic_materials" class="learndash_topic_materials">
			<h4><?php printf( esc_html_x( '%s Materials', 'Topic Materials Label', 'lds_skins' ), LearnDash_Custom_Label::get_label( 'topic' ) ); ?></h4>
			<p><?php echo $materials; ?></p>
		</div>
	<?php endif; ?>

	<div class="learndash_content"><?php echo $content; ?></div>

    <div id="lds-shortcode" class="lds-container-fluid lds-course-list-style-banner u-lds-flush-margins">

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
    <?php endif;
        /**
         * Show Mark Complete Button
         */
        if ( $all_quizzes_completed ) echo '<p>' . learndash_mark_complete( $post ) . '</p>'; ?>

    </div>

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
