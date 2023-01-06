<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor my Subscription Widget
 *
 * Elementor widget that lists the omeda subscriptions
 *
 * @since 1.0.0
 */
class Elementor_mySubscription_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve mySubscription_Widget widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'mysubs';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve mySubscription_Widget widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'My Subscription listing', 'elementor-make-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve mySubscription_Widget widget icon.
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
	 * Retrieve the list of categories the mySubscription_Widget widget belongs to.
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
	 * Retrieve the list of keywords the mySubscription_Widget widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'make', 'omeda', 'subscription'];
	}

	/**
	 * Register mySubscription_Widget widget controls.
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

	/**
	 * Render mySubscription_Widget widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		//display all php Errors
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);

		$settings = $this->get_settings_for_display();
    	$user = wp_get_current_user();

		$return = '';
		$customer_array = array();

		$user_email = $user->user_email;
		//$user_email = 'cathy@make.co'; //no subscription
		//$user_email = 'TMC104@GMAIL.COM'; //pending subscription
		//$user_email = 'alicia@make.co'; //active subscription
		$user_email = 'alleriodrone@yahoo.com'; //expired subscription
	    //$user_email = 'dana@thelabellas.com'; //active gift subscription - recipient
		//$user_email = 'KOA.ROSA@GMAIL.COM'; //2 active subscriptions, 4 gift subscriptions given
		//$user_email = 'rio@make.co'; //active subscription
		//$user_email = 'kentkrue@gmail.com'; //active example
		//$user_email = 'pjo@pobox.com'; //no subscription, 2 gift subscriptions example
		//$user_email = 'MICHAEL@MFRANCE.NET'; // multiple subscriptions

		//$user_email = 'dhares@hickoryhill-consulting.com'; //multiple active customer accounts
		//$user_email = 'mike.kinsman@gmail.com'; //multiple active customer accounts
		//$user_email = 'BRIAN@TREEGECKO.COM'; //multiple active customer accounts
		//$user_email = 'webmaster@make.co'; //no subscription
		//$return .= '  Using email '.$user_email.'<br/>';

		/*                   Subscription Lookup By Email
		This service returns all subscription information stored for all customers
		with the given Email Address and optional Product Id. Note, this includes
		both current subscription and deactivated subscriptions.
		*/
		/*$sub_by_email_api = 'https://ows.omeda.com/webservices/rest/brand/MK/customer/email/'.$user_email.'/subscription/product/7/*';
		$header = array("x-omeda-appid: 0387143E-E0DB-4D2F-8441-8DAB0AF47954");

		//echo '<b>Calling customer by email API '.$sub_by_email_api.'</b><br/>';
		$subscriptionJson = json_decode(basicCurl($sub_by_email_api, $header));

		// check if customer found at omeda, otherwise skip
		$customers = (isset($subscriptionJson->Customers)?$subscriptionJson->Customers:array());
*/
		?>

		<div class="dashboard-box make-elementor-expando-box">
			<h4 class="open"><?php echo ($settings['title']!=''?$settings['title']:'My Omeda Subscriptions');?></h4>
			<ul class="open">
				<li>
					<?php
					echo 'code goes here';
					?>
				</li>
			</ul>
		</div>
		<?php
	} //end render function
}
