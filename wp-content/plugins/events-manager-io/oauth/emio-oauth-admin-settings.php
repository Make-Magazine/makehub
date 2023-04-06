<?php
class EMIO_OAuth_Admin_Settings {

	public static $option_name = 'emio_oauth';
	public static $service_name = 'EMIO OAuth 2.0';
	public static $service_api_class = 'EMIO_OAuth_API';
	public static $service_url = 'http://example.com';
	public static $icon_url = '';


	public static function init(){
		$class = get_called_class(); //get child class name to call
		self::$icon_url = plugin_dir_url(__FILE__). 'icon.png';
		//handle service app creds
		add_action('emio_settings_apps', "$class::emio_settings_apps");
		add_action('emio_settings_save_apps', "$class::emio_settings_save_apps");
		//handle settings for user authentication - we only need to output the button, oauth is handled elsewhere
		add_action('emio_settings_user_auth', "$class::emio_settings_user_auth");
	}

	public static function emio_settings_save_apps(){
		$options = array();
		$option_names = array(static::$option_name.'_app_id', static::$option_name.'_app_secret');
		foreach( $option_names as $option_name ){
			$options[$option_name] = !empty($_REQUEST[$option_name]) ? $_REQUEST[$option_name] : '';
		}
		EMIO_Options::set($options);
	}

	public static function emio_settings_apps(){
		$desc = esc_html__("You'll need to create an App with %s and obtain the App credentials by going to %s.", 'events-manager-io');
		$desc_url = esc_html__('You will also need supply an OAuth redirect url to %s. Your url for this site is : %s', 'events-manager-io');
		$client_class = static::$service_api_class; /* @var EMIO_OAuth_API_Client $client_class */
		$callback_url = $client_class::get_oauth_callback_url();
		?>
		<div  class="postbox " id="em-opt-<?php echo static::$option_name; ?>-app" >
			<div class="handlediv" title="<?php __('Click to toggle', 'events-manager'); ?>"><br /></div><h3><span><?php echo static::$service_name; ?></span></h3>
			<div class="inside">
				<p><?php printf( $desc, static::$service_name, '<a href="'. static::$service_url .'">'. static::$service_url .'</a>'); ?></p>
				<p><?php printf( $desc_url, static::$service_name, "<code>$callback_url</code>") ?></p>
				<table class='form-table'>
					<?php
					emio_input_text(sprintf(__('%s App ID', 'events-manager-io'), static::$service_name), static::$option_name.'_app_id', EMIO_Options::get(static::$option_name.'_app_id'));
					emio_input_text(sprintf(__('%s App Secret', 'events-manager-io'), static::$service_name), static::$option_name.'_app_secret', EMIO_Options::get(static::$option_name.'_app_secret'));
					?>
				</table>
			</div> <!-- . inside -->
		</div> <!-- .postbox -->
		<?php
	}
	
