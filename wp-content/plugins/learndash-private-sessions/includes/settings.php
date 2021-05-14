<?php

/**
* Private Sessions Settings class
*/
class Ldms_Private_Sessions {

	/**
	 * Label fields
	 * @var array
	 */
	private $fields;

	/**
	 * Construct
	 */
	public function __construct() {
		$this->fields   = $this->settings_fields();

		add_action( 'admin_enqueue_scripts', array( $this, 'ldms_admin_enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'ldms_register_setting' ) );
		add_action( 'admin_menu', array( $this, 'ldms_admin_menu' ) );

		add_filter( 'learndash_admin_tabs', array( $this, 'ldms_settings_tab' ) );

	}

	/**
	 * Register settings page
	 */
	public function ldms_admin_menu() {

		add_submenu_page( 'learndash-lms-non-existant',__('LearnDash Private Sessions','ldmessenger'), __('LearnDash Private Sessions','ldmessenger'), 'manage_options', 'learndash-private-sessions', array( $this, 'ldms_admin_page' ) );

	}

	function ldms_settings_tab( $admin_tabs ) {

	    $admin_tabs['private_sessions'] = array(
	        'link'  =>  'admin.php?page=learndash-private-sessions',
	        'name'  =>  __( 'Private Sessions', 'ldmessenger' ),
	        'id'    =>  'admin_page_learndash-private-sessions',
	        'menu_link' =>  'edit.php?post_type=sfwd-courses&page=sfwd-lms_sfwd_lms.php_post_type_sfwd-courses'
	    );

	    return $admin_tabs;

	}

	function learndash_private_session_admin_tabs_on_page( $admin_tabs_on_page, $admin_tabs, $current_page_id ) {

		return $admin_tabs_on_page;

	}

	public function ldms_admin_enqueue_scripts() {

		global $learndash_assets_loaded;

		$screen = get_current_screen();

		if( 'admin_page_learndash-private-sessions' != $screen->id ) return;

		wp_enqueue_style(
			'learndash_style',
			LEARNDASH_LMS_PLUGIN_URL . 'assets/css/style'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css',
			array(),
			LEARNDASH_VERSION
		);
		$learndash_assets_loaded['styles']['learndash_style'] = __FUNCTION__;

		wp_enqueue_style(
			'sfwd-module-style',
			LEARNDASH_LMS_PLUGIN_URL . '/assets/css/sfwd_module'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css',
			array(),
			LEARNDASH_VERSION
		);
		$learndash_assets_loaded['styles']['sfwd-module-style'] = __FUNCTION__;

		wp_enqueue_script(
			'sfwd-module-script',
			LEARNDASH_LMS_PLUGIN_URL . '/assets/js/sfwd_module'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.js',
			array( 'jquery' ),
			LEARNDASH_VERSION,
			true
		);
		$learndash_assets_loaded['scripts']['sfwd-module-script'] = __FUNCTION__;

		wp_localize_script( 'sfwd-module-script', 'sfwd_data', array() );

	}


	/**
	 * Register settings for custom label
	 */
	public function ldms_register_setting() {
		register_setting( 'ldms_private_sessions_settings_group', 'ldms_private_sessions_settings', array( $this, 'ldms_sanitize_setting' ) );
	}

	/**
	 * Sanitize setting inputs
	 * @param  array $inputs Settings inputted
	 * @return array         Sanitized settings
	 *
	 * TODO ADD FIGURE OUT HOW TO SANIZE THE FIELDS
	 */

	public function ldms_sanitize_setting( $inputs ) {

		$settings = get_option( 'ldms_private_sessions_settings', array() );

		foreach ( $inputs as $key => $input ) {

			if(  $key == 'ldms_email_rely_message' ||  $key == 'ldms_email_message' ){
				$inputs[ $key ] = wp_kses_post( $input );
			}elseif( $key == 'ldms_users_can_start' || $key == 'ldms_user_moderators' || $key == 'ldms_groups_can_start' ){
				//remove default
				unset($input['default']);
				$inputs[ $key ] = array_map( 'sanitize_text_field', wp_unslash( $input ) );
			}else{
				$inputs[ $key ] = sanitize_text_field( $input );
			}
		}

		return apply_filters( 'ldms_private_sessions_sanitized_settings', array_merge( $settings, $inputs ) );

	}

	/**
	 * Output settings page
	 */
	public function ldms_admin_page() {

		wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_style( 'wp-color-picker' );

		?>
		<div id="ldmessenger-settings" class="wrap">
			<h1><?php _e( 'Private Sessions', 'ldmessenger' ); ?></h1>
			<form method="post" action="options.php">
				<div class="sfwd_options_wrapper sfwd_settings_left">
					<div id="advanced-sortables" class="meta-box-sortables">
						<div id="sfwd-courses_metabox" class="postbox ldmessenger-settings-postbox">
							<div class="handlediv" title="<?php _e( 'Click to toggle', 'ldmessenger' ); ?>"><br></div>
							<h3 class="hndle"><span><?php _e( 'Private Sessions', 'ldmessenger' ); ?></span></h3>
							<div class="inside">
								<div class="sfwd sfwd_options sfwd-courses_settings">
									<?php settings_fields( 'ldms_private_sessions_settings_group' ); ?>
									<?php foreach ( $this->fields as $key => $field ) : ?>
										<?php $field['id'] = $key; ?>

										<div class="sfwd_input " id="sfwd-custom-label_<?php echo $key; ?>">
											<span class="sfwd_option_label" style="text-align:right;vertical-align:top;">
												<a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-custom-label_<?php echo $key; ?>_tip');">
													<img src="<?php echo LEARNDASH_LMS_PLUGIN_URL . 'assets/images/question.png' ?>">
													<label class="sfwd_label textinput"><?php echo $field['label']; ?></label>
												</a>
											</span>
											<span class="sfwd_option_input">
												<div class="sfwd_option_div">

													<?php $callback = $field['type'] . '_callback'; ?>

													<?php $this->$callback( $field ); ?>

												</div>
												<div class="sfwd_help_text_div" style="display:none" id="sfwd-custom-label_<?php echo $key; ?>_tip"><label class="sfwd_help_text"><?php echo $field['desc']; ?></label></div>
											</span>
											<p style="clear:left"></p>
										</div>

									<?php endforeach; ?>
								</div>
							</div>

						</div>
					</div>
				</div>
				<p class="submit" style="clear: both;">
				<?php submit_button(); ?>
				</p>
			</form>
		</div>
		<script>
			jQuery(document).ready(function($) {
				$( '.wp-color-picker' ).wpColorPicker();
			});
		</script>
		<?php
	}

	/**
	 * Define settings fields
	 * @return array Array of settings fields for custom label
	 */
	public function settings_fields() {
		$settings = get_option( 'ldms_private_sessions_settings', array() );

		$fields = array(
			'ldms_license_key' => array(
				'name'  => 'ldms_license_key',
				'type'  => 'license_key',
				'label' => __( 'License Key', 'ldmessenger' ),
				'desc'  => __( 'Enter and activate your license key', 'ldmessenger' ),
				'value' => isset( $settings['ldms_license_key'] ) ? $settings['ldms_license_key'] : '',
			),
			'ldms_indicator_location' => array(
				'name'  => 'ldms_indicator_location',
				'type'  => 'dropdown',
				'label' => __( 'Indicator Location', 'ldmessenger' ),
				'desc'  => __( 'Where would you like the Session Locator?', 'ldmessenger' ),
				'value' => isset( $settings['ldms_indicator_location'] ) ? $settings['ldms_indicator_location'] : '',
				'options' => array(
                                'top'   =>  __( 'Attached to Top', 'ldmessenger' ),
                                'bottom'    =>  __( 'Attached to Bottom', 'ldmessenger' ),
                                'right'     =>  __( 'Attached to the right of the screen', 'ldmessenger' ),
								'shortcode'	=>	__( 'Embed using [sessions_widget] shortcode', 'ldmessenger' ),
								'widget'	=>	__( 'Embed using a widget', 'ldmessenger' )
                            )
			),
			'ldms_indicator_position' => array(
				'name'  => 'ldms_indicator_position',
				'type'  => 'number',
				'label' => __( 'Indicator Spacing', 'ldmessenger' ),
				'desc'  => __( 'How far from the edge do you want the tab to appear (in pixels)?', 'ldmessenger' ),
				'value' => isset( $settings['ldms_indicator_position'] ) ? $settings['ldms_indicator_position'] : '',
			),
			'ldms_accent_color'	=>	array(
				'name'  => 'ldms_accent_color',
				'type'  => 'color',
				'label' => __( 'Accent Color', 'ldmessenger' ),
				'desc'  => __( 'What color would you like to use as a background accent?', 'ldmessenger' ),
				'value' => isset( $settings['ldms_accent_color'] ) ? $settings['ldms_accent_color'] : '#DB4902',
			),
			'ldms_accent_txt_color'	=>	array(
				'name'  => 'ldms_accent_txt_color',
				'type'  => 'color',
				'label' => __( 'Accent Text Color', 'ldmessenger' ),
				'desc'  => __( 'What color would you like to use as a text accent?', 'ldmessenger' ),
				'value' => isset( $settings['ldms_accent_txt_color'] ) ? $settings['ldms_accent_txt_color'] : '#ffffff',
			),
			'ldma_sessions_page' => array(
				'name'  => 'ldma_sessions_page',
				'type'  => 'page_dropdown',
				'label' => __( 'Private Sessions Page', 'ldmessenger' ),
				'desc'  => __( 'Select the page with the private sessions shortcode. The page "Private Sessions" was created and the shortcode was added when you activate the plugin. Feel free to pick another page that you have added the shortcode on', 'ldmessenger' ),
				'value' => isset( $settings['ldma_sessions_page'] ) ? $settings['ldma_sessions_page'] : '',
			),
			'ldms_file_uploads'	=>	array(
				'name'	=>	'ldms_file_uploads',
				'type'	=>	'dropdown',
				'label'	=>	__( 'File Uploads', 'ldmessenger' ),
				'desc'	=>	__( 'Enable or disable uploads to the private sessions page', 'ldmessenger' ),
				'value'	=>	isset( $settings['ldms_file_uploads'] ) ? $settings['ldms_file_uploads'] : '',
				'options'	=>	array(
					'enabled'	=>	__( 'Enabled', 'ldmessenger' ),
					'disabled'	=>	__( 'Disabled', 'ldmessenger' ),
				)
			),
			'ldms_send_email' => array(
				'name'	=>	'ldms_send_email',
				'type'	=>	'dropdown',
				'label'	=>	__( 'Send e-mail notifications', 'ldmessenger' ),
				'desc'	=>	__( 'Enable or disable email notifications', 'ldmessenger' ),
				'value'	=>	isset( $settings['ldms_send_email'] ) ? $settings['ldms_send_email'] : 'yes',
				'options'	=>	array(
					'yes' 	=>	__( 'Yes', 'ldmessenger' ),
					'no'	=>	__( 'No', 'ldmessenger' ),
				),
			),
			'ldms_email_style'	=>	array(
				'name'	=>	'ldms_email_style',
				'type'	=>	'dropdown',
				'label'	=>	__( 'Email Format', 'ldmessenger' ),
				'desc'	=>	__( 'Do you want to send an HTML email or plain text?', 'ldmessenger' ),
				'value'	=>	isset( $settings['ldms_email_style'] ) ? $settings['ldms_email_style'] : '',
				'options'	=>	array(
					'html'	=>	__( 'HTML', 'ldmessenger' ),
					'text'	=>	__( 'Plain Text', 'ldmessenger' )
				),
			),
			'ldms_email_message' => array(
				'name'  => 'ldms_email_message',
				'type'  => 'wysiwyg',
				'label' => __( 'E-mail Message', 'ldmessenger' ),
				'desc'  => __( 'Create your own Default message using the following variables %recipient_name% , %sender_name%, %site_name%, %message%, %session_url%', 'ldmessenger' ),
				'value' => isset( $settings['ldms_email_message'] ) ? $settings['ldms_email_message'] : '',
				'email_type' => 'orginal'
			),
			'ldms_email_rely_message' => array(
				'name'  => 'ldms_email_rely_message',
				'type'  => 'wysiwyg',
				'label' => __( 'E-mail Reply Message', 'ldmessenger' ),
				'desc'  => __( 'Create your own Default Reply message using the following variables %recipient_name% , %sender_name%, %site_name%, %message%, %session_url%', 'ldmessenger' ),
				'value' => isset( $settings['ldms_email_rely_message'] ) ? $settings['ldms_email_rely_message'] : '',
				'email_type' => 'reply'
			),
			'ldms_users_can_start' => array(
				'name'  => 'ldms_users_can_start',
				'type'  => 'users',
				'label' => __( 'Other users allowed to Start Sessions', 'ldmessenger' ),
				'desc'  => __( 'Allow users other then Admin, Editor, and Group Leaders to start a session with a Group Leader', 'ldmessenger' ),
				'value' => !empty( $settings['ldms_users_can_start'] ) ? $settings['ldms_users_can_start'] : array(),
			),
			'ldms_groups_can_start' => array(
				'name'	=>	'ldms_groups_can_start',
				'type'	=>	'groups',
				'label'	=>	__( 'What groups can start sessions', 'ldmessenger' ),
				'desc'	=>	__( 'Allow users within this group to start sessions with a group leader', 'ldmessenger' ),
				'value'	=>	!empty( $settings['ldms_groups_can_start'] ) ? $settings['ldms_groups_can_start'] : array(),
			),
			'ldms_user_moderators_field' => array(
				'name'	=>	'ldms_user_moderators',
				'type'	=>	'user_moderators',
				'label'	=>	__( 'Session Moderators', 'ldmessenger' ),
				'desc'	=>	__( 'Allow these users to see all sessions', 'ldmessenger' ),
				'value' => !empty( $settings['ldms_user_moderators'] ) ? $settings['ldms_user_moderators'] : array(),
			)
		);

		// Custom integration for simply schedule
	     if( class_exists('Simply_Schedule_Appointments') ) {
			$fields['ldms_simply_schedule'] = array(
		          'name'	=>	'can_simply_schedule',
		          'type'	=>	'dropdown',
		          'label'	=>	__( 'Appointment Scheduling', 'ldmessenger' ),
		          'desc'	=>	__( 'Allow users to schedule an appointment through Simply Schedule', 'ldmessenger' ),
		          'value'	=>	isset( $settings['can_simply_schedule'] ) ? $settings['can_simply_schedule'] : '',
		          'options'	=>	array(
		               'no'	=>	__( 'No', 'ldmessenger' ),
		               'yes'	=>	__( 'Yes', 'ldmessenger' ),
		          )
		     );
		}

		return apply_filters( 'ldms_private_sessions_fields', $fields );
	}

	public function checkbox_callback( $field ) {

		$checked = ( isset( $field['value'] ) && $field['value'] == 'yes' ? 'checked' : '' );

		$html = $field['value'].' - <input type="checkbox" name="ldms_private_sessions_settings['.$field['name'].'"  '.$checked.' >';

		echo $html;

	}

	public function groups_callback( $field ) {

		$groups = learndash_get_groups( true );

		if( !$groups || empty($groups) ) {
			return false;
		}

		foreach( $groups as $group_id ) {

			$active_groups = $field['value'];
			$checked = '';

			if( $active_groups && !empty($active_groups) ) {
				foreach($active_groups as $group => $value ) {
					if( $group == $group_id && $value == 'true' ) {
						$checked = 'checked';
					}
				}
			}

			$group_object = get_post($group_id);

			echo '<p><input name="ldms_private_sessions_settings[' . $field['name'] . '][' . $group_id . ']" type="checkbox" value="true" ' . $checked . '>'. get_the_title($group_id) .'</p>';

		}

		echo '<input name="ldms_private_sessions_settings[' . $field['name'] . '][default]" type="hidden" value="true" ' . $checked . '>';

	}

	public function users_callback( $field ) {
			global $wp_roles;

		if ( ! isset( $wp_roles ) ){
			$wp_roles = new WP_Roles();
		}

		$roles = $wp_roles->get_names();

		//remove defaults
		unset($roles['administrator']);
		unset($roles['editor']);
		unset($roles['group_leader']);

		foreach ($roles as $role_value => $role_name) {
			$users = $field['value'];
			$checked = '';
			foreach($users as $user => $value){
				if($user == $role_value && $value == 'true'){
					$checked = 'checked';
				}

			}
			echo '<p><input name="ldms_private_sessions_settings[' . $field['name'] . ']['.$role_value.']" type="checkbox" value="true" '.$checked.'>'.$role_name.'</p>';
		}
		echo '<input name="ldms_private_sessions_settings[' . $field['name'] . '][default]" type="hidden" value="true" '.$checked.'>';
	}

	public function user_moderators_callback( $field ) {
			global $wp_roles;

		if ( ! isset( $wp_roles ) ){
			$wp_roles = new WP_Roles();
		}

		$roles = $wp_roles->get_names();
		$users = $field['value'];

		foreach ($roles as $role_value => $role_name) {
			$checked = '';
			foreach($users as $user => $value){
				if($user == $role_value && $value == 'true'){
					$checked = 'checked';
				}
			}
			echo '<p><input name="ldms_private_sessions_settings[' . $field['name'] . ']['.$role_value.']" type="checkbox" value="true" '.$checked.'>'.$role_name.'</p>';
		}
		echo '<input name="ldms_private_sessions_settings[' . $field['name'] . '][default]" type="hidden" value="true" '.$checked.'>';
	}

	/**
	 * Callback for text setting fields
	 * @param  array $field Field arguments
	 */
	public function text_callback( $field ) {
		$html  = '<input type="text" name="ldms_private_sessions_settings[' . $field['name'] . ']" value="' . $field['value'] . '" class="regular-text">';
		echo $html;
	}

	/**
	 * Callback for text setting fields
	 * @param  array $field Field arguments
	 */
	public function number_callback( $field ) {
		$html  = '<input type="number" name="ldms_private_sessions_settings[' . $field['name'] . ']" value="' . $field['value'] . '" class="regular-number"> px';
		echo $html;
	}


	/**
	 * Callback for dropdown setting fields
	 * @param  array $field Field arguments
	 */
	public function dropdown_callback( $field ) {


		$html  = '<select name="ldms_private_sessions_settings[' . $field['name'] . ']" id="ldms_private_sessions_settings[' . $field['name'] . ']">';

            $options = $field['options'];
            foreach( $options as $option => $label ):
				$checked = ( $option == $field['value'] ? 'selected' : '' );
               $html  .= '<option value="'.esc_attr($option).'" '.$checked.' >'.esc_html($label).'</option>';
            endforeach;

        $html  .= '</select>';

		echo $html;
	}

	public function color_callback( $field ) {

		$html = '<input type="text" name="ldms_private_sessions_settings[' . $field['name'] . ']" value="' . $field['value'] . '" class="wp-color-picker">';

		echo $html;

	}

	/**
	 * Callback for WYSIWYG setting fields
	 * @param  array $field Field arguments
	 */
	public function page_dropdown_callback( $field ) {


		$html  = '<select name="ldms_private_sessions_settings[' . $field['name'] . ']" id="ldms_private_sessions_settings[' . $field['name'] . ']">';

		$current = get_option('ldma_sessions_page' );
		$args   = array(
		    'post_type'         =>  'page',
		    'posts_per_page'    =>  -1,
		);
		$pages = new WP_Query($args);
		while( $pages->have_posts() ): $pages->the_post();
			$checked = ( get_the_ID() == $field['value'] ? 'selected' : '' );
		    $html  .= '<option value="'.esc_attr(get_the_ID()).'" '.$checked.'>'.esc_html(get_the_title(get_the_ID())).'</option>';
		endwhile;
		wp_reset_postdata();

		$html  .= '</select>';

		if( isset($field['value']) && !empty($field['value']) ) $html .= ' <a href="' . get_permalink( $field['value'] ) . '" target="_new">' . __( 'View Page', 'ldmessenger' ) . '</a>';

        echo $html;
	}


	/**
	 * Callback for WYSIWYG setting fields
	 * @param  array $field Field arguments
	 */
	public function wysiwyg_callback( $field ) {

		$name = 'ldms_private_sessions_settings[' . $field['name'] . ']';
		if($field['value'] == ''){
			$content = ldms_get_default_email_message($field['email_type']);
		}else{
			$content = $field['value'];
		}

        wp_editor( $content , 'ldms_texarea_'.$field['name'] , array('textarea_name'=> $name ) );

		echo wpautop( '<code>' . esc_html($field['desc']) . '</code>' );

	}

	public function license_key_callback( $field ) {

		$status = get_option( 'ldms_license_status' ); ?>

		<input type="text" name="ldms_private_sessions_settings[<?php esc_attr_e($field['name']); ?>]" value="<?php esc_attr_e($field['value']); ?>">

		<?php
		if( !empty($field['value'] ) ) {
			if( $status !== false && $status == 'valid' && !empty($field['value']) ) { ?>
				<span style="color:green; padding: 5px 10px; background: #f9f9f9; border-radius: 3px; display: inline-block; margin: 5px 10px 0 0;"><?php _e( 'Active', 'ldmessenger' ); ?></span>
				<?php wp_nonce_field( 'ldms_sample_nonce', 'ldms_sample_nonce' ); ?>
				<input type="submit" style="margin-top: 5px;" class="button-secondary" name="ldms_license_deactivate" value="<?php esc_attr_e('Deactivate License','ldmessager'); ?>"/>
			<?php } else {
					wp_nonce_field( 'ldms_sample_nonce', 'ldms_sample_nonce' ); ?>
					<input type="submit"  style="margin-top: 5px;" class="button-secondary" name="ldms_license_activate" value="<?php esc_attr_e('Activate License','ldmessager'); ?>"/>
			<?php }
		}

	}



}

global $Ldms_Private_Sessions;

$Ldms_Private_Sessions = new Ldms_Private_Sessions();

do_action( 'ldms_after_settings_init' );

function ldms_sanitize_license( $new ) {
	$old = ldms_get_option( 'ldms_license_key' );
	if( $old && $old != $new ) {
		ldms_delete_option( 'ldms_license_key' ); // new license has been entered, so must reactivate
	}
	return $new;
}

function ldms_activate_license() {
	// listen for our activate button to be clicked
	if( isset( $_POST['ldms_license_activate'] ) ) {
		// run a quick security check
	 	if( ! check_admin_referer( 'ldms_sample_nonce', 'ldms_sample_nonce' ) )
			return; // get out if we didn't click the Activate button
		// retrieve the license from the database
		$license = trim( ldms_get_option( 'ldms_license_key' ) );
		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( LDMA_ITEM_NAME ), // the name of our product in EDD
			'url'        => home_url()
		);
		// Call the custom API.
		$response = wp_remote_post( LDMA_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$get_error_message = $response->get_error_message();
			$message =  ( is_wp_error( $response ) && ! empty( $get_error_message ) ) ? $get_error_message : __( 'An error occurred, please try again.' );
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
						$message = __( 'Invalid license.' );
						break;
					case 'invalid' :
					case 'site_inactive' :
						$message = __( 'Your license is not active for this URL.' );
						break;
					case 'item_name_mismatch' :
						$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), EDD_SAMPLE_ITEM_NAME );
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
			$base_url = admin_url( 'admin.php?page=learndash-private-sessions' );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );
			wp_redirect( $redirect );
			exit();
		}
		// $license_data->license will be either "valid" or "invalid"
		update_option( 'ldms_license_status', $license_data->license );
		wp_redirect( admin_url( 'admin.php?page=learndash-private-sessions' ) );
		exit();
	}
}
add_action('admin_init', 'ldms_activate_license');

