<?php

add_filter( 'gform_notification_events', 'gv_revisions_notification_events', 10, 2 );

/**
 * This is controlled via the `gravityview/entry-revisions/send-notifications`
 * filter inside `GV_Entry_Revisions::add_revision`, which defaults to true.
 *
 * @since 1.0
 *
 * @param array $form
 * @param string $entry_id
 * @param array $original_entry
 */
function gv_revisions_send_notifications( $form = array(), $entry_id = '', $original_entry = array() ) {
	remove_action( 'gform_after_update_entry', __FUNCTION__, 10, 3 ); // Run once

	$entry = GFAPI::get_entry( $entry_id );

	if ( ! $entry || is_wp_error( $entry ) ) {
		gv_revisions_log_debug( __METHOD__ . ': Entry not found at ID #' . $entry_id );
		return;
	}

	GFAPI::send_notifications( $form, $entry, 'gravityview/entry-revisions/gform_after_update_entry' );
}

/**
 * Allow custom notification events to be added.
 *
 * @since 1.0
 *
 * @param array $notification_events The notification events.
 * @param array $form The current form.
 */
function gv_revisions_notification_events( $notification_events = array(), $form = array() ) {

	$notification_events['gravityview/entry-revisions/gform_after_update_entry'] = esc_html_x( 'Entry is updated, revision is saved', 'The title for an event in a notifications drop down list.', 'gravityview-entry-revisions' );

	return $notification_events;
}