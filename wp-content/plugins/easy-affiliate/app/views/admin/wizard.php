<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\WizardHelper;
?>
<div class="esaf esaf-wizard-page wrap">
  <div class="esaf-container">
    <div class="esaf-wizard-exit"><a href="<?php echo esc_url(admin_url('admin.php?page=easy-affiliate')); ?>" class="button button-primary"><i class="ea-icon ea-icon-cancel-circled-outline"></i><?php esc_html_e('Exit Setup', 'easy-affiliate'); ?></a></div>
    <div class="esaf-wizard-logo">
      <?php echo file_get_contents(ESAF_IMAGES_PATH . '/header-logo.svg'); ?>
    </div>
    <div class="esaf-wizard-steps">
      <?php
        foreach($steps as $key => $step) {
          printf('<div class="esaf-wizard-step esaf-wizard-step-%s">', $key + 1);
          echo WizardHelper::get_progress($key + 1, count($steps));
          require $step;
          echo '</div>';
        }
      ?>
    </div>
  </div>
</div>
