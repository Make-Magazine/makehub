<?php
/**
 * Custom shortcodes for more advanced output
 */


add_shortcode( 'lds_login', 'lds_login_shortcode' );
function lds_login_shortcode( $args ) {

	ob_start();

	echo '<div class="' . learndash_get_wrapper_class() . ' ldvc-login">' . learndash_get_template_part( 'modules/login-modal.php', array(), false ) . '</div>';

	return ob_get_clean();

}

add_shortcode( 'lds_course_list', 'lds_course_list_shortcode' );
function lds_course_list_shortcode( $atts ) {

	if( !isset($atts['style']) ) {
		$style = 'default';
	}

	$atts  = ( isset($atts) && !empty($atts) ? $atts : array() );
	$style = ( isset($atts['style']) && !empty($atts['style']) ? $atts['style'] : 'default' );

	$styles = apply_filters( 'lds_available_styles', array(
		'default',
		'expanded',
		'grid-banners'
	) );

	$legacy = array(
		'icon',
		'banner'
	);

	if( in_array( $style, $legacy ) ) {
		$style = 'grid-banners';
	}

	$cuser 	 = wp_get_current_user();
	$user_id = $cuser->ID;

	if( !in_array($style, $styles) ) {
		$style = 'default';
	}

	$atts['array'] = 'true';
	$atts['num'] = '-1';

	$courses = ld_course_list( $atts );

	$class = learndash_get_wrapper_class() . ' lds-course-list-' . $style . ' lds-course-list';

	if( isset($atts['cols']) ) {
		$class .= ' lds-course-list-cols-' . $atts['cols'];
	}

	ob_start(); ?>

		<div class="<?php echo esc_attr($class); ?>">
			<div class="ld-item-list ld-course-list">
				<?php
				foreach( $courses as $course ):
					include( lds_get_template_part( 'shortcodes/course-list/row.php' ) );
				endforeach; ?>
			</div>
		</div>

	<?php
	return ob_get_clean();

}

add_shortcode( 'ldvc_lesson_list', 'ldvc_lesson_list_shortcode' );
function ldvc_lesson_list_shortcode( $attr = array() ) {

	global $learndash_shortcode_used;
	$learndash_shortcode_used = true;

	if ( ! is_array( $attr ) ) {
		$attr = array();
	}

	$attr['post_type'] = learndash_get_post_type_slug( 'lesson' );
	$attr['mycourses'] = false;
	$attr['status']    = false;

	// If we have a course_id. Then we set the orderby to match the items within the course.
	if ( ( isset( $attr['course_id'] ) ) && ( ! empty( $attr['course_id'] ) ) ) {
		$attr['course_id'] = absint( $attr['course_id'] );
		$course_steps      = learndash_course_get_steps_by_type( $attr['course_id'], $attr['post_type'] );
		if ( ! empty( $course_steps ) ) {
			$attr['post__in'] = $course_steps;
		}

		if ( LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Builder', 'shared_steps' ) == 'yes' ) {
			if ( ! isset( $attr['order'] ) ) {
				$attr['order'] = 'ASC';
			}
			if ( ! isset( $attr['orderby'] ) ) {
				$attr['orderby'] = 'post__in';
			}
		}
	}

	return ldvc_coures_list( $attr );

}
