<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\Track;
use EasyAffiliate\Models\Options;
use EasyAffiliate\Models\Transaction;

class FormidableCtrl extends BaseCtrl {
  public function load_hooks() {
    $options = Options::fetch();

    if(!in_array('formidable', $options->integration) || !self::is_plugin_active()) {
      return;
    }

    add_filter('frm_add_form_settings_section', [$this, 'add_form_settings_section']);
    add_filter('frm_form_options_before_update', [$this, 'form_options_before_update'], 15, 2);
    add_action('frm_after_create_entry', [$this, 'add_pending_transaction'], 10, 2);
    add_action('frm_payment_status_complete', [$this, 'mark_transaction_complete']);
    add_action('frm_payment_paypal_ipn', [$this, 'mark_transaction_complete_paypal']);
    add_action('frm_payment_status_failed', [$this, 'refund_transaction']);
    add_action('frm_payment_status_refunded', [$this, 'refund_transaction']);
    add_filter('esaf_transaction_source_label', [$this, 'transaction_source_label'], 10, 2);
  }

  /**
   * Is the Formidable plugin active?
   *
   * @return bool
   */
  public static function is_plugin_active() {
    return class_exists('FrmAppHelper');
  }

  /**
   * Add the Easy Affiliate form settings section
   *
   * @param array $sections
   * @return array
   */
  public function add_form_settings_section($sections) {
    $sections['easy_affiliate'] = [
      'id' => 'esaf-formidable-settings',
      'name' => __('Easy Affiliate', 'easy-affiliate'),
      'title' => __('Easy Affiliate Settings', 'easy-affiliate'),
      'function' => [$this, 'display_form_settings_section']
    ];

    return $sections;
  }

  /**
   * Display the Easy Affiliate form options
   *
   * @param array $values
   */
  public function display_form_settings_section($values) {
    global $wpdb;
    $form_id = isset($values['id']) && is_numeric($values['id']) ? (int) $values['id'] : 0;
    $fields = \FrmField::getAll($wpdb->prepare("fi.form_id = %d AND fi.type NOT IN ('checkbox', 'divider', 'end_divider', 'html', 'break', 'captcha', 'rte', 'form')", $form_id), ' ORDER BY field_order ASC');

    $commissions_enabled = !empty($values['easy_affiliate']['enable_commissions']);
    $selected_payment_amount_field = isset($values['easy_affiliate']['payment_amount_field']) ? $values['easy_affiliate']['payment_amount_field'] : '';
    $selected_payment_description_field = isset($values['easy_affiliate']['payment_description_field']) ? $values['easy_affiliate']['payment_description_field'] : '';
    ?>
    <table class="form-table">
      <tbody>
        <tr>
          <td colspan="2">
            <input type="checkbox" id="esaf-formidable-enable-commissions" name="options[easy_affiliate][enable_commissions]" value="1" <?php checked($commissions_enabled); ?>> <label for="esaf-formidable-enable-commissions"><?php esc_html_e('Enable commissions on payments made using this form', 'easy-affiliate'); ?></label>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="esaf-formidable-payment-amount-field"><?php esc_html_e('Payment Amount', 'easy-affiliate'); ?></label>
          </th>
          <td>
            <select id="esaf-formidable-payment-amount-field" name="options[easy_affiliate][payment_amount_field]">
              <option value=""><?php esc_html_e('Please select', 'easy-affiliate'); ?></option>
              <?php if(is_array($fields) && count($fields) > 0) : ?>
                <?php foreach($fields as $field) : ?>
                  <option value="<?php echo esc_attr($field->id); ?>" <?php selected($selected_payment_amount_field, $field->id); ?>><?php echo esc_html($field->name); ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="esaf-formidable-payment-description-field"><?php esc_html_e('Payment Description', 'easy-affiliate'); ?></label>
          </th>
          <td>
            <select id="esaf-formidable-payment-description-field" name="options[easy_affiliate][payment_description_field]">
              <option value=""><?php esc_html_e('Please select', 'easy-affiliate'); ?></option>
              <?php if(is_array($fields) && count($fields) > 0) : ?>
                <?php foreach($fields as $field) : ?>
                  <option value="<?php echo esc_attr($field->id); ?>" <?php selected($selected_payment_description_field, $field->id); ?>><?php echo esc_html($field->name); ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </td>
        </tr>
      </tbody>
    </table>
    <?php
  }

