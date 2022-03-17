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
use Activecampaign_For_Woocommerce_AC_Contact_Repository as Contact_Repository;
use Activecampaign_For_Woocommerce_Ecom_Customer_Repository as Customer_Repository;
use Activecampaign_For_Woocommerce_Bulksync_Repository as Bulksync_Repository;
use Activecampaign_For_Woocommerce_Ecom_Bulksync as Ecom_Bulksync;
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
	 * The bulksync repository object.
	 *
	 * @since 1.6.0
	 *
	 * @var Activecampaign_For_Woocommerce_Bulksync_Repository
	 */
	private $bulksync_repository;

	/**
	 * The initializing status array.
	 *
	 * @since 1.6.0
	 *
	 * @var array
	 */
	private $status = [
		'current_record'        => 0, // based on count not record number
		'last_processed_id'     => 0, // The id of the last record processed
		'record_offset'         => 0, // What record should be the starting point
		'success_count'         => 0,
		'batch_limit'           => 100, // How many records per batch (200 is the API limit)
		'start_time'            => null, // WP date time
		'end_time'              => null, // WP date time
		'failed_order_id_array' => [], // Array of failed IDs
		'is_paused'             => false, // if the sync is paused
		'is_running'            => true, // running status
		'sync_type'             => 'bulk',
	];

	/**
	 * Activecampaign_For_Woocommerce_Historical_Sync_Job constructor.
	 *
	 * @param     Activecampaign_For_Woocommerce_Logger|null              $logger     The logger object.
	 * @param     Activecampaign_For_Woocommerce_Order_Utilities          $order_utilities     The order utilities class.
	 * @param     Activecampaign_For_Woocommerce_Customer_Utilities       $customer_utilities     The customer utility class.
	 * @param     Activecampaign_For_Woocommerce_AC_Contact_Repository    $contact_repository     The contact repository object.
	 * @param     Activecampaign_For_Woocommerce_Ecom_Customer_Repository $customer_repository     The customer repository object.
	 * @param     Activecampaign_For_Woocommerce_Ecom_Order_Repository    $order_repository     The order repository object.
	 * @param     Activecampaign_For_Woocommerce_Bulksync_Repository      $bulksync_repository The bulksync repository object.
	 */
	public function __construct(
		Logger $logger = null,
		Order_Utilities $order_utilities,
		Customer_Utilities $customer_utilities,
		Contact_Repository $contact_repository,
		Customer_Repository $customer_repository,
		Order_Repository $order_repository,
		Bulksync_Repository $bulksync_repository
	) {
		if ( ! $logger ) {
			$this->logger = new Logger();
		} else {
			$this->logger = $logger;
		}

		$this->order_utilities     = $order_utilities;
		$this->contact_repository  = $contact_repository;
		$this->customer_repository = $customer_repository;
		$this->order_repository    = $order_repository;
		$this->customer_utilities  = $customer_utilities;
		$this->bulksync_repository = $bulksync_repository;

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
		if ( ! $this->logger ) {
			$this->logger = new Logger();
		}

		// If from a paused state, use the stored status
		if ( get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME ) ) {
			$this->status = json_decode( get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME ), 'array' );

			if ( true === $this->status['is_running'] || 'true' === $this->status['is_running'] ) {
				$this->logger->info(
					'Historical sync is already running or in a hung state.',
					[
						'sync_status' => $this->status,
					]
				);

				delete_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_SCHEDULED_STATUS_NAME );

				return;
			}

			$this->logger->info(
				'Historical sync process discovered a previous run that may have errored or been paused. Continuing from this data.',
				[
					'status' => $this->status,
				]
			);

			$this->status['is_paused']  = false;
			$this->status['is_running'] = true;
			$this->update_sync_status();
		} else {
			delete_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_LAST_STATUS_NAME );

			// Set the init sync status
			$this->update_sync_status();
		}

		// set the start time
		$this->status['start_time'] = wp_date( 'F d, Y - G:i:s e' );

		// Remove the scheduled status because our process is no longer scheduled & now running
		delete_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_SCHEDULED_STATUS_NAME );

		if ( isset( $args[0] ) ) {
			// This usually only gets set when starting the initial sync, not from a pause
			if ( ! empty( $args[0]->sync_type ) ) {
				$this->status['sync_type'] = $args[0]->sync_type;
			} elseif ( ! empty( $args[0]['sync_type'] ) ) {
				$this->status['sync_type'] = $args[0]['sync_type'];
			}

			if ( ! empty( $args[0]->batch_limit ) ) {
				$this->status['batch_limit'] = $args[0]->batch_limit;
			} elseif ( ! empty( $args[0]['batch_limit'] ) ) {
				$this->status['batch_limit'] = $args[0]['batch_limit'];
			}

			if ( ! empty( $args[0]->start_rec ) ) {
				$this->status['record_offset'] = $args[0]->start_rec;
			} elseif ( ! empty( $args[0]['start_rec'] ) ) {
				$this->status['record_offset'] = $args[0]['start_rec'];
			}
		}

		$this->logger->debug(
			'Historical sync is starting with the following settings',
			[
				$this->status,
			]
		);

		$this->run_sync_process();

	}

	/**
	 * This runs our sync process after being initialized by the execute command.
	 *
	 * @since 1.6.0
	 */
	private function run_sync_process() {
		if ( $this->status['record_offset'] ) {
			$exclude = [];
			if ( $this->status['record_offset'] > 1 ) {
				for ( $i = 0; $i < $this->status['record_offset']; $i ++ ) {
					$exclude[] = $i;
				}
			}
		}

		// phpcs:disable
		while ( $orders = $this->get_orders_by_page( $this->status['current_record'], $this->status['batch_limit'], $exclude ) ) {
		// phpcs:enable

			$this->bulk_sync_data( $orders );

			$sync_stop_type = $this->check_for_stop();

			if ( $sync_stop_type ) {
				switch ( $sync_stop_type ) {
					case '1':
						$this->status['stop_type_name'] = 'cancelled';
						$this->update_sync_status( 'cancel' );
						break;
					case '2':
						$this->status['stop_type_name'] = 'paused';
						$this->update_sync_status( 'pause' );
						break;
					default:
						$this->logger->error(
							'Historical sync stop status found but did not match a type. There may be a bug. Sync will continue.',
							[
								'status'    => $this->status,
								'stop_type' => $sync_stop_type,
							]
						);
						break;
				}

				break;
			}

			$this->status['current_record'] += count( $orders );
			$this->update_sync_status();
		}

		if ( ! isset( $this->status['stop_type_name'] ) ) {
			$this->update_sync_status( 'finished' );
		}
	}


	/**
	 * We absolutely need the WC_Order so we need to make every attempt to get it for a valid order.
	 * The order could be anything so we have to make every attempt to get WC_Order from whatever we get from WC.
	 *
	 * @param object|string|array $order The unknown order item passed back from WC.
	 *
	 * @return bool|WC_Order
	 */
	private function get_wc_order( $order ) {
		if ( $order instanceof WC_Order ) {
			return $order;
		}

		// If it's not a valid WC_Order, try using it as a non WC object.
		if ( is_object( $order ) ) {
			try {
				$wc_order = wc_get_order( $order );

				if ( $wc_order instanceof WC_Order ) {
					return $wc_order;
				}

				$wc_order = wc_get_order( $order->get_id() );

				if ( $wc_order instanceof WC_Order ) {
					return $wc_order;
				}
			} catch ( Throwable $t ) {
				$this->logger->debug(
					'Historical Sync: wc_get_order threw an error on the order object. ',
					[
						'message'     => $t->getMessage(),
						'order class' => get_class( $order ),
					]
				);
			}
		}

		// Try the order as an array
		try {
			if ( is_array( $order ) ) {
				$wc_order = wc_get_order( $order['id'] );

				if ( $wc_order instanceof WC_Order ) {
					return $wc_order;
				}
			}
		} catch ( Throwable $t ) {
			$this->logger->debug(
				'Historical Sync: There was an issue parsing this order as an array.',
				[
					'message' => $t->getMessage(),
				]
			);
		}

		try {
			$wc_order = wc_get_order( $order );

			if ( $wc_order instanceof WC_Order ) {
				return $wc_order;
			}
		} catch ( Throwable $t ) {
			$this->logger->debug(
				'Historical Sync: A final WC_Order object failed to retrieve.',
				[
					'message' => $t->getMessage(),
					'order'   => $wc_order,
				]
			);
		}

		try {
			if ( is_object( $order ) && method_exists( $order, 'get_id' ) ) {
				$wc_order = new WC_Order( $order->get_id() );
			} elseif ( isset( $order['id'] ) ) {
				$wc_order = new WC_Order( $order['id'] );
			} else {
				$wc_order = new WC_Order( $order );
			}

			if ( $wc_order instanceof WC_Order ) {
				return $wc_order;
			}
		} catch ( Throwable $t ) {
			$this->logger->debug(
				'Historical Sync: Could not create a new WC_Order from any data type.',
				[
					'message' => $t->getMessage(),
				]
			);
		}

		$this->logger->warning(
			'Historical Sync: A WC_Order object could not be generated.',
			[
				'order' => $order,
			]
		);

		return $order;
	}

	/**
	 * This is the sync process using bulk sync.
	 *
	 * @since 1.6.0
	 *
	 * @param array $orders An array of orders.
	 */
	public function bulk_sync_data( $orders ) {
		$customers       = [];
		$customer_orders = [];
		$success_count   = 0;

		foreach ( $orders as $order ) {
			if ( ! isset( $order ) ) {
				$this->logger->warning( 'Historical Sync: This order record is not set.', [ $order ] );
				continue;
			}

			$wc_order = $this->get_wc_order( $order );

			if ( $wc_order instanceof WC_Order ) {
				$this->status['last_processed_id'] = $wc_order->get_id();

				if ( $this->order_utilities->is_refund_order( $wc_order ) ) {
					$this->status['failed_order_id_array'][] = $wc_order->get_order_number();
					continue;
				}

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
						'Historical sync failed to create a customer',
						[
							'message'     => $t->getMessage(),
							'stack_trace' => $this->logger->clean_trace( $t->getTrace() ),
						]
					);

					$this->status['failed_order_id_array'][] = $wc_order->get_order_number();

					continue;
				}

				try {
					// Get the order
					$ecom_order_with_products                         = $this->build_ecom_order( $ecom_customer, $wc_order );
					$customer_orders[ $ecom_customer->get_email() ][] = $this->serialize_ecom_order_for_bulksync( $ecom_order_with_products );
					$success_count++;
				} catch ( Throwable $t ) {
					$this->logger->error(
						'Historical sync failed to create an order',
						[
							'message'     => $t->getMessage(),
							'stack_trace' => $this->logger->clean_trace( $t->getTrace() ),
						]
					);

					$this->status['failed_order_id_array'][] = $wc_order->get_order_number();

					continue;
				}
			} else {
				try {
					if ( $this->order_utilities->is_refund_order( $wc_order ) ) {
						$this->status['failed_order_id_array'][] = $wc_order->get_order_number();
						continue;
					}

					$this->logger->warning(
						'Historical Sync: Could not retrieve a valid WC_Order from WooCommerce. This order cannot be synced at this time.',
						[
							'order data' => $wc_order->get_data(),
							'order_id'   => method_exists( $wc_order, 'get_id' ) ? $wc_order->get_id() : null,
						]
					);
					$this->status['failed_order_id_array'][] = $wc_order->get_id();
				} catch ( Throwable $t ) {
					$this->logger->warning(
						'Historical Sync: This order was not processable.',
						[
							'message'     => $t->getMessage(),
							'wc_order'    => $wc_order,
							'order'       => $order,
							'order class' => get_class( $order ),
						]
					);
				}

				continue;
			}
		}

		unset( $orders ); // save memory

		$serialized_customers = [];
		// Now that we have all of the serialized customers and orders we can put them in the right object
		if ( count( $customers ) > 0 ) {
			foreach ( $customers as $customer_email => $customer ) {
				try {
					if ( count( $customer_orders[ $customer_email ] ) > 0 ) {
						$customer['orders']     = $customer_orders[ $customer_email ];
						$serialized_customers[] = $customer;
					}
				} catch ( Throwable $t ) {
					$this->logger->debug(
						'Historical Sync: There was an issue setting the serialized customers.',
						[
							'message' => $t->getMessage(),
						]
					);
				}
			}

			$customer_orders = null; // save memory
			$customers       = null; // save memory

			if ( count( $serialized_customers ) > 0 ) {
				try {
					$ecom_bulksync = new Ecom_Bulksync();
					$ecom_bulksync->set_service( 'woocommerce' );
					$ecom_bulksync->set_customers( $serialized_customers );
					$ecom_bulksync->set_externalid( get_site_url() );

					$serialized_customers = null; // save memory

					$response = $this->bulksync_repository->create( $ecom_bulksync );
				} catch ( Throwable $t ) {
					$this->logger->warning(
						'There was an issue with the bulksync API send.',
						[
							'message' => $t->getMessage(),
						]
					);
				}
				if ( ! isset( $response ) || false === $response ) {
					return false;
				}

				$this->status['success_count'] += $success_count;
			}
		}
	}

	/**
	 * This builds the ecom order object.
	 *
	 * @param     Activecampaign_For_Woocommerce_Ecom_Customer $ecom_customer The ecom customer object.
	 * @param     WC_Order                                     $order The WC order object.
	 *
	 * @return Activecampaign_For_Woocommerce_Ecom_Order|bool|null
	 */
	private function build_ecom_order( Ecom_Customer $ecom_customer, $order ) {
		try {
			$ecom_order = $this->order_utilities->setup_woocommerce_order_from_admin( $order, true );
			$ecom_order = $this->customer_utilities->add_customer_to_order( $order, $ecom_order );
		} catch ( Throwable $t ) {
			$this->logger->error(
				'Activecampaign_For_Woocommerce_Historical_Sync: There was an error with the order build.',
				[
					'message'     => $t->getMessage(),
					'stack_trace' => $this->logger->clean_trace( $t->getTrace() ),
				]
			);

			return null;
		}

		try {
			if ( $ecom_order && $ecom_order->get_order_number() && $ecom_order->get_externalid() ) {
				$ecom_order->set_connectionid( $this->connection_id );
				$ecom_order->set_email( $ecom_customer->get_email() );

				// Return the order with products
				return $this->order_utilities->build_products_for_order( $order, $ecom_order );
			}
		} catch ( Throwable $t ) {
			$this->logger->error(
				'Historical sync failed to format an ecommerce order object.',
				[
					'message'      => $t->getMessage(),
					'order_number' => $ecom_order->get_order_number(),
					'order_id'     => $ecom_order->get_externalid(),
				]
			);
			return null;
		}

		return $ecom_order;
	}

	/**
	 * Gets all orders by page filtered by status.
	 *
	 * @param int   $offset The offset.
	 * @param int   $batch_limit The limit of results.
	 * @param array $exclude The records to exclude.
	 *
	 * @return stdClass|WC_Order[]
	 */
	public function get_orders_by_page( $offset, $batch_limit, $exclude ) {
		// limits and paged can be added
		$orders = wc_get_orders(
			array(
				'status'  => array( 'wc-processing', 'wc-completed' ),
				'limit'   => $batch_limit,
				'offset'  => $offset,
				'exclude' => $exclude,
				'orderby' => 'id',
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
	 * Serialize the order in preparation for bulksync. This requires a very specific structure so for now we do this.
	 *
	 * @since 1.6.0
	 *
	 * @param     Activecampaign_For_Woocommerce_Ecom_Order $order The ecom order object.
	 *
	 * @return object
	 */
	private function serialize_ecom_order_for_bulksync( Ecom_Order $order ) {
		try {
			return (object) [
				'ecomOrder' => $order->serialize_to_array(),
			];
		} catch ( Throwable $t ) {
			$this->logger->debug(
				'Historical Sync: Could not serialize an ecom order in bulksync.',
				[
					'message' => $t->getMessage(),
					'order'   => $order,
				]
			);
		}
	}

	/**
	 * Updates the sync statuses in the options for the info readout to use on the frontend.
	 * This is how the admin panel is able to tell where we are in the process and to keep record of the sync.
	 *
	 * @param string $type Indicates the type of update.
	 */
	private function update_sync_status( $type = '' ) {
		try {
			switch ( $type ) {
				case 'pause':
					update_option(
						ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME,
						wp_json_encode(
							[
								'current_record'        => $this->status['current_record'],
								'success_count'         => $this->status['success_count'],
								'failed_order_id_array' => $this->status['failed_order_id_array'],
								'last_processed_id'     => $this->status['last_processed_id'],
								'is_paused'             => true,
								'is_running'            => false,
								'is_finished'           => true,
							]
						)
					);
					update_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_LAST_STATUS_NAME, wp_json_encode( $this->status ) );
					break;
				case 'cancel':
				case 'finished':
					update_option(
						ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME,
						wp_json_encode(
							[
								'is_paused'   => false,
								'is_running'  => false,
								'is_finished' => true,
							]
						)
					);
					// delete_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME );
					$this->status['end_time']   = wp_date( 'F d, Y - G:i:s e' );
					$this->status['is_running'] = false;
					delete_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME );
					update_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_LAST_STATUS_NAME, wp_json_encode( $this->status ) );
					$this->logger->info(
						'Historical Sync Ended',
						[
							'status' => $this->status,
						]
					);
					break;
				default:
					update_option(
						ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME,
						wp_json_encode(
							[
								'current_record'        => $this->status['current_record'],
								'success_count'         => $this->status['success_count'],
								'failed_order_id_array' => $this->status['failed_order_id_array'],
								'last_processed_id'     => $this->status['last_processed_id'],
								'is_running'            => true,
							]
						)
					);
					break;
			}
		} catch ( Throwable $t ) {
			$this->logger->error(
				'There was an issue attempting to save historical sync status.',
				[
					'message' => $t->getMessage(),
					'status'  => $this->status,
				]
			);
		}
	}
}
