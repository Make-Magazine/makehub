<?php
remove_action('wp_head', 'wp_generator');

//universal functions
require_once(ABSPATH . 'wp-content/universal-assets/v2/universal-functions.php');

// parent theme styles - required
function onecommunity_child_enqueue_styles() {
	wp_enqueue_style('parent-theme', get_template_directory_uri() .'/style.css');
}
add_action('wp_enqueue_scripts', 'onecommunity_child_enqueue_styles');


//parent theme scripts - required
function onecommunity_js_functions_child() {
	wp_enqueue_script( 'onecommunity-js-functions-child', get_stylesheet_directory_uri() . '/js/functions.js', true );
}
add_action( 'wp_enqueue_scripts', 'onecommunity_js_functions_child' );

// the default wp user created emails are bad, we got auth0 for that
function disable_new_user_notifications() {
    remove_action( 'register_new_user', 'wp_send_new_user_notifications' );
}
add_action( 'init', 'disable_new_user_notifications' );

// Include all function files in the make-community/functions directory:
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