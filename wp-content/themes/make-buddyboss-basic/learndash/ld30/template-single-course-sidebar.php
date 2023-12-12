<?php
global $wpdb;
$is_enrolled         = false;
$current_user_id     = get_current_user_id();
$course_price        = learndash_get_course_meta_setting( $course_id, 'course_price' );
$course_price_type   = learndash_get_course_meta_setting( $course_id, 'course_price_type' );
$course_button_url   = learndash_get_course_meta_setting( $course_id, 'custom_button_url' );
$paypal_settings     = LearnDash_Settings_Section::get_section_settings_all( 'LearnDash_Settings_Section_PayPal' );
$course_video_embed  = get_post_meta( $course_id, '_buddyboss_lms_course_video', true );
$course_certificate  = learndash_get_course_meta_setting( $course_id, 'certificate' );
$courses_progress    = learndash_get_course_progress( $current_user_id );
$course_progress     = isset( $courses_progress[ $course_id ] ) ? $courses_progress[ $course_id ] : null;
$admin_enrolled      = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_Admin_User', 'courses_autoenroll_admin_users' );
$lesson_count        = learndash_get_course_lessons_list( $course_id, null, array( 'num' => - 1 ) );
$lesson_count        = array_column( $lesson_count, 'post' );
$course_pricing      = learndash_get_course_price( $course_id );
$has_access          = sfwd_lms_has_access( $course_id, $current_user_id );
$file_info           = pathinfo( $course_video_embed );


if ( '' != $course_video_embed ) {
	$thumb_mode = 'thumbnail-container-vid';
} else {
	$thumb_mode = 'thumbnail-container-img';
}

if ( sfwd_lms_has_access( $course->ID, $current_user_id ) ) {
	$is_enrolled = true;
} else {
	$is_enrolled = false;
}
?>

