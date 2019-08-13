<?php

class YZ_Info_Box {

    /**
     * Address Box Args
     */
    function address() {

        // Args
        $box_args = array(
            'box_icon'  => 'fas fa-home',
            'box_class' => 'address',
            'box_id'    => 'user_address',
            'option_id' => 'yz_address_info_box_field',
            'box_title' => __( 'Address', 'youzer' ),
        );

        return $box_args;
    }

    /**
     * Address Args
     */
    function address_args() {

        // Arguments
        $args = array(
            'display_title'     => false,
            'widget_title'      => 'address',
            'widget_name'       => 'address',
            'main_data'         => 'user_address',
            'function_options'  => $this->address(),
            'load_effect'       => yz_options( 'yz_address_load_effect' )
        );

        // Filter
        $args = apply_filters( 'yz_address_box_widget_args', $args );

        return $args;

    }

    /**
     * Website Box Args
     */
    function website() {

        // Args.
        $box_args = array(
            'box_icon'  => 'fas fa-link',
            'box_class' => 'website',
            'box_id'    => 'user_url',
            'option_id' => 'yz_website_info_box_field',
            'box_title' => __( 'Website', 'youzer' )
        );

        return $box_args;
    }

    /**
     * Website Args
     */
    function website_args() {

        // Arguments
        $args = array(
            'display_title'     => false,
            'widget_title'      => 'website',
            'widget_name'       => 'website',
            'main_data'         => 'user_url',
            'function_options'  => $this->website(),
            'load_effect'       => yz_options( 'yz_website_load_effect' )
        );

        // Filter
        $args = apply_filters( 'yz_website_box_widget_args', $args );

        return $args;
    }

    /**
     * E-mail Box Args
     */
    function email() {

        // Args
        $box_args = array(
            'box_icon'  => 'far fa-envelope',
            'box_class' => 'email',
            'box_id'    => 'email_address',
            'option_id' => 'yz_email_info_box_field',
            'box_title' => __( 'E-mail Address', 'youzer' )
        );

        return $box_args;
    }

    /**
     * E-mail Args
     */
    function email_args() {

        //  Arguments
        $args = array(
            'display_title'     => false,
            'widget_title'      => 'email',
            'widget_name'       => 'email',
            'function_options'  => $this->email(),
            'main_data'         => 'email_address',
            'load_effect'       => yz_options( 'yz_email_load_effect' )
        );

        // Filter
        $args = apply_filters( 'yz_email_box_widget_args', $args );

        return $args;
    }

    /**
     * Phone Box Args
     */
    function phone() {

        // Get Box Args
        $box_args = array(
            'box_class' => 'phone',
            'box_id'    => 'phone_nbr',
            'box_icon'  => 'fas fa-phone-volume',
            'option_id' => 'yz_phone_info_box_field',
            'box_title' => __( 'Phone Number', 'youzer' )
        );
    
        return $box_args;

    }

    /**
     * Phone Args
     */
    function phone_args() {

        // Arguments
        $args =  array(
            'display_title'     => false,
            'widget_title'      => 'phone',
            'widget_name'       => 'phone',
            'main_data'         => 'phone_nbr',
            'function_options'  => $this->phone(),
            'load_effect'       => yz_options( 'yz_phone_load_effect' )
        );

        // Filter
        $args = apply_filters( 'yz_phone_box_widget_args', $args );

        return $args;
    }


    /**
     * # Content.
     */
    function widget( $args ) {

        // Get Field  Id.
        $field_id = yz_options( $args['option_id'] );

        if ( ! empty( $field_id ) ) {
        
            // Get Field Data.
            $field = new BP_XProfile_Field( $field_id );
            
            // Get Field Value.
            $value = bp_get_profile_field_data( array( 'user_id' => bp_displayed_user_id(), 'field' => $field_id ) );

            // Get Field Title.
            $title = $field->name;

        } else {

            // Get Field Title.
            $title = $args['box_title'];

            // Get Field Value.
            $value = yz_get_xprofile_field_value( $args['box_id'] );

        }

        // Hide Box if there's no content.
        if ( empty( $value ) ) {
            return false;
        }
        
        // Check User Website Url.
        if ( 'user_url' == $args['box_id'] ) {
            $value = '<a href="' . esc_url( $value ). '">' . yz_esc_url( $value ) . '</a>';
        }

		?>

		<div class="yz-infobox-content <?php echo "yz-box-" . $args['box_class']; ?>">
			<div class="yz-box-head">
				<div class="yz-box-icon">
					<i class="<?php echo $args['box_icon']; ?>"></i>
				</div>
				<h2 class="yz-box-title"><?php echo $title; ?></h2>
			</div>
			<div class="yz-box-content">
				<p><?php echo $value; ?></p>
			</div>
		</div>

		<?php

	}

