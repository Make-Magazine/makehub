<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Make: Campus Tickets listings Widget
 *
 * Elementor widget that lists the Maker Campus tickets the user has purchased
 *
 * @since 1.0.0
 */
class Elementor_MyCampusTickets_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'makecampustickets';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Make: Campus Tickets', 'elementor-make-widget' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-custom';
	}

	/**
	 * Get widget categories.
	 */
	public function get_categories() {
		return [ 'make-category' ];
	}

	/**
	 * Get widget keywords.
	 */
	public function get_keywords() {
		return [ 'make', 'makercampus', 'ticket'];
	}

  /**
	 * Register widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 */
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

	}

	/**
	 * Render widget output on the frontend.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		echo '<h4>'.$settings['title'].'</h4>';

    $user = wp_get_current_user();
    $user_email = (string) $user->user_email;

    //if this individual is an attenddee
    $events = EEM_Event::instance()->get_all(array(array('Attendee.ATT_email' => $user_email)));
    if ($events) {
        ?>
        <div class="dashboard-box expando-box" style="width:100%">
            <h4 class="close"><?php echo ($settings['title']!=''?$settings['title']:'Maker Campus Tickets');?></h4>
            <ul class="close">
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
