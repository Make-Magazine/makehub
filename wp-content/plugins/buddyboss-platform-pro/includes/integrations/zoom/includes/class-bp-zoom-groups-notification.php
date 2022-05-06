<?php
/**
 * BuddyBoss Groups Notification Class.
 *
 * @package BuddyBoss/Groups
 *
 * @since 1.2.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Set up the BP_Groups_Notification class.
 *
 * @since 1.2.1
 */
class BP_Zoom_Groups_Notification extends BP_Core_Notification_Abstract {

	/**
	 * Instance of this class.
	 *
	 * @since 1.2.1
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * Get the instance of this class.
	 *
	 * @since 1.2.1
	 *
	 * @return null|BP_Zoom_Groups_Notification|Controller|object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since 1.2.1
	 */
	public function __construct() {
		// Initialize.
		$this->start();
	}

	/**
	 * Initialize all methods inside it.
	 *
	 * @since 1.2.1
	 *
	 * @return mixed|void
	 */
	public function load() {
		if ( ! bbp_pro_is_license_valid() || ! bp_is_active( 'groups' ) || ! bp_zoom_is_zoom_enabled() || ! bp_zoom_is_zoom_groups_enabled() ) {
			return;
		}

		$this->register_notification_group(
			'groups',
			esc_html__( 'Social Groups', 'buddyboss-pro' ),
			esc_html__( 'Social Groups', 'buddyboss-pro' ),
			6
		);

		// Group zoom meeting schedule.
		$this->register_notification_for_group_zoom_schedule();
	}

	/**
	 * Register notification for meeting schedule.
	 *
	 * @since 1.2.1
	 */
	public function register_notification_for_group_zoom_schedule() {
		$this->register_notification_type(
			'bb_groups_new_zoom',
			esc_html__( 'New meeting or webinar is scheduled in one of your groups', 'buddyboss-pro' ),
			esc_html__( 'A Zoom meeting or webinar is scheduled in a group', 'buddyboss-pro' ),
			'groups'
		);

		$this->register_email_type(
			'zoom-scheduled-meeting-email',
			array(
				/* translators: do not remove {} brackets or translate its contents. */
				'email_title'         => __( '[{{{site.name}}}] {{poster.name}} scheduled a Zoom Meeting in the group: "{{group.name}}"', 'buddyboss-pro' ),
				/* translators: do not remove {} brackets or translate its contents. */
				'email_content'       => __( "<a href=\"{{{poster.url}}}\">{{poster.name}}</a> scheduled a Zoom Meeting in the group \"<a href=\"{{{group.url}}}\">{{group.name}}</a>\":\n\n{{{zoom_meeting}}}", 'buddyboss-pro' ),
				/* translators: do not remove {} brackets or translate its contents. */
				'email_plain_content' => __( "{{poster.name}} scheduled a Zoom Meeting in the group \"{{group.name}}\":\n\n{{{zoom_meeting}}}", 'buddyboss-pro' ),
				'situation_label'     => __( 'A Zoom meeting is scheduled in a group', 'buddyboss-pro' ),
				'unsubscribe_text'    => __( 'You will no longer receive emails when someone schedules a meeting in a group.', 'buddyboss-pro' ),
			),
			'bb_groups_new_zoom'
		);

		$this->register_email_type(
			'zoom-scheduled-webinar-email',
			array(
				/* translators: do not remove {} brackets or translate its contents. */
				'email_title'         => __( '[{{{site.name}}}] {{poster.name}} scheduled a Zoom Webinar in the group: "{{group.name}}"', 'buddyboss-pro' ),
				/* translators: do not remove {} brackets or translate its contents. */
				'email_content'       => __( "<a href=\"{{{poster.url}}}\">{{poster.name}}</a> scheduled a Zoom Webinar in the group \"<a href=\"{{{group.url}}}\">{{group.name}}</a>\":\n\n{{{zoom_webinar}}}", 'buddyboss-pro' ),
				/* translators: do not remove {} brackets or translate its contents. */
				'email_plain_content' => __( "{{poster.name}} scheduled a Zoom Webinar in the group \"{{group.name}}\":\n\n{{{zoom_webinar}}}", 'buddyboss-pro' ),
				'situation_label'     => __( 'A Zoom webinar is scheduled in a group', 'buddyboss-pro' ),
				'unsubscribe_text'    => __( 'You will no longer receive emails when someone schedules a webinar in a group.', 'buddyboss-pro' ),
			),
			'bb_groups_new_zoom'
		);

		$this->register_notification(
			'groups',
			'bb_groups_new_zoom',
			'bb_groups_new_zoom'
		);

		$this->register_notification_filter(
			__( 'Group meetings and webinars', 'buddyboss-pro' ),
			array( 'bb_groups_new_zoom' ),
			95
		);
	}

	/**
	 * Format the notifications.
	 *
	 * @since 1.2.1
	 *
	 * @param string $content               Notification content.
	 * @param int    $item_id               Notification item ID.
	 * @param int    $secondary_item_id     Notification secondary item ID.
	 * @param int    $action_item_count     Number of notifications with the same action.
	 * @param string $component_action_name Canonical notification action.
	 * @param string $component_name        Notification component ID.
	 * @param int    $notification_id       Notification ID.
	 * @param string $screen                Notification Screen type.
	 *
	 * @return array
	 */
	public function format_notification( $content, $item_id, $secondary_item_id, $action_item_count, $component_action_name, $component_name, $notification_id, $screen ) {
		return $content;
	}
}
