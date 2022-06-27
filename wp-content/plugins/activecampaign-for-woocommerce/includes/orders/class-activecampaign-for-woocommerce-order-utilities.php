<?php

/**
 * Various order utilities for the Activecampaign_For_Woocommerce plugin.
 *
 * @link       https://www.activecampaign.com/
 * @since      1.5.0
 *
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/includes
 */

use Activecampaign_For_Woocommerce_Ecom_Customer as Ecom_Customer;
use Activecampaign_For_Woocommerce_Ecom_Product_Factory as Ecom_Product_Factory;
use Activecampaign_For_Woocommerce_User_Meta_Service as User_Meta_Service;
use Activecampaign_For_Woocommerce_Ecom_Order as Ecom_Order;
use Activecampaign_For_Woocommerce_Ecom_Product as Ecom_Product;
use Activecampaign_For_Woocommerce_Logger as Logger;
use Activecampaign_For_Woocommerce_Ecom_Customer_Repository as Customer_Repository;
use Activecampaign_For_Woocommerce_Customer_Utilities as Customer_Utilities;
use Brick\Money\Money;

/**
 * The Order Utilities Class.
 *
 * @since      1.5.0
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/includes/orders
 * @author     acteamintegrations <team-integrations@activecampaign.com>
 */
class Activecampaign_For_Woocommerce_Order_Utilities {

	/**
	 * The custom ActiveCampaign logger
	 *
	 * @var Activecampaign_For_Woocommerce_Logger
	 */
	private $logger;

	/**
	 * The custom ActiveCampaign product factory.
	 *
	 * @var Activecampaign_For_Woocommerce_Ecom_Product_Factory
	 */
	private $product_factory;

	/**
	 * The ActiveCampaign connection ID.
	 *
	 * @var connection_id
	 */
	private $connection_id;

	/**
	 * The customer repository.
	 *
	 * @var customer_repository
	 */
	private $customer_repository;

	/**
	 * Activecampaign_For_Woocommerce_Order_Utilities constructor.
	 *
	 * @param     Ecom_Product_Factory|null                               $product_factory     The Ecom Product Factory.
	 * @param     Logger|null                                             $logger     The Logger.
	 * @param     Activecampaign_For_Woocommerce_Ecom_Customer_Repository $customer_repository The customer repository.
	 */
	public function __construct(
		Ecom_Product_Factory $product_factory,
		Logger $logger = null,
		Customer_Repository $customer_repository
	) {
		if ( ! $product_factory ) {
			$this->product_factory = new Ecom_Product_Factory();
		} else {
			$this->product_factory = $product_factory;
		}

		if ( ! $logger ) {
			$this->logger = new Logger();
		} else {
			$this->logger = $logger;
		}

		if ( ! $logger ) {
			$this->customer_repository = new Customer_Repository();
		} else {
			$this->customer_repository = $customer_repository;
		}
	}

	/**
	 * Initialize function.
	 */
	private function init() {
		$admin_storage = get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_STORAGE_NAME );
		if ( ! empty( $admin_storage ) && isset( $admin_storage['connection_id'] ) ) {
			$this->connection_id = $admin_storage['connection_id'];
		}

