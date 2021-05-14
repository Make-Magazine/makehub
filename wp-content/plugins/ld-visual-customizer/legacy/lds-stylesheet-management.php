<?php
function lds_build_stylesheet() {
	update_option( 'ldvc_add_method' , 'inline' );
}

function lds_switch_stylesheet_method() {

	echo 'Could not open file!';

	// Switch the method from generated to inline
	update_option( 'ldvc_add_method' , 'inline' );

}

function lds_generate_styles() {

	ob_start();

	$lds_heading_bg 			= get_option('lds_heading_bg');
	$lds_heading_txt 			= get_option('lds_heading_txt');
	$lds_row_bg 				= get_option('lds_row_bg');
	$lds_row_bg_alt 			= get_option('lds_row_bg_alt');
	$lds_row_txt 				= get_option('lds_row_txt');

	$lds_sub_row_bg 			= get_option('lds_sub_row_bg');
	$lds_sub_row_bg_alt 		= get_option('lds_sub_row_bg_alt');
	$lds_sub_row_txt 			= get_option('lds_sub_row_txt');

	$lds_button_bg 				= get_option('lds_button_bg');
	$lds_button_txt 			= get_option('lds_button_txt');
	$lds_complete_button_bg 	= get_option('lds_complete_button_bg');
	$lds_complete_button_txt 	= get_option('lds_complete_button_txt');

	$lds_progress 				= get_option('lds_progress');
	$lds_links 					= get_option('lds_links');
	$lds_complete 				= get_option('lds_complete');

	$lds_widget_bg 				= get_option('lds_widget_bg');
	$lds_widget_header_bg 		= get_option('lds_widget_header_bg');
	$lds_widget_header_txt 		= get_option('lds_widget_header_txt');
	$lds_widget_txt 			= get_option('lds_widget_txt');

	$lds_checkbox_incomplete 	= get_option('lds_checkbox_incomplete');
	$lds_checkbox_complete 		= get_option('lds_checkbox_complete');

	$lds_arrow_incomplete 		= get_option('lds_arrow_incomplete');
	$lds_arrow_complete 		= get_option('lds_arrow_complete');

	$lds_border_radius 			= get_option('lds_button_border_radius');

	$lds_icon_style 			= get_option('lds_icon_style');

	$lds_open_css 				= get_option('lds_open_css');

	$widget_title				= get_option( 'lds_widget_title' , '.widget-title');

	$lds_table_heading_font_size 	=	get_option( 'lds_table_heading_font_size' );
	$lds_table_row_font_size		=	get_option( 'lds_table_row_font_size' );
	$lds_table_sub_row_font_size	=	get_option( 'lds_table_sub_row_font_size' );
	$lds_widget_heading_font_size	=	get_option( 'lds_widget_heading_font_size' );
	$lds_widget_text_font_size		=	get_option( 'lds_widget_text_font_size' );

	$fontawesome_ver				= 	get_option( 'lds_fontawesome_ver' );

	if( get_option('lds_skin') != 'default' ):
		echo file_get_contents( LDS_PATH . '/legacy/assets/css/ldvc-base.css' );
	endif;

	$css	= '';
	$skins 	= array(
		'modern'	=>	'modern.css',
		'rustic'	=>	'rustic.css',
		'classic'	=>	'classic.css',
		'playful'	=>	'playful.css',
		'upscale'	=>	'upscale.css'
	);

	foreach( $skins as $slug => $file ) {
		if( get_option('lds_skin') == $slug ) {
			$css = file_get_contents( LDS_PATH . '/legacy/assets/css/' . $file );
		}
	}

	$css = str_replace( '.widget-title' , $widget_title , $css );

	echo $css;

	if(get_option('lds_animation') == 'yes') {
		echo file_get_contents( LDS_PATH . '/legacy/assets/css/animation.css' );
	}

	// ========================
	// = Custom Border Radius =
	// ========================

	if( !empty( $lds_border_radius ) ) { ?>

		/* CUSTOM BORDER RADIUS */

		.wpProQuiz_button,
		.wpProQuiz_button:hover,
		#uploadfile_btn,
		input.wpProQuiz_button2,
		input.wpProQuiz_button,
		input.wpProQuiz_button2:hover,
		input.wpProQuiz_button:hover,
		.btn-join,
		#btn-join,
		a#quiz_continue_link,
		#learndash_mark_complete_button,
		#learndash_next_prev_link a,
		p.wpProQuiz_certificate a,
		#lds-shortcode .lds-button,
		#lds-shortcode input[type="submit"],
		#lds-shortcode .btn,
		#lds-shortcode .btn-primary {
			border-radius: <?php echo $lds_border_radius; ?>px !important;
		}


	<?php }

	// ================
	// = Custom Icons =
	// ================

	if( ( !empty( $lds_icon_style ) ) && ( $lds_icon_style != 'default' ) ) {

		echo '/* ICON STYLE IS '. $lds_icon_style .' */';

		$styles = apply_filters( 'lds_custom_icons', array(
			'modern',
			'minimal',
			'chunky',
			'playful',
			'circles'
		) );

		foreach($styles as $style) {

			if($lds_icon_style == $style) {

				if( $fontawesome_ver != 4 ) {
					echo file_get_contents( LDS_PATH . '/legacy/assets/css/icons/v5/' . $style . '.css' );
					echo file_get_contents( LDS_PATH . '/legacy/assets/css/icons/v5/reset.css' );

				} else {
					echo file_get_contents( LDS_PATH . '/legacy/assets/css/icons/reset.css' );
					echo file_get_contents( LDS_PATH . '/legacy/assets/css/icons/' . $style . '.css' );
				}

			}

		}

	} ?>

	/*
	 * Global Styling for all styles
	 *
	 *
	 */

	 .widget_ldcourseprogress ul,
	 .widget_sfwd-lessons-widget ul,
	 .widget_ldcoursenavigation ul,
	 .widget_ldcourseinfo ul,
	 .widget_sfwd-certificates-widget ul,
	 .widget_sfwd-courses-widget ul {
	 	list-style: none;
		margin: 0;
		padding: 0 15px;
	 }

	 #ld_course_info,
	 #course_navigation {
	 	padding: 0 15px 15px;
	 }

	 .widget_ldcourseprogress *,
	  .widget_sfwd-lessons-widget *,
	  .widget_ldcoursenavigation *,
	  .widget_ldcourseinfo *,
	  .widget_sfwd-certificates-widget *,
	  .widget_sfwd-courses-widget * {
		font-size: <?php echo $lds_widget_text_font_size; ?>px;
	 }

	 .widget_ldcourseprogress .widget-title,
 	.widget_sfwd-lessons-widget .widget-title,
 	.widget_ldcoursenavigation .widget-title,
 	.widget_ldcourseinfo .widget-title,
 	.widget_sfwd-certificates-widget .widget-title,
 	.widget_sfwd-courses-widget .widget-title {
		font-size: <?php echo $lds_widget_heading_font_size; ?>px !important;
	}

	/*
	 * Table Heading Backgrounds
	 *
	 */

	#lesson_heading,
	#quiz_heading,
	#learndash_lessons #lesson_heading,
	#learndash_profile .learndash_profile_heading,
	#learndash_quizzes #quiz_heading,
	#learndash_lesson_topics_list div > strong,
	table.notes-listing th,
	#learndash_enhanced_course_header {
		background-color:<?php echo $lds_heading_bg; ?> !important;
	}

	/*
	 * Heading Text
	 *
	 */

	#lds-shortcode .lds-course-item-icon i.fa,
	#lds-shortcode .course-item-icon a,
	#lds-shortcode .lds-entry-title a,
	#lesson_heading span,
	#quiz_heading span,
	#learndash_lesson_topics_list div > strong,
	.learndash_profile_heading span,
	.learndash_profile_heading,
	table.notes-listing th,
	#learndash_enhanced_course_header h2,
	#learndash_enhanced_course_header li {
		color: <?php echo $lds_heading_txt; ?> !important ;
	}

	/*
     * Custom LearnDash element widgets
	 */

	#learndash_course_materials {
		background-color:<?php echo $lds_heading_bg; ?> !important;
	}

	#lds-shortcode .m-lds-grid-title,
	#lds-shortcode .m-lds-grid-title a,
	#lds-shortcode .m-lds-toggle,
	#lds-shortcode .m-lds-topic-item a,
	#learndash_course_materials h1,
 	#learndash_course_materials h2,
 	#learndash_course_materials h3,
 	#learndash_course_materials h4,
 	#learndash_course_materials h5,
 	#learndash_course_materials li,
 	#learndash_course_materials td,
 	#learndash_course_materials p {
 	    position: relative;
 	    z-index: 5;
		color: <?php echo $lds_heading_txt; ?> !important;
 	}

	/*
	 * Table Cells
	 *
	 */
	#learndash_profile .profile_info,
	#lessons_list > div,
	#quiz_list > div,
	#learndash_profile .course_progress,
	#learndash_profile #course_list > div,
	#learndash_lesson_topics_list ul > li,
	#learndash_profile .profile_info a,
	#learndash_profile #course_list a,
	#learndash_profile #course_list a span,
	#learndash_lessons a {
	    background-color: <?php echo $lds_row_bg; ?>;
		color: <?php echo $lds_row_txt; ?>
	}

	#lessons_list > div:nth-child(odd),
	#quiz_list > div:nth-child(odd),
	#learndash_lesson_topics_list ul > li.nth-of-type-odd
	{
	    background: <?php echo $lds_row_bg_alt; ?>;
		color: <?php echo $lds_row_txt; ?>
	}

	#learndash_lesson_topics_list a,
	#lds-shortcode .lds-expanded-course-lesson-list li a,
	#lessons_list h4 a,
	#quiz_list h4 a {
	    color: <?php echo $lds_row_txt; ?> !important;
	}

	#learndash_lesson_topics_list a span {
		color: <?php echo $lds_row_txt; ?> !important;
	}

	/*
	 * Buttons
	 *
	 */

	/* Complete Button */

	#sfwd-mark-complete input.button,
	#sfwd-mark-complete input[type="submit"],
	#learndash_mark_complete_button,
	p.wpProQuiz_certificate a,
	p.wpProQuiz_certificate a.btn-blue,
	a#quiz_continue_link,
	#learndash_course_certificate a.btn-blue {
		background-color: <?php echo $lds_complete_button_bg; ?> !important;
		color: <?php echo $lds_complete_button_txt; ?> !important;
	}

	/* Standard Button */

	#lds-shortcode .lds-button,
	.btn-join,
	#btn-join,
	.wpProQuiz_button,
	#uploadfile_btn,
	#learndash_next_prev_link a {
		background-color: <?php echo $lds_button_bg; ?> !important;
		color: <?php echo $lds_button_txt; ?> !important;
	}


	/*
	 * Visual Elements
	 *
	 */

	.course_progress {
		max-width: 90%;
		margin-left: auto !important;
		margin-right: auto !important;
		display: block;
	}

	.lds-progress-bar span,
	dd.course_progress div.course_progress_blue {
		background-color: <?php echo $lds_progress; ?> !important;
	}


	/*
	 * Links
	 *
	 */

	.widget_ldcourseprogress a,
	.widget_sfwd-lessons-widget a,
	.widget_ldcoursenavigation a,
	.widget_ldcourseinfo a,
	.widget_sfwd-certificates-widget a,
	.widget_sfwd-courses-widget a {
		color: <?php echo $lds_links; ?> !important;
	}




	/*
	 * Widgets
	 *
	 */

	.widget_ldcourseprogress,
	#sfwd-certificates-widget-2,
	#sfwd-courses-widget-2,
	#ldcourseinfo-2,
	.widget_sfwd-lessons-widget,
	.widget_ldcoursenavigation,
	.widget_ldcourseinfo,
	.widget_sfwd-certificates-widget,
	.widget_sfwd-courses-widget,
	#lds-shortcode .lds-dialog {
		background-color: <?php echo $lds_widget_bg; ?> !important;
		color: <?php echo $lds_widget_txt; ?> !important;
	}

	#lds-shortcode .lds-dialog input[type="submit"],
	#lds-shortcode .lds-dialog .btn {
		background-color: <?php echo $lds_button_bg; ?>;
		color: <?php echo $lds_button_txt; ?>
	}

	#learndash_course_content .learndash_topic_dots ul > li:nth-of-type(2n+1) {
		background: <?php echo $lds_sub_row_bg; ?>;
	}

	#learndash_course_content .learndash_topic_dots ul > li {
		background: <?php echo $lds_sub_row_bg_alt; ?>;
	}

	#learndash_course_content .learndash_topic_dots ul > li a span {
		color: <?php echo $lds_sub_row_txt; ?> !important;
	}

	#learndash_course_content .learndash_topic_dots ul > li:hover {
		background: <?php echo $lds_sub_row_bg; ?>
	}

	#learndash_course_content .learndash_topic_dots ul > li:nth-of-type(2n+1) {
		background: <?php echo $lds_sub_row_bg_alt; ?>;
	}

	.widget_ldcourseprogress <?php echo $widget_title; ?>,
	.widget_sfwd-lessons-widget <?php echo $widget_title; ?>,
	.widget_ldcoursenavigation <?php echo $widget_title; ?>,
	.widget_ldcourseinfo <?php echo $widget_title; ?>,
	.widget_sfwd-certificates-widget <?php echo $widget_title; ?>,
	.widget_sfwd-courses-widget <?php echo $widget_title; ?>,
	#lds-shortcode .lds-dialog hgroup.lds-dialog-title {
		background-color: <?php echo $lds_widget_header_bg; ?> !important;
		<?php if( !empty( $lds_widget_heading_font_size ) ): ?>
			font-size: <?php echo $lds_widget_heading_font_size; ?>px;
		<?php endif; ?>
	}

	#lds-shortcode .lds-dialog hgroup.lds-dialog-title h2 {
		<?php if( !empty($lds_widget_header_txt) ): ?>
		color: <?php echo $lds_widget_header_txt; ?> !important;
		<?php
		endif;
		if( !empty( $lds_widget_heading_font_size ) ): ?>
			font-size: <?php echo $lds_widget_heading_font_size; ?>px !important;
		<?php endif; ?>
	}

	<?php
	if( !empty($lds_widget_text_font_size) ): ?>
		.widget_ldcourseprogress,
		.widget_ldcourseprogress li,
		.widget_ldcourseprogress p,
		.widget_sfwd-lessons-widget,
		.widget_sfwd-lessons-widget li,
		.widget_sfwd-lessons-widget p,
		.widget_ldcoursenavigation,
		.widget_ldcoursenavigation li,
		.widget_ldcoursenavigation p,
		.widget_ldcourseinfo,
		.widget_ldcourseinfo li,
		.widget_ldcourseinfo p,
		.widget_sfwd-certificates-widget,
		.widget_sfwd-certificates-widget li,
		.widget_sfwd-certificates-widget p,
		.widget_sfwd-courses-widget,
		.widget_sfwd-courses-widget li,
		.widget_sfwd-courses-widget p {
			font-size: <?php echo $lds_widget_text_font_size; ?>px;
		}
	<?php endif; ?>

	.widget_ldcourseprogress <?php echo $widget_title; ?>,
	.widget_sfwd-lessons-widget <?php echo $widget_title; ?>,
	.widget_ldcoursenavigation <?php echo $widget_title; ?>,
	.widget_ldcourseinfo <?php echo $widget_title; ?>,
	.widget_sfwd-certificates-widget <?php echo $widget_title; ?>,
	.widget_sfwd-courses-widget <?php echo $widget_title; ?> {
		color: <?php echo $lds_widget_header_txt; ?> !important;
	}

	#course_navigation a,
	.widget_sfwd-lessons-widget ul li a {
		color: <?php echo $lds_links; ?> !important;
	}

	#lds-shortcode .lds-expanded-course-item .lds-expanded-course-lesson-list ul li a.lds-content-status-completed span.lds-ec-icon {
		background: <?php echo $lds_checkbox_complete; ?>;
	}
	.lds-status-completed,
	a.completed::before,
	.learndash_profile_quizzes .passed_icon:before,
	.learndash .completed:after,
	#learndash_profile .completed:after,
	.learndash .topic-completed span:after,
	.learndash_navigation_lesson_topics_list .list_arrow.collapse.lesson_completed:before,
	.learndash_nevigation_lesson_topics_list .list_arrow.collapse.lesson_completed:before,
	.learndash a.completed::after, #learndash_profile a.completed:after,
	#learndash_profile .list_arrow.collapse:before,
	#learndash_profile .list_arrow.expand:before,
	.learndash .topic-completed span::before,
	.learndash_profile_quizzes .passed_icon
	 {
		color: <?php echo $lds_checkbox_complete; ?>
	}

	.list_arrow.expand.lesson_completed:before,
	.learndash_navigation_lesson_topics_list .topic-completed:before,
	.learndash_nevigation_lesson_topics_list .topic-completed:before,
	.list_arrow.expand.lesson_completed:before {
		color: <?php echo $lds_checkbox_complete; ?>
	}

	/**
	 * Incomplete icons
	 */

	 #lds-shortcode .lds-expanded-course-item .lds-expanded-course-lesson-list ul li a.lds-content-status-notcompleted span.lds-ec-icon {
 		background: <?php echo $lds_checkbox_incomplete; ?>;
 	}

	.lds-status-notcompleted,
	.learndash .notcompleted:after,
	#learndash_profile .notcompleted:after,
	#leardash_profile .topic-notcompleted span:before,
	.learndash .topic-notcompleted span:before,
	.learndash .notcompleted:before,
	#learndash_profile .notcompleted:before,
	#leardash_profile .topic-notcompleted span:before,
	.learndash .topic-notcompleted span:before,
	.learndash_navigation_lesson_topics_list .topic-notcompleted:before,
	.learndash_nevigation_lesson_topics_list .topic-notcompleted:before,
	.learndash_profile_quizzes .failed_icon,
	.learndash .topic-notcompleted span:after {
		color: <?php echo $lds_checkbox_incomplete; ?>;
	}

	.learndash_navigation_lesson_topics_list .list_arrow.collapse:before,
	.learndash_nevigation_lesson_topics_list .list_arrow.collapse:before,
	.lesson_incomplete.list_arrow.expand::before
	{
		color: <?php echo $lds_arrow_incomplete; ?>
	}

	#learndash_profile .profile_info .profile_avatar img {
		border-color: <?php echo $lds_button_bg; ?>
	}

	<?php if( ( $lds_table_heading_font_size != 'default' ) && ( !empty( $lds_table_heading_font_size ) ) ) { ?>

		#learndash_lesson_topics_list .learndash_topic_dots strong,
		.learndash_profile_heading span,
		.learndash_profile_heading span,
		.learndash_profile_heading,
		#quiz_heading span,
		#lds-shortcode .lds-entry-title a,
		#lesson_heading span,
		#learndash_enhanced_course_header h2 {
			font-size: <?php echo $lds_table_heading_font_size ?>px !important;
		}

	<?php } ?>

	<?php if( ( $lds_table_row_font_size != 'default' ) && ( !empty( $lds_table_row_font_size ) ) ) { ?>

		#quiz_list h4 a,
		#learndash_lessons h4 a,
		#lds-shortcode .lds-expanded-course-lesson-list li a,
		#learndash_profile h4 a {
			font-size: <?php echo $lds_table_row_font_size ?>px;
		}

	<?php } ?>

	<?php if( ( $lds_table_sub_row_font_size != 'default' ) && ( !empty( $lds_table_sub_row_font_size ) ) ) { ?>

		.learndash_topic_dots a,
		.learndash_topic_dots a > span,
		#learndash_lesson_topics_list span a {
			font-size: <?php echo $lds_table_sub_row_font_size ?>px;
		}

	<?php } ?>

	<?php if( get_option('lds_quiz_bg') || get_option('lds_quiz_txt') || get_option('lds_quiz_border_color') ) { ?>
		.wpProQuiz_questionList,
		.wpProQuiz_mextrixTr > td,
		.wpProQuiz_questionListItem > table,
		.wpProQuiz_matrixSortString,
		.wpProQuiz_sortable,
		.wpProQuiz_sortStringItem,
		.wpProQuiz_reviewQuestion,
		.wpProQuiz_reviewQuestion li,
		.wpProQuiz_box,
		.wpProQuiz_tipp > div,
		.wpProQuiz_lock,
		.wpProQuiz_prerequisite,
		.wpProQuiz_startOnlyRegisteredUser,
		.wpProQuiz_loadQuiz,
		.wpProQuiz_toplistTable th,
		.wpProQuiz_addToplist,
		.wpProQuiz_reviewQuestion,
		.wpProQuiz_box,
		.wpProQuiz_reviewQuestion div,
		.wpProQuiz_catOverview span,
		.wpProQuiz_response {
			<?php if(get_option('lds_quiz_bg') ) echo 'background: ' . get_option('lds_quiz_bg') . ' !important;'; ?>
			<?php if(get_option('lds_quiz_txt') ) echo 'color: ' . get_option('lds_quiz_txt') . ' !important;'; ?>
			<?php if( get_option('lds_quiz_border_color') ) echo 'border-color: ' . get_option('lds_quiz_border_color') . ' !important'; ?>
			<?php if(get_option('lds_border_style') == 'light' ) echo 'border-color: rgba(255,255,255,.5) !important;'; ?>
			<?php if(get_option('lds_border_style') == 'dark' ) echo 'border-color: rgba(0,0,0,.25) !important;'; ?>
		}

		<?php if( get_option('lds_border_style') || get_option('lds_quiz_border_color') ): ?>
			.wpProQuiz_mextrixTr > td {
				<?php if(get_option('lds_border_style') == 'light' ) echo 'border-color: rgba(255,255,255,.5) !important;'; ?>
				<?php if(get_option('lds_border_style') == 'dark' ) echo 'border-color: rgba(0,0,0,.25) !important;'; ?>
				<?php if( get_option('lds_quiz_border_color') ) echo 'border-color: ' . get_option('lds_quiz_border_color') . ' !important'; ?>
			}
		<?php endif; ?>

	<?php } ?>

	<?php if( get_option('lds_quiz_correct_bg') ): ?>
		.wpProQuiz_correct {
			padding: 10px;
		}
		.wpProQuiz_correct,
		.wpProQuiz_answerCorrect {
			background: <?php echo get_option('lds_quiz_correct_bg'); ?> !important;
			<?php if( get_option('lds_quiz_correct_txt')): ?>
				color: <?php echo get_option('lds_quiz_correct_txt'); ?> !important;
			<?php endif; ?>
		}
	<?php endif; ?>

	<?php if( get_option('lds_quiz_incorrect_bg') || get_option('lds_quiz_incorrect_txt') ): ?>
		.wpProQuiz_incorrect {
			padding: 10px;
		}
		.wpProQuiz_incorrect,
		.wpProQuiz_answerIncorrect,
		.wpProQuiz_invalidate {
			background: <?php echo get_option('lds_quiz_incorrect_bg'); ?> !important;
			<?php if( get_option('lds_quiz_incorrect_txt')): ?>
				color: <?php echo get_option('lds_quiz_incorrect_txt'); ?> !important;
			<?php endif; ?>
		}
	<?php endif; ?>

	<?php echo $lds_open_css; ?>

	<?php
	return ob_get_clean();

}