	public static function emio_settings_user_auth(){
		$api_class = static::$service_api_class; /* @var EMIO_OAuth_API $api_class */
		$api_client = $api_class::get(false); //a fresh client with no token for generating oauth links
        if( !is_wp_error($api_client) ){ //oauth is not configured correctly...
            $oauth_url = $api_client->get_oauth_url();
            //we don't need to verify connections at this point, we just need to know if there are any
            $user_id = get_current_user_id();
            $user_tokens = get_user_meta( $user_id, 'emio_oauth_'. static::$option_name, true );
            if( empty($user_tokens) ) $user_tokens = array();
            $user_accounts = array();
            $connected = $reconnect_required = false;
            foreach( $user_tokens as $account_id => $user_account ){
                try {
                    $api_client->load_token( $user_id, $account_id );
                    $verification = true;
                } catch ( EMIO_Exception $e ) {
                    $verification = false;
                }
                $user_account['id'] = !empty($user_account['email']) ? $user_account['email'] : $account_id;
                $disconnect_url_args = array( 'action' => 'emio_'. static::$option_name, 'callback' => 'disconnect', 'account' => $account_id, 'nonce' => wp_create_nonce('emio-'. static::$option_name .'-disconnect-'.$account_id) );
                $user_account['disconnect'] = add_query_arg( $disconnect_url_args, admin_url( 'admin-ajax.php' ) );
                if( !$verification ){
                    $user_account['reconnect'] = true;
                    $reconnect_required = true;
                }else{
                    $connected = true;
                }
                $user_accounts[] = $user_account;
            }
            if( $connected ){
                $button_url = add_query_arg( array( 'action' => 'emio_'. static::$option_name, 'callback' => 'disconnect', 'nonce' => wp_create_nonce('emio-'. static::$option_name .'-disconnect') ), admin_url( 'admin-ajax.php' ) );
                $button_text = count($user_accounts) > 1 ? __('Disconnect All', 'events-manager-io') : __('Disonnect', 'events-manager-io');
                $button_class = 'button-secondary';
            }else{
                $button_url = $oauth_url;
                $button_text = __('Connect', 'events-manager-io');
                $button_class = 'button-primary';
            }
        }
		?>
		<div  class="postbox emio-service-connect" id="em-opt-<?php echo static::$option_name; ?>-connect" >
			<div>
				<img class="emio-service-logo" src="<?php echo static::$icon_url; ?>" width="50" height="50">
                <?php if( !is_wp_error($api_client) ): ?>
				<a class="<?php echo $button_class; ?>  emio-connect-button" href="<?php echo esc_url($button_url); ?>"><?php echo esc_html($button_text); ?></a>
				<div class="emio-service-info">
					<h2><?php echo static::$service_name; ?></h2>
					<?php if( $connected || $reconnect_required ): ?>
						<p><?php echo sprintf(esc_html__('You are successfully connected to the following %s accounts and can import events from various services:', 'events-manager-io'), static::$service_name); ?></p>
						<ul clss="emio-service-accounts">
							<?php foreach ( $user_accounts as $user_account ): ?>
								<li class="emio-service-account emio-account-<?php echo empty($user_account['reconnect']) ? 'connected':'disconnected'; ?>">
									<img src="<?php echo esc_url($user_account['photo']); ?>" width="25" height="25">
									<div class="emio-account-description">
                                <span class="emio-account-label">
                                    <?php if( !empty($user_account['reconnect']) ): ?><span class="dashicons dashicons-warning"></span><?php endif; ?>
	                                <?php echo esc_html($user_account['name']) .' <em>('. esc_html($user_account['id']) .')</em>'; ?>
                                </span>
								<span class="emio-account-actions">
                                    <?php if( count($user_accounts) > 1 ): ?>
	                                    <a href="<?php echo esc_url($user_account['disconnect']); ?>"><?php esc_html_e('Disconnect', 'events-manager-io'); ?></a>
                                    <?php elseif( !empty($user_account['reconnect']) ): ?>
	                                    <a href="<?php echo esc_url($oauth_url); ?>"><?php esc_html_e('Reconnect', 'events-manager-io'); ?></a> |
                                        <a href="<?php echo esc_url($user_account['disconnect']); ?>"><?php esc_html_e('Remove', 'events-manager-io'); ?></a>
                                    <?php endif; ?>
                                </span>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
                        <p><a class="button-secondary" href="<?php echo esc_url($oauth_url); ?>"><?php esc_html_e('Connect additional account') ?></a></p>
						<?php do_action('emio_settings_user_auth_after_connect_additional_'.static::$option_name); ?>
						<p><em><?php esc_html_e('If you are experiencing errors when trying to use any of these accounts, try disconnecting and connecting again.', 'events-manager-io'); ?></em></p>
					<?php else: ?>
						<p><em><?php echo sprintf(esc_html__('Connect to import events and locations from %s.','events-manager-io'), static::$service_name); ?></em></p>
					<?php endif; ?>
				</div>
                <?php else: ?>
                <div class="emio-service-info">
                    <p><em><?php echo $api_client->get_error_message(); ?></em></p>
                </div>
                <?php endif; ?>
			</div> <!-- . inside -->
		</div> <!-- .postbox -->
		<?php
	}
}