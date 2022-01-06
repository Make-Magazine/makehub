<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Lib\CptCtrl;
use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\AffiliateApplication;
use EasyAffiliate\Models\Options;
use EasyAffiliate\Models\User;

class AffiliateApplicationCtrl extends CptCtrl {
  public function load_hooks() {
    $this->ctaxes = [];

    add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
    add_action('save_post', [$this, 'save_postdata']);

    // Add Normal and Sortable Columns
    add_action('manage_' . AffiliateApplication::$cpt . '_posts_custom_column', [$this, 'custom_columns'], 10, 2);
    add_filter('manage_edit-' . AffiliateApplication::$cpt . '_columns', [$this, 'columns']);
    add_filter('manage_edit-' . AffiliateApplication::$cpt . '_sortable_columns', [$this, 'sortable_columns']);

    // Add View/Filter Links at the top of the List page
    add_filter('views_edit-' . AffiliateApplication::$cpt, [$this, 'list_views']);

    // Add Filter dropdown at the top of the List page
    add_action('restrict_manage_posts', [$this,'list_table_filters']);

    // Modify query to filter and add custom orderby's for sortable columns
    add_filter('parse_query', [$this, 'list_table_query']);

    // AJAX endpoints
    add_action('wp_ajax_esaf-update-affiliate-application-status', [$this, 'ajax_update_application_status']);
    add_action('wp_ajax_esaf_resend_affiliate_application_approved_email', [$this, 'ajax_resend_application_approved_email']);

    // Additional Event Actions
    add_action('esaf_event_affiliate-application-submitted', [$this, 'event_application_submitted']);
    add_action('esaf_event_affiliate-application-approved', [$this, 'event_application_approved']);

    add_filter('pre_get_posts', [$this, 'pre_get_posts'], 10, 1);
    add_filter('posts_search', [$this, 'posts_search'], 10, 2);
  }

  public function posts_search($sql, $query) {
    global $pagenow;

    if ($pagenow != 'edit.php') {
      return $sql;
    }

    $q = $query->query_vars;

    if ($q['post_type'] == AffiliateApplication::$cpt && !empty($q['s'])) {
      // Remove search by post_title and post_content statement
      return '';
    }

    return $sql;
  }

  public function pre_get_posts(&$query) {
    global $pagenow;

    if ($pagenow != 'edit.php') {
      return $query;
    }

    $q = $query->query_vars;

    if ($q['post_type'] == AffiliateApplication::$cpt && !empty($q['s'])) {
      // Search by meta
      $meta_query = [];
      $meta_query['relation'] = 'OR';

      foreach (
        [
          '_wafp_affiliateapplication_email',
          '_wafp_affiliateapplication_last_name',
          '_wafp_affiliateapplication_first_name',
        ] as $key) {
        $meta_query[] = [
          'key'       => $key,
          'value'     => $q['s'],
          'compare'   => 'LIKE',
        ];
      }

      $query->query_vars['meta_query'] = $meta_query;
    }

    return $query;
  }

  public function register_post_type() {
    $this->cpt = (object) [
      'slug' => AffiliateApplication::$cpt,
      'config' => [
        'labels' => [
          'name' => esc_html__('Affiliate Applications', 'easy-affiliate'),
          'singular_name' => esc_html__('Affiliate Application', 'easy-affiliate'),
          'add_new_item' => esc_html__('Add New Affiliate Application', 'easy-affiliate'),
          'edit_item' => esc_html__('Edit Affiliate Application', 'easy-affiliate'),
          'new_item' => esc_html__('New Affiliate Application', 'easy-affiliate'),
          'view_item' => esc_html__('View Affiliate Application', 'easy-affiliate'),
          'search_items' => esc_html__('Search Affiliate Applications', 'easy-affiliate'),
          'not_found' => esc_html__('No Affiliate Applications found', 'easy-affiliate'),
          'not_found_in_trash' => esc_html__('No Affiliate Applications found in Trash', 'easy-affiliate'),
          'parent_item_colon' => esc_html__('Parent Affiliate Application:', 'easy-affiliate')
        ],
        'public' => false,
        'publicly_queryable' => false,
        'show_ui' => true,
        'show_in_rest' => false,
        'show_in_menu' => false,
        'has_archive' => false,
        'capability_type' => 'post',
        'hierarchical' => false,
        'register_meta_box_cb' => [$this, 'add_meta_boxes'],
        'rewrite' => false,
        'supports' => false, // support nothing
        'taxonomies' => $this->ctaxes
      ]
    ];

    register_post_type( AffiliateApplication::$cpt, $this->cpt->config );
  }

