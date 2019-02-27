<?php
/**
 * Learn based off Monochrome Pro.
 *
 * This file adds functions to the Learn Theme.
 *
 * @package Learn
 * @author  Makermedia
 * @license GPL-2.0+
 * @link    https://github.com/Make-Magazine/makehub.git
 */

// Start the engine.
include_once( get_template_directory() . '/lib/init.php' );

// Child theme (do not remove).
define( 'CHILD_THEME_NAME', 'Learn' );
define( 'CHILD_THEME_URL', 'https://my.studiopress.com/themes/monochrome/' );
define( 'CHILD_THEME_VERSION', '1.1.0' );

// Setup Theme.
include_once( get_stylesheet_directory() . '/lib/theme-defaults.php' );

// Set Localization (do not remove).
add_action( 'after_setup_theme', 'learn_localization_setup' );
function learn_localization_setup(){

	load_child_theme_textdomain( 'learn', get_stylesheet_directory() . '/languages' );

}

// Add the theme helper functions.
include_once( get_stylesheet_directory() . '/lib/helper-functions.php' );

// Add Image upload and Color select to WordPress Theme Customizer.
require_once( get_stylesheet_directory() . '/lib/customize.php' );

// Include Customizer CSS.
include_once( get_stylesheet_directory() . '/lib/output.php' );

// Add that navwalker for the custom menus
require_once('lib/wp_bootstrap_navwalker.php');

// Add WooCommerce support.
include_once( get_stylesheet_directory() . '/lib/woocommerce/woocommerce-setup.php' );

// Include the Customizer CSS for the WooCommerce plugin.
include_once( get_stylesheet_directory() . '/lib/woocommerce/woocommerce-output.php' );

// Include notice to install Genesis Connect for WooCommerce.
include_once( get_stylesheet_directory() . '/lib/woocommerce/woocommerce-notice.php' );

add_action( 'after_setup_theme', 'genesis_child_gutenberg_support' );
/**
 * Adds Gutenberg opt-in features and styling.
 *
 * Allows plugins to remove support if required.
 *
 * @since 1.1.0
 */
function genesis_child_gutenberg_support() {

	require_once get_stylesheet_directory() . '/lib/gutenberg/init.php';

}

