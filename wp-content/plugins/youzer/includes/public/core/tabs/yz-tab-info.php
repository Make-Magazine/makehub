<?php

class YZ_Info_Tab {


    /**
     * # Tab.
     */
    function tab() {

        // Get User Profile Widgets
        $this->get_user_infos();

        do_action( 'youzer_after_infos_widgets' );
    }

    /**
     * # Get Custom Widgets functions.
     */
    function get_user_infos() {
        
        if ( ! bp_is_active( 'xprofile' ) ) {
            return false;
        }

        require_once YZ_PUBLIC_CORE . 'widgets/yz-widgets/class-yz-custom-infos.php';
        
        $custom_infos = new YZ_Custom_Infos();
        
        do_action( 'bp_before_profile_loop_content' );
        
        if ( bp_has_profile() ) : while ( bp_profile_groups() ) : bp_the_profile_group();
                
            if ( bp_profile_group_has_fields() ) :
                    
                $group_id = bp_get_the_profile_group_id();

                yz_widgets()->yz_widget_core( 'custom_infos', $custom_infos, array(
                    'icon'   => yz_get_xprofile_group_icon( $group_id ),
                    'name'  => bp_get_the_profile_group_name(),
                    'id'   => 'custom_infos',
                    'load_effect'   => 'fadeIn'
                ) );
				if(bp_get_the_profile_group_id() == 1) {
					if(bp_is_my_profile() == true) {
						$return = '<div class="yz-widget yz_effect yz-white-bg yz-wg-title-icon-bg fadeIn">
										 <div class="yz-widget-main-content">
											<a href="'.bp_loggedin_user_domain().'widgets" class="yz-widget-head">
											  <h2 class="yz-widget-title">
												  <i class="fas fa-id-card"></i>Add Profile Widgets
											  </h2>
											  <i class="far fa-edit yz-edit-widget"></i>
											</a>
										 </div>
									  </div>';
						echo $return;
					}
					// Get Overview Widgets and add them to the bottom of the info page
					$profile_widgets = yz_options( 'yz_profile_main_widgets' );
					// Filter 
					$profile_widgets = apply_filters( 'yz_profile_main_widgets', $profile_widgets );
					// Get Widget Content.
					yz_widgets()->get_widget_content( $profile_widgets );	
				 }

        endif; endwhile;
        
        endif;

        do_action( 'bp_after_profile_loop_content' );

    }

}