<?php

// add the learndash lessons and cpt user projects to wordpress search
function add_post_type_to_search( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }
	if ( $query->is_search() ) {
		$query->set(
			'post_type',
			array( 'post', 'user-projects', 'sfwd-lessons' ),
		);
	}
	return $query;

}
add_filter( 'pre_get_posts', 'add_post_type_to_search', 999, 2 );

/*function add_post_type_to_search_and_filter( $query_args, $sfid ) {
	var_dump($query_args);
	//modify $query_args here before returning it
	$query_args['post_type'] = array( 'user-projects', 'sfwd-lessons' );

	return $query_args;
}
add_filter( 'sf_edit_query_args', 'add_post_type_to_search_and_filter', 20, 2 );*/

// make order of projects library random
add_filter( 'posts_orderby', 'randomise_with_pagination' );
function randomise_with_pagination( $orderby ) {
	if( is_page(5777) ) { // Page 5777 is the project library page id
	  	// Reset seed on load of initial archive page
		if( ! get_query_var( 'paged' ) || get_query_var( 'paged' ) == 0 || get_query_var( 'paged' ) == 1 ) {
			if( isset( $_SESSION['seed'] ) ) {
				unset( $_SESSION['seed'] );
			}
		}
		// Get seed from session variable if it exists
		$seed = false;
		if( isset( $_SESSION['seed'] ) ) {
			$seed = $_SESSION['seed'];
		}
    	// Set new seed if none exists
    	if ( ! $seed ) {
      		$seed = rand();
      		$_SESSION['seed'] = $seed;
    	}
    	// Update ORDER BY clause to use seed
    	$orderby = 'RAND(' . $seed . ')';
	}
	return $orderby;
}

function get_lesson_output($lesson_id, $course_id) {
    global $post;
    do_action(THEME_HOOK_PREFIX . '_template_parts_content_top');
    $course_settings = learndash_get_setting($lesson_id);

    $lesson_materials = '';
    if (( 'on' === $course_settings['lesson_materials_enabled'] ) && (!empty($course_settings['lesson_materials']) )) {
        $lesson_materials = wp_specialchars_decode($course_settings['lesson_materials'], ENT_QUOTES);
        if (!empty($lesson_materials)) {
            $lesson_materials = do_shortcode($lesson_materials);
        }
    }
    ?>
    <div id="primary" class="content-area bb-grid-cell">
        <main id="main" class="site-main">
            <div style="text-align:center"><img src="<?php echo get_stylesheet_directory_uri() . '/images/makercamp-logo.png'; ?> " /></div>

			<div class="print-materials-wrapper">
				<div class="print-materials">
					<?php echo $lesson_materials; ?>
				</div>
            </div>

            <div class="print-body">
                <?php
                //print out lesson content
                $post = get_post($lesson_id, OBJECT); //set post info
                get_template_part('template-parts/content', 'sfwd');

                $topics = learndash_course_get_topics($course_id, $lesson_id);
                foreach ($topics as $topic) {
                    //print out topic content
                    $post = get_post($topic->ID, OBJECT); //set post info
                    get_template_part('template-parts/content', 'sfwd');
                }
                ?>
            </div>



        </main>
    </div>
    <?php
    //do_action(THEME_HOOK_PREFIX . '_single_template_part_content', 'page');
}
