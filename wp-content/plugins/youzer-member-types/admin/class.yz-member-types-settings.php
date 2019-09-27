<?php

class Youzer_Member_Types_Settings {

	function __construct() {

	}

    /**
     * # Member Types Dropdown.
     */
    function member_types_drop_down() {

        // Get Var.
        $options = array();

        // Add default option
        $options[''] = __( '-- Select Default Type --', 'youzer-member-types' );

        // Get Member Types.
        $member_types = yz_get_registration_member_types();

        if ( empty( $member_types ) ) {
            return $options;
        }

        foreach ( $member_types as $type ) {

            // Get Type ID.
            $type_id = isset( $type['id'] ) ? $type['id'] : yz_get_member_type_id( $type['singular'] );

            // Get Option.
            $options[ $type_id ] = $type['name'];

        }

        return $options;

    }

    /**
     * # Get Roles.
     */
    function get_roles() {
        global $wp_roles;

        $all_roles = $wp_roles->roles;

        $editable_roles = apply_filters('editable_roles', $all_roles);

        return $editable_roles;
    }

    /**
     * # Custom Tabs Settings.
     */
    function settings() {

        global $Youzer_Admin, $Yz_Settings;

        $Yz_Settings->get_field(
            array(
                'title' => __( 'Registration Settings', 'youzer-member-types' ),
                'type'  => 'openBox'
            )
        );

        $modal_args = array(
            'button_id' => 'yz-add-member-type',
            'id'        => 'yz-member-types-form',
            'title'     => __( 'create new member type', 'youzer-member-types' )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'Enable Member Types While Registration', 'youzer-member-types' ),
                'desc'  => __( 'make member types selectable in the registration form', 'youzer-member-types' ),
                'id'    => 'yz_enable_member_types_registration',
                'type'  => 'checkbox'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'Set Default Members Type', 'youzer-member-types' ),
                'desc'  => __( 'works only if the the option above is disabled', 'youzer-member-types' ),
                'id'    => 'yz_default_member_type',
                'opts'  => $this->member_types_drop_down(),
                'type'  => 'select'
            ) 
        );

        $Yz_Settings->get_field( array( 'type' => 'closeBox' ) );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'general Settings', 'youzer-member-types' ),
                'type'  => 'openBox'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'Enable Member Types', 'youzer-member-types' ),
                'desc'  => __( 'activate member types', 'youzer-member-types' ),
                'id'    => 'yz_enable_member_types',
                'type'  => 'checkbox'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'Enable Member Types Modification', 'youzer-member-types' ),
                'desc'  => __( 'enable users to edit the member type after registration', 'youzer-member-types' ),
                'id'    => 'yz_enable_member_types_modification',
                'type'  => 'checkbox'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'Display Member Types in Infos Tab', 'youzer-member-types' ),
                'desc'  => __( 'show member types in the infos tab', 'youzer-member-types' ),
                'id'    => 'yz_enable_member_types_in_infos',
                'type'  => 'checkbox'
            )
        );
    
        $Yz_Settings->get_field(
            array(
                'title' => __( 'Allow members with no type', 'youzer-member-types' ),
                'desc'  => __( 'give the users the ability to stay with no member types', 'youzer-member-types' ),
                'id'    => 'yz_allow_no_member_type',
                'type'  => 'checkbox'
            )
        );

        $Yz_Settings->get_field( array( 'type' => 'closeBox' ) );

        // Get New Member Types Form.
        $Youzer_Admin->panel->modal( $modal_args, array( &$this, 'form' ) );

        // Get Member Types List.
        $this->get_types_list();

    }

    /**
     * # Create New Member Types Form.
     */
    function form() {

        // Get Data.
        global $Yz_Settings;

        $Yz_Settings->get_field(
            array(
                'type'  => 'openDiv',
                'class' => 'yz-member-types-form'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title'      => __( 'enable member type', 'youzer-member-types' ),
                'desc'       => __( 'enable this member type', 'youzer-member-types' ),
                'id'         => 'yz_member_type_active',
                'type'       => 'checkbox',
                'std'        => 'on',
                'no_options' => true
            )
        );

        $Yz_Settings->get_field(
            array(
                'title'      => __( 'display in registration form', 'youzer-member-types' ),
                'desc'       => __( 'make this type selectable in the registration form', 'youzer-member-types' ),
                'id'         => 'yz_member_type_register',
                'type'       => 'checkbox',
                'std'        => 'on',
                'no_options' => true
            )
        );

        $Yz_Settings->get_field(
            array(
                'title'        => __( 'member type icon', 'youzer-member-types' ),
                'desc'         => __( 'select member type icon', 'youzer-member-types' ),
                'id'           => 'yz_member_type_icon',
                'type'         => 'icon',
                'std'          => 'fas fa-user',
                'no_options'   => true
            )
        );

        $Yz_Settings->get_field(
            array(
                'title'        => __( 'member type ID', 'youzer-member-types' ),
                'desc'         => __( 'Should be in english letters and without spaces you can use only underscores to link words example: company_ceo', 'youzer-member-types' ),
                'id'           => 'yz_member_type_id',
                'type'         => 'text',
                'no_options'   => true
            )
        );

        $Yz_Settings->get_field(
            array(
                'title'        => __( 'member type name', 'youzer-member-types' ),
                'desc'         => __( 'example: students', 'youzer-member-types' ),
                'id'           => 'yz_member_type_name',
                'type'         => 'text',
                'no_options'   => true
            )
        );

        $Yz_Settings->get_field(
            array(
                'title'      => __( 'member type singular name', 'youzer-member-types' ),
                'desc'       => __( 'example: student', 'youzer-member-types' ),
                'id'         => 'yz_member_type_singular',
                'type'       => 'text',
                'no_options' => true
            )
        );

        $Yz_Settings->get_field(
            array(
                'title'      => __( 'member type directory slug', 'youzer-member-types' ),
                'desc'       => __( 'example: students', 'youzer-member-types' ),
                'id'         => 'yz_member_type_slug',
                'type'       => 'text',
                'no_options' => true
            )
        );

        $Yz_Settings->get_field(
            array(
                'title'      => __( 'display in members directory', 'youzer-member-types' ),
                'desc'       => __( 'show member type tab in members directory', 'youzer-member-types' ),
                'id'         => 'yz_member_type_show_in_md',
                'type'       => 'checkbox',
                'std'        => 'on',
                'no_options' => true
            )
        );

        // foreach ( get_editable_roles() as $id => $role ) {
        //     $checkbox_roles[ $id ] = $role['name']; 
        // }
        // // foreach ( get_editable_roles() as $id => $role ) {
        //       $Yz_Settings->get_field(
        //     array(
        //             'title' => __( 'associated roles', 'youzer-member-types' ),
        //             // 'desc'  => __( 'make member types selectable in the registration form', 'youzer-member-types' ),
        //             'id'    => 'yz_member_type_roles',
        //             'type'  => 'checkbox',
        //             'opts' => $checkbox_roles,
        //         'std'        => 'off',
        //         'no_options' => true
        //         )
        //     );

        // // }

        $Yz_Settings->get_field(
            array(
                'title'      => __( 'icon background left color', 'youzer-member-types' ),
                'desc'       => __( 'directory icon background left color', 'youzer-member-types' ),
                'id'         => 'yz_member_type_left_color',
                'type'       => 'color',
                'no_options' => true
            )
        );

        $Yz_Settings->get_field(
            array(
                'title'      => __( 'icon background right color', 'youzer-member-types' ),
                'desc'       => __( 'directory icon background right color', 'youzer-member-types' ),
                'id'         => 'yz_member_type_right_color',
                'type'       => 'color',
                'no_options' => true
            )
        );

        // Add Hidden Input
        $Yz_Settings->get_field(
            array(
                'type'       => 'hidden',
                'class'      => 'yz-keys-name',
                'std'        => 'yz_member_types',
                'id'         => 'yz_member_types_form',
                'no_options' => true
            )
        );

        $Yz_Settings->get_field( array( 'type' => 'closeDiv' ) );

    }

    /**
     * Get Member Types List
     */
    function get_types_list() {

        global $Yz_Settings;

        // Get Member Type Items
        $yz_member_types = yz_options( 'yz_member_types' );

        // Next Member Type ID
        $next_id = yz_options( 'yz_next_member_type_nbr' );
        $yz_nextMType = ! empty( $next_id ) ? $next_id : '1';

        ?>

        <script> var yz_nextMType = <?php echo $yz_nextMType; ?>; </script>

        <div class="yz-custom-section">
            <div class="yz-cs-head">
                <div class="yz-cs-buttons">
                    <button class="yz-md-trigger yz-member-types-button" data-modal="yz-member-types-form">
                        <i class="fas fa-user-plus"></i>
                        <?php _e( 'add new member type', 'youzer-member-types' ); ?>
                    </button>
                </div>
            </div>
        </div>

        <ul id="yz_member_types" class="yz-cs-content">

        <?php

            // Show No Types Found .
            if ( empty( $yz_member_types ) ) {
                global $Yz_Translation;
                $msg = $Yz_Translation['no_member_types'];
                echo "<p class='yz-no-content yz-no-member-types'>$msg</p></ul>";
                return false;
            }

            foreach ( $yz_member_types as $type => $data ) :

                // Get Widget Data.
                $icon = $data['icon'];
                $slug = $data['slug'];
                $title = $data['name'];
                $active = $data['active'];
                $singular = $data['singular'];
                $register = $data['register'];
                $show_in_md = $data['show_in_md'];
                $left_color = $data['left_color'];
                $right_color = $data['right_color'];
                $id = isset( $data['id'] ) ? $data['id'] : yz_get_member_type_id( $data['singular'] );

                // Get Field Name.
                $name = "yz_member_types[$type]";

                ?>

                <!-- Tab Item -->
                <li class="yz-member-type-item" data-member-type-name="<?php echo $type; ?>">
                    <h2 class="yz-member-type-name">
                        <i class="yz-member-type-icon <?php echo $icon; ?>"></i>
                        <span><?php echo $title; ?></span>
                    </h2>
                    <input type="hidden" name="<?php echo $name; ?>[id]" value="<?php echo $id; ?>">
                    <input type="hidden" name="<?php echo $name; ?>[icon]" value="<?php echo $icon; ?>">
                    <input type="hidden" name="<?php echo $name; ?>[name]" value="<?php echo $title; ?>">
                    <input type="hidden" name="<?php echo $name; ?>[singular]" value="<?php echo $singular; ?>">
                    <input type="hidden" name="<?php echo $name; ?>[active]" value="<?php echo $active; ?>">
                    <input type="hidden" name="<?php echo $name; ?>[right_color]" value="<?php echo $right_color; ?>">
                    <input type="hidden" name="<?php echo $name; ?>[left_color]" value="<?php echo $left_color; ?>">
                    <input type="hidden" name="<?php echo $name; ?>[show_in_md]" value="<?php echo $show_in_md; ?>">
                    <input type="hidden" name="<?php echo $name; ?>[slug]" value="<?php echo $slug; ?>">
                    <input type="hidden" name="<?php echo $name; ?>[register]" value="<?php echo $register; ?>">
                    <a class="yz-edit-item yz-edit-member-type"></a>
                    <a class="yz-delete-item yz-delete-member-type"></a>
                </li>

            <?php endforeach; ?>

        </ul>

        <?php
    }

}