// Enqueue scripts and styles.
add_action( 'wp_enqueue_scripts', 'learn_enqueue_scripts_styles' );
function learn_enqueue_scripts_styles() {
	$my_theme = wp_get_theme();
   $my_version = $my_theme->get('Version');
   wp_enqueue_style('bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', '', 'all' );
	wp_enqueue_style('font-awesome-css', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css', '', 'all' );
	wp_enqueue_style('linearicons', 'https://cdn.linearicons.com/free/1.0.0/icon-font.min.css', '', 'all' );
	wp_enqueue_style('fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.6/css/jquery.fancybox.min.css', '', 'all');
	wp_enqueue_style( 'learn-ionicons', '//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css', array(), CHILD_THEME_VERSION );
	wp_enqueue_style('universal.css', content_url() . '/universal-assets/v1/css/universal.min.css');
	// this is their precompiled style, we'll be replacing this with our own stuff soon
	wp_enqueue_style('old-style.css', get_stylesheet_directory_uri() .  '/old-style.css', array(), CHILD_THEME_VERSION );

	wp_enqueue_script('auth0', 'https://cdn.auth0.com/js/auth0/9.3.1/auth0.min.js', array(), false, true );
	wp_enqueue_script('bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array('jquery'), '', true );
	wp_enqueue_script('fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.6/js/jquery.fancybox.min.js', array('jquery'), '', true );
	wp_enqueue_script('universal', content_url() . '/universal-assets/v1/js/min/universal.min.js', array(), $my_version, true );
	
	wp_enqueue_script( 'learn-global-script', get_stylesheet_directory_uri() . '/js/global.js', array( 'jquery' ), '1.0.0', true );

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script( 'learn-responsive-menu', get_stylesheet_directory_uri() . '/js/responsive-menus' . $suffix . '.js', array( 'jquery' ), CHILD_THEME_VERSION, true );
	wp_localize_script( 'learn-responsive-menu', 'genesis_responsive_menu', learn_responsive_menu_settings() );

}

// Define our responsive menu settings.
function learn_responsive_menu_settings() {

	$settings = array(
		'mainMenu'         => __( 'Menu', 'learn' ),
		'menuIconClass'    => 'ionicons-before ion-navicon',
		'subMenu'          => __( 'Submenu', 'learn' ),
		'subMenuIconClass' => 'ionicons-before ion-chevron-down',
		'menuClasses'      => array(
			'combine' => array( ),
			'others'  => array(
				'.nav-primary',
			),
		),
	);

	return $settings;

}

// Add HTML5 markup structure.
add_theme_support( 'html5', array( 'caption', 'comment-form', 'comment-list', 'gallery', 'search-form' ) );

// Add Accessibility support.
add_theme_support( 'genesis-accessibility', array( '404-page', 'drop-down-menu', 'headings', 'rems', 'search-form', 'skip-links' ) );

// Add viewport meta tag for mobile browsers.
add_theme_support( 'genesis-responsive-viewport' );

// Add support for custom header.
add_theme_support( 'custom-header', array(
	'width'           => 320,
	'height'          => 120,
	'header-selector' => '.site-title a',
	'header-text'     => false,
	'flex-height'     => true,
	'flex-width'     => true,
) );

// Add support for after entry widget.
add_theme_support( 'genesis-after-entry-widget-area' );

// Add image sizes.
add_image_size( 'front-blog', 960, 540, TRUE );
add_image_size( 'sidebar-thumbnail', 80, 80, TRUE );

// Remove header right widget area.
unregister_sidebar( 'header-right' );

// Remove secondary sidebar.
unregister_sidebar( 'sidebar-alt' );

// Remove site layouts.
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );

// Remove output of primary navigation right extras.
remove_filter( 'genesis_nav_items', 'genesis_nav_right', 10, 2 );
remove_filter( 'wp_nav_menu_items', 'genesis_nav_right', 10, 2 );

// Remove navigation meta box.
add_action( 'genesis_theme_settings_metaboxes', 'learn_remove_genesis_metaboxes' );
function learn_remove_genesis_metaboxes( $_genesis_theme_settings_pagehook ) {

	remove_meta_box( 'genesis-theme-settings-nav', $_genesis_theme_settings_pagehook, 'main' );

}

// Register navigation menus.
add_theme_support( 'genesis-menus', array( 'primary' => __( 'Header Menu', 'learn' ), 'secondary' => __( 'Footer Menu', 'learn' ) ) );

// Reposition primary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_header', 'genesis_do_nav', 12 );

// Reposition secondary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_after', 'genesis_do_subnav', 12 );

// Add the search icon to the header if the option is set in the Customizer.
add_action( 'genesis_meta', 'learn_add_search_icon' );
function learn_add_search_icon() {

	$show_icon = get_theme_mod( 'learn_header_search', learn_customizer_get_default_search_setting() );

	// Exit early if option set to false.
	if ( ! $show_icon ) {
		return;
	}

	add_action( 'genesis_header', 'learn_do_header_search_form', 14 );
	add_filter( 'genesis_nav_items', 'learn_add_search_menu_item', 10, 2 );
	add_filter( 'wp_nav_menu_items', 'learn_add_search_menu_item', 10, 2 );

}

// Function to modify the menu item output of the Header Menu.
function learn_add_search_menu_item( $items, $args ) {

	$search_toggle = sprintf( '<li class="menu-item">%s</li>', learn_get_header_search_toggle() );

	if ( 'primary' === $args->theme_location ) {
		$items .= $search_toggle;
	}

	return $items;

}

// Reduce secondary navigation menu to one level depth.
add_filter( 'wp_nav_menu_args', 'learn_secondary_menu_args' );
function learn_secondary_menu_args( $args ) {

	if ( 'secondary' != $args['theme_location'] ) {
		return $args;
	}

	$args['depth'] = 1;

	return $args;

}

// Modify Gravatar size in author box.
add_filter( 'genesis_author_box_gravatar_size', 'learn_author_box_gravatar' );
function learn_author_box_gravatar( $size ) {

	return 90;

}

// Customize entry meta in entry header.
add_filter( 'genesis_post_info', 'learn_entry_meta_header' );
function learn_entry_meta_header( $post_info ) {

	$post_info = '[post_author_posts_link] &middot; [post_date format="M j, Y"] &middot; [post_comments] [post_edit]';

	return $post_info;

}

// Customize entry meta in entry footer.
add_filter( 'genesis_post_meta', 'learn_entry_meta_footer' );
function learn_entry_meta_footer( $post_meta ) {

	$post_meta = '[post_categories before=""] [post_tags before=""]';

	return $post_meta;

}

// Modify Gravatar size in entry comments.
add_filter( 'genesis_comment_list_args', 'learn_comments_gravatar' );
function learn_comments_gravatar( $args ) {

	$args['avatar_size'] = 48;

	return $args;

}

// Setup widget counts.
function learn_count_widgets( $id ) {

	$sidebars_widgets = wp_get_sidebars_widgets();

	if ( isset( $sidebars_widgets[ $id ] ) ) {
		return count( $sidebars_widgets[ $id ] );
	}

}

// Calculate widget count.
function learn_widget_area_class( $id ) {

	$count = learn_count_widgets( $id );

	$class = '';

	if ( $count == 1 ) {
		$class .= ' widget-full';
	} elseif ( $count % 3 == 1 ) {
		$class .= ' widget-thirds';
	} elseif ( $count % 4 == 1 ) {
		$class .= ' widget-fourths';
	} elseif ( $count % 2 == 0 ) {
		$class .= ' widget-halves uneven';
	} else {
		$class .= ' widget-halves';
	}

	return $class;

}

// Customize content limit read more link markup.
add_filter( 'get_the_content_limit', 'learn_content_limit_read_more_markup', 10, 3 );
function learn_content_limit_read_more_markup( $output, $content, $link ) {

	$output = sprintf( '<p>%s &#x02026;</p><p class="more-link-wrap">%s</p>', $content, str_replace( '&#x02026;', '', $link ) );

	return $output;

}

// Remove entry meta in entry footer.
remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );

