<?php if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) { exit( 'No direct script access allowed !' ); }

use EEA_QuickBooks\OAuthSimple;

/**
 * 	Class  EED_QuickBooks_Oauth
 *
 *	@package 		Event Espresso
 *	@subpackage 	eea-quickbooks-gateway
 *	@author 		Event Espresso
 *	
 */
class EED_QuickBooks_Oauth extends EED_Module {

	/**
	 * @return EED_QuickBooks_Oauth
	 */
	public static function instance() {
		return parent::get_instance( __CLASS__ );
	}


	/**
	 * 	run - initial module setup
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public function run( $WP ) {

	}


	/**
	 * 	set_hooks - for hooking into EE Core, other modules, etc
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public static function set_hooks() {
		if( EE_Maintenance_Mode::instance()->models_can_query() ) {
			// Log QuickBooks JS errors.
			add_action( 'wp_ajax_nopriv_eea_quickbooks_log_error', array( 'EED_QuickBooks_Oauth', 'log_quickbooks_js_error' ) );
			// A hook to handle the process after the oAuth and request an access token from Intuit.
			add_action( 'admin_post_nopriv_eea_qb_oauth_request_access', array( 'EED_QuickBooks_Oauth', 'eea_qb_oauth_request_access_token' ) );
		}
	}


	/**
	 * 	set_hooks_admin - for hooking into EE Admin Core, other modules, etc
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public static function set_hooks_admin() {
		if( EE_Maintenance_Mode::instance()->models_can_query() ) {
			// QB connection Notice.
			add_action( 'admin_notices', array( 'EED_QuickBooks_Oauth', 'qb_connection_admin_notice'), 3 );
			// Log QuickBooks JS errors.
			add_action( 'wp_ajax_eea_quickbooks_log_error', array( 'EED_QuickBooks_Oauth', 'log_quickbooks_js_error' ) );
			// QuickBooks OAuth requests.
			add_action( 'wp_ajax_eea_get_qb_request_token', array( 'EED_QuickBooks_Oauth', 'eea_get_qb_request_token' ) );
			// Check the OAuth status.
			add_action( 'wp_ajax_eea_qb_update_oauth_status', array( 'EED_QuickBooks_Oauth', 'eea_qb_update_oauth_status' ) );
			// oAuth Reconnect.
			add_action( 'wp_ajax_eea_qb_oauth_reconnect', array( 'EED_QuickBooks_Oauth', 'eea_qb_oauth_reconnect' ) );
			// oAuth Disconnect.
			add_action( 'wp_ajax_eea_qb_oauth_disconnect', array( 'EED_QuickBooks_Oauth', 'eea_qb_oauth_disconnect' ) );
			// oAuth Reset.
			add_action( 'wp_ajax_eea_qb_oauth_reset_oathsettings', array( 'EED_QuickBooks_Oauth', 'eea_qb_oauth_reset_oathsettings' ) );
			// A hook to handle the process after the oAuth and request an access token from Intuit.
			add_action( 'admin_post_eea_qb_oauth_request_access', array( 'EED_QuickBooks_Oauth', 'eea_qb_oauth_request_access_token' ) );
		}
	}


	/**
	 *  Log JS TXN Error.
	 *
	 *	@return void
	 */
	public static function log_quickbooks_js_error() {
		if ( isset($_POST['txn_id']) && ! empty($_POST['txn_id']) ) {
			$quickbooks_pm = EEM_Payment_Method::instance()->get_one_of_type( $_POST['pm_slug'] );
			$transaction = EEM_Transaction::instance()->get_one_by_ID( $_POST['txn_id'] );
			$quickbooks_pm->type_obj()->get_gateway()->log( array('JS Error (Transaction: ' . $transaction->ID() . ')' => $_POST['message']), $transaction );
		}
	}


