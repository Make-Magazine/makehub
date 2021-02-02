<?php if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) { exit( 'No direct script access allowed !' ); }

use EEA_QuickBooks\OAuthSimple;

/**
 * Class  EEG_QuickBooks_Onsite
 *
 *	@package 		Event Espresso
 *	@subpackage 	eea-quickbooks-gateway
 *	@author 		Event Espresso
 *	
 */
class EEG_QuickBooks_Onsite extends EE_Onsite_Gateway {

	/**
	 * OAuth version.
	 * @var $_oauth_version string
	 */
	protected $_oauth_version = NULL;

	/**
	 * OAuth Consumer Key.
	 * @var $_consumer_key string
	 */
	protected $_consumer_key = NULL;

	/**
	 * OAuth Consumer Secret.
	 * @var $_shared_secret string
	 */
	protected $_shared_secret = NULL;

	/**
	 * OAuth access token.
	 * @var $_oauth_token string
	 */
	protected $_oauth_token = NULL;

	/**
	 * OAuth access token secret.
	 * @var $_oauth_token_secret string
	 */
	protected $_oauth_token_secret = NULL;

	/**
	 * OAuth 2.0 access token.
	 * @var $_access_token
	 */
	protected $_access_token = NULL;

	/**
	 * OAuth 2.0 refresh token.
	 * @var $_refresh_token string
	 */
	protected $_refresh_token = NULL;

	/**
	 *	Currencies supported by this gateway.
	 *	@var array
	 */
	protected $_currencies_supported = EE_Gateway::all_currencies_supported;


	/**
	 * Process the payment.
	 *
	 * @param EEI_Payment $payment
	 * @param array  $billing_info
	 * @return EE_Payment|EEI_Payment
	 */
	public function do_direct_payment( $payment, $billing_info = null ) {
		if ( $payment instanceof EEI_Payment ) {
			$transaction = $payment->transaction();
			if ( $transaction instanceof EE_Transaction ) {
                $amount = str_replace(array(','), '', number_format($payment->amount(), 2));
                $order_description = sprintf(__('Event Registrations from %s', 'event_espresso'), get_bloginfo('name'));
                $body_parameters = array(
                    'amount'      => $amount,
                    'currency'    => $payment->currency_code(),
                    'capture'     => true,
                    'token'       => $billing_info['qb_cc_token'],
                    'description' => $order_description,
                    'context'     => array(
                        'mobile'      => false,
                        'isEcommerce' => true
                    )
                );
				// Send an Authorization request and auto capture.
				$auth_capture_response = $this->send_cherge_request($payment, $transaction, $body_parameters);

				if (! is_wp_error($auth_capture_response)
                    && is_array($auth_capture_response)
                    && (
                        $auth_capture_response['response']['code'] == '201'
                        || $auth_capture_response['response']['code'] == '200'
                    )
                ) {
					$auth_capture_inf = json_decode( $auth_capture_response['body'], TRUE );
					if ( $auth_capture_inf && $auth_capture_inf['status'] === 'CAPTURED' ) {
						unset($billing_info['qb_cc']);
						unset($billing_info['qb_cc_token']);
						unset($billing_info['qb_select_flag']);
						$payment->set_status( $this->_pay_model->approved_status() );
						$payment->set_gateway_response( $auth_capture_inf['status'] );
						$payment->set_amount( floatval($auth_capture_inf['amount']) );
						$payment->set_txn_id_chq_nmbr( $auth_capture_inf['id'] );
						$payment->set_details( $billing_info );
					} else {
						$this->_log_clean_request( $body_parameters, $payment, 'Card Authorization/Capture Request' );
						$this->log( array( 'Card Authorization/Capture Error' => $auth_capture_inf), $payment );
						$payment->set_status( $this->_pay_model->declined_status() );
						$err_inf = ( !empty($auth_capture_inf['errors']) && is_array($auth_capture_inf['errors']) ) ? reset($auth_capture_inf['errors']) : $auth_capture_inf;
						$err_msg_code = ( isset($err_inf['code']) ) ? $err_inf['code'] : '';
						$err_msg_message = ( isset($err_inf['message']) ) ? $err_inf['message'] : '';
						$err_msg = sprintf(__('QuickBooks Card Authorization/Capture Error: %1$s %2$s', 'event_espresso'), $err_msg_code, $err_msg_message);
						$payment->set_gateway_response( $err_msg );
					}
				} else {
					$this->_log_clean_request( $body_parameters, $payment, 'Authorization/Capture Request' );
					$this->log( array( 'Authorization/Capture Request Error' => $auth_capture_response), $payment );
					$payment->set_status( $this->_pay_model->failed_status() );
					$err_msg = __('QuickBooks Card Authorization/Capture Error', 'event_espresso');
					if ( is_array($auth_capture_response) && $auth_capture_response['body'] ) {
						$err_inf = json_decode( $auth_capture_response['body'], TRUE );
						$err_inf = ( !empty($err_inf['errors']) && is_array($err_inf['errors']) ) ? reset($err_inf['errors']) : $err_inf;
						$err_inf_code = ( $err_inf && is_array($err_inf) && isset($err_inf['code']) ) ? $err_inf['code'] : '';
						$err_inf_message = ( $err_inf && is_array($err_inf) && isset($err_inf['message']) ) ? $err_inf['message'] : '';
						$err_msg = sprintf(__('QuickBooks Card Authorization/Capture Error: %1$s %2$s', 'event_espresso'), $err_inf_code, $err_inf_message);
					}
					$payment->set_gateway_response( $err_msg );
				}
			} else {
				$payment->set_status( $this->_pay_model->failed_status() );
			}
		} else {
			$payment->set_status( $this->_pay_model->failed_status() );
		}

		return $payment;
	}


