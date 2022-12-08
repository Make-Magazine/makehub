<?php

/**
 * Controls the new order sync process.
 * This will only be run by new order execution or cron so make sure all methods are admin only.
 *
 * @link       https://www.activecampaign.com/
 * @since      1.8.0
 *
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/includes
 */

use Activecampaign_For_Woocommerce_Executable_Interface as Executable;
use Activecampaign_For_Woocommerce_Logger as Logger;
use Activecampaign_For_Woocommerce_AC_Contact as AC_Contact;
use Activecampaign_For_Woocommerce_Order_Utilities as Order_Utilities;
use Activecampaign_For_Woocommerce_Customer_Utilities as Customer_Utilities;
use Activecampaign_For_Woocommerce_Ecom_Order_Repository as Order_Repository;
use Activecampaign_For_Woocommerce_Ecom_Customer as Ecom_Customer;
use Activecampaign_For_Woocommerce_Ecom_Customer_Repository as Customer_Repository;
use Activecampaign_For_Woocommerce_Utilities as AC_Utilities;

/**
 * The Historical_Sync Event Class.
 *
 * @since      1.5.0
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/includes/events
 * @author     acteamintegrations <team-integrations@activecampaign.com>
 */
class Activecampaign_For_Woocommerce_New_Order_Sync_Job implements Executable {

	/**
	 * The custom ActiveCampaign logger
	 *
	 * @var Activecampaign_For_Woocommerce_Logger
	 */
	private $logger;

	/**
	 * The Ecom Order Repo
	 *
	 * @var Activecampaign_For_Woocommerce_Ecom_Order_Repository
	 */
	private $order_repository;

	/**
	 * The order utilities functions.
	 *
	 * @var Activecampaign_For_Woocommerce_Order_Utilities
	 */
	private $order_utilities;

	/**
	 * The order utilities functions.
	 *
	 * @var Activecampaign_For_Woocommerce_Customer_Utilities
	 */
	private $customer_utilities;

	/**
	 * The Ecom Customer Repo
	 *
	 * @var Activecampaign_For_Woocommerce_Ecom_Customer_Repository
	 */
	private $customer_repository;

	/**
	 * The Ecom Connection ID
	 *
	 * @var int
	 */
	private $connection_id;

