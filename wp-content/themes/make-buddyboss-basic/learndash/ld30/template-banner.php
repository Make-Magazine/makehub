<?php
$course_cover_photo = false;

$course     = get_post( $course_id );
$has_access = sfwd_lms_has_access( $course_id, get_current_user_id() );
$lessons    = learndash_get_lesson_list( $course_id );
?>
<div class="mk-vw-container mk-learndash-banner">

    <div class="mk-course-banner-info container mk-learndash-side-area">
        <div class="flex flex-wrap">
            <div class="mk-course-banner-inner">
				<?php
				/*if ( taxonomy_exists( 'ld_course_category' ) ) {
					//category
					$course_cats = get_the_terms( $course->ID, 'ld_course_category' );
					if ( ! empty( $course_cats ) ) { ?>
                        <div class="mk-course-category">
							<?php foreach ( $course_cats as $course_cat ) { ?>
                                <span class="course-category-item"><a title="<?php echo $course_cat->name; ?>"
                                                                      href="<?php echo home_url() ?>/courses/?search=&filter-categories=<?php echo $course_cat->slug; ?>"><?php echo $course_cat->name; ?></a><span>,</span></span>
							<?php } ?>
                        </div>
					<?php }
				}*/
				?>
                <h1 class="entry-title"><?php echo get_the_title( $course_id ); ?></h1>

				<?php if ( has_excerpt( $course_id ) ) { ?>
                    <div class="mk-course-excerpt">
						<?php echo get_the_excerpt( $course_id ); ?>
                    </div>
				<?php } ?>

                <div class="mk-course-points">
                    <a class="anchor-course-points" href="#learndash-course-content">
						<?php echo sprintf( esc_html_x('View %s details', 'link: View Course details', 'buddyboss-theme'), LearnDash_Custom_Label::get_label( 'course' ) );?>
                        <i class="fa fa-caret-down"></i>
                    </a>
                </div>

				<?php
					$bb_single_meta_pfx = 'bb_single_meta_off';
				?>

                <div class="mk-course-single-meta flex align-items-center <?php echo $bb_single_meta_pfx; ?>">
					<?php if ( class_exists( 'BuddyPress' ) ) { ?>
                    	<a href="<?php echo bp_core_get_user_domain( $course->post_author ); ?>">
					<?php } else { ?>
                        <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID', $course->post_author ), get_the_author_meta( 'user_nicename', $course->post_author ) ); ?>">
					<?php } ?>
							<?php echo get_avatar( get_the_author_meta( 'email', $course->post_author ), 80 ); ?>
							<span class="author-name"><?php the_author(); ?></span>
                        </a>

                </div>

            </div>
        </div>
    </div>
</div>
