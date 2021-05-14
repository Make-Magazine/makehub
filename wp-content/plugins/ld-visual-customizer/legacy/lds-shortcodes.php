<?php
/**
 * lds-shortcodes.php
 *
 * Custom shortcodes for more advanced output
 */

add_shortcode( 'lds_lesson_list', 'lds_lesson_list_shortcode' );
function lds_lesson_list_shortcode( $attr ) {
	global $learndash_shortcode_used;
	$learndash_shortcode_used = true;

	ob_start();

	if( !isset( $attr['id'] ) ) return false;
	$lessons = learndash_get_course_lessons_list( $attr['id'] );

	lds_shortcodes_enqueue_scripts();

	include( ldvc_get_template_part('shortcodes/lds_lesson_list.php') );

	return ob_get_clean();

}

add_shortcode( 'lds_progress', 'lds_detailed_progress_shortcode' );
function lds_detailed_progress_shortcode( $atts ) {

	$progress = learndash_course_progress( array( 'course_id' => 0, 'user_id' => 0, 'array' => true ) );

    lds_shortcodes_enqueue_scripts();

	if( !$progress ) return;

    ob_start(); ?>

        <div class="lds-course-progress">
            <p class="lds-progress-bar"><span style='width: <?php echo esc_attr( $progress['percentage'] ); ?>%;'><?php if( $progress['percentage'] > 10 ): ?><b><?php echo esc_html( $progress['percentage'] ); ?>%</b><?php endif; ?></span> </p>
			<?php if( $progress['total'] != 0 ): ?>
				<p class="progress-meta"><?php echo sprintf( __( '<strong>%s out of %s steps</strong> completed', 'lds_skins' ), $progress['completed'], $progress['total'] ); ?></p>
			<?php endif; ?>
		</div>

    <?php
    return ob_get_clean();

}

