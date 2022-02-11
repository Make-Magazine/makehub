<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\WizardHelper;
?>
<div class="esaf-wizard-white-box">
  <div class="esaf-wizard-box-title">
    <h2><?php esc_html_e('Migrate Affiliate Program', 'easy-affiliate'); ?></h2>
    <p><?php esc_html_e('Migrate your affiliate program to Easy Affiliate with one click.', 'easy-affiliate'); ?></p>
  </div>
  <div class="esaf-wizard-box-content esaf-wizard-box-content-migrate">
    <?php if(WizardHelper::is_migration_available()) : ?>
      <?php if(WizardHelper::is_affiliatewp_detected()) : ?>
        <h3><?php esc_html_e('AffiliateWP Detected', 'easy-affiliate'); ?></h3>
        <button type="button" id="esaf-wizard-migrate-affiliatewp" class="button button-secondary"><?php esc_html_e('Migrate AffiliateWP Data, Settings and Links into Easy Affiliate', 'easy-affiliate'); ?></button>
        <div id="esaf-wizard-migrating-affiliatewp">
          <div class="esaf-wizard-migrating-affiliatewp-text"><?php esc_html_e('Migrating Settings', 'easy-affiliate'); ?></div>
          <div class="esaf-determinate-progress-bar"><div class="esaf-determinate-progress-bar-inner"></div></div>
        </div>
      <?php endif; ?>
      <?php if(WizardHelper::is_affiliate_royale_detected()) : ?>
        <h3><?php esc_html_e('Affiliate Royale Detected', 'easy-affiliate'); ?></h3>
        <button type="button" id="esaf-wizard-migrate-affiliate-royale" class="button button-secondary"><?php esc_html_e('Migrate Affiliate Royale Data, Settings and Links into Easy Affiliate', 'easy-affiliate'); ?></button>
        <div id="esaf-wizard-migrating-affiliate-royale">
          <div class="esaf-wizard-migrating-affiliate-royale-text"><?php esc_html_e('Migrating From Affiliate Royale', 'easy-affiliate'); ?></div>
          <div class="esaf-indeterminate-progress-bar"></div>
        </div>
      <?php endif; ?>
    <?php else : ?>
      <p><?php esc_html_e('You don\'t currently have a known Affiliate Program plugin installed to migrate from.', 'easy-affiliate'); ?></p>
    <?php endif; ?>
  </div>
  <div class="esaf-wizard-save-and-continue">
    <button type="button" id="esaf-wizard-migrate-save-and-continue" class="button button-primary button-hero esaf-wizard-next-step"><?php esc_html_e('Save and Continue &rarr;', 'easy-affiliate'); ?></button>
  </div>
</div>