  public function columns() {
    $columns = [
      'cb'           => '<input type="checkbox" />',
      'ID'           => esc_html__('ID', 'easy-affiliate'),
      'name'         => esc_html__('Name', 'easy-affiliate'),
      'email'        => esc_html__('Email', 'easy-affiliate'),
      'status'       => esc_html__('Status', 'easy-affiliate'),
      'affiliate'    => esc_html__('Affiliate', 'easy-affiliate'),
      'submitted_at' => esc_html__('Submitted', 'easy-affiliate'),
    ];

    return $columns;
  }

  public function sortable_columns($columns) {
    $columns['status'] = 'app_status';

    return $columns;
  }

  private function get_row_actions($app_id) {
    $edit_url = admin_url("post.php?post={$app_id}&action=edit");

    $status_url = admin_url("admin-ajax.php?action=esaf-update-affiliate-application-status&id={$app_id}");
    $approve_url = $status_url . '&status=approved';
    $ignore_url = $status_url . '&status=ignored';

    $output = sprintf(
      '<span class="esaf-aff-app-edit"><a data-id="%s" href="%s">%s</a></span>',
      esc_attr($app_id),
      esc_url($edit_url),
      esc_html__('Edit', 'easy-affiliate')
    );

    $output .= sprintf(
      '<span class="esaf-aff-app-approve">&nbsp;|&nbsp;<a data-id="%s" href="%s">%s</a></span>',
      esc_attr($app_id),
      esc_url($approve_url),
      esc_html__('Approve', 'easy-affiliate')
    );

    $output .= sprintf(
      '<span class="esaf-aff-app-ignore">&nbsp;|&nbsp;<a data-id="%s" href="%s">%s</a></span>',
      esc_attr($app_id),
      esc_url($ignore_url),
      esc_html__('Ignore', 'easy-affiliate')
    );

    $output .= sprintf(
      '<span class="esaf-aff-app-resend-approved-email">&nbsp;|&nbsp;<a data-id="%s" href="">%s</a></span>',
      esc_attr($app_id),
      esc_html__('Resend Approved Email', 'easy-affiliate')
    );

    return $output;
  }

