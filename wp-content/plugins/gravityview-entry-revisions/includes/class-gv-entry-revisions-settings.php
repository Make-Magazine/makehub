<?php

if ( ! class_exists( 'GFForms' ) ) {
	return;
}

GFForms::include_addon_framework();

class GV_Entry_Revisions_Settings extends GFAddOn {

	/**
	 * @var string Minimum required GF version
	 */
	protected $_min_gravityforms_version = '2.0';

	/**
	 * @var string Slug for settings page URL
	 */
	protected $_slug = 'gravityview-entry-revisions';

	/**
	 * @var string Path used by GF to add Settings link to addon settings in Plugins page
	 */
	protected $_path = 'gravityview-entry-revisions/gravityview-entry-revisions.php';

	protected $_full_path = __FILE__;

	protected $_title = 'GravityView - Entry Revisions';

	protected $_short_title = 'GravityView Plugins';

	private static $_instance = null;

	private $_did_license_refresh = false;

	/** @var string The setting name for default Inline Edit support. */
	const INLINE_EDIT_GLOBAL_SETTING = 'inline_edit_create_revisions';

	/** @var string The setting name for per-form Inline Edit support. */
	const INLINE_EDIT_FORM_SETTING = 'inline_edit_create_revisions_per_form';

	public function __construct() {

		if ( self::$_instance ) {
			return self::$_instance;
		}

		$this->_short_title = esc_html__( 'Entry Revisions', 'gravityview-entry-revisions' );

		add_filter( 'gform_form_settings_fields', array( $this, 'add_form_settings_fields' ), 20 );

		parent::__construct();
	}

	/**
	 * Adds a per-form option to enable or disable Entry Revisions for Inline Edit changes.
	 *
	 * @since 1.2
	 *
	 * @param array $fields Form settings fields.
	 *
	 * @return array
	 */
	public function add_form_settings_fields( $fields ) {

		$inline_edit_exists = defined( 'GRAVITYVIEW_INLINE_VERSION' );

		$setting = $this->get_plugin_setting(self::INLINE_EDIT_GLOBAL_SETTING );

		// If global settings haven't yet been configured, set to enabled.
		if ( is_null( $setting ) ) {
			$setting = '1';
		}

		// If Inline Edit isn't active, don't remove the form setting if it has been already set.
		if ( ! $inline_edit_exists && ! is_null( $setting ) ) {

			$fields['form_options']['fields'][] = array(
				'name'          => self::INLINE_EDIT_FORM_SETTING,
				'type'          => 'hidden',
				'value' => $setting,
			);

			return $fields;
		}

		$fields['form_options']['fields'][] = array(
			'name'          => self::INLINE_EDIT_FORM_SETTING,
			'type'          => 'radio',
			'label'         => esc_html__( 'Inline Edit Behavior', 'gravityview-entry-revisions' ),
			'description'   => '<p class="clear">' . esc_html__( 'Should edits made using the Inline Edit plugin create revisions?', 'gravityview-entry-revisions' ) . '</p>',
			'default_value' => $setting,
			'dependency'    => array(
				'live'   => true,
				'fields' => array(
					array(
						'field'  => 'gv_inline_edit_enable',
						'values' => array( '1', true ),
					),
				),
			),
			'choices'       => array(
				array( 'label' => esc_html__( 'Add revisions for edits made using Inline Edit', 'gravityview-entry-revisions' ), 'value' => '1' ),
				array( 'label' => esc_html__( 'Ignore edits made using Inline Edit', 'gravityview-entry-revisions' ), 'value' => '0' ),
			),
		);

		return $fields;
	}

