<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Models\Options;
$options = Options::fetch();
?>
<div class="wafp-commissions-table postbox wafp-hidden">
  <div class="wafp-commissions-delete-new"><a href="#"><i class="ea-icon ea-icon-trash"></i></a></div>
  <table>
    <tbody>
      <tr class="wafp-commissions-level-row">
        <td><label for="new_commissions[commission_level][]"><?php esc_html_e('Commission Level', 'easy-affiliate'); ?></label></td>
        <td><input type="text" name="new_commissions[commission_level][]" class="regular-text" value="1" style="width: 30px;"></td>
      </tr>
      <tr>
          <td><label for="new_commissions[referrer][]"><?php esc_html_e('Affiliate', 'easy-affiliate'); ?></label></td>
          <td><input type="text" name="new_commissions[referrer][]" class="regular-text wafp-affiliate-referrer" autocomplete="off"></td>
        </tr>
      <tr>
          <td><label for="new_commissions[commission_type][]"><?php esc_html_e('Commission Type', 'easy-affiliate'); ?></label></td>
          <td>
            <select name="new_commissions[commission_type][]" class="wafp_multi_commission_type_new">
              <option value="percentage" selected="selected"><?php esc_html_e('Percentage', 'easy-affiliate'); ?></option>
              <option value="fixed"><?php esc_html_e('Fixed Amount', 'easy-affiliate'); ?></option>
            </select>
          </td>
        </tr>
      <tr>
          <td><label for="new_commissions[commission_percentage][]"><?php esc_html_e('Commission', 'easy-affiliate'); ?></label></td>
          <td><span class="commissions-currency-symbol" style="display: none;"><?php echo esc_html($options->currency_symbol); ?></span><input type="text" name="new_commissions[commission_percentage][]" value="" style="width: 60px;"><span class="commissions-percent-symbol" style="">%</span></td>
      </tr>
    </tbody>
  </table>
</div>
