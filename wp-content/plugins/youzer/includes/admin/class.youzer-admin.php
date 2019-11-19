<?php

class Youzer_Admin {

	function __construct() {

		// Youzer Admin Pages
	    $this->admin_pages = array(
	    	'youzer-panel', 'yz-profile-settings', 'yz-widgets-settings', 'yz-membership-settings'
	    );

		// Init Admin Area
		add_action( 'init', array( &$this, 'init' ) );
		
        // Add Plugin Links.
        add_filter(
            'plugin_action_links_' . YOUZER_BASENAME,
            array( $this, 'plugin_action_links' )
        );

        // Add Plugin Links in Multisite..
        add_filter(
            'network_admin_plugin_action_links_' . YOUZER_BASENAME,
            array( $this, 'plugin_action_links' )
        );

	}

	/**
	 * Init Admin Functions.
	 */
	function init() {

		require_once YZ_ADMIN_CORE . 'functions/yz-general-functions.php';
		require_once YZ_ADMIN_CORE . 'functions/yz-account-functions.php';
		require_once YZ_ADMIN_CORE . 'functions/yz-profile-functions.php';
		require_once YZ_ADMIN_CORE . 'yz-extensions.php';
		// require_once YZ_ADMIN_CORE . 'functions/yz-update-notifier.php';

		if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
			require_once YZ_ADMIN_CORE . 'functions/kainelabs-plugins-updater.php';
		}
		

		// Extension Updaters.
		add_action( 'admin_enqueue_scripts', array( &$this, 'extensions_updater' ) );
		
		// Add Youzer Plugin Admin Pages.
		add_action( 'admin_menu', array( &$this, 'admin_init' ) );
		add_action( 'wp_ajax_yz_save_addon_key_license', array( &$this, 'save_addon_key_license' ) );


	    if ( ! wp_doing_ajax() && ! is_youzer_panel() ) {
	    	return;
	    }

		add_action( 'admin_init',  array( &$this, 'youzer_admin_init' ) );

