<?php

namespace EasyAffiliate\Lib;

use EasyAffiliate\Models\Commission;
use EasyAffiliate\Models\Creative;

class Db {
  public $clicks, $events, $jobs, $transactions, $payments, $commissions, $customlinks;

  public function __construct() {
    global $wpdb;

    $this->clicks       = "{$wpdb->prefix}wafp_clicks";
    $this->events       = "{$wpdb->prefix}wafp_events";
    $this->jobs         = "{$wpdb->prefix}wafp_jobs";
    $this->transactions = "{$wpdb->prefix}wafp_transactions";
    $this->payments     = "{$wpdb->prefix}wafp_payments";
    $this->commissions  = "{$wpdb->prefix}wafp_commissions";
    $this->customlinks = "{$wpdb->prefix}wafp_customlinks";
  }

  /**
   * Get the database instance
   *
   * @param  bool $force Set to true to force a fresh instance
   * @return Db
   */
  public static function fetch($force = false) {
    static $db;

    if(!isset($db) || $force) {
      $db = new Db();
    }

    return apply_filters('esaf_fetch_db', $db);
  }

  public function upgrade() {
    global $wpdb;

    static $upgrade_already_running;

    if(isset($upgrade_already_running) && true===$upgrade_already_running) {
      return;
    }
    else {
      $upgrade_already_running = true;
    }

    $old_db_version = get_option('esaf_db_version');

    if(empty($old_db_version)) {
      // For backwards compatibility with Affiliate Royale
      $old_db_version = get_option('wafp_db_version');
    }

    if(ESAF_DB_VERSION > $old_db_version) {
      @ignore_user_abort(true);
      @set_time_limit(0);

      // Save the updated DB version early to try and avoid running this multiple times
      update_option('esaf_db_version', ESAF_DB_VERSION);

      // Save the install time as a reference point for the legacy cookie support
      add_option('esaf_install_time', time());

      // Ensure our big queries can run in an upgrade
      $wpdb->query('SET SQL_BIG_SELECTS=1'); //This may be getting set back to 0 when SET MAX_JOIN_SIZE is executed
      $wpdb->query('SET MAX_JOIN_SIZE=18446744073709551615');

      $this->before_upgrade($old_db_version);

      // This was introduced in WordPress 3.5
      // $char_col = $wpdb->get_charset_collate(); //This doesn't work for most non english setups
      $char_col = "";
      $collation = $wpdb->get_row("SHOW FULL COLUMNS FROM {$wpdb->posts} WHERE field = 'post_content'");

      if(isset($collation->Collation)) {
        $charset = explode('_', $collation->Collation);

        if(is_array($charset) && count($charset) > 1) {
          $charset = $charset[0]; //Get the charset from the collation
          $char_col = "DEFAULT CHARACTER SET {$charset} COLLATE {$collation->Collation}";
        }
      }

      //Fine we'll try it your way this time
      if(empty($char_col)) { $char_col = $wpdb->get_charset_collate(); }

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

      /* Create/Upgrade Clicks Table */
      $clicks = "
        CREATE TABLE {$this->clicks} (
          id int(11) NOT NULL auto_increment,
          ip varchar(255) default NULL,
          browser varchar(255) default NULL,
          referrer varchar(255) default NULL,
          uri varchar(255) default NULL,
          robot tinyint(4) default 0,
          first_click tinyint(4) default 0,
          created_at datetime NOT NULL,
          link_id int(11) default NULL,
          affiliate_id int(11) default NULL,
          PRIMARY KEY  (id),
          KEY link_id (link_id),
          KEY ip (ip(191)),
          KEY browser (browser(191)),
          KEY referrer (referrer(191)),
          KEY uri (uri(191)),
          KEY robot (robot),
          KEY first_click (first_click),
          KEY created_at (created_at),
          KEY affiliate_id (affiliate_id)
        ) {$char_col};";

      dbDelta($clicks);

      /* Create/Upgrade Transactions Table */
      $transactions = "
        CREATE TABLE {$this->transactions} (
          id int(11) NOT NULL auto_increment,
          affiliate_id int(11) NOT NULL,
          click_id int(11) NOT NULL,
          item_id varchar(64) DEFAULT NULL,
          item_name varchar(255) DEFAULT NULL,
          coupon varchar(64) DEFAULT NULL,
          sale_amount float(9,2) NOT NULL,
          refund_amount float(9,2) DEFAULT 0.00,
          subscr_id varchar(255) DEFAULT NULL,
          subscr_paynum int(11) DEFAULT 0,
          ip_addr varchar(255) DEFAULT NULL,
          cust_email varchar(255) DEFAULT NULL,
          cust_name varchar(255) DEFAULT NULL,
          trans_num varchar(255) DEFAULT NULL,
          type varchar(255) DEFAULT NULL,
          source varchar(64) DEFAULT 'general',
          order_id int(11) DEFAULT 0,
          status varchar(255) DEFAULT NULL,
          rebill tinyint(1) DEFAULT 0,
          created_at datetime NOT NULL,
          PRIMARY KEY  (id),
          KEY affiliate_id (affiliate_id),
          KEY item_id (item_id),
          KEY item_name (item_name),
          KEY coupon (coupon),
          KEY sale_amount (sale_amount),
          KEY refund_amount (refund_amount),
          KEY subscr_id (subscr_id(191)),
          KEY subscr_paynum (subscr_paynum),
          KEY ip_addr (ip_addr(191)),
          KEY cust_email (cust_email(191)),
          KEY cust_name (cust_name(191)),
          KEY trans_num (trans_num(191)),
          KEY type (type(191)),
          KEY source (source),
          KEY order_id (order_id),
          KEY status (status(191)),
          KEY created_at (created_at)
        ) {$char_col};";

      dbDelta($transactions);

      /* Create/Upgrade Payments Table */
      $payments = "
        CREATE TABLE {$this->payments} (
          id int(11) NOT NULL auto_increment,
          affiliate_id int(11) NOT NULL,
          amount float(9,2) NOT NULL,
          payout_method varchar(64) DEFAULT NULL,
          batch_id varchar(64) DEFAULT NULL,
          created_at datetime NOT NULL,
          PRIMARY KEY  (id),
          KEY created_at (created_at),
          KEY amount (amount),
          KEY affiliate_id (affiliate_id),
          KEY payout_method (payout_method),
          KEY batch_id (batch_id)
        ) {$char_col};";

      dbDelta($payments);

      /* Create/Upgrade Commissions Table */
      $commissions = "
        CREATE TABLE {$this->commissions} (
          id int(11) NOT NULL auto_increment,
          affiliate_id int(11) NOT NULL,
          transaction_id int(11) NOT NULL,
          commission_level int(11) DEFAULT 0,
          commission_percentage float(9,2) NOT NULL,
          commission_type varchar(255) DEFAULT 'percentage',
          commission_amount float(9,2) NOT NULL,
          correction_amount float(9,2) DEFAULT 0.00,
          payment_id int(11) DEFAULT 0,
          created_at datetime NOT NULL,
          PRIMARY KEY  (id),
          KEY created_at (created_at),
          KEY transaction_id (transaction_id),
          KEY commission_level (commission_level),
          KEY commission_percentage (commission_percentage),
          KEY commission_type (commission_type(191)),
          KEY commission_amount (commission_amount),
          KEY correction_amount (correction_amount),
          KEY payment_id (payment_id),
          KEY affiliate_id (affiliate_id)
        ) {$char_col};";

      dbDelta($commissions);

      $events = "
        CREATE TABLE {$this->events} (
          id int(11) NOT NULL auto_increment,
          event varchar(255) NOT NULL DEFAULT 'login',
          ip varchar(255) DEFAULT NULL,
          args varchar(255) DEFAULT NULL,
          evt_id int(11) NOT NULL,
          evt_id_type varchar(255) NOT NULL,
          created_at datetime NOT NULL,
          PRIMARY KEY  (id),
          KEY event_ip (ip(191)),
          KEY event_args (args(191)),
          KEY event_event (event(191)),
          KEY event_evt_id (evt_id),
          KEY event_evt_id_type (evt_id_type(191)),
          KEY event_created_at (created_at)
        ) {$char_col};";

      dbDelta($events);

      $jobs = "
        CREATE TABLE {$this->jobs} (
          id int(11) NOT NULL auto_increment,
          runtime datetime NOT NULL,
          firstrun datetime NOT NULL,
          lastrun datetime DEFAULT NULL,
          priority int(11) DEFAULT 10,
          tries int(11) DEFAULT 0,
          class varchar(255) NOT NULL,
          batch varchar(255) DEFAULT NULL,
          args text DEFAULT '',
          reason text DEFAULT '',
          status varchar(255) DEFAULT 'pending',
          created_at datetime NOT NULL,
          PRIMARY KEY  (id),
          KEY job_runtime (runtime),
          KEY job_firstrun (firstrun),
          KEY job_lastrun (lastrun),
          KEY job_status (status(191)),
          KEY job_priority (priority),
          KEY job_tries (tries),
          KEY job_class (class(191)),
          KEY job_batch (batch(191)),
          KEY job_created_at (created_at)
        ) {$char_col};";

      dbDelta($jobs);

      $custom_links = "
        CREATE TABLE {$this->customlinks} (
          id int(11) NOT NULL auto_increment,
          affiliate_id int(11) NOT NULL,
          pretty_link_id int(11) NULL,
          destination_link text NOT NULL,
          created_at datetime NOT NULL,
          PRIMARY KEY  (id),
          KEY created_at (created_at),
          KEY pretty_link_id (pretty_link_id),
          KEY affiliate_id (affiliate_id)
        ) {$char_col};";

      dbDelta($custom_links);

      $this->after_upgrade($old_db_version);
    }
  }

