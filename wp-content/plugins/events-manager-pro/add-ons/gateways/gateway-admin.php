<?php
namespace EM\Payments;
/**
 * This class is a parent class which gateways should extend. There are various variables and functions that are automatically taken care of by
 * EM_Gateway, which will reduce redundant code and unecessary errors across all gateways. You can override any function you want on your gateway,
 * but it's advised you read through before doing so.
 *
 */
class Gateway_Admin {
	
	/**
	 * @var string
	 */
	public static $gateway = 'unknown';
	/**
	 * @var Gateway
	 */
	public static $gateway_class = false;
	/**
	 * @var array Associative array of key => label for storing API credentials, this should be assigned in init() so that labels are translated
	 */
	public static $api_cred_fields = array();
	public static $webhook_events = array();
	public static $webhook_admin_urls = array();
	public static $documentation_url_api = '';
	
	public static $api_cred_name;
	
	public static function init(){
		// set the right Gateway static class reference if not set hard-coded (and if it even exists)
		static::$gateway = static::gateway()::$gateway;
		if( empty( static::$api_cred_name ) ) {
			static::$api_cred_name = 'em_' . static::$gateway . '_api';
		}
	}
	
	/**
	 * @return Gateway|string
	 */
	public static function gateway(){
		// set the right Gateway static class reference if not set hard-coded (and if it even exists)
		if( !static::$gateway_class ){
			$Gateway = str_replace('_Admin', '', static::class);
			if( class_exists($Gateway) ) {
				return $Gateway;
			}
			return '\EM\Payments\Gateway';
		}
		return static::$gateway_class;
	}
	
	/**
	 * Toggles gateway on/off.
	 * @return bool
	 */
	public static function toggle() {
		$active = get_option('em_payment_gateways');

		if(array_key_exists(static::$gateway, $active)) {
			unset($active[static::$gateway]);
			update_option('em_payment_gateways',$active);
			return true;
		} else {
			$active[static::$gateway] = true;
			update_option('em_payment_gateways',$active);
			return true;
		}
	}
	
	/**
	 * @deprecated
	 * @see Gateway_Admin::toggle()
	 * @return bool
	 */
	public static function toggleactivation(){
		return static::toggle();
	}
	
	public static function activate() {
		$active = get_option('em_payment_gateways', array());
		if(array_key_exists(static::$gateway, $active)) {
			return true;
		} else {
			$active[static::$gateway] = true;
			update_option('em_payment_gateways', $active);
			return true;
		}
	}
	
	public static function deactivate() {
		$active = get_option('em_payment_gateways');
		if(array_key_exists(static::$gateway, $active)) {
			unset($active[static::$gateway]);
			update_option('em_payment_gateways', $active);
			return true;
		} else {
			return true;
		}
	}
	
	public static function settings_tabs( $custom_tabs = array() ){
		$settings = array(
			'general' => array(
				'name' => esc_html__emp('General Options'),
				'callback' => array(static::class, 'settings_general'),
			),
		);
		if( !empty( static::$api_cred_fields ) ){
			$settings['api'] = array(
				'name' => esc_html__emp('API Keys/Notifications'),
				'callback' => array(static::class, 'settings_api'),
			);
		}
		$settings = array_merge( $settings, $custom_tabs );
		return apply_filters('em_gateway_settings_tabs', $settings, static::gateway());
	}
	
