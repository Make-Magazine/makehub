<?php

if (!defined('ABSPATH'))
    define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-load.php');

// Can we load the universal scripts this way
function universal_scripts() {
    //auth0
    wp_enqueue_script('auth0', 'https://cdn.auth0.com/js/auth0/9.3.1/auth0.min.js', array(), false, true);
}
add_action('wp_enqueue_scripts', 'universal_scripts', 10, 2);

// check if user is logged in
function ajax_check_user_logged_in() {
    echo is_user_logged_in() ? 'yes' : 'no';
    die();
}
add_action('wp_ajax_is_user_logged_in', 'ajax_check_user_logged_in');
add_action('wp_ajax_nopriv_is_user_logged_in', 'ajax_check_user_logged_in');

// Add that navwalker for the custom menus
require_once('lib/wp_bootstrap_navwalker.php');

// check if user is a user, but isn't a user of the current blog, and add them if they aren't
function create_user_on_blog($user_object) {
	$blog_id = get_current_blog_id();
   	if ( $blog_id == 3 || $blog_id == 3 ) {
	    $user_id = $user_object->ID;
	    $blog_id = get_current_blog_id();
	    if ($user_id && !is_user_member_of_blog($user_id, $blog_id)) {
	        add_user_to_blog($blog_id, $user_id, "subscriber");
	    }
	}
}
add_action('auth0_before_login', 'create_user_on_blog', 10, 6);

/** Set up the Ajax WP Logout */
function MM_wordpress_logout() {
    //check_ajax_referer( 'ajax-logout-nonce', 'ajaxsecurity' );
    wp_logout();
    ob_clean(); // probably overkill for this, but good habit
    wp_send_json_success();
}
add_action('wp_ajax_mm_wplogout', 'MM_wordpress_logout');
add_action('wp_ajax_nopriv_mm_wplogout', 'MM_wordpress_logout');

/** Set up the Ajax WP Login */
function MM_WPlogin() {
    //check_ajax_referer( 'ajax-login-nonce', 'ajaxsecurity' );
    global $wpdb; // access to the database
    //use auth0 plugin to log people into wp
    $a0_plugin = new WP_Auth0_InitialSetup(WP_Auth0_Options::Instance());
    $a0_options = WP_Auth0_Options::Instance();
    $users_repo = new WP_Auth0_UsersRepo($a0_options);
    $login_manager = new WP_Auth0_LoginManager($users_repo, $a0_options);

    //get the user information passed from auth0
    $userinput = filter_input_array(INPUT_POST);
    $userinfo = (object) $userinput['auth0_userProfile'];
    $userinfo->email_verified = true;
    $access_token = filter_input(INPUT_POST, 'auth0_access_token', FILTER_SANITIZE_STRING);
    $id_token = filter_input(INPUT_POST, 'auth0_id_token', FILTER_SANITIZE_STRING);

    if ($login_manager->login_user($userinfo, $id_token, $access_token)) {
        /* $blog_id = get_current_blog_id(); // this only triggered for a user logged into another site to begin with
          $user_id = username_exists( sanitize_text_field( $userinfo->nickname ) );
          if ( $user_id && ! is_user_member_of_blog( $user_id, $blog_id ) ) {
          add_user_to_blog( $blog_id, $user_id, "subscriber" );
          } */
        wp_send_json_success();
    } else {
        error_log('Failed login');
        error_log(print_r($userinput, TRUE));
        wp_send_json_error();
    }
}
add_action('wp_ajax_mm_wplogin', 'MM_WPlogin');
add_action('wp_ajax_nopriv_mm_wplogin', 'MM_WPlogin');

// Write to the php error log by request
function make_error_log() {
    $error = filter_input(INPUT_POST, 'make_error', FILTER_SANITIZE_STRING);
    error_log(print_r($error, TRUE));
}
add_action('wp_ajax_make_error_log', 'make_error_log');
add_action('wp_ajax_nopriv_make_error_log', 'make_error_log');

function randomString() {
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
    return substr(str_shuffle($permitted_chars), 0, 10);
}

// prevent non admin users from seeing the admin dashboard -- I don't believe this is necessary anymore. - Rio 3/2/22
/*I function blockusers_init() {
    if (is_admin() && !current_user_can('administrator') && !( defined('DOING_AJAX') && DOING_AJAX )) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('init', 'blockusers_init');*/