	/**
     * # Admin Settings.
     */
    function admin_settings() {

        global $Yz_Settings;


        $Yz_Settings->get_field(
            array(
                'title' => __( 'Email settings', 'youzer' ),
                'class' => 'ukai-box-2cols',
                'type'  => 'openBox'
            )
        );

        $Yz_Settings->get_field(
            array(

                'title' => __( 'E-mail field', 'youzer' ),
                'desc'  => __( 'select the email box field', 'youzer' ),
                'opts'  => yz_get_panel_profile_fields(),
                'id'    => 'yz_email_info_box_field',
                'type'  => 'select'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'email loading effect', 'youzer' ),
                'opts'  => $Yz_Settings->get_field_options( 'loading_effects' ),
                'desc'  => __( 'email loading effect', 'youzer' ),
                'id'    => 'yz_email_load_effect',
                'type'  => 'select'
            )
        );
        $Yz_Settings->get_field(
            array(
                'title' => __( 'background left', 'youzer' ),
                'desc'  => __( 'gradient background color', 'youzer' ),
                'id'    => 'yz_ibox_email_bg_left',
                'type'  => 'color'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'background right', 'youzer' ),
                'desc'  => __( 'gradient background color', 'youzer' ),
                'id'    => 'yz_ibox_email_bg_right',
                'type'  => 'color'
            )
        );

        $Yz_Settings->get_field( array( 'type' => 'closeBox' ) );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'address Styling settings', 'youzer' ),
                'class' => 'ukai-box-2cols',
                'type'  => 'openBox'
            )
        );

        $Yz_Settings->get_field(
            array(

                'title' => __( 'Address field', 'youzer' ),
                'desc'  => __( 'select the address box field', 'youzer' ),
                'opts'  => yz_get_panel_profile_fields(),
                'id'    => 'yz_address_info_box_field',
                'type'  => 'select'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'address loading effect', 'youzer' ),
                'opts'  => $Yz_Settings->get_field_options( 'loading_effects' ),
                'desc'  => __( 'address loading effect', 'youzer' ),
                'id'    => 'yz_address_load_effect',
                'type'  => 'select'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'background left', 'youzer' ),
                'desc'  => __( 'gradient background color', 'youzer' ),
                'id'    => 'yz_ibox_address_bg_left',
                'type'  => 'color'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'background right', 'youzer' ),
                'desc'  => __( 'gradient background color', 'youzer' ),
                'id'    => 'yz_ibox_address_bg_right',
                'type'  => 'color'
            )
        );

        $Yz_Settings->get_field( array( 'type' => 'closeBox' ) );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'website Styling settings', 'youzer' ),
                'class' => 'ukai-box-2cols',
                'type'  => 'openBox'
            )
        );

        $Yz_Settings->get_field(
            array(

                'title' => __( 'Website field', 'youzer' ),
                'desc'  => __( 'select the website box field', 'youzer' ),
                'opts'  => yz_get_panel_profile_fields(),
                'id'    => 'yz_website_info_box_field',
                'type'  => 'select'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'website loading effect', 'youzer' ),
                'desc'  => __( 'website loading effect?', 'youzer' ),
                'opts'  => $Yz_Settings->get_field_options( 'loading_effects' ),
                'id'    => 'yz_website_load_effect',
                'type'  => 'select'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'background left', 'youzer' ),
                'desc'  => __( 'gradient background color', 'youzer' ),
                'id'    => 'yz_ibox_website_bg_left',
                'type'  => 'color'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'background right', 'youzer' ),
                'desc'  => __( 'gradient background color', 'youzer' ),
                'id'    => 'yz_ibox_website_bg_right',
                'type'  => 'color'
            )
        );

        $Yz_Settings->get_field( array( 'type' => 'closeBox' ) );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'phone number Styling settings', 'youzer' ),
                'class' => 'ukai-box-2cols',
                'type'  => 'openBox'
            )
        );

        $Yz_Settings->get_field(
            array(

                'title' => __( 'phone field', 'youzer' ),
                'desc'  => __( 'select the phone box field', 'youzer' ),
                'opts'  => yz_get_panel_profile_fields(),
                'id'    => 'yz_phone_info_box_field',
                'type'  => 'select'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'phone loading effect', 'youzer' ),
                'desc'  => __( 'phone number loading effect?', 'youzer' ),
                'opts'  => $Yz_Settings->get_field_options( 'loading_effects' ),
                'id'    => 'yz_phone_load_effect',
                'type'  => 'select'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'background left', 'youzer' ),
                'desc'  => __( 'gradient background color', 'youzer' ),
                'id'    => 'yz_ibox_phone_bg_left',
                'type'  => 'color'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'background right', 'youzer' ),
                'desc'  => __( 'gradient background color', 'youzer' ),
                'id'    => 'yz_ibox_phone_bg_right',
                'type'  => 'color'
            )
        );

        $Yz_Settings->get_field( array( 'type' => 'closeBox' ) );
    }

}