add_shortcode( 'lds_course_list', 'lds_course_list_shortcode' );
function lds_course_list_shortcode( $attr ) {

 $shortcode_atts = shortcode_atts(

 		array(
 			'num' => '-1',
 			'post_type' => 'sfwd-courses',
 			'post_status' => 'publish',
 			'order' => 'DESC',
 			'orderby' => 'ID',
 			'mycourses' => false, 'meta_key' => '',
 			'meta_value' => '',
 			'meta_compare' => '',
 			'post__in'	=> null,
 			'tag' => '',
 			'tag_id' => 0, 'tag__and' => '',
 			'tag__in' => '',
 			'tag__not_in' => '',
 			'tag_slug__and' => '',
 			'tag_slug__in' => '',
 			'cat' => '',
 			'category_name' => 0, 'category__and' => '',
 			'category__in' => '',
 			'category__not_in' => '',
 			'categoryselector' => '',
 			'author__in' => '',
 			'col' => '',
            'cols'  =>  '2',
 			'array' => false,
            'style' =>  'icon'
 		),
 		$attr
 	);

 	extract( $shortcode_atts );

 	global $post;

 	$filter = array(
 		'post_type' => $post_type,
 		'post_status' => $post_status,
 		'posts_per_page' => $num,
 		'order' => $order,
 		'orderby' => $orderby
 	);

 	if ( ! empty( $author__in ) ) {
 		$filter['author__in'] = $author__in;
 	}

	if ( ! empty( $tag ) ) {
 		$filter['tax_query'] = array(
			array(
				'taxonomy'	=>	'ld_course_tag',
				'field'		=>	'slug',
				'terms'		=>	$tag
			)
		);
 	}

 	if ( ! empty( $tag_id ) ) {
 		$filter['tag_id'] = $tag;
 	}

 	if ( ! empty( $meta_key ) ) {
 		$filter['meta_key'] = $meta_key;
 	}

 	if ( ! empty( $meta_value ) ) {
 		$filter['meta_value'] = $meta_value;
 	}

 	if ( ! empty( $meta_compare ) ) {
 		$filter['meta_compare'] = $meta_compare;
 	}

 	if ( ! empty( $post__in ) ) {
 		$filter['post__in'] = $post__in;
 	}

 	if ( ! empty( $tag__and ) ) {
 		$filter['tag__and'] = explode( ',', $tag__and );
 	}

 	if ( ! empty( $tag__in ) ) {
 		$filter['tag__in'] = explode( ',', $tag__in );
 	}

 	if ( ! empty( $tag__not_in ) ) {
 		$filter['tag__not_in'] = explode( ',', $tag__not_in );
 	}

 	if ( ! empty( $tag_slug__and ) ) {
 		$filter['tag_slug__and'] = explode( ',', $tag_slug__and );
 	}

 	if ( ! empty( $tag_slug__in ) ) {
 		$filter['tag_slug__in'] = explode( ',', $tag_slug__in );
 	}

 	if ( ! empty( $cat ) ) {
		$filter['tax_query'] = array(
			array(
				'taxonomy'	=>	'ld_course_category',
				'field'		=>	'slug',
				'terms'		=>	$cat
			)
		);
 	}

 	if ( ! empty( $category_name ) ) {
 		$filter['category_name'] = $category_name;
 	}

 	if ( ! empty( $category__and ) ) {
 		$filter['category__and'] = explode( ',', $category__and );
 	}

 	if ( ! empty( $category__in ) ) {
 		$filter['category__in'] = explode( ',', $category__in );
 	}

 	if ( ! empty( $category__not_in ) ) {
 		$filter['category__not_in'] = explode( ',', $category__not_in );
 	}

 	if ( $array ) {
 		return get_posts( $filter );
 	}

 	if ( @$post->post_type == $post_type ) {
 		$filter['post__not_in'] = array( $post->ID );
 	}

 	$loop = new WP_Query( $filter );

 	$level = ob_get_level();

 	ob_start();

    $ld_categorydropdown = '';

if ( trim( $categoryselector ) == 'true' ) {
    $cats = array();
    $posts = get_posts( $filter );

    foreach( $posts as $post ) {
        $post_categories = wp_get_post_categories( $post->ID );

        foreach( $post_categories as $c ) {

            if ( empty( $cats[ $c ] ) ) {
                $cat = get_category( $c );
                $cats[ $c ] = array('id' => $cat->cat_ID, 'name' => $cat->name, 'slug' => $cat->slug, 'parent' => $cat->parent, 'count' => 0, 'posts' => array()); //stdClass Object ( [term_id] => 39 [name] => Category 2 [slug] => category-2 [term_group] => 0 [term_taxonomy_id] => 41 [taxonomy] => category [description] => [parent] => 0 [count] => 3 [object_id] => 656 [filter] => raw [cat_ID] => 39 [category_count] => 3 [category_description] => [cat_name] => Category 2 [category_nicename] => category-2 [category_parent] => 0 )

            }

            $cats[ $c ]['count']++;
            $cats[ $c ]['posts'][] = $post->ID;
        }

    }

    $categorydropdown = '<div id="ld_categorydropdown">';
    $categorydropdown.= '<form method="get">
            <label for="ld_categorydropdown_select">' . __( 'Categories', 'lds_skins' ) . '</label>
            <select id="ld_categorydropdown_select" name="catid" onChange="jQuery(\'#ld_categorydropdown form\').submit()">';
    $categorydropdown.= '<option value="">' . __( 'Select category', 'lds_skins' ) . '</option>';

    foreach( $cats as $cat ) {
        $selected =( empty( $_GET['catid'] ) || $_GET['catid'] != $cat['id'] ) ? '' : 'selected="selected"';
        $categorydropdown.= "<option value='" . $cat['id'] . "' " . $selected . '>' . $cat['name'] . ' (' . $cat['count'] . ')</option>';
    }

    $categorydropdown.= "</select><input type='submit' style='display:none'></form></div>";

    /**
     * Filter HTML output of category dropdown
     *
     * @since 2.1.0
     *
     * @param  string  $categorydropdown
     */
    echo apply_filters( 'ld_categorydropdown', $categorydropdown, $shortcode_atts, $filter );
}

// Setup variables
$i      = 0;
$class  = ( $cols == 2 ? 'lds-col-md-6' : 'lds-col-md-4' );

// Enqueue the necissary shortcode scripts
lds_shortcodes_enqueue_scripts(); ?>

<div id="lds-shortcode" class="lds-container-fluid lds-course-list-style-<?php echo esc_attr( $style ); ?>">
    <div class="lds-row">
        <?php
        while ( $loop->have_posts() ): $loop->the_post();

			global $post;

            if ( trim( $categoryselector ) == 'true' && ! empty( $_GET['catid'] ) && !in_array( get_the_ID(), (array)@$cats[ $_GET['catid']]['posts'] ) ) {
                continue;
            }

            if ( !$mycourses || sfwd_lms_has_access( get_the_ID() ) ):

                if( $i % $cols == 0 && $i > 1 ) echo '</div><div class="lds-row">'; ?>

                <div class="lds-course-item <?php echo esc_attr( $class ); ?>">

                    <div class="lds-course-item-icon">
                        <?php
						$icon = '';
                        if( has_post_thumbnail() ):
                            $image_class    = 'custom';
                            $size           = ( $style == 'icon' ? 'course_icon' : 'course_banner' );
                            $src            = wp_get_attachment_image_src( get_post_thumbnail_id(), $size );
                            $src            = $src[0];
                        else:
							$icon			=  ldvc_get_content_icon( get_the_ID() );
                            $image_class    = 'default';
                            $src            = LDS_URL . '/assets/img/shortcodes/course-placeholder.jpg';
                        endif; ?>

						<?php if( !$icon ): ?>
                        	<a href="<?php echo esc_url( learndash_get_step_permalink( get_the_ID(), $post->ID ) ); ?>" style="background-image: url('<?php echo esc_url( $src ); ?>');" class="<?php echo esc_attr( $image_class ); ?>"></a>
						<?php
						endif;
						if( $icon ): ?>
							<a href="<?php echo esc_url( learndash_get_step_permalink( get_the_ID(), $post->ID ) ); ?>" class="<?php echo esc_attr( $image_class ); ?>"><i class="fa <?php echo esc_attr($icon); ?>"></i></a>
						<?php endif; ?>
					</div>

                    <?php the_title( '<h3 class="lds-entry-title"><a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></h3>' ); ?>

					<?php if( get_post_meta( $post->ID, '_lds_duration', true ) ): ?>
						<p class="lds-course-meta"><i class="fa fa-clock-o"></i> <?php echo get_post_meta( $post->ID, '_lds_duration', true ); ?></p>
					<?php endif; ?>

                    <div class="lds-entry-content">
                    	<?php global $more; $more = 0; ?>
						<?php
						if( get_post_meta( $post->ID, '_lds_short_description' ) ):
							echo wpautop( get_post_meta( $post->ID, '_lds_short_description', true ) );
						endif; ?>
                    </div>

                    <p><a href="<?php echo esc_url( learndash_get_step_permalink( get_the_ID() ) ); ?>" class="lds-button lds-button-primary"><?php esc_html_e( 'Learn More', 'lds_skins' ); ?> <i class="fa fa-angle-right"></i></a></p>

                </div>

                <?php
            $i++; endif;
        endwhile; ?>
    </div>
</div>

    <?php
	$output = learndash_ob_get_clean( $level );
	wp_reset_query();

	$learndash_shortcode_used = true;

	/**
	 * Filter HTML output of category dropdown
	 *
	 * @since 2.1.0
	 *
	 * @param  string $output
	 */
	return apply_filters( 'ld_course_list', $output, $shortcode_atts, $filter );
}

