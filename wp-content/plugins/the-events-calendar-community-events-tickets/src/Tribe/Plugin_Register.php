<?php

/**
 * Class Tribe__Events__Community__Tickets__Plugin_Register
 *
 * @since 4.6
 */
class  Tribe__Events__Community__Tickets__Plugin_Register extends Tribe__Abstract_Plugin_Register {

	/**
	 * Community Tickets' main class name.
	 *
	 * @var string $main_class
	 */
	protected $main_class = 'Tribe__Events__Community__Tickets__Main';

	/**
	 * Community Tickets' requirements.
	 *
	 * @see   \tribe_register_community_tickets()
	 *
	 * @var array $dependencies
	 */
	protected $dependencies = [
		'parent-dependencies' => [
			'Tribe__Events__Main'  => '5.0.0',
			'Tribe__Tickets__Main' => '4.11.0',
		],
		'co-dependencies'     => [
			'Tribe__Tickets_Plus__Main'      => '4.10.6-dev',
			'Tribe__Events__Community__Main' => '4.6.3-dev',
		],
	];

	/**
	 * Constructor method.
	 *
	 * @since 4.6
	 */
	public function __construct() {
		$this->base_dir = EVENTS_COMMUNITY_TICKETS_FILE;
		$this->version  = Tribe__Events__Community__Tickets__Main::VERSION;

		add_filter( 'tribe_register_Tribe__Events__Community__Tickets__Main_plugin_dependencies', [ $this, 'add_woo_as_dependency_if_able_via_common' ] );

		$this->inform_dependency_manager_of_woocommerce();
		$this->register_plugin();
	}

	/**
	 * Allow Common's Dependency manager/notices to handle requiring WooCommerce by informing
	 * it of which version is active, if active at all.
	 *
	 * @since 4.7.1
	 */
	private function inform_dependency_manager_of_woocommerce() {
		$woo = tribe_community_tickets_get_woocommerce_info_array();

		/**
		 * Tell that it's active and what version it is.
		 *
		 * @var Tribe__Dependency $dependency
		 */
		$dependency = tribe( Tribe__Dependency::class );

		$woo_version = null;

		if (
			function_exists( 'WC' )
			&& ! empty( WC()->version )
		) {
			$woo_version = WC()->version;

			$dependency->add_registered_plugin( $woo['class'], $woo_version, $woo['path'] );
			$dependency->add_active_plugin( $woo['class'], $woo_version, $woo['path'] );
		}
	}

	/**
	 * Add WooCommerce as a co-dependency via filter instead of class property to avoid grammar errors in the notice.
	 *
	 * @todo  Add WooCommerce as class property once Common v4.9.17 is far enough in the past.
	 *
	 * @since 4.7.4
	 *
	 * @see   \tribe_community_tickets_get_woocommerce_info_array()
	 *
	 * @param array $dependencies An array of dependencies for the plugins. These can include parent, add-on and other dependencies.
	 *
	 * @return array
	 */
	public function add_woo_as_dependency_if_able_via_common( $dependencies ) {
		if (
			! empty( $GLOBALS['tribe-common-info']['version'] )
			&& -1 !== version_compare( $GLOBALS['tribe-common-info']['version'], '4.9.17' )
			&& is_array( $dependencies )
			&& is_array( $dependencies['co-dependencies'] )
			&& ! in_array( 'WooCommerce', $dependencies['co-dependencies'], true )
		) {
			$dependencies['co-dependencies']['WooCommerce'] = '3.2.0';
		}

		return $dependencies;
	}
}