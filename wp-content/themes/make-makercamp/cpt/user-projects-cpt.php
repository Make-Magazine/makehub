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

add_action( 'init', 'register_taxonomy_content_categories' );

/**
 * Add the Content Categories taxonomy
 *
 */
function register_taxonomy_content_categories() {

	$labels = array(
		'name' => _x( 'Content Categories', 'content_categories' ),
		'singular_name' => _x( 'Content Category', 'content_categories' ),
		'search_items' => _x( 'Content Categories', 'content_categories' ),
		'popular_items' => _x( 'Popular Content Categories', 'content_categories' ),
		'all_items' => _x( 'All Content Categories', 'content_categories' ),
		'parent_item' => _x( 'Parent Content Category', 'content_categories' ),
		'parent_item_colon' => _x( 'Parent Content Category:', 'content_categories' ),
		'edit_item' => _x( 'Edit Content Category', 'content_categories' ),
		'update_item' => _x( 'Update Content Category', 'content_categories' ),
		'add_new_item' => _x( 'Add New Content Category', 'content_categories' ),
		'new_item_name' => _x( 'New Content Category', 'content_categories' ),
		'separate_items_with_commas' => _x( 'Separate content categories with commas', 'content_categories' ),
		'add_or_remove_items' => _x( 'Add or remove Content Categories', 'content_categories' ),
		'choose_from_most_used' => _x( 'Choose from most used Content Categories', 'content_categories' ),
		'menu_name' => _x( 'Content Categories', 'content_categories' ),
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_in_nav_menus' => true,
		'show_ui' => true,
		'show_tagcloud' => true,
		'hierarchical' => true,
		'rewrite' => true,
		'query_var' => true,
		'show_in_rest' => true
	);

	register_taxonomy( 'content_categories', array('user-projects', 'sfwd-lessons'), $args );

	$content_categories = [
		"electronics" => "Electronics",
		"engineering" => "Engineering",
		"fabrication" => "Fabrication",
		"arts-and-crafts" => "Arts & Crafts",
		"woodworking" => "Woodworking",
		"wearables" => "Wearables",
		"stem-or-steam" => "STEM or STEAM",
		"microntrollers" => "Microcontrollers",
		"music" => "Music",
		"food" => "Food",
		"sustainability" => "Sustainability",
		"science" => "Science",
		"paper-crafts" => "Paper Crafts",
		"fiber-crafts" => "Fiber Arts",
		"chemistry" => "Chemistry",
		"physics" => "Physics",
		"programming" => "Programming",
		"games" => "Games"
	];
	if(empty(get_terms('content_categories'))) {
		foreach ($content_categories as $slug => $name) {
			wp_insert_term($name, 'content_categories', [
				'slug' => $slug,
			]);
		}
	}
}

add_action( 'init', 'register_taxonomy_materials' );

/**
 * Add the Materials taxonomy
 *
 */
function register_taxonomy_materials() {

	$labels = array(
		'name' => _x( 'Materials', 'materials' ),
		'singular_name' => _x( 'Material', 'materials' ),
		'search_items' => _x( 'Search Materials', 'materials' ),
		'popular_items' => _x( 'Popular Materials', 'materials' ),
		'all_items' => _x( 'All Materials', 'materials' ),
		'parent_item' => _x( 'Parent Material', 'materials' ),
		'parent_item_colon' => _x( 'Parent Material:', 'materials' ),
		'edit_item' => _x( 'Edit Material', 'materials' ),
		'update_item' => _x( 'Update Material', 'materials' ),
		'add_new_item' => _x( 'Add New Material', 'materials' ),
		'new_item_name' => _x( 'New Material', 'materials' ),
		'separate_items_with_commas' => _x( 'Separate materials with commas', 'materials' ),
		'add_or_remove_items' => _x( 'Add or remove Materials', 'materials' ),
		'choose_from_most_used' => _x( 'Choose from most used Materials', 'materials' ),
		'menu_name' => _x( 'Materials', 'materials' ),
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_in_nav_menus' => true,
		'show_ui' => true,
		'show_tagcloud' => true,
		'hierarchical' => true,
		'rewrite' => true,
		'query_var' => true,
		'show_in_rest' => true
	);

	register_taxonomy( 'materials', array('user-projects', 'sfwd-lessons'), $args );

}

add_action( 'init', 'register_taxonomy_ages' );

