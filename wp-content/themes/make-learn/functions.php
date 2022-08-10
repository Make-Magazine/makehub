<?php
/**
 * @package Make Learn
 * The parent theme functions are located at /buddyboss-theme/inc/theme/functions.php
 * Add your own functions at the bottom of this file.
 */
/* * **************************** THEME SETUP ***************************** */

require_once(ABSPATH . 'wp-content/universal-assets/v1/universal-functions.php');

// Defines the child theme (do not remove).
define('CHILD_THEME_NAME', 'Make - Learn');
define('CHILD_THEME_URL', 'https://learn.make.co');

/**
 * Sets up theme for translation
 *
 * @since Make Learn 1.0.0
 */
function make_learn_languages() {
    /**
     * Makes child theme available for translation.
     * Translations can be added into the /languages/ directory.
     */
    // Translate text from the PARENT theme.
    load_theme_textdomain('buddyboss-theme', get_stylesheet_directory() . '/languages');

    // Translate text from the CHILD theme only.
    // Change 'buddyboss-theme' instances in all child theme files to 'make-learn'.
}

add_action('after_setup_theme', 'make_learn_languages');

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Make Learn  1.0.0
 */
function make_learn_scripts_styles() {
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
    // Styles
    wp_enqueue_style('bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', '', 'all');
    wp_enqueue_style('fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.6/css/jquery.fancybox.min.css', '', 'all');
    ### SUBTHEME STYLES ###
    wp_enqueue_style('make-co-style', get_stylesheet_directory_uri() . '/css/style.min.css', array(), $my_version);

    // Javascript
    wp_enqueue_script('fontawesome5-js', 'https://kit.fontawesome.com/7c927d1b5e.js', array(), '', true);

    // lib src packages up bootstrap, fancybox, jquerycookie etc
    wp_enqueue_script('built-libs-js', get_stylesheet_directory_uri() . '/js/min/built-libs.min.js', array('jquery'), $my_version, true);
    wp_enqueue_script('make_learn-js', get_stylesheet_directory_uri() . '/js/min/scripts.min.js', array('jquery'), $my_version, true);
}

add_action('wp_enqueue_scripts', 'make_learn_scripts_styles', 9999);

add_action('admin_enqueue_scripts', 'load_admin_styles');

function load_admin_styles() {
    wp_enqueue_style('admin_css', get_stylesheet_directory_uri() . '/css/admin-styles.css', false, '1.0.0');
}

/* * **************************** CUSTOM FUNCTIONS ***************************** */

// Add your own custom functions here
remove_filter('wp_edit_nav_menu_walker', 'indeed_create_walker_menu_class');

//clean up the top black nav bar in admin

function make_learn_remove_toolbar_node($wp_admin_bar) {
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

add_action('admin_bar_menu', 'make_learn_remove_toolbar_node', 999);

// Include all function files in the make-learn/functions directory:
foreach (glob(get_stylesheet_directory() . '/functions/*.php') as $file) {
    include_once $file;
}

// Include all class files in the make-learn/classes directory:
foreach (glob(dirname(__FILE__) . '/classes/*.php') as $file) {
    include_once $file;
}
//include any subfolders like 'widgets'
foreach (glob(dirname(__FILE__) . '/classes/*/*.php') as $file) {
    include_once $file;
}

add_filter('gform_ajax_spinner_url', 'spinner_url', 10, 2);

function spinner_url($image_src, $form) {
    return "/wp-content/universal-assets/v1/images/makey-spinner.gif";
}

function parse_yturl($url) {
    $pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
    preg_match($pattern, $url, $matches);
    return (isset($matches[1])) ? $matches[1] : false;
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
    if (isset($post)) {
        if ($post->post_name) {
            $classes[] = $post->post_type . '-' . $post->post_name;
        } else {
            $classes[] = $post->post_type . '-' . str_replace("/", "-", trim($_SERVER['REQUEST_URI'], '/'));
        }
        // let's see if your the group owner and what kind of group it is (hidden, private, etc)
        if (bp_is_groups_component()) {
            $classes[] = 'group-' . groups_get_group(array('group_id' => bp_get_current_group_id()))->status;
            if (current_user_can('manage_options') || groups_is_user_mod(get_current_user_id(), bp_get_current_group_id()) || groups_is_user_admin(get_current_user_id(), bp_get_current_group_id())) {
                $classes[] = 'my-group';
            }
        }
        return $classes;
    }
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

// limit default site search to learndash categories
function searchfilter($query) {
    if ($query->is_search && !is_admin()) {
        $query->set('post_type', array('sfwd-courses', 'sfwd-lessons', 'sfwd-quiz', 'sfwd-topic', 'sfwd-certificates'));
    }
    return $query;
}

add_filter('pre_get_posts', 'searchfilter');



add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}

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

/* This allows us to send elementor styled pages to other blogs */
add_action("rest_api_init", function () {
    register_rest_route(
            "MakeLearn/v1"
            , "/pages/(?P<id>\d+)/contentElementor"
            , [
        "methods" => "GET",
        'permission_callback' => '__return_true',
        "callback" => function (\WP_REST_Request $req) {

            $contentElementor = "";

            if (class_exists("\\Elementor\\Plugin")) {
                $post_ID = $req->get_param("id");

                $pluginElementor = \Elementor\Plugin::instance();
                $contentElementor = $pluginElementor->frontend->get_builder_content($post_ID);
            }


            return $contentElementor;
        },
            ]
    );
});
?>