	/**
	 * Generates a settings pages.
	 * @uses EM_Gateway::mysettings()
	 */
	public static function settings() {
		if( static::gateway()::$legacy ){
			$link = '<a href="https://eventsmanagerpro.com/downloads/">'. esc_html__('Download new payment methods for this gateway.').'</a>';
			echo '<div class="notice notice-warning"><p>'. sprintf(esc_html__('This is a legacy payment method, and has been discontinued by the payment provider itself, whilst it may work for the time being, we cannot guarantee when the payment gatewway provider will completely stop supporting it.', 'em-pro')) . ' ' . $link . '</p></div>';
		}
		?>
	    <script type="text/javascript" charset="utf-8"><?php include(EM_DIR.'/includes/js/admin-settings.js'); ?></script>
		<div class='wrap nosubsub tabs-active'>
			<h1><?php echo sprintf(__('Edit &quot;%s&quot; settings','em-pro'), esc_html(static::gateway()::$title) ); ?></h1>
			<?php
			$messages['updated'] = esc_html__('Gateway updated.', 'em-pro');
			$messages['error'] = esc_html__('Gateway not updated.', 'em-pro');
			if ( isset($_GET['msg']) && !empty($messages[$_GET['msg']]) ){
				echo '<div id="message" class="'.$_GET['msg'].' fade"><p>' . $messages[$_GET['msg']] .
				' <a href="'.em_add_get_params($_SERVER['REQUEST_URI'], array('action'=>null,'gateway'=>null, 'msg' => null)).'">'.esc_html__('Back to gateways','em-pro').'</a>'.
				'</p></div>';
			}
			?>
			<h2 class="nav-tab-wrapper">
				<?php
				$settings_tabs = static::settings_tabs();
				foreach( $settings_tabs as $tab_key => $tab ){
					$tab_name = is_array($tab) ? $tab['name'] : $tab;
					$tab_link = !empty($tabs_enabled) ? esc_url(add_query_arg( array('em_tab'=>$tab_key))) : '';
					$active_class = !empty($tabs_enabled) && !empty($_GET['em_tab']) && $_GET['em_tab'] == $tab_key ? 'nav-tab-active':'';
					echo "<a href='$tab_link#$tab_key' id='em-menu-$tab_key' class='nav-tab $active_class'>{$tab_name}</a>";
				}
				?>
			</h2>
			<form action='' method='post' name='gatewaysettingsform' class="em-gateway-settings">
				<input type='hidden' name='action' id='action' value='updated' />
				<input type='hidden' name='gateway' id='gateway' value='<?php echo static::$gateway; ?>' />
				<?php wp_nonce_field('updated-' . static::$gateway); ?>
				<?php
				foreach( $settings_tabs as $tab_key => $tab ){
					$display = $tab_key == 'general' ? '':'display:none;';
					?>
					<div class="em-menu-<?php echo esc_attr($tab_key) ?> em-menu-group" style="<?php echo $display; ?>">
						<?php
						if( !empty($tab['callback']) ) {
							call_user_func( $tab['callback'], static::gateway() );
						}
						do_action('em_gateway_settings_tab_'. $tab_key, static::gateway());
						?>
						<p class="submit">
							<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
						</p>
					</div>
					<?php
				}
				?>
			</form>
		</div> <!-- wrap -->
		<?php
	}
	
	public static function settings_general(){
		$gateway_link = '<a href="'.admin_url('edit.php?post_type='.EM_POST_TYPE_EVENT.'&page=events-manager-options#bookings+gateway-options').'">'. strtolower(esc_html__('Settings', 'em-pro')) . '</a>';
		static::settings_general_header();
		?>
		<h3><?php echo sprintf(esc_html__emp( '%s Options', 'events-manager'),esc_html__emp('Booking Form','events-manager')); ?></h3>
		<table class="form-table">
			<tbody>
			<?php
			//Gateway Title
			$desc = sprintf(__('Only if you have not enabled quick pay buttons in your %s page.', 'em-pro'), $gateway_link);
			$desc .= ' ' . __('The user will see this as the text option when choosing a payment method.','em-pro');
			em_options_input_text(__('Gateway Title', 'em-pro'), 'em_' . static::$gateway.'_option_name', $desc);
			
			//Gateway booking form info
			$desc = sprintf(__('Only if you have not enabled quick pay buttons in your %s page.','em-pro'), $gateway_link);
			$desc .= ' ' . __('If a user chooses to pay with this gateway, or it is selected by default, this message will be shown just below the selection.', 'em-pro');
			em_options_textarea(__('Booking Form Information', 'em-pro'), 'em_' . static::$gateway.'_form', $desc);
			
			if(static::gateway()::$button_enabled) {
				$desc = sprintf( __( 'If you have chosen to only use quick pay buttons in your %s page, this button text will be used.', 'em-pro' ), $gateway_link );
				$desc .= ' ' . sprintf( __( 'Choose the button text. To use an image instead, enter the full url starting with %s or %s.', 'em-pro' ), '<code>http://...</code>', '<code>https://...</code>' );
				em_options_input_text( __( 'QuickPay Payment Button', 'em-pro' ), 'em_' . static::$gateway . '_button', $desc );
			}
			?>
			</tbody>
		</table>
		<?php static::settings_general_feedback(); ?>
		<?php static::settings_general_cancellation(); ?>
		<?php static::settings_general_footer(); ?>
		<?php
		do_action( 'em_gateway_settings_footer', static::gateway() );
	}
	
