<?php
namespace memberpress\gifting\helpers;
use memberpress\gifting\models as models;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class AppHelper {
  public static function human_readable_status( $status, $txn = null ) {
    switch( $status ) {
      case ($txn && $txn->status == \MeprTransaction::$refunded_str):
        return __('Invalid', 'memberpress-gifting');
      case models\Gift::$claimed_str:
        return __('Claimed','memberpress-gifting');
      case models\Gift::$unclaimed_str:
        return __('Unclaimed','memberpress-gifting');
      default:
        return __('Unknown','memberpress-gifting');
    }
  }


  public static function get_class_namespace($namespace, $class){
    return $namespace.'\\'.$class;
  }

} //End class

