<?php if ( ! defined('EVENT_ESPRESSO_VERSION') ) { exit('No direct script access allowed'); }

/**
 *
 * Class EEA_QB_Billing_Form
 *
 *
 * @package			Event Espresso
 * @subpackage		eea-quickbooks-gateway
 * @author			Event Espresso
 *
 */
class EE_QB_Credit_Card_Input extends EE_Form_Input_Base {

	/**
	 * @param array $input_settings
	 */
	function __construct( $input_settings = array() ) {
		$this->_set_display_strategy( new EE_Text_Input_Display_Strategy() );
		$this->_set_normalization_strategy( new EE_Text_Normalization() );
		$this->_add_validation_strategy( new EE_Credit_Card_Validation_Strategy( isset( $input_settings[ 'validation_error_message' ] ) ? $input_settings[ 'validation_error_message' ] : NULL ) );
		$this->set_sensitive_data_removal_strategy( new EE_Credit_Card_Sensitive_Data_Removal() );
		parent::__construct($input_settings);
	}
}