	public static function settings_general_footer(){}
	public static function settings_general_header(){}
	
	/**
	 * Called by $this->settings(), override this to output your own gateway options on this gateway settings page
	 */
	public static function settings_general_feedback(){
		?>
		<h3><?php esc_html_e('Booking Actions - Payment Complete', 'em-pro'); ?></h3>
		<table class="form-table">
			<tbody>
			<?php
				if( !empty(static::gateway()::$payment_flow['redirect']) ) {
					$feedback_message = sprintf(esc_html__('The message that is shown to a user when a booking is successful whilst being redirected to %s for payment.','em-pro'), 'Paypal');
					em_options_input_text( esc_html__('Success Message', 'em-pro'), 'em_'. static::$gateway . '_booking_feedback', $feedback_message );
				}elseif( empty(static::gateway()::$payment_flow['redirect-success']) ) {
					$feedback_message = esc_html__('The message that is shown to a user when a payment is successful and booking is complete.','em-pro');
					em_options_input_text( esc_html__('Success Message', 'em-pro'), 'em_'. static::$gateway . '_booking_feedback', $feedback_message );
				}
				if( !empty(static::gateway()::$payment_flow['redirect-success']) ) {
					em_options_input_text( esc_html__('Return URL', 'em-pro'), 'em_'. static::$gateway . '_return', esc_html__('Once a payment is completed, users are redirected back to your site. Leave blank for the default thank you page, or add your own custom page which could contain further instructions.', 'em-pro') );
					em_options_input_text( esc_html__('Thank You Message', 'em-pro'), 'em_'. static::$gateway . '_booking_feedback_completed', sprintf(esc_html__('If you choose to return users to the default Events Manager thank you page after a user has paid via %s, you can customize the thank you message here.','em-pro'), static::gateway()::$title) );
				}
			?>
			<?php if ( static::gateway()::$count_pending_spaces ) : ?>
				<tr valign="top">
					<th scope="row"><?php esc_html_e_emp('Reserved unconfirmed spaces?') ?></th>
					<td>
						<?php $v = get_option('em_' . static::$gateway . '_reserve_pending'); ?>
						<select name="em_<?php echo static::$gateway; ?>_reserve_pending">
							<option value="1" <?php if( $v ) echo 'selected="selected"'; ?>><?php esc_html_e_emp('Yes'); ?></option>
							<option value="0" <?php if( !$v ) echo 'selected="selected"'; ?>><?php esc_html_e_emp('No'); ?></option>
						</select>
						<br>
						<em><?php echo esc_html__('If set to "Yes", spaces will be reserved once a user submits a booking and proceeds to payment, whilst payment is still pending.' ,'em-pro'); ?></em>
			
						<?php if( static::gateway()::$has_timeout ): ?>
						<br>
						<em><?php echo sprintf(esc_html__('We recommend setting this to "Yes" and automatically expiring upaid bookings after at least %s minutes in the %s setting below','em-pro'), '<strong>15</strong>', '<strong>'.esc_html__('Unpaid Bookings Expiry', 'em-pro').'</strong>'); ?></em>
						<?php endif; ?>
					</td>
				</tr>
			<?php endif; ?>
			<?php if( static::gateway()::$has_timeout ): ?>
				<tr valign="top">
					<th scope="row"><?php _e('Unpaid Bookings Expiry', 'em-pro') ?></th>
					<td>
						<input type="text" name="em_<?php echo static::$gateway; ?>_booking_timeout" style="width:50px;" value="<?php esc_attr_e(get_option('em_'. static::$gateway . "_booking_timeout" )); ?>" style='width: 40em;' /> <?php _e('minutes','em-pro'); ?><br>
						<em><?php esc_html_e('Once a booking is initially submitted, Events Manager stores a booking record in the database to identify the incoming payment. If you would like these bookings to expire after x minutes without payment confirmation, please enter a value above.','em-pro'); ?></em>
						<br>
						<em><?php esc_html_e('If a booking remains unpaid after this time, the booking will expire, and the payment method cancelled.','em-pro'); ?></em>
						<br>
						<em>
							<?php
							$gateway_link = '<a href="'.admin_url('edit.php?post_type='.EM_POST_TYPE_EVENT.'&page=events-manager-options#bookings+gateway-options').'">'. strtolower(esc_html__('Settings', 'em-pro')) . '</a>';
							echo sprintf( esc_html__('If set to 0, this booking will take its options from the %s page.','em-pro'), $gateway_link );
							?>
						</em>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Expired Booking Action', 'em-pro') ?></th>
					<td>
						<?php $v = get_option('em_' . static::$gateway . '_booking_timeout_action', 'delete'); ?>
						<select name="em_<?php echo static::$gateway; ?>_booking_timeout_action">
							<option value="none" <?php if( $v === 'none' ) echo 'selected="selected"'; ?>><?php esc_html_e_emp('No Action'); ?></option>
							<option value="delete" <?php if( $v === 'delete' ) echo 'selected="selected"'; ?>><?php esc_html_e_emp('Delete'); ?></option>
							<option value="cancel" <?php if( $v === 'cancel' ) echo 'selected="selected"'; ?>><?php esc_html_e_emp('Cancel'); ?></option>
						</select><br>
						<em><?php esc_html_e('Once a booking has expired, decide whether to cancel or delete it.','em-pro'); ?></em>
					</td>
				</tr>
			<?php endif; ?>
			<?php if( static::gateway()::$can_manually_approve ): ?>
			<tr valign="top">
				<th scope="row"><?php _e('Manually approve completed transactions?', 'em-pro') ?></th>
				<td>
					<input type="checkbox" name="em_<?php echo static::$gateway; ?>_manual_approval" value="1" <?php echo (get_option('em_'. static::$gateway . "_manual_approval" )) ? 'checked="checked"':''; ?> /><br>
					<em><?php _e('By default, when someone pays for a booking, it gets automatically approved once the payment is confirmed. If you would like to manually verify and approve bookings, tick this box.','em-pro'); ?></em><br>
					<em><?php echo sprintf(__('Approvals must also be required for all bookings in your <a href="%s">settings</a>.','em-pro'),EM_ADMIN_URL.'&amp;page=events-manager-options'); ?></em>
				</td>
			</tr>
			<?php endif; ?>
			</tbody>
		</table>
		<?php
	}
	
