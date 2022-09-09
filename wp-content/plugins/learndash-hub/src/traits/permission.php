<?php

namespace LearnDash\Hub\Traits;

trait Permission {

	/**
	 * Check if the current user have permission for execute an action
	 *
	 * @return bool
	 */
	public function check_permission(): bool {
		if ( ! is_user_logged_in() ) {
			return false;
		}
		$cap = is_multisite() ? 'manage_network_options' : 'manage_options';

		// todo check if the current one in the allow list.
		return current_user_can( $cap );
	}

	/**
	 * A quick hand for verify the nonce.
	 *
	 * @param string $action
	 *
	 * @return bool
	 */
	public function verify_nonce( string $action ): bool {
		if ( ! isset( $_REQUEST['hubnonce'] ) ) {
			return false;
		}

		return wp_verify_nonce( $_REQUEST['hubnonce'], $action );
	}
}
