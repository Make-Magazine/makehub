<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\Cookie;
use EasyAffiliate\Lib\Track;
use EasyAffiliate\Models\Options;
use EasyAffiliate\Models\Transaction;

class WPFormsCtrl extends BaseCtrl {
  public function load_hooks() {
    $options = Options::fetch();

    if(!in_array('wpforms', $options->integration) || !self::is_plugin_active()) {
      return;
    }

    add_action('wpforms_process_complete', [$this, 'add_pending_transaction'], 10, 4);
    add_action('wpforms_stripe_process_complete', [$this, 'mark_transaction_complete'], 10, 3);
    add_action('wpforms_paypal_standard_process_complete', [$this, 'mark_transaction_complete'], 10, 3);
    add_filter('wpforms_builder_settings_sections', [$this, 'add_form_settings_section']);
    add_action('wpforms_form_settings_panel_content', [$this, 'display_form_settings']);
    add_filter('esaf_transaction_source_label', [$this, 'transaction_source_label'], 10, 2);
  }

  public static function is_plugin_active() {
    return function_exists('wpforms');
  }

  /**
   * Add a pending transaction when the form is submitted
   *
   * @param array $fields
   * @param array $entry
   * @param array $form_data
   * @param int $entry_id
   */
  public function add_pending_transaction($fields, $entry, $form_data, $entry_id) {
    // Don't track admin users
    if(is_super_admin()) {
      return;
    }

    if(!function_exists('wpforms_get_total_payment')) {
      return;
    }

    if(empty($entry_id)) {
      return;
    }

    if(empty($form_data['easy_affiliate']['esaf_enable_commissions'])) {
      return;
    }

    $total = wpforms_get_total_payment($fields);

    if(empty($total)) {
      return;
    }

    $description = $this->get_payment_description($fields);

    if(empty($description)) {
      $description = $form_data['settings']['form_title'];
    }

    Track::pending_sale(
      'wpforms',
      $total,
      sprintf('wpforms_entry_%d', $entry_id),
      '',
      $description,
      $entry_id,
      '',
      get_current_user_id()
    );
  }

  /**
   * Mark the transaction complete when payment is successful
   *
   * @param array $fields
   * @param array $form_data
   * @param int $entry_id
   */
  public function mark_transaction_complete($fields, $form_data, $entry_id) {
    if(empty($entry_id)) {
      return;
    }

    $transaction = Transaction::get_one(['source' => 'wpforms', 'order_id' => $entry_id]);

    if($transaction instanceof Transaction) {
      $transaction->status = 'complete';
      $transaction->store();
    }
  }

  /**
   * Get a description of the payment fields
   *
   * @param array $fields
   * @return string
   */
  public function get_payment_description($fields) {
    $description = [];

    foreach($fields as $field) {
      if(empty($field['type'])) {
        continue;
      }

      if($field['type'] == 'payment-single') {
        if(!empty($field['name'])) {
          $description[] = $field['name'];
        }
      }
      elseif($field['type'] == 'payment-select' || $field['type'] == 'payment-multiple') {
        if(!empty($field['name'])) {
          $parts = [
            $field['name']
          ];

          if(!empty($field['value'])) {
            $parts[] = html_entity_decode($field['value']);
          }

          $description[] = join(' | ', $parts);
        }
      }
      elseif($field['type'] == 'payment-checkbox') {
        if(!empty($field['name'])) {
          $parts = [
            $field['name']
          ];

          if(!empty($field['value'])) {
            $values = explode("\n", $field['value']);
            $values = array_map('html_entity_decode', $values);
            $values = array_map('trim', $values);

            $parts[] = join(', ', $values);
          }

          $description[] = join(' | ', $parts);
        }
      }
    }

    return join(', ', $description);
  }

  /**
   * Add the Easy Affiliate section to the form settings
   *
   * @param array $sections
   * @return array
   */
  public function add_form_settings_section($sections) {
    $sections['easy_affiliate'] = __('Easy Affiliate', 'easy-affiliate');

    return $sections;
  }

  /**
   * Display the Easy Affiliate form settings
   *
   * @param \WPForms_Builder_Panel_Settings $instance Settings management panel instance
   */
  public function display_form_settings($instance) {
    echo '<div class="wpforms-panel-content-section wpforms-panel-content-section-easy_affiliate">';
    echo '<div class="wpforms-panel-content-section-title">';
    esc_html_e('Easy Affiliate', 'easy-affiliate');
    echo '</div>';

    if(defined('WPFORMS_VERSION') && version_compare(WPFORMS_VERSION, '1.6.8', '>=')) {
      $option = 'toggle';
    }
    else {
      $option = 'checkbox';
    }

    wpforms_panel_field(
      $option,
      'easy_affiliate',
      'esaf_enable_commissions',
      $instance->form_data,
      __('Enable Easy Affiliate commissions', 'easy-affiliate'),
      [
        'tooltip' => __('Enable Easy Affiliate commissions on payments made using this form.', 'easy-affiliate'),
      ]
    );

    echo '</div>';
  }

  /**
   * Link the transaction source label to the WPForms entry if applicable
   *
   * @param  string    $label The original transaction source label (already escaped)
   * @param  \stdClass $rec   The transaction rec object
   * @return string
   */
  public function transaction_source_label($label, $rec) {
    $source = isset($rec->source) && is_string($rec->source) ? $rec->source : '';
    $order_id = isset($rec->order_id) && is_numeric($rec->order_id) ? (int) $rec->order_id : 0;

    if($source == 'wpforms' && $order_id && self::is_plugin_active()) {
      $label = sprintf(
        '<a href="%s">%s</a>',
        esc_url(admin_url("admin.php?page=wpforms-entries&view=details&entry_id={$order_id}")),
        $label
      );
    }

    return $label;
  }
}
