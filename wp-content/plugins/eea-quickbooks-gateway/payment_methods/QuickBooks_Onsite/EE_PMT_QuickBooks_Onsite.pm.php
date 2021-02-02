<?php if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) { exit( 'No direct script access allowed !' ); }


/**
 * 	Class  EE_PMT_QuickBooks_Onsite
 *
 *	@package 		Event Espresso
 *	@subpackage 	eea-quickbooks-gateway
 *	@author 		Event Espresso
 *	
 */
class EE_PMT_QuickBooks_Onsite extends EE_PMT_Base {

	/**
	 *  Path to the templates folder for the QuickBooks payment method.
	 *	@var string
	 */
	protected $_template_path = NULL;

	/**
	 *	Class constructor.
	 *
	 *	@param EE_Payment_Method $pm_instance
	 *	@throws \EE_Error
	 *	@return \EE_PMT_QuickBooks_Onsite
	 */
	public function __construct( $pm_instance = NULL ) {
		$this->_template_path = dirname( __FILE__ ) . DS . 'templates' . DS;
		$this->_default_description = __( 'Please provide the following billing information.', 'event_espresso' );
		$this->_pretty_name = __( 'QuickBooks', 'event_espresso' );
		$this->_cache_billing_form = TRUE;
		$this->_requires_https = TRUE;

		// Load gateway.
		require_once( EEA_QUICKBOOKS_PM_PLUGIN_PATH  . 'payment_methods' . DS .  'QuickBooks_Onsite' . DS . 'EEG_QuickBooks_Onsite.gateway.php' );
		$this->_gateway = new EEG_QuickBooks_Onsite();

		parent::__construct( $pm_instance );

		// Load Scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_quickbooks_payment_scripts' ) );
	}


	/**
	 *  Generate a new payment settings form.
	 *
	 *	@return EE_Payment_Method_Form
	 */
	public function generate_new_settings_form() {
		$pms_form = new EE_QuickBooks_PM_Form($this, $this->_pm_instance);

		return $pms_form;
	}


	/**
	 *  Creates a billing form for this payment method type.
	 *
	 *	@param \EE_Transaction $transaction
	 *	@return \EE_Billing_Info_Form
	 */
	public function generate_new_billing_form( EE_Transaction $transaction = NULL, $extra_args = array() ) {
		$form = new EE_QuickBooks_Billing_Form( $this, $this->_pm_instance );
		return $this->generate_billing_form_debug_content( $form );
	}


	/**
	 *  Possibly adds debug content to the billing form.
	 *
	 *	@param \EE_Billing_Info_Form $billing_form
	 *	@return \EE_Billing_Info_Form
	 */
	public function generate_billing_form_debug_content( EE_Billing_Info_Form $billing_form ) {
		if ( $this->_pm_instance->debug_mode()  || $this->_pm_instance->get_extra_meta( 'test_transactions', TRUE, FALSE ) ) {
			$billing_form->add_subsections(
				array( 'fyi_about_autofill' => $billing_form->payment_fields_autofilled_notice_html() ),
				'account_type'
			);
			$billing_form->add_subsections(
				array( 'debug_content' => new EE_Form_Section_HTML_From_Template( $this->_template_path . 'quickbooks_debug_info.template.php' )),
				'account_type'
			);

			$billing_form->get_input('credit_card')->set_default( '4111111111111111' );
			$billing_form->get_input('exp_month')->set_default( '06' );
			$billing_form->get_input('exp_year')->set_default( date("Y") + 2 );
			$billing_form->get_input('cvv')->set_default( '123' );
			$billing_form->get_input('cc_name')->set_default( 'Test Customer' );
		}
		return $billing_form;
	}


