<?php
/**
 * Make - Makerspaces Theme
 *
 * This file adds functions to the Make - Makerspaces Theme.
 *
 * @package Make - Makerspaces
 * @author  Make Community
 * @license GPL-2.0-or-later
 * @link    https://make.co/
 */

// Starts the engine.
require_once get_template_directory() . '/lib/init.php';

// Defines the child theme (do not remove).
define( 'CHILD_THEME_NAME', 'Make - Makerspaces' );
define( 'CHILD_THEME_URL', 'https://make.co' );
define( 'CHILD_THEME_VERSION', '1.0.0' );

 // Load child theme textdomain.
load_child_theme_textdomain( 'makerspaces' );

// Sets up the Theme.
require_once get_stylesheet_directory() . '/lib/theme-defaults.php';

add_action( 'after_setup_theme', 'make_makerspaces_localization_setup' );
/**
 * Sets localization (do not remove).
 *
 * @since 1.0.0
 */
function make_makerspaces_localization_setup() {

	load_child_theme_textdomain( 'make-makerspaces', get_stylesheet_directory() . '/languages' );

}

// Adds helper functions.
require_once get_stylesheet_directory() . '/lib/helper-functions.php';

// Adds image upload and color select to Customizer.
require_once get_stylesheet_directory() . '/lib/customize.php';

// Includes Customizer CSS.
require_once get_stylesheet_directory() . '/lib/output.php';

// Adds WooCommerce support.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-setup.php';

// Adds the required WooCommerce styles and Customizer CSS.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-output.php';

// Adds the Genesis Connect WooCommerce notice.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-notice.php';

// Include all function files in the makerfaire/functions directory:
foreach (glob(get_stylesheet_directory() . '/functions/*.php') as $file) {
   include_once $file;
}

// Universal functions like auth0
require_once(ABSPATH . 'wp-content/universal-assets/v2/universal-functions.php');

add_action( 'after_setup_theme', 'genesis_child_gutenberg_support' );
/**
 * Adds Gutenberg opt-in features and styling.
 *
 * @since 2.7.0
 */
function genesis_child_gutenberg_support() { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- using same in all child themes to allow action to be unhooked.
	require_once get_stylesheet_directory() . '/lib/gutenberg/init.php';
}


add_action( 'wp_enqueue_scripts', 'make_makerspaces_enqueue_scripts_styles' );
/**
 * Enqueues scripts and styles.
 *
 * @since 1.0.0
 */
function make_makerspaces_enqueue_scripts_styles() {
	$my_theme = wp_get_theme();
  	$my_version = $my_theme->get('Version');

	### GENESIS STYLES #####
	$parent_style = 'genesis-style';
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );

	### SUBTHEME STYLES ###
	wp_enqueue_style('make-makerspaces-style', get_stylesheet_directory_uri() . '/css/style.min.css', array(), $my_version );


	wp_enqueue_style( 'dashicons' );

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script(
		'make-makerspaces-responsive-menu',
		get_stylesheet_directory_uri() . "/js/responsive-menus{$suffix}.js",
		array( 'jquery' ),
		$my_version,
		true
	);

	wp_localize_script(
		'make-makerspaces-responsive-menu',
		'genesis_responsive_menu',
		make_makerspaces_responsive_menu_settings()
	);

	wp_enqueue_script('markerclusterer', 'https://cdnjs.cloudflare.com/ajax/libs/js-marker-clusterer/1.0.0/markerclusterer_compiled.js', '', 'all');

	wp_enqueue_script('theme-js', get_stylesheet_directory_uri() . '/js/min/scripts.min.js', array('jquery'), $my_version, true);

	// Map page only
	if (is_page_template('page-makerspaces-map.php')) {
		wp_enqueue_script('google-map', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDtWsCdftU2vI9bkZcwLxGQwlYmNRnT2VM', false, false, false);
		wp_enqueue_script('google-markers', 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js', array('google-map'), false, false);
		wp_enqueue_script('vue', get_stylesheet_directory_uri() . '/js/ms-map/vue.min.js', false, false, false);
		wp_enqueue_script('axios', get_stylesheet_directory_uri() . '/js/ms-map/axios.min.js', array('vue'), false, false);
		wp_enqueue_script('vue-table-2', get_stylesheet_directory_uri() . '/js/ms-map/vue-tables-2.min.js', array('vue'), false, false);
		wp_enqueue_script('vue-map', get_stylesheet_directory_uri() . '/js/min/ms-map.min.js', array(), $my_version, false );
	}

	wp_enqueue_script(
		'make-makerspaces',
		get_stylesheet_directory_uri() . '/js/make-makerspaces.js',
		array( 'jquery' ),
		$my_version,
		true
	);
}

function add_slug_body_class($classes) {
    global $post;
    global $bp;
		$classes[] = 'makerspaces';
    if (isset($post)) {
        if ($post->post_name) {
            $classes[] = $post->post_type . '-' . $post->post_name;
            // any query string becomes a body class too
            parse_str($_SERVER['QUERY_STRING'], $query_array);
            foreach($query_array as $key => $value) {
                $classes[] = $key . "-" . $value;
            }
        }
    }
	return $classes;
}

add_filter('body_class', 'add_slug_body_class');

// remove the subtheme level style.css to use it as a version
remove_action( 'genesis_meta', 'genesis_load_stylesheet' );

add_filter('show_admin_bar', 'remove_admin_bar', PHP_INT_MAX);
// if you're not an admin, don't show the admin bar
function remove_admin_bar(){
    if (current_user_can('administrator')) {
        return true;
    }
    return false;
}


/**
 * Defines responsive menu settings.
 *
 * @since 2.3.0
 */
function make_makerspaces_responsive_menu_settings() {

	$settings = array(
		'mainMenu'         => __( 'Menu', 'make-makerspaces' ),
		'menuIconClass'    => 'dashicons-before dashicons-menu',
		'subMenu'          => __( 'Submenu', 'make-makerspaces' ),
		'subMenuIconClass' => 'dashicons-before dashicons-arrow-down-alt2',
		'menuClasses'      => array(
			'combine' => array(
				'.nav-primary',
			),
			'others'  => array(),
		),
	);

	return $settings;

}

// Adds support for HTML5 markup structure.
add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption'  ) );

// Adds support for accessibility.
add_theme_support( 'genesis-accessibility', genesis_get_config( 'accessibility' ) );

// Adds viewport meta tag for mobile browsers.
add_theme_support( 'genesis-responsive-viewport' );

// Adds custom logo in Customizer > Site Identity.
add_theme_support( 'custom-logo', genesis_get_config( 'custom-logo' ) );

// Renames primary and secondary navigation menus.
add_theme_support( 'genesis-menus', genesis_get_config( 'menus' ) );

// Adds image sizes.
add_image_size( 'sidebar-featured', 75, 75, true );

// Adds support for after entry widget.
add_theme_support( 'genesis-after-entry-widget-area' );

// Adds support for 3-column footer widgets.
add_theme_support( 'genesis-footer-widgets', 3 );

// Removes header right widget area.
unregister_sidebar( 'header-right' );

// Removes secondary sidebar.
unregister_sidebar( 'sidebar-alt' );

// Removes site layouts.
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );

