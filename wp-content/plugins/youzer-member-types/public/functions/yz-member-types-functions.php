<?php

/**
 * Get Member Types Settings
 */
function yz_member_types_settings() {
    // Init Settings.
    $member_types_settings = new Youzer_Member_Types_Settings();
    // Call Settings.
    $member_types_settings->settings();
}

/**
 * Get Member Types
 */
function yz_get_member_types() {

    // Get All Member Types Data
    $member_types = yz_options( 'yz_member_types' );

    // Filter
    $member_types = apply_filters( 'yz_member_types', $member_types );

    return $member_types;
}

/**
 * Get Member Types Singulars
 */
function yz_get_member_types_ids() {

    // Get All Member Types Data
    $member_types = yz_get_member_types();

    if ( empty( $member_types ) ) {
        return false;
    }
    
    // Get Types Slugs
    $singulars = wp_list_pluck( $member_types, 'id' );

    foreach ( $singulars as $key => $singular ) {
        $singulars[ $key ] = strtolower( $singular );
    }

    // Filter
    $singulars = apply_filters( 'yz_member_types_ids', $singulars );

    return $singulars;
}

/**
 * Get Member Types List
 */
function yz_get_member_types_slugs() {

    // Get All Member Types Data
    $member_types = yz_get_member_types();

    if ( empty( $member_types ) ) {
        return false;
    }
    
    // Get Types Slugs
    $slugs = wp_list_pluck( $member_types, 'slug' );

    // Filter
    $slug = apply_filters( 'yz_member_types_slugs', $slugs );

    return $slugs;
}

/**
 * Get Registration Member Types List
 */
function yz_get_registration_member_types() {

    // Init Registration form.
    $registration_types = array();

    // Get All Member Types Data
    $member_types = yz_get_member_types();

    if ( empty( $member_types ) ) {
        return $registration_types;
    }

    foreach ( $member_types as $member_type ) {
        if ( $member_type['register'] == 'true' ) {
            $registration_types[] = $member_type;
        }
    }

    // Filter
    $registration_types = apply_filters( 'yz_registration_member_types', $registration_types );

    return $registration_types;
}

/**
 * Register Member Types.
 */
function yz_register_member_types() {

    if ( ! yz_is_member_types_active() ) {
        return false;
    }

    // Get All Member Types
    $member_types = yz_get_member_types();

    if ( empty( $member_types ) ) {
        return false;
    }

    // Register Types.
    foreach ( $member_types as $type ) {

        if ( isset( $type['name'] ) && empty( $type['name'] )  ) {
            continue;
        }

        // Get Type Slug
        $type_slug = isset( $type['slug'] ) && ! empty( $type['slug'] ) ? $type['slug'] : false;

        // Make the id lowercase.
        $type_id = isset( $type['id'] ) ? $type['id'] : yz_get_member_type_id( $type['singular'] );

        // Register Type
        bp_register_member_type( $type_id, array(
            'labels' => array(
                'name' => $type['name'],
                'singular_name' => $type['singular'],
            ),
            'has_directory' => $type_slug
            )
        );

    }
}

add_action( 'bp_register_member_types', 'yz_register_member_types' );

/**
 * Add "Member Types" Field
 */
function yz_add_member_types_field( $fields ) {

    if ( ! yz_is_member_types_active() ) {
        return $fields;
    }

    // Get New Fields.
    $new_fields = array(
        'member_types' => 'YZ_XProfile_Field_Type_MemberType',
    );

    // Get Fields.
    $fields = array_merge( $fields, $new_fields );

    return $fields;
}

add_filter( 'bp_xprofile_get_field_types', 'yz_add_member_types_field' );

/**
 * Update the member type of a user when member type field is updated.
 */
function yz_update_member_type( $data_field ) {
    // Avoid Conflicting with Users Page Bulk Action.
    if ( is_admin() ) {

        global $pagenow;

        if ( $pagenow == 'users.php' && ! isset( $_GET['page'] ) ) {
            return;
        }

    }

    // Get Field.
    $field = xprofile_get_field( $data_field->field_id ) ;

    // Check if field type is member types. 
    if ( 'member_types' != $field->type ) {
        return ;
    }

    // Get User ID.
    $user_id = $data_field->user_id;

    // Get Member Type.
    $member_type = maybe_unserialize( $data_field->value );
    
    if ( empty( $member_type ) ) {
        // Remove Member Type.
        bp_set_member_type( $user_id, '' );
        return ;
    }

    // Set Member Type.
    if ( bp_get_member_type_object( $member_type ) ) {
        bp_set_member_type( $user_id, $member_type );
    }

}

