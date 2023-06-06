<?php
/**
 * Functions for the Gift Guide custom post type
 *
 * @package    makeco
 * @license    http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author     Rio Roth-Barreiro
 *
 */

add_action( 'init', 'register_cpt_gift_guide', 998, 3 );

/**
 * Register the projects custom post type
 * @uses add_rewite_rule
 * @uses regoster_post_type
 */

function register_cpt_gift_guide() {

	$labels = array(
		'name' => _x( 'Gift Guides', 'Gift Guide' ),
		'singular_name' => _x( 'Gift Guide Product', 'Gift Guide' ),
		'add_new' => _x( 'Add New', 'Gift Guide' ),
		'add_new_item' => _x( 'Add New Gift Guide', 'Gift Guide' ),
		'edit_item' => _x( 'Edit Gift Guide', 'Gift Guide' ),
		'new_item' => _x( 'New Gift Guide', 'Gift Guide' ),
		'view_item' => _x( 'View Gift Guide', 'Gift Guide' ),
		'search_items' => _x( 'Search Gift Guides', 'Gift Guide' ),
		'not_found' => _x( 'No Gift Guides found', 'Gift Guide' ),
		'not_found_in_trash' => _x( 'No Gift Guides found in Trash', 'Gift Guide' ),
		'parent_item_colon' => _x( 'Parent Gift Guide:', 'Gift Guide' ),
		'menu_name' => _x( 'Gift Guides', 'Gift Guide' ),
		'all_items' => _x( 'Gift Guide Products', 'Gift Guide' )
	);

	$args = array(
		'labels' => $labels,
		'hierarchical' => false,
		'description' => 'Gift Guides are submitted by a Gravity form for Gift Guides',
		'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes' ),
		'taxonomies' => array( 'gift_guide_categories', 'audiences', 'gift_guide_years' ),
		'public' => true,
		'menu_icon' => 'dashicons-products',
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

	register_post_type( 'gift-guides', $args );

}

add_action( 'init', 'register_taxonomy_gift_guide_categories' );

/**
 * Add the Gift Guide Categories taxonomy
 *
 */
