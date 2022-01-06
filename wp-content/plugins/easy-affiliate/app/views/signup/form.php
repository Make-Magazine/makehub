<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Lib\Utils;
/** @var \EasyAffiliate\Models\Options $options */
/** @var \EasyAffiliate\Models\User $user */
/** @var \EasyAffiliate\Models\AffiliateApplication $app */
?>

<div class="esaf esaf-signup">

  <form class="esaf-form esaf-signup-form" name="wafp_registerform" id="wafp_registerform" method="post">

    <?php if(isset($errors) && is_array($errors) && count($errors) > 0) : ?>
      <div class="esaf-form-errors">
        <?php foreach($errors as $error) : ?>
          <div class="esaf-form-error"><strong><?php esc_html_e('ERROR', 'easy-affiliate'); ?></strong>: <?php echo esc_html($error); ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if(!!$app && $app->ready()) : ?>
      <input type="hidden" name="application" value="<?php echo esc_attr($app->uuid); ?>" />
    <?php endif; ?>

    <?php $readonly = ($logged_in ? 'readonly' : ''); ?>

    <div class="esaf-form-row esaf_first_name">
      <div class="esaf-form-label">
        <label for="<?php echo esc_attr($user->first_name_str); ?>"><?php esc_html_e('First Name', 'easy-affiliate'); ?><span class="esaf-required">*</span></label>
        <span class="esaf-validation-error"><?php esc_html_e('Required', 'easy-affiliate'); ?></span>
      </div>
      <input type="text" name="<?php echo esc_attr($user->first_name_str); ?>" id="<?php echo esc_attr($user->first_name_str); ?>" value="<?php echo esc_attr($user->first_name); ?>" required <?php echo !empty($user->first_name) ? $readonly : ''; ?> />
    </div>

    <div class="esaf-form-row esaf_last_name">
      <div class="esaf-form-label">
        <label for="<?php echo esc_attr($user->last_name_str); ?>"><?php esc_html_e('Last Name', 'easy-affiliate'); ?><span class="esaf-required">*</span></label>
        <span class="esaf-validation-error"><?php esc_html_e('Required', 'easy-affiliate'); ?></span>
      </div>
      <input type="text" name="<?php echo esc_attr($user->last_name_str); ?>" id="<?php echo esc_attr($user->last_name_str); ?>" value="<?php echo esc_attr($user->last_name); ?>" required <?php echo !empty($user->last_name) ? $readonly : ''; ?> />
    </div>

    <div class="esaf-form-row esaf_username">
      <div class="esaf-form-label">
        <label for="<?php echo esc_attr($user->user_login_str); ?>"><?php esc_html_e('Username', 'easy-affiliate'); ?><span class="esaf-required">*</span></label>
        <span class="esaf-validation-error"><?php esc_html_e('Required', 'easy-affiliate'); ?></span>
      </div>
      <input type="text" name="<?php echo esc_attr($user->user_login_str); ?>" id="<?php echo esc_attr($user->user_login_str); ?>" value="<?php echo esc_attr($user->user_login); ?>" required <?php echo $readonly; ?> />
    </div>

    <div class="esaf-form-row esaf_email">
      <div class="esaf-form-label">
        <label for="<?php echo esc_attr($user->user_email_str); ?>"><?php esc_html_e('Email', 'easy-affiliate'); ?><span class="esaf-required">*</span></label>
        <span class="esaf-validation-error"><?php esc_html_e('Required and must be a valid email address', 'easy-affiliate'); ?></span>
      </div>
      <input type="email" name="<?php echo esc_attr($user->user_email_str); ?>" id="<?php echo esc_attr($user->user_email_str); ?>" value="<?php echo esc_attr($user->user_email); ?>" required <?php echo $readonly; ?> />
    </div>

    <?php if($options->show_address_fields) : ?>
      <div class="esaf-form-row esaf_address_one">
        <div class="esaf-form-label">
          <label for="<?php echo esc_attr($user->address_one_str); ?>"><?php esc_html_e('Address Line 1', 'easy-affiliate'); echo $options->require_address_fields ? '<span class="esaf-required">*</span>' : ''; ?></label>
          <?php if($options->require_address_fields) : ?>
            <span class="esaf-validation-error"><?php esc_html_e('Required', 'easy-affiliate'); ?></span>
          <?php endif; ?>
        </div>
        <input type="text" name="<?php echo esc_attr($user->address_one_str); ?>" id="<?php echo esc_attr($user->address_one_str); ?>" value="<?php echo esc_attr($user->address_one); ?>"<?php echo $options->require_address_fields ? ' required' : ''; ?> />
      </div>

      <div class="esaf-form-row esaf_address_two">
        <div class="esaf-form-label">
          <label for="<?php echo esc_attr($user->address_two_str); ?>"><?php esc_html_e('Address Line 2', 'easy-affiliate'); ?></label>
        </div>
        <input type="text" name="<?php echo esc_attr($user->address_two_str); ?>" id="<?php echo esc_attr($user->address_two_str); ?>" value="<?php echo esc_attr($user->address_two); ?>" />
      </div>

      <div class="esaf-form-row esaf_city">
        <div class="esaf-form-label">
          <label for="<?php echo esc_attr($user->city_str); ?>"><?php esc_html_e('City', 'easy-affiliate'); echo $options->require_address_fields ? '<span class="esaf-required">*</span>' : ''; ?></label>
          <?php if($options->require_address_fields) : ?>
            <span class="esaf-validation-error"><?php esc_html_e('Required', 'easy-affiliate'); ?></span>
          <?php endif; ?>
        </div>
        <input type="text" name="<?php echo esc_attr($user->city_str); ?>" id="<?php echo esc_attr($user->city_str); ?>" value="<?php echo esc_attr($user->city); ?>"<?php echo $options->require_address_fields ? ' required' : ''; ?> />
      </div>

      <div class="esaf-form-row esaf_state">
        <div class="esaf-form-label">
          <label for="<?php echo esc_attr($user->state_str); ?>"><?php esc_html_e('State/Province', 'easy-affiliate'); echo $options->require_address_fields ? '<span class="esaf-required">*</span>' : ''; ?></label>
          <?php if($options->require_address_fields) : ?>
            <span class="esaf-validation-error"><?php esc_html_e('Required', 'easy-affiliate'); ?></span>
          <?php endif; ?>
        </div>
        <input type="text" name="<?php echo esc_attr($user->state_str); ?>" id="<?php echo esc_attr($user->state_str); ?>" value="<?php echo esc_attr($user->state); ?>"<?php echo $options->require_address_fields ? ' required' : ''; ?> />
      </div>

      <div class="esaf-form-row esaf_zip">
        <div class="esaf-form-label">
          <label for="<?php echo esc_attr($user->zip_str); ?>"><?php esc_html_e('Zip/Postcode', 'easy-affiliate'); echo $options->require_address_fields ? '<span class="esaf-required">*</span>' : ''; ?></label>
          <?php if($options->require_address_fields) : ?>
            <span class="esaf-validation-error"><?php esc_html_e('Required', 'easy-affiliate'); ?></span>
          <?php endif; ?>
        </div>
        <input type="text" name="<?php echo esc_attr($user->zip_str); ?>" id="<?php echo esc_attr($user->zip_str); ?>" value="<?php echo esc_attr($user->zip); ?>"<?php echo $options->require_address_fields ? ' required' : ''; ?> />
      </div>

      <div class="esaf-form-row esaf_country">
        <div class="esaf-form-label">
          <label for="<?php echo esc_attr($user->country_str); ?>"><?php esc_html_e('Country', 'easy-affiliate'); echo $options->require_address_fields ? '<span class="esaf-required">*</span>' : ''; ?></label>
          <?php if($options->require_address_fields) : ?>
            <span class="esaf-validation-error"><?php esc_html_e('Required', 'easy-affiliate'); ?></span>
          <?php endif; ?>
        </div>
        <?php echo AppHelper::countries_dropdown($user->country_str, $user->country_str, $user->country, $options->require_address_fields); ?>
      </div>
    <?php endif; ?>

    <?php if($options->show_tax_id_fields) : ?>
      <div class="esaf-form-row esaf_tax_id_us">
        <div class="esaf-form-label">
          <label for="<?php echo esc_attr($user->tax_id_us_str); ?>"><?php esc_html_e('SSN / Tax ID', 'easy-affiliate'); echo $options->require_tax_id_fields ? '<span class="esaf-required">*</span>' : ''; ?></label>
        </div>
        <input type="text" name="<?php echo esc_attr($user->tax_id_us_str); ?>" id="<?php echo esc_attr($user->tax_id_us_str); ?>" value="<?php echo esc_attr($user->tax_id_us); ?>" />
      </div>

      <div class="esaf-form-row esaf_tax_id_int">
        <div class="esaf-form-label">
          <label for="<?php echo esc_attr($user->tax_id_int_str); ?>"><?php esc_html_e('International Tax ID', 'easy-affiliate'); echo $options->require_tax_id_fields ? '<span class="esaf-required">*</span>' : ''; ?></label>
        </div>
        <input type="text" name="<?php echo esc_attr($user->tax_id_int_str); ?>" id="<?php echo esc_attr($user->tax_id_int_str); ?>" value="<?php echo esc_attr($user->tax_id_int); ?>" />
      </div>
    <?php endif; ?>

    <?php if($options->is_payout_method_paypal()) : ?>
      <div class="esaf-form-row esaf_paypal_email">
        <div class="esaf-form-label">
          <label for="<?php echo esc_attr($user->paypal_email_str); ?>"><?php esc_html_e('PayPal Email', 'easy-affiliate'); ?><span class="esaf-required">*</span></label>
          <span class="esaf-validation-error"><?php esc_html_e('Required', 'easy-affiliate'); ?></span>
        </div>
        <input type="text" name="<?php echo esc_attr($user->paypal_email_str); ?>" id="<?php echo esc_attr($user->paypal_email_str); ?>" value="<?php echo esc_attr($user->paypal_email); ?>" required />
      </div>
    <?php endif; ?>

    <?php do_action('esaf-inner-user-signup-fields'); ?>

    <?php if(!$logged_in) : ?>
      <div class="esaf-form-row esaf_password">
        <div class="esaf-form-label">
          <label for="<?php echo esc_attr($user->user_pass_str); ?>"><?php esc_html_e('Create a Password', 'easy-affiliate'); ?><span class="esaf-required">*</span></label>
          <span class="esaf-validation-error"><?php esc_html_e('Required', 'easy-affiliate'); ?></span>
        </div>
        <input type="password" name="<?php echo esc_attr($user->user_pass_str); ?>" id="<?php echo esc_attr($user->user_pass_str); ?>" class="esaf-password" required />
      </div>

      <div class="esaf-form-row esaf_password_confirm">
        <div class="esaf-form-label">
          <label for="wafp_user_password_confirm"><?php esc_html_e('Password Confirmation', 'easy-affiliate'); ?><span class="esaf-required">*</span></label>
          <span class="esaf-validation-error"><?php esc_html_e('Required and must match password', 'easy-affiliate'); ?></span>
        </div>
        <input type="password" name="wafp_user_password_confirm" id="wafp_user_password_confirm" class="esaf-password-confirm" required />
      </div>
    <?php endif; ?>

    <?php if($options->affiliate_agreement_enabled) : ?>
      <div class="esaf-form-row esaf_signup_agreement">
        <input type="checkbox" name="wafp_user_signup_agreement" id="wafp_user_signup_agreement" required />
        <?php
          $affiliate_agreement_label = sprintf(
            // translators: %1$s: open link tag, %2$s close link tag
            esc_html__('I agree to the %1$sAffiliate Sign-up Agreement%2$s', 'easy-affiliate'),
            '<a href="#" id="wafp_agreement_agree">',
            '</a>'
          );
        ?>
        <label for="wafp_user_signup_agreement"><?php echo $affiliate_agreement_label; ?><span class="esaf-required">*</span></label>
        <div id="wafp_signup_agreement_text" style="display:none;">
          <div class="esaf-signup-agreement-text"><?php echo Utils::has_html_tag($options->affiliate_agreement_text) ? $options->affiliate_agreement_text : nl2br($options->affiliate_agreement_text); ?></div>
        </div>
      </div>
    <?php endif; ?>

    <?php do_action('esaf-user-signup-fields'); ?>

    <label for="wafp_honeypot" class="esaf-visually-hidden"><?php esc_html_e('Field should be empty', 'easy-affiliate'); ?></label>
    <input type="text" name="wafp_honeypot" id="wafp_honeypot" class="esaf-visually-hidden" autocomplete="new-password" />

    <div class="esaf-form-button-row">
      <button class="esaf-submit"><?php esc_html_e('Sign Up', 'easy-affiliate'); ?></button>
      <img src="<?php echo esc_url(ESAF_IMAGES_URL . '/loading.gif'); ?>" class="esaf-loading-gif esaf-hidden" alt="Loading..." />
      <span class="esaf-form-has-errors"><?php esc_html_e('Please fix the errors above', 'easy-affiliate'); ?></span>
    </div>

  </form>

</div>
