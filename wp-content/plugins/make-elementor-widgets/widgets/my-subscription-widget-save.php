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
		$sub_by_email_api = 'https://ows.omeda.com/webservices/rest/brand/MK/customer/email/'.$user_email.'/subscription/product/7/*';
		$header = array("x-omeda-appid: 0387143E-E0DB-4D2F-8441-8DAB0AF47954");

		//echo '<b>Calling customer by email API '.$sub_by_email_api.'</b><br/>';
		$subscriptionJson = json_decode(basicCurl($sub_by_email_api, $header));

		// check if customer found at omeda, otherwise skip
		$customers = (isset($subscriptionJson->Customers)?$subscriptionJson->Customers:array());
/*
		//loop through all customers associated with this email
		foreach($customers as $customer){
			$customer_id = $customer->OmedaCustomerId;

			//pull customer information
			if(isset($customer->Url)){
				//echo '<b>Calling customer specific API '.$customer->Url.'</b><br/>';
				// Call Omeda customer specific API to return customer data
				$customerInfo = json_decode(basicCurl($customer->Url, $header));

				//address information
				if(isset($customerInfo->Addresses)){
					//echo '<b>Calling customer address API '.$customerInfo->Addresses.'</b><br/>';
					$customer_address = json_decode(basicCurl($customerInfo->Addresses, $header));

					//save addresses for this customer
					$address_array=array();
					foreach($customer_address->Addresses as $address){
						//only write the primary address
						if($address->StatusCode==1){
							$address_array[] = $address;
						}
					} //end customer address loop
				} //end check if customer address url set
			} //end check if customer url set

			// loop through all subscriptions for this customer
			foreach($customer->Subscriptions as $customer_sub){
				//was this subscription gifted?
				$donorInfo = '';
				if(isset($customer_sub->DonorId)){
					//pull donor name
					$donor_api  = 'https://ows.omeda.com/webservices/rest/brand/MK/customer/'.$customer_sub->DonorId.'/*';
					//echo '<b>Calling donor API '.$donor_api.'</b><br/>';
					$donorInfo  = json_decode(basicCurl($donor_api, $header));
				}

				$customer_array['subscriptions'][] = array(
					'customer_id'		=> $customer_id,   //customer id associated with this subscriptiobn
					'customer_subs'	    => $customer_sub,  //each subscription
					'customer_info'		=> $customerInfo,  //customer basic information
					'address_array'		=> $address_array, //addresses associated with this subscription
					'donorInfo'			=> $donorInfo	   //donor information if any
				);
			} //end customer subscription loop

			//now let's see if this customer has given any gifts
			$giftAPI = 'https://ows.omeda.com/webservices/rest/brand/MK/customer/'.$customer_id.'/gift/*';

			//echo '<b>Calling gift API '.$giftAPI.'</b><br/>';
			$gift_array = json_decode(basicCurl($giftAPI, $header));

			if(isset($gift_array->GiftRecipients) && !empty($gift_array->GiftRecipients)){
				$customer_array['gifts'] = $gift_array->GiftRecipients;
			} //end check for gift recipients*/

		} //end customer loop
		?>

		<div class="dashboard-box make-elementor-expando-box">
			<h4 class="open"><?php echo ($settings['title']!=''?$settings['title']:'My Omeda Subscriptions');?></h4>
			<ul class="open">
				<li>
					<?php
					//var_dump($customer_array);
					//die();
					/*
					if(empty($customer_array) || empty($customer_array['subscriptions']) ){
						echo "<p>I'm sorry, we couldn't find any subscriptions using email ". $user_email.'</p><br/>';
						echo '    <a href="https://subscribe.makezine.com" target="_blank" class="btn universal-btn">Subscribe Today</a>';
					}else{
						//subscription information
						echo '<h3>Subscription Information</h3>';
						$subscriptions = array();

						//ensure the subscriptions are sorted with the most recent subscription on top
						krsort($customer_array['subscriptions']);

						//check for any active or pending subscriptions
						$active  = array_search(1, array_column($customer_array['subscriptions'], 'status_code'));
						$pending = array_search(2, array_column($customer_array['subscriptions'], 'status_code'));

						if($active === false && $pending===false){ //no active subscription found
							echo 'no active or pending subsriptions found';
							//remove all but the most recent subscription
							$subscriptions = array_shift($customer_array['subscriptions']);
						}else{
							foreach($customer_array['subscriptions'] as $subscription){
								//save only the active and pending orders
								if($subscription['status_code']==1 || $subscription['status_code']==2){
									$subscriptions[] = $subscription;
								}
							}
						}

						//build output
						foreach($subscriptions as $subscription){
							$return = '';
							//determine supscription type
							if(isset($subscription['version_code']){
								switch ($subscription['version_code']) {
									case "P":
										$subscription_type = "Print";
										break;
									case "D":
										$subscription_type = "Digital";
										break;
									case "B":
										$subscription_type = "Print & Digital";
										break;
									default:
										$subscription_type = $subscription['version_code'];
										break;
								}
							}

							//determine subscription Status
							$subscription_status = '';
							if(isset($subscription['status_code'])){
								switch ($subscription['status_code']){
									case 1:   $subscription_status = "Active"; break;
									case 2:   $subscription_status = "Pending"; break;
									case 3:   $subscription_status = "Expired"; break;
									case 4:   $subscription_status = "Cancelled"; break;
									case 5:   $subscription_status = "Graced"; break;
									case 6:   $subscription_status = "Standing Order"; break;
									default:  $subscription_status = $subscription['status_code']; break;
								}
							}

							//Build the output
							$return .=  '<h3>'.$subscription['first_name'].' '. $subscription['last_name'].'</h3>';

							//output address information
							$return .=  '<b>Account Number:</b> '. $subscription['postal_id'].' ('.$subscription_status.')<br/>';

							//show the address associated with this subscription
							if(isset($subscription['address_array']) ){
								foreach($subscription['address_array'] as $address_info) {
									$return .= $address_info['address'].'<br/>';
									$return .= ($address_info['address2']!=''?$address_info['address2'].'<br/>':'');
									$return .= $address_info['city'] .', '. $address_info['state'].' '. $address_info['zipCode'].'<br/>';
									$return .= $address_info['country'].'<br/><br/>';
								}
							}

							//loop through subscriptions
							$return .= 'Subscription Type: '.$subscription_type.'<br/>';

							if($subscription['donorName']!=''){
								$return .= '<i style="color:#eb002a" class="fas fa-gift"></i> Lucky you! This subscription was gifted to you by '.$subscription['donorName']. (isset($subscription['GiftSentDate']&&$subscription['GiftSentDate']!='')?' on '.$subscription['GiftSentDate']:"").'<br/><br/>';
							}

							//show expiration date and number of issues remaining if subscription is not expired
							if($subscription['status_code'] !=3){
								$exp_date = date_format(date_create($subscription['exp_date']), "Y/m/d");
								$return .= 'Your current subscription expires on ' . $exp_date.' and you have '. $subscription['issues_remaining'].' issues remaining.<br/><br/>';
							}

							//renewal type
							// 0 = Not Auto Renewal, 5 = Auto Charge, 6 = Auto Bill Me on Invoice
							switch ($subscription['renewal_type'){
								case 0: $return .= "Your account is not set up for Auto Renewal"; break;
								case 5: $return .= "Your Account is set to Auto Renew."; break;
								case 6: $return .= "Your Account will be billed with an invoice."; break;
							}

							$return .= '<br/><br/>';

							if(isset($subscription['last_payment_date'])){
								$last_pay_date = date_format(date_create($subscription['last_payment_date']), "Y/m/d");
								$return .= 'Last payment was received on ' . $last_pay_date.' for '. $customer_sub->LastPaymentAmount.'. Thank You.<br/><br/>';
							}

							$order_date=date_format(date_create($customer_sub->OrderDate), "Y/m/d");
							$return .= 'Ordered on: '. $order_date.'<br/><br/>';
							//end loop through subscriptions

							//loop through gifts
							$return .= '<h3>Gift Subscriptions Given</h3>';
							$return .= $gift_recipients->FirstName .' '.$gift_recipients->LastName.'<br/>';
								//loop through gift subscriptions
								$return .= 'Ordered on: '.$gift_subscriptions->OrderDate.'<br/>';
								$return .= 'Expires on: '.(isset($gift_subscriptions->IssueExpirationDate)?$gift_subscriptions->IssueExpirationDate:'').'<br/>';
								$return .= 'Copies Remaining: '. (isset($gift_subscriptions->CopiesRemaining)?$gift_subscriptions->CopiesRemaining:'').'<br/>';
								//end loop through gift subscriptions

							//end loop through gifts
							*/
						} // end loop through subscriptions
					} //end check if subscription or customer is empty
					?>
				</li>
			</ul>
		</div>
		<?php
	} //end render function
}
