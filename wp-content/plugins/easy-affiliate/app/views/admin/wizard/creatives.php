<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
?>
<div class="esaf-wizard-white-box">
  <div class="esaf-wizard-box-title">
    <h2><?php esc_html_e('Creatives', 'easy-affiliate'); ?></h2>
    <p><?php esc_html_e('Add and view your banner and text creatives.', 'easy-affiliate'); ?></p>
  </div>
  <div class="esaf-wizard-box-content esaf-wizard-box-content-creatives">
    <button type="button" id="esaf-wizard-creatives-add-creative" class="button button-primary"><?php esc_html_e('+ Add', 'easy-affiliate'); ?></button>
    <div id="esaf-wizard-creatives-uploaded" class="esaf-hidden">
      <table>
        <tbody>
          <tr>
            <th><?php esc_html_e('Name', 'easy-affiliate'); ?></th>
            <th><?php esc_html_e('URL', 'easy-affiliate'); ?></th>
            <th><?php esc_html_e('Creative', 'easy-affiliate'); ?></th>
          </tr>
        </tbody>
      </table>
      <p><a href="<?php echo esc_url(admin_url('edit.php?post_type=esaf-creative')); ?>" target="_blank"><?php esc_html_e('View All Creatives', 'easy-affiliate'); ?></a></p>
    </div>
    <div class="esaf-wizard-skip-and-continue">
      <button type="button" id="esaf-wizard-creatives-skip-and-continue" class="button button-primary button-hero esaf-wizard-next-step"><?php esc_html_e('Skip and Continue &rarr;', 'easy-affiliate'); ?></button>
    </div>
  </div>
</div>
<div id="esaf-wizard-creatives-popup" class="esaf-popup mfp-hide">
  <div class="esaf-popup-content">
    <div class="esaf-wizard-box-title">
      <h2>
        <?php echo file_get_contents(ESAF_IMAGES_PATH . '/up-arrow-box.svg'); ?>
        <?php esc_html_e('Upload Creative', 'easy-affiliate'); ?>
      </h2>
    </div>
    <div class="esaf-wizard-box-content esaf-wizard-box-content-creatives">
      <div class="esaf-wizard-creative-form-row">
        <label for="esaf-wizard-creative-form-name">
          <?php esc_html_e('Name', 'easy-affiliate'); ?>
          <?php
            AppHelper::info_tooltip(
              'esaf-wizard-creative-name',
              esc_html__('The name of this creative (displayed to affiliates).', 'easy-affiliate')
            );
          ?>
        </label>
        <input type="text" id="esaf-wizard-creative-form-name">
      </div>
      <div class="esaf-wizard-creative-form-row">
        <label for="esaf-wizard-creative-form-url">
          <?php esc_html_e('URL', 'easy-affiliate'); ?>
          <?php
            AppHelper::info_tooltip(
              'esaf-wizard-creative-url',
              esc_html__('The destination URL for this creative.', 'easy-affiliate')
            );
          ?>
        </label>
        <input type="url" id="esaf-wizard-creative-form-url">
      </div>
      <div class="esaf-wizard-creative-form-row">
        <div class="esaf-wizard-tiles">
          <div>
            <input type="radio" name="esaf_wizard_creative_type" value="banner" id="esaf-wizard-creative-type-banner" checked>
            <label for="esaf-wizard-creative-type-banner">
              <?php echo file_get_contents(ESAF_IMAGES_PATH . '/creative-type-banner.svg'); ?>
            </label>
            <div class="esaf-wizard-creative-type-description"><?php esc_html_e('Banner', 'easy-affiliate'); ?></div>
          </div>
          <div>
            <input type="radio" name="esaf_wizard_creative_type" value="text" id="esaf-wizard-creative-type-text">
            <label for="esaf-wizard-creative-type-text">
              <?php echo file_get_contents(ESAF_IMAGES_PATH . '/creative-type-text.svg'); ?>
            </label>
            <div class="esaf-wizard-creative-type-description"><?php esc_html_e('Text', 'easy-affiliate'); ?></div>
          </div>
        </div>
      </div>
      <div id="esaf-wizard-creative-form-banner-row" class="esaf-wizard-creative-form-row">
        <label><?php esc_html_e('Creative', 'easy-affiliate'); ?></label>
        <div id="esaf-wizard-creative-form-banner-preview" class="esaf-hidden"></div>
        <div id="esaf-wizard-creative-form-banner">
          <button type="button" id="esaf-wizard-creative-form-banner-button" class="button button-secondary"><?php esc_html_e('Choose Image', 'easy-affiliate'); ?></button>
        </div>
        <div id="esaf-wizard-creative-form-remove-banner" class="esaf-hidden">
          <button type="button" id="esaf-wizard-creative-form-remove-banner-button" class="button button-secondary"><?php esc_html_e('Remove', 'easy-affiliate'); ?></button>
        </div>
      </div>
      <div id="esaf-wizard-creative-form-text-row" class="esaf-wizard-creative-form-row esaf-hidden">
        <label for="esaf-wizard-creative-form-text">
          <?php esc_html_e('Text', 'easy-affiliate'); ?>
          <?php
            AppHelper::info_tooltip(
              'link-text',
              esc_html__('The text of the link.', 'easy-affiliate')
            );
          ?>
        </label>
        <input type="text" id="esaf-wizard-creative-form-text">
      </div>
      <div class="esaf-wizard-creative-form-button-row">
        <button type="button" id="esaf-wizard-creatives-create-creative" class="button button-primary button-hero"><?php esc_html_e('+ Add Creative', 'easy-affiliate'); ?></button>
      </div>
    </div>
  </div>
</div>
