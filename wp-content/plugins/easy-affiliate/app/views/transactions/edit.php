<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Payment;
use EasyAffiliate\Models\User;
?>
<div class="wrap">
  <?php
    AppHelper::plugin_title(__('Edit Transaction','easy-affiliate'));
    require ESAF_VIEWS_PATH . '/shared/errors.php';
  ?>

  <div class="form-wrap">
    <form action="" method="post">
      <?php if(isset($txn) and $txn->id > 0): ?>
        <input type="hidden" name="id" value="<?php echo esc_attr($txn->id); ?>" />
      <?php endif; ?>
      <input type="hidden" name="action" value="update" />
      <table class="form-table">
        <tbody>
          <tr valign="top"><th scope="row"><label><?php esc_html_e('Transaction ID:', 'easy-affiliate'); ?></label></th><td><?php echo esc_html($txn->id); ?></td></tr>
          <tr valign="top"><th scope="row"><label><?php esc_html_e('Created:', 'easy-affiliate'); ?></label></th><td><?php echo esc_html(Utils::format_date($txn->created_at)); ?></td></tr>
          <?php require ESAF_VIEWS_PATH . '/transactions/_form.php'; ?>
        </tbody>
      </table>
      <div class="wafp-commissions<?php echo count($commissions) > 1 ? ' wafp-has-multiple-commissions' : ''; ?>">
        <h3 class="wp-heading-inline"><?php esc_html_e('Edit Recorded Commissions', 'easy-affiliate'); ?></h3>
        <?php
          foreach($commissions as $commish):
            $aff = new User($commish->affiliate_id);
            ?>
            <div id="wafp-commissions-<?php echo esc_attr($commish->id); ?>" class="wafp-commissions-table postbox">
              <?php
                if($commish->payment_id > 0):
                  $payment = Payment::get_one($commish->payment_id)
                ?>
                  <div class="wafp-commissions-paid"><?php echo esc_html(sprintf(__('Paid on %s','easy-affiliate'), $payment->created_at)); ?></div>
              <?php
                else:
              ?>
                  <div class="wafp-commissions-delete"><a href="" data-id="<?php echo esc_attr($commish->id); ?>"><i class="ea-icon ea-icon-trash"></i></a></div>
              <?php
                endif;
              ?>
              <table>
                <tr class="wafp-commissions-level-row">
                  <td><label for="commissions[<?php echo esc_attr($commish->id); ?>][commission_level]"><?php esc_html_e('Commission Level', 'easy-affiliate'); ?></label></td>
                  <?php if( $commish->payment_id <= 0 ): ?>
                    <td><input type="text" name="commissions[<?php echo esc_attr($commish->id); ?>][commission_level]" class="regular-text" value="<?php echo esc_attr($commish->commission_level+1); ?>" style="width: 30px;"/></td>
                  <?php else: ?>
                    <td><input type="hidden" name="commissions[<?php echo esc_attr($commish->id); ?>][commission_level]" value="<?php echo esc_attr($commish->commission_level+1); ?>"/><?php echo esc_html($commish->commission_level+1); ?></td>
                  <?php endif; ?>
                </tr>
                <tr>
                  <td><label for="commissions[<?php echo esc_attr($commish->id); ?>][referrer]"><?php esc_html_e('Affiliate', 'easy-affiliate'); ?></label></td>
                  <?php if( $commish->commission_level < 1 or $commish->payment_id > 0 ): ?>
                    <td><input type="hidden" name="commissions[<?php echo esc_attr($commish->id); ?>][referrer]" value="<?php echo esc_attr($aff->user_login); ?>" /><?php echo esc_html($aff->user_login); ?></td>
                  <?php else: ?>
                    <td><input type="text" name="commissions[<?php echo esc_attr($commish->id); ?>][referrer]" class="regular-text wafp-affiliate-referrer" value="<?php echo esc_attr($aff->user_login); ?>" /></td>
                  <?php endif; ?>
                </tr>
                <tr>
                  <td><label for="commissions[<?php echo esc_attr($commish->id); ?>][commission_type]"><?php esc_html_e('Commission Type', 'easy-affiliate'); ?></label></td>
                  <?php if( $commish->payment_id <= 0 ): ?>
                    <td>
                      <select name="commissions[<?php echo esc_attr($commish->id); ?>][commission_type]" data-id="commissions-<?php echo esc_attr($commish->id); ?>" class="wafp_multi_commission_type">
                        <option value="percentage"<?php selected('percentage',$commish->commission_type); ?>><?php esc_html_e('Percentage', 'easy-affiliate'); ?></option>
                        <option value="fixed"<?php selected('fixed',$commish->commission_type); ?>><?php esc_html_e('Fixed Amount', 'easy-affiliate'); ?></option>
                      </select>
                    </td>
                  <?php else: ?>
                    <td><input type="hidden" name="commissions[<?php echo esc_attr($commish->id); ?>][commission_type]" value="<?php echo esc_attr($commish->commission_type); ?>" class="wafp_multi_commission_type" /><?php echo esc_html($commish->commission_type); ?></td>
                  <?php endif; ?>
                </tr>
                <tr>
                  <td><label for="commissions[<?php echo esc_attr($commish->id); ?>][commission_percentage]"><?php esc_html_e('Commission', 'easy-affiliate'); ?></label></td>
                  <?php if( $commish->payment_id <= 0 ): ?>
                    <td><span id="commissions-<?php echo esc_attr($commish->id); ?>-currency-symbol"><?php echo esc_html($options->currency_symbol); ?></span><input type="text" name="commissions[<?php echo esc_attr($commish->id); ?>][commission_percentage]" value="<?php echo esc_attr($commish->commission_percentage); ?>" style="width: 60px;" /><span id="commissions-<?php echo esc_attr($commish->id); ?>-percent-symbol">%</span></td>
                  <?php else: ?>
                    <td><span id="commissions-<?php echo esc_attr($commish->id); ?>-currency-symbol"><?php echo esc_html($options->currency_symbol); ?></span><input type="hidden" name="commissions[<?php echo esc_attr($commish->id); ?>][commission_percentage]" value="<?php echo esc_attr($commish->commission_percentage); ?>" /><?php echo esc_html($commish->commission_percentage); ?><span id="commissions-<?php echo esc_attr($commish->id); ?>-percent-symbol">%</span></td>
                  <?php endif; ?>
                </tr>
                <tr>
                  <td><?php esc_html_e('Voided Amount', 'easy-affiliate'); ?></td>
                  <td><?php echo esc_html(AppHelper::format_currency($commish->correction_amount)); ?></td>
                </tr>
                <tr>
                  <td><?php esc_html_e('Commission Amount', 'easy-affiliate'); ?></td>
                  <td><?php echo esc_html(AppHelper::format_currency($commish->commission_amount)); ?></td>
                </tr>
              </table>
            </div>
            <?php
          endforeach;
          ?>
      </div>
      <p class="submit">
        <input type="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Update', 'easy-affiliate'); ?>" />
      </p>
    </form>
  </div>
</div>
