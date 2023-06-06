<?php
class EMIO_OAuth_API {

	public static $option_name = 'emio_oauth';
	public static $service_name = 'EM I/O OAuth 2.0';

	/**
	 * Loads the service credentials into an abstract client api object. If a user ID is supplied and there's an issue retrieving an access token, a WP_Error will be returned.
	 * @param int $user_id The User ID in WordPress
	 * @param int $api_user_id The ID of the account in Google (i.e. the email)
	 * @return EMIO_OAuth_API_Client|WP_Error
	 */
	public static function get( $user_id = 0, $api_user_id = 0 ) {
		try {
			//set up the client
			$client_class = get_called_class().'_Client';
			$client = new $client_class(); /* @var EMIO_OAuth_API_Client $client */
			//load user access token
			if( $user_id !== false ) {
				if ( empty($user_id) ) $user_id = get_current_user_id();
				$client->load_token( $user_id, $api_user_id );
			}
		} catch ( EMIO_Exception $ex ) {
			$messages = $ex->get_messages();
			$WP_Error = new WP_Error();
			foreach( $messages as $code => $message ){
				$WP_Error->add($code, $message);
			}
			return $WP_Error;
		}
		return $client;
	}
	
	public static function get_user_tokens( $user_id = false ){
		if( empty($user_id) ) $user_id = get_current_user_id();
		return get_user_meta( $user_id, 'emio_oauth_'.static::$option_name, true );
	}

	public static function get_oauth_callback_url(){
		$client_class = get_called_class().'_Client'; /* @var EMIO_OAuth_API_Client $client_class */
		return $client_class::get_oauth_callback_url();
	}

	/**
	 * Includes and calls the code required to handle a callback from FB to store user auth token.
	 */
	public static function oauth_authorize() {
		global $EM_Notices;
		if( !empty($EM_Notices) ) $EM_Notices = new EM_Notices();

		$client = static::get(false);
		if( !is_wp_error($client) && !empty($_REQUEST['code']) ){
			try {
				$client->request( $_REQUEST['code'] );
				$EM_Notices->add_confirm( sprintf( esc_html__( 'Your account has been successfully connected with %s!', 'events-manager-io' ), static::$service_name ), true);
			} catch ( EMIO_Exception $e ){
				$EM_Notices->add_error( sprintf( esc_html__( 'There was an error connecting to %s: %s', 'events-manager-io' ), static::$service_name, '<code>'.$e->getMessage().'</code>' ), true );
			}
		}else{
			if( is_wp_error($client) ){
				$EM_Notices->add_error($client->get_error_messages(), true);
			}else{
				$EM_Notices->add_error( sprintf( esc_html__( 'There was an error connecting to %s: %s', 'events-manager-io' ), static::$service_name, '<code>No Authorization Code Provided</code>'), true );
			}
		}
		// Redirect to import page
		$query_args = array( 'page' => 'events-manager-io-settings' );
		$url = add_query_arg( $query_args, admin_url( 'admin.php' ) ) . '#connect';
		wp_redirect( $url );
		die();
	}

	/**
	 * Handles disconnecting a user from one or all their connected Google accounts, attempting to revoke their key in the process.
	 */
	public static function oauth_disconnect(){
		global $EM_Notices;
		if( !empty($EM_Notices) ) $EM_Notices = new EM_Notices();

		$user_tokens = get_user_meta( get_current_user_id(), 'emio_oauth_'.static::$option_name, true );
		$accounts_to_disconnect = array();
		if( empty($_REQUEST['user']) && !empty($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], 'emio-'. static::$option_name .'-disconnect') ){
			$accounts_to_disconnect = array_keys($user_tokens);
		}elseif( !empty($_REQUEST['account']) && !empty($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], 'emio-'. static::$option_name .'-disconnect-'.$_REQUEST['account']) ){
			if( !empty($user_tokens[$_REQUEST['account']]) ){
				$accounts_to_disconnect[] = $_REQUEST['account'];
			}
		}else{
			$EM_Notices->add_error('Missing nonce, please contact your administrator.', true);
		}
		if( !empty($accounts_to_disconnect) ){
			$errors = $disconnected_accounts = array();
			foreach( $accounts_to_disconnect as $account_id ){
				$client = static::get( get_current_user_id(), $account_id);
				try {
					if( !is_wp_error($client) ) $client->revoke();
					$disconnected_accounts[] = $account_id;
					unset($user_tokens[$account_id]);
				} catch ( EMIO_Exception $ex ){
					$account_name = !empty( $client->token->email ) ? $client->token->email : $client->token->name;
					$errors[] = "<em>$account_name</em> - " . $ex->getMessage();
				}
			}
			if( !empty($disconnected_accounts) ){
				if( empty($user_tokens) ){
					delete_user_meta( get_current_user_id(), 'emio_oauth_'.static::$option_name );
				}else{
					update_user_meta( get_current_user_id(), 'emio_oauth_'.static::$option_name, $user_tokens );
				}
				$success = _n('You have successfully disconnected from your %s account.', 'You have successfully disconnected from your %s accounts.', count($accounts_to_disconnect), 'events-manager-io');
				$EM_Notices->add_confirm(sprintf($success, static::$service_name), true);
			}
			if( !empty($errors) ){
				$error_msg = sprintf( esc_html__('There were some issues whilst disconnecting from your %s account(s) :', 'events-manager-io'), static::$service_name );
				array_unshift( $errors, $error_msg );
				$EM_Notices->add_error( $errors, true );
			}
		}

		// Redirect to import page
		$query_args = array( 'page' => 'events-manager-io-settings' );
		$url = add_query_arg( $query_args, admin_url( 'admin.php' ) ) . '#connect';
		wp_redirect( $url );
		die();
	}
}