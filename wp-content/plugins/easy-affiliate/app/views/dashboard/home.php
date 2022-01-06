<?php
if( ! defined('ABSPATH')) {
  die('You are not allowed to call this page directly.');
}

use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Helpers\CreativesHelper;
use EasyAffiliate\Lib\Utils;

if ($affiliate->show_showcase_url()) {
  CreativesHelper::show_showcase_url($showcase_url, true);
}

if($affiliate->show_dashboard_my_affiliate_link()) {
  CreativesHelper::dashboard_my_affiliate_link($default_affiliate_url, true);
}
?>
<h3><?php esc_html_e('My Affiliate Dashboard', 'easy-affiliate'); ?></h3>
<?php do_action('esaf-dashboard-home-top', $affiliate_id, $overall_stats); ?>
<div class="esaf-affiliate-dashboard-custom-message">
  <?php echo do_shortcode(shortcode_unautop(wpautop($options->custom_message))); ?>
</div>
<div class="esaf-affiliate-dashboard-home-box-columns">
  <div class="esaf-affiliate-dashboard-home-box-column">
    <div class="esaf-affiliate-dashboard-home-box">
      <div class="esaf-affiliate-dashboard-home-box-label"><?php esc_html_e('Current Balance', 'easy-affiliate'); ?></div>
      <div class="esaf-affiliate-dashboard-home-box-amount"><?php echo esc_html(AppHelper::format_currency($owed)); ?></div>
    </div>
  </div>
  <div class="esaf-affiliate-dashboard-home-box-column">
    <div class="esaf-affiliate-dashboard-home-box">
      <div class="esaf-affiliate-dashboard-home-box-label"><?php esc_html_e('Estimated Next Payout', 'easy-affiliate'); ?></div>
      <div class="esaf-affiliate-dashboard-home-box-amount"><?php echo esc_html(AppHelper::format_currency($estimated_next_payout)); ?></div>
    </div>
  </div>
  <div class="esaf-affiliate-dashboard-home-box-column">
    <div class="esaf-affiliate-dashboard-home-box">
      <a href="<?php echo esc_url(Utils::dashboard_url(['action' => 'creatives', 'view' => 'custom-links'])); ?>"><?php esc_html_e('Generate Custom Link', 'easy-affiliate'); ?></a>
    </div>
  </div>
