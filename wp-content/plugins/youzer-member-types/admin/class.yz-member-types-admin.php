<?php

class Youzer_Member_Types_Admin {

	function __construct() {

		// Init Admin Area
		$this->admin_init();

		// Add Settings Tab.
		add_filter( 'yz_panel_general_settings_menus', array( &$this, 'settings_menu' ) );

		// Load Admin Scripts & Styles .
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_scripts' ) );

		// Sync Member Type => Field Value.
		add_action( 'current_screen', array( &$this, 'users_page_screen' ) );

	}

	/**
	 * # Add Settings Tab
	 */
	function settings_menu( $tabs ) {

		$tabs['member-types'] = array(
       	    'icon'  => 'fas fa-sitemap',
       	    'id'    => 'member-types',
       	    'function' => 'yz_member_types_settings',
       	    'title' => __( 'Member Types settings', 'youzer-member-types' ),
        );
        
        return $tabs;

	}

	/**
	 * # Add Settings Tab Content
	 */
	function save_settings( $data ) {

	    // Get form type.
	    $is_members_types_form = $data['yz_member_types_form'];
		
	    // Save Member Types.
	    if ( $is_members_types_form ) {
     		// Member Types Options
	    	$member_types = $data['yz_member_types'];
		    // Update Options
	    	$this->save_member_types( $member_types );
	    }

		return $content; 
	}

	/**
	 * # Initialize Admin Functions
	 */
	function admin_init() {

		// Init Admin Files.
        require_once YZMT_ADMIN . 'class.yz-member-types-ajax.php';
		require_once YZMT_ADMIN . 'class.yz-member-types-settings.php';
		
		// init Administration
		$this->ajax = new Youzer_Member_Types_Ajax();

	}

	/**
	 * # Admin Scripts.
	 */
	function admin_scripts() {
        if ( isset( $_GET['page'] ) && isset( $_GET['tab'] ) && 'member-types' == $_GET['tab'] ) {

            global $Youzer_Member_Types;

	        $data = array(
	            'update_member_type'    => __( 'update member type', 'youzer-member-types' ),
	            'no_member_types'       => __( 'No member types Found !', 'youzer-member-types' ),
	            'mtype_name_empty'      => __( 'member type name is empty!', 'youzer-member-types' ),
	            'mtype_slug_empty'      => __( 'member type slug is empty!', 'youzer-member-types' ),
	            'mtype_singular_empty'  => __( 'member type singular name is empty!', 'youzer-member-types' ),
	            'mtype_singular_lowercase'  => __( 'member type singular name should be all lowercase !', 'youzer-member-types' ),
	            'mtype_singular_spaces'  => __( 'member type singular name should not contain spaces !', 'youzer-member-types' ),
	        );

            // Load Member Types Script.
            wp_enqueue_script( 'yz-member-types', YZMT_AA . 'js/yz-member-types.min.js', array( 'jquery' ), $Youzer_Member_Types->version, true );
            
            wp_localize_script( 'yz-member-types', 'Youzer_MT', $data );

        }
	}

	/**
	 * Users Page Screen Actions.
	 **/
	function users_page_screen() {

		// Get Current Screen Page.
    	$current_screen = get_current_screen();

	    if ( $current_screen->id === 'users' ) {
			add_action( 'bp_set_member_type', array( &$this, 'link_member_type_to_field_value' ), 10, 2 );
	    }
	}

	
	/**
	 * Sync Member Type => Field Value.
	 */
	function link_member_type_to_field_value( $user_id, $member_type ) {

	    if ( ! is_admin() ) {
	        return;
	    }

	    // Get Filed ID.
	    $field_id = yz_get_xprofile_fields_by_type( 'member_types' );


	    if ( ! isset( $field_id[0] ) || empty( $field_id[0] ) ) {
	        return;
	    }

	    xprofile_set_field_data( $field_id[0], $user_id, $member_type );

	}
}