	/**
	 *  Add admin notification about QB connection.
	 *
	 * @return void
	 */
	public static function qb_connection_admin_notice() {
		$quickbooks_pm = EEM_Payment_Method::instance()->get_one_of_type( 'QuickBooks_Onsite' );

		//If the quickbooks PM is not active we do not need to display an notices about the connection.
		if (! $quickbooks_pm instanceof EE_Payment_Method
            || (
                $quickbooks_pm instanceof EE_Payment_Method
                && ! $quickbooks_pm->active()
            )
        ) {
			return;
		}

		$connected_on = $quickbooks_pm->get_extra_meta('connected_on', true, false);
        $x_expires_in = $quickbooks_pm->get_extra_meta('x_refresh_token_expires_on', true, false);
        $oauth_version = $quickbooks_pm->get_extra_meta('oauth_version', true, 'v1a');
        if ($oauth_version === 'v1a') {
            if (! $connected_on) {
                return;
            }
            $expires_on = date('Y-m-d', strtotime($connected_on . ' + 180 days'));
            $connection_notified = 'eeag_qb_connection_notified';
        } else {
            if (! $x_expires_in) {
                return;
            }
            $expires_on = date('Y-m-d', (int)$x_expires_in);
            $connection_notified = 'eeag_qb_connection_notified_v2';
        }
		$date_diff = floor((strtotime($expires_on) - time()) / (60 * 60 * 24));

		$admin_email = EE_Config::instance()->organization->email;
		$notified = get_site_option( $connection_notified, 'false' );
		$is_notified = ( $notified === 'true' ) ? true : false;

		if ($date_diff <= 0) {
			EE_Error::add_error(
				sprintf(
                    __(
                        // @codingStandardsIgnoreStart
                        'QuickBooks Payments will not be processed because the app connection has expired. Please visit %1$s Payment Methods settings page %2$s and establish a new connection.',
                        // @codingStandardsIgnoreEnd
                        'event_espresso'
                    ),
					'<a href="admin.php?page=espresso_payment_settings">',
					'</a>'
				),
				__FILE__, __FUNCTION__, __LINE__
			);
		} elseif ($date_diff <= 30) {
			$err_msg = sprintf(
                    __(
                        // @codingStandardsIgnoreStart
                        ' %1$s QuickBooks Payments app connection will expire soon %2$s. Please visit %3$s Event Espresso Payment Methods settings page %4$s and %5$s "Reconnect" %6$s .',
                        // @codingStandardsIgnoreEnd
                        'event_espresso'
                    ),
					'<strong>',
					'</strong>',
					'<a href="' . get_admin_url() . 'admin.php?page=espresso_payment_settings">',
					'</a>',
					'<strong>',
					'</strong>'
				);
			EE_Error::add_attention( $err_msg, __FILE__, __FUNCTION__, __LINE__ );
			// Send an email notification.
			$allow_emails = apply_filters( 'FHEEA__EED_QuickBooks_Oauth__qb_connection_admin_notice__email', TRUE );
			if ( $allow_emails && ! empty($admin_email) && function_exists( 'wp_mail' ) && ! $is_notified ) {
				wp_mail( $admin_email, __('Event Espresso - QuickBooks Payments Gateway', 'event_espresso'), $err_msg );
				update_site_option($connection_notified, 'true');
			}
		} elseif ((! $connected_on && $oauth_version === 'v1a') || (! $x_expires_in && $oauth_version === 'v2')) {
			EE_Error::add_error(
				sprintf(
                    __(
                        // @codingStandardsIgnoreStart
                        'QuickBooks Payments will not be processed because the App connection was not established. Please visit the %1$s Event Espresso Payment Methods settings page %2$s and establish a new connection.',
                        // @codingStandardsIgnoreEnd
                        'event_espresso'
                    ),
					'<a href="admin.php?page=espresso_payment_settings">',
					'</a>'
				),
				__FILE__, __FUNCTION__, __LINE__
			);
		} elseif ( $is_notified ) {
			// Here, let's assume that the user already re-activated.
			// We delete this meta-data so that the next time the connection expires the notification could go out again.
			update_site_option( $connection_notified, 'false' );
		}
	}


