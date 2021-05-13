<?php

/**
 * The file that saves the abandoned carts.
 *
 * @link       https://www.activecampaign.com/
 * @since      1.3.2
 *
 * @package    Activecampaign_For_Woocommerce
 */

use Activecampaign_For_Woocommerce_Logger as Logger;
use Activecampaign_For_Woocommerce_Plugin_Upgrade_Command as Plugin_Upgrade;

/**
 * Save the cart to a table to keep the record in case it gets abandoned
 *
 * @since      1.0.0
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/includes/commands
 * @author     acteamintegrations <team-integrations@activecampaign.com>
 */
class Activecampaign_For_Woocommerce_Save_Abandoned_Cart_Command {

	/**
	 * The logger interface.
	 *
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * Activecampaign_For_Woocommerce_Update_Cart_Command constructor.
	 *
	 * @param     Logger $logger     The logger interface.
	 */
	public function __construct(
		Logger $logger = null
	) {
		$this->logger = $logger;
	}

	/**
	 * Store the last activity time for the current user.
	 * This is the initialization event which triggers on any cart change.
	 *
	 * @param     array ...$args     args passed.
	 *
	 * @return bool
	 */
	public function init( ...$args ) {
		// Get logged in user
		$current_user = wp_get_current_user();

		// Schedule single event for a logged in user if there's a cart
		if ( wc()->cart && $current_user ) {
			// Store the cart
			$this->abandoned_cart_store_cart();
			$this->abandoned_cart_schedule_recurring_abandon_task();
		}

		return true;
	}

