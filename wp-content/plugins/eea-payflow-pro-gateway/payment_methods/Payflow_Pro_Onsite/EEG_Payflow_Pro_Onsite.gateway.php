<?php if ( ! defined( 'EVENT_ESPRESSO_VERSION' )) { exit('NO direct script access allowed'); }


/**
 *
 * Class  EEG_Payflow_Pro_Onsite
 *
 * @package			Event Espresso
 * @subpackage		espresso-payflow-pro-gateway
 * @author			Event Espresso
 * @version		 	$VID:$
 */
class EEG_Payflow_Pro_Onsite extends EE_Onsite_Gateway {

	/**
	 * Payflow user id.
	 * @var $_login_id string
	 */
	protected $_user = NULL;

	/**
	 * Payflow vendor id.
	 * @var $_login_id string
	 */
	protected $_vendor = NULL;

	/**
	 * Payflow user password.
	 * @var $_password string
	 */
	protected $_password = NULL;

	/**
	 * Payflow Partner id.
	 * @var $_partner string
	 */
	protected $_partner = NULL;

	/**
	 * Payflow Transactions process method.
	 * @var $_transaction_method string
	 */
	protected $_transaction_method = NULL;

	/**
	 *	Currencies supported by this gateway.
	 *	@var array
	 */
	protected $_currencies_supported = array(
			'AUD',
			'BRL',
			'CAD',
			'EUR',
			'GBP',
			'CZK',
			'DKK',
			'HKD',
			'HUF',
			'ILS',
			'JPY',
			'MXN',
			'TWD',
			'NZD',
			'NOK',
			'PHP',
			'PLN',
			'SGD',
			'SEK',
			'CHF',
			'THB',
			'USD'
		);


