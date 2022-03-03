<?php
/**
 * Plugin Name: Make: Elementor Widgets
 * Description: This plugin adds some common Make: dashboard widgets including: Makershed purchases, My Makerspace listings, Facilitator Event listings, Maker Campus tickets, Maker camp projects 
 * Version:     1.0.0
 * Author:      Alicia Williams
 * Text Domain: elementor-make-widget
 *
 * Elementor tested up to: 3.5.0
 * Elementor Pro tested up to: 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register oEmbed Widget.
 *
 * Include widget file and register widget class.
 *
 * @since 1.0.0
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 * @return void
 */
function register_make_widgets( $widgets_manager ) {
  // Include all function files in the makerfaire/functions directory:
  foreach (glob(__DIR__ . '/widgets/*.php') as $file) {
      require_once($file);
  }

	$widgets_manager->register( new \Elementor_mShedPurch_Widget() );
  $widgets_manager->register( new \Elementor_myMspaces_Widget() );
  $widgets_manager->register( new \Elementor_MakeFacilitatorEvents_Widget() );
  $widgets_manager->register( new \Elementor_MyCampusTickets_Widget() );
  $widgets_manager->register( new \Elementor_MyMakerCamp_Widget() );

}
add_action( 'elementor/widgets/register', 'register_make_widgets' );

/* Add new Make: category for our widgets */
function add_elementor_widget_categories( $elements_manager ) {
  error_log('function add_elementor_widget_categories');
	$elements_manager->add_category(
		'make-category',
		[
			'title' => esc_html__( 'Make:', 'elementor-make-widget' ),
			'icon' => 'fa fa-plug',
		]
	);
}
add_action( 'elementor/elements/categories_registered', 'add_elementor_widget_categories' );

function build_ee_ticket_section($event, $user_email) {
    // if the first date of event has passed and it's a multiday event with one ticket, skip this item in the loop
    $firstExpiredDate = EEM_Datetime::instance()->get_oldest_datetime_for_event($event->ID(), true, false, 1)->start();
    $now = new DateTime("now", new DateTimeZone('America/Los_Angeles'));
    $now = $now->format('Y-m-d H:i:s');
    $past_event = (date('Y-m-d H:i:s', $firstExpiredDate) < $now ? TRUE : FALSE);
    $registrations = $event->get_many_related('Registration', array(array('Attendee.ATT_email' => $user_email)));
    $time_range = espresso_event_date_range('', '', '', '', $event->ID(), FALSE);
    //get group link
    $group_id = get_field('group_id', $event->ID());
    $group = groups_get_group(array('group_id' => $group_id));
    $group_link = bp_get_group_link($group);

    //build the inner rows
    $return = '<tr class="ee-my-events-event-section-summary-row">
                    <td>' . $group_link . '</td>
                    <td>' . $time_range . '</td>
                    <td>' . count($registrations) . ' </td>
                    <td>';
    foreach ($registrations as $registration) {
        if (!$registration instanceof EE_Registration) {
            continue;
        }
        $actions = array();
        $link_to_edit_registration_text = esc_html__('Link to edit registration.', 'event_espresso');
        $link_to_make_payment_text = esc_html__('Link to make payment', 'event_espresso');
        $link_to_view_receipt_text = esc_html__('Link to view receipt', 'event_espresso');
        $link_to_view_invoice_text = esc_html__('Link to view invoice', 'event_espresso');

        //attendee name
        $attendee = $registration->attendee();
        $return .= $attendee->full_name() . '<br/>';

        if (!$past_event) {
            // only show the edit registration link IF the registration has question groups.
            $actions['edit_registration'] = $registration->count_question_groups() ? '<a aria-label="' . $link_to_edit_registration_text
                    . '" title="' . $link_to_edit_registration_text
                    . '" href="' . $registration->edit_attendee_information_url() . '">'
                    . '<span class="ee-icon ee-icon-user-edit ee-icon-size-16"></span></a>' : '';

            // resend confirmation email.
            $resend_registration_link = add_query_arg(
                    array('token' => $registration->reg_url_link(), 'resend' => true),
                    null
            );
        }

        // make payment?
        if ($registration->is_primary_registrant() && $registration->transaction() instanceof EE_Transaction && $registration->transaction()->remaining()) {
            $actions['make_payment'] = '<a aria-label="' . $link_to_make_payment_text
                    . '" title="' . $link_to_make_payment_text
                    . '" href="' . $registration->payment_overview_url() . '">'
                    . '<span class="dashicons dashicons-cart"></span></a>';
        }

        // receipt link?
        if ($registration->is_primary_registrant() && $registration->receipt_url()) {
            $actions['receipt'] = '<a aria-label="' . $link_to_view_receipt_text
                    . '" title="' . $link_to_view_receipt_text
                    . '" href="' . $registration->receipt_url() . '">'
                    . '<span class="dashicons dashicons-media-default ee-icon-size-18"></span></a>';
        }

        // ...and echo the actions!
        if (!empty($actions))
            $return .= implode('&nbsp;', $actions) . '<br/>';
    }

    $return .= '    </td>
                </tr>';

    return $return;
}
