<?php
/**
 * @package Make Experiences
 * The parent theme functions are located at /buddyboss-theme/inc/theme/functions.php
 * Add your own functions at the bottom of this file.
 */
/* * **************************** THEME SETUP ***************************** */

require_once(ABSPATH . 'wp-content/universal-assets/v1/universal-functions.php');

// Defines the child theme (do not remove).
define('CHILD_THEME_NAME', 'Make - Experiences');
define('CHILD_THEME_URL', 'https://experiences.make.co');

/**
 * Sets up theme for translation
 *
 * @since Make Experiences 1.0.0
 */
function make_experiences_languages() {
    /**
     * Makes child theme available for translation.
     * Translations can be added into the /languages/ directory.
     */
    // Translate text from the PARENT theme.
    load_theme_textdomain('buddyboss-theme', get_stylesheet_directory() . '/languages');

    // Translate text from the CHILD theme only.
    // Change 'buddyboss-theme' instances in all child theme files to 'make_experiences'.
    // load_theme_textdomain( 'make_experiences', get_stylesheet_directory() . '/languages' );
}
add_action('after_setup_theme', 'make_experiences_languages');

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Make Experiences  1.0.0
 */
function make_experiences_scripts_styles() {
    $my_theme = wp_get_theme();
    $my_version = $my_theme->get('Version');
    /**
     * Scripts and Styles loaded by the parent theme can be unloaded if needed
     * using wp_deregister_script or wp_deregister_style.
     *
     * See the WordPress Codex for more information about those functions:
     * http://codex.wordpress.org/Function_Reference/wp_deregister_script
     * http://codex.wordpress.org/Function_Reference/wp_deregister_style
     * */
    // Styles
    wp_enqueue_style('bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', '', 'all');
    wp_enqueue_style('fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css', '', 'all');
    ### UNIVERSAL STYLES ###
    wp_enqueue_style('universal.css', content_url() . '/universal-assets/v1/css/universal.min.css', array(), $my_version);
    ### SUBTHEME STYLES ###
    wp_enqueue_style('make-co-style', get_stylesheet_directory_uri() . '/css/style.min.css', array(), $my_version);

    // Javascript
    wp_enqueue_script('fontawesome5-js', 'https://kit.fontawesome.com/7c927d1b5e.js', array(), '', true);
	  wp_enqueue_script('universal', content_url() . '/universal-assets/v1/js/min/universal.min.js', array(), $my_version, true);

    // lib src packages up bootstrap js and fancybox
    wp_enqueue_script('built-libs-js', get_stylesheet_directory_uri() . '/js/min/built-libs.min.js', array('jquery'), $my_version, true);
    wp_enqueue_script('make_experiences-js', get_stylesheet_directory_uri() . '/js/min/scripts.min.js', array('jquery'), $my_version, true);

    $user = wp_get_current_user();    
    $headers = setMemPressHeaders();
    $memberInfo = basicCurl("https://make.co/wp-json/mp/v1/members/".$user->ID, $headers);
    $memberArray = json_decode($memberInfo);
    $membershipType = checkForUpgrade($memberArray);

    wp_localize_script('universal', 'ajax_object',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'home_url' => get_home_url(),
                'logout_nonce' => wp_create_nonce('ajax-logout-nonce'),
                'wp_user_email' => $user->user_email,
                'wp_user_nicename' => $user->user_nicename,
                'wp_user_avatar' => get_avatar_url($user->ID),
                'wp_user_memlevel' => $membershipType
            )
    );
}
add_action('wp_enqueue_scripts', 'make_experiences_scripts_styles', 9999);

function load_admin_styles() {
    wp_register_style( 'admin_css', get_stylesheet_directory_uri() . '/css/admin-styles.css', false, '1.0.4' );
	wp_enqueue_style( 'admin_css' );
}
add_action('admin_enqueue_scripts', 'load_admin_styles');

