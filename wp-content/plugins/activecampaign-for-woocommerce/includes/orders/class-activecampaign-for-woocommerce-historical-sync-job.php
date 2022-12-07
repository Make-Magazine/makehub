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

use Activecampaign_For_Woocommerce_AC_Contact as AC_Contact;
use Activecampaign_For_Woocommerce_Logger as Logger;
use Activecampaign_For_Woocommerce_Order_Utilities as Order_Utilities;
use Activecampaign_For_Woocommerce_Executable_Interface as Executable;
use Activecampaign_For_Woocommerce_Ecom_Customer as Ecom_Customer;
use Activecampaign_For_Woocommerce_Bulksync_Repository as Bulksync_Repository;
use Activecampaign_For_Woocommerce_Ecom_Bulksync as Ecom_Bulksync;
use Activecampaign_For_Woocommerce_AC_Contact_Batch as AC_Contact_Batch;
use Activecampaign_For_Woocommerce_AC_Contact_Batch_Repository as AC_Contact_Batch_Repository;
use Activecampaign_For_Woocommerce_Utilities as AC_Utilities;

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
	 * The order utilities functions.
	 *
	 * @var Activecampaign_For_Woocommerce_Order_Utilities
	 */
	private $order_utilities;

	/**
	 * The AC contact batch repository.
	 *
	 * @var object AC_Batch_Repository.
	 */
	private $contact_batch_repository;

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
	 * @var int
	 */
	private $max_retries;

	/**
	 * The halt count for failures.
	 *
	 * @since 1.9.4
	 *
	 * @var int
	 */
	private $halt_count;

	/**
	 * The external ID used in Hosted
	 *
	 * @since 1.9.5
	 *
	 * @var string
	 */
	private $site_external_id;

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
		'last_update'           => null, // WP date time
		'end_time'              => null, // WP date time
		'failed_order_id_array' => [], // Array of failed IDs
		'is_paused'             => false, // if the sync is paused
		'is_running'            => true, // running status
		'sync_type'             => 'bulk',
		'status_name'           => 'none',
	];

	/**
	 * Activecampaign_For_Woocommerce_Historical_Sync_Job constructor.
	 *
	 * @param     Activecampaign_For_Woocommerce_Logger|null                 $logger     The logger object.
	 * @param     Activecampaign_For_Woocommerce_Order_Utilities             $order_utilities     The order utilities class.
	 * @param     Activecampaign_For_Woocommerce_AC_Contact_Batch_Repository $contact_batch_repository The repository for contact batching.
	 * @param     Activecampaign_For_Woocommerce_Bulksync_Repository         $bulksync_repository     The bulksync repository object.
	 */
	public function __construct(
		Logger $logger = null,
		Order_Utilities $order_utilities,
		AC_Contact_Batch_Repository $contact_batch_repository,
		Bulksync_Repository $bulksync_repository
	) {
		if ( ! $logger ) {
			$this->logger = new Logger();
		} else {
			$this->logger = $logger;
		}

		$this->max_retries              = 2;
		$this->halt_count               = 0;
		$this->order_utilities          = $order_utilities;
		$this->contact_batch_repository = $contact_batch_repository;
		$this->bulksync_repository      = $bulksync_repository;
		$this->run_background           = true;

		$admin_storage = get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_STORAGE_NAME );
		if ( ! empty( $admin_storage ) && isset( $admin_storage['connection_id'] ) ) {
			$this->connection_id = $admin_storage['connection_id'];
		}

		if ( ! empty( $admin_storage ) && isset( $admin_storage['external_id'] ) ) {
			$this->site_external_id = $admin_storage['external_id'];
		} else {
			$this->site_external_id = get_site_url();
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

		$_post    = wp_unslash( $_POST );
		$_request = wp_unslash( $_REQUEST );
		if (
			isset( $_request['activecampaign_for_woocommerce_nonce_field'] )
			&& wp_verify_nonce( $_request['activecampaign_for_woocommerce_nonce_field'], 'activecampaign_for_woocommerce_historical_sync_form' )
		) {
			if ( isset( $_post['activecampaign-historical-sync-limit'] ) && ! empty( $_post['activecampaign-historical-sync-limit'] ) ) {
				$this->status['batch_limit'] = $_post['activecampaign-historical-sync-limit'];
			}
			if ( isset( $_post['activecampaign-historical-sync-starting-record'] ) && ! empty( $_post['activecampaign-historical-sync-starting-record'] ) ) {
				$this->status['record_offset'] = $_post['activecampaign-historical-sync-starting-record'];
			}
		}

		delete_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_LAST_STATUS_NAME );
		$this->status['status_name'] = 'orders';

		// Set the init sync status
		$this->update_sync_status();

		$this->logger->debug(
			'Historical sync is starting with the following settings',
			[
				$this->status,
			]
		);

		if ( $this->run_background ) {
			$this->logger->debug( 'Run stepped historical sync' );
			// $this->run_historical_sync_one_time();
			$this->run_full_historical_sync_process();
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
		$exclude                     = [];
		$this->status['status_name'] = 'orders';

		if ( ! isset( $this->status['record_offset'] ) && isset( $this->status['current_record'] ) ) {
			$this->status['record_offset'] = $this->status['current_record'];
		}

		if ( $this->status['record_offset'] > 1 ) {
			$this->status['current_record'] = $this->status['record_offset'];
		}

		if ( ! isset( $this->status['batch_limit'] ) ) {
			$this->status['batch_limit'] = 50;
		}

		$this->update_sync_status();

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
			$this->cache_flush_extend_time();
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
	private function bulk_sync_data( $orders ) {
		$customers       = [];
		$customer_orders = [];
		$success_count   = 0;

		foreach ( $orders as $order ) {
			if ( ! isset( $order ) ) {
				$this->logger->warning( 'Historical Sync: This order record is not set.', [ $order ] );
				continue;
			}

			$wc_order = $this->order_utilities->get_wc_order( $order );

			if ( AC_Utilities::validate_object( $wc_order, 'get_data' ) ) {
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
					$ecom_order_with_products = $this->order_utilities->build_ecom_order( $ecom_customer, $wc_order, 0 );

					if ( isset( $ecom_order_with_products ) && ! empty( $ecom_order_with_products ) ) {

						if ( AC_Utilities::validate_object( $wc_order, 'get_id' ) ) {
							$ecom_order_with_products->set_id( $this->order_utilities->get_ac_order_id( $wc_order->get_id() ) );
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
							'order_number' => AC_Utilities::validate_object( $wc_order, 'get_order_number' ) ? $wc_order->get_order_number() : null,
							'order_id'     => AC_Utilities::validate_object( $wc_order, 'get_id' ) ? $wc_order->get_id() : null,
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
							'order data' => AC_Utilities::validate_object( $wc_order, 'get_data' ) ? $wc_order->get_data() : null,
							'order_id'   => AC_Utilities::validate_object( $wc_order, 'get_id' ) ? $wc_order->get_id() : null,
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
					$ecom_bulksync->set_externalid( $this->site_external_id );

					$serialized_customers = null; // save memory
					$this->bulksync_repository->set_max_retries( $this->max_retries );

					// Until corrected on deepdata bulksync will always return empty but errors will return in our code.
					// Be aware that unless there is a major error this process will not tell you if it succeeded or not.
					$response = $this->bulksync_repository->create( $ecom_bulksync );

					if ( is_array( $response ) && isset( $response['type'] ) ) {
						if ( 'error' === $response['type'] && 400 !== $response['code'] ) {
							$this->halt_count++;

							if ( $this->halt_count > 10 ) {
								$this->status['stop_type_name'] = 'halted';
								$this->status['stop_type_code'] = $response['code'];
								$this->logger->error(
									'Hosted returned a bad response and cannot be reached or cannot process the request at this time. Historical sync will be stopped. Please try again later.',
									[
										'half_count' => $this->halt_count,
										'response'   => $response,
									]
								);
								$this->update_sync_status( 'halted' );
							}

							return false;
						}

						if ( 'timeout' === $response['type'] ) {
							$this->status['stop_type_name'] = 'halted';
							$this->status['stop_type_code'] = $response['code'];
							$this->logger->error(
								'Hosted could not be reached due to timeout.',
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
				$this->halt_count = 0;

				if ( ! isset( $response ) || false === $response ) {
					return false;
				}

				$this->status['success_count'] += $success_count;
			}
		}
	}

	/**
	 * Add a failed order to the status.
	 *
	 * @param     object   $order The order.
	 * @param     WC_Order $wc_order The WooCommerce order.
	 */
	private function add_failed_order_to_status( $order = null, $wc_order = null ) {
		if ( AC_Utilities::validate_object( $wc_order, 'get_id' ) && ! empty( $wc_order->get_id() ) ) {
			$this->status['failed_order_id_array'][] = $wc_order->get_id();
		} elseif ( AC_Utilities::validate_object( $order, 'get_id' ) && ! empty( $order->get_id() ) ) {
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
	private function get_orders_by_page( $offset, $batch_limit, $exclude ) {
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
			delete_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_HISTORICAL_SYNC_STOP_CHECK_NAME );
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
			$this->status['last_update'] = wp_date( 'F d, Y - G:i:s e' );
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
								'status_name'           => 'paused',
								'status'                => $this->status,
							]
						)
					);
					update_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_LAST_STATUS_NAME, wp_json_encode( $this->status ) );
					break;
				case 'cancel':
				case 'finished':
					$this->status['is_running']  = false;
					$this->status['is_finished'] = true;
					$this->status['end_time']    = wp_date( 'F d, Y - G:i:s e' );
					$this->status['is_running']  = false;
					delete_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME, $this->status );
					update_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_LAST_STATUS_NAME, wp_json_encode( $this->status ) );
					$this->logger->info(
						'Historical Sync Ended',
						[
							'status' => $this->status,
						]
					);
					break;
				case 'halted':
					$this->status['status_name'] = 'halt';
					$this->status['is_paused']   = false;
					$this->status['is_running']  = false;
					$this->status['is_halted']   = true;
					$this->status['is_finished'] = true;
					$this->status['end_time']    = wp_date( 'F d, Y - G:i:s e' );
					update_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME, wp_json_encode( $this->status ) );
					update_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_LAST_STATUS_NAME, wp_json_encode( $this->status ) );
					$this->logger->info(
						'Historical Sync was halted due to an error',
						[
							'status' => $this->status,
						]
					);
					die( 'There was a fatal error encountered and Historical Sync was halted. Please go back to the historical sync page and check your ActiveCampaign logs.' );
				default:
					update_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME, wp_json_encode( $this->status ) );
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
	 * Schedules another followup bulk historical sync to run as a background job.
	 *
	 * @param int $start_rec The start record.
	 * @param int $batch_limit The limit for the batch.
	 *
	 * @since 1.7.3
	 */
	private function schedule_bulk_historical_sync( $start_rec, $batch_limit ) {
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

	/**
	 * Step 1 run historical sync on contacts
	 */
	public function run_historical_sync_contacts() {
		global $wpdb;

		// set the start time
		if ( ! isset( $this->status['start_time'] ) ) {
			$this->status['start_time'] = wp_date( 'F d, Y - G:i:s e' );
		}

		$this->update_sync_status();
		$this->output_echo( 'Syncing contacts...' );
		$this->status['status_name']   = 'contacts';
		$this->status['contact_count'] = 0;
		$this->status['contact_total'] = $wpdb->get_var( 'SELECT count(email) FROM ' . $wpdb->prefix . 'wc_customer_lookup WHERE email != "";' );

		$this->update_sync_status();

		$c               = 0;
		$limit           = 200;
		$synced_contacts = 0;
		// phpcs:disable
		while ( $wc_customers = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT first_name, last_name, email, user_id, customer_id FROM ' . $wpdb->prefix . 'wc_customer_lookup WHERE email != "" ORDER BY customer_id LIMIT %d, %d;',
				[ $c, $limit ]
			)
		// phpcs:enable
		) ) {
			$bulk_contacts = [];
			foreach ( $wc_customers as $wc_customer ) {
				try {
					if ( isset( $wc_customer->email ) ) {
						$ac_contact = new AC_Contact();
						$ac_contact->set_first_name( $wc_customer->first_name );
						$ac_contact->set_last_name( $wc_customer->last_name );
						$ac_contact->set_email( $wc_customer->email );
						$ac_contact->set_phone( $this->find_wc_phone_number( $wc_customer ) );

						$bulk_contacts[] = $ac_contact->serialize_to_array();
						$synced_contacts ++;
					}
				} catch ( Throwable $t ) {
					$this->logger->warning(
						'A contact failed validation. This record will be skipped.',
						[
							'message' => $t->getMessage(),
						]
					);
				}
			}

			try {
				$ac_contact_batch = new AC_Contact_Batch();
				$ac_contact_batch->set_contacts( $bulk_contacts );
				$batch    = $ac_contact_batch->to_json();
				$response = $this->contact_batch_repository->create( $ac_contact_batch );

				if (
					 is_array( $response ) &&
					 isset( $response['type'] ) &&
					 ( 'timeout' === $response['type'] || 'error' === $response['type'] )
				) {
					$this->halt_count++;
					if ( $this->halt_count > 3 ) {
						$this->status['stop_type_name']     = 'halted'; // todo: if is there multiple halts in a row?
						$this->status['stop_type_code']     = $response['code'];
						$this->status['stop_type_response'] = wp_json_encode( $response );
						$this->logger->error(
							'Hosted returned a bad response and cannot be reached or cannot process the request at this time. Historical sync will be stopped. Please try again later.',
							[
								'response' => $response,
							]
						);

						$this->update_sync_status( 'halted' );
					}
					return false;
				}

				// We did not receive an error state, reset halt count
				$this->halt_count = 0;

				$this->logger->debug(
					'Processing the batch customer object...',
					[
						'batch'    => $batch,
						'response' => $response,
					]
				);
			} catch ( Throwable $t ) {
				$this->logger->error(
					'There was an issue creating a contact batch',
					[]
				);
			}

			$c += $limit;
			$this->output_echo( $c . ' Contacts Synced' );
			$this->status['contact_count'] = $synced_contacts;
			$this->update_sync_status();
			// Don't let cache overflow
			$this->cache_flush_extend_time();
		}
		$this->output_echo( 'Contacts Finished Syncing' );
		$this->logger->info(
			'Contacts synced',
			[
				'count' => $c,
			]
		);
	}

	/**
	 * Finds the phone number through few methods.
	 *
	 * @param object $wc_customer The WooCommerce customer.
	 *
	 * @return string|null The phone number returned.
	 */
	private function find_wc_phone_number( $wc_customer ) {
		global $wpdb;
		try {
			if ( AC_Utilities::validate_object( $wc_customer, 'user_id' ) && ! empty( $wc_customer->user_id ) ) {
				$phone = $wpdb->get_var(
					$wpdb->prepare(
						'SELECT meta_value FROM ' . $wpdb->prefix . 'usermeta where meta_key = %s AND user_id = %d;',
						[ 'billing_phone', $wc_customer->user_id ]
					)
				);

				if ( isset( $phone ) && ! empty( $phone ) ) {
					return $phone;
				}
			}
		} catch ( Throwable $t ) {
			$this->logger->error(
				'Historical Sync: There was an error trying to find phone number from usermeta.',
				[
					'message' => $t->getMessage(),
					'trace'   => $this->logger->clean_trace( ( $t->getTrace() ) ),
				]
			);
		}

		try {
			if ( ! empty( $wc_customer->customer_id ) ) {
				$order_id = $wpdb->get_var(
					$wpdb->prepare(
						'SELECT order_id FROM ' . $wpdb->prefix . 'wc_order_stats where customer_id = %d ORDER BY order_id ASC LIMIT 1;',
						[ $wc_customer->customer_id ]
					)
				);

				if ( isset( $order_id ) ) {
					$wc_order = wc_get_order( $order_id );

					if ( AC_Utilities::validate_object( $wc_order, 'get_billing_phone' ) ) {
						$phone = $wc_order->get_billing_phone();
					}

					if ( isset( $phone ) && ! empty( $phone ) ) {
						return $phone;
					}
				}
			}
		} catch ( Throwable $t ) {
			$this->logger->error(
				'Historical Sync: There was an error trying to find phone number from order stats.',
				[
					'message' => $t->getMessage(),
					'trace'   => $this->logger->clean_trace( ( $t->getTrace() ) ),
				]
			);
		}

		return '';
	}


	/**
	 * Outputs an echo and flushes the string.
	 *
	 * @param string $output The output string.
	 */
	private function output_echo( $output ) {
		echo '<p>' . esc_html( $output ) . '</p>';
		ob_flush();
		flush();
	}

	/**
	 * Flush the cache and extend the time limit
	 */
	private function cache_flush_extend_time() {
		if ( function_exists( 'wp_cache_flush_runtime' ) ) {
			// Memory will overflow if we don't clear the cache during read. This will flush just this cycle.
			wp_cache_flush_runtime();
		} elseif ( function_exists( 'wp_cache_flush' ) ) {
			// This will flush all the cache but we have to for this to not crash
			wp_cache_flush();
		}

		if ( function_exists( 'set_time_limit' ) ) {
			// This extends our time limit another 30 seconds to make sure we don't get a process end early.
			set_time_limit( 30 );
		}
	}
}
