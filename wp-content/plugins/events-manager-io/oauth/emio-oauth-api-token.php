<?php
class EMIO_OAuth_API_Token {

	public $access_token = '';
	public $refresh_token = '';
	public $token_type = '';
	public $expires_in = 0;
	/**
	 * @var int Timestamp when a token will expire at, which can be supplied instead of expires_in and that value will be generated from this one.
	 */
	public $expires_at = 0;
	public $created = 0;

	public $id = '';
	public $email = '';
	public $name = '';
	public $photo = '';

	/**
	 * @param array $token
	 * @throws EMIO_Exception
	 */
	public function __construct( $token ){
		$this->refresh($token);
		if( empty($token['created']) ) $this->created = time();
	}

	/**
	 * @param array $token
	 * @return boolean $updated
	 * @throws EMIO_Exception
	 */
	public function refresh( $token ){
		$updated = false;
		foreach( $token as $k => $v ){
			if( empty($this->$k) || $this->$k != $token[$k] ){
				$this->$k = $token[$k];
				$updated = true;
			}
		}
		if( empty($this->id) && !empty($this->email) ) $this->id = $this->email;
		if( empty($this->expires_in) && !empty($this->expires_at) ){
			$this->expires_in = $this->expires_at - time();
		}else{
			$this->expires_at = $this->expires_in + time();
		}
		$this->verify();
		return $updated;
	}

	/**
	 * @throws EMIO_Exception
	 */
	public function verify(){
		$missing = array();
		foreach( array('access_token', 'expires_in') as $k ){
			if( empty($this->$k) ) $missing[] = $k;
		}
		if( !empty($missing) ) throw new EMIO_Exception( sprintf(__('Involid token credentials, the folloiwng are missing: %s.', 'events-manager-io'), implode(', ', $missing)) );
	}

	public function is_expired(){
		return $this->expires_in != 0 && $this->created + $this->expires_in <= time();
	}

	public function to_array(){
		$array = array();
		$ignore = array('id');
		foreach( get_object_vars($this) as $k => $v ){
			if( !in_array($k, $ignore) && !empty($this->$k) ) $array[$k] = $this->$k;
		}
		return $array;
	}
}