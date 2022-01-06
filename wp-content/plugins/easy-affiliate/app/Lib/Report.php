<?php

namespace EasyAffiliate\Lib;

class Report {

  public static function affiliate_frontend_payments($affiliate_id, $page = 1, $perpage = 5) {
    global $wpdb;
    $offset = ((int)$page - 1) * $perpage;
    $db = Db::fetch();

    $select_payments = "SELECT
        round(sum(`amount`), 3) AS `paid`,
        max(pmt.created_at) as `date_value` ,
        YEAR(pmt.created_at)    AS `year`,
        MONTH(pmt.created_at)   AS `month`
      FROM {$db->payments}  pmt
      WHERE pmt.affiliate_id =%d
      GROUP BY `year`, `month`
      ORDER BY `date_value` DESC
      LIMIT %d, %d
      ";

    $query = $wpdb->prepare( $select_payments, $affiliate_id, $offset, $perpage );

    return $wpdb->get_results($query);
  }

  public static function affiliate_payment_totals($affiliate_id) {
    global $wpdb;
    $db = Db::fetch();

    $query_str = "SELECT (SUM(co.commission_amount) - SUM(co.correction_amount)) AS owed FROM {$db->commissions} co LEFT JOIN {$db->transactions} txn ON co.transaction_id = txn.id WHERE co.payment_id = 0 AND co.affiliate_id = %d AND txn.status = 'complete'";
    $query = $wpdb->prepare($query_str, $affiliate_id);
    $owed = $wpdb->get_var($query);

    $query_str = "SELECT SUM(pmt.amount) AS paid FROM {$db->payments} pmt WHERE pmt.affiliate_id = %d";
    $query = $wpdb->prepare($query_str, $affiliate_id);
    $paid = $wpdb->get_var($query);

    $owed = ((!$owed)?0.00:$owed);
    $paid = ((!$paid)?0.00:$paid);

    return compact('owed','paid');
  }

  public static function affiliate_bulk_file_totals($payment_ids = null) {
    global $wpdb;
    $db = Db::fetch();

    if(empty($payment_ids) or is_null($payment_ids)) {
      return false;
    }

    $payment_ids = Db::prepare_ids(explode(',', $payment_ids));

    $query = "SELECT (SUM(co.commission_amount) - SUM(co.correction_amount)) as paid, co.affiliate_id FROM {$db->commissions} co WHERE co.payment_id IN ({$payment_ids}) GROUP BY co.affiliate_id";

    return $wpdb->get_results($query);
  }

  public static function get_user_count() {
    $db = Db::fetch();
    global $wpdb;
    return $db->get_count( $wpdb->users );
  }

  public static function get_report_stats_in_period(\DateTimeImmutable $start, \DateTimeImmutable $end) {
    $clicks = Report::get_clicks_in_period($start, $end);
    $uniques = Report::get_unique_clicks_in_period($start, $end);
    $transactions = Report::get_transactions_in_period($start, $end);
    $commissions = Report::get_commission_total_in_period($start, $end);
    $corrections = Report::get_correction_total_in_period($start, $end);

    $stats = [];
    $interval = new \DateInterval('P1D');
    $period = new \DatePeriod($start, $interval, $end->modify('+1 day'));

    foreach($period as $date) {
      $date = $date->format('Y-m-d');

      $stat = [
        'date' => $date,
        'clicks' => isset($clicks[$date]) ? (int) $clicks[$date] : 0,
        'uniques' => isset($uniques[$date]) ? (int) $uniques[$date] : 0,
        'transactions' => isset($transactions[$date]) ? (int) $transactions[$date] : 0,
        'commissions' => isset($commissions[$date]) ? (float) $commissions[$date] : 0,
        'corrections' => isset($corrections[$date]) ? (float) $corrections[$date] : 0,
      ];

      $stat['total'] = $stat['commissions'] - $stat['corrections'];

      $stats[] = $stat;
    }

    return $stats;
  }

  public static function get_clicks_in_period(\DateTimeInterface $start, \DateTimeInterface $end, $affiliate_id = 0) {
    global $wpdb;
    $db = Db::fetch();

    $query = "SELECT COUNT(*) AS clicks,
      DATE(cl.created_at) AS `date`
      FROM {$db->clicks} AS cl";

    $query .= $wpdb->prepare(
      " WHERE DATE(cl.created_at) >= %s AND DATE(cl.created_at) <= %s",
      $start->format('Y-m-d'),
      $end->format('Y-m-d')
    );

    if(!empty($affiliate_id)) {
      $query .= $wpdb->prepare(" AND cl.affiliate_id = %d", $affiliate_id);
    }

    $query .= " GROUP BY `date` ORDER BY `date` ASC";

    $results = $wpdb->get_results($query);

    return wp_list_pluck($results, 'clicks', 'date');
  }