	public static function settings_general_cancellation(){
		if( !empty(static::gateway()::$payment_flow['redirect-cancel']) ) {
			?>
			<h3><?php esc_html_e('Payment Cancellation', 'em-pro'); ?></h3>
			<table class="form-table">
				<p>
					<?php esc_html_e('If a user cancels before the payment process completes, such as by clicking the cancel/back button on the payment page (not the back button on a browser), they will be redirected to a specific page of your choosing and display a customized message.', 'em-pro'); ?>
				</p>
				<p>
					<?php esc_html_e('Cancelling a payment would result in their temporary booking also being cancelled and deleted.','em-pro'); ?>
				</p>
				<tbody>
				<?php
				em_options_input_text( esc_html__('Cancel URL', 'em-pro'), 'em_'. static::$gateway . '_cancel_return', esc_html__('If left blank the user is redirected to the event or checkout page.', 'em-pro') );
				em_options_input_text( esc_html__('Payment Cancelled Message', 'em-pro'), 'em_'. static::$gateway . '_booking_feedback_cancelled' );
				?>
				</tbody>
			</table>
			<?php
		}
	}
	
	/*
	 * PayPal
	 * PayPal Checkout/Advanced
	 *  - warn users of new integration methods
	 * Authorize AIM
	 * Authorize API
	 * Stripe El / Checkout
	 */
	
	public static function settings_api(){
		static::settings_api_credentials();
		static::settings_api_notifications();
	}
	