	/**
	 *  OAuth Request token.
	 *
	 *	@return void
	 */
	public static function eea_get_qb_request_token() {
		try {
            $quickbooks_pm = EE_PMT_QuickBooks_Onsite::is_pm_valid($_POST, 'exit');
			// Include OAuth dependencies.
			require_once( EEA_QUICKBOOKS_PM_PLUGIN_PATH . 'includes' . DS . 'oauth_simple' . DS . 'OAuthSimple.php' );
			$consumer_key = $quickbooks_pm->get_extra_meta( 'consumer_key', true );
			$shared_secret = $quickbooks_pm->get_extra_meta( 'shared_secret', true );
			// Signature required; exit if missing.
			if ( ! $consumer_key || ! $shared_secret ) {
				echo json_encode( array('qb_error' => 'Missing some signature required PM data !' ) );
				exit();
			}

			$signatures = array(
				'consumer_key' => $consumer_key,
				'shared_secret' => $shared_secret,
				'oauth_signature_method' => 'HMAC-SHA1',
				'Request-Id' => substr( EED_QuickBooks_Oauth::get_GUID(), 0, 50 )
			);

			$oauth_object = new OAuthSimple();
			// Get a temporary request token to facilitate the user authorization.
			$oauth_callback = get_site_url() . '/wp-admin/admin-post.php?action=eea_qb_oauth_request_access&pmslg=' . $quickbooks_pm->slug();
			$sign_token_result = $oauth_object->sign( array(
				'path' => "https://oauth.intuit.com/oauth/v1/get_request_token",
				'parameters' => array(
					'oauth_callback' => $oauth_callback
				),
				'signatures' => $signatures
			));

			if ( ! is_array($sign_token_result) || ! isset($sign_token_result['signed_url']) || empty($sign_token_result['signed_url']) ) {
				echo json_encode( array('qb_error' => 'There was an Error while trying to sign the Request URL !' ) );
				exit();
			} elseif ( strpos($sign_token_result['signed_url'], 'oauth_callback') === false ) {
				echo json_encode( array('qb_error' => 'There was a problem while trying to sign the Request URL : oauth_callback parameter missing.') );
				exit();
			}
			// Request the request_token and request_token_secret.
			$request_token_response = wp_remote_get( $sign_token_result['signed_url'] );
			if ( ! is_array($request_token_response) || empty($request_token_response['body']) || $request_token_response['response']['code'] != 200 || $request_token_response['response']['message'] !== 'OK' ) {
				$error_msg = 'There was an Error while trying to request a request_token, please try again';
				if ( is_array($request_token_response) && ! empty($request_token_response['body']) ) {
					$error_msg .= ' : ' . $request_token_response['body'];
				}
				echo json_encode( array('qb_error' => $error_msg) );
				exit();
			}

			parse_str($request_token_response['body'], $rt_returned_items);
			$request_token = $rt_returned_items['oauth_token'];
			$request_token_secret = base64_encode($rt_returned_items['oauth_token_secret']);

			// We will need the request token and secret after the authorization.
			setcookie( 'eea_qb_oauth_ts', $request_token_secret, time() + 3600, '/' );

			// Authorize the Request Token.
			// Generate a URL for an authorization request, then redirect to that URL.
			$signatures['Request-Id'] = substr( EED_QuickBooks_Oauth::get_GUID(), 0, 50 );
			$auth_token_result = $oauth_object->sign( array(
				'path' => "https://appcenter.intuit.com/Connect/Begin",
				'parameters' => array(
					'oauth_token' => $request_token
				),
				'signatures' => $signatures
			));
			$signed_url = ( is_array($auth_token_result) ) ? $auth_token_result['signed_url'] : false;
			if ( ! $signed_url || ! isset($signed_url) || empty($signed_url) ) {
				echo json_encode( array('qb_error' => 'There was an Error while trying to sign the authorization Request URL !') );
				exit();
			}

			// Let our JS redirect the merchant to authorize on Intuit.
			echo json_encode( array(
				'qb_success' => 'true',
				'handle_return_url' => $signed_url
			));
			exit();

		} catch ( OAuthException $e ) {
			echo json_encode( array('qb_error' => 'There was an Error while trying to request an authentication token : ' . $e) );
			exit();
		}
	}