	/**
	 * Process the payment.
	 *
	 * @param EEI_Payment $payment
	 * @param array       $billing_info
	 * @return EE_Payment|EEI_Payment
	 */
	public function do_direct_payment( $payment, $billing_info = null ) {
		if ( $payment instanceof EEI_Payment ) {
			$transaction = $payment->transaction();
			if ( $transaction instanceof EE_Transaction ) {
				// Update transaction_method option if not yet set after Plugin update.
				if ( ! $this->_transaction_method ) {
					$transaction->payment_method()->update_extra_meta( 'transaction_method', 'S' );
					$this->_transaction_method = 'S';
				}

				$order_description  = $this->_format_order_description($payment);
				$primary_registrant = $transaction->primary_registration();
				$events_list = array_unique( EEM_Event::instance()->get_col( array( array( 'Registration.TXN_ID' => $transaction->ID() ) ), 'EVT_name' ) );

				// Itemized list.
				if ( $this->_can_easily_itemize_transaction_for($payment) ) {
					$item_num = 1;
					$total_line_item = $transaction->total_line_item();
					$order_l_items = array();
					foreach ( $total_line_item->get_items() as $line_item ) {
						$l_item = array(
							'L_NAME' . $item_num => substr($this->_format_line_item_name($line_item, $payment), 0, 127),
							'L_DESC' . $item_num => substr($this->_format_line_item_desc($line_item, $payment), 0, 127),
							'L_COST' . $item_num => number_format( $line_item->unit_price(), 2, '.', '' ),
							'L_QTY' . $item_num => $line_item->quantity(),
							'L_TAXAMT' . $item_num => '',
							'L_SKU' . $item_num => $item_num++
						);
						array_push($order_l_items, $l_item);
					}
					$item_amount = $total_line_item->get_items_total();
					$tax_amount = $total_line_item->get_total_tax();
				} else {
					$order_l_items = array();
					$item_amount = $payment->amount();
					$tax_amount = 0;
					array_push($order_l_items, array(
						'L_NAME1' => substr($this->_format_partial_payment_line_item_name($payment), 0, 127),
						'L_DESC1' => substr($this->_format_partial_payment_line_item_desc($payment), 0, 127),
						'L_COST1' => $payment->amount(),
						'L_SKU1' => 1,
						'L_QTY1' => 1
					));
				}

				// Prep Authorization request parameters.
				$payment_request_args = array(
					'TENDER' => 'C',
					'TRXTYPE' => $this->_transaction_method,
					'ACCT' => $billing_info['credit_card'],
					'EXPDATE' => $billing_info['exp_month'] . $billing_info['exp_year'],
					'CVV2' => $billing_info['card_cvv'],
					'AMT' => number_format( $payment->amount(), 2, '.', '' ),
					'CURRENCY' => $payment->currency_code(),

					'BILLTOFIRSTNAME' => substr($billing_info['first_name'], 0, 30),
					'BILLTOLASTNAME' => substr($billing_info['last_name'], 0, 30),
					'SHIPTOFIRSTNAME' => substr($billing_info['first_name'], 0, 30),
					'SHIPTOLASTNAME' => substr($billing_info['last_name'], 0, 30),
					'BILLTOEMAIL' => $billing_info['email'],
					'CUSTIP' => $_SERVER['REMOTE_ADDR'],

					'BILLTOSTREET' => substr($billing_info['address'], 0, 30),
					'BILLTOSTREET2' => substr($billing_info['address2'], 0, 30),
					'BILLTOCITY' => substr($billing_info['city'], 0, 20),
					'BILLTOSTATE' => substr($billing_info['state'], 0, 30),
					'BILLTOZIP' => $billing_info['zip'],
					'BILLTOCOUNTRY' => $billing_info['country'],
					'SHIPTOSTREET' => substr($billing_info['address'], 0, 30),
					'SHIPTOCITY' => substr($billing_info['city'], 0, 20),
					'SHIPTOSTATE' => substr($billing_info['state'], 0, 30),
					'SHIPTOZIP' => $billing_info['zip'],
					'SHIPTOCOUNTRY' => $billing_info['country'],

					'ORDERDESC' => $order_description,
					'INVNUM' => substr($transaction->ID() . '_' . wp_generate_password(6, false), 0, 9),
					'COMMENT1' => 'Reg Code: ' . $primary_registrant->reg_code(),
					'COMMENT2' => implode(', ', $events_list)
				);
				// Include the items list.
				foreach ($order_l_items as $li_item) {
					foreach ($li_item as $li_ref => $item_val) {
						$payment_request_args[$li_ref] = $item_val;
					}
				}
                $payment_request_args = apply_filters(
                    'FHEE__EEG_Payflow_Pro_Onsite__do_direct_payment__payment_request_args',
                    $payment_request_args,
                    $this,
                    $payment
                );
				try {
					if ( $this->_transaction_method === 'A' ) {	// Do a Authorization request.
						// Logging.
						$this->_log_clean_request( $payment_request_args, $payment, 'Payflow Pro Authorization Request' );
						$authorization = $this->_request_to_payflow( $payment_request_args, $transaction );
						$this->_log_clean_response( $authorization, $payment, 'Payflow Pro Authorization Response' );

						if ( $authorization['RESULT'] === '0' && $authorization['RESPMSG'] == 'Approved' ) {
							// Prep Delayed Capture request parameters.
							$capture_request_args['TRXTYPE'] = 'D';
							$capture_request_args['ORIGID'] = $authorization['PNREF'];
							$capture_request_args['AMT'] = $payment_request_args['AMT'];
							$capture_request_args['CURRENCY'] = $payment_request_args['CURRENCY'];
							//$capture_request_args['VERBOSITY'] = 'HIGH';

							// Logging.
							$this->_log_clean_request( $capture_request_args, $payment, 'Payflow Pro Delayed Capture Request' );
							// Send Delayed Capture request.
							$delayed_capture = $this->_request_to_payflow( $capture_request_args, $transaction );
							$this->_log_clean_response( $delayed_capture, $payment, 'Payflow Pro Delayed Capture Response' );

							if ( $delayed_capture['RESULT'] === '0' && $delayed_capture['RESPMSG'] == 'Approved' ) {
								$payment->set_status($this->_pay_model->approved_status());
								if ( isset($delayed_capture['AMT']) ) {
									$payment->set_amount( floatval( $delayed_capture['AMT'] ) );
								}
								$payment->set_gateway_response( $delayed_capture['RESPMSG'] );
								$payment->set_txn_id_chq_nmbr( isset( $delayed_capture['PNREF'] ) ? $delayed_capture['PNREF'] : null );
							} elseif ( $delayed_capture['RESULT'] === '126' ) {
								// Fraud Protection Services Filter — Flagged for review by filters, so lets set a rejected status.
								$payment->set_txn_id_chq_nmbr( isset( $delayed_capture['PNREF'] ) ? $delayed_capture['PNREF'] : null );
								$payment->set_status( $this->_pay_model->pending_status() );
								$payment->set_gateway_response( $delayed_capture['RESPMSG'] );
								$payment->set_extra_accntng(
									substr(
										__( 'Requires MANUAL APPROVAL in Event Espresso', 'event_espresso' ),
										0,
										100
									)
								);
							} else {
								$payment->set_status( $this->_pay_model->declined_status() );
								$payment->set_gateway_response( $delayed_capture['RESPMSG'] );
							}
						} elseif ( $authorization['RESULT'] === '126' ) {
							// Fraud Protection Services Filter — Flagged for review by filters, so lets set a rejected status.
							$payment->set_txn_id_chq_nmbr( isset( $authorization['PNREF'] ) ? $authorization['PNREF'] : null );
							$payment->set_status( $this->_pay_model->pending_status() );
							$payment->set_gateway_response( $delayed_capture['RESPMSG'] );
							$payment->set_extra_accntng(
									substr(
											__( 'Requires MANUAL APPROVAL in Event Espresso', 'event_espresso' ),
											0,
											100 ) );
						} else {
							$payment->set_status( $this->_pay_model->declined_status() );
							$payment->set_gateway_response( $authorization['RESPMSG'] );
						}
					} elseif ( $this->_transaction_method === 'S' ) {	// Process transaction as a Sale.
						// Logging.
						$this->_log_clean_request( $payment_request_args, $payment, 'Payflow Pro Sale Request' );

						$payment_request_args['VERBOSITY'] = 'HIGH';
						$sale = $this->_request_to_payflow( $payment_request_args, $transaction );
						$this->_log_clean_response( $sale, $payment, 'Payflow Pro Sale Response' );

						if ( $sale['RESULT'] === '0' ) {
							$payment->set_status( $this->_pay_model->approved_status() );
							if ( isset($sale['AMT']) ) {
								$payment->set_amount( floatval( $sale['AMT'] ) );
							}
							$payment->set_gateway_response( $sale['RESPMSG'] );
							$payment->set_txn_id_chq_nmbr( isset( $sale['PNREF'] ) ? $sale['PNREF'] : null );
						} elseif ( $sale['RESULT'] === '126' ) {
							// Fraud Protection Services Filter — Flagged for review by filters, so lets set a rejected status.
							$payment->set_txn_id_chq_nmbr( isset( $sale['PNREF'] ) ? $sale['PNREF'] : null );
							$payment->set_status( $this->_pay_model->pending_status() );
							$payment->set_gateway_response( $sale['RESPMSG'] );
							$payment->set_extra_accntng(
									substr(
											__( 'Requires MANUAL APPROVAL in Event Espresso', 'event_espresso' ),
											0,
											100 ) );
						} else {
							$payment->set_status( $this->_pay_model->declined_status() );
							$payment->set_gateway_response( $sale['RESPMSG'] );
						}
					}
				} catch ( Exception $e ) {
					$payment->set_status( $this->_pay_model->failed_status() );
					$payment->set_gateway_response( $e->getMessage() );
				}
			}
		}
		return $payment;
	}


