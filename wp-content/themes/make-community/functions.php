<?php
remove_action('wp_head', 'wp_generator');

//universal functions
require_once(ABSPATH . 'wp-content/universal-assets/v2/universal-functions.php');

// parent theme styles - required
function onecommunity_child_enqueue_styles() {
	wp_enqueue_style('parent-theme', get_template_directory_uri() .'/style.css');
}
add_action('wp_enqueue_scripts', 'onecommunity_child_enqueue_styles');


// the default wp user created emails are bad, we got auth0 for that
function disable_new_user_notifications() {
    remove_action( 'register_new_user', 'wp_send_new_user_notifications' );
}
add_action( 'init', 'disable_new_user_notifications' );

// Include all function files in the /functions directory:
foreach (glob(get_stylesheet_directory() . '/functions/*.php') as $file) {
    include_once $file;
}

// Include all custom post type files in the /cpts directory:
foreach (glob(get_stylesheet_directory() . '/cpt/*.php') as $file) {
    include_once $file;
}

// Include all widget files in the /widgets directory:
foreach (glob(dirname(__FILE__) . '/widgets/*.php') as $file) {
    include_once $file;
}

// Include all widget files in the /widgets/classes directory:
foreach (glob(dirname(__FILE__) . '/widgets/classes/*.php') as $file) {
    include_once $file;
}

function remove_unnecessary_scripts() {
    wp_dequeue_style( 'font-awesome' );
    // Check if LearnDash exists to prevent fatal errors.
    if ( class_exists( 'SFWD_LMS' ) ) {
        if( !is_singular( array( 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz', 'sfwd-assignment' ) ) ) {
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
    if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
		$script = $scripts->registered['jquery'];
		if ( $script->deps ) {
			$script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
		}
	}	
}
add_action( 'wp_print_styles', 'remove_unnecessary_scripts', PHP_INT_MAX ); // we want this to happen absolutely last

// prevent password changed email
add_filter( 'send_password_change_email', '__return_false' );