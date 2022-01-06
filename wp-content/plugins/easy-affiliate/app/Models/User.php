<?php

namespace EasyAffiliate\Models;

use EasyAffiliate\Controllers\AppCtrl;
use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Lib\BuiltinModel;
use EasyAffiliate\Lib\Db;
use EasyAffiliate\Lib\Utils;

class User extends BuiltinModel {

  public function __construct($id = null) {
    // we have to do this for backwards compatibility
    $this->custom_attr_keys = [
      'id'              => 'ID',
      'first_name'      => 'first_name',
      'last_name'       => 'last_name',
      'paypal_email'    => 'wafp_paypal_email',
      'address_one'     => 'wafp_user_address_one',
      'address_two'     => 'wafp_user_address_two',
      'city'            => 'wafp_user_city',
      'state'           => 'wafp_user_state',
      'zip'             => 'wafp_user_zip',
      'country'         => 'wafp_user_country',
      'tax_id_us'       => 'wafp_user_tax_id_us',
      'tax_id_int'      => 'wafp_user_tax_id_int',
      'is_affiliate'    => 'wafp_is_affiliate',
      'is_blocked'      => 'wafp_is_blocked',
      'blocked_message' => 'wafp_blocked_message',
      'referrer'        => 'wafp-affiliate-referrer',
      'notes'           => 'wafp_affiliate_notes',
      'unsubscribed'    => 'wafp_affiliate_unsubscribed'
    ];

    $this->attrs = [];
    $this->initialize_new_user(); //A bit redundant I know - But this prevents a nasty error when Standards = STRICT in PHP
    $this->load_user_data_by_id($id);
  }

  public function validate() {
    $this->validate_is_email($this->user_email,'user_email');
    $this->validate_not_empty($this->user_login,'user_login');
  }

  protected function initialize_new_user() {
    if(!isset($this->attrs) or !is_array($this->attrs)) { $this->attrs = []; }

    $this->meta_attrs = [
      'paypal_email'        => ['default' => null, 'type' => 'string'],
      'address_one'         => ['default' => null, 'type' => 'string'],
      'address_two'         => ['default' => null, 'type' => 'string'],
      'city'                => ['default' => null, 'type' => 'string'],
      'state'               => ['default' => null, 'type' => 'string'],
      'zip'                 => ['default' => null, 'type' => 'string'],
      'country'             => ['default' => null, 'type' => 'string'],
      'tax_id_us'           => ['default' => null, 'type' => 'string'],
      'tax_id_int'          => ['default' => null, 'type' => 'string'],
      'is_affiliate'        => ['default' => null, 'type' => 'bool'],
      'is_blocked'          => ['default' => null, 'type' => 'bool'],
      'blocked_message'     => ['default' => null, 'type' => 'string'],
      'referrer'            => ['default' => null, 'type' => 'integer'],
      'notes'               => ['default' => null, 'type' => 'string'],
      'unsubscribed'        => ['default' => null, 'type' => 'bool'],
    ];

    $this->attrs = array_merge(
      [
        'ID'                  => ['default' => null, 'type' => 'integer'],
        'first_name'          => ['default' => null, 'type' => 'string'],
        'last_name'           => ['default' => null, 'type' => 'string'],
        'user_login'          => ['default' => null, 'type' => 'string'],
        'user_nicename'       => ['default' => null, 'type' => 'string'],
        'user_email'          => ['default' => null, 'type' => 'string'],
        'user_url'            => ['default' => null, 'type' => 'string'],
        'user_pass'           => ['default' => null, 'type' => 'string'],
        'user_registered'     => ['default' => null, 'type' => 'datetime'],
        'user_activation_key' => ['default' => null, 'type' => 'string'],
        'user_status'         => ['default' => null, 'type' => 'string'],
        'display_name'        => ['default' => null, 'type' => 'string']
      ],
      $this->meta_attrs,
      $this->attrs
    );

    $this->rec = $this->get_defaults_from_attrs($this->attrs);

    return $this->rec;
  }

  public function load_user_data_by_id($id = null) {
    if(empty($id) or !is_numeric($id)) {
      $this->initialize_new_user();
    }
    else {
      $wp_user_obj = Utils::get_user_by('id', $id);

      if ($wp_user_obj) {
        $this->load_wp_user($wp_user_obj);
        $this->load_meta();
      } else {
        $this->initialize_new_user();
      }
    }

    // This must be here to ensure that we don't pull an encrypted
    // password, encrypt it a second time and store it
    unset($this->user_pass);
  }