    /**
     * Process the payment.
     *
     * @param EEI_Payment     $payment
     * @param EE_Transaction  $transaction
     * @param array           $body_parameters
     * @return array|WP_Error
     */
    public function send_cherge_request($payment, $transaction, $body_parameters) {
        // Using OAuth 1.0a credentials.
        $environment = 'https://api.intuit.com/quickbooks/v4/payments/charges';
        if ($this->_debug_mode) {
            $environment = 'https://sandbox.api.intuit.com/quickbooks/v4/payments/charges';
        }
        // Charge request depends on the OAuth creds.
		if ($this->_oauth_version === 'v1a') {
            // Include OAuth dependencies.
            require_once(EEA_QUICKBOOKS_PM_PLUGIN_PATH . 'includes' . DS . 'oauth_simple' . DS . 'OAuthSimple.php');
            $oauth_object = new OAuthSimple();

            // Prep requests data.
            $sign_params = array(
                'path'       => $environment,
                'action'     => 'POST',
                'method'     => 'HMAC-SHA1',
                'parameters' => $body_parameters,
                'signatures' => array(
                    'consumer_key'  => $this->_consumer_key,
                    'shared_secret' => $this->_shared_secret,
                    'oauth_token'   => $this->_oauth_token,
                    'oauth_secret'  => $this->_oauth_token_secret
                )
            );
            $post_params = array(
                'method'      => 'POST',
                'timeout'     => 60,
                'redirection' => 5,
                'blocking'    => true,
                'headers'     => array(
                    'Content-Type' => 'application/json',
                    'Request-Id'   => substr(EED_QuickBooks_Oauth::get_GUID(), 0, 50)
                ),
                'body'    => json_encode($body_parameters),
                'cookies' => array()
            );

            $auth_request_result = $oauth_object->sign($sign_params);
            // Can't work without that signed URL.
            if (! is_array($auth_request_result)
                || ! isset($auth_request_result['signed_url'])
                || empty($auth_request_result['signed_url'])
            ) {
                $err_msg = esc_html__('Was not able to sign the card Authorization/Capture.', 'event_espresso');
                $this->_log_clean_request($sign_params, $payment, 'Sign URL Request');
                $this->log(array($err_msg => $auth_request_result), $payment);
                $payment->set_gateway_response($err_msg);
                $payment->set_status($this->_pay_model->failed_status());
                return $payment;
            }
            $signed_url = $auth_request_result['signed_url'];
            $auth_capture_response = wp_remote_post($signed_url, $post_params);
        } else {
            $qb_pm = $transaction->payment_method();
            // Check the access token because it is valid only for 1 hour. Might need to update it.
            $keys_updated = EED_QuickBooks_Oauth_v2::check_access_token($qb_pm);
            // If keys did update we need the new ones here.
            if ($keys_updated) {
                $this->_access_token = $qb_pm->get_extra_meta('access_token', true);
                $this->_refresh_token = $qb_pm->get_extra_meta('refresh_token', true);
            }
            // Using OAuth 2.0 credentials.
            $post_params = array(
                'method'      => 'POST',
                'timeout'     => 60,
                'redirection' => 5,
                'blocking'    => true,
                'headers'     => array(
                    'Authorization' => 'Bearer ' . $this->_access_token,
                    'Content-Type'  => 'application/json',
                    'Request-Id'    => substr(EED_QuickBooks_Oauth::get_GUID(), 0, 50)
                ),
                'body'        => json_encode($body_parameters)
            );
            $auth_capture_response = wp_remote_post($environment, $post_params);
        }
        return $auth_capture_response;
    }


    /**
	 * CLeans out sensitive data and then logs it.
	 *
	 * @param array $request
	 * @param EEI_Payment $payment
	 * @param string $data_info
	 * @return void
	 */
	private function _log_clean_request( $request, $payment, $data_info ) {
		$cleaned_request_data = $request;
		unset( $cleaned_request_data['signatures']['oauth_token'] );
		unset( $cleaned_request_data['signatures']['oauth_secret'] );
		$this->log( array($data_info => $cleaned_request_data), $payment );
	}
}