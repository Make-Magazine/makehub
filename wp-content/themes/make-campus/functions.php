<?php

/**
 * Maker Campus Theme
 *
 * This file adds functions to the Maker Campus Theme.
 *
 * @package Maker Campus
 * @author  Make: Community
 * @license GPL-2.0-or-later
 * @link    https://make.co
 */
// Starts the engine.
require_once get_template_directory() . '/lib/init.php';

// Defines the child theme (do not remove).
define('CHILD_THEME_NAME', 'Make - Campus');
define('CHILD_THEME_URL', 'https://experiences.make.co');

// Sets up the Theme.
require_once get_stylesheet_directory() . '/lib/theme-defaults.php';

add_action('after_setup_theme', 'make_campus_localization_setup');

/**
 * Sets localization (do not remove).
 *
 * @since 1.0.0
 */
function make_campus_localization_setup() {
    load_child_theme_textdomain('make-campus', get_stylesheet_directory() . '/languages');
}

// Adds helper functions.
require_once get_stylesheet_directory() . '/lib/helper-functions.php';

// Adds image upload and color select to Customizer.
require_once get_stylesheet_directory() . '/lib/customize.php';

// Includes Customizer CSS.
require_once get_stylesheet_directory() . '/lib/output.php';

// Adds WooCommerce support.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-setup.php';

// Adds the required WooCommerce styles and Customizer CSS.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-output.php';

// Adds the Genesis Connect WooCommerce notice.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-notice.php';

function make_campus_add_woocommerce_support() {
	add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'make_campus_add_woocommerce_support' );

// Add that navwalker for the custom menus
require_once('lib/wp_bootstrap_navwalker.php');

// FIX CONFLICT BETWEEN TRIBE EVENTS PLUGIN AND FRONT END IMAGE UPLOADER FOR BLOG POSTS
require_once(ABSPATH . 'wp-admin/includes/screen.php');

// Include all function files in the makerfaire/functions directory:
foreach (glob(get_stylesheet_directory() . '/functions/*.php') as $file) {
    include_once $file;
}

// Include all classes files in the makerfaire/classes directory:
foreach (glob(get_stylesheet_directory() . '/classes/*.php') as $file) {
    include_once $file;
}

add_action('after_setup_theme', 'genesis_child_gutenberg_support');

/**
 * Adds Gutenberg opt-in features and styling.
 *
 * @since 2.7.0
 */
function genesis_child_gutenberg_support() { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- using same in all child themes to allow action to be unhooked.
    require_once get_stylesheet_directory() . '/lib/gutenberg/init.php';
}

function set_universal_constants() {
    // Assume that we're in prod; only change if we are definitively in another
    $context = stream_context_create(array());
    $host = $_SERVER['HTTP_HOST'];
    // legacy staging environments
    if (strpos($host, '.stagemakehub.wpengine.com') > -1 || strpos($host, '.devmakehub.wpengine.com') > -1) {
        $context = stream_context_create(array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
            ),
                )
        );
    }
    // Set the important bits as CONSTANTS that can easily be used elsewhere
    define('STREAM_CONTEXT', $context);
}

set_universal_constants();

add_action('wp_enqueue_scripts', 'make_campus_enqueue_scripts', 0);

/**
 * Enqueues styles, run this before youzer scripts run
 *
 * @since 1.0.0
 */
function make_campus_enqueue_scripts() {
    $my_theme = wp_get_theme();
    $my_version = $my_theme->get('Version');
    $suffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';
    wp_enqueue_script(
            'make-campus-responsive-menu',
            get_stylesheet_directory_uri() . "/js/site/responsive-menus{$suffix}.js",
            array('jquery'),
            $my_version,
            true
    );

    wp_localize_script(
            'make-campus-responsive-menu',
            'genesis_responsive_menu',
            make_campus_responsive_menu_settings()
    );

    wp_enqueue_script('bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array('jquery'), '', true);
    wp_enqueue_script('fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.6/js/jquery.fancybox.min.js', array('jquery'), '', true);

    wp_enqueue_script('fontawesome5-js', 'https://kit.fontawesome.com/7c927d1b5e.js', array(), '', true);
    wp_enqueue_script('universal', content_url() . '/universal-assets/v1/js/min/universal.min.js', array(), $my_version, true);
    wp_enqueue_script('theme-js', get_stylesheet_directory_uri() . '/js/min/scripts.min.js', array('jquery'), $my_version, true);


    wp_localize_script('make-campus', 'ajax_object',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'home_url' => get_home_url(),
                'logout_nonce' => wp_create_nonce('ajax-logout-nonce'),
                'wp_user_email' => wp_get_current_user()->user_email,
                'wp_user_nicename' => wp_get_current_user()->user_nicename
            )
    );
}

