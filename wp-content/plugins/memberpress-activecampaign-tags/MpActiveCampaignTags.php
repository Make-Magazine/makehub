<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/*
Integration of ActiveCampaign (Tags) into MemberPress
*/
class MpActiveCampaignTags {
  public function __construct() {
    // Storing fields
    add_action('mepr_display_autoresponders',   array($this, 'display_option_fields'));
    add_action('mepr-process-options',          array($this, 'store_option_fields'));
    add_action('mepr-user-signup-fields',       array($this, 'display_signup_field'));
    add_action('mepr-product-advanced-metabox', array($this, 'display_membership_options'));
    add_action('mepr-product-save-meta',        array($this, 'save_membership_options'));
    add_filter('mepr-validate-account',         array($this, 'update_user_email'), 10, 2); //Need to use this hook to get old and new emails

    // Signup
    add_action('mepr-signup-user-loaded', array($this, 'process_signup'));

    // Updating tags
    add_action('mepr-account-is-active',   array($this, 'add_tags'));
    add_action('mepr-account-is-inactive', array($this, 'remove_tags'));

    // Enqueue scripts
    add_action('mepr-options-admin-enqueue-script', array($this,'admin_enqueue_options_scripts'));
    add_action('mepr-product-admin-enqueue-script', array($this,'admin_enqueue_product_scripts'));

    // AJAX Endpoints
    add_action('wp_ajax_mepr_activecampaign_ping_apikey', array($this, 'ajax_ping_apikey'));
    add_action('wp_ajax_mepr_activecampaign_get_lists',   array($this, 'ajax_get_lists'));
    add_action('wp_ajax_mepr_activecampaign_get_forms',   array($this, 'ajax_get_forms'));

    // Admin notices
    add_action('admin_notices', array($this, 'maybe_admin_notice'), 3);
  }

  public function maybe_admin_notice() {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    if(is_plugin_active('memberpress-activecampaign/main.php')) {
      deactivate_plugins('memberpress-activecampaign/main.php');
    }

    if(defined('MEPR_VERSION') && version_compare(MEPR_VERSION, '1.7.3', '<')) {
      $class = 'notice notice-error';
      $message = __('Your ActiveCampaign integration with MemberPress may be broken. Please update MemberPress to version 1.7.3 or newer to fix this issue.', 'memberpress-activecampaigntags');

      printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }
  }

  public function admin_enqueue_options_scripts($hook) {
    wp_register_script('mp-activecampaign-js', MPACTIVECAMPAIGNTAGS_URL.'/activecampaign.js');
    wp_enqueue_script('mp-activecampaign-options-js', MPACTIVECAMPAIGNTAGS_URL.'/activecampaign_options.js', array('mp-activecampaign-js'));
    wp_localize_script('mp-activecampaign-options-js', 'MeprActiveCampaign', array('wpnonce' => wp_create_nonce(MEPR_PLUGIN_SLUG)));
  }

  public function admin_enqueue_product_scripts($hook) {
    wp_register_script('mp-activecampaign-js', MPACTIVECAMPAIGNTAGS_URL.'/activecampaign.js');
    wp_enqueue_script('mp-activecampaign-product-js', MPACTIVECAMPAIGNTAGS_URL.'/activecampaign_product.js', array('mp-activecampaign-js'));
  }

  public function update_user_email($errors, $mepr_user) {
    if(!$this->is_enabled_and_authorized() || !empty($errors)) { return $errors; }

    //Check if the email is even changing before we do anything else
    $new_email = stripslashes($_POST['user_email']);

    if($mepr_user->user_email != $new_email) {
      //First let's update the global list_id
      $this->update_subscriber($mepr_user, $this->list_id(), $new_email);

      $products = $mepr_user->active_product_subscriptions('products');

      if(!empty($products)) {
        foreach($products as $prd) {
          $enabled = (bool)get_post_meta($prd->ID, '_mepractivecampaign_list_override', true);
          $list_id = get_post_meta($prd->ID, '_mepractivecampaign_list_override_id', true);

          if($enabled && !empty($list_id)) {
            $this->update_subscriber($mepr_user, $list_id, $new_email);
          }
        }
      }
    }

    return $errors;
  }

  public function display_option_fields() {
    require(MPACTIVECAMPAIGNTAGS_PATH.'/views/options.php');
  }

  public function validate_option_fields($errors) {
    // Nothing to validate yet -- if ever
  }

  public function update_option_fields() {
    // Nothing to do yet -- if ever
  }

