<?php
/**
 * @package Maker Camp
 */
/* * **************************** THEME SETUP ***************************** */

require_once(ABSPATH . 'wp-content/universal-assets/v2/universal-functions.php');

// Defines the child theme (do not remove).
define('CHILD_THEME_NAME', 'Make - Maker Camp');
define('CHILD_THEME_URL', 'https://makercamp.make.co');

/**
 * Sets up theme for translation
 *
 * @since Maker Camp 1.0.0
 */
function maker_camp_languages() {
    /**
     * Makes child theme available for translation.
     * Translations can be added into the /languages/ directory.
     */
    // Translate text from the PARENT theme.
    load_theme_textdomain('onecommunity', get_stylesheet_directory() . '/languages');
}

add_action('after_setup_theme', 'maker_camp_languages');

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Maker Camp 1.0.0
 */
function maker_camp_scripts_styles() {
    $my_theme = wp_get_theme();
    $my_version = $my_theme->get('Version');
    /**
     * Scripts and Styles loaded by the parent theme can be unloaded if needed
     * using wp_deregister_script or wp_deregister_style.
     *
     * See the WordPress Codex for more information about those functions:
     * http://codex.wordpress.org/Function_Reference/wp_deregister_script
     * http://codex.wordpress.org/Function_Reference/wp_deregister_style
     * */

    ### SUBTHEME STYLES ###
    wp_enqueue_style('maker_camp-style', get_stylesheet_directory_uri() . '/css/style.min.css', array(), $my_version);

    // lib src packages up bootstrap, jquerycookie etc
    wp_enqueue_script('built-libs-js', get_stylesheet_directory_uri() . '/js/min/built-libs.min.js', array('jquery'), $my_version, true);
    wp_enqueue_script('maker_camp-js', get_stylesheet_directory_uri() . '/js/min/scripts.min.js', array('jquery'), $my_version, true);
}

add_action('wp_enqueue_scripts', 'maker_camp_scripts_styles', 9999);

// Build Admin styles for
function load_admin_styles() {
	wp_register_style( 'admin_css', get_stylesheet_directory_uri() . '/css/admin-styles.css', false, '1.0.4' );
	wp_enqueue_style( 'admin_css' );
}
add_action('admin_enqueue_scripts', 'load_admin_styles');

/* * **************************** CUSTOM FUNCTIONS ***************************** */

// Add your own custom functions here
remove_filter('wp_edit_nav_menu_walker', 'indeed_create_walker_menu_class');

//clean up the top black nav bar in admin

function experiences_remove_toolbar_node($wp_admin_bar) {
    $wp_admin_bar->remove_node('wp-logo');
    $wp_admin_bar->remove_node('new-content');
    $wp_admin_bar->remove_node('updates');
    $wp_admin_bar->remove_node('customize');
    $wp_admin_bar->remove_node('comments');
    $wp_admin_bar->remove_node('bp-notifications'); //buddypress notifications
    $wp_admin_bar->remove_node('uap_dashboard_menu'); //ultimate affiliate pro
    $wp_admin_bar->remove_node('elementor_inspector'); // elementor debugger
    $wp_admin_bar->remove_node('essb'); // easy social share buttons
}

add_action('admin_bar_menu', 'experiences_remove_toolbar_node', 999);

// Include all function files in the make-makercamp/functions directory:
foreach (glob(get_stylesheet_directory() . '/functions/*.php') as $file) {
    include_once $file;
}

// Include all custom post type files in the make-makercamp/cpt directory:
foreach (glob(get_stylesheet_directory() . '/cpt/*.php') as $file) {
    include_once $file;
}

// Include all class files in the make-makercamp/classes directory:
foreach (glob(dirname(__FILE__) . '/classes/*.php') as $file) {
    include_once $file;
}
//include any subfolders like 'widgets'
foreach (glob(dirname(__FILE__) . '/classes/*/*.php') as $file) {
    include_once $file;
}

add_filter('gform_ajax_spinner_url', 'spinner_url', 10, 2);

function spinner_url($image_src, $form) {
    return "/wp-content/universal-assets/v2/images/makey-spinner.gif";
}

function parse_yturl($url) {
    $pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
    preg_match($pattern, $url, $matches);
    return (isset($matches[1])) ? $matches[1] : false;
}

// get the parent of a given taxonomy term
function get_term_top_most_parent( $term, $taxonomy ) {
    // Start from the current term
    $parent  = get_term( $term, $taxonomy );
    // Climb up the hierarchy until we reach a term with parent = '0'
    while ( $parent->parent != '0' ) {
        $term_id = $parent->parent;
        $parent  = get_term( $term_id, $taxonomy);
    }
    return $parent;
}

function validate_url($url) {
    if (preg_match('/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i', $url)) {
        $path = parse_url($url, PHP_URL_PATH);
        $encoded_path = array_map('urlencode', explode('/', $path));
        $url = str_replace($path, implode('/', $encoded_path), $url);
        return filter_var($url, FILTER_VALIDATE_URL) ? true : false;
    } else {
        return false;
    }
    return true;
}

