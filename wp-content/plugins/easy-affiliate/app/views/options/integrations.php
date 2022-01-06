<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Helpers\OptionsHelper;
use EasyAffiliate\Lib\Utils;
/** @var \EasyAffiliate\Models\Options $options */
$integrations = array_merge($options->get_integrations(), OptionsHelper::get_upgrade_integrations());
?>
<?php if(is_array($integrations) && count($integrations)) : ?>
  <div class="esaf-integrations">
    <?php foreach($integrations as $integration) : ?>
      <?php
        $is_upgrade = !empty($integration['upgrade']);
        $connected = !empty($integration['connected']);
        $upgrade_class = $is_upgrade  ? ' esaf-integration-upgrade' : '';
        $data = $is_upgrade ? sprintf(' data-name="%s" data-plan="%s"', esc_attr($integration['name']), esc_attr($integration['plan'])) : '';
      ?>
      <div class="esaf-integration<?php echo esc_attr($upgrade_class); ?>"<?php echo $data; ?>>
        <div class="esaf-integration-header esaf-clearfix">
          <div class="esaf-integration-logo">
            <i class="ea-icon ea-icon-angle-right"></i>
            <img src="<?php echo esc_url($integration['logo']); ?>" alt="<?php echo esc_attr($integration['name']); ?> logo">
          </div>
          <div class="esaf-integration-details">
            <h3><?php echo esc_html($integration['title']); ?></h3>
            <p><?php echo esc_html($integration['description']); ?></p>
            <?php if($connected) : ?>
              <span class="esaf-integration-connected"><i class="ea-icon ea-icon-ok-circled2"></i><?php esc_html_e('Connected', 'easy-affiliate'); ?></span>
            <?php endif; ?>
          </div>
        </div>
        <?php if(isset($integration['settings']) && is_callable($integration['settings'])) : ?>
          <div class="esaf-integration-settings">
            <?php call_user_func($integration['settings']); ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
<?php else : ?>
  <p>
    <?php
      printf(
        // translators: %1$s: open link tag, %2$s: close link tag
        esc_html__('No integrations were found, integrations can be installed on the %1$sAdd-ons page%2$s.', 'easy-affiliate'),
        sprintf('<a href="%s">', esc_url(admin_url('admin.php?page=easy-affiliate-addons'))),
        '</a>'
      );
    ?>
  </p>
<?php endif; ?>
<div id="esaf-integration-upgrade-popup" class="esaf-popup esaf-upgrade-popup mfp-hide">
  <div class="esaf-popup-content">
    <i class="ea-icon ea-icon-lock"></i>
    <h3>
      <?php
        /* translators: %1$s: add-on name, %2$s: either Pro or Plus */
        esc_html_e('%1$s is a %2$s Feature', 'easy-affiliate');
      ?>
    </h3>
    <p>
      <?php
        /* translators: %1$s: add-on name, %2$s: either Pro or Plus */
        esc_html_e('We\'re sorry, the %1$s is not available on your plan. Please upgrade to the %2$s plan to unlock all these awesome features.', 'easy-affiliate');
      ?>
    </p>
    <a href="https://easyaffiliate.com/login/?redirect_to=%2Fpricing%2F" class="button button-primary button-hero"><?php esc_html_e('Upgrade to %s', 'easy-affiliate'); ?></a>
  </div>
</div>
