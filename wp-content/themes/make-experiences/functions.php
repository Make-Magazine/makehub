<?php
/**
 * @package Make Experiences
 * The parent theme functions are located at /buddyboss-theme/inc/theme/functions.php
 * Add your own functions at the bottom of this file.
 */
/* * **************************** THEME SETUP ***************************** */

require_once(ABSPATH . 'wp-content/universal-assets/v1/universal-functions.php');

// Defines the child theme (do not remove).
define('CHILD_THEME_NAME', 'Make - Experiences');
define('CHILD_THEME_URL', 'https://experiences.make.co');

/**
 * Sets up theme for translation
 *
 * @since Make Experiences 1.0.0
 */
function make_experiences_languages() {
    /**
     * Makes child theme available for translation.
     * Translations can be added into the /languages/ directory.
     */
    // Translate text from the PARENT theme.
    load_theme_textdomain('buddyboss-theme', get_stylesheet_directory() . '/languages');

    // Translate text from the CHILD theme only.
    // Change 'buddyboss-theme' instances in all child theme files to 'make_experiences'.
    // load_theme_textdomain( 'make_experiences', get_stylesheet_directory() . '/languages' );
}
add_action('after_setup_theme', 'make_experiences_languages');

function remove_unnecessary_scripts() {
    wp_dequeue_style( 'font-awesome' );
	// if it's not a buddypress page, no need for these scripts
    if ( is_page() && !is_buddypress() ) {
        remove_action( 'wp_enqueue_scripts', 'buddyboss_theme_scripts' );
        wp_dequeue_style( 'bp-nouveau' );
        wp_deregister_script('bp-jquery-query');
        wp_deregister_script('bp-confirm');
        wp_deregister_script('bp-nouveau-magnific-popup');
        wp_dequeue_style('buddyboss-theme-plugins');
        wp_dequeue_style('buddyboss-theme-buddypress');
        wp_dequeue_style('buddyboss-theme-select2-css');
        wp_dequeue_style('buddyboss-theme-magnific-popup-css');
        wp_dequeue_style('buddyboss-theme-template');
        
    }
    // Check if LearnDash exists to prevent fatal errors.
    if ( class_exists( 'SFWD_LMS' ) ) {
        if( !is_singular( array( ‘sfwd-courses’, ‘sfwd-lessons’, ‘sfwd-topic’, ‘sfwd-quiz’, ‘sfwd-assignment’ ) ) ) {
            // Remove Default LearnDash Styles;
            wp_dequeue_style( 'learndash_lesson_video-css' );
            wp_dequeue_style( 'ldvc-css' );
            wp_dequeue_style( 'learndash_quiz_front_css' );
            wp_dequeue_style( 'learndash-front' );
            wp_deregister_style( 'learndash-front' );
            wp_dequeue_style( 'learndash-front' ); 
            wp_deregister_script( 'learndash-front' );
            wp_dequeue_script( 'learndash-front' );
            wp_dequeue_script( 'buddyboss-theme-learndash-js' );
            wp_dequeue_style( 'buddyboss-theme-learndash' );
        }
    }
    // WooCommerce.
    if ( function_exists( 'WC' ) ) {
        if(!is_woocommerce()) {
            wp_dequeue_style('buddyboss-theme-woocommerce');
        }
    }
	
}
add_action( 'wp_print_styles', 'remove_unnecessary_scripts', PHP_INT_MAX ); // we want this to happen absolutely last

function remove_jquery_migrate( $scripts ) {
	if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
		$script = $scripts->registered['jquery'];
		if ( $script->deps ) {
			$script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
		}
	}
}
add_action( 'wp_print_styles', 'remove_jquery_migrate' );

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Make Experiences  1.0.0
 */
function make_experiences_scripts_styles() {
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
    wp_enqueue_style('make-co-style', get_stylesheet_directory_uri() . '/css/style.min.css', array(), $my_version);

    // lib src packages
    wp_enqueue_script('built-libs-js', get_stylesheet_directory_uri() . '/js/min/built-libs.min.js', array('jquery'), $my_version, true);
    wp_enqueue_script('make_experiences-js', get_stylesheet_directory_uri() . '/js/min/scripts.min.js', array('jquery'), $my_version, true);

	if ( is_page_template('page-media-center.php') ) {
        wp_enqueue_script( 'jquery-ui-tabs' );
    }
}
add_action('wp_enqueue_scripts', 'make_experiences_scripts_styles', 9999);