  public function store_option_fields() {
    update_option('mepractivecampaign_enabled', (isset($_POST['mepractivecampaign_enabled'])));
    update_option('mepractivecampaign_account', $_POST['mepractivecampaign_account']);
    update_option('mepractivecampaign_api_key', stripslashes($_POST['mepractivecampaign_api_key']));
    update_option('mepractivecampaign_list_id', (isset($_POST['mepractivecampaign_list_id']))?stripslashes($_POST['mepractivecampaign_list_id']):false);
    update_option('mepractivecampaign_form_id', (isset($_POST['mepractivecampaign_form_id']))?stripslashes($_POST['mepractivecampaign_form_id']):false);
    update_option('mepractivecampaign_tags', stripslashes($_POST['mepractivecampaign_tags']));
    update_option('mepractivecampaign_optin', (isset($_POST['mepractivecampaign_optin'])));
    update_option('mepractivecampaign_text', stripslashes($_POST['mepractivecampaign_text']));
  }

  public function display_signup_field() {
    $mepr_options = MeprOptions::fetch();
    $post = MeprUtils::get_current_post();
    $prd = MeprProduct::is_product_page($post);

    //If the per product list is enabled, and the global list is disabled -- then we should be sure the member doesn't see this
    if($prd !== false) {
      $enabled = (bool)get_post_meta($prd->ID, '_mepractivecampaign_list_override', true);

      if($enabled && $mepr_options->disable_global_autoresponder_list) { return; }
    }

    if($this->is_enabled_and_authorized() and $this->is_optin_enabled()) {
      $optin = (MeprUtils::is_post_request())?isset($_POST['mepractivecampaign_opt_in']):$mepr_options->opt_in_checked_by_default;

      ?>
      <div class="mp-form-row">
        <div class="mepr-activecampaign-signup-field">
          <div id="mepr-activecampaign-checkbox">
            <input type="checkbox" name="mepractivecampaign_opt_in" id="mepractivecampaign_opt_in" class="mepr-form-checkbox" <?php checked($optin); ?> />
            <span class="mepr-activecampaign-message"><?php echo $this->optin_text(); ?></span>
          </div>
          <div id="mepr-activecampaign-privacy">
            <small>
              <a href="http://www.activecampaign.com/help/privacy-policy/" class="mepr-activecampaign-privacy-link" target="_blank"><?php _e('We Respect Your Privacy', 'memberpress-activecampaign', 'memberpress-activecampaigntags'); ?></a>
            </small>
          </div>
        </div>
      </div>
    <?php
    }
  }

  public function process_signup($user) {
    $mepr_options = MeprOptions::fetch();
    $enabled = (bool)get_post_meta((int)sanitize_text_field($_POST['mepr_product_id']), '_mepractivecampaign_list_override', true);

    //If the per product tag is enabled, and the global tag is disabled -- then we should be sure the member doesn't get added
    if(!$this->is_enabled_and_authorized() || ($enabled && $mepr_options->disable_global_autoresponder_list)) { return; }

    $this->add_subscriber($user, $this->list_id(), true);
  }

  public function add_tags($txn) {
    $contact = $txn->user();

    $add_tags = (bool)get_post_meta($txn->product_id, '_mepractivecampaign_add_tags', true);
    $tags = get_post_meta($txn->product_id, '_mepractivecampaign_tags', true);

    if($tags != false) { $tags = str_replace(', ', ',', $tags); }

    if(empty($add_tags) || empty($tags)) { return false; }

    $args = array(
      'email' => $contact->user_email,
      'tags' => explode(',',$tags),
    );

    $args = MeprHooks::apply_filters('mepr-activecampaign-add-tags-args', $args, $contact);
    $res = (array)json_decode($this->call('contact_tag_add', $args, 'POST'));

    return ($res['result_code'] == 1);
  }

  public function remove_tags($txn) {
    $contact = $txn->user();

    $add_tags = (bool)get_post_meta($txn->product_id, '_mepractivecampaign_add_tags', true);
    $tags = get_post_meta($txn->product_id, '_mepractivecampaign_tags', true);

    if($tags != false) { $tags = str_replace(', ', ',', $tags); }

    if(empty($add_tags) || empty($tags)) { return false; }

    $args = array(
      'email' => $contact->user_email,
      'tags' => explode(',',$tags),
    );

    $args = MeprHooks::apply_filters('mepr-activecampaign-tags-remove-tags-args', $args, $contact);
    $res = (array)json_decode($this->call('contact_tag_remove', $args, 'POST')); // used to be DELETE - but POST must be used here apparently

    return ($res['result_code'] == 1);
  }

