<?php
/**
* Plugin Name: OneCommunity Shortcodes
* Plugin URI: http://themeforest.net/user/Diabolique
* Description: OneCommunity shortcodes plugin
* Version: 3.5.3
* Author: Diabolique Design
* Author URI: http://themeforest.net/user/Diabolique
* Text Domain: onecommunity-shortcodes
* Domain Path: /lang
**/

define('ONECOMMUNITY_SHORTCODES_PATH', plugin_dir_path(__FILE__) );

function onecommunity_shortcodes_load_plugin_textdomain() {
    load_plugin_textdomain( 'onecommunity-shortcodes', FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
}
add_action( 'plugins_loaded', 'onecommunity_shortcodes_load_plugin_textdomain' );


function add_onecommunity_shortcodes_style() {
	wp_enqueue_style( 'onecommunity-shortcodes', plugins_url('css/shortcodes.css', __FILE__), false );
	wp_enqueue_style( 'swiper-style', plugins_url('css/swiper-bundle.min.css', __FILE__), false );
}
add_action( 'wp_enqueue_scripts', 'add_onecommunity_shortcodes_style', 100 );


function onecommunity_shortcode_scripts(){
  if (!is_admin()) {
   wp_enqueue_script('masonry');
   wp_enqueue_script('shortcode-scripts',plugin_dir_url( __FILE__ ).'js/functions.js', true, null, 'in_footer' );
   wp_enqueue_script('swiper-js',plugin_dir_url( __FILE__ ).'js/swiper-bundle.min.js', true, null, '' );
  }
}
add_action('wp_enqueue_scripts','onecommunity_shortcode_scripts');


add_filter('widget_text', 'do_shortcode');
add_filter( 'bp_get_the_topic_post_content', 'do_shortcode' );
add_filter( 'bp_get_group_description', 'do_shortcode' );

add_filter("the_content", "the_content_filter");

function the_content_filter($content) {

// array of custom shortcodes requiring the fix
$block = join("|",array("img","go","quoteby","clear","highlight","quote","leftpullquote","rightpullquote","member","h1","h2","h3","h4","h5","h6","one_third","one_third_last","two_third","two_third_last","one_half","one_half_last","one_fourth","one_fourth_last","three_fourth","three_fourth_last","one_fifth","one_fifth_last","two_fifth","two_fifth_last","three_fifth","three_fifth_last","four_fifth","four_fifth_last","one_sixth","one_sixth_last","five_sixth","five_sixth_last"
));

// opening tag
$rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]",$content);
// closing tag
$rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)?/","[/$2]",$rep);

return $rep;

}

function onecommunity_plugin_init() {

	include ONECOMMUNITY_SHORTCODES_PATH . '/inc/general.php';

	include ONECOMMUNITY_SHORTCODES_PATH . '/inc/blog.php';

	include ONECOMMUNITY_SHORTCODES_PATH . '/inc/swiper.php';

	if ( function_exists( 'bp_is_active' ) ) {
		include ONECOMMUNITY_SHORTCODES_PATH . '/inc/buddypress.php';
	}

	if ( function_exists( 'GamiPress' ) ) {
		include ONECOMMUNITY_SHORTCODES_PATH . '/inc/gamipress.php';
	}

	if ( class_exists( 'WooCommerce', false ) ) {
		include ONECOMMUNITY_SHORTCODES_PATH . '/inc/woocommerce.php';
	}

	if ( class_exists( 'LearnPress' ) ) {
		include ONECOMMUNITY_SHORTCODES_PATH . '/inc/learnpress.php';
	}

	if ( class_exists( 'bbPress' ) ) {
		include ONECOMMUNITY_SHORTCODES_PATH . '/inc/bbpress.php';
	}
	
}
add_action( 'plugins_loaded', 'onecommunity_plugin_init' );