	public static function settings_api_credentials(){
		?>
		<h3><?php echo sprintf(__('%s Credentials','em-pro'), static::gateway()::$title ); ?></h3>
		<?php if( static::$documentation_url_api ): ?>
		<p><?php echo sprintf(__('Please visit the <a href="%s">documentation</a> for further instructions.','em-pro'), static::$documentation_url_api); ?></p>
		<?php endif; ?>
		<?php
		if( static::gateway()::$requires_ssl ) {
			$ajax_url = str_replace( 'http://', 'https://', admin_url( 'admin-ajax.php' ) );
			$verify   = @wp_remote_get( $ajax_url );
			if ( is_wp_error( $verify ) ) {
				/* @public static $verify WP_Error */
				foreach ( $verify->get_error_messages() as $error ) {
					if ( preg_match( '/SSL/', $error ) ) {
						echo '<div class="em-gateway-ssl-warning" style="color:red">';
						echo sprintf( esc_html__( 'A valid SSL certificate is required for live payments using this gateway. We are not able to connect to this URL: %s.', 'em-pro' ), '<a href="' . $ajax_url . '"><code>' . $ajax_url . '</code></a>' );
						echo '</div>';
					}
				}
			}
		}
		?>
		<table class="form-table">
			<tbody>
			<?php
			$status_modes = array('live' => __('Live Site', 'em-pro'), 'sandbox' => __('Test Mode (Sandbox)', 'em-pro') );
			em_options_select(esc_html__('Gateway Mode', 'em-pro'), 'em_'. static::$gateway . "_mode", $status_modes);
			$is_sandbox = get_option('em_'.static::$gateway.'_mode') == 'sandbox';
			static::settings_sensitive_credentials( static::$api_cred_fields, $is_sandbox );
			?>
			</tbody>
		</table>
		<?php
	}
	
	public static function settings_sensitive_credentials( $api_cred_fields, $is_sandbox ){
		if( !is_ssl() ){
			?>
			<tr>
				<td colspan="2">
					<?php
					echo '<p style="color:red;">';
					echo sprintf( esc_html__('Your site is not using SSL! Whilst not a requirement, if you\'re going to submit API information for a live %s account, we recommend you do so over a secure connection. If this is not possible, consider an alternative option of submitting your API information as covered in our %s.', 'em-pro'),
						static::gateway()::$title, '<a href="http://wp-events-plugin.com/documentation/events-with-paypal/safe-encryption-api-keys/">'.esc_html__('documentation','events-manager').'</a>');
					echo '</p>';
					if( !em_constant('EMP_GATEWAY_SSL_OVERRIDE') && ($is_sandbox && empty($_REQUEST['show_keys'])) ){
						echo '<p>'.esc_html__('If you are only using testing credentials, you can display and save them safely.', 'em-pro');
						echo ' <a href="'. esc_url(add_query_arg('show_keys', wp_create_nonce('show_'. static::$gateway . '_creds'))) .'" class="button-secondary">'. esc_html__('Show API Keys', 'em-pro') .'</a>';
						echo '</p>';
					}
					?>
				</td>
			</tr>
			<?php
		}
		$api_options = get_option(static::$api_cred_name);
		if( static::settings_show_settings_credentials( $is_sandbox ) ) {
			foreach( $api_cred_fields as $api_cred_opt => $api_cred_label ){
				$api_cred_value = !empty($api_options[$api_cred_opt]) && $api_options[$api_cred_opt] !== $api_cred_label ? $api_options[$api_cred_opt] : '';
				?>
				<tr valign="top" id='<?php echo static::$api_cred_name  .'_'. esc_attr($api_cred_opt); ?>_row'>
					<th scope="row"><?php echo esc_html($api_cred_label); ?></th>
					<td>
						<input value="<?php echo esc_attr($api_cred_value); ?>" name="<?php echo static::$api_cred_name . '_'. esc_attr($api_cred_opt) ?>" type="text" id="<?php echo static::$api_cred_name . esc_attr($api_cred_opt) ?>" style="width: 95%" size="45" />
					</td>
				</tr>
				<?php
			}
		} else {
			foreach( $api_cred_fields as $api_cred_opt => $api_cred_label ){
				$api_cred_value = !empty($api_options[$api_cred_opt]) && $api_options[$api_cred_opt] !== $api_cred_label ? $api_options[$api_cred_opt] : '';
				?>
				<tr valign="top">
					<th scope="row"><?php echo esc_html($api_cred_label); ?></th>
					<td>
						<?php
						$chars = '';
						for( $i = 0; $i < strlen($api_cred_value); $i++ ) $chars = $chars . '*';
						echo esc_html(str_replace( substr($api_cred_value, 1, -1), $chars, $api_cred_value) );
						?>
					</td>
				</tr>
				<?php
			}
		}
	}
	