  public function load_user_data_by_login($login = null) {
    if(empty($login)) {
      $this->initialize_new_user();
    }
    else {
      $wp_user_obj = Utils::get_user_by('login', $login);

      if ($wp_user_obj) {
        $this->load_wp_user($wp_user_obj);
        $this->load_meta();
      } else {
        $this->initialize_new_user();
      }
    }

    // This must be here to ensure that we don't pull an encrypted
    // password, encrypt it a second time and store it
    unset($this->user_pass);
  }

  public function load_user_data_by_email($email = null) {
    if(empty($email)) {
      $this->initialize_new_user();
    }
    else {
      $wp_user_obj = Utils::get_user_by('email', $email);

      if ($wp_user_obj) {
        $this->load_wp_user($wp_user_obj);
        $this->load_meta();
      } else {
        $this->initialize_new_user();
      }
    }

    // This must be here to ensure that we don't pull an encrypted
    // password, encrypt it a second time and store it
    unset($this->user_pass);
  }

  public function load_wp_user($wp_user_obj) {
    $this->rec->ID = $wp_user_obj->ID;
    $this->rec->user_login = $wp_user_obj->user_login;
    $this->rec->user_nicename = (isset($wp_user_obj->user_nicename))?$wp_user_obj->user_nicename:'';
    $this->rec->user_email = $wp_user_obj->user_email;
    $this->rec->user_url = (isset($wp_user_obj->user_url))?$wp_user_obj->user_url:'';
    $this->rec->user_pass = $wp_user_obj->user_pass;
    $this->rec->user_registered = $wp_user_obj->user_registered;
    $this->rec->user_activation_key = (isset($wp_user_obj->user_activation_key))?$wp_user_obj->user_activation_key:'';
    $this->rec->user_status = (isset($wp_user_obj->user_status))?$wp_user_obj->user_status:'';
    // We don't need this, and as of WP 3.9 -- this causes wp_update_user() to wipe users role/caps!!!
    // $this->rec->role = (isset($wp_user_obj->role))?$wp_user_obj->role:'';
    $this->rec->display_name = (isset($wp_user_obj->display_name))?$wp_user_obj->display_name:'';
  }

  public function load_meta() {
    $this->rec->first_name = get_user_meta($this->ID, $this->first_name_str, true);
    $this->rec->last_name = get_user_meta($this->ID, $this->last_name_str, true);
    $this->rec->user_pass = get_user_meta($this->ID, $this->user_pass_str, true);
    $this->rec->paypal_email = get_user_meta($this->ID, $this->paypal_email_str, true);
    $this->rec->address_one = get_user_meta($this->ID, $this->address_one_str, true);
    $this->rec->address_two = get_user_meta($this->ID, $this->address_two_str, true);
    $this->rec->city = get_user_meta($this->ID, $this->city_str, true);
    $this->rec->state = get_user_meta($this->ID, $this->state_str, true);
    $this->rec->zip = get_user_meta($this->ID, $this->zip_str, true);
    $this->rec->country = get_user_meta($this->ID, $this->country_str, true);
    $this->rec->tax_id_us = get_user_meta($this->ID, $this->tax_id_us_str, true);
    $this->rec->tax_id_int = get_user_meta($this->ID, $this->tax_id_int_str, true);
    $this->rec->is_affiliate = get_user_meta($this->ID, $this->is_affiliate_str, true);
    $this->rec->is_blocked = get_user_meta($this->ID, $this->is_blocked_str, true);
    $this->rec->blocked_message = get_user_meta($this->ID, $this->blocked_message_str, true);
    $this->rec->referrer = get_user_meta($this->ID, $this->referrer_str, true);
    $this->rec->notes = get_user_meta($this->ID, $this->notes_str, true);
    $this->rec->unsubscribed = get_user_meta($this->ID, $this->unsubscribed_str, true);
  }

  public function full_name() {
    return "{$this->first_name} {$this->last_name}";
  }

  /**
   * Get the user's full name, first name or email address depending on what's set
   *
   * @return string
   */
  public function name_or_email() {
    $name = [];

    if(!empty($this->first_name)) {
      $name[] = $this->first_name;

      if(!empty($this->last_name)) {
        $name[] = $this->last_name;
      }
    }

    if(!empty($name)) {
      return join(' ', $name);
    }

    return $this->user_email;
  }

  // For backwards compatibility
  public function __extend_model_set_is_affiliate($value) {
    do_action('esaf-set-is-affiliate', $this, $value);
    return $value;
  }

  public function __extend_model_set_referrer($value) {
    //Don't allow setting yourself as a referrer DOH!
    if($value == $this->ID) {
      return $this->referrer;
    }

    return $value;
  }