  public function validate_signup_field($errors) {
    // Nothing to validate -- if ever
  }

  public function display_membership_options($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    $add_tags = (bool)get_post_meta($product->ID, '_mepractivecampaign_add_tags', true);
    $tags = get_post_meta($product->ID, '_mepractivecampaign_tags', true);

    require(MPACTIVECAMPAIGNTAGS_PATH.'/views/membership.php');
  }

  public function save_membership_options($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    if(isset($_POST['mepractivecampaign_add_tags'])) {
      update_post_meta($product->ID, '_mepractivecampaign_add_tags', true);
      update_post_meta($product->ID, '_mepractivecampaign_tags', stripslashes($_POST['mepractivecampaign_tags']));
    }
    else {
      update_post_meta($product->ID, '_mepractivecampaign_add_tags', false);
    }
  }

  public function ping_apikey() {
    return $this->call('account_view', array(), 'GET', '', '');
  }

  public function ajax_ping_apikey() {
    // Validate nonce and user capabilities
    if(!isset($_REQUEST['wpnonce']) || !wp_verify_nonce($_REQUEST['wpnonce'], MEPR_PLUGIN_SLUG) || !MeprUtils::is_mepr_admin()) {
      die(json_encode(array('error' => __('Hey yo, why you creepin\'?', 'memberpress-activecampaigntags'), 'type' => 'memberpress')));
    }

    // Validate inputs
    if(!isset($_REQUEST['apikey']) || !isset($_REQUEST['account'])) {
      die(json_encode(array('error' => __('No apikey code was sent', 'memberpress-activecampaigntags'), 'type' => 'memberpress')));
    }

    die($this->call('account_view', array(), 'GET', $_REQUEST['account'], $_REQUEST['apikey']));
  }

  public function ajax_get_lists() {
    $args = array("ids" => "all"); //A comma-separated list of subscription form ID's of lists you wish to view. Pass "all" to view all lists.

    // Validate nonce and user capabilities
    if(!isset($_POST['wpnonce']) || !wp_verify_nonce($_POST['wpnonce'], MEPR_PLUGIN_SLUG) || !MeprUtils::is_mepr_admin()) {
      die(json_encode(array('error' => __('Hey yo, why you creepin\'?', 'memberpress-activecampaigntags'), 'type' => 'memberpress')));
    }

    // Validate inputs
    if(!isset($_POST['apikey']) || !isset($_POST['account'])) {
      die(json_encode(array('error' => __('No apikey code was sent', 'memberpress-activecampaigntags'), 'type' => 'memberpress')));
    }

    die($this->call('list_list', $args, 'GET', $_POST['account'], $_POST['apikey']));
  }

  public function ajax_get_forms() {
    $args = array();

    // Validate nonce and user capabilities
    if(!isset($_REQUEST['wpnonce']) || !wp_verify_nonce($_REQUEST['wpnonce'], MEPR_PLUGIN_SLUG) || !MeprUtils::is_mepr_admin()) {
      die(json_encode(array('error' => __('Hey yo, why you creepin\'?', 'memberpress-activecampaigntags'), 'type' => 'memberpress')));
    }

    // Validate inputs
    if(!isset($_REQUEST['apikey']) || !isset($_REQUEST['account'])) {
      die(json_encode(array('error' => __('No apikey code was sent', 'memberpress-activecampaigntags'), 'type' => 'memberpress')));
    }

    die($this->call('form_getforms', $args, 'GET'));
  }

  public function add_subscriber(MeprUser $contact, $list_id, $is_signup = false) {
    $args = array(
      'email' => $contact->user_email,
      'p' => array($list_id => $list_id),   // list ID: p[1] = 1
      'status' => array($list_id => 1),     // 1: active, 2: unsubscribed
      'instantresponders' => array($list_id => 1), // Whether or not to set "send instant responders." Examples: 1 = yes, 0 = no.
      'first_name' => $contact->first_name,
      'last_name' => $contact->last_name,
      'ip4' => $contact->user_ip
    );

    // Only add the global tag on signup if the user checks the box, or the opt-in box is hidden
    if($is_signup && (!$this->is_optin_enabled() || ($this->is_optin_enabled() && isset($_POST['mepractivecampaign_opt_in'])))) {
      $global_tags = $this->global_tags();
      if(!empty($global_tags)) {
        $args['tags'] = $global_tags;
      }
    }

    $form_id = $this->form_id();
    if(!empty($form_id)) {
      $args['form']=$form_id;
    }

    $args = MeprHooks::apply_filters('mepr-activecampaign-add-subscriber-args', $args, $contact);

    $res = (array)json_decode($this->call('contact_sync', $args, 'POST'));

    return ($res['result_code'] == 1);
  }