/**
 * Add the Age taxonomy
 *
 */
function register_taxonomy_ages() {

	$labels = array(
		'name' => _x( 'Ages', 'ages' ),
		'singular_name' => _x( 'Age', 'ages' ),
		'search_items' => _x( 'Search Ages', 'ages' ),
		'popular_items' => _x( 'Popular Ages', 'ages' ),
		'all_items' => _x( 'All Ages', 'ages' ),
		'parent_item' => _x( 'Parent Age', 'ages' ),
		'parent_item_colon' => _x( 'Parent Age:', 'ages' ),
		'edit_item' => _x( 'Edit Age', 'ages' ),
		'update_item' => _x( 'Update Age', 'ages' ),
		'add_new_item' => _x( 'Add New Age', 'ages' ),
		'new_item_name' => _x( 'New Age', 'ages' ),
		'separate_items_with_commas' => _x( 'Separate ages with commas', 'ages' ),
		'add_or_remove_items' => _x( 'Add or remove Ages', 'ages' ),
		'choose_from_most_used' => _x( 'Choose from most used Ages', 'ages' ),
		'menu_name' => _x( 'Ages', 'ages' ),
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_in_nav_menus' => true,
		'show_ui' => true,
		'show_tagcloud' => true,
		'hierarchical' => true,
		'rewrite' => true,
		'query_var' => true,
		'show_in_rest' => true
	);

	register_taxonomy( 'ages', array('user-projects', 'sfwd-lessons'), $args );

	$ages = [
		"5-and-under" => "5 and under",
		"6-8" => "6-8",
		"9-10" => "9-10",
		"11-14" => "11-14",
		"15-and-up" => "15 and up"
	];
	if(empty(get_terms('ages'))) {
		foreach ($ages as $slug => $name) {
			wp_insert_term($name, 'ages', [
				'slug' => $slug,
			]);
		}
	}
}

add_action( 'init', 'register_taxonomy_times' );

/**
 * Add the Time taxonomy
 *
 */
function register_taxonomy_times() {

	$labels = array(
		'name' => _x( 'Times', 'times' ),
		'singular_name' => _x( 'Time', 'times' ),
		'search_items' => _x( 'Search Times', 'times' ),
		'popular_items' => _x( 'Popular Times', 'times' ),
		'all_items' => _x( 'All Times', 'times' ),
		'parent_item' => _x( 'Parent Time', 'times' ),
		'parent_item_colon' => _x( 'Parent Time:', 'times' ),
		'edit_item' => _x( 'Edit Time', 'times' ),
		'update_item' => _x( 'Update Time', 'times' ),
		'add_new_item' => _x( 'Add New Time', 'times' ),
		'new_item_name' => _x( 'New Time', 'times' ),
		'separate_items_with_commas' => _x( 'Separate times with commas', 'times' ),
		'add_or_remove_items' => _x( 'Add or remove Times', 'times' ),
		'choose_from_most_used' => _x( 'Choose from most used Times', 'times' ),
		'menu_name' => _x( 'Times', 'times' ),
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_in_nav_menus' => true,
		'show_ui' => true,
		'show_tagcloud' => true,
		'hierarchical' => true,
		'rewrite' => true,
		'query_var' => true,
		'show_in_rest' => true
	);

	register_taxonomy( 'times', array('user-projects', 'sfwd-lessons'), $args );

	$times = [
		"10-30-min" => "10-30 min",
		"30-45-min" => "30-45 min",
		"45-60-min" => "45-60 min",
		"60-90-min" => "60-90 min",
		"90-120-min" => "90-120 min",
		"over-120-min" => "over 120 min"
	];
	if(empty(get_terms('times'))) {
		foreach ($times as $slug => $name) {
			wp_insert_term($name, 'times', [
				'slug' => $slug,
			]);
		}
	}
}

add_action( 'init', 'register_taxonomy_skill_levels' );

/**
 * Add the Skill Level taxonomy
 *
 */