	/**
	 * This adds the cart info to our table.
	 */
	private function abandoned_cart_store_cart() {
		$dt = new DateTime( 'NOW' );

		$current_user = wp_get_current_user();

		// Get current session
		$customer              = wc()->customer->get_data();
		$cart                  = wc()->cart->get_cart();
		$removed_cart_contents = wc()->cart->removed_cart_contents;
		$cart_totals           = wc()->cart->get_totals();
		$customer_id           = wc()->customer->get_id();
		$customer_email        = wc()->customer->get_email();
		$customer_first_name   = wc()->customer->get_first_name();
		$customer_last_name    = wc()->customer->get_last_name();

		// If we have a customer, do the stuff
		if ( ! empty( $customer_email ) ) {
			$activecampaignfwc_order_external_uuid = wc()->session->get( 'activecampaignfwc_order_external_uuid' );
			if ( ! $activecampaignfwc_order_external_uuid ) {
				$this->generate_externalcheckoutid( $customer->id, $customer->email );
				$activecampaignfwc_order_external_uuid = wc()->session->get( 'activecampaignfwc_order_external_uuid' );
			}

			// Step 1 verify we added a table
			$ac_table = new Plugin_upgrade();
			if ( ! $ac_table->verify_table() ) {
				$this->logger->error( 'Save abandoned cart command: Could not verify the abandoned cart table...' );

				return;
			}

			global $wpdb;

			try {
				$stored_id = $wpdb->get_var(
				// phpcs:disable
					$wpdb->prepare(
						'SELECT id FROM ' . $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME . ' WHERE customer_id = %s',
						$customer_id
					)
				// phpcs:enable
				);

				if ( $wpdb->last_error ) {
					$this->logger->error(
						'Save abandoned cart command: There was an error selecting the id for a customer abandoned cart record.',
						[
							'wpdb_last_error' => $wpdb->last_error,
							'customer_id'     => $customer_id,
						]
					);
				}

				if ( empty( $cart ) ) {
					$this->abandoned_cart_remove_empty( $customer_id );

					return;
				}

				// clean user_pass from user
				unset( $current_user->user_pass );

				if ( ! is_null( $stored_id ) ) {
					$wpdb->update(
						$wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME,
						[
							'activecampaignfwc_order_external_uuid' => $activecampaignfwc_order_external_uuid,
							'synced_to_ac'         => 0,
							'removed_cart_contents_ref_json' => wp_json_encode( $removed_cart_contents, JSON_UNESCAPED_UNICODE ),
							'cart_totals_ref_json' => wp_json_encode( $cart_totals, JSON_UNESCAPED_UNICODE ),
							'last_access_time'     => $dt->format( 'Y-m-d H:i:s' ),
							'customer_ref_json'    => wp_json_encode( $customer, JSON_UNESCAPED_UNICODE ),
							'user_ref_json'        => wp_json_encode( $current_user, JSON_UNESCAPED_UNICODE ),
							'cart_ref_json'        => wp_json_encode( $cart, JSON_UNESCAPED_UNICODE ),
						],
						[
							'id' => $stored_id,
						]
					);

					if ( $wpdb->last_error ) {
						$this->logger->error(
							'Save abandoned cart command: There was an error updating an abandoned cart record.',
							[
								'wpdb_last_error' => $wpdb->last_error,
								'activecampaignfwc_order_external_uuid' => $activecampaignfwc_order_external_uuid,
								'stored_id'       => $stored_id,
							]
						);
					}
				} else {
					$wpdb->insert(
						$wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME,
						[
							'synced_to_ac'         => 0,
							'customer_id'          => $customer_id,
							'customer_email'       => $customer_email,
							'customer_first_name'  => $customer_first_name,
							'customer_last_name'   => $customer_last_name,
							'last_access_time'     => $dt->format( 'Y-m-d H:i:s' ),
							'customer_ref_json'    => wp_json_encode( $customer, JSON_UNESCAPED_UNICODE ),
							'user_ref_json'        => wp_json_encode( $current_user, JSON_UNESCAPED_UNICODE ),
							'cart_ref_json'        => wp_json_encode( $cart, JSON_UNESCAPED_UNICODE ),
							'cart_totals_ref_json' => wp_json_encode( $cart_totals, JSON_UNESCAPED_UNICODE ),
							'removed_cart_contents_ref_json' => wp_json_encode( $removed_cart_contents, JSON_UNESCAPED_UNICODE ),
							'activecampaignfwc_order_external_uuid' => $activecampaignfwc_order_external_uuid,
						]
					);

					if ( $wpdb->last_error ) {
						$this->logger->error(
							'Save abandoned cart command: There was an error inserting an abandoned cart record.',
							[
								'wpdb_last_error'     => $wpdb->last_error,
								'customer_id'         => $customer_id,
								'customer_first_name' => $customer_first_name,
								'customer_last_name'  => $customer_last_name,
								'activecampaignfwc_order_external_uuid' => $activecampaignfwc_order_external_uuid,
							]
						);
					}
				}
			} catch ( Exception $e ) {
				$this->logger->warning(
					'Save abandoned cart command: There was an error attempting to save this abandoned cart',
					[
						'exception'           => $e,
						'customer_email'      => $customer_email,
						'customer_first_name' => $customer_first_name,
						'$customer_last_name' => $customer_last_name,
					]
				);
			}
		}
	}

	/**
	 * Removes an empty record by passing it the customer ID
	 *
	 * @param     int $customer_id     The customer id.
	 */
	private function abandoned_cart_remove_empty( $customer_id ) {
		try {
			global $wpdb;
			$table_name = $wpdb->prefix . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME;
			$wpdb->delete(
				$table_name,
				[
					'customer_id' => $customer_id,
				]
			);

			return;
		} catch ( Exception $e ) {
			$this->logger->debug(
				'Checkout meta: could not delete the abandoned cart entry.',
				[
					'exception'   => $e,
					'customer_id' => $customer_id,
				]
			);

			return;
		}
	}

	/**
	 * This schedules the recurring event and verifies it's still set up
	 */
	private function abandoned_cart_schedule_recurring_abandon_task() {
		// If not scheduled, set up our recurring event
		if ( ! wp_next_scheduled( 'activecampaign_for_woocommerce_cart_updated_recurring_event' ) ) {
			wp_schedule_event( time(), 'hourly', 'activecampaign_for_woocommerce_cart_updated_recurring_event' );
		} else {
			$this->logger->debug(
				'Recurring cron already scheduled',
				[
					'time_now' => time(),
					'myevent'  => wp_get_scheduled_event( 'activecampaign_for_woocommerce_cart_updated_recurring_event' ),
				]
			);
		}
	}
}
