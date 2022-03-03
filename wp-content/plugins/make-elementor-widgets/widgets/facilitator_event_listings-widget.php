<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Make: Facilitator event listings Widget
 *
 * Elementor widget that lists the makerspaces that you have submitted and links back to edit them
 *
 * @since 1.0.0
 */
class Elementor_MakeFacilitatorEvents_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve MakeFacilitEvents_Widget widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'makefacilEvents';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve MakeFacilitEvents_Widget widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Facilitator Events', 'elementor-make-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve MakeFacilitEvents_Widget widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-custom';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the MakeFacilitEvents_Widget widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'make-category' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the myMspaces_Widget widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'make', 'facilitator', 'event'];
	}

	/**
	 * Register MakeFacilitEvents_Widget widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
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
	 * Render MakeFacilitEvents_Widget widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
    $settings = $this->get_settings_for_display();
		echo '<h4>'.$settings['title'].'</h4>';

		global $user_email;
    $hosted_events = EEM_Event::instance()->get_all(
            array(
                //'limit' => 10,
                'order_by' => array('EVT_visible_on' => 'DESC'),
                array(
                    'Person.PER_email' => $user_email
                )
            )
    );
    if (!empty($hosted_events)) {
        ?>
        <div class="dashboard-box expando-box">
            <h4 class="close"><?php echo ($settings['title']!=''?$settings['title']:'Maker Campus Facilitator');?></h4>
            <ul class="close">
                <?php
                foreach ($hosted_events as $event) {
                    ?>
                    <li><b><?php echo $event->name(); ?></b> - <a href="<?php echo $event->get_permalink(); ?>">View</a></li>
                    <?php
                }
                ?>
                <li><a class="btn universal-btn" href="/facilitator-portal/">Facilitator Portal</a></li>
            </ul>
        </div>
        <?php
    }
	}
}
