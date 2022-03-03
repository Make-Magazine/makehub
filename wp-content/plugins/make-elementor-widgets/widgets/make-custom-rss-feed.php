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
		    			'link',
		    			[
		    				'label' => esc_html__( 'Link (defaults to site)', 'elementor-make-widget' ),
		    				'type' => \Elementor\Controls_Manager::TEXT,
		    				'placeholder' => esc_html__( 'Provide a link', 'elementor-make-widget' ),
		    			]
		);
		$this->add_control(
		    			'rss_url',
		    			[
		    				'label' => esc_html__( 'Enter the full RSS feed URL', 'elementor-make-widget' ),
		    				'type' => \Elementor\Controls_Manager::TEXT,
		    				'placeholder' => esc_html__( 'https://', 'elementor-make-widget' ),
		    			]
		);
				$this->add_control(
							'num_display',
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
								'disp_order',
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
		$num_display = $settings['num_display'];
		$disp_order = $settings['disp_order'];
		$title = $settings['title'];
		echo '<h4>'.$settings['title'].'</h4>';

		$url = !empty($settings['rss_url']) ? $settings['rss_url'] : '';
		while (stristr($url, 'http') != $url) {
				$url = substr($url, 1);
		}
		if (empty($url)) {
				return;
		}

		// self-url destruction sequence
		if (in_array(untrailingslashit($url), array(site_url(), home_url()))) {
				return;
		}

		$rss = fetch_feed($url);
		$desc = '';

		if (!is_wp_error($rss)) {
				$desc = esc_attr(strip_tags(@html_entity_decode($rss->get_description(), ENT_QUOTES, get_option('blog_charset'))));
				if (empty($title)) {
						$title = strip_tags($rss->get_title());
				}
				if (empty($link)) {
						$link = strip_tags($rss->get_permalink());
						while (stristr($link, 'http') != $link) {
								$link = substr($link, 1);
						}
				}
		}

		if (empty($title)) {
				$title = !empty($desc) ? $desc : __('Unknown Feed');
		}

		$url = strip_tags($url);
		if ($title) {
				$title = '<a target="_blank" class="rsswidget" href="' . esc_url($link) . '">' . $title . '</a>';
		}

		makewidget_rss_output($rss, $settings);

		if (!is_wp_error($rss)) {
				$rss->__destruct();
		}
		unset($rss);

	} //end render public function
}