	/**
	 *  Check on the OAuth status.
	 *
	 *	@return void
	 */
	public static function eea_qb_update_oauth_status() {
		$submitted_pm = $_POST['submitted_pm'];
		$quickbooks_pm = EEM_Payment_Method::instance()->get_one_of_type( $submitted_pm );
		$oauth_token = $quickbooks_pm->get_extra_meta( 'oauth_token', true );
		$oauth_token_secret = $quickbooks_pm->get_extra_meta( 'oauth_token_secret', true );

		if ( isset($oauth_token) && isset($oauth_token_secret) && ! empty($oauth_token) && ! empty($oauth_token_secret) ) {
			echo json_encode( array(
				'oauthed' => true
			));
			exit();
		} else {
			echo json_encode( array(
				'oauthed' => false
			));
			exit();
		}
	}


    /**
	 *	Request an access token from Intuit.
	 *	This will handle the process when the user returns after OAuth.
	 *
	 *	@return void
	 */
	public static function eea_qb_oauth_request_access_token() {
		// Check if all the needed parameters are present.
        $quickbooks_pm = EE_PMT_QuickBooks_Onsite::is_pm_valid($_GET, 'close');
		// If all is fine process and save the long term QuickBooks request token.
		if ( isset($_GET['oauth_token']) && isset($_GET['oauth_verifier']) && isset($_COOKIE['eea_qb_oauth_ts']) &&
			! empty($_GET['oauth_token']) && ! empty($_GET['oauth_verifier']) && ! empty($_COOKIE['eea_qb_oauth_ts']) ) {
			// Include OAuth dependencies.
			require_once( EEA_QUICKBOOKS_PM_PLUGIN_PATH . 'includes' . DS . 'oauth_simple' . DS . 'OAuthSimple.php' );
			$oauth_object = new OAuthSimple();
			$consumer_key = ( $quickbooks_pm->get_extra_meta( 'consumer_key', true ) ) ? $quickbooks_pm->get_extra_meta( 'consumer_key', true ) : false;
			$shared_secret = ( $quickbooks_pm->get_extra_meta( 'shared_secret', true ) ) ? $quickbooks_pm->get_extra_meta( 'shared_secret', true ) : false;
			// Signature required !
			if ( ! $consumer_key || ! $shared_secret ) {
				echo json_encode( array('qb_error' => 'Missing some signature required PM data !' ) );
				exit();
			}
			// Prep a request for Long-Term Access Token.
			$request_token_secret = base64_decode( $_COOKIE['eea_qb_oauth_ts'] );
			$signatures = array(
				'consumer_key' => $consumer_key,
				'shared_secret' => $shared_secret,
				'oauth_secret' => $request_token_secret,
				'oauth_token' => $_GET['oauth_token'],
				'oauth_signature_method' => 'HMAC-SHA1',
				'Request-Id' => substr( EED_QuickBooks_Oauth::get_GUID(), 0, 50 )
			);

			$sign_lt_at = $oauth_object->sign( array(
				'path' => 'https://oauth.intuit.com/oauth/v1/get_access_token',
				'parameters' => array(
					'oauth_verifier' => $_GET['oauth_verifier'],
					'oauth_token' => $_GET['oauth_token']
				),
				'signatures' => $signatures
			));

			// Check the signed URL.
			if ( ! isset($sign_lt_at['signed_url']) || empty($sign_lt_at['signed_url']) ) {
				echo '<script type="text/javascript">
					window.opener.console.log("There was an Error while trying to sign the Request URL for the Long-Term Access Token !");
					window.opener = self;
					window.close();
				</script>';
				return;
			}
			// Request an access token from Intuit.
			$access_token_response = wp_remote_get( $sign_lt_at['signed_url'] );

			if ( ! is_array($access_token_response) || empty($access_token_response['body']) || $access_token_response['response']['code'] != 200 || $access_token_response['response']['message'] !== 'OK' ) {
				$error_msg = 'There was an Error while trying to request an access token.';
				if ( is_array($access_token_response) && ! empty($access_token_response['body']) ) {
					$error_msg .= ' : ' . $access_token_response['body'];
				}
				echo '<script type="text/javascript">
					window.opener.console.log("' . $error_msg . '");
					window.opener = self;
					window.close();
				</script>';
				return;
			}
			// Parse response data.
			parse_str($access_token_response['body'], $lt_at_returned_items);
			$access_token = $lt_at_returned_items['oauth_token'];
			$access_token_secret = $lt_at_returned_items['oauth_token_secret'];

			// Save the OAuth data for this PM.
			$quickbooks_pm->update_extra_meta( 'oauth_token', $access_token );
			$quickbooks_pm->update_extra_meta( 'oauth_token_secret', $access_token_secret );
			$quickbooks_pm->update_extra_meta( 'realmId', $_GET['realmId'] );
			$quickbooks_pm->update_extra_meta( 'connected_on', date("Y-m-d H:i:s"));

			// Discard the saved data.
			unset( $_COOKIE['eea_qb_oauth_ts'] );
			setcookie( 'eea_qb_oauth_ts', null, -1, '/' );

			// Write JS to pup-up window to refresh parent and close the pop-up.
			echo '<script type="text/javascript">
					window.opener = self;
					window.close();
				</script>';
			die();
		} elseif ( ! isset($_COOKIE['eea_qb_oauth_ts']) ) {
			echo '<script type="text/javascript">
					window.opener.console.log("oauth_ts missing !");
					window.opener = self;
					window.close();
				</script>';
			die();
		} else {
			echo '<script type="text/javascript">
					window.opener.console.log("Payment Method not specified / recognized or missing some authentication data !");
					window.opener = self;
					window.close();
				</script>';
			die();
		}
	}