  /**
   * Save the Easy Affiliate form option values when the form settings are saved
   *
   * @param array $options
   * @param array $values
   * @return array
   */
  public function form_options_before_update($options, $values) {
    $options['easy_affiliate']['enable_commissions'] = isset($values['options']['easy_affiliate']['enable_commissions']) ? '1' : '0';
    $options['easy_affiliate']['payment_amount_field'] = isset($values['options']['easy_affiliate']['payment_amount_field']) ? $values['options']['easy_affiliate']['payment_amount_field'] : '';
    $options['easy_affiliate']['payment_description_field'] = isset($values['options']['easy_affiliate']['payment_description_field']) ? $values['options']['easy_affiliate']['payment_description_field'] : '';

    return $options;
  }

  /**
   * Add a pending transaction when the form is submitted
   *
   * @param int $entry_id
   * @param int $form_id
   */
  public function add_pending_transaction($entry_id, $form_id) {
    $form = \FrmForm::getOne($form_id);

    if(empty($form)) {
      return;
    }

    $enable_commissions = isset($form->options['easy_affiliate']['enable_commissions']) ? $form->options['easy_affiliate']['enable_commissions'] : 0;
    $payment_amount_field = isset($form->options['easy_affiliate']['payment_amount_field']) ? $form->options['easy_affiliate']['payment_amount_field'] : 0;
    $payment_description_field = isset($form->options['easy_affiliate']['payment_description_field']) ? $form->options['easy_affiliate']['payment_description_field'] : 0;

    if(empty($enable_commissions) || empty($payment_amount_field)) {
      return;
    }

    $total = (float) \FrmEntryMeta::get_entry_meta_by_field($entry_id, $payment_amount_field);
    $description = \FrmEntryMeta::get_entry_meta_by_field($entry_id, $payment_description_field);

    if(empty($description)) {
      $description = $form->name;
    }

    Track::pending_sale(
      'formidable',
      $total,
      sprintf('formidable_entry_%d', $entry_id),
      '',
      $description,
      $entry_id,
      '',
      get_current_user_id()
    );
  }

  /**
   * Mark the transaction as complete when payment completes
   *
   * @param array $atts
   */
  public function mark_transaction_complete($atts) {
    $transaction = Transaction::get_one(['source' => 'formidable', 'order_id' => $atts['entry_id']]);

    if($transaction instanceof Transaction) {
      $transaction->status = 'complete';
      $transaction->store();
    }
  }

  /**
   * Mark the transaction as complete when the PayPal payment completes
   *
   * @param array $atts
   */
  public function mark_transaction_complete_paypal($atts) {
    if(isset($atts['pay_vars']['completed'], $atts['entry']->id) && $atts['pay_vars']['completed']) {
      $transaction = Transaction::get_one(['source' => 'formidable', 'order_id' => $atts['entry']->id]);

      if($transaction instanceof Transaction) {
        $transaction->status = 'complete';
        $transaction->store();
      }
    }
  }

  /**
   * Void the transaction when the payment is refunded
   *
   * @param array $atts
   */
  public function refund_transaction($atts) {
    $transaction = Transaction::get_one(['source' => 'formidable', 'order_id' => $atts['entry_id']]);

    if($transaction instanceof Transaction) {
      $transaction->apply_refund($transaction->sale_amount);
      $transaction->store();
    }
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

    if($source == 'formidable' && $order_id && self::is_plugin_active()) {
      $label = sprintf(
        '<a href="%s">%s</a>',
        esc_url(admin_url("admin.php?page=formidable-entries&frm_action=show&id={$order_id}")),
        $label
      );
    }

    return $label;
  }
}
