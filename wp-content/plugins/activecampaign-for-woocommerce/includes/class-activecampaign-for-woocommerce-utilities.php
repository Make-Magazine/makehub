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
		// phpcs:disable
		$get_input = null;
		$post_input = null;
		$request_input = null;

		try {
			$post_input = filter_input( INPUT_POST, $field, FILTER_SANITIZE_STRING );
			$get_input  = filter_input( INPUT_GET, $field, FILTER_SANITIZE_STRING );

			if ( ! empty( $post_input ) ) {
				return $post_input;
			}

			if ( ! empty( $get_input ) ) {
				return $get_input;
			}
		}catch(Throwable $t){
			$logger = new Activecampaign_For_Woocommerce_Logger();
			$logger->error('There was an issues getting get or post data for a field',[
				'get_input'=>$get_input,
				'post_input'=>$post_input,
			]);
		}

		try {
			$request_input = $_REQUEST[ $field ];


			if ( ! empty( $request_input ) ) {
				return $request_input;
			}
		}catch(Throwable $t){
			$logger = new Activecampaign_For_Woocommerce_Logger();
			$logger->error('There was an issues getting request data for a field',[
			'request_input'=>$request_input,
			]);
		}

		return null;
		// phpcs:enable
	}
}