// Hook before footer CTA widget area.
add_action( 'genesis_before_footer', 'learn_before_footer_cta' );
function learn_before_footer_cta() {

	genesis_widget_area( 'before-footer-cta', array(
		'before' => '<div class="before-footer-cta"><div class="wrap">',
		'after'  => '</div></div>',
	) );

}

// Remove site footer.
remove_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
remove_action( 'genesis_footer', 'genesis_do_footer' );
remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );

// Add site footer.
add_action( 'genesis_after', 'genesis_footer_markup_open', 5 );
add_action( 'genesis_after', 'genesis_do_footer' );
add_action( 'genesis_after', 'genesis_footer_markup_close', 15 );

// Register widget areas.
genesis_register_sidebar( array(
	'id'          => 'front-page-1',
	'name'        => __( 'Front Page 1', 'learn' ),
	'description' => __( 'This is the front page 1 image section.', 'learn' ),
) );
genesis_register_sidebar( array(
	'id'          => 'front-page-2',
	'name'        => __( 'Front Page 2', 'learn' ),
	'description' => __( 'This is the front page 2 section.', 'learn' ),
) );
genesis_register_sidebar( array(
	'id'          => 'front-page-3',
	'name'        => __( 'Front Page 3', 'learn' ),
	'description' => __( 'This is the front page 3 image section.', 'learn' ),
) );
genesis_register_sidebar( array(
	'id'          => 'front-page-4',
	'name'        => __( 'Front Page 4', 'learn' ),
	'description' => __( 'This is the front page 4 section.', 'learn' ),
) );
genesis_register_sidebar( array(
	'id'          => 'before-footer-cta',
	'name'        => __( 'Before Footer CTA', 'learn' ),
	'description' => __( 'This is the before footer CTA section.', 'learn' ),
) );
