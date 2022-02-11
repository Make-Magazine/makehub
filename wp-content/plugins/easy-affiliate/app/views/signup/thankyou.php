<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Lib\Utils;
?>
<h3><?php esc_html_e('Thanks for Registering for our Affiliate Program!', 'easy-affiliate'); ?></h3>
<p><?php esc_html_e('You should shortly receive a confirmation email with your login information.', 'easy-affiliate'); ?></p>

<?php if($logged_in): ?>
  <p><a href="<?php echo esc_url(Utils::dashboard_url()); ?>"><?php esc_html_e('View your Affiliate Dashboard', 'easy-affiliate'); ?></a></p>
<?php else: ?>
  <p><a href="<?php echo esc_url(Utils::login_url(['redirect_to' => urlencode(Utils::dashboard_url())])); ?>"><?php esc_html_e('Log in to your Affiliate Dashboard', 'easy-affiliate'); ?></a></p>
<?php endif; ?>

<?php do_action('esaf_signup_thankyou_message'); ?>
