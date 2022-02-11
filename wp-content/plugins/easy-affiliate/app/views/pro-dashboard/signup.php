<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use EasyAffiliate\Controllers\SignupCtrl;
use EasyAffiliate\Helpers\DashboardHelper;

echo DashboardHelper::get_pro_header();
?>
<div class="esaf-pro-dashboard-signup">
  <div class="esaf-pro-dashboard-signup-content">
    <h1><?php esc_html_e('Affiliate Signup', 'easy-affiliate'); ?></h1>
    <?php
      while(have_posts()) {
        the_post();
        the_content();
      }
    ?>
  </div>
</div>
<?php echo DashboardHelper::get_pro_footer(); ?>
