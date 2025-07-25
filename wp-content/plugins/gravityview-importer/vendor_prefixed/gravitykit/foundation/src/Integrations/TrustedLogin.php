<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by The GravityKit Team on 25-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Integrations;

use GravityKit\GravityImport\Foundation\Helpers\Arr;
use GravityKit\GravityImport\Foundation\Helpers\Core as CoreHelpers;
use GravityKit\GravityImport\Foundation\Licenses\LicenseManager;
use GravityKit\GravityImport\Foundation\ThirdParty\TrustedLogin\Admin as TrustedLoginAdmin;
use GravityKit\GravityImport\Foundation\ThirdParty\TrustedLogin\Form as TrustedLoginForm;
use GravityKit\GravityImport\Foundation\ThirdParty\TrustedLogin\SupportUser as TrustedLoginSupportUser;
use GravityKit\GravityImport\Foundation\ThirdParty\TrustedLogin\SiteAccess as TrustedLoginSiteAccess;
use GravityKit\GravityImport\Foundation\ThirdParty\TrustedLogin\Logging as TrustedLoginLogging;
use GravityKit\GravityImport\Foundation\ThirdParty\TrustedLogin\Config as TrustedLoginConfig;
use GravityKit\GravityImport\Foundation\ThirdParty\TrustedLogin\Client as TrustedLoginClient;
use GravityKit\GravityImport\Foundation\Logger\Framework as LoggerFramework;
use GravityKit\GravityImport\Foundation\WP\AdminMenu;
use Exception;

class TrustedLogin {
	const ID = 'gk_foundation_trustedlogin';

	const TL_API_KEY = '3b3dc46c0714cc8e';

	/**
	 * Access capabilities.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $_capability = 'manage_options';

	/**
	 * TL Client class instance.
	 *
	 * @since 1.0.0
	 *
	 * @var TrustedLoginClient
	 */
	private $_trustedlogin_client;

	/**
	 * Class instance.
	 *
	 * @since 1.0.0
	 *
	 * @var TrustedLogin
	 */
	private static $_instance;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function __construct() {
		try {
			$this->_trustedlogin_client = new TrustedLoginClient(
				new TrustedLoginConfig( $this->get_config() )
			);
		} catch ( Exception $e ) {
			LoggerFramework::get_instance()->error( 'Unable to initialize TrustedLogin client: ' . $e->getMessage() );

			return;
		}

		try {
			$this->add_gk_submenu_item();
		} catch ( Exception $e ) {
			LoggerFramework::get_instance()->error( 'Unable to add TrustedLogin to the Foundation menu: ' . $e->getMessage() );

			return;
		}

		add_filter( 'gk/foundation/integrations/helpscout/configuration', [ $this, 'add_tl_key_to_helpscout_beacon' ] );
	}

	/**
	 * Returns class instance.
	 *
	 * @since 1.0.0
	 *
	 * @return TrustedLogin
	 */
	public static function get_instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Adds Settings submenu to the GravityKit top-level admin menu.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception TrustedLoginConfig throws an exception when the config object is empty (do not apply to us).
	 *
	 * @return void
	 */
	public function add_gk_submenu_item() {
		$tl_config  = new TrustedLoginConfig( $this->get_config() );
		$tl_logging = new TrustedLoginLogging( $tl_config );
		$tl_form    = new TrustedLoginForm( $tl_config, $tl_logging, new TrustedLoginSupportUser( $tl_config, $tl_logging ), new TrustedLoginSiteAccess( $tl_config, $tl_logging ) );

		$page_title = esc_html__( 'Grant Support Access', 'gk-gravityimport' );
		$menu_title = $page_title;

		AdminMenu::add_submenu_item(
            [
				'page_title'         => $page_title,
				'menu_title'         => $menu_title,
				'capability'         => $this->_capability,
				'id'                 => self::ID,
				'callback'           => [ $tl_form, 'print_auth_screen' ],
				'order'              => 1,
				'hide_admin_notices' => true,
			],
            'bottom'
        );
	}

	/**
	 * Returns TrustedLogin configuration.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_config() {
		$config = [
			'auth'            => [
				'api_key' => self::TL_API_KEY,
			],
			'menu'            => [
				'slug' => false, // Prevent TL from adding a menu item; we'll do it manually in the add_gk_submenu_item() method.
			],
			'role'            => 'administrator',
			'clone_role'      => false,
			'logging'         => [
				'enabled' => false,
			],
			'vendor'          => [
				'namespace'    => self::ID,
				'title'        => 'GravityKit',
				'email'        => 'support+{hash}@gravitykit.com',
				'website'      => 'https://www.gravitykit.com',
				'support_url'  => 'https://www.gravitykit.com/support/',
				'display_name' => 'GravityKit Support',
				'logo_url'     => CoreHelpers::get_assets_url( 'gravitykit-logo.svg' ),
			],
			'register_assets' => true,
			'paths'           => [
				'css' => CoreHelpers::get_assets_url( 'trustedlogin/trustedlogin.css' ),
			],
			'webhook'         => [
				'url'           => 'https://hooks.zapier.com/hooks/catch/28670/bnwjww2/silent/',
				'debug_data'    => true,
				'create_ticket' => true,
			],
		];

		$license_manager = LicenseManager::get_instance();

		foreach ( $license_manager->get_licenses_data() as $license_data ) {
			if ( Arr::get( $license_data, 'products' ) && ! $license_manager->is_expired_license( Arr::get( $license_data, 'expiry' ) ) ) {
				Arr::set( $config, 'auth.license_key', Arr::get( $license_data, 'key' ) );

				break;
			}
		}

		return $config;
	}

	/**
	 * Updates Help Scout beacon with TL access key.
	 *
	 * @since 1.0.0
	 *
	 * @param array $configuration Help Scout beacon configuration data.
	 *
	 * @return array
	 */
	public function add_tl_key_to_helpscout_beacon( $configuration ) {
		Arr::set( $configuration, 'identify.tl_access_key', $this->_trustedlogin_client->get_access_key() );

		return $configuration;
	}
}
