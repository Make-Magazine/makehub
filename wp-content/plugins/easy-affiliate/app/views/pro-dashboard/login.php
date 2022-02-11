<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Controllers\LoginCtrl;
use EasyAffiliate\Helpers\DashboardHelper;
echo DashboardHelper::get_pro_header();
?>
<div class="esaf-pro-dashboard-login">
  <div class="esaf-pro-dashboard-login-content">
    <h1><?php esc_html_e('Affiliate Login', 'easy-affiliate'); ?></h1>
    <?php
      while(have_posts()) {
        the_post();
        the_content();
      }
    ?>
  </div>
</div>
<?php echo DashboardHelper::get_pro_footer(); ?>
