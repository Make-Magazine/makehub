<?php

namespace LearnDash\Hub\Controller;

use Hub\Traits\Time;
use LearnDash\Hub\Component\API;
use LearnDash\Hub\Framework\Controller;

/**
 * Main Controller, this will register a root page into wp-admin.
 */
class Main_Controller extends Controller {
	use Time;

	/**
	 * Projects constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->register_page(
			__( 'LearnDash Hub', 'learndash_hub' ),
			'learndash-hub',
			array( new Projects_Controller(), 'display' )
		);
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_filter( 'custom_menu_order', '__return_true' );
		add_filter( 'menu_order', array( $this, 'menu_order' ) );
	}

	/**
	 * Position the menu.
	 *
	 * @param array $menu_order The current admin menu order.
	 */
	public function menu_order( array $menu_order ) {
		global $submenu;
		if ( isset( $submenu['learndash-hub'] ) ) {
			$submenu['learndash-hub'][0][0] = __( 'Add-ons', 'learndash-hub' );
		}
		$learndash_key = array_search( 'learndash-lms', $menu_order, true );
		if ( empty( $learndash_key ) ) {
			// just return things by default.
			return $menu_order;
		}
		$ld_hub_key = array_search( 'learndash-hub', $menu_order, true );
		unset( $menu_order[ $ld_hub_key ] );

		array_splice( $menu_order, $learndash_key, 0, array( 'learndash-hub' ) );

		return $menu_order;
	}

	/**
	 * All the scripts should be registered here, later we can use it when render the view.
	 */
	public function register_scripts() {
		$scripts = array(
			'projects',
			'settings',
		);
		foreach ( $scripts as $script ) {
			wp_register_script(
				'learndash-hub-' . $script,
				hub_asset_url( '/assets/scripts/' . $script . '.js' ),
				array(
					'react',
					'react-dom',
					'wp-i18n',
				),
				HUB_VERSION,
				true
			);
		}
		wp_register_style(
			'learndash-hub-fontawesome',
			hub_asset_url( '/assets/css/fontawesome.min.css' ),
			array(),
			HUB_VERSION
		);
		wp_register_style(
			'learndash-hub',
			hub_asset_url( '/assets/css/app.css' ),
			array( 'learndash-hub-fontawesome' ),
			HUB_VERSION
		);
	}
}
