<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
$websites_required = apply_filters('esaf_application_websites_required', true);
$strategy_required = apply_filters('esaf_application_strategy_required', true);
?>
<div class="esaf esaf-affiliate-application">

  <form class="esaf-form esaf-affiliate-application-form" method="post">

    <?php if(isset($errors) && is_array($errors) && count($errors) > 0) : ?>
      <div class="esaf-form-errors">
        <?php foreach($errors as $error) : ?>
          <div class="esaf-form-error"><strong><?php esc_html_e('ERROR', 'easy-affiliate'); ?></strong>: <?php echo esc_html($error); ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="esaf-form-description"><?php esc_html_e('Please fill out this form to apply for our Affiliate Program:', 'easy-affiliate'); ?></div>

    <?php if(!empty($form->affiliate)): ?>
      <input type="hidden" name="<?php echo esc_attr($form->affiliate_str); ?>" value="<?php echo esc_attr($form->affiliate); ?>" />
    <?php endif; ?>

    <?php $readonly = ($logged_in ? 'readonly' : ''); ?>

    <div class="esaf-form-row esaf_first_name">
      <div class="esaf-form-label">
        <label for="<?php echo esc_attr($form->first_name_str); ?>"><?php esc_html_e('First Name', 'easy-affiliate'); ?><span class="esaf-required">*</span></label>
        <span class="esaf-validation-error"><?php esc_html_e('Required', 'easy-affiliate'); ?></span>
      </div>
      <input type="text" name="<?php echo esc_attr($form->first_name_str); ?>" id="<?php echo esc_attr($form->first_name_str); ?>" value="<?php echo esc_attr($form->first_name); ?>" required <?php echo !empty($form->first_name) ? esc_attr($readonly) : ''; ?> />
    </div>

    <div class="esaf-form-row esaf_last_name">
      <div class="esaf-form-label">
        <label for="<?php echo esc_attr($form->last_name_str); ?>"><?php esc_html_e('Last Name', 'easy-affiliate'); ?><span class="esaf-required">*</span></label>
        <span class="esaf-validation-error"><?php esc_html_e('Required', 'easy-affiliate'); ?></span>
      </div>
      <input type="text" name="<?php echo esc_attr($form->last_name_str); ?>" id="<?php echo esc_attr($form->last_name_str); ?>" value="<?php echo esc_attr($form->last_name); ?>" required <?php echo !empty($form->last_name) ? esc_attr($readonly) : ''; ?> />
    </div>

    <div class="esaf-form-row esaf_email">
      <div class="esaf-form-label">
        <label for="<?php echo esc_attr($form->email_str); ?>"><?php esc_html_e('Email', 'easy-affiliate'); ?><span class="esaf-required">*</span></label>
        <span class="esaf-validation-error"><?php esc_html_e('Required and must be a valid email address', 'easy-affiliate'); ?></span>
      </div>
      <input type="email" name="<?php echo esc_attr($form->email_str); ?>" id="<?php echo esc_attr($form->email_str); ?>" value="<?php echo esc_attr($form->email); ?>" required <?php echo esc_attr($readonly); ?> />
    </div>

    <div class="esaf-form-row esaf_websites">
      <div class="esaf-form-label">
        <label for="<?php echo esc_attr($form->websites_str); ?>"><?php esc_html_e('Please list the websites you\'ll use to promote us (one per line)', 'easy-affiliate'); ?><?php echo $websites_required ? '<span class="esaf-required">*</span>' : ''; ?></label>
        <?php if($websites_required) : ?>
          <span class="esaf-validation-error"><?php esc_html_e('Required', 'easy-affiliate'); ?></span>
        <?php endif; ?>
      </div>
      <textarea name="<?php echo esc_attr($form->websites_str); ?>" id="<?php echo esc_attr($form->websites_str); ?>"<?php echo $websites_required ? ' required' : ''; ?>><?php echo esc_textarea($form->websites); ?></textarea>
    </div>

    <div class="esaf-form-row esaf_strategy">
      <div class="esaf-form-label">
        <label for="<?php echo esc_attr($form->strategy_str); ?>"><?php esc_html_e('How are you planning to promote us?', 'easy-affiliate'); ?><?php echo $strategy_required ? '<span class="esaf-required">*</span>' : ''; ?></label>
        <?php if($strategy_required) : ?>
          <span class="esaf-validation-error"><?php esc_html_e('Required', 'easy-affiliate'); ?></span>
        <?php endif; ?>
      </div>
      <textarea name="<?php echo esc_attr($form->strategy_str); ?>" id="<?php echo esc_attr($form->strategy_str); ?>"<?php echo $strategy_required ? ' required' : ''; ?>><?php echo esc_textarea($form->strategy); ?></textarea>
    </div>

    <div class="esaf-form-row esaf_social">
      <div class="esaf-form-label">
        <label for="<?php echo esc_attr($form->social_str); ?>"><?php esc_html_e('Social media handles/links', 'easy-affiliate'); ?></label>
      </div>
      <textarea name="<?php echo esc_attr($form->social_str); ?>" id="<?php echo esc_attr($form->social_str); ?>"><?php echo esc_textarea($form->social); ?></textarea>
    </div>

    <?php do_action('esaf-affiliate-application-before-submit'); ?>

    <label for="wafp_honeypot" class="esaf-visually-hidden"><?php esc_html_e('Field should be empty', 'easy-affiliate'); ?></label>
    <input type="text" name="wafp_honeypot" id="wafp_honeypot" class="esaf-visually-hidden" autocomplete="new-password" />

    <div class="esaf-form-button-row">
      <button class="esaf-submit"><?php esc_html_e('Apply to become an Affiliate', 'easy-affiliate'); ?></button>
      <img src="<?php echo esc_url(ESAF_IMAGES_URL . '/loading.gif'); ?>" class="esaf-loading-gif esaf-hidden" alt="Loading..." />
      <span class="esaf-form-has-errors"><?php esc_html_e('Please fix the errors above', 'easy-affiliate'); ?></span>
    </div>

  </form>

</div>