function timezone_abbr_from_name($timezone_name) {
    $dateTime = new DateTime();
    $dateTime->setTimeZone(new DateTimeZone($timezone_name));
    return $dateTime->format('T');
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

function get_tag_ID($tag_name) {
	$tag = get_term_by('name', $tag_name, 'post_tag');
	if ($tag) {
		return $tag->term_id;
	} else {
		return 0;
	}
}

function add_universal_body_classes($classes) {
    if ( defined('EVENT_ESPRESSO_VERSION') ){
        $classes[] = "event-espresso";
        return $classes;
    }
}
add_filter('body_class', 'add_universal_body_classes');

// don't just use the auth0 email field for the wpuser name
add_filter( 'auth0_use_management_api_for_userinfo', '__return_false', 101 );

/* disable wordpress emails if that is set in wp-config
add_filter('wp_mail','disabling_emails', 10,1);
function disabling_emails( $args ){
	error_log("variable is: " . ALLOW_WP_EMAILS);
    if ( ! $_GET['allow_wp_mail'] ) {
        unset ( $args['to'] );
    }
    return $args;
}
*/

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


/* This allows us to send elementor styled pages to other blogs */
add_action("rest_api_init", function () {
    register_rest_route(
        "elementor/v1"
        , "/pages/(?P<id>\d+)/contentElementor"
        , [
            "methods" => "GET",
            'permission_callback' => '__return_true',
            "callback" => function (\WP_REST_Request $req) {

            $contentElementor = "";

            if (class_exists("\\Elementor\\Plugin")) {
                $post_ID = $req->get_param("id");

                $pluginElementor = \Elementor\Plugin::instance();
                $contentElementor = $pluginElementor->frontend->get_builder_content($post_ID);
            }


            return $contentElementor;
            },
            ]
        );
});

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

add_action('elementor/widgets/widgets_registered', function( $widget_manager ){
	$widget_manager->unregister_widget_type('form');
	$widget_manager->unregister_widget_type('uael-table-of-contents');
	$widget_manager->unregister_widget_type('uael-registration-form');
	$widget_manager->unregister_widget_type('uael-login-form');
	$widget_manager->unregister_widget_type('wp-widget-members-widget-login');
	$widget_manager->unregister_widget_type('uael-gf-styler');
}, 15);

add_action( 'rest_api_init', function () {
  register_rest_route( 'MakeHub/v1', '/userNav/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'make_user_info',
  ) );
} );

