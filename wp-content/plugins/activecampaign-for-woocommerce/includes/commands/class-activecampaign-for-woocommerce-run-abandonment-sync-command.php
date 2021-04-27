<?php

/**
 * The file that runs the abandonment synchronization for abandoned carts.
 *
 * @link       https://www.activecampaign.com/
 * @since      1.3.2
 *
 * @package    Activecampaign_For_Woocommerce
 */

use Activecampaign_For_Woocommerce_Admin as Admin;
use Activecampaign_For_Woocommerce_Ecom_Customer as Ecom_Customer;
use Activecampaign_For_Woocommerce_Ecom_Customer_Repository as Ecom_Customer_Repository;
use Activecampaign_For_Woocommerce_Ecom_Order as Ecom_Order;
use Activecampaign_For_Woocommerce_Ecom_Order_Repository as Ecom_Order_Repository;
use Activecampaign_For_Woocommerce_Logger as Logger;
use Brick\Money\Money;

/**
 * Sync the abandoned carts and their products to ActiveCampaign.
 *
 * @since      1.0.0
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/includes/commands
 * @author     acteamintegrations <team-integrations@activecampaign.com>
 */
class Activecampaign_For_Woocommerce_Run_Abandonment_Sync_Command {

	/**
	 * The logger interface.
	 *
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * The Admin object
	 *
	 * @var Activecampaign_For_Woocommerce_Admin
	 */
	private $admin;

	/**
	 * The Ecom Order Repo
	 *
	 * @var Activecampaign_For_Woocommerce_Ecom_Order_Repository
	 */
	private $order_repository;

	/**
	 * The Ecom Customer Repo
	 *
	 * @var Activecampaign_For_Woocommerce_Ecom_Customer_Repository
	 */
	private $customer_repository;

	/**
	 * Activecampaign_For_Woocommerce_Update_Cart_Command constructor.
	 *
	 * @param     Activecampaign_For_Woocommerce_Admin|null $admin     The admin object.
	 * @param     Logger                                    $logger     The logger interface.
	 * @param     Ecom_Customer_Repository|null             $customer_repository     The Ecom Customer Repo.
	 * @param     Ecom_Order_Repository                     $order_repository     The Ecom Order Repo.
	 */
	public function __construct(
		Admin $admin,
		Logger $logger,
		Ecom_Customer_Repository $customer_repository,
		Ecom_Order_Repository $order_repository
	) {
		$this->admin               = $admin;
		$this->logger              = $logger;
		$this->customer_repository = $customer_repository;
		$this->order_repository    = $order_repository;
	}

	/**
	 * The hourly task that runs via hook
	 * This initializes via Activecampaign_For_Woocommerce_Save_Abandoned_Cart_Command
	 */
	public function abandoned_cart_hourly_task() {
		$this->logger = $this->logger ?: new Logger();

		// Check for abandoned carts
		$abandoned_carts = $this->get_all_abandoned_carts_from_table();
		if ( ! empty( $abandoned_carts ) ) {
			$this->process_abandoned_carts_per_record( $abandoned_carts );
		} else {
			$this->logger->debug( 'Abandoned cart hourly task: No abandoned carts to process...' );
		}
	}


	/**
	 * Get all active carts
	 *
	 * @return mixed Whether or not there are abandoned carts
	 */
	private function get_all_abandoned_carts_from_table() {
		global $wpdb;

		// default is 1 hour abandon cart expiration
		$expire_time = 60 * 60;

		// Get the expire time period from the db
		$activecampaign_for_woocommerce_settings = get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_OPTION_NAME );
		$activecampaign_for_woocommerce_settings = stripslashes_deep( $activecampaign_for_woocommerce_settings );
		if ( isset( $activecampaign_for_woocommerce_settings['abcart_wait'] ) ) {
			$activecampaign_for_woocommerce_abcart_wait = $activecampaign_for_woocommerce_settings['abcart_wait'];
			$expire_time                                = 60 * $activecampaign_for_woocommerce_abcart_wait;
		}

