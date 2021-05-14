<?php
/**
 * LearnDash Private Sessions
 *
 * @package     LDPrivateSessons
 * @author      3.7 Designs
 * @copyright   2017 3.7 Designs
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Private Sessions for LearnDash
 * Plugin URI:  https://example.com/plugin-name
 * Description: Private 1 on 1 coaching sessions for LearnDash
 * Version:     1.3.8.2
 * Author:      SnapOrbital
 * Author URI:  https://3.7designs.co
 * Text Domain: ldmessenger
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

$definitions = array(
    'LDMA_VER'   =>  '1.3.8.2',
    'LDMA_URL'   =>  plugin_dir_url( __FILE__ ),
    'LDMA_PATH'  =>  plugin_dir_path( __FILE__ ),
    'LDMA_ITEM_NAME'    =>  'LearnDash Private Sessions',
    'LDMA_STORE_URL'    =>  'https://www.snaporbital.com',
);

foreach( $definitions as $definition => $value ) {
    if( !defined($definition) ) define( $definition, $value );
}

function learndash_ldps_i18ize() {
	load_plugin_textdomain( 'ldmessenger', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', "learndash_ldps_i18ize" );

add_action( 'plugins_loaded', 'ldms_init', 20 );
function ldms_init() {

     do_action( 'ldma_before_init' );

     $includes = apply_filters( 'ldma_includes', array(
         'model',
         'view',
         'controller',
         'assets',
         'ldms-users',
         'ldms-comments',
         'ldms-shortcodes',
         'ldms-docs',
         'settings',
         'notifications',
         'attachments',
         'widget',
         'integrations/init'
     ) );

     foreach( $includes as $include ) require_once( 'includes/' . $include . '.php' );

     if( ldms_get_option('ldms_file_uploads') != 'disabled' && !class_exists('wpCommentAttachment') ) {
         include( 'vendor/attachments/comment-attachment.php' );
     }

     // retrieve our license key from the DB
     $license_key = trim( ldms_get_option( 'ldms_license_key' ) );

     // setup the updater
     $edd_updater = new EDD_SL_Plugin_Updater( LDMA_STORE_URL, __FILE__, array(
     		'version' 	      => LDMA_VER, 		// current version number
     		'license' 	      => $license_key, 	// license key (used get_option above to retrieve from DB)
     		'item_name'       => LDMA_ITEM_NAME, 	// name of this plugin
     		'author' 	      => '3.7 Designs',  // author of this plugin
     		'url'             => home_url()
     	)
     );

     do_action( 'ldma_after_init' );

}


/**
 * Init routine, create a page for the messages shortcode and save the page_id as an option
 * @var [type]
 */
register_activation_hook( __FILE__, 'lmda_init_plugin' );
function lmda_init_plugin() {

    update_option( 'ldms_show_welcome_page', 1 );

    if( get_option('ldma_installed') == 'true' ) return;

    /**
     * Create a post
     * @var $args array
     */
    $args = array(
        'post_type'     =>  'page',
        'post_status'   =>  'publish',
        'post_title'    =>  __( 'Private Sessions', 'ldmessenger' ),
        'post_content'  =>  '[private_sessions] [create_session]',
    );

    $post_id = wp_insert_post($args);

    $settings = get_option('ldms_private_sessions_settings');
    $settings['ldma_sessions_page'] = $post_id;

    update_option( 'ldms_private_sessions_settings', $settings );

    update_option( 'ldma_installed', 'true' );

}

add_action( 'admin_init', 'ldms_welcome_screen' );
function ldms_welcome_screen() {

    if( get_option('ldms_show_welcome_page') != 1 ) return;

    update_option( 'ldms_show_welcome_page', 0 );

    wp_redirect( add_query_arg( array( 'page' => 'ldms-welcome-screen' ), admin_url( 'index.php' ) ) );

    exit();

}

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}
