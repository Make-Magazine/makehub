<?php

function onecommunity_child_enqueue_styles() {
	wp_enqueue_style('parent-theme', get_template_directory_uri() .'/style.css');
}
add_action('wp_enqueue_scripts', 'onecommunity_child_enqueue_styles');


function onecommunity_js_functions_child() {
wp_enqueue_script( 'onecommunity-js-functions-child', get_stylesheet_directory_uri() . '/js/functions.js', true );
}
add_action( 'wp_enqueue_scripts', 'onecommunity_js_functions_child' );