function load_admin_styles() {
  wp_register_style( 'admin_css', get_stylesheet_directory_uri() . '/css/admin-styles.css', false, '1.0.4' );
	wp_enqueue_style( 'admin_css' );
}
add_action('admin_enqueue_scripts', 'load_admin_styles');


/* * **************************** CUSTOM FUNCTIONS ***************************** */
remove_filter('wp_edit_nav_menu_walker', 'indeed_create_walker_menu_class');

// Include all function files in the make-experiences/functions directory:
foreach (glob(get_stylesheet_directory() . '/functions/*.php') as $file) {
    include_once $file;
}

// Include all custom post type files in the make-experiences/cpts directory:
foreach (glob(get_stylesheet_directory() . '/cpt/*.php') as $file) {
    include_once $file;
}

// Include all class files in the make-experiences/classes directory:
foreach (glob(dirname(__FILE__) . '/classes/*.php') as $file) {
    include_once $file;
}
//include any subfolders like 'widgets'
foreach (glob(dirname(__FILE__) . '/classes/*/*.php') as $file) {
    include_once $file;
}

//* Disable email match check for all users - this error would keep users from registering users already in our system
add_filter('EED_WP_Users_SPCO__verify_user_access__perform_email_user_match_check', '__return_false');

function parse_yturl($url) {
    $pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
    preg_match($pattern, $url, $matches);
    return (isset($matches[1])) ? $matches[1] : false;
}

//do not display doing it wrong errors
add_filter('doing_it_wrong_trigger_error', function () {
    return false;
}, 10, 0);

function add_slug_body_class($classes) {
    global $post;
    global $bp;
    if (isset($post)) {
        if ($post->post_name) {
            $classes[] = $post->post_type . '-' . $post->post_name;
            // any query string becomes a body class too
            parse_str($_SERVER['QUERY_STRING'], $query_array);
            foreach($query_array as $key => $value) {
                $classes[] = $key . "-" . $value;
            }
        } else {
            $classes[] = $post->post_type . '-' . str_replace("/", "-", trim($_SERVER['REQUEST_URI'], '/'));
        }
		// For Course and Lessons, check for the Primary category and add it to the body class if found
		if ( $post->post_type == "sfwd-courses") {
			$ld_course_category = get_post_primary_category($post->ID, 'ld_course_category');
			if(isset($ld_course_category['primary_category']->slug)) {
				$classes[] = 'cat-' . $ld_course_category['primary_category']->slug;
			}
		} else if ( $post->post_type == "sfwd-lessons") {
			$ld_lesson_category = get_post_primary_category($post->ID, 'ld_lesson_category');
			if(isset($ld_lesson_category['primary_category']->slug)) {
				$classes[] = 'cat-' . $ld_lesson_category['primary_category']->slug;
			}
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

// Disables the block editor from managing widgets in the Gutenberg plugin.
add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
// Disables the block editor from managing widgets.
add_filter( 'use_widgets_block_editor', '__return_false' );

/*
 * Override any of the translation files if we need to change language
 *
 * @param $translation The current translation
 * @param $text The text being translated
 * @param $domain The domain for the translation
 * @return string The translated / filtered text.
 */
function filter_gettext($translation, $text, $domain) {
    $translations = get_translations_for_domain($_SERVER['HTTP_HOST']);
    switch ($text) {
        case 'Nickname':
            return $translations->translate('Display Name');
            break;
    }
    return $translation;
}
add_filter('gettext', 'filter_gettext', 10, 4);

// Disable automatic plugin updates
add_filter( 'auto_update_plugin', '__return_false' );

// Set Buddypress emails from and reply to
add_filter( 'bp_email_set_reply_to', function( $retval ) {
    return new BP_Email_Recipient( 'make@make.co' );
} );
add_filter( 'wp_mail_from', function( $email ) {
    return 'make@make.co';
}, 10, 3 );
add_filter( 'wp_mail_from_name', function( $name ) {
    return 'Make: Community';
}, 10, 3 );

// prevent password changed email
add_filter( 'send_password_change_email', '__return_false' );

// the default wp user created emails are bad, we got auth0 for that
function disable_new_user_notifications() {
    remove_action( 'register_new_user', 'wp_send_new_user_notifications' );
}
add_action( 'init', 'disable_new_user_notifications' );


?>
