<?php

// Kleo Theme Fixes.
add_filter( 'enable_kleo_bp_dir_send_private_message_button', '__return_false' );
add_action( 'after_setup_theme', 'kleo_remove_bp_dynamic_style', 14 );

function kleo_remove_bp_dynamic_style() {

   remove_filter( 'kleo_dynamic_main', 'kleo_buddypress_dynamic_style', 12 );

	if ( is_buddypress() ) {
		remove_action( 'wp_enqueue_scripts', 'kleo_load_woocommerce_css', 20 );
	}

	remove_action( 'bbp_enqueue_scripts', 'kleo_bbpress_register_style', 15 );

}

/**
 * Bimber Theme Fix
 */
function yz_disable_bimber_theme_bp_css() {
    return false;
}

add_filter( 'bimber_bp_load_css', 'yz_disable_bimber_theme_bp_css' );

/**
 * Ghostpool Themes Fix
 */
add_filter( 'ghostpool_bbpress_css', '__return_false' );
add_filter( 'ghostpool_buddypress_main_css', '__return_false' );
add_filter( 'ghostpool_woocommerce_css', 'yz_disable_woocommerce_css' );
function yz_disable_woocommerce_css( $active ) {
	if ( function_exists( 'yz_is_woocommerce_tab' ) && yz_is_woocommerce_tab() ) {
    	return false;
	}
	return $active;
}

/**
 * Kleo Theme Fix
 */
function yz_remove_kleo_bp_css() {
	wp_dequeue_style( 'bp-parent-css' );
	wp_dequeue_style( 'kleo-bbpress' );	
}

add_action( 'wp_enqueue_scripts' , 'yz_remove_kleo_bp_css', 999 );

/**
 * My Listing Theme Fix
 */
function yz_remove_c27_bp_css() {
  	// 27 Theme
  	// wp_dequeue_script( 'select2' );
  	// wp_deregister_script( 'select2' );
	wp_dequeue_style( 'c27-buddypress-style' );
}

add_action( 'wp_enqueue_scripts' , 'yz_remove_c27_bp_css', 999 );

/**
 * Remove Theme Default Buddypress CSS
 */
function youzer_remove_themes_default_bp_css() {

	$theme = wp_get_theme();

	if ( $theme == 'Aardvark' || $theme == 'Aardvark Child' ) {
		wp_dequeue_style( 'ghostpool-bp' );
		wp_dequeue_style( 'ghostpool-bbp' );
	}

	if ( $theme == 'OceanWP' || $theme == 'OceanWP Child' ) {
		wp_dequeue_style( 'oceanwp-buddypress' );
	}
	
	if ( $theme == 'Enfold' || $theme == 'Enfold Child' ) {
		wp_dequeue_style( 'avia-bbpress' );
	}

}

add_action( 'wp_enqueue_scripts' , 'youzer_remove_themes_default_bp_css', 999 );


function yz_my_listing_theme_css_fix() {

	$theme = wp_get_theme();

	if ( 'My Listing' != $theme ) {
		return;
	}

	?>

	<style type="text/css">
		.bp-user .yz-page {
			margin-top: 89px;
		}
		.buddypress.bp-user  .c27-main-header.header.header-fixed {
			background-color: rgba(29, 35, 41, 0.98);
		}

		.buddypress.listings .yz-sidebar-column {
			display:none;
		}

		#buddypress.youzer #activity-stream .ac-reply-content a {
		    background: transparent;
		    color: #898989;
		    font-weight: 600;
		}

		.directory.activity #buddypress div.item-list-tabs ul li.selected a,
		#buddypress.youzer #activity-stream .ac-reply-content a:hover  {
			background:transparent !important;
		}

		.youzer #activity-stream .ac-reply-content input[type="submit"] {
			width:initial;
		}

		@media screen and ( max-width: 1024px ) {
			.bp-user .yz-page {
			margin-top: 80px;
			}
		}
		@media screen and ( max-width: 425px ) {
			.bp-user .yz-page {
			margin-top: 60px;
			}
		}	
	</style>
	
	<?php
}

add_action( 'wp_head', 'yz_my_listing_theme_css_fix' );