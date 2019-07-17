<?php
namespace Indeed\Ihc\PaymentGateways;

class StripeCheckoutV2 extends \Indeed\Ihc\PaymentGateways\PaymentAbstract
{
    protected $attributes       = array();
    protected $redirectUrl      = '';
    protected $abort            = false;
    protected $paymentTypeLabel = 'Stripe Checkout V2 Payment';
    protected $currency         = '';
    protected $options          = array();
    private   $sessionId        = '';
    private   $levelData        = array();
    private   $paymentIntent    = '';
    private   $amount           = 0;

    public function __construct()
    {
        include IHC_PATH . 'classes/PaymentGateways/stripe_checkout_v2/vendor/autoload.php';
        $this->currency = get_option('ihc_currency');
        $this->options = ihc_return_meta_arr('payment_stripe_checkout_v2');//getting metas
        $this->siteUrl = get_option( 'siteurl' );
    }

    public function doPayment()
    {
        \Stripe\Stripe::setApiKey( $this->options['ihc_stripe_checkout_v2_secret_key'] );
        $levels = get_option('ihc_levels');
        $this->levelData = $levels[$this->attributes['lid']];

        if ( isset($this->levelData['access_type']) && $this->levelData['access_type']=='regular_period' ){
            \Ihc_User_Logs::write_log( $this->paymentTypeLabel . __( ': Recurrence payment set.', 'ihc' ), 'payments');
            $this->sessionId = $this->getSessionIdForRecurringPayment();
        } else {
            \Ihc_User_Logs::write_log( $this->paymentTypeLabel . __( ': single payment set.', 'ihc' ), 'payments');
            $this->sessionId = $this->getSessionIdForSinglePayment();
        }

        /// save transaction
        $transactionData = array(
                      'lid'                 => $this->attributes['lid'],
                      'uid'                 => $this->attributes['uid'],
                      'ihc_payment_type'    => 'stripe_checkout_v2',
                      'amount'              => $this->amount,
                      //'message'             => 'pending',
                      'currency'            => $this->currency,
                      'item_name'           => $this->levelData['name'],
                      'payment_status'      => 'pending',
        );
        /// save the transaction without saving the order
        ihc_insert_update_transaction( $this->attributes['uid'], $this->paymentIntent, $transactionData ); /// will save the order too
        //file_put_contents( IHC_PATH . 'log123.log', 'txn is  ' . $this->paymentIntent . ', orderid is ' . $this->attributes['orderId'] );
        //\Ihc_Db::updateTransactionAddOrderId( $this->paymentIntent, @$this->attributes['orderId'] );

        return $this;
    }

    private function getSessionIdForSinglePayment()
    {
        $couponData = $this->getCouponData();

        $this->amount = $this->levelData['price'];

        /// coupon
        if ( $couponData ){
          $this->amount = ihc_coupon_return_price_after_decrease( $this->amount, $couponData, true, $this->attributes['uid'], $this->attributes['lid'] );
        }

        /// coupon if its case
        //if ( $couponData ){
        //    $this->amount = ihc_coupon_return_price_after_decrease( $this->amount, $couponData, true, $this->attributes['uid'], $this->attributes['lid'] );
        //}

        /// TAXES
        $this->amount = $this->addTaxes( $this->levelData['price'] );

        /// dynamic price
        $this->amount = $this->applyDynamicPrice( $this->amount );

        $amount = $this->amount * 100;
        if ( $amount> 0 && $amount<50){
            $amount = 50;// 0.50 cents minimum amount for stripe transactions
        }

        $session = \Stripe\Checkout\Session::create([
          'payment_method_types'    => ['card'],
          "line_items" => [[
                  "name"        => $this->levelData['label'],
                  "description" => $this->levelData['description'],
                  "amount"      => $amount,
                  "currency"    => $this->currency,
                  "quantity"    => 1
          ]],
          'success_url'               => $this->siteUrl,
          'cancel_url'                => $this->siteUrl,
          'client_reference_id'       => $this->attributes['uid'] . '_' . $this->attributes['lid'], // {uid}_{lid}
        ]);

        /// save payment intent
        $this->paymentIntent = isset( $session->payment_intent ) ? $session->payment_intent : '';
        //file_put_contents( IHC_PATH.'log.log', $this->paymentIntent, FILE_APPEND );

        return isset( $session->id ) ? $session->id : 0;
    }

