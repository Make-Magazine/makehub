<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div id="header" style="width: 680px; padding: 0px; margin: 0 auto; text-align: left;">
  <h1 style="font-size: 30px; margin-bottom: 15px;"><?php _ex('Hi {$giftee_name}', 'ui', 'memberpress-gifting'); ?></h1>
</div>
<div id="body" style="width: 600px; background: white; padding: 40px; margin: 0 auto; text-align: left;">
  <div style="margin-bottom: 20px;"><?php printf(_x('%1$s has sent you a gift for %2$s membership content.', 'ui', 'memberpress-gifting'), '{$gifter_name}', '{$product_name}'); ?></div>

  <div style="margin-bottom: 20px;">{$gift_note}</div>

  <div style="margin-bottom: 20px;background: #eee; padding: 15px"><?php printf(_x('You can claim your gift here: <a href="%1$s">%2$s</a>', 'ui', 'memberpress-gifting'), '{$gift_url}', '{$gift_url}'); ?></div>

  <div style="margin-bottom: 20px;"><?php _ex('Cheers!', 'ui', 'memberpress-gifting'); ?></div>

  <div style="margin-bottom: 20px;"><?php printf(_x('The %s Team', 'ui', 'memberpress-gifting'), '{$blog_name}', '{$gift_url}'); ?></div>
</div>

