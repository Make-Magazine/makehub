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
use Activecampaign_For_Woocommerce_Ecom_Customer as Ecom_Customer;
use Activecampaign_For_Woocommerce_Ecom_Customer_Repository as Ecom_Customer_Repository;
use Activecampaign_For_Woocommerce_Ecom_Order_Factory as Ecom_Order_Factory;
use Activecampaign_For_Woocommerce_Ecom_Order_Repository as Ecom_Order_Repository;
use Activecampaign_For_Woocommerce_Logger as Logger;
use Activecampaign_For_Woocommerce_Ecom_Product_Factory as Ecom_Product_Factory;
use AcVendor\GuzzleHttp\Exception\GuzzleException;
use Brick\Money\Money;

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
	 * The WC Cart
	 *
	 * @var WC_Cart
	 */
	public $cart;

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
	 * The guest customer email address
	 *
	 * @var string
	 */
	private $customer_email;

	/**
	 * The guest customer first name
	 *
	 * @var string
	 */
	private $customer_first_name;

	/**
	 * The guest customer last name
	 *
	 * @var string
	 */
	private $customer_last_name;

	/**
	 * The WooCommerce customer object
	 *
	 * @var WC_Customer
	 */
	private $customer_woo;

	/**
	 * The resulting existing or newly created AC ecom customer
	 *
	 * @var Ecom_Model
	 */
	private $customer_ac;

	/**
	 * Hash of the WooCommerce session ID plus the guest customer email.
	 * Used to identify an order as being created in a pending state.
	 *
	 * @var string
	 */
	private $external_checkout_id;

	/**
	 * The native ecom order object used to
	 * create or update an order in AC
	 *
	 * @var Activecampaign_For_Woocommerce_Ecom_Order
	 */
	private $ecom_order;

	/**
	 * The resulting existing or newly created AC ecom order
	 *
	 * @var Activecampaign_For_Woocommerce_Ecom_Model_Interface
	 */
	private $order_ac;

	/**
	 * The custom ActiveCampaign logger
	 *
	 * @var Activecampaign_For_Woocommerce_Logger
	 */
	private $logger;

	/**
	 * The WooCommerce session
	 *
	 * @var WC_Session|null
	 */
	private $wc_session;

	/**
	 * The Ecom Product Factory
	 *
	 * @var Ecom_Product_Factory
	 */
	private $product_factory;

	/**
	 * The Ecom Connection ID
	 *
	 * @var int
	 */
	private $connection_id;

	/**
	 * Activecampaign_For_Woocommerce_Cart_Emptied_Event constructor.
	 *
	 * @param     Activecampaign_For_Woocommerce_Admin|null              $admin     The Admin object.
	 * @param     Activecampaign_For_Woocommerce_Ecom_Order_Factory|null $factory     The ecom order factory.
	 * @param     Ecom_Order_Repository|null                             $order_repository     The Ecom Order Repository.
	 * @param     Ecom_Product_Factory|null                              $product_factory     The Ecom Product Factory.
	 * @param     Ecom_Customer_Repository|null                          $customer_repository     The Ecom Customer Repository.
	 * @param     Logger|null                                            $logger     The Logger.
	 */
	public function __construct(
		Admin $admin,
		Ecom_Order_Factory $factory,
		Ecom_Order_Repository $order_repository,
		Ecom_Product_Factory $product_factory,
		Ecom_Customer_Repository $customer_repository,
		Logger $logger = null
	) {
		$this->admin               = $admin;
		$this->factory             = $factory;
		$this->order_repository    = $order_repository;
		$this->product_factory     = $product_factory;
		$this->customer_repository = $customer_repository;
		$this->logger              = $logger;
	}

	/**
	 * Called when an order checkout is completed and meta can be added to the final order
	 *
	 * @param     order_id $order_id     The order ID.
	 *
	 * @return bool
	 */
	public function checkout_meta( $order_id ) {
		try {
			$this->logger = $this->logger ?: new Logger();
			// get order
			if ( ! empty( $this->admin->get_storage() ) && isset( $this->admin->get_storage()['connection_id'] ) ) {
				$this->connection_id = $this->admin->get_storage()['connection_id'];

				$order = wc_get_order( $order_id );

				if ( $order && $order->get_id() && $order->get_billing_email() && $this->verify_order_status( $order->get_status() ) ) {
					// init functions
					if ( ! empty( wc()->customer ) ) {
						// event origin is triggered by customer
						$this->cart           = $this->cart ?: wc()->cart;
						$this->customer       = $this->customer ?: wc()->customer;
						$this->customer_woo   = $this->customer;
						$this->customer_email = $order->get_billing_email();
						$this->setup_woocommerce_customer();

						$find_or_create_ac_order = $this->setup_woocommerce_order( $order );

						if ( 1 === $find_or_create_ac_order ) {
							// Existing order found in AC, try to update it
							$this->logger->debug( 'Checkout meta: order exists, update it' );
							$this->update_ac_order();
						}

						if ( ! $find_or_create_ac_order ) {
							// 0 was returned, meaning some kind of exception
							$this->logger->error(
								'Checkout meta: Could not create an order in AC',
								[
									'order_id'      => $order->get_id(),
									'email'         => $this->customer_email,
									'connection_id' => $this->connection_id,
								]
							);
						} else {
							// Mark the sync time in the order
							add_post_meta( $order_id, 'activecampaign_for_woocommerce_last_synced', date( 'Y-m-d H:i:s' ), false );
						}
					}

					return true;
				}
			} else {
				$this->logger->error(
					'Checkout meta: Could not retrieve or find connection id.',
					[
						'connection_id' => $this->connection_id,
					]
				);
			}
		} catch ( Exception $e ) {
			$this->logger->error(
				'Checkout meta: There was a fatal error in the checkout AC sync process.',
				[
					'exception'     => $e,
					'message'       => $e->getMessage(),
					'stack_trace'   => $e->getTrace(),
					'connection_id' => $this->connection_id,
				]
			);
		}
	}

	/**
	 * Sets up the order and sends to AC
	 *
	 * @param     order $order     The order object.
	 *
	 * @return int
	 * @throws Exception Does not stop.
	 */
	private function setup_woocommerce_order( $order ) {
		// Setup the woocommerce cart
		$this->wc_session           = $this->wc_session ?: wc()->session;
		$this->external_checkout_id = self::generate_externalcheckoutid(
			$this->wc_session->get_customer_id(),
			$this->customer_email
		);

		try {
			// setup the ecom order
			$this->ecom_order = $this->factory->from_woocommerce( $this->cart, $this->customer_woo );
			$this->logger->debug( 'Checkout meta: external_checkout_id = ' . $this->external_checkout_id );

			// add the data to the order factory
			$this->ecom_order->set_externalcheckoutid( $this->external_checkout_id );
			if ( isset( $this->customer_ac ) && $this->customer_ac->get_id() ) {
				$this->ecom_order->set_customerid( $this->customer_ac->get_id() );
			}

			$date = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
			if ( isset( $this->ecom_order ) ) {
				$this->ecom_order->set_source( '1' );
				$this->ecom_order->set_email( $this->customer->get_email() );
				$this->ecom_order->set_total_price( $this->get_cart_total( $this->cart ) );
				$this->ecom_order->set_currency( get_woocommerce_currency() );
				$this->ecom_order->set_connectionid( $this->connection_id );
				$this->ecom_order->set_order_date( $date->format( DATE_ATOM ) );
				$this->ecom_order->set_order_number( $order->get_order_number() );
				$this->ecom_order->set_externalid( $order->get_order_number() );
				$this->ecom_order->set_order_url( wc_get_cart_url() );
				$this->ecom_order->set_discount_amount( Money::of( $order->get_total_discount(), get_woocommerce_currency() )->getMinorAmount() );
				$this->ecom_order->set_shipping_amount( Money::of( $order->get_shipping_total(), get_woocommerce_currency() )->getMinorAmount() );
				$this->ecom_order->set_shipping_method( $order->get_shipping_method() );
				$this->ecom_order->set_tax_amount( Money::of( $order->get_total_tax(), get_woocommerce_currency() )->getMinorAmount() );
			}
		} catch ( Exception $e ) {
			$this->logger->error(
				'Checkout meta: There was an error creating the order object for AC:',
				[
					'exception'            => $e,
					'message'              => $e->getMessage(),
					'stack_trace'          => $e->getTrace(),
					'email'                => $this->customer->get_email(),
					'external_checkout_id' => $this->external_checkout_id,
					'connection_id'        => $this->connection_id,
				]
			);

			return 0;
		}

		try {
			$products = $this->product_factory->create_products_from_cart_contents( $this->cart->get_cart_contents() );
			// Add products to list
			array_walk( $products, [ $order, 'push_order_product' ] );
		} catch ( Exception $e ) {
			$this->logger->error(
				'Checkout meta: There was an error creating the products objects for AC:',
				[
					'exception'            => $e,
					'message'              => $e->getMessage(),
					'stack_trace'          => $e->getTrace(),
					'email'                => $this->customer->get_email(),
					'external_checkout_id' => $this->external_checkout_id,
					'connection_id'        => $this->connection_id,
				]
			);
		}

		// Let's try to create the order
		$this->order_ac = null;

		try {
			// Try to find the order by it's externalcheckoutid
			$this->order_ac = $this->order_repository->find_by_externalcheckoutid( $this->external_checkout_id );
		} catch ( Exception $e ) {
			$this->logger->debug(
				'Checkout meta: Could not find existing order by this id: ',
				[
					'external_checkout_id' => $this->external_checkout_id,
					'connection_id'        => $this->connection_id,
					'exception'            => $e,
					'message'              => $e->getMessage(),
					'stack_trace'          => $e->getTrace(),
				]
			);
		}

		if ( ! $this->order_ac ) {
			try {
				$this->logger->debug(
					'Checkout meta: Creating order in ActiveCampaign: ',
					[
						'serialized_order' => \AcVendor\GuzzleHttp\json_encode( $this->ecom_order->serialize_to_array() ),
					]
				);

				// Try to create the new order in AC
				$this->order_ac = $this->order_repository->create( $this->ecom_order );

				return 2;
			} catch ( Exception $e ) {
				$this->logger->error(
					'Checkout meta: Could not send/create order in AC: ',
					[
						'exception'            => $e,
						'message'              => $e->getMessage(),
						'stack_trace'          => $e->getTrace(),
						'email'                => $this->customer->get_email(),
						'external_checkout_id' => $this->external_checkout_id,
						'connection_id'        => $this->connection_id,
					]
				);

				return 0;
			}
		} else {
			$this->logger->debug( 'Checkout meta: Valid AC order' );

			return 1;
		}
	}

	/**
	 * Sets up and sends the customer for AC
	 *
	 * @throws GuzzleException Is not fatal.
	 */
	private function setup_woocommerce_customer() {
		// setup_woocommerce_customer
		$this->customer_woo->set_email( $this->customer_email );

		// find_or_create_ac_customer
		$this->customer_ac = null;
		try {
			// Try to find the customer in AC
			$this->customer_ac = $this->customer_repository->find_by_email_and_connection_id( $this->customer_email, $this->connection_id );
		} catch ( Exception $e ) {
			$this->logger->debug(
				'Checkout meta: Could not find existing customer: ',
				[
					'customer_email' => $this->customer_email,
				]
			);
		}

		if ( $this->customer_ac ) {
			return true;
		} else {
			try {
				// Try to create the new customer in AC
				$new_customer = new Ecom_Customer();
				$new_customer->set_email( $this->customer_email );
				$new_customer->set_connectionid( $this->connection_id );
				$new_customer->set_first_name( $this->customer_first_name );
				$new_customer->set_last_name( $this->customer_last_name );

				$this->logger->debug(
					'Checkout meta: Creating customer in ActiveCampaign: '
					. \AcVendor\GuzzleHttp\json_encode( $new_customer->serialize_to_array() )
				);

				$this->customer_ac = $this->customer_repository->create( $new_customer );
			} catch ( Exception $e ) {
				$this->logger->error(
					'Checkout meta: Could not create customer in AC: ',
					[
						'connection_id' => $this->connection_id,
						'email'         => $this->customer_email,
						'exception'     => $e,
						'message'       => $e->getMessage(),
						'stack_trace'   => $e->getTrace(),
					]
				);

				return false;
			}

			if ( $this->customer_ac ) {
				return true;
			} else {
				$this->logger->error(
					'Checkout meta: It appears we could not find or create a contact in AC.',
					[
						'connection_id' => $this->connection_id,
						'email'         => $this->customer_email,
						'first_name'    => $this->customer_first_name,
						'last_name'     => $this->customer_last_name,
					]
				);
				return false;
			}
		}
	}

	/**
	 * Get the cart's total price in cents,
	 * considering whether the global setting indicates that tax should be included.
	 *
	 * @param     WC_Cart $cart     The WC Cart.
	 *
	 * @return int
	 */
	private function get_cart_total( WC_Cart $cart ) {
		$totals = new WC_Cart_Totals( $cart );

		return $totals->get_total( 'total', true );
	}

	/**
	 * Update the existing ecom order in AC
	 *
	 * @return bool Whether or not this job was successful
	 */
	private function update_ac_order() {
		$this->ecom_order->set_id( $this->order_ac->get_id() );

		try {
			$this->order_repository->update( $this->ecom_order );

			return true;
		} catch ( Exception $e ) {
			$this->logger->error(
				'Checkout meta: Could not update the order in AC: ',
				[
					'message'     => $e->getMessage(),
					'stack_trace' => $e->getTrace(),
				]
			);

			return false;
		}
	}

	/**
	 * Generate the externalcheckoutid hash which
	 * is used to tie together pending and complete
	 * orders in Hosted (so we don't create duplicate orders).
	 * This has been modified to accurately work with woo commerce not independently
	 * tracking cart session vs order session
	 *
	 * @param     string $wc_session_hash     The unique WooCommerce cart session ID.
	 * @param     string $billing_email     The guest customer's email address.
	 *
	 * @return string The hash used as the externalcheckoutid value
	 */
	public static function generate_externalcheckoutid( $wc_session_hash, $billing_email ) {
		// Get the custom session if it exists
		$order_external_uuid = wc()->session->get( 'activecampaignfwc_order_external_uuid' );

		// If custom session is not set, create one on the cart
		if ( ! $order_external_uuid || '' === $order_external_uuid ) {
			$order_external_uuid = uniqid( '', true );
			wc()->session->set( 'activecampaignfwc_order_external_uuid', $order_external_uuid );
		}

		// Generate the hash we'll use
		return md5( $wc_session_hash . $billing_email . $order_external_uuid );
	}

	/**
	 * Verifies the status of the order for sending to AC
	 *
	 * @param string $status The order status.
	 *
	 * @return bool Whether or not the order passes.
	 */
	private function verify_order_status( $status ) {
		if ( ! empty( $status ) ) {
			$accepted_statuses = [ 'completed', 'processing' ];

			return in_array( $status, $accepted_statuses, true );
		} else {
			return false;
		}
	}
}