	/**
	 *  OAuth Reconnect.
	 *
	 *	@return void
	 */
	public static function eea_qb_oauth_reconnect() {
		try {
			// Include OAuth dependencies.
			require_once( EEA_QUICKBOOKS_PM_PLUGIN_PATH . 'includes' . DS . 'oauth_simple' . DS . 'OAuthSimple.php' );
			// Get the submitted PM.
			$quickbooks_pm = EE_PMT_QuickBooks_Onsite::is_pm_valid($_POST, 'exit');

			$consumer_key = ( $quickbooks_pm->get_extra_meta( 'consumer_key', true ) ) ? $quickbooks_pm->get_extra_meta( 'consumer_key', true ) : false ;
			$shared_secret = ( $quickbooks_pm->get_extra_meta( 'shared_secret', true ) ) ? $quickbooks_pm->get_extra_meta( 'shared_secret', true ) : false ;
			$oauth_token = ( $quickbooks_pm->get_extra_meta( 'oauth_token', true ) ) ? $quickbooks_pm->get_extra_meta( 'oauth_token', true ) : false ;
			$oauth_token_secret = ( $quickbooks_pm->get_extra_meta( 'oauth_token_secret', true ) ) ? $quickbooks_pm->get_extra_meta( 'oauth_token_secret', true ) : false ;
			// These are signature required.
			if ( ! $consumer_key || ! $shared_secret || ! $oauth_token || ! $oauth_token_secret ) {
				echo json_encode( array('qb_error' => 'Missing some signature required PM data !' ) );
				exit();
			}

			$signatures = array(
				'consumer_key' => $consumer_key,
				'shared_secret' => $shared_secret,
				'oauth_token' => $oauth_token,
				'oauth_token_secret' => $oauth_token_secret,
				'oauth_signature_method' => 'HMAC-SHA1',
				'Request-Id' => substr( EED_QuickBooks_Oauth::get_GUID(), 0, 50 )
			);

			$oauth_object = new OAuthSimple();
			// Sign the Reconnect request.
			$sign_reconnect = $oauth_object->sign( array(
				'path' => 'https://appcenter.intuit.com/api/v1/connection/reconnect',
				'signatures' => $signatures
			));

			if ( ! is_array($sign_reconnect) || ! isset($sign_reconnect['signed_url']) || empty($sign_reconnect['signed_url']) ) {
				echo json_encode( array('qb_error' => 'There was an Error while trying to sign the Reconnect request !' ) );
				exit();
			}

			// Send Reconnect request.
			$reconnect_request = wp_remote_get( $sign_reconnect['signed_url'] );

			// Check the response.
			if ( ! is_array($reconnect_request) || empty($reconnect_request['body']) || $reconnect_request['response']['code'] != 200 ) {
				$error_msg = 'Unknown response';
				if ( is_array($reconnect_request) && ! empty($reconnect_request['body']) ) {
					$error_msg .= ' : ' . $reconnect_request['body'];
				}
				echo json_encode( array('qb_error' => $error_msg) );
				exit();
			}

			// Parse the XML response.
			$reconnect_response = new SimpleXMLElement( $reconnect_request['body'] );

			if ( ! $reconnect_response ) {
				echo json_encode( array('qb_error' => 'Could not Parse response !') );
				exit();
			} elseif ( $reconnect_response->ErrorCode != '0' ) {
				$error_msg = 'Error '. $reconnect_response->ErrorCode. ' ' . gettype($reconnect_response->ErrorCode) . ' : ' . $reconnect_response->ErrorMessage;
				echo json_encode( array('qb_error' => $error_msg) );
				exit();
			}

			// Update the Tokens.
			$quickbooks_pm->update_extra_meta( 'oauth_token', $reconnect_response->OAuthToken );
			$quickbooks_pm->update_extra_meta( 'oauth_token_secret', $reconnect_response->OAuthTokenSecret );
			$quickbooks_pm->update_extra_meta( 'connected_on', date("Y-m-d H:i:s"));

			echo json_encode( array(
				'qb_success' => 'true'
			));
			exit();
		} catch ( OAuthException $e ) {
			echo json_encode( array('qb_error' => 'There was an Error while trying to Reconnect : ' . $e) );
			exit();
		}
	}


