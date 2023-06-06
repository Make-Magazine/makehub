<?php
/**
 * @package Make Buddyboss Basic
 * The parent theme functions are located at /buddyboss-theme/inc/theme/functions.php
 * Add your own functions at the bottom of this file.
 */
/* * **************************** THEME SETUP ***************************** */

require_once(ABSPATH . 'wp-content/universal-assets/v2/universal-functions.php');

// Defines the child theme (do not remove).
define('CHILD_THEME_NAME', 'Make - Buddyboss Basic');
define('CHILD_THEME_URL', 'https://makershed.make.co');

/**
 * Sets up theme for translation
 *
 * @since Make Experiences 1.0.0
 */
function make_buddyboss_basic_languages() {
    /**
     * Makes child theme available for translation.
     * Translations can be added into the /languages/ directory.
     */
    // Translate text from the PARENT theme.
    load_theme_textdomain('buddyboss-theme', get_stylesheet_directory() . '/languages');

    // Translate text from the CHILD theme only.
    // Change 'buddyboss-theme' instances in all child theme files to 'make_buddyboss_basic'.
    // load_theme_textdomain( 'make_buddyboss_basic', get_stylesheet_directory() . '/languages' );
}
add_action('after_setup_theme', 'make_buddyboss_basic_languages');

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Make Buddyboss Basic  1.0.0
 */
function make_buddyboss_basic_scripts_styles() {
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
    wp_enqueue_script('make_buddyboss_basic-js', get_stylesheet_directory_uri() . '/js/min/scripts.min.js', array('jquery'), $my_version, true);
}
add_action('wp_enqueue_scripts', 'make_buddyboss_basic_scripts_styles', 9999);

function load_admin_styles() {
  wp_register_style( 'admin_css', get_stylesheet_directory_uri() . '/css/admin-styles.css', false, '1.0.4' );
	wp_enqueue_style( 'admin_css' );
}
add_action('admin_enqueue_scripts', 'load_admin_styles');

// fight back against buddyboss ruining our accessibility
if ( ! function_exists( 'buddyboss_theme_viewport_meta' ) ) {
    add_action( 'init', 'remove_bb_actions');
    function remove_bb_actions() {
        remove_action( 'wp_head', 'buddyboss_theme_viewport_meta' );
    }
    add_action( 'wp_head', 'buddyboss_theme_viewport_meta' );
    /**
     * Add a viewport meta.
     */
    function buddyboss_theme_viewport_meta_custom() {
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=1" />';
    }
    add_action( 'wp_head', 'buddyboss_theme_viewport_meta_custom' );
}


/* * **************************** CUSTOM FUNCTIONS ***************************** */
remove_filter('wp_edit_nav_menu_walker', 'indeed_create_walker_menu_class');

// Include all function files in the make-experiences/functions directory:
foreach (glob(get_stylesheet_directory() . '/functions/*.php') as $file) {
    include_once $file;
}

// Include all custom post type files in the make-makercamp/cpt directory:
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
		// add page template class to page
		if (!empty(get_post_meta(get_the_ID(), '_wp_page_template', true))) {
			// Remove the `template-` prefix and get the name of the template without the file extension.
			$templateName = basename(get_page_template_slug(get_the_ID()));
			$templateName = "page-template-" . str_replace(".php", "", $templateName);

			$classes[] = $templateName;
		}
		if(current_user_can('administrator')) {
			$classes[] = "admin-bar";
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

        return $classes;
    }
}
add_filter('body_class', 'add_slug_body_class');

function tf_check_user_role( $roles ) {
    /*@ Check user logged-in */
    if ( is_user_logged_in() ) {
        /*@ Get current logged-in user data */
        $user = wp_get_current_user();
        /*@ Fetch only roles */
        $currentUserRoles = $user->roles;
        /*@ Intersect both array to check any matching value */
        $isMatching = array_intersect( $currentUserRoles, $roles);
        $response = false;
        /*@ If any role matched then return true */
        if ( !empty($isMatching) ) :
            $response = true;
        endif;
        return $response;
    } else {
		return true;
	}
}
$roles = [ 'customer', 'subscriber' ];
if ( tf_check_user_role($roles) ) {
    add_filter('show_admin_bar', '__return_false', 999);
}

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

// remove svg code that are appearing in share preview excerpts
add_filter('wpseo_opengraph_desc','custom_meta');
function custom_meta( $desc ){
    $desc = preg_replace("/\.cls-\b([0-9]|[1-9][0-9])\b\{(.*?)\}/", "", $desc);
    return $desc;
}

?>
