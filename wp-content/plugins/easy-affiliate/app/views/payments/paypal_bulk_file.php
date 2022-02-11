<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\User;

if(Utils::is_logged_in_and_an_admin()) {
  $filename = date("ymdHis",time()) . '_paypal_bulk_file.txt';
  header("Content-Type: text/plain");
  header("Content-Disposition: attachment; filename=\"$filename\"");
  header("Expires: ".gmdate("D, d M Y H:i:s", mktime(date("H")+2, date("i"), date("s"), date("m"), date("d"), date("Y")))." GMT");
  header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
  header("Cache-Control: no-cache, must-revalidate");
  header("Pragma: no-cache");

  foreach($bulk_totals as $bulk_total) {
    $affiliate = new User($bulk_total->affiliate_id);
    echo $affiliate->paypal_email . "\t" . Utils::format_float( $bulk_total->paid ) . "\t{$options->currency_code}\t" . $bulk_total->affiliate_id . "\tYour {$blogname} Affiliate Commission Payment\n";
  }
}
else {
  header("Location: " . $blogurl);
}
