<?php

/**
 * The file that defines the Abandoned Cart Functions.
 *
 * @link       https://www.activecampaign.com/
 * @since      1.x
 *
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/includes/abandoned_carts
 */

use Activecampaign_For_Woocommerce_Customer_Utilities as Customer_Utilities;
use Activecampaign_For_Woocommerce_Logger as Logger;

/**
 * The Order_Finished Event Class.
 *
 * @since      1.0.0
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/includes/events
 * @author     acteamintegrations <team-integrations@activecampaign.com>
 */
class Activecampaign_For_Woocommerce_Abandoned_Cart_Utilities {

	/**
	 * Get an abandoned cart by row id.
	 *
	 * @param int $id The row id.
	 *
	 * @return array|bool|object|null
	 */
	public function get_abandoned_cart_by_row_id( $id ) {
		global $wpdb;
		$logger = new Logger();
		try {
			// Get the expired carts from our table
			$abandoned_cart = $wpdb->get_results(
			// phpcs:disable
				$wpdb->prepare( 'SELECT id, customer_ref_json, cart_ref_json, cart_totals_ref_json, removed_cart_contents_ref_json, activecampaignfwc_order_external_uuid 
					FROM
						`' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME . '`
					WHERE
						id = %s;',
					$id
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

			if ( ! empty( $abandoned_cart ) ) {
				// abandoned carts found
				return $abandoned_cart;
			} else {
				// no abandoned carts
				return false;
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
	}

	/**
	 * Checks for an existing order using our metadata on orders
	 *
	 * @param string $externalcheckout_id The external checkoutid.
	 *
	 * @return string|bool
	 */
	public function find_existing_wc_order( $externalcheckout_id ) {
		global $wpdb;

		if ( ! empty( $externalcheckout_id ) ) {
			$wc_post_id = $wpdb->get_var(
				// phpcs:disable
				$wpdb->prepare(
					'SELECT post_id
					FROM
						`' . $wpdb->prefix . 'postmeta`
					WHERE
						(meta_key = %s OR meta_key = %s OR meta_key = %s) AND meta_value = %s;',
					'activecampaign_for_woocommerce_externalcheckoutid',
					'activecampaign_for_woocommerce_external_checkout_id',
					'activecampaign_for_woocommerce_persistent_cart_id',
					$externalcheckout_id
				)
				// phpcs:enable
			);
			if ( ! empty( $wc_post_id ) ) {
				return $wc_post_id;
			}
		}

		return false;
	}

	/**
	 * Delete an abandoned cart by order object.
	 *
	 * @param     WC_Order|null $order The order object.
	 *
	 * @return bool
	 */
	public function delete_abandoned_cart_by_order( $order = null ) {
		$customer_util = new Customer_Utilities();
		$logger        = new Logger();
		if ( isset( $order ) && ! empty( $order ) ) {
			$customer_id = $customer_util->get_customer_id( $order );
		}

		if ( empty( $customer_id ) ) {
			$logger->error(
				'Abandoned Cart: Could not delete the abandoned cart. No valid order passed or no customer ID provided.',
				[
					'passed data' => $order,
					'customer_id' => $customer_id,
				]
			);

			return false;
		}

		$this->delete_abandoned_cart_by_filter( 'customer_id', $customer_id );
		return true;
	}

	/**
	 * Delete an abandoned cart by customer.
	 *
	 * @return bool
	 */
	public function delete_abandoned_cart_by_customer() {
		$customer_util = new Customer_Utilities();
		$customer_id   = $customer_util->get_customer_id();
		$logger        = new Logger();

		if ( empty( $customer_id ) ) {
			$logger->error(
				'Abandoned Cart: Could not delete the abandoned cart. No valid order passed or no customer ID found.',
				[
					'customer_id' => $customer_id,
				]
			);

			return false;
		}

		$this->delete_abandoned_cart_by_filter( 'customer_id', $customer_id );
		return true;
	}

	/**
	 * Deletes an abandoned cart record based on a filter value pair.
	 *
	 * @param string $filter_name The filter column name to use.
	 * @param string $filter_value The data passed to perform the deletion.
	 *
	 * @return bool
	 */
	public function delete_abandoned_cart_by_filter( $filter_name, $filter_value ) {
		global $wpdb;
		$logger = new Logger();

		if (
			! isset( $filter_name, $filter_value ) ||
			empty( $filter_name ) ||
			empty( $filter_value )
		) {
			$logger->error(
				'Abandoned Cart: Deletion name or value was not set.',
				[
					$filter_name => $filter_value,
				]
			);

			return false;
		}

		try {
			$table_name = $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME;

			$wpdb->delete(
				$table_name,
				[
					$filter_name => $filter_value,
				]
			);

			if ( ! empty( $wpdb->last_error ) ) {
				$logger->error(
					'Abandoned cart: There was an error removing the abandoned cart record.',
					[
						$filter_name      => $filter_value,
						'wpdb_last_error' => $wpdb->last_error,
					]
				);

				return false;
			}

			return true;

		} catch ( Throwable $t ) {
			$logger->warning(
				'Abandoned cart: could not delete the abandoned cart entry.',
				[
					'message'  => $t->getMessage(),
					'session'  => method_exists( wc()->session, 'get_session_data' ) ? wc()->session->get_session_data() : null,
					'customer' => method_exists( wc()->customer, 'get_data' ) ? wc()->customer->get_data() : null,
					'trace'    => $logger->clean_trace( $t->getTrace() ),
				]
			);

			return false;
		}
	}

	/**
	 * This schedules the recurring event and verifies it's still set up
	 */
	public function schedule_recurring_abandon_cart_task() {
		// If not scheduled, set up our recurring event
		$logger = new Logger();

		try {
			if ( ! wp_next_scheduled( 'activecampaign_for_woocommerce_cart_updated_recurring_event' ) ) {
				wp_schedule_event( time(), 'hourly', 'activecampaign_for_woocommerce_cart_updated_recurring_event' );
			} else {
				if ( function_exists( 'wp_get_scheduled_event' ) ) {
					$logger->debug(
						'Recurring cron already scheduled',
						[
							'time_now' => time(),
							'myevent'  => wp_get_scheduled_event( 'activecampaign_for_woocommerce_cart_updated_recurring_event' ),
						]
					);
				}
			}
		} catch ( Throwable $t ) {
			$logger->debug(
				'There was an issue scheduling the abandoned cart event.',
				[
					'message' => $t->getMessage(),
				]
			);
		}
	}

	/**
	 * Send the table data to the database
	 *
	 * @param   Array       $data The data.
	 * @param     null|string $stored_id The stored id of the customer.
	 */
	public function store_abandoned_cart_data( $data, $stored_id = null ) {
		global $wpdb;
		$logger = new Logger();
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
				$logger->error(
					'Abandoned cart: There was an error creating/updating an abandoned cart record.',
					[
						'wpdb_last_error' => $wpdb->last_error,
						'data'            => $data,
						'stored_id'       => $stored_id,
					]
				);
			}
		} catch ( Throwable $t ) {
			$logger->error(
				'Abandoned cart: There was an error attempting to save this abandoned cart',
				[
					'message'       => $t->getMessage(),
					'stored_id'     => $stored_id,
					'customer_data' => $data,
					'trace'         => $logger->clean_trace( $t->getTrace() ),
				]
			);
		}
	}

	/**
	 * Generate the externalcheckoutid hash which
	 * is used to tie together pending and complete
	 * orders in Hosted (so we don't create duplicate orders).
	 * This has been modified to accurately work with woo commerce not independently
	 * tracking cart session vs order session
	 *
	 * @param     string $customer_id     The unique WooCommerce cart session ID.
	 * @param     string $customer_email     The guest customer's email address.
	 * @param     string $order_external_uuid The UUID value.
	 *
	 * @return string The hash used as the externalcheckoutid value
	 */
	public function generate_externalcheckoutid( $customer_id, $customer_email, $order_external_uuid = null ) {
		// Get the custom session if it exists
		if ( is_null( $order_external_uuid ) ) {
			$order_external_uuid = $this->get_or_generate_uuid();
		}

		// Generate the hash we'll use and return it
		return md5( $customer_id . $customer_email . $order_external_uuid );
	}

	/**
	 * Get the UUID from the session cart or generate a UUID for the customer abandoned cart.
	 *
	 * @return string
	 */
	public function get_or_generate_uuid() {
		if ( isset( wc()->session ) && wc()->session->get( 'activecampaignfwc_order_external_uuid' ) ) {
			$uuid = wc()->session->get( 'activecampaignfwc_order_external_uuid' );
		} else {
			$uuid = uniqid( '', true );
			wc()->session->set( 'activecampaignfwc_order_external_uuid', $uuid );
		}

		return $uuid;
	}

	/**
	 * Checks WooCommerce for a valid order.
	 *
	 * @param object $customer The customer object.
	 * @param string $activecampaignfwc_order_external_uuid The order UUID.
	 *
	 * @return bool|WC_Order
	 */
	public function check_for_valid_order( $customer, $activecampaignfwc_order_external_uuid ) {
		if ( $customer->id && ! empty( $activecampaignfwc_order_external_uuid ) ) {
			try {
				// Check if we have a valid order that may have failed to send.
				$externalcheckout_id = $this->generate_externalcheckoutid( $customer->id, $customer->email, $activecampaignfwc_order_external_uuid );
				$wc_post_id          = $this->find_existing_wc_order( $externalcheckout_id );
				$wc_order            = wc_get_order( $wc_post_id );

				// We have a valid order, do not send this as abandoned. Create an order instead.
				if ( $wc_order && isset( $wc_post_id ) && ! empty( $wc_post_id ) && $wc_order->get_id() ) {

					// This was a valid order, nothing else to do so skip the rest
					return $wc_order;
				}
			} catch ( Throwable $t ) {
				$logger = new Logger();
				$logger->error(
					'Abandonment Sync: There was an error trying to validate if this is an existing order. Do not process.',
					[
						'exception_message' => $t->getMessage(),
						'exception_trace'   => $logger->clean_trace( $t->getTrace() ),
					]
				);

				return true;
			}
		}

		return false;
	}

	/**
	 * Get the UUID for an abandoned cart by the customer ID.
	 *
	 * @param string $customer_id The customer ID from WC.
	 *
	 * @return array|bool|object|null
	 */
	public function get_uuid_by_customer_id( $customer_id ) {
		global $wpdb;
		$logger = new Logger();

		try {
			// Get the expired carts from our table
			$abandoned_uuid = $wpdb->get_var(
			// phpcs:disable
				$wpdb->prepare( 'SELECT activecampaignfwc_order_external_uuid 
					FROM
						`' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME . '`
					WHERE
						synced_to_ac = 0 AND
						customer_id = %s;',
					$customer_id
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

			if ( ! empty( $abandoned_uuid ) ) {
				// abandoned carts found
				return $abandoned_uuid;
			} else {
				// no abandoned carts
				return false;
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
	}

	/**
	 * Resets our UUID
	 */
	public function cleanup_session_activecampaignfwc_order_external_uuid() {
		$logger = new Logger();
		if ( isset( wc()->session ) && $this->get_or_generate_uuid() ) {
			wc()->session->set( 'activecampaignfwc_order_external_uuid', null );
			$logger->debug( 'Reset the activecampaignfwc_order_external_uuid on cart' );
		}
	}
}
