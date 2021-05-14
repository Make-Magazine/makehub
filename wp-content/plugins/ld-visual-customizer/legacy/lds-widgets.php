<?php
/**
 * Enhanced Course Progress Widget
 *
 * since 1.4
 */
class LDVC_Course_Progress_Widget extends WP_Widget {

	/**
	 * Set up course project widget
	 */
	function __construct() {

		$label = 'Course';
		$slug  = 'course';

		if( class_exists('LearnDash_Custom_Label') ) {
			$label = LearnDash_Custom_Label::get_label( 'course' );
			$slug  = LearnDash_Custom_Label::label_to_lower( 'course' );
		}

		$widget_ops = array(
			'classname' => 'widget_ldcourseprogress',
			'description' => sprintf( _x( 'Enhanced LearnDash %s progress bar', 'placeholders: course', 'lds_skins' ), $slug )
		);
		$control_ops = array(); //'width' => 400, 'height' => 350);
		parent::__construct( 'ldvccourseprogress', sprintf( _x( 'Enhanced %s Progress Bar', 'Course Progress Bar Label', 'lds_skins' ), $label ), $widget_ops, $control_ops );
	}



	/**
	 * Displays widget
	 *
	 * @since 2.1.0
	 *
	 * @param  array $args     widget arguments
	 * @param  array $instance widget instance
	 * @return string          widget output
	 */
	function widget( $args, $instance ) {
		global $learndash_shortcode_used;

		extract( $args );
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance );

		if ( ! is_singular() ) {
			return;
		}

		$progressbar = lds_detailed_progress_shortcode( $args );

		if ( empty( $progressbar ) ) {
			return;
		}

		echo $before_widget;

		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}

		echo $progressbar;
		echo $after_widget;

		$learndash_shortcode_used = true;
	}



	/**
	 * Handles widget updates in admin
	 *
	 * @since 2.1.0
	 *
	 * @param  array $new_instance
	 * @param  array $old_instance
	 * @return array $instance
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}



	/**
	 * Display widget form in admin
	 *
	 * @since 2.1.0
	 *
	 * @param  array $instance widget instance
	 */
	function form( $instance ) {
		$instance = wp_parse_args( (array)$instance, array('title' => '') );
		$title = strip_tags( $instance['title'] );
		//$text = format_to_edit( $instance['text'] );
		?>
			<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'lds_skins' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
		<?php
	}

}

add_action( 'widgets_init', 'ldvc_course_progress_widget' );
function ldvc_course_progress_widget() {
	register_widget("LDVC_Course_Progress_Widget");
}

/**
 * Expanded course navigation widget
 *
 * Since 2.0
 */
class LDVC_Expanded_Course_Navigation_Widget extends WP_Widget {

	/**
	 * Setup Course Navigation Widget
	 */
	function __construct() {

		$label = 'Course';

		if( class_exists('LearnDash_Custom_Label') ) {
			$label = LearnDash_Custom_Label::get_label( 'course' );
			$slug  = LearnDash_Custom_Label::label_to_lower( 'course' );
		}

		$widget_ops = array(
			'classname' => 'widget_ldexpandedcoursenavigation',
			'description' => sprintf( _x( 'LearnDash - Expanded %s Navigation. Shows lessons and topics on the current course using the expanded format.', 'LearnDash - Course Navigation. Shows lessons and topics on the current course.', 'lds_skins' ), $label )
		);
		$control_ops = array(); //'width' => 400, 'height' => 350);

		parent::__construct( 'widget_ldexpandedcoursenavigation', sprintf( _x( 'Expanded %s Navigation', 'Expanded course navigation Label', 'lds_skins' ), $label ), $widget_ops, $control_ops );
	}


	/**
	 * Displays widget
	 *
	 * @since 2.1.0
	 *
	 * @param  array $args     widget arguments
	 * @param  array $instance widget instance
	 * @return string          widget output
	 */
	function widget( $args, $instance ) {

		global $learndash_shortcode_used;

		global $post;

		if ( empty( $post->ID ) || ! is_single() ) {
			return;
		}

		$course_id = learndash_get_course_id( $post->ID );

		if ( empty( $course_id ) ) {
			return;
		}

		extract( $args );

		 /**
		 * Filter widget title
		 *
		 * @since 2.1.0
		 *
		 * @param  string
		 */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance );

		echo $before_widget;

		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}

		learndash_expanded_course_navigation( $course_id, $instance );

		echo $after_widget;

		$learndash_shortcode_used = true;
	}


	/**
	 * Handles widget updates in admin
	 *
	 * @since 2.1.0
	 *
	 * @param  array $new_instance
	 * @param  array $old_instance
	 * @return array $instance
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] 					= 	strip_tags( $new_instance['title'] );

		$instance['show_lesson_quizzes']	= 	isset( $new_instance['show_lesson_quizzes'] ) ? (bool) $new_instance['show_lesson_quizzes'] : false;
		$instance['show_topic_quizzes'] 	= 	isset( $new_instance['show_topic_quizzes'] ) ? (bool) $new_instance['show_topic_quizzes'] : false;
		$instance['show_course_quizzes'] 	= 	isset( $new_instance['show_course_quizzes'] ) ? (bool) $new_instance['show_course_quizzes'] : false;

		return $instance;
	}


	/**
	 * Display widget form in admin
	 *
	 * @since 2.1.0
	 *
	 * @param  array $instance widget instance
	 */
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags( $instance['title'] );
		$show_lesson_quizzes 	= isset( $instance['show_lesson_quizzes'] ) ? (bool) $instance['show_lesson_quizzes'] : false;
		$show_topic_quizzes 	= isset( $instance['show_topic_quizzes'] ) ? (bool) $instance['show_topic_quizzes'] : false;
		$show_course_quizzes 	= isset( $instance['show_course_quizzes'] ) ? (bool) $instance['show_course_quizzes'] : false;

		//$text = format_to_edit($instance['text']);
		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'lds_skins' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>


			<p>
				<input class="checkbox" type="checkbox"<?php checked( $show_course_quizzes ); ?> id="<?php echo $this->get_field_id( 'show_course_quizzes' ); ?>" name="<?php echo $this->get_field_name( 'show_course_quizzes' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'show_course_quizzes' ); ?>"><?php _e( 'Show Course Quizzes?', 'lds_skins' ); ?></label>
			</p>
			<p>
				<input class="checkbox" type="checkbox"<?php checked( $show_lesson_quizzes ); ?> id="<?php echo $this->get_field_id( 'show_lesson_quizzes' ); ?>" name="<?php echo $this->get_field_name( 'show_lesson_quizzes' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'show_lesson_quizzes' ); ?>"><?php _e( 'Show Lesson Quizzes?', 'lds_skins' ); ?></label>
			</p>
			<p>
				<input class="checkbox" type="checkbox"<?php checked( $show_topic_quizzes ); ?> id="<?php echo $this->get_field_id( 'show_topic_quizzes' ); ?>" name="<?php echo $this->get_field_name( 'show_topic_quizzes' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'show_topic_quizzes' ); ?>"><?php _e( 'Show Topic Quizzes?', 'lds_skins' ); ?></label>
			</p>
		<?php
	}

}

add_action( 'widgets_init', 'ldvc_register_expanded_course_navigation_widget' );

function ldvc_register_expanded_course_navigation_widget() {
	register_widget("LDVC_Expanded_Course_Navigation_Widget");
}