	public static function settings_api_notifications(){
		$verify_webhook = static::verify_webhook();
		?>
		<h3><?php esc_html_e('Payment Notifications', 'em-pro'); ?></h3>
		<p>
			<em>
				<?php
				if( $verify_webhook === true ){
					echo '<span style="color:green;">';
					esc_html_e('We have verified your credentials, and a valid Webhook was set up correctly, please do not delete this webhook so that we can detect updates to a payment, such as refunds and disputes.', 'em-pro');
					echo '</span>';
				}elseif( $verify_webhook === false ){
					echo '<span style="color:red;">';
					esc_html_e('You do not currently have a valid Webhook assigned. This is needed for detecting updates to a payment, such as refunds and disputes. Please re-save your settings and a webhook should be automatically created for you.', 'em-pro');
					echo '</span>';
				}
				?>
			</em>
		</p>
		<p><?php echo esc_html__('If you would like to receive notifications from %s and handle events such as voided transactions, refunds and chargebacks automatically, you need to create a Webhook.','em-pro'); ?></p>
		<p><?php echo sprintf(__('Your Webhooks Endpoint url is %s.','em-pro'),'<code>'.static::gateway()::get_api_notify_url() . '</code>'); ?></p>
		<?php if( !empty(static::$webhook_events) ): ?>
		<p><?php echo sprintf(__('Supported webhook events: %s.','em-pro'),'<code>' . implode('</code><code>', static::$webhook_events) . '</code>'); ?></p>
		<?php endif; ?>
		<?php
		if( !empty( static::$webhook_admin_urls) ) {
			if ( count( static::$webhook_admin_urls ) === 1 ) {
				$webhooks_url = '<a href="' . static::$webhook_admin_urls[0] . '">' . esc_html__( 'here', 'em-pro' ) . '</a>';
				?>
				<p><?php echo sprintf(__('You can create your webhook %1$s.','em-pro'), $webhooks_url); ?></p>
				<?php
			} else {
				$sandbox_webhooks_url = '<a href="' . static::$webhook_admin_urls[0] . '">' . esc_html__('sandbox', 'em-pro') . '</a>';
				$production_webhooks_url = '<a href="' . static::$webhook_admin_urls[1] . '">' . esc_html__('production', 'em-pro') . '</a>';
				?>
				<p><?php echo sprintf(__('You can create your webhook in %1$s or %2$s environments.','em-pro'), $sandbox_webhooks_url, $production_webhooks_url); ?></p>
				<?php
			}
		}
	}
	
