<?php
/**
 * Is User Can Edit Attachments
 */
function yzea_is_user_can_edit_attachments() {
	
	$can = false;

	if ( yzea_is_moderator() || 'on' == yz_options( 'yzea_attachments_edition' ) ) {
		$can = true;
	}
	
	return apply_filters( 'yzea_is_user_can_edit_attachments', $can );
}

/**
 * Is user can edit activities.
 */
function yzea_is_user_can_edit_activities( $user_id = false ) {
	
	// Init Var.
	$can = false;

	// Get Roles.
	$roles = yz_get_multicheckbox_options( 'yzea_edit_activity_edit_roles' );
	
	if ( ! empty( $roles ) ) {

		// Get Activity Owner0
		$activity_owner = get_userdata( $user_id );

		// Checking Role Permission.
		foreach ( $activity_owner->roles as $role ) {
			if (  in_array( $role, $roles ) && $user_id == bp_loggedin_user_id())  {
				$can = true;					
			}
		}
	}

	return apply_filters( 'yzea_is_user_can_edit_activities', $can );
}

/**
 * Is user is moderator.
 */
function yzea_is_moderator() {
	
	// Init Var.
	$can = false;

	// Get Moderators Roles.
	$moderators = yz_get_multicheckbox_options( 'yzea_edit_activity_moderators' );

	if ( ! empty( $moderators ) ) {

		// Get Current User.
		$current_user = wp_get_current_user();
		
		// Check if user is moderator.
		foreach ( $current_user->roles as $role ) {
			if ( in_array( $role, $moderators ) ) {
				$can = true;				
			}
		}
	}

	return apply_filters( 'yzea_is_moderator', $can );
}


/**
 * Default Options.
 */
function yzea_default_options( $options ) {

    // Options.
    $new_options = array(
        'yzea_attachments_edition' => 'off',
        'yzea_disable_edit_by_minutes' => 0,
    	'yzea_edit_activity_comment' => 'off',
        'yzea_groups_posts_edition' => 'off',
        'yzea_groups_comments_edition' => 'off',
        'yzea_edit_activity_moderators' => array( 'administrator' => 'on' ),
        'yzea_edit_activity_edit_roles' => array( 'administrator' => 'on' ),
    );
    
    $options = array_merge( $options, $new_options );

    return $options;
}

add_filter( 'yz_default_options', 'yzea_default_options' );

/**
 * Check if User Can Edit Activity.
 */
function yzea_is_activity_editable( $activity = null ) {

	if ( ! is_user_logged_in() ) {
		return false;
	}

	// Get Activity.
	$activity = isset( $activity->current_comment ) ? $activity->current_comment : $activity;

	// Get Component
	if ( $activity->type == 'activity_comment' ) {
		$comment_activity = new BP_Activity_Activity( $activity->item_id );
		$component = $comment_activity->component;
	} else {
		$component = $activity->component;
	}


	$editable = false;

	if ( ! yzea_is_moderator() ) {

		// Check if current user can edit activities.
		$editable = yzea_is_user_can_edit_activities( $activity->user_id );

		if ( $editable === true ) {

			if ( $activity->type == 'activity_comment' ) {

				// Normal Comments Visibility.
				if ( 'on' == yz_options( 'yzea_edit_activity_comment' ) ) {
					$editable = true;
				} else {
					$editable = false;
				}
				
				// Groups Comments Visibility.
				if ( $component == 'groups' ) {
					if ( yz_options( 'yzea_groups_comments_edition' ) == 'off' ) {
						$editable = false;
					} else {
						$editable = true;
					}
				}

			} else {
				
				// Check if the current activity type is editable.
				if ( 'on' == yz_options( 'yzea_edit_' . $activity->type ) ) {
					$editable = true;
				} else {
					$editable = false;
				}

			}

			if ( $editable === true ) {

				if ( $component == 'groups' && $activity->type != 'activity_comment' &&  yz_options( 'yzea_groups_posts_edition' ) == 'off' ) {
					$editable = false;
				}

				/**
				 * Check if the current activity passed the timeout?
				 */
				if ( $editable == true ) {

					// Get Timeout
					$timeout = yz_options( 'yzea_disable_edit_by_minutes' );

					if ( $timeout > 0 ) {

						// Get Activity Comment Time.
						$activity_time = strtotime( $activity->date_recorded );

						// Get CUrrent Time.
						$current_time = time();
						
						// Calculate The Time Diffrence.
						$diff = (int) abs( $current_time - $activity_time );

						if ( floor( $diff / 60 ) >= $timeout ){
							//timeout must be in minutes!
							$editable = false;
						}
					}
				}

			}
		}
		
	} else {
		$editable = true;
	}
	
	return apply_filters( 'yzea_is_activity_editable', $editable, $activity );
}