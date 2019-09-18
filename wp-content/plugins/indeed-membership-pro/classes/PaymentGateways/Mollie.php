<?php
namespace Indeed\Ihc\PaymentGateways;
/*
@since 7.4
*/
class Mollie extends \Indeed\Ihc\PaymentGateways\PaymentAbstract
{
    protected $attributes       = array();
    protected $redirectUrl      = '';
    protected $abort            = false;
    protected $paymentTypeLabel = 'Mollie Payment';
    protected $currency         = '';

    public function __construct()
    {
        include_once IHC_PATH . 'classes/PaymentGateways/mollie/vendor/autoload.php';
        $this->currency = get_option('ihc_currency');
    }

  	public function doPayment()
    {
        \Ihc_User_Logs::set_user_id(@$this->attributes['uid']);
        \Ihc_User_Logs::set_level_id(@$this->attributes['lid']);
        \Ihc_User_Logs::write_log( __('Mollie Payment: Start process', 'ihc'), 'payments');

        $settings = ihc_return_meta_arr('payment_mollie');
        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($settings['ihc_mollie_api_key']);

        $levels = get_option('ihc_levels');
        $levelData = $levels[$this->attributes['lid']];

        $siteUrl = site_url();
        $siteUrl = trailingslashit($siteUrl);
        $webhook = add_query_arg('ihc_action', 'mollie', $siteUrl);
        $amount = $levelData['price'];

        $reccurrence = FALSE;
    		if (isset($levelData['access_type']) && $levelData['access_type']=='regular_period'){
    			$reccurrence = TRUE;
    			\Ihc_User_Logs::write_log( __('Mollie Payment: Recurrence payment set.', 'ihc'), 'payments');
    		}
    		$couponData = array();
    		if (!empty($this->attributes['ihc_coupon'])){
    			$couponData = ihc_check_coupon($this->attributes['ihc_coupon'], $this->attributes['lid']);
    			\Ihc_User_Logs::write_log( __('Mollie Payment: the user used the following coupon: ', 'ihc') . $this->attributes['ihc_coupon'], 'payments');
    		}

        if ($reccurrence){

            /// RECCURING
            if ($couponData){
              if (!empty($couponData['reccuring'])){
                //everytime the price will be reduced
                $levelData['price'] = ihc_coupon_return_price_after_decrease($levelData['price'], $couponData, TRUE, $this->attributes['uid'], $this->attributes['lid']);
              }
            }

            /// TAXES
            $levelData['price'] = $this->addTaxes($levelData['price']);

            $amount = $levelData['price'];
            \Ihc_User_Logs::write_log( __('Mollie Payment: amount set @ ', 'ihc') . $amount . $this->currency, 'payments');

            if ($levelData['billing_type']=='bl_ongoing'){
              //$rec = 52;
              $recurringLimit = 52;
            } else {
              if (isset($levelData['billing_limit_num'])){
                $recurringLimit = (int)$levelData['billing_limit_num'];
              } else {
                $recurringLimit = 52;
              }
            }
            $interval = $levelData['access_regular_time_value'];
            switch ($levelData['access_regular_time_type']){
              case 'D':
                $interval .= ' days';
                break;
              case 'W':
                $interval .= ' weeks';
                break;
              case 'M':
                $interval .= ' months';
                break;
              case 'Y':
                $interval = $interval * 12; //' years';
                if ( $interval > 12 ){
                    $interval = 12;
                }
                $interval .= ' months';
                break;
            }
            \Ihc_User_Logs::write_log( __('Mollie Payment: recurrence number: ', 'ihc') . $recurringLimit, 'payments');

            /*************************** DYNAMIC PRICE ***************************/
            if (ihc_is_magic_feat_active('level_dynamic_price') && isset($this->attributes['ihc_dynamic_price'])){
                $temp_amount = $this->attributes['ihc_dynamic_price'];
                if (ihc_check_dynamic_price_from_user($this->attributes['lid'], $temp_amount)){
                    $amount = $temp_amount;
                    \Ihc_User_Logs::write_log( __('Mollie Payment: Dynamic price on - Amount is set by the user @ ', 'ihc') . $amount . $this->currency, 'payments');
                }
            }
            /**************************** DYNAMIC PRICE ***************************/
			/******TRIAL SETTING *******/
			$trial_period_days = 0;
					if (!empty($levelData['access_trial_type'])){
						if ($levelData['access_trial_type']==1 && isset($levelData['access_trial_time_value'])
								&& $levelData['access_trial_time_value'] !=''){
							switch ($levelData['access_trial_time_type']){
								case 'D':
									$trial_period_days = $levelData['access_trial_time_value'];
									break;
								case 'W':
									$trial_period_days = $levelData['access_trial_time_value'] * 7;
									break;
								case 'M':
									$trial_period_days = $levelData['access_trial_time_value'] * 31;
									break;
								case 'Y':
									$trial_period_days = $levelData['access_trial_time_value'] * 365;
									break;
							}
						} else if ($levelData['access_trial_type']==2 && isset($levelData['access_trial_couple_cycles'])
									&& $levelData['access_trial_couple_cycles']!=''){
							switch ($levelData['access_regular_time_type']){
								case 'day':
									$trial_period_days = $levelData['access_regular_time_value'] * $levelData['access_trial_couple_cycles'];
									break;
								case 'week':
									$trial_period_days = $levelData['access_regular_time_value'] * $levelData['access_trial_couple_cycles'] * 7;
									break;
								case 'month':
									$trial_period_days = $levelData['access_regular_time_value'] * $levelData['access_trial_couple_cycles'] * 31;
									break;
								case 'year':
									$trial_period_days = $levelData['access_regular_time_value'] * $levelData['access_trial_couple_cycles'] * 365;
									break;
							}
						}
					}

			$start_time = date('Y-m-d');
			if($trial_period_days > 0){
				$start_time = date('Y-m-d', strtotime("+ ".$trial_period_days." days"));
				 \Ihc_User_Logs::write_log( __('Mollie Payment: Trial Time ends on ', 'ihc') . $start_time, 'payments');
			}


			$amount = number_format((float)$amount, 2, '.', '');

            $customer = $mollie->customers->create([
                "name"    => \Ihc_Db::getUserFulltName($this->attributes['uid']),
                "email"   => \Ihc_Db::user_get_email($this->attributes['uid']),
            ]);

            $payment = $customer->createPayment([
                //"customerId"      => $customer->id,
                "amount" => [
                    "currency"    => $this->currency,
                    "value"       => $amount,
                ],
                "description"     => __('Buy ', 'ihc') . $levelData['label'],
                "redirectUrl"     => $siteUrl,
                "webhookUrl"      => $webhook,
                "metadata" => [
                    "order_id" => @$this->attributes['orderId'],
                ],
                "sequenceType" => \Mollie\Api\Types\SequenceType::SEQUENCETYPE_FIRST,
            ]);

            //file_put_contents( IHC_PATH . 'log.log', serialize( $payment) );

			      $mandate = $customer->createMandate([
               "method" => \Mollie\Api\Types\MandateMethod::DIRECTDEBIT,
               "consumerName" => \Ihc_Db::getUserFulltName($this->attributes['uid']),
               "consumerAccount" => "NL55INGB0000000000", /// 'NL55INGB0000000000'
       		  ]);

            $subscriptionObject = $customer->createSubscription([
                "amount"      => [
                    "currency"    => $this->currency,
                    "value"       => $amount,
                ],
                "times"       => $recurringLimit,
                "startDate"   => $start_time,
				        "interval"    => $interval,
                "description" => __('Buy ', 'ihc') . $levelData['label'],
                "webhookUrl"  => $webhook,
                "method"      => NULL,
            ]);
            $subscriptionData = [
                'subscriptionId'    => $subscriptionObject->id,
                'customerId'        => $subscriptionObject->customerId,
            ];
        } else {
            /// SINGLE payment_type
            if ($couponData){
      				$amount = ihc_coupon_return_price_after_decrease($amount, $couponData, TRUE, $this->attributes['uid'], $this->attributes['lid']);
      			}

            /// TAXES
            $levelData['price'] = $this->addTaxes($levelData['price']);

            /*************************** DYNAMIC PRICE ***************************/
            if (ihc_is_magic_feat_active('level_dynamic_price') && isset($this->attributes['ihc_dynamic_price'])){
              $temp_amount = $this->attributes['ihc_dynamic_price'];
              if (ihc_check_dynamic_price_from_user($this->attributes['lid'], $temp_amount)){
                $amount = $temp_amount;
                \Ihc_User_Logs::write_log( __('Mollie Payment: Dynamic price on - Amount is set by the user @ ', 'ihc') . $amount . $this->currency, 'payments');
              }
            }
            /**************************** DYNAMIC PRICE ***************************/

            $amount = number_format((float)$amount, 2, '.', '');
            $payment = $mollie->payments->create([
                "amount" => [
                    "currency"    => $this->currency,
                    "value"       => $amount,
                ],
                "description"     => __('Buy ', 'ihc') . $levelData['label'],
                "redirectUrl"     => $siteUrl,
                "webhookUrl"      => $webhook,
                "method"          => \Mollie\Api\Types\PaymentMethod::CREDITCARD,
            ]);
        }

        $paymentId = $payment->id;
        $this->redirectUrl = $payment->getCheckoutUrl();

        $transactionData = array(
                      'lid'                 => $this->attributes['lid'],
                      'uid'                 => $this->attributes['uid'],
                      'ihc_payment_type'    => 'mollie',
                      'amount'              => $amount,
                      'message'             => 'pending',
                      'currency'            => $this->currency,
                      'item_name'           => $levelData['name'],
        );
        if (!empty($subscriptionData)){
            $transactionData = $transactionData + $subscriptionData;
        }
        /// save the transaction without saving the order
        ihc_insert_update_transaction($this->attributes['uid'], $paymentId, $transactionData, true); /// will save the order too

        /// update indeed_members_payments table, add order id
        \Ihc_Db::updateTransactionAddOrderId($paymentId, @$this->attributes['orderId']);

        return $this;
    }