add_action('admin_init', 'ldms_decactivate_license');
function ldms_decactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['ldms_license_deactivate'] ) ) {
		// run a quick security check
	 	if( ! check_admin_referer( 'ldms_sample_nonce', 'ldms_sample_nonce' ) )
			return; // get out if we didn't click the Activate button
		// retrieve the license from the database
		$license = trim( ldms_get_option( 'ldms_license_key' ) );
		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( LDMA_ITEM_NAME ), // the name of our product in EDD
			'url'        => home_url()
		);

		$response = wp_remote_post( LDMA_STORE_URL , array( 'body' => $api_params, 'timeout' => 15, 'sslverify' => false ) );
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		update_option( 'ldms_license_status', $license_data->license );
		wp_redirect( admin_url( 'admin.php?page=learndash-private-sessions' ) );
		exit();

	}
}

function ldms_admin_notices() {
	if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {
		switch( $_GET['sl_activation'] ) {
			case 'false':
				$message = urldecode( $_GET['message'] );
				?>
				<div class="error">
					<p><?php esc_html_e($message); ?></p>
				</div>
				<?php
				break;
			case 'true':
			default:
				// Developers can put a custom success message here for when activation is successful if they way.
				break;
		}
	}
}
add_action( 'admin_notices', 'ldms_admin_notices' );
