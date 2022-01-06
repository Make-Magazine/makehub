<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php echo file_get_contents(ESAF_IMAGES_PATH . '/header-logo.svg'); ?>
<p><?php esc_html_e('Your 7 day Affiliate activity:', 'easy-affiliate'); ?></p>
<div id="esaf-admin-dashboard-widget-chart" data-stats="<?php echo esc_attr(wp_json_encode($stats)); ?>" style="height:200px;">
  <canvas id="esaf-admin-dashboard-widget-chart-canvas"></canvas>
</div>
<p><a href="<?php echo esc_url(admin_url('admin.php?page=easy-affiliate')); ?>" class="button"><?php esc_html_e('View More Easy Affiliate Reports', 'easy-affiliate'); ?></a></p>
