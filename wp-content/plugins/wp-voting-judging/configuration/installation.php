<?php
if ( ! function_exists('judge_post_type') ) {

	// Register Judge Post Type
	function judge_post_type() {

		$labels = array(
			'name'                  => _x( 'Judging Group',  'voting-contest' ),
			'singular_name'         => _x( 'Judge',  'voting-contest' ),
			'menu_name'             => __( 'Contest Judging', 'voting-contest' ),
			'name_admin_bar'        => __( 'Judging Group', 'voting-contest' ),
			'archives'              => __( 'Judging Group Archives', 'voting-contest' ),
			'attributes'            => __( 'Judging Group Attributes', 'voting-contest' ),
			'parent_item_colon'     => __( 'Parent Judging Group:', 'voting-contest' ),
			'all_items'             => __( 'All Judging Group', 'voting-contest' ),
			'add_new_item'          => __( 'Add New Judging Group', 'voting-contest' ),
			'add_new'               => __( 'Add New', 'voting-contest' ),
			'new_item'              => __( 'New Judging Group', 'voting-contest' ),
			'edit_item'             => __( 'Edit Judging Group', 'voting-contest' ),
			'update_item'           => __( 'Update Judging Group', 'voting-contest' ),
			'view_item'             => __( 'View Judging Group', 'voting-contest' ),
			'view_items'            => __( 'View Judging Group', 'voting-contest' ),
			'search_items'          => __( 'Search Judging Group', 'voting-contest' ),
			'not_found'             => __( 'Not found', 'voting-contest' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'voting-contest' ),			
			'items_list'            => __( 'Judging Group list', 'voting-contest' ),
			'items_list_navigation' => __( 'Judging Group list navigation', 'voting-contest' ),
			'filter_items_list'     => __( 'Filter Judging Group list', 'voting-contest' ),
		);
		$args = array(
			'label'                 => __( 'Judge', 'voting-contest' ),
			'description'           => __( 'Post Type Description', 'voting-contest' ),
			'labels'                => $labels,
			'supports'              => array( 'title'),			
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 99,
			'menu_icon'  			=> 'dashicons-megaphone',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',	
			'show_in_rest'			=> true
			
		);
		register_post_type( 'owjudge', $args );

	}
	add_action( 'init', 'judge_post_type', 0 );

}

if(!function_exists('wpvc_create_judging_table')){
	function wpvc_create_judging_table(){
		global $wpdb;
		$judge_tbl_sql = 'CREATE TABLE IF NOT EXISTS ' . WPVC_JUDGES_TBL . '(
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			ip VARCHAR( 255 ) NOT NULL,
			answers LONGTEXT NOT NULL,
			avg_score FLOAT(10,2) NOT NULL,
			post_id INT NOT NULL,
			termid INT NOT NULL,
			user_id bigint UNSIGNED NOT NULL,							
			date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
		)';
		ob_start();
        $wpdb->query($judge_tbl_sql);
	}
}