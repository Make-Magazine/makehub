<?php

if (! defined('EVENT_ESPRESSO_VERSION')) {
    exit('No direct script access allowed !');
}


/**
 *  Class  EED_QuickBooks_Oauth_v2
 *
 *  @package        Event Espresso
 *  @subpackage     eea-quickbooks-gateway
 *  @author         Event Espresso
 *  @version        1.0.10.p
 */
class EED_QuickBooks_Oauth_v2 extends EED_Module
{
    /**
     * @return EED_QuickBooks_Oauth_v2
     */
    public static function instance()
    {
        return parent::get_instance(__CLASS__);
    }


    /**
     *  Initial module setup
     *
     * @access  public
     * @return  void
     */
    public function run($WP)
    {}


    /**
     *  For hooking into EE Core, other modules, etc.
     *
     * @access  public
     * @return  void
     */
    public static function set_hooks()
    {
        if (EE_Maintenance_Mode::instance()->models_can_query()) {
            // A hook to handle the process after the oAuth and request an access token from Intuit.
            add_action('init', array('EED_QuickBooks_Oauth_v2', 'request_access_token'), 16);
        }
    }


    /**
     *  For hooking into EE Admin Core, other modules, etc.
     *
     * @access  public
     * @return  void
     */
    public static function set_hooks_admin()
    {
        if (EE_Maintenance_Mode::instance()->models_can_query()) {
            // QuickBooks OAuth requests.
            add_action('wp_ajax_eea_get_qb_request_token_v2', array('EED_QuickBooks_Oauth_v2', 'get_redirect_url'));
            // Check the OAuth status.
            add_action('wp_ajax_eea_qb_update_oauth_status_v2', array('EED_QuickBooks_Oauth_v2', 'update_oauth_status'));
            // oAuth Reset.
            add_action('wp_ajax_eea_qb_oauth_reset_oathsettings_v2', array('EED_QuickBooks_Oauth_v2', 'reset_oath_settings'));
            // oAuth Reconnect.
            add_action('wp_ajax_eea_qb_oauth_reconnect_v2', array('EED_QuickBooks_Oauth_v2', 'refresh_tokens'));
            // oAuth Disconnect.
            add_action('wp_ajax_eea_qb_oauth_disconnect_v2', array('EED_QuickBooks_Oauth_v2', 'revoke_access'));
        }
    }


    /**
     *  Do an authorization request depending on the OAuth version.
     *
     * @return void
     */
    public static function get_redirect_url()
    {
        // Try getting the PM. Exit on error.
        $quickbooks_pm = EE_PMT_QuickBooks_Onsite::is_pm_valid($_POST, 'exit');
        $err_msg = '';
        $redirect_uri = site_url();
        $redirect_uri = trailingslashit($redirect_uri);
        $client_id = $quickbooks_pm->get_extra_meta('consumer_key', true);
        // Add a nonce.
        $the_nonce = wp_create_nonce('eeg-quickbooks-oauth');
        // oAuth return handler.
        // Note: this has to be added and match with the redirect URI in your QuickBooks App settings
        // and should look like: "https://[your.site]".
        // Request URL.
        $request_url = add_query_arg(
            array(
                'response_type'  => 'code',
                'client_id'      => rawurlencode($client_id),
                'scope'          => 'com.intuit.quickbooks.payment',
                'redirect_uri'   => rawurlencode($redirect_uri),
                'state'          => 'eeg_quickbooks_request_access_v2,' . $quickbooks_pm->slug() . ',' . $the_nonce
            ),
            'https://appcenter.intuit.com/connect/oauth2'
        );
        if ($client_id && $redirect_uri && $request_url) {
            echo wp_json_encode(array(
                'qb_success'  => true,
                'handle_return_url' => $request_url
            ));
            exit();
        } else {
            $err_msg = esc_html__('Data for the OAuth page could not be generated properly.', 'event_espresso');
        }

        // If we got here then something went wrong.
        echo wp_json_encode(array('qb_error' => $err_msg));
        exit();
    }


