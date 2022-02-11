<?php

namespace EasyAffiliate\Lib;

use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Models\Commission;
use EasyAffiliate\Models\Options;
use EasyAffiliate\Models\User;

class Utils
{
  public static function build_query_string( $add_params=array(),
    $include_query_string=false,
    $exclude_params=array(),
    $exclude_referer=true ) {
    $query_string = '';
    if($include_query_string) {
      $query_string = $_SERVER['QUERY_STRING'];
    }

    if(empty($query_string)) {
      $query_string = http_build_query($add_params);
    }
    else {
      $query_string = $query_string . '&' . http_build_query($add_params);
    }

    if($exclude_referer) {
      $exclude_params[] = '_wp_http_referer';
      $exclude_params[] = 'page';
    }

    foreach($exclude_params as $param) {
      $query_string = preg_replace('!&?' . preg_quote($param,'!') . '=[^&]*!', '', $query_string);
    }

    return $query_string;
  }

  public static function get_delim($link) {
    return ((preg_match("#\?#",$link))?'&':'?');
  }

  // $add_nonce = [$action,$name]
  public static function admin_url( $path,
    $add_nonce=array(),
    $add_params=array(),
    $include_query_string=false,
    $exclude_params=array(),
    $exclude_referer=true ) {
    $delim = self::get_delim($path);

    // Automatically exclude the nonce if it's present
    if(!empty($add_nonce)) {
      $nonce_action = $add_nonce[0];
      $nonce_name = (isset($add_nonce[1]) ? $add_nonce[1] : '_wpnonce');
      $exclude_params[] = $nonce_name;
    }

    $url = admin_url($path.$delim.self::build_query_string($add_params,$include_query_string,$exclude_params,$exclude_referer));

    if(empty($add_nonce)) {
      return $url;
    }
    else {
      return html_entity_decode(wp_nonce_url($url,$nonce_action,$nonce_name));
    }
  }

  public static function get_user_id_by_email($email) {
    if(isset($email) && !empty($email)) {
      global $wpdb;
      $query = "SELECT ID FROM {$wpdb->users} WHERE user_email=%s";
      $query = $wpdb->prepare($query, esc_sql($email));
      return (int) $wpdb->get_var($query);
    }

    return '';
  }

  public static function is_image($filename) {
    if(!file_exists($filename)) {
      return false;
    }

    $file_meta = getimagesize($filename);

    $image_mimes = ["image/gif", "image/jpeg", "image/png"];

    return in_array($file_meta['mime'], $image_mimes);
  }

  public static function is_curl_enabled() {
    return function_exists('curl_version');
  }

  public static function is_post_request() {
    return (strtolower($_SERVER['REQUEST_METHOD']) == 'post');
  }

  public static function base36_encode($base10) {
    return base_convert($base10, 10, 36);
  }

  public static function base36_decode($base36) {
    return base_convert($base36, 36, 10);
  }

  public static function is_date($str) {
    if(!is_string($str)) { return false; }
    $d = strtotime($str);
    return ($d !== false);
  }

