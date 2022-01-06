<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
?>
<h3><?php esc_html_e('Affiliate Settings', 'easy-affiliate'); ?></h3>
<?php if($options->is_payout_method_paypal() && $user->is_affiliate) : ?>
  <table class="form-table">
    <tr>
      <th><label for="<?php echo esc_attr($user->paypal_email_str); ?>"><?php esc_html_e('PayPal Email', 'easy-affiliate'); ?></label></th>
      <td><input type="email" name="<?php echo esc_attr($user->paypal_email_str); ?>" id="<?php echo esc_attr($user->paypal_email_str); ?>" class="regular-text" value="<?php echo esc_attr($user->paypal_email); ?>" /></td>
    </tr>
  </table>
<?php endif; ?>
<?php if(($options->show_address_fields || $options->show_address_fields_account) && $user->is_affiliate) : ?>
  <table class="form-table">
    <tr>
      <th><label for="<?php echo esc_attr($user->address_one_str); ?>"><?php esc_html_e('Address 1', 'easy-affiliate'); ?></label></th>
      <td><input type="text" name="<?php echo esc_attr($user->address_one_str); ?>" id="<?php echo esc_attr($user->address_one_str); ?>" class="regular-text" value="<?php echo esc_attr($user->address_one); ?>" /></td>
    </tr>
    <tr>
      <th><label for="<?php echo esc_attr($user->address_two_str); ?>"><?php esc_html_e('Address 2', 'easy-affiliate'); ?></label></th>
      <td><input type="text" name="<?php echo esc_attr($user->address_two_str); ?>" id="<?php echo esc_attr($user->address_two_str); ?>" class="regular-text" value="<?php echo esc_attr($user->address_two); ?>" /></td>
    </tr>
    <tr>
      <th><label for="<?php echo esc_attr($user->city_str); ?>"><?php esc_html_e('City', 'easy-affiliate'); ?></label></th>
      <td><input type="text" name="<?php echo esc_attr($user->city_str); ?>" id="<?php echo esc_attr($user->city_str); ?>" class="regular-text" value="<?php echo esc_attr($user->city); ?>" /></td>
    </tr>
    <tr>
      <th><label for="<?php echo esc_attr($user->state_str); ?>"><?php esc_html_e('State', 'easy-affiliate'); ?></label></th>
      <td><input type="text" name="<?php echo esc_attr($user->state_str); ?>" id="<?php echo esc_attr($user->state_str); ?>" class="regular-text" value="<?php echo esc_attr($user->state); ?>" /></td>
    </tr>
    <tr>
      <th><label for="<?php echo esc_attr($user->zip_str); ?>"><?php esc_html_e('Zip', 'easy-affiliate'); ?></label></th>
      <td><input type="text" name="<?php echo esc_attr($user->zip_str); ?>" id="<?php echo esc_attr($user->zip_str); ?>" class="regular-text" value="<?php echo esc_attr($user->zip); ?>" /></td>
    </tr>
    <tr>
      <th><label for="<?php echo esc_attr($user->country_str); ?>"><?php esc_html_e('Country', 'easy-affiliate'); ?></label></th>
      <td><input type="text" name="<?php echo esc_attr($user->country_str); ?>" id="<?php echo esc_attr($user->country_str); ?>" class="regular-text" value="<?php echo esc_attr($user->country); ?>" /></td>
    </tr>
  </table>
<?php endif; ?>
<?php if(($options->show_tax_id_fields || $options->show_tax_id_fields_account) && $user->is_affiliate) : ?>
  <table class="form-table">
    <tr>
      <th><label for="<?php echo esc_attr($user->tax_id_us_str); ?>"><?php esc_html_e('SSN / Tax ID', 'easy-affiliate'); ?></label></th>
      <td><input type="text" name="<?php echo esc_attr($user->tax_id_us_str); ?>" id="<?php echo esc_attr($user->tax_id_us_str); ?>" class="regular-text" value="<?php echo esc_attr($user->tax_id_us); ?>" /></td>
    </tr>
    <tr>
      <th><label for="<?php echo esc_attr($user->tax_id_int_str); ?>"><?php esc_html_e('Int\'l Tax ID', 'easy-affiliate'); ?></label></th>
      <td><input type="text" name="<?php echo esc_attr($user->tax_id_int_str); ?>" id="<?php echo esc_attr($user->tax_id_int_str); ?>" class="regular-text" value="<?php echo esc_attr($user->tax_id_int); ?>" /></td>
    </tr>
  </table>
