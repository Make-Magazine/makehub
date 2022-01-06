<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

$login = isset($_POST['log']) && is_string($_POST['log']) ? sanitize_user(wp_unslash($_POST['log'])) : '';
$remember = isset($_POST['rememberme']);
?>
<form class="esaf-form" method="post">
  <div class="esaf-form-row">
    <div class="esaf-form-label">
      <label for="user_login"><?php esc_html_e('Username', 'easy-affiliate'); ?></label>
    </div>
    <input type="text" name="log" id="user_login" value="<?php echo esc_attr($login); ?>" tabindex="500" />
  </div>
  <div class="esaf-form-row">
    <div class="esaf-form-label">
      <label for="user_pass"><?php esc_html_e('Password', 'easy-affiliate'); ?></label>
    </div>
    <input type="password" name="pwd" id="user_pass" tabindex="510" />
  </div>
  <div class="esaf-form-row">
    <input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="520"<?php checked($remember); ?> /> <label for="rememberme"><?php esc_html_e('Remember Me', 'easy-affiliate'); ?></label>
  </div>
  <?php do_action('esaf-login-form-before-submit'); ?>
  <div class="esaf-form-button-row">
    <button tabindex="530"><?php esc_html_e('Log In', 'easy-affiliate'); ?></button>
    <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>" />
    <input type="hidden" name="testcookie" value="1" />
    <input type="hidden" name="wafp_process_login_form" value="true" />
  </div>
</form>
<div class="esaf-login-actions">
  <a href="<?php echo esc_url($signup_url); ?>"><?php esc_html_e('Register', 'easy-affiliate'); ?></a>&nbsp;|
  <a href="<?php echo esc_url($forgot_password_url); ?>"><?php esc_html_e('Lost Password?', 'easy-affiliate'); ?></a>
</div>