// Removes output of primary navigation right extras.
remove_filter( 'genesis_nav_items', 'genesis_nav_right', 10, 2 );
remove_filter( 'wp_nav_menu_items', 'genesis_nav_right', 10, 2 );

add_action( 'genesis_theme_settings_metaboxes', 'make_makerspaces_remove_metaboxes' );
/**
 * Removes output of unused admin settings metaboxes.
 *
 * @since 2.6.0
 *
 * @param string $_genesis_admin_settings The admin screen to remove meta boxes from.
 */
function make_makerspaces_remove_metaboxes( $_genesis_admin_settings ) {

	remove_meta_box( 'genesis-theme-settings-header', $_genesis_admin_settings, 'main' );
	remove_meta_box( 'genesis-theme-settings-nav', $_genesis_admin_settings, 'main' );

}

add_filter( 'genesis_customizer_theme_settings_config', 'make_makerspaces_remove_customizer_settings' );
/**
 * Removes output of header and front page breadcrumb settings in the Customizer.
 *
 * @since 2.6.0
 *
 * @param array $config Original Customizer items.
 * @return array Filtered Customizer items.
 */
function make_makerspaces_remove_customizer_settings( $config ) {

	unset( $config['genesis']['sections']['genesis_header'] );
	unset( $config['genesis']['sections']['genesis_breadcrumbs']['controls']['breadcrumb_front_page'] );
	return $config;

}

// Displays custom logo.
add_action( 'genesis_site_title', 'the_custom_logo', 0 );

// Repositions primary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_header', 'genesis_do_nav', 12 );

// Repositions the secondary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_footer', 'genesis_do_subnav', 10 );

add_filter( 'wp_nav_menu_args', 'make_makerspaces_secondary_menu_args' );
/**
 * Reduces secondary navigation menu to one level depth.
 *
 * @since 2.2.3
 *
 * @param array $args Original menu options.
 * @return array Menu options with depth set to 1.
 */
function make_makerspaces_secondary_menu_args( $args ) {

	if ( 'secondary' !== $args['theme_location'] ) {
		return $args;
	}

	$args['depth'] = 1;
	return $args;

}

add_filter( 'genesis_author_box_gravatar_size', 'make_makerspaces_author_box_gravatar' );
/**
 * Modifies size of the Gravatar in the author box.
 *
 * @since 2.2.3
 *
 * @param int $size Original icon size.
 * @return int Modified icon size.
 */
function make_makerspaces_author_box_gravatar( $size ) {

	return 90;

}

add_filter( 'genesis_comment_list_args', 'make_makerspaces_comments_gravatar' );
/**
 * Modifies size of the Gravatar in the entry comments.
 *
 * @since 2.2.3
 *
 * @param array $args Gravatar settings.
 * @return array Gravatar settings with modified size.
 */
function make_makerspaces_comments_gravatar( $args ) {

	$args['avatar_size'] = 60;
	return $args;

}

//=============================================
// Return field ID number based on the
// the Parameter Name for a specific form
//=============================================
function get_value_by_label($key, $form, $entry = array()) {
   $return = array();
   if(!isset($form['fields'])){
       error_log('error in get_value_by_label in wp-content/themes/make-makerspaces/functions/functions.php');
       error_log('$key='.$key);
       error_log('Entry');
       error_log(print_r($entry,true));
       error_log('Form');
       error_log(print_r($form,true));
   }
   foreach ($form['fields'] as &$field) {
      $lead_key = $field['inputName'];
      if ($lead_key == $key) {
         //is this a checkbox field?
         if ($field['type'] == 'checkbox') {
            $retArray = array();

            foreach ($field['inputs'] as $input) {
               if (isset($entry[$input['id']]) && $entry[$input['id']] == $input['label']) {
                  $retArray[] = array('id' => $input['id'], 'value' => $input['label']);
               }
            }
            $return = $retArray;
         } else {
            $return['id'] = $field['id'];
            if (!empty($entry)) {
               $return['value'] = $entry[$field['id']];
            } else {
               $return['value'] = '';
            }
         }
         return $return;
      }
   }
   return '';
}