		// Load Admin Scripts & Styles .
		add_action( 'admin_print_styles', array( &$this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_scripts' ) );

	}

    /**
     * Youzer Action Links
     */
    function plugin_action_links( $links ) {
        
        // Get Youzer Plugin Pages. 
        $panel_url 	 = esc_url( add_query_arg( array( 'page' => 'youzer-panel' ), admin_url( 'admin.php' ) ) );
        $plugin_url  = 'https://codecanyon.net/item/youzer-new-wordpress-user-profiles-era/19716647';
        $docs_url    = 'https://kainelabs.ticksy.com/articles/';
        $support_url = 'https://kainelabs.ticksy.com';
        $addons_url  = 'https://kainelabs.com';

        // Add a few links to the existing links array.
        return array_merge( $links, array(
            'settings' => '<a href="' . $panel_url . '">' . esc_html__( 'Settings', 'youzer' ) . '</a>',
            'about'    => '<a href="' . $plugin_url . '">' . esc_html__( 'About', 'youzer' ) . '</a>',
            'docs'    => '<a href="' . $docs_url . '">' . esc_html__( 'Docs', 'youzer' ) . '</a>',
            'support'  => '<a href="' . $support_url . '">' . esc_html__( 'Support',  'youzer' ) . '</a>',
            'addons'  => '<a href="' . $addons_url . '">' . esc_html__( 'Add-Ons',  'youzer' ) . '</a>'
        ) );

    }

	/**
	 * # Initialize Youzer Admin Panel
	 */
	function youzer_admin_init() {

		// Init Admin Files.
		require_once YZ_ADMIN_CORE . 'yz-admin-dashboard.php';
		require_once YZ_ADMIN_CORE . 'yz-admin-panel.php';
		require_once YZ_ADMIN_CORE . 'yz-admin-ajax.php';

		// General Settings .
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-wall.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-panel.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-emoji.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-author.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-groups.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-schemes.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-widgets.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-general.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-social-networks.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-custom-styling.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-groups-directory.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-members-directory.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-account-verification.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-bookmarks.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-reviews.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-patches.php';
        
        if ( defined( 'myCRED_VERSION' ) ) {
			require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-mycred.php';
		}

        if ( class_exists( 'bbPress' ) ) {
			require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-bbpress.php';
		}

        if ( class_exists( 'Woocommerce' ) ) {
			require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-woocommerce.php';
		}

		// Profile Settings .
		require_once YZ_ADMIN_CORE . 'profile-settings/yz-settings-ads.php';
		require_once YZ_ADMIN_CORE . 'profile-settings/yz-settings-tabs.php';
		require_once YZ_ADMIN_CORE . 'profile-settings/yz-settings-media.php';
		require_once YZ_ADMIN_CORE . 'profile-settings/yz-settings-posts.php';
		require_once YZ_ADMIN_CORE . 'profile-settings/yz-settings-header.php';
		require_once YZ_ADMIN_CORE . 'profile-settings/yz-settings-navbar.php';
		require_once YZ_ADMIN_CORE . 'profile-settings/yz-settings-general.php';
		require_once YZ_ADMIN_CORE . 'profile-settings/yz-settings-comments.php';
		require_once YZ_ADMIN_CORE . 'profile-settings/yz-settings-tab-info.php';
		require_once YZ_ADMIN_CORE . 'profile-settings/yz-settings-structure.php';
		require_once YZ_ADMIN_CORE . 'profile-settings/yz-settings-profile-404.php';

		// init Administration
		$this->panel 	  = new Youzer_Panel();
		$this->dashboard  = new Youzer_Dashboard();
		$this->ajax 	  = new Youzer_Admin_Ajax();

	}

	/**
	 * # Add Youzer Admin Pages .
	 */
	function admin_init() {

		// Show Youzer Panel to Admin's Only.
		if ( ! current_user_can( 'manage_options' ) && ! apply_filters( 'yz_show_youzer_panel', false ) ) {
			return false;
		}

	    // Add Youzer Plugin Admin Page.
	    add_menu_page(
	    	__( 'Youzer Panel', 'youzer' ),
	    	__( 'Youzer Panel', 'youzer' ),
	    	'administrator',
	    	'youzer-panel',
	    	array( &$this->dashboard, 'general_settings' ),
	    	YZ_AA . 'images/icon.png'
	    );

		// Add "General Settings" Page .
	    add_submenu_page(
	    	'youzer-panel',
	    	__( 'Youzer - General Settings', 'youzer' ),
	    	__( 'General Settings', 'youzer' ),
	    	'administrator',
	    	'youzer-panel',
	    	array( &$this->dashboard, 'general_settings' )
	    );

	    // Add "Profile Settings" Page .
	    add_submenu_page(
	    	'youzer-panel',
	    	__( 'Youzer - Profile Settings', 'youzer' ),
	    	__( 'Profile Settings', 'youzer' ),
	    	'administrator',
	    	'yz-profile-settings',
	    	array( &$this->dashboard, 'profile_settings' )
	    );

	    // Add "Widgets Settings" Page .
	    add_submenu_page(
	    	'youzer-panel',
	    	__( 'Youzer - Widgets Settings', 'youzer' ),
	    	__( 'Widgets Settings', 'youzer' ),
	    	'administrator',
	    	'yz-widgets-settings',
	    	array( &$this->dashboard, 'widgets_settings' )
	    );

	}

	/**
	 * # Admin Scripts.
	 */
	function admin_scripts() {

		if ( ! isset( $_GET['page'] ) ) {
			return false;
		}

		// Set Up Variables
		$jquery = array( 'jquery' );

	    if ( in_array( $_GET['page'], $this->admin_pages ) ) {

	        global $Yz_Translation;

	        // Load Panel JS
	        wp_enqueue_script( 'yz-panel' );

	    	// Admin Panel Script
	    	wp_enqueue_script( 'yz-panel', YZ_PA . 'js/yz-settings-page.min.js', $jquery, YZ_Version, true );
	        wp_enqueue_script( 'ukai-panel', YZ_AA . 'js/ukai-panel.min.js', $jquery, YZ_Version, true );
	        wp_localize_script( 'yz-panel', 'yz', $Yz_Translation );

	        // Load Color Picker
	        wp_enqueue_script( 'wp-color-picker' );
    		wp_enqueue_style( 'wp-color-picker' );

	    }

	    // Functions script Tabs
	    $functions_script = array( 'custom-widgets', 'user-tags-widget', 'reaction-settings' );

	    if ( 
	    	'youzer-panel' == $_GET['page'] || 'yz-profile-settings' == $_GET['page']
	    	||
	    	( isset( $_GET['tab'] ) && in_array( $_GET['tab'], $functions_script ) )
	    ) {
		    // Admin Panel Script
		    wp_enqueue_script(
		    	'yz-functions',
		    	YZ_AA . 'js/yz-functions.min.js',
		    	array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'yz-iconpicker' ),
		    	YZ_Version, true
		    );
	    }

	    if ( 'yz-profile-settings' == $_GET['page'] ) {
	    	// Profile Structure
	        wp_enqueue_script( 'yz-profile-structure' );
	    }

	    if ( isset( $_GET['tab'] ) && 'social-networks' == $_GET['tab'] ) {
		    // Load Social Networks Builder .
		    wp_enqueue_script( 'yz-networks', YZ_AA . 'js/yz-networks.min.js', $jquery, YZ_Version, true );
	    }

        // Load Tags Script
        wp_enqueue_script( 'yz-ukaitags' );

	    if ( in_array( $_GET['page'] , $this->admin_pages) )  {
	        // Uploader Scripts
	        wp_enqueue_media();
	    }

		// Load Ads Builder.
	    if ( isset( $_GET['tab'] ) && 'ads' == $_GET['tab'] ) {
		    wp_enqueue_script( 'yz-ads', YZ_AA . 'js/yz-ads.min.js', $jquery, YZ_Version, true );
	    }
	    
	    if ( isset( $_GET['tab'] ) && 'custom-widgets' == $_GET['tab'] ) {
	    	// Load Profile Widgets Script.
		    wp_enqueue_script( 'yz-profile-widgets', YZ_AA . 'js/yz-profile-widgets.min.js', $jquery, YZ_Version, true );
	    }

	    if ( isset( $_GET['tab'] ) && 'custom-tabs' == $_GET['tab'] ) {
	    	// Load Custom Tabs Script.
		    wp_enqueue_script( 'yz-custom-tabs', YZ_AA . 'js/yz-custom-tabs.min.js', $jquery, YZ_Version, true );
	    }

	    if ( isset( $_GET['tab'] ) && 'user-tags-widget' == $_GET['tab'] ) {
	    	// Load User Tags Script.
		    wp_enqueue_script( 'yz-user-tags', YZ_AA . 'js/yz-user-tags.min.js', $jquery, YZ_Version, true );
	    }

	}

