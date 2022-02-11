<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<p>
  <?php
    printf(
      // translators: %1$s: open link tag, %2$s: close link tag
      esc_html__('You\'re unauthorized to view this page. Please %1$slog in%2$s and try again.', 'easy-affiliate'),
      sprintf('<a href="%s">', esc_url($loginURL)),
      '</a>'
    );
  ?>
</p>
