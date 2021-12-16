<?php
namespace Indeed\Ihc;
/**
 * @since version 10.3
 */
class ProrateMembership
{
    /**
     * @var bool
     */
    private $resetInterval                          = false;

    /**
     * @param none
     * @return none
     */
    public function __construct()
    {
        // put here the enable condition
        return;
        if ( !get_option( 'ihc_prorate_subscription_enabled', 0 ) ){
            return;
        }

        // settings
        $this->resetInterval = get_option( 'ihc_prorate_subscription_reset_billing_period', 0 );

        // filters
        add_filter( 'ihc_filter_prepare_payment_level_data', [ $this, 'modifyLevelData' ], 1, 3 );
    }

    /**
     * @param array
     * @param array
     * @param string
     * @return array
     */
    public function modifyLevelData( $levelData=[], $inputData=[], $paymentType='' )
    {
        if ( $paymentType === '' || $paymentType !== 'stripe_connect' ){
            // available only in stripe connect
            return $levelData;
        }
        if ( empty( $inputData['uid'] ) ){
            // not available for register
            return $levelData;
        }
        // get last order for this subscription
        $oldLid = \Indeed\Ihc\UserSubscriptions::getLastForUid( $inputData['uid'], $levelData['id'] );

        if ( $oldLid === false ){
            // no membership for this user. out
            return $levelData;
        }

        $orderId = \Ihc_Db::getLastOrderIdByUserAndLevel( $inputData['uid'], $oldLid );
        if ( $orderId === null || $orderId === false ){
            return $levelData;
        }

        // get amount
        $ordersMeta = new \Indeed\Ihc\Db\OrderMeta();
        $amount = $ordersMeta->get( $orderId, 'base_price' );

        // get amount for the new subscription
        $newAmount = $levelData['access_trial_price'] === false || $levelData['access_trial_price'] === '' ? $levelData['price'] : $levelData['access_trial_price'];
        $newAmount = (float)$newAmount;

        // get start time & end time
        $subscriptionTime = \Indeed\Ihc\UserSubscriptions::getOne( $inputData['uid'], $oldLid );
        if ( !isset( $subscriptionTime['update_time'] ) && !isset( $subscriptionTime['expire_time'] ) ){
            return $levelData;
        }

        // calculate time remaining
        $currentTime = indeed_get_unixtimestamp_with_timezone();
        $expireTime = strtotime( $subscriptionTime['expire_time'] );
        $updateTime = strtotime( $subscriptionTime['update_time'] );


        $subscriptionTimeLeft = $expireTime - $currentTime;

        if ( $subscriptionTimeLeft > 3600 ){
            $subscriptionTimeLeftInHours = $subscriptionTimeLeft / 3600;
        } else {
            $subscriptionTimeLeftInHours = 0;
        }

        $initialTimeNewSubscription = $subscriptionTimeLeftInHours / 24; // in days
        $initialTimeNewSubscription = (int)$initialTimeNewSubscription;

        // if current subscription is expired
        if ( $subscriptionTimeLeftInHours === 0 || $subscriptionTimeLeftInHours < 0 ){
            //dd($levelData);
            return $levelData;
        }

        // calculate percentage left from this subscription
        if ( $expireTime > $updateTime ){
            $percentageLeft = ((int)$expireTime - (int)$currentTime) * 100 / ((int)$expireTime - (int)$updateTime);
        } else {
            $percentageLeft = 0;
        }

        // calculate the amount left of current subscription
        $amountLeft = $amount * $percentageLeft / 100;
        $amountLeft = round( $amountLeft, 2 );
        $newAmount = $newAmount - $amountLeft;
        if ( $newAmount <=0 ){
            $newAmount = 0;
        }

        /// limited, lifetime or date range
        if ( $levelData['access_type'] === 'unlimited' || $levelData['access_type'] === 'limited' || $levelData['access_type'] === 'date_interval' ){
            $levelData['price'] = $newAmount;
            $levelData['upgrade_from'] = $oldLid;
            return $levelData;
        }

        // recurring
        if ( $this->resetInterval ){ //////////////////////// need to complete this
            // Reset the subscription interval
            // calculate the amount to pay for the new subscription
            $levelData['access_trial_price'] = round( $newAmount, 2 );
            $levelData['access_trial_type'] = 2; // couple of cycles
            $levelData['access_trial_couple_cycles'] = 1;
            $levelData['upgrade_from'] = $oldLid;
        } else {
            // no Reset
            $intervalType = isset( $levelData['access_trial_time_type'] ) ? $levelData['access_trial_time_type'] : $levelData['access_regular_time_type'];
            $intervalValue = isset( $levelData['access_trial_time_value'] ) ? $levelData['access_trial_time_value'] : $levelData['access_regular_time_value'];

            switch ( $intervalType ) {
              case 'D':
                $intervalType = 'day';
                break;
              case 'W':
                $intervalType = 'week';
                break;
              case 'M':
                $intervalType = 'month';
                break;
              case 'Y':
                $intervalType = 'year';
                break;
            }

            $featureExpireTime = strtotime('+' . $intervalValue . ' ' . $intervalType );

            if ( $expireTime > $featureExpireTime ){
                // in this case we modify the amount and set the first interation to trial
                $levelData['access_trial_price'] = round( $newAmount, 2 );
                $levelData['access_trial_type'] = 2; // couple of cycles
                $levelData['access_trial_couple_cycles'] = 1;
            } else {
                // we set a certain trial period with a custom amount
                $levelData['access_trial_type'] = 1; // certain period
                $levelData['access_trial_time_value'] = $initialTimeNewSubscription;
                $levelData['access_trial_time_type'] = 'D';
                $levelData['access_trial_price'] = round( $newAmount, 2 );
            }
            $levelData['upgrade_from'] = $oldLid;
        }

        return $levelData;
    }


}
