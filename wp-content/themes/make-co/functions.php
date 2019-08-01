<?php
/**
 * Make - Co Theme
 *
 * This file adds functions to the Make - Co Theme.
 *
 * @package Make - Co
 * @author  Maker Media
 * @license GPL-2.0-or-later
 * @link    https://makermedia.com/
 */

// Starts the engine.
require_once get_template_directory() . '/lib/init.php';

// Defines the child theme (do not remove).
define( 'CHILD_THEME_NAME', 'Make - Co' );
define( 'CHILD_THEME_URL', 'https://makermedia.com' );

// Sets up the Theme.
require_once get_stylesheet_directory() . '/lib/theme-defaults.php';

add_action( 'after_setup_theme', 'make_learn_localization_setup' );
/**
 * Sets localization (do not remove).
 *
 * @since 1.0.0
 */
function make_learn_localization_setup() {

	load_child_theme_textdomain( 'make-co', get_stylesheet_directory() . '/languages' );

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

// Add that navwalker for the custom menus
require_once('lib/wp_bootstrap_navwalker.php');

// Include all function files in the makerfaire/functions directory:
foreach (glob(get_stylesheet_directory() . '/functions/*.php') as $file) {
   include_once $file;
}

add_action( 'after_setup_theme', 'genesis_child_gutenberg_support' );
/**
 * Adds Gutenberg opt-in features and styling.
 *
 * @since 2.7.0
 */
function genesis_child_gutenberg_support() { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- using same in all child themes to allow action to be unhooked.
	require_once get_stylesheet_directory() . '/lib/gutenberg/init.php';
}


add_action( 'wp_enqueue_scripts', 'make_learn_enqueue_scripts_styles' );
/**
 * Enqueues scripts and styles.
 *
 * @since 1.0.0
 */
function make_learn_enqueue_scripts_styles() {
	$my_theme = wp_get_theme();
   $my_version = $my_theme->get('Version');
	
   wp_enqueue_style('bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', '', 'all' );
	wp_enqueue_style('font-awesome-css', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css', '', 'all' );
	wp_enqueue_style('linearicons', 'https://cdn.linearicons.com/free/1.0.0/icon-font.min.css', '', 'all' );
	wp_enqueue_style('fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.6/css/jquery.fancybox.min.css', '', 'all');
	wp_enqueue_style( 'learn-ionicons', '//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css', array() );
	
	### GENESIS STYLES #####
	$parent_style = 'genesis-style'; 
   wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
	
	### UNIVERSAL STYLES ###
	wp_enqueue_style('universal.css', content_url() . '/universal-assets/v1/css/universal.min.css', array(), $my_version );
	
	### SUBTHEME STYLES ###
	wp_enqueue_style('make-co-style', get_stylesheet_directory_uri() . '/css/style.min.css', array(), $my_version );

	wp_enqueue_style(
		'make-co-fonts',
		'//fonts.googleapis.com/css?family=Source+Sans+Pro:400,400i,600,700',
		array(),
		$my_version
	);

	wp_enqueue_style( 'dashicons' );

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script(
		'make-co-responsive-menu',
		get_stylesheet_directory_uri() . "/js/responsive-menus{$suffix}.js",
		array( 'jquery' ),
		$my_version,
		true
	);

	wp_localize_script(
		'make-co-responsive-menu',
		'genesis_responsive_menu',
		make_learn_responsive_menu_settings()
	);
	
	
	wp_enqueue_script('auth0', 'https://cdn.auth0.com/js/auth0/9.3.1/auth0.min.js', array(), false, true );
	wp_enqueue_script('bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array('jquery'), '', true );
	wp_enqueue_script('fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.6/js/jquery.fancybox.min.js', array('jquery'), '', true );
	wp_enqueue_script('universal', content_url() . '/universal-assets/v1/js/min/universal.min.js', array(), $my_version, true );
	wp_enqueue_script('theme-js', get_stylesheet_directory_uri() . '/js/min/scripts.min.js', array('jquery'), $my_version, true);

	wp_enqueue_script(
		'make-co',
		get_stylesheet_directory_uri() . '/js/make-co.js',
		array( 'jquery' ),
		$my_version,
		true
	);
	
	wp_localize_script('make-co', 'ajax_object',
	  array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'home_url' => get_home_url(),
			'logout_nonce' => wp_create_nonce('ajax-logout-nonce'),
			'wp_user_email' => wp_get_current_user()->user_email,
	  )
	);

}

// remove the subtheme level style.css to use it as a version
remove_action( 'genesis_meta', 'genesis_load_stylesheet' );


/**
 * Defines responsive menu settings.
 *
 * @since 2.3.0
 */
function make_learn_responsive_menu_settings() {

	$settings = array(
		'mainMenu'         => __( 'Menu', 'make-co' ),
		'menuIconClass'    => 'dashicons-before dashicons-menu',
		'subMenu'          => __( 'Submenu', 'make-co' ),
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
add_theme_support( 'html5', genesis_get_config( 'html5' ) );

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

add_action( 'genesis_theme_settings_metaboxes', 'make_learn_remove_metaboxes' );
/**
 * Removes output of unused admin settings metaboxes.
 *
 * @since 2.6.0
 *
 * @param string $_genesis_admin_settings The admin screen to remove meta boxes from.
 */
function make_learn_remove_metaboxes( $_genesis_admin_settings ) {

	remove_meta_box( 'genesis-theme-settings-header', $_genesis_admin_settings, 'main' );
	remove_meta_box( 'genesis-theme-settings-nav', $_genesis_admin_settings, 'main' );

}

add_filter( 'genesis_customizer_theme_settings_config', 'make_learn_remove_customizer_settings' );
/**
 * Removes output of header and front page breadcrumb settings in the Customizer.
 *
 * @since 2.6.0
 *
 * @param array $config Original Customizer items.
 * @return array Filtered Customizer items.
 */
function make_learn_remove_customizer_settings( $config ) {

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

add_filter( 'wp_nav_menu_args', 'make_learn_secondary_menu_args' );
/**
 * Reduces secondary navigation menu to one level depth.
 *
 * @since 2.2.3
 *
 * @param array $args Original menu options.
 * @return array Menu options with depth set to 1.
 */
function make_learn_secondary_menu_args( $args ) {

	if ( 'secondary' !== $args['theme_location'] ) {
		return $args;
	}

	$args['depth'] = 1;
	return $args;

}

add_filter( 'genesis_author_box_gravatar_size', 'make_learn_author_box_gravatar' );
/**
 * Modifies size of the Gravatar in the author box.
 *
 * @since 2.2.3
 *
 * @param int $size Original icon size.
 * @return int Modified icon size.
 */
function make_learn_author_box_gravatar( $size ) {

	return 90;

}

add_filter( 'genesis_comment_list_args', 'make_learn_comments_gravatar' );
/**
 * Modifies size of the Gravatar in the entry comments.
 *
 * @since 2.2.3
 *
 * @param array $args Gravatar settings.
 * @return array Gravatar settings with modified size.
 */
function make_learn_comments_gravatar( $args ) {

	$args['avatar_size'] = 60;
	return $args;

}

/****************************************************
LOGIN FUNCTIONS 
****************************************************/


/* Old auth0 scripts
// redirect wp-login.php to the auth0 login page 

function load_auth0_js() {
    //auth0
    wp_enqueue_script('auth0', 'https://cdn.auth0.com/js/auth0/9.6.1/auth0.min.js', array(), false);
    wp_enqueue_script('auth0Login', get_stylesheet_directory_uri() . '/auth0/js/auth0login.js', array(), false);
}

add_action('login_enqueue_scripts', 'load_auth0_js', 10);

// Set up the Ajax Logout 
add_action('wp_ajax_mm_wplogout', 'MM_wordpress_logout');
add_action('wp_ajax_nopriv_mm_wplogout', 'MM_wordpress_logout');

function MM_wordpress_logout() {
    //check_ajax_referer( 'ajax-logout-nonce', 'ajaxsecurity' );
    wp_logout();
    ob_clean(); // probably overkill for this, but good habit
    wp_send_json_success();
}

add_action('wp_ajax_mm_wplogin', 'MM_WPlogin');
add_action('wp_ajax_nopriv_mm_wplogin', 'MM_WPlogin');

// allow capital letters in usernames
remove_action( 'sanitize_user', 'strtolower' );

// Set up the Ajax WP Login 
function MM_WPlogin() {
    //check_ajax_referer( 'ajax-login-nonce', 'ajaxsecurity' );
    global $wpdb; // access to the database
    //use auth0 plugin to log people into wp
    $a0_plugin = new WP_Auth0();
    $a0_options = WP_Auth0_Options::Instance();
    $users_repo = new WP_Auth0_UsersRepo($a0_options);
    $users_repo->init();

    $login_manager = new WP_Auth0_LoginManager($users_repo, $a0_options);
    $login_manager->init();

    //get the user information passed from auth0
    $userinput = filter_input_array(INPUT_POST);
    $userinfo = (object) $userinput['auth0_userProfile'];
    $userinfo->email_verified = true;
    $access_token = filter_input(INPUT_POST, 'auth0_access_token', FILTER_SANITIZE_STRING);
    $id_token = filter_input(INPUT_POST, 'auth0_id_token', FILTER_SANITIZE_STRING);

    if ($login_manager->login_user($userinfo, $id_token, $access_token)) {
        wp_send_json_success();
    } else {
        error_log('Failed login');
        error_log(print_r($userinput, TRUE));
        wp_send_json_error();
    }
} */


/**
 * These Functions Add and Verify the Invisible Google reCAPTCHA on Login
 * Normal users never see the wp-login.php page as they are forwarded to Auth0. 
 * this will stop spam bots from signing up
 */
/*

/*
function cookie_login_warning() { ?>
    <style type="text/css">
        .wp-core-ui #login { width: 80%; }
        #login::before {
            content: "We are unable to process your login as we have detected that you have cookies blocked. Please make sure cookies are enabled in your browser and try again.";
            text-align: center;
            font-size:42px;
            line-height: 46px;
        }
        #form-signin-wrapper {
            display: none;
        }
    </style>
    <?php

}

add_action('login_enqueue_scripts', 'cookie_login_warning');

add_action('login_enqueue_scripts', 'login_recaptcha_script');

function login_recaptcha_script() {
    wp_register_script('recaptcha_login', 'https://www.google.com/recaptcha/api.js');
    wp_enqueue_script('recaptcha_login');
}

add_action('login_form', 'display_recaptcha_on_login');

function display_recaptcha_on_login() {
    echo "<script>
            function onSubmit(token) {
                document.getElementById('loginform').submit();
            }
          </script>
            <button class='g-recaptcha' data-sitekey='6Lf_-kEUAAAAAHtDfGBAleSvWSynALMcgI1hc_tP' data-callback='onSubmit' data-size='invisible' style='display:none;'>Submit</button>";
}

add_filter('wp_authenticate_user', 'verify_recaptcha_on_login', 10, 2);

function verify_recaptcha_on_login($user, $password) {
    if (isset($_POST['g-recaptcha-response'])) {
        $response = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret=6Lf_-kEUAAAAAHtDfGBAleSvWSynALMcgI1hc_tP&response=' . $_POST['g-recaptcha-response']);
        $response = json_decode($response['body'], true);

        if (true == $response['success']) {
            return $user;
        } else {
            // FIXME: This one fires if your password is incorrect... Check if password was incorrect before returning this error...
            return new WP_Error( 'Captcha Invalid', __('<strong>ERROR</strong>: You are a bot') );
        }
    } else {
        return new WP_Error('Captcha Invalid', __('<strong>ERROR</strong>: You are a bot. If not then enable JavaScript.'));
    }
}

add_action( 'login_form_lostpassword', 'wpse45134_filter_option' );
add_action( 'login_form_retrievepassword', 'wpse45134_filter_option' );
add_action( 'login_form_register', 'wpse45134_filter_option' );

*/

/**
 * Simple wrapper around a call to add_filter to make sure we only
 * filter an option on the login page.
 */
 /*
function wpse45134_filter_option()
{
    // use __return_zero because pre_option_{$opt} checks
    // against `false`
    add_filter( 'pre_option_users_can_register', '__return_zero' );
} 
*/

function custom_login_stylesheets() {
    wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/css/style-login.css' );
	 wp_enqueue_style( 'custom-login', '/wp-content/universal-assets/v1/css/universal.css' );
}
//This loads the function above on the login page
add_action( 'login_enqueue_scripts', 'custom_login_stylesheets' );

add_action( 'login_header', function() {
    get_header();
});
add_action( 'login_footer', function() {
    get_footer();
});


/*
// this will add all users, but will have to be commented out so it doesn't run everytime a page is loaded
function buddypress_add_last_activity() {

  $members =  get_users( 'blog_id=1&fields=ID' );
  // $members =  get_users( 'fields=ID&role=subscriber' );
  
  foreach ( $members as $user_id ) {
	  //error_log(print_r(ihc_get_avatar_for_uid($user_id), TRUE));
     bp_update_user_last_activity( $user_id, bp_core_current_time() );
	  xprofile_set_field_data('Profile Photo', $user_id, ihc_get_avatar_for_uid($user_id) );
  }

}
add_action('bp_init', 'buddypress_add_last_activity' );
// just in case, prevent a billion activation emails from being sent
add_filter( 'bp_core_signup_send_activation_key', create_function('','return false;') );
*/


/* This won't be necessary, Alicia is going to rename the mm user logins
$blogusers = get_users( 'blog_id=1' );
// Array of WP_User objects.
foreach ( $blogusers as $user ) {
	//error_log(print_r($user, TRUE));
	$new_user_login = substr($user->user_login, 0, strpos($user->user_login, "@"));
	error_log($new_user_login);
	if($new_user_login && $new_user_login != "") {
		$wpdb->update($wpdb->users, array('user_login' => $new_user_login), array('ID' => $user->ID));
	}
}
*/