  public function before_upgrade($curr_db_version) {
    // nothing yet
  }

  public function after_upgrade($curr_db_version) {
    global $wpdb;
    $db = Db::fetch();

    if( (int)$curr_db_version < 11 &&
        $this->column_exists($this->transactions, 'commission_percentage') &&
        $this->column_exists($this->transactions, 'commission_amount') ) {
      $transactions = $wpdb->get_results("SELECT * FROM {$db->transactions}");

      foreach($transactions as $transaction) {
        if( !empty($transaction->affiliate_id) &&
            $transaction->commission_amount > 0.00) {
          $commission = new Commission();
          $commission->affiliate_id = $transaction->affiliate_id;
          $commission->transaction_id = $transaction->id;
          $commission->commission_percentage = $transaction->commission_percentage;
          $commission->commission_amount = $transaction->commission_amount;
          $commission->payment_id = $transaction->payment_id;
          $commission->correction_amount = $transaction->correction_amount;
          $commission->store();

          // Manually update the timestamp
          $query = $wpdb->prepare("UPDATE {$db->commissions} SET created_at=%s WHERE id=%d", $transaction->created_at, $commission->id);
          $wpdb->query($query);
        }
      }

      $wpdb->query("ALTER TABLE {$db->transactions} MODIFY commission_percentage float(9,2) DEFAULT 0.00");
      $wpdb->query("ALTER TABLE {$db->transactions} MODIFY subscr_id varchar(255) DEFAULT NULL");
    }

    $legacy_links_table = "{$wpdb->prefix}wafp_links";
    if((int)$curr_db_version < 24 && $this->table_exists($legacy_links_table)) {
      $q = $wpdb->prepare("
          SELECT
            id,
            target_url AS url,
            slug,
            CONCAT(%s,' ',slug) AS post_title,
            description AS link_text,
            info AS link_info,
            image,
            height AS image_height,
            width AS image_width,
            created_at AS post_date
          FROM {$legacy_links_table}
        ",
        __('Creative:', 'easy-affiliate')
      );

      $legacy_links = $wpdb->get_results($q, ARRAY_A);

      $link_case_query = '';
      foreach($legacy_links as $legacy_link) {
        // Don't want this id to upset load_from_array
        $legacy_link_id = $legacy_link['id'];
        unset($legacy_link['id']);

        // Check if we have already migrated this link
        $already_migrated = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_wafp_legacy_link_id' AND meta_value = %d", $legacy_link_id));

        if($already_migrated) {
          continue;
        }

        $creative = new Creative();
        $creative->load_from_array($legacy_link);

        if(!empty($legacy_link['image'])) {
          $creative->link_type = 'banner';
        }
        else {
          $creative->link_type = 'text';

          if(empty($creative->link_text)) {
            $creative->link_text = __('Affiliate Link', 'easy-affiliate');
          }
        }

        $creative_id = $creative->store();

        if(is_wp_error($creative_id)) {
          continue;
        }

        // Save the legacy link ID so we can use it to prevent duplicates
        update_post_meta($creative_id, '_wafp_legacy_link_id', $legacy_link_id);

        // The slug option has been removed, but we still support it via this meta key so that migrated creative links don't break
        if(!empty($legacy_link['slug'])) {
          update_post_meta($creative_id, '_wafp_creative_slug', $legacy_link['slug']);
        }

        $link_case_query .= $wpdb->prepare("
            WHEN link_id = %d THEN %d
          ",
          $legacy_link_id,
          $creative_id
        );
      }

      // Update clicks
      if(!empty($link_case_query)) {
        $q = "
          UPDATE {$db->clicks}
             SET link_id = (
               CASE {$link_case_query}
               END
             )
           WHERE link_id IN (
             SELECT id FROM {$legacy_links_table}
           )
        ";

        $wpdb->query($q);
      }
    }

    // TODO: Cycle through transactions ... set rebill value based on the current subscr_paynum
  }

