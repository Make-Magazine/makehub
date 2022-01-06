<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Controllers\DashboardCtrl;
use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\User;
$user = Utils::get_currentuserinfo();
$logo_url = DashboardCtrl::get_pro_dashboard_logo_url();
?><!doctype html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php wp_head(); ?>
  </head>
  <body <?php body_class(); ?>>
    <?php Utils::wp_body_open(); ?>
    <div class="esaf-pro-dashboard">
      <header class="esaf-pro-dashboard-header">
        <a href="<?php echo esc_url(home_url()); ?>" class="esaf-pro-dashboard-back-link"><?php echo file_get_contents(ESAF_IMAGES_PATH . '/back-arrow.svg'); ?></a>
        <?php if(!empty($logo_url)) : ?>
          <div class="esaf-pro-dashboard-header-logo">
            <img src="<?php echo esc_url($logo_url); ?>" alt="">
          </div>
        <?php else : ?>
          <h1><?php echo esc_html(Utils::blogname()); ?></h1>
        <?php endif; ?>
        <?php if($user instanceof User) : ?>
          <?php $avatar = get_avatar($user->ID, 38); ?>
          <div class="esaf-pro-dashboard-header-menu">
            <?php echo $avatar ? $avatar : ''; ?>
            <i class="ea-icon ea-icon-angle-down"></i>
            <div class="esaf-pro-dashboard-header-menu-drop">
              <div class="esaf-pro-dashboard-header-menu-content">
                <div class="esaf-pro-dashboard-user-box">
                  <?php echo $avatar ? $avatar : ''; ?>
                  <div class="esaf-pro-dashboard-user-info">
                    <div class="esaf-pro-dashboard-user-name">
                      <?php echo esc_html($user->name_or_email()); ?>
                    </div>
                    <div class="esaf-pro-dashboard-user-username-id">
                      (<?php echo esc_html($user->user_login); ?>) - ID: <?php echo esc_html($user->ID); ?>
                    </div>
                  </div>
                </div>
                <a href="<?php echo esc_url(Utils::logout_url()); ?>"><?php esc_html_e('Logout', 'easy-affiliate'); ?></a>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </header>
