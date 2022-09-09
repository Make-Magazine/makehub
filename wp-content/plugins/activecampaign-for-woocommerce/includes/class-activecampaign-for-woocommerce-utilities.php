<?php

/**
 * The file that defines the Global Utilities.
 *
 * @link       https://www.activecampaign.com/
 * @since      1.x
 *
 * @package    Activecampaign_For_Woocommerce
 */

/**
 * The Utilities Class.
 *
 * @since      1.0.0
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/includes/events
 * @author     acteamintegrations <team-integrations@activecampaign.com>
 */
class Activecampaign_For_Woocommerce_Utilities {
	/**
	 * Checks both post and get for values. WC seems to pass nonce as GET but fields pass as POST.
	 *
	 * @param string $field The field name.
	 *
	 * @return mixed|null Returns field data.
	 */
	public static function get_request_data( $field ) {
		$get_input     = null;
		$post_input    = null;
		$request_input = null;

		try {
			// phpcs:disable
			$post_input = filter_input( INPUT_POST, $field, FILTER_SANITIZE_STRING );
			$get_input  = filter_input( INPUT_GET, $field, FILTER_SANITIZE_STRING );
			// phpcs:enable
			if ( ! empty( $post_input ) ) {
				return $post_input;
			}

			if ( ! empty( $get_input ) ) {
				return $get_input;
			}
		} catch ( Throwable $t ) {
			$logger = new Activecampaign_For_Woocommerce_Logger();
			$logger->error(
				'There was an issues getting get or post data for a field',
				[
					'get_input'  => $get_input,
					'post_input' => $post_input,
				]
			);
		}

		try {
			// phpcs:disable
			if ( isset( $_REQUEST[ $field ] ) ){
				$request_input = $_REQUEST[ $field ];
				// phpcs:enable
				if ( ! empty( $request_input ) ) {
					return $request_input;
				}
			}
		} catch ( Throwable $t ) {
			$logger = new Activecampaign_For_Woocommerce_Logger();
			$logger->error(
				'There was an issues getting request data for a field',
				[
					'request_input' => $request_input,
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
	public static function validate_object( $o, $s ) {
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