	/**
	 * Send a request to Payflow.
	 *
	 * @param array $payment_request_args
	 * @param EE_Transaction $transaction
	 * @return void
	 */
	private function _request_to_payflow( $payment_request_args, $transaction ) {
		$request_url = $this->_debug_mode ? 'https://pilot-payflowpro.paypal.com' : 'https://payflowpro.paypal.com';
		$partner = ( $this->_partner == NULL ) ? 'PayPal' : $this->_partner;
		$headers = array();
		// Max 32 chars.
		$unique_id = substr( $transaction->ID() . '_' . wp_generate_password(22, false), 0, 32 );
		$headers[] = "Content-Type: text/namevalue";
		// Set the server timeout value to 45, but notice below in the cURL section, the timeout
		// for cURL is set to 90 seconds.  Make sure the server timeout is less than the connection.
		$headers[] = "X-VPS-CLIENT-TIMEOUT: 45";
		$headers[] = "X-VPS-REQUEST-ID:" . $unique_id;
		// Merchant info.
		$nvp_request = 'USER=' . $this->_user . '&VENDOR=' . $this->_vendor . '&PARTNER=' . $partner . '&PWD=' . $this->_password . '&BUTTONSOURCE=EventEspresso_SP';

		foreach ($payment_request_args as $ref => $value) {
			$nvp_request .= '&' . $ref . '=' . $value;
		}

		$request_response = wp_remote_post(
			$request_url,
			array(
				'method' => 'POST',
				'timeout' => 60,
				'redirection' => 5,
				'blocking' => true,
				'headers' => $headers,
				'body' => $nvp_request,
				'cookies' => array(),
				'httpversion' => '1.1',
			)
		);

		return $this->_decode_response( $request_response['body'] );
	}



	/**
	 * This function will take NVPString and convert it to an Associative Array and it will decode the response.
	 *
	 * @param array $request
	 * @param NVPString $response
	 * @return array  Request response in an array format.
	 */
	private function _decode_response( $response ) {
		$intial = 0;
	 	$nvp_array = array();
		while ( strlen($response) ) {
			// Postion of Key.
			$keypos = strpos($response,'=');
			// Position of value.
			$valuepos = strpos($response, '&') ? strpos($response, '&') : strlen($response);
			// Getting the Key and Value values and storing in a Associative Array.
			$keyval = substr($response, $intial, $keypos);
			$valval = substr($response, $keypos + 1, $valuepos - $keypos - 1);
			// Decoding the respose.
			$nvp_array[urldecode($keyval)] = urldecode( $valval );
			$response = substr( $response, $valuepos + 1, strlen($response) );
	     }
		return $nvp_array;
	}


	/**
	 * CLeans out sensitive CC data and then logs it.
	 *
	 * @param array $request
	 * @param EEI_Payment $payment
	 * @return void
	 */
	private function _log_clean_request( $request, $payment, $data_info ) {
		$cleaned_request_data = $request;
		unset( $cleaned_request_data['ACCT'] );
		unset( $cleaned_request_data['CVV2'] );
		unset( $cleaned_request_data['EXPDATE'] );
		$this->log( array($data_info => $cleaned_request_data), $payment );
	}


	/**
	 * Log a clean response.
	 *
	 * @param array $response
	 * @param EEI_Payment $payment
	 * @return array cleaned
	 */
	private function _log_clean_response( $response, $payment, $data_info ) {
		$this->log( array($data_info => $response), $payment );
		return $response;
	}
}