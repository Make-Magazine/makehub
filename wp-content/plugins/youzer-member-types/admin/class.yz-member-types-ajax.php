<?php

class Youzer_Member_Types_Ajax {

	function __construct() {

		// Save Settings
		add_action( 'yz_panel_save_settings',  array( &$this, 'save_settings' ) );

	}

	/**
	 * # Add Settings Tab Content
	 */
	function save_settings( $data ) {

	    // Save Member Types.
	    if ( isset( $data['yz_member_types_form'] ) ) {
     		// Member Types Options
	    	$member_types = $data['yz_member_types'];
		    // Update Options
	    	$this->save_member_types( $member_types );
	    }

	}

	/**
	 * # Save Member Types.
	 */
	function save_member_types( $types ) {

		global $Youzer_Admin;
		
		if ( empty( $types ) ) {
			delete_option( 'yz_member_types' );
			return false;
		}
		
		// Update Types.
    	$update_options = update_option( 'yz_member_types', $types );

		// Update Next ID
    	if ( $update_options ) {
			update_option(
				'yz_next_member_type_nbr', $Youzer_Admin->ajax->get_next_ID( $types, 'member_type' )
			);
    	}

	}

}