<?php
/**
 * @package WordPress
 * @subpackage BuddyBoss BPLA
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'BuddyBoss_BPLA_Admin' ) ):

	/**
	 *
	 * BuddyBoss BPLA Admin
	 * ********************
	 *
	 *
	 */
	class BuddyBoss_BPLA_Admin {
		/* Options/Load
		 * ===================================================================
		 */

		/**
		 * Plugin options
		 *
		 * @var array
		 */
		public	$options = array();
		private $plugin_settings_tabs = array(),
				$network_activated = false,
				$plugin_slug = 'bp-location-autocomplete',
				$menu_hook = 'admin_menu',
				$settings_page = 'buddyboss-settings',
				$capability = 'manage_options',
				$form_action = 'options.php',
				$plugin_settings_url;

		/**
		 * Empty constructor function to ensure a single instance
		 */
		public function __construct() {
			// ... leave empty, see Singleton below
		}

		/* Singleton
		 * ===================================================================
		 */

		/**
		 * Admin singleton
		 *
		 * @since BuddyBoss BPLA (1.0.0)
		 *
		 * @param  array  $options [description]
		 *
		 * @uses BuddyBoss_BPLA_Admin::setup() Init admin class
		 *
		 * @return object Admin class
		 */
		public static function instance() {
			static $instance = null;

			if ( null === $instance ) {
				$instance = new BuddyBoss_BPLA_Admin;
				$instance->setup();
			}

			return $instance;
		}

		/* Utility functions
		 * ===================================================================
		 */

		/**
		 * Get option
		 *
		 * @since BuddyBoss BPLA (1.0.0)
		 *
		 * @param  string $key Option key
		 *
		 * @uses BuddyBoss_BPLA_Plugin::option() Get option
		 *
		 * @return mixed      Option value
		 */
		public function option( $key ) {
			$value = bp_bpla()->option( $key );
			return $value;
		}

		/* Actions/Init
		 * ===================================================================
		 */

		/**
		 * Setup admin class
		 *
		 * @since BuddyBoss BPLA (1.0.0)
		 *
		 * @uses bp_bpla() Get options from main BuddyBoss_BPLA_Plugin class
		 * @uses is_admin() Ensures we're in the admin area
		 * @uses curent_user_can() Checks for permissions
		 * @uses add_action() Add hooks
		 */
		public function setup() {
			if ( ( ! is_admin() && ! is_network_admin() ) || ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$this->plugin_settings_url = admin_url( 'admin.php?page=' . $this->plugin_slug );

			$this->network_activated = $this->is_network_activated();

			//if the plugin is activated network wide in multisite, we need to override few variables
			if ( $this->network_activated ) {
				// Main settings page - menu hook
				$this->menu_hook = 'network_admin_menu';

				// Main settings page - parent page
				$this->settings_page = 'settings.php';

				// Main settings page - Capability
				$this->capability = 'manage_network_options';

				// Settins page - form's action attribute
				$this->form_action = 'edit.php?action=' . $this->plugin_slug;

				// Plugin settings page url
				$this->plugin_settings_url = network_admin_url( 'settings.php?page=' . $this->plugin_slug );
			}

			//if the plugin is activated network wide in multisite, we need to process settings form submit ourselves
			if ( $this->network_activated ) {
				add_action( 'network_admin_edit_' . $this->plugin_slug, array( $this, 'save_network_settings_page' ) );
			}

			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_init', array($this, 'register_support_settings' ) );
			
			add_action( $this->menu_hook, array( $this, 'admin_menu' ) );

			add_filter( 'plugin_action_links', array( $this, 'add_action_links' ), 10, 2 );
			add_filter( 'network_admin_plugin_action_links', array( $this, 'add_action_links' ), 10, 2 );
                       
		}

		/**
		 * Check if the plugin is activated network wide(in multisite).
		 * 
		 * @return boolean
		 */
		private function is_network_activated() {
			$network_activated = false;
			if ( is_multisite() ) {
				if ( ! function_exists( 'is_plugin_active_for_network' ) )
					require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

				if ( is_plugin_active_for_network( basename( constant( 'BUDDYBOSS_BPLA_PLUGIN_DIR' ) ) . '/bp-location-autocomplete.php' ) ) {
					$network_activated = true;
				}
			}
			return $network_activated;
		}

		/**
		 * Register admin settings
		 *
		 * @since BuddyBoss BPLA (1.0.0)
		 *
		 * @uses register_setting() Register plugin options
		 * @uses add_settings_section() Add settings page option sections
		 * @uses add_settings_field() Add settings page option
		 */
        public function admin_init() {
            $this->plugin_settings_tabs['bp_bpla_plugin_options'] = 'General';
            register_setting( 'bp_bpla_plugin_options', 'bp_bpla_plugin_options', array( $this, 'plugin_options_validate' ) );
            add_settings_section( 'general_section', __( 'General Settings', 'bp-location-autocomplete' ), array( $this, 'section_general' ), __FILE__ );

            //general options
            add_settings_field('google-api-key', __( 'Google Maps API key', 'bp-location-autocomplete' ), array($this, 'google_api_key'), __FILE__, 'general_section');
            add_settings_field('address-field-selection', __( 'Number of Fields', 'bp-location-autocomplete' ), array($this, 'address_field_selection'), __FILE__, 'general_section');
            
            $address_field = $this->option( 'location-field-address-selection' ); {
                if ( $address_field && 'single' == $address_field ) {
                    $field_profile_name = 'Profile Address Field';
                    $field_group_name = 'Group Address Field';
                } else {
                    $field_profile_name = 'Profile Location Fields';
                    $field_group_name = 'Group Location Fields';
                }
            }
            
            add_settings_field('location-profile-fields', __( $field_profile_name, 'bp-location-autocomplete' ), array($this, 'location_profile_fields'), __FILE__, 'general_section');

            if ( bp_is_active( 'groups' ) ) {
	            add_settings_field( 'location-group-fields', __( $field_group_name, 'bp-location-autocomplete' ), array(
		            $this,
		            'location_group_fields'
	            ), __FILE__, 'general_section' );
            }
            
        }

        function address_field_selection(){
            $this->section_address_field();
            ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 0">
                    <p class="submit"><input name="bboss_g_s_settings_submit" type="submit" class="button-primary" value="Save Changes"></p>
            <?php
        }

        function location_profile_fields() {
            $address_field = $this->option( 'location-field-address-selection' );
            $value = $this->option( 'enable-for-profiles' );
            $checked = '';
            if ( $value=='yes' ){
                    $checked = ' checked="checked" ';
            } ?>
                <input <?php echo $checked; ?> id='enable-for-profiles' name='bp_bpla_plugin_options[enable-for-profiles]' type='checkbox' value='yes' /><label for="enable-for-profiles" ><?php _e( 'Enable for Profiles', 'bp-location-autocomplete' ); ?></label><br /><br />
            <?php
            if ( $address_field && 'single' == $address_field ) {
                $display1 = '';
                $display2 = 'style="display:none;"';
                echo '<p class="description">' . __('Go to Users > Profile Fields, and create a profile field to be used for Address. Make sure the field type is a <strong>Text Box</strong>. Then return here and select your profile field below.', 'bp-location-autocomplete') . '</p><br />';
            } else {
                $display1 = 'style="display:none;"';
                $display2 = '';
                echo '<p class="description">' . __('Go to Users > Profile Fields, and create profile fields for each location source you need (State, Postal Code, etc.). Make sure each profile field type is a <strong>Text Box</strong>. Then return here and select your profile fields for the sections below.', 'bp-location-autocomplete') . '</p><br />';
            } ?>
                <table class="form-table" <?php echo $display1; ?>>
                    <tbody>
                    <tr class="user-street-address-wrap">
                        <th><label><?php _e('Address', 'bp-location-autocomplete'); ?></label></th>
                        <td><?php $this->setting_location_field_address(); ?></td>
                    </tr>
                    </tbody>
                </table>
                <table class="form-table" <?php echo $display2; ?>>
                    <tbody>
                    <tr class="user-street-address-wrap">
                        <th><label><?php _e('Street Address', 'bp-location-autocomplete'); ?></label></th>
                        <td><?php $this->setting_location_field_street(); ?></td>
                    </tr>
                    <tr class="user-city-wrap">
                        <th><label><?php _e('City', 'bp-location-autocomplete'); ?></label></th>
                        <td><?php $this->setting_location_field_city(); ?></td>
                    </tr>
                    <tr class="user-state-province-wrap">
                        <th><label><?php _e('State/Province', 'bp-location-autocomplete'); ?></label></th>
                        <td><?php $this->setting_location_field_state(); ?></td>
                    </tr>
                    <tr class="user-zip-postal-wrap">
                        <th><label><?php _e('ZIP/Postal Code', 'bp-location-autocomplete'); ?></label></th>
                        <td><?php $this->setting_location_field_zipcode(); ?></td>
                    </tr>
                    <tr class="user-country-wrap">
                        <th><label><?php _e('Country', 'bp-location-autocomplete'); ?></label></th>
                        <td><?php $this->setting_location_field_country(); ?></td>
                    </tr>
                    </tbody>
                </table>
            <?php
        }

        function location_group_fields(){
            $address_field = $this->option( 'location-field-address-selection' );
            $value = $this->option( 'enable-for-groups' );
            $checked = '';
            if ( $value=='yes' ){
                    $checked = ' checked="checked" ';
            } ?>
                <input <?php echo $checked; ?> id='enable-for-groups' name='bp_bpla_plugin_options[enable-for-groups]' type='checkbox' value='yes' /><label for="enable-for-groups" ><?php _e( 'Enable for Groups', 'bp-location-autocomplete' ); ?></label><br /><br />
            <?php
            if ( $address_field && 'single' == $address_field ) {
                $display1 = '';
                $display2 = 'style="display:none;"';
                echo '<p class="description">' . __('Add your own title for the Address field, to be used when editing a group.', 'bp-location-autocomplete') . '</p><br />';
            } else {
                $display1 = 'style="display:none;"';
                $display2 = '';
                echo '<p class="description">' . __('Check all of the location fields you want to enable, and add your own titles for each field.', 'bp-location-autocomplete') . '</p><br />';
            }

            $location_group_address = $this->option( 'location-group-address' );
            $location_group_street = $this->option( 'location-group-street-address' );
            $location_group_city = $this->option( 'location-group-city' );
            $location_group_state = $this->option( 'location-group-state' );
            $location_group_zip = $this->option( 'location-group-zip' );
            $location_group_country = $this->option( 'location-group-country' );
            ?>
                <table class="form-table" <?php echo $display1; ?>>
                    <tbody>
                    <tr class="group-address-wrap">
                        <th><label><?php _e('Address', 'bp-location-autocomplete'); ?></label></th>
                        <td><input name="bp_bpla_plugin_options[location-group-address]" type="text" placeholder="Address" value="<?php echo esc_attr( $location_group_address ); ?>" /></td>
                    </tr>
                    </tbody>
                </table>
                <table class="form-table" <?php echo $display2; ?>>
                    <tbody>
                    <tr class="group-street-address-wrap">
                        <th>
                            <?php
                            $street_address_req = $this->option( 'group-street-address-required-field' );
                            $checked = '';
                            if ( $street_address_req == 'yes' ) {
                                    $checked = ' checked="checked" ';
                            } ?>
                            <input <?php echo $checked; ?> id='location-group-street-address' name='bp_bpla_plugin_options[group-street-address-required-field]' type='checkbox' value='yes' />
                            <label for="location-group-street-address"><?php _e('Street Address', 'bp-location-autocomplete'); ?></label>
                        </th>
                        <td><input name="bp_bpla_plugin_options[location-group-street-address]" type="text" placeholder="Street Address" value="<?php echo esc_attr( $location_group_street ); ?>" /></td>
                    </tr>
                    <tr class="group-city-wrap">
                        <th>
                            <?php
                            $city_address_req = $this->option( 'group-city-required-field' );
                            $checked = '';
                            if ( $city_address_req == 'yes' ) {
                                    $checked = ' checked="checked" ';
                            } ?>
                            <input <?php echo $checked; ?> id='location-city-address' name='bp_bpla_plugin_options[group-city-required-field]' type='checkbox' value='yes' />
                            <label for="location-city-address"><?php _e('City', 'bp-location-autocomplete'); ?></label>
                        </th>
                        <td><input name="bp_bpla_plugin_options[location-group-city]" type="text" placeholder="City" value="<?php echo esc_attr( $location_group_city ); ?>" /></td>
                    </tr>
                    <tr class="group-state-wrap">
                        <th>
                            <?php
                            $state_address_req = $this->option( 'group-state-required-field' );
                            $checked = '';
                            if ( $state_address_req == 'yes' ) {
                                    $checked = ' checked="checked" ';
                            } ?>
                            <input <?php echo $checked; ?> id='location-state-address' name='bp_bpla_plugin_options[group-state-required-field]' type='checkbox' value='yes' />
                            <label for="location-state-address"><?php _e('State/Province', 'bp-location-autocomplete'); ?></label>
                        </th>
                        <td><input name="bp_bpla_plugin_options[location-group-state]" type="text" placeholder="State/Province" value="<?php echo esc_attr( $location_group_state ); ?>" /></td>
                    </tr>
                    <tr class="group-zip-wrap">
                        <th>
                            <?php
                            $zip_address_req = $this->option( 'group-zip-required-field' );
                            $checked = '';
                            if ( $zip_address_req == 'yes' ) {
                                    $checked = ' checked="checked" ';
                            } ?>
                            <input <?php echo $checked; ?> id='location-zip-address' name='bp_bpla_plugin_options[group-zip-required-field]' type='checkbox' value='yes' />
                            <label for="location-zip-address"><?php _e('Zip/Postal Code', 'bp-location-autocomplete'); ?></label>
                        </th>
                        <td><input name="bp_bpla_plugin_options[location-group-zip]" type="text" placeholder="Zip/Postal Code" value="<?php echo esc_attr( $location_group_zip ); ?>" /></td>
                    </tr>
                    <tr class="group-country-wrap">
                        <th>
                            <?php
                            $country_address_req = $this->option( 'group-country-required-field' );
                            $checked = '';
                            if ( $country_address_req == 'yes' ) {
                                    $checked = ' checked="checked" ';
                            } ?>
                            <input <?php echo $checked; ?> id='location-country-address' name='bp_bpla_plugin_options[group-country-required-field]' type='checkbox' value='yes' />
                            <label for="location-country-address"><?php _e('Country', 'bp-location-autocomplete'); ?></label>
                        </th>
                        <td><input name="bp_bpla_plugin_options[location-group-country]" type="text" placeholder="Country" value="<?php echo esc_attr( $location_group_country ); ?>" /></td>
                    </tr>
                    </tbody>
                </table>
                <p class="description"><?php _e('Are group admins required to enter a group address?', 'bp-location-autocomplete'); ?> </p><br />
                <?php
                $value = $this->option( 'group-required-field' );
		$checked = '';
		if ( $value=='yes' ){
			$checked = ' checked="checked" ';
		} ?>
                <input <?php echo $checked; ?> id='location-group-address' name='bp_bpla_plugin_options[group-required-field]' type='checkbox' value='yes' /><label for="location-group-address" ><?php _e( 'Address Fields Required', 'bp-location-autocomplete' ); ?></label>

                <?php    
            }

                function google_api_key(){
                        echo '<p class="description">' . __( 'A Google Maps API key is <strong>required</strong> to use this plugin.', 'bp-location-autocomplete' ) . ' <a href="https://console.developers.google.com/projectselector/apis/credentials?pli=1" target= "_blank">' . esc_html__( 'Generate your API key', 'bp-location-autocomplete' ) . '</a> ' . __( 'and then paste it below.', 'bp-location-autocomplete' ) . ' <a class="button" href="https://youtu.be/WlhULYTRqHM?list=PL5kBYJSuuvEj5ueag2G-RVtFlgYHJh1AA" target= "_blank">' . esc_html__( 'Video Tutorial', 'bp-location-autocomplete' ) . '</a></p><br />';
                        $this->setting_location_api_key();
                }
		
		function register_support_settings() {
			$this->plugin_settings_tabs[ 'bp_bpla_support_options' ] = __( 'Support','bp-location-autocomplete' );

			register_setting( 'bp_bpla_support_options', 'bp_bpla_support_options' );
			add_settings_section( 'section_support', ' ', array( &$this, 'section_support_desc' ), 'bp_bpla_support_options' );
		}

		function section_support_desc() {
			if ( file_exists( dirname( __FILE__ ) . '/help-support.php' ) ) {
				require_once( dirname( __FILE__ ) . '/help-support.php' );
			}
		}

		/**
		 * Add plugin settings page
		 *
		 * @since BuddyBoss BPLA (1.0.0)
		 *
		 * @uses add_options_page() Add plugin settings page
		 */
		public function admin_menu() {
			add_submenu_page(
                            $this->settings_page, 'Location Autocomplete for BuddyPress', 'Location Fields', $this->capability, $this->plugin_slug, array( $this, 'options_page' )
			);
		}

		/**
		 * Add plugin settings page
		 *
		 * @since BuddyBoss BPLA (1.0.0)
		 *
		 * @uses BuddyBoss_BPLA_Admin::admin_menu() Add settings page option sections
		 */
		public function network_admin_menu() {
			return $this->admin_menu();
		}

		/* Settings Page + Sections
		 * ===================================================================
		 */

		/**
		 * Render settings page
		 *
		 * @since BuddyBoss BPLA (1.0.0)
		 *
		 * @uses do_settings_sections() Render settings sections
		 * @uses settings_fields() Render settings fields
		 * @uses esc_attr_e() Escape and localize text
		 */
		public function options_page() {
			$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : __FILE__;
                        
                        if ( isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true'  ) { ?>
                            <div class="updated settings-error notice is-dismissible" id="setting-error-settings_updated"> 
                            <p><strong><?php _e('Settings saved.','bp-location-autocomplete'); ?></strong></p><button class="notice-dismiss" type="button"><span class="screen-reader-text"><?php _e('Dismiss this notice.','bp-location-autocomplete'); ?></span></button></div><?php
                        }
                        
			?>
			<div class="wrap">
				<h2><?php _e( 'Location Autocomplete for BuddyPress', 'bp-location-autocomplete' ); ?></h2>
				<?php $this->plugin_options_tabs(); ?>
				<form action="<?php echo $this->form_action; ?>" method="post">

					<?php
					if ( $this->network_activated && isset( $_GET[ 'updated' ] ) ) {
						echo "<div class='updated'><p>" . __( 'Settings updated.', 'bp-location-autocomplete' ) . "</p></div>";
					}
					if ( 'bp_bpla_plugin_options' == $tab || empty($_GET['tab']) ) {
						settings_fields( 'bp_bpla_plugin_options' );
						do_settings_sections( __FILE__ ); ?>
						<p class="submit">
							<input name="bboss_g_s_settings_submit" type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes','bp-location-autocomplete' ); ?>" />
						</p><?php
					} else {
						settings_fields( $tab );
						do_settings_sections( $tab );
					} ?>
						
				</form>
			</div>

			<?php
		}
		
		function plugin_options_tabs() {
			$current_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'bp_bpla_plugin_options';

			echo '<h2 class="nav-tab-wrapper">';
			foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
				echo '<a class="nav-tab ' . $active . '" href="?page=' . 'bp-location-autocomplete' . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
			}
			echo '</h2>';
		}
		
		public function add_action_links( $links, $file ) {
			// Return normal links if not this plugin
			if ( plugin_basename( basename( constant( 'BUDDYBOSS_BPLA_PLUGIN_DIR' ) ) . '/bp-location-autocomplete.php' ) != $file ) {
				return $links;
			}

			$mylinks = array(
				'<a href="' . esc_url( $this->plugin_settings_url ) . '">' . __( "Settings", 'bp-location-autocomplete' ) . '</a>',
			);
			return array_merge( $links, $mylinks );
		}

		public function save_network_settings_page() {
			if ( ! check_admin_referer( 'bp_bpla_plugin_options-options' ) )
				return;

			if ( ! current_user_can( $this->capability ) )
				die( 'Access denied!' );

			if ( isset( $_POST[ 'bboss_g_s_settings_submit' ] ) ) {
				$submitted = stripslashes_deep( $_POST[ 'bp_bpla_plugin_options' ] );
				$submitted = $this->plugin_options_validate( $submitted );

				update_site_option( 'bp_bpla_plugin_options', $submitted );
			}

			// Where are we redirecting to?
			$base_url = trailingslashit( network_admin_url() ) . 'settings.php';
			$redirect_url = esc_url_raw( add_query_arg( array( 'page' => $this->plugin_slug, 'updated' => 'true' ), $base_url ) );

			// Redirect
			wp_redirect( $redirect_url );
			die();
		}
		
		/**
		 * Address field section
		 *
		 * @since BuddyBoss BPLA (1.0.0)
		 */
		public function section_address_field() {
  
                    echo '<p class="description">Do you want to collect all location data in one Address field, or break it apart into multiple location fields?</p><br />';

                    $address_field = $this->option( 'location-field-address-selection' );
                    
                    if( !$address_field ) {
                            $address_field = 'multiple';
                    }
                    
                    $options = array(
                            'single'	=> __( '<strong>Single Address Field</strong>', 'bp-location-autocomplete' ),
                            'multiple'	=> __( '<strong>Multiple Location Fields</strong>', 'bp-location-autocomplete' )
                    );
                    foreach( $options as $option=>$label ) {
                            $checked = $address_field == $option ? ' checked' : '';
                            echo '<label><input type="radio" name="bp_bpla_plugin_options[location-field-address-selection]" value="'. $option . '" '. $checked . '>' . $label . '</label>&nbsp;&nbsp;';
                    }
                    
		}
                
		/**
		 * General settings section
		 *
		 * @since BuddyBoss BPLA (1.0.0)
		 */
		public function section_general() {

		}
                
                /**
                 * Associate Location Field Street selectbox
                 * 
                 * @since BuddyBoss BPLA (1.0.0)
                 */
                public function setting_location_field_address() {
                    $location_field_address = $this->option( 'location-field-address' );
                    
                    echo '<select name="bp_bpla_plugin_options[location-field-address]">';
                    
                    $options = $this->fetch_profile_fields();
                    
                    foreach( $options as $option=>$label ){
                            $selected = $option==$location_field_address ? ' selected' : '';
                            echo '<option value="' . esc_attr( $option ).  '" ' . $selected . '>' . $label . '</option>';
                    }

                    echo '</select>';
                }
                
                /**
                 * Associate Location Field Street selectbox
                 * 
                 * @since BuddyBoss BPLA (1.0.0)
                 */
                public function setting_location_field_street() {
                    $location_field_city = $this->option( 'location-field-street' );
                    
                    echo '<select name="bp_bpla_plugin_options[location-field-street]">';
                    
                    $options = $this->fetch_profile_fields();
                    
                    foreach( $options as $option=>$label ){
                            $selected = $option==$location_field_city ? ' selected' : '';
                            echo '<option value="' . esc_attr( $option ).  '" ' . $selected . '>' . $label . '</option>';
                    }

                    echo '</select>';
                }
                
                /**
                 * Associate Location Field City selectbox
                 * 
                 * @since BuddyBoss BPLA (1.0.0)
                 */
                public function setting_location_field_city() {
                    $location_field_city = $this->option( 'location-field-city' );
                    
                    echo '<select name="bp_bpla_plugin_options[location-field-city]">';
                    
                    $options = $this->fetch_profile_fields();
                    
                    foreach( $options as $option=>$label ){
                            $selected = $option==$location_field_city ? ' selected' : '';
                            echo '<option value="' . esc_attr( $option ).  '" ' . $selected . '>' . $label . '</option>';
                    }

                    echo '</select>';
                }
                
                /**
                 * Associate Location Field State selectbox
                 * 
                 * @since BuddyBoss BPLA (1.0.0)
                 */
                public function setting_location_field_state() {
                    $location_field_city = $this->option( 'location-field-state' );
                    
                    echo '<select name="bp_bpla_plugin_options[location-field-state]">';
                    
                    $options = $this->fetch_profile_fields();
                    
                    foreach( $options as $option=>$label ){
                            $selected = $option==$location_field_city ? ' selected' : '';
                            echo '<option value="' . esc_attr( $option ).  '" ' . $selected . '>' . $label . '</option>';
                    }

                    echo '</select>';
                }
 
                /**
                 * Associate Location Field ZIP Code selectbox
                 * 
                 * @since BuddyBoss BPLA (1.0.0)
                 */
                public function setting_location_field_zipcode() {
                    $location_field_city = $this->option( 'location-field-zipcode' );
                    
                    echo '<select name="bp_bpla_plugin_options[location-field-zipcode]">';
                    
                    $options = $this->fetch_profile_fields();
                    
                    foreach( $options as $option=>$label ){
                            $selected = $option==$location_field_city ? ' selected' : '';
                            echo '<option value="' . esc_attr( $option ).  '" ' . $selected . '>' . $label . '</option>';
                    }

                    echo '</select>';
                }

                /**
                 * Associate Location Field Country selectbox
                 * 
                 * @since BuddyBoss BPLA (1.0.0)
                 */
                public function setting_location_field_country() {
                    $location_field_city = $this->option( 'location-field-country' );
                    
                    echo '<select name="bp_bpla_plugin_options[location-field-country]">';
                    
                    $options = $this->fetch_profile_fields();
                    
                    foreach( $options as $option=>$label ){
                            $selected = $option==$location_field_city ? ' selected' : '';
                            echo '<option value="' . esc_attr( $option ).  '" ' . $selected . '>' . $label . '</option>';
                    }

                    echo '</select>';
                }
                
                /**
                 * Google API Key
                 * 
                 * @since BuddyBoss BPLA (1.0.0)
                 */
                public function setting_location_api_key() {
                    $location_api_key = $this->option( 'location-api-key' );
                    echo '<input type="text" name="bp_bpla_plugin_options[location-api-key]" value="'.esc_attr( $location_api_key).'" style="width: 35%;" >';                        
                }

		/**
		 * Validate plugin option
		 *
		 * @since BuddyBoss BPLA (1.0.0)
		 */
		public function plugin_options_validate( $input ) {
                    if( isset($input[ 'enabled' ]) ){
                        $input[ 'enabled' ] = sanitize_text_field( $input[ 'enabled' ] );
                    }

			return $input; // return validated input
		}
                
                public function fetch_profile_fields() {
                    $groups = bp_xprofile_get_groups(array(
                        'fetch_fields' => true
                    ));
                    $options = array('field_0' => 'Select');
                    $group_one_array = array();
                    
                    foreach ( $groups as $group ) {
                        $group_one_array = $group->fields;
                        break;
                    }
                    
                    foreach ( $group_one_array as $group_field ) {
                        $options['field_'.$group_field->id] = $group_field->name;
                    }
                    
                    return $options;
                    
                }
                

	} //End of BuddyBoss_BPLA_Admin class
	

endif;