	/**
	 *  OAuth Disconnect.
	 *
	 *	@return void
	 */
	public static function eea_qb_oauth_disconnect() {
		try {
			// Include OAuth dependencies.
			require_once( EEA_QUICKBOOKS_PM_PLUGIN_PATH . 'includes' . DS . 'oauth_simple' . DS . 'OAuthSimple.php' );
			// Get the submitted PM.
			$quickbooks_pm = EE_PMT_QuickBooks_Onsite::is_pm_valid($_POST, 'exit');

			$consumer_key = ( $quickbooks_pm->get_extra_meta( 'consumer_key', true ) ) ? $quickbooks_pm->get_extra_meta( 'consumer_key', true ) : false ;
			$shared_secret = ( $quickbooks_pm->get_extra_meta( 'shared_secret', true ) ) ? $quickbooks_pm->get_extra_meta( 'shared_secret', true ) : false ;
			$oauth_token = ( $quickbooks_pm->get_extra_meta( 'oauth_token', true ) ) ? $quickbooks_pm->get_extra_meta( 'oauth_token', true ) : false ;
			$oauth_token_secret = ( $quickbooks_pm->get_extra_meta( 'oauth_token_secret', true ) ) ? $quickbooks_pm->get_extra_meta( 'oauth_token_secret', true ) : false ;
			// These are signature required.
			if ( ! $consumer_key || ! $shared_secret || ! $oauth_token || ! $oauth_token_secret ) {
				echo json_encode( array('qb_error' => 'Missing some signature required PM data !' ) );
				exit();
			}

			$signatures = array(
				'consumer_key' => $consumer_key,
				'shared_secret' => $shared_secret,
				'oauth_token' => $oauth_token,
				'oauth_token_secret' => $oauth_token_secret,
				'oauth_signature_method' => 'HMAC-SHA1',
				'Request-Id' => substr( EED_QuickBooks_Oauth::get_GUID(), 0, 50 )
			);

			$oauth_object = new OAuthSimple();
			// Sign the Disconnect request.
			$sign_disconnect = $oauth_object->sign( array(
				'path' => 'https://appcenter.intuit.com/api/v1/connection/disconnect',
				'signatures' => $signatures
			));

			if ( ! is_array($sign_disconnect) || ! isset($sign_disconnect['signed_url']) || empty($sign_disconnect['signed_url']) ) {
				echo json_encode( array('qb_error' => 'There was an Error while trying to sign the Disconnect request !' ) );
				exit();
			}

			// Send Disconnect request.
			$disconnect_request = wp_remote_get( $sign_disconnect['signed_url'] );

			// Check the response.
			if ( ! is_array($disconnect_request) || empty($disconnect_request['body']) || $disconnect_request['response']['code'] != 200 ) {
				$error_msg = 'Unknown response';
				if ( is_array($disconnect_request) && ! empty($disconnect_request['body']) ) {
					$error_msg .= ' : ' . $disconnect_request['body'];
				}
				echo json_encode( array('qb_error' => $error_msg) );
				exit();
			}

			// Parse the XML response.
			$disconnect_response = new SimpleXMLElement( $disconnect_request['body'] );
			if ( ! $disconnect_response ) {
				echo json_encode( array('qb_error' => 'Could not Parse response !') );
				exit();
			} elseif ( $disconnect_response->ErrorCode != '0' ) {
				$error_msg = 'Error '. gettype($disconnect_response->ErrorCode) . ' ' . $disconnect_response->ErrorCode . ' : ' . $disconnect_response->ErrorMessage;
				echo json_encode( array('qb_error' => $error_msg) );
				exit();
			}

			// Delete the Token data.
			$quickbooks_pm->delete_extra_meta( 'oauth_token' );
			$quickbooks_pm->delete_extra_meta( 'oauth_token_secret' );
			$quickbooks_pm->delete_extra_meta( 'connected_on' );
			$quickbooks_pm->delete_extra_meta( 'realmId' );

			echo json_encode( array(
				'qb_success' => 'true'
			));
			exit();
		} catch ( OAuthException $e ) {
			echo json_encode( array('qb_error' => 'There was an Error while trying to Disconnect : ' . $e) );
			exit();
		}
	}


