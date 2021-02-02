<?php if ( ! defined('EVENT_ESPRESSO_VERSION') ) { exit('No direct script access allowed'); }

/**
 *	Class EE_QuickBooks_Billing_Form
 *
 *	@package 		Event Espresso
 *	@subpackage 	eea-quickbooks-gateway
 *	@author 		Event Espresso
 *
 */
class EE_QuickBooks_Billing_Form extends EE_Billing_Attendee_Info_Form {

	protected function _normalize( $req_data ) {
		parent::_normalize( $req_data );
	}

	/**
	 *
	 * @param EE_PMT_QuickBooks_Onsite $payment_method_type
	 * @param EE_Payment_Method $payment_method
	 */
	public function __construct( EE_PMT_QuickBooks_Onsite $payment_method_type, EE_Payment_Method $payment_method ) {
		$state_list = array( '' => array( '' => '' ) );
		$state_list = array_merge( $state_list, $payment_method_type->get_state_abbrv_list() );

		// Default the state field to an empty selection.
		add_filter( 'FHEE__EE_Billing_Attendee_Info_Form__populate_from_attendee', array($this, 'populate_qb_state_defaults'), 12, 3 );

		$options_array = array(
			'name' => 'QuickBooks_Onsite_Billing_Form',
			'html_id' => 'eea-quickbooks-billing-form',
			'html_class' => 'ee-billing-form',
			'subsections' => array(
				'state' => new EE_Select_Input( $state_list, array(
					'required' => true
				)),
				'cc_name' =>  new EE_Text_Input( array(
					'html_class' => 'eea-quickbooks-billing-form-cc-name',
					'html_label_text' => __( 'Name on Card', 'event_espresso' ),
					'required' => true
				)),
				'credit_card' =>  new EE_QB_Credit_Card_Input( array(
					'html_class' => 'eea-quickbooks-billing-form-credit-card',
					'html_label_text' => sprintf( __( 'Card Number %s', 'event_espresso' ), '<span class="ee-asterisk">*</span>' ),
					'required' => false,
					'html_other_attributes' => 'required=""'
				)),
				'exp_month' =>  new EE_Credit_Card_Month_Input( true, array(
					'html_class' => 'eea-quickbooks-billing-form-exp-month',
					'html_label_text' => __( 'Expiry Month', 'event_espresso' ),
					'required' => true
				)),
				'exp_year' =>  new EE_Credit_Card_Year_Input( array(
					'html_class' => 'eea-quickbooks-billing-form-exp-year',
					'html_label_text' => __( 'Expiry Year', 'event_espresso' ),
					'required' => true
				)),
				'cvv' =>  new EE_CVV_Input( array(
					'html_class' => 'eea-quickbooks-billing-form-cvv',
					'html_label_text' => __( 'CVV', 'event_espresso' ),
					'required' => true
				)),
				'qb_cc_token' => new EE_Hidden_Input( array(
					'html_id' => 'eea_quickbooks_cc_token',
					'default' => '',
					'sensitive_data_removal_strategy' => new EE_All_Sensitive_Data_Removal()
				)),
				'qb_select_flag' => new EE_Hidden_Input( array(
					'html_id' => 'eea_qb_submit_payment',
					'default' => 'qb_flag',
					'sensitive_data_removal_strategy' => new EE_All_Sensitive_Data_Removal()
				)),
				'qb_cc_ex' => new EE_Hidden_Input( array(
					'html_id' => 'eea-qb-submit-cc-ex',
					'default' => '',
					'sensitive_data_removal_strategy' => new EE_All_Sensitive_Data_Removal()
				))
			)
		);

		parent::__construct( $payment_method, $options_array );
	}


	/**
	 * Populates the default data for the State input.
	 *
	 * @param array $defaults  Form defaults
	 */
	public function populate_qb_state_defaults( $defaults, $attendee, $form ) {
		if( $form instanceof EE_QuickBooks_Billing_Form ) {
			$state = EEM_State::instance()->get_one_by_ID( $attendee->state_ID() );
			if ( $state instanceof EE_State ) {
				$defaults['state'] = $state->abbrev();
			} else {
				$defaults['state'] = '';
			}
		}
		return $defaults;
	}


	/**
	 * Performs validation on this form section and its subsections.
	 */
	function _validate() {
		parent::_validate();
		// Add normalized CC info.
		$this->_subsections['credit_card']->set_default( $this->_subsections['qb_cc_ex']->normalized_value() );
	}

}