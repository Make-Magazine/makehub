<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor my Subscription Widget
 *
 * Elementor widget that lists the makerspaces that you have submitted and links back to edit them
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
    $settings = $this->get_settings_for_display();

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
        <div class="dashboard-box make-elementor-expando-box">
            <h4 class="closed"><?php echo ($settings['title']!=''?$settings['title']:'My Makerspace listings');?></h4>
            <ul class="closed">
                <li><p><b><?php echo $ms_results[0]->meta_value; ?></b> - <a href="<?php echo $ms_results[1]->meta_value; ?>" target="_blank"><?php echo $ms_results[1]->meta_value; ?></a></p></li>
                <li><a href="https://makerspaces.make.co/edit-your-makerspace/" class="btn universal-btn">Manage your Makerspace Listing</a></li>
            </ul>
        </div>
        <?php
    }

		//old code
		?>
		<div class="dashboard-box make-elementor-expando-box">
				<h4 class="closed"><?php echo ($settings['title']!=''?$settings['title']:'My Makerspace listings');?></h4>
				<ul class="closed">
					<li>
						<?php
						$return = '';
						$user_email = $user->user_email;
				    //$user_email = 'cathy@make.co'; //membership example
				    //$user_email = 'alicia@make.co'; //active gift subscription - recipient
				    //$user_email = 'rio@make.co'; //active subscription
				    //$user_email = 'kentkrue@gmail.com'; //membership example
				    //$user_email = 'alleriodrone@yahoo.com'; //expired subscription
				    //$user_email = 'pjo@pobox.com'; //no subscription, 2 gift subscriptions example
				    //$user_email = 'MICHAEL@MFRANCE.NET'; // multiple subscriptions
				    $user_email = 'webmaster@make.co'; //no subscription
				    //$return .= '  Using email '.$user_email.'<br/>';

						/*                   Subscription Lookup By Email
				    This service returns all subscription information stored for all customers
				    with the given Email Address and optional Product Id. Note, this includes
				    both current subscription and deactivated subscriptions.
				    */
				    $sub_by_email_api = 'https://ows.omeda.com/webservices/rest/brand/MK/customer/email/'.$user_email.'/subscription/product/7/*';
				    $header = array("x-omeda-appid: 0387143E-E0DB-4D2F-8441-8DAB0AF47954");

				    $subscriptionJson = json_decode(basicCurl($sub_by_email_api, $header));
				    if(isset($subscriptionJson->Errors)){
				      $return .= '<ul class="open">';
				      $return .= '  <li>';
				      $return .= '    <p>I\'m sorry, we couldn\'t find any subscriptions using email '. $user_email.'</p><br/>';
				      $return .= '    <a href="https://subscribe.makezine.com" target="_blank" class="btn universal-btn">Subscribe Today</a>';
				      $return .= '  </li>';
				      $return .= '</ul>';
				    }

				    // check if customer found at omeda, otherwise skip
				    $customers = (isset($subscriptionJson->Customers)?$subscriptionJson->Customers:array());

				    //loop through all customers associated with this email
				    foreach($customers as $customer){
				      $customer_id = $customer->OmedaCustomerId;
				      //pull customer information
				      if(isset($customer->Url)){
				        //$return .= '<b>Calling API '.$customer->Url.'</b><br/>';
				        $customerInfo = json_decode(basicCurl($customer->Url, $header));
				        $customer_name = $customerInfo->FirstName.' '. $customerInfo->LastName;
				        $return .= '<h3>'.$customer_name.'</h3>';
				        //address information
				        if(isset($customerInfo->Addresses)){
				          $customer_address = json_decode(basicCurl($customerInfo->Addresses, $header));

				          $customer_address_array = array();
				          //save addresses for this customer
				          foreach($customer_address->Addresses as $address){
				            $customer_address_array[$address->Id] = array(
				                'address'   => $address->Street,
				                'address2'  => (isset($address->ExtraAddress)?$address->ExtraAddress:''),
				                'city'      => $address->City,
				                'state'     => $address->RegionCode,
				                'zipCode'   => $address->PostalCode,
				                'country'   => $address->CountryCode
				            );
				          }
				        }
				      }

				      if(empty($customer->Subscriptions)){

				        $return .= '<ul class="open">';
				        $return .= '  <li>';
				        $return .= '    <p>I\'m sorry, we couldn\'t find any subscriptions for this customer.</p><br/>';
				        $return .= '    <a href="https://subscribe.makezine.com" target="_blank" class="btn universal-btn">Subscribe Today</a>';
				        $return .= '  </li>';
				        $return .= '</ul>';
				      }
				      // loop through all subscriptions for this customer
				      foreach($customer->Subscriptions as $customer_sub){
				        //determine supscription type
				        if(isset($customer_sub->ActualVersionCode)){
				          switch ($customer_sub->ActualVersionCode) {
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
				              $subscription_type = $customer_sub->ActualVersionCode;
				              break;
				          }
				        }else{
				          $requestedVersion = '';
				        }

				        //determibe subscription Status
				        $subscription_status = '';
				        if(isset($customer_sub->Status)){
				          switch ($customer_sub->Status){
				            case 1:   $subscription_status = "Active"; break;
				            case 2:   $subscription_status = "Pending"; break;
				            case 3:   $subscription_status = "Expired"; break;
				            case 4:   $subscription_status = "Cancelled"; break;
				            case 5:   $subscription_status = "Graced"; break;
				            case 6:   $subscription_status = "Standing Order"; break;
				            default:  $subscription_status = $customer_sub->status; break;
				          }
				        }

				        //Account number (also known as postal address in Omeda)
				        $postal_address_id = $customer_sub->ShippingAddressId;
				        $return .= 'Account Number: '. $postal_address_id.' ('.$subscription_status.')<br/>';

				        //show the address associated with this subscription
				        if(isset($customer_address_array[$postal_address_id])){
				          $address_info = $customer_address_array[$postal_address_id];
				          $return .= $address_info['address'].'<br/>';
				          $return .= ($address_info['address2']!=''?$address_info['address2'].'<br/>':'');
				          $return .= $address_info['city'] .', '. $address_info['state'].' '. $address_info['zipCode'].'<br/>';
				          $return .= $address_info['country'].'<br/><br/>';
				        }

				        $return .= 'Subscription Type: '.$subscription_type.'<br/>';

				        //was this subscription gifted?
				        if(isset($customer_sub->DonorId)){
				          //pull donor name
				          $donor_api = 'https://ows.omeda.com/webservices/rest/brand/MK/customer/'.$customer_sub->DonorId.'/*';
				          $donorInfo = json_decode(basicCurl($donor_api, $header));
				          $donorName = $donorInfo->FirstName.' '. $donorInfo->LastName;
				          $return .= '<i style="color:#eb002a" class="fas fa-gift"></i> Lucky you! This subscription was gifted to you by '.$donorName. (isset($customer_sub->GiftSentDate)?' on '.$customer_sub->GiftSentDate:"").'<br/><br/>';
				        }

				        //show expiration date and number of issues remaining if subscription is not expired
				        if($customer_sub->Status !=3){
				          $exp_date=date_format(date_create($customer_sub->IssueExpirationDate), "Y/m/d");
				          $return .= 'Your current subscription expires on ' . $exp_date.' and you have '. $customer_sub->IssuesRemaining.' issues remaining.<br/><br/>';
				        }

				        //renewal type
				        switch ($customer_sub->AutoRenewalCode){
				          case 0: $return .= "Enroll in Auto Renewal Now **TBD** add with link"; break;
				          case 5: $return .= "Your Account is set to Auto Renew."; break;
				          case 6: $return .= "Your Account will be billed with an invoice."; break;
				        }
				        $return .= '<br/><br/>';

				        if(isset($customer_sub->LastPaymentDate)){
				          $last_pay_date=date_format(date_create($customer_sub->LastPaymentDate), "Y/m/d");
				          $return .= 'Last payment was received on ' . $last_pay_date.' for '. $customer_sub->LastPaymentAmount.'. Thank You.<br/><br/>';
				        }

				        //subscription order date
				        $order_date=date_format(date_create($customer_sub->OrderDate), "Y/m/d");
				        $return .= 'Ordered on: '. $order_date.'<br/><br/>';

				      }

				      //now let's see if this customer has given any Gift
				      //$return .= 'My customer id is '. $customer_id.'<br/>';
				      $giftAPI = 'https://ows.omeda.com/webservices/rest/brand/MK/customer/'.$customer_id.'/gift/*';

				      $gift_array = json_decode(basicCurl($giftAPI, $header));
				      if(isset($gift_array->GiftRecipients) && !empty($gift_array->GiftRecipients)){
				          $return .= '<h3>Gift Subscriptions Given</h3>';
				          foreach($gift_array->GiftRecipients as $gift_recipients){
				              $return .= $gift_recipients->FirstName .' '.$gift_recipients->LastName.'<br/>';
				              foreach($gift_recipients->Subscriptions as $gift_subscriptions){
				                $return .= 'Ordered on: '.$gift_subscriptions->OrderDate.'<br/>';
				                $return .= 'Expires on: '.(isset($gift_subscriptions->IssueExpirationDate)?$gift_subscriptions->IssueExpirationDate:'').'<br/>';
				                $return .= 'Copies Remaining: '. (isset($gift_subscriptions->CopiesRemaining)?$gift_subscriptions->CopiesRemaining:'').'<br/>';
				              }
				              $return .= '<br/>';
				          }

				      }
				    }

				    $return .= '</div>';

				  	echo $return;
						?>
					</li>
				</ul>
		</div>
					<?php	   	 
	}

}
