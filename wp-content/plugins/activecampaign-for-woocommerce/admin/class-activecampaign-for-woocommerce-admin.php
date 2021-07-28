<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.activecampaign.com/
 * @since      1.0.0
 *
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/admin
 */

use Activecampaign_For_Woocommerce_Admin_Settings_Updated_Event as Admin_Settings_Updated;
use Activecampaign_For_Woocommerce_Admin_Settings_Validator as Validator;
use Activecampaign_For_Woocommerce_Logger as Logger;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/admin
 * @author     acteamintegrations <team-integrations@activecampaign.com>
 */
class Activecampaign_For_Woocommerce_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The response array that will be returned.
	 *
	 * @var array The array.
	 */
	private $response = [];

	/**
	 * The class that handles validating options changes.
	 *
	 * @var Validator The validator class.
	 */
	private $validator;

	/**
	 * The event class to be triggered after a successful options update.
	 *
	 * @var Activecampaign_For_Woocommerce_Admin_Settings_Updated_Event The event class.
	 */
	private $event;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param     string                 $plugin_name     The name of this plugin.
	 * @param     string                 $version     The version of this plugin.
	 * @param     Validator              $validator     The validator for the admin options.
	 * @param     Admin_Settings_Updated $event     The admin settings updated event class.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version, Validator $validator, Admin_Settings_Updated $event ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->validator   = $validator;
		$this->event       = $event;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/activecampaign-for-woocommerce-admin.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_register_script(
			$this->plugin_name . 'admin',
			plugin_dir_url( __FILE__ ) . 'js/activecampaign-for-woocommerce-admin.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		wp_register_script(
			$this->plugin_name . 'abandoned-cart',
			plugin_dir_url( __FILE__ ) . 'scripts/activecampaign-for-woocommerce-abandoned-cart.js',
			array( 'jquery' ),
			$this->version,
			true
		);

	}

	/**
	 * Register the page for the admin section, adds to the WooCommerce menu parent
	 *
	 * @since    1.0.0
	 */
	public function add_admin_page() {
		$ac_icon = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMzYiIHZpZXdCb3g9IjAgMCAyNCAzNiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0wLjQzNDYzMyAwLjIxNzMwM0MxLjM3NjI4IDAuNzk2NzggMjIuMDIwMSAxNS4yMTEzIDIyLjUyNzIgMTUuNjQ1OUMyMy4zOTY0IDE2LjIyNTQgMjMuODMxIDE2Ljk0OTcgMjMuODMxIDE3LjY3NFYxOC4xMDg3QzIzLjgzMSAxOC42ODgxIDIzLjYxMzcgMTkuNjI5OCAyMi41OTk2IDIwLjM1NDFDMjIuMTY1IDIwLjcxNjMgMC4yMTczMzggMzYgMC4yMTczMzggMzZWMzIuNTk1NkMwLjIxNzMzOCAzMS41ODE1IDAuMjg5NzM3IDMxLjA3NDQgMS40NDg2OSAzMC4zNTAxQzIuMzkwMzQgMjkuNzcwNiAxNy4zMTE5IDE5LjQxMjUgMTkuMjY3NiAxOC4wMzYyQzE4LjMzNTkgMTcuMzgwNiAxNC41MTA3IDE0LjcxOTIgMTAuNjUwOCAxMi4wMzM3QzYuNDA4NjQgOS4wODIyNCAyLjEyNDU0IDYuMTAxNiAxLjU5MzU3IDUuNzIyMzRMMS40NDg2OSA1LjY0OTlDMS4zOTM3MyA1LjYwNTkzIDEuMzM5NjEgNS41NjMyMiAxLjI4NjUxIDUuNTIxMzFDMC42Mzk3NDggNS4wMTA4OCAwLjE0NDg3NyA0LjYyMDMyIDAuMTQ0ODc3IDMuNTQ5M1YwTDAuNDM0NjMzIDAuMjE3MzAzWk0xMS4zNzIyIDE4Ljk3NzhDMTAuNzkyOCAxOS40MTI1IDEwLjIxMzMgMTkuNjI5OCA5LjYzMzg0IDE5LjYyOThDOS4xMjY4IDE5LjYyOTggOC42MTk3NSAxOS40ODQ5IDguMDQwMjcgMTkuMTIyN0M2LjczNjQ1IDE4LjI1MzUgMC4xNDQ4OTYgMTMuNjkwMSAwLjA3MjQ2MTEgMTMuNjE3N0wwIDEzLjU0NTNWMTEuMjk5OEMwIDEwLjcyMDMgMC4yODk3NTYgMTAuMzU4MSAwLjY1MTkyOSAxMC4xNDA4QzEuMDE0MSA5LjkyMzUyIDEuNTkzNiA5Ljk5NTk2IDIuMDI4MiAxMC4zNTgxQzMuMDQyMjkgMTEuMDEgMTIuNjc2MSAxNy42NzQgMTIuNzQ4NSAxNy43NDY1TDEyLjk2NTggMTcuODkxM0wxMi43NDg1IDE4LjAzNjJDMTIuNzQ4NSAxOC4wMzYyIDEyLjA5NjYgMTguNDcwOCAxMS4zNzIyIDE4Ljk3NzhaIiBmaWxsPSIjMDA0Q0ZGIi8+Cjwvc3ZnPgo=';

		add_menu_page(
			'ActiveCampaign for WooCommerce',
			'ActiveCampaign',
			'manage_options',
			ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE,
			array( $this, 'fetch_admin_page' ),
			$ac_icon,
			'55.8'
		);

		add_submenu_page(
			ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE,
			'ActiveCampaign for WooCommerce Settings',
			'WooCommerce Settings',
			'manage_options',
			ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE,
			array( $this, 'fetch_admin_page' )
		);

		add_submenu_page(
			ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE,
			'ActiveCampaign for WooCommerce Abandoned Carts',
			'Abandoned Carts',
			'manage_options',
			ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE . '_abandoned_carts',
			array( $this, 'fetch_abandoned_cart_page' )
		);

		// Temporarily keep the old menu item so users don't get confused
		// Will be deprecated soon
		add_submenu_page(
			'woocommerce',
			'ActiveCampaign for WooCommerce',
			'ActiveCampaign for WooCommerce',
			'manage_options',
			ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE,
			array( $this, 'fetch_admin_page' )
		);
	}

	/**
	 * This function adds to our plugin listing on the plugin page a link to our settings page.
	 *
	 * @param     array $links     The existing links being passed in.
	 *
	 * @return array
	 */
	public function add_plugin_settings_link( $links ) {
		$html_raw = '<a href="%s" aria-label="%s">%s</a>';

		$html = sprintf(
			$html_raw,
			admin_url( 'admin.php?page=' . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE ),
			esc_attr__(
				'View ActiveCampaign settings',
				ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN
			),
			esc_html__( 'Settings', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN )
		);

		$action_links = [
			$html,
		];

		return array_merge( $action_links, $links );
	}

	/**
	 * Fetch the PHP template file that is used for the admin page
	 *
	 * @since    1.0.0
	 */
	public function fetch_admin_page() {
		wp_enqueue_script( $this->plugin_name . 'admin' );
		require_once plugin_dir_path( __FILE__ )
					 . 'partials/activecampaign-for-woocommerce-admin-display.php';
	}

	/**
	 * Populates an admin notice.
	 */
	public function error_admin_notice() {
		global $pagenow;

		// Verify we're on an admin section
		if ( 'admin.php' === $pagenow ) {
			global $wpdb;

			$err_count = $wpdb->get_var(
				'SELECT COUNT(log_id) 
							FROM ' . $wpdb->prefix . 'woocommerce_log
							WHERE source = "activecampaign-for-woocommerce"
							AND level = "500"
						'
			);

			if ( $err_count ) {
				if ( function_exists( 'wc_admin_url' ) ) {
					$admin_log_url = wc_admin_url(
						'status',
						array(
							'page' => 'wc-status',
							'tab'  => 'logs',
						)
					);
				} else {
					$admin_log_url = admin_url( 'admin.php?page=wc-status&tab=logs' );
				}

				echo '<div id="activecampaign-for-woocommerce-notice-error" class="notice notice-error is-dismissible activecampaign-for-woocommerce-error"><p>' .
					esc_html(
						'The ActiveCampaign for WooCommerce plugin has recorded ' . $err_count . ' ' .
							translate_nooped_plural(
								[
									'singular' => 'error',
									'plural'   => 'errors',
									'domain'   => ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN,
									'context'  => null,
								],
								$err_count
							) .
							   '.'
					) .
					 '<br/><a href="' . esc_url( $admin_log_url ) . '">' . esc_html( 'Please check the ActiveCampaign logs for issues.' ) .
					 '</a></p></div>
						<script type="text/javascript">
						jQuery(document).ready(function($) {
						    $("#activecampaign-for-woocommerce-notice-error").click(function(){
								jQuery.ajax({
						            url: ajaxurl,
							        data: {
												action: "activecampaign_for_woocommerce_dismiss_error_notice"
							        }
						        });
							});
						});
					</script>';
			}
		}
	}

	/**
	 * Updates the dismiss error notice option in the database
	 */
	public function update_dismiss_error_notice_option() {
		$setting                          = json_decode( get_option( 'activecampaign_for_woocommerce_dismiss_error_notice' ), 'array' );
		$setting[ get_current_user_id() ] = 1;
		update_option( 'activecampaign_for_woocommerce_dismiss_error_notice', wp_json_encode( $setting ) );
	}

	/**
	 * Clears the error log history.
	 */
	public function clear_error_logs() {
		$logger = new Logger();
		$count  = $logger->clear_wc_error_log();
		delete_option( 'activecampaign_for_woocommerce_dismiss_error_notice' );
		wp_send_json_success(
			$count . ' ' .
			translate_nooped_plural(
				[
					'singular' => 'record',
					'plural'   => 'records',
					'domain'   => ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN,
					'context'  => null,
				],
				$count
			) . ' removed from the database.'
		);
	}

	/**
	 * Gets the most recent 10 error log entries saved
	 *
	 * @return array|object|null
	 */
	private function fetch_recent_log_errors() {
		global $wpdb;
		$results = $wpdb->get_results(
			'SELECT message, context 
							FROM ' . $wpdb->prefix . 'woocommerce_log
							WHERE source = "activecampaign-for-woocommerce"
							AND level = "500" 
							ORDER BY timestamp DESC
							LIMIT 10
						'
		);

		return $results;
	}

	/**
	 * Fetch the PHP template file that is used for the admin abandoned cart page.
	 *
	 * @since    1.3.7
	 */
	public function fetch_abandoned_cart_page() {
		wp_enqueue_script( $this->plugin_name . 'abandoned-cart' );

		require_once plugin_dir_path( __FILE__ )
					 . 'partials/activecampaign-for-woocommerce-abandoned-cart-display.php';
	}

	/**
	 * Radio options for "How long after a cart is abandoned should ActiveCampaign trigger automations?"
	 * How long should we wait until we determine a cart is abandoned?
	 * These options let the user decide.
	 */
	public function get_ab_cart_wait_options() {
		$options = wp_json_encode(
			[
				// value     // label
				'1'  => esc_html__( '1 hour (recommended)', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ),
				'6'  => esc_html__( '6 hours', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ),
				'10' => esc_html__( '10 hours', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ),
				'24' => esc_html__( '24 hours', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ),
			]
		);

		return $options;
	}

	/**
	 * Gets the abandoned carts from our table.
	 *
	 * @param     int $page The page number.
	 *
	 * @return array|object|null
	 */
	public function get_abandoned_carts( $page = 0 ) {
		global $wpdb;
		$limit  = 40;
		$offset = $page * $limit;
		return $wpdb->get_results(
			// phpcs:disable
			$wpdb->prepare(
				'SELECT id, synced_to_ac, customer_id, customer_email, customer_first_name, customer_last_name, last_access_time
        		FROM `' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME . '` LIMIT %d,%d',
				[ $offset, $limit ]
			), OBJECT
			// phpcs:enable
		);
	}

	/**
	 * Get the abandoned carts total.
	 *
	 * @return string|null
	 */
	public function get_total_abandoned_carts() {
		global $wpdb;
		// phpcs:disable
		return $wpdb->get_var( 'SELECT COUNT(id) FROM `' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME . '`' );
		// phpcs:enable
	}

	/**
	 * Get the unsynced abandoned cart total.
	 *
	 * @return string|null
	 */
	public function get_total_abandoned_carts_unsynced() {
		global $wpdb;
		// phpcs:disable
		return $wpdb->get_var( 'SELECT COUNT(id) FROM `' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME . '` WHERE synced_to_ac = 0' );
		// phpcs:enable
	}

	/**
	 * Triggers abandoned cart sync action
	 */
	public function handle_abandon_cart_sync() {
		do_action( 'activecampaign_for_woocommerce_run_manual_abandonment_sync' );
	}

	/**
	 * Handles the abandoned cart delete function and triggers the manual delete
	 */
	public function handle_abandon_cart_delete() {
		if ( ! wp_verify_nonce( $_REQUEST['activecampaign_for_woocommerce_settings_nonce_field'], 'activecampaign_for_woocommerce_abandoned_form' ) ) {
			$this->logger->warning( 'Invalid nonce from the delete abandoned cart row call:', [ 'request' => $_REQUEST ] );
			wp_send_json_error( 'The nonce appears to be invalid.' );
			return false;
		}

		if ( isset( $_REQUEST['rowId'] ) ) {
			do_action( 'activecampaign_for_woocommerce_run_manual_abandonment_delete', $_REQUEST['rowId'] );
		} else {
			$this->logger->warning( 'Invalid request, rowId missing from the delete abandoned cart call:', [ 'request' => $_REQUEST ] );
			wp_send_json_error( 'No row ID defined.' );
		}
	}

	/**
	 * Handles the abandoned cart sync function and triggers the manual forced sync
	 */
	public function handle_abandon_cart_force_row_sync() {
		if ( ! wp_verify_nonce( $_REQUEST['activecampaign_for_woocommerce_settings_nonce_field'], 'activecampaign_for_woocommerce_abandoned_form' ) ) {
			$this->logger->warning( 'Invalid nonce from the force row sync call:', [ 'request' => $_REQUEST ] );
			wp_send_json_error( 'The nonce appears to be invalid.' );
			return false;
		}

		if ( isset( $_REQUEST['rowId'] ) ) {
			do_action( 'activecampaign_for_woocommerce_run_force_row_abandonment_sync', $_REQUEST['rowId'] );
		} else {
			$this->logger->warning( 'Invalid request, rowId missing from the force row sync call:', [ 'request' => $_REQUEST ] );
			wp_send_json_error( 'The request appears to be invalid. The rowId is missing from the request.' );
		}
	}

	/**
	 * Radio option for toggling debug logging.
	 */
	public function get_ac_debug_options() {
		return wp_json_encode(
			[
				// value  // label
				'1' => esc_html__( 'On', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ),
				'0' => esc_html__( 'Off', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ),
			]
		);
	}

	/**
	 * Radio options for "Checkbox display options"
	 */
	public function get_checkbox_display_options() {
		$options = wp_json_encode(
			[
				// value                          // label
				'visible_checked_by_default'   => esc_html__(
					'Visible, checked by default',
					ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN
				),
				'visible_unchecked_by_default' => esc_html__(
					'Visible, unchecked by default',
					ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN
				),
				'not_visible'                  => esc_html__(
					'Not visible',
					ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN
				),
			]
		);

		return $options;
	}

	/**
	 * Returns an encoded array of existing notices to be displayed on page-load.
	 *
	 * Once displayed, these notifications are then removed so they don't constantly build up in the
	 * UI.
	 *
	 * @return string
	 */
	public function get_admin_notices() {
		$storage = $this->get_storage();

		$notifications = isset( $storage['notifications'] ) ? $storage['notifications'] : [];

		$this->update_storage(
			[
				'notifications' => [],
			]
		);

		return wp_json_encode( $notifications );
	}

	/**
	 * Handles the API Test request from the settings page,
	 * then redirects back to the plugin page
	 */
	public function handle_api_test() {
		if ( ! $this->validate_request_nonce() ) {
			wp_send_json_error( $this->get_response(), 403 );
		}

		$new_data     = $this->extract_post_data();
		$current_data = get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_OPTION_NAME );

		$errors = $this->validator->validate( $new_data, $current_data, true );

		if ( ! empty( $errors ) ) {
			foreach ( $errors as $error ) {
				$this->push_response_error(
					$this->format_response_message(
						$error,
						'error'
					)
				);
			}
		}

		if ( $this->response_has_errors() ) {
			wp_send_json_error( $this->get_response(), 422 );
		}

		$this->push_response_notice(
			$this->format_response_message( 'API tested successfully!', 'success' )
		);

		wp_send_json_success( $this->get_response() );
	}


	/**
	 * Handles the form submission for the settings page,
	 * then redirects back to the plugin page.
	 */
	public function handle_settings_post() {
		if ( ! $this->validate_request_nonce() ) {
			wp_send_json_error( $this->get_response(), 403 );
		}

		$post_data = $this->extract_post_data();

		$this->update_options( $post_data );

		if ( $this->response_has_errors() ) {
			wp_send_json_error( $this->get_response(), 422 );
		}

		$this->push_response_notice(
			$this->format_response_message( 'Settings successfully updated!', 'success' )
		);

		wp_send_json_success( $this->get_response() );
	}

	/**
	 * Returns the options values in the DB.
	 *
	 * @return array
	 */
	public function get_options() {
		if ( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_OPTION_NAME ) {
			return get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_OPTION_NAME );
		} else {
			return get_option( 'activecampaign_for_woocommerce_settings' );
		}
	}

	/**
	 * Updates the settings options in the DB.
	 *
	 * @param     array $data     An array of data that will be serialized into the DB.
	 *
	 * @return array
	 * @throws Exception When the container is missing definitions.
	 */
	public function update_options( $data ) {
		$current_settings = get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_OPTION_NAME );

		$this->validate_options_update( $data, $current_settings );

		if ( $this->response_has_errors() ) {
			return $this->response;
		}

		$api_url_changing = $this->api_url_is_changing( $data, $current_settings );

		if ( $current_settings ) {
			$data = array_merge( $current_settings, $data );
		}

		update_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_OPTION_NAME, $data );

		$this->event->trigger(
			[
				'api_url_changed' => $api_url_changing,
			]
		);
		return $this->get_options();
	}

	/**
	 * Returns the storage values in the DB.
	 *
	 * @return array
	 */
	public function get_storage() {
		return get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_STORAGE_NAME );
	}

	/**
	 * Updates the storage values in the DB.
	 *
	 * @param     array $data     An array of data that will be serialized into the DB.
	 *
	 * @return bool
	 */
	public function update_storage( $data ) {
		$current_settings = get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_STORAGE_NAME );

		if ( $current_settings ) {
			$data = array_merge( $current_settings, $data );
		}

		update_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_STORAGE_NAME, $data );

		return true;
	}

	/**
	 * Allows an event listener/async process to store a notification to be displayed
	 * on the next settings page load.
	 *
	 * @param     string $message     The message to be translated and escaped for display.
	 * @param     string $level     The level of severity of the message.
	 */
	public function add_async_processing_notification( $message, $level = 'info' ) {
		$current_storage = $this->get_storage();

		if ( ! isset( $current_storage['notifications'] ) ) {
			$current_storage['notifications'] = [];
		}

		$notifications = $current_storage['notifications'];

		$notifications[] = $this->format_response_message( $message, $level );

		$this->update_storage(
			[
				'notifications' => $notifications,
			]
		);
	}

	/**
	 * Validates the request nonce for the settings form to ensure valid requests.
	 *
	 * @return bool
	 */
	private function validate_request_nonce() {
		/**
		 * Validate the nonce created for this specific form action.
		 * The nonce input is generated in the template using the wp_nonce_field().
		 */
		$valid = wp_verify_nonce(
			$_POST['activecampaign_for_woocommerce_settings_nonce_field'],
			'activecampaign_for_woocommerce_settings_form'
		);

		if ( ! $valid ) {
			$this->push_response_error(
				$this->format_response_message( 'Form nonce is invalid.', 'error' )
			);
		}

		return $valid;
	}

	/**
	 * Extracts from the $_POST superglobal an array of sanitized data.
	 *
	 * Before sanitizing the data, certain key/value pairs from the array are
	 * removed. This is because CSRF values are currently in the POST body
	 * and we do not want to persist them to the DB.
	 *
	 * @return array
	 */
	private function extract_post_data() {
		$post_data = $_POST;

		/**
		 * Unset all the form fields that don't need to be persisted in the DB.
		 */
		unset( $post_data['action'] );
		unset( $post_data['activecampaign_for_woocommerce_settings_nonce_field'] );
		unset( $post_data['_wp_http_referer'] );

		/**
		 * Map through all values sent in and sanitize them.
		 */
		$post_data = array_map(
			function ( $entry ) {
				return sanitize_text_field( $entry );
			},
			$post_data
		);

		return $post_data;
	}

	/**
	 * Translates and sanitizes error/notice messages into an associative array.
	 *
	 * This will be returned as part of a response to be displayed as a notice in the
	 * admin section of the site.
	 *
	 * @param     string $message     The message that will be translated and returned.
	 * @param     string $level     The notice level (e.g. info, success...).
	 *
	 * @return array
	 */
	private function format_response_message( $message, $level = 'info' ) {
		// phpcs:disable
		return [
			'level'   => sanitize_text_field( $level ),
			'message' => esc_html__(
				$message,
				ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN
			),
		];
		// phpcs:enable
	}

	/**
	 * Adds to the array of response errors a new error.
	 *
	 * @param     array $error     The error associative array containing the error message and level.
	 */
	private function push_response_error( $error ) {
		if ( ! isset( $this->response['errors'] ) ) {
			$this->response['errors'] = [];
		}

		$this->response['errors'][] = $error;
	}

	/**
	 * Adds to the array of response notices a new notice.
	 *
	 * @param     array $notice     The notice associative array containing the notice message and level.
	 */
	private function push_response_notice( $notice ) {
		if ( ! isset( $this->response['notices'] ) ) {
			$this->response['notices'] = [];
		}

		$this->response['notices'][] = $notice;
	}

	/**
	 * Returns an array of the response array with notices and errors
	 * merged with the current state of the options array.
	 *
	 * @return array
	 */
	private function get_response() {
		return array_merge( $this->response, $this->get_options() ?: [] );
	}

	/**
	 * Checks whether or not the current response contains errors.
	 *
	 * @return bool
	 */
	private function response_has_errors() {
		return isset( $this->response['errors'] ) &&
			   count( $this->response['errors'] ) > 0;
	}

	/**
	 * Validates the new data for the options table.
	 *
	 * @param     array $new_data     The array of data to be updated.
	 * @param     array $current_data     The existing data for the options.
	 */
	private function validate_options_update( $new_data, $current_data ) {
		$errors = $this->validator->validate( $new_data, $current_data );

		if ( ! empty( $errors ) ) {
			foreach ( $errors as $error ) {
				$this->push_response_error(
					$this->format_response_message(
						$error,
						'error'
					)
				);
			}
		}
	}

	/**
	 * Checks if the API Url setting is changing.
	 *
	 * @param     array $new_data     An array of new data to be saved.
	 * @param     array $current_data     An array of data that's already saved.
	 *
	 * @return bool
	 */
	private function api_url_is_changing( $new_data, $current_data ) {
		return ( isset( $new_data['api_url'] ) && isset( $current_data['api_url'] ) ) && // both are set
			   $new_data['api_url'] !== $current_data['api_url'];                        // and changing
	}

	/**
	 * Registers available WooCommerce route.
	 */
	public function active_campaign_register_settings_api() {
		register_rest_route(
			'wc',
			'/v2/active-campaign-for-woocommerce/register-integration',
			[
				'methods'             => 'POST',
				'callback'            => [
					$this,
					'save_active_campaign_settings',
				],
				'permission_callback' => [
					$this,
					'validate_rest_user',
				],
			]
		);
	}

	/**
	 * Saves our integration connection settings.
	 *
	 * @param     WP_REST_Request $request     The request object.
	 *
	 * @return WP_REST_Response The REST response object.
	 */
	public function save_active_campaign_settings( WP_REST_Request $request ) {
		$logger = new Logger();

		if ( $request->has_param( 'api_url' ) && $request->has_param( 'api_key' ) ) {
			$params  = $request->get_params();
			$options = $this->get_options();

			// We need to set the default values so WP doesn't error
			$defaults = [
				'abcart_wait'             => 1,
				'optin_checkbox_text'     => 'Keep me up to date on news and exclusive offers',
				'checkbox_display_option' => 'visible_checked_by_default',
				'custom_email_field'      => 'billing_email',
				'ac_debug'                => '0',
			];

			foreach ( $defaults as $key => $default ) {
				if ( ! isset( $options[ $key ] ) ) {
					$params[ $key ] = $default;
				}
			}

			$logger->info( 'Saving integration settings from ActiveCampaign...' );

			$response = $this->update_options( $params );

			return new WP_REST_Response( 'ActiveCampaign connection settings successfully saved to WordPress.', 201 );
		} else {
			$logger->error( 'Required parameters were missing from the API setup call. Setup may need to be finished manually. Please contact support about this issue.' );

			return new WP_REST_Response( 'Error: Missing required parameters.', 400 );
		}
	}

	/**
	 * Callback function to validate the user can save settings
	 *
	 * @return bool|WP_Error The error or true.
	 */
	public function validate_rest_user() {
		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'Unauthorized', __( 'Unauthorized', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ), [ 'status' => 401 ] );
		} elseif ( ! current_user_can( 'administrator' ) ) {
			return new WP_Error( 'Forbidden', __( 'Forbidden', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ), [ 'status' => 403 ] );
		} else {
			return true;
		}
	}

}
