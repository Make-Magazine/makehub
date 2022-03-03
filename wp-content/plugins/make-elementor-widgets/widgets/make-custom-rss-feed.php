<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Make: Custom RSS feed
 *
 * Elementor widget that allows you to pull in an RSS feed and customize the look and feel
 *
 * @since 1.0.0
 */
class Elementor_makeCustomRss_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'makecustomrss';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Make: Custom RSS feed', 'elementor-make-widget' );
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
		return [ 'make', 'custom', 'rss'];
	}

	/**
	 * Register widget controls.
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
    				'label' => esc_html__( 'Title (optional)', 'elementor-make-widget' ),
    				'type' => \Elementor\Controls_Manager::TEXT,
    				'placeholder' => esc_html__( 'Enter your title', 'elementor-make-widget' ),
    			]
    		);

				$this->add_control(
							'border_style',
							[
								'label' => esc_html__( 'Items to display', 'plugin-name' ),
								'type' => \Elementor\Controls_Manager::SELECT,
								'default' => 'solid',
								'options' => [
									'1'  => esc_html__( '1', 'elementor-make-widget' ),
									'2' => esc_html__( '2', 'elementor-make-widget' ),
									'3' => esc_html__( '3', 'elementor-make-widget' ),
									'4' => esc_html__( '4', 'elementor-make-widget' ),
									'5' => esc_html__( '5', 'elementor-make-widget' ),
									'6' => esc_html__( '6', 'elementor-make-widget' ),
									'7' => esc_html__( '7', 'elementor-make-widget' ),
									'8' => esc_html__( '8', 'elementor-make-widget' ),
									'9' => esc_html__( '9', 'elementor-make-widget' ),
									'10' => esc_html__( '10', 'elementor-make-widget' ),
								],
							]
						);
						$this->add_control(
								'text_align',
								[
									'label' => esc_html__( 'Display Order', 'elementor-make-widget' ),
									'type' => \Elementor\Controls_Manager::CHOOSE,
									'options' => [
										'random' => [
											'title' => esc_html__( 'Random', 'elementor-make-widget' ),
											'icon' => 'eicon-text-align-left',
										],
										'center' => [
											'title' => esc_html__( 'Date', 'elementor-make-widget' ),
											'icon' => 'eicon-date',
										],
										'right' => [
											'title' => esc_html__( 'Author', 'elementor-make-widget' ),
											'icon' => 'eicon-person',
										],
									],
									'default' => 'center',
									'toggle' => true,
								]
							);
		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
    $settings = $this->get_settings_for_display();
		echo '<h4>'.$settings['title'].'</h4>';

    global $wpdb;
    global $bp;

    $user = wp_get_current_user();
    $user_email = (string) $user->user_email;
    $user_slug = $user->user_nicename;

    //pull makerspace information from the makerspace site
    $sql = 'SELECT meta_key, meta_value from wp_3_gf_entry_meta '
            . ' where entry_id = (select entry_id FROM `wp_3_gf_entry_meta` '
            . '                    WHERE `meta_key` LIKE "141" and meta_value like "' . $user_email . '")';
    $ms_results = $wpdb->get_results($sql);

    if (!empty($ms_results)) {
        ?>
        <div class="dashboard-box expando-box">
            <h4 class="open"><?php echo ($settings['title']!=''?$settings['title']:'My Makerspace listings');?></h4>
            <ul class="open">
                <li><p><b><?php echo $ms_results[0]->meta_value; ?></b> - <a href="<?php echo $ms_results[1]->meta_value; ?>" target="_blank"><?php echo $ms_results[1]->meta_value; ?></a></p></li>
                <li><a href="https://makerspaces.make.co/edit-your-makerspace/" class="btn universal-btn">Manage your Makerspace Listing</a></li>
            </ul>
        </div>
        <?php
    }

	}

}