	/**
	 *  Check if Connected to QuickBooks.
	 *
	 *	@return boolean
	 */
	public function is_oauthed() {
		if ( ! $this->_pm_instance ) {
			return false;
		}
		$oauth_token = $this->_pm_instance->get_extra_meta( 'oauth_token', true );
        $oauth_version = $this->_pm_instance->get_extra_meta('oauth_version', true);
        $refresh_token = $this->_pm_instance->get_extra_meta('refresh_token', true);
		if (($oauth_version === 'v1a' && $oauth_token) || ($oauth_version === 'v2' && $refresh_token)) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 *  Reset QuickBooks connection settings.
	 *
	 *	@return void
	 */
	public function reset_qb_settings() {
		// Delete the Token data.
		$this->_pm_instance->delete_extra_meta( 'oauth_token' );
		$this->_pm_instance->delete_extra_meta( 'oauth_token_secret' );
		$this->_pm_instance->delete_extra_meta( 'connected_on' );
		$this->_pm_instance->delete_extra_meta( 'realmId' );
        // OAuth 2.0:
        $this->_pm_instance->delete_extra_meta('access_token');
		$this->_pm_instance->delete_extra_meta('refresh_token');
		$this->_pm_instance->delete_extra_meta('x_refresh_token_expires_on');
		$this->_pm_instance->delete_extra_meta('expires_on');
		$this->_pm_instance->delete_extra_meta('token_type');
	}


	/**
	 *  Load the scripts needed for the QiuckBooks admin pages.
	 *
	 * @return void
	 */
	public function enqueue_quickbooks_payment_scripts() {
		if ( $this->_pm_instance->debug_mode() ) {
			$environment = 'sandbox';
			$token_request_url = 'https://sandbox.api.intuit.com/quickbooks/v4/payments/tokens';
		} else {
			$environment = 'production';
			$token_request_url = 'https://api.intuit.com/quickbooks/v4/payments/tokens';
		}

		$trans_args = array(
			'qb_connected' => $this->is_oauthed(),
			'token_request_url' => $token_request_url,
			'payment_method_slug' => $this->_pm_instance->slug(),
			'invalid_SPCO_submit_button' => __( 'The Registration Form Submit button could not be located! Please refresh the page and try again or contact support.', 'event_espresso' ),
			'no_qb_args_error' => __( 'It appears that QuickBooks transaction parameters were not loaded properly! Please refresh the page and try again or contact support. QuickBooks payments will not be processed.', 'event_espresso' ),
			'qb_not_connected' => __( 'A connection to QuickBooks is required in order to process payments. This can be setup in the PM settings. QuickBooks payments will not be processed.', 'event_espresso' ),
			'missing_cc' => __( 'Please provide a credit card.', 'event_espresso' ),
            'tokenize_error' => __('Unable to tokenize the card', 'event_espresso' )
		);
		wp_enqueue_script( 'eea_quickbooks_pm_js', EEA_QUICKBOOKS_PM_PLUGIN_URL . 'scripts' . DS . 'eea-quickbooks-gtw.js', array(), EEA_QUICKBOOKS_PM_VERSION, true );
		// Localize the script with our transaction data.
		wp_localize_script( 'eea_quickbooks_pm_js', 'EEA_QUICKBOOKS_ARGS', $trans_args);
	}


	/**
	 *  Adds a help tab.
	 *
	 *	@see EE_PMT_Base::help_tabs_config()
	 *	@return array
	 */
	public function help_tabs_config() {
		$site_url = site_url();
		$site_url = trailingslashit( $site_url );

		return array(
			$this->get_help_tab_name() => array(
				'title' => __( 'QuickBooks Settings', 'event_espresso' ),
				'filename' => 'payment_methods_overview_quickbooks',
				'template_args' => array(
					'redirect_uri' => $site_url,
				)
			)
		);
	}


	/**
	 *	Get all active US and Canada states.
	 *
	 *	@return array
	 */
	public function get_state_abbrv_list() {
		$state_options = array();
		$states = EEM_State::instance()->get_all_active_states();
		if ( ! empty( $states )) {
			foreach( $states as $state ){
				if ( $state instanceof EE_State ) {
					$state_options[ $state->country()->name() ][ $state->abbrev() ] = $state->name();
				}
			}
		} else {
			$state_options = array();
		}
		return $state_options;
	}


    /**
     *  Check if the slug is present/right and the PM is available/valid.
     *
     * @param string $pm_slug
     * @param string $return_type ['exit', 'close']
     * @return void|array
     */
    public static function is_pm_valid($pm_slug, $return_type = '')
    {
        $the_pm_slug = null;
        if (is_array($pm_slug) && isset($pm_slug['submitted_pm'])) {
            // OAuth v1 and v2
            $the_pm_slug = sanitize_text_field($pm_slug['submitted_pm']);
        } elseif (is_array($pm_slug) && isset($pm_slug['pmslg'])) {
            // OAuth v1
            $the_pm_slug = sanitize_text_field($pm_slug['pmslg']);
        } elseif (is_string($pm_slug)) {
            $the_pm_slug = sanitize_text_field($pm_slug);
        }

        $quickbooks_pm = EEM_Payment_Method::instance()->get_one_by_slug($the_pm_slug);
        $no_pm_msg = esc_html__('Could not specify the payment method.', 'event_espresso');
        if ($quickbooks_pm instanceof EE_Payment_Method) {
            return $quickbooks_pm;
        } else {
            switch ($return_type) {
                case 'exit':
                    echo json_encode(array('qb_error' => $no_pm_msg));
                    exit();
                case 'close':
                    EE_QuickBooks_PM_Form::close_oauth_window($no_pm_msg);
                default:
                    return array('qb_error' => $no_pm_msg);
            }
        }
    }
}