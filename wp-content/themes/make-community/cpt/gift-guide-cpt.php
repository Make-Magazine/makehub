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
		'name' => __( 'Gift Guides', 'makeco' ),
		'singular_name' => __( 'Gift Guide Product', 'makeco' ),
		'add_new' => __( 'Add New', 'makeco' ),
		'add_new_item' => __( 'Add New Gift Guide', 'makeco' ),
		'edit_item' => __( 'Edit Gift Guide', 'makeco' ),
		'new_item' => __( 'New Gift Guide', 'makeco' ),
		'view_item' => __( 'View Gift Guide', 'makeco' ),
		'search_items' => __( 'Search Gift Guides', 'makeco' ),
		'not_found' => __( 'No Gift Guides found', 'makeco' ),
		'not_found_in_trash' => __( 'No Gift Guides found in Trash', 'makeco' ),
		'parent_item_colon' => __( 'Parent Gift Guide:', 'makeco' ),
		'menu_name' => __( 'Gift Guides', 'makeco' ),
		'all_items' => __( 'Gift Guide Products', 'makeco' )
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
		'name' => __( 'Gift Guide Categories', 'makeco' ),
		'singular_name' => __( 'Gift Guide Category', 'makeco' ),
		'search_items' => __( 'Gift Guide Categories', 'makeco' ),
		'popular_items' => __( 'Gift Guide Content Categories', 'makeco' ),
		'all_items' => __( 'All Gift Guide Categories', 'makeco' ),
		'parent_item' => __( 'Parent Gift Guide Category', 'makeco' ),
		'parent_item_colon' => __( 'Parent Gift Guide Category:', 'makeco' ),
		'edit_item' => __( 'Edit Gift Guide Category', 'makeco' ),
		'update_item' => __( 'Update Gift Guide Category', 'makeco' ),
		'add_new_item' => __( 'Add New Gift Guide Category', 'makeco' ),
		'new_item_name' => __( 'New Gift Guide Category', 'makeco' ),
		'separate_items_with_commas' => __( 'Separate Gift Guide categories with commas', 'makeco' ),
		'add_or_remove_items' => __( 'Add or remove Gift Guide Categories', 'makeco' ),
		'choose_from_most_used' => __( 'Choose from most used Gift Guide Categories', 'makeco' ),
		'menu_name' => __( 'Gift Guide Categories', 'makeco' ),
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
		'name' => __( 'Audiences', 'makeco' ),
		'singular_name' => __( 'Audience', 'makeco' ),
		'search_items' => __( 'Search Audience', 'makeco' ),
		'popular_items' => __( 'Popular Audiences', 'makeco' ),
		'all_items' => __( 'All Audiences', 'makeco' ),
		'parent_item' => __( 'Parent Audience', 'makeco' ),
		'parent_item_colon' => __( 'Parent Audience:', 'makeco' ),
		'edit_item' => __( 'Edit Audience', 'makeco' ),
		'update_item' => __( 'Update Audience', 'makeco' ),
		'add_new_item' => __( 'Add New Audience', 'makeco' ),
		'new_item_name' => __( 'New Audience', 'makeco' ),
		'separate_items_with_commas' => __( 'Separate Audiences with commas', 'makeco' ),
		'add_or_remove_items' => __( 'Add or remove Audiences', 'makeco' ),
		'choose_from_most_used' => __( 'Choose from most used Audiences', 'makeco' ),
		'menu_name' => __( 'Audiences', 'makeco' ),
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
		'name' => __( 'Gift Guide Years', 'makeco' ),
		'singular_name' => __( 'Gift Guide Year', 'makeco' ),
		'search_items' => __( 'Search Gift Guide Year', 'makeco' ),
		'popular_items' => __( 'Popular Gift Guide Years', 'makeco' ),
		'all_items' => __( 'All Gift Guide Years', 'makeco' ),
		'parent_item' => __( 'Parent Gift Guide Year', 'makeco' ),
		'parent_item_colon' => __( 'Parent Gift Guide Year:', 'makeco' ),
		'edit_item' => __( 'Edit Gift Guide Year', 'makeco' ),
		'update_item' => __( 'Update Gift Guide Year', 'makeco' ),
		'add_new_item' => __( 'Add New Gift Guide Year', 'makeco' ),
		'new_item_name' => __( 'New Gift Guide Year', 'makeco' ),
		'separate_items_with_commas' => __( 'Separate Gift Guide Years with commas', 'makeco' ),
		'add_or_remove_items' => __( 'Add or remove Gift Guide Years', 'makeco' ),
		'choose_from_most_used' => __( 'Choose from most used Gift Guide Years', 'makeco' ),
		'menu_name' => __( 'Gift Guide Years', 'makeco' ),
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
