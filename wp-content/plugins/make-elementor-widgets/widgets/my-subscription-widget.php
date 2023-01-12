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
		//$user_email = 'rio@make.co'; //active subscription
		//$user_email = 'tim@cometoconnect.com'; //expired subscription
		//$user_email = 'steam.jazzy.0w@icloud.com'; //cancelled (no active or pending) subscription
	    //$user_email = 'dana@thelabellas.com'; //active gift subscription - recipient
		//$user_email = 'KOA.ROSA@GMAIL.COM'; //active subscription and given gifts
		//$user_email = 'KOA.ROSA@GMAIL.COM'; //no active subscription and given gifts
		//$user_email = 'tyler.smelley@gmail.com'; //auto renewal
		//$user_email = 'kentkrue@gmail.com'; //active example
		//$user_email = 'pjo@pobox.com'; //no subscription, 2 gift subscriptions example
		//$user_email = 'MICHAEL@MFRANCE.NET'; // multiple expired subscriptions
		//$user_email = 'dhares@hickoryhill-consulting.com'; //multiple active customer accounts
		$user_email = 'mike.kinsman@gmail.com'; //multiple active customer accounts
		//$user_email = 'BRIAN@TREEGECKO.COM'; //multiple active customer accounts
		//$user_email = 'webmaster@make.co'; //no subscription
		//$return .= '  Using email '.$user_email.'<br/>';

		/*                   Subscription Lookup By Email
			This service returns all subscription information stored for all customers
			with the given Email Address and optional Product Id. Note, this includes
			both current subscription and deactivated subscriptions.
			https://training.omeda.com/knowledge-base/subscription-lookup-by-email/

			Returns:
				- Omeda Customer ID,
				- API URL for customer information
				- Subscription object
		*/
		//echo '<b>Calling customer by email API '.$sub_by_email_api.'</b><br/>';
		$sub_by_email_api = 'https://ows.omeda.com/webservices/rest/brand/MK/customer/email/'.$user_email.'/subscription/product/7/*';
		$header = array("x-omeda-appid: 0387143E-E0DB-4D2F-8441-8DAB0AF47954");

		$subscriptionJson = json_decode(basicCurl($sub_by_email_api, $header));

		// check if customer found at omeda, otherwise skip
		$customers = (isset($subscriptionJson->Customers)?$subscriptionJson->Customers:array());

		//loop through all customers associated with this email
		foreach($customers as $customer){
			$customer_id = $customer->OmedaCustomerId;

			//pull customer information
			if(isset($customer->Url)){
				/*                   Customer Lookup By Customer Id
					The response will include basic Customer information and various
					links to look up additional Customer information such as Demographics,
					Addresses, etc for a single Customer record.
					https://training.omeda.com/knowledge-base/customer-lookup-by-customer-id/
				*/
				//echo '<b>Calling customer specific API '.$customer->Url.'</b><br/>';
				$customerInfo = json_decode(basicCurl($customer->Url, $header));

				/*                   Address Lookup By Customer Id
					This API provides the ability look up a Customer’s Address by the Customer Id.
					The response will return all active addresses stored for a given customer.
					https://training.omeda.com/knowledge-base/customer-lookup-by-customer-id/
				*/
				if(isset($customerInfo->Addresses)){
					//echo '<b>Calling customer address API '.$customerInfo->Addresses.'</b><br/>';
					$customer_address = json_decode(basicCurl($customerInfo->Addresses, $header));

					//save addresses for this customer
					$address_array=array();
					foreach($customer_address->Addresses as $address){
						//only write the primary address
						if($address->StatusCode==1){
							$address_array[] = (array) $address;
						}
					} //end customer address loop
				} //end check if customer address url set
			} //end check if customer url set

			// loop through all subscriptions for this customer
			foreach($customer->Subscriptions as $customer_sub){
				//was this subscription gifted?
				$donorName = '';
				if(isset($customer_sub->DonorId)){
					//pull donor information
					$donor_api  = 'https://ows.omeda.com/webservices/rest/brand/MK/customer/'.$customer_sub->DonorId.'/*';
					//echo '<b>Calling donor API '.$donor_api.'</b><br/>';
					$donorInfo  = json_decode(basicCurl($donor_api, $header));
					$donorName = (isset($donorInfo->FirstName) ? $donorInfo->FirstName:'') . ' ' .
					             (isset($donorInfo->LastName) ? $donorInfo->LastName:'');
				}

				// the customer array contains all information regarding the customer.
				// each row is specific to a subscription
				$customer_array['subscriptions'][$customer_sub->ShippingAddressId]  = (array) $customer_sub;
				$customer_array['subscriptions'][$customer_sub->ShippingAddressId]['customer_id'] = $customer_id;   //customer id associated with this subscriptiobn
				$customer_array['subscriptions'][$customer_sub->ShippingAddressId]['FirstName']   = $customerInfo->FirstName;  //customer basic information
				$customer_array['subscriptions'][$customer_sub->ShippingAddressId]['LastName']	   = $customerInfo->LastName;  //customer basic information
				$customer_array['subscriptions'][$customer_sub->ShippingAddressId]['address_array'] = $address_array; //addresses associated with this subscription
				$customer_array['subscriptions'][$customer_sub->ShippingAddressId]['donorName']     = $donorName;	   //donor information if any,
			} //end customer subscription loop

			//now let's see if this customer has given any gifts
			$giftAPI = 'https://ows.omeda.com/webservices/rest/brand/MK/customer/'.$customer_id.'/gift/*';

			//echo '<b>Calling gift API '.$giftAPI.'</b><br/>';
			$gift_array = json_decode(basicCurl($giftAPI, $header));

			if(isset($gift_array->GiftRecipients) && !empty($gift_array->GiftRecipients)){
				foreach($gift_array->GiftRecipients as $giftRecipients){
					//only process gifts with a subscription attached
					if(isset($giftRecipients->Subscriptions)){
						//var_dump($giftRecipients->Subscriptions);
						//save addresses for this customer
						$address_array=array();
						foreach($giftRecipients->Addresses as $address){
							//only write the primary address
							if($address->StatusCode==1){
								$address_array[] = (array) $address;
							}
						} //end customer address loop

						$emails = (isset($giftRecipients->Emails) ? (array) $giftRecipients->Emails: array());
						//loop through subscriptions for each gift recipien`t
						foreach($giftRecipients->Subscriptions as $gift_sub){
							// the customer array contains all information regarding the customer.
							// each row is specific to a subscription
							$customer_array['gifts'][$gift_sub->ShippingAddressId]  = (array) $gift_sub;
							$customer_array['gifts'][$gift_sub->ShippingAddressId]['FirstName']   = $giftRecipients->FirstName;  //customer basic information
							$customer_array['gifts'][$gift_sub->ShippingAddressId]['LastName']	   = $giftRecipients->LastName;  //customer basic information
							$customer_array['gifts'][$gift_sub->ShippingAddressId]['address_array'] = $address_array; //addresses associated with this subscription
							$customer_array['gifts'][$gift_sub->ShippingAddressId]['Emails'] = $emails; //addresses associated with this subscription
						}
					}
				}
			} //end check for gift recipients*/

		} //end customer loop

		?>

		<div class="dashboard-box make-elementor-expando-box">
			<h4 class="open"><?php echo ($settings['title']!=''?$settings['title']:'My Magazine Subscription(s)');?></h4>
			<ul class="open">
				<li>
					<?php
					$return = '';
					//var_dump($customer_array);
					if(empty($customer_array) || empty($customer_array['subscriptions']) ){
						echo "<p>I'm sorry, we couldn't find any subscriptions using email ". $user_email.'</p><br/>';
						echo '    <a href="https://subscribe.makezine.com" target="_blank" class="btn universal-btn">Subscribe Today</a>';
					}

					//process subscription array
					if(!empty($customer_array['subscriptions']) ){
						//We only want to display Active or pending subscriptions. If none are found, display the most recent sub based on postal ID
						$subscriptions = cleanSubs($customer_array['subscriptions']);
						//build output
						foreach($subscriptions as $subscription){
							$return .= buildSubOutput($subscription);
						} //end subscription loop
					} //end check if subscription array is set

					//Check if customer has given any gifts
					if(isset($customer_array['gifts']) && !empty($customer_array['gifts']) ){
						$return .= '<h3>Gifts Given</h3>';
						$gift_subs = cleanSubs($customer_array['gifts']);
						foreach($gift_subs as $gift){
							$return .= buildSubOutput($gift);
						}
					}
					echo $return;
					?>
				</li>
			</ul>
		</div>
		<?php
	} //end render function
}

