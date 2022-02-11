<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="notice notice-error">
  <p>
    <?php
      printf(
        esc_html__('Your Affiliate Program must be configured. Go to the %s to set it up.', 'easy-affiliate'),
        sprintf(
          '<a href="%1$s">%2$s</a>',
          esc_url(admin_url('admin.php?page=easy-affiliate-wizard')),
          esc_html__('Easy Affiliate Wizard', 'easy-affiliate')
        )
      );
    ?>
  </p>
</div>