  public function create() {
    if(isset($this->ID)) {
      unset($this->ID);
    }

    $user_id = $this->store();

    $this->ID = $user_id;

    return $user_id;
  }

  // alias of store
  public function update() {
    return $this->store();
  }

  public function store($validate = true) {
    if($validate) {
      try {
        $this->validate();
      }
      catch(\Exception $e) {
        return new \WP_Error(get_class($e), $e->getMessage());
      }
    }

    // WP expects the values to be slashed
    $record = wp_slash($this->get_record());

    if(isset($this->ID) and is_numeric($this->ID)) {
      $user_id = wp_update_user($record);
    }
    else {
      $user_id = wp_insert_user($record);
    }

    if(is_wp_error($user_id)) {
      return $user_id;
    }

    $this->ID = $user_id;
    $this->store_meta();

    return $user_id;
  }

  public function destroy() {
    // Save this to cleanup transactions / commissions / payments if deletion is successful
    $affiliate_id = $this->ID;

    require_once(ABSPATH.'wp-admin/includes/user.php' );
    if(!wp_delete_user($this->ID)) {
      // Short circuit on failure
      return new \WP_Error(sprintf(__('Failed to delete Affiliate %d', 'easy-affiliate'), $affiliate_id));
    }

/* TODO: Should we clean up dependencies? Doing so will alter the stats and possibly hurt our data integrity.
    $clicks = Click::get_all_by_affiliate_id($affiliate_id);
    if(is_array($clicks)) {
      foreach($clicks as $click) {
        $click->destroy();
      }
    }

    $payments = Payment::get_all_by_affiliate_id($affiliate_id);
    if(is_array($payments)) {
      foreach($payments as $payment) {
        $payment->destroy();
      }
    }

    $commissions = Commission::get_all_by_affiliate_id($affiliate_id);
    if(is_array($commissions)) {
      foreach($commissions as $commission) {
        $commission->destroy();
      }
    }

    $transactions = Transaction::get_all_by_affiliate_id($affiliate_id);
    if(is_array($transactions)) {
      foreach($transactions as $transaction) {
        $transaction->destroy();
      }
    }
*/

    return $this;
  }

  public function store_meta() {
    if($this->ID <= 0) { return; }

    $attrs = (array)$this->get_meta_values();

    foreach($attrs as $attr => $attr_value) {
      $attr_key = $this->get_attr_key($attr);
      $old_attr_value = get_user_meta($this->ID, $attr_key, true);

      do_action(
        "esaf_user_store_meta-{$attr}",
        $this->cast_attr($attr,$attr_value),
        $this->cast_attr($attr,$old_attr_value),
        $this,
        $attr_key
      );

      // WP expects the meta value to be slashed
      $value = wp_slash($this->cast_attr($attr, $attr_value));

      update_user_meta($this->ID, $attr_key, $value);
    }
  }

  public function send_account_notifications($send_admin_notification = true, $send_affiliate_notification = true) {
    $site_name = Utils::blogname();
    $options = Options::fetch();

    if($send_admin_notification) {
      /* translators: In this string, %s is the Blog Name/Title */
      $subject = sprintf(__("[%s] New Affiliate Signup",'easy-affiliate'), $site_name);

      /* translators: In this string, %1$s is the blog's name/title, %2$s is the user's real name, %3$s is the user's username and %4$s is the user's email */
      $message = sprintf(__( "A new user just joined your Affiliate Program at %1\$s!\n\nName: %2\$s\nUsername: %3\$s\nE-Mail: %4\$s", 'easy-affiliate'), $site_name, $this->full_name(), $this->user_login, $this->user_email ) . "\n\n";

      Utils::wp_mail_to_admin($subject, $message);
    }

    if($send_affiliate_notification) {
      Utils::send_email_notification(
        $this->user_email,
        $options->welcome_email_subject,
        $options->welcome_email_body,
        $this->get_email_vars(),
        $options->welcome_email_use_template
      );
    }
  }

  /**
   * Get the array of email variables for this user
   *
   * @return array
   */
  public function get_email_vars() {
    return apply_filters('esaf_user_email_vars', [
      'affiliate_first_name' => Utils::with_default($this->first_name, $this->user_login),
      'affiliate_last_name' => $this->last_name,
      'affiliate_full_name' => $this->full_name(),
      'affiliate_login' => $this->user_login,
      'affiliate_email' => $this->user_email,
      'affiliate_id' => $this->ID
    ], $this);
  }

