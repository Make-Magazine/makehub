<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
  </div>
  <?php
    if(is_customize_preview()) {
      wp_footer();
    }
  ?>
  <?php do_action('esaf_pro_dashboard_footer'); ?>
</body>
</html>