  public function update_subscriber(MeprUser $contact, $list_id, $new_email) {
    $email = $contact->user_email;
    $contact_id = $this->contact_id($email);

    $args = array(
      'id' => $contact_id,
      'email' => $new_email,
      'p' => array($list_id => $list_id),
      'overwrite' => 0
    );

    $res = (array)json_decode($this->call('contact_edit', $args, 'POST'));//overwrite=0: only update included post parameters

    return ($res["result_code"] == 1);
  }

  /* unsubscribe */
  public function delete_subscriber(MeprUser $contact, $list_id) {
    $contact_id = $this->contact_id_in_list($list_id, $contact->user_email); //Make sure this person is actually in this list before we remove them
    if(empty($contact_id)) { return false; }

    $args = array(
      'id'          => $contact_id,
      'email'       => $contact->user_email,
      'p'           => array($list_id => $list_id),
      'status'      => array($list_id => 2), // 2: unsubscribed
      'first_name'  => $contact->first_name,
      'last_name'   => $contact->last_name,
      'overwrite'   => 0
    );

    $res = (array)json_decode($this->call('contact_edit', $args, 'POST'));

    return ($res["result_code"] == 1);
  }

  /* re-subscribe the contact to the list they were unsubscribed from */
  public function undelete_subscriber(MeprUser $contact, $list_id, $contact_id) {
    if(empty($contact_id)) { return false; }

    $args = array(
      'id'          => $contact_id,
      'email'       => $contact->user_email,
      'p'           => array($list_id => $list_id),
      'status'      => array($list_id => 1), // 1: subscribed
      'first_name'  => $contact->first_name,
      'last_name'   => $contact->last_name,
      'overwrite'   => 0
    );

    $res = (array)json_decode($this->call('contact_edit', $args, 'POST'));

    return ($res["result_code"] == 1);
  }

  private function call($endpoint, $args=array(), $method='GET', $account=null, $apikey=null) {
    if(is_null($apikey)) { $apikey = $this->apikey(); }
    if(is_null($account)) { $account = $this->account(); }

    $url = "https://{$account}.api-us1.com/admin/api.php/?api_action=" . $endpoint;
    $url .= "&api_key=" . $apikey;
    $url .= "&api_output=json";

    $wp_args = array(
      'timeout' => 60,
      'sslverify' => false,
      'method' => strtoupper($method),
      'body' => array(),
    );

    if(strtoupper($method) == 'GET' || strtoupper($method) == 'DELETE') {
      foreach ($args as $key => $value) {
        $url .= "&" . $key . "=" . $value;
      }
    }
    else {
      $wp_args['body'] = $args;
    }

    $res = wp_remote_request( $url, $wp_args );

    if(!is_wp_error($res)) {
      return $res['body'];
    }
    else {
      return false;
    }
  }

  // I realize these are more like model methods
  // but we want everything centralized here people
  private function is_enabled() {
    return get_option('mepractivecampaign_enabled', false);
  }

  private function is_authorized() {
    $apikey = get_option('mepractivecampaign_api_key', '');
    return !empty($apikey);
  }

  private function is_enabled_and_authorized() {
    return ($this->is_enabled() and $this->is_authorized());
  }

  private function account() {
    return get_option('mepractivecampaign_account', '');
  }

  private function apikey() {
    return get_option('mepractivecampaign_api_key', '');
  }

  private function list_id() {
    return get_option('mepractivecampaign_list_id', false);
  }

  private function form_id() {
    return get_option('mepractivecampaign_form_id', false);
  }

  private function global_tags() {
    return get_option('mepractivecampaign_tags', __('memberpress-active', 'memberpress-activecampaigntags'));
  }

  private function is_optin_enabled() {
    return get_option('mepractivecampaign_optin', true);
  }

  private function optin_text() {
    $default = sprintf(__('Sign Up for the %s Newsletter', 'memberpress-activecampaign', 'memberpress-activecampaigntags'), get_option('blogname'));
    return get_option('mepractivecampaign_text', $default);
  }

  private function contact_id($email){
    $args = array(
      'email' => $email
    );

    $res = (array)json_decode($this->call('contact_view_email',$args));

    if($res["result_code"] == 1) {
      return $res['id'];
    }
    else {
      return false;
    }
  }
} //END CLASS