////////////////////////////////////////////////////////////////////
// Use Jetpack Photon if it exists, else use original photo
////////////////////////////////////////////////////////////////////

function get_resized_remote_image_url($url, $width, $height, $escape = true) {
    if (class_exists('Jetpack') && Jetpack::is_module_active('photon')) {
        $width = (int) $width;
        $height = (int) $height;
        // Photon doesn't support redirects, so help it out by doing http://foobar.wordpress.com/files/ to http://foobar.files.wordpress.com/
        if (function_exists('new_file_urls'))
            $url = new_file_urls($url);

        $thumburl = jetpack_photon_url($url, array(
            'resize' => array($width, $height),
            'strip' => 'all',
        ));
        return ($escape) ? esc_url($thumburl) : $thumburl;
    } else {
        return $url;
    }
}

/**
 * Jetpack Photon fit image
 */
function get_fitted_remote_image_url($url, $width, $height, $escape = true) {
    if (class_exists('Jetpack') && Jetpack::is_module_active('photon')) {
        $width = (int) $width;
        $height = (int) $height;

        // Photon doesn't support redirects, so help it out by doing http://foobar.wordpress.com/files/ to http://foobar.files.wordpress.com/
        if (function_exists('new_file_urls'))
            $url = new_file_urls($url);

        $thumburl = jetpack_photon_url($url, array(
            'fit' => array($width, $height),
            'strip' => 'all'
        ));

        return ($escape) ? esc_url($thumburl) : $thumburl;
    } else {
        return $url;
    }
}

function first_sentence($content) {
    $pos = strpos($content, '.');
    return substr($content, 0, $pos+1);
}

function get_first_image_url($html) {
    if (preg_match('/<img.+?src="(.+?)"/', $html, $matches)) {
        return $matches[1];
    } else
        return get_stylesheet_directory_uri() . "/images/default-related-article.jpg";
}

function featuredtoRSS($content) {
    global $post;
    if (has_post_thumbnail($post->ID)) {
        $content = '<div>' . get_the_post_thumbnail($post->ID, 'medium', array('style' => 'margin-bottom: 15px;')) . '</div>' . $content;
    }
    return $content;
}

add_filter('the_excerpt_rss', 'featuredtoRSS', 20, 1);
add_filter('the_content_feed', 'featuredtoRSS', 20, 1);

function add_slug_body_class($classes) {
    global $post;
    global $bp;

	$classes = array();
	// not sure why this isn't getting added normally
	if( is_user_logged_in() ) {
		$classes[] = "logged-in";
	}
	if(current_user_can('administrator')) {
		$classes[] = "admin-bar";
	}
    if (isset($post)) {
        if ($post->post_name) {
            $classes[] = $post->post_type . '-' . $post->post_name;
        } else {
            $classes[] = $post->post_type . '-' . str_replace("/", "-", trim($_SERVER['REQUEST_URI'], '/'));
        }
    }
	if ( is_singular( 'user-projects' ) ) { // Change 'custom_post_type_slug' to your Custom Post Type slug. For example: 'tutorials'.
        $classes[] = 'single-user-project';
    }
	return $classes;
}

add_filter('body_class', 'add_slug_body_class');

// don't lazyload on the project print template
function lazyload_exclude() {
    if (is_page_template('project-print-template.php') == true) {
        return false;
    } else {
        return true;
    }
}

add_filter('lazyload_is_enabled', 'lazyload_exclude', 15);
add_filter('wp_lazy_loading_enabled', 'lazyload_exclude', 10, 3);
add_filter('do_rocket_lazyload', 'lazyload_exclude', 10, 3);

function remove_admin_bar() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'remove_admin_bar');

define('BP_AVATAR_URL', '/wp-content/uploads/');

function bpdev_fix_avatar_dir_path($path) {
    if (is_multisite())
        $path = ABSPATH . 'wp-content/uploads/';
    return $path;
}

add_filter('bp_core_avatar_upload_path', 'bpdev_fix_avatar_dir_path', 1);

//fix the upload dir url
function bpdev_fix_avatar_dir_url($url) {
    if (is_multisite())
        $url = network_home_url('/wp-content/uploads');
    return $url;
}

add_filter('bp_core_avatar_url', 'bpdev_fix_avatar_dir_url', 1);

/*-------------------------------------
 Move Yoast to the Bottom
---------------------------------------*/
function yoasttobottom() {
	return 'low';
}
add_filter( 'wpseo_metabox_prio', 'yoasttobottom');

// add the ability to add tags or categories to pages
function register_taxonomies() {
    // Add tag metabox to page
    register_taxonomy_for_object_type('post_tag', 'page');
    // Add category metabox to page
    register_taxonomy_for_object_type('category', 'page');
}

add_action( 'init', 'register_taxonomies' );

?>