    private function getSessionIdForRecurringPayment()
    {
        $couponData = $this->getCouponData();
        $this->amount = $this->levelData['price'];
        /// TAXES
        $this->amount = $this->addTaxes( $this->levelData['price'] );

        /// dynamic price
        $this->amount = $this->applyDynamicPrice( $this->amount );

        \Ihc_User_Logs::write_log( __('Stripe Payment: Recurrence payment set.', 'ihc'), 'payments');
        switch ($this->levelData['access_regular_time_type']){
          case 'D':
            $this->levelData['access_regular_time_type'] = 'day';
            break;
          case 'W':
            $this->levelData['access_regular_time_type'] = 'week';
            break;
          case 'M':
            $this->levelData['access_regular_time_type'] = 'month';
            break;
          case 'Y':
            $this->levelData['access_regular_time_type'] = 'year';
            break;
        }

        ///trial
        $trial_period_days = 0;
        if (!empty($this->levelData['access_trial_type'])){
          if ($this->levelData['access_trial_type']==1 && isset($this->levelData['access_trial_time_value'])
              && $this->levelData['access_trial_time_value'] !=''){
            switch ($this->levelData['access_trial_time_type']){
              case 'D':
                $trial_period_days = $this->levelData['access_trial_time_value'];
                break;
              case 'W':
                $trial_period_days = $this->levelData['access_trial_time_value'] * 7;
                break;
              case 'M':
                $trial_period_days = $this->levelData['access_trial_time_value'] * 31;
                break;
              case 'Y':
                $trial_period_days = $this->levelData['access_trial_time_value'] * 365;
                break;
            }
          } else if ($this->levelData['access_trial_type']==2 && isset($this->levelData['access_trial_couple_cycles'])
                && $this->levelData['access_trial_couple_cycles']!=''){
            switch ($this->levelData['access_regular_time_type']){
              case 'day':
                $trial_period_days = $this->levelData['access_regular_time_value'] * $this->levelData['access_trial_couple_cycles'];
                break;
              case 'week':
                $trial_period_days = $this->levelData['access_regular_time_value'] * $this->levelData['access_trial_couple_cycles'] * 7;
                break;
              case 'month':
                $trial_period_days = $this->levelData['access_regular_time_value'] * $this->levelData['access_trial_couple_cycles'] * 31;
                break;
              case 'year':
                $trial_period_days = $this->levelData['access_regular_time_value'] * $this->levelData['access_trial_couple_cycles'] * 365;
                break;
            }
          }
        }
        //end of trial

        //v.7.1 - Recurring Level with Coupon 100% => 1 free Trial cycle
        if ( $trial_period_days == 0 ){
          if ( isset( $couponData ) ){
            $discounted_value = ihc_get_discount_value( $this->amount, $couponData );

            if ( $this->amount - $discounted_value == 0){
              $order_extra_metas['discount_value'] = $discounted_value;
              $order_extra_metas['coupon_used'] = $this->attributes['ihc_coupon'];
              switch ($this->level_data['access_regular_time_type']){
                case 'day':
                  $trial_period_days = $this->levelData['access_regular_time_value'];
                  break;
                case 'week':
                  $trial_period_days = $this->levelData['access_regular_time_value']  * 7;
                  break;
                case 'month':
                  $trial_period_days = $this->levelData['access_regular_time_value'] * 31;
                  break;
                case 'year':
                  $trial_period_days = $this->levelData['access_regular_time_value']  * 365;
                  break;
              }
              \Ihc_User_Logs::write_log( __('Stripe Payment: the user used the following coupon: ', 'ihc') . $this->attributes['ihc_coupon'], 'payments');				}
          }
        }


        $amount = $this->amount * 100;
        if ( $amount> 0 && $amount<50){
            $amount = 50;// 0.50 cents minimum amount for stripe transactions
        }

        $ihcPlanCode = $this->attributes['uid'] . '_' . $this->attributes['lid'] . '_' . time();
        $plan = array(
            "amount"          => $amount,
            "interval_count"  => $this->levelData['access_regular_time_value'],
            "interval"        => $this->levelData['access_regular_time_type'],
            "product"         => array(
                                  "name"    => "Level " . $this->levelData['name'] . ' for ' . \Ihc_Db::get_username_by_wpuid( $this->attributes['uid'] ),
                                  'type'    => 'service',
            ),
            "currency"        => $this->currency,
            "id"              => $ihcPlanCode,
        );
        $trial_end = "now";
        if ( !empty( $trial_period_days ) ){
            \Ihc_User_Logs::write_log( __('Stripe Payment: Trial time value set @ ', 'ihc') . $trial_period_days . __(' days.', 'ihc'), 'payments');
            $trial_end = strtotime("+ ".$trial_period_days." days");
        }

        $return_data_plan = \Stripe\Plan::create($plan);

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types'    => ['card'],
            'subscription_data'         => [
              "items" => [[
                  'plan'        => $ihcPlanCode, /// ID of plan
                  'quantity'    => 1,
              ]],
              'metadata'  => [
                  'uid'        => $this->attributes['uid'],
                  'lid'        => $this->attributes['lid'],
              ]
            ],
            'success_url'               => $this->siteUrl,
            'cancel_url'                => $this->siteUrl,
            'client_reference_id'       => $this->attributes['orderId'], // {uid}_{lid}
        ]);

        /// save payment intent
        // $this->paymentIntent = isset( $session->payment_intent ) ? $session->payment_intent : '';
        $this->paymentIntent = $this->attributes['orderId'];

        return isset( $session->id ) ? $session->id : 0;

    }

    private function getCouponData()
    {
        $couponData = array();
        if ( !empty( $this->attributes['ihc_coupon'] ) ){
            $couponData = ihc_check_coupon( $this->attributes['ihc_coupon'], $this->attributes['lid'] );
            \Ihc_User_Logs::write_log( $this->paymentTypeLabel . __( ': the user used the following coupon: ', 'ihc' ) . $this->attributes['ihc_coupon'], 'payments');
        }
        return $couponData;
    }


    public function redirect()
    {
        if ( $this->sessionId ){
            /// redirect
            \Ihc_User_Logs::write_log( $this->paymentTypeLabel . __(': Request submited.', 'ihc'), 'payments');
            $redirect = IHC_URL . 'public/stripe_checkout_v2_payment.php?sessionId=' . $this->sessionId . '&key=' . $this->options['ihc_stripe_checkout_v2_publishable_key'];
        } else {
            /// go home
            $redirect = $this->siteUrl;
        }
        header( 'location:' . $redirect );
        exit();
    }

    public function webhook()
    {
        $timestamp = time();
        $response = @file_get_contents( 'php://input' );

        if ( !$response ){
            exit();
        }

        $responseData = json_decode( $response, true );

        if ( !$responseData ){
            exit;
        }

        if ( empty( $responseData['data']['object']['payment_intent'] ) || empty( $responseData['type'] ) || $responseData['type'] != 'charge.succeeded' ){
            exit;
        }

        $transactionIdentificator = $responseData['data']['object']['payment_intent'];

        $dataFromDb = ihcGetTransactionDetails( $transactionIdentificator );

        if ( !$dataFromDb ){
            $this->approveRecurringPAymentLevel();
        } else {
            $this->approveSinglePaymentLevel( $responseData, $transactionIdentificator, $dataFromDb );
        }


    }

    private function approveSinglePaymentLevel( $transactionDetails=[], $transactionIdentificator='', $data=[] )
    {

         //Check Order status to avoid multiple responses received issue.
        end($data['orders']);
        $orderId = current($data['orders']);
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
            exit();
        }

        $levels = get_option('ihc_levels');
        $levelData = $levels[$data['lid']];
        $currentTransactionAmount = $transactionDetails['data']['object']['amount']/100;
        unset($transactionDetails['data']);
        sleep(10);

        ihc_update_user_level_expire( $levelData, $data['lid'], $data['uid'] );
        \Ihc_User_Logs::write_log( __("Stripe Payment Webhook: Updated user (".$data['uid'].") level (".$data['lid'].") expire time.", 'ihc'), 'payments');
        ihc_switch_role_for_user($data['uid']);

        $dataDb = $data;//array_merge( $data, $transactionDetails['data']['object'] );
        $dataDb['message'] = 'success';
        $dataDb['status'] = 'Completed';/// this is very important

        if ( $dataDb['amount'] != $currentTransactionAmount || $dataDb['amount'] == 0 || $dataDb['amount'] == NULL ){
            \Ihc_User_Logs::write_log( __('Stripe Payment Webhook: Update the right amount ' . $currentTransactionAmount, 'ihc'), 'payments');
            $dataDb['amount'] = $currentTransactionAmount;
        }


        ihc_insert_update_transaction( $data['uid'], $transactionIdentificator, $dataDb );

        //send notification to user
        ihc_send_user_notifications($data['uid'], 'payment', $data['lid']);
        ihc_send_user_notifications($data['uid'], 'admin_user_payment', $data['lid']);//send notification to admin
        do_action( 'ihc_payment_completed', $data['uid'], $data['lid'] );

        http_response_code(200);
        exit();
    }

    private function approveRecurringPAymentLevel( $transactionDetails=[] )
    {
        //

        if ( empty( $transactionDetails['data']['object']['payment_intent'] ) ){
            exit();
        }
        $paymentIntentData = \Stripe\PaymentIntent::retrieve( $transactionDetails['data']['object']['payment_intent'] );
        if ( empty( $paymentIntentData ) ){ /// && $paymentIntentData->status == 'succeeded'
            exit();
        }

        // file_put_contents( IHC_PATH . 'log.log', serialize( $transactionData ) . '>>>>>>>>>>>>>>>>>>' . 'status is ' . $paymentIntentData->status, FILE_APPEND );

          $transactionIdentificator = $transactionDetails['data']['object']['client_reference_id'];

          $data = ihcGetTransactionDetails( $transactionIdentificator );

           //Check Order status to avoid multiple responses received issue.
          end($data['orders']);
          $orderId = current($data['orders']);
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
              exit();
          }

          $levels = get_option('ihc_levels');
          $levelData = $levels[$data['lid']];
          $currentTransactionAmount = $transactionDetails['data']['object']['amount']/100;
          unset($transactionDetails['data']);
          sleep(10);

          ihc_update_user_level_expire( $levelData, $data['lid'], $data['uid'] );
          \Ihc_User_Logs::write_log( __("Stripe Payment Webhook: Updated user (".$data['uid'].") level (".$data['lid'].") expire time.", 'ihc'), 'payments');
          ihc_switch_role_for_user($data['uid']);

          $dataDb = $data;//array_merge( $data, $transactionDetails['data']['object'] );
          $dataDb['message'] = 'success';
          $dataDb['status'] = 'Completed';/// this is very important

          if ( $dataDb['amount'] != $currentTransactionAmount || $dataDb['amount'] == 0 || $dataDb['amount'] == NULL ){
              \Ihc_User_Logs::write_log( __('Stripe Payment Webhook: Update the right amount ' . $currentTransactionAmount, 'ihc'), 'payments');
              $dataDb['amount'] = $currentTransactionAmount;
          }

          ihc_insert_update_transaction( $data['uid'], $transactionIdentificator, $dataDb );

          //send notification to user
          ihc_send_user_notifications($data['uid'], 'payment', $data['lid']);
          ihc_send_user_notifications($data['uid'], 'admin_user_payment', $data['lid']);//send notification to admin
          do_action( 'ihc_payment_completed', $data['uid'], $data['lid'] );

          http_response_code(200);
          exit();
    }

    public function cancelSubscription()
    {

    }

}
