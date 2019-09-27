<?php
/**
 * Plugin Name: Youzer - Member Types
 * Plugin URI:  http://youzer.kainelabs.com
 * Description: Member Types plugin is the best way to create and manage unlimited member types easily, and get a separate directory for each member type.
 * Version:     1.0.3
 * Author:      Youssef Kaine
 * Author URI:  http://www.kainelabs.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: youzer-member-types
 * Domain Path: /languages/
 */
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Check if Youzer Plugin is installed
 */
function yzmt_check_for_youzer_plugin() {

    if ( ! class_exists( 'Youzer' ) ) {

        function yzmt_extension_admin_notice() {

            ?>
            <div class="notice notice-warning is-dismissible">
                <p><?php echo sprintf( __( '<strong> Notice : </strong>Please install and activate <strong><a href="%1s">Youzer</strong></a> plugin to use the <strong>Youzer - Member Types</strong> extension.', 'youzer-member-types' ), 'https://codecanyon.net/item/youzer-new-wordpress-user-profiles-era/19716647' ); ?></p>
            </div>
            <?php
        }

        // Show Notice.
        add_action( 'admin_notices', 'yzmt_extension_admin_notice' );

        // Deactivate The Plugin.
        deactivate_plugins( plugin_basename( __FILE__ ) );

        return;

    }
    
}

// Check for Youzer.
add_action( 'admin_init', 'yzmt_check_for_youzer_plugin' );

// Youzer Basename
define( 'YZMT_BASENAME', plugin_basename( __FILE__ ) );

// Youzer Path.
define( 'YZMT_PATH', plugin_dir_path( __FILE__ ) );

// Youzer Path.
define( 'YZMT_URL', plugin_dir_url( __FILE__ ) );

// Public & Admin Core Path's
define( 'YZMT_PUBLIC', YZMT_PATH . 'public/' );
define( 'YZMT_ADMIN', YZMT_PATH . 'admin/' );

// Assets ( PA = Public Assets & AA = Admin Assets ).
define( 'YZMT_PA', plugin_dir_url( __FILE__ ) . 'public/assets/' );
define( 'YZMT_AA', plugin_dir_url( __FILE__ ) . 'admin/assets/' );

/**
 * Init Member Types.
 */
function youzer_member_types_init() {

	// Init.
	require_once YZMT_PATH . 'class.youzer-member-types.php';

	global $Youzer_Member_Types;

	// Init Class
	$Youzer_Member_Types = new Youzer_Member_Types();


	// Init Admin
	if ( is_admin() ) {
	    require_once YZMT_ADMIN . '/class.yz-member-types-admin.php';
	    $Youzer_Member_Types_Admin = new Youzer_Member_Types_Admin();
	}

}

add_action( 'youzer_after_setup_actions', 'youzer_member_types_init' );

/**
 * Install New Version
 */
function yzmt_install_new_version() {
    
    if ( ! get_option( 'yzmt_install_version_1_0_3' ) ) {

        // Get Member Types.
        $member_types = yz_options( 'yz_member_types' );

        if ( ! empty( $member_types ) ) {
            
            foreach ( $member_types as $key => $member_type ) {

                if ( ! isset( $member_type['id'] ) ) {
                
                    $type_id = yz_get_member_type_id( $member_type['singular'] );

                    if ( empty( $type_id ) ) {
                        $type_id = $member_type['singular'];
                    }

                    $member_types[ $key ]['id'] = $type_id;
                }
            }

            update_option( 'yz_member_types', $member_types );
        }

        update_option( 'yzmt_install_version_1_0_3', 1 );
    }
}

add_action( 'init', 'yzmt_install_new_version' );

/**
 * On Youzer Member Types Activation Hook.
 */
function youzer_member_types_activation() {

    // Install Options.
    if ( ! get_option( 'yz_next_member_type_nbr' ) ) {
        update_option( 'yz_next_member_type_nbr', '1' );
    }

}

register_activation_hook( __FILE__, 'youzer_member_types_activation' );