  public static function get_unique_clicks_in_period(\DateTimeInterface $start, \DateTimeInterface $end, $affiliate_id = 0) {
    global $wpdb;
    $db = Db::fetch();

    $query = "SELECT COUNT(*) AS uniques,
      DATE(cl.created_at) AS `date`
      FROM {$db->clicks} AS cl";

    $query .= $wpdb->prepare(
      " WHERE cl.first_click <> 0 AND DATE(cl.created_at) >= %s AND DATE(cl.created_at) <= %s",
      $start->format('Y-m-d'),
      $end->format('Y-m-d')
    );

    if(!empty($affiliate_id)) {
      $query .= $wpdb->prepare(" AND cl.affiliate_id = %d", $affiliate_id);
    }

    $query .= " GROUP BY `date` ORDER BY `date` ASC";

    $results = $wpdb->get_results($query);

    return wp_list_pluck($results, 'uniques', 'date');
  }

  public static function get_transactions_in_period(\DateTimeInterface $start, \DateTimeInterface $end, $affiliate_id = 0) {
    global $wpdb;
    $db = Db::fetch();

    $query = "SELECT COUNT(*) AS transactions,
      DATE(txn.created_at) AS `date`
      FROM {$db->transactions} AS txn";

    $query .= $wpdb->prepare(
      " WHERE status = 'complete' AND type='commission' AND DATE(txn.created_at) >= %s AND DATE(txn.created_at) <= %s",
      $start->format('Y-m-d'),
      $end->format('Y-m-d')
    );

    if(!empty($affiliate_id)) {
      $query .= $wpdb->prepare(" AND txn.affiliate_id = %d", $affiliate_id);
    }

    $query .= " GROUP BY `date` ORDER BY `date` ASC";

    $results = $wpdb->get_results($query);

    return wp_list_pluck($results, 'transactions', 'date');
  }

  public static function get_commissions_in_period(\DateTimeInterface $start, \DateTimeInterface $end, $affiliate_id = 0) {
    global $wpdb;
    $db = Db::fetch();

    $query = "SELECT COUNT(*) AS commissions,
      DATE(commission.created_at) AS `date`
      FROM {$db->commissions} AS commission
      LEFT JOIN {$db->transactions} AS txn
      ON commission.transaction_id = txn.id";

    $query .= $wpdb->prepare(
      " WHERE txn.status = 'complete' AND DATE(commission.created_at) >= %s AND DATE(commission.created_at) <= %s",
      $start->format('Y-m-d'),
      $end->format('Y-m-d')
    );

    if(!empty($affiliate_id)) {
      $query .= $wpdb->prepare(" AND commission.affiliate_id = %d", $affiliate_id);
    }

    $query .= " GROUP BY `date` ORDER BY `date` ASC";

    $results = $wpdb->get_results($query);

    return wp_list_pluck($results, 'commissions', 'date');
  }

  public static function get_commission_total_in_period(\DateTimeInterface $start, \DateTimeInterface $end, $affiliate_id = 0) {
    global $wpdb;
    $db = Db::fetch();

    $query = "SELECT SUM(commission.commission_amount) AS commission_total,
      DATE(commission.created_at) AS `date`
      FROM {$db->commissions} AS commission
      LEFT JOIN {$db->transactions} AS txn
      ON commission.transaction_id = txn.id";

    $query .= $wpdb->prepare(
      " WHERE txn.status = 'complete' AND DATE(commission.created_at) >= %s AND DATE(commission.created_at) <= %s",
      $start->format('Y-m-d'),
      $end->format('Y-m-d')
    );

    if(!empty($affiliate_id)) {
      $query .= $wpdb->prepare(" AND commission.affiliate_id = %d", $affiliate_id);
    }

    $query .= " GROUP BY `date` ORDER BY `date` ASC";

    $results = $wpdb->get_results($query);

    return wp_list_pluck($results, 'commission_total', 'date');
  }

  public static function get_correction_total_in_period(\DateTimeInterface $start, \DateTimeInterface $end, $affiliate_id = 0) {
    global $wpdb;
    $db = Db::fetch();

    $query = "SELECT SUM(commission.correction_amount) AS correction_total,
      DATE(commission.created_at) AS `date`
      FROM {$db->commissions} AS commission
      LEFT JOIN {$db->transactions} AS txn
      ON commission.transaction_id = txn.id";

    $query .= $wpdb->prepare(
      " WHERE txn.status = 'complete' AND DATE(commission.created_at) >= %s AND DATE(commission.created_at) <= %s",
      $start->format('Y-m-d'),
      $end->format('Y-m-d')
    );

    if(!empty($affiliate_id)) {
      $query .= $wpdb->prepare(" AND commission.affiliate_id = %d", $affiliate_id);
    }

    $query .= " GROUP BY `date` ORDER BY `date` ASC";

    $results = $wpdb->get_results($query);

    return wp_list_pluck($results, 'correction_total', 'date');
  }
}