</div>
<div class="esaf-affiliate-dashboard-home-content">
  <div class="esaf-affiliate-dashboard-home-stats">
    <div class="esaf-affiliate-dashboard-home-stat">
      <?php echo file_get_contents(ESAF_IMAGES_PATH . '/clicks.svg'); ?>
      <span class="esaf-affiliate-dashboard-home-stat-label"><?php esc_html_e('Clicks', 'easy-affiliate'); ?></span>
      <span class="esaf-affiliate-dashboard-home-stat-value"><?php echo esc_html($overall_stats['clicks']); ?></span>
    </div>
    <div class="esaf-affiliate-dashboard-home-stat">
      <?php echo file_get_contents(ESAF_IMAGES_PATH . '/conversions.svg'); ?>
      <span class="esaf-affiliate-dashboard-home-stat-label"><?php esc_html_e('Conversions', 'easy-affiliate'); ?></span>
      <span class="esaf-affiliate-dashboard-home-stat-value"><?php echo esc_html($overall_stats['transactions']); ?></span>
    </div>
    <div class="esaf-affiliate-dashboard-home-stat">
      <?php echo file_get_contents(ESAF_IMAGES_PATH . '/revenue.svg'); ?>
      <span class="esaf-affiliate-dashboard-home-stat-label"><?php esc_html_e('Revenue', 'easy-affiliate'); ?></span>
      <span class="esaf-affiliate-dashboard-home-stat-value"><?php echo esc_html(AppHelper::format_currency($overall_stats['total'])); ?></span>
    </div>
    <?php if(apply_filters('esaf_dashboard_display_commission_rate', true)) : ?>
      <div class="esaf-affiliate-dashboard-home-stat">
        <?php echo file_get_contents(ESAF_IMAGES_PATH . '/dashboard-payments.svg'); ?>
        <span class="esaf-affiliate-dashboard-home-stat-label"><?php esc_html_e('Commission Rate', 'easy-affiliate'); ?></span>
        <span class="esaf-affiliate-dashboard-home-stat-value"><?php echo esc_html($overall_stats['commission_rate']); ?></span>
      </div>
    <?php endif; ?>
    <div class="esaf-affiliate-dashboard-home-stat">
      <?php echo file_get_contents(ESAF_IMAGES_PATH . '/commissions.svg'); ?>
      <span class="esaf-affiliate-dashboard-home-stat-label"><?php esc_html_e('Commissions', 'easy-affiliate'); ?></span>
      <span class="esaf-affiliate-dashboard-home-stat-value"><?php echo esc_html(AppHelper::format_currency($overall_stats['commission'])); ?></span>
    </div>
    <div class="esaf-affiliate-dashboard-home-stat">
      <?php echo file_get_contents(ESAF_IMAGES_PATH . '/voids.svg'); ?>
      <span class="esaf-affiliate-dashboard-home-stat-label"><?php esc_html_e('Voids', 'easy-affiliate'); ?></span>
      <span class="esaf-affiliate-dashboard-home-stat-value"><?php echo esc_html(AppHelper::format_currency($overall_stats['refund'])); ?></span>
    </div>
    <div class="esaf-affiliate-dashboard-home-stat">
      <?php echo file_get_contents(ESAF_IMAGES_PATH . '/epc.svg'); ?>
      <span class="esaf-affiliate-dashboard-home-stat-label"><?php esc_html_e('Earns Per Click (EPC)', 'easy-affiliate'); ?></span>
      <span class="esaf-affiliate-dashboard-home-stat-value"><?php echo esc_html(AppHelper::format_currency($overall_stats['epc'])); ?></span>
    </div>
  </div>
  <div class="esaf-affiliate-dashboard-home-charts">
    <div id="esaf-affiliate-dashboard-chart-date-range">
      <span id="esaf-affiliate-dashboard-chart-date-range-text"></span>
      <span class="ea-icon ea-icon-angle-down"></span>
    </div>
    <div class="esaf-affiliate-dashboard-home-chart">
      <h4><?php esc_html_e('Clicks', 'easy-affiliate'); ?></h4>
      <div class="esaf-affiliate-dashboard-chart esaf-affiliate-dashboard-clicks-chart">
        <canvas id="esaf-affiliate-dashboard-clicks-chart-canvas"></canvas>
      </div>
    </div>
    <div class="esaf-affiliate-dashboard-home-chart">
      <h4><?php esc_html_e('Conversions', 'easy-affiliate'); ?></h4>
      <div class="esaf-affiliate-dashboard-chart esaf-affiliate-dashboard-conversions-chart">
        <canvas id="esaf-affiliate-dashboard-conversions-chart-canvas"></canvas>
      </div>
    </div>
  </div>
</div>

<?php do_action('esaf-dashboard-home-bottom', $affiliate_id, $overall_stats); ?>