	/**
	 * Returns TRUE if the settings "Save" button was pressed
	 *
	 * @since 1.0.3 Fixes conflict with Import Entries plugin
	 *
	 * @return bool True: Settings form is being saved and the Entry Revisions setting is in the posted values (form settings)
	 */
	public function is_save_postback() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		return ! rgempty( 'gform-settings-save' ) && ( isset( $_POST['gform_settings_save_nonce'] ) || isset( $_POST['_gravityview-entry-revisions_save_settings_nonce'] ) );
	}

	/**
	 * Get the one instance of the object
	 *
	 * @since 1.0
	 *
	 * @return GV_Entry_Revisions_Settings
	 */
	public static function get_instance() {

		if ( self::$_instance == null ) {

			self::$_instance = new self();

			GFAddOn::register( __CLASS__ );
		}

		return self::$_instance;
	}

	/**
	 * Register the settings field for the EDD License field type
	 *
	 * @since 1.0
	 *
	 * @param array $field
	 * @param bool $echo Whether to echo the
	 *
	 * @return string
	 */
	public function settings_edd_license( $field, $echo = true ) {

		// Otherwise, it'd be output as an attribute. Didn't want to use the `gaddon_no_output_field_properties` filter
		unset( $field['description'] );

		$return = self::settings_text( $field, false );

		if ( $echo ) {
			echo $return;
		}

		return $return;
	}

	/**
	 * Perform the call to EDD based on the AJAX call or passed data
	 *
	 * @since 1.0
	 *
	 * @param array $settings {
	 * @type string $edd_action The EDD action to perform, like `check_license`
	 * @type string $license The license key
	 * @type string $format If `object`, return the object of the license data. `array` for array, `json` to return the JSON-encoded object. [Default: "array"]
	 * }
	 *
	 * @return mixed|WP_Error
	 */
	public function license_call( $settings = array() ) {

		$current_settings = $this->get_current_settings();

		$data = wp_parse_args( $settings, array(
			'edd_action' => 'activate_license',
			'license_key'    => rgar( $current_settings, 'license_key' ),
			'format'     => 'array',
		) );

		$api_params = array(
			'edd_action' => rgar( $data, 'edd_action' ),
			'version'   => GV_ENTRY_REVISIONS_VERSION,
			'license'   => rgar( $data, 'license_key' ),
			'item_name' => $this->_title,
			'item_id'   => GV_Entry_Revisions::get_instance()->get_item_id(),
			'author'    => 'GravityView',
			'url'       => home_url(),
		);

		$url = add_query_arg( $api_params, 'https://gravityview.co' );

		$response = wp_remote_get( $url, array(
			'timeout'   => 15,
			'sslverify' => false,
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// Not JSON; there's been an error.
		if ( empty( $license_data ) ) {
			return new WP_Error( 'invalid_response', 'There was a problem connecting to the GravityView licensing server.' );
		}

		$license_data->message = $this->get_license_message( $license_data );

		// Store the license key inside the data array
		$license_data->license_key = rgar( $data, 'license_key' );

		switch( rgar( $data, 'format' ) ) {
			case 'object':
				$return = $license_data;
				break;
			case 'json':
				$return = json_encode( $license_data );
				break;
			case 'array':
			default:
				$return = (array) $license_data;
				break;
		}

		return $return;
	}

	/**
	 * Generate the status message displayed in the license field
	 *
	 * @since 1.0
	 *
	 * @param object|null $license_data Remote response from GravityView license call, or NULL if bad JSON returned
	 *
	 * @return string
	 */
	private function get_license_message( $license_data ) {

		if ( empty( $license_data ) ) {
			$message = $this->strings( 'error' );
		} else {
			$key = ! empty( $license_data->error ) ? $license_data->error : $license_data->license;

			$message = $this->strings( $key, $license_data );
		}

		return sprintf( '<p><strong>%s: %s</strong></p>', $this->strings( 'status' ), $message );
	}

	/**
	 * Have one place for all the various strings involved in license validation
	 *
	 * @since 1.0
	 *
	 * @param  array|null $status Status to get. If empty, get all strings.
	 * @param  object|null $license_data Response from GravityView license server
	 *
	 * @return array          Modified array of content
	 */
	public function strings( $status = NULL, $license_data = null ) {

		$renewal_url = ! empty( $license_data->renewal_url ) ? $license_data->renewal_url : 'https://gravityview.co/account/';

		$strings = array(
			'status'              => esc_html__( 'Status', 'gravityview-entry-revisions' ),
			'error'               => esc_html__( 'There was an error processing the request.', 'gravityview-entry-revisions' ),
			'failed'              => esc_html__( 'Could not deactivate the license. The license key you attempted to deactivate may not be active or valid.', 'gravityview-entry-revisions' ),
			'site_inactive'       => esc_html__( 'The license key is valid, but it has not been activated for this site.', 'gravityview-entry-revisions' ),
			'no_activations_left' => esc_html__( 'Invalid: this license has reached its activation limit.', 'gravityview-entry-revisions' ),
			'deactivated'         => esc_html__( 'The license has been deactivated.', 'gravityview-entry-revisions' ),
			'valid'               => esc_html__( 'The license key is valid and active.', 'gravityview-entry-revisions' ),
			'invalid'             => esc_html__( 'The license key entered is invalid.', 'gravityview-entry-revisions' ),
			'missing'             => esc_html__( 'The license key entered is invalid.', 'gravityview-entry-revisions' ), // Missing is "the license couldn't be found", not "you submitted an empty license"
			'revoked'             => esc_html__( 'This license key has been revoked.', 'gravityview-entry-revisions' ),
			'invalid_item_id'     => esc_html__( 'This license key does not have access to this plugin.', 'gravityview-entry-revisions' ),
			'expired'             => sprintf( esc_html__( 'This license key has expired. %sRenew your license on the GravityView website%s', 'gravityview-entry-revisions' ), '<a href="' . esc_url( $renewal_url ) . '" rel="external">', '</a>' ),
			'verifying_license'   => esc_html__( 'Verifying license&hellip;', 'gravityview-entry-revisions' ),
			'activate_license'    => esc_html__( 'Activate License', 'gravityview-entry-revisions' ),
			'deactivate_license'  => esc_html__( 'Deactivate License', 'gravityview-entry-revisions' ),
			'check_license'       => esc_html__( 'Verify License', 'gravityview-entry-revisions' ),
		);

		if ( empty( $status ) ) {
			return $strings;
		}

		if ( isset( $strings[ $status ] ) ) {
			return $strings[ $status ];
		}

		return NULL;
	}

	/**
	 * When the plugin settings are saved, validate the license key
	 *
	 * @since 1.0
	 *
	 * @param array $field Setting field meta
	 * @param array $settings Submitted settings
	 *
	 * @return bool|null True if valid or license key is empty; false if any other situation occurs
	 */
	public function validate_edd_license_settings( $field = array(), $settings = array() ) {

		if( '' === rgar( $settings, 'license_key' ) ) {
			return true;
		}

		$license = $this->get_license( false, array(
			'license'    => rgar( $settings, 'license_key' ),
			'format'     => 'array',
			'edd_action' => 'activate_license',
		) );

		$status = rgar( $license, 'license' );

		switch ( $status ) {
			case 'valid':
				$return = true;
				break;
			case 'invalid':
			default:
				$return = false;
				$this->set_field_error( $field, $license['message'] );
				break;
		}

		// Regardless of the results, save the settings with posted values, because by default,
		// GF won't save if there's an error.
		$this->update_plugin_settings( $settings );

		return $return;
	}

	/**
	 * Show a x or check if valid or invalid, and if error, set the field error message
	 *
	 * If the plugin hasn't been saved, no error or checkbox
	 *
	 * @since 1.0
	 *
	 * @param string $value The setting for a specific field/input
	 * @param array $field The current plugin setting field (from GV_Entry_Revisions_Settings::plugin_settings_fields)
	 *
	 * @return bool|null True: no errors, valid. False: errors, not valid. NULL: settings not yet saved.
	 */
	public function setup_field_validation_feedback( $value, $field ) {

		// If the plugin settings haven't been saved yet, no error
		if ( ! $this->get_plugin_settings() ) {
			return null;
		}

		$license = $this->get_license();

		// Show the stored error message
		if( ! $success = rgar( $license, 'success' ) ) {
			$this->set_field_error( $field, rgar( $license, 'message' ) );
		}

		$errors = $this->get_field_errors( $field );

		return empty( $errors );
	}

	/**
	 * @param object|array|string|WP_Error $response
	 */
	public function set_license_response( $response ) {
		set_transient( 'gv-entry-revisions-license', (array) $response, DAY_IN_SECONDS );
	}

	/**
	 * Get license data from the website
	 *
	 * @uses license_call()
	 *
	 * @param array $settings {
	 * @type string $edd_action The EDD action to perform, like `check_license`
	 * @type string $license The license key
	 * @type string $format If `object`, return the object of the license data. `array` for array, `json` to return the JSON-encoded object. [Default: "array"]
	 * }
	 *
	 * @return array|object|string|WP_Error Returns license data in the format specified (default: array). If error, returns WP_Error.
	 */
	public function fetch_license( $params = array() ) {

		$license_response = $this->license_call( $params );

		$this->set_license_response( $license_response );

		$this->_did_license_refresh = true;

		return $license_response;
	}

	/**
	 * Get license information for this plugin
	 *
	 * Fetches fresh information if the plugin's GF settings have been saved, or if $force_refresh is true
	 *
	 * @param bool $force_refresh Whether to force fetching fresh data about the license from the website
	 * @param array $params
	 *
	 * @return array License details. If error, returns array with error message.
	 */
	public function get_license( $force_refresh = false, $params = array() ) {

		$license = get_transient( 'gv-entry-revisions-license' );

		$force_refresh = ( $force_refresh || $this->is_save_postback() );

		if ( empty( $license ) || $force_refresh ) {
			if( empty( $this->_did_license_refresh ) ) {
				$license = $this->fetch_license( $params );
			}
		}

		if ( is_wp_error( $license ) ) {
			$license = array(
				'success' => false,
				'message' => $license->get_error_message(),
			);
		}

		return (array) $license;
	}

	/**
	 * Override the invalid field icon so there aren't duplicate icons, and so we can format the error message
	 *
	 * @since 1.0
	 *
	 * @param array $field - The field meta.
	 *
	 * @return string - The full markup for the error
	 */
	public function get_error_icon( $field ) {

		$error = $this->get_field_errors( $field );

		return '<div class="alert_red clear" style="padding: 6px 10px 10px;">' . $error . '</div>';
	}

	/**
	 * Define the plugin addon settings
	 *
	 * @since 1.0
	 *
	 * @return array Array that contains plugin settings
	 */
	public function plugin_settings_fields() {

		$inline_edit_exists = defined( 'GRAVITYVIEW_INLINE_VERSION' );

		$fields = array(
			array(
				'name'          => 'license_key',
				'required'      => true,
				'label'         => esc_html__( 'License Key', 'gravityview-entry-revisions' ),
				'description'   => '<p class="clear">' . esc_html__( 'Enter the GravityView license key that was sent to you on purchase. This enables plugin updates &amp; support.', 'gravityview-entry-revisions' ) . '</p>',
				'type'          => 'edd_license',
				'feedback_callback' => array( $this, 'setup_field_validation_feedback' ),
				'placeholder'   => esc_html__('Enter your GravityView license key here', 'gravityview-entry-revisions' ),
				'default_value' => '',
				'class'         => 'activate code regular-text edd-license-key',
			),
		);

		if ( $inline_edit_exists ) {
			$fields[] = array(
				'name'          => self::INLINE_EDIT_GLOBAL_SETTING,
				'label'         => esc_html__( 'Default Inline Edit Behavior', 'gravityview-entry-revisions' ),
				'description'   => '<p class="clear">' . esc_html__( 'Should edits made using the Inline Edit plugin create revisions?', 'gravityview-entry-revisions' ) . ' ' . esc_html__( 'Note: This is the global default. You may override this setting in a form&rsquo;s Settings page.', 'gravityview-entry-revisions' ) . '</p>',
				'type'          => 'radio',
				'default_value' => '1',
				'choices'       => array(
					array( 'label' => esc_html__( 'Add revisions for edits made using Inline Edit', 'gravityview-entry-revisions' ), 'value' => '1' ),
					array( 'label' => esc_html__( 'Ignore edits made using Inline Edit', 'gravityview-entry-revisions' ), 'value' => '0' ),
				),
			);
		}

		return array(
			array(
				'title'  => '',
				'fields' => $fields,
			)
		);
	}

	/**
	 * Don't show the uninstall form
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function render_uninstall() {}

}