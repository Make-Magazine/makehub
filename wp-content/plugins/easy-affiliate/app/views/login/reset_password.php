<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<h3><?php esc_html_e('Enter your new password', 'easy-affiliate'); ?></h3>
<form class="esaf-form" method="post">
  <div class="esaf-form-row">
    <div class="esaf-form-label">
      <label for="esaf-user-password"><?php esc_html_e('Password', 'easy-affiliate'); ?></label>
    </div>
    <input type="password" name="wafp_user_password" id="esaf-user-password" />
  </div>
  <div class="esaf-form-row">
    <div class="esaf-form-label">
      <label for="esaf-user-password-confirm"><?php esc_html_e('Password Confirmation', 'easy-affiliate'); ?></label>
    </div>
    <input type="password" name="wafp_user_password_confirm" id="esaf-user-password-confirm" />
  </div>
  <div class="esaf-form-button-row">
    <button><?php esc_html_e('Reset Password', 'easy-affiliate'); ?></button>
    <input type="hidden" name="wafp_process_reset_password_form" value="Y" />
    <input type="hidden" name="wafp_screenname" value="<?php echo esc_attr($wafp_screenname); ?>" />
    <input type="hidden" name="wafp_key" value="<?php echo esc_attr($wafp_key); ?>" />
  </div>
</form>