    /**
     *  Request an access token from Intuit.
     *
     * @return void
     */
    public static function request_access_token()
    {
        // Check if this is the webhook we expect and if all the needed parameters are present.
        if (! isset($_GET['state']) || ! $_GET['state']) {
            return;
        }
        $request_data = explode(',', $_GET['state']);
        if (! is_array($request_data) || ! $request_data[0] || $request_data[0] !== 'eeg_quickbooks_request_access_v2') {
            return;
        }
        $no_nonce_msg = esc_html__('Nonce fail.', 'event_espresso');
        if (
            ! isset($request_data[2])
            || ! $request_data[2]
            || ! wp_verify_nonce($request_data[2], 'eeg-quickbooks-oauth')
        ) {
            EE_QuickBooks_PM_Form::close_oauth_window($no_nonce_msg);
        }
        // Try getting the PM.
        $quickbooks_pm = EE_PMT_QuickBooks_Onsite::is_pm_valid($request_data[1], 'close');
        // Check for an error or maybe a missing auth code.
        if (isset($_GET['error']) || empty($_GET['code'])) {
            $error_desc = isset($_GET['error'])
                ? $_GET['error']
                : esc_html__('Missing some authentication data !', 'event_espresso');
            EE_QuickBooks_PM_Form::close_oauth_window($error_desc);
        }

        // If we passed all the above we can assume that all is fine and we have a correct request.
        $client_id = $quickbooks_pm->get_extra_meta('consumer_key', true);
        $client_secret = $quickbooks_pm->get_extra_meta('shared_secret', true);
        $redirect_uri = site_url();
        $redirect_uri = trailingslashit($redirect_uri);
        $oauth_args = array(
            'code'         => $_GET['code'],
            'grant_type'   => 'authorization_code',
            'redirect_uri' => $redirect_uri
        );
        $post_args = array(
            'method'      => 'POST',
            'timeout'     => 60,
            'redirection' => 5,
            'blocking'    => true,
            'headers'     => array(
                'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $client_secret),
            ),
            'body'        => $oauth_args
        );
        $post_url = 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer';
        // Request the token.
        $response = wp_remote_post($post_url, $post_args);
        // Try updating the data. This checks the response.
        $response_msg = EED_QuickBooks_Oauth_v2::update_oauth_data($response, $quickbooks_pm);
        // Done. Close the window.
        EE_QuickBooks_PM_Form::close_oauth_window($response_msg);
    }


    /**
     *  Check on the OAuth status.
     *
     * @return void
     */
    public static function update_oauth_status()
    {
        $quickbooks_pm = EEM_Payment_Method::instance()->get_one_of_type($_POST['submitted_pm']);
        if (! $quickbooks_pm instanceof EE_Payment_Method) {
            echo json_encode( array(
                'oauthed' => false
            ));
            exit();
        }
        $access_token = $quickbooks_pm->get_extra_meta('access_token', true);

        if ($access_token && ! empty($access_token)) {
            echo json_encode(array(
                'oauthed' => true
            ));
            exit();
        } else {
            echo json_encode(array(
                'oauthed' => false
            ));
            exit();
        }
    }


    /**
     *  Reset OAuthed data.
     *
     * @return void
     */
    public static function reset_oath_settings()
    {
        // Get the PM.
        $quickbooks_pm = EE_PMT_QuickBooks_Onsite::is_pm_valid($_POST, 'exit');

        // Delete the Token data.
        $quickbooks_pm->delete_extra_meta('access_token');
        $quickbooks_pm->delete_extra_meta('refresh_token');
        $quickbooks_pm->delete_extra_meta('x_refresh_token_expires_on');
        $quickbooks_pm->delete_extra_meta('expires_on');
        $quickbooks_pm->delete_extra_meta('token_type');

        echo json_encode(array(
            'qb_success' => true
        ));
        exit();
    }


    /**
     *  Refresh the access tokens.
     *
     * @param EE_Payment_Method  $quickbooks_pm
     * @param boolean            $allow_exit
     * @return void|array
     */
    public static function refresh_tokens($quickbooks_pm = null, $allow_exit = true)
    {
        if (! $quickbooks_pm instanceof EE_Payment_Method) {
            $afteraction = $allow_exit ? 'exit' : '';
            $quickbooks_pm = EE_PMT_QuickBooks_Onsite::is_pm_valid($_POST, $afteraction);
            // Here we may not receive the EE_Payment_Method.
            if (! $allow_exit && ! $quickbooks_pm instanceof EE_Payment_Method) {
                return $quickbooks_pm;
            }
        }
        // Prep the credentials.
        $client_id = $quickbooks_pm->get_extra_meta('consumer_key', true);
        $client_secret = $quickbooks_pm->get_extra_meta('shared_secret', true);
        $refresh_token = $quickbooks_pm->get_extra_meta('refresh_token', true);
        // Prep request data.
         $refresh_args = array(
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refresh_token
        );
        $post_args = array(
            'method'      => 'POST',
            'timeout'     => 60,
            'redirection' => 5,
            'blocking'    => true,
            'headers'     => array(
                'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $client_secret),
            ),
            'body'        => $refresh_args
        );
        $post_url = 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer';
        // Refresh the token.
        $response = wp_remote_post($post_url, $post_args);
        // Try updating the data. This checks the response.
        $response_msg = EED_QuickBooks_Oauth_v2::update_oauth_data($response, $quickbooks_pm);
        // Done.
        if ($allow_exit) {
            if ($response_msg === null) {
                echo json_encode(array(
                    'qb_success' => true
                ));
                exit();
            } else {
                echo wp_json_encode(array(
                    'qb_error' => $response_msg
                ));
                exit();
            }
        } else {
            return array('qb_error' => $response_msg);
        }
    }