  public function create_record($table, $args, $record_created_at = true, $output = false, $record_created_ts = false) {
    global $wpdb;

    $cols = [];
    $vars = [];
    $values = [];

    $i = 0;
    foreach($args as $key => $value) {
      if($key == 'created_at' && $record_created_at && empty($value)) { continue; }

      $cols[$i] = $key;
      if(is_numeric($value) and preg_match('!\.!',$value)) {
        $vars[$i] = '%f';
      }
      else if(is_int($value) or is_numeric($value) or is_bool($value)) {
        $vars[$i] = '%d';
      }
      else {
        $vars[$i] = '%s';
      }

      if(is_bool($value)) {
        $values[$i] = $value ? 1 : 0;
      }
      else {
        $values[$i] = $value;
      }

      $i++;
    }

    if($record_created_at && (!isset($args['created_at']) || empty($args['created_at']))) {
      $cols[$i] = 'created_at';
      $vars[$i] = $wpdb->prepare('%s',Utils::db_now());
      $i++;
    }

    if($record_created_ts) {
      $cols[$i] = 'created_ts';
      $vars[$i] = time();
    }

    if(empty($cols)) {
      return false;
    }

    $cols_str = implode(',',$cols);
    $vars_str = implode(',',$vars);

    $query = "INSERT INTO {$table} ( {$cols_str} ) VALUES ( {$vars_str} )";
    if(empty($values)) {
      $query = esc_sql( $query );
    }
    else {
      $query = $wpdb->prepare( $query, $values );
    }

    if($output)
      echo $query . "\n";

    $query_results = $wpdb->query($query);

    if($query_results)
      return $wpdb->insert_id;
    else
      return false;
  }