	/**
	 * # Panel Styles.
	 */
	function admin_styles() {

		if ( ! isset( $_GET['page'] ) ) {
			return false;
		}

		// Load Admin Panel Styles
	    if ( in_array( $_GET['page'], $this->admin_pages ) ) {
	    	// Load Settings Style
		    wp_enqueue_style( 'yz-panel-css', YZ_AA . 'css/yz-panel-css.min.css', array(), YZ_Version );
	        // Load Admin Panel Style
		    wp_enqueue_style( 'yz-admin-style', YZ_AA . 'css/yz-admin-style.min.css', array(), YZ_Version );
	        // Load Google Fonts
	        wp_enqueue_style( 'yz-fonts', 'https://fonts.googleapis.com/css?family=Open+Sans:100,400,600', array(), YZ_Version );
			// Icons.
			$this->icon_picker_scripts();	    	
	    }

	}
	
	/**
	 * Icon
	 */
	function icon_picker_scripts() {
		// Loading Font Awesome.
    	wp_enqueue_style( 'yz-icons', YZ_AA . 'css/all.min.css', array(), YZ_Version );
	    wp_enqueue_style( 'yz-iconpicker' );
	}

	/**
	 * Extension Updaters.
	 **/
	function extensions_updater() {

		global $pagenow;

		if ( $pagenow == 'plugins.php' ) {
		    wp_enqueue_script( 'yz-automatic-updates', YZ_AA . 'js/yz-automatic-updates.js', array(), YZ_Version );
		    wp_localize_script( 'yz-automatic-updates', 'Yz_Automatic_Updates', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		}

		if ( $pagenow == 'widgets.php' ) {
			// Icons.
			$this->icon_picker_scripts();	   
		}
		
	}

	/**
	 * Save Add On Key License
	 */
	function save_addon_key_license() {

		// run a quick security check
	 	if ( ! check_admin_referer( 'yz_addon_license_notice', 'nounce' ) ) {
			return;
	 	}

		// retrieve the license from the database
		$license = trim( $_POST['license'] );


		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( $_POST['product_name'] ), // the name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( EDD_KAINELABS_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch( $license_data->error ) {

					case 'expired' :

						$message = sprintf(
							__( 'Your license key expired on %s.', 'youzer' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'disabled' :
					case 'revoked' :

						$message = __( 'Your license key has been disabled.', 'youzer' );
						break;

					case 'missing' :

						$message = __( 'Invalid license.', 'youzer' );
						break;

					case 'invalid' :
					case 'site_inactive' :

						$message = __( 'Your license is not active for this URL.', 'youzer' );
						break;

					case 'item_name_mismatch' :

						$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'youzer' ), $_POST['product_name'] );
						break;

					case 'no_activations_left':

						$message = __( 'Your license key has reached its activation limit.', 'youzer' );
						break;

					default :

						$message = __( 'An error occurred, please try again.', 'youzer' );
						break;
				}

			}

		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			$response = array( 'error' => $message );
		} else {
			update_option( $_POST['name'], $license );
			$response = array( 'success' => __( 'Thank you for registering your license.', 'youzer' ) );
		}
		
		echo json_encode( $response );

		exit();

	}

	/**
	 * Add License Activation Notice.
	 */
	function extension_validate_license_notice( $args = null ) {

		?>

		<style type="text/css">
			
			.yz-addon-license-area input {
				margin-right: 8px;
			}
			
			.yz-addon-license-area .yz-activate-addon-key {
				background-color: #03A9F4;
				height: 27px;
				line-height: 27px;
				padding: 0 15px;
				color: #fff;
				border-radius: 2px;
				font-weight: 600;
				cursor: pointer;
				font-size: 13px;
				min-width: 80px;
				text-align: center;
			}

			.yz-addon-license-area input,
			.yz-addon-license-area .yz-activate-addon-key {
				display: inline-block;vertical-align: middle;
			}

			.yz-addon-license-msg {
				color: #616060;
				margin: 12px 0;
				font-size: 13px;
				background: #fff;
				font-weight: 600;
				border-radius: 2px;
				padding: 10px 25px;
				border-left: 5px solid #9E9E9E;
			}

			.yz-addon-error-msg {
				border-color: #F44336; 
			}

			.yz-addon-success-msg {
				border-color: #8BC34A; 
			}
			
		</style>

		<tr class="active">
			<td>&nbsp;</td>
			<td colspan="2">
				<div class="yz-addon-license-area">
					<div class="yz-addon-license-content">
						<?php _e( 'Please enter and activate your license key to enable automatic updates :', 'youzer' ); ?>
						<input type="text" class="yz-addon-license-key" name="<?php echo $args['field_name']; ?>"><div data-product-name="<?php echo $args['product_name']; ?>" data-nounce="<?php echo wp_create_nonce( 'yz_addon_license_notice' ); ?>" class="yz-activate-addon-key"><?php _e( 'Verify Key', 'youzer' ); ?></div>
					</div>
				</div>
		    </td>
		</tr>

		<?php

	}
}