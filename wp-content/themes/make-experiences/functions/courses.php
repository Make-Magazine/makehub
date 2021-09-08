<?php

//Automatically add certain new memberships to certain groups

function automatic_group_memberships( $user_login, $user ) {
    if( !$user ) return false;
    
    //error_log(print_r($user, TRUE));
    $user_id = $user->ID;
    //error_log(print_r(get_user_meta($user_id), TRUE));
    //$group_id = BP_Groups_Group::get_id_from_slug('school-maker-faire-producers');
    $user_levels = get_user_meta($user_id)['ihc_user_levels'][0];
    error_log(print_r($user_levels, TRUE));
    error_log("it is happening for " . $user_id);
    if(in_array('18', explode(',', $user_levels))) {
        error_log("it works");
        groups_join_group( 152, $user_id );
    }
    
}

add_action( 'wp_login', 'automatic_group_memberships', 10, 2 );