add_action('wp_enqueue_scripts', 'make_campus_enqueue_styles');

/**
 * Enqueues styles.
 *
 * @since 1.0.0
 */
function make_campus_enqueue_styles() {
    $my_theme = wp_get_theme();
    $my_version = $my_theme->get('Version');

    wp_enqueue_style('bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', '', 'all');
    wp_enqueue_style('fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.6/css/jquery.fancybox.min.css', '', 'all');

    ### GENESIS STYLES #####
    $parent_style = 'genesis-style';
    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');

    ### UNIVERSAL STYLES ###
    wp_enqueue_style('universal.css', content_url() . '/universal-assets/v1/css/universal.min.css', array(), $my_version);

    ### SUBTHEME STYLES ###
    wp_enqueue_style('make-campus-style', get_stylesheet_directory_uri() . '/css/style.min.css', array(), $my_version);

    wp_enqueue_style(
            'make-campus-fonts',
            '//fonts.googleapis.com/css?family=Source+Sans+Pro:400,400i,600,700',
            array(),
            $my_version
    );

    wp_enqueue_style('dashicons');
}

// remove the subtheme level style.css to use it as a version
remove_action('genesis_meta', 'genesis_load_stylesheet');

add_filter('show_admin_bar', 'remove_admin_bar', PHP_INT_MAX);

// if you're not an admin, don't show the admin bar
function remove_admin_bar() {
    if (current_user_can('administrator')) {
        return true;
    }

    return false;
}

/**
 * Defines responsive menu settings.
 *
 * @since 2.3.0
 */
function make_campus_responsive_menu_settings() {

    $settings = array(
        'mainMenu' => __('Menu', 'make-campus'),
        'menuIconClass' => 'dashicons-before dashicons-menu',
        'subMenu' => __('Submenu', 'make-campus'),
        'subMenuIconClass' => 'dashicons-before dashicons-arrow-down-alt2',
        'menuClasses' => array(
            'combine' => array(
                '.nav-primary',
            ),
            'others' => array(),
        ),
    );

    return $settings;
}

// Adds support for HTML5 markup structure.
add_theme_support('html5', genesis_get_config('html5'));

// Adds support for accessibility.
add_theme_support('genesis-accessibility', genesis_get_config('accessibility'));

// Adds viewport meta tag for mobile browsers.
add_theme_support('genesis-responsive-viewport');

// Adds custom logo in Customizer > Site Identity.
add_theme_support('custom-logo', genesis_get_config('custom-logo'));

// Renames primary and secondary navigation menus.
add_theme_support('genesis-menus', genesis_get_config('menus'));

// Adds image sizes.
add_image_size('sidebar-featured', 75, 75, true);

// Adds support for after entry widget.
add_theme_support('genesis-after-entry-widget-area');

// Adds support for 3-column footer widgets.
add_theme_support('genesis-footer-widgets', 3);

// Removes header right widget area.
unregister_sidebar('header-right');

// Removes secondary sidebar.
unregister_sidebar('sidebar-alt');

// Removes site layouts.
genesis_unregister_layout('content-sidebar-sidebar');
genesis_unregister_layout('sidebar-content-sidebar');
genesis_unregister_layout('sidebar-sidebar-content');

// Removes output of primary navigation right extras.
remove_filter('genesis_nav_items', 'genesis_nav_right', 10, 2);
remove_filter('wp_nav_menu_items', 'genesis_nav_right', 10, 2);

add_action('genesis_theme_settings_metaboxes', 'make_campus_remove_metaboxes');

