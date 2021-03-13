<?php
/**
 * @package Make Experiences
 * The parent theme functions are located at /buddyboss-theme/inc/theme/functions.php
 * Add your own functions at the bottom of this file.
 */


/****************************** THEME SETUP ******************************/

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
  load_theme_textdomain( 'buddyboss-theme', get_stylesheet_directory() . '/languages' );

  // Translate text from the CHILD theme only.
  // Change 'buddyboss-theme' instances in all child theme files to 'make_experiences'.
  // load_theme_textdomain( 'make_experiences', get_stylesheet_directory() . '/languages' );

}
add_action( 'after_setup_theme', 'make_experiences_languages' );

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Make Experiences  1.0.0
 */
function make_experiences_scripts_styles(){
	$my_theme = wp_get_theme();
	$my_version = $my_theme->get('Version');
	/**
	* Scripts and Styles loaded by the parent theme can be unloaded if needed
	* using wp_deregister_script or wp_deregister_style.
	*
	* See the WordPress Codex for more information about those functions:
	* http://codex.wordpress.org/Function_Reference/wp_deregister_script
	* http://codex.wordpress.org/Function_Reference/wp_deregister_style
	**/

	// Styles
	wp_enqueue_style('bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', '', 'all');
	wp_enqueue_style('fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.6/css/jquery.fancybox.min.css', '', 'all');
	### UNIVERSAL STYLES ###
    wp_enqueue_style('universal.css', content_url() . '/universal-assets/v1/css/universal.min.css', array(), $my_version);
    ### SUBTHEME STYLES ###
    wp_enqueue_style('make-co-style', get_stylesheet_directory_uri() . '/css/style.min.css', array(), $my_version);

	// Javascript
	wp_enqueue_script('fontawesome5-js', 'https://kit.fontawesome.com/7c927d1b5e.js', array(), '', true ); 
	// lib src packages up bootstrap, fancybox, jquerycookie etc
	wp_enqueue_script('lib-src-js', get_stylesheet_directory_uri().'/js/lib-src.min.js', array('jquery'), $my_version, true);
	wp_enqueue_script('universal', content_url() . '/universal-assets/v1/js/min/universal.min.js', array(), $my_version, true);
	wp_enqueue_script( 'make_experiences-js', get_stylesheet_directory_uri().'/js/min/scripts.min.js', array('jquery'), $my_version, true);

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
add_action( 'wp_enqueue_scripts', 'make_experiences_scripts_styles', 9999 );


/****************************** CUSTOM FUNCTIONS ******************************/

// Add your own custom functions here
remove_filter( 'wp_edit_nav_menu_walker', 'indeed_create_walker_menu_class' );

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

// Include all function files in the makerfaire/functions directory:
foreach (glob(get_stylesheet_directory() . '/functions/*.php') as $file) {
    include_once $file;
}

//* Disable email match check for all users - this error would keep users from registering users already in our system
add_filter( 'EED_WP_Users_SPCO__verify_user_access__perform_email_user_match_check', '__return_false' );

?>