		if ( ! $this->product_factory ) {
			$this->product_factory = new Ecom_Product_Factory();
		}
	}

	/**
	 * Sets up the order and sends to AC.
	 *
	 * @param     WC_Order $order     The order object.
	 * @param bool     $is_historical Sets whether or not this is a historical sync.
	 *
	 * @throws Exception Does not stop.
	 * @return Ecom_Order $ecom_order
	 */
	public function setup_woocommerce_order_from_admin( $order, $is_historical = false ) {
		if ( ! $this->validate_object( $order, 'get_order' ) || ! $this->validate_object( $order, 'get_order_number' ) ) {
			return null;
		}

		// Setup the woocommerce cart
		$this->init();

		try {
			// setup the ecom order
			$ecom_order = new Ecom_Order();

			/**
			 * Source is a method of sync marker indicating it's historical
			 * or that it's an up to date send of data.
			 * 0 = historical
			 * 1 = webhook/live order
			 */
			if ( $is_historical ) {
				$ecom_order->set_source( '0' );
			} else {
				$ecom_order->set_source( '1' );
			}

			// add the data to the order factory
			$ecom_order->set_connectionid( $this->connection_id );
			$ecom_order->set_currency( get_woocommerce_currency() );
			$ecom_order->set_order_number( $order->get_order_number() );
			$ecom_order->set_externalid( $order->get_id() );
			$ecom_order->set_order_url( $order->get_edit_order_url() );
			$ecom_order->set_discount_amount( $this->convert_money_to_cents( $order->get_total_discount() ) );
			$ecom_order->set_shipping_amount( $this->convert_money_to_cents( $order->get_shipping_total() ) );
			$ecom_order->set_shipping_method( $order->get_shipping_method() );
			$ecom_order->set_tax_amount( $this->convert_money_to_cents( $order->get_total_tax() ) );
			$ecom_order->set_total_price( $this->convert_money_to_cents( $order->get_total() ) );

			// Set the order dates by the time set from WC
			$created_date = new DateTime( $order->get_date_created(), new DateTimeZone( 'UTC' ) );
			$ecom_order->set_order_date( $created_date->format( DATE_ATOM ) );
			$ecom_order->set_external_created_date( $created_date->format( DATE_ATOM ) );
			$modified_date = new DateTime( $order->get_date_modified(), new DateTimeZone( 'UTC' ) );
			$ecom_order->set_external_updated_date( $modified_date->format( DATE_ATOM ) );

			if ( $order->get_user_id() ) {
				// Set if the AC id is set
				$ecom_order->set_id( User_Meta_Service::get_current_cart_ac_id( $order->get_user_id() ) );
			} elseif ( $order->get_customer_id() ) {
				$ecom_order->set_id( User_Meta_Service::get_current_cart_ac_id( $order->get_customer_id() ) );
			}

			if ( empty( $ecom_order->get_total_price() ) ) {
				$order->calculate_totals();
				$ecom_order->set_total_price( $this->convert_money_to_cents( $order->get_total() ) );
			}

			return $ecom_order;
		} catch ( Throwable $t ) {
			$this->logger->error(
				'Activecampaign_For_Woocommerce_Order_Utilities: There was an error creating the order object for AC:',
				[
					'message'     => $t->getMessage(),
					'stack_trace' => $t->getTrace(),
				]
			);

			return null;
		}
	}

	/**
	 * Build products from the order object.
	 *
	 * @param     WC_Order                                  $order The WC order.
	 * @param     Activecampaign_For_Woocommerce_Ecom_Order $ecom_order The AC order.
	 *
	 * @return Activecampaign_For_Woocommerce_Ecom_Order|null
	 */
	public function build_products_for_order( $order, Ecom_Order $ecom_order ) {
		try {
			// There is no cart object, build the products and add to the order
			$products = [];

			if ( $this->validate_object( $order, 'get_items' ) ) {
				// Get and Loop Over Order Items to populate products
				foreach ( $order->get_items() as $item_id => $item ) {
					$product = $this->build_ecom_product( $item, $item_id );

					if ( $this->validate_object( $product, 'get_id' ) ) {
						$products[ $item_id ] = $product;
					} else {
						$this->logger->warning(
							'Order Utilities: The ecom product could not be set.',
							[
								'item_id'      => $item_id,
								'item'         => $item,
								'product'      => $product,
								'product_type' => typeOf( $product ),
							]
						);
					}
				}
			}

			if ( count( $products ) > 0 ) {
				// Add products to list
				array_walk( $products, [ $ecom_order, 'push_order_product' ] );
			}

			return $ecom_order;
		} catch ( Throwable $t ) {
			$this->logger->error(
				'Order_Utilities: There was an error creating the products objects for AC',
				[
					'message'       => $t->getMessage(),
					'stack_trace'   => $t->getTrace(),
					'connection_id' => $this->connection_id,
				]
			);
		}

		return null;
	}

	/**
	 * Updates the last synced date on a record.
	 *
	 * @param int $order_id The order ID from the order.
	 *
	 * @throws Exception Does not stop.
	 */
	public function update_last_synced( $order_id ) {
		$date = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
		try {
			update_post_meta( $order_id, 'activecampaign_for_woocommerce_last_synced', $date->format( 'Y-m-d H:i:s e' ) );
		} catch ( Throwable $t ) {
			$this->logger->warning(
				'Activecampaign_For_Woocommerce_Order_Finished_Event: Could not create datetime or save sync metadata to the order',
				[
					'message'  => $t->getMessage(),
					'date'     => $date->format( 'Y-m-d H:i:s e' ),
					'order_id' => $order_id,
					'trace'    => $this->logger->clean_trace( $t->getTrace() ),
				]
			);
		}
	}

	/**
	 * Stores the ActiveCampaign Hosted record ID for the order record.
	 *
	 * @param string $order_id The order ID.
	 * @param string $ac_id The record ID from Hosted.
	 */
	public function store_ac_id( $order_id, $ac_id ) {
		add_post_meta( $order_id, 'activecampaign_for_woocommerce_hosted_id', $ac_id, true );
	}

	/**
	 * Builds an ecom product from the item passed in.
	 *
	 * @param     object          $item     The order/cart product object. This is different than a WC_Product.
	 * @param     int|string|null $item_id The item/product ID.
	 *
	 * @return Activecampaign_For_Woocommerce_Ecom_Product
	 */
	public function build_ecom_product( $item, $item_id = null ) {
		try {
			$product_data = null;

			if ( ! empty( $item_id ) ) {
				$product_data = $this->get_wc_product_from_id( $item_id );
			}

			if (
				$this->validate_object( $item, 'get_product_id' ) &&
				! $this->validate_object( $product_data, 'get_id' )
			) {
				$product_data = $this->get_wc_product_from_id( $item->get_product_id() );
			}

			$pre_product = [
				'data'         => $product_data,
				'item'         => $item,
				'product_id'   => $item->get_product_id(),
				'variation_id' => $item->get_variation_id(),
				'product'      => $item->get_data_store(),
				'name'         => $item->get_name(),
				'quantity'     => $item->get_quantity(),
				'subtotal'     => $item->get_subtotal(),
				'total'        => $item->get_total(),
				'tax'          => $item->get_subtotal_tax(),
				'taxclass'     => $item->get_tax_class(),
				'taxstat'      => $item->get_tax_status(),
				'allmeta'      => $item->get_meta_data(),
				'somemeta'     => $item->get_meta( '_whatever', true ),
				'type'         => $item->get_type(),
			];

			$product = $this->product_factory->product_from_cart_content( $pre_product );

			if ( isset( $product ) ) {
				return $product;
			}
		} catch ( Throwable $t ) {
			$this->logger->error(
				'Activecampaign_For_Woocommerce_Order_Utilities: There was an error building an ecom product for the order.',
				[
					'message'     => $t->getMessage(),
					'item'        => $item,
					'stack_trace' => $this->logger->clean_trace( $t->getTrace() ),
				]
			);

			return null;
		}

		return null;
	}

	/**
	 * Tries every method to get a product from the product ID.
	 *
	 * @param string|int $id The product id.
	 *
	 * @return bool|false|WC_Product|null
	 */
	public function get_wc_product_from_id( $id ) {
		$logger = new Logger();
		try {
			if ( is_string( $id ) && is_numeric( $id ) ) {
				$id = (int) $id;
			}

			$product = wc_get_product( $id );

			if ( ! $this->validate_object( $product, 'get_id' ) || empty( $product->get_id() ) ) {
				$product = WC()->product_factory->get_product( $id );
			}

			if ( ! $this->validate_object( $product, 'get_id' ) || empty( $product->get_id() ) ) {
				$_pf     = new WC_Product_Factory();
				$product = $_pf->get_product( $id );
			}

			if ( $this->validate_object( $product, 'get_id' ) && ! empty( $product->get_id() ) ) {
				return $product;
			}
		} catch ( Throwable $t ) {
			$logger->warning(
				'There was an error getting WC_Product from id.',
				[
					'id'             => $id,
					'thrown_message' => $t->getMessage(),
					'trace'          => $logger->clean_trace( $t->getTrace() ),
				]
			);
		}

		return null;
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
	 * Get the image URL for a given WC Product.
	 *
	 * @param     WC_Product $product     The WC Product.
	 *
	 * @return string|null
	 */
	public function get_product_image_url( $product ) {
		try {
			if ( $this->validate_object( $product, 'get_id' ) ) {
				$post         = get_post( $product->get_id() );
				$thumbnail_id = get_post_thumbnail_id( $post );
				$image_src    = wp_get_attachment_image_src( $thumbnail_id, 'woocommerce_single' );

				if ( ! is_array( $image_src ) ) {
					return null;
				}

				// The first element is the actual URL
				return $image_src[0];
			}
		} catch ( Throwable $t ) {
			$logger = new Logger();
			$logger->warning(
				'Could not retrieve product image URL',
				[
					'message' => $t->getMessage(),
					'product' => $this->validate_object( $product, 'get_data' ) ? $product->get_data() : null,
				]
			);
		}

		return null;
	}

	/**
	 * Parse the results of the all of a product's categories and return all as separated list
	 *
	 * @param WC_Product $product The WC Product.
	 *
	 * @return string|null
	 */
	public function get_product_category( $product ) {
		$logger = new Logger();
		try {
			if ( $this->validate_object( $product, 'get_id' ) ) {
				$terms = get_the_terms( $product->get_id(), 'product_cat' );
			}
		} catch ( Throwable $t ) {
			$logger->warning(
				'Could not get the terms/categories for a product.',
				[
					'message' => $t->getMessage(),
					'product' => $product,
				]
			);
		}

		$cat_list = [];
		try {
			// go through the categories and make a named list
			if ( ! empty( $terms ) && is_array( $terms ) ) {
				foreach ( $terms as $term ) {
					$product_cat_id   = $term->term_id;
					$product_cat_name = $term->name;
					if ( $product_cat_id >= 0 && ! empty( $product_cat_name ) ) {
						$cat_list[] = $product_cat_name;
					} else {
						$logger->warning(
							'A product category attached to this product does not have a valid category and/or name.',
							[
								'product_id' => $this->validate_object( $product, 'get_id' ) ? $product->get_id() : null,
								'term_id'    => $term->term_id,
								'term_name'  => $term->name,
							]
						);
					}
				}
			}
		} catch ( Throwable $t ) {
			$logger->warning(
				'There was an error getting all product categories.',
				[
					'terms'          => $terms,
					'product_id'     => $this->validate_object( $product, 'get_id' ) ? $product->get_id() : null,
					'trace'          => $logger->clean_trace( $t->getTrace() ),
					'thrown_message' => $t->getMessage(),
				]
			);
		}

		if ( ! empty( $cat_list ) ) {
			// Convert to a comma separated string
			return implode( ', ', $cat_list );
		}

		return null;
	}

	/**
	 * Checks if the order contains a refund.
	 *
	 * @param string|WC_Order|object $order The order object.
	 *
	 * @return bool
	 */
	public function is_refund_order( $order ) {
		try {
			if ( $this->validate_object( $order, 'get_item_count_refunded' ) && $order->get_item_count_refunded() > 0 ) {
				// refunds don't work yet
				$this->logger->debug(
					'Historical sync cannot currently sync refund data. This order will be ignored.',
					[
						'order_id'            => $this->validate_object( $order, 'get_id' ) ? $order->get_id() : null,
						'item_count_refunded' => $this->validate_object( $order, 'get_item_count_refunded' ) ? $order->get_item_count_refunded() : null,
					]
				);

				return true;
			}
		} catch ( Throwable $t ) {
			$this->logger->error(
				'Historical sync had an error processing a refund order.',
				[
					'message'     => $t->getMessage(),
					'stack_trace' => $this->logger->clean_trace( $t->getTrace() ),
				]
			);

			return true;
		}

		return false;
	}

	/**
	 * Gets the order by ID.
	 *
	 * @param string|int $order_id The order ID.
	 *
	 * @return array|bool|WC_Order|WC_Order_Refund|null
	 */
	public function get_order_by_id( $order_id ) {
		$wc_order = wc_get_order( $order_id );

		if ( $this->validate_object( $wc_order, 'get_id' ) && $wc_order->get_id() ) {
			return $wc_order;
		}
	}

	/**
	 * Make an ecom product from the wc product object.
	 *
	 * @param WC_Product $product The product object.
	 * @param     string     $format The format to send this back.
	 *
	 * @return Activecampaign_For_Woocommerce_Ecom_Product|array
	 */
	public function make_ecom_product_from_wc_product( $product, $format = 'array' ) {
		$wc_product = wc_get_product( $product );

		$categories = [];
		foreach ( $wc_product['categories'] as $cat ) {
			$categories[] = $cat['name'];
		}

		if ( ! empty( $wc_product['short_description'] ) ) {
			$description = $this->clean_description( $wc_product['short_description'] );
		} elseif ( ! empty( $wc_product['description'] ) ) {
			$description = $this->clean_description( $wc_product['description'] );
		} else {
			$description = '';
		}

		$imgurl = '';

		if ( isset( $wc_product['images'][0] ) && ! empty( $wc_product['images'][0]['src'] ) ) {
			$imgurl = $wc_product['images'][0]['src'];
		}

		$ecom_product_array = [
			'category'    => implode( ', ', $categories ),
			'externalid'  => $product['product_id'],
			'name'        => $product['name'],
			'price'       => $product['price'],
			'quantity'    => $product['quantity'],
			'description' => $description,
			'productUrl'  => $wc_product['permalink'],
			'sku'         => $product['sku'],
			'imageUrl'    => $imgurl,
		];

		if ( 'array' === $format ) {
			return $ecom_product_array;
		}

		if ( 'object' === $format ) {
			$ecom_product = new Ecom_Product();
			return $ecom_product->set_properties_from_serialized_array( $ecom_product );
		}
	}

	/**
	 * Get orders from the unsynced data in the table.
	 *
	 * @param object $data The data which should be a set of orders from the table.
	 *
	 * @return array
	 */
	public function get_orders_from_unsynced_data( $data ) {
		$wc_orders = [];

		if ( count( $data ) > 0 ) {
			foreach ( $data as $line ) {
				try {
					$order = wc_get_order( $line->wc_order_id );
					if ( $this->verify_order_status( $order->get_status() ) ) {
						$wc_orders[] = $order;
					}
				} catch ( Throwable $t ) {
					$this->logger->error(
						'There was an issue with collecting the unsynced order.',
						[
							'line'    => $line,
							'message' => $t->getMessage(),
						]
					);
				}
			}
		}

		return $wc_orders;
	}

	/**
	 * Convert money to cents
	 *
	 * @param string|int $amount The currency amount.
	 *
	 * @return \Brick\Math\BigDecimal|int
	 */
	public function convert_money_to_cents( $amount ) {
		$cents    = 0;
		$currency = get_woocommerce_currency();

		try {
			if ( ! empty( $currency ) && ! empty( $amount ) ) {
				// round to 2 decimals and convert to minor "cents"
				$amount = number_format( $amount, 2, '.', '' );
				$cents  = Money::of( $amount, $currency )->getMinorAmount()->toInt();
			}
		} catch ( Throwable $t ) {
			$this->logger->error(
				'There was an issue converting money to cents.',
				[
					'message'       => $t->getMessage(),
					'amount_passed' => $amount,
					'amount_cents'  => $cents,
					'stack_trace'   => $this->logger->clean_trace( $t->getTrace() ),
				]
			);

			if ( is_numeric( $cents ) ) {
				$cents = $amount;
			} else {
				return 0;
			}
		}

		return $cents;
	}

	/**
	 * Builds the order url.
	 *
	 * @param array|WC_Order $order The order object.
	 *
	 * @return string
	 */
	public function build_order_url( $order ) {
		if ( ! empty( $order['id'] ) && ! empty( $order['order_key'] ) ) {
			return get_site_url( null, '/checkout/order-received/' . $order['id'] . '?key=' . $order['order_key'] );
		}

		if ( ! empty( $order['id'] ) ) {
			$wc_order = wc_get_order( $order['id'] );
			return $wc_order->get_checkout_order_received_url();
		}

		return get_site_url();
	}

	/**
	 * Get the accepts marketing value.
	 *
	 * @param array|WC_Order $order The order object.
	 *
	 * @return int
	 */
	public function get_accepts_marketing( $order ) {
		$accepts_marketing = 0;

		try {
			if ( is_array( $order ) && isset( $order['id'] ) ) {
				$meta_value = get_post_meta( $order['id'], 'activecampaign_for_woocommerce_accepts_marketing' );
			} elseif ( $this->validate_object( $order, 'get_id' ) ) {
				$meta_value = get_post_meta( $order->get_id(), 'activecampaign_for_woocommerce_accepts_marketing' );
			}

			if ( isset( $meta_value[0] ) ) {
				$accepts_marketing = $meta_value[0];
			}
		} catch ( Throwable $t ) {
			$this->logger->warning(
				'Order Utilities: Failed to get accepts marketing.',
				[
					'message' => $t->getMessage(),
				]
			);
		}

		return $accepts_marketing;
	}

	/**
	 * Cleans a description field by removing tags and shortening the number of words to a max amount.
	 *
	 * @param string $description The description.
	 *
	 * @return string
	 */
	public function clean_description( $description ) {
		$logger = new Logger();

		try {
			$plain_description = str_replace( array( "\r", "\n", '&nbsp;' ), ' ', $description );
			$plain_description = trim( wp_strip_all_tags( $plain_description, false ) );
			$plain_description = preg_replace( '/\s+/', ' ', $plain_description );
			$wrap_description  = wordwrap( $plain_description, 300 );
			$description_arr   = explode( "\n", $wrap_description );
			if ( isset( $description_arr[0] ) ) {
				$fin_description = $description_arr[0] . '...';
			}
		} catch ( Throwable $t ) {
			$logger->warning(
				'There was an issue cleaning the description field.',
				[
					'message'     => $t->getMessage(),
					'description' => $description,
				]
			);
		}

		if ( ! empty( $fin_description ) ) {
			return $fin_description;
		}

		if ( ! empty( $plain_description ) ) {
			return $plain_description;
		}

		return $description;
	}



	/**
	 * Verifies the status of the order for sending to AC
	 *
	 * @param     string $status     The order status.
	 *
	 * @return bool Whether or not the order passes.
	 */
	public function verify_order_status( $status ) {
		if ( ! empty( $status ) ) {
			$accepted_statuses = [ 'completed', 'processing', 'wc-completed', 'wc-processing' ];

			return in_array( $status, $accepted_statuses, true );
		}

		return false;
	}
	/**
	 * Update the existing ecom order in AC
	 *
	 * @return bool Whether or not this job was successful
	 */
	public function update_ac_order() {
		$this->ecom_order->set_id( $this->order_ac->get_id() );

		try {
			if ( ! $this->order_repository->update( $this->ecom_order ) ) {
				$this->logger->error(
					'Could not update the order in AC: ',
					[
						'order_array' => $this->ecom_order->serialize_to_array(),
					]
				);
			}

			return true;
		} catch ( Throwable $t ) {
			$this->logger->error(
				'Activecampaign_For_Woocommerce_Order_Finished_Event: Could not update the order in AC',
				[
					'message'     => $t->getMessage(),
					'stack_trace' => $this->logger->clean_trace( $t->getTrace() ),
				]
			);

			return false;
		}
	}

	/**
	 * Send the table data to the database
	 *
	 * @param Array       $data The data.
	 * @param null|string $stored_id The stored id of the customer.
	 */
	public function store_order_data( $data, $stored_id = null ) {
		global $wpdb;
		try {
			if ( ! is_null( $stored_id ) ) {
				$wpdb->update(
					$wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME,
					$data,
					[
						'id' => $stored_id,
					]
				);

			} else {
				$wpdb->insert(
					$wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME,
					$data
				);
			}

			if ( $wpdb->last_error ) {
				$this->logger->error(
					'There was an error creating/updating an abandoned cart record.',
					[
						'wpdb_last_error' => $wpdb->last_error,
						'data'            => $data,
						'stored_id'       => $stored_id,
					]
				);
			}
		} catch ( Throwable $t ) {
			$this->logger->error(
				'There was an error attempting to save this abandoned cart.',
				[
					'message'       => $t->getMessage(),
					'stored_id'     => $stored_id,
					'customer_data' => $data,
					'trace'         => $this->logger->clean_trace( $t->getTrace() ),
				]
			);
		}
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
	public function serialize_ecom_order_for_bulksync( Ecom_Order $order ) {
		$logger = new Logger();
		try {
			return (object) [
				'ecomOrder' => $order->serialize_to_array(),
			];
		} catch ( Throwable $t ) {
			$logger->debug(
				'Could not serialize an ecom order to bulksync.',
				[
					'message' => $t->getMessage(),
					'order'   => $order,
				]
			);
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
	public function get_wc_order( $order ) {
		if ( $this->validate_object( $order, 'get_id' ) && ! empty( $order->get_id() ) ) {

			return $order;
		}

		// If it's not a valid WC_Order, try using it as a non WC object.
		if ( is_object( $order ) ) {
			try {
				$wc_order = wc_get_order( $order );

				if ( $this->validate_object( $wc_order, 'get_id' ) && ! empty( $wc_order->get_id() ) ) {
					return $wc_order;
				}

				$wc_order = wc_get_order( $order->get_id() );

				if ( $this->validate_object( $wc_order, 'get_id' ) && ! empty( $wc_order->get_id() ) ) {
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

				if ( $this->validate_object( $wc_order, 'get_id' ) && ! empty( $wc_order->get_id() ) ) {
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

			if ( $this->validate_object( $wc_order, 'get_id' ) && ! empty( $wc_order->get_id() ) ) {
				return $wc_order;
			}
		} catch ( Throwable $t ) {
			$this->logger->debug(
				'Historical Sync: A final WC_Order object failed to retrieve.',
				[
					'message' => $t->getMessage(),
					'order'   => $this->validate_object( $wc_order, 'get_data' ) ? $wc_order->get_data() : null,
				]
			);
		}

		try {
			if ( $this->validate_object( $order, 'get_id' ) ) {
				$wc_order = new WC_Order( $order->get_id() );
			} elseif ( isset( $order['id'] ) ) {
				$wc_order = new WC_Order( $order['id'] );
			} else {
				$wc_order = new WC_Order( $order );
			}

			if ( $this->validate_object( $wc_order, 'get_id' ) && ! empty( $wc_order->get_id() ) ) {
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
	 * This builds the ecom order object.
	 *
	 * @param     Activecampaign_For_Woocommerce_Ecom_Customer $ecom_customer     The ecom customer object.
	 * @param     WC_Order                                     $order     The WC order object.
	 * @param     int|string                                   $source The source designation.
	 *
	 * @return Activecampaign_For_Woocommerce_Ecom_Order|bool|null
	 */
	public function build_ecom_order( Ecom_Customer $ecom_customer, $order, $source ) {
		try {
				$customer_utilities = new Customer_Utilities();
				$ecom_order         = $this->setup_woocommerce_order_from_admin( $order, $source );

			if ( null === $ecom_order ) {
				return null;
			}

			$ecom_order = $customer_utilities->add_customer_to_order( $order, $ecom_order );
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
			if ( $this->validate_object( $ecom_order, 'get_order_number' ) && ! empty( $ecom_order->get_order_number() ) && $ecom_order->get_externalid() ) {
				$ecom_order->set_connectionid( $this->connection_id );
				$ecom_order->set_email( $ecom_customer->get_email() );

				// Return the order with products
				return $this->build_products_for_order( $order, $ecom_order );
			}
		} catch ( Throwable $t ) {
			$this->logger->error(
				'Historical sync failed to format an ecommerce order object.',
				[
					'message'      => $t->getMessage(),
					'order_number' => $this->validate_object( $ecom_order, 'get_order_number' ) ? $ecom_order->get_order_number() : null,
					'order_id'     => $this->validate_object( $ecom_order, 'get_externalid' ) ? $ecom_order->get_externalid() : null,
				]
			);
			return null;
		}

		return $ecom_order;
	}

	/**
	 * This schedules the recurring event and verifies it's still set up
	 */
	public function schedule_recurring_order_sync_task() {
		// If not scheduled, set up our recurring event
		$logger = new Logger();

		try {
			// wp_clear_scheduled_hook('activecampaign_for_woocommerce_cart_updated_recurring_event');
			if ( ! wp_next_scheduled( 'activecampaign_for_woocommerce_run_order_sync' ) ) {
				wp_schedule_event( time(), 'every_minute', 'activecampaign_for_woocommerce_run_order_sync' );
			} elseif ( function_exists( 'wp_get_scheduled_event' ) ) {
				$logger->debug(
					'Recurring order sync already scheduled',
					[
						'time_now' => time(),
						'myevent'  => wp_get_scheduled_event( 'activecampaign_for_woocommerce_run_order_sync' ),
					]
				);

			}
		} catch ( Throwable $t ) {
			$logger->debug(
				'There was an issue scheduling the order sync event.',
				[
					'message' => $t->getMessage(),
				]
			);
		}
	}

	/**
	 * Gets the externalcheckoutid from our unsynced table.
	 *
	 * @param string|int $order_id The order ID.
	 *
	 * @return string|null
	 */
	public function get_externalcheckoutid_from_table_by_orderid( $order_id ) {
		global $wpdb;
		$logger = new Logger();
		try {
			// Get the expired carts from our table
			$ac_externalcheckoutid = $wpdb->get_var(
			// phpcs:disable
				$wpdb->prepare( 'SELECT ac_externalcheckoutid 
					FROM
						`' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME . '`
					WHERE
						abandoned_date IS NOT NULL
						AND wc_order_id = %s LIMIT 1',
					$order_id
				)
			// phpcs:enable
			);

			if ( $wpdb->last_error ) {
				$logger->error(
					'Abandonment sync: There was an error getting results for abandoned cart records.',
					[
						'wpdb_last_error' => $wpdb->last_error,
					]
				);
			}

			if ( ! empty( $ac_externalcheckoutid ) ) {
				// abandoned carts found
				return $ac_externalcheckoutid;
			}
		} catch ( Throwable $t ) {
			$logger->error(
				'Abandonment Sync: There was an error with preparing or getting abandoned cart results.',
				[
					'message' => $t->getMessage(),
					'trace'   => $logger->clean_trace( $t->getTrace() ),
				]
			);
		}

		return null;
	}

	/**
	 * Gets the AC order ID from the unsynced table.
	 *
	 * @param string|int $order_id The order id.
	 *
	 * @return string|null
	 */
	public function get_ac_orderid_from_wc_order( $order_id ) {
		global $wpdb;
		$logger = new Logger();
		try {
			// Get the expired carts from our table
			$ac_order_id = $wpdb->get_var(
			// phpcs:disable
				$wpdb->prepare( 'SELECT ac_order_id 
					FROM
						`' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME . '`
					WHERE wc_order_id = %s LIMIT 1',
					$order_id
				)
			// phpcs:enable
			);

			if ( $wpdb->last_error ) {
				$logger->error(
					'Abandonment sync: There was an error getting results for abandoned cart records.',
					[
						'wpdb_last_error' => $wpdb->last_error,
					]
				);
			}

			if ( ! empty( $ac_order_id ) ) {
				// abandoned carts found
				return $ac_order_id;
			}
		} catch ( Throwable $t ) {
			$logger->error(
				'Abandonment Sync: There was an error with preparing or getting abandoned cart results.',
				[
					'message' => $t->getMessage(),
					'trace'   => $logger->clean_trace( $t->getTrace() ),
				]
			);
		}

		return null;
	}

	/**
	 * Validates an object with isset check and method_exists check in one call.
	 *
	 * @param object $o The string|object.
	 * @param string $s The string for the call.
	 *
	 * @return bool
	 */
	public function validate_object( $o, $s ) {
		if (
			isset( $o ) &&
			( is_object( $o ) || is_string( $o ) ) &&
			method_exists( $o, $s )
		) {
			return true;
		}
		return false;
	}
}