/**
 * Removes output of unused admin settings metaboxes.
 *
 * @since 2.6.0
 *
 * @param string $_genesis_admin_settings The admin screen to remove meta boxes from.
 */
function make_campus_remove_metaboxes($_genesis_admin_settings) {
    remove_meta_box('genesis-theme-settings-header', $_genesis_admin_settings, 'main');
    remove_meta_box('genesis-theme-settings-nav', $_genesis_admin_settings, 'main');
}

add_filter('genesis_customizer_theme_settings_config', 'make_campus_remove_customizer_settings');

/**
 * Removes output of header and front page breadcrumb settings in the Customizer.
 *
 * @since 2.6.0
 *
 * @param array $config Original Customizer items.
 * @return array Filtered Customizer items.
 */
function make_campus_remove_customizer_settings($config) {
    unset($config['genesis']['sections']['genesis_header']);
    unset($config['genesis']['sections']['genesis_breadcrumbs']['controls']['breadcrumb_front_page']);
    return $config;
}

// Displays custom logo.
add_action('genesis_site_title', 'the_custom_logo', 0);

// Repositions primary navigation menu.
remove_action('genesis_after_header', 'genesis_do_nav');
add_action('genesis_header', 'genesis_do_nav', 12);

// Repositions the secondary navigation menu.
remove_action('genesis_after_header', 'genesis_do_subnav');
add_action('genesis_footer', 'genesis_do_subnav', 10);

add_filter('genesis_author_box_gravatar_size', 'make_campus_author_box_gravatar');
/* Modifies size of the Gravatar in the author box. */

function make_campus_author_box_gravatar($size) {
    return 90;
}

add_filter('genesis_comment_list_args', 'make_campus_comments_gravatar');
/* Modifies size of the Gravatar in the entry comments. */

function make_campus_comments_gravatar($args) {
    $args['avatar_size'] = 60;
    return $args;
}

// Function to change email address from wordpress to webmaster
function wpb_sender_email($original_email_address) {
    return 'webmaster@make.co';
}

// Function to change sender name
function wpb_sender_name($original_email_from) {
    return 'Make: Community';
}

// Hooking up our functions to WordPress filters 
add_filter('wp_mail_from', 'wpb_sender_email');
add_filter('wp_mail_from_name', 'wpb_sender_name');

// change the subject too
function makeco_password_subject_filter($old_subject) {
    $subject = 'Make: Community - Password Reset';
    return $subject;
}

add_filter('retrieve_password_title', 'makeco_password_subject_filter', 10, 1);

function custom_login_stylesheets() {
    wp_enqueue_style('custom-login', '/wp-content/themes/make-campus/css/style-login.css');
    wp_enqueue_style('custom-login', '/wp-content/universal-assets/v1/css/universal.css');
}

// style the login page and give it the universal header and footer
add_action('login_enqueue_scripts', 'custom_login_stylesheets');

add_action('login_header', function() {
    get_header();
});
add_action('login_footer', function() {
    get_footer();
});


require_once( ABSPATH . 'wp-content/plugins/event-tickets/src/Tribe/Tickets.php');

// All Event fields that aren't standard have to be mapped manually (_1 is the form id) had to remove it from the end of the action because the form name was different on stage :(
add_action('gform_advancedpostcreation_post_after_creation', 'update_event_information', 10, 4);

