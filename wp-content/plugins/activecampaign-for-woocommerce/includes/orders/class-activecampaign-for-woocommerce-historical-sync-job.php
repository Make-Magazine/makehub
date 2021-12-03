<?php

/**
 * Controls the historical sync process.
 * This will only be run by admin or cron so make sure all methods are admin only.
 *
 * @link       https://www.activecampaign.com/
 * @since      1.5.0
 *
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/includes
 */

use Activecampaign_For_Woocommerce_Logger as Logger;
use Activecampaign_For_Woocommerce_Order_Utilities as Order_Utilities;
use Activecampaign_For_Woocommerce_Customer_Utilities as Customer_Utilities;
use Activecampaign_For_Woocommerce_Executable_Interface as Executable;
use Activecampaign_For_Woocommerce_Ecom_Order as Ecom_Order;
use Activecampaign_For_Woocommerce_Ecom_Order_Repository as Order_Repository;
use Activecampaign_For_Woocommerce_Ecom_Customer as Ecom_Customer;
use Activecampaign_For_Woocommerce_AC_Contact as AC_Contact;
use Activecampaign_For_Woocommerce_AC_Contact_Repository as Contact_Repository;
use Activecampaign_For_Woocommerce_Ecom_Customer_Repository as Customer_Repository;

/**
 * The Historical_Sync Event Class.
 *
 * @since      1.5.0
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/includes/events
 * @author     acteamintegrations <team-integrations@activecampaign.com>
 */
