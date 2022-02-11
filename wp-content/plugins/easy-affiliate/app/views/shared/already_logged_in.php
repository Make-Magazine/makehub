<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php if(isset($redirect_to) && !empty($redirect_to)) : ?>
<script type="text/javascript">
  window.location.href='<?php echo esc_url_raw($redirect_to); ?>';
</script>
<?php endif; ?>
<p class="wafp-already-logged-in">
  <?php
    printf(
      // translators: %1$s: open link tag, %2$s: close link tag
      esc_html__('You\'re already logged in. %1$sLog out.%2$s', 'easy-affiliate'),
      sprintf('<a href="%s">', esc_url(wp_logout_url($redirect_to))),
      '</a>'
    );
  ?>
</p>