function update_event_information($post_id, $feed, $entry, $form) {    
    //field mapping - ** note - upload fields don't work here. use post creation feed for that **
    //0 indicie = gravity form field id
    //1 indicie = acf field name/event meta fields
    $field_mapping = array(
        array('4', 'preferred_start_date'),
        array('5', 'preferred_start_time'),
        array('6', 'preferred_end_date'),
        array('7', 'preferred_end_time'),
        array('96', 'alternative_start_date'),
        array('97', 'alternative_start_time'),
        array('98', 'alternative_end_time'),
        array('99', 'alternative_end_date'),        
        array('19', 'about'),
        array('73', 'audience'),
        array('57', 'location'),
        array('72', 'materials'),
        array('78', 'kit_required'),
        array('79', 'kit_price_included'),
        array('80', 'kit_supplier'),
        array('111', 'other_kit_supplier'),
        array('82', 'kit_url'),
        array('83', 'amazon_url'),
        array('87', 'prior_hosted_event'),
        array('88', 'hosted_live_stream'),        
        array('90', 'other_video_conferencing'),
        array('91', 'prev_session_links'),
        array('92', 'comfort_level'),
        array('93', 'technical_setup'),
        array('108', 'basic_skills'),
        array('109', 'skills_taught'),
    );
    
    //update the acf fields with the submitted values from the form
    foreach($field_mapping as $field){
        $fieldID    = $field[0];
        $meta_field = $field[1];
        if(isset($entry[$fieldID])){
            //error_log('updating ACF field '.$meta_field. ' with GF field '.$fieldID . ' with value '.$entry[$fieldID]);
            update_post_meta($post_id, $meta_field, $entry[$fieldID]);
        }
    }
        
    //field 89 - 'video_conferencing' field_5f60f9bfa1d1e   
    $field = GFAPI::get_field($form, 89);
    if ($field->type == 'checkbox') {
        // Get a comma separated list of checkboxes checked
        $checked = $field->get_value_export($entry);
        // Convert to array.
        $values = explode(', ', $checked);    
    }
    update_field('field_5f60f9bfa1d1e', $values, $post_id); 
    
    //field 73 - 'audience' 'field_5f35a5f833a04'
    // Update Audience Checkbox
    $field = GFAPI::get_field($form, 73);
    if ($field->type == 'checkbox') {
        // Get a comma separated list of checkboxes checked
        $checked = $field->get_value_export($entry);
        // Convert to array.
        $values = explode(', ', $checked);    
    }            
    update_field('field_5f35a5f833a04', $values, $post_id); 
        
    //event start date
    $event_st_dt = $entry['4'];
    $event_st_time = $entry['5'];
            
    $date=date_create($event_st_dt.' ' . $event_st_time);
    $start_dt = date_format($date,"Y-m-d H:i:s");    
    update_post_meta($post_id, '_EventStartDate', $start_dt);
    
    //Event End date
    $event_end_dt = $entry['6'];
    $event_end_time = $entry['7'];
    
    $date=date_create($event_end_dt.' ' . $event_end_time);
    $end_dt = date_format($date,"Y-m-d H:i:s");
    update_post_meta($post_id, '_EventEndDate', $end_dt);
    
    
    // create ticket for event // CHANGE TO WOOCOMMERCE AFTER PURCHASING EVENTS PLUS PLUGIN
    //$api = Tribe__Tickets__Commerce__PayPal__Main::get_instance();
	$api = Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance();
    $ticket = new Tribe__Tickets__Ticket_Object();
    $ticket->name = (isset($entry['40'])?$entry['40']:'');
    $ticket->description = (isset($entry['42'])?$entry['42']:'');
    $ticket->price = (isset($entry['37'])?$entry['37']:'');
    $ticket->capacity = (isset($entry['43'])?$entry['43']:'');
    $ticket->start_date = (isset($entry['45'])?$entry['45']:'');
    $ticket->start_time = (isset($entry['46'])?$entry['46']:'');
    $ticket->end_date = (isset($entry['47'])?$entry['47']:'');
    $ticket->end_time = (isset($entry['48'])?$entry['48']:'');
	
	//error_log($ticket);

    // Save the ticket
    $ticket->ID = $api->save_ticket($post_id, $ticket, array(
        'ticket_name' => $ticket->name,
        'ticket_price' => $ticket->price,
        'ticket_description' => $ticket->description,
        'start_date' => $ticket->start_date,
        'start_time' => $ticket->start_time,
        'end_date' => $ticket->end_date,
        'end_time' => $ticket->end_time,
		// none of these work
		'event_capacity' => $ticket->capacity,
		'capacity' => $ticket->capacity,
		'stock' => $ticket->capacity,
        'tribe_ticket' => [
			'mode'           => 'global',
			'event_capacity' => $ticket->capacity,
			'capacity'       => $ticket->capacity
		],
    ));
	
    /*
      $tickets_handler = tribe( 'tickets.handler' );
      $event_stock = new Tribe__Tickets__Global_Stock( $post_id );
      $tickets_handler->remove_hooks();
      // We need to update event post meta - if we've set a global stock
      $event_stock->enable();
      $event_stock->set_stock_level( $ticket->capacity, true );
      update_post_meta( $post_id, $tickets_handler->key_capacity, $ticket->capacity );
      update_post_meta( $post_id, $event_stock::GLOBAL_STOCK_ENABLED, 1 );
      $tickets_handler->add_hooks();
      error_log(print_r($data, TRUE));
      error_log(print_r($ticket, TRUE));
      error_log(print_r($tickets_handler, TRUE));
      error_log(print_r($event_stock, TRUE));
     */
}

