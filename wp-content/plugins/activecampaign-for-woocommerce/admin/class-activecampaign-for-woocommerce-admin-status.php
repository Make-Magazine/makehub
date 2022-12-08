<?php

/**
 * The admin status page specific functionality of the plugin.
 *
 * @link       https://www.activecampaign.com/
 * @since      1.8.0
 *
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/admin
 */

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
class Activecampaign_For_Woocommerce_Admin_Status {

	/**
	 * Logger class.
	 *
	 * @var Activecampaign_For_Woocommerce_Logger The logger class.
	 */
	private $logger;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct() {
		$this->logger = new Logger();
	}

	/**
	 * Gets the data for the status page.
	 *
	 * @return array
	 */
	public function get_status_page_data() {
		$data = [];
		$this->delete_old_log_records();
		$data = $this->get_cron_data( $data );
		$data = $this->get_table_data( $data );
		$data = $this->get_woocommerce_data( $data );
		$data = $this->get_recent_ac_data( $data );

		return $data;
	}

	/**
	 * Delete old records
	 */
	public function delete_old_log_records() {
		// remove very old records
		try {
			$date = new DateTime();
			$date->modify( '-30 days' );
			WC_Log_Handler_DB::delete_logs_before_timestamp( $date->format( 'U' ) );
		} catch ( Throwable $t ) {
			$this->logger->warning(
				'There was an issue trying to remove old log records.',
				[
					'message' => $t->getMessage(),
				]
			);
		}
	}

	/**
	 * Gets the most recent 10 error log entries saved
	 *
	 * @return array|object|null
	 */
	public function fetch_recent_log_errors() {
		global $wpdb;

		$results = $wpdb->get_results(
			'SELECT DISTINCT message, context, timestamp 
							FROM ' . $wpdb->prefix . 'woocommerce_log
							WHERE ( source = "activecampaign-for-woocommerce" OR source = "activecampaign-for-woocommerce-errors" )
							AND level = "500" 
							ORDER BY timestamp DESC
							LIMIT 20
						'
		);

		return $results;
	}

	/**
	 * Gets the recent AC data stuff.
	 *
	 * @param mixed $data The data.
	 *
	 * @return mixed The data.
	 */
	private function get_recent_ac_data( $data ) {
		global $wpdb;

		try {
			$data['recent_log_errors'] = $this->fetch_recent_log_errors();
			// phpcs:disable
			$data['wc_actionscheduler_status_array'] = $wpdb->get_results( 'SELECT status, COUNT(*) as "count" FROM ' . $wpdb->prefix . 'actionscheduler_actions GROUP BY status;' );
			$data['wc_webhooks']                     = $wpdb->get_results( 'SELECT name, status FROM ' . $wpdb->prefix . 'wc_webhooks;' );
			$data['wc_rest_keys']                    = $wpdb->get_results( 'SELECT description, last_access, permissions FROM ' . $wpdb->prefix . 'woocommerce_api_keys;' );
			$data['synced_results']                  = $wpdb->get_results( 'SELECT count(*) as count, synced_to_ac FROM `' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_TABLE_NAME . '` WHERE order_date IS NOT NULL AND wc_order_id is not null GROUP BY synced_to_ac' );
			$data['abandoned_results']               = $wpdb->get_results( 'SELECT count(*) as count, synced_to_ac FROM `' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_TABLE_NAME . '` WHERE order_date IS NULL AND wc_order_id is null GROUP BY synced_to_ac' );
			// phpcs:enable
			$abandoned_cart_last_run = get_option( 'activecampaign_for_woocommerce_abandoned_cart_last_run' );
			$date_now                = date_create( 'NOW' );
			$last_order_sync         = get_option( 'activecampaign_for_woocommerce_last_order_sync' );

			if ( $abandoned_cart_last_run ) {
				$abandoned_cart_last_run_interval   = date_diff( $date_now, $abandoned_cart_last_run );
				$data['abandoned_interval_minutes'] = $abandoned_cart_last_run_interval->format( '%i' );
			}

			if ( $last_order_sync ) {
				$last_order_sync_interval            = date_diff( $date_now, $last_order_sync );
				$data['last_order_interval_minutes'] = $last_order_sync_interval->format( '%i' );
			}

			$activecampaign_for_woocommerce_plugins = get_plugin_updates();
			if ( count( $activecampaign_for_woocommerce_plugins ) > 0 && isset( $activecampaign_for_woocommerce_plugins['activecampaign-for-woocommerce/activecampaign-for-woocommerce.php'] ) ) {
				$activecampaign_for_woocommerce_plugin_data = $activecampaign_for_woocommerce_plugins['activecampaign-for-woocommerce/activecampaign-for-woocommerce.php'];
				$data['plugin_data']                        = (object) _get_plugin_data_markup_translate( 'activecampaign-for-woocommerce/activecampaign-for-woocommerce.php', (array) $activecampaign_for_woocommerce_plugin_data, false, true );
			}
		} catch ( Throwable $t ) {
			$this->logger->warning(
				'ActiveCampaign status page threw an error',
				[
					'message' => $t->getMessage(),
				]
			);
		}
		return $data;
	}

