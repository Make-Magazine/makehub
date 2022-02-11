<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
$wafp_user_or_email = isset($_POST['wafp_user_or_email']) && is_string($_POST['wafp_user_or_email']) ? sanitize_text_field(wp_unslash($_POST['wafp_user_or_email'])) : '';
?>
<h3><?php esc_html_e('Request a Password Reset', 'easy-affiliate'); ?></h3>
<form class="esaf-form" method="post">
  <div class="esaf-form-row">
    <div class="esaf-form-label">
      <label for="esaf-user-or-email"><?php esc_html_e('Enter Your Username or Email Address', 'easy-affiliate'); ?></label>
    </div>
    <input type="text" name="wafp_user_or_email" id="esaf-user-or-email" value="<?php echo esc_attr($wafp_user_or_email); ?>" />
  </div>
  <?php do_action('esaf-forgot-password-form-before-submit'); ?>
  <div class="esaf-form-button-row">
    <input type="hidden" name="wafp_process_forgot_password_form" value="true" />
    <button><?php esc_html_e('Request Password Reset', 'easy-affiliate'); ?></button>
  </div>
</form>
