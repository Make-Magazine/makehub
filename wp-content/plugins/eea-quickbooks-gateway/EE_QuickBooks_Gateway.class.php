<?php if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) { exit( 'No direct script access allowed !' ); }

// Define the plugin directory path and URL.
define('EEA_QUICKBOOKS_PM_PLUGIN_BASENAME', plugin_basename(EEA_QUICKBOOKS_PM_PLUGIN_FILE));
define('EEA_QUICKBOOKS_PM_PLUGIN_URL', plugin_dir_url(EEA_QUICKBOOKS_PM_PLUGIN_FILE));
define(
    'EEA_QUICKBOOKS_ONSITE_PM_URL',
    EEA_QUICKBOOKS_PM_PLUGIN_URL . 'payment_methods' . DS . 'QuickBooks_Onsite' . DS
);
define(
    'EEA_QUICKBOOKS_ONSITE_PM_PATH',
    EEA_QUICKBOOKS_PM_PLUGIN_PATH . 'payment_methods' . DS . 'QuickBooks_Onsite' . DS
);

/**
 *	Class EE_QuickBooks_Gateway
 *
 *	@package 		Event Espresso
 *	@subpackage 	eea-quickbooks-gateway
 *	@author 		Event Espresso
 *	
 */
class EE_QuickBooks_Gateway extends EE_Addon {


	/**
	 *	Register add-on.
	 *
	 *	@access public
	 *	@return void
	 */
	public static function register_addon() {
		// Register this add-on via EE Plugin API.
		EE_Register_Addon::register(
			'QuickBooks_Gateway',
			array(
				'version'          => EEA_QUICKBOOKS_PM_VERSION,
				'min_core_version' => EEA_QUICKBOOKS_PM_MIN_CORE_VERSION,
				'main_file_path'   => EEA_QUICKBOOKS_PM_PLUGIN_FILE,
				'admin_callback'   => 'additional_quickbooks_admin_hooks',
				// Register auto-loaders.
				'autoloader_paths' => array(
					'EE_PMT_Base'                => EE_LIBRARIES . 'payment_methods' . DS . 'EE_PMT_Base.lib.php',
					'EE_PMT_QuickBooks_Onsite'   => EEA_QUICKBOOKS_ONSITE_PM_PATH . 'EE_PMT_QuickBooks_Onsite.pm.php',
                    'EE_QuickBooks_PM_Form'      => EEA_QUICKBOOKS_ONSITE_PM_PATH . 'forms' . DS . 'EE_QuickBooks_PM_Form.form.php',
					'OAuthSimple'                => EEA_QUICKBOOKS_PM_PLUGIN_PATH . 'includes' . DS . 'oauth_simple'
                        . DS . 'OAuthSimple.php',
					'EE_QuickBooks_Billing_Form' => EEA_QUICKBOOKS_PM_PLUGIN_PATH  . 'payment_methods'. DS
                        . 'QuickBooks_Onsite' . DS . 'EE_QuickBooks_Billing_Form.form.php',
                    // Some of the settings in the default 'EE_Credit_Card_Input' does not fit so let's create a new input, similar to the default one.
					'EE_QB_Credit_Card_Input'    => EEA_QUICKBOOKS_PM_PLUGIN_PATH  . 'payment_methods' . DS
                        . 'QuickBooks_Onsite' . DS . 'EE_QB_Credit_Card_Input.input.php'
				),
				'module_paths' => array(
                    EEA_QUICKBOOKS_PM_PLUGIN_PATH  . 'EED_QuickBooks_Oauth.module.php',
                    EEA_QUICKBOOKS_PM_PLUGIN_PATH  . 'EED_QuickBooks_Oauth_v2.module.php'
                ),
				// If plugin update engine is being used for auto-updates. Not needed if PUE is not being used.
				'pue_options'  => array(
					'pue_plugin_slug' => 'eea-quickbooks-gateway',
					'plugin_basename' => EEA_QUICKBOOKS_PM_PLUGIN_BASENAME,
					'checkPeriod'     => '24',
					'use_wp_update'   => FALSE
				),
				'payment_method_paths' => array(
					EEA_QUICKBOOKS_PM_PLUGIN_PATH . 'payment_methods' . DS . 'QuickBooks_Onsite'
				)
			)
		);
	}


	/**
	 *	Setup default data for the add-on.
	 *
	 *	@access public
	 *	@return void
	 */
	public function initialize_default_data() {
		parent::initialize_default_data();

		// Setup default currencies supported by this gateway (if list updated).
		$quickbooks = EEM_Payment_Method::instance()->get_one_of_type( 'QuickBooks_Onsite' );
		// Update if the Payment Method exists.
		if ( $quickbooks ) {
			$currencies = $quickbooks->get_all_usable_currencies();
			$all_related = $quickbooks->get_many_related( 'Currency' );

			if ( $currencies != $all_related ) {
				$quickbooks->_remove_relations( 'Currency' );
				foreach ( $currencies as $currency_obj ) {
					$quickbooks->_add_relation_to( $currency_obj, 'Currency' );
				}
			}
		}
	}


	/**
	 *	Additional admin hooks.
	 *
	 *	@access public
	 *	@return void
	 */
	public static function additional_quickbooks_admin_hooks() {
		// Is this an admin and is not in M-Mode ?
		if ( is_admin() && ! EE_Maintenance_Mode::instance()->level() ) {
			add_filter( 'plugin_action_links', array( 'EE_QuickBooks_Gateway', 'plugin_actions' ), 10, 2 );
		}
	}


	/**
	 *	Add a link to the plugin settings page.
	 *
	 *	@param $links array
	 *	@param $file
	 *	@access public
	 *	@return array
	 */
	public static function plugin_actions( $links, $file ) {
		if ( $file == EEA_QUICKBOOKS_PM_PLUGIN_BASENAME ) {
			// Add before other links.
			array_unshift( $links, '<a href="admin.php?page=espresso_payment_settings">' . __('Settings') . '</a>' );
		}
		return $links;
	}
}
// End of file: EE_QuickBooks_Gateway.class.php
// Location: ../wp-content/plugins/espresso-quickbooks-gateway/