// Returns the User Information for the right hand side of the Universal nav
function make_user_info( $data ) {
  $userID = $data['id'];
  $return = array();
  $return['makeLogin'] =
              '<span class="login-section" style="display: block;">
                  <a id="LoginBtn" href="/wp-login.php?redirect_to=https://www.makehub.local/dashboard/%3Flogin%3Dtrue" title="Log In" style="display: none;">Log In</a>
                  <div id="profile-view" class="dropdown v-select" style="display: flex;">
                      <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <img class="avatar" style="width: 38px; display: block;" alt="avatar" src="https://www.makehub.local/wp-content/uploads/avatars/2/5e8cd4bac4027-bpthumb.png">
                      </a>

                      <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                          <div class="profile-info">
                              <img class="avatar" style="width:80px" alt="avatar" src="https://www.makehub.local/wp-content/uploads/avatars/2/5e8cd4bac4027-bpthumb.png">
                              <div class="profile-text">
                                  <div class="profile-name">makey</div>
                                  <div class="profile-email">webmaster@make.co</div>
                              </div>
                          </div>
                          <div class="dropdown-links" id="profileLinks">
                          <ul id="header-my-account-menu" class="bb-my-account-menu has-icon"><li class="menu-item icon-added"><a href="https://make.co/dashboard"><i class="_mi _before buddyboss bb-icon-board-list" aria-hidden="true"></i><span>My Dashboard</span></a></li><li class="menu-item menu-item-facilitator-portal"><a href="https://make.co/edit-submission/"><i class="_mi _before buddyboss bb-icon-graduation-cap" aria-hidden="true"></i><span>Facilitator Portal</span></a></li><li class="menu-item menu-item-event-cart"><a href="https://make.co/registration-checkout/?event_cart=view"><i class="_mi _before buddyboss bb-icon-shopping-cart" aria-hidden="true"></i><span>Event Cart</span></a></li><li class="bp-menu bp-profile-nav menu-item menu-item-has-children"><a href="https://make.co/members/me/profile/"><i class="_mi _before buddyboss bb-icon-user-alt" aria-hidden="true"></i><span>Profile</span></a><div class="wrapper ab-submenu"><ul class="bb-sub-menu"><li class="bp-menu bp-public-sub-nav menu-item no-icon"><a href="https://make.co/members/me/profile/">View</a></li><li class="bp-menu bp-edit-sub-nav menu-item no-icon"><a href="https://make.co/members/me/profile/edit/">Edit</a></li><li class="bp-menu bp-change-avatar-sub-nav menu-item no-icon"><a href="https://make.co/members/me/profile/change-avatar/">Profile Photo</a></li><li class="bp-menu bp-change-cover-image-sub-nav menu-item no-icon"><a href="https://make.co/members/me/profile/change-cover-image/">Cover Photo</a></li></ul></div></li><li class="bp-menu bp-settings-nav menu-item menu-item-has-children"><a href="https://make.co/members/me/settings/"><i class="_mi _before buddyboss bb-icon-settings" aria-hidden="true"></i><span>Account</span></a><div class="wrapper ab-submenu"><ul class="bb-sub-menu"><li class="bp-menu bp-settings-notifications-sub-nav menu-item no-icon"><a href="https://make.co/members/me/settings/notifications/">Email Preferences</a></li><li class="bp-menu bp-view-sub-nav menu-item no-icon"><a href="https://make.co/members/me/settings/profile/">Privacy</a></li><li class="bp-menu bp-blocked-members-sub-nav menu-item no-icon"><a href="https://make.co/members/me/settings/blocked-members/">Blocked Members</a></li><li class="bp-menu bp-group-invites-settings-sub-nav menu-item no-icon"><a href="https://make.co/members/me/settings/invites/">Group Invites</a></li><li class="bp-menu bp-export-sub-nav menu-item no-icon"><a href="https://make.co/members/me/settings/export/">Export Data</a></li></ul></div></li><li class="bp-menu bp-friends-nav menu-item menu-item-has-children"><a href="https://make.co/members/me/friends/"><i class="_mi _before buddyboss bb-icon-users" aria-hidden="true"></i><span>Connections</span></a><div class="wrapper ab-submenu"><ul class="bb-sub-menu"><li class="bp-menu bp-my-friends-sub-nav menu-item no-icon"><a href="https://make.co/members/me/friends/">My Connections</a></li><li class="bp-menu bp-requests-sub-nav menu-item no-icon"><a href="https://make.co/members/me/friends/requests/">Requests</a></li></ul></div></li><li class="bp-menu bp-groups-nav menu-item menu-item-has-children"><a href="https://make.co/members/me/groups/"><i class="_mi _before buddyboss bb-icon-groups" aria-hidden="true"></i><span>Groups</span></a><div class="wrapper ab-submenu"><ul class="bb-sub-menu"><li class="bp-menu bp-groups-create-nav menu-item no-icon"><a href="https://make.co/groups/create/">Create Group</a></li><li class="bp-menu bp-my-groups-sub-nav menu-item no-icon"><a href="https://make.co/members/me/groups/">My Groups</a></li><li class="bp-menu bp-group-invites-sub-nav menu-item no-icon"><a href="https://make.co/members/me/groups/invites/">Invitations</a></li></ul></div></li></ul></div>
                          <a id="LogoutBtn" href="/wp-login.php?action=logout" title="Log Out" style="display: flex;">Log Out</a>
                      </div>
                  </div>
              </span>';
  $return['makeCoins'] = '1,000';
  $return['makeJoin'] =
              '<span class="search-separator nav-separator"></span>
                <div class="search-button-wrapper">
                    <div class="subscribe-call-out">
                        <div class="subscribe-text">
                            <a target="_none" href="https://make.co/join/?utm_source=make&amp;utm_medium=universalnav&amp;utm_campaign=subscribe-call-out&amp;utm_content=launch">
                                Join
                            </a>
                        </div>
                        <a target="_none" href="https://make.co/join/?utm_source=make&amp;utm_medium=universalnav&amp;utm_campaign=subscribe-call-out&amp;utm_content=launch">
                            <img src="https://make.co/wp-content/universal-assets/v1/images/magazine-nav-subscribe-single.jpg?v=80" id="nav-subscribe-img" alt="Get Make: Magazine Issue 79">
                        </a>
                        <div class="subscribe-pop-out">
                            <a target="_none" href="https://make.co/join/?utm_source=make&amp;utm_medium=universalnav&amp;utm_campaign=subscribe-popout&amp;utm_content=launch">
                                <img src="https://make.co/wp-content/universal-assets/v1/images/subscribe-today.jpg?v=80" alt="Subscribe Today to Make: Magazine">
                            </a>
                        </div>
                    </div>

                    <div id="sb-search" class="sb-search"></div>

                </div>
                <div class="user-wrap user-wrap-container menu-item-has-children">
                </div>
            </div>';

  $response = new WP_REST_Response($return);
  $response->set_status(200);

  return $response;
}
