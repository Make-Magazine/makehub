<?php if ( ! defined( 'EVENT_ESPRESSO_VERSION' )) { exit('NO direct script access allowed'); }


/**
 *
 * Class  EE_PMT_Payflow_Pro_Onsite
 *
 * @package			Event Espresso
 * @subpackage		espresso-payflow-pro-gateway
 * @author			Event Espresso
 * @version		 	$VID:$
 */
 class EE_PMT_Payflow_Pro_Onsite extends EE_PMT_Base {


	/**
	 * Path to the templates folder for the Payflow Pro PM
	 * @var string
	 */
	protected $_template_path = NULL;


	/**
	 *
	 * @param EE_Payment_Method $pm_instance
	 * @throws \EE_Error
	 * @return \EE_PMT_Payflow_Pro_Onsite
	 */
	public function __construct( $pm_instance = NULL ) {
		$this->_pretty_name = __( 'Payflow Pro', 'event_espresso' );
		$this->_default_description = __( 'Please provide the following billing information.', 'event_espresso' );
		$this->_template_path = dirname( __FILE__ ) . DS . 'templates' . DS;
		$this->_requires_https = true;
		$this->_cache_billing_form = true;

		require_once( $this->file_folder() . 'EEG_Payflow_Pro_Onsite.gateway.php' );
		$this->_gateway = new EEG_Payflow_Pro_Onsite();

		parent::__construct( $pm_instance );
	}


	/**
	 * Generate a new payment settings form.
	 *
	 * @return EE_Payment_Method_Form
	 */
	public function generate_new_settings_form() {
		return new EE_Payment_Method_Form( array(
			'extra_meta_inputs' => array(
				'vendor' => new EE_Text_Input( array(
					'html_label_text' => sprintf( __('Vendor ID %s', 'event_espresso'), $this->get_help_tab_link() ),
					'required' => true
				)),
				'user' => new EE_Text_Input( array(
					'html_label_text' => sprintf( __('User ID %s', 'event_espresso'), $this->get_help_tab_link() ),
					'required' => true
				)),
				'password' => new EE_Password_Input( array(
					'html_label_text' => sprintf( __('Password %s', 'event_espresso'), $this->get_help_tab_link() ),
					'required' => true
				)),
				'partner' => new EE_Text_Input( array(
					'html_label_text' => sprintf( __('Partner ID %s', 'event_espresso'), $this->get_help_tab_link() ),
					'html_help_text' => __('If left blank the default Partner ID will be used: "PayPal"', 'event_espresso'),
					'required' => false
				)),
				'transaction_method' => new EE_Select_Input(
					array(
						'S' => __('Sale', 'event_espresso'),
						'A' => __('Authorization with Delayed Capture', 'event_espresso')
					),
					array(
						'html_label_text' => sprintf( __('Process Transactions as: %s', 'event_espresso'), $this->get_help_tab_link() ),
						'required' => true,
						'default' => 'S'
					)
				)
			)
		));
	}


	/**
	 * Creates a billing form for this payment method type.
	 * @param \EE_Transaction $transaction
	 * @return \EE_Billing_Info_Form
	 */
	public function generate_new_billing_form( EE_Transaction $transaction = NULL, $extra_args = array() ) {
		require_once( $this->file_folder() . 'EEA_Payflow_Pro_Billing_Form.form.php' );
		$form = new EEA_Payflow_Pro_Billing_Form( $this, $this->_pm_instance );
		return $this->apply_billing_form_debug_settings( $form );
	}


	/**
	 *	Possibly adds debug content to Payflow Pro checkout billing form.
	 *
	 *	@param \EE_Billing_Info_Form $billing_form
	 *	@return \EE_Billing_Info_Form
	 */
	public function apply_billing_form_debug_settings( EE_Billing_Info_Form $billing_form ) {
		if ( $this->_pm_instance->debug_mode() ) {
			$billing_form->add_subsections(
				array( 'fyi_about_autofill' => $billing_form->payment_fields_autofilled_notice_html() ),
				'account_type'
			);
			$billing_form->add_subsections(
				array( 'debug_content' => new EE_Form_Section_HTML_From_Template( $this->_template_path . 'payflow_pro_debug_info.template.php' )),
				'account_type'
			);
			$billing_form->get_input( 'credit_card' )->set_default( '4111111111111111' );
			$billing_form->get_input( 'card_cvv' )->set_default( '117' );
			$billing_form->get_input( 'exp_year' )->set_default( intval( date('y') ) + 6 );
		}
		return $billing_form;
	}


	/**
	 * Adds the help tab
	 *
	 * @see EE_PMT_Base::help_tabs_config()
	 * @return array
	 */
	public function help_tabs_config() {
		return array(
			$this->get_help_tab_name() => array(
				'title' => __( 'Payflow Pro Settings', 'event_espresso' ),
				'filename' => 'payment_methods_overview_payflow_pro'
			),
		);
	}


	/**
	 * Adjust the billing form data.
	 * Need Country and State abbreviations, not full names.
	 *
	 * @param billing_form
	 * @return array
	 */
	protected function _get_billing_values_from_form( $billing_form ) {
		$billing_values = parent::_get_billing_values_from_form( $billing_form );
		$billing_values[ 'country' ] = $billing_form->get_input_value( 'country' );

		$state = EEM_State::instance()->get_col( array( array('STA_name' => $billing_values['state']), 'limit' => 1), 'STA_abbrev' );
		$billing_values[ 'state' ] = $state[0];
		return $billing_values;
	}

 }