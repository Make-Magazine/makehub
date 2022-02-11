<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Lib\Utils;
?>
<h3><?php esc_html_e('Password successfully reset', 'easy-affiliate'); ?></h3>
<p>
  <?php
    printf(
      /* translators: %1$s: open link tag, %2$s: close link tag */
      esc_html__('You can now %1$slog in%2$s with your new password.', 'easy-affiliate'),
      '<a href="' . esc_url(Utils::login_url()) . '">',
      '</a>'
    );
  ?>
</p>
