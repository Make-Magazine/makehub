<?php

/**
 * The file that defines the Cart_Emptied Event Class.
 *
 * @link       https://www.activecampaign.com/
 * @since      1.0.0
 *
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/includes/events
 */

use Activecampaign_For_Woocommerce_Admin as Admin;
use Activecampaign_For_Woocommerce_AC_Contact_Repository as AC_Contact_Repository;
use Activecampaign_For_Woocommerce_Ecom_Order_Factory as Ecom_Order_Factory;
use Activecampaign_For_Woocommerce_Logger as Logger;
use Activecampaign_For_Woocommerce_Abandoned_Cart_Utilities as Abandoned_Cart_Utilities;
use Activecampaign_For_Woocommerce_Order_Utilities as Order_Utilities;
use Activecampaign_For_Woocommerce_Customer_Utilities as Customer_Utilities;

/**
 * The Order_Finished Event Class.
 *
 * @since      1.0.0
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/includes/events
 * @author     acteamintegrations <team-integrations@activecampaign.com>
 */
class Activecampaign_For_Woocommerce_Order_Finished_Event {

	/**
	 * The WC Customer
	 *
	 * @var WC_Customer
	 */
	public $customer;

	/**
	 * The Ecom Order Factory
	 *
	 * @var Ecom_Order_Factory
	 */
	public $factory;

	/**
	 * The Ecom Order Repo
	 *
	 * @var Activecampaign_For_Woocommerce_Ecom_Order_Repository
	 */
	public $order_repository;

	/**
	 * The Ecom Customer Repo
	 *
	 * @var Activecampaign_For_Woocommerce_Ecom_Customer_Repository
	 */
	public $customer_repository;

	/**
	 * The Admin object
	 *
	 * @var Activecampaign_For_Woocommerce_Admin
	 */
	private $admin;

	/**
	 * The custom ActiveCampaign logger
	 *
	 * @var Activecampaign_For_Woocommerce_Logger
	 */
	private $logger;

	/**
	 * Abandoned cart utilities class
	 *
	 * @var Activecampaign_For_Woocommerce_Abandoned_Cart_Utilities
	 */
	private $abandoned_cart_util;

	/**
	 * The order utilities functions.
	 *
	 * @var Activecampaign_For_Woocommerce_Order_Utilities
	 */
	private $order_utilities;

	/**
	 * Activecampaign_For_Woocommerce_Cart_Emptied_Event constructor.
	 *
	 * @param     Activecampaign_For_Woocommerce_Admin|null               $admin     The Admin object.
	 * @param     Logger|null                                             $logger     The Logger.
	 * @param     Activecampaign_For_Woocommerce_Abandoned_Cart_Utilities $abandoned_cart_util The abandoned cart utilities object.
	 * @param     Activecampaign_For_Woocommerce_Order_Utilities          $order_utilities     The order utilities object.
	 */
	public function __construct(
		Admin $admin,
		Logger $logger,
		Abandoned_Cart_Utilities $abandoned_cart_util,
		Order_Utilities $order_utilities
	) {
		$this->admin               = $admin;
		$this->logger              = $logger;
		$this->abandoned_cart_util = $abandoned_cart_util;
		$this->order_utilities     = $order_utilities;
	}

