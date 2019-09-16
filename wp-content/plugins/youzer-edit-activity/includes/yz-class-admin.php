<?php

if ( ! class_exists( 'Youzer_Edit_Activity_Admin' ) ):
	
/**
 * Youzer Edit Activity Admin.
 */
class Youzer_Edit_Activity_Admin {
	
	public $field_name;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Set Licence Field Name
		$this->field_name = 'yz_edit_activity_license_key';

        add_filter( 'plugin_action_links_' . YZEA_BASENAME,  array( $this, 'plugin_action_links' ) );
        add_filter( 'network_admin_plugin_action_links_' . YZEA_BASENAME, array( $this, 'plugin_action_links' ) );

		// Add Plugin Updater.
		add_action( 'after_plugin_row_' . YZEA_BASENAME, array( &$this, 'activate_license_notice' ), 10, 3 );

	}

	/**
	 * Admin Instance.
	 */
	public static function instance(){

		static $instance = null;

		if ( null === $instance ) {
			$instance = new Youzer_Edit_Activity_Admin();
			$instance->setup();
			$instance->check_for_updates();
		}

		return $instance;
	}
	
	/**
	 * Setup Admin Class
	 */
	public function setup() {

		if ( ( ! is_admin() && ! is_network_admin() ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Add Settings Tab.
		add_filter( 'yz_panel_general_settings_menus', array( &$this, 'settings_menu' ) );

		// Add Settings Tab.
		add_filter( 'yzea_get_panel_activity_action', array( &$this, 'filter_activity_actions' ) );

	}
	
	/**
	 * # Add Settings Tab
	 */
	function settings_menu( $tabs ) {

		$tabs['edit-activity'] = array(
       	    'id'    => 'edit-activity',
       	    'icon'  => 'fas fa-edit',
       	    'function' => array( $this, 'settings' ),
       	    'title' => __( 'Edit Activity settings', 'youzer-edit-activity' ),
        );
        
        return $tabs;

	}

	/**
	 * Settings
	 */
	function settings() {

	    global $Yz_Settings;

	    $Yz_Settings->get_field(
	        array(
	            'title' => __( 'General Settings', 'youzer-edit-activity' ),
	            'type'  => 'openBox'
	        )
	    );

	    $Yz_Settings->get_field(
	        array(
	            'title'  => __( 'Comments & replies edition', 'youzer-edit-activity' ),
	            'desc'  => __( 'allow users to edit posts comments and replies.', 'youzer-edit-activity' ),
	            'id'    => 'yzea_edit_activity_comment',
	            'type'  => 'checkbox',

	        )
	    );

	    $Yz_Settings->get_field(
	        array(
	            'title'  => __( 'Attachments edition', 'youzer-edit-activity' ),
	            'desc'  => __( 'allow users to edit posts attachments.', 'youzer-edit-activity' ),
	            'id'    => 'yzea_attachments_edition',
	            'type'  => 'checkbox',

	        )
	    );

	    $Yz_Settings->get_field(
	        array(
	            'title'  => __( 'set editing timeout by minutes', 'youzer-edit-activity' ),
	            'desc'  => __( 'Leave it empty to set no time limit.', 'youzer-edit-activity' ),
	            'id'    => 'yzea_disable_edit_by_minutes',
	            'type'  => 'number',

	        )
	    );
	    
	    $Yz_Settings->get_field( array( 'type' => 'closeBox' ) );

	    $Yz_Settings->get_field(
	        array(
	            'title' => __( 'Groups Settings', 'youzer-edit-activity' ),
	            'type'  => 'openBox'
	        )
	    );

	    $Yz_Settings->get_field(
	        array(
	            'title'  => __( 'Enable posts edition', 'youzer-edit-activity' ),
	            'desc'  => __( 'allow users to edit groups posts.', 'youzer-edit-activity' ),
	            'id'    => 'yzea_groups_posts_edition',
	            'type'  => 'checkbox',

	        )
	    );


	    $Yz_Settings->get_field(
	        array(
	            'title'  => __( 'Enable comments edition', 'youzer-edit-activity' ),
	            'desc'  => __( 'allow users to edit groups comments.', 'youzer-edit-activity' ),
	            'id'    => 'yzea_groups_comments_edition',
	            'type'  => 'checkbox',

	        )
	    );

	    $Yz_Settings->get_field( array( 'type' => 'closeBox' ) );

	    $Yz_Settings->get_field(
	        array(
	            'title' => __( 'Editable Activities', 'youzer-edit-activity' ),
            	'class' => 'ukai-box-3cols',
	            'type'  => 'openBox'
	        )
	    );

	    $context_actions = array();

	    $activity_actions = bp_activity_get_actions();

	    $forbidden_actions = array( 'activity_comment', 'activity_update', 'friends_register_activity_action', 'updated_profile', 'group_details_updated' );

	    $post_types = array();

		foreach ( bp_activity_get_actions() as $component_actions ) {
			
			foreach ( $component_actions as $component_action ) {	
				if ( in_array( $component_action['key'], $forbidden_actions ) ) {
					continue;
				}

				// echo $component_action['key'];
				$post_types[ $component_action['key'] ] = $component_action;
			}
		}

		foreach ( $post_types as $post_type ) {

			$post_type = apply_filters( 'yzea_get_panel_activity_action', $post_type );
		    
		    $Yz_Settings->get_field(
		        array(
		            'title'  => ! empty( $post_type['label'] ) ? $post_type['label'] : $post_type['value'],
		            'desc'  => __( 'Enable activity type', 'youzer-edit-activity' ),
		            'id'    => 'yzea_edit_' . $post_type['key'],
		            'type'  => 'checkbox',
		        )
		    );
		}

	    $Yz_Settings->get_field( array( 'type' => 'closeBox' ) );

	    $Yz_Settings->get_field(
	        array(
	            'title' => __( 'Activity Editors Roles', 'youzer-edit-activity' ),
	            'type'  => 'openBox'
	        )
	    );

	    $Yz_Settings->get_field(
	        array(
	            'desc'  => __( 'Set which roles can edit their own activities.', 'youzer-edit-activity' ),
	            'id'    => 'yzea_edit_activity_edit_roles',
	            'type'  => 'checkbox',
	            'opts'  => yz_get_site_roles(),

	        )
	    );

	    $Yz_Settings->get_field( array( 'type' => 'closeBox' ) );

	    $Yz_Settings->get_field(
	        array(
	            'title' => __( 'Activity Moderators', 'youzer-edit-activity' ),
	            'type'  => 'openBox'
	        )
	    );

	    $Yz_Settings->get_field(
	        array(
	            'desc'  => __( 'Set which roles can edit other users activities.', 'youzer-edit-activity' ),
	            'id'    => 'yzea_edit_activity_moderators',
	            'type'  => 'checkbox',
	            'opts'  => yz_get_site_roles(),

	        )
	    );

	    $Yz_Settings->get_field( array( 'type' => 'closeBox' ) );

	}
	
	/**
	 * Filter Actions
	 */
	function filter_activity_actions( $args ) {

		switch ( $args['key'] ) {

			case 'bbp_reply_create':
				$args['label'] = __( 'Forum reply', 'youzer-edit-activity' );
				break;
			
			case 'bbp_topic_create':
				$args['label'] = __( 'Forum topic', 'youzer-edit-activity' );
				break;

			case 'friendship_accepted':
				$args['label'] = __( 'Friendship accepted', 'youzer-edit-activity' );
				break;

			case 'friendship_created':
				$args['label'] = __( 'Friendship created', 'youzer-edit-activity' );
				break;

			case 'new_avatar':
				$args['label'] = __( 'New Avatar', 'youzer-edit-activity' );
				break;

			case 'new_blog_post':
				$args['label'] = __( 'Blog post', 'youzer-edit-activity' );
				break;

			case 'new_blog_comment':
				$args['label'] = __( 'Blog comment', 'youzer-edit-activity' );
				break;
		}

		return $args;
	}

	/**
	 * Plugin Links.
	 */
	public function plugin_action_links( $links ) {

 		// Get Plugin Pages. 
        $panel_url = add_query_arg( array( 'page' => 'youzer-panel&tab=edit-activity' ), admin_url( 'admin.php' ) );
        $plugin_url = "https://www.kainelabs.com/downloads/buddypress-edit-activity/";
        $documentation_url = 'https://kainelabs.ticksy.com/article/14846/';

        // Add a few links to the existing links array.
        return array_merge( $links, array(
            'settings' => '<a href="' . $panel_url . '">' . esc_html__( 'Settings', 'youzer-edit-activity' ) . '</a>',
            'documentation' => '<a href="' . $documentation_url . '">' . esc_html__( 'Documentation', 'youzer-edit-activity' ) . '</a>',
            'about'    => '<a href="' . $plugin_url . '">' . esc_html__( 'About',    'youzer-edit-activity' ) . '</a>'
        ) );

	}

	/**
	 * Check For Updates.
	 **/
	function check_for_updates() {

		// Get Licence.
		$license = get_option( $this->field_name );

		if ( empty( $license ) ) {
			return;
		}


		// setup the updater
		$edd_updater = new EDD_SL_Plugin_Updater( EDD_KAINELABS_STORE_URL, YZEA_FILE,
			array(
				'version' => YZEA_VERSION,                   
				'license' => $license,
				'item_id' => '22081',
				'author'  => 'Youssef Kaine', 
				'beta'    => false,
			)
		);

	}

	/**
	 * Activate License Notice.
	 */
	function activate_license_notice( $plugin_file, $plugin_data, $status ) {

		if ( ! empty( get_option( $this->field_name ) ) ) {
			return;
		}
		
		global $Youzer_Admin;

		// Display Activation Notice.
		$Youzer_Admin->extension_validate_license_notice( array( 'field_name' => $this->field_name, 'product_name' => 'Buddypress Edit Activity' ) );

	}

}

endif;