function register_taxonomy_skill_levels() {

	$labels = array(
		'name' => _x( 'Skill Levels', 'skill_levels' ),
		'singular_name' => _x( 'Skill Level', 'skill_levels' ),
		'search_items' => _x( 'Search Skill Levels', 'skill_levels' ),
		'popular_items' => _x( 'Popular Skill Levels', 'skill_levels' ),
		'all_items' => _x( 'All Skill Levels', 'skill_levels' ),
		'parent_item' => _x( 'Parent Skill Level', 'skill_levels' ),
		'parent_item_colon' => _x( 'Parent Skill Level:', 'skill_levels' ),
		'edit_item' => _x( 'Edit Skill Level', 'skill_levels' ),
		'update_item' => _x( 'Update Skill Level', 'skill_levels' ),
		'add_new_item' => _x( 'Add New Skill Level', 'skill_levels' ),
		'new_item_name' => _x( 'New Skill Level', 'skill_levels' ),
		'separate_items_with_commas' => _x( 'Separate skill levels with commas', 'skill_levels' ),
		'add_or_remove_items' => _x( 'Add or remove Skill Levels', 'skill_levels' ),
		'choose_from_most_used' => _x( 'Choose from most used Skill Levels', 'skill_levels' ),
		'menu_name' => _x( 'Skill Levels', 'skill_levels' ),
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_in_nav_menus' => true,
		'show_ui' => true,
		'show_tagcloud' => true,
		'hierarchical' => true,
		'rewrite' => true,
		'query_var' => true,
		'show_in_rest' => true
	);

	register_taxonomy( 'skill_levels', array('user-projects', 'sfwd-lessons'), $args );

	$skill_levels = [
		"advanced" => "Advanced",
		"intermediate" => "Intermediate",
		"novice" => "Novice",
		"proficient" => "Proficient"
	];
	if(empty(get_terms('skill_levels'))) {
		foreach ($skill_levels as $slug => $name) {
			wp_insert_term($name, 'skill_levels', [
				'slug' => $slug,
			]);
		}
	}
}


add_action( 'init', 'register_taxonomy_makeyland_theme' );

/**
 * Add the Makey Land Theme taxonomy
 *
 */
function register_taxonomy_makeyland_theme() {

	$labels = array(
		'name' => _x( 'Makeyland Themes', 'makeyland_themes' ),
		'singular_name' => _x( 'Makeyland Theme', 'makeyland_themes' ),
		'search_items' => _x( 'Makeyland Themes', 'makeyland_themes' ),
		'popular_items' => _x( 'Makeyland Themes', 'makeyland_themes' ),
		'all_items' => _x( 'Makeyland Themes', 'makeyland_themes' ),
		'parent_item' => _x( 'Parent Makeyland Theme', 'makeyland_themes' ),
		'parent_item_colon' => _x( 'Parent Makeyland Theme:', 'makeyland_themes' ),
		'edit_item' => _x( 'Edit Makeyland Theme', 'makeyland_themes' ),
		'update_item' => _x( 'Update Makeyland Theme', 'makeyland_themes' ),
		'add_new_item' => _x( 'Add New Makeyland Theme', 'makeyland_themes' ),
		'new_item_name' => _x( 'New Makeyland Theme', 'makeyland_themes' ),
		'separate_items_with_commas' => _x( 'Separate Makeyland themes with commas', 'makeyland_themes' ),
		'add_or_remove_items' => _x( 'Add or remove Makeyland Themes', 'makeyland_themes' ),
		'choose_from_most_used' => _x( 'Choose from most used Makeyland Themes', 'makeyland_themes' ),
		'menu_name' => _x( 'Makeyland Themes', 'makeyland_themes' ),
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_in_nav_menus' => true,
		'show_ui' => true,
		'show_tagcloud' => true,
		'hierarchical' => true,
		'rewrite' => true,
		'query_var' => true,
		'show_in_rest' => true
	);

	register_taxonomy( 'makeyland_themes', array('user-projects', 'sfwd-lessons'), $args );

	$makeyland_themes = [
		"art-craft-studio" => "Art/Craft Studio",
		"carnival-theme-park" => "Carnival/Theme Park",
		"construction-site" => "Construction Site",
		"marina-waterfront" => "Marina/Waterfront",
		"the-canteen" => "The Canteen (Mess Hall and Recycling Station)",
		"the-depot" => "The Depot (Airport/Space Station/ Racetrack)",
		"the-shop" => "The Shop (Makerspace)",
	];
	if(is_array(get_terms('makeyland_themes')) && empty(get_terms('makeyland_themes'))) {
		foreach ($makeyland_themes as $slug => $name) {
			wp_insert_term($name, 'makeyland_themes', [
				'slug' => $slug,
			]);
		}
	}
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