add_shortcode( 'lds_expanded_course_list', 'lds_expanded_course_list_shortcode' );
function lds_expanded_course_list_shortcode( $attr ) {

 $shortcode_atts = shortcode_atts(

 		array(
 			'num' => '-1',
 			'post_type' => 'sfwd-courses',
 			'post_status' => 'publish',
 			'order' => 'DESC',
 			'orderby' => 'ID',
 			'mycourses' => false, 'meta_key' => '',
 			'meta_value' => '',
 			'meta_compare' => '',
 			'post__in'	=> null,
			'id'		=>	null,
 			'tag' => '',
 			'tag_id' => 0, 'tag__and' => '',
 			'tag__in' => '',
 			'tag__not_in' => '',
 			'tag_slug__and' => '',
 			'tag_slug__in' => '',
 			'cat' => '',
 			'category_name' => 0, 'category__and' => '',
 			'category__in' => '',
 			'category__not_in' => '',
 			'categoryselector' => '',
 			'author__in' => '',
 			'col' => '',
            'cols'  =>  '2',
 			'array' => false,
            'style' =>  'icon'
 		),
 		$attr
 	);

 	extract( $shortcode_atts );

 	global $post;

 	$filter = array(
 		'post_type' => $post_type,
 		'post_status' => $post_status,
 		'posts_per_page' => $num,
 		'order' => $order,
 		'orderby' => $orderby
 	);

 	if ( ! empty( $author__in ) ) {
 		$filter['author__in'] = $author__in;
 	}

 	if ( ! empty( $tag ) ) {
 		$filter['tax_query'] = array(
			array(
				'taxonomy'	=>	'ld_course_tag',
				'field'		=>	'slug',
				'terms'		=>	$tag
			)
		);
 	}

 	if ( ! empty( $tag_id ) ) {
 		$filter['tag_id'] = $tag;
 	}

 	if ( ! empty( $meta_key ) ) {
 		$filter['meta_key'] = $meta_key;
 	}

 	if ( ! empty( $meta_value ) ) {
 		$filter['meta_value'] = $meta_value;
 	}

 	if ( ! empty( $meta_compare ) ) {
 		$filter['meta_compare'] = $meta_compare;
 	}

 	if ( ! empty( $post__in ) ) {
 		$filter['post__in'] = $post__in;
 	}

	if( ! empty( $id ) ) {
		$filter['post__in'] = array( $id );
	}

 	if ( ! empty( $tag__and ) ) {
 		$filter['tag__and'] = explode( ',', $tag__and );
 	}

 	if ( ! empty( $tag__in ) ) {
 		$filter['tag__in'] = explode( ',', $tag__in );
 	}

 	if ( ! empty( $tag__not_in ) ) {
 		$filter['tag__not_in'] = explode( ',', $tag__not_in );
 	}

 	if ( ! empty( $tag_slug__and ) ) {
 		$filter['tag_slug__and'] = explode( ',', $tag_slug__and );
 	}

 	if ( ! empty( $tag_slug__in ) ) {
 		$filter['tag_slug__in'] = explode( ',', $tag_slug__in );
 	}

 	if ( ! empty( $cat ) ) {
		$filter['tax_query'] = array(
			array(
				'taxonomy'	=>	'ld_course_category',
				'field'		=>	'slug',
				'terms'		=>	$cat
			)
		);
 	}

 	if ( ! empty( $category_name ) ) {
 		$filter['category_name'] = $category_name;
 	}

 	if ( ! empty( $category__and ) ) {
 		$filter['category__and'] = explode( ',', $category__and );
 	}

 	if ( ! empty( $category__in ) ) {
 		$filter['category__in'] = explode( ',', $category__in );
 	}

 	if ( ! empty( $category__not_in ) ) {
 		$filter['category__not_in'] = explode( ',', $category__not_in );
 	}

 	if ( $array ) {
 		return get_posts( $filter );
 	}

 	if ( @$post->post_type == $post_type ) {
 		$filter['post__not_in'] = array( $post->ID );
 	}

 	$loop = new WP_Query( $filter );

 	$level = ob_get_level();

 	ob_start();

    $ld_categorydropdown = '';

if ( trim( $categoryselector ) == 'true' ) {
    $cats = array();
    $posts = get_posts( $filter );

    foreach( $posts as $post ) {
        $post_categories = wp_get_post_categories( $post->ID );

        foreach( $post_categories as $c ) {

            if ( empty( $cats[ $c ] ) ) {
                $cat = get_category( $c );
                $cats[ $c ] = array('id' => $cat->cat_ID, 'name' => $cat->name, 'slug' => $cat->slug, 'parent' => $cat->parent, 'count' => 0, 'posts' => array()); //stdClass Object ( [term_id] => 39 [name] => Category 2 [slug] => category-2 [term_group] => 0 [term_taxonomy_id] => 41 [taxonomy] => category [description] => [parent] => 0 [count] => 3 [object_id] => 656 [filter] => raw [cat_ID] => 39 [category_count] => 3 [category_description] => [cat_name] => Category 2 [category_nicename] => category-2 [category_parent] => 0 )

            }

            $cats[ $c ]['count']++;
            $cats[ $c ]['posts'][] = $post->ID;
        }

    }

    $categorydropdown = '<div id="ld_categorydropdown">';
    $categorydropdown.= '<form method="get">
            <label for="ld_categorydropdown_select">' . __( 'Categories', 'lds_skins' ) . '</label>
            <select id="ld_categorydropdown_select" name="catid" onChange="jQuery(\'#ld_categorydropdown form\').submit()">';
    $categorydropdown.= '<option value="">' . __( 'Select category', 'lds_skins' ) . '</option>';

    foreach( $cats as $cat ) {
        $selected =( empty( $_GET['catid'] ) || $_GET['catid'] != $cat['id'] ) ? '' : 'selected="selected"';
        $categorydropdown.= "<option value='" . $cat['id'] . "' " . $selected . '>' . $cat['name'] . ' (' . $cat['count'] . ')</option>';
    }

    $categorydropdown.= "</select><input type='submit' style='display:none'></form></div>";

    /**
     * Filter HTML output of category dropdown
     *
     * @since 2.1.0
     *
     * @param  string  $categorydropdown
     */
    echo apply_filters( 'ld_categorydropdown', $categorydropdown, $shortcode_atts, $filter );
}
// Enqueue the necissary shortcode scripts
lds_shortcodes_enqueue_scripts(); ?>

<div id="lds-shortcode" class="lds-container-fluid lds-course-list-style-expanded">

    <?php
    while ( $loop->have_posts() ): $loop->the_post();

        if ( trim( $categoryselector ) == 'true' && ! empty( $_GET['catid'] ) && !in_array( get_the_ID(), (array)@$cats[ $_GET['catid']]['posts'] ) ) {
            continue;
        }

        if ( !$mycourses || sfwd_lms_has_access( get_the_ID() ) ):

			global $post;

			$permalink 		= learndash_get_step_permalink($post->ID);
			$title			= get_the_title();
			$description 	= ( get_post_meta( get_the_ID(), '_lds_short_description' ) ? get_post_meta( get_the_ID(), '_lds_short_description', true ) : get_the_excerpt() );
			$duration 		= get_post_meta( get_the_ID(), '_lds_duration', true );
			$post_icon		= get_post_meta( get_the_ID(), '_lds_course_icon', true );
			$hgroup_class	= ( has_post_thumbnail() || $post_icon ? 'with-image' : '' ); ?>

            <div class="lds-expanded-course-item <?php if( isset($class) ) echo esc_attr($class); ?>">

				<hgroup class="<?php echo esc_attr($hgroup_class); ?>">
					<?php if( has_post_thumbnail() || $post_icon ): ?>
		                <div class="lds-expanded-course-item-icon course-item-icon">
		                    <?php
							$src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'course_icon' );
							if($src): ?>
		                    	<a href="<?php echo esc_url( learndash_get_step_permalink( get_the_ID() ) ); ?>" style="background-image: url('<?php echo esc_url($src[0]); ?>');"></a>
							<?php
							elseif($post_icon): ?>
								<a href="<?php echo esc_url( learndash_get_step_permalink( get_the_ID() ) ); ?>"><i class="fa <?php echo esc_attr($post_icon); ?>"></i></a>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<h3 class="lds-entry-title"><a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a></h3>

					<?php
					$lessons = (array) learndash_get_course_lessons_list( get_the_ID() );
					if( ( count($lessons) > 0 ) || ( $duration ) ): ?>
						<aside class="lds-expanded-meta-line">
							<?php
							if( count($lessons) > 0 && !isset( $lessons[0] ) ): ?>
								<span class="lds-expanded-meta"><i class="fa fa-files-o"></i> <?php echo esc_html(count($lessons)) . ' ' . _n( __( 'Lesson', 'lds_skins' ), __('Lessons', 'lds_skins'), count($lessons) ); ?></span>
							<?php
							endif;
							if( $duration ): ?>
								<span class="lds-expanded-meta"><i class="fa fa-clock-o"></i> <?php echo esc_html( $duration ); ?></span>
							<?php endif; ?>
						</aside>
					<?php endif; ?>
				</hgroup>

				<aside class="lds-expanded-short-description lds-expanded-section">
						<p><strong><?php esc_html_e( 'Description', 'lds_skins' ); ?></strong></p>
						<?php echo wpautop( esc_html( $description ) ); ?>
				</aside>

				<?php if( count($lessons) > 0 && !isset( $lessons[0] ) ): ?>
	                <div class="lds-expanded-course-lesson-list lds-expanded-section">
						<p><strong><?php echo LearnDash_Custom_Label::get_label( 'lessons' ) ?></strong></p>
						<ul>
							<?php foreach( $lessons as $lesson ): ?>
								<li>
									<a href="<?php echo esc_url( learndash_get_step_permalink( $lesson['post']->ID, $post->ID ) ); ?>" class="lds-content-status-<?php echo esc_attr( $lesson['status'] ); ?>">
										<?php
										$icon = ldvc_get_content_icon( $lesson['post']->ID );
										echo '<span class="lds-ec-icon fa ' . esc_attr($icon) . '"></span>' . esc_html($lesson['post']->post_title); ?>
										<?php
										if( get_post_meta( $lesson['post']->ID, '_lds_duration' ) ): ?>
											<span class="lds-ec-duration"><i class="fa fa-clock-o"></i> <?php echo esc_html( get_post_meta( $lesson['post']->ID, '_lds_duration', true ) ); ?></span>
										<?php endif; ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
	                </div>
				<?php endif; ?>

                <p><a href="<?php echo esc_url($permalink); ?>" class="lds-button lds-button-primary"><?php esc_html_e( 'Learn More', 'lds_skins' ); ?> <i class="fa fa-angle-right"></i></a></p>

            </div>

            <?php
        	endif;
    endwhile; ?>
</div>

    <?php
	$output = learndash_ob_get_clean( $level );
	wp_reset_query();

	$learndash_shortcode_used = true;

	/**
	 * Filter HTML output of category dropdown
	 *
	 * @since 2.1.0
	 *
	 * @param  string $output
	 */
	return apply_filters( 'ld_course_list', $output, $shortcode_atts, $filter );
}

add_shortcode( 'lds_login', 'lds_login_shortcode' );
function lds_login_shortcode( $args ) {

	if( is_user_logged_in() ) return;

	lds_shortcodes_enqueue_scripts();

	ob_start();
	include( ldvc_get_template_part('shortcodes/login.php') );
	return ob_get_clean();

}