  public function reset_form_key_is_valid($key)
  {
    $stored_key = get_user_meta($this->ID, 'wafp_reset_password_key', true);

    return ($stored_key and ($key == $stored_key));
  }

  public function send_reset_password_requested_notification()
  {
    $wafp_blogname = Utils::blogname();
    $wafp_blogurl = Utils::blogurl();

    $key = md5(time() . $this->ID);
    update_user_meta( $this->ID, 'wafp_reset_password_key', $key );

    $permalink = Utils::login_url();

    $delim     = AppCtrl::get_param_delimiter_char($permalink);

    $reset_password_link = "{$permalink}{$delim}action=reset_password&mkey={$key}&u=" . $this->get_urlencoded_user_login();

    // Send password email to new users
    $recipient = "{$this->full_name()} <{$this->user_email}>"; //recipient

    /* translators: In this string, %s is the Blog Name/Title */
    $subject = sprintf( __("[%s] Affiliate Password Reset",'easy-affiliate'), $wafp_blogname);

    /* translators: In this string, %1$s is the user's username, %2$s is the blog's name/title, %3$s is the blog's url, %4$s the reset password link */
    $message = sprintf( __( "Someone requested to reset your password for %1\$s on the Affiliate Program at %2\$s at %3\$s\n\nTo reset your password visit the following address, otherwise just ignore this email and nothing will happen.\n\n%4\$s", 'easy-affiliate' ), $this->user_login, $wafp_blogname, $wafp_blogurl, $reset_password_link );

    Utils::wp_mail($recipient, $subject, $message);
  }

  public function set_password_and_send_notifications($key, $password)
  {
    $wafp_blogname = Utils::blogname();

    if($this->reset_form_key_is_valid($key)) {
      delete_user_meta( $this->ID, 'wafp_reset_password_key' );

      wp_set_password(wp_slash($password), $this->ID);

      /* translators: In this string, %s is the Blog Name/Title */
      $subject = sprintf( __("[%s] Affiliate Password Lost/Changed",'easy-affiliate'), $wafp_blogname);

      /* translators: In this string, %1$s is the user's username */
      $message = sprintf( __( "Affiliate Password Lost and Changed for user: %1\$s", 'easy-affiliate' ), $this->user_login );

      Utils::wp_mail_to_admin($subject, $message);

      $login_link = Utils::login_url();

      // Send password email to new user
      $recipient = "{$this->full_name()} <{$this->user_email}>"; //recipient

      /* translators: In this string, %s is the Blog Name/Title */
      $subject = sprintf( __("[%s] Your new Affiliate Password",'easy-affiliate'), $wafp_blogname);

      /* translators: In this string, %1$s is the user's first name, %2$s is the blog's name/title, %3$s is the user's username, and %4$s is the blog's URL... */
      $message = sprintf( __( "%1\$s,\n\nYour password was successfully reset on %2\$s!\n\nUsername: %3\$s\n\nYou can log in here: %4\$s", 'easy-affiliate' ), (empty($this->first_name)?$this->user_login:$this->first_name), $wafp_blogname, $this->user_login, $login_link );

      Utils::wp_mail($recipient, $subject, $message);

      return true;
    }

    return false;
  }

  /** Returns an array of the ancestor affiliates for this user */
  public function get_ancestors($compress_levels = false, $used_ids = []) {
    $ancestors = [];

    if(in_array((int)$this->ID,$used_ids) || ($compress_levels && !$this->is_affiliate)) {
      //$ancestors = array(); // Skip me bro
    }
    else {
      $ancestors = [$this];
    }

    if( !empty($this->referrer) && is_numeric($this->referrer) &&
        !in_array((int)$this->ID,$used_ids) && // Yeah ... avoid infinite recursion bro ... not cool
        ($ref = new User($this->referrer)) ) {
      $used_ids[] = (int)$this->ID;
      $ancestors = array_merge( $ancestors, $ref->get_ancestors( $compress_levels, $used_ids ) );
    }

    return $ancestors;
  }

  public function get_children($just_affiliates = false) {
    return $this->get_descendants( 1, $just_affiliates );
  }

  public function child_count() {
    global $wpdb;
    $query = "SELECT count(*) FROM {$wpdb->usermeta} AS um WHERE um.meta_key=%s AND um.meta_value=%s";
    $query = $wpdb->prepare( $query, $this->referrer_str, $this->ID );
    return $wpdb->get_var($query);
  }

