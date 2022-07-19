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
   $myavatar = 'https://make.co/wp-content/universal-assets/v1/images/default-makey.png';
   $avatar_defaults[$myavatar] = "Default Makey Avatar";
   return $avatar_defaults;
 }