  public function update_record( $table, $id, $args )
  {
    global $wpdb;

    if(empty($args) or empty($id))
      return false;

    $set = '';
    $values = [];
    foreach($args as $key => $value)
    {
      if(empty($set))
        $set .= ' SET';
      else
        $set .= ',';

      $set .= " {$key}=";

      if(is_numeric($value) and preg_match('!\.!',$value))
        $set .= "%f";
      else if(is_int($value) or is_numeric($value) or is_bool($value))
        $set .= "%d";
      else
        $set .= "%s";

      if(is_bool($value))
        $values[] = $value ? 1 : 0;
      else
        $values[] = $value;
    }

    $values[] = $id;
    $query = "UPDATE {$table}{$set} WHERE id=%d";

    if( empty($values) ) {
      $query = esc_sql( $query );
    }
    else {
      $query = $wpdb->prepare( $query, $values );
    }

    if($wpdb->query($query)) {
      return $id;
    }
    else {
      return false;
    }
  }

  public function delete_records($table, $args)
  {
    global $wpdb;

    extract(Db::get_where_clause_and_values( $args ));

    $query = "DELETE FROM {$table}{$where}";

    if( empty($values) ) {
      $query = esc_sql( $query );
    }
    else {
      $query = $wpdb->prepare( $query, $values );
    }

    return $wpdb->query($query);
  }

