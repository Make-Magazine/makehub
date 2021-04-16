<?php
$accessType = isset( $subscriptionMetas['access_type']  ) ? $subscriptionMetas['access_type']  : $membershipData['access_type'];
$price = isset( $subscriptionMetas['price'] ) ? $subscriptionMetas['price'] : $membershipData['price'];
switch ( $accessType ){
    case 'regular_period':
      $trialPrice = isset( $subscriptionMetas['access_trial_price']  ) ? $subscriptionMetas['access_trial_price']  : $membershipData['access_trial_price'];
      $trialType = isset( $subscriptionMetas['access_trial_type']  ) ? $subscriptionMetas['access_trial_type']  : $membershipData['access_trial_type'];
      $timeType = isset( $subscriptionMetas['access_regular_time_type']  ) ? $subscriptionMetas['access_regular_time_type']  : $membershipData['access_regular_time_type'];
      $timeValue = isset( $subscriptionMetas['access_regular_time_value']  ) ? $subscriptionMetas['access_regular_time_value']  : $membershipData['access_regular_time_value'];
      if ( $trialPrice != '' ){
          //  trial
          if ( $trialType == 1 ){
              // certain period
              $trialTimeValue = isset( $subscriptionMetas['access_trial_time_value']  ) ? $subscriptionMetas['access_trial_time_value']  : $membershipData['access_trial_time_value'];
              $trialTimeType = isset( $subscriptionMetas['access_trial_time_type']  ) ? $subscriptionMetas['access_trial_time_type']  : $membershipData['access_trial_time_type'];
              echo ihc_format_price_and_currency( $currency, $trialPrice ) . __( ' for ', 'ihc' )  . $trialTimeValue . ihcGetTimeTypeByCode( $trialTimeType, $trialTimeType ) .
               __( ' then ', 'ihc' );
          } else {
              // couple of cycles
              echo ihc_format_price_and_currency( $currency, $trialPrice ) . __( ' for ', 'ihc' ) . $trialCycles . __( ' cycles then ', 'ihc' );
          }
      }
          if ( $timeValue == 1 ){
              echo ihc_format_price_and_currency( $currency, $price ) . __( ' every ', 'ihc' ) . ihcGetTimeTypeByCode( $timeType, $timeValue );
          } else {
              echo ihc_format_price_and_currency( $currency, $price ) . __( ' for ', 'ihc' ) . $timeValue . ihcGetTimeTypeByCode( $timeType, $timeValue );
          }

      break;
    case 'unlimited':
      echo ihc_format_price_and_currency( $currency, $price );
      _e( ' for LifeTime.', 'ihc' );
      break;
    case 'limited':
      $timeValue = isset( $subscriptionMetas['access_limited_time_value']  ) ? $subscriptionMetas['access_limited_time_value']  : $membershipData['access_limited_time_value'];
      $timeType = isset( $subscriptionMetas['access_limited_time_type']  ) ? $subscriptionMetas['access_limited_time_type']  : $membershipData['access_limited_time_type'];
      echo ihc_format_price_and_currency( $currency, $price );
      echo __( ' for ', 'ihc' ) . $timeValue . ' ' . ihcGetTimeTypeByCode( $timeType, $timeValue );
      break;
    case 'date_interval':
      $start = isset( $subscriptionMetas['access_interval_start']  ) ? $subscriptionMetas['access_interval_start']  : $membershipData['access_interval_start'];
      $end = isset( $subscriptionMetas['access_interval_end']  ) ? $subscriptionMetas['access_interval_end']  : $membershipData['access_interval_end'];
      echo ihc_format_price_and_currency( $currency, $price );
      _e( ' for period: ', 'ihc' );
      echo ihc_convert_date_time_to_us_format( $start );
      echo ' - ';
      echo ihc_convert_date_time_to_us_format( $end );
      break;
}
