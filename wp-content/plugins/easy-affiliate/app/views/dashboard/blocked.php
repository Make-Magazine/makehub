<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<h2><?php esc_html_e('Your Affiliate Account Has Been Blocked', 'easy-affiliate'); ?></h2>

<?php
$blocked_message = $user->blocked_message;
if(!empty($blocked_message)) {
  echo wpautop($blocked_message);
}
