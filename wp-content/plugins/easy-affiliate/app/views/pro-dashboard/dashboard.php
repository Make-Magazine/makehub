<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\DashboardHelper;
use EasyAffiliate\Models\Creative;
use EasyAffiliate\Models\Options;
use EasyAffiliate\Lib\Utils;
$options = Options::fetch();
$show_creatives = (bool) Creative::get_count();
echo DashboardHelper::get_pro_header();
?>
<div class="esaf-pro-dashboard-layout">
  <div class="esaf-pro-dashboard-responsive-nav">
    <div class="esaf-pro-dashboard-responsive-nav-toggle">
      <i class="ea-icon ea-icon-menu"></i>
      <span><?php esc_html_e('Menu', 'easy-affiliate'); ?></span>
    </div>
  </div>
  <div class="esaf-pro-dashboard-menu">
    <div class="esaf-pro-dashboard-menu-inner">
      <a href="<?php echo esc_url(Utils::dashboard_url(['action' => 'home'])); ?>" <?php DashboardHelper::active(); ?>><?php echo file_get_contents(ESAF_IMAGES_PATH . '/home.svg'); ?><?php esc_html_e('Home', 'easy-affiliate'); ?></a>
      <?php if($show_creatives) : ?>
        <a href="<?php echo esc_url(Utils::dashboard_url(['action' => 'creatives'])); ?>" <?php DashboardHelper::active('creatives'); ?>><?php echo file_get_contents(ESAF_IMAGES_PATH . '/link.svg'); ?><?php esc_html_e('Links & Banners', 'easy-affiliate'); ?></a>
      <?php endif; ?>
      <a href="<?php echo esc_url(Utils::dashboard_url(['action' => 'account'])); ?>" <?php DashboardHelper::active('account'); ?>><?php echo file_get_contents(ESAF_IMAGES_PATH . '/account.svg'); ?><?php esc_html_e('Account', 'easy-affiliate'); ?></a>
      <a href="<?php echo esc_url(Utils::dashboard_url(['action' => 'payments'])); ?>" <?php DashboardHelper::active('payments'); ?>><?php echo file_get_contents(ESAF_IMAGES_PATH . '/payments.svg'); ?><?php esc_html_e('Payments', 'easy-affiliate'); ?></a>
      <?php foreach($options->dash_nav as $page_id) : ?>
        <a href="<?php echo esc_url(get_permalink($page_id)); ?>"><i class="ea-icon ea-icon-doc-text"></i><?php echo esc_html(get_the_title($page_id)); ?></a>
      <?php endforeach; ?>
      <?php do_action('esaf_pro_dashboard_nav'); ?>
      <a href="<?php echo esc_url(Utils::logout_url()); ?>"><?php echo file_get_contents(ESAF_IMAGES_PATH . '/logout.svg'); ?><?php esc_html_e('Logout', 'easy-affiliate'); ?></a>
    </div>
  </div>
  <div class="esaf-pro-dashboard-content">
    <?php
      while(have_posts()) {
        the_post();
        the_content();
      }
    ?>
  </div>
</div>
<?php echo DashboardHelper::get_pro_footer(); ?>
