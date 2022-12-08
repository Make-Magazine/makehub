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
use Activecampaign_For_Woocommerce_Utilities as AC_Utilities;
use Activecampaign_For_Woocommerce_Connection as Connection;
use Activecampaign_For_Woocommerce_Connection_Repository as Connection_Repository;
use Activecampaign_For_Woocommerce_Connection_Option_Repository as Connection_Option_Repository;
use Activecampaign_For_Woocommerce_Admin_Status as Admin_Status;
use Activecampaign_For_Woocommerce_Admin_Product_Sync as Admin_Product_Status;

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
	private $response = array();

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
	 * The class for connection repository.
	 *
	 * @var Activecampaign_For_Woocommerce_Connection_Repository The connection class.
	 */
	private $connection_repository;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param     string                                               $plugin_name     The name of this plugin.
	 * @param     string                                               $version     The version of this plugin.
	 * @param     Validator                                            $validator     The validator for the admin options.
	 * @param     Admin_Settings_Updated                               $event     The admin settings updated event class.
	 * @param     Activecampaign_For_Woocommerce_Connection_Repository $connection_repository The connection repository.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version, Validator $validator, Admin_Settings_Updated $event, Connection_Repository $connection_repository ) {
		$this->plugin_name           = $plugin_name;
		$this->version               = $version;
		$this->validator             = $validator;
		$this->event                 = $event;
		$this->connection_repository = $connection_repository;
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles_scripts() {
		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/activecampaign-for-woocommerce-admin.css',
			array(),
			$this->version,
			'all'
		);

		wp_register_script(
			$this->plugin_name . 'settings-page',
			plugin_dir_url( __FILE__ ) . 'scripts/activecampaign-for-woocommerce-settings-page.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		wp_register_script(
			$this->plugin_name . 'status-page',
			plugin_dir_url( __FILE__ ) . 'scripts/activecampaign-for-woocommerce-status-page.js',
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

		wp_register_script(
			$this->plugin_name . 'historical-sync',
			plugin_dir_url( __FILE__ ) . 'scripts/activecampaign-for-woocommerce-historical-sync.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		wp_register_script(
			$this->plugin_name . 'product-sync',
			plugin_dir_url( __FILE__ ) . 'scripts/activecampaign-for-woocommerce-product-sync.js',
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
		$ac_icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB3aWR0aD0iMjBweCIgaGVpZ2h0PSIyMHB4IiB2aWV3Qm94PSIwIDAgMjAgMjAiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+CiAgICA8dGl0bGU+R3JvdXA8L3RpdGxlPgogICAgPGcgaWQ9IlBhZ2UtMSIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPGcgaWQ9Ikdyb3VwIj4KICAgICAgICAgICAgPHJlY3QgaWQ9IlJlY3RhbmdsZSIgb3BhY2l0eT0iMCIgeD0iMCIgeT0iMCIgd2lkdGg9IjIwIiBoZWlnaHQ9IjIwIj48L3JlY3Q+CiAgICAgICAgICAgIDxwYXRoIGQ9Ik00LjIxNzMxNjUsMS4xMDg2NTE1IEM0LjY4ODE0LDEuMzk4MzkgMTUuMDEwMDUsOC42MDU2NSAxNS4yNjM2LDguODIyOTUgQzE1LjY5ODIsOS4xMTI3IDE1LjkxNTUsOS40NzQ4NSAxNS45MTU1LDkuODM3IEwxNS45MTU1LDEwLjA1NDM1IEMxNS45MTU1LDEwLjM0NDA1IDE1LjgwNjg1LDEwLjgxNDkgMTUuMjk5OCwxMS4xNzcwNSBDMTUuMDgyNSwxMS4zNTgxNSA0LjEwODY2OSwxOSA0LjEwODY2OSwxOSBMNC4xMDg2NjksMTcuMjk3OCBDNC4xMDg2NjksMTYuNzkwNzUgNC4xNDQ4Njg1LDE2LjUzNzIgNC43MjQzNDUsMTYuMTc1MDUgQzUuMTk1MTcsMTUuODg1MyAxMi42NTU5NSwxMC43MDYyNSAxMy42MzM4LDEwLjAxODEgQzEzLjE2Nzk1LDkuNjkwMyAxMS4yNTUzNSw4LjM1OTYgOS4zMjU0LDcuMDE2ODUgQzcuMjA0MzIsNS41NDExMiA1LjA2MjI3LDQuMDUwOCA0Ljc5Njc4NSwzLjg2MTE3IEw0LjcyNDM0NSwzLjgyNDk1IEM0LjY5Njg2NSwzLjgwMjk2NSA0LjY2OTgwNSwzLjc4MTYxIDQuNjQzMjU1LDMuNzYwNjU1IEM0LjMxOTg3NCwzLjUwNTQ0IDQuMDcyNDM4NSwzLjMxMDE2IDQuMDcyNDM4NSwyLjc3NDY1IEw0LjA3MjQzODUsMSBMNC4yMTczMTY1LDEuMTA4NjUxNSBaIE05LjY4NjEsMTAuNDg4OSBDOS4zOTY0LDEwLjcwNjI1IDkuMTA2NjUsMTAuODE0OSA4LjgxNjkyLDEwLjgxNDkgQzguNTYzNCwxMC44MTQ5IDguMzA5ODc1LDEwLjc0MjQ1IDguMDIwMTM1LDEwLjU2MTM1IEM3LjM2ODIyNSwxMC4xMjY3NSA0LjA3MjQ0OCw3Ljg0NTA1IDQuMDM2MjMwNTUsNy44MDg4NSBMNCw3Ljc3MjY1IEw0LDYuNjQ5OSBDNCw2LjM2MDE1IDQuMTQ0ODc4LDYuMTc5MDUgNC4zMjU5NjQ1LDYuMDcwNCBDNC41MDcwNSw1Ljk2MTc2IDQuNzk2OCw1Ljk5Nzk4IDUuMDE0MSw2LjE3OTA1IEM1LjUyMTE0NSw2LjUwNSAxMC4zMzgwNSw5LjgzNyAxMC4zNzQyNSw5Ljg3MzI1IEwxMC40ODI5LDkuOTQ1NjUgTDEwLjM3NDI1LDEwLjAxODEgQzEwLjM3NDI1LDEwLjAxODEgMTAuMDQ4MywxMC4yMzU0IDkuNjg2MSwxMC40ODg5IFoiIGlkPSJTaGFwZSIgZmlsbD0iIzAwNENGRiI+PC9wYXRoPgogICAgICAgIDwvZz4KICAgIDwvZz4KPC9zdmc+';

		add_menu_page(
			'ActiveCampaign for WooCommerce',
			'ActiveCampaign',
			'manage_options',
			ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE,
			array( $this, 'fetch_admin_page' ),
			$ac_icon,
			55
		);

		add_submenu_page(
			ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE,
			'ActiveCampaign for WooCommerce Settings',
			'WooCommerce Settings',
			'manage_options',
			ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE,
			array( $this, 'fetch_admin_page' )
		);

		$activecampaign_for_woocommerce_options = $this->get_options();
		if (
			isset(
				$activecampaign_for_woocommerce_options['api_url'],
				$activecampaign_for_woocommerce_options['api_key'],
				$this->get_storage()['connection_id'],
				$this->get_storage()['connection_option_id']
			) &&
			! empty( $this->get_storage()['connection_id'] ) &&
			! empty( $this->get_storage()['connection_option_id'] )
		) {
			add_submenu_page(
				ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE,
				'ActiveCampaign for WooCommerce Abandoned Carts',
				'Abandoned Carts',
				'manage_options',
				ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE . '_abandoned_carts',
				array( $this, 'fetch_abandoned_cart_page' )
			);

			add_submenu_page(
				ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE,
				'ActiveCampaign for WooCommerce Historical Sync',
				'Historical Sync',
				'manage_options',
				ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE . '_historical_sync',
				array( $this, 'fetch_historical_sync_page' )
			);

			if ( Activecampaign_For_Woocommerce_Product_Repository::is_enabled() ) {
				add_submenu_page(
					ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE,
					'ActiveCampaign for WooCommerce Product Sync',
					'Product Sync',
					'manage_options',
					ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE . '_product_sync',
					array( $this, 'fetch_product_sync_page' )
				);
			}
		}

		add_submenu_page(
			ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE,
			'ActiveCampaign for WooCommerce Status',
			'Status',
			'manage_options',
			ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE . '_status',
			array( $this, 'fetch_status_page' )
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

		$action_links = array(
			$html,
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Fetch the PHP template file that is used for the admin page
	 *
	 * @since    1.0.0
	 */
	public function fetch_admin_page() {
		wp_enqueue_script( $this->plugin_name . 'settings-page' );
		require_once plugin_dir_path( __FILE__ )
					 . 'views/activecampaign-for-woocommerce-admin-display.php';
	}

	/**
	 * Fetch the PHP template file that is used for the admin page
	 *
	 * @param     array $array array for hook.
	 *
	 * @since    1.4.9
	 */
	public function please_configure_plugin_notice( $array ) {
		global $pagenow;
		global $plugin_page;

		// Verify we're on an admin section
		if (
			'activecampaign_for_woocommerce' !== $plugin_page &&
			current_user_can( 'administrator' ) &&
			(
				'admin.php' === $pagenow
				|| 'plugins.php' === $pagenow
				|| get_current_screen()->in_admin()
			)
		) {
			require_once plugin_dir_path( __FILE__ ) . 'views/activecampaign-for-woocommerce-please-configure-plugin-notice.php';
		}
	}

	/**
	 * Populates an admin notice dismiss in db.
	 */
	public function update_dismiss_plugin_notice_option() {
		$setting                          = json_decode( get_option( 'activecampaign_for_woocommerce_dismiss_plugin_notice' ), 'array' );
		$setting[ get_current_user_id() ] = 1;
		update_option( 'activecampaign_for_woocommerce_dismiss_plugin_notice', wp_json_encode( $setting ) );
	}

	/**
	 * Populates an admin notice.
	 */
	public function error_admin_notice() {
		global $pagenow;

		// Verify we're on an admin section
		if ( 'admin.php' === $pagenow ) {
			global $wpdb;

			$err_count = null;

			try {
				$level  = 500;
				$source = 'activecampaign-for-woocommerce';
				// phpcs:disable
				$err_count = $wpdb->get_var(
					$wpdb->prepare(
						'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'woocommerce_log WHERE source = %s AND level = %d',
						[ $source, $level ]
					)
				);
				// phpcs:enable
			} catch ( Throwable $t ) {
				$logger = new Logger();
				$logger->warning( 'There was an issue retrieving log information from the woocommerce_log table.' );
			}

			if ( ! empty( $err_count ) ) {
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
							array(
								'singular' => 'error',
								'plural'   => 'errors',
								'domain'   => ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN,
								'context'  => null,
							),
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
				array(
					'singular' => 'record',
					'plural'   => 'records',
					'domain'   => ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN,
					'context'  => null,
				),
				$count
			) . ' removed from the database.'
		);
	}

	/**
	 * Fetch the PHP template file that is used for the admin abandoned cart page.
	 *
	 * @since    1.3.7
	 */
	public function fetch_abandoned_cart_page() {
		wp_enqueue_script( $this->plugin_name . 'abandoned-cart' );
		require_once plugin_dir_path( __FILE__ )
					 . 'views/activecampaign-for-woocommerce-abandoned-cart-display.php';
	}

	/**
	 * Fetches the historical sync page view.
	 *
	 * @since 1.5.0
	 */
	public function fetch_historical_sync_page() {
		wp_enqueue_script( $this->plugin_name . 'historical-sync' );

		require_once plugin_dir_path( __FILE__ )
					 . 'views/activecampaign-for-woocommerce-historical-sync.php';

	}

	/**
	 * Fetches the product sync page view.
	 *
	 * @since 1.9.0
	 */
	public function fetch_product_sync_page() {
		$admin_product_sync = new Admin_Product_Status();
		// This gets ported to the display page through require
		$activecampaign_for_woocommerce_product_sync_data            = $admin_product_sync->get_product_sync_page_data();
		$activecampaign_for_woocommerce_product_sync_data['options'] = $this->get_options();

		wp_enqueue_script( $this->plugin_name . 'product-sync' );

		require_once plugin_dir_path( __FILE__ )
					 . 'views/activecampaign-for-woocommerce-product-sync.php';

	}

	/**
	 * Schedules the historical sync to run as a background job.
	 *
	 * @since 1.5.0
	 */
	public function schedule_single_historical_sync() {
		$logger = new Logger();
		try {
			update_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_SCHEDULED_STATUS_NAME, true );
			$start_rec = AC_Utilities::get_request_data( 'start_rec' );

			wp_schedule_single_event(
				time() + 10,
				ACTIVECAMPAIGN_FOR_WOOCOMMERCE_RUN_SYNC_NAME,
				array(
					'args' => array(
						'sync_type'    => 'single',
						'start_rec'    => $start_rec,
						'record_limit' => 1,
					),
				)
			);

			$logger->info(
				'Schedule historical sync',
				array(
					'current_time'    => time(),
					'start_on_record' => $start_rec,
					'schedule'        => wp_get_scheduled_event( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_RUN_SYNC_NAME ),
				)
			);
		} catch ( Throwable $t ) {
			$logger->error(
				'There was an issue scheduling historical sync',
				array(
					'message'  => $t->getMessage(),
					'function' => 'schedule_single_historical_sync',
				)
			);
		}
	}

	/**
	 * Schedules the bulk historical sync to run as a background job.
	 *
	 * @since 1.6.0
	 */
	public function schedule_bulk_historical_sync() {
		$logger = new Logger();
		try {
			$sync_contacts = AC_Utilities::get_request_data( 'syncContacts' );
			update_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_SCHEDULED_STATUS_NAME, true );
			if ( isset( $sync_contacts ) && $sync_contacts ) {
				// Sync all the contacts from the orders
				update_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_SCHEDULED_STATUS_NAME, 2 );
				do_action( 'activecampaign_for_woocommerce_run_historical_sync_contacts' );
			}

			$start_rec   = AC_Utilities::get_request_data( 'startRec' );
			$batch_limit = AC_Utilities::get_request_data( 'batchLimit' );

			wp_schedule_single_event(
				time() + 10,
				ACTIVECAMPAIGN_FOR_WOOCOMMERCE_RUN_SYNC_NAME,
				array(
					'args' => array(
						'sync_type'   => 'bulk',
						'start_rec'   => $start_rec,
						'batch_limit' => $batch_limit,
					),
				)
			);

			$logger->info(
				'Schedule historical sync',
				array(
					'current_time'    => time(),
					'start_on_record' => $start_rec,
					'batch_limit'     => $batch_limit,
					'schedule'        => wp_get_scheduled_event( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_RUN_SYNC_NAME ),
				)
			);
		} catch ( Throwable $t ) {
			$logger->error(
				'There was an issue scheduling historical sync',
				array(
					'message'  => $t->getMessage(),
					'trace'    => $t->getTrace(),
					'function' => 'schedule_single_historical_sync',
				)
			);
		}
	}

	/**
	 * Checks the status of historical sync and returns the result.
	 *
	 * @since 1.5.0
	 */
	public function check_historical_sync_status() {
		try {
			$status       = json_decode( get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME ), 'array' );
			$total_orders = $this->get_sync_ready_order_count();
			$last_sync    = json_decode( get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_LAST_STATUS_NAME ), 'array' );

			// If the sync is scheduled but has not run
			if ( ! empty( wp_get_scheduled_event( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_RUN_SYNC_NAME ) ) || get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_SCHEDULED_STATUS_NAME ) ) {
					$status['total_orders'] = $total_orders;
					$data                   = (object) array(
						'status'    => $status,
						'last_sync' => $last_sync,
					);
					wp_send_json_success( $data );

			}
			$data = null;
			global $wpdb;
			$sync_count = $wpdb->get_var(
			// phpcs:disable
				'SELECT count(id)
						FROM `' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_TABLE_NAME . '`
						WHERE synced_to_ac = 3 '
			// phpcs:enable
			);

			if ( $sync_count > 0 && ! $last_sync ) {
				$status['total_orders'] = $sync_count;
			}

			$data = (object) array(
				'status'    => $status,
				'last_sync' => $last_sync,
			);

			wp_send_json_success( $data );
		} catch ( Throwable $t ) {
			$logger = new Logger();
			$logger->warning(
				'There was an issue getting the historical sync status',
				array(
					'message'  => $t->getMessage(),
					'function' => 'check_historical_sync_status',
				)
			);
		}
	}

	/**
	 * Fetch the historical sync results for the last sync
	 */
	public function fetch_last_historical_sync() {
		try {
			ob_start();
			include plugin_dir_path( __FILE__ ) . 'partials/activecampaign-for-woocommerce-last-historical-sync.php';
			$html = ob_get_clean();
			wp_send_json_success(
				array(
					'html' => $html,
				)
			);
		} catch ( Throwable $t ) {
			$logger = new Logger();
			$logger->warning(
				'There was an issue getting the last historical sync.',
				array(
					'message'  => $t->getMessage(),
					'function' => 'fetch_last_historical_sync',
				)
			);
		}
	}

	/**
	 * Sets a stop for the historical sync with a condition of cancel or pause.
	 *
	 * @since 1.5.0
	 */
	public function stop_historical_sync() {
		$logger = new Logger();
		try {
			$stop_type = AC_Utilities::get_request_data( 'type' );
			$user      = wp_get_current_user();

			if ( ! empty( $stop_type ) ) {
				$logger->alert(
					'Historical sync stop requested',
					array(
						'type'              => $stop_type,
						'requested by user' => array(
							'user_id'    => isset( $user->ID ) ? $user->ID : null,
							'user_email' => isset( $user->data->user_email ) ? $user->data->user_email : null,
						),
					)
				);

				update_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_HISTORICAL_SYNC_STOP_CHECK_NAME, $stop_type, false );
				wp_send_json_success( 'Stop requested...' );
			} else {
				wp_send_json_success( 'No argument provided' );
			}
		} catch ( Throwable $t ) {
			$logger->warning(
				'There was an issue stopping the historical sync.',
				array(
					'message'  => $t->getMessage(),
					'function' => 'stop_historical_sync',
				)
			);
		}
	}

	/**
	 * Resets the historical sync if it gets in a stuck position.
	 *
	 * @since 1.5.0
	 */
	public function reset_historical_sync() {
		delete_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME );
		delete_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_SCHEDULED_STATUS_NAME );
		delete_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_HISTORICAL_SYNC_STOP_CHECK_NAME );
		delete_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_LAST_STATUS_NAME );
		wp_send_json_success( 'Sync statuses cleared.' );
	}

	/**
	 * Radio options for "How long after a cart is abandoned should ActiveCampaign trigger automations?"
	 * How long should we wait until we determine a cart is abandoned?
	 * These options let the user decide.
	 */
	public function get_ab_cart_wait_options() {
		$options = wp_json_encode(
			array(
				// value     // label
				'1'  => esc_html__( '1 hour (recommended)', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ),
				'6'  => esc_html__( '6 hours', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ),
				'10' => esc_html__( '10 hours', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ),
				'24' => esc_html__( '24 hours', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ),
			)
		);

		return $options;
	}

	/**
	 * Fetch the PHP template file that is used for the admin status page.
	 *
	 * @since    1.4.9
	 */
	public function fetch_status_page() {
		$admin_status = new Admin_Status();
		// This gets ported to the display page through require
		$activecampaign_for_woocommerce_status_data = $admin_status->get_status_page_data();
		wp_enqueue_script( $this->plugin_name . 'status-page' );
		require_once plugin_dir_path( __FILE__ )
					 . 'views/activecampaign-for-woocommerce-status-display.php';
	}

	/**
	 * Gets the abandoned carts from our table.
	 *
	 * @param     int $page The page number.
	 *
	 * @return array|object|null
	 */
	public function get_abandoned_carts( $page = 0 ) {
		$logger = new Logger();
		try {
			global $wpdb;

			do_action( 'activecampaign_for_woocommerce_verify_tables' );

			$limit  = 40;
			$offset = $page * $limit;

			$result = $wpdb->get_results(
			// phpcs:disable
				$wpdb->prepare(
					'SELECT 
				       id, 
				       order_date,
				       abandoned_date, 
				       synced_to_ac,
				       customer_id,
				       customer_email, 
				       customer_first_name, 
				       customer_last_name, 
				       last_access_time,
				       activecampaignfwc_order_external_uuid, 
				       cart_ref_json, 
				       customer_ref_json
	                FROM `' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_TABLE_NAME . '` 
	                WHERE order_date IS NULL OR abandoned_date IS NOT NULL
	                LIMIT %d,%d',
					[ $offset, $limit ]
				), OBJECT
			// phpcs:enable
			);

			if ( $wpdb->last_error ) {
				$logger->error(
					'Save abandoned cart command: There was an error selecting the id for a customer abandoned cart record.',
					array(
						'wpdb_last_error' => $wpdb->last_error,
						'result'          => $result,
					)
				);
			}
			return $result;
		} catch ( Throwable $t ) {
			$logger->warning(
				'There was an issue getting abandoned carts',
				array(
					'message'  => $t->getMessage(),
					'function' => 'get_abandoned_carts',
				)
			);
		}
	}

	/**
	 * Get the abandoned carts total.
	 *
	 * @return string|null
	 */
	public function get_total_abandoned_carts() {
		global $wpdb;
		// phpcs:disable

		do_action('activecampaign_for_woocommerce_verify_tables');

		return $wpdb->get_var( 'SELECT COUNT(id) FROM `' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_TABLE_NAME . '` WHERE order_date IS NULL OR abandoned_date IS NOT NULL' );
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
		return $wpdb->get_var( 'SELECT COUNT(id) FROM `' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_TABLE_NAME . '` WHERE synced_to_ac = 0 AND order_date IS NULL' );
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
		$logger = new Logger();
		try {
			if ( ! $this->validate_request_nonce( 'activecampaign_for_woocommerce_abandoned_form' ) ) {
				wp_send_json_error( 'The nonce appears to be invalid.' );
			}

			$row_id = AC_Utilities::get_request_data( 'rowId' );
			if ( isset( $row_id ) ) {
				do_action( 'activecampaign_for_woocommerce_run_manual_abandonment_delete', $row_id );
			} else {
				// phpcs:disable
				$logger->warning(
				'Invalid request, rowId missing from the delete abandoned cart call:',
				[
					'request' => $_REQUEST,
					'post'    => $_POST,
					'get'     => $_GET,
				]
				);
				// phpcs:enable
				wp_send_json_error( 'No row ID defined.' );
			}
		} catch ( Throwable $t ) {
			$logger->warning(
				'There was an issue deleting an abandoned cart',
				array(
					'message'  => $t->getMessage(),
					'function' => 'handle_abandon_cart_delete',
				)
			);
		}
	}

	/**
	 * Handles the abandoned cart sync function and triggers the manual forced sync
	 */
	public function handle_abandon_cart_force_row_sync() {
		$logger = new Logger();
		try {
			if ( ! $this->validate_request_nonce( 'activecampaign_for_woocommerce_abandoned_form' ) ) {
				wp_send_json_error( 'The nonce appears to be invalid.' );
			}

			$row_id = AC_Utilities::get_request_data( 'rowId' );
			if ( isset( $row_id ) ) {
				do_action( 'activecampaign_for_woocommerce_run_force_row_abandonment_sync', $row_id );
			} else {
				// phpcs:disable
				$logger->warning(
				'Invalid request, rowId missing from the force row sync call:',
				[
					'request' => $_REQUEST,
					'post'    => $_POST,
					'get'     => $_GET,
				]
				);
				// phpcs:enable
				wp_send_json_error( 'The request appears to be invalid. The rowId is missing from the request.' );
			}
		} catch ( Throwable $t ) {
			$logger->warning(
				'There was an issue forcing a row sync on an abandoned cart',
				array(
					'message'  => $t->getMessage(),
					'function' => 'handle_abandon_cart_force_row_sync',
				)
			);
		}
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
		try {
			$storage = $this->get_storage();

			$notifications = isset( $storage['notifications'] ) ? $storage['notifications'] : array();

			$this->update_storage(
				array(
					'notifications' => array(),
				)
			);

			return wp_json_encode( $notifications );
		} catch ( Throwable $t ) {
			$logger = new Logger();
			$logger->warning(
				'There was an issue forcing a row sync on an abandoned cart',
				array(
					'message'  => $t->getMessage(),
					'function' => 'handle_abandon_cart_force_row_sync',
				)
			);
		}
	}

	/**
	 * Handles the API Test request from the settings page,
	 * then redirects back to the plugin page
	 */
	public function handle_api_test() {
		if ( ! $this->validate_request_nonce( 'activecampaign_for_woocommerce_settings_form' ) ) {
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

	public function check_for_existing_connection() {
		$logger  = new Logger();
		$storage = $this->get_storage();

		if (
				isset( $storage['connection_id'] ) &&
				! empty( $storage['connection_id'] )
		) {
			$connection = $this->connection_repository->find_by_id( $storage['connection_id'] );
		}

		if ( ! isset( $connection ) || ! $connection->get_id() ) {
			$connection = $this->connection_repository->find_current(); // If we don't have an active connection ID, check if we have one for the URL
			$logger->debug( 'Find my current connection', [ AC_Utilities::validate_object( $connection, 'serialize_to_array' ) ? $connection->serialize_to_array() : null ] );
		}

		// No valid connection, find current if it exists
		if ( ! isset( $connection ) || ! $connection->get_id() ) {
			// No valid connection check by filter
			// If we don't have an ID now just get the WC service we find
			$connection = $this->connection_repository->find_by_filter( 'service', 'woocommerce' );

			$logger->debug(
				'This is the full connection check output',
				[
					'$storage'         => $storage,
					'site_url'         => get_site_url(),
					'serializetoarray' => AC_Utilities::validate_object( $connection, 'serialize_to_array' ) ? $connection->serialize_to_array() : null,
				]
			);
		}

		// If this is accurate store it
		if ( isset( $connection ) && ( get_site_url() === $connection->get_externalid() || get_site_url() . '/' === $connection->get_externalid() ) ) {
			if ( $this->update_storage_from_connection( $connection ) ) {
				do_action( 'activecampaign_for_woocommerce_admin_settings_updated' );
			}
			return $connection;
		}

		return false;
	}


	/**
	 * Checks the health of the connection and returns issues or empty.
	 *
	 * @return array|bool
	 */
	public function connection_health_check() {
		$issues   = array();
		$now      = date_create( 'NOW' );
		$last_run = get_option( 'activecampaign_for_woocommerce_connection_health_check_last_run' );
		$settings = $this->get_options();
		$storage  = $this->get_storage();
		$logger   = new Logger();

		if ( false !== $last_run ) {
			$interval         = date_diff( $now, $last_run );
			$interval_minutes = $interval->format( '%i' );
		} else {
			$interval_minutes = 0;
		}

		if ( false === $last_run || $interval_minutes >= 60 || ! isset( $storage['connection_id'] ) ) {
			update_option( 'activecampaign_for_woocommerce_connection_health_check_last_run', $now );

			if ( empty( $storage ) || ( empty( $settings['api_url'] ) && empty( $settings['api_key'] ) ) ) {
				$issues[] = 'API URL and/or Key is missing.';
			}

			if ( empty( $storage['connection_id'] ) ) {
				$issues[] = 'Connection id is missing from settings!';
			} else {
				try {
					$connection = $this->connection_repository->find_by_id( $storage['connection_id'] );

					if ( ! isset( $connection ) || empty( $connection->get_externalid() ) ) {
						$connection = $this->check_for_existing_connection();
						if ( ! $connection ) {
							$issues[] = 'A valid connection ID for this store could not be found from the stored data.';
						} else {
							$issues  = array();
							$storage = $this->get_storage();
						}
					}
				} catch ( Throwable $t ) {
					$logger->warning(
						'There was an issue trying to validate connection ID.',
						array(
							'message' => $t->getMessage(),
							'trace'   => $logger->clean_trace( $t->getTrace() ),
						)
					);

					$issues[] = $t->getMessage();
				}

				try {
					if (
						isset( $connection ) &&
						AC_Utilities::validate_object( $connection, 'get_id' ) &&
						! empty( $connection->get_id() ) &&
						(
								get_site_url() !== $connection->get_externalid() &&
								get_site_url() . '/' !== $connection->get_externalid()
						)
					) {
						$issues[] = 'The connection URL and your site URL do not match.';
						if ( empty( $connection->get_externalid() ) ) {
							$issues[] = 'Your stored connection ID could not be found in ActiveCampaign. You will need to fix your connection.';
						} else {
							$issues[] = 'Your URL is ' . get_site_url() . ' | The stored integration URL in ActiveCampaign matching ID ' . $storage['connection_id'] . ' is ' . $connection->get_externalid();
						}
					}
				} catch ( Throwable $t ) {
					$logger->warning(
						'The connection to ActiveCampaign was not defined.',
						array(
							'message' => $t->getMessage(),
							'trace'   => $logger->clean_trace( $t->getTrace() ),
						)
					);

					$issues[] = $t->getMessage();
				}
			}
		}

		if ( count( $issues ) > 0 ) {
			$issues[] = '* ActiveCampaign functionality will be disabled until the connection is repaired.';
			$issues[] = '* Please update your settings and validate your connection.';
			delete_option( 'activecampaign_for_woocommerce_connection_health_check_last_run' );
			$logger->error(
				'Connection Issues Recorded',
				[
					'issues' => $issues,
				]
			);
		}

		return $issues;
	}

	/**
	 * Handles the form submission for the settings page,
	 * then redirects back to the plugin page.
	 */
	public function handle_settings_post() {
		try {
			if ( ! $this->validate_request_nonce( 'activecampaign_for_woocommerce_settings_form' ) ) {
				wp_send_json_error( $this->get_response(), 403 );
			}

			$post_data = $this->extract_post_data();

			$this->update_options( $post_data );

			if ( $this->response_has_errors() ) {
				wp_send_json_error( $this->get_response(), 422 );
			}

			// Settings saved, make sure our table is populated.
			do_action( 'activecampaign_for_woocommerce_verify_tables' );

			$this->schedule_cron_syncs();

			$this->push_response_notice(
				$this->format_response_message( 'Settings successfully updated!', 'success' )
			);

			wp_send_json_success( $this->get_response() );
		} catch ( Throwable $t ) {
			$logger = new Logger();
			$logger->warning(
				'There was an issue saving settings.',
				array(
					'message'  => $t->getMessage(),
					'function' => 'handle_settings_post',
				)
			);
		}
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
			array(
				'api_url_changed' => $api_url_changing,
			)
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
	 * Gets the count of sync ready orders.
	 *
	 * @param     string $type The type to return. Expects "array".
	 *
	 * @return int|string|array
	 */
	public function get_sync_ready_order_count( $type = 'int' ) {
		if ( 'array' === $type ) {
			$order_totals = array(
				'processing' => wc_orders_count( 'processing' ),
				'completed'  => wc_orders_count( 'completed' ),
			);
		} else {
			$order_totals = wc_orders_count( 'processing' ) + wc_orders_count( 'completed' );
		}

		return $order_totals;
	}

	public function update_storage_from_connection( $connection ) {
		if ( isset( $connection ) && AC_Utilities::validate_object( $connection, 'get_id' ) ) {
			$this->update_storage(
				[
					'connection_id' => $connection->get_id(),
					'name'          => $connection->get_name(),
					'external_id'   => $connection->get_externalid(),
					'service'       => $connection->get_service(),
					'link_url'      => $connection->get_link_url(),
					'logo_url'      => $connection->get_logo_url(),
					'is_internal'   => $connection->get_is_internal(),
				]
			);
		}
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
			$current_storage['notifications'] = array();
		}

		$notifications = $current_storage['notifications'];

		$notifications[] = $this->format_response_message( $message, $level );

		$this->update_storage(
			array(
				'notifications' => $notifications,
			)
		);
	}

	/**
	 * Validates the request nonce for any form to ensure valid requests by passing the form action name.
	 *
	 * @param string $action_name The action name.
	 *
	 * @return bool
	 */
	public function validate_request_nonce( $action_name ) {
		/**
		 * Validate the nonce created for this specific form action.
		 * The nonce input is generated in the template using the wp_nonce_field().
		 */

		$logger = new Logger();
		$valid  = false;

		try {
			$nonce = AC_Utilities::get_request_data( 'activecampaign_for_woocommerce_settings_nonce_field' );
			$valid = wp_verify_nonce( $nonce, $action_name );
		} catch ( Throwable $t ) {
			$logger->error(
				'There was an issue validating the request nonce.',
				array(
					'message'     => $t->getMessage(),
					'action_name' => $action_name,
				)
			);
		}

		if ( ! $valid ) {
			try {
				$logger->warning(
					'Invalid nonce:',
					array(
						'action_name' => $action_name,
						'request'     => $_REQUEST,
						'get'         => $_GET,
						'post'        => $_POST,
					)
				);
			} catch ( Throwable $t ) {
				$logger->warning(
					'A request type was not allowed to log.',
					array(
						'message' => $t->getMessage(),
					)
				);
			}

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
		$_request = wp_unslash( $_REQUEST );
		if ( wp_verify_nonce( $_request['activecampaign_for_woocommerce_settings_nonce_field'], 'activecampaign_for_woocommerce_settings_form' ) ) {
			$post_data = wp_unslash( $_POST );

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
			$this->response['errors'] = array();
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
			$this->response['notices'] = array();
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
		if ( $this->get_options() ) {
			return array_merge( $this->response, $this->get_options() );
		}

		return array_merge( $this->response, array() );
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
			array(
				'methods'             => 'POST',
				'callback'            => array(
					$this,
					'save_active_campaign_settings',
				),
				'permission_callback' => array(
					$this,
					'validate_rest_user',
				),
			)
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
			$defaults = array(
				'abcart_wait'             => 1,
				'optin_checkbox_text'     => 'Keep me up to date on news and exclusive offers',
				'checkbox_display_option' => 'visible_checked_by_default',
				'custom_email_field'      => 'billing_email',
				'ac_debug'                => '0',
				ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PRODUCT_SYNC_ENABLED_NAME => '0',
			);

			foreach ( $defaults as $key => $default ) {
				if ( ! isset( $options[ $key ] ) ) {
					$params[ $key ] = $default;
				}
			}

			$logger->info( 'Saving integration settings from ActiveCampaign...', array( $params ) );

			$this->update_options( $params );

			// If settings were saved we should populate our table to enable functionality
			do_action( 'activecampaign_for_woocommerce_verify_tables' );
			$this->schedule_cron_syncs();
			return new WP_REST_Response( 'ActiveCampaign connection settings successfully saved to WordPress.', 201 );
		} else {
			$logger->error( 'Required parameters were missing from the API setup call. Setup may need to be finished manually. Please contact support about this issue.' );

			return new WP_REST_Response( 'Error: Missing required parameters.', 400 );
		}
	}

	/**
	 * Generate the cron sync scheduled processes
	 */
	public function schedule_cron_syncs() {
		$activecampaign_for_woocommerce_options = $this->get_options();

		try {
			if (
				isset(
					$activecampaign_for_woocommerce_options['api_url'],
					$activecampaign_for_woocommerce_options['api_key'],
					$this->get_storage()['connection_id'],
					$this->get_storage()['connection_option_id']
				) &&
				! empty( $this->get_storage()['connection_id'] ) &&
				! empty( $this->get_storage()['connection_option_id'] )
			) {
				wp_schedule_event( time() + 10, 'hourly', 'activecampaign_for_woocommerce_cart_updated_recurring_event' );
				wp_schedule_event( time() + 10, 'every_minute', 'activecampaign_for_woocommerce_run_order_sync' );
			}
		} catch ( Throwable $t ) {
			$logger = new Logger();
			$logger->warning(
				'There was an issue scheduling the cron events from admin settings.',
				array(
					'message' => $t->getMessage(),
				)
			);
		}
	}
	/**
	 * Callback function to validate the user can save settings
	 *
	 * @return bool|WP_Error The error or true.
	 */
	public function validate_rest_user() {
		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'Unauthorized', __( 'Unauthorized', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ), array( 'status' => 401 ) );
		} elseif ( ! current_user_can( 'administrator' ) ) {
			return new WP_Error( 'Forbidden', __( 'Forbidden', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ), array( 'status' => 403 ) );
		} else {
			return true;
		}
	}

	/**
	 * Handles the ajax call for clear plugin settings.
	 */
	public function handle_clear_plugin_settings() {
		$logger = new Logger();

		if ( ! $this->validate_request_nonce( 'activecampaign_for_woocommerce_settings_form' ) ) {
			wp_send_json_error( 'The nonce appears to be invalid.' );
		}

		if ( $this->clear_plugin_settings() ) {
			$logger->info( 'Plugin settings have been manually cleared by the admin. The plugin will not run until new settings are saved.' );
			wp_send_json_success( 'ActiveCampaign for WooCommerce settings have been cleared. NOTICE: The plugin will not run until new settings are saved.' );
		} else {
			wp_send_json_error( 'There was an issue attempting to clear the plugin settings' );
		}
	}

	/**
	 * Attempts to clear the plugin settings.
	 *
	 * @return bool
	 */
	private function clear_plugin_settings() {
		try {
			if ( delete_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_OPTION_NAME ) && delete_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_STORAGE_NAME ) ) {
				wp_clear_scheduled_hook( 'activecampaign_for_woocommerce_cart_updated_recurring_event' );
				wp_clear_scheduled_hook( 'activecampaign_for_woocommerce_run_order_sync' );

				return true;
			} else {
				return false;
			}
		} catch ( Throwable $t ) {
			$logger = new Logger();
			$logger->warning(
				'There was an issue trying to reset the connection ID',
				array(
					'message' => $t->getMessage(),
					'trace'   => $logger->clean_trace( $t->getTrace() ),
				)
			);
			return false;
		}
	}

	/**
	 * Handles the ajax call for reset connection.
	 *
	 * @send string json_success|json_error AJAX return for success or failure.
	 */
	public function handle_reset_connection_id() {
		$logger = new Logger();

		if ( ! $this->validate_request_nonce( 'activecampaign_for_woocommerce_settings_form' ) ) {
			wp_send_json_error( 'The nonce appears to be invalid.' );
		}

		if ( $this->reset_connection_id() ) {
			$logger->info(
				'The connection ID has been manually reset. These are the stored options.',
				array(
					'storage_values' => $this->get_storage(),
					'option_values'  => get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_STORAGE_NAME ),
				)
			);
			$this->check_for_existing_connection();
			wp_send_json_success( 'ActiveCampaign connection ID has been updated/repaired.' );
		} else {
			wp_send_json_error( 'There was an issue attempting to reset the connection ID' );
		}
	}

	/**
	 * Clears then attampts to grab the connection ids from Hosted.
	 *
	 * @return bool
	 */
	private function reset_connection_id() {
		try {
			if ( delete_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_STORAGE_NAME ) ) {
				do_action( 'activecampaign_for_woocommerce_admin_settings_updated' );
				$update_vals = get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_STORAGE_NAME );
				if ( empty( $update_vals['connection_id'] ) ) {
					return false;
				}
				return true;
			} else {
				return false;
			}
		} catch ( Throwable $t ) {
			$logger = new Logger();
			$logger->warning(
				'There was an issue trying to reset the connection ID. Please check the logs for details.',
				array(
					'message' => $t->getMessage(),
					'trace'   => $logger->clean_trace( $t->getTrace() ),
				)
			);
			return false;
		}
	}

	/**
	 * Runs historical sync in active mode.
	 */
	public function run_historical_sync_active() {
		$url = esc_url( admin_url( 'admin.php?page=' . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE . '_historical_sync' ) );
		require_once plugin_dir_path( __FILE__ ) . 'partials/activecampaign-for-woocommerce-historical-sync-active.php';
		$sync_contacts = AC_Utilities::get_request_data( 'activecampaign-historical-sync-contacts' );

		if ( isset( $sync_contacts ) && ! empty( $sync_contacts ) ) {
			// Sync all the contacts from the orders
			do_action( 'activecampaign_for_woocommerce_run_historical_sync_contacts' );
		}

		// Then do the normal historical sync
		do_action( 'activecampaign_for_woocommerce_run_historical_sync_active' );

		$this->historical_active_load_finished( $url );
		echo '</div></section>';
		exit;
	}

	/**
	 * Loads the finished process for historical sync
	 * .
	 *
	 * @param string $url The URL for the page.
	 */
	public function historical_active_load_finished( $url ) {
		?>
		<p>
			<?php esc_html_e( 'Historical sync ended.', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
		</p>

		<script>
			location.href = "<?php echo esc_url( $url ); ?>"
		</script>
		<?php
	}

	/**
	 * Outputs flushed PHP actively being processed.
	 *
	 * @param string $output The output string.
	 */
	public function output_echo( $output ) {
		echo '<p>' . esc_html( $output ) . '</p>';
		ob_flush();
		flush();
	}

	public function check_product_sync_status() {
		$admin_product_sync = new Admin_Product_Status();
		$admin_product_sync->get_product_sync_status();
	}

	public function run_product_sync( ...$args ) {
		$admin_product_sync = new Admin_Product_Status();
		$admin_product_sync->run_product_sync( $args );
	}
}