		$now               = new DateTime( 'NOW' );
		$expiration_cutoff = $now->getTimestamp() - $expire_time;
		$expire_datetime   = $now->setTimestamp( $expiration_cutoff );

		// Get the expired carts from our table
		$abandoned_carts = $wpdb->get_results(
			// phpcs:disable
			$wpdb->prepare('SELECT id, customer_ref_json, cart_ref_json, cart_totals_ref_json, removed_cart_contents_ref_json, activecampaignfwc_order_external_uuid 
				FROM
					`' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME . '`
				WHERE
					last_access_time <= %s
					AND synced_to_ac = 0;',
				$expire_datetime->format( 'Y-m-d H:i:s' )
			)
			// phpcs:enable
		);

		if ( $wpdb->last_error ) {
			$this->logger->error( 'Abandonment sync: There was an error getting results for abandoned cart records.', [
				'wpdb_last_error' => $wpdb->last_error,
			] );
		}

		if ( ! empty( $abandoned_carts ) ) {
			// abandoned carts found
			return $abandoned_carts;
		} else {
			// no abandoned carts
			return false;
		}
	}

	/**
	 * Process the abandoned carts per record
	 *
	 * @param Array $abandoned_carts Abandoned carts found in the database.
	 */
	private function process_abandoned_carts_per_record( $abandoned_carts ) {
		// set each cart as though it's the existing active cart
		global $wpdb;

		foreach ( $abandoned_carts as $ab_order ) {
			// parse the values
			$customer                              = json_decode( $ab_order->customer_ref_json );
			$cart                                  = json_decode( $ab_order->cart_ref_json );
			$cart_totals                           = json_decode( $ab_order->cart_totals_ref_json );
			$removed_cart_contents                 = json_decode( $ab_order->removed_cart_contents_ref_json );
			$activecampaignfwc_order_external_uuid = $ab_order->activecampaignfwc_order_external_uuid;
			$customer->activecampaignfwc_order_external_uuid = $activecampaignfwc_order_external_uuid;

			$item_count_total = 0;
			$products         = [];
			// get the products set up
			foreach ( $cart as $product ) {
				try {
					$product_id       = $product->product_id;
					$item_count_total = $item_count_total + $product->quantity;
					$wc_product       = wc_get_product( $product_id );
					$product->data    = $wc_product->get_data();

					// Create ecom product
					$ecom_product = new Activecampaign_For_Woocommerce_Ecom_Product();
					$ecom_product->set_externalid( $wc_product->get_id() );
					$ecom_product->set_name( $wc_product->get_name() );
					$ecom_product->set_price( Money::of( $wc_product->get_price(), get_woocommerce_currency() )->getMinorAmount() );
					$ecom_product->set_description( $wc_product->get_description() );
					$ecom_product->set_category( $this->get_product_category( $wc_product ) );
					$ecom_product->set_image_url( $this->get_product_image_url( $wc_product ) );
					$ecom_product->set_sku( $wc_product->get_sku() );
					$ecom_product->set_quantity( $product->quantity );

					$products[] = $ecom_product;
				} catch ( Exception $e ) {
					$this->logger->error( 'Abandonment Sync: Failed to build the product: ', [
						'exception'         => $e,
						'exception_message' => $e->getMessage(),
						'exception_trace'   => $e->getTrace(),
						'product_id'        => $product_id,
					] );
				}
			}

			try {
				$wc_cart = new WC_Cart();
				$wc_cart->set_cart_contents( $cart );
				$wc_cart->set_removed_cart_contents( $removed_cart_contents );
				$wc_cart->set_totals( $cart_totals );
			} catch ( Exception $e ) {
				$this->logger->error( 'Abandonment Sync: Failed to build the cart: ', [
					'exception'         => $e,
					'exception_message' => $e->getMessage(),
					'exception_trace'   => $e->getTrace(),
					'cart'              => $cart,
				] );
			}

			// Get or register our contact
			$customer_ac = $this->find_or_create_ac_customer( $customer );

			// Step 1: Check if we have customer in AC & create or update
			if ( ! isset( $customer_ac ) || empty( $customer_ac ) ) {
				$this->logger->warning( 'Process single abandon cart: Could not find or create customer...',
					[
						'customer id'         => $customer->id,
						'customer first name' => $customer->first_name,
						'customer last name'  => $customer->last_name,
					]
				);

				return;
			}

			try {
				// Step 2: Let's make the abandoned order for AC
				$order               = new Ecom_Order();
				$date                = new DateTime( 'now' );
				$externalcheckout_id = $this->abandoned_cart_generate_externalcheckoutid( $customer );
				$order->set_externalcheckoutid( $externalcheckout_id );
				$order->set_source( '1' );
				$order->set_email( $customer->email );
				$order->set_currency( get_woocommerce_currency() );
				$order->set_total_price( Money::of( $cart_totals->subtotal, get_woocommerce_currency() )->getMinorAmount() ); // must be in cents
				$order->set_connectionid( $this->admin->get_storage()['connection_id'] );
				$order->set_customerid( $customer_ac->get_id() );
				$order->set_abandoned_date( $date->format( DATE_ATOM ) );
				$order->set_external_created_date( $date->format( DATE_ATOM ) );
				$order->set_order_url( wc_get_cart_url() );
				$order->set_total_products( $item_count_total );

				// Step 3: Add the products to the order
				array_walk( $products, [ $order, 'push_order_product' ] );
			} catch ( Exception $e ) {
				$this->logger->error( 'Abandonment Sync: Failed to build ecom order: ', [
					'exception'         => $e,
					'exception_message' => $e->getMessage(),
					'exception_trace'   => $e->getTrace(),
				] );
			}

			try {
				// Try to find the order by it's externalcheckoutid
				$order_ac = $this->order_repository->find_by_externalcheckoutid( $externalcheckout_id );
			} catch ( Exception $e ) {
				$this->logger->debug( 'Abandonment Sync: Find order in AC exception: ', [
					'exception'           => $e,
					'exception_message'   => $e->getMessage(),
					'exception_trace'     => $e->getTrace(),
					'connection_id'       => $this->admin->get_storage()['connection_id'],
					'customer'            => $customer,
					'customer_ac'         => $customer_ac,
					'externalcheckout_id' => $externalcheckout_id,
				] );
			}

			if ( ! isset( $order_ac ) || null === $order_ac ) {
				// Order does not exist in AC yet
				try {
					// Try to create the new order in AC
					$this->logger->debug( 'Abandonment Sync: Creating order in ActiveCampaign: ', [
						'order_created' => \AcVendor\GuzzleHttp\json_encode( $order->serialize_to_array() ),
					] );

					$order_ac     = $this->order_repository->create( $order );
					$synced_to_ac = true;
				} catch ( Exception $e ) {
					$this->logger->debug( 'Abandonment Sync: Order creation exception: ', [
						'exception'           => $e,
						'exception_message'   => $e->getMessage(),
						'exception_trace'     => $e->getTrace(),
						'connection_id'       => $this->admin->get_storage()['connection_id'],
						'customer'            => $customer,
						'customer_ac'         => $customer_ac,
						'externalcheckout_id' => $externalcheckout_id,
					] );
					$synced_to_ac = false;
				}
			} else {
				$synced_to_ac = true;
				$this->logger->debug( 'Abandonment Sync: Valid AC order, sent to ActiveCampaign' );
			}

			try {
				if ( $synced_to_ac ) {
					// Update the record to show we've synced so we don't sync it again
					$wpdb->update(
						$wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME,
						[
							'synced_to_ac' => 1,
						],
						[
							'id' => $ab_order->id,
						]
					);

					if ( $wpdb->last_error ) {
						$this->logger->error( 'Abandonement sync: There was an error updating an abandoned cart record as synced.', [
							'wpdb_last_error' => $wpdb->last_error,
							'order_id'        => $ab_order->id,
						] );
					}
				}
			} catch ( Exception $e ) {
				$this->logger->error( 'Abandonment Sync: Issue in updating the abandonment record as synced: ', [
					'exception'           => $e,
					'exception_message'   => $e->getMessage(),
					'exception_trace'     => $e->getTrace(),
					'abandoned_order_id'  => $ab_order->id,
					'externalcheckout_id' => $externalcheckout_id,
				] );
			}
		}
	}

	/**
	 * Generate the externalcheckoutid hash which is used to tie together pending and complete.
	 *
	 * @param   WC_Customer $customer  Customer object.
	 *
	 * @return string The hash used as the externalcheckoutid value.
	 */
	private static function abandoned_cart_generate_externalcheckoutid( $customer ) {
		// Get the custom session uuid
		$order_external_uuid = $customer->activecampaignfwc_order_external_uuid;

		// Generate the hash we'll use
		return md5( $customer->id . $customer->billing_email . $order_external_uuid );
	}

	/**
	 * Gets the product category list.
	 *
	 * @param     WC_Product $product The product object.
	 *
	 * @return string|null
	 */
	private function get_product_category( WC_Product $product ) {
		$terms = get_the_terms( $product->get_id(), 'product_cat' );

		if ( ! is_array( $terms ) ) {
			return null;
		}

		$term = array_pop( $terms );
		if ( $term instanceof WP_Term ) {
			return $term->name;
		}

		return null;
	}


	/**
	 * Get the image URL for a given WC Product.
	 *
	 * @param     WC_Product $product     The WC Product.
	 *
	 * @return string|null
	 */
	private function get_product_image_url( WC_Product $product ) {
		$post         = get_post( $product->get_id() );
		$thumbnail_id = get_post_thumbnail_id( $post );
		$image_src    = wp_get_attachment_image_src( $thumbnail_id, 'woocommerce_single' );

		if ( ! is_array( $image_src ) ) {
			return null;
		}

		// The first element is the actual URL
		return $image_src[0];
	}

	/**
	 * Lookup ecom customer record in AC. If it does not exist, create it. This is altered specifically for abandonment.
	 *
	 * @param     WC_Customer $customer     The customer object.
	 *
	 * @return object $customer_ac The customer object from ActiveCampaign.
	 */
	private function find_or_create_ac_customer( $customer ) {
		$customer_ac   = null;
		$connection_id = $this->admin->get_storage()['connection_id'];

		try {
			// Try to find the customer in AC
			$customer_ac = $this->customer_repository->find_by_email_and_connection_id( $customer->email, $connection_id );
		} catch ( Exception $e ) {
			$this->logger->debug( 'Abandon find customer exception: ', [
				'exception'           => $e->getMessage(),
				'exception_trace'     => $e->getTrace(),
				'exception_message'   => $e,
				'customer_email'      => $customer->email,
				'customer_first_name' => $customer->first_name,
				'customer_last_name'  => $customer->last_name,
				'connection_id'       => $connection_id,
			] );
		}

		if ( ! $customer_ac ) {
			try {
				// Customer does not exist in AC yet
				// Set up AC customer model
				$new_customer = new Ecom_Customer();
				$new_customer->set_email( $customer->email );
				$new_customer->set_connectionid( $connection_id );
				$new_customer->set_first_name( $customer->first_name );
				$new_customer->set_last_name( $customer->last_name );

				// Try to create the new customer in AC
				$this->logger->debug(
					'Creating customer in ActiveCampaign: '
					. \AcVendor\GuzzleHttp\json_encode( $new_customer->serialize_to_array() )
				);

				$customer_ac = $this->customer_repository->create( $new_customer );
			} catch ( Exception $e ) {
				$this->logger->debug( 'Abandon customer creation exception: ', [
					'exception'           => $e,
					'exception_trace'     => $e->getTrace(),
					'exception_message'   => $e->getMessage(),
					'customer_email'      => $customer->email,
					'customer_first_name' => $customer->first_name,
					'customer_last_name'  => $customer->last_name,
					'connection_id'       => $connection_id,
				] );
			}
			if ( ! $customer_ac ) {
				$this->logger->debug( 'Invalid AC customer', [
					'customer_email'      => $customer->email,
					'customer_first_name' => $customer->first_name,
					'customer_last_name'  => $customer->last_name,
					'connection_id'       => $connection_id,
				] );
			}
		}

		return $customer_ac;
	}
}
