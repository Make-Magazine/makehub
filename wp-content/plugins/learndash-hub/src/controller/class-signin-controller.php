<?php

namespace LearnDash\Hub\Controller;

use LearnDash\Hub\Framework\Controller;
use LearnDash\Hub\Traits\License;
use LearnDash\Hub\Traits\Permission;

/**
 * This controller only appear if user not signed in.
 */
class Signin_Controller extends Controller {
	use Permission;
	use License;

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->register_page(
			__( 'LearnDash Hub', 'learndash_hub' ),
			'learndash-hub',
			array( $this, 'display' )
		);
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'wp_ajax_ld_hub_verify_and_save_license', array( $this, 'verify_license' ) );
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
	 * Ajax endpoint to verifying the license.
	 */
	public function verify_license() {
		if ( ! $this->check_permission() ) {
			return;
		}

		if ( ! $this->verify_nonce( 'ld_hub_verify_license' ) ) {
			return;
		}

		//phpcs:ignore
		$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		//phpcs:ignore
		$key   = isset( $_POST['license_key'] ) ? sanitize_text_field( wp_unslash( $_POST['license_key'] ) ) : '';

		if ( empty( $email ) || empty( $key ) ) {
			wp_send_json_error( __( 'Please provide a valid email and license key.', 'learndash-hub' ) );
		}

		$ret = $this->get_api()->verify_license( $email, $key );
		if ( is_wp_error( $ret ) ) {
			wp_send_json_error( $ret->get_error_message() );
		}
		$lists = get_site_option( 'learndash_hub_access_list' );
		if ( ! is_array( $lists ) ) {
			$lists = array();
		}
		$lists[ get_current_user_id() ] = array(
			'allow'     => array( 'dashboard', 'projects', 'billing', 'settings' ),
			'is_master' => true,
		);
		update_site_option( 'learndash_hub_access_list', $lists );
		wp_send_json_success();
	}

	/**
	 * Register the scripts
	 */
	public function register_scripts() {
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

	/**
	 * Display the login.
	 */
	public function display() {
		wp_enqueue_style( 'learndash-hub' );
		$this->render( 'signin' );
	}
}