class Activecampaign_For_Woocommerce_Historical_Sync_Job implements Executable {

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
	 * The AC contact repository.
	 *
	 * @var object AC_Contact.
	 */
	private $contact_repository;

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
	 * @param     Activecampaign_For_Woocommerce_Customer_Utilities       $customer_utilities The customer utility class.
	 * @param     Activecampaign_For_Woocommerce_AC_Contact_Repository    $contact_repository     The contact repository object.
	 * @param     Activecampaign_For_Woocommerce_Ecom_Customer_Repository $customer_repository     The customer repository object.
	 * @param     Activecampaign_For_Woocommerce_Ecom_Order_Repository    $order_repository     The order repository object.
	 */
	public function __construct(
		Logger $logger = null,
		Order_Utilities $order_utilities,
		Customer_Utilities $customer_utilities,
		Contact_Repository $contact_repository,
		Customer_Repository $customer_repository,
		Order_Repository $order_repository
	) {
		$this->logger              = $logger ?: new Logger();
		$this->order_utilities     = $order_utilities;
		$this->contact_repository  = $contact_repository;
		$this->customer_repository = $customer_repository;
		$this->order_repository    = $order_repository;
		$this->customer_utilities  = $customer_utilities;

		$admin_storage = get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_STORAGE_NAME );
		if ( ! empty( $admin_storage ) && isset( $admin_storage['connection_id'] ) ) {
			$this->connection_id = $admin_storage['connection_id'];
		}
	}

	/**
	 * Execute function.
	 *
	 * @param     mixed ...$args The arg.
	 *
	 * @return mixed|void
	 */
	public function execute( ...$args ) {
		$this->logger->debug(
			'Start historical sync',
			[
				'args' => $args,
			]
		);

		$this->run_historical_sync_data();
	}

	/**
	 * Updates the sync statuses in the options for the info readout to use on the frontend.
	 * This is how the admin panel is able to tell where we are in the process and to keep record of the sync.
	 *
	 * @param array  $status The status array.
	 * @param string $type Indicates the type of update.
	 */
	private function update_sync_status( $status, $type = '' ) {
		switch ( $type ) {
			case 'pause':
				update_option(
					ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME,
					wp_json_encode(
						[
							'current_record'        => $status['current_record'],
							'success_count'         => $status['success_count'],
							'failed_order_id_array' => $status['failed_order_id_array'],
							'current_page'          => $status['current_page'],
							'is_paused'             => true,
							'is_running'            => false,
						]
					)
				);
				update_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_LAST_STATUS_NAME, wp_json_encode( $status ) );
				break;
			case 'cancel':
			case 'finished':
				delete_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME );
				$status['end_time'] = wp_date( 'F m, Y - G:i:s e' );
				update_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_LAST_STATUS_NAME, wp_json_encode( $status ) );
				$this->logger->info(
					'Historical Sync Ended',
					[
						'status' => $status,
					]
				);
				break;
			default:
				update_option(
					ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME,
					wp_json_encode(
						[
							'current_record'        => $status['current_record'],
							'success_count'         => $status['success_count'],
							'failed_order_id_array' => $status['failed_order_id_array'],
							'current_page'          => $status['current_page'],
							'is_running'            => true,
						]
					)
				);
				break;
		}
	}

	/**
	 * Runs the historical sync process.
	 */
	private function run_historical_sync_data() {
		if ( get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME ) ) {
			$status = json_decode( get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME ), 'array' );
			$this->logger->info(
				'Historical sync process discovered a previous run that may have errored or been paused. Continuing from this data.',
				[
					'status' => $status,
				]
			);
		} else {
			$status = [
				'current_record'        => 0,
				'success_count'         => 0,
				'current_page'          => 0,
				'failed_order_id_array' => [],
			];

			$this->update_sync_status( $status );
		}

		// Remove the scheduled status because our process is no longer scheduled & now running
		delete_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_SCHEDULED_STATUS_NAME );

		$status['start_time'] = wp_date( 'F m, Y - G:i:s e' );

		// phpcs:disable
		while ( $orders = $this->get_orders_by_page( $status['current_page'] ) ) {
			// phpcs:enable
			foreach ( $orders as $order ) {
				if ( ! isset( $order ) ) {
					continue;
				}

				$success = false;

				// Contact is allowed to fail because customer will do most of this anyway
				$ecom_contact = new AC_Contact();

				if ( $ecom_contact->create_ecom_contact_from_order( $order ) ) {
					$ecom_contact->set_connectionid( $this->connection_id );
				}

				try {
					$ecom_customer = new Ecom_Customer();
					$ecom_customer->create_ecom_customer_from_order( $order );
				} catch ( Throwable $t ) {
					$this->logger->error(
						'Activecampaign_For_Woocommerce_Historical_Sync: There was an error with the order build.',
						[
							'message'     => $t->getMessage(),
							'stack_trace' => $t->getTrace(),
						]
					);

					return;
				}

				if ( isset( $order ) && ! empty( $order->get_id() ) ) {
					try {
						$ecom_order = $this->order_utilities->setup_woocommerce_order_from_admin( $order, true );
						$ecom_order = $this->customer_utilities->add_customer_to_order( $order, $ecom_order );
					} catch ( Throwable $t ) {
						$this->logger->error(
							'Activecampaign_For_Woocommerce_Historical_Sync: There was an error with the order build.',
							[
								'message'     => $t->getMessage(),
								'stack_trace' => $t->getTrace(),
							]
						);
						$this->update_sync_status( $status, 'cancel' );
						return;
					}

					try {
						if ( $ecom_order && $ecom_order->get_order_number() && $ecom_order->get_externalid() ) {
							$ecom_customer->set_connectionid( $this->connection_id );
							$ecom_order->set_connectionid( $this->connection_id );
							$ecom_order->set_email( $ecom_customer->get_email() );
							$ecom_order_with_products = $this->order_utilities->build_products_for_order( $order, $ecom_order );

							if ( $ecom_customer->get_accepts_marketing() === null ) {
								$ecom_customer->set_accepts_marketing( $order->get_meta( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ACCEPTS_MARKETING_NAME ) );
							}

							if ( $ecom_order_with_products && $this->sync_to_hosted( $ecom_contact, $ecom_customer, $ecom_order_with_products ) ) {
								$success = true;
								$this->order_utilities->update_last_synced( $order->get_id() );
								$status['success_count'] ++;
							}
						}
					} catch ( Throwable $t ) {
						$this->logger->error(
							'Historical sync failed to format an ecommerce order object. Record skipped.',
							[
								'message'      => $t->getMessage(),
								'order_number' => $ecom_order->get_order_number(),
								'order_id'     => $ecom_order->get_externalid(),
							]
						);
					}
				}

				if ( ! $success ) {
					$status['failed_order_id_array'][] = $order->get_order_number();
					$this->logger->debug(
						'Historical sync failed to sync an order',
						[
							'order_number' => $order->get_order_number(),
						]
					);
				}

				$status['current_record'] ++;
				$sync_stop_type = $this->check_for_stop();

				if ( $sync_stop_type ) {
					switch ( $sync_stop_type ) {
						case '1':
							$status['stop_type_name'] = 'cancelled';
							$this->update_sync_status( $status, 'cancel' );
							break;
						case '2':
							$status['stop_type_name'] = 'paused';
							$this->update_sync_status( $status, 'pause' );
							break;
						default:
							$this->logger->error(
								'Historical sync stop status found but did not match a type. There may be a bug. Sync will continue.',
								[
									'status'    => $status,
									'stop_type' => $sync_stop_type,
								]
							);
							break;
					}

					break 2;
				}
			}

			$status['current_page'] ++;
			$this->update_sync_status( $status );
		}

		if ( ! isset( $status['stop_type_name'] ) ) {
			$this->update_sync_status( $status, 'finished' );
		}
	}

	/**
	 * Gets all orders by page filtered by status.
	 *
	 * @param int $page The page number.
	 *
	 * @return stdClass|WC_Order[]
	 */
	public function get_orders_by_page( $page ) {
		// limits and paged can be added
		$orders = wc_get_orders(
			array(
				'status'  => array( 'wc-processing', 'wc-completed' ),
				'limit'   => 1,
				'paged'   => $page,
				'orderby' => 'date',
				'order'   => 'ASC',
			)
		);

		return $orders;
	}

	/**
	 * Checks for a stop condition.
	 * 1 = cancel, 2 = pause
	 */
	private function check_for_stop() {
		global $wpdb;
		$sync_stop_type = $wpdb->get_var( 'SELECT option_value from ' . $wpdb->prefix . 'options WHERE option_name = "activecampaign_for_woocommerce_historical_sync_stop"' );

		if ( ! empty( $sync_stop_type ) ) {
			delete_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_STOP_CHECK_NAME );
			$this->logger->alert(
				'Historical Sync Stop Request Found: Cancelled by admin.',
				[
					'stop_type' => $sync_stop_type,
				]
			);

			return $sync_stop_type;
		}

		return false;
	}

	/**
	 * Creates or updates the contact, customer, and order objects to Hosted.
	 *
	 * @param     Activecampaign_For_Woocommerce_AC_Contact    $ecom_contact The object to send to AC ecom contact.
	 * @param     Activecampaign_For_Woocommerce_Ecom_Customer $ecom_customer The object to send to AC ecom customer.
	 * @param     Activecampaign_For_Woocommerce_Ecom_Order    $ecom_order The object to send to AC ecom order.
	 *
	 * @return bool
	 */
	private function sync_to_hosted( AC_Contact $ecom_contact, Ecom_Customer $ecom_customer, Ecom_Order $ecom_order ) {
		// Sync the contact
		try {
			// Contact is allowed to fail
			$ac_contact = $this->contact_repository->find_by_email( $ecom_contact->get_email() );
			if ( isset( $ac_contact ) && $ac_contact->get_id() ) {
				$ecom_contact->set_id( $ac_contact->get_id() );
				$this->contact_repository->update( $ecom_contact );
			} else {
				$this->contact_repository->create( $ecom_contact );
			}
		} catch ( Throwable $t ) {
			$this->logger->warning(
				'Historical Sync: Could not create contact.',
				[
					'email'   => $ecom_contact->get_email(),
					'message' => $t->getMessage(),
				]
			);
		}

		// Sync the customer
		try {
			$ac_customer = $this->customer_repository->find_by_email_and_connection_id( $ecom_customer->get_email(), $this->connection_id );

			if ( isset( $ac_customer ) && $ac_customer->get_id() ) {
				$ecom_customer->set_id( $ac_customer->get_id() );
				$customer_response = $this->customer_repository->update( $ecom_customer );
			} else {
				$customer_response = $this->customer_repository->create( $ecom_customer );
			}
		} catch ( Throwable $t ) {
			$this->logger->error(
				'Historical Sync: Customer create process received a thrown error.',
				[
					'email'   => $ecom_customer->get_email(),
					'message' => $t->getMessage(),
				]
			);

			return false;
		}

		// Sync the order
		try {
			$ac_order = $this->order_repository->find_by_externalid( $ecom_order->get_externalid() );

			if ( isset( $ac_customer ) && ! empty( $ac_customer->get_id() ) ) {
				$ecom_order->set_customerid( $ac_customer->get_id() );
			} else {
				$ecom_order->set_customerid( $customer_response->get_id() );
			}

			if ( $ecom_order->get_customerid() && $ac_order->get_id() ) {
				$ecom_order->set_id( $ac_order->get_id() );
				$ecom_order->set_source( $ac_order->get_source() );
				$order_response = $this->order_repository->update( $ecom_order );
			} else {
				$ecom_order->set_source( '0' );
				$order_response = $this->order_repository->create( $ecom_order );
			}

			$this->order_utilities->store_ac_id( $ecom_order->get_externalid(), $order_response->get_id() );
		} catch ( Throwable $t ) {
			$this->logger->error(
				'Historical Sync: Could not create order.',
				[
					'order_json' => $ecom_order->order_to_json(),
					'message'    => $t->getMessage(),
					'trace'      => $t->getTrace(),
				]
			);

			return false;
		}

		try {
			if ( $customer_response->get_id() && $order_response->get_id() ) {
				return true;
			} else {
				return false;
			}
		} catch ( Throwable $t ) {
			$this->logger->error(
				'Historical sync: Issue with syncing order',
				[
					'customer' => $ecom_customer->get_id(),
					'contact'  => $ecom_contact->get_id(),
					'order'    => $ecom_order->get_id(),
				]
			);
			return false;
		}
	}
}
