<?php
//Registers Course Notes as custom post type
function nt_register_course_note_create_type() {

	$visibility = apply_filters( 'ldnt_notes_post_visibility', array(
		'show_ui'			=>	false,
		'show_in_nav_menus'	=>	false,
		'show_in_menu'		=>	false,
	) );

	$post_labels = array(
		'name' 			    	=> __( 'Course Notes', 'sfwd-lms' ),
		'singular_name' 		=> __( 'Course Notes', 'sfwd-lms' ),
		'add_new' 		    	=> __( 'Add New', 'sfwd-lms' ),
		'add_new_item'  		=> __( 'Add New Note', 'sfwd-lms' ),
		'edit'		        	=> __( 'Edit', 'sfwd-lms' ),
		'edit_item'	        	=> __( 'Edit Course Note', 'sfwd-lms' ),
		'new_item'	        	=> __( 'New Course Note', 'sfwd-lms' ),
		'view' 			    	=> __( 'View Course Note', 'sfwd-lms' ),
		'view_item' 			=> __( 'View Course Note', 'sfwd-lms' ),
		'search_term'   		=> __( 'Search Notes', 'sfwd-lms' ),
		'parent' 		    	=> __( 'Parent Course Note', 'sfwd-lms' ),
		'not_found' 			=> __( 'No Notes Found', 'sfwd-lms' ),
		'not_found_in_trash' 	=> __( 'No Notes in Trash', 'sfwd-lms' ),
	);

	$post_settings = apply_filters( 'ldnt_notes_post_settings', array(
		'labels' 				=> $post_labels,
		'public' 				=> true,
		'exclude_from_search'	=> true,
		'publicly_queryable'	=> true,
		'menu_icon'				=> 'dashicons-format-aside',
		'show_in_admin_bar'		=> false,
		'has_archive' 			=> false,
		'supports' 				=> array( 'title', 'editor', 'thumbnail','page-attributes' ),
		'taxonomies' 			=> array( 'post_tag', 'category' ),
		'exclude_from_search' 	=> true,
		'capability_type' 		=> 'post',
		'rewrite' 				=> array( 'slug' => 'lds-course-notes' ),
	) );

	if( did_action('elementor/loaded') ) {
		$visibility['show_in_nav_menus'] = true;
	}

	if( get_option( 'ldnt_show_notes_in_admin', 'no' ) != 'yes' && current_user_can('read_others_nt_notes') ) {
		$post_settings = array_merge( $post_settings, $visibility );
	}

	register_post_type( 'coursenote', $post_settings );

	// If the permalinks haven't been flushed yet, let's flush these guys!
	if( get_option('ldnt_ver') != LDNT_VER ) {

		flush_rewrite_rules();
		update_option( 'ldnt_ver', LDNT_VER );

	}

}
add_action( 'init', 'nt_register_course_note_create_type' );

//Adds Course Note taxonomies
function nt_regsiter_taxonomy() {
	$labels = array(
		'name'              => __( 'Course Note Categories', 'sfwd-lms' ),
		'singular_name'     => __( 'Course Note Category', 'sfwd-lms' ),
		'search_items'      => __( 'Search Course Notes Categories', 'sfwd-lms' ),
		'all_items'         => __( 'All Course Note Categories', 'sfwd-lms' ),
		'edit_item'         => __( 'Edit Course Note Category', 'sfwd-lms' ),
		'update_item'       => __( 'Update Course Note Category', 'sfwd-lms' ),
		'add_new_item'      => __( 'Add New Course Note Category', 'sfwd-lms' ),
		'new_item_name'     => __( 'New Course Note Category', 'sfwd-lms' ),
		'menu_name'         => __( 'Course Note Categories', 'sfwd-lms' ),
	);
	// register taxonomy
	register_taxonomy( 'coursenotecat', 'coursenote', array(
		'hierarchical' 			=> true,
		'labels' 				=> $labels,
		'query_var' 			=> true,
		'show_admin_column' 	=> false
	) );
}
add_action('init', 'nt_regsiter_taxonomy');

add_filter( 'manage_edit-coursenote_columns', 'ldnt_coursenote_columns' );
function ldnt_coursenote_columns( $columns ) {

	$columns = array(
		'cb' 		=> '<input type="checkbox" />',
		'author'	=> __( 'User', 'sfwd-lms' ),
		'title' 	=> __( 'Title', 'sfwd-lms'  ),
		'location' 	=> __( 'Location', 'sfwd-lms'  ),
		'date' 		=> __( 'Date', 'sfwd-lms'  ),
		'updated'	=> __( 'Updated', 'sfwd-lms'  )
	);

	return $columns;

}

add_action( 'manage_coursenote_posts_custom_column', 'ldnt_coursenote_column_content', 10, 2 );
function ldnt_coursenote_column_content( $column, $post_id = null ) {

	global $post;

	switch( $column ) {

		case 'location' :

			$course_array = get_post_meta( $post->ID, '_nt-course-array', true );
			echo nt_course_breadcrumbs( $course_array );

			break;

		case 'updated' :

			the_modified_date();
			echo "<br>";
			the_modified_time();

			break;

	}

}