	/**
	 * Activecampaign_For_Woocommerce_Historical_Sync_Job constructor.
	 *
	 * @param     Activecampaign_For_Woocommerce_Logger|null              $logger     The logger object.
	 * @param     Activecampaign_For_Woocommerce_Order_Utilities          $order_utilities     The order utilities class.
	 * @param     Activecampaign_For_Woocommerce_Customer_Utilities       $customer_utilities     The customer utility class.
	 * @param     Activecampaign_For_Woocommerce_Ecom_Customer_Repository $customer_repository     The customer repository object.
	 * @param     Activecampaign_For_Woocommerce_Ecom_Order_Repository    $order_repository     The order repository object.
	 */
	public function __construct(
		Logger $logger,
		Order_Utilities $order_utilities,
		Customer_Utilities $customer_utilities,
		Customer_Repository $customer_repository,
		Order_Repository $order_repository
	) {
		if ( ! $logger ) {
			$this->logger = new Logger();
		} else {
			$this->logger = $logger;
		}

		$this->order_utilities     = $order_utilities;
		$this->customer_repository = $customer_repository;
		$this->order_repository    = $order_repository;
		$this->customer_utilities  = $customer_utilities;

		$admin_storage = get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_STORAGE_NAME );
		if ( ! empty( $admin_storage ) && isset( $admin_storage['connection_id'] ) ) {
			$this->connection_id = $admin_storage['connection_id'];
		}
	}

	/**
	 * Sync any new/live orders.
	 * Triggered from a hook call.
	 *
	 * @param     mixed ...$args The passed args.
	 */
	public function execute( ...$args ) {
		if ( ! $this->logger ) {
			$this->logger = new Logger();
		}

		$order_id            = null;
		$interval_minutes    = 0;
		$unsynced_order_data = null;
		$recovered_orders    = null;

		if ( isset( $args[0] ) ) {
			// We're just syncing one row
			$order_id            = $args[0];
			$unsynced_order_data = $this->get_unsynced_orders_from_table( $order_id, false );
			$recovered_orders    = $this->get_unsynced_orders_from_table( $order_id, true );
		} else {
			$now      = date_create( 'NOW' );
			$last_run = get_option( 'activecampaign_for_woocommerce_abandoned_cart_last_run' );

			if ( false !== $last_run ) {
				$interval         = date_diff( $now, $last_run );
				$interval_minutes = $interval->format( '%i' );
				if ( $interval_minutes >= 10 ) {
					$unsynced_order_data = $this->get_unsynced_orders_from_table( $order_id, false );
					$recovered_orders    = $this->get_unsynced_orders_from_table( $order_id, true );
				}
			}
		}

		if ( ! empty( $unsynced_order_data ) && count( $unsynced_order_data ) > 0 ) {
			$unsynced_wc_orders = $this->order_utilities->get_orders_from_unsynced_data( $unsynced_order_data );

			if ( ! empty( $unsynced_wc_orders ) && count( $unsynced_wc_orders ) > 0 ) {

				// $this->bulk_sync_data( $unsynced_wc_orders );

				foreach ( $unsynced_order_data as $prep_order ) {
					$wc_order = $this->order_utilities->get_wc_order( $prep_order->wc_order_id );
					$ac_order = $this->single_sync_data( $wc_order );
					$this->check_synced_order( $prep_order, $ac_order );
				}
			}
		}

		if ( ! empty( $recovered_orders ) && count( $recovered_orders ) > 0 ) {
			foreach ( $recovered_orders as $unsynced_recovered_order ) {
				$wc_order = $this->order_utilities->get_wc_order( wc_get_order( $unsynced_recovered_order->wc_order_id ) );
				$ac_order = $this->sync_recovered_order( $wc_order );

				$this->check_synced_order( $unsynced_recovered_order, $ac_order );
			}
		}
	}

	/**
	 * Runs a sync on a single record of data.
	 *
	 * @param WC_Order $wc_order The WooCommerce order.
	 *
	 * @return bool
	 */
	private function single_sync_data( $wc_order ) {
		if ( ! isset( $wc_order ) ) {
			return false;
		}

		// Contact is allowed to fail because customer will do most of this anyway
		$ecom_contact = new AC_Contact();

		if ( $ecom_contact->create_ecom_contact_from_order( $wc_order ) ) {
			$ecom_contact->set_connectionid( $this->connection_id );
		}

		try {
			$ecom_customer = new Ecom_Customer();
			$ecom_customer->create_ecom_customer_from_order( $wc_order );
		} catch ( Throwable $t ) {
			$this->logger->error(
				'Activecampaign_For_Woocommerce_Single_Sync: There was an error with the order build.',
				[
					'message'     => $t->getMessage(),
					'stack_trace' => $t->getTrace(),
				]
			);

			return false;
		}

		if ( isset( $wc_order ) && ! empty( $wc_order->get_id() ) ) {
			try {
				$ecom_order = $this->order_utilities->setup_woocommerce_order_from_admin( $wc_order, 1 );
				$ecom_order = $this->customer_utilities->add_customer_to_order( $wc_order, $ecom_order );
			} catch ( Throwable $t ) {
				$this->logger->error(
					'Activecampaign_For_Woocommerce_Single_Sync: There was an error with the order build.',
					[
						'message'     => $t->getMessage(),
						'stack_trace' => $t->getTrace(),
					]
				);
				return false;
			}

			try {
				if ( $ecom_order && $ecom_order->get_order_number() && $ecom_order->get_externalid() ) {
					$ecom_customer->set_connectionid( $this->connection_id );
					$ecom_order->set_connectionid( $this->connection_id );
					$ecom_order->set_email( $ecom_customer->get_email() );
					$ecom_order_with_products = $this->order_utilities->build_products_for_order( $wc_order, $ecom_order );

					if ( $ecom_customer->get_accepts_marketing() === null ) {
						$ecom_customer->set_accepts_marketing( $wc_order->get_meta( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ACCEPTS_MARKETING_NAME ) );
					}

					if ( isset( $ecom_order_with_products ) ) {
						return $this->order_utilities->sync_to_hosted( $ecom_contact, $ecom_customer, $ecom_order_with_products );
					}
				}
			} catch ( Throwable $t ) {
				$this->logger->error(
					'Sync failed to format an eCommerce order object. Record not synced.',
					[
						'message'      => $t->getMessage(),
						'order_number' => $ecom_order->get_order_number(),
						'order_id'     => $ecom_order->get_externalid(),
						'trace'        => $t->getTrace(),
					]
				);
			}
		}
	}

	/**
	 * Sync a recovered order.
	 *
	 * @param WC_Order $wc_order The WooCommerce order.
	 *
	 * @return Activecampaign_For_Woocommerce_Ecom_Model_Interface|null
	 */
	private function sync_recovered_order( $wc_order ) {
		// Get the customer
		try {
			$ecom_customer = new Ecom_Customer();
			$ecom_customer->create_ecom_customer_from_order( $wc_order );

			// Make sure our customer is added to the customers array
			if ( ! isset( $customers[ $ecom_customer->get_email() ] ) ) {
				if ( $ecom_customer->get_accepts_marketing() === null ) {
					$ecom_customer->set_accepts_marketing( $wc_order->get_meta( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ACCEPTS_MARKETING_NAME ) );
				}

				$customers[ $ecom_customer->get_email() ] = $ecom_customer->serialize_to_array();
			}
		} catch ( Throwable $t ) {
			$this->logger->error(
				'Sync failed to create a customer',
				[
					'message'     => $t->getMessage(),
					'stack_trace' => $this->logger->clean_trace( $t->getTrace() ),
				]
			);

			return null;
		}

		try {
			// Get the order
			$ecom_order_with_products = $this->order_utilities->build_ecom_order( $ecom_customer, $wc_order, 1 );

			if ( ! empty( $ecom_order_with_products ) ) {
				$ecom_order_with_products->set_id( $this->order_utilities->get_ac_order_id( $wc_order->get_id() ) );
				$result = $this->order_repository->update( $ecom_order_with_products );

				if ( isset( $result ) ) {
					return $result;
				}
			} else {
				return null;
			}
		} catch ( Throwable $t ) {
			$this->logger->error(
				'Historical sync failed to create an order',
				[
					'message'      => $t->getMessage(),
					'order_number' => AC_Utilities::validate_object( $wc_order, 'get_order_number' ) ? $wc_order->get_order_number() : null,
					'order_id'     => AC_Utilities::validate_object( $wc_order, 'get_id' ) ? $wc_order->get_id() : null,
					'stack_trace'  => $this->logger->clean_trace( $t->getTrace() ),
				]
			);
			return null;
		}
	}

	/**
	 * Check if our order was properly synced to AC then mark result in the table.
	 *
	 * @param     object     $unsynced_order     The stored cart or order object.
	 * @param     Ecom_Order $ac_order The AC order object.
	 */
	private function check_synced_order( $unsynced_order, $ac_order = null ) {
		global $wpdb;

		$ac_customer_id = null;
		$ac_order_id    = null;

		if ( isset( $unsynced_order->wc_order_id, $unsynced_order->customer_email ) && ! empty( $unsynced_order->customer_email ) ) {
			try {
				if ( AC_Utilities::validate_object( $ac_order, 'get_id' ) ) {
					$ac_order_id = $ac_order->get_id();
				} elseif ( isset( $ac_order->id ) ) {
					$ac_order_id = $ac_order->id;
				}
			} catch ( Throwable $t ) {
				$this->logger->info(
					'Check synced order failed to get ID from ac_order',
					[
						'unsynced_order' => $unsynced_order,
						'ac_order'       => $ac_order,
					]
				);
			}

			try {
				// If no AC order id then find one in hosted
				if ( empty( $ac_order_id ) && isset( $unsynced_order->wc_order_id ) ) {
					$ac_order = $this->order_repository->find_by_externalid( $unsynced_order->wc_order_id );

					if ( AC_Utilities::validate_object( $ac_order, 'get_id' ) ) {
						$ac_order_id = $ac_order->get_id();
					}

					if ( AC_Utilities::validate_object( $ac_order, 'get_customerid' ) ) {
						$ac_customer_id = $ac_order->get_customerid();
					}
				}
			} catch ( Throwable $t ) {
				$this->logger->warning(
					'Check synced order encountered an error trying to find record by external ID',
					[
						'unsynced_order' => $unsynced_order,
						'ac_order'       => $ac_order,
					]
				);
			}

			try {
				if ( empty( $ac_customer_id ) && isset( $ac_order->customerid ) && ! empty( $ac_order->customerid ) ) {
					$ac_customer_id = $ac_order->customerid;
				}
			} catch ( Throwable $t ) {
				$this->logger->warning(
					'customerid not set or threw an error',
					[
						'unsynced_order' => $unsynced_order,
						'ac_order'       => $ac_order,
					]
				);
			}

			try {
				if ( empty( $ac_customer_id ) && AC_Utilities::validate_object( $ac_order, 'serialize_to_array' ) ) {
					$ac_order_array = $ac_order->serialize_to_array();
					$ac_customer_id = $ac_order_array['customerid'];
				}
			} catch ( Throwable $t ) {
				$this->logger->warning(
					'Check synced order failed to get ID from ac_order',
					[
						'unsynced_order' => $unsynced_order,
						'ac_order'       => $ac_order,
					]
				);
			}

			try {
				if ( ! isset( $ac_customer_id ) ) {
					$ac_customer = $this->customer_repository->find_by_email_and_connection_id( $unsynced_order->customer_email, $this->connection_id );
					if ( AC_Utilities::validate_object( $ac_customer, 'get_id' ) ) {
						$ac_customer_id = $ac_customer->get_id();
					}
				}
			} catch ( Throwable $t ) {
				$this->logger->warning(
					'Check synced order failed to get ID from ac_order',
					[
						'unsynced_order' => $unsynced_order,
						'ac_order'       => $ac_order,
					]
				);
			}

			$data = [ 'synced_to_ac' => 9 ];

			if ( isset( $ac_customer_id ) && ! empty( $ac_customer_id ) ) {
				$data['ac_customer_id'] = $ac_customer_id;
			}

			if ( ! empty( $ac_order_id ) ) {
				$data['ac_order_id']  = $ac_order_id;
				$data['synced_to_ac'] = 1;
				$note                 = 'Order record synced to ActiveCampaign (ID: ' . $ac_order_id . ')';
				wc_create_order_note( $unsynced_order->wc_order_id, $note );
				$created_date = new DateTime( 'NOW', new DateTimeZone( 'UTC' ) );
				update_option( 'activecampaign_for_woocommerce_last_order_sync', $created_date );
			}

			// if order do this
			$wpdb->update(
				$wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_TABLE_NAME,
				$data,
				[
					'id' => $unsynced_order->id,
				]
			);

			if ( $wpdb->last_error ) {
				$this->logger->error(
					'Abandonement sync: There was an error updating an abandoned cart record as synced.',
					[
						'wpdb_last_error' => $wpdb->last_error,
					]
				);
			}
		}
	}

	/**
	 * Get all of the unsynced orders from the table.
	 *
	 * @param     int|null $id     The row id.
	 * @param     bool     $recovered If this is a recovered call.
	 *
	 * @return array|bool|object|null
	 */
	private function get_unsynced_orders_from_table( $id = null, $recovered = false ) {
		global $wpdb;

		try {
			// Get the expired carts from our table
			if ( ! empty( $id ) ) {
				if ( true === $recovered ) {
					$where = 'abandoned_date IS NOT NULL';
				} else {
					$where = 'abandoned_date IS NULL';
				}

				$orders = $wpdb->get_results(
				// phpcs:disable
					$wpdb->prepare( 'SELECT id, wc_order_id, ac_externalcheckoutid, customer_email, abandoned_date
						FROM
							`' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_TABLE_NAME . '`
						WHERE '.$where.'
							AND id = %d
							LIMIT 1',
						$id

					)
				// phpcs:enable
				);

			} else {
				if ( true === $recovered ) {
					$where = 'AND abandoned_date IS NOT NULL';
				} else {
					$where = 'AND abandoned_date IS NULL';
				}

				$orders = $wpdb->get_results(
				// phpcs:disable
					$wpdb->prepare( 'SELECT id, wc_order_id, ac_externalcheckoutid, customer_email
					FROM
						`' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_TABLE_NAME . '`
					WHERE
						wc_order_id IS NOT NULL 
						'.$where.'
						AND synced_to_ac = %d
						ORDER BY id ASC
						LIMIT 100',
						0

					)
				// phpcs:enable
				);
			}

			if ( $wpdb->last_error ) {
				$this->logger->error(
					'Abandonment sync: There was an error getting results for abandoned cart records.',
					[
						'wpdb_last_error' => $wpdb->last_error,
					]
				);
			}

			if ( ! empty( $orders ) ) {
				return $orders;
			} else {
				return false;
			}
		} catch ( Throwable $t ) {
			$this->logger->error(
				'Order Sync: There was an error with preparing or getting order results.',
				[
					'message' => $t->getMessage(),
					'trace'   => $this->logger->clean_trace( $t->getTrace() ),
				]
			);
		}
	}
}
