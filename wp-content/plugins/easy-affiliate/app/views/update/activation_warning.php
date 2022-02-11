<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php if(defined('EASY_AFFILIATE_LICENSE_KEY') && isset($error)) : ?>
  <div class="notice notice-error">
    <p>
      <?php echo esc_html(sprintf(__('Error with EASY_AFFILIATE_LICENSE_KEY: %s', 'easy-affiliate'), $error)); ?>
    </p>
  </div>
<?php else: ?>
  <div class="notice notice-error">
    <p>
      <?php
        printf(
          // translators: %1$s: open b tag, %2$s: close b tag, %3$s: open link tag, %4$s: close link tag
          esc_html__('%1$sEasy Affiliate hasn\'t been activated yet.%2$s Go to the Easy Affiliate %3$sSettings page%4$s to activate it.', 'easy-affiliate'),
          '<b>',
          '</b>',
          sprintf('<a href="%s">', esc_url(admin_url('admin.php?page=easy-affiliate-settings'))),
          '</a>'
        );
      ?>
    </p>
  </div>
<?php endif; ?>
