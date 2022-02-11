<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }
use EasyAffiliate\Helpers\CreativesHelper;
/** @var \EasyAffiliate\Models\CustomLink[] $custom_links */
/** @var \EasyAffiliate\Models\User $user */
?>
<?php do_action('esaf_creatives_custom_links'); ?>
<form id="esaf-dashboard-custom-links-form" class="esaf-form esaf-custom-links-form">
  <input type="hidden" name="action" value="creatives" />
  <input type="hidden" name="view" value="custom-links" />
  <h3><?php echo file_get_contents(ESAF_IMAGES_PATH . '/link.svg'); ?><?php esc_html_e('Custom Link Generator', 'easy-affiliate'); ?></h3>
  <p><?php esc_html_e('Browse this website and find pages or products that you want to link to directly.', 'easy-affiliate'); ?></p>
  <label for="esaf-dashboard-custom-link-url-field"><?php esc_html_e('Paste URL', 'easy-affiliate'); ?></label>
  <div class="esaf-dashboard-custom-link-input">
    <input type="url" id="esaf-dashboard-custom-link-url-field" />
    <span class="esaf-copy-clipboard" data-clipboard-target="#esaf-dashboard-custom-link-url-field"><i class="ea-icon ea-icon-docs"></i></span>
  </div>
  <div class="esaf-dashboard-custom-links-button">
    <button id="esaf-dashboard-custom-link-create"><?php esc_html_e('Create custom link', 'easy-affiliate'); ?></button>
  </div>
</form>
<form id="esaf-dashboard-custom-links-update-form" class="esaf-form esaf-custom-links-form esaf-hidden">
  <input type="hidden" name="action" value="creatives" />
  <input type="hidden" name="view" value="custom-links" />
  <input type="hidden" id="esaf-dashboard-custom-link-id" />
  <h3><?php echo file_get_contents(ESAF_IMAGES_PATH . '/link.svg'); ?><?php esc_html_e('Your Custom Link', 'easy-affiliate'); ?></h3>
  <p><?php esc_html_e('Browse this website and find pages or products that you want to link to directly.', 'easy-affiliate'); ?></p>
  <label for="esaf-dashboard-custom-link-url-update-field"><?php esc_html_e('URL', 'easy-affiliate'); ?></label>
  <div class="esaf-dashboard-custom-link-input">
    <input type="url" id="esaf-dashboard-custom-link-url-update-field" />
    <span class="esaf-copy-clipboard" data-clipboard-target="#esaf-dashboard-custom-link-url-update-field"><i class="ea-icon ea-icon-docs"></i></span>
  </div>
  <div class="esaf-dashboard-custom-links-button">
    <button id="esaf-dashboard-custom-link-update"><?php esc_html_e('Update link', 'easy-affiliate'); ?></button>
  </div>
</form>
<table id="esaf-dashboard-custom-links-table"<?php echo count($custom_links) == 0 ? ' class="esaf-hidden"' : ''; ?>>
  <thead>
    <tr>
      <th class="esaf-dashboard-custom-links-th-custom-link"><?php esc_html_e('Custom Link', 'easy-affiliate'); ?></th>
      <th class="esaf-dashboard-custom-links-th-destination-url"><?php esc_html_e('Destination URL', 'easy-affiliate'); ?></th>
      <th class="esaf-dashboard-custom-links-th-date-created"><?php esc_html_e('Date Created', 'easy-affiliate'); ?></th>
    </tr>
  </thead>
  <tbody id="esaf-dashboard-custom-links-table-body">
  <?php
    if(count($custom_links) > 0) {
      foreach($custom_links as $custom_link) {
        echo CreativesHelper::custom_link_row($custom_link, $user);
      }
    }
  ?>
  </tbody>
</table>
