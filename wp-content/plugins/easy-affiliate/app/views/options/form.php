<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
?>
<div class="esaf esaf-settings-page">
  <form name="wafp_options_form" id="wafp_options_form" method="post">
    <input type="hidden" name="action" value="process-form">

    <div class="esaf-settings-header">
      <div class="esaf-container">
        <div class="esaf-settings-header-image">
          <?php echo file_get_contents(ESAF_IMAGES_PATH . '/header-logo.svg'); ?>
        </div>
        <div class="esaf-settings-header-button">
          <input type="submit" name="Submit" class="button button-primary button-hero" value="<?php esc_attr_e('Update Options', 'easy-affiliate') ?>" />
        </div>
      </div>
    </div>

    <div class="esaf-settings-navigation">
      <div class="esaf-container">
        <nav class="esaf-settings-nav">
          <a data-id="general" class="esaf-active"><?php esc_html_e('General', 'easy-affiliate'); ?></a>
          <a data-id="affiliates"><?php esc_html_e('Affiliates', 'easy-affiliate'); ?></a>
          <a data-id="commissions"><?php esc_html_e('Commissions', 'easy-affiliate'); ?></a>
          <a data-id="ecommerce"><?php esc_html_e('eCommerce', 'easy-affiliate'); ?></a>
          <a data-id="integrations"><?php esc_html_e('Integrations', 'easy-affiliate'); ?></a>
          <a data-id="advanced"><?php esc_html_e('Advanced', 'easy-affiliate'); ?></a>
          <?php do_action('esaf_admin_options_nav'); ?>
        </nav>
      </div>
    </div>

    <?php if(isset($settings_saved)) : ?>
      <div class="esaf-settings-saved">
        <div class="esaf-container">
          <div class="notice notice-success"><p><strong><?php esc_html_e('Settings saved.', 'easy-affiliate'); ?></strong></p></div>
        </div>
      </div>
    <?php endif; ?>

    <?php if(isset($errors) && is_array($errors) && count($errors) > 0) : ?>
      <div class="esaf-settings-error">
        <div class="esaf-container">
          <?php require ESAF_VIEWS_PATH . '/shared/errors.php'; ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="esaf-settings-content">
      <div class="esaf-container">
        <div class="esaf-page" id="esaf-page-general">
          <?php require ESAF_VIEWS_PATH . '/options/general.php'; ?>
          <?php do_action('esaf_general_options'); ?>
        </div>
        <div class="esaf-page" id="esaf-page-affiliates">
          <?php require ESAF_VIEWS_PATH . '/options/affiliates.php'; ?>
          <?php do_action('esaf_affiliates_options'); ?>
        </div>
        <div class="esaf-page" id="esaf-page-commissions">
          <?php require ESAF_VIEWS_PATH . '/options/commissions.php'; ?>
          <?php do_action('esaf_commissions_options'); ?>
        </div>
        <div class="esaf-page" id="esaf-page-ecommerce">
          <?php require ESAF_VIEWS_PATH . '/options/ecommerce.php'; ?>
          <?php do_action('esaf_ecommerce_options'); ?>
        </div>
        <div class="esaf-page" id="esaf-page-integrations">
          <?php require ESAF_VIEWS_PATH . '/options/integrations.php'; ?>
          <?php do_action('esaf_integrations_options'); ?>
        </div>
        <div class="esaf-page" id="esaf-page-advanced">
          <?php require ESAF_VIEWS_PATH . '/options/advanced.php'; ?>
          <?php do_action('esaf_advanced_options'); ?>
        </div>
        <?php do_action('esaf_display_options'); ?>
      </div>
    </div>

    <div class="esaf-settings-button">
      <div class="esaf-container">
        <p class="submit">
          <input type="submit" name="Submit" class="button button-primary button-hero" value="<?php esc_attr_e('Update Options', 'easy-affiliate') ?>" />
        </p>
      </div>
    </div>

  </form>
</div>