function set_universal_asset_constants() {
	if (isset($_SERVER['HTTPS']) &&
	    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
	    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
	    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
	  		$protocol = 'https://';
	} else {
		$protocol = 'http://';
	}
    // Set the important bits as CONSTANTS that can easily be used elsewhere
	define('CURRENT_URL', $protocol . $_SERVER['HTTP_HOST']);
	define('CURRENT_POSTID', url_to_postid( CURRENT_URL . $_SERVER[ 'REQUEST_URI' ]));

	// Decide if user can upgrade
	$canUpgrade = true;
	$hasMembership = false;
	// this is a list of memberships that can't be upgraded further
	$fullMemberships = array("Premium Member", "School Maker Faire", "Global Producers", "Multi-Seat Membership");
	$currentMemberships = array();

	if( class_exists('MeprUtils') ) {
	    $mepr_current_user = MeprUtils::get_currentuserinfo();
		  // see if you can get the "slug" in this query and test against that in the $fullMemberships list
	    $sub_cols = array('id','user_id','product_id','product_name','subscr_id','status','created_at','expires_at','active');
		if($mepr_current_user) {
		    $table = MeprSubscription::account_subscr_table(
		      'created_at', 'DESC',
		      1, '', 'any', 0, false,
		      array(
		        'member' => $mepr_current_user->user_login,
		      ),
		      $sub_cols
		    );
		    $subscriptions = $table['results'];
			foreach($subscriptions as $subscription) {
				if($subscription->active == '<span class="mepr-active">Yes</span>') {
					$hasMembership = true;
					$currentMemberships[] = $subscription->product_name;
					if( in_array($subscription->product_name, $fullMemberships) ) {
						$canUpgrade = false;
					}
				}
			}
		} else {
			$canUpgrade = false;
		}
	}
	define('CURRENT_MEMBERSHIPS', $currentMemberships);
	define('IS_MEMBER', $hasMembership);
	define('CAN_UPGRADE', $canUpgrade);
}
set_universal_asset_constants();

/* * **************************** CUSTOM FUNCTIONS ***************************** */
remove_filter('wp_edit_nav_menu_walker', 'indeed_create_walker_menu_class');
//clean up the top black nav bar in admin
function experiences_remove_toolbar_node($wp_admin_bar) {
    $wp_admin_bar->remove_node('wp-logo');
    $wp_admin_bar->remove_node('new-content');
    $wp_admin_bar->remove_node('updates');
    $wp_admin_bar->remove_node('customize');
    $wp_admin_bar->remove_node('comments');
    $wp_admin_bar->remove_node('bp-notifications'); //buddypress notifications
    $wp_admin_bar->remove_node('elementor_inspector'); // elementor debugger
    $wp_admin_bar->remove_node('essb'); // easy social share buttons
}
add_action('admin_bar_menu', 'experiences_remove_toolbar_node', 999);

