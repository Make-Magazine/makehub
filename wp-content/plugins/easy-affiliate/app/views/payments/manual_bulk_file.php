<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\User;

if(Utils::is_logged_in_and_an_admin()) {
  $filename = date("ymdHis",time()) . '_manual_pay_bulk_file.csv';
  header("Content-Type: text/csv");
  header("Content-Disposition: attachment; filename=\"$filename\"");
  header("Expires: ".gmdate("D, d M Y H:i:s", mktime(date("H")+2, date("i"), date("s"), date("m"), date("d"), date("Y")))." GMT");
  header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
  header("Cache-Control: no-cache, must-revalidate");
  header("Pragma: no-cache");

  echo __('Affiliate ID', 'easy-affiliate') . ',';
  echo __('Affiliate Login', 'easy-affiliate') . ',';
  echo __('Affiliate Email', 'easy-affiliate') . ',';

  if($options->is_payout_method_paypal()) {
    echo __('PayPal Email', 'easy-affiliate') . ',';
  }

  echo __('Payment Amount', 'easy-affiliate');
  echo PHP_EOL;

  if(is_array($bulk_totals)) {
    foreach ($bulk_totals as $bulk_total) {
      $affiliate = new User($bulk_total->affiliate_id);
      echo $affiliate->ID . ',';
      echo $affiliate->user_login . ',';
      echo $affiliate->user_email . ',';

      if($options->is_payout_method_paypal()) {
        echo $affiliate->paypal_email . ',';
      }

      echo Utils::format_float($bulk_total->paid);
      echo PHP_EOL;
    }
  }
}
else {
  header("Location: " . $blogurl);
}