function get_event_attendees($event_id) {
	$attendee_list = Tribe__Tickets__Tickets::get_event_attendees($event_id);
	return $attendee_list;
}

// After the gravity view is updated, we want to update the created post associated with it. 
// SO FAR, THIS IS UPDATING THE TITLE, CONTENT, FEATURED IMAGE, AND TEXT ACF FIELDS... needs work for taxonomies
add_action('gravityview/edit_entry/after_update', 'gravityview_event_update', 10, 4);

function gravityview_event_update($form, $entry_id, $entry_object) {
    $post_obj = gform_get_meta($entry_id, "gravityformsadvancedpostcreation_post_id");
    $post_id = $post_obj[0]["post_id"];
    $post_data = array(
        'ID' => $post_id,
        'post_title' => gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Event Title"),
        'post_content' => gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Describe What You Do"),
        'post_category' => gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Experience Type"),
            //'tags_input' => gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Category Tags")
    );
    wp_update_post($post_data);
    //error_log(print_r(gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Experience Type"), TRUE));
    //error_log(print_r(gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Category Tags"), TRUE));
    //error_log("Featured Image: " . print_r(gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Featured Image")), TRUE);
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $media = media_sideload_image(gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Featured Image"), $post_id);
    if (!empty($media) && !is_wp_error($media)) {
        $args = array(
            'post_type' => 'attachment',
            'posts_per_page' => -1,
            'post_status' => 'any',
            'post_parent' => $post_id
        );
        $attachments = get_posts($args);
        if (isset($attachments) && is_array($attachments)) {
            foreach ($attachments as $attachment) {
                $image = wp_get_attachment_image_src($attachment->ID, 'full');
                // determine if in the $media image we created, the string of the URL exists
                if (strpos($media, $image[0]) !== false) {
                    // if so, we found our image. set it as thumbnail
                    set_post_thumbnail($post_id, $attachment->ID);
                    break;
                }
            }
        }
    }
    // Not sure how else to update all the fields but to mention the by name
    update_field("about", gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "About You"), $post_id);
    update_field("location", gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Location"), $post_id);
    update_field("materials", gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Experience Materials"), $post_id);
}

// rather than use potentially changing field ids, look up by label
function gf_get_value_by_label($form, $entry, $label) {
    foreach ($form['fields'] as $field) {
        $lead_key = $field->label;
        if (strToLower($lead_key) == strToLower($label)) {
            return $entry[$field->id];
        }
    }
    return false;
}

add_filter('acf/load_value/type=checkbox', function($value, $post_id, $field) {
    // Value should be an array, not a string
    if (is_string($value)) {
        $value = get_post_meta($post_id, $field['name'], false);
    }
    return $value;
}, 10, 3);


add_filter('gform_ajax_spinner_url', 'spinner_url', 10, 2);
function spinner_url($image_src, $form) {
    return "/wp-content/universal-assets/v1/images/makey-spinner.gif";
} 


function smartTruncate($string, $limit, $break=".", $pad="...")
{
  // return with no change if string is shorter than $limit
  if(strlen($string) <= $limit) return $string;
  // is $break present between $limit and the end of the string?
  if(false !== ($breakpoint = strpos($string, $break, $limit))) {
    if($breakpoint < strlen($string) - 1) {
      $string = substr($string, 0, $breakpoint) . $pad;
    }
  }

  return $string;
}