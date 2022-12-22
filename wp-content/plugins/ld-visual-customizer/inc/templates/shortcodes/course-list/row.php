<?php
$course_id   = $course->ID;
$is_enrolled = sfwd_lms_has_access( $course_id, $user_id );
$course      = get_post( $course_id);
$course_link = get_permalink( $course_id );

$progress = learndash_course_progress( array(
    'user_id'   => $user_id,
    'course_id' => $course_id,
    'array'     => true
) );

$status = ( $progress['percentage'] == 100 ) ? 'completed' : 'notcompleted';


if( $progress['percentage'] > 0 && $progress['percentage'] !== 100 ) {
    $status = 'progress';
}

$course_class = apply_filters( 'learndash-course-row-class',
                                'ld-item-list-item ld-item-list-item-course ld-expandable ' . ( $progress['percentage'] == 100 ? 'learndash-complete' : 'learndash-incomplete' ), $course, $user_id ); ?>

<div class="<?php echo esc_attr($course_class); ?>" id="<?php echo esc_attr( 'ld-course-list-item-' . $course_id ); ?>">

        <?php do_action( 'lds_course_list_item_name_before', $course_id, $style ); ?>

        <div class="lds-course-title">

            <a href="<?php echo esc_url( get_the_permalink($course_id) ); ?>" class="ld-item-name">
                <?php learndash_status_icon( $status, get_post_type(), null, true ); ?>
                <span class="ld-course-title"><?php echo esc_html( get_the_title($course_id) ); ?></span>
            </a> <!--/.ld-course-name-->

            <span class="lds-course-meta-price">
                <?php
                $course_pricing = learndash_get_course_price( $course_id );
                if( isset($course_pricing['price']) && !empty($course_pricing['price']) ):
                    if( $course_pricing['type'] !== 'closed' ):
                        echo wp_kses_post( '<span class="ld-currency">' . learndash_30_get_currency_symbol() . '</span>' );
                    endif;
                    echo wp_kses_post($course_pricing['price']);
                else:

                    $label = apply_filters( 'learndash_no_price_price_label', ( $course_pricing['type'] == 'closed' ? __( 'Closed', 'learndash' ) : __( 'Free', 'learndash' ) ) );

                    echo esc_html($label);

                endif;

                if( isset($course_pricing['type']) && $course_pricing['type'] == 'subscribe' ): ?>
                   <span class="ld-text ld-recurring-duration"><?php echo sprintf( esc_html_x( 'Every %s %s', 'Recurring duration message', 'learndash' ), $course_pricing['interval'], $course_pricing['frequency'] ); ?></span>
               <?php endif; ?>
            </span>

        </div>

        <div class="ld-item-details">
            <?php
            $certificateLink = learndash_get_course_certificate_link( $course->ID, $user_id );
            if ( !empty( $certificateLink ) ): ?>
                <a class="ld-certificate-link" target="_blank" href="<?php echo esc_attr($certificateLink); ?>" aria-label="<?php esc_attr_e( 'Certificate', 'learndash' ); ?>"><span class="ld-icon ld-icon-certificate"></span></span></a>
            <?php endif; ?>

            <?php
            if( $is_enrolled ): ?>

                <div class="ldvc-progress-steps"><?php echo sprintf( esc_html_x( '%1$d/%2$d Steps Completed', 'placeholder: completed steps, total steps', 'learndash' ), $progress['completed'], $progress['total'] ); ?></div>

                <?php
                echo wp_kses_post( learndash_status_bubble($status) );  ?>

            <?php else: ?>

                <div class="ld-status ldvc-status-not-enrolled"><?php esc_html_e( 'Not Enrolled', 'lds-skins' ); ?></div>

            <?php endif; ?>

        </div> <!--/.ld-course-details-->

</div> <!--/.ld-course-list-item-->
