<?php
/**
 * Plugin admin file handle admin ajax requests.
 *
 * @package    BPPTC
 * @subpackage Admin
 */

/**
 * If file accessed directly it will exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BPPTC_Admin_Ajax_handler
 */
class BPPTC_Admin_Ajax_handler {

	/**
	 * Boot class
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Setup callbacks
	 */
	private function setup() {
		add_action( 'wp_ajax_bpptc_get_users_list', array( $this, 'user_auto_suggest_handler' ) );
	}

	/**
	 * User response builder
	 */
	public function user_auto_suggest_handler() {
		$search_term = isset( $_POST['q'] ) ? sanitize_text_field( $_POST['q'] ) : '';
		$excluded    = ! empty( $_POST['included'] ) ? wp_parse_id_list( $_POST['included'] ) : false;

		$users = bp_core_get_users(
			array(
				'search_terms' => $search_term,
				'exclude'      => $excluded,
			)
		);

		$list = array();

		if ( is_array( $users ) && ! empty( $users['users'] ) ) {
			foreach ( $users['users'] as $user ) {
				$list[] = array(
					'label' => $user->display_name,
					'url'   => bpptc_get_user_url( $user->ID ),
					'id'    => $user->ID,
				);
			}
		}

		echo wp_json_encode( $list );
		exit( 0 );
	}
}

BPPTC_Admin_Ajax_handler::boot();