// Include all function files in the make-experiences/functions directory:
foreach (glob(get_stylesheet_directory() . '/functions/*.php') as $file) {
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

//* Disable email match check for all users - this error would keep users from registering users already in our system
add_filter('EED_WP_Users_SPCO__verify_user_access__perform_email_user_match_check', '__return_false');

function spinner_url($image_src, $form) {
    return "/wp-content/universal-assets/v1/images/makey-spinner.gif";
}
add_filter('gform_ajax_spinner_url', 'spinner_url', 10, 2);

function parse_yturl($url) {
    $pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
    preg_match($pattern, $url, $matches);
    return (isset($matches[1])) ? $matches[1] : false;
}

////////////////////////////////////////////////////////////////////
// Use Jetpack Photon if it exists, else use original photo
////////////////////////////////////////////////////////////////////

function get_resized_remote_image_url($url, $width, $height, $escape = true) {
    if (class_exists('Jetpack') && Jetpack::is_module_active('photon')) {
        $width = (int) $width;
        $height = (int) $height;
        // Photon doesn't support redirects, so help it out by doing http://foobar.wordpress.com/files/ to http://foobar.files.wordpress.com/
        if (function_exists('new_file_urls'))
            $url = new_file_urls($url);

        $thumburl = jetpack_photon_url($url, array(
            'resize' => array($width, $height),
            'strip' => 'all',
        ));
        return ($escape) ? esc_url($thumburl) : $thumburl;
    } else {
        return $url;
    }
}

function get_first_image_url($html) {
    if (preg_match('/<img.+?src="(.+?)"/', $html, $matches)) {
        return $matches[1];
    } else
        return get_stylesheet_directory_uri() . "/images/default-related-article.jpg";
}

function new_image_sizes() {
    add_image_size('grid-cropped', 300, 300, true);
    add_image_size('medium-large', 600, 600);
}
add_action('after_setup_theme', 'new_image_sizes');

function featuredtoRSS($content) {
    global $post;
    if (has_post_thumbnail($post->ID)) {
        $content = '<div>' . get_the_post_thumbnail($post->ID, 'medium', array('style' => 'margin-bottom: 15px;')) . '</div>' . $content;
    }
    return $content;
}
add_filter('the_excerpt_rss', 'featuredtoRSS', 20, 1);
add_filter('the_content_feed', 'featuredtoRSS', 20, 1);

function add_event_date_to_rss() {
    global $post;
    if (get_post_type() == 'espresso_events') {
        //determine start date
        $event = EEM_Event::instance()->get_one_by_ID($post->ID);
        $date = $event->first_datetime();
        $start_date = date('m/d/Y', strtotime($date->start_date()));
        ?>
        <event_date><?php echo $start_date ?></event_date>
        <?php
    }
}
add_action('rss2_item', 'add_event_date_to_rss', 30, 1);

// Exclude espresso_events from rss feed if marked for supression
function filter_posts_from_rss($where, $query = NULL) {
    global $wpdb;

    if (isset($query->query['post_type']) && !$query->is_admin && $query->is_feed && $query->query['post_type']) {
  		if($query->query['post_type'] == 'espresso_events') {
  			$dbSQL = "SELECT post_id FROM `wp_postmeta` WHERE `meta_key` LIKE 'suppress_from_rss_widget' and meta_value = 1";
  			$results = $wpdb->get_results($dbSQL);
  			$suppression_IDs = array();

  			foreach($results as $result){
  				$suppression_IDs[] = $result->post_id;
  			}

  			$exclude = implode(",", $suppression_IDs);

  			if (!empty($exclude)) {
  				$where .= ' AND wp_posts.ID NOT IN (' . $exclude . ')';
  			}
  		}
    }
    return $where;
}
add_filter( 'posts_where', 'filter_posts_from_rss', 1, 4 );

function register_ee_attendee_id_meta() {
    global $wpdb;
    $args = array(
        'type' => 'integer',
        'single' => true,
        'show_in_rest' => true
    );
    register_meta(
            'user',
            $wpdb->prefix . 'EE_Attendee_ID',
            $args
    );
}
add_action('rest_api_init', 'register_ee_attendee_id_meta');

//do not display doing it wrong errors
add_filter('doing_it_wrong_trigger_error', function () {
    return false;
}, 10, 0);

function add_slug_body_class($classes) {
    global $post;
    global $bp;
    if (isset($post)) {
        if ($post->post_name) {
            $classes[] = $post->post_type . '-' . $post->post_name;
            // any query string becomes a body class too
            parse_str($_SERVER['QUERY_STRING'], $query_array);
            foreach($query_array as $key => $value) {
                $classes[] = $key . "-" . $value;
            }
        } else {
            $classes[] = $post->post_type . '-' . str_replace("/", "-", trim($_SERVER['REQUEST_URI'], '/'));
        }
		// For Course and Lessons, check for the Primary category and add it to the body class if found
		if ( $post->post_type == "sfwd-courses") {
			$ld_course_category = get_post_primary_category($post->ID, 'ld_course_category');
			if(isset($ld_course_category['primary_category']->slug)) {
				$classes[] = 'cat-' . $ld_course_category['primary_category']->slug;
			}
		} else if ( $post->post_type == "sfwd-lessons") {
			$ld_lesson_category = get_post_primary_category($post->ID, 'ld_lesson_category');
			if(isset($ld_lesson_category['primary_category']->slug)) {
				$classes[] = 'cat-' . $ld_lesson_category['primary_category']->slug;
			}
		}

        // let's see if your the group owner and what kind of group it is (hidden, private, etc)
        if (bp_is_groups_component()) {
            $classes[] = 'group-' . groups_get_group(array('group_id' => bp_get_current_group_id()))->status;
            if (current_user_can('manage_options') || groups_is_user_mod(get_current_user_id(), bp_get_current_group_id()) || groups_is_user_admin(get_current_user_id(), bp_get_current_group_id())) {
                $classes[] = 'my-group';
            }
        }
        return $classes;
    }
}
add_filter('body_class', 'add_slug_body_class');

/*
 * Override any of the translation files if we need to change language
 *
 * @param $translation The current translation
 * @param $text The text being translated
 * @param $domain The domain for the translation
 * @return string The translated / filtered text.
 */

function filter_gettext($translation, $text, $domain) {
    $translations = get_translations_for_domain($_SERVER['HTTP_HOST']);
    switch ($text) {
        case 'Nickname':
            return $translations->translate('Display Name');
            break;
    }
    return $translation;
}
add_filter('gettext', 'filter_gettext', 10, 4);

// Disable automatic plugin updates
add_filter( 'auto_update_plugin', '__return_false' );


// Set Buddypress emails from and reply to
add_filter( 'bp_email_set_reply_to', function( $retval ) {
    return new BP_Email_Recipient( 'community@make.co' );
} );
add_filter( 'wp_mail_from', function( $email ) {
    return 'community@make.co';
}, 10, 3 );
add_filter( 'wp_mail_from_name', function( $name ) {
    return 'Make: Community';
}, 10, 3 );

//add link to 'Add event' form entries in gf admin drop down
function gf_add_entries_link( $wp_admin_bar ) {
	$wp_admin_bar->add_node(
			array(
					'id'     => 'gform-form-entries',
					'parent' => 'gform-forms',
					'title'  => esc_html__( "'Add Event' Entries", 'gravityforms' ),
					'href'   => admin_url('admin.php?page=gf_entries&id=1')
			));
	return $wp_admin_bar;
}
add_filter( 'admin_bar_menu', 'gf_add_entries_link', 25 );

// add the ability to add tags or categories to pages
function register_taxonomies() {
    // Add tag metabox to page
    register_taxonomy_for_object_type('post_tag', 'page');
    // Add category metabox to page
    register_taxonomy_for_object_type('category', 'page');
}
 // Add to the admin_init hook of your theme functions.php file
add_action( 'init', 'register_taxonomies' );

// get the main category of a post
function get_post_primary_category($post_id, $term='category', $return_all_categories=false){
    $return = array();
    if (class_exists('WPSEO_Primary_Term')){
        // Show Primary category by Yoast if it is enabled & set
        $wpseo_primary_term = new WPSEO_Primary_Term( $term, $post_id );
        $primary_term = get_term($wpseo_primary_term->get_primary_term());
        if (!is_wp_error($primary_term)){
            $return['primary_category'] = $primary_term;
        }
    }
    if (empty($return['primary_category']) || $return_all_categories){
        $categories_list = get_the_terms($post_id, $term);
        if (empty($return['primary_category']) && !empty($categories_list)){
            $return['primary_category'] = $categories_list[0];  //get the first category
        }
        if ($return_all_categories){
            $return['all_categories'] = array();
            if (!empty($categories_list)){
                foreach($categories_list as &$category){
                    $return['all_categories'][] = $category->term_id;
                }
            }
        }
    }
    return $return;
}

// prevent password changed email
add_filter( 'send_password_change_email', '__return_false' );

//allow us to have a drop down menu for social list fields
add_filter( 'gform_column_input_8_39_1', 'set_column', 10, 5 );
function set_column( $input_info, $field, $column, $value, $form_id ) {
    return array( 'type' => 'select', 'choices' => 'Instagram,Facebook, Twitter, YouTube, TikTok' );
}

//fix error that was keeping regular admins (non super admins, from being able to edit users)
function mc_admin_users_caps( $caps, $cap, $user_id, $args ){
    foreach( $caps as $key => $capability ){
        if( $capability != 'do_not_allow' )
            continue;

        switch( $cap ) {
            case 'edit_user':
            case 'edit_users':
                $caps[$key] = 'edit_users';
                break;
            case 'delete_user':
            case 'delete_users':
                $caps[$key] = 'delete_users';
                break;
            case 'create_users':
                $caps[$key] = $cap;
                break;
        }
    }
    return $caps;
}
add_filter( 'map_meta_cap', 'mc_admin_users_caps', 1, 4 );
remove_all_filters( 'enable_edit_any_user_configuration' );
add_filter( 'enable_edit_any_user_configuration', '__return_true');

/**
 * Checks that both the editing user and the user being edited are
 * members of the blog and prevents the super admin being edited.
 */
function mc_edit_permission_check() {
    global $current_user, $profileuser;
    $screen = get_current_screen();
    wp_get_current_user();

    if( ! is_super_admin( $current_user->ID ) && in_array( $screen->base, array( 'user-edit', 'user-edit-network' ) ) ) { // editing a user profile
        if ( is_super_admin( $profileuser->ID ) ) { // trying to edit a superadmin while less than a superadmin
            wp_die( __( 'You do not have permission to edit this user.' ) );
        } elseif ( ! ( is_user_member_of_blog( $profileuser->ID, get_current_blog_id() ) && is_user_member_of_blog( $current_user->ID, get_current_blog_id() ) )) { // editing user and edited user aren't members of the same blog
            wp_die( __( 'You do not have permission to edit this user.' ) );
        }
    }
}
add_filter( 'admin_head', 'mc_edit_permission_check', 1, 4 );
?>
