<?php

/**
 * Adding Fields to group caretion step
 */
function bpla_group_add_fields() {
   
    if ( 'yes' != bp_bpla()->option('enable-for-groups') ) {
        return;
    }
    
    global $bp;
    
    if ( 'create' == bp_current_action() ) {
        
        if (isset($bp->groups->new_group_id)) {
            $group_id = $bp->groups->new_group_id;
        } else {
            $group_id = 0;
        }
        
        
    } else {
        $group_id = $bp->groups->current_group->id;
    }
    
    $street_address_req = bp_bpla()->option( 'group-street-address-required-field' );
    $city_address_req = bp_bpla()->option( 'group-city-required-field' );
    $state_address_req = bp_bpla()->option( 'group-state-required-field' );
    $zip_address_req = bp_bpla()->option( 'group-zip-required-field' );
    $country_address_req = bp_bpla()->option( 'group-country-required-field' );
    
    $required = (bp_bpla()->option('group-required-field') == 'yes' ) ? '(required)' : '';
    $required_field = ( $required ) ? 'required' : '';
    
    ?>
    <style type="text/css">
    	form .gm-err-autocomplete {
		    background-image: none!important;
		    border: 1px solid red!important;
		}
		body.bp-nouveau #save {
		    margin-top: 10px;
		}
    </style>
    <?php

    if ( 'single' == bp_bpla()->option( 'location-field-address-selection' ) ) { 
        $group_address = groups_get_groupmeta($group_id,'location-group-address',true);
        $location_group_address = ( bp_bpla()->option('location-group-address') ) ? bp_bpla()->option('location-group-address') : 'Address'; ?>
        <label for="bpla-group-address"><?php echo $location_group_address . ' ' . $required; ?></label>
        <input type="text" name="bpla-group-address" id="bpla-group-address" value="<?php echo $group_address; ?>" <?php echo $required_field; ?> /><?php
    } else { 
        
        if ( $street_address_req == 'yes' ) { 
            $group_street = groups_get_groupmeta($group_id,'location-group-street',true); 
            $location_group_street = ( bp_bpla()->option('location-group-street-address') ) ? bp_bpla()->option('location-group-street-address') : 'Street'; ?>
        
            <label for="bpla-group-street"><?php echo $location_group_street . ' ' . $required; ?></label>
            <input type="text" name="bpla-group-street" id="bpla-group-street" value="<?php echo $group_street; ?>" <?php echo $required_field; ?> />
        <?php } 
        
        if ( $city_address_req == 'yes' ) { 
            $group_city = groups_get_groupmeta($group_id,'location-group-city',true);
            $location_group_city = ( bp_bpla()->option('location-group-city') ) ? bp_bpla()->option('location-group-city') : 'City'; ?>
        
            <label for="bpla-group-city"><?php echo $location_group_city . ' ' . $required; ?></label>
            <input type="text" name="bpla-group-city" id="bpla-group-city" value="<?php echo $group_city; ?>" <?php echo $required_field; ?> />
        <?php } 
        
        if ( $state_address_req == 'yes' ) { 
            $group_state = groups_get_groupmeta($group_id,'location-group-state',true); 
            $location_group_state = ( bp_bpla()->option('location-group-state') ) ? bp_bpla()->option('location-group-state') : 'State/Province'; ?>
        
            <label for="bpla-group-state"><?php echo $location_group_state . ' ' . $required; ?></label>
            <input type="text" name="bpla-group-state" id="bpla-group-state" value="<?php echo $group_state; ?>" <?php echo $required_field; ?> />
        <?php } 
        
        if ( $zip_address_req == 'yes' ) { 
            $group_zip = groups_get_groupmeta($group_id,'location-group-zip',true);
            $location_group_zip = ( bp_bpla()->option('location-group-zip') ) ? bp_bpla()->option('location-group-zip') : 'ZIP/Postal Code'; ?>
        
            <label for="bpla-group-zip"><?php echo $location_group_zip . ' ' . $required; ?></label>
            <input type="text" name="bpla-group-zip" id="bpla-group-zip" value="<?php echo $group_zip; ?>" <?php echo $required_field; ?> />
        <?php } 
        
        if ( $country_address_req == 'yes' ) { 
            
            $group_country = groups_get_groupmeta($group_id,'location-group-country',true);
            $location_group_country = ( bp_bpla()->option('location-group-country') ) ? bp_bpla()->option('location-group-country') : 'Country'; ?>
        
            <label for="bpla-group-country"><?php echo $location_group_country . ' ' . $required; ?></label>
            <input type="text" name="bpla-group-country" id="bpla-group-country" value="<?php echo $group_country; ?>" <?php echo $required_field; ?> />
        <?php
    } }


}

