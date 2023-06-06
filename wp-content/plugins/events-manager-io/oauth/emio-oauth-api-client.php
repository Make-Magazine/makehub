<?php
class EMIO_OAuth_API_Client {

	/**
	 * @var string
	 */
	protected static $service_name = 'OAuth';
	/**
	 * @var string
	 */
	protected static $option_name = 'oauth';
	/**
	 * @var string
	 */
	protected static $token_class = 'EMIO_OAuth_API_Token';

	/**
	 * @var string
	 */
	public $id = '';
	/**
	 * @var string
	 */
	public $secret = '';
	/**
	 * @var string
	 */
	public $scope = '';
	/**
	 * @var
	 */
	public $client;

	/**
	 * @var EMIO_OAuth_API_Token
	 */
	public $token;
	/**
	 * @var string
	 */
	public $user_id = '';
	/**
	 * @var bool
	 */
	public $authorized = false;

	/**
	 * The URL that'll be used to request an authorization code from the user. Can include strings CLIENT_ID, ACCESS_SCOPE and REDIRECT_URI which will be replaced dynamically.
	 * @var string
	 */
	public $oauth_authorize_url = '';
	/**
	 * Required by child class unless it overrides the request_access_token() method.
	 * @var string
	 */
	public $oauth_request_token_url = '';
	/**
	 * Required by child class unless it overrides the verify_access_token() method.
	 * @var string
	 */
	public $oauth_verification_url = '';
	public $oauth_revoke_url = '';