	/**
	 * Run by EM_Gateways_Admin::handle_gateways_panel_updates() if this gateway has been updated. You should capture the values of your new fields above and save them as options here.
	 * @param $options array of option names that get updated when this gateway settings page is saved
	 * return boolean
	 * @todo add $options as a parameter to method, and update all extending classes to prevent strict errors
	 */
	public static function update( $options = array() ) {
		//default action is to return true
		if ( static::gateway()::$button_enabled ) {
			$options_wpkses[] = 'em_' . static::$gateway . '_button';
			add_filter( 'update_em_' . static::$gateway . '_button', 'wp_kses_post' );
		}
		if ( ! empty( static::gateway()::$payment_flow['redirect'] ) || empty( static::gateway()::$payment_flow['redirect-success'] ) ) {
			$options_wpkses[] = 'em_' . static::$gateway . '_booking_feedback';
		}
		if ( ! empty( static::gateway()::$payment_flow['redirect-success'] ) ) {
			$defaul_options[] = 'em_' . static::$gateway . '_return';
			$options_wpkses[] = 'em_' . static::$gateway . '_booking_feedback_completed';
		}
		if ( ! empty( static::gateway()::$payment_flow['redirect-cancel'] ) ) {
			$defaul_options[] = 'em_' . static::$gateway . '_cancel_return';
			$options_wpkses[] = 'em_' . static::$gateway . '_booking_feedback_cancelled';
		}
		if ( ! empty( static::$api_cred_fields ) ) {
			if( static::settings_show_settings_credentials( get_option( 'em_' . static::$gateway . '_mode' ) == 'sandbox' ) ) {
				$defaul_options[ static::$api_cred_name ] = array_keys(static::$api_cred_fields);
			}
			$defaul_options[] = 'em_' . static::$gateway . '_mode';
		}
		if ( static::gateway()::$count_pending_spaces ) {
			$defaul_options[] = 'em_' . static::$gateway . '_reserve_pending';
		}
		if( static::gateway()::$has_timeout ) {
			$defaul_options[] = 'em_' . static::$gateway . '_booking_timeout';
			$defaul_options[] = 'em_' . static::$gateway . '_booking_timeout_action';
		}
		if( static::gateway()::$can_manually_approve ) {
			$defaul_options[] = 'em_' . static::$gateway . '_manual_approval';
		}
		// general options
		$options_wpkses[] = 'em_' . static::$gateway . '_option_name';
		$options_wpkses[] = 'em_' . static::$gateway . '_form';
		
		//add filters for all $option_wpkses values so they go through wp_kses_post
		foreach( $options_wpkses as $option_wpkses ) add_filter('gateway_update_'.$option_wpkses,'wp_kses_post');
		$options = array_merge($defaul_options, $options_wpkses, $options);
		
		//go through the options, grab them from $_REQUEST, run them through a filter for sanitization and save
		foreach( $options as $option_index => $option_name ){
			if( is_array( $option_name ) ){
				$option_values = array();
				foreach( $option_name as $option_key ){
					$option_value_raw = !empty($_REQUEST[$option_index.'_'.$option_key]) ? stripslashes($_REQUEST[$option_index.'_'.$option_key]) : '';
					$option_values[$option_key] = apply_filters('gateway_update_'.$option_index.'_'.$option_key, $option_value_raw);
				}
				update_option($option_index, $option_values);
			}else{
				$option_value_raw = !empty($_REQUEST[$option_name]) ? stripslashes($_REQUEST[$option_name]) : '';
				$option_value = apply_filters('gateway_update_'.$option_name, $option_value_raw);
				update_option($option_name, $option_value);
			}
		}
		//multilingual, same as above
		if( \EM_ML::$is_ml ) {
			foreach ( $options as $option_name ) {
				if ( ! empty( $_REQUEST[ $option_name . '_ml' ] ) && is_array( $_REQUEST[ $option_name . '_ml' ] ) ) {
					$option_ml_value = array();
					foreach ( $_REQUEST[ $option_name . '_ml' ] as $lang => $option_value_raw ) {
						if ( ! empty( $option_value_raw ) ) {
							$option_ml_value[ $lang ] = apply_filters( 'gateway_update_' . $option_name, stripslashes( $option_value_raw ) );
						}
					}
					update_option( $option_name . '_ml', $option_ml_value );
				}
			}
		}
		
		do_action('em_updated_gateway_options', $options, static::gateway());
		do_action('em_gateway_update', static::gateway());
		return true;
	}
	
	/**
	 * Override and return true or false if gateway supports a webhook and if detected. If gateway supports webhooks but has no API for auto-creating or verifying, leave this as is and add instructions.
	 * @return bool|null
	 */
	public static function verify_webhook(){
		return null;
	}
	
	public static function settings_show_settings_credentials( $is_sandbox = false ){
		return is_ssl() || em_constant('EMP_GATEWAY_SSL_OVERRIDE') || ($is_sandbox && !empty($_REQUEST['show_keys']) && wp_verify_nonce($_REQUEST['show_keys'], 'show_'. static::$gateway . '_creds'));
	}
}
?>