<?php
/*
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

 //Elementor Make: Campus Tickets listings Widget
 //
 //Elementor widget that lists the Maker Campus tickets the user has purchased
 //
 // @since 1.0.0


class Elementor_MyCampusTickets_Widget extends \Elementor\Widget_Base {

	//Get widget name.
	public function get_name() {
		return 'makecampustickets';
	}

	// Get widget title.
	public function get_title() {
		return esc_html__( 'Make: Campus Tickets', 'elementor-make-widget' );
	}

	// Get widget icon.
	public function get_icon() {
		return 'eicon-custom';
	}

	// Get widget categories.
	public function get_categories() {
		return [ 'make-category' ];
	}

	// Get widget keywords.
	public function get_keywords() {
		return [ 'make', 'makercampus', 'ticket'];
	}

  // Register widget controls.
  // Add input fields to allow the user to customize the widget settings.
	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'elementor-make-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

    	$this->add_control(
    			'title',
    			[
    				'label' => esc_html__( 'Title', 'elementor-make-widget' ),
    				'type' => \Elementor\Controls_Manager::TEXT,
    				'placeholder' => esc_html__( 'Enter your title', 'elementor-make-widget' ),
    			]
    		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'elementor-make-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_alignment',
			[
				'label' => esc_html__( 'Icon Alignment', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'after' => esc_html__( 'After', 'elementor-make-widget' ),
					'before' => esc_html__( 'Before', 'elementor-make-widget' ),
				],
				'default' => 'after',
				'prefix_class' => 'expandobox-align-',
			]
		);

		$this->end_controls_section();

	}

	// Render widget output on the frontend.
	protected function render() {
		$settings = $this->get_settings_for_display();
		echo '<h4>'.$settings['title'].'</h4>';

    $user = wp_get_current_user();
    $user_email = (string) $user->user_email;

    //if this individual is an attenddee
    $events = EEM_Event::instance()->get_all(array(array('Attendee.ATT_email' => $user_email)));
    if ($events) {
        ?>
        <div class="dashboard-box make-elementor-expando-box" style="width:100%">
            <h4 class="closed"><?php echo ($settings['title']!=''?$settings['title']:'Maker Campus Tickets');?></h4>
            <ul class="closed">
                <li>
                    <div class="espresso-my-events evetn_section_container">
                        <div class="espresso-my-events-inner-content">
                            <table class="espresso-my-events-table event_section_table">
                                <thead>
                                    <tr>
                                        <th width="45%" scope="col" class="espresso-my-events-event-th">Event Group</th>
                                        <th width="35%" scope="col" class="espresso-my-events-datetime-range-th">When</th>
                                        <th width="5%" scope="col" class="espresso-my-events-tickets-num-th">#</th>
                                        <th width="15%" scope="col" class="espresso-my-events-actions-th">Attendee(s)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $return = "";
                                    foreach ($events as $event) {
                                        $return .= build_ee_ticket_section($event, $user_email);
                                    }
                                    echo $return;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <?php
    }
	}
}

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
*/
