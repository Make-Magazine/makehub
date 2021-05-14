<?php
function lds_course_list( $attr ) {

    global $learndash_shortcode_used;

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
			'array' => false,
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
		$filter['tag'] = $tag;
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
		$filter['cat'] = $cat;
	}

	if ( ! empty( $cat ) ) {
		$filter['cat'] = $cat;
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

	$col = intval($col);
	if (!empty($col)) {
		$row_item_count = 0;
	}

	while ( $loop->have_posts() ) {
		$loop->the_post();
		if ( trim( $categoryselector ) == 'true' && ! empty( $_GET['catid'] ) && !in_array( get_the_ID(), (array)@$cats[ $_GET['catid']]['posts'] ) ) {
			continue;
		}

		if ( !$mycourses || sfwd_lms_has_access( get_the_ID() ) ) {
			if ( !empty( $col ) ) {
				$row_item_count += 1;

				if ( $row_item_count == 1 ) {
					?><div class="row"><?php
				}
			}

            include( ldvc_get_template_part('shortcodes/course_content-enhanced.php') );

			if ( !empty( $col ) ) {
				if ( $row_item_count >= $col ) {
					?></div><?php
					$row_item_count = 0;
				}
			}
		}
	}

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

/**
 * Outputs course navigation template for widget
 *
 * @since 2.0
 *
 * @param  int 		$course_id  course id
 * @return string 			 	course navigation output
 */
function learndash_expanded_course_navigation( $course_id, $widget_instance ) {

	$course = get_post( $course_id );

	if ( empty( $course->ID ) || $course_id != $course->ID ) {
		return;
	}

	$course = get_post( $course_id );

	if ( empty( $course->ID ) || $course->post_type != 'sfwd-courses' ) {
		return;
	}

	$course_settings = learndash_get_setting( $course );
	$lessons = learndash_get_course_lessons_list( $course );

	include( ldvc_get_template_part('widgets/course-navigation-expanded.php') );

}

function lds_get_user_stats( $lessons = NULL, $topics = NULL, $quizzes = NULL ) {

    $stats = array(
        'lessons'   =>  array(
            'total'     =>  count($lessons),
            'completed' =>  0,
            'title'     =>  LearnDash_Custom_Label::get_label( 'lessons' )
        ),
        'topics'    =>  array(
            'total'     =>  count($topics),
            'completed' =>  0,
            'title'     =>  LearnDash_Custom_Label::get_label( 'topics' )
        ),
        'quizzes'   =>  array(
            'total'     =>  count($quizzes),
            'completed' =>  0,
            'title'     =>  LearnDash_Custom_Label::get_label( 'quizzes' )
        )
    );

    if( $lessons ) {
        foreach( $lessons as $lesson ) {

            if( $lesson['status'] == 'completed' ) $stats['lessons']['completed']++;

            $quizzes  = learndash_get_lesson_quiz_list( $lesson['post']->ID, get_current_user_id() );
            if( $quizzes ) foreach ( $quizzes as $quiz ) {
                $stats['quizzes']['total']++;
                if( $quiz['status'] == 'completed' ) $stats['quizzes']['completed']++;
            }

        }
    }

    if( $topics ) {
        foreach( $topics as $topic ) {

            if ( !empty($topic->completed) ) $stats['topics']['completed']++;

            if( isset( $topic->ID ) ) {

                $quizzes = learndash_get_lesson_quiz_list( $topic->ID, get_current_user_id() );
                if( $quizzes ) foreach ( $quizzes as $quiz ) {
                    $stats['quizzes']['total']++;
                    if( $quiz['status'] == 'completed' ) $stats['quizzes']['completed']++;
                }

            }

        }
    }

    if( $quizzes ) {
        foreach( $quizzes as $quiz ) {
            if ( isset($quiz['status']) && $quiz['status'] == 'completed' ) {
                $stats['quizzes']['completed']++;
            }
        }
    }

    return apply_filters( 'lds_user_stats', $stats, $lessons, $topics, $quizzes );

}

function lds_enhanced_course_header_background( $course_status = null ) {
    echo lds_get_enhanced_course_header_background( $course_status );
}

function lds_get_enhanced_course_header_background( $course_status = null ) {

    if( !has_post_thumbnail() ) return;

    $background = get_the_post_thumbnail_url();
    $class      = ( ( $course_status == 'In Progress' || $course_status == 'completed' ) && get_option( 'lds_show_leaderboard', 'yes' ) == 'yes' ? 'lds-has-background' : 'lds-has-background lds-no-leaderboard' );

    return apply_filters( 'lds_enhanced_course_header_background', ' style="background-image: url(\'' . $background . '\');" class="'. esc_attr($class) . '" ' );

}

function ldvc_get_content_icon( $post_id = NULL ) {

    $post_id = ( $post_id == NULL ? get_the_ID() : $post_id );

    $custom_icon = ( get_post_meta( $post_id, '_lds_course_icon', true ) ? get_post_meta( $post_id, '_lds_course_icon', true ) : get_post_meta( $post_id, '_lds_content_type', true ) );

    return ( $custom_icon ? $custom_icon : 'fa-star-o' );

}

// add_filter( 'template_include', 'ldvc_custom_template_include', 99 );
function ldvc_custom_template_include( $template ) {

    $content_types = array(
        'sfwd-courses',
        'sfwd-lessons',
        'sfwd-topic',
        'sfwd-quiz'
    );

    if( in_array( get_post_type(), $content_types ) ) {

        $new_template   = get_option('lds_page_template');
        $new_template   = locate_template( array( $new_template ) );

        wp_die( var_dump($new_template) );

        if( '' != $new_template ) return $new_template;

    }

    return $template;

}

add_filter( 'body_class', 'ldvc_custom_template_body_class', 99 );
function ldvc_custom_template_body_class( $classes ) {

    $content_types = array(
        'sfwd-courses',
        'sfwd-lessons',
        'sfwd-topic',
        'sfwd-quiz'
    );

    if( !in_array( get_post_type(), $content_types ) || get_option('lds_page_template') == '' ) return $classes;

    $template_slug  = get_page_template_slug( get_option('lds_page_template') );

    $classes[] = $template_slug;

    return $classes;

}

function ldvc_get_template_part( $template ) {

    $template	= ( substr( $template, -4, 4 ) == '.php' ? $template : $template . '.php' );

    if ( $theme_file = locate_template( array( 'learndash/ldvc/' . $template ) ) ) {
      $file = $theme_file;
    } else {
      $file = LDS_PATH . 'legacy/views/' . $template;
    }

    return apply_filters( 'ldvc_template_' . $template, $file );

}