<div class="mk-single-course-sidebar mk-preview-wrap">
    <div class="mk-ld-sticky-sidebar">
        <div class="widget mk-enroll-widget">
            <div class="mk-enroll-widget flex-1 push-right">
                <div class="mk-course-preview-wrap mk-thumbnail-preview">
                    <div class="mk-preview-course-link-wrap">
                        <div class="thumbnail-container <?php echo esc_attr( $thumb_mode ); ?>">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail();
							}
							?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mk-course-preview-content">

                <div class="mk-course-status-wrap">
					<?php
					do_action( 'learndash-course-infobar-status-cell-before', get_post_type(), $course_id, $current_user_id );
					$progress = learndash_course_progress( array(
						'user_id'   => $current_user_id,
						'course_id' => $course_id,
						'array'     => true,
					) );

					if( empty ( $progress ) ) {
						$progress = array (
							'percentage' =>  0,
							'completed'  =>  0,
							'total'      =>  0,
						);
					}

					$status = ( $progress['percentage'] == 100 ) ? 'completed' : 'notcompleted';

					if ( $progress['percentage'] > 0 && $progress['percentage'] !== 100 ) {
						$status = 'progress';
					}

					if ( is_user_logged_in() && isset( $has_access ) && $has_access ) { ?>

                        <div class="mk-course-status-content">
						<?php learndash_status_bubble( $status ); ?>
                        </div><?php

					} elseif ( $course_pricing['type'] !== 'open' ) {

						echo '<div class="mk-course-status-content">';
						echo '<div class="ld-status ld-status-incomplete ld-third-background">Not Enrolled</div>';
						echo '</div>';

					}
					do_action( 'learndash-course-infobar-status-cell-after', get_post_type(), $course_id, $current_user_id ); ?>
                </div>

                <div class="mk-button-wrap">
					<?php

					$login_model = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Theme_LD30', 'login_mode_enabled' );
					$login_url   = apply_filters( 'learndash_login_url', ( $login_model === 'yes' ? '#login' : wp_login_url( get_the_permalink( $course_id ) ) ) );

					if ( $course_price_type == 'open' || $course_price_type == 'free' ) {
						if ( apply_filters( 'learndash_login_modal', true, $course_id, $current_user_id ) && ! is_user_logged_in() && $course_price_type != 'open' ):
							?>
                        <div class="learndash_join_button">
                            <a href="<?php echo esc_url( $login_url ); ?>"
                               class="btn-advance ld-primary-background">Login to Enroll</a>
                            </div><?php
						else:
							if ( $course_price_type == 'free' && false === $is_enrolled ) {
								$button_text = LearnDash_Custom_Label::get_label( 'button_take_this_course' );
								?>
                            <div class="learndash_join_button">
                                <form method="post">
                                    <input type="hidden" value="<?php echo $course_id; ?>" name="course_id"/>
                                    <input type="hidden" name="course_join"
                                           value="<?php echo wp_create_nonce( 'course_join_' . get_current_user_id() . '_' . $course_id ); ?>"/>
                                    <input type="submit" value="<?php echo $button_text; ?>" class="btn-join"
                                           id="btn-join"/>
                                </form>
                                </div><?php
							} else {
								?>
                                <div class="learndash_join_button">
									<?php echo do_shortcode('[ld_course_resume]'); ?>
                                </div>
								<?php
							}
						endif;

						if ( $course_price_type == 'open' ) {
							?>
                            <span class="mk-course-type mk-course-type-open">Open Registration</span><?php
						} else {
							?>
                            <span class="mk-course-type mk-course-type-free">Free</span><?php
						}
					} elseif ( $course_price_type == 'closed' ) {
						$learndash_payment_buttons = learndash_payment_buttons( $course );
						if ( empty( $learndash_payment_buttons ) ):
							if ( false === $is_enrolled ) {
								echo '<span class="ld-status ld-status-incomplete ld-third-background ld-text">This course is currently closed</span>';
								if ( ! empty( $course_price ) ) {
									echo '<span class="mk-course-type mk-course-type-paynow"><span class="ld-currency">' . wp_kses_post( function_exists( 'learndash_get_currency_symbol' ) ? learndash_get_currency_symbol() : learndash_30_get_currency_symbol() ) . '</span> ' . wp_kses_post( $course_pricing['price'] ) . '</span>';
								}
							} else { ?>
                                <div class="learndash_join_button">
									<?php echo do_shortcode('[ld_course_resume]'); ?>
                                </div>
								<?php
							}
						else:
							?>
                            <div class="learndash_join_button <?php echo 'btn-advance-continue '; ?>"> <?php
								echo $learndash_payment_buttons; ?>
                            </div>
							<?php
							if ( ! empty( $course_price ) ) {
								echo '<span class="mk-course-type mk-course-type-paynow"><span class="ld-currency">' . wp_kses_post( function_exists( 'learndash_get_currency_symbol' ) ? learndash_get_currency_symbol() : learndash_30_get_currency_symbol() ) . '</span> ' . wp_kses_post( $course_pricing['price'] ) . '</span>';
							}
						endif;
					} elseif ( $course_price_type == 'paynow' || $course_price_type == 'subscribe' ) {
						if ( false === $is_enrolled ) {
							$meta                = get_post_meta( $course_id, '_sfwd-courses', true );
							$course_price_type   = @$meta['sfwd-courses_course_price_type'];
							$course_price        = @$meta['sfwd-courses_course_price'];
							$course_no_of_cycles = @$meta['sfwd-courses_course_no_of_cycles'];
							$course_price        = @$meta['sfwd-courses_course_price'];
							$custom_button_url   = @$meta['sfwd-courses_custom_button_url'];
							$custom_button_label = @$meta['sfwd-courses_custom_button_label'];

							if ( $course_price_type == 'subscribe' && $course_price == '' ) {
								if ( empty( $custom_button_label ) ) {
									$button_text = LearnDash_Custom_Label::get_label( 'button_take_this_course' );
								} else {
									$button_text = esc_attr( $custom_button_label );
								}
								$join_button = '<div class="learndash_join_button"><form method="post">
								<input type="hidden" value="' . $course->ID . '" name="course_id" />
								<input type="hidden" name="course_join" value="' . wp_create_nonce( 'course_join_' . get_current_user_id() . '_' . $course->ID ) . '" />
								<input type="submit" value="' . $button_text . '" class="btn-join" id="btn-join" /></form></div>';
								echo $join_button;
							} else {
								echo learndash_payment_buttons( $course );
							}
						} else {
							?>
                        <div class="learndash_join_button">
							<?php echo do_shortcode('[ld_course_resume]'); ?>
                        </div><?php
						}

						if ( apply_filters( 'learndash_login_modal', true, $course_id, $user_id ) && ! is_user_logged_in() ):
							echo '<span class="ld-status">or<a class="ld-login-text" href="' . esc_attr( $login_url ) . '">Login</a></span>';
						endif;

						if ( false === $is_enrolled ) {
							if ( $course_price_type == 'paynow' ) {
								?><span class="mk-course-type mk-course-type-paynow">
								<?php
								echo wp_kses_post(
									'<span class="ld-currency">' . function_exists( 'learndash_get_currency_symbol' ) ?
										learndash_get_currency_symbol() : learndash_30_get_currency_symbol() . '</span> '
								);
								echo wp_kses_post( $course_pricing['price'] ); ?></span>
								<?php
							} else {
								$course_price_billing_p3 = get_post_meta( $course_id, 'course_price_billing_p3', true );
								$course_price_billing_t3 = get_post_meta( $course_id, 'course_price_billing_t3', true );
								if ( $course_price_billing_t3 == 'D' ) {
									$course_price_billing_t3 = 'day(s)';
								} elseif ( $course_price_billing_t3 == 'W' ) {
									$course_price_billing_t3 = 'week(s)';
								} elseif ( $course_price_billing_t3 == 'M' ) {
									$course_price_billing_t3 = 'month(s)';
								} elseif ( $course_price_billing_t3 == 'Y' ) {
									$course_price_billing_t3 = 'year(s)';
								}
								?>
                                <span class="mk-course-type mk-course-type-subscribe">
								<?php
								if ( '' === $course_price && $course_price_type == 'subscribe' ) {
									?>
                                    <span class="mk-course-type mk-course-type-subscribe">Free</span>
									<?php
								} else {
									echo wp_kses_post( '<span class="ld-currency">' . function_exists( 'learndash_get_currency_symbol' ) ?
										learndash_get_currency_symbol() : learndash_30_get_currency_symbol() . '</span> ' );
									echo wp_kses_post( $course_pricing['price'] );
								}

								$recuring = ( '' === $course_price_billing_p3 ) ? 0 : $course_price_billing_p3;

								//if ( !empty( $course_price_billing_p3 ) ) { ?>
                                    <span class="course-bill-cycle"> / <?php echo $recuring . ' ' . $course_price_billing_t3; ?> </span><?php
									//} ?>
							</span>
								<?php
							}
						}
					} ?>
                </div>

				<?php
				$topics_count = 0;
				foreach ( $lesson_count as $lesson ) {
					$lesson_topics = learndash_get_topic_list( $lesson->ID );
					if ( $lesson_topics ) {
						$topics_count += sizeof( $lesson_topics );
					}
				}

				//course quizzes.
				$course_quizzes       = learndash_get_course_quiz_list( $course_id );
				$course_quizzes_count = sizeof( $course_quizzes );

				//lessons quizzes.
				if ( is_array( $lesson_count ) || is_object( $lesson_count ) ) {
					foreach ( $lesson_count as $lesson ) {
						$quizzes       = learndash_get_lesson_quiz_list( $lesson->ID, null, $course_id );
						$lesson_topics = learndash_topic_dots( $lesson->ID, false, 'array', null, $course_id );
						if ( $quizzes && ! empty( $quizzes ) ) {
							$course_quizzes_count += count( $quizzes );
						}
						if ( $lesson_topics && ! empty( $lesson_topics ) ) {
							foreach ( $lesson_topics as $topic ) {
								$quizzes = learndash_get_lesson_quiz_list( $topic, null, $course_id );
								if ( ! $quizzes || empty( $quizzes ) ) {
									continue;
								}
								$course_quizzes_count += count( $quizzes );
							}
						}
					}
				}

				if ( sizeof( $lesson_count ) > 0 || $topics_count > 0 || $course_quizzes_count > 0 || $course_certificate ) { 
				$course_label = LearnDash_Custom_Label::get_label( 'course' ); ?>
                    <div class="mk-course-volume">
                    <h4><?php echo sprintf( esc_html__( '%s Includes', 'onecommunity' ), $course_label ); ?></h4>
                    <ul class="mk-course-volume-list">
						<?php if ( sizeof( $lesson_count ) > 0 ) { ?>
                            <li>
                                <i class="fa fa-book"></i><?php echo sizeof( $lesson_count ); ?> <?php echo sizeof( $lesson_count ) > 1 ? LearnDash_Custom_Label::get_label( 'lessons' ) : LearnDash_Custom_Label::get_label( 'lesson' ); ?>
                            </li>
						<?php } ?>
                    </ul>
                    </div><?php
				} ?>
            </div>
		</div>
		<?php
        if ( is_active_sidebar( 'learndash_course_sidebar' ) ) { ?>
			<ul class="ld-sidebar-widgets">
				<?php dynamic_sidebar( 'learndash_course_sidebar' ); ?>
			</ul>
		<?php
		}
        ?>
    </div>
</div>

<div class="mk-modal bb_course_video_details mfp-hide">
	<?php
	if ( $course_video_embed !== '' ) :
		if ( wp_oembed_get( $course_video_embed ) ) : ?><?php echo wp_oembed_get( $course_video_embed ); ?><?php elseif ( isset( $file_info['extension'] ) && $file_info['extension'] === 'mp4' ) : ?>
            <video width="100%" controls>
                <source src="<?php echo $course_video_embed; ?>" type="video/mp4">
				<?php _e( 'Your browser does not support HTML5 video.', 'onecommunity' ); ?>
            </video>
		<?php
		else :
			_e( 'Video format is not supported, use Youtube video or MP4 format.', 'onecommunity' );
		endif;
	endif; ?>
</div>
