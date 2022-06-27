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
	 * The historical source designation.
	 *
	 * @since 1.7.0
	 *
	 * @var bool
	 */
	private $is_historical;

	/**
	 * If we run the background job or not.
	 *
	 * @since 1.7.3
	 *
	 * @var bool
	 */
	private $run_background;

	/**
	 * Sets the max retries in the case of a failure to connect to hosted.
	 *
	 * @since 1.7.5
	 *
	 * @var bool
	 */
	private $max_retries;

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

		$this->is_historical       = 1;
		$this->max_retries         = 2;
		$this->order_utilities     = $order_utilities;
		$this->contact_repository  = $contact_repository;
		$this->customer_repository = $customer_repository;
		$this->order_repository    = $order_repository;
		$this->customer_utilities  = $customer_utilities;
		$this->bulksync_repository = $bulksync_repository;
		$this->run_background      = true;

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

			$this->logger->info(
				'Historical sync process is continuing from this data.',
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
		if ( ! isset( $this->status['start_time'] ) ) {
			$this->status['start_time'] = wp_date( 'F d, Y - G:i:s e' );
		}

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

		if (
			isset( $_REQUEST['activecampaign_for_woocommerce_nonce_field'] )
			&& wp_verify_nonce( $_REQUEST['activecampaign_for_woocommerce_nonce_field'], 'activecampaign_for_woocommerce_historical_sync_form' )
		) {
			if ( isset( $_POST['activecampaign-historical-sync-limit'] ) && ! empty( $_POST['activecampaign-historical-sync-limit'] ) ) {
				$this->status['batch_limit'] = $_POST['activecampaign-historical-sync-limit'];
			}
			if ( isset( $_POST['activecampaign-historical-sync-starting-record'] ) && ! empty( $_POST['activecampaign-historical-sync-starting-record'] ) ) {
				$this->status['record_offset'] = $_POST['activecampaign-historical-sync-starting-record'];
			}
		}

		$this->logger->debug(
			'Historical sync is starting with the following settings',
			[
				$this->status,
			]
		);

		if ( $this->run_background ) {
			$this->logger->debug( 'Run stepped historical sync' );
			$this->run_historical_sync_one_time();
		} else {
			$this->logger->debug( 'Run active historical sync' );
			 $this->run_full_historical_sync_process();
		}
	}


	/**
	 * Runs the job as an active process.
	 *
	 * @since 1.7.3
	 */
	public function run_active() {
		$this->run_background = false;
		$this->max_retries    = 1;
		$this->execute();
	}

	/**
	 * This runs our sync process after being initialized by the execute command.
	 *
	 * @since 1.6.0
	 */
	private function run_full_historical_sync_process() {
		$exclude = [];

		if ( ! isset( $this->status['record_offset'] ) && isset( $this->status['current_record'] ) ) {
			$this->status['record_offset'] = $this->status['current_record'];
		}

		if ( $this->status['record_offset'] > 1 ) {
			$this->status['current_record'] = $this->status['record_offset'];
		}

		if ( ! isset( $this->status['batch_limit'] ) ) {
			$this->status['batch_limit'] = 50;
		}

		echo esc_html( 'Last record processed (batches of ' . $this->status['batch_limit'] . '):' );

		// phpcs:disable
		while ( $orders = $this->get_orders_by_page( $this->status['current_record'], $this->status['batch_limit'], $exclude ) ) {
			// phpcs:enable
			if ( isset( $orders ) && ! empty( $orders ) ) {
				$this->bulk_sync_data( $orders );

				$sync_stop_type = $this->check_for_stop();

				if ( $sync_stop_type ) {
					switch ( $sync_stop_type ) {
						case '1':
							$this->status['stop_type_name'] = 'cancelled';
							$this->update_sync_status( 'cancel' );
							return;
						case '2':
							$this->status['stop_type_name'] = 'paused';
							$this->update_sync_status( 'pause' );
							return;
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
				echo '<p>';
				echo esc_html( 'Record ' . $this->status['last_processed_id'] );
				echo '</p>';
				ob_flush();
				flush();
			}
		}

		if ( ! isset( $this->status['stop_type_name'] ) ) {
			$this->update_sync_status( 'finished' );
		}
	}

	/**
	 * This runs our sync process after being initialized by the execute command.
	 *
	 * @since 1.6.0
	 */
	private function run_historical_sync_one_time() {
		$exclude = [];

		if ( $this->status['record_offset'] > 1 ) {
			$this->status['current_record'] = $this->status['record_offset'];
		}

		$orders = $this->get_orders_by_page( $this->status['current_record'], $this->status['batch_limit'], $exclude );

		if ( isset( $orders ) && ! empty( $orders ) ) {
			$this->bulk_sync_data( $orders );

			$this->status['current_record'] += count( $orders );
			// $this->status['is_running'] = false;
			$next_starting_id = $this->status['current_record'];

			$sync_stop_type = $this->check_for_stop();

			if ( $sync_stop_type ) {
				switch ( $sync_stop_type ) {
					case '1':
						$this->status['stop_type_name'] = 'cancelled';
						$this->update_sync_status( 'cancel' );
						return;
					case '2':
						$this->status['stop_type_name'] = 'paused';
						$this->update_sync_status( 'pause' );
						return;
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
			}

			$this->update_sync_status();

			$this->schedule_bulk_historical_sync( $next_starting_id, $this->status['batch_limit'] );
		} elseif ( ! isset( $this->status['stop_type_name'] ) ) {
			$this->update_sync_status( 'finished' );
		}
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

			$wc_order = $this->order_utilities->get_wc_order( $order );

			if ( $this->order_utilities->validate_object( $wc_order, 'get_data' ) ) {
				$this->status['last_processed_id'] = $wc_order->get_id();

				if ( $this->order_utilities->is_refund_order( $wc_order ) ) {
					$this->add_failed_order_to_status( $order, $wc_order );
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

					$this->add_failed_order_to_status( $order, $wc_order );
					continue;
				}

				try {
					// Get the order
					$ecom_order_with_products = $this->order_utilities->build_ecom_order( $ecom_customer, $wc_order, $this->is_historical );

					if ( isset( $ecom_order_with_products ) && ! empty( $ecom_order_with_products ) ) {

						if ( ! $this->is_historical && $this->order_utilities->validate_object( $wc_order, 'get_id' ) ) {
							$ecom_order_with_products->set_id( $this->get_ac_order_id( $wc_order->get_id() ) );
						}

						$customer_orders[ $ecom_customer->get_email() ][] = $this->order_utilities->serialize_ecom_order_for_bulksync( $ecom_order_with_products );
						$success_count ++;
					} else {
						$this->add_failed_order_to_status( $order, $wc_order );
						continue;
					}
				} catch ( Throwable $t ) {
					$this->logger->error(
						'Historical sync failed to create an order',
						[
							'message'      => $t->getMessage(),
							'order_number' => $this->order_utilities->validate_object( $wc_order, 'get_order_number' ) ? $wc_order->get_order_number() : null,
							'order_id'     => $this->order_utilities->validate_object( $wc_order, 'get_id' ) ? $wc_order->get_id() : null,
							'stack_trace'  => $this->logger->clean_trace( $t->getTrace() ),
						]
					);

					$this->add_failed_order_to_status( $order, $wc_order );
					continue;
				}
			} else {
				try {
					if ( $this->order_utilities->is_refund_order( $wc_order ) ) {
						$this->add_failed_order_to_status( $order, $wc_order );
						continue;
					}

					$this->logger->warning(
						'Historical Sync: Could not retrieve a valid WC_Order from WooCommerce. This order cannot be synced at this time.',
						[
							'order data' => $this->order_utilities->validate_object( $wc_order, 'get_data' ) ? $wc_order->get_data() : null,
							'order_id'   => $this->order_utilities->validate_object( $wc_order, 'get_id' ) ? $wc_order->get_id() : null,
						]
					);
					$this->status['failed_order_id_array'][] = $wc_order->get_id();
				} catch ( Throwable $t ) {
					$this->logger->warning(
						'Historical Sync: This order was not processable.',
						[
							'message'     => $t->getMessage(),
							'wc_order'    => isset( $wc_order ) ? $wc_order : null,
							'order'       => isset( $order ) ? $order : null,
							'order class' => get_class( $order ),
						]
					);
				}

				continue;
			}
		}

		$orders = null; // save memory

		$serialized_customers = [];
		// Now that we have all of the serialized customers and orders we can put them in the right object
		if ( count( $customers ) > 0 ) {
			foreach ( $customers as $customer_email => $customer ) {
				try {
					if ( isset( $customer_orders[ $customer_email ] ) && count( $customer_orders[ $customer_email ] ) > 0 ) {
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
					$this->bulksync_repository->set_max_retries( $this->max_retries );

					// Until corrected on deepdata bulksync will always return empty but errors will return in our code.
					// Be aware that unless there is a major error this process will not tell you if it succeeded or not.
					$response = $this->bulksync_repository->create( $ecom_bulksync );

					if ( is_array( $response ) && isset( $response['type'] ) ) {
						if ( 'error' === $response['type'] ) {
							$this->status['stop_type_name'] = 'halted';
							$this->status['stop_type_code'] = $response['code'];
							$this->logger->error(
								'Hosted returned a bad response and cannot be reached or cannot process the request at this time. Historical sync will be stopped. Please try again later.',
								[
									'response' => $response,
								]
							);
							$this->update_sync_status( 'halted' );
						}

						if ( 'timeout' === $response['type'] ) {
							$this->status['stop_type_name'] = 'halted';
							$this->status['stop_type_code'] = 'connection timeout';
							$this->logger->error(
								'Hosted could not be reached.',
								[
									'response' => $response,
								]
							);
							$this->update_sync_status( 'halted' );
						}
					}
				} catch ( Throwable $t ) {
					$this->logger->warning(
						'There was an issue with the bulksync API send.',
						[
							'message'     => $t->getMessage(),
							'stack_trace' => $this->logger->clean_trace( $t->getTrace() ),
							'response'    => $response,
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
	 * Get the ActiveCampaign ID.
	 *
	 * @param string|int $order_id The order ID.
	 *
	 * @return mixed|string|null
	 */
	public function get_ac_order_id( $order_id ) {
		// check if we have it in storage ac_order_id
		$ac_order_id = $this->order_utilities->get_ac_orderid_from_wc_order( $order_id );

		if ( ! isset( $ac_order_id ) ) {
			// check ac by externalcheckoutid
			$externalcheckout_id = $this->order_utilities->get_externalcheckoutid_from_table_by_orderid( $order_id );

			if ( ! empty( $externalcheckout_id ) ) {
				$order_ac = $this->order_repository->find_by_externalcheckoutid( $externalcheckout_id );
			}

			if ( ! $this->order_utilities->validate_object( $order_ac, 'get_id' ) || empty( $order_ac->get_id() ) ) {
				// check ac by external order id
				$order_ac = $this->order_repository->find_by_externalid( $order_id );
			}

			$ac_order_id = $order_ac->get_id();
		}

		return $ac_order_id;
	}

	/**
	 * Add a failed order to the status.
	 *
	 * @param     object   $order The order.
	 * @param     WC_Order $wc_order The WooCommerce order.
	 */
	private function add_failed_order_to_status( $order = null, $wc_order = null ) {
		if ( $this->order_utilities->validate_object( $wc_order, 'get_id' ) && ! empty( $wc_order->get_id() ) ) {
			$this->status['failed_order_id_array'][] = $wc_order->get_id();
		} elseif ( $this->order_utilities->validate_object( $order, 'get_id' ) && ! empty( $order->get_id() ) ) {
			$this->status['failed_order_id_array'][] = $order->get_id();
		} else {
			$this->status['failed_order_id_array'][] = $order;
		}
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
				'Historical Sync Stop Request Found: Cancelled.',
				[
					'stop_type' => $sync_stop_type,
				]
			);

			return $sync_stop_type;
		}

		return false;
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
				case 'halted':
					update_option(
						ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME,
						wp_json_encode(
							[
								'is_paused'   => false,
								'is_running'  => false,
								'is_halted'   => true,
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
						'Historical Sync was halted due to an error',
						[
							'status' => $this->status,
						]
					);
					die( 'There was a fatal error encountered and Historical Sync was halted. Please go back to the historical sync page and check your ActiveCampaign logs.' );
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

	/**
	 * Sync any new orders.
	 * Triggered from a hook call.
	 *
	 * @param     mixed ...$args The passed args.
	 */
	public function sync_new_orders( ...$args ) {
		$order_id = null;

		if ( isset( $args[0] ) ) {
			// We're just syncing one row
			$order_id = $args[0];
		}

		$unsynced_order_data = $this->get_unsynced_orders_from_table( $order_id, false );
		$recovered_orders    = $this->get_unsynced_orders_from_table( $order_id, true );

		if ( ! empty( $unsynced_order_data ) && count( $unsynced_order_data ) > 0 ) {
			$unsynced_wc_orders = $this->order_utilities->get_orders_from_unsynced_data( $unsynced_order_data );

			if ( ! empty( $unsynced_wc_orders ) && count( $unsynced_wc_orders ) > 0 ) {
				$this->bulk_sync_data( $unsynced_wc_orders );

				foreach ( $unsynced_order_data as $order ) {
					$this->check_synced_order( $order );
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
			$ecom_order_with_products = $this->order_utilities->build_ecom_order( $ecom_customer, $wc_order, 0 );

			if ( ! empty( $ecom_order_with_products ) ) {
				$ecom_order_with_products->set_id( $this->get_ac_order_id( $wc_order->get_id() ) );
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
					'order_number' => $this->order_utilities->validate_object( $wc_order, 'get_order_number' ) ? $wc_order->get_order_number() : null,
					'order_id'     => $this->order_utilities->validate_object( $wc_order, 'get_id' ) ? $wc_order->get_id() : null,
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

		if ( $this->order_utilities->validate_object( $ac_order, 'get_id' ) ) {
			$ac_order_id = $ac_order->get_id();
		} else {
			$ac_order = $this->order_repository->find_by_externalid( $unsynced_order->wc_order_id );

			if ( $this->order_utilities->validate_object( $ac_order, 'get_id' ) ) {
				$ac_order_id = $ac_order->get_id();
			}
		}

		if ( $this->order_utilities->validate_object( $ac_order, 'get_customer_id' ) ) {
			$ac_customer_id = $ac_order->get_customer_id();
		}

		if ( ! isset( $ac_customer_id ) ) {
			$ac_customer = $this->customer_repository->find_by_email_and_connection_id( $unsynced_order->customer_email, $this->connection_id );
			if ( $this->order_utilities->validate_object( $ac_customer, 'get_id' ) ) {
				$ac_customer_id = $ac_customer->get_id();
			}
		}

		$data = [ 'synced_to_ac' => 0 ];

		if ( ! empty( $ac_customer_id ) ) {
			$data['ac_customer_id'] = $ac_customer_id;
		}

		if ( ! empty( $ac_order_id ) ) {
			$data['ac_order_id']  = $ac_order_id;
			$data['synced_to_ac'] = 1;
			$note                 = 'Order record synced to ActiveCampaign (ID: ' . $ac_order_id . ')';
			wc_create_order_note( $unsynced_order->wc_order_id, $note );
		}

		// if order do this
		$wpdb->update(
			$wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME,
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
							`' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME . '`
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
						`' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME . '`
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
				$this->is_historical = 0;

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

	/**
	 * Schedules another followup bulk historical sync to run as a background job.
	 *
	 * @param int $start_rec The start record.
	 * @param int $batch_limit The limit for the batch.
	 *
	 * @since 1.7.3
	 */
	public function schedule_bulk_historical_sync( $start_rec, $batch_limit ) {
		$logger = new Logger();
		try {
			wp_schedule_single_event(
				time() + 10,
				ACTIVECAMPAIGN_FOR_WOOCOMMERCE_RUN_SYNC_NAME,
				[
					'args' => [
						'sync_type'   => 'bulk',
						'start_rec'   => $start_rec,
						'batch_limit' => $batch_limit,
					],
				]
			);

			$logger->info(
				'Schedule historical sync',
				[
					'current_time'    => time(),
					'start_on_record' => $start_rec,
					'batch_limit'     => $batch_limit,
					'schedule'        => wp_get_scheduled_event( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_RUN_SYNC_NAME ),
				]
			);
		} catch ( Throwable $t ) {
			$logger->error(
				'There was an issue scheduling historical sync',
				[
					'message'  => $t->getMessage(),
					'function' => 'schedule_single_historical_sync',
				]
			);
		}
	}
}