<script>
  jQuery(document).ready(function ($) {
    var chart1 = null, chart2 = null;
    var initial_start_date = moment().subtract(29, 'days').format('YYYY-MM-DD').toString();
    var initial_to_date = moment().format('YYYY-MM-DD').toString();
    var init_charts = function (data) {
      var chartData1 = data.clicks && data.clicks.length > 0 ? data.clicks : [];
      var chartData2 = data.conversions && data.conversions.length > 0 ? data.conversions : [];

      if(chart1 === null && chart2 === null) {
        var ctx = document.getElementById('esaf-affiliate-dashboard-clicks-chart-canvas');
        var ctx2 = document.getElementById('esaf-affiliate-dashboard-conversions-chart-canvas');

        var cfg = {
          data: {
            datasets: [{
              backgroundColor: '#6fabce',
              borderColor: '#6fabce',
              data: chartData1,
              type: 'line',
              pointRadius: 4,
              fill: false,
              lineTension: 0.01,
              borderWidth: 4,
              label: '<?php echo esc_js(__('Clicks', 'easy-affiliate')); ?>'
            }]
          },
          options: {
            maintainAspectRatio: false,
            legend: {
              display: false
            },
            animation: {
              duration: 0
            },
            scales: {
              xAxes: [{
                type: 'time',
                distribution: 'series',
                bounds: 'ticks',
                offset: true,
                time: {
                  unit: 'day',
                  tooltipFormat: 'll'
                },
                ticks: {
                  major: {
                    enabled: true,
                    fontStyle: 'bold'
                  },
                  source: 'data',
                  autoSkip: true,
                  autoSkipPadding: 50
                },
              }],
              yAxes: [{
                ticks: {
                  beginAtZero: true,
                  autoSkip: true,
                  autoSkipPadding: 50,
                  userCallback: function (label) {
                    // when the floored value is the same as the value we have a whole number
                    if(Math.floor(label) === label) {
                      return label;
                    }
                  }
                },
                gridLines: {
                  drawBorder: false
                },
                scaleLabel: {
                  display: false
                }
              }]
            },
            tooltips: {
              intersect: false,
              mode: 'index'
            }
          }
        };

        var cfg2 = {};
        Object.assign(cfg2, cfg);
        cfg2.data = {
          datasets: [{
            data: chartData2,
            backgroundColor: '#f16f78',
            borderColor: '#f16f78',
            type: 'line',
            pointRadius: 4,
            fill: false,
            lineTension: 0.01,
            borderWidth: 4,
            label: '<?php echo esc_js(__('Conversions', 'easy-affiliate')); ?>'
          }]
        };

        chart1 = new Chart(ctx, cfg);
        chart2 = new Chart(ctx2, cfg2);
      }
      else {
        var dataset1 = chart1.config.data.datasets[0];
        var dataset2 = chart2.config.data.datasets[0];
        var point_radius = chartData1.length > 60 ? 1 : 4;
        var border_width = chartData1.length > 60 ? 1 : 4;
        dataset1.data = chartData1;
        dataset2.data = chartData2;
        dataset1.pointRadius = point_radius;
        dataset2.pointRadius = point_radius;
        dataset1.borderWidth = border_width;
        dataset2.borderWidth = border_width;
        chart1.update();
        chart2.update();
      }
    };

    var xhrUrl = '<?php echo admin_url('admin-ajax.php', 'relative'); ?>';

    $.post(xhrUrl, {
      action: 'esaf_get_clicks_and_conversions_statistic',
      start: initial_start_date,
      end: initial_to_date
    }, function (data) {
      init_charts(data.data);
    }, "json");

    var start_date = moment().subtract(29, 'days'),
      end_date = moment();

    $('#esaf-affiliate-dashboard-chart-date-range-text').text(start_date.format('MMMM D, YYYY') + ' - ' + end_date.format('MMMM D, YYYY'));

    $('#esaf-affiliate-dashboard-chart-date-range').daterangepicker({
      ranges: {
        '<?php esc_html_e('Last 30 Days', 'easy-affiliate'); ?>': [moment().subtract(29, 'days'), moment()],
        '<?php esc_html_e('Last Month', 'easy-affiliate'); ?>': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        '<?php echo esc_html(sprintf(__('%d Months Ago', 'easy-affiliate'), '2')); ?>': [moment().subtract(2, 'month').startOf('month'), moment().subtract(2, 'month').endOf('month')],
        '<?php echo esc_html(sprintf(__('%d Months Ago', 'easy-affiliate'), '3')); ?>': [moment().subtract(3, 'month').startOf('month'), moment().subtract(3, 'month').endOf('month')],
        '<?php echo esc_html(sprintf(__('Last %d Months', 'easy-affiliate'), '6')); ?>': [moment().subtract(6, 'month'), moment()],
        '<?php esc_html_e('This Year', 'easy-affiliate'); ?>': [moment().startOf('year'), moment()],
        '<?php esc_html_e('Last Year', 'easy-affiliate'); ?>': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
      },
      startDate: start_date,
      endDate: end_date,
      maxDate: moment(),
      alwaysShowCalendars: true,
      autoApply: true,
      autoUpdateInput: true,
      showCustomRangeLabel: false,
      opens: 'left'
    }).on('apply.daterangepicker', function (ev, picker) {
      $('#esaf-affiliate-dashboard-chart-date-range-text').text(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));

      $.post(xhrUrl, {
        action: 'esaf_get_clicks_and_conversions_statistic',
        start: picker.startDate.format('YYYY-MM-DD'),
        end: picker.endDate.format('YYYY-MM-DD')
      }, function (data) {
        init_charts(data.data);
      }, "json");
    });
  })
  ;
</script>
