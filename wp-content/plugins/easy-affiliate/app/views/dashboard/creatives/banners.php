<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }
use EasyAffiliate\Helpers\CreativesHelper;
/** @var \EasyAffiliate\Models\Creative[] $banners */
/** @var int $affiliate_id */
?>
<div id="esaf-creatives-banners" class="wafp-grid">
  <?php do_action('esaf_creatives_banners'); ?>
  <?php if(is_array($banners) && count($banners)) : ?>
    <p class="esaf-col-xs-12 esaf-creatives-banners-intro"><?php esc_html_e('Click a thumbnail to copy & paste code', 'easy-affiliate'); ?></p>
    <div id="esaf-creatives-banners-grid" class="esaf-row">
      <?php
        foreach ($banners as $banner) {
          echo CreativesHelper::banner_grid_item($banner, $affiliate_id);
        }
      ?>
    </div>
    <div id="esaf-banner-link-get-html-code-popup" class="esaf-popup mfp-hide">
      <div class="esaf-popup-content">
        <p class="esaf-banner-link-title"><?php esc_html_e('Banner ID:', 'easy-affiliate'); ?><b id="esaf-banner-link-get-html-id"></b></p>
        <img id="esaf-banner-link-information" src="" alt="" />
        <p class="esaf-banner-link-size">
          <span id="esaf-banner-link-information-width"></span> x <span id="esaf-banner-link-information-height"></span>
        </p>
        <h4 class="esaf-get-html-code-title"><?php esc_html_e('Copy and Paste code', 'easy-affiliate'); ?></h4>
        <textarea id="esaf-banner-link-get-html-code-field" class="esaf-code-textarea" readonly></textarea>
        <input type="text" id="esaf-banner-link-get-html-code-url-only" class="esaf-invisible-field" />
        <div class="esaf-get-html-code-copy-buttons">
          <button id="esaf-banner-link-get-html-code-copy-all" class="esaf-transparent-button esaf-copy-clipboard" type="button" data-clipboard-target="#esaf-banner-link-get-html-code-field">
            <i class="ea-icon ea-icon-docs"></i>
            <?php esc_html_e('Copy All', 'easy-affiliate'); ?>
          </button>
          <button id="esaf-banner-link-get-html-code-copy-url-only" class="esaf-transparent-button esaf-copy-clipboard" type="button" data-clipboard-target="#esaf-banner-link-get-html-code-url-only">
            <i class="ea-icon ea-icon-docs"></i>
            <?php esc_html_e('Copy URL Only', 'easy-affiliate'); ?>
          </button>
        </div>
      </div>
    </div>
  <?php else : ?>
    <p><?php esc_html_e('No banners found.', 'easy-affiliate'); ?></p>
  <?php endif; ?>
</div>
