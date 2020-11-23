<?php

/**
 * Maker Campus Theme
 *
 * This file adds functions to the Maker Campus Theme.
 *
 * @package Maker Campus
 * @author  Make: Community
 * @license GPL-2.0-or-later
 * @link    https://make.co
 */
// Starts the engine.
require_once get_template_directory() . '/lib/init.php';

// Defines the child theme (do not remove).
define('CHILD_THEME_NAME', 'Make - Campus');
define('CHILD_THEME_URL', 'https://experiences.make.co');

// Sets up the Theme.
require_once get_stylesheet_directory() . '/lib/theme-defaults.php';

add_action('after_setup_theme', 'make_campus_localization_setup');

/**
 * Sets localization (do not remove).
 *
 * @since 1.0.0
 */
function make_campus_localization_setup() {
    load_child_theme_textdomain('make-campus', get_stylesheet_directory() . '/languages');
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

function make_campus_add_woocommerce_support() {
    add_theme_support('woocommerce');
}

add_action('after_setup_theme', 'make_campus_add_woocommerce_support');

// Add that navwalker for the custom menus
require_once('lib/wp_bootstrap_navwalker.php');

// FIX CONFLICT BETWEEN TRIBE EVENTS PLUGIN AND FRONT END IMAGE UPLOADER FOR BLOG POSTS
require_once(ABSPATH . 'wp-admin/includes/screen.php');

// Include all function files in the makerfaire/functions directory:
foreach (glob(get_stylesheet_directory() . '/functions/*.php') as $file) {
    include_once $file;
}

// Include all classes files in the makerfaire/classes directory:
foreach (glob(get_stylesheet_directory() . '/classes/*.php') as $file) {
    include_once $file;
}

add_action('after_setup_theme', 'genesis_child_gutenberg_support');

/**
 * Adds Gutenberg opt-in features and styling.
 *
 * @since 2.7.0
 */
function genesis_child_gutenberg_support() { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- using same in all child themes to allow action to be unhooked.
    require_once get_stylesheet_directory() . '/lib/gutenberg/init.php';
}

require_once(ABSPATH . 'wp-content/universal-assets/v1/universal-functions.php');

add_action('wp_enqueue_scripts', 'make_campus_enqueue_scripts', 0);

/**
 * Enqueues styles, run this before youzer scripts run
 *
 * @since 1.0.0
 */
function make_campus_enqueue_scripts() {
    $my_theme = wp_get_theme();
    $my_version = $my_theme->get('Version');
    $suffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';
    wp_enqueue_script(
            'make-campus-responsive-menu',
            get_stylesheet_directory_uri() . "/js/site/responsive-menus{$suffix}.js",
            array('jquery'),
            $my_version,
            true
    );

    wp_localize_script(
            'make-campus-responsive-menu',
            'genesis_responsive_menu',
            make_campus_responsive_menu_settings()
    );

    wp_enqueue_script('bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array('jquery'), '', true);
    wp_enqueue_script('fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.6/js/jquery.fancybox.min.js', array('jquery'), '', true);

    wp_enqueue_script('fontawesome5-js', 'https://kit.fontawesome.com/7c927d1b5e.js', array(), '', true);
    wp_enqueue_script('universal', content_url() . '/universal-assets/v1/js/min/universal.min.js', array(), $my_version, true);
    wp_enqueue_script('theme-js', get_stylesheet_directory_uri() . '/js/min/scripts.min.js', array('jquery'), $my_version, true);


    wp_localize_script('theme-js', 'ajax_object',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
            )
    );
	wp_localize_script('universal', 'ajax_object',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'home_url' => get_home_url(),
                'logout_nonce' => wp_create_nonce('ajax-logout-nonce'),
                'wp_user_email' => wp_get_current_user()->user_email,
                'wp_user_nicename' => wp_get_current_user()->user_nicename
            )
    );
}

add_action('wp_enqueue_scripts', 'make_campus_enqueue_styles');

/**
 * Enqueues styles.
 *
 * @since 1.0.0
 */