	/**
	 *  OAuth Reset.
	 *
	 *	@return void
	 */
	public static function eea_qb_oauth_reset_oathsettings() {
		// Get the submitted PM.
		$quickbooks_pm = EE_PMT_QuickBooks_Onsite::is_pm_valid($_POST, 'exit');

		// Delete the Token data.
		$quickbooks_pm->delete_extra_meta( 'oauth_token' );
		$quickbooks_pm->delete_extra_meta( 'oauth_token_secret' );
		$quickbooks_pm->delete_extra_meta( 'connected_on' );
		$quickbooks_pm->delete_extra_meta( 'realmId' );

		echo json_encode( array(
			'qb_success' => 'true'
		));
		exit();
	}


	/**
	 * Create GUID.
	 *
	 * @return string GUID
	 */
	public static function get_GUID() {
		if ( function_exists('com_create_guid') ) {
			return str_replace(array('{', '}'), '', com_create_guid());
		} else {
			mt_srand( (double)microtime() * 10000 );
			$charid = strtoupper( md5(uniqid(rand(), true)) );
			$hyphen = chr(45); // "-"
			$uuid = substr($charid, 0, 8) . $hyphen
				.substr($charid, 8, 4) . $hyphen
				.substr($charid,12, 4) . $hyphen
				.substr($charid,16, 4) . $hyphen
				.substr($charid,20,12);
			return $uuid;
		}
	}



	/**
	 *		@ override magic methods
	 *		@ return void
	 */
	public function __set($a,$b) { return FALSE; }
	public function __get($a) { return FALSE; }
	public function __isset($a) { return FALSE; }
	public function __unset($a) { return FALSE; }
	public function __clone() { return FALSE; }
	public function __wakeup() { return FALSE; }
	public function __destruct() { return FALSE; }
}