	/**
	 * Gets the WC related data.
	 *
	 * @param mixed $data The data.
	 *
	 * @return mixed The data.
	 */
	private function get_woocommerce_data( $data ) {

		try {
			$wc_report                          = wc()->api->get_endpoint_data( '/wc/v3/system_status' );
			$data['wc_environment']             = $wc_report['environment'];
			$data['wc_database']                = $wc_report['database'];
			$data['wc_post_type_counts']        = isset( $wc_report['post_type_counts'] ) ? $wc_report['post_type_counts'] : array();
			$data['wc_settings']                = $wc_report['settings'];
			$data['wc_theme']                   = $wc_report['theme'];
			$data['legacy_api']                 = get_option( 'woocommerce_api_enabled' );
			$data['woocommerce_version']        = wc()->api->get_rest_api_package_version();
			$data['woocommerce_latest_version'] = get_transient( 'woocommerce_system_status_wp_version_check' );
		} catch ( Throwable $t ) {
			$this->logger->warning(
				'ActiveCampaign status page threw an error',
				[
					'message' => $t->getMessage(),
				]
			);
		}
		return $data;
	}

	/**
	 * Gets the table related data.
	 *
	 * @param mixed $data The data.
	 *
	 * @return mixed The data.
	 */
	private function get_table_data( $data ) {
		global $wpdb;

		try {
			$data['table_exists'] = false;
			$table_name           = $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_TABLE_NAME;
			if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) === $table_name ) {
				$data['table_exists'] = true;
			}
		} catch ( Throwable $t ) {
			$this->logger->warning(
				'ActiveCampaign status page threw an error',
				[
					'message' => $t->getMessage(),
				]
			);
		}

		try {
			$data['table_good'] = false;
		} catch ( Throwable $t ) {
			$this->logger->warning(
				'ActiveCampaign status page threw an error',
				[
					'message' => $t->getMessage(),
				]
			);
		}
		return $data;

	}

	/**
	 * Gets the cron related data.
	 *
	 * @param mixed $data The data.
	 *
	 * @return mixed The data.
	 */
	private function get_cron_data( $data ) {
		$logger = new Logger();

		try {
			if ( function_exists( 'wp_get_scheduled_event' ) ) {
				$abandoned_schedule = wp_get_scheduled_event( 'activecampaign_for_woocommerce_cart_updated_recurring_event' );
				$new_order_schedule = wp_get_scheduled_event( 'activecampaign_for_woocommerce_run_order_sync' );

				if ( $abandoned_schedule ) {
					$data['abandoned_schedule']['timestamp'] = gmdate( DATE_ATOM, $abandoned_schedule->timestamp );

					if ( $abandoned_schedule->timestamp && $abandoned_schedule->interval ) {
						$next = $abandoned_schedule->timestamp + $abandoned_schedule->interval - time();
						$data['abandoned_schedule']['next_scheduled'] = $next;
					}

					$data['abandoned_schedule']['schedule'] = $abandoned_schedule->schedule;
					$data['abandoned_schedule']['error']    = false;
				} else {
					$data['abandoned_schedule']['error'] = true;
					$logger->warning(
						'Abandoned cart is not scheduled.',
						[
							'abandoned_cart_schedule' => $abandoned_schedule,
						]
					);
				}

				if ( $new_order_schedule ) {
					$data['new_order_schedule']['error']     = false;
					$data['new_order_schedule']['timestamp'] = gmdate( DATE_ATOM, $new_order_schedule->timestamp );
					$data['new_order_schedule']['schedule']  = $new_order_schedule->schedule;
					if ( $new_order_schedule->timestamp && $new_order_schedule->interval ) {
						$next = $new_order_schedule->timestamp + $new_order_schedule->interval - time();
						$data['new_order_schedule']['next_scheduled'] = $next;
					}
				} else {
					$data['new_order_schedule']['error'] = true;
					$logger->warning(
						'New order sync is not scheduled.',
						[
							'new_order_schedule' => $new_order_schedule,
						]
					);
				}
			} elseif ( function_exists( 'wp_next_scheduled' ) ) {
				$logger->warning( 'The wp_get_scheduled_event function may not exist. Performing wp_next_scheduled instead.' );
				$abandoned_schedule                      = wp_next_scheduled( 'activecampaign_for_woocommerce_cart_updated_recurring_event' );
				$new_order_schedule                      = wp_next_scheduled( 'activecampaign_for_woocommerce_run_order_sync' );
				$data['abandoned_schedule']['timestamp'] = gmdate( DATE_ATOM, $abandoned_schedule );
				$data['new_order_schedule']['timestamp'] = gmdate( DATE_ATOM, $new_order_schedule );
				$data['new_order_schedule']['error']     = false;
				if ( ! $new_order_schedule || ! $abandoned_schedule ) {
					$logger->warning(
						'An order sync is not scheduled.',
						[
							'new_order_schedule'      => $new_order_schedule,
							'abandoned_cart_schedule' => $abandoned_schedule,
						]
					);
				}
			} else {
				$data['new_order_schedule']['error'] = true;
				$logger->warning( 'One of the cron syncs may not be scheduled.' );
			}
		} catch ( Throwable $t ) {
			$logger->warning(
				'ActiveCampaign status page threw an error',
				[
					'message' => $t->getMessage(),
				]
			);
			$data['new_order_schedule']['error'] = true;
		}

		return $data;
	}

}
