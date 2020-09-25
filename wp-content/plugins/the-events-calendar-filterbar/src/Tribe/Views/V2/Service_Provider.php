<?php
namespace Tribe\Events\Filterbar\Views\V2;

use \Tribe__Events__Filterbar__View;

/**
 * The main service provider for Filterbar support and additions to the Views V2 functions.
 *
 * @since   4.9.0
 * @package Tribe\Events\Filterbar\Views\V2
 */
class Service_Provider extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.9.0
	 */
	public function register() {
		require_once Tribe__Events__Filterbar__View::plugin_path( 'src/functions/views/provider.php' );

		if ( ! tribe_events_views_v2_is_enabled() ) {
			return;
		}

		$this->container->singleton( Filters::class, Filters::class );

		$this->register_hooks();
		$this->register_assets();

		// Register the SP on the container
		$this->container->singleton( 'filterbar.views.v2.provider', $this );
		$this->container->singleton( static::class, $this );
	}

	/**
	 * Registers the provider handling all the 1st level filters and actions for Views v2.
	 *
	 * @since 4.9.0
	 */
	protected function register_assets() {
		$assets = new Assets( $this->container );
		$assets->register();

		$this->container->singleton( Assets::class, $assets );

		/**
		 * @todo Remove this once the facelift goes live.
		 */
		if ( ! tribe_events_filterbar_views_v2_is_enabled() ) {
			return;
		}

		// register filterbar facelift stuff here.
	}

	/**
	 * Registers the provider handling all the 1st level filters and actions for Views v2.
	 *
	 * @since 4.9.0
	 */
	protected function register_hooks() {
		$hooks = new Hooks( $this->container );
		$hooks->register();

		// Allow Hooks to be removed, by having the them registered to the container
		$this->container->singleton( Hooks::class, $hooks );
		$this->container->singleton( 'filterbar.views.v2.hooks', $hooks );

		/**
		 * @todo Remove this once the facelift goes live.
		 */
		if ( ! tribe_events_filterbar_views_v2_is_enabled() ) {
			return;
		}

		// register filterbar facelift stuff here.
	}

}