add_action( 'xprofile_data_after_save', 'yz_update_member_type' );

/**
 * Check if is Member Types Active.
 */
function yz_is_member_types_active() {

    // Get Value.
    $member_types = yz_options( 'yz_enable_member_types' );

    if ( 'on' == $member_types ) {
        $activate = true;
    } else {
        $activate = false;
    }

    return apply_filters( 'yz_is_member_types_active', $activate );

}


/**
 * Hide Member Types Field
 */
function yz_hide_xprofile_member_type_field( $retval ) {
    
    if ( ! bp_is_active( 'xprofile' ) ) {
        return false;
    }

    // Get "Member Types" Field.
    $member_types_fields = yz_get_xprofile_fields_by_type( 'member_types' );

    if ( empty( $member_types_fields ) ) {
        return $retval;
    }

    // Get Member types
    $member_types = yz_get_member_types();

    // Cover Ids into string.
    $member_types_fields = implode( ',', $member_types_fields );

    if ( ! yz_is_member_types_active() || empty( $member_types ) ) {
        $retval['exclude_fields'] = $member_types_fields;
    }

    // Make Admins Able to edit Member types fields.
    if ( is_super_admin() ) {
        return $retval;
    }     

    // Are users able to edit member types ?
    $edit_member_type = yz_options( 'yz_enable_member_types_modification' );

    // Remove field from edit tab
    if ( bp_is_user_profile_edit() && 'off' == $edit_member_type ) {       
        $retval['exclude_fields'] = $member_types_fields;
    }

    // Are users able to select member types while the registration ?
    $select_member_type = yz_options( 'yz_enable_member_types_registration' );

    // Allow field on register page
    if ( bp_is_register_page() && 'off' == $select_member_type ) {
        $retval['exclude_fields'] = $member_types_fields;
    }       

    // Display member type in the infos tab ?
    $show_in_infos = yz_options( 'yz_enable_member_types_in_infos' );
    
    // Get User Data Field .
    $user_data = bp_get_profile_field_data( 'field=' . $member_types_fields );

    // Hide the field on profile view tab
    if ( ! bp_is_register_page() && ! bp_is_user_profile_edit() && ( empty( $user_data ) || 'off' == $show_in_infos ) ) {
        $retval['exclude_fields'] = $member_types_fields;
    }
    
    return $retval; 
}

add_filter( 'bp_after_has_profile_parse_args', 'yz_hide_xprofile_member_type_field', 10, 1 );

/**
 * Set User Default 
 */
function yz_set_user_default_member_type( $user_id ) {

    // Is Member Type selectable.
    $select_member_type = yz_options( 'yz_enable_member_types_registration' );

    if ( ! yz_is_member_types_active() || 'on' == $select_member_type ) {
        return false;
    }

    // Get Default Member Type
    $default_type = yz_options( 'yz_default_member_type' );

    if ( empty( $default_type ) ) {
        return false;
    }

    // Set Default Member Type
    bp_set_member_type( $user_id, $default_type );

}

add_action( 'bp_core_activated_user', 'yz_set_user_default_member_type' );
// add_action( 'bp_core_signup_user', 'yz_set_user_default_member_type' );

/**
 * Member Directory Filter.
 */
function yz_add_md_types_tabs() {

    // Get Member Types
    $member_types = yz_get_member_types();

    if ( ! yz_is_member_types_active() || empty( $member_types ) ) {
        return false;
    }

    foreach ( $member_types as $type ) {

            if ( 'false' == $type['show_in_md'] ) {
                continue;
            }

            // Make the id lowercase.
            $type_id = isset( $type['id'] ) ? $type['id'] : yz_get_member_type_id( $type['singular'] );

            // Get Type
            $type_infos = bp_get_term_by( 'slug', $type_id, bp_get_member_type_tax_name() );

            if ( ! isset ( $type_infos->count ) || $type_infos->count < 1 ) {
                continue;
            }

            
        ?>

        <li id="members-<?php echo $type['id']; ?>" class="yzmt-directory-tab"><a href="<?php echo bp_member_type_directory_permalink( $type_id ); ?>"><i class="<?php echo $type['icon']; ?>"></i><?php printf( __( '%1s %2s', 'youzer-member-types' ), $type['name'], '<span>' . $type_infos->count . '</span>' ); ?></a></li>

        <?php
    }

}

