<?php
remove_action('wp_head', 'wp_generator');

//universal functions
require_once(ABSPATH . 'wp-content/universal-assets/v2/universal-functions.php');

// parent theme styles - required
function onecommunity_child_enqueue_styles() {
	wp_enqueue_style('parent-theme', get_template_directory_uri() .'/style.css');
}
add_action('wp_enqueue_scripts', 'onecommunity_child_enqueue_styles');


/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Make: Community  1.0.0
 */
function make_community_scripts_styles() {
    $my_theme = wp_get_theme();
    $my_version = $my_theme->get('Version');
    
    ### SUBTHEME STYLES ###
    wp_enqueue_style('make-co-style', get_stylesheet_directory_uri() . '/css/style.min.css', array(), $my_version);
    //buddypress pages not always getting their css correctly
    if ( bp_current_component() ) {
        wp_enqueue_style( 'bp-nouveau-css', get_template_directory_uri() . "/buddypress/css/buddypress.min.css", array(), $my_version);
    }
    
    // lib src packages
    wp_enqueue_script('built-libs-js', get_stylesheet_directory_uri() . '/js/min/built-libs.min.js', array('jquery'), $my_version, true);
    wp_enqueue_script('make_co-js', get_stylesheet_directory_uri() . '/js/min/scripts.min.js', array('jquery'), $my_version, true);
    
    if ( is_page_template('page-media-center.php') ) {
        wp_enqueue_script( 'jquery-ui-tabs' );
    }
}
add_action('wp_enqueue_scripts', 'make_community_scripts_styles', 9999);


// the default wp user created emails are bad, we got auth0 for that
function disable_new_user_notifications() {
    remove_action( 'register_new_user', 'wp_send_new_user_notifications' );
}
add_action( 'init', 'disable_new_user_notifications' );

// Include all function files in the /functions directory:
foreach (glob(get_stylesheet_directory() . '/functions/*.php') as $file) {
    include_once $file;
}

// Include all custom post type files in the /cpts directory:
foreach (glob(get_stylesheet_directory() . '/cpt/*.php') as $file) {
    include_once $file;
}

// Include all widget files in the /widgets directory:
foreach (glob(dirname(__FILE__) . '/widgets/*.php') as $file) {
    include_once $file;
}

// Include all widget files in the /widgets/classes directory:
foreach (glob(dirname(__FILE__) . '/widgets/classes/*.php') as $file) {
    include_once $file;
}

function remove_unnecessary_styles() {
    wp_dequeue_style( 'font-awesome' );
    
    /* unless user is admin user, they don't need the dashicons - NOTE: dashicons is used by plugins, need to see what else can be done to switch those plugins to fontawesome
    if (!current_user_can( 'manage_options' )) {
        wp_deregister_style('dashicons');
    }*/
    
    // Check if LearnDash exists to prevent fatal errors.
    if ( class_exists( 'SFWD_LMS' ) ) {
        if( !is_singular( array( 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz', 'sfwd-assignment' ) ) ) {
            // Remove Default LearnDash Styles;
            wp_dequeue_style( 'learndash_lesson_video-css' );
            wp_dequeue_style( 'ldvc-css' );
            wp_dequeue_style( 'learndash_quiz_front_css' );
            wp_dequeue_style( 'learndash-front' );
            wp_deregister_style( 'learndash-front' );
            wp_dequeue_style( 'learndash-front' ); 
        }
    }
}
add_action( 'wp_print_styles', 'remove_unnecessary_styles', PHP_INT_MAX ); // we want this to happen absolutely last

function remove_unnecessary_scripts() {
    if (is_admin()) {
        if (is_plugin_active( 'elementor/elementor.php' )) {
            wp_deregister_script( 'elementor-ai' );
            wp_dequeue_script( 'elementor-ai' );
        }
    } 
    // Check if LearnDash exists to prevent fatal errors.
    if ( class_exists( 'SFWD_LMS' ) ) {
        if( !is_singular( array( 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz', 'sfwd-assignment' ) ) ) {
            wp_deregister_script( 'learndash-front' );
            wp_dequeue_script( 'learndash-front' );
        }
    }
    if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
		$script = $scripts->registered['jquery'];
		if ( $script->deps ) {
			$script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
		}
	}	
}
add_action( 'wp_print_scripts', 'remove_unnecessary_scripts', PHP_INT_MAX ); // we want this to happen absolutely last

// prevent password changed email
add_filter( 'send_password_change_email', '__return_false' );

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

        // let's see if your the group owner and what kind of group it is (hidden, private, etc)
        if (bp_is_groups_component()) {
            $classes[] = 'group-' . groups_get_group(array('group_id' => bp_get_current_group_id()))->status;
            if (current_user_can('manage_options') || groups_is_user_mod(get_current_user_id(), bp_get_current_group_id()) || groups_is_user_admin(get_current_user_id(), bp_get_current_group_id())) {
                $classes[] = 'my-group';
            }
        }
        // add the users membership levels to the body class so specific pages can be styled differently based on membership
        foreach (CURRENT_MEMBERSHIPS as $membership) {
            $classes[] = "member-level-" . str_replace(' ', '-',strtolower($membership));
        }
        return $classes;
    }
}
add_filter('body_class', 'add_slug_body_class', 12);