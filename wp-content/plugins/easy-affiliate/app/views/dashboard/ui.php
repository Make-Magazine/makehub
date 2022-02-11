<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\DashboardHelper;
?>
<div id="esaf-dash-wrapper">
<?php
  // if $show_nav isn't set we'll just show it
  if(!isset($show_nav) || $show_nav) {
    $default_link = false;

    DashboardHelper::nav();
  }

  require ESAF_VIEWS_PATH . "/dashboard/{$action}.php";
?>
</div>