  /** Returns an array of the descendants for this user */
  public function get_descendants($depth = 10, $just_affiliates = false, $level = 0, $user_ids = []) {
    global $wpdb;

    $user_ids[] = $this->ID;

    // Minimum depth of 1 level (always return at least children)
    if($depth < 1) { $depth = 1; }

    $aff_array = [];

    if( $level >= $depth )
      return $aff_array;

    $query = "SELECT um.user_id FROM {$wpdb->usermeta} AS um WHERE um.meta_key=%s AND um.meta_value=%s";
    $query = $wpdb->prepare( $query, $this->referrer_str, $this->ID );

    $aff_ids = $wpdb->get_col( $query );

    foreach( $aff_ids as $aff_id ) {
      if( is_numeric($aff_id) && ( $aff = new User($aff_id) ) ) {
        if($just_affiliates && !$aff->is_affiliate) {
          continue;
        }

        // Prevent issues from potential circular inheritance
        if( !in_array($aff_id, $user_ids) ) {
          $aff_array[] = [
            'object' => $aff,
            'level' => $level,
            'children' => $aff->get_descendants( $depth, $just_affiliates, ( $level + 1 ) , $user_ids )
          ];
        }
      }
    }

    return $aff_array;
  }

  public function sale_count() {
    global $wpdb;
    $db = Db::fetch();

    $query = $wpdb->prepare("SELECT COUNT(*) FROM {$db->transactions} WHERE affiliate_id = %d AND status = 'complete' AND type = 'commission'", $this->ID);
    return $wpdb->get_var($query);
  }

  public function sales_total() {
    $db = Db::fetch();
    global $wpdb;

    $query = $wpdb->prepare("SELECT SUM(sale_amount) FROM {$db->transactions} WHERE affiliate_id = %d AND status = 'complete' AND type = 'commission'", $this->ID);
    return $wpdb->get_var($query);
  }

  public function commissions_total() {
    $db = Db::fetch();
    global $wpdb;

    $query = $wpdb->prepare("SELECT SUM(cm.commission_amount)-SUM(cm.correction_amount) FROM {$db->commissions} AS cm JOIN {$db->transactions} AS tr ON cm.transaction_id=tr.id WHERE cm.affiliate_id = %d AND tr.status = 'complete' AND tr.type = 'commission'", $this->ID);
    return $wpdb->get_var($query);
  }

  public function click_count() {
    $db = Db::fetch();
    global $wpdb;

    $query = $wpdb->prepare("SELECT COUNT(*) FROM {$db->clicks} WHERE affiliate_id = %d", $this->ID);
    return $wpdb->get_var($query);
  }

  public function refund_total() {
    $db = Db::fetch();
    global $wpdb;

    $query = $wpdb->prepare("SELECT SUM(cm.correction_amount) FROM {$db->commissions} AS cm JOIN {$db->transactions} AS tr ON cm.transaction_id=tr.id WHERE cm.affiliate_id = %d AND tr.status = 'complete' AND tr.type = 'commission'", $this->ID);
    return $wpdb->get_var($query);
  }

  public static function get_dashboard_stats( $affiliate_id ) {
    $aff = new User($affiliate_id);

    $commission_rate = '0%';
    $commission_levels = Commission::get_levels($aff);
    $commission_type = Commission::get_type($aff);

    if(isset($commission_levels[0])) {
      $commission_rate = $commission_type == 'fixed' ? AppHelper::format_currency($commission_levels[0]) : $commission_levels[0] . '%';
    }

    $stats = [
      'clicks' => $aff->click_count(),
      'transactions' => $aff->sale_count(),
      'total' => $aff->sales_total(),
      'commission_rate' => $commission_rate,
      'commission' => $aff->commissions_total(),
      'refund' => $aff->refund_total(),
    ];

    $stats['epc'] = $stats['clicks'] > 0 ? round($stats['commission'] / $stats['clicks'], 2) : 0;

    return $stats;
  }

