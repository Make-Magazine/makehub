<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\ReportsHelper;
/** @var \DateTimeImmutable $start */
/** @var \DateTimeImmutable $end */
?>
<div class="wrap">
  <div id="esaf-reports-affiliate-stats" data-stats="<?php echo esc_attr(wp_json_encode($stats)); ?>" data-start="<?php echo esc_attr($start->format('Y-m-d')); ?>" data-end="<?php echo esc_attr($end->format('Y-m-d')); ?>">
    <div class="esaf-reports-affiliate-stats-chart-area">
      <div class="esaf-reports-affiliate-stats-header">
        <div class="esaf-reports-affiliate-stats-title"><?php esc_html_e('Affiliate Stats', 'easy-affiliate'); ?></div>
        <div class="esaf-reports-affiliate-stats-legend">
          <div class="esaf-reports-affiliate-stats-legend-clicks"><?php esc_html_e('Clicks', 'easy-affiliate'); ?></div>
          <div class="esaf-reports-affiliate-stats-legend-uniques"><?php esc_html_e('Uniques', 'easy-affiliate'); ?></div>
          <div class="esaf-reports-affiliate-stats-legend-sales"><?php esc_html_e('Sales', 'easy-affiliate'); ?></div>
        </div>
        <div id="esaf-reports-date-range"><span id="esaf-reports-date-range-current"></span><i class="ea-icon ea-icon-angle-down"></i></div>
      </div>
      <div id="esaf-reports-affiliate-stats-chart">
        <canvas id="esaf-reports-affiliate-stats-chart-canvas"></canvas>
      </div>
    </div>
    <div id="esaf-reports-affiliate-stats-table">
      <?php echo ReportsHelper::get_affiliate_stats_table_html($stats, $start, $end); ?>
    </div>
  </div>
</div>