	/**
	 * Called when an order checkout is completed so that we can process and send data to Hosted.
	 * Directly called via hook action.
	 *
	 * @param     int $order_id     $order_id     The order ID.
	 */
	public function checkout_completed( $order_id ) {
		if ( ! $this->logger ) {
			$this->logger = new Logger();
		}

		global $wpdb;

		$wc_order           = wc_get_order( $order_id );
		$customer_utilities = new Customer_Utilities();
		$customer_data      = $customer_utilities->build_customer_data();
		$cart_uuid          = $this->abandoned_cart_util->get_or_generate_uuid();
		$externalcheckoutid = $this->abandoned_cart_util->generate_externalcheckoutid( $customer_data['id'], $customer_data['email'], $cart_uuid );
		$abandoned_cart_id  = wc()->session->get( 'activecampaign_abandoned_cart_id' );

		if ( ! empty( $abandoned_cart_id ) ) {
			$stored_id = $wpdb->get_var(
			// phpcs:disable
				$wpdb->prepare(
					'SELECT id FROM ' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME . ' 
				WHERE id = %d 
				OR ac_externalcheckoutid = %s 
				OR activecampaignfwc_order_external_uuid = %s',
					[ $abandoned_cart_id, $externalcheckoutid, $cart_uuid ]
				)
			// phpcs:enable
			);
		} else {
			$stored_id = $wpdb->get_var(
			// phpcs:disable
				$wpdb->prepare(
					'SELECT id FROM ' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME . ' 
				WHERE ac_externalcheckoutid = %s 
				OR activecampaignfwc_order_external_uuid = %s',
					[ $externalcheckoutid, $cart_uuid ]
				)
			// phpcs:enable
			);
		}

		try {
			$dt = new DateTime( $wc_order->get_date_created(), new DateTimeZone( 'UTC' ) );
			$dt->format( 'Y-m-d H:i:s' );

			$store_data = [
				'synced_to_ac'                          => 0,
				'customer_id'                           => $customer_data['id'],
				'customer_email'                        => $customer_data['email'],
				'customer_first_name'                   => $customer_data['first_name'],
				'customer_last_name'                    => $customer_data['last_name'],
				'wc_order_id'                           => $order_id,
				'order_date'                            => $dt->format( 'Y-m-d H:i:s' ),
				'activecampaignfwc_order_external_uuid' => $cart_uuid,
				'ac_externalcheckoutid'                 => $externalcheckoutid,
				'customer_ref_json'                     => null,
				'user_ref_json'                         => null,
				'cart_ref_json'                         => null,
				'cart_totals_ref_json'                  => null,
				'removed_cart_contents_ref_json'        => null,
			];
		} catch ( Throwable $t ) {
			$this->logger->error(
				'There was an issue forming the order data.',
				[
					'message'           => $t->getMessage(),
					'abandoned_cart_id' => $stored_id,
					'trace'             => $this->logger->clean_trace( $t->getTrace() ),
				]
			);
		}

		try {
			if ( ! empty( $stored_id ) ) {
				$wpdb->update(
					$wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME,
					$store_data,
					[
						'id' => $stored_id,
					]
				);
				$this->schedule_sync_job( $stored_id );
			} elseif ( ! empty( $abandoned_cart_id ) ) {
				$wpdb->update(
					$wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME,
					$store_data,
					[
						'id' => $abandoned_cart_id,
					]
				);
			} else {
				$stored_id = $wpdb->insert(
					$wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME,
					$store_data
				);
			}

			if ( $wpdb->last_error ) {
				$this->logger->error(
					'Save finished order command: There was an error creating/updating an abandoned cart record.',
					[
						'wpdb_last_error' => $wpdb->last_error,
						'stored_id'       => $stored_id,
					]
				);
			}
		} catch ( Throwable $t ) {
			$this->logger->error(
				'There was an issue saving the order data.',
				[
					'message'           => $t->getMessage(),
					'abandoned_cart_id' => $stored_id,
					'trace'             => $this->logger->clean_trace( $t->getTrace() ),
				]
			);
		}

		$this->abandoned_cart_util->cleanup_session_activecampaignfwc_order_external_uuid();
	}

	/**
	 * Schedules the sync job.
	 *
	 * @param int $row_id The row id.
	 */
	private function schedule_sync_job( $row_id ) {
		wp_schedule_single_event(
			time() + 5,
			'activecampaign_for_woocommerce_run_order_sync',
			[
				'id' => $row_id,
			]
		);

		$this->logger->debug(
			'Schedule finished order for immediate sync.',
			[
				'row_id'       => $row_id,
				'current_time' => time() + 5,
				'schedule'     => wp_get_scheduled_event( 'activecampaign_for_woocommerce_run_order_sync' ),
			]
		);

		$this->order_utilities->schedule_recurring_order_sync_task();
	}
}