    /**
     *  Revoke OAuth access.
     *
     * @return void
     */
    public static function revoke_access()
    {
        // Check if all the needed parameters are present.
        $quickbooks_pm = EE_PMT_QuickBooks_Onsite::is_pm_valid($_POST, 'exit');

        // Get the credentials.
        $client_id = $quickbooks_pm->get_extra_meta('consumer_key', true);
        $client_secret = $quickbooks_pm->get_extra_meta('shared_secret', true);
        $refresh_token = $quickbooks_pm->get_extra_meta('refresh_token', true);
        // Prep request data.
        $post_args = array(
            'method'      => 'POST',
            'timeout'     => 60,
            'redirection' => 5,
            'blocking'    => true,
            'headers'     => array(
                'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $client_secret),
            ),
            'body'        => array('token' => $refresh_token)
        );
        $post_url = 'https://developer.api.intuit.com/v2/oauth2/tokens/revoke';
        // Request the revocation.
        $response = wp_remote_post($post_url, $post_args);
        $response_msg = null;
        // Try to decode the response.
        $response_data = false;
        if (! is_wp_error($response) && isset($response['response']) && $response['response']) {
            $response_data = $response['response'];
        }
        // If decoded, check response data.
        if (
            isset($response_data['code'])
            && (
                $response_data['code'] == 200
                // Will get this error if already disconnected.
                || strpos($response_data['message'], 'oAuth request Error.') !== false
            )
        ) {
            $quickbooks_pm->delete_extra_meta('access_token');
            $quickbooks_pm->delete_extra_meta('refresh_token');
            $quickbooks_pm->delete_extra_meta('x_refresh_token_expires_on');
            $quickbooks_pm->delete_extra_meta('expires_on');
            $quickbooks_pm->delete_extra_meta('token_type');
            // All good, just exit.
            echo wp_json_encode(array(
                'qb_success' => true
            ));
            exit();
        } elseif (isset($response_data['message'])) {
            $response_msg = $response_data['message'];
        } else {
            $response_msg = esc_html__('Unknown response received!', 'event_espresso');
        }
        // If we got here then something went wrong.
        echo wp_json_encode(array('qb_error' => $response_msg));
        exit();
    }


    /**
     *  Parse the data and save OAuth granted data.
     *
     * @param array|WP_Error  $data
     * @param EE_Payment_Method  $quickbooks_pm
     * @return string|null
     */
    public static function update_oauth_data($data, $quickbooks_pm)
    {
        $response_msg = null;
        // Try to decode the response.
        $response_body = false;
        if (! is_wp_error($data) && isset($data['body']) && $data['body']) {
            $response_body = json_decode($data['body']);
        }
        // If decoded, check response data.
        if ($response_body && isset($response_body->access_token)) {
            $quickbooks_pm->update_extra_meta('access_token', $response_body->access_token);
            $quickbooks_pm->update_extra_meta('refresh_token', $response_body->refresh_token);
            $quickbooks_pm->update_extra_meta('x_refresh_token_expires_on',
                isset($response_body->x_refresh_token_expires_in)
                    ? time() + (int)$response_body->x_refresh_token_expires_in : time());
            $quickbooks_pm->update_extra_meta('expires_on',
                isset($response_body->expires_in) ? time() + (int)$response_body->expires_in : time());
            $quickbooks_pm->update_extra_meta('token_type',
                isset($response_body->token_type) ? $response_body->token_type : '');
        } elseif (isset($response_body->error_description)) {
            $response_msg = $response_body->error_description;
        } else {
            $response_msg = esc_html__('Request error. Unknown response received.', 'event_espresso');
        }
        return $response_msg;
    }


    /**
     *  Check if the access token is still valid. Update it if needed.
     *
     * @param EE_Payment_Method  $quickbooks_pm
     * @return string|null
     */
    public static function check_access_token(EE_Payment_Method $quickbooks_pm)
    {
        $expires_on = $quickbooks_pm->get_extra_meta('expires_on', true);
        // Get diff in minutes. Access token lives only 1 hour.
        $time_diff = floor(((int)$expires_on - time()) / 60);
        if ($time_diff <= 5) {
            EED_QuickBooks_Oauth_v2::refresh_tokens($quickbooks_pm, false);
            return true;
        } else {
            return false;
        }
    }
}