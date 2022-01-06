<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Lib\Utils;
?>
<div class="esaf-wizard-white-box">
  <div class="esaf-wizard-box-title">
    <h2><?php esc_html_e('Awesome, You\'re All Set!', 'easy-affiliate'); ?></h2>
    <p><?php esc_html_e('Easy Affiliate is all set up and ready to use.', 'easy-affiliate'); ?></p>
  </div>
  <div class="esaf-wizard-box-content esaf-wizard-box-content-finish">
    <ol>
      <li>
        <?php
          printf(
            // translators: %s: the link URL to the affiliate signup form
            esc_html__('Affiliates can now apply to your Affiliate Program by applying at the following URL: %s', 'easy-affiliate'),
            sprintf('<a href="%1$s" target="_blank">%1$s</a>', esc_url(Utils::signup_url()))
          );
        ?>
      </li>
      <li>
        <?php
          printf(
            // translators: %s: the link URL to the affiliate dashboard
            esc_html__('You can check out your new Affiliate Dashboard here: %s', 'easy-affiliate'),
            sprintf('<a href="%1$s" target="_blank">%1$s</a>', esc_url(Utils::dashboard_url()))
          );
        ?>
      </li>
      <li><?php esc_html_e('You are now fully setup to track Affiliate Commissions on your sales as soon as your Affiliates start sharing your link!', 'easy-affiliate'); ?></li>
      <li>
        <?php
          printf(
            // translators: %1$s: open link tag, %2$s: close link tag
            esc_html__('%1$sSubscribe to the Easy Affiliate blog%2$s for tips on how to get the most out of your Affiliate Program.', 'easy-affiliate'),
            '<a href="https://easyaffiliate.com/blog/" target="_blank">',
            '</a>'
          );
        ?>
      </li>
    </ol>
    <div class="esaf-wizard-save-and-continue">
      <a href="<?php echo esc_url(admin_url('admin.php?page=easy-affiliate')); ?>" class="button button-primary button-hero"><?php esc_html_e('Finish Setup & Exit Wizard &rarr;', 'easy-affiliate'); ?></a>
    </div>
  </div>
</div>
