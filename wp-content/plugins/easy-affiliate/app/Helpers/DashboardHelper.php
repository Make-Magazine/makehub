<?php

namespace EasyAffiliate\Helpers;

use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Creative;
use EasyAffiliate\Models\Options;

class DashboardHelper {
  public static function active($link = 'home') {
    $action = isset($_REQUEST['action']) && is_string($_REQUEST['action']) ? sanitize_text_field(wp_unslash($_REQUEST['action'])) : 'home';

    if($action == $link) {
      echo ' class="esaf-nav-active"';
    }
  }

  public static function nav() {
    $options = Options::fetch();
    $show_creatives = (bool) Creative::get_count();
    ?>
    <div class="esaf-dashboard-responsive-nav">
      <div class="esaf-dashboard-responsive-nav-toggle">
        <i class="ea-icon ea-icon-menu"></i>
        <span><?php esc_html_e('Menu', 'easy-affiliate'); ?></span>
      </div>
    </div>
    <div class="esaf-dashboard-nav-wrapper">
      <div class="esaf-dashboard-nav">
        <a href="<?php echo esc_url(Utils::dashboard_url(['action' => 'home'])); ?>"<?php DashboardHelper::active(); ?>><?php esc_html_e('Home', 'easy-affiliate'); ?></a>
        <?php if($show_creatives) : ?>
          <a href="<?php echo esc_url(Utils::dashboard_url(['action' => 'creatives'])); ?>"<?php DashboardHelper::active('creatives'); ?>><?php esc_html_e('Links & Banners', 'easy-affiliate'); ?></a>
        <?php endif; ?>
        <a href="<?php echo esc_url(Utils::dashboard_url(['action' => 'account'])); ?>"<?php DashboardHelper::active('account'); ?>><?php esc_html_e('Account', 'easy-affiliate'); ?></a>
        <a href="<?php echo esc_url(Utils::dashboard_url(['action' => 'payments'])); ?>"<?php DashboardHelper::active('payments'); ?>><?php esc_html_e('Payments', 'easy-affiliate'); ?></a>
        <?php foreach($options->dash_nav as $page_id) : ?>
          <a href="<?php echo esc_url(get_permalink($page_id)); ?>"><?php echo esc_html(get_the_title($page_id)); ?></a>
        <?php endforeach; ?>
        <?php do_action('esaf-affiliate-dashboard-nav'); ?>
        <a href="<?php echo esc_url(Utils::logout_url()); ?>"><?php esc_html_e('Logout', 'easy-affiliate'); ?></a>
      </div>
    </div>
    <?php
  }

  /**
   * Get the header for the Pro Dashboard page
   *
   * @return string
   */
  public static function get_pro_header() {
    ob_start();

    require ESAF_VIEWS_PATH . '/pro-dashboard/header.php';

    return apply_filters('esaf_pro_dashboard_header_html', ob_get_clean());
  }

  /**
   * Get the footer for the Pro Dashboard page
   *
   * @return string
   */
  public static function get_pro_footer() {
    ob_start();

    require ESAF_VIEWS_PATH . '/pro-dashboard/footer.php';

    return apply_filters('esaf_pro_dashboard_footer_html', ob_get_clean());
  }
}
