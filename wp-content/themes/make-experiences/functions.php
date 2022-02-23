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

    wp_localize_script('universal', 'ajax_object',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'home_url' => get_home_url(),
                'logout_nonce' => wp_create_nonce('ajax-logout-nonce'),
                'wp_user_email' => wp_get_current_user()->user_email,
                'wp_user_nicename' => wp_get_current_user()->user_nicename
            )
    );
}

add_action('wp_enqueue_scripts', 'make_experiences_scripts_styles', 9999);


add_action('admin_enqueue_scripts', 'load_admin_styles');

function load_admin_styles() {
    wp_enqueue_style('admin_css', get_stylesheet_directory_uri() . '/css/admin-styles.css', false, '1.0.2');
}


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
	$fullMemberships = array("Premium Subscriber", "School Maker Faire", "Global Producers", "Multi-Seat Membership");
	$currentMemberships = array();

	if ( class_exists( '\Indeed\Ihc\UserSubscriptions' ) ) {
		$levels = \Indeed\Ihc\UserSubscriptions::getAllForUser(get_current_user_id(), TRUE);
		if (!empty($levels)) {
			$hasmembership = true;
			foreach($levels as $level) {
				switch($level['level_slug']){
					case "school_maker_faire":
					case "individual_first_year_discount":
					case "individual":
					case "family":
					case "makerspacesmallbusiness":
					case "patron":
					case "founder":
					case "benefactor":
					case "make_projects_school":
					case "global_producers":
						$canUpgrade = false;
						break;
				}
			}
		}
	} else if( class_exists('MeprUtils') ) {
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
    $wp_admin_bar->remove_node('uap_dashboard_menu'); //ultimate affiliate pro
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

add_filter('gform_ajax_spinner_url', 'spinner_url', 10, 2);

function spinner_url($image_src, $form) {
    return "/wp-content/universal-assets/v1/images/makey-spinner.gif";
}

function basicCurl($url, $headers = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if ($headers != null) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
	if (strpos(CURRENT_URL, '.local') > -1 || strpos(CURRENT_URL, '.test') > -1 ) { // wpengine local environments
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function postCurl($url, $headers = null, $datastring = null) {
	$ch = curl_init($url);

	if (strpos(CURRENT_URL, '.local') > -1  || strpos(CURRENT_URL, '.test') > -1) { // wpengine local environments
	  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

	if($datastring != null) {
		curl_setopt(
		  $ch,
		  CURLOPT_POSTFIELDS,
		  $datastring
		);
	}

	if ($headers != null) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	}

	$response = curl_exec($ch);

	if(curl_errno($ch)){
	  throw new Exception(curl_error($ch));
	}

	echo $response;
	curl_close($ch);
}

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

add_action('after_setup_theme', 'new_image_sizes');

function new_image_sizes() {
    add_image_size('grid-cropped', 300, 300, true);
    add_image_size('medium-large', 600, 600);
}

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

add_action('rest_api_init', 'register_ee_attendee_id_meta');

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
		if ( $post->post_type == "sfwd-courses" && get_post_primary_category($post->ID, 'ld_course_category')['primary_category']) {
			$classes[] = 'cat-' . get_post_primary_category($post->ID, 'ld_course_category')['primary_category']->slug;
		}
		if ( $post->post_type == "sfwd-lessons" && get_post_primary_category($post->ID, 'ld_lesson_category')['primary_category'] ) {
			$classes[] = 'cat-' . get_post_primary_category($post->ID, 'ld_lesson_category')['primary_category']->slug;
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

// don't lazyload on the project print template
function lazyload_exclude() {
    if (is_page_template('project-print-template.php') == true) {
        return false;
    } else {
        return true;
    }
}
add_filter('lazyload_is_enabled', 'lazyload_exclude', 15);
add_filter('wp_lazy_loading_enabled', 'lazyload_exclude', 10, 3);
add_filter('do_rocket_lazyload', 'lazyload_exclude', 10, 3 );

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
?>
