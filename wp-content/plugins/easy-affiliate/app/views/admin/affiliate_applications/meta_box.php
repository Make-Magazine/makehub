<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AffiliateApplicationHelper;
$websites_required = apply_filters('esaf_application_websites_required', true);
$strategy_required = apply_filters('esaf_application_strategy_required', true);
?>

<input type="hidden" name="_esaf_affiliateapplication_nonce" value="<?php echo esc_attr(wp_create_nonce('esaf_save_affiliate_application')); ?>" />

<table class="form-table">
  <tbody>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo esc_attr($app->status_str); ?>"><?php esc_html_e('Status', 'easy-affiliate'); ?></label>
      </th>
      <td>
        <select name="<?php echo esc_attr($app->status_str); ?>" id="<?php echo esc_attr($app->status_str); ?>">
          <option value="pending" <?php selected($app->status,'pending'); ?>><?php esc_html_e('Awaiting Approval', 'easy-affiliate'); ?></option>
          <option value="approved" <?php selected($app->status,'approved'); ?>><?php esc_html_e('Approved', 'easy-affiliate'); ?></option>
          <option value="ignored" <?php selected($app->status,'ignored'); ?>><?php esc_html_e('Ignored', 'easy-affiliate'); ?></option>
        </select>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo esc_attr($app->first_name_str); ?>"><?php esc_html_e('First Name', 'easy-affiliate'); ?></label>
      </th>
      <td>
        <input type="text" name="<?php echo esc_attr($app->first_name_str); ?>" id="<?php echo esc_attr($app->first_name_str); ?>" class="regular-text" value="<?php echo esc_attr($app->first_name); ?>" required />
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo esc_attr($app->last_name_str); ?>"><?php esc_html_e('Last Name', 'easy-affiliate'); ?></label>
      </th>
      <td>
        <input type="text" name="<?php echo esc_attr($app->last_name_str); ?>" id="<?php echo esc_attr($app->last_name_str); ?>" class="regular-text" value="<?php echo esc_attr($app->last_name); ?>" required />
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo esc_attr($app->email_str); ?>"><?php esc_html_e('Email', 'easy-affiliate'); ?></label>
      </th>
      <td>
        <input type="email" name="<?php echo esc_attr($app->email_str); ?>" id="<?php echo esc_attr($app->email_str); ?>" class="regular-text" value="<?php echo esc_attr($app->email); ?>" required />
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo esc_attr($app->websites_str); ?>"><?php esc_html_e('Please list the websites you\'ll use to promote us (one per line)', 'easy-affiliate'); ?></label>
      </th>
      <td>
        <textarea name="<?php echo esc_attr($app->websites_str); ?>" id="<?php echo esc_attr($app->websites_str); ?>" class="large-text esaf-larger-textarea"<?php echo $websites_required ? ' required' : ''; ?>><?php echo esc_textarea($app->websites); ?></textarea>
        <?php echo AffiliateApplicationHelper::get_website_audit_links_html($app->websites); ?>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo esc_attr($app->strategy_str); ?>"><?php esc_html_e('How are you planning to promote us?', 'easy-affiliate'); ?></label>
      </th>
      <td>
        <textarea name="<?php echo esc_attr($app->strategy_str); ?>" id="<?php echo esc_attr($app->strategy_str); ?>" class="large-text esaf-larger-textarea"<?php echo $strategy_required ? ' required' : ''; ?>><?php echo esc_textarea($app->strategy); ?></textarea>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo esc_attr($app->social_str); ?>"><?php esc_html_e('Social media handles/links', 'easy-affiliate'); ?></label>
      </th>
      <td>
        <textarea name="<?php echo esc_attr($app->social_str); ?>" id="<?php echo esc_attr($app->social_str); ?>" class="large-text esaf-larger-textarea"><?php echo esc_textarea($app->social); ?></textarea>
        <?php echo AffiliateApplicationHelper::get_website_audit_links_html($app->social, false); ?>
      </td>
    </tr>
  </tbody>
</table>
