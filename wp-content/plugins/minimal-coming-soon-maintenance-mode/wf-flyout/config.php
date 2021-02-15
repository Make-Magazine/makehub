<?php
$config = array();

$config['plugin_screen'] = 'settings_page_maintenance_mode_options';
$config['icon_border'] = '1px solid #00000099';
$config['icon_right'] = '25px';
$config['icon_bottom'] = '75px';
$config['icon_image'] = 'csmm.png';
$config['icon_padding'] = '4px';
$config['icon_size'] = '55px';
$config['menu_accent_color'] = '#fe2929';
$config['custom_css'] = '#wf-flyout .wff-menu-item .dashicons.dashicons-universal-access { font-size: 30px; padding: 0px 10px 0px 0; } #wf-flyout .csmm-icon .wff-icon img { max-width: 70%; } #wf-flyout .csmm-icon .wff-icon { line-height: 57px; }';

$config['menu_items'] = array(
  array('href' => 'https://comingsoonwp.com/?ref=wff-csmm&coupon=welcome', 'target' => '_blank', 'label' => 'Get Coming Soon PRO with 25% off', 'icon' => 'csmm.png', 'class' => 'csmm-icon accent'),
  array('href' => 'https://wpreset.com/?ref=wff-csmm', 'target' => '_blank', 'label' => 'Get WP Reset PRO with 50% off', 'icon' => 'wp-reset.png'),
  array('href' => '#', 'target' => '_blank', 'class' => 'open-accessibe-upsell', 'label' => 'Make your site accessible to people with disabilities', 'icon' => 'dashicons-universal-access'),
  array('href' => 'https://wpsticky.com/?ref=wff-csmm', 'target' => '_blank', 'label' => 'Make a menu sticky with WP Sticky', 'icon' => 'dashicons-admin-post'),
  array('href' => 'https://wordpress.org/support/plugin/minimal-coming-soon-maintenance-mode/reviews/?filter=5#new-post', 'target' => '_blank', 'label' => 'Rate the Plugin', 'icon' => 'dashicons-thumbs-up'),
  array('href' => 'https://wordpress.org/support/plugin/minimal-coming-soon-maintenance-mode/#new-post', 'target' => '_blank', 'label' => 'Get Support', 'icon' => 'dashicons-sos'),
);
