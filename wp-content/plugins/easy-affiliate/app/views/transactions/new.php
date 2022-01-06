<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
?>
<div class="wrap">
  <?php
    AppHelper::plugin_title(__('Add New Affiliate Transaction','easy-affiliate'));
    require ESAF_VIEWS_PATH . '/shared/errors.php';
  ?>

  <div class="form-wrap">
    <p class="description"><?php esc_html_e('Creating a new transaction here will calculate and record commissions based on the affiliate selected. Also, any commission notification emails that are enabled will be sent out.','easy-affiliate'); ?></p>
    <form action="" method="post">
      <input type="hidden" name="action" value="create" />
      <table class="form-table">
        <tbody>
          <?php require ESAF_VIEWS_PATH . '/transactions/_form.php'; ?>
        </tbody>
      </table>
      <p class="submit">
        <input type="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Create', 'easy-affiliate'); ?>" />
      </p>
    </form>
  </div>
</div>
