<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Lib\Utils;
?>
<p><?php esc_html_e('Only affiliates can access this page. If you would like to become an affiliate, please click the button below:', 'easy-affiliate'); ?></p>
<?php if($options->affiliate_agreement_enabled): ?>
  <p>
    <?php
      printf(
        // translators: %1$s: open link tag, %2$s: close link tag
        esc_html__('By clicking the button below, you agree to the %1$sAffiliate Sign-up Agreement%2$s.', 'easy-affiliate'),
        '<a href="#" id="wafp_agreement_agree">',
        '</a>'
      );
    ?>
  </p>
  <div id="wafp_signup_agreement_text" style="display:none;">
    <div class="esaf-signup-agreement-text"><?php echo Utils::has_html_tag($options->affiliate_agreement_text) ? $options->affiliate_agreement_text : nl2br($options->affiliate_agreement_text); ?></div>
  </div>
  <p></p>
<?php endif; ?>
<form name="become_affiliate_form" action="" method="post">
  <input type="hidden" name="become_affiliate_submit" value="1">
  <button><?php esc_html_e('Become an Affiliate', 'easy-affiliate'); ?></button>
</form>
