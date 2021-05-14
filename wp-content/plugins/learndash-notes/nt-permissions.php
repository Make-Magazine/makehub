<?php
add_action( 'admin_init', 'nt_create_user_permissions');
function nt_create_user_permissions() {

    $permissions = apply_filters( 'nt_create_user_permissions', array(
        'read_others_nt_notes',
        'edit_others_nt_notes',
        'delete_others_nt_notes',
    ) );

    $admins = apply_filters( 'nt_admin_roles', array(
        get_role('editor'),
        get_role('administrator'),
    ) );

    foreach( $admins as $admin ) {

        if( !$admin ) {
            continue;
        }

        foreach( $permissions as $permission ) {
            $admin->add_cap($permission);
        }

    }

    $group_leader = get_role('group_leader');

    if( $group_leader ) {
        $group_leader->add_cap('read_others_nt_notes');
    }

}

/**
 * Get group ids that the user is an administrator of
 * learndash_get_administrators_group_ids($user_id);
 *
 * Get a users group id's
 * learndash_get_users_group_ids($user_id);
 */
function nt_is_current_user_group_leader_of_user_id( $user_id ) {

    if( !function_exists('learndash_get_administrators_group_ids') ) {
        return false;
    }

    $cuser          = wp_get_current_user();
    $cuser_groups   = learndash_get_administrators_group_ids($cuser->ID);
    $user_groups    = learndash_get_users_group_ids($user_id);

    if( array_intersect( $cuser_groups, $user_groups ) ) return true;

    return false;

}