add_action( 'bp_members_directory_member_types', 'yz_add_md_types_tabs' );

/**
 * Members Directory - Max Members Per Page.
 */
function yz_get_md_members_type_list( $loop ) {
    if ( ! yz_is_member_types_active() ) {
        return $loop;
    }

    // Get Types Singulars
    $singulars = yz_get_member_types_ids();

    if ( empty( $singulars ) ) {
        return $loop;
    }

    if ( bp_is_members_directory() && isset( $_POST['scope'] ) && in_array( $_POST['scope'], $singulars ) ) {
        $loop['member_type'] = $_POST['scope'];
    }

    return $loop;
}

add_filter( 'bp_after_has_members_parse_args', 'yz_get_md_members_type_list', 1 );

/**
 * Directory Icons Gradient Background
 */
function yz_md_member_types_icons_styling() {
    
    if ( ! yz_is_member_types_active() || ! bp_is_members_directory() ) {
        return false;
    }

    // Get Member Types
    $member_types = yz_get_member_types();

    if ( empty( $member_types ) ) {
        return false;
    }

    wp_enqueue_style(
        'youzer-customStyle',
        YZ_AA . 'css/custom-script.css'
    );
    
    // Init var.
    $custom_css = null;

    // Pattern Path
    $pattern = 'url(' . YZ_PA . 'images/dotted-bg.png)';

    // Add Lists Icons Styles Class
    $icons_style = yz_options( 'yz_tabs_list_icons_style' );

    foreach ( $member_types as $type ) {

        // Get Options Data
        $left_color  = $type['left_color'];
        $right_color = $type['right_color'];

        // if the one of the values are empty go out.
        if ( empty( $left_color ) || empty( $right_color ) ) {
            continue;
        }

        // Get Selector.
        if ( $icons_style == 'yz-tabs-list-gradient' ) {
            $selector  = '.yz-tabs-list-gradient #members-' . $type['id'] . ' a i';   
            $custom_css = "
                $selector {
                    background: $pattern,linear-gradient(to right, $left_color , $right_color ) !important;
                    background: $pattern,-webkit-linear-gradient(left, $left_color , $right_color ) !important;
                }
            ";
        } elseif ( $icons_style == 'yz-tabs-list-colorful' ) {
            $selector  = '.yz-tabs-list-colorful #members-' . $type['id'] . ' a i'; 
            $custom_css = "
                $selector {
                    background: $left_color;
                }
            ";  
        }


        wp_add_inline_style( 'youzer-customStyle', $custom_css );
    }

}

add_action( 'wp_enqueue_scripts', 'yz_md_member_types_icons_styling' );

/**
 * Get Member Type Id
 */
function yz_get_member_type_id( $singular ) {

    $id = strtolower( $singular );
    $id = str_replace( array( ' ', '-' ), '_' , $id );
    $id = preg_replace( '/[^A-Za-z0-9\_]/', '', $id );
    $id = preg_replace( '/_+/', '_', $id );
    return apply_filters( 'yz_get_member_type_id', $id, $singular );
}

/**
 * Replace Member Type Data Value
 */
function yzmt_replace_member_type_data_value( $values, $field_id, $user_id ) {

    $field_type = BP_XProfile_Field::get_type( $field_id );

    if ( $field_type == 'member_types' ) {
        return yzmt_get_user_member_type_singular( $values );
    }

    return $values;
}

add_filter( 'xprofile_get_field_data', 'yzmt_replace_member_type_data_value', 10, 3 );

/**
 * Replace Member Type Field Value
 */
function yzmt_replace_member_type_field_value( $values, $field_type, $user_id ) {

    if ( $field_type == 'member_types' ) {
        return yzmt_get_user_member_type_singular( $values );
    }

    return $values;
}

add_filter( 'bp_get_the_profile_field_value', 'yzmt_replace_member_type_field_value', 10, 3 );

/**
 * Get User Member Type By ID.
 */
function yzmt_get_user_member_type_singular( $member_type_id = null ) {
    // Get Member Type.
    $member_type = bp_get_member_type_object( $member_type_id );
    // Get Member Singular Value.
    $singular = isset( $member_type->labels['singular_name'] ) ? $member_type->labels['singular_name'] : '';
    return apply_filters( 'yzmt_get_user_member_type_singular', $singular, $member_type_id  );
}