    public function redirect()
    {
        \Ihc_User_Logs::write_log( __('Mollie Payment: Request submited.', 'ihc'), 'payments');
        header( 'location:' . $this->redirectUrl);
        exit();
    }

    public function webhook()
    {
		\Ihc_User_Logs::write_log( __("Mollie Payment Webhook: Start Process.", 'ihc'), 'payments');
        $settings = ihc_return_meta_arr('payment_mollie');
        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($settings['ihc_mollie_api_key']);
        $transactionId = esc_sql($_POST["id"]);
        $payment = $mollie->payments->get( $transactionId );
        $paymentData = ihcGetTransactionDetails($transactionId);

        if ($payment->isPaid() && !$payment->hasRefunds() && !$payment->hasChargebacks()) {
            /// completed
            if (empty($paymentData)){
                return false;
            }

			//debugging stage only
			//\Ihc_User_Logs::write_log( __("Mollie Payment Webhook: Payment:", 'ihc').json_encode($payment), 'payments');
			// \Ihc_User_Logs::write_log( __("Mollie Payment Webhook: Payment Data:", 'ihc').json_encode($paymentData), 'payments');

			end($paymentData['orders']);

		    $orderId = current($paymentData['orders']);

		   //Check Order status to avoid multiple responses received issue.
		   global $wpdb;
		   	$table = $wpdb->prefix . 'ihc_orders';
			$q = $wpdb->prepare("SELECT status, amount_value FROM $table
											WHERE
											id=%d
											ORDER BY create_date DESC
											LIMIT 1
				", $orderId);
			$query_result = $wpdb->get_row($q);
			if (isset($query_result->status) && $query_result->status != 'pending'){
					return false;
			}

		    /// update
            $levelData = ihc_get_level_by_id($paymentData['lid']);//getting details about current level
            ihc_update_user_level_expire($levelData, $paymentData['lid'], $paymentData['uid']);
            ihc_switch_role_for_user($paymentData['uid']);
            $paymentData['message'] = 'success';
            $paymentData['status'] = 'Completed';

			//Forcing for Trial with 0 amount.
			if (isset($query_result->amount_value) && $query_result->amount_value == 0 ){
					$paymentData['amount'] = 0;
			}

            ihc_insert_update_transaction($paymentData['uid'], $transactionId, $paymentData);

            \Ihc_User_Logs::write_log( __("Mollie Payment Webhook: Update user level expire time.", 'ihc'), 'payments');

            //send notification to user
            ihc_send_user_notifications($paymentData['uid'], 'payment', $paymentData['lid']);
            ihc_send_user_notifications($paymentData['uid'], 'admin_user_payment', $paymentData['lid']);//send notification to admin
            do_action( 'ihc_payment_completed', $paymentData['uid'], $paymentData['lid'] );

        } elseif ($payment->isOpen()) {
        } elseif ($payment->isPending()) {
            /// pending
            ihc_delete_user_level_relation($paymentData['lid'], $paymentData['uid']);
        } elseif ($payment->isFailed()) {
            ihc_delete_user_level_relation($paymentData['lid'], $paymentData['uid']);
        } elseif ($payment->isExpired()) {
        } elseif ($payment->isCanceled()) {
            ihc_delete_user_level_relation($paymentData['lid'], $paymentData['uid']);
        } elseif ($payment->hasRefunds()) {
            ihc_delete_user_level_relation($paymentData['lid'], $paymentData['uid']);
        } elseif ($payment->hasChargebacks()) {
        }
    }

    public function cancelSubscription($transactionId='')
    {
        if (empty($transactionId)){
            return false;
        }
        $subscriptionData = \Ihc_Db::mollieGetSubscriptionDataByTransaction($transactionId);
        if (empty($subscriptionData) || empty($subscriptionData['customerId']) || empty($subscriptionData['subscriptionId'])){
            return false;
        }
        $settings = ihc_return_meta_arr('payment_mollie');
        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($settings['ihc_mollie_api_key']);
        $customer = $mollie->customers->get($subscriptionData['customerId']);
        $canceledSubscription = $customer->cancelSubscription($subscriptionData['subscriptionId']);
        return $canceledSubscription;
    }

}
