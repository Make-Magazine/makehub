<?php
/**
 * Functions for the Make: Newsletter Article custom post type
 *
 * @package    makeco
 * @license    http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author     Make: Community
 *
 */

add_action( 'init', 'register_cpt_newsletter', 998, 3 );

/**
 * Register the custom post type
 * @uses add_rewite_rule
 * @uses regoster_post_type
 */

function register_cpt_newsletter() {

	$labels = array(
		'name' => __( 'Newsletter Articles', 'makeco' ),
		'singular_name' => __( 'Newsletter Article', 'makeco' ),
		'add_new' => __( 'Add New Article', 'makeco' ),
		'add_new_item' => __( 'Add New Article', 'makeco' ),
		'edit_item' => __( 'Edit Article', 'makeco' ),
		'new_item' => __( 'New Article', 'makeco' ),
		'view_item' => __( 'View Article', 'makeco' ),
		'search_items' => __( 'Search Newsletter Articles', 'makeco' ),
		'not_found' => __( 'No Articles found', 'makeco' ),
		'not_found_in_trash' => __( 'No Newsletter Articles found in Trash', 'makeco' ),
		'parent_item_colon' => __( 'Parent Newsletter Article:', 'makeco' ),
		'menu_name' => __( 'Newsletter', 'makeco' ),
		'all_items' => __( 'All Articles', 'makeco' )
	);

	$args = array(
		'labels' => $labels,
		'hierarchical' => false,
		'description' => 'Newsletter Articles are used for a Substack like Newsletter from Make.co',
		'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes' ),
		'taxonomies' => array( 'newsletter_categories', 'post_tag'),
		'public' => true,
		'menu_icon' => 'dashicons-media-text',
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

	register_post_type( 'newsletter', $args );

}

add_action( 'init', 'register_taxonomy_newsletter_categories' );

/**
 * Add the Newsletter Article Categories taxonomy
 *
 */
function register_taxonomy_newsletter_categories() {

	$labels = array(
		'name' => __( 'Article Categories', 'makeco' ),
		'singular_name' => __( 'Article Category', 'makeco' ),
		'search_items' => __( 'Article Categories', 'makeco' ),
		'popular_items' => __( 'Article Content Categories', 'makeco' ),
		'all_items' => __( 'All Article Categories', 'makeco' ),
		'parent_item' => __( 'Parent Article Category', 'makeco' ),
		'parent_item_colon' => __( 'Parent Article Category:', 'makeco' ),
		'edit_item' => __( 'Edit Article Category', 'makeco' ),
		'update_item' => __( 'Update Article Category', 'makeco' ),
		'add_new_item' => __( 'Add New Article Category', 'makeco' ),
		'new_item_name' => __( 'New Article Category', 'makeco' ),
		'separate_items_with_commas' => __( 'Separate Article categories with commas', 'makeco' ),
		'add_or_remove_items' => __( 'Add or remove Article Categories', 'makeco' ),
		'choose_from_most_used' => __( 'Choose from most used Article Categories', 'makeco' ),
		'menu_name' => __( 'Article Categories', 'makeco' ),
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

	register_taxonomy( 'newsletter_categories', array('newsletters'), $args );
}

function newsletter_body_class( $classes ) {
    if ( is_singular( 'newsletter' ) ) {
        $classes[] = 'newsletter';
    }
    return $classes;
}
add_filter( 'body_class', 'newsletter_body_class' );

/* Remove Yoast SEO Columns
 *
 * If you have custom post types, you can add additional lines in this format
 * add_filter( 'manage_edit-{$post_type}_columns', 'yoast_seo_admin_remove_columns', 10, 1 );
 * replacing {$post_type} with the name of the custom post type.
 */

 add_filter( 'manage_edit-newsletter_columns', 'yoast_seo_admin_remove_columns', 10, 1 );
 
 function yoast_seo_admin_remove_columns( $columns ) {
   unset($columns['wpseo-score']);
   unset($columns['wpseo-score-readability']);
   unset($columns['wpseo-title']);
   unset($columns['wpseo-metadesc']);
   unset($columns['wpseo-focuskw']);
   unset($columns['wpseo-links']);
   unset($columns['wpseo-linked']);
   return $columns;
 }

/**
 * Add a new admin bar menu item.
 */

function newsletters_toolbar_node( $admin_bar ) {       
    //add top level item
	$admin_bar->add_menu(
		array(
			'id'    => 'newsletters-menu',
			'title' => '<span class="ab-icon dashicons dashicons-email"></span>Newsletter',
			'href'  => '/wp-admin/edit.php?post_type=newsletter'
		)
	);

    // Add a submenu to the above item. add_menu is just a wrapper for add_node.
    $admin_bar->add_node(
		array(
			'parent' => 'newsletters-menu',
			'id'     => 'newsletters-submenu1',
			'title'  => 'All Articles',
			'href'   => '/wp-admin/edit.php?post_type=newsletter'		
		)
	);
	$admin_bar->add_node(
		array(
			'parent' => 'newsletters-menu',
			'id'     => 'newsletters-submenu2',
			'title'  => 'Add Articles',
			'href'   => '/wp-admin/post-new.php?post_type=newsletter'		
		)
	);
	$admin_bar->add_node(
		array(
			'parent' => 'newsletters-menu',
			'id'     => 'newsletters-submenu3',
			'title'  => 'Article Categories',
			'href'   => '/wp-admin/edit-tags.php?taxonomy=newsletter_categories&post_type=newsletter'		
		)
	);    

	$admin_bar->add_node(
		array(
			'parent' => 'newsletters-menu',
			'id'     => 'newsletters-submenu4',
			'title'  => 'All Newsletters',
			'href'   => '/wp-admin/admin.php?page=mailoptin-emails'		
		)
	);    

    $admin_bar->add_node(
		array(
			'parent' => 'newsletters-menu',
			'id'     => 'newsletters-submenu5',
			'title'  => 'Newsletter Stats',
			'href'   => '/wp-admin/admin.php?page=mailoptin-statistics'		
		)
	);    
    
}
 add_action('admin_bar_menu', 'newsletters_toolbar_node', 999);