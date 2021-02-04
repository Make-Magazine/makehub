<?php if ( ! defined( 'EVENT_ESPRESSO_VERSION' )) { exit('NO direct script access allowed'); }


/**
 *
 * Class  EEA_Payflow_Pro_Billing_Form
 *
 * @package			Event Espresso
 * @subpackage		espresso-payflow-pro-gateway
 * @author			Event Espresso
 * @version		 	$VID:$
 */
class EEA_Payflow_Pro_Billing_Form extends EE_Billing_Attendee_Info_Form {

	protected function _normalize( $req_data ) {
		parent::_normalize( $req_data );
	}


	/**
	 *
	 * @param EE_PMT_Payflow_Pro_Onsite $payment_method_type
	 * @param EE_Payment_Method $payment_method
	 */
	public function __construct( EE_PMT_Payflow_Pro_Onsite $payment_method_type, EE_Payment_Method $payment_method ) {
		$settings = $payment_method->settings_array();
		$options_array = array(
			'name' => 'eea_payflow_pro_onsite_billing_form',
			'subsections'=>array(
				'credit_card' => new EE_Credit_Card_Input( array(
					'html_class' => 'ee-payflow-pro-billing-form-credit-card',
					'required' => true,
					'html_label_text' => __('Card Number', 'event_espresso'),
				)),
				'exp_month' => new EE_Credit_Card_Month_Input( true, array(
					'html_class' => 'ee-payflow-pro-billing-form-exp-month',
					'required' => true,
					'html_label_text' =>  __( 'Expiry Month', 'event_espresso' ),
				)),
				'exp_year' => new EE_Credit_Card_Year_Input( 
					array(
						'html_class' => 'ee-payflow-pro-billing-form-exp-year',
						'required' => true,
						'html_label_text' => __( 'Expiry Year', 'event_espresso' )
					),
					false
				),
				'card_cvv' => new EE_CVV_Input( array(
					'html_class' => 'ee-payflow-pro-billing-form-card-cvv',
					'required' => true,
					'html_label_text' => __( 'CVV', 'event_espresso' )
				)),
			),
			'exclude' => array( 'phone' )
		);

		parent::__construct( $payment_method, $options_array );
	}


	/**
	 * Create_attendee_from_billing_form_data
	 * uses info from the billing form to create a new attendee
	 *
	 * @return \EE_Attendee
	 */
	public function create_attendee_from_billing_form_data() {
		// Grab billing form data.
		$data = $this->valid_data();
		return EE_Attendee::new_instance( array(
			'ATT_fname' => ! empty( $data['first_name'] ) ? $data['first_name'] : '',
			'ATT_lname' => ! empty( $data['last_name'] ) ? $data['last_name'] : '',
			'ATT_email' => ! empty( $data['email'] ) ? $data['email'] : '',
			'ATT_address' => ! empty( $data['address'] ) ? $data['address'] : '',
			'ATT_address2' => ! empty( $data['address2'] ) ? $data['address2'] : '',
			'ATT_city' => ! empty( $data['city'] ) ? $data['city'] : '',
			'STA_abbrev' => ! empty( $data['state'] ) ? $data['state'] : '',
			'CNT_ISO' => ! empty( $data['country'] ) ? $data['country'] : '',
			'ATT_zip' => ! empty( $data['zip'] ) ? $data['zip'] : '',
			'ATT_phone' => ! empty( $data['phone'] ) ? $data['phone'] : ''
		));
	}

}
// End of class EEA_Payflow_Pro_Billing_Form.
// End of file EEA_Payflow_Pro_Billing_Form.form.php