$_bp_theme_package_id = bp_get_option( '_bp_theme_package_id' );
if ( 'nouveau' == $_bp_theme_package_id ) {
    // for bp nouveau template.
    add_action('bp_after_group_details_admin', 'bpla_group_add_fields');
    if ( 'create' == bp_current_action() ) {
	    add_action('groups_custom_group_fields_editable', 'bpla_group_add_fields');
    }
} else {
	add_action('groups_custom_group_fields_editable', 'bpla_group_add_fields');
}

/**
 * Saving Custom group fields
 * @global type $bp
 * @param type $groupid
 */

function bpla_save_extra_group_details($groupid) {
    
    if ( 'yes' != bp_bpla()->option('enable-for-groups') ) {
        return;
    }
    
    if ( empty( $groupid ) ) {
        global $bp;
        $groupid = $bp->groups->new_group_id;
    }
    
    if (isset($_POST['bpla-group-address'])) {
        groups_update_groupmeta($groupid, 'location-group-address', $_POST['bpla-group-address']);
    }
    if (isset($_POST['bpla-group-street'])) {
        groups_update_groupmeta($groupid, 'location-group-street', $_POST['bpla-group-street']);
    }
    if (isset($_POST['bpla-group-city'])) {
        groups_update_groupmeta($groupid, 'location-group-city', $_POST['bpla-group-city']);
    }
    if (isset($_POST['bpla-group-state'])) {
        groups_update_groupmeta($groupid, 'location-group-state', $_POST['bpla-group-state']);
    }
    if (isset($_POST['bpla-group-zip'])) {
        groups_update_groupmeta($groupid, 'location-group-zip', $_POST['bpla-group-zip']);
    }
    if (isset($_POST['bpla-group-country'])) {
        groups_update_groupmeta($groupid, 'location-group-country', $_POST['bpla-group-country']);
    }
}

add_action('groups_create_group_step_save_group-details', 'bpla_save_extra_group_details');
add_action('groups_details_updated','bpla_save_extra_group_details');

/**
 * Output address field in group info
 * @param string $desc
 * @param type $group
 * @return type mix
 */
function bplp_display_group_address_info( $desc , $group ) {
    
    if ( 'groups' != bp_current_component() ) {
        return apply_filters( 'bplp_display_group_address_info', $desc, $group );
    }
    
    if ( 'yes' != bp_bpla()->option('enable-for-groups') ) {
        return apply_filters( 'bplp_display_group_address_info', $desc, $group );
    }
    
    $group_id = $group->id;
    
    if ( 'single' == bp_bpla()->option( 'location-field-address-selection' ) ) {
        $address = groups_get_groupmeta($group_id,'location-group-address',true);
    } else {
        $street = (groups_get_groupmeta($group_id,'location-group-street',true) && bp_bpla()->option( 'group-street-address-required-field' ) ) ? groups_get_groupmeta($group_id,'location-group-street',true).', ' : '' ;
        $city = ( groups_get_groupmeta($group_id,'location-group-city',true) && bp_bpla()->option( 'group-city-required-field' ) ) ? groups_get_groupmeta($group_id,'location-group-city',true).', ' : '';
        $state = ( groups_get_groupmeta($group_id,'location-group-state',true) && bp_bpla()->option( 'group-state-required-field' ) ) ? groups_get_groupmeta($group_id,'location-group-state',true).', ' : '';
        $zip = ( groups_get_groupmeta($group_id,'location-group-zip',true) && bp_bpla()->option( 'group-zip-required-field' ) ) ? groups_get_groupmeta($group_id,'location-group-zip',true).', ' : '';
        $country = ( groups_get_groupmeta($group_id,'location-group-country',true) && bp_bpla()->option( 'group-country-required-field' ) ) ? groups_get_groupmeta($group_id,'location-group-country',true).' ' :'';
        
        $address = $street.$city.$state.$zip.$country;
        $address = rtrim($address);
        $address = rtrim($address,',');
        
    }
    
    if ( $address ) {
        $desc .= '<p><i class="fa fa-map-marker" aria-hidden="true"></i>'.' '.$address.'</p>';
    } else {
        return apply_filters( 'bplp_display_group_address_info', $desc, $group );
    }
    
    return apply_filters( 'bplp_display_group_address_info', $desc, $group );
    
}

add_filter('bp_get_group_description','bplp_display_group_address_info',99,2);