	/**
	 * EMIO_OAuth_API_Client constructor.
	 *
	 * @throws EMIO_Exception
	 */
	public function __construct(){
		//set default option/class/client names if not defined by parent constructor
		$service_name = str_replace(array('EMIO_','_API_Client'), '', get_class($this));
		if( static::$service_name == 'OAuth' ) static::$service_name = $service_name;
		if( static::$option_name == 'oauth' ) static::$option_name = strtolower($service_name);
		if( static::$token_class == 'EMIO_OAuth_API_Token' && class_exists('EMIO_'.$service_name.'_API_Token') ) static::$token_class = 'EMIO_'.$service_name.'_API_Token';
		//check credentials
		$creds = array(
			'id' => EMIO_Options::get( static::$option_name. '_app_id' ),
			'secret' => EMIO_Options::get( static::$option_name. '_app_secret' )
		);
		foreach( array('id', 'secret', 'scope') as $k ){
			if( !empty($creds[$k]) ){
				$this->$k = $creds[$k];
			}elseif( empty($this->$k) ) { //constructors can be overriden to add any of the above
				throw new EMIO_Exception( __('OAuth application information incomplete.', 'events-manager-io') );
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function get_oauth_url(){
		$scope = is_array($this->scope) ? urlencode(implode('+', $this->scope)) : $this->scope;
		$replacements = array( urlencode($this->id), $scope, urlencode($this->get_oauth_callback_url()) );
		return str_replace( array('CLIENT_ID','ACCESS_SCOPE','REDIRECT_URI'), $replacements, $this->oauth_authorize_url );
	}

	/**
	 * @return string
	 */
	public static function get_oauth_callback_url(){
		$service_name = str_replace(array('EMIO_','_API_Client'), '', get_called_class());
		if( static::$option_name == 'oauth' ) static::$option_name = strtolower($service_name);
		$redirect_base_uri = defined('EMIO_OAUTH_REDIRECT') ? EMIO_OAUTH_REDIRECT : admin_url('admin-ajax.php'); //you can completely replace the oauth redirect link for testing locally via a proxy for example
		if( defined('EMIO_OAUTH_TUNNEL') ){ //for local development or other reasons, you can replace the domain with a tunnel domain, which should be with http(s):// included
			$redirect_base_uri = str_replace(get_home_url(), EMIO_OAUTH_TUNNEL, $redirect_base_uri);
		}
		return add_query_arg(array('action'=>'emio_'.static::$option_name, 'callback'=>'authorize'), $redirect_base_uri);
	}

	/**
	 * Returns a native client for this service, in the event we want to load an SDK provided by the service.
	 * @return stdClass
	 */
	public function client(){
		return new stdClass();
	}

	//Baseic OAuth interaction functions, loading a token into client as well as requesting, refreshing, verifying and revoking tokens.

	/**
	 * @param int $user_id
	 * @param int $account_id
	 * @throws EMIO_Exception
	 */
	public function load_token( $user_id, $account_id = 0 ){
		if( $this->authorized && $this->authorized = $user_id.'|'.$account_id && $this->user_id == $user_id && $this->token->id == $account_id) return;
		//reload token
		$this->authorized = $this->token = false;
		$this->user_id = $user_id;
		//get token information from user account
		$this->get_access_token( $account_id );
		//renew token if expired
		if ( $this->token->is_expired() ) {
			// Refresh the token if it's expired and update WP user meta.
			$this->refresh();
		}else{
			$this->authorized = $user_id .'|'. $this->token->id;
		}
	}

	/**
	 * Requests an access token from the supplied authorization code. The access token is further verified and populated with service account meta.
	 * If successful, token and meta information is saved for the user $user_id or current user if not specified.
	 * Throws an EMIO_Exception if unsuccessful at any stage in this process.
	 *
	 * @var string $code
	 * @var int $user_id
	 * @throws EMIO_Exception
	 */
	public function request($code, $user_id = 0 ){
		$this->user_id = empty($user_id) ? get_current_user_id() : $user_id; //used in $this->save_access_token()
		$access_token = $this->request_access_token($code);
		if( !empty($access_token['error']) ){
			throw new EMIO_Exception($access_token['error'], $access_token['code']);
		}else{
			$this->token = new EMIO_OAuth_API_Token($access_token);
			if( $this->token->refresh_token === true ) $this->token->refresh_token = false; //if no token was provided, we may be able to obtain it here, otherwise validation will fail upon refresh.
			//verify the access token so we can establish the id of this account and then save it to user profile
			$access_token_meta = $this->verify_access_token();
			//now, check for previous tokens and save to it instead of overwriting (we do this in case ppl reauthorize the same account and get a new token with no refresh_token)
			if( empty($this->token->id) ){
				$token = $this->token;
				try{
					$this->get_access_token($access_token_meta['id']);
					$this->token->refresh( $token->to_array() ); //merge in new token info to old token
				} catch ( EMIO_Exception $ex ){
					$this->token = $token; //revert back to new token
				}
			}
			//refresh current or new token with the meta info and save
			$this->token->refresh( $access_token_meta );
			$this->save_access_token();
		}
	}

	/**
	 * @throws EMIO_Exception
	 */
	public function refresh(){
		if ( $this->token->refresh_token ) {
			$access_token = $this->refresh_access_token();
			if ( is_array($access_token) && empty($access_token['error']) ) {
				$this->token->refresh($access_token);
				$this->save_access_token();
				$this->authorized = $this->user_id .'|'. $this->token->id;
			}else{
				throw new EMIO_Exception( array(
					static::$option_name.'-error' => sprintf(esc_html__( 'There was an error connecting to %s: %s', 'events-manager-io' ), static::$service_name, "<code>{$access_token['error']}</code>"),
					static::$option_name.'-token-expired' => $this->reauthorize_error_string()
				));
			}
		}else{
			throw new EMIO_Exception( $this->reauthorize_error_string() );
		}
	}

	/**
	 * Verify the token for this client by obtaining meta data of the account associated to this token and saving it to the current token.
	 * @var boolean $update_token
	 * @return boolean
	 * @throws EMIO_Exception
	 */
	public function verify( $update_token = true ){
		$access_token_meta = $this->verify_access_token();
		//refresh current or new token with the meta info and save
		$updated = $this->token->refresh( $access_token_meta );
		if( $updated && $update_token ) $this->save_access_token();
		return true; //if we get here, verification passed.
	}

	/**
	 * @return boolean
	 * @throws EMIO_Exception
	 */
	public function revoke(){
		return $this->revoke_access_token();
	}

	/* START OVERRIDABLE FUNCTIONS - THESE FUNCTIONS COULD BE OVERRIDDEN TO SPECIFICALLY DEAL WITH PARTICULAR OAUTH PROVIDERS */

	/**
	 * Specific function which requests the access token from the API Service and returns the access token array, an error array if service replies.
	 * Throws an EMIO_Exception if there are any other connection issues.
	 *
	 * @var string $code
	 * @return array
	 * @throws EMIO_Exception
	 */
	public function request_access_token( $code ){
		$args = array(
			'headers' => array(
				'Content-Type' => 'application/x-www-form-urlencoded'
			),
			'body' => array(
				'client_id' => $this->id,
				'client_secret' => $this->secret,
				'grant_type' => 'authorization_code',
				'redirect_uri' => $this->get_oauth_callback_url(),
				'code' => $code
			)
		);
		$response = wp_remote_post( $this->oauth_request_token_url, $args );
		if( is_wp_error($response) ){
			throw new EMIO_Exception($response->get_error_messages());
		}
		return json_decode($response['body'], true);
	}

	/**
	 * @return array
	 * @throws EMIO_Exception
	 */
	public function refresh_access_token(){
		$args = array(
			'headers' => array(
				'Content-Type' => 'application/x-www-form-urlencoded'
			),
			'body' => array(
				'client_id' => $this->id,
				'client_secret' => $this->secret,
				'grant_type' => 'refresh_token',
				'refresh_token' => $this->token->refresh_token,
			)
		);
		$response = wp_remote_post( $this->oauth_request_token_url, $args );
		if( is_wp_error($response) ){
			throw new EMIO_Exception($response->get_error_messages());
		}
		$access_token = json_decode($response['body'], true);
		if( empty($access_token) || $response['response']['code'] != 200 ){
			$access_token['error'] = $response['response']['code'] .' - '. $response['body'];
		}
		return $access_token;
	}

	/**
	 * Verifies an access token by obtaining further meta data about the account associated with that token.
	 * Expected return is an associative array containing the id (service account id the token belongs to), name, photo and email (optional).
	 *
	 * @return array
	 * @throws EMIO_Exception
	 */
	public function verify_access_token(){
		$request_url = str_replace('ACCESS_TOKEN', $this->token->access_token, $this->oauth_verification_url);
		$response = wp_remote_get($request_url);
		if( is_wp_error($response) ){
			throw new EMIO_Exception($response->get_error_messages());
		}elseif( $response['response']['code'] != '200' ){
			$errors = json_decode($response['body']);
			$error = current($errors);
			throw new EMIO_Exception($error->message, $error->code);
		}
		$access_token = json_decode($response['body'], true); //we may want to override this depending on what's returned
		return $access_token;
	}

	/**
	 * @return bool
	 * @throws EMIO_Exception
	 */
	public function revoke_access_token(){
		if( empty($this->oauth_revoke_url) ) return false;
		$request_url = str_replace('ACCESS_TOKEN', $this->token->access_token, $this->oauth_revoke_url);
		$response = wp_remote_get($request_url);
		if( is_wp_error($response) ){
			throw new EMIO_Exception($response->get_error_messages());
		}elseif( $response['response']['code'] != '200' ){
			$errors = json_decode($response['body']);
			$error = current($errors);
			throw new EMIO_Exception($error->message, $error->code);
		}
		return true;
	}

	/* END OVERRIDABLE FUNCTIONS */

	/**
	 * @param int $api_user_id
	 * @return string
	 */
	public function reauthorize_error_string($api_user_id = 0 ){
		$settings_page_url = '<a href="'.admin_url('admin.php?page=events-manager-io-settings').'#connect'.'">'. esc_html__('settings page', 'events-manager-io-google').'</a>';
		if( !$api_user_id && !empty($this->token->id) ){
			$api_user_id = !empty($this->token->email) ? $this->token->email : $this->token->id;
		}
		if( $api_user_id ){
			return sprintf(__('You need to reauthorize access to account %s by visiting the %s page.', 'events-manager-io-google'), $api_user_id, $settings_page_url);
		}
		return sprintf(__('You need to authorize access to your %s account by visiting the %s page.', 'events-manager-io-google'), static::$service_name, $settings_page_url);
	}

	/**
	 * Gets an access token for a specific account, or provides first account user has available, if any. If no access token is available, an EMIO_Exception is thrown.
	 *
	 * @param int $api_user_id The ID (email) of the Google account
	 * @return EMIO_OAuth_API_Token
	 * @throws EMIO_Exception
	 */
	public function get_access_token( $api_user_id = 0 ){
		$user_tokens = get_user_meta( $this->user_id, 'emio_oauth_'.static::$option_name, true );
		if( empty($user_tokens) ) $user_tokens = array();
		$user_token = array();
		if( $api_user_id ){
			if( !empty($user_tokens[$api_user_id]) ){
				$user_token = $user_tokens[$api_user_id];
				$user_token['id'] = $api_user_id;
			}
		}elseif( !empty($user_tokens) ){
			$user_token = current($user_tokens);
			$user_token['id'] = key($user_tokens);
		}
		if( empty($user_token) ) throw new EMIO_Exception( $this->reauthorize_error_string($api_user_id) );
		$this->token = new static::$token_class($user_token);
		return $this->token;
	}

	/**
	 * Sets the access token to the user meta storage where all connected accounts for the user of that token are stored.
	 */
	public function save_access_token(){
		$user_tokens = get_user_meta($this->user_id, 'emio_oauth_'.static::$option_name, true);
		$token = $this->token->to_array();
		if( empty($user_tokens) ) $user_tokens = array();
		$user_tokens[$this->token->id] = $token;
		update_user_meta($this->user_id, 'emio_oauth_'.static::$option_name, $user_tokens);
	}
}