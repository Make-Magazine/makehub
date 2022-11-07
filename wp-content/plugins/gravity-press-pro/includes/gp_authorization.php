<?php
/**
 * Gravity Press Authorizations
 *
 *
 * @package Gravity Press
 * @version 1.0.0
 * @since   1.0.0
 */

/******* Define Constants ******/

// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'EDD_GRAVITYPRESS_STORE_URL', 'http://ristrettoapps.com' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the ID of your product. This is the ID of your product in EDD and should match the download ID in EDD exactly
define( 'EDD_GRAVITYPRESS_ITEM_ID', 14108 ); // IMPORTANT: change the name of this constant to something unique to prevent conflicts with other plugins using this system

// the name of the settings page for the license input to be displayed
define( 'EDD_GRAVITYPRESS_PLUGIN_LICENSE_PAGE', 'gf_settings&subview=gravitypress' );

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( GRAVITYPRESS_PLUGIN_DIR . '/includes/EDD_SL_Plugin_Updater.php' );
}

function edd_sl_gravitypress_plugin_updater() {

	// retrieve our license key from the DB
	$license_key = gp_get_license_key();

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( EDD_GRAVITYPRESS_STORE_URL, GRAVITYPRESS_PLUGIN_DIR . '/gravitypress.php', array(
			'version' 	=> GRAVITYPRESS_VERSION, 				// current version number
			'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
			'item_id' => EDD_GRAVITYPRESS_ITEM_ID,       // ID of the product
			'author' 	=> 'Kevin Marshall',  // author of this plugin
			'beta'		=> false
		)
	);

}
add_action( 'admin_init', 'edd_sl_gravitypress_plugin_updater', 0 );

function edd_check_license($apikey) {

	$message = '';
	if (isset($_POST['_gform_setting_edd_gravitypress_license_key'])){
		// retrieve the license from the database
		$license = gp_get_license_key();

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_id'    => EDD_GRAVITYPRESS_ITEM_ID, // The ID of the item in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( EDD_GRAVITYPRESS_STORE_URL, array( 'timeout' => 30, 'sslverify' => false, 'body' => $api_params ) );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		update_option( 'edd_gravitypress_license_status', $license_data->license );

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

					$message = __( 'Invalid license.' );
					break;

				case 'invalid' :
				case 'site_inactive' :

					$message = __( 'Your license is not active for this URL.' );
					break;

				case 'item_name_mismatch' :

					$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), EDD_GRAVITYPRESS_ITEM_NAME );
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

	$status      = get_option( 'edd_gravitypress_license_status' );

	echo "<b>Key status: </b>&nbsp;";

	if( !empty($message) ){
		echo '<span style="color:red"> <i class="fa icon-warning fa-warning"></i> '.$message.'</span>';
	}elseif( $status != false && $status == 'valid' ){
		echo '<span style="color:green"><i class="fa icon-check fa-check"></i> Active</span>';
	}else{
		echo '<span style="color:red"><i class="fa icon-remove fa-times"></i> Inactive</span>';
	}
}

function gravitypress_admin_notice_getstarted() {

    //check license validity
    $license_status = get_option('edd_gravitypress_license_status');
    $status  = get_option( 'edd_gravitypress_license_status' );

    //Check that the user hasn't already clicked to ignore the message  or if they've already activated license
    if ( $license_status != 'valid' && current_user_can( 'administrator' ) ) {
        echo '<div class="error"><p>';
        printf(__('You have an invalid or expired license key for <i>Gravity Press</i>. Please <a href="admin.php?page=gf_settings&subview=gravitypress">Activate Your License</a> to correct this issue</span>'), '');
        echo "</p></div>";
        //You have invalid or expired license keys for Easy Digital Downloads. Please go to the Licenses page to correct this issue.
    }
}

/**
 * Admin notice to deactivate the Subscription Support Plugin in latest version of GravityPress plugin
 */
function gp_admin_notice_deactivate_sub_plugin(){
	//check if Subscription Support Plugin is activated
	if (defined('GP_SUBSCRIPTION_SUPPORT')) {
		$deactivate_url =  wp_nonce_url('plugins.php?action=deactivate&amp;plugin=' . urlencode( 'gravity-press-subscription-support/gp_subscription_support.php' ) . '&amp;plugin_status=all&paged=1&s', 'deactivate-plugin_gravity-press-subscription-support/gp_subscription_support.php');
		echo '<div class="error"><p>';
		printf(__('This version of <b>Gravity Press Pro</b> includes all previous features of <b>Gravity Press Subscription Support</b> plugin, meaning there is no need to have both activated.
		 <a href="%s">Click to deactivate</a> the <b>Gravity Subscription Support</b> Plugin now.'), $deactivate_url);
        echo "</p></div>";
	}
}
add_action( 'admin_notices', 'gp_admin_notice_deactivate_sub_plugin' );

/**
 * GET LICENSE KEY
 */
function gp_get_license_key(){
	if ( ! class_exists( 'GFForms' ) ) {
		return '';
	}
	$license = trim(gp_addon()->get_plugin_setting( 'edd_gravitypress_license_key' ));
	return $license;
}