  /* Will return list table code or list_table */
  public static function affiliate_list_table( $order_by = '',
                                               $order = '',
                                               $paged = '',
                                               $search = '',
                                               $perpage = 10 ) {
    $db = Db::fetch();
    global $wpdb;
    $options = Options::fetch();

    $user = new User();

    $year = date('Y');
    $month = date('m');
    $cols = [
      'ID' => "{$wpdb->users}.ID",
      'username' => "{$wpdb->users}.user_login",
      'email' => "{$wpdb->users}.user_email",
      'name' => "CONCAT(um_last_name.meta_value, ', ', um_first_name.meta_value)",
      'status' => "CASE WHEN um_is_blocked.meta_value = 1 THEN 'blocked' WHEN (SELECT IFNULL(COUNT(*),0) FROM {$db->clicks} as clx WHERE clx.affiliate_id={$wpdb->users}.ID AND created_at BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW()) = 0 THEN 'inactive' ELSE 'active' END",
      'mtd_clicks' => "(SELECT IFNULL(COUNT(*),0) FROM {$db->clicks} as clk WHERE clk.affiliate_id={$wpdb->users}.ID AND created_at BETWEEN '{$year}-{$month}-01 00:00:00' AND NOW())",
      'ytd_clicks' => "(SELECT IFNULL(COUNT(*),0) FROM {$db->clicks} as clk WHERE clk.affiliate_id={$wpdb->users}.ID AND created_at BETWEEN '{$year}-01-01 00:00:00' AND NOW())",
      'mtd_commissions' => "(SELECT IFNULL(SUM(commish.commission_amount),0.00) FROM {$db->commissions} AS commish LEFT JOIN {$db->transactions} txn ON commish.transaction_id = txn.id WHERE txn.status = 'complete' AND commish.affiliate_id={$wpdb->users}.ID AND commish.created_at BETWEEN '{$year}-{$month}-01 00:00:00' AND NOW())",
      'ytd_commissions' => "(SELECT IFNULL(SUM(commish.commission_amount),0.00) FROM {$db->commissions} AS commish LEFT JOIN {$db->transactions} txn ON commish.transaction_id = txn.id WHERE txn.status = 'complete' AND commish.affiliate_id={$wpdb->users}.ID AND commish.created_at BETWEEN '{$year}-01-01 00:00:00' AND NOW())",
      'signup_date' => "DATE({$wpdb->users}.user_registered)",
      'parent_name' => "CONCAT(um_parent_first_name.meta_value,' ', um_parent_last_name.meta_value, ' (', parent.user_login, ')')",
      'parent_id' => "parent.ID",
      'notes' => "um_notes.meta_value",
    ];

    $search_cols = [
      "{$wpdb->users}.user_login",
      "{$wpdb->users}.user_email",
      "um_last_name.meta_value",
      "um_first_name.meta_value",
      "parent.user_login",
    ];

    $joins = [
      "LEFT OUTER JOIN {$wpdb->usermeta} AS um_first_name ON um_first_name.user_id={$wpdb->users}.ID AND um_first_name.meta_key='first_name'",
      "LEFT OUTER JOIN {$wpdb->usermeta} AS um_last_name ON um_last_name.user_id={$wpdb->users}.ID AND um_last_name.meta_key='last_name'",
      "LEFT OUTER JOIN {$wpdb->usermeta} AS um_is_blocked ON um_is_blocked.user_id={$wpdb->users}.ID AND um_is_blocked.meta_key='wafp_is_blocked'",
      "LEFT OUTER JOIN {$wpdb->usermeta} AS um_affiliate_referrer ON um_affiliate_referrer.user_id={$wpdb->users}.ID AND um_affiliate_referrer.meta_key='".$user->referrer_str."'",
      "LEFT OUTER JOIN {$wpdb->users} AS parent ON parent.ID=um_affiliate_referrer.meta_value",
      "LEFT OUTER JOIN {$wpdb->usermeta} AS um_parent_first_name ON um_parent_first_name.user_id=parent.ID AND um_parent_first_name.meta_key='first_name'",
      "LEFT OUTER JOIN {$wpdb->usermeta} AS um_parent_last_name ON um_parent_last_name.user_id=parent.ID AND um_parent_last_name.meta_key='last_name'",
      "LEFT OUTER JOIN {$wpdb->usermeta} AS um_is_affiliate ON um_is_affiliate.user_id={$wpdb->users}.ID AND um_is_affiliate.meta_key='wafp_is_affiliate'",
      "LEFT OUTER JOIN {$wpdb->usermeta} AS um_notes ON um_notes.user_id={$wpdb->users}.ID AND um_notes.meta_key='wafp_affiliate_notes'",
    ];

    $args = [
      'um_is_affiliate.meta_value IS NOT NULL',
      'um_is_affiliate.meta_value=1'
    ];

    return Db::list_table($cols, $wpdb->users, $joins, $args, $order_by, $order, $paged, $search, $perpage, false, $search_cols);
  }

/***** STATIC METHODS *****/
  public static function get_one($args) {
    return self::get_one_by_class(User::class, $args);
  }

  public static function get_all($order_by = '', $limit = '', $args = []) {
    return self::get_all_by_class(User::class, $order_by, $limit, $args);
  }

  public static function get_count($args = []) {
    return self::get_count_by_class(User::class, $args);
  }

