<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\CreativesHelper;
?>
<div id="esaf-creatives-text-links">
  <?php do_action('esaf_creatives_text_links'); ?>

  <?php CreativesHelper::dashboard_my_affiliate_link($default_affiliate_url); ?>

  <?php if(is_array($text_links) && count($text_links)) : ?>
    <table id="esaf-creatives-text-links-table">
      <thead>
        <tr>
          <th class="esaf-th-id"><?php esc_html_e('ID', 'easy-affiliate'); ?></th>
          <th class="esaf-th-description"><?php esc_html_e('Description', 'easy-affiliate'); ?></th>
          <th class="esaf-th-example"><?php esc_html_e('Example', 'easy-affiliate'); ?></th>
          <th class="esaf-th-modified"><?php esc_html_e('Modified', 'easy-affiliate'); ?></th>
          <th class="esaf-th-actions"><?php esc_html_e('Actions', 'easy-affiliate'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($text_links as $text_link) : ?>
          <?php echo CreativesHelper::text_link_row($text_link, $affiliate_id); ?>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div id="esaf-text-link-get-html-code-popup" class="esaf-popup mfp-hide">
      <div class="esaf-popup-content">
        <h4 class="esaf-get-html-code-title"><?php esc_html_e('Copy and Paste code', 'easy-affiliate'); ?></h4>
        <textarea id="esaf-text-link-get-html-code-field" class="esaf-code-textarea" readonly></textarea>
        <input type="text" id="esaf-text-link-get-html-code-url-only" class="esaf-invisible-field" />
        <div class="esaf-get-html-code-copy-buttons">
          <button id="esaf-text-link-get-html-code-copy-all" class="esaf-transparent-button esaf-copy-clipboard" type="button" data-clipboard-target="#esaf-text-link-get-html-code-field">
            <i class="ea-icon ea-icon-docs"></i>
            <?php esc_html_e('Copy All', 'easy-affiliate'); ?>
          </button>
          <button id="esaf-text-link-get-html-code-copy-url-only" class="esaf-transparent-button esaf-copy-clipboard" type="button" data-clipboard-target="#esaf-text-link-get-html-code-url-only">
            <i class="ea-icon ea-icon-docs"></i>
            <?php esc_html_e('Copy URL Only', 'easy-affiliate'); ?>
          </button>
        </div>
      </div>
    </div>
  <?php do_action('esaf-affiliate-dashboard-creatives-text-links-page', $affiliate_id); ?>
  <?php else : ?>
    <p><?php esc_html_e('No text links found.', 'easy-affiliate'); ?></p>
  <?php endif; ?>
</div>
