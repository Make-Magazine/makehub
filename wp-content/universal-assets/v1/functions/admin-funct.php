<?php
// alphabetize menu items
function sort_admin_menu() {
	if(is_admin()) {
	    global $menu;
	    // alphabetize submenu items
		if($menu) {
		    usort( $menu, function ( $a, $b ) {
		        if(isset($a['5']) && $a[5]!='menu-dashboard'){
		          // format of a submenu item is [ 'My Item', 'read', 'manage-my-items', 'My Item' ]
		          return strcasecmp( strip_tags($a[0]), strip_tags($b[0]) );
		        }
		    } );
		    //remove separators
		    $menu = array_filter($menu, function($item) {
		        return $item[0] != '';
		    });
		}
	}
}
add_action('admin_init','sort_admin_menu');

/**
 * Eliminate some of the default admin list columns that squish the title
 */
 add_action( 'current_screen', function( $screen ) {
 	if ( ! isset( $screen->id ) ) return;
 	add_filter( "manage_{$screen->id}_columns", 'remove_default_columns', 99 );
 } );

 function remove_default_columns( $columns ) {
	unset($columns['essb_shares'], $columns['essb_shareinfo']);
 	return $columns;
 }

 add_action('admin_bar_menu', 'toolbar_link_to_mypage', 999);

 function toolbar_link_to_mypage($wp_admin_bar) {
     $args = [
         'id' => 'wp-submit-asana-bug',
         'title' => '<span class="wp-menu-image dashicons-before dashicons-buddicons-replies"></span>' . 'Report a Bug',
         'meta' => array('target' => '_blank'),
         'href' => 'https://form.asana.com/?hash=936d55d2283dea9fe2382a75e80722675681f3881416d93f7f75e8a4941c6d47&id=1149238253861292',
     ];
     $wp_admin_bar->add_menu($args);
 }

 //set default user avatar to grey makey
 add_filter( 'avatar_defaults', 'wpb_new_gravatar' );
 function wpb_new_gravatar ($avatar_defaults) {
   $myavatar = 'https://make.co/wp-content/universal-assets/v1/images/default-makey-big.png';
   $avatar_defaults[$myavatar] = "Default Makey Avatar";
   return $avatar_defaults;
 }

// Allw admin users to edit elementor and wysiwyg without stripping styles and scripts
function allow_unfiltered_html_multisite( $caps, $cap, $user_id, $args ) {
	if ( $user_id !== 0 && $cap === 'unfiltered_html' ) {
		$user_meta = get_userdata($user_id);
		if ( in_array( 'administrator', $user_meta->roles, true ) ) {
			// Re-add the cap
			unset( $caps );
			$caps[] = $cap;
		}
	}
	return $caps;
}
add_filter('map_meta_cap', 'allow_unfiltered_html_multisite', 10, 4 );

// for non-super admins, hides some items they don't need to trouble with
function hide_unnecessary_menu_items(){
	if( !is_super_admin(wp_get_current_user()) ) {
        remove_menu_page( 'jetpack' ); //Jetpack
        remove_menu_page( 'themes.php' ); //Appearance
        remove_menu_page( 'plugins.php' ); //Plugins
        remove_menu_page( 'edit.php?post_type=acf-field-group' ); //Custom Fields
        remove_menu_page( 'edit.php?post_type=search-filter-widget' ); //Search and Filter
        remove_menu_page( 'activecampaign_for_woocommerce' ); //ActiveCampaign
        remove_menu_page( 'wpa0' ); //Auth0
        remove_menu_page( 'wpseo_dashboard' ); //Yoast
        remove_menu_page( 'members' ); //Members
        remove_menu_page( 'elementor' ); //Elementor Settings
	}
}
add_action( 'admin_init', 'hide_unnecessary_menu_items' );