function make_campus_enqueue_styles() {
    $my_theme = wp_get_theme();
    $my_version = $my_theme->get('Version');

    wp_enqueue_style('bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', '', 'all');
    wp_enqueue_style('fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.6/css/jquery.fancybox.min.css', '', 'all');

    ### GENESIS STYLES #####
    $parent_style = 'genesis-style';
    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');

    ### UNIVERSAL STYLES ###
    wp_enqueue_style('universal.css', content_url() . '/universal-assets/v1/css/universal.min.css', array(), $my_version);

    ### SUBTHEME STYLES ###
    wp_enqueue_style('make-campus-style', get_stylesheet_directory_uri() . '/css/style.min.css', array(), $my_version);

    wp_enqueue_style(
            'make-campus-fonts',
            '//fonts.googleapis.com/css?family=Source+Sans+Pro:400,400i,600,700',
            array(),
            $my_version
    );

    wp_enqueue_style('dashicons');
}

// remove the subtheme level style.css to use it as a version
remove_action('genesis_meta', 'genesis_load_stylesheet');

add_filter('show_admin_bar', 'remove_admin_bar', PHP_INT_MAX);

// if you're not an admin, don't show the admin bar
function remove_admin_bar() {
    if (current_user_can('administrator')) {
        return true;
    }
    return false;
}

function make_campus_admin_scripts() {
    $my_theme = wp_get_theme();
    $my_version = $my_theme->get('Version');
    $suffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';
    wp_enqueue_script('universal', content_url() . '/universal-assets/v1/js/owl.carousel.js', array(), $my_version, true);
}

add_action('admin_enqueue_scripts', 'make_campus_admin_scripts');

// Remove Yoast columns
add_filter('manage_edit-tribe_events_columns', 'yoast_seo_admin_remove_columns');
add_filter('manage_edit-post_columns', 'yoast_seo_admin_remove_columns');
add_filter('manage_edit-page_columns', 'yoast_seo_admin_remove_columns');