  public static function validate_signup($values) {
    $options = Options::fetch();
    $user = Utils::get_currentuserinfo();
    $logged_in = !!$user;
    $errors = [];

    // $nonce_data = Nonces::get_cookie_data();
    // if($nonce_data === false || !Nonces::is_valid($nonce_data['nonce'], $nonce_data['ts']))
      // $errors[] = __('Robots not allowed', 'easy-affiliate');

    if(!$user) {
      $user = new User();
    }

    if(!$logged_in) {
      if(empty($values[$user->first_name_str])) {
        $errors[] = __('You must enter a First Name','easy-affiliate');
      }

      if(empty($values[$user->last_name_str])) {
        $errors[] = __('You must enter a Last Name','easy-affiliate');
      }

      if(empty($values[$user->user_login_str])) {
        $errors[] = __('Username must not be blank','easy-affiliate');
      }
      elseif(!preg_match('#^[a-zA-Z0-9_\-@.]+$#',$values[$user->user_login_str])) {
        $errors[] = __('Username must only contain letters, numbers, dashes and/or underscores','easy-affiliate');
      }
      elseif(username_exists($values[$user->user_login_str])) {
        $errors[] = __('Username is already taken','easy-affiliate');
      }

      if(empty($values[$user->user_email_str])) {
        $errors[] = __('You must enter an Email Address','easy-affiliate');
      }
      elseif(!is_email($values[$user->user_email_str])) {
        $errors[] = __('Email must be a real and properly formatted email address','easy-affiliate');
      }
      elseif(email_exists($values[$user->user_email_str])) {
        $errors[] = __('Email Address has already been used by another user','easy-affiliate');
      }
    }

    if($options->show_address_fields && $options->require_address_fields) {
      if(empty($values[$user->address_one_str])) {
        $errors[] = __('You must enter an Address', 'easy-affiliate');
      }

      if(empty($values[$user->city_str])) {
        $errors[] = __('You must enter a City', 'easy-affiliate');
      }

      if(empty($values[$user->state_str])) {
        $errors[] = __('You must enter a State/Province', 'easy-affiliate');
      }

      if(empty($values[$user->zip_str])) {
        $errors[] = __('You must enter a Zip/Postal Code', 'easy-affiliate');
      }

      if(empty($values[$user->country_str])) {
        $errors[] = __('You must enter a Country', 'easy-affiliate');
      }
    }

    if($options->show_tax_id_fields && $options->require_tax_id_fields) {
      if(empty($values[$user->tax_id_us_str]) && empty($values[$user->tax_id_int_str])) {
        $errors[] = __('You must enter an SSN / Tax ID or International Tax ID', 'easy-affiliate');
      }
    }

    if($options->is_payout_method_paypal()) {
      if(empty($values[$user->paypal_email_str])) {
        $errors[] = __('PayPal email address is required','easy-affiliate');
      }
      elseif(!is_email($values[$user->paypal_email_str])) {
        $errors[] = __('PayPal email address must be a real and properly formatted email address','easy-affiliate');
      }
    }

    if(!$logged_in) {
      if(empty($values[$user->user_pass_str])) {
        $errors[] = __('You must enter a Password','easy-affiliate');
      }

      if(empty($values['wafp_user_password_confirm'])) {
        $errors[] = __('You must enter a Password Confirmation', 'easy-affiliate');
      }

      if($values[$user->user_pass_str] != $values['wafp_user_password_confirm']) {
        $errors[] = __('Your Password and Password Confirmation don\'t match', 'easy-affiliate');
      }
    }

    if(isset($values['wafp_honeypot']) && !empty($values['wafp_honeypot'])) {
      $errors[] = __('You must be a human to sign up for this site', 'easy-affiliate');
    }

    if($options->affiliate_agreement_enabled && !$values['wafp_user_signup_agreement']) {
      $errors[] = __('You must agree to the Affiliate Signup Agreement', 'easy-affiliate');
    }

    return $errors;
  }

  public static function validate_login($params,$errors) {
    if(empty($params['log'])) {
      $errors[] = __('Username must not be blank','easy-affiliate');
    }

    if(!username_exists($params['log'])) {
      $errors[] = __('Username was not found','easy-affiliate');
    }

    return $errors;
  }

  public static function validate_forgot_password($params,$errors) {
    if(empty($params['wafp_user_or_email'])) {
      $errors[] = __('You must enter a Username or Email','easy-affiliate');
    }
    else {
      $is_email = (is_email($params['wafp_user_or_email']) && email_exists($params['wafp_user_or_email']));
      $is_username = username_exists($params['wafp_user_or_email']);

      if(!$is_email && !$is_username) {
        $errors[] = __('That Username or Email wasn\'t found.','easy-affiliate');
      }
    }

    return $errors;
  }

