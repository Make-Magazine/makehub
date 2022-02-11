<?php

namespace EasyAffiliate\Helpers;

use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Click;

class ReportsHelper {
  public static function periods_dropdown($field_name, $curr_period, $onchange='')
  {
    $field_value = (isset($_POST[$field_name])?$_POST[$field_name]:'');

    $periods = ReportsHelper::get_periods();

    rsort($periods);
    ?>
      <select name="<?php echo esc_attr($field_name); ?>" id="<?php echo esc_attr($field_name); ?>" onchange="<?php echo $onchange; ?>" class="wafp-dropdown wafp-periods-dropdown">
      <?php
        foreach($periods as $period)
        {
          $period_time = $period['time'];
          $period_label = $period['label'];
          ?>
          <option value="<?php echo esc_attr($period_time); ?>" <?php echo (((isset($_POST[$field_name]) and $_POST[$field_name] == $curr_period) or (!isset($_POST[$field_name]) and $period_time == $curr_period))?' selected="selected"':''); ?>><?php echo esc_html($period_label); ?>&nbsp;</option>
          <?php
        }
      ?>
      </select>
    <?php
  }

  public static function get_periods()
  {
    $first_click = Click::get_first_click();
    $first_timestamp = ($first_click?$first_click->created_at_ts:time());

    $period = array(date('Y', $first_timestamp),date('n', $first_timestamp));
    $timestamp = mktime(0,0,0,$period[1],1,$period[0]);

    $curr_timestamp = time();
    $curr_period = array(date('Y'),date('n'));

    $periods = array();

    while( $curr_timestamp >= $timestamp ) {
      $periods[] = array( 'time' => $timestamp, 'label' => date('F 01-t, Y', $timestamp));

      if($period[1]==12) {
        $period[1] = 1;
        $period[0]++;
      }
      else
        $period[1]++;

      $timestamp = mktime(0,0,0,$period[1],1,$period[0]);
    }

    return $periods;
  }

  public static function get_affiliate_stats_table_html(array $rows, \DateTimeInterface $start, \DateTimeInterface $end) {
    $clicks_total = 0;
    $uniques_total = 0;
    $transactions_total = 0;
    $commissions_total = 0;
    $corrections_total = 0;
    $totals_total = 0;
    $utc = new \DateTimeZone('UTC');
    $export_url = add_query_arg([
      'action' => 'esaf_report_stats_export_csv',
      '_ajax_nonce' => wp_create_nonce('esaf_report_stats_export_csv'),
      'start' => $start->format('Y-m-d'),
      'end' => $end->format('Y-m-d')
    ], admin_url('admin-ajax.php'));

    ob_start();
    ?>
    <table class="widefat post fixed" cellspacing="0">
      <thead>
        <tr>
          <th scope="col" class="manage-column"><?php esc_html_e('Date', 'easy-affiliate'); ?></th>
          <th scope="col" class="manage-column"><?php esc_html_e('Clicks', 'easy-affiliate'); ?></th>
          <th scope="col" class="manage-column"><?php esc_html_e('Uniques', 'easy-affiliate'); ?></th>
          <th scope="col" class="manage-column"><?php esc_html_e('Transactions', 'easy-affiliate'); ?></th>
          <th scope="col" class="manage-column"><?php esc_html_e('Commissions', 'easy-affiliate'); ?></th>
          <th scope="col" class="manage-column"><?php esc_html_e('Voided', 'easy-affiliate'); ?></th>
          <th scope="col" class="manage-column"><?php esc_html_e('Total', 'easy-affiliate'); ?></th>
          <?php do_action('esaf-admin-stats-table-header'); ?>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach($rows as $row_index => $row) {
          $clicks_total += $row['clicks'];
          $uniques_total += $row['uniques'];
          $transactions_total += $row['transactions'];
          $commissions_total += $row['commissions'];
          $corrections_total += $row['corrections'];
          $totals_total += ((float) $row['commissions'] - (float) $row['corrections']);
          $alternate = ( $row_index % 2 ? '' : 'alternate' );
          ?>
          <tr class="<?php echo esc_attr($alternate); ?>">
            <td><?php echo esc_html(Utils::format_date($row['date'], '', $utc)); ?></td>
            <td><?php echo esc_html($row['clicks']); ?></td>
            <td><?php echo esc_html($row['uniques']); ?></td>
            <td><?php echo esc_html($row['transactions']); ?></td>
            <td><?php echo esc_html(AppHelper::format_currency($row['commissions'])); ?></td>
            <td><?php echo esc_html(AppHelper::format_currency($row['corrections'])); ?></td>
            <td><?php echo esc_html(AppHelper::format_currency($row['total'])); ?></td>
            <?php do_action('esaf-admin-stats-table-row',$row); ?>
          </tr>
        <?php } ?>
      </tbody>
      <tfoot style="text-align: left;">
        <tr>
          <th scope="col" class="manage-column"><?php esc_html_e('Totals', 'easy-affiliate'); ?></th>
          <th scope="col" class="manage-column"><?php echo esc_html($clicks_total); ?></th>
          <th scope="col" class="manage-column"><?php echo esc_html($uniques_total); ?></th>
          <th scope="col" class="manage-column"><?php echo esc_html($transactions_total); ?></th>
          <th scope="col" class="manage-column"><?php echo esc_html(AppHelper::format_currency($commissions_total)); ?></th>
          <th scope="col" class="manage-column"><?php echo esc_html(AppHelper::format_currency($corrections_total)); ?></th>
          <th scope="col" class="manage-column"><?php echo esc_html(AppHelper::format_currency($totals_total)); ?></th>
          <?php do_action('esaf-admin-stats-table-footer'); ?>
        </tr>
      </tfoot>
    </table>
    <a href="<?php echo esc_url($export_url); ?>"><?php echo esc_html(sprintf(_n('Export table as CSV (%s record)', 'Export table as CSV (%s records)', count($rows), 'easy-affiliate'), count($rows))); ?></a>
    <?php
    return ob_get_clean();
  }
}
