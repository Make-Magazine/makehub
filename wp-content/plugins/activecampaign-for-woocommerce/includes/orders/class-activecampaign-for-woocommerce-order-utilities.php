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

use Activecampaign_For_Woocommerce_Ecom_Product_Factory as Ecom_Product_Factory;
use Activecampaign_For_Woocommerce_User_Meta_Service as User_Meta_Service;
use Activecampaign_For_Woocommerce_Ecom_Order as Ecom_Order;
use Activecampaign_For_Woocommerce_Logger as Logger;
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
	 * Activecampaign_For_Woocommerce_Order_Utilities constructor.
	 *
	 * @param     Ecom_Product_Factory|null $product_factory     The Ecom Product Factory.
	 * @param     Logger|null               $logger     The Logger.
	 */
	public function __construct(
		Ecom_Product_Factory $product_factory,
		Logger $logger = null
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
	 * @param bool     $historical_sync Sets whether or not this is a historical sync.
	 *
	 * @throws Exception Does not stop.
	 * @return Ecom_Order $ecom_order
	 */
	public function setup_woocommerce_order_from_admin( $order, $historical_sync = false ) {
		// Setup the woocommerce cart
		$this->init();

		try {
			// setup the ecom order
			$ecom_order = new Ecom_Order();

			if ( ! method_exists( $order, 'get_order' ) && ! method_exists( $order, 'get_order_number' ) ) {
				return null;
			}

			/**
			 * Source is a method of sync marker indicating it's historical
			 * or that it's an up to date send of data.
			 * 0 = historical
			 * 1 = webhook
			 */
			if ( $historical_sync ) {
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
			$ecom_order->set_discount_amount( Money::of( wc_format_decimal( $order->get_total_discount(), 2, 0 ), get_woocommerce_currency() )->getMinorAmount() );
			$ecom_order->set_shipping_amount( Money::of( wc_format_decimal( $order->get_shipping_total(), 2, 0 ), get_woocommerce_currency() )->getMinorAmount() );
			$ecom_order->set_shipping_method( $order->get_shipping_method() );
			$ecom_order->set_tax_amount( Money::of( wc_format_decimal( $order->get_total_tax(), 2, 0 ), get_woocommerce_currency() )->getMinorAmount() );
			$ecom_order->set_externalcheckoutid( null );
			$ecom_order->set_total_price( Money::of( wc_format_decimal( $order->get_total(), 2, 0 ), get_woocommerce_currency() )->getMinorAmount() );

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
				$ecom_order->set_total_price( Money::of( wc_format_decimal( $order->get_total(), 2, 0 ), get_woocommerce_currency() )->getMinorAmount() );
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
			if ( method_exists( $order, 'get_items' ) ) {
				// Get and Loop Over Order Items to populate products
				foreach ( $order->get_items() as $item_id => $item ) {
					$product = $this->build_ecom_product( $item, $item_id );
					if ( null !== $product ) {
						$products[ $item_id ] = $product;
					} else {
						$this->logger->warning(
							'Order Utilities: A product passed back as null.',
							[
								'item_id' => $item_id,
								'item'    => $item,
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
	 * @param     object          $item     The product object.
	 * @param     int|string|null $item_id The item ID.
	 *
	 * @return Activecampaign_For_Woocommerce_Ecom_Product
	 */
	private function build_ecom_product( $item, $item_id = null ) {
		try {
			$product_data = null;
			$product      = null;

			if ( ! empty( $item_id ) ) {
				$product_data = $this->get_wc_product_from_id( $item_id );
			}

			if ( ! $product_data instanceof WC_Product && ! empty( $item->get_product_id() ) ) {
				$product_data = $this->get_wc_product_from_id( $item->get_product_id() );
			}

			if ( $product_data instanceof WC_Product ) {
				$product = [
					'data'         => $product_data,
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

				$product = $this->product_factory->product_from_cart_content( $product );
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

		return $product;
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

			if ( ! $product instanceof WC_Product ) {
				$product = WC()->product_factory->get_product( $id );
			}

			if ( ! $product instanceof WC_Product ) {
				$_pf     = new WC_Product_Factory();
				$product = $_pf->get_product( $id );
			}

			if ( $product instanceof WC_Product ) {
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
			if ( method_exists( $product, 'get_id' ) ) {
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
					'product' => method_exists( $product, 'get_data' ) ? $product->get_data() : null,
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
			if ( method_exists( $product, 'get_id' ) ) {
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
								'product_id' => method_exists( $product, 'get_id' ) ? $product->get_id() : null,
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
					'product_id'     => method_exists( $product, 'get_id' ) ? $product->get_id() : null,
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
			if ( $order->get_item_count_refunded() > 0 ) {
				// refunds don't work yet
				$this->logger->debug(
					'Historical sync cannot currently sync refund data. This order will be ignored.',
					[
						'order_id'            => method_exists( $order, 'get_id' ) ? $order->get_id() : null,
						'item_count_refunded' => $order->get_item_count_refunded(),
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
}