  public static function validate_reset_password($params) {
    $errors = [];

    if(empty($params['wafp_user_password'])) {
      $errors[] = __('You must enter a Password.','easy-affiliate');
    }

    if(empty($params['wafp_user_password_confirm'])) {
      $errors[] = __('You must enter a Password Confirmation.', 'easy-affiliate');
    }

    if($_POST['wafp_user_password'] != $_POST['wafp_user_password_confirm']) {
      $errors[] = __('Your Password and Password Confirmation don\'t match.', 'easy-affiliate');
    }

    return $errors;
  }

  //$str may be a user_login, or a User ID - let's attempt to figure out which and return the proper aff id
  public static function get_aff_id_from_string($str) {
    $str = urldecode($str);

    if($ID = username_exists($str)) {
      return $ID;
    }

    if(is_numeric($str)) {
      $aff = new User($str);

      if($aff->ID && $aff->is_affiliate) {
        return $str;
      }
    }

    return false;
  }

  public function default_affiliate_url() {
    $url = apply_filters('esaf_default_affiliate_url', home_url());

    if(apply_filters('esaf_affiliate_param_use_id', is_email($this->user_login), $this)) {
      $username = $this->ID;
    }
    else {
      $username = $this->get_urlencoded_user_login();
    }

    $args = apply_filters('esaf_default_affiliate_url_args', ['aff' => $username], $this, $url);

    return add_query_arg($args, $url);
  }

  public function showcase_url() {
    $options = Options::fetch();
    $url = apply_filters('esaf_showcase_url', ! empty( $options->showcase_url_href ) ? $options->showcase_url_href : home_url() );

    if(apply_filters('esaf_affiliate_param_use_id', is_email($this->user_login), $this)) {
      $username = $this->ID;
    }
    else {
      $username = $this->get_urlencoded_user_login();
    }

    $args = apply_filters('esaf_showcase_url_args', ['aff' => $username], $this, $url);

    return add_query_arg($args, $url);
  }

  public function get_urlencoded_user_login() {
    return urlencode($this->user_login);
  }

  public function affiliate_profile() {
    if( !empty($this->referrer) ) {
      $ref = new User( $this->referrer );
      $refname = $ref->full_name() . ' (' . $ref->user_login . ')';
    }
    else {
      $refname = '';
    }

    return apply_filters(
      'esaf-affiliate-profile',
      [
        'referrer' => $refname,
        'name' => $this->full_name(),
        'is_affiliate' => $this->is_affiliate ? __('Yes','easy-affiliate') : __('No','easy-affiliate'),
        'id' => $this->ID,
        'username' => $this->user_login,
        'email' => $this->user_email,
        'sales' => $this->sale_count()
      ],
      $this->ID
    );
  }

  public function show_dashboard_my_affiliate_link() {
    return apply_filters('esaf_show_dashboard_my_affiliate_link', true, $this);
    /* We should always show the affiliate link
    global $wpdb;
    $db = Db::fetch();

    $login_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$db->events} WHERE event = 'affiliate-login' AND evt_id = %d", $this->ID));

    return apply_filters('esaf_show_dashboard_my_affiliate_link', $login_count < 3, $this);
    */
  }

  public function show_showcase_url() {
    $options = Options::fetch();
    return apply_filters('esaf_show_showcase_url', ! empty( $options->showcase_url_enabled ), $this);
  }

  /**
   * Get the estimated next payout amount for this affiliate
   *
   * @return int|float
   */
  public function get_estimated_next_payout() {
    $options = Options::fetch();
    $utc = new \DateTimeZone('UTC');

    try {
      // Start with the first day of the month to avoid any issues with differing days at the end of months
      $target = new \DateTime('first day of this month', $utc);
      $now = new \DateTimeImmutable('now', $utc);

      if($options->payout_waiting_period > 0) {
        $target->modify('-' . $options->payout_waiting_period . ' months');
      }

      $already_paid_this_period = Payment::affiliate_paid_in_period(
        $this->ID,
        $now->modify('first day of this month 00:00:00'),
        $now->modify('last day of this month 23:59:59')
      );

      if(!$already_paid_this_period) {
        $target->modify('-1 month');
      }

      // Set it to the last second of the month we're working with
      $target->modify('last day of this month 23:59:59');

      return Payment::affiliate_owed_on_date($this->ID, $target);
    }
    catch(\Exception $e) {
      // Ignore exceptions
    }

    return 0;
  }
}
