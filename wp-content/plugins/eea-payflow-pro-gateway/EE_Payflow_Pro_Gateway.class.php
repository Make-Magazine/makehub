<?php if ( ! defined( 'EVENT_ESPRESSO_VERSION' )) { exit('NO direct script access allowed'); }


define( 'EEA_PAYFLOW_PRO_PLUGIN_BASENAME', plugin_basename( EEA_PAYFLOW_PRO_PLUGIN_FILE ) );
define( 'EEA_PAYFLOW_PRO_PLUGIN_URL', plugin_dir_url( EEA_PAYFLOW_PRO_PLUGIN_FILE ) );

/**
 *
 * Class  EE_Payflow_Pro_Gateway
 *
 * @package			Event Espresso
 * @subpackage		espresso-payflow-pro-gateway
 * @author			Event Espresso
 * @version		 	$VID:$
 */
class EE_Payflow_Pro_Gateway extends EE_Addon {

	/**
	 *	class constructor
	 *
	 *	@return EE_Payflow_Pro_Gateway
	 */
	function __construct() {

	}


	/**
	 *	Register this add-on with EE.
	 *
	 *	@return void
	 */
	public static function register_addon() {
		// Register addon via EE Plugin API.
		EE_Register_Addon::register(
			'Payflow_Pro_Gateway',
			array(
				'version' => EEA_PAYFLOW_PRO_VERSION,
				'min_core_version' => '4.6.0.p',
				'main_file_path' => EEA_PAYFLOW_PRO_PLUGIN_FILE,
				'admin_callback' => 'additional_payflow_pro_admin_hooks',
				// Register autoloaders.
				'autoloader_paths' => array(
					'EE_PMT_Base' => EE_LIBRARIES . 'payment_methods' . DS . 'EE_PMT_Base.lib.php',
					'EE_PMT_PayFlow_Pro_Onsite' => EEA_PAYFLOW_PRO_PLUGIN_PATH . 'payment_methods' . DS . 'Payflow_Pro_Onsite' . DS . 'EE_PMT_Payflow_Pro_Onsite.pm.php',
				),
				// If plugin update engine is being used for auto-updates. not needed if PUE is not being used.
				'pue_options' => array(
					'pue_plugin_slug' => 'eea-payflow-pro-gateway',
					'plugin_basename' => EEA_PAYFLOW_PRO_PLUGIN_BASENAME,
					'checkPeriod' => '24',
					'use_wp_update' => FALSE,
				),
				'payment_method_paths' => array(
					EEA_PAYFLOW_PRO_PLUGIN_PATH . 'payment_methods' . DS . 'Payflow_Pro_Onsite'
				),
		));
	}


	/**
	 * 	Setup default data for the addon.
	 *
	 *  @access public
	 *  @return void
	 */
	public function initialize_default_data() {
		parent::initialize_default_data();

		// Update the currencies supported by this gateway (if changed).
		$payflow = EEM_Payment_method::instance()->get_one_of_type( 'Payflow_Pro_Onsite' );
		// Update If the payment method already exists.
		if ( $payflow ) {
			$currencies = $payflow->get_all_usable_currencies();
			$all_related = $payflow->get_many_related( 'Currency' );

			if ( ($currencies != $all_related) ) {
				$payflow->_remove_relations( 'Currency' );
				foreach ( $currencies as $currency_obj ) {
					$payflow->_add_relation_to( $currency_obj, 'Currency' );
				}
			}
		}
	}


	/**
	 * 	Additional admin hooks.
	 *
	 *  @access public
	 *  @return void
	 */
	public static function additional_payflow_pro_admin_hooks() {
		// Is admin and not in M-Mode ?
		if ( is_admin() && ! EE_Maintenance_Mode::instance()->level() ) {
			add_filter( 'plugin_action_links', array( 'EE_Payflow_Pro_Gateway', 'plugin_actions' ), 10, 2 );
		}
	}


	/**
	 * Add a settings link to the Plugins page, so people can go straight from the plugin page to the settings page.
	 *
	 * @param $links
	 * @param $file
	 * @return array
	 */
	public static function plugin_actions( $links, $file ) {
		if ( $file == EEA_PAYFLOW_PRO_PLUGIN_BASENAME ) {
			// Before other links.
			array_unshift( $links, '<a href="admin.php?page=espresso_payment_settings">' . __('Settings') . '</a>' );
		}
		return $links;
	}

}
// End of file EE_Payflow_Pro_Gateway.class.php
// Location: wp-content/plugins/espresso-payflow-pro-gateway/EE_Payflow_Pro_Gateway.class.php