function register_taxonomy_gift_guide_categories() {

	$labels = array(
		'name' => _x( 'Gift Guide Categories', 'gift_guide_categories' ),
		'singular_name' => _x( 'Gift Guide Category', 'gift_guide_categories' ),
		'search_items' => _x( 'Gift Guide Categories', 'gift_guide_categories' ),
		'popular_items' => _x( 'Gift Guide Content Categories', 'gift_guide_categories' ),
		'all_items' => _x( 'All Gift Guide Categories', 'gift_guide_categories' ),
		'parent_item' => _x( 'Parent Gift Guide Category', 'gift_guide_categories' ),
		'parent_item_colon' => _x( 'Parent Gift Guide Category:', 'gift_guide_categories' ),
		'edit_item' => _x( 'Edit Gift Guide Category', 'gift_guide_categories' ),
		'update_item' => _x( 'Update Gift Guide Category', 'gift_guide_categories' ),
		'add_new_item' => _x( 'Add New Gift Guide Category', 'gift_guide_categories' ),
		'new_item_name' => _x( 'New Gift Guide Category', 'gift_guide_categories' ),
		'separate_items_with_commas' => _x( 'Separate Gift Guide categories with commas', 'gift_guide_categories' ),
		'add_or_remove_items' => _x( 'Add or remove Gift Guide Categories', 'gift_guide_categories' ),
		'choose_from_most_used' => _x( 'Choose from most used Gift Guide Categories', 'gift_guide_categories' ),
		'menu_name' => _x( 'Gift Guide Categories', 'gift_guide_categories' ),
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

	register_taxonomy( 'gift_guide_categories', array('gift_guides'), $args );


	// Original terms, commented out so they can be edited in admin without issue
	/*$gift_guide_categories = [
		"arts-crafts" => "Arts/Crafts",
		"books" => "Books",
		"gadgets" => "Gadgets",
		"games-toys" => "Games/Toys",
		"home" => "Home",
		"lifestyle" => "Lifestyle",
		"maker-wear" => "Maker-Wear",
		"tech" => "Tech",
		"tools" => "Tools",
		"other" => "Other"
	];
	if(empty(get_terms('gift_guide_categories'))) {
		foreach ($gift_guide_categories as $slug => $name) {
			wp_insert_term($name, 'gift_guide_categories', [
				'slug' => $slug,
			]);
		}
	}*/
}

add_action( 'init', 'register_taxonomy_audiences' );

/**
 * Add the Audience taxonomy
 *
 */
function register_taxonomy_audiences() {

	$labels = array(
		'name' => _x( 'Audiences', 'audiences' ),
		'singular_name' => _x( 'Audience', 'audiences' ),
		'search_items' => _x( 'Search Audience', 'audiences' ),
		'popular_items' => _x( 'Popular Audiences', 'audiences' ),
		'all_items' => _x( 'All Audiences', 'audiences' ),
		'parent_item' => _x( 'Parent Audience', 'audiences' ),
		'parent_item_colon' => _x( 'Parent Audience:', 'audiences' ),
		'edit_item' => _x( 'Edit Audience', 'audiences' ),
		'update_item' => _x( 'Update Audience', 'audiences' ),
		'add_new_item' => _x( 'Add New Audience', 'audiences' ),
		'new_item_name' => _x( 'New Audience', 'audiences' ),
		'separate_items_with_commas' => _x( 'Separate Audiences with commas', 'audiences' ),
		'add_or_remove_items' => _x( 'Add or remove Audiences', 'audiences' ),
		'choose_from_most_used' => _x( 'Choose from most used Audiences', 'audiences' ),
		'menu_name' => _x( 'Audiences', 'audiences' ),
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

	register_taxonomy( 'audiences', array('gift_guides'), $args );

	// Original terms, commented out so they can be edited in admin without issue
	/*$audiences = [
		"educators" => "Educators",
		"family-friends" => "Family/Friends",
		"kids" => "Kids",
		"teens" => "Teens",
	];
	if(empty(get_terms('audiences'))) {
		foreach ($audiences as $slug => $name) {
			wp_insert_term($name, 'audiences', [
				'slug' => $slug,
			]);
		}
	}*/

}


add_action( 'init', 'register_taxonomy_gift_guide_years' );
/**
 * Add the Gift Guide Year taxonomy
 *
 */
function register_taxonomy_gift_guide_years() {

	$labels = array(
		'name' => _x( 'Gift Guide Years', 'gift_guide_years' ),
		'singular_name' => _x( 'Gift Guide Year', 'gift_guide_years' ),
		'search_items' => _x( 'Search Gift Guide Year', 'gift_guide_years' ),
		'popular_items' => _x( 'Popular Gift Guide Years', 'gift_guide_years' ),
		'all_items' => _x( 'All Gift Guide Years', 'gift_guide_years' ),
		'parent_item' => _x( 'Parent Gift Guide Year', 'gift_guide_years' ),
		'parent_item_colon' => _x( 'Parent Gift Guide Year:', 'gift_guide_years' ),
		'edit_item' => _x( 'Edit Gift Guide Year', 'gift_guide_years' ),
		'update_item' => _x( 'Update Gift Guide Year', 'gift_guide_years' ),
		'add_new_item' => _x( 'Add New Gift Guide Year', 'gift_guide_years' ),
		'new_item_name' => _x( 'New Gift Guide Year', 'gift_guide_years' ),
		'separate_items_with_commas' => _x( 'Separate Gift Guide Years with commas', 'gift_guide_years' ),
		'add_or_remove_items' => _x( 'Add or remove Gift Guide Years', 'gift_guide_years' ),
		'choose_from_most_used' => _x( 'Choose from most used Gift Guide Years', 'gift_guide_years' ),
		'menu_name' => _x( 'Gift Guide Years', 'gift_guide_years' ),
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

	register_taxonomy( 'gift_guide_years', array('gift_guides'), $args );

}

function gift_guide_body_class( $classes ) {
    if ( is_singular( 'gift_guide' ) ) {
        $classes[] = 'gift-guide';
    }
    return $classes;
}
add_filter( 'body_class', 'gift_guide_body_class' );
