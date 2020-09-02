<?php
/**
 * Plugin Name: Youzer - Edit Activity
 * Plugin URI:  https://www.kainelabs.com/buddypress-edit-activity
 * Description: Allow members to edit activity posts, comments with real time modifications. Set editable activities by type & moderators & limit edition by user role.
 * Author:      Youssef Kaine
 * Author URI:  https://www.kainelabs.com/
 * Text Domain: youzer-edit-activity
 * Domain Path: /languages/
 * Version:     1.0.8
 */
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Plugin Class.
 */
class Youzer_Edit_Activity {

    /**
     * Constructor..
     */
    private function __construct() { /** KEEP IT EMPTY ! */ }

    /**
     * Main Youzer_Profile_Completeness Instance.
     */
    public static function instance() {

		static $instance = null;

		if ( null === $instance ) {

			$instance = new Youzer_Edit_Activity();

	        if ( ! $instance->have_required_plugins()) {
	            return;
	        }

			$instance->constants();
			$instance->includes();
			$instance->setup_actions();
			$instance->setup_textdomain();

		}

		return $instance;

    }

	/**
	 * Constants.
	 */
	private function constants() {

		// Plugin FILE
		define( 'YZEA_FILE', __FILE__ );

		// Plugin Basename
		define( 'YZEA_BASENAME', plugin_basename( __FILE__ ) );

		// Plugin Path.
		define( 'YZEA_PATH', plugin_dir_path( __FILE__ ) );

		// Plugin URL.
		define( 'YZEA_URL', plugin_dir_url( __FILE__ ) );

		// Plugin Version.
		define( 'YZEA_VERSION', '1.0.8' );

	}

	/**
	 * Includes
	 */
	private function includes() {
		include_once YZEA_PATH . 'includes/functions/yz-general-functions.php';
	}

	/**
	 * Setup Actions.
	 */
	private function setup_actions() {

		// Admin
		add_action( 'init', array( $this, 'load_admin' ) );

		// Hook into Youzer init
		add_action( 'bp_init', array( $this, 'load' ) );

	}

	/**
	 * Include Admin Files.
	 */
	public function load_admin() {

		if ( ( is_admin() || is_network_admin() ) && current_user_can( 'manage_options' ) ) {
			require_once( YZEA_PATH . 'includes/yz-class-admin.php' );
		}

	}

	/**
	 * Load plugin text domain.
	 */
	public function setup_textdomain() {

		// Get Plugin Domain.
		$domain = 'youzer-edit-activity';

		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		// Load Translation from the wordprss Languages Directory First.
		load_textdomain( $domain, WP_LANG_DIR . '/plugins/' . $domain . '-' . $locale . '.mo' );

		// Load The Current Plugin Translation.
		load_plugin_textdomain( $domain, false, 'youzer-edit-activity/languages' );

	}

	/**
	 * Load Main Files.
	 */
	public function load() {

		// Includes.
		include_once YZEA_PATH . 'includes/yz-class-edit-form.php';

		$edit_form = new Youzer_Activity_Edit_Form();

	}

    /**
     * Check Extension Dependencies.
     */
    public function have_required_plugins() {

        // Get Required Fields.
        $required_plugins = array( 'youzer' => 'youzer'  );

        // Get Active Plugins List.
        $active_plugins = (array) get_option( 'active_plugins', array() );

        // Check is Multisite.
        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }

        // Check if plugin exist.
        foreach ( $required_plugins as $key => $required ) {

            $required = ( ! is_numeric( $key ) ) ? "{$key}/{$required}.php" : "{$required}/{$required}.php";

            if ( ! in_array( $required, $active_plugins ) && ! array_key_exists( $required, $active_plugins ) ) {
                // Show Notice.
                add_action( 'admin_notices', array( $this, 'activation_notice' ) );
                return false;
            }

        }

        return true;

    }

    /**
     * Activate Youzer Notice.
     */
    function activation_notice() {

        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php echo sprintf( __( '<strong> Notice : </strong>Please install and activate <strong><a href="%1s">Youzer</strong></a> plugin to use the <strong>Youzer - Edit Activity</strong> extension.', 'youzer-edit-activity' ), 'https://codecanyon.net/item/youzer-new-wordpress-user-profiles-era/19716647' ); ?></p>
        </div>
        <?php
    }

}

// Init.
function youzer_edit_activity() {
    return Youzer_Edit_Activity::instance();
}

// Init Edit Activity.
youzer_edit_activity();