function yoast_seo_admin_remove_columns($columns) {
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
 * Defines responsive menu settings.
 *
 * @since 2.3.0
 */
function make_campus_responsive_menu_settings() {

    $settings = array(
        'mainMenu' => __('Menu', 'make-campus'),
        'menuIconClass' => 'dashicons-before dashicons-menu',
        'subMenu' => __('Submenu', 'make-campus'),
        'subMenuIconClass' => 'dashicons-before dashicons-arrow-down-alt2',
        'menuClasses' => array(
            'combine' => array(
                '.nav-primary',
            ),
            'others' => array(),
        ),
    );

    return $settings;
}

// Adds support for HTML5 markup structure.
add_theme_support('html5', genesis_get_config('html5'));

// Adds support for accessibility.
add_theme_support('genesis-accessibility', genesis_get_config('accessibility'));

// Adds viewport meta tag for mobile browsers.
add_theme_support('genesis-responsive-viewport');

// Adds custom logo in Customizer > Site Identity.
add_theme_support('custom-logo', genesis_get_config('custom-logo'));

// Renames primary and secondary navigation menus.
add_theme_support('genesis-menus', genesis_get_config('menus'));

// Adds image sizes.
add_image_size('sidebar-featured', 75, 75, true);

// Adds support for after entry widget.
add_theme_support('genesis-after-entry-widget-area');

// Adds support for 3-column footer widgets.
add_theme_support('genesis-footer-widgets', 3);

// Removes header right widget area.
unregister_sidebar('header-right');

// Removes secondary sidebar.
unregister_sidebar('sidebar-alt');

// Removes site layouts.
genesis_unregister_layout('content-sidebar-sidebar');
genesis_unregister_layout('sidebar-content-sidebar');
genesis_unregister_layout('sidebar-sidebar-content');

// Removes output of primary navigation right extras.
remove_filter('genesis_nav_items', 'genesis_nav_right', 10, 2);
remove_filter('wp_nav_menu_items', 'genesis_nav_right', 10, 2);

add_action('genesis_theme_settings_metaboxes', 'make_campus_remove_metaboxes');

/**
 * Removes output of unused admin settings metaboxes.
 *
 * @since 2.6.0
 *
 * @param string $_genesis_admin_settings The admin screen to remove meta boxes from.
 */
function make_campus_remove_metaboxes($_genesis_admin_settings) {
    remove_meta_box('genesis-theme-settings-header', $_genesis_admin_settings, 'main');
    remove_meta_box('genesis-theme-settings-nav', $_genesis_admin_settings, 'main');
}

add_filter('genesis_customizer_theme_settings_config', 'make_campus_remove_customizer_settings');

/**
 * Removes output of header and front page breadcrumb settings in the Customizer.
 *
 * @since 2.6.0
 *
 * @param array $config Original Customizer items.
 * @return array Filtered Customizer items.
 */
function make_campus_remove_customizer_settings($config) {
    unset($config['genesis']['sections']['genesis_header']);
    unset($config['genesis']['sections']['genesis_breadcrumbs']['controls']['breadcrumb_front_page']);
    return $config;
}

// Displays custom logo.
add_action('genesis_site_title', 'the_custom_logo', 0);

// Repositions primary navigation menu.
remove_action('genesis_after_header', 'genesis_do_nav');
add_action('genesis_header', 'genesis_do_nav', 12);

// Repositions the secondary navigation menu.
remove_action('genesis_after_header', 'genesis_do_subnav');
add_action('genesis_footer', 'genesis_do_subnav', 10);

add_filter('genesis_author_box_gravatar_size', 'make_campus_author_box_gravatar');
/* Modifies size of the Gravatar in the author box. */

function make_campus_author_box_gravatar($size) {
    return 90;
}

add_filter('genesis_comment_list_args', 'make_campus_comments_gravatar');
/* Modifies size of the Gravatar in the entry comments. */

function make_campus_comments_gravatar($args) {
    $args['avatar_size'] = 60;
    return $args;
}

// Function to change email address from wordpress to webmaster
function wpb_sender_email($original_email_address) {
    return 'webmaster@make.co';
}

// Function to change sender name
function wpb_sender_name($original_email_from) {
    return 'Make: Community';
}

// Hooking up our functions to WordPress filters 
add_filter('wp_mail_from', 'wpb_sender_email');
add_filter('wp_mail_from_name', 'wpb_sender_name');

// change the subject too
function makeco_password_subject_filter($old_subject) {
    $subject = 'Make: Community - Password Reset';
    return $subject;
}

add_filter('retrieve_password_title', 'makeco_password_subject_filter', 10, 1);

function custom_login_stylesheets() {
    wp_enqueue_style('custom-login', '/wp-content/themes/make-campus/css/style-login.css');
    wp_enqueue_style('custom-login', '/wp-content/universal-assets/v1/css/universal.css');
}

// style the login page and give it the universal header and footer
add_action('login_enqueue_scripts', 'custom_login_stylesheets');

add_action('login_header', function() {
    get_header();
});
add_action('login_footer', function() {
    get_footer();
});


add_filter('acf/load_value/type=checkbox', function($value, $post_id, $field) {
    // Value should be an array, not a string
    if (is_string($value)) {
        $value = get_post_meta($post_id, $field['name'], false);
    }
    return $value;
}, 10, 3);


add_filter('gform_ajax_spinner_url', 'spinner_url', 10, 2);

function spinner_url($image_src, $form) {
    return "/wp-content/universal-assets/v1/images/makey-spinner.gif";
}

function smartTruncate($string, $limit, $break = ".", $pad = "...") {
    // return with no change if string is shorter than $limit
    if (strlen($string) <= $limit)
        return $string;
    // is $break present between $limit and the end of the string?
    if (false !== ($breakpoint = strpos($string, $break, $limit))) {
        if ($breakpoint < strlen($string) - 1) {
            $string = substr($string, 0, $breakpoint) . $pad;
        }
    }

    return $string;
}

//check if gravity view plugin is active
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active('gravityview/gravityview.php') ) {    
    $gv_fe = GravityView_frontend::getInstance();
    remove_filter('parse_query', array($gv_fe, 'parse_query_fix_frontpage'));
}

function parse_yturl($url) {
    $pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
    preg_match($pattern, $url, $matches);
    return (isset($matches[1])) ? $matches[1] : false;
}

// block wp-admin access for 
function wpabsolute_block_users_backend() {
    $user = wp_get_current_user();
    if (is_admin() && !wp_doing_ajax() & in_array('event_facilitator', (array) $user->roles)) {
        wp_redirect(home_url());
        exit;
    }
}

add_action('init', 'wpabsolute_block_users_backend');

//remove edit entry event links from 'atendees report' in facilitator report
add_action('tribe_tickets_attendees_event_action_links', function() {return '';});