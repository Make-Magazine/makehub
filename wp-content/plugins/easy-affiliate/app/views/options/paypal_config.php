<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="esaf-payment-integration-config esaf-paypal-config">
    <p><strong><?php esc_html_e('Here are the steps you\'ll need to follow to integrate Easy Affiliate with PayPal', 'easy-affiliate'); ?>:</strong></p>
  <h3><?php esc_html_e('To create your payment button:', 'easy-affiliate'); ?></h3>
  <ol>
    <li><?php esc_html_e('Log Into Your PayPal Account', 'easy-affiliate'); ?></li>
    <li>
      <?php
        printf(
          // translators: %s: PayPal Buttons URL
          esc_html__('Go to %s', 'easy-affiliate'),
          '<a href="https://www.paypal.com/buttons" target="_blank">https://www.paypal.com/buttons</a>'
        );
      ?>
    </li>
    <li><?php esc_html_e('Create either a "Buy Now" or "Subscribe" Button', 'easy-affiliate'); ?></li>
    <li><?php esc_html_e('Make Sure You Uncheck "Save Button at PayPal" in Step 2', 'easy-affiliate'); ?></li>
    <li><?php esc_html_e('Make Sure "Add advanced variables" is checked in Step 3 and add the following text into the "Advanced Variables" text area:', 'easy-affiliate'); ?><br/>
<pre>
notify_url=[esaf_ipn]
custom=[esaf_custom_args]</pre>
    </li>
    <li><?php esc_html_e('Click "Create Button"', 'easy-affiliate'); ?></li>
    <li><?php esc_html_e('Click "Remove code protection"', 'easy-affiliate'); ?></li>
    <li><?php esc_html_e('Now you can copy your button\'s code and paste it somewhere on this site. Note: the button must reside on this site in a page or post in order for the affiliate tracking to work properly.', 'easy-affiliate'); ?></li>
    <li>
      <?php
        printf(
          // translators: %1$s: first code to replace, %2$s first code replacement, %3$s second code to replace, %4$s second code replacement
          esc_html__('Lastly you\'ll need to edit the HTML you copied onto your page and replace %1$s with just %2$s and %3$s with just %4$s. This is required to get around a change made to shortcode processing in WordPress 4.2.3 and later.', 'easy-affiliate'),
          '<code>&lt;input type="hidden" name="custom" value="[esaf_custom_args]"&gt;</code>',
          '<code>[esaf_custom_args]</code>',
          '<code>&lt;input type="hidden" name="notify_url" value="[esaf_ipn]"&gt;</code>',
          '<code>[esaf_ipn]</code>'
        );
      ?>
    </li>
  </ol>
  <h3><?php esc_html_e('(Optional) Setup Easy Affiliate to automatically record refunds and process recurring payments:', 'easy-affiliate'); ?></h3>
  <ol>
    <li><?php esc_html_e('Go to "My Account" -> "Profile" -> "Instant Payment Notification Preferences" in PayPal', 'easy-affiliate'); ?></li>
    <li><?php esc_html_e('Make sure IPN is enabled', 'easy-affiliate'); ?></li>
    <li><?php esc_html_e('Paste the following URL into the Notification URL text field and hit save:', 'easy-affiliate'); ?><br/>
      <pre><?php echo esc_url(ESAF_SCRIPT_URL . "&controller=paypal&action=ipn"); ?></pre>
    </li>
  </ol>
  <p><strong><?php esc_html_e('Valid PayPal Email Addresses (recommended)', 'easy-affiliate') ?></strong></p>
  <input type="text" style="width: 100%;" class="form-field" id="<?php echo esc_attr($options->paypal_emails_str); ?>" name="<?php echo esc_attr($options->paypal_emails_str); ?>" value="<?php echo esc_attr($options->paypal_emails); ?>" /><br/>
  <div class="description"><?php esc_html_e("This is a list of valid paypal email addresses that IPN requests can be recieved from. If this is left blank then any valid IPN request will be recorded as a transaction. It is recommended that you enter all the paypal email addresses (comma separated) that could be used to send IPN requests to your affiliate commission tracker to prevent fraud.", 'easy-affiliate') ?></div>
  <p><input type="checkbox" class="esaf-toggle-switch" name="<?php echo esc_attr($options->paypal_sandbox_str); ?>" id="<?php echo esc_attr($options->paypal_sandbox_str); ?>"<?php checked($options->paypal_sandbox); ?>/><label for="<?php echo esc_attr($options->paypal_sandbox_str); ?>"></label><label for="<?php echo esc_attr($options->paypal_sandbox_str); ?>">&nbsp;<?php esc_html_e('Use PayPal Sandbox','easy-affiliate'); ?></label></p>
  <p><strong><?php esc_html_e('Recieve IPN Requests from hosts other than PayPal:', 'easy-affiliate') ?></strong></p>
  <input type="text" style="width: 100%;" class="form-field" id="<?php echo esc_attr($options->paypal_src_str); ?>" name="<?php echo esc_attr($options->paypal_src_str); ?>" value="<?php echo esc_attr($options->paypal_src); ?>" />
  <span class="description"><?php esc_html_e('Please enter the IP Addresses, separated by commas, of hosts authorized for Easy Affiliate to receive IPN requests from. If this is left blank then only IPN requests coming directly from PayPal will be recorded.', 'easy-affiliate'); ?></span><br/>
  <p><strong><?php esc_html_e('Forward IPN Requests to additional URLs:', 'easy-affiliate') ?></strong></p>
  <textarea style="width: 100%; min-height: 150px;" class="form-field" id="<?php echo esc_attr($options->paypal_dst_str); ?>" name="<?php echo esc_attr($options->paypal_dst_str); ?>"><?php echo esc_textarea($options->paypal_dst); ?></textarea><br/>
  <span class="description"><?php esc_html_e('Please enter URLs to forward IPN requests to. Each URL should be on its own line. If this is left blank then IPN requests will not be forwarded.', 'easy-affiliate'); ?></span>
</div>
