<?php

use EM_IO\EM_License_Admin;

class EMIO_Admin_Settings {

    public static function init(){
        do_action('emio_settings_init');
        if( !empty($_REQUEST['emio_settings_nonce']) && wp_verify_nonce($_REQUEST['emio_settings_nonce'], 'events-manager-io-settings') ){
            //do some validation here
            if( current_user_can('manage_options') ){
                //save regular settings
                $option_names = array('google_server_key');
	            $options = get_option('emio_settings', array()); //used in EMIO_Options
                foreach( $option_names as $option_name ){
                    $option_value = !isset($_REQUEST[$option_name]) ? '':$_REQUEST[$option_name];
                    $options[$option_name] = $option_value;
                }
                update_option('emio_settings', $options); //used in EMIO_Options
                //save apps
                do_action('emio_settings_save_apps');
            }
            //save user auth settings (if not oauth-style things)
            do_action('emio_settings_save_user_auth');
        }
    }

    public static function main(){
	    global $save_button;
	    $save_button = '<tr><th>&nbsp;</th><td><p class="submit" style="margin:0; padding:0; text-align:right;"><input type="submit" class="button-primary" name="Submit" value="'. esc_attr__( 'Save Changes', 'events-manager') .' ('. esc_attr__('All','events-manager') .')" /></p></td></tr>';
	    $is_admin = current_user_can('manage_options');
	    ?>
	    <div class="wrap events-manager-io-settings tabs-active">
		    <h1 id="em-options-title"><?php _e ( 'Event Manager I/O Options', 'events-manager-io'); ?></h1>
		    <h2 class="nav-tab-wrapper">
			    <a href="#general" id="emio-settings-general" class="nav-tab nav-tab-active"><?php _e('General','events-manager-io'); ?></a>
                <?php if( $is_admin ): ?>
			    <a href="#apps" id="emio-settings-apps" class="nav-tab"><?php _e('Server-Side Credentials','events-manager-io'); ?></a>
                <?php endif; ?>
		        <?php if( has_action('emio_settings_user_auth') || $is_admin ): ?>
                <a href="#connect" id="emio-settings-connect" class="nav-tab"><?php _e('Service Connections','events-manager-io'); ?></a>
		        <?php endif; ?>
			    <?php if( $is_admin ): ?>
				    <a href="#license" id="emio-settings-license" class="nav-tab"><?php _e('License','events-manager-io'); ?></a>
			    <?php endif; ?>
		    </h2>
		    <form id="emio-settings" method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="emio_settings_nonce" value="<?php echo wp_create_nonce('events-manager-io-settings'); ?>" />
			    <div class="metabox-holder">
				    <div class="emio-settings-general emio-settings-group" id="general">
					    <div class='postbox-container'>
						    <div>
							    <div  class="postbox " id="em-opt-general" >
								    <div class="handlediv" title="<?php __('Click to toggle', 'events-manager'); ?>"><br /></div><h3><span><?php esc_html_e ( 'General Options', 'events-manager'); ?> </span></h3>
								    <div class="inside">
									    <table class='form-table'>
										    <?php
										    emio_input_text ( __( 'Google API Server Key', 'events-manager-io'), 'google_server_key', EMIO_Options::get('google_server_key'));
										    echo $save_button;
										    ?>
									    </table>
								    </div> <!-- . inside -->
							    </div> <!-- .postbox -->
						    </div> <!-- .metabox-sortables -->
					    </div> <!-- .postbox-container -->
                        <p class="submit"><input type="submit" class="button-primary" name="Submit" value="<?php esc_attr_e( 'Save Changes', 'events-manager-io'); ?>" /></p>
				    </div> <!-- .em-menu-general .em-menu-group -->
				    <?php if( $is_admin ): ?>
				    <div class="emio-settings-apps emio-settings-group" id="apps">
                        <p><?php esc_html_e('Formats that connect to external services may require you to create "apps" or similar credentials that provide you with server-side credentials allowing you to connect to their services. Below are a list of the services requiring special credentials.','events-manager-io'); ?></p>
                        <?php if( !has_action('emio_settings_apps') ): ?>
                            <p><em><?php esc_html_e('You currently have no formats requiring server-side credentials to connect to their serivces.','events-manager-io'); ?></em></p>
                            <p><a class="button-primary" href="https://eventsmanagerpro.com/events-manager-io/"><?php esc_html_e('Install a new format','events-manager-io'); ?></a></p>
                        <?php else: ?>
                            <div class='postbox-container'>
                                <div>
	                                <?php do_action('emio_settings_apps'); ?>
                                </div> <!-- .metabox-sortables -->
                            </div> <!-- .postbox-container -->
                            <p class="submit"><input type="submit" class="button-primary" name="Submit" value="<?php esc_attr_e( 'Save Changes', 'events-manager-io'); ?>" /></p>
                        <?php endif; ?>
				    </div> <!-- .em-menu-apps .em-menu-group -->
                    <?php endif; ?>
	                <?php if( has_action('emio_settings_user_auth') || $is_admin ): ?>
                    <div class="emio-settings-connect emio-settings-group" id="connect">
					    <?php if( !has_action('emio_settings_user_auth') ): ?>
                            <p><em><?php esc_html_e('You currently have no formats requiring server-side credentials to connect to their serivces.','events-manager-io'); ?></em></p>
                            <?php if( $is_admin ) : ?>
                            <p><a class="button-primary" href="https://eventsmanagerpro.com/events-manager-io/"><?php esc_html_e('Install a new format','events-manager-io'); ?></a></p>
                            <?php endif; ?>
					    <?php else: ?>
                            <p><?php esc_html_e('When importing/exporting data to/from external services, you may need to give us permission to access your account. See below for each available service.','events-manager-io'); ?></p>
                            <div class='postbox-container'>
                                <div>
                                    <?php do_action('emio_settings_user_auth'); ?>
                                </div> <!-- .metabox-sortables -->
                            </div> <!-- .postbox-container -->
                            <p class="submit"><input type="submit" class="button-primary" name="Submit" value="<?php esc_attr_e( 'Save Changes', 'events-manager-io'); ?>" /></p>
					    <?php endif; ?>
                    </div> <!-- .em-menu-formats .em-menu-group -->
	                <?php endif; ?>
				    <?php if( $is_admin ): ?>
				    <div class="emio-settings-license emio-settings-group" id="license">
					    <?php EM_IO\License_Admin::admin_tab_content(); ?>
				    </div> <!-- .emio-settings-license emio-settings-group -->
	                <?php endif; ?>
			    </div> <!-- .metabox-holder -->
		    </form>
	    </div>
	    <?php
    }
}
EMIO_Admin_Settings::init();