<?php endif; ?>
<table class="form-table">
  <tr>
    <th><label for="<?php echo esc_attr($user->referrer_str) ?>"><?php esc_html_e('Affiliate Referrer', 'easy-affiliate'); ?> <span class="description"><?php esc_html_e('(login name)', 'easy-affiliate'); ?></span></label></th>
    <td><input type="text" name="<?php echo esc_attr($user->referrer_str) ?>" id="<?php echo esc_attr($user->referrer_str) ?>" class="regular-text" value="<?php echo $affiliate ? esc_attr($affiliate->user_login) : ''; ?>" /></td>
  </tr>
  <tr>
    <th><label for="<?php echo esc_attr($user->is_affiliate_str); ?>"><?php esc_html_e('User is an Affiliate', 'easy-affiliate'); ?></label></th>
    <td>
      <input type="checkbox" name="<?php echo esc_attr($user->is_affiliate_str); ?>" id="<?php echo esc_attr($user->is_affiliate_str); ?>" <?php checked($user->is_affiliate); ?> />
      <label for="<?php echo esc_attr($user->is_affiliate_str); ?>"><?php esc_html_e('Is this user an Affiliate?', 'easy-affiliate'); ?></label>
    </td>
  </tr>
  <tr>
    <th><label for="<?php echo esc_attr($user->is_blocked_str); ?>"><?php esc_html_e('User is Blocked', 'easy-affiliate'); ?></label></th>
    <td>
      <input type="checkbox" name="<?php echo esc_attr($user->is_blocked_str); ?>" id="<?php echo esc_attr($user->is_blocked_str); ?>" class="esaf-toggle-checkbox" data-box="esaf-affiliate-is-blocked-box" <?php checked($user->is_blocked); ?> />
      <label for="<?php echo esc_attr($user->is_blocked_str); ?>"><?php esc_html_e('Is this user blocked?', 'easy-affiliate'); ?></label>
      <div class="esaf-affiliate-is-blocked-box">
        <h4><?php esc_html_e('Affiliate Blocked Message:', 'easy-affiliate'); ?></h4>
        <?php wp_editor($user->blocked_message, $user->blocked_message_str, ['editor_height' => 250]); ?>
      </div>
    </td>
  </tr>
  <tr>
    <th><label for="<?php echo esc_attr($user->unsubscribed_str); ?>"><?php esc_html_e('Unsubscribe', 'easy-affiliate'); ?></label></th>
    <td><input type="checkbox" name="<?php echo esc_attr($user->unsubscribed_str); ?>" id="<?php echo esc_attr($user->unsubscribed_str); ?>" <?php checked($user->unsubscribed); ?> />&nbsp;<label for="<?php echo esc_attr($user->unsubscribed_str); ?>"><?php esc_html_e('Unsubscribe from commission notification emails', 'easy-affiliate'); ?></label></td>
  </tr>
  <tr>
    <th><label for="<?php echo esc_attr($user->notes_str); ?>"><?php esc_html_e('Affiliate Notes', 'easy-affiliate'); ?></label></th>
    <td><textarea name="<?php echo esc_attr($user->notes_str); ?>" id="<?php echo esc_attr($user->notes_str); ?>"><?php echo esc_textarea($user->notes); ?></textarea></td>
  </tr>
  <tr>
    <th><label for="wafp_override_enabled"><?php esc_html_e('Commission Override', 'easy-affiliate'); ?></label></th>
    <td>
      <label><input type="checkbox" name="wafp_override_enabled" id="wafp_override_enabled" class="esaf-show-commission-override"<?php checked($commission_override_enabled); ?> />&nbsp;<?php esc_html_e('Enable commission override for this user', 'easy-affiliate'); ?></label>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <?php AppHelper::display_affiliate_commissions($user->ID); ?>
      <?php AppHelper::display_commission_override($commission_type, $commission_levels, $subscription_commissions); ?>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <button type="button" class="button button-secondary esaf-resend-welcome-email" data-user-id="<?php echo esc_attr($user->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('esaf_resend_welcome_email')); ?>"><?php esc_html_e('Resend Affiliate Program Welcome Email', 'easy-affiliate'); ?></button>&nbsp;&nbsp;<img src="<?php echo esc_url(admin_url('images/loading.gif')); ?>" alt="<?php esc_html_e('Loading...', 'easy-affiliate'); ?>" class="esaf-resend-welcome-email-loader" />&nbsp;&nbsp;<span class="esaf-resend-welcome-email-message">&nbsp;</span>
    </td>
  </tr>
</table>
