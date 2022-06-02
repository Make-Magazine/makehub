<?php
/*
Plugin Name: WP Voting Contest - Judging
Version: 1.0.4
Description: Wp Voting Plugin - It allows judges to Judge a contestant based upon the corresponding settings.
Author: Ohio Web Technologies
Author URI: http://www.ohiowebtech.com
Copyright (c) 2008-2014 Ohio Web Technoloselectgies All Rights Reserved.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/
//ini_set('display_errors', 1);
//error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
error_reporting(0);
defined( 'ABSPATH' ) or die( 'Direct script access disallowed.' );


class wp_voting_judging
{
	function __construct()
	{
		
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		register_activation_hook( __FILE__, array($this,'ow_judging_admin_notice_activation_hook') );
		add_action('admin_notices', array($this,'plugin_admin_notice'));		
	
		//Check Core is active or not
		if(is_plugin_active('wp-voting-contest/ow_votes.php')){
			require_once('configuration/config.php');			

			add_action( 'admin_init',array($this,'wpvc_judging_version_updater_admin')  );

			add_filter('wpvc_extension_licenses',array($this,'wpvc_judging_license'), 10);
			add_action('admin_init', array($this,'wpvc_software_register_option'));
			add_action('admin_init', array($this,'wpvc_software_activate_license'));
			add_action( 'admin_notices', array($this,'wpvc_judging_license_notices') );
			
		}
	}

	function ow_judging_admin_notice_activation_hook(){ 
		//If not active set transient
		if(!is_plugin_active('wp-voting-contest/ow_votes.php')){
			set_transient( 'ow_judging_check', true, 5);			
		}
		else{
			require_once('configuration/installation.php');
			wpvc_create_judging_table();
			add_role( 'wpvc_judge_role', 'Voting Judge', array( 'read' => true, 'level_0' => true ) );
		}
	}

	function plugin_admin_notice(){
		if( get_transient( 'ow_judging_check' ) ){
			?>
			<div class="error notice">
				<p><strong>Please enable WP Voting Contest Core Plugin</strong>.</p>
			</div>
			<?php
			/* Delete transient, only display this notice once. */
			delete_transient( 'ow_judging_check' );
		}
	}

	
	function wpvc_judging_version_updater_admin()
	{		
		$license_key = trim( get_option( 'wp_voting_judging_license_key' ) );
		$edd_updater = new Wpvc_Voting_Judge_Updater( OW_WP_VOTING_JUDGE_STORE_API_URL, __FILE__, array(
				'version' 	=> '1.0.4',
				'license' 	=> $license_key,
				'item_id'   => WPVC_JUDGE_PRODUCT_ID,
				'author' 	=> 'Ohio Web Technologies',
			)
		);
	}

	//Licensing & Updater
	public function wpvc_judging_license(){
		$license = get_option( 'wp_voting_judging_license_key' );
		$status  = get_option( 'wp_voting_judging_license_status' );
		echo '<div data-key="'. $license .'"  data-status="'. $status .'" id="judge_statuskey" ></div>';
	}

	public function wpvc_software_register_option() {
		register_setting('wp_voting_judging_license', 'wp_voting_judging_license_key', array($this,'wpvc_sanitize_license') );
	}

	public function wpvc_sanitize_license($new) {
		$old = get_option( 'wp_voting_judging_license_key' );
		if( $old && $old != $new ) {
			delete_option( 'wp_voting_judging_license_status' );
		}
		return $new;
	}

	public function wpvc_judging_license_notices() {
		if ( isset( $_GET['wpvc_judge_activation'] ) && ! empty( $_GET['message'] ) ) {
			switch( $_GET['wpvc_judge_activation'] ) {
				case 'false':
					$message = urldecode( $_GET['message'] );
					?>
					<div class="error">
						<p><?php echo $message; ?></p>
					</div>
					<?php
					break;
				case 'true':
				default:					
					break;
			}
		}
	}

	public function wpvc_software_activate_license(){

		if( isset( $_POST['wpvc_judge_license_key'] ) ) {
			
					
			update_option( 'wp_voting_judging_license_key', $_POST['wpvc_judge_license_key'] );
			$license = trim( get_option( 'wp_voting_judging_license_key' ) );
			// data to send in our API request
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $license,
				'item_name'    => OW_WP_VOTING_JUDGE_PRODUCT_NAME,
				'url'        => home_url()
			);

			// Call the custom API.
			$response = wp_remote_post( OW_WP_VOTING_JUDGE_STORE_API_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
				$message =  ( is_wp_error( $response ) && ! empty( $response->get_error_message() ) ) ? $response->get_error_message() : __( 'An error occurred, please try again.' );
			} else {
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				

				if ( false === $license_data->success ) {
					switch( $license_data->error ) {
						case 'expired' :
							$message = sprintf(
								__( 'Your license key expired on %s.' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
							);
							break;
						case 'revoked' :
							$message = __( 'Your license key has been disabled.' );
							break;
						case 'missing' :
							$message = sprintf( __( 'Invalid license key for %s.' ), OW_WP_VOTING_JUDGE_PRODUCT_NAME );
							break;
						case 'invalid' :
						case 'site_inactive' :
							$message = __( 'Your license is not active for this URL.' );
							break;
						case 'item_name_mismatch' :
							$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), OW_WP_VOTING_JUDGE_PRODUCT_NAME );
							break;
						case 'no_activations_left':
							$message = __( 'Your license key has reached its activation limit.' );
							break;
						default :
							$message = __( 'An error occurred, please try again.' );
							break;
					}
				}
			}


			// Check if anything passed on a message constituting a failure
			if ( ! empty( $message ) ) {
				$base_url = admin_url( 'admin.php?page=wpvc-votes-license' );
				$redirect = add_query_arg( array( 'wpvc_judge_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );
				wp_redirect( $redirect );
				exit();
			}
			// $license_data->license will be either "valid" or "invalid"
			update_option( 'wp_voting_judging_license_status', $license_data->license );
			wp_redirect( admin_url( 'admin.php?page=wpvc-votes-license' ) );
			exit();
		}

	}
	
	

	
}

new wp_voting_judging();