function cleanSubs($subArray) {
	$subscriptions = array();

	//ensure the subscriptions are sorted with the most recent subscription on top, based on exp date
	$exp_date=array_column($subArray,"IssueExpirationDate");
	array_multisort($exp_date, SORT_DESC,$subArray);

	//check for any active or pending subscriptions
	$active  = array_search(1, array_column($subArray, 'Status'));
	$pending = array_search(2, array_column($subArray, 'Status'));

	//we only want to display active or pending subscriptions
	if($active === false && $pending===false){
		//	If there are no active or pending subscriptions found,
		//	display the most recent subscription based on postal ID
		//echo 'no active or pending subsriptions found';
		//remove all but the most recent subscription
		$subscriptions[] = array_shift($subArray);
	}else{
		//loop through the subscriptions and only output the active and pending subscriptions
		foreach($subArray as $subscription){
			//save only the active and pending orders
			if(isset($subscription['Status']) && ($subscription['Status']==1 || $subscription['Status']==2)){
				$subscriptions[] = $subscription;
			}

		}
	}
	return $subscriptions;
}

function buildSubOutput($subscription) {
	$return = '';

	//Build the output
	$name_address =  ucfirst($subscription['FirstName'].' '. $subscription['LastName']).'<br/>';

	//show the address associated with this subscription
	if(isset($subscription['address_array']) ){
		foreach($subscription['address_array'] as $address_info) {
			$name_address .= (isset($address_info['Company'])?$address_info['Company'].'<br/>':'');
			$name_address .= $address_info['Street'].'<br/>';
			$name_address .= (isset($address_info['ApartmentMailStop'])!=''?$address_info['ApartmentMailStop'].'<br/>':'');
			$name_address .= (isset($address_info['ExtraAddress'])!=''?$address_info['ExtraAddress'].'<br/>':'');
			$name_address .= $address_info['City'] .', '. $address_info['Region'].' '. $address_info['PostalCode'].'<br/>';
			$name_address .= $address_info['Country'].'<br/><br/>';
		}
	}

	//determine supscription type
	if(isset($subscription['ActualVersionCode'])){
		switch ($subscription['ActualVersionCode']) {
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
				$subscription_type = $subscription['ActualVersionCode'];
				break;
		}
	}

	//determine subscription Status
	$subscription_status = '';
	if(isset($subscription['Status']) ){
		switch ($subscription['Status']){
			case 1:   $subscription_status = "Active"; break;
			case 2:   $subscription_status = "Pending"; break;
			case 3:   $subscription_status = "Expired"; break;
			case 4:   $subscription_status = "Cancelled"; break;
			case 5:   $subscription_status = "Graced"; break;
			case 6:   $subscription_status = "Standing Order"; break;
			default:  $subscription_status = $subscription['Status']; break;
		}
	}

	//renewal type
	$auto_renew = '';
	// 0 = Not Auto Renewal, 5 = Auto Charge, 6 = Auto Bill Me on Invoice
	if(isset($subscription['AutoRenewalCode']) ){
		switch ($subscription['AutoRenewalCode']){
			case 0: $auto_renew = "(account not set up for auto renewal)"; break;
			case 5: $auto_renew .= "(account will auto renew)"; break;
			case 6: $auto_renew .= "(account will be billed with an invoice)"; break;
		}
	}

	//expiration date - show expiration date and number of issues remaining if subscription is not expired
	$exp_date = $issues_remaining = '';
	if(isset($subscription['Status']) && $subscription['Status'] ==1){
		$exp_date = date_format(date_create($subscription['IssueExpirationDate']), "Y/m/d");
		$issues_remaining = $subscription['IssuesRemaining'];
	}

	//last payment date
	$last_pay_date = $last_pay_amt = '';
	if(isset($subscription['LastPaymentDate'])){
		$last_pay_date = date_format(date_create($subscription['LastPaymentDate']), "Y/m/d");
		$last_pay_amt  = $subscription['LastPaymentAmount'];
	}

	//Order date
	if(isset($subscription['OrderDate'])) {
		$order_date=date_format(date_create($subscription['OrderDate']), "Y/m/d");
		//$return .= 'Ordered on: '. $order_date.'<br/><br/>';
	}

	$return .= '<div class="container">';
	$return .= '	<div class="row">
						<div class="col-sm-3">'.$subscription['ShippingAddressId'].'</div>
						<div class="col-sm-3">'.$subscription_type.' Subscription</div>
					   	<div class="col-sm-3">'.$subscription_status.'</div>
					   	<div class="col-sm-3">'.$name_address.'</div>
				   	</div>
				 </div>';

	 //was this subscription a gift?
	 if(isset($subscription['donorName'])&&$subscription['donorName']!=''){
	 	$return .= '<i style="color:#eb002a" class="fas fa-gift"></i> Lucky you! This subscription was gifted to you by '.$subscription['donorName'].'.<br/><br/>';
	 }

	$return .= '<div id="additional_info" class="container">';
	$return .= 		($exp_date!=''?'Subscription expires on '.$exp_date.' '.$auto_renew.'.<br/>':'');
	$return .= 		($last_pay_date!='' ? 'Last payment received on '.$last_pay_date.' for '.$last_pay_amt.'. Thank you!<br/>':'');
	$return .= '</div>';

	$return .= '<hr>';
	return $return;
}