  public static function is_ip($ip) {
    // return preg_match('#^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$#',$ip);
    return ((bool)filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || (bool)filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6));
  }

  public static function is_url($str) {
    return preg_match('/https?:\/\/[\w-]+(\.[\w-]{2,})*(:\d{1,5})?/', $str);
  }

  public static function is_email($str) {
    return preg_match('/[\w\d._%+-]+@[\w\d.-]+\.[\w]{2,4}/', $str);
  }

  public static function is_phone($str) {
    return preg_match('/\(?\d{3}\)?[- ]\d{3}-\d{4}/', $str);
  }

  public static function http_status_codes() {
    return [
      100 => 'Continue',
      101 => 'Switching Protocols',
      102 => 'Processing',
      200 => 'OK',
      201 => 'Created',
      202 => 'Accepted',
      203 => 'Non-Authoritative Information',
      204 => 'No Content',
      205 => 'Reset Content',
      206 => 'Partial Content',
      207 => 'Multi-Status',
      300 => 'Multiple Choices',
      301 => 'Moved Permanently',
      302 => 'Found',
      303 => 'See Other',
      304 => 'Not Modified',
      305 => 'Use Proxy',
      306 => 'Switch Proxy',
      307 => 'Temporary Redirect',
      400 => 'Bad Request',
      401 => 'Unauthorized',
      402 => 'Payment Required',
      403 => 'Forbidden',
      404 => 'Not Found',
      405 => 'Method Not Allowed',
      406 => 'Not Acceptable',
      407 => 'Proxy Authentication Required',
      408 => 'Request Timeout',
      409 => 'Conflict',
      410 => 'Gone',
      411 => 'Length Required',
      412 => 'Precondition Failed',
      413 => 'Request Entity Too Large',
      414 => 'Request-URI Too Long',
      415 => 'Unsupported Media Type',
      416 => 'Requested Range Not Satisfiable',
      417 => 'Expectation Failed',
      418 => 'I\'m a teapot',
      422 => 'Unprocessable Entity',
      423 => 'Locked',
      424 => 'Failed Dependency',
      425 => 'Unordered Collection',
      426 => 'Upgrade Required',
      449 => 'Retry With',
      450 => 'Blocked by Windows Parental Controls',
      500 => 'Internal Server Error',
      501 => 'Not Implemented',
      502 => 'Bad Gateway',
      503 => 'Service Unavailable',
      504 => 'Gateway Timeout',
      505 => 'HTTP Version Not Supported',
      506 => 'Variant Also Negotiates',
      507 => 'Insufficient Storage',
      509 => 'Bandwidth Limit Exceeded',
      510 => 'Not Extended'
    ];
  }

  public static function exit_with_status($status, $message = '') {
    $codes = self::http_status_codes();
    header("HTTP/1.1 {$status} {$codes[$status]}", true, $status);
    exit($message);
  }

  public static function rewriting_on() {
    $permalink_structure = get_option('permalink_structure');

    return ($permalink_structure && !empty($permalink_structure));
  }

  // Returns a list of just user data from the wp_users table
  public static function get_raw_users($where = '', $order_by = 'user_login') {
    global $wpdb;

    static $raw_users;

    if(!isset($raw_users)) {
      $where    = ((empty($where))?'':" WHERE {$where}");
      $order_by = ((empty($order_by))?'':" ORDER BY {$order_by}");

      $query = "SELECT * FROM {$wpdb->users}{$where}{$order_by}";
      $raw_users = $wpdb->get_results($query);
    }

    return $raw_users;
  }

  public static function site_domain() {
    return preg_replace('#^https?://(www\.)?([^\?\/]*)#','$2', home_url());
  }

  /**
   * Send an email notification to the admin email address(es)
   *
   * @param string  $subject      The subject of the email
   * @param string  $body         The body of the email
   * @param array   $variables    The variables to replace in the subject and body
   * @param boolean $use_template Whether to wrap the body in the default HTML template
   * @param array   $headers      Additional email headers
   */
  public static function send_admin_email_notification($subject, $body, $variables, $use_template, $headers = []) {
    $options = Options::fetch();

    if($options->admin_email_addresses) {
      self::send_email_notification($options->admin_email_addresses, $subject, $body, $variables, $use_template, $headers);
    }
  }

  /**
   * Send an email notification
   *
   * @param string  $recipient    The email address of the recipient (separate multiples by comma)
   * @param string  $subject      The subject of the email
   * @param string  $body         The body of the email
   * @param array   $variables    The variables to replace in the subject and body
   * @param boolean $use_template Whether to wrap the body in the default HTML template
   * @param array   $headers      Additional email headers
   */
  public static function send_email_notification($recipient, $subject, $body, $variables, $use_template, $headers = []) {
    $headers = array_merge(['Content-Type: text/html'], $headers);
    $variables = array_merge(self::get_default_email_vars(), $variables);
    $body = self::replace_text_variables($body, $variables);
    $subject = self::replace_text_variables($subject, $variables);
    $body = self::has_html_tag($body) ? $body : nl2br($body);

    if(apply_filters('esaf_email_notification_rtl', is_rtl())) {
      $body = sprintf('<div dir="rtl">%s</div>', $body);
    }

    if($use_template) {
      ob_start();
      require ESAF_VIEWS_PATH . '/emails/template.php';
      $body = ob_get_clean();
    }

    $body = apply_filters('esaf_email_notification_body', $body, $subject, $variables, $use_template);

    add_action('phpmailer_init', [self::class, 'set_alt_body']);

    self::wp_mail($recipient, $subject, $body, $headers);

    remove_action('phpmailer_init', [self::class, 'set_alt_body']);
  }

  /**
   * Set a plain text AltBody on the email
   *
   * @param \PHPMailer\PHPMailer\PHPMailer|\PHPMailer $phpmailer
   */
  public static function set_alt_body($phpmailer) {
    $phpmailer->AltBody = wp_specialchars_decode($phpmailer->Body, ENT_QUOTES);
    $phpmailer->AltBody = Utils::convert_to_plain_text($phpmailer->AltBody);
    $phpmailer->AltBody = apply_filters('esaf_email_plaintext_body', $phpmailer->AltBody, $phpmailer);
  }

  /**
   * Convert the given HTML into plain text
   *
   * @param string $text
   * @return string
   */
  public static function convert_to_plain_text($text) {
    $text = preg_replace('~<style[^>]*>[^<]*</style>~','',$text);
    $text = strip_tags($text);
    $text = trim($text);
    $text = preg_replace("~\r~",'',$text); // Make sure we're only dealing with \n's here
    $text = preg_replace("~\n\n+~","\n\n",$text); // reduce 1 or more blank lines to 1

    return $text;
  }

  public static function send_affiliate_sale_notifications($params, $affiliates, $transaction = null) {
    $options = Options::fetch();

    if(!$options->affiliate_email) {
      return;
    }

    foreach($affiliates as $level => $affiliate) {
      // Prevent blocked affiliates from getting emails
      if($affiliate->is_blocked) {
        continue;
      }

      if($affiliate->unsubscribed) {
        continue;
      }

      $curr_percentage = Commission::get_percentage($level, $affiliate, $transaction);

      if((float) $curr_percentage <= 0.00) {
        continue;
      }

      $commission_type = Commission::get_type($affiliate, $transaction);

      $variables = $affiliate->get_email_vars();

      $variables['commission_percentage'] = self::format_float($curr_percentage);
      $variables['commission_amount']     = Commission::calculate($level, $affiliate, $transaction);

      $variables['commission_percentage'] =
        ( $commission_type=='fixed' ? $options->currency_symbol : '' ) .
        $variables['commission_percentage'] .
        ( $commission_type=='percentage' ? '%' : '' );

      $variables['commission_amount']     = AppHelper::format_currency($variables['commission_amount']);
      $variables['payment_level']         = $level + 1; // we're doing 1 based level any time its displayed

      $variables = array_merge($params, $variables);

      self::send_email_notification(
        $affiliate->user_email,
        $options->affiliate_email_subject,
        $options->affiliate_email_body,
        $variables,
        $options->affiliate_email_use_template
      );
    }
  }

  public static function send_admin_sale_notification($variables, $affiliate, $transaction = null) {
    $options = Options::fetch();

    if(!$options->admin_email) {
      return;
    }

    $curr_percentage = Commission::get_percentage(0, $affiliate, $transaction);

    if((float) $curr_percentage <= 0.00) {
      return;
    }

    $commission_type = Commission::get_type($affiliate, $transaction);
    $commission_percentage = self::format_float($curr_percentage);
    $commission_amount_num = Commission::calculate(0, $affiliate, $transaction);
    $commission_amount = AppHelper::format_currency($commission_amount_num);

    $commission_percentage =
      ( $commission_type=='fixed' ? $options->currency_symbol : '' ) .
      $commission_percentage .
      ( $commission_type=='percentage' ? '%' : '' );

    $variables = array_merge(
      $variables,
      $affiliate->get_email_vars(),
      compact('commission_percentage', 'commission_amount_num', 'commission_amount')
    );

    self::send_admin_email_notification(
      $options->admin_email_subject,
      $options->admin_email_body,
      $variables,
      $options->admin_email_use_template
    );
  }

  public static function send_admin_affiliate_applied_notification($app) {
    $options = Options::fetch();

    if(!$options->admin_aff_applied_email_enabled) {
      return;
    }

    $headers = [
      sprintf('Reply-To: %s <%s>',
        $app->first_name . ' ' . $app->last_name,
        $app->email
      )
    ];

    $variables = array_merge(
      $app->get_values(),
      [
        'websites' => nl2br($app->websites),
        'strategy' => nl2br($app->strategy),
        'social' => nl2br($app->social),
        'edit_url' => $app->edit_url()
      ]
    );

    self::send_admin_email_notification(
      $options->admin_aff_applied_email_subject,
      $options->admin_aff_applied_email_body,
      $variables,
      $options->admin_aff_applied_email_use_template,
      $headers
    );
  }

  public static function send_affiliate_approved_notification($app) {
    $options = Options::fetch();

    if(!$options->aff_approved_email_enabled) {
      return;
    }

    $variables = array_merge(
      $app->get_values(),
      [
        'signup_url' => $app->signup_url()
      ]
    );

    self::send_email_notification(
      $app->email,
      $options->aff_approved_email_subject,
      $options->aff_approved_email_body,
      $variables,
      $options->aff_approved_email_use_template
    );
  }

  public static function is_logged_in_and_current_user($user_id) {
    $current_user = self::get_currentuserinfo();

    return (self::is_user_logged_in() and ($current_user->ID == $user_id));
  }

  public static function is_logged_in_and_an_admin() {
    return (self::is_user_logged_in() and self::is_admin());
  }

  public static function is_logged_in_and_a_subscriber() {
    return (self::is_user_logged_in() and self::is_subscriber());
  }

  public static function is_admin() {
    return current_user_can('administrator');
  }

  public static function is_subscriber() {
    return (current_user_can('subscriber') and !current_user_can('contributor'));
  }

  public static function array_to_string($my_array, $debug = false, $level = 0) {
    return self::object_to_string($my_array);
  }

  public static function object_to_string($object) {
    ob_start();
    print_r($object);

    return ob_get_clean();
  }

  public static function replace_text_variables($text, $variables) {
    $patterns = [];
    $replacements = [];

    foreach($variables as $var_key => $var_val) {
      $patterns[] = '/\{\$' . preg_quote( $var_key, '/' ) . '\}/';
      $replacements[] = preg_replace( '/\\$/', '\\\$', $var_val ); // $'s must be escaped for some reason
    }

    $preliminary_text = preg_replace( $patterns, $replacements, $text );

    // Clean up any failed matches
    return preg_replace( '/\{\$.*?\}/', '', $preliminary_text );
  }

  public static function with_default($variable, $default)
  {
    if(isset($variable)) {
      if(is_numeric($variable)) {
        return $variable;
      }
      elseif(!empty($variable)) {
        return $variable;
      }
    }

    return $default;
  }

  public static function format_float($number, $num_decimals = 2) {
    return number_format((float)$number, $num_decimals, '.', '');
  }

  public static function is_subdir_install() {
    return preg_match( '#^https?://[^/]+/.+$#', home_url() );
  }

  /**
   * Get the URL for the Dashboard page
   *
   * @param array $args
   * @return string
   */
  public static function dashboard_url($args = []) {
    $options = Options::fetch();

    if($options->dashboard_page_id > 0) {
      $url = get_permalink($options->dashboard_page_id);
    }

    if(empty($url) || !is_string($url)) {
      $url = home_url();
    }

    if(count($args)) {
      $url = add_query_arg($args, $url);
    }

    return $url;
  }

  /**
   * Get the URL for the Signup page
   *
   * @param array $args
   * @return string
   */
  public static function signup_url($args = []) {
    $options = Options::fetch();

    if($options->signup_page_id > 0) {
      $url = get_permalink($options->signup_page_id);
    }

    if(empty($url) || !is_string($url)) {
      $url = wp_login_url();
      $args['action'] = 'register';
    }

    if(count($args)) {
      $url = add_query_arg($args, $url);
    }

    return $url;
  }

  /**
   * Get the URL for the Login page
   *
   * @param array $args
   * @return string
   */
  public static function login_url($args = []) {
    $options = Options::fetch();

    if($options->login_page_id > 0) {
      $url = get_permalink($options->login_page_id);
    }

    if(empty($url) || !is_string($url)) {
      $url = wp_login_url(self::dashboard_url());
    }

    if(count($args)) {
      $url = add_query_arg($args, $url);
    }

    return $url;
  }

  public static function logout_url() {
    return wp_logout_url(self::login_url());
  }

  public static function get_pages() {
    global $wpdb;

    $query = "SELECT * FROM {$wpdb->posts} WHERE post_status=%s AND post_type=%s";

    $query = $wpdb->prepare( $query, "publish", "page" );

    $results = $wpdb->get_results( $query );

    if($results) {
      return $results;
    }
    else {
      return [];
    }
  }

  /**
  * Formats a line (passed as a fields array) as CSV and returns the CSV as a string.
  * Adapted from http://us3.php.net/manual/en/function.fputcsv.php#87120
  */
  public static function to_csv( $struct,
                                 $delimiter = ',',
                                 $enclosure = '"',
                                 $enclose_all = false,
                                 $null_to_mysql_null = false ) {
    $delimiter_esc = preg_quote($delimiter, '/');
    $enclosure_esc = preg_quote($enclosure, '/');

    $csv = '';
    $line_num = 0;

    if((!is_array($struct) and !is_object($struct)) or empty($struct)) { return $csv; }

    foreach( $struct as $line ) {
      $output = [];

      foreach( $line as $field ) {
        if( is_null($field) and $null_to_mysql_null ) {
          $output[] = 'NULL';
          continue;
        }

        // Enclose fields containing $delimiter, $enclosure or whitespace
        if( $enclose_all or preg_match( "/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field ) )
          $output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
        else
          $output[] = $field;
      }

      $csv .= implode( $delimiter, $output ) . "\n";
      $line_num++;
    }

    return $csv;
  }

  public static function random_string($length = 10, $lowercase = true, $uppercase = false, $symbols = false ) {
    $characters = '0123456789';
    $characters .= $uppercase?'ABCDEFGHIJKLMNOPQRSTUVWXYZ':'';
    $characters .= $lowercase?'abcdefghijklmnopqrstuvwxyz':'';
    $characters .= $symbols?'@#*^%$&!':'';
    $string = '';
    $max_index = strlen($characters) - 1;

    for($p = 0; $p < $length; $p++) {
      $string .= $characters[mt_rand(0, $max_index)];
    }

    return $string;
  }

  /* Keys to indexes, indexes to keys ... do it! */
  public static function array_invert($invertable) {
    $inverted = [];

    foreach( $invertable as $key => $orray ) {
      foreach($orray as $index => $value) {
        if(!isset($inverted[$index])) { $inverted[$index] = []; }
        $inverted[$index][$key] = $value;
      }
    }

    return $inverted;
  }

  public static function blogurl() {
    return ((get_option('home'))?get_option('home'):get_option('siteurl'));
  }

  public static function siteurl() {
    return get_option('siteurl');
  }

  public static function blogname() {
    return wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
  }

  public static function blogdescription() {
    return get_option('blogdescription');
  }

  public static function db_date_to_ts($mysql_date) {
    return strtotime($mysql_date);
  }

  public static function ts_to_mysql_date($ts, $format='Y-m-d H:i:s') {
    return gmdate($format, $ts);
  }

  public static function db_now($format='Y-m-d H:i:s') {
    return self::ts_to_mysql_date(time(),$format);
  }

  public static function db_lifetime() {
    return '0000-00-00 00:00:00';
  }

  public static function error_log($error) {
    if(is_array($error) || is_object($error)) {
      $error = json_encode($error);
    }

    error_log(sprintf(__("*** Easy Affiliate Error\n==========\n%s\n==========\n", 'easy-affiliate'),$error));
  }

  public static function debug_log($message) {
    if(is_array($message) || is_object($message)) {
      $message = json_encode($message);
    }

    //Getting some complaints about using WP_DEBUG here
    if(defined('WP_ESAF_DEBUG') && WP_ESAF_DEBUG) {
      error_log(sprintf(__('*** Easy Affiliate Debug: %s', 'easy-affiliate'), $message));
    }
  }

  public static function filter_array_keys($sarray, $keys) {
    $rarray = [];
    foreach($sarray as $key => $value) {
      if(in_array($key, $keys)) {
        $rarray[$key] = $value;
      }
    }
    return $rarray;
  }

  public static function get_property($className, $property) {
    if(!class_exists($className)) { return null; }
    if(!property_exists($className, $property)) { return null; }

    $vars = get_class_vars($className);

    return $vars[$property];
  }

  public static function get_static_property($className, $property) {
    $r = new \ReflectionClass($className);
    return $r->getStaticPropertyValue($property);
  }

  public static function is_associative_array($arr) {
    return array_keys($arr) !== range(0, count($arr) - 1);
  }

  public static function protocol() {
    if( is_ssl() ||
        ( defined('MEPR_SECURE_PROXY') && //USER must define this in wp-config.php if they're doing HTTPS between the proxy
          isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
          strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https' ) ) {
      return 'https';
    }
    else {
      return 'http';
    }
  }

  public static function current_url($handle_query='show-query') {
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];

    if($handle_query!=='show-query') {
      $uri = preg_replace('/\?.*/','',$uri);
    }

    return self::protocol() . "://{$host}{$uri}";
  }

  public static function is_ssl() {
    return (self::protocol() === 'https');
  }

  // Special handling for protocol
  public static function get_permalink($id = 0, $leavename = false) {
    $permalink = get_permalink($id, $leavename);

    if(self::is_ssl()) {
      $permalink = preg_replace( '!^https?://!', 'https://', $permalink );
    }

    return $permalink;
  }

  /* PLUGGABLE FUNCTIONS AS TO NOT STEP ON OTHER PLUGINS' CODE */
  public static function get_currentuserinfo() {
    self::_include_pluggables('wp_get_current_user');
    $current_user = wp_get_current_user();

    if(isset($current_user->ID) && $current_user->ID > 0) {
      return new User($current_user->ID);
    }
    else {
      return false;
    }
  }

  public static function get_userdata($id) {
    self::_include_pluggables('get_userdata');
    $data = get_userdata($id);
    // Handle the returned object for wordpress > 3.2
    if (!empty($data->data)) {
      return $data->data;
    }
    return $data;
  }

  public static function get_user_by($field, $value) {
    self::_include_pluggables('get_user_by');
    return get_user_by($field, $value);
  }

  public static function get_userdatabylogin($screenname) {
    self::_include_pluggables('get_user_by');
    $data = get_user_by('login', $screenname);
    //$data = get_userdatabylogin($screenname);
    // Handle the returned object for wordpress > 3.2
    if(isset($data->data) and !empty($data->data)) {
      return $data->data;
    }
    return $data;
  }

  public static function get_wafp_admin_capability() {
    return apply_filters('esaf-admin-capability', 'remove_users');
  }

  public static function is_wafp_admin($user_id=null) {
    $wafp_cap = self::get_wafp_admin_capability();

    if(empty($user_id)) {
      return self::current_user_can($wafp_cap);
    }
    else {
      return user_can($user_id, $wafp_cap);
    }
  }

  public static function current_user_can($role) {
    self::_include_pluggables('wp_get_current_user');
    return current_user_can($role);
  }

  public static function minutes($n = 1) {
    return $n * 60;
  }

  public static function hours($n = 1) {
    return $n * self::minutes(60);
  }

  public static function days($n = 1) {
    return $n * self::hours(24);
  }

  public static function weeks($n = 1) {
    return $n * self::days(7);
  }

  public static function months($n, $month_timestamp = false, $backwards = false) {
    $month_timestamp = empty($month_timestamp) ? time() : $month_timestamp;
    $seconds = 0;

    // If backward we start in the previous month
    if($backwards) {
      $month_timestamp -= self::days((int)date('t', $month_timestamp));
    }

    for($i=0; $i < $n; $i++) {
      $month_seconds = self::days((int)date('t', $month_timestamp));
      $seconds += $month_seconds;

      // We want the months going into the past
      if($backwards) {
        $month_timestamp -= $month_seconds;
      }
      else { // We want the months going into the past
        $month_timestamp += $month_seconds;
      }
    }

    return $seconds;
  }

  public static function years($n, $year_timestamp = false, $backwards = false) {
    $year_timestamp = empty($year_timestamp) ? time() : $year_timestamp;
    $seconds = 0;

    // If backward we start in the previous year
    if($backwards) {
      $year_timestamp -= self::days((int)date('t', $year_timestamp));
    }

    for($i=0; $i < $n; $i++) {
      $seconds += $year_seconds = self::days(365 + (int)date('L', $year_timestamp));
      // We want the years going into the past
      if($backwards) {
        $year_timestamp -= $year_seconds;
      }
      else { // We want the years going into the past
        $year_timestamp += $year_seconds;
      }
    }

    return $seconds;
  }

  public static function wp_mail_to_admin($subject, $message, $headers = '', $attachments = []) {
    $options = Options::fetch();

    if($options->admin_email_addresses) {
      self::wp_mail($options->admin_email_addresses, $subject, $message, $headers, $attachments);
    }
  }

  public static function wp_mail($recipient, $subject, $message, $headers = '', $attachments = []) {
    self::_include_pluggables('wp_mail');

    // Parse shortcodes in the message body
    $message = do_shortcode($message);

    add_filter('wp_mail_from_name', [self::class, 'set_mail_from_name']);
    add_filter('wp_mail_from', [self::class, 'set_mail_from_address']);
    add_action('phpmailer_init', [self::class, 'reset_alt_body'], 5);

    // We just send individual emails
    $recipients = explode(',', $recipient);

    foreach($recipients as $to) {
      $recipient = trim($to);

      //Let's get rid of the pretty TO's -- causing too many problems
      //mbstring?
      if(extension_loaded('mbstring')) {
        if(mb_strpos($recipient, '<') !== false) {
          $recipient = mb_substr($recipient, (mb_strpos($recipient, '<') + 1), -1);
        }
      }
      else {
        if(strpos($recipient, '<') !== false) {
          $recipient = substr($recipient, (strpos($recipient, '<') + 1), -1);
        }
      }

      wp_mail($recipient, $subject, $message, $headers, $attachments);
    }

    remove_action('phpmailer_init', [self::class, 'reset_alt_body'], 5);
    remove_filter('wp_mail_from', [self::class, 'set_mail_from_address']);
    remove_filter('wp_mail_from_name', [self::class, 'set_mail_from_name']);
  }

  /**
   * Set the email "From" name
   *
   * @param string $name
   * @return string
   */
  public static function set_mail_from_name($name) {
    $options = Options::fetch();

    if($options->email_from_name) {
      $name = $options->email_from_name;
    }

    return $name;
  }

  /**
   * Set the email "From" address
   *
   * @param string $email
   * @return string
   */
  public static function set_mail_from_address($email) {
    $options = Options::fetch();

    if(is_email($options->email_from_address)) {
      $email = $options->email_from_address;
    }

    return $email;
  }

  /**
   * Make sure to reset the AltBody or it can contain remnants of other emails already sent in the same request
   *
   * @param \PHPMailer\PHPMailer\PHPMailer|\PHPMailer $phpmailer
   */
  public static function reset_alt_body($phpmailer) {
    $phpmailer->AltBody = '';
  }

  public static function is_user_logged_in() {
    self::_include_pluggables('is_user_logged_in');
    return is_user_logged_in();
  }

  public static function get_avatar($id, $size) {
    self::_include_pluggables('get_avatar');
    return get_avatar($id, $size);
  }

  public static function wp_hash_password($password_str) {
    self::_include_pluggables('wp_hash_password');
    return wp_hash_password($password_str);
  }

  public static function wp_generate_password($length, $special_chars) {
    self::_include_pluggables('wp_generate_password');
    return wp_generate_password($length, $special_chars);
  }

  public static function wp_redirect($location, $status = 302) {
    self::_include_pluggables('wp_redirect');
    return wp_redirect($location, $status);
  }

  public static function wp_salt($scheme = 'auth') {
    self::_include_pluggables('wp_salt');
    return wp_salt($scheme);
  }

  public static function check_ajax_referer($slug, $param) {
    self::_include_pluggables('check_ajax_referer');
    return check_ajax_referer($slug, $param);
  }

  public static function check_admin_referer($slug, $param) {
    self::_include_pluggables('check_admin_referer');
    return check_admin_referer($slug, $param);
  }

  public static function _include_pluggables($function_name) {
    if(!function_exists($function_name)) {
      require_once ABSPATH . WPINC . '/pluggable.php';
    }
  }

  public static function wp_body_open() {
    if(function_exists('wp_body_open')) {
      wp_body_open();
    }
    else {
      do_action('wp_body_open');
    }
  }

  public static function get_current_screen_id() {
    global $current_screen;

    if ($current_screen instanceof \WP_Screen) {
      return $current_screen->id;
    }

    return null;
  }

  /**
   * Sanitizes a multiline string
   *
   * This function exists because sanitize_textarea_field() is WP 4.7+ only
   *
   * @param   string  $str
   * @return  string
   */
  public static function sanitize_textarea_field($str) {
    if (function_exists('sanitize_textarea_field')) {
      return sanitize_textarea_field($str);
    }

    return join("\n", array_map('sanitize_text_field', explode("\n", $str)));
  }

  /**
   * Ensure the given number $x is between $min and $max inclusive
   *
   * @param   mixed  $x
   * @param   mixed  $min
   * @param   mixed  $max
   * @return  mixed
   */
  public static function clamp($x, $min, $max) {
    return min(max($x, $min), $max);
  }

  /**
   * Get the date format from the WP settings
   *
   * @return string
   */
  public static function get_date_format() {
    $date_format = get_option('date_format');

    if(empty($date_format)) {
      $date_format = 'm/d/Y';
    }

    return $date_format;
  }

  /**
   * Get the time format from the WP settings
   *
   * @return string
   */
  public static function get_time_format() {
    $time_format = get_option('time_format');

    if(empty($time_format)) {
      $time_format = 'H:i';
    }

    return $time_format;
  }

  /**
   * Get the number of items per page for the current screen
   *
   * @param string $fallback The fallback usermeta meta_key if the current screen is null (e.g. when exporting)
   * @return int
   */
  public static function get_per_page_screen_option($fallback = null) {
    $screen = get_current_screen();

    if($screen instanceof \WP_Screen) {
      $per_page = get_user_meta(
        get_current_user_id(),
        $screen->get_option('per_page', 'option'),
        true
      );

      if(is_numeric($per_page)) {
        $per_page = (int) $per_page;

        if($per_page >= 1 && $per_page <= 999) {
          return $per_page;
        }
      }

      return $screen->get_option('per_page', 'default');
    }
    elseif($fallback) {
      $per_page = (int) get_user_meta(get_current_user_id(), $fallback, true);

      if($per_page >= 1 && $per_page <= 999) {
        return $per_page;
      }
    }

    return 10;
  }

  public static function is_addon_active($addon) {
    switch($addon) {
      case 'commission-rules':
        return defined('EACR_VERSION');
      case 'commission-levels':
        return defined('EACL_VERSION');
      case 'activecampaign':
        return defined('EAAC_VERSION');
      case 'convertkit':
        return defined('EACK_VERSION');
      case 'mailchimp':
        return defined('EAMC_VERSION');
      case 'getresponse':
        return defined('EAGR_VERSION');
    }

    return false;
  }

  /**
   * Get the JSON decoded response from a WP remote response
   *
   * @param array $response The response from wp_remote_request
   * @return mixed|null The JSON decoded response, or null on failure
   */
  public static function json_decode_response($response) {
    $body = wp_remote_retrieve_body($response);

    if(is_string($body) && $body !== '') {
      return json_decode($body, true);
    }

    return null;
  }

  /**
   * Does the given string contain at least one HTML tag?
   *
   * @param string $string
   * @return boolean
   */
  public static function has_html_tag($string) {
    return preg_match('/<[^<]+>/', $string);
  }

  /**
   * Get the array of default email variables
   *
   * @param boolean $include_deprecated
   * @return array
   */
  public static function get_default_email_vars($include_deprecated = true) {
    $variables = [
      'site_name' => self::blogname(),
      'site_url' => self::siteurl(),
      'login_url' => self::login_url(),
      'signup_url' => self::signup_url(),
      'dashboard_url' => self::dashboard_url(),
      'remote_ip_addr' => $_SERVER['REMOTE_ADDR']
    ];

    if($include_deprecated) {
      // We don't want to show deprecated variables in the insert variable menu but they still need to work
      $variables = array_merge($variables, [
        'blogname' => self::blogname()
      ]);
    }

    return apply_filters('esaf_default_email_vars', $variables, $include_deprecated);
  }

  /**
   * Get the array of all countries
   *
   * @param bool $prioritize_my_country
   * @return array
   */
  public static function get_countries($prioritize_my_country = true) {
    $options = Options::fetch();
    $countries = include ESAF_I18N_PATH . '/countries.php';

    if($prioritize_my_country) {
      $country_code = $options->business_address_country;

      if(!empty($country_code) && isset($countries[$country_code])) {
        $my_country = [$country_code => $countries[$country_code]];
        unset($countries[$country_code]);
        $countries = array_merge($my_country, $countries);
      }
    }

    return apply_filters('esaf_countries', $countries);
  }

  /**
   * Formats and translates the given date string and converts to the configured WP timezone
   *
   * @param string        $date     The date to format
   * @param string        $format   The date format, leave blank to use the WP date format
   * @param \DateTimeZone $timezone The timezone of the returned date, will default to the WP timezone if omitted
   * @return string|false           The formatted date or false if there was an error
   */
  public static function format_date($date, $format = '', \DateTimeZone $timezone = null) {
    try {
      if(empty($format)) {
        $format = self::get_date_format();
      }

      return self::date($format, new \DateTime($date), $timezone);
    }
    catch(\Exception $e) {
      return false;
    }
  }

  /**
   * Converts the given date string to a time string in the configured WP timezone
   *
   * @param string        $date   The date to format
   * @return string|false         The formatted date or false if there was an error
   */
  public static function format_time($date) {
    return self::format_date($date, self::get_time_format());
  }

  /**
   * Converts the given date string to a datetime string in the configured WP timezone
   *
   * @param string        $date   The date to format
   * @return string|false         The formatted date or false if there was an error
   */
  public static function format_datetime($date) {
    return self::format_date($date, self::get_date_format() . ' ' . self::get_time_format());
  }

  /**
   * Formats and translates a date or time
   *
   * @param string                  $format   The format of the returned date
   * @param \DateTimeInterface|null $date     The DateTime or DateTimeImmutable instance representing the moment of time in UTC, or null to use the current time
   * @param \DateTimeZone|null      $timezone The timezone of the returned date, will default to the WP timezone if omitted
   * @return string|false                     The formatted date or false if there was an error
   */
  public static function date($format, \DateTimeInterface $date = null, \DateTimeZone $timezone = null) {
    if(!$date) {
      $date = date_create('@' . time());

      if(!$date) {
        return false;
      }
    }

    $timestamp = $date->getTimestamp();

    if($timestamp === false || !function_exists('wp_date')) {
      $timezone = $timezone ? $timezone : self::get_timezone();
      $date->setTimezone($timezone);

      return $date->format($format);
    }

    return wp_date($format, $timestamp, $timezone);
  }

  /**
   * Get the WP timezone as a DateTimeZone instance
   *
   * Duplicate of wp_timezone() for WP <5.3.
   *
   * @return \DateTimeZone
   */
  public static function get_timezone() {
    if(function_exists('wp_timezone')) {
      return wp_timezone();
    }

    $timezone_string = get_option('timezone_string');

    if($timezone_string) {
      return new \DateTimeZone($timezone_string);
    }

    $offset  = (float) get_option('gmt_offset');
    $hours   = (int) $offset;
    $minutes = ($offset - $hours);

    $sign      = ($offset < 0) ? '-' : '+';
    $abs_hour  = abs($hours);
    $abs_mins  = abs($minutes * 60);
    $tz_offset = sprintf('%s%02d:%02d', $sign, $abs_hour, $abs_mins);

    return new \DateTimeZone($tz_offset);
  }

  /**
   * Set a cookie
   *
   * @param string $name     The name of the cookie
   * @param string $value    The value of the cookie
   * @param int    $expires  The time the cookie expires as Unix timestamp
   * @param bool   $secure   Send the cookie over HTTPS only
   * @param bool   $httponly Make the cookie only accessible over the HTTP protocol
   * @param string $samesite The SameSite attribute
   */
  public static function set_cookie($name, $value = '', $expires = 0, $secure = false, $httponly = false, $samesite = 'Lax') {
    if(version_compare(phpversion(), '7.3.0', '>=')) {
      $options = compact('expires', 'secure', 'httponly', 'samesite');
      $options['path'] = COOKIEPATH;
      $options['domain'] = COOKIE_DOMAIN;

      setcookie($name, $value, $options);
    }
    else {
      setcookie($name, $value, $expires, COOKIEPATH, COOKIE_DOMAIN, $secure, $httponly);
    }
  }

  /**
   * Are we currently on one of the Pro Dashboard pages?
   *
   * @return bool
   */
  public static function is_pro_dashboard_page() {
    $options = Options::fetch();

    return $options->pro_dashboard_enabled && (self::is_dashboard_page() || self::is_signup_page() || self::is_login_page());
  }

  /**
   * Is the current page the Dashboard page?
   *
   * @return bool
   */
  public static function is_dashboard_page() {
    $queried_object = get_queried_object();

    if($queried_object instanceof \WP_Post && $queried_object->post_type == 'page') {
      $options = Options::fetch();

      return $options->dashboard_page_id > 0 && $queried_object->ID == $options->dashboard_page_id;
    }

    return false;
  }

  /**
   * Is the current page the Signup page?
   *
   * @return bool
   */
  public static function is_signup_page() {
    $queried_object = get_queried_object();

    if($queried_object instanceof \WP_Post && $queried_object->post_type == 'page') {
      $options = Options::fetch();

      return $options->signup_page_id > 0 && $queried_object->ID == $options->signup_page_id;
    }

    return false;
  }

  /**
   * Is the current page the Login page?
   *
   * @return bool
   */
  public static function is_login_page() {
    $queried_object = get_queried_object();

    if($queried_object instanceof \WP_Post && $queried_object->post_type == 'page') {
      $options = Options::fetch();

      return $options->login_page_id > 0 && $queried_object->ID == $options->login_page_id;
    }

    return false;
  }

  /**
   * Is the user agent a known bot?
   *
   * @param string $user_agent
   * @return bool
   */
  public static function is_user_agent_bot($user_agent) {
    $regex = apply_filters(
      'esaf_bot_user_agents',
      'Googlebot|facebookexternalhit|Google-AMPHTML|s~amp-validator|AdsBot|Google Keyword Suggestion|' .
      'Facebot|YandexBot|YandexMobileBot|bingbot|ia_archiver|AhrefsBot|Ezooms|GSLFbot|WBSearchBot|Twitterbot|' .
      'TweetmemeBot|Twikle|PaperLiBot|Wotbox|UnwindFetchor|Exabot|MJ12bot|YandexImages|TurnitinBot|Pingdom|' .
      'contentkingapp|AspiegelBot|Barkrowler|hypefactors|serendeputy|Sogou web spider|SemrushBot|Qwantify|CCBot|' .
      'Re-re Studio|startmebot|brokenlinkcheck|Xenu Link Sleuth|BLEXBot|Jooblebot|AwarioSmartBot|FeedFetcher-Google|' .
      'LivelapBot|TrendsmapResolver|PetalBot|Pinterestbot|Applebot|MojeekBot|SeznamBot|bitlybot|archive\.org_bot|' .
      'MTRobot|serpstatbot|Yahoo! Slurp|Baiduspider|FeedBurner|CloudFlare-AlwaysOnline|DotBot|Mail\.RU_Bot|' .
      'Domain Re-Animator Bot|EasouSpider|Flamingo_SearchEngine|MauiBot|EveryoneSocialBot|AndersPinkBot'
    );

    $is_bot = (bool) preg_match(sprintf('#%s#is', $regex), $user_agent);

    return apply_filters('esaf_is_bot_user_agent', $is_bot);
  }
}
