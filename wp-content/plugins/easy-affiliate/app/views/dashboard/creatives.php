<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\CreativesHelper;
?>
<h3><?php esc_html_e('Links & Banners', 'easy-affiliate'); ?></h3>
<?php
  CreativesHelper::dashboard_sub_nav();
  require ESAF_VIEWS_PATH . "/dashboard/creatives/{$view}.php";
?>
