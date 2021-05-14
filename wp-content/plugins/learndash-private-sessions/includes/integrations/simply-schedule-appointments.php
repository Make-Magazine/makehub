<?php

// $settings = new Ldms_Private_Sessions();

add_action( 'ldms_after_settings_init', 'ldms_ssa_settings_init' );
function ldms_ssa_settings_init() {
     global $Ldms_Private_Sessions;
     add_filter( 'ldms_private_sessions_fields', array( 'Ldms_Private_Sessions', 'ldms_simply_schedule_setting_fields' ), 10, 1 );
}

function ldms_simply_schedule_setting_fields( $fields ) {

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

     return $fields;

}

add_filter( 'ldms_session_tabs', 'ldms_ss_session_tabs' );
function ldms_ss_session_tabs( $tabs ) {

	if( !ldms_get_option('can_simply_schedule') ) { // && !current_user_can('manage_options') && !ldms_is_group_leader( $cuser->ID ) ) {
		return $tabs;
	}

     $tabs['appointments'] = array(
          'title'   =>   __( 'Schedule Appointment', 'ldmessenger' ),
          'content' =>   do_shortcode('[ssa_booking]')
     );

     return $tabs;

}
