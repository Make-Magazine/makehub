<?php
/*
   Plugin Name: Makehub Functions
   Plugin URI: makermedia.com
   Description: This plugin adds common makehub functionality used across the Genesis child themes for makehub
   Version: 1.0
   Author: Alicia Williams
   License: GPL2
   */

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

// Include all function files in the functions directory:
foreach (glob(plugin_dir_path( __FILE__ ) . '/functions/*.php') as $file) {
   include_once $file;
}

add_action( 'wp_enqueue_scripts', 'makehub_enqueue_scripts' );
function makehub_enqueue_scripts(){
    //add scripts here
}

add_action( 'wp_enqueue_scripts', 'makehub_enqueue_styles');
function makehub_enqueue_styles() {
    $my_version = '1.0.24';
    wp_enqueue_style('makehub-style', plugins_url( '/css/style.min.css', __FILE__ ), array(), $my_version );
}