  public function custom_columns($column, $post_id) {
    $app = new AffiliateApplication($post_id);

    if($app->ID !== null) {
      switch($column) {
        case 'ID':
          echo esc_html($app->ID); break;
        case 'name':
          ?>
          <div class="esaf-cpt-row-title esaf-bold">
            <a href="<?php echo esc_url(admin_url("post.php?post={$app->ID}&action=edit")); ?>"><?php echo esc_html("{$app->last_name}, {$app->first_name}"); ?></a>
          </div>
          <div class="esaf-cpt-row-actions-wrapper">
            <span class="esaf-cpt-row-actions" id="esaf-cpt-row-actions-<?php echo esc_attr($app->ID); ?>" data-status="<?php echo esc_attr($app->status); ?>" data-id="<?php echo esc_attr($app->ID); ?>" data-affiliate="<?php echo esc_attr($app->affiliate); ?>">
              <?php echo $this->get_row_actions($app->ID); ?>
            </span>
            &nbsp;
          </div>
          <?php
          break;
        case 'email':
          ?><a href="<?php echo esc_url("mailto:{$app->email}"); ?>"><?php echo esc_html($app->email); ?></a><?php
          break;
        case 'status':
          if($app->status=='pending') {
            ?><span id="esaf-app-status-<?php echo esc_attr($app->ID); ?>" class="esaf-red esaf-bold"><?php esc_html_e('Awaiting Approval', 'easy-affiliate'); ?></span><?php
          }
          else if($app->status=='approved') {
            ?><span id="esaf-app-status-<?php echo esc_attr($app->ID); ?>" class="esaf-green esaf-bold"><?php esc_html_e('Approved', 'easy-affiliate'); ?></span><?php
          }
          else if($app->status=='ignored') {
            ?><span id="esaf-app-status-<?php echo esc_attr($app->ID); ?>" class="esaf-blue esaf-bold"><?php esc_html_e('Ignored', 'easy-affiliate'); ?></span><?php
          }
          break;
        case 'affiliate':
          if(empty($app->affiliate)) {
            esc_html_e('None', 'easy-affiliate');
          }
          else {
            $affiliate = new User($app->affiliate);
            ?>
              <a href="<?php echo esc_url(admin_url("user-edit.php?user_id={$affiliate->ID}")); ?>"><?php echo esc_html($affiliate->full_name()); ?></a>
            <?php
          }

          break;
        case 'submitted_at':
          echo esc_html(Utils::format_datetime($app->post_date_gmt)); break;
      }
    }
  }

  public function add_meta_boxes() {
    add_meta_box(
      'ea-affiliate-application-metabox',
      esc_html__('Affiliate Application', 'easy-affiliate'),
      [$this, 'meta_box'],
      AffiliateApplication::$cpt,
      'normal'
    );
  }

  public function meta_box() {
    global $post;

    if(isset($post) && is_object($post) && $post instanceof \WP_Post) {
      $app = new AffiliateApplication($post->ID);
    }
    else {
      $app = new AffiliateApplication();
    }

    $show_form = ($app->ID <= 0);

    require ESAF_VIEWS_PATH . '/admin/affiliate_applications/meta_box.php';
  }

  public function admin_enqueue_scripts() {
    global $current_screen;

    if($current_screen->post_type == AffiliateApplication::$cpt) {
      wp_enqueue_style('esaf-admin-affiliate-applications', ESAF_CSS_URL . '/admin-affiliate-applications.css', [], ESAF_VERSION);

      wp_dequeue_script('autosave'); //Disable auto-saving

      wp_enqueue_script('esaf-admin-affiliate-applications', ESAF_JS_URL . '/admin-affiliate-applications.js', ['jquery'], ESAF_VERSION);

      wp_localize_script('esaf-admin-affiliate-applications', 'EsafAffiliateApplications', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'submit_button_text' => __('Update', 'easy-affiliate'),
        'pending_text' => __('Awaiting Approval', 'easy-affiliate'),
        'approved_text' => __('Approved', 'easy-affiliate'),
        'ignored_text' => __('Ignored', 'easy-affiliate'),
        'security' => wp_create_nonce('esaf-admin-affiliate-application'),
        'confirm_resend_approved_email' => __('Are you sure you want to resend the affiliate application approved email to this affiliate?', 'easy-affiliate'),
      ]);
    }
  }

  public function save_postdata($post_id) {
    $nonce = isset($_POST['_esaf_affiliateapplication_nonce']) ? $_POST['_esaf_affiliateapplication_nonce'] : '';

    if(!wp_verify_nonce($nonce, 'esaf_save_affiliate_application')) {
      return; // Nonce prevents meta data from being wiped on move to trash
    }

    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return;
    }

    if(defined('DOING_AJAX')) {
      return;
    }

    $app = new AffiliateApplication($post_id);
    $values = $this->sanitize_meta_box_form_data(wp_unslash($_POST));
    $app->load_from_sanitized_array($values);
    $app->store_meta();
  }

  /**
   * Sanitize the meta box form data
   *
   * @param   array $values
   * @return  array
   */
  private function sanitize_meta_box_form_data($values) {
    $values = $this->sanitize_form_data($values);
    $values['_wafp_affiliateapplication_status'] = isset($values['_wafp_affiliateapplication_status']) && is_string($values['_wafp_affiliateapplication_status']) ? $values['_wafp_affiliateapplication_status'] : 'pending';

    return $values;
  }

  private function get_view_link($args, $anchor_text, $count=0, $selected=false) {
    if($selected) {
      return sprintf(
        '<span><span class="esaf-black esaf-bold">%s</span> <span class="count">(%s)</span></span>',
        esc_html($anchor_text),
        esc_html($count)
      );
    }
    else {
      return sprintf(
        '<a href="%s">%s <span class="count">(%s)</span></a>',
        esc_url('edit.php?post_type=' . AffiliateApplication::$cpt . '&' . http_build_query($args, null, '&')),
        esc_html($anchor_text),
        esc_html($count)
      );
    }
  }

  public function list_views($views) {
    $custom_views = [];

    if(isset($views['all'])) {
      $pending_count  = AffiliateApplication::get_status_count('pending');
      $approved_count = AffiliateApplication::get_status_count('approved');
      $ignored_count  = AffiliateApplication::get_status_count('ignored');

      $custom_views['all'] = $views['all'];

      if($pending_count > 0) {
        $selected = (isset($_GET['app_status']) && $_GET['app_status']=='pending');
        $custom_views['pending'] = $this->get_view_link(['app_status'=>'pending'], __('Awaiting Approval', 'easy-affiliate'), $pending_count, $selected);
      }

      if($approved_count > 0) {
        $selected = (isset($_GET['app_status']) && $_GET['app_status']=='approved');
        $custom_views['approved'] = $this->get_view_link(['app_status'=>'approved'], __('Approved', 'easy-affiliate'), $approved_count, $selected);
      }

      if($ignored_count > 0) {
        $selected = (isset($_GET['app_status']) && $_GET['app_status']=='ignored');
        $custom_views['ignored'] = $this->get_view_link(['app_status'=>'ignored'], __('Ignored', 'easy-affiliate'), $ignored_count, $selected);
      }

      if(isset($views['trash'])) {
        $custom_views['trash'] = $views['trash'];
      }

      return $custom_views;
    }

    return $views;
  }

  public function list_table_filters() {
    global $current_screen;

    if($current_screen->post_type == AffiliateApplication::$cpt) {
      $statuses = AffiliateApplication::get_available_statuses();

      $dropdown = [];
      $dropdown[0] = [
        'label' => __('Show all statuses', 'easy-affiliate'),
        'selected' => false,
      ];

      foreach($statuses as $status) {
        $dropdown[$status] = [];
        $dropdown[$status]['selected'] = (!empty($_GET['app_status']) && $_GET['app_status'] == $status);

        if($status=='pending') {
          $dropdown[$status]['label'] = __('Awaiting Approval', 'easy-affiliate');
        }
        else if($status=='approved') {
          $dropdown[$status]['label'] = __('Approved', 'easy-affiliate');
        }
        else if($status=='ignored') {
          $dropdown[$status]['label'] = __('Ignored', 'easy-affiliate');
        }
      }

      ?>
      <select name="app_status" id="filter-by-application-status">
        <?php foreach($dropdown as $status => $config): ?>
          <option value="<?php echo esc_attr($status); ?>"<?php selected($config['selected']); ?>><?php echo esc_html($config['label']); ?></option>
        <?php endforeach; ?>
      </select>
      <?php
    }
  }

  public function list_table_query( $query ) {
    if( is_admin() && isset($query->query) && isset($query->query['post_type']) &&
        $query->query['post_type'] == AffiliateApplication::$cpt ) {
      $qv = &$query->query_vars;
      $qv['meta_query'] = [];

      $app = new AffiliateApplication();

      if(isset($_GET['app_status']) && is_string($_GET['app_status']) && !empty($_GET['app_status'])) {
        $qv['meta_query'][] = [
          'field' => $app->status_str,
          'value' => sanitize_key($_GET['app_status']),
          'compare' => '=',
          'type' => 'STRING'
        ];
      }

      if(!empty($_GET['orderby'] ) && $_GET['orderby']=='app_status' ) {
        $order = isset($_GET['order']) && is_string($_GET['order']) && strtoupper($_GET['order']) == 'DESC' ? 'DESC' : 'ASC';

        $qv['orderby'] = 'meta_value';
        $qv['meta_key'] = $app->status_str;
        $qv['order'] = $order;
      }
    }
  }

  // AJAX Endpoints
  public function ajax_update_application_status() {
    Utils::check_ajax_referer('esaf-admin-affiliate-application','security');

    // If this isn't a MemberPress authorized user then bail
    if(!Utils::is_wafp_admin()) {
      Utils::exit_with_status(403,json_encode(['error'=>__('Forbidden', 'easy-affiliate')]));
    }

    if(!isset($_REQUEST['id'])) {
      Utils::exit_with_status(400,json_encode(['error'=>__('Must specify an application', 'easy-affiliate')]));
    }

    if(!is_numeric($_REQUEST['id'])) {
      Utils::exit_with_status(400,json_encode(['error'=>__('id must be an integer', 'easy-affiliate')]));
    }

    $app = new AffiliateApplication($_REQUEST['id']);

    if($app->ID == 0) {
      Utils::exit_with_status(400,json_encode(['error'=>__('Invalid application', 'easy-affiliate')]));
    }

    if(!isset($_REQUEST['status'])) {
      Utils::exit_with_status(400,json_encode(['error'=>__('Must specify a status', 'easy-affiliate')]));
    }

    $status = sanitize_text_field($_REQUEST['status']);

    if(!in_array($status,$app->statuses)) {
      Utils::exit_with_status(400,json_encode(['error'=>__('Must specify a valid status', 'easy-affiliate')]));
    }

    $app->status = $status;
    $res = $app->store();

    if(is_wp_error($res)) {
      Utils::exit_with_status(500,json_encode(['error' => sprintf(__('Internal Error: %s', 'easy-affiliate'), $res->get_error_message())]));
    }
    else {
      $message = __('The status of the Affiliate Application was successfully changed', 'easy-affiliate');
      Utils::exit_with_status(200,json_encode(compact('message')));
    }
  }

  public function ajax_resend_application_approved_email() {
    if(!Utils::is_post_request() || !isset($_POST['id']) || !is_numeric($_POST['id'])) {
      wp_send_json_error(__('Bad request', 'easy-affiliate'));
    }

    if(!Utils::is_logged_in_and_an_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'easy-affiliate'));
    }

    if(!check_ajax_referer('esaf-admin-affiliate-application', false, false)) {
      wp_send_json_error(__('Security check failed.', 'easy-affiliate'));
    }

    $app = new AffiliateApplication((int) $_POST['id']);

    if($app->ID > 0) {
      Utils::send_affiliate_approved_notification($app);

      wp_send_json_success(__('The application approved email was successfully sent to the affiliate.', 'easy-affiliate'));
    }
    else {
      wp_send_json_error(__('The application was not found.', 'easy-affiliate'));
    }
  }

  // Front end application methods
  public function route() {
    $options = Options::fetch();

    if($options->registration_type == 'application') {
      if(Utils::is_post_request()) {
        $this->process_form();
      }
      else {
        $this->display_form();
      }
    }
    else {
      printf(
        '<p>%s</p>',
        esc_html__('Affiliate applications are disabled.', 'easy-affiliate')
      );
    }
  }

  public function display_form($message = '', $errors = [], $values = []) {
    $form = new AffiliateApplication();

    $current_user = Utils::get_currentuserinfo();
    $logged_in = !!$current_user;

    if($logged_in) {
      if($current_user->is_affiliate) {
        // this isn't ideal but our best option for now
        ?>
        <script>
          window.location = "<?php echo esc_url_raw(Utils::dashboard_url()); ?>";
        </script>
        <?php

        return;
      }
      else {
        $form->first_name = $current_user->first_name;
        $form->last_name = $current_user->last_name;
        $form->email = $current_user->user_email;
        $form->affiliate = $current_user->ID;
      }
    }

    if(Utils::is_post_request() && count($values)) {
      $form->load_from_sanitized_array($values);
    }

    require ESAF_VIEWS_PATH . '/affiliate_applications/form.php';
  }

  public function process_form() {
    $options = Options::fetch();

    $values = $this->sanitize_form_data(wp_unslash($_POST));
    $errors = AffiliateApplication::validate_affiliate_application_form($values);
    $errors = apply_filters('esaf-validate-affiliate-application', $errors, $values);

    if(empty($errors)) {
      $application = new AffiliateApplication();
      $application->load_from_sanitized_array($values);

      if(Utils::is_user_logged_in()) {
        $current_user = Utils::get_currentuserinfo();
        $application->affiliate = $current_user->ID;
      }

      $res = $application->store();

      if(is_wp_error($res)) {
        $errors = [$res->get_error_message()];
        return $this->display_form('', $errors, $values);
      }

      echo $options->application_thank_you;
    }
    else {
      $this->display_form('', $errors, $values);
    }
  }

  /**
   * Sanitize the form data
   *
   * @param   array  $values
   * @return  array
   */
  private function sanitize_form_data($values) {
    $values['_wafp_affiliateapplication_first_name'] = isset($values['_wafp_affiliateapplication_first_name']) && is_string($values['_wafp_affiliateapplication_first_name']) ? sanitize_text_field($values['_wafp_affiliateapplication_first_name']) : '';
    $values['_wafp_affiliateapplication_last_name'] = isset($values['_wafp_affiliateapplication_last_name']) && is_string($values['_wafp_affiliateapplication_last_name']) ? sanitize_text_field($values['_wafp_affiliateapplication_last_name']) : '';
    $values['_wafp_affiliateapplication_email'] = isset($values['_wafp_affiliateapplication_email']) && is_string($values['_wafp_affiliateapplication_email']) ? sanitize_text_field($values['_wafp_affiliateapplication_email']) : '';
    $values['_wafp_affiliateapplication_websites'] = isset($values['_wafp_affiliateapplication_websites']) && is_string($values['_wafp_affiliateapplication_websites']) ? Utils::sanitize_textarea_field($values['_wafp_affiliateapplication_websites']) : '';
    $values['_wafp_affiliateapplication_strategy'] = isset($values['_wafp_affiliateapplication_strategy']) && is_string($values['_wafp_affiliateapplication_strategy']) ? Utils::sanitize_textarea_field($values['_wafp_affiliateapplication_strategy']) : '';
    $values['_wafp_affiliateapplication_social'] = isset($values['_wafp_affiliateapplication_social']) && is_string($values['_wafp_affiliateapplication_social']) ? Utils::sanitize_textarea_field($values['_wafp_affiliateapplication_social']) : '';
    $values['wafp_honeypot'] = isset($values['wafp_honeypot']) && is_string($values['wafp_honeypot']) ? sanitize_text_field($values['wafp_honeypot']) : '';

    return $values;
  }

  public function event_application_submitted($event) {
    $app = $event->get_data();

    if(!is_wp_error($app) && $app instanceof AffiliateApplication) {
      Utils::send_admin_affiliate_applied_notification($app);
    }
  }

  public function event_application_approved($event) {
    $app = $event->get_data();

    if(!is_wp_error($app) && $app instanceof AffiliateApplication) {
      Utils::send_affiliate_approved_notification($app);
    }
  }
}