  public function get_count($table, $args = []) {
    global $wpdb;

    extract(Db::get_where_clause_and_values( $args ));

    $query = "SELECT COUNT(*) FROM {$table}{$where}";

    if( empty($values) ) {
      $query = esc_sql( $query );
    }
    else {
      $query = $wpdb->prepare( $query, $values );
    }

    return $wpdb->get_var($query);
  }

  public function get_where_clause_and_values( $args ) {
    $args = (array)$args;

    $where = '';
    $values = [];
    foreach($args as $key => $value)
    {
      if(!empty($where))
        $where .= ' AND';
      else
        $where .= ' WHERE';

      $where .= " {$key}=";

      if(is_numeric($value) and preg_match('!\.!',$value))
        $where .= "%f";
      else if(is_int($value) or is_numeric($value) or is_bool($value))
        $where .= "%d";
      else
        $where .= "%s";

      if(is_bool($value))
        $values[] = $value ? 1 : 0;
      else
        $values[] = $value;
    }

    return compact('where','values');
  }

  public function get_one_model($model, $args = []) {
    $table = $this->get_table_for_model($model);

    $rec = $this->get_one_record($table, $args);

    if(!empty($rec)) {
      $obj = new $model();
      $obj->load_from_array($rec);
      return $obj;
    }

    return $rec;
  }

  public function get_one_record($table, $args = [])
  {
    global $wpdb;

    extract(Db::get_where_clause_and_values( $args ));

    $query = "SELECT * FROM {$table}{$where} LIMIT 1";

    if( empty($values) ) {
      $query = esc_sql( $query );
    }
    else {
      $query = $wpdb->prepare( $query, $values );
    }

    return $wpdb->get_row($query);
  }

  public function get_models($model, $order_by = '', $limit = '', $args = []) {
    $table = $this->get_table_for_model($model);
    $recs = $this->get_records($table, $args, $order_by, $limit);

    $models = [];
    foreach($recs as $rec) {
      $obj = new $model();
      $obj->load_from_array($rec);
      $models[] = $obj;
    }

    return $models;
  }

  public function get_records($table, $args = [], $order_by = '', $limit = '', $joins = [], $return_type = OBJECT) {
    global $wpdb;

    extract(Db::get_where_clause_and_values( $args ));
    $join = '';

    if(!empty($order_by)) {
      $order_by = " ORDER BY {$order_by}";
    }

    if(!empty($limit)) {
      $limit = " LIMIT {$limit}";
    }

    if(!empty($joins)) {
      foreach($joins as $join_clause) {
        $join .= " {$join_clause}";
      }
    }

    $query = "SELECT * FROM {$table}{$join}{$where}{$order_by}{$limit}";

    if(empty($values)) {
      $query = esc_sql($query);
    }
    else {
      $query = $wpdb->prepare($query, $values);
    }

    return $wpdb->get_results($query, $return_type);
  }

