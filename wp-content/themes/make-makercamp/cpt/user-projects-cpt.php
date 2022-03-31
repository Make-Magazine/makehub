<?php
/**
 * Functions for the User projects custom post type.
 *
 * @package    makeco
 * @license    http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author     Rio Roth-Barreiro
 *
 */

add_action( 'init', 'register_cpt_user_project', 998, 3 );

/**
 * Register the projects custom post type
 * @uses add_rewite_rule
 * @uses regoster_post_type
 */

function register_cpt_user_project() {

	$labels = array(
		'name' => _x( 'User Projects', 'User Project' ),
		'singular_name' => _x( 'User Project', 'User Project' ),
		'add_new' => _x( 'Add New', 'User Project' ),
		'add_new_item' => _x( 'Add New User Project', 'User Project' ),
		'edit_item' => _x( 'Edit User Project', 'User Project' ),
		'new_item' => _x( 'New User Project', 'User Project' ),
		'view_item' => _x( 'View User Project', 'User Project' ),
		'search_items' => _x( 'Search User Projects', 'User Project' ),
		'not_found' => _x( 'No User Projects found', 'User Project' ),
		'not_found_in_trash' => _x( 'No User Projects found in Trash', 'User Project' ),
		'parent_item_colon' => _x( 'Parent User Project:', 'User Project' ),
		'menu_name' => _x( 'User Projects', 'User Project' ),
	);

	$args = array(
		'labels' => $labels,
		'hierarchical' => false,
		'description' => 'User projects are submitted by a Gravity form and are distinct from Make: generated',
		'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes' ),
		'taxonomies' => array( 'ld_lesson_category' ),
		'public' => true,
		'menu_icon' => 'dashicons-hammer',
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'show_in_rest' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'has_archive' => true,
		'query_var' => true,
		'can_export' => true,
		'capability_type' => 'post',
		'menu_position' => 40,
	);

	register_post_type( 'user-projects', $args );

}


// user projects isn't that nice a slug for public consumption, let's rewrite it
if( !function_exists('change_user_projects_slug') ){
    add_filter( 'register_post_type_args', 'change_user_projects_slug', 10, 2 );
    function change_user_projects_slug( $args, $post_type ) {
        if ( 'user-projects' === $post_type ) {
            $args['rewrite']['slug'] = 'projects';
        }
        return $args;
    }
}

function user_project_body_class( $classes ) {
    if ( is_singular( 'user-projects' ) ) {
        $classes[] = 'single-user-project';
    }
    return $classes;
}
add_filter( 'body_class', 'user_project_body_class' );
