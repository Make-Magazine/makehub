<?php /*
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

//Elementor Make: Facilitator event listings Widget
 //Elementor widget that lists the makerspaces that you have submitted and links back to edit them
 //@since 1.0.0
class Elementor_MakeFacilitatorEvents_Widget extends \Elementor\Widget_Base {

	// Get widget name
	public function get_name() {
		return 'makefacilEvents';
	}

	//Get widget title.
	public function get_title() {
		return esc_html__( 'Facilitator Events', 'elementor-make-widget' );
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
		return [ 'make', 'facilitator', 'event'];
	}

	// Register MakeFacilitEvents_Widget widget controls.
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
				'label' => esc_html__( 'Style', 'plugin-name' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_alignment',
			[
				'label' => esc_html__( 'Icon Alignment', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'after' => esc_html__( 'After', 'elementor' ),
					'before' => esc_html__( 'Before', 'elementor' ),
				],
				'default' => 'after',
				'prefix_class' => 'expandobox-align-',
			]
		);

		$this->end_controls_section();

	}

	// Render MakeFacilitEvents_Widget widget output on the frontend.
	protected function render() {
	    $settings = $this->get_settings_for_display();

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
	        <div class="dashboard-box make-elementor-expando-box">
	            <h4 class="closed"><?php echo ($settings['title']!=''?$settings['title']:'Maker Campus Facilitator');?></h4>
	            <ul class="closed">
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
*/