  /* Built to work with WordPress' built in WP_List_Table class */
  public static function list_table( $cols,
                                     $from,
                                     $joins = [],
                                     $args = [],
                                     $order_by = '',
                                     $order = '',
                                     $paged = '',
                                     $search = '',
                                     $perpage = 10,
                                     $countonly = false,
                                     $search_cols = []) {
    global $wpdb;

    // Setup selects
    $col_str_array = [];
    foreach( $cols as $col => $code ) {
      $col_str_array[] = "{$code} AS {$col}";
    }

    $col_str = implode(", ",$col_str_array);

    // Setup Joins
    if(!empty($joins)) {
      $join_str = " " . implode( " ", $joins );
    }
    else {
      $join_str = '';
    }

    $args_str = implode(' AND ', $args);

    /* -- Ordering parameters -- */
    //Parameters that are going to be used to order the result
    $order_by = (!empty($order_by) and !empty($order)) ? ( $order_by = ' ORDER BY ' . $order_by . ' ' . $order ) : '';

    //Page Number
    if(empty($paged) or !is_numeric($paged) or $paged<=0 ){ $paged=1; }

    $limit = '';
    //adjust the query to take pagination into account
    if(!empty($paged) and !empty($perpage)) {
      $offset=($paged-1)*$perpage;
      $limit = ' LIMIT '.(int)$offset.','.(int)$perpage;
    }

    // Searching
    $search_str = "";
    $searches = [];
    if(!empty($search)) {
      $search = '%' . $wpdb->esc_like($search) . '%';

      if(empty($search_cols)) {
        $search_cols = $cols;
      }

      foreach($search_cols as $search_col) {
        $searches[] = $wpdb->prepare("{$search_col} LIKE %s", $search);
      }

      if(!empty($searches)) {
        $search_str = implode(' OR ', $searches);
      }
    }

    $conditions = "";

    // Pull Searching into where
    if(!empty($args)) {
      if(!empty($searches)) {
        $conditions = " WHERE $args_str AND ({$search_str})";
      }
      else {
        $conditions = " WHERE $args_str";
      }
    }
    else {
      if(!empty($searches)) {
        $conditions = " WHERE {$search_str}";
      }
    }

    $query = "SELECT {$col_str} FROM {$from}{$join_str}{$conditions}{$order_by}{$limit}";
    $total_query = "SELECT COUNT(*) FROM {$from}{$join_str}{$conditions}";

    //Allows us to run the bazillion JOINS we use on the list tables
    $wpdb->query("SET SQL_BIG_SELECTS=1");

    $results = $wpdb->get_results($query);
    $count = $wpdb->get_var($total_query);

    return ['results' => $results, 'count' => $count];
  }

  public function get_table_for_model($model) {
    global $wpdb;
    $table = strtolower(basename(str_replace('\\', '/', $model)));

    // TODO: We need to get true inflections working here eventually ...
    //       just tacking on an 's' like this is sketchy
    return "{$wpdb->prefix}wafp_{$table}s";
  }

  public function table_exists($table) {
    global $wpdb;
    $q = $wpdb->prepare('SHOW TABLES LIKE %s', $table);
    $table_res = $wpdb->get_var($q);
    return ($table_res == $table);
  }

  public function table_empty($table) {
    return ($this->get_count($table) <= 0);
  }

  public function column_exists($table, $column) {
    global $wpdb;
    $q = $wpdb->prepare("SHOW COLUMNS FROM {$table} LIKE %s", $column);
    $res = $wpdb->get_col($q);
    return (count($res) > 0);
  }

  /**
   * Prepare an array of IDs for use in an IN clause
   *
   * @param  array  $ids The array of IDs
   * @return string      The sanitized string for the IN clause
   */
  public static function prepare_ids(array $ids) {
    $ids = self::sanitize_ids($ids);
    $ids = array_map('esc_sql', $ids);
    $ids = join(',', $ids);

    return $ids;
  }

  /**
   * Sanitize the array of IDs ensuring they are all integers
   *
   * @param  array  $ids The array of IDs
   * @return array       The array of sanitized IDs
   */
  private static function sanitize_ids(array $ids) {
    $sanitized = [];

    foreach ($ids as $id) {
      if (!is_numeric($id)) {
        continue;
      }

      $id = (int) $id;

      if ($id > 0) {
        $sanitized[] = $id;
      }
    }

    $sanitized = array_unique($sanitized);

    return $sanitized;
  }
}
