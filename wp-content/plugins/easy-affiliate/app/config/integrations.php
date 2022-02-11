<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

return apply_filters(
  'esaf-config-integrations',
  array(
    'memberpress' => array(
      'label' => __('MemberPress', 'easy-affiliate'),
      'deprecated' => false,
      'detectable' => true,
      'controller' => '\\EasyAffiliate\\Controllers\\MemberPressCtrl',
    ),
    'woocommerce' => array(
      'label' => __('WooCommerce', 'easy-affiliate'),
      'deprecated' => false,
      'detectable' => true,
      'controller' => '\\EasyAffiliate\\Controllers\\WooCommerceCtrl',
      'config' => ESAF_VIEWS_PATH . '/options/woocommerce_config.php',
    ),
    'easy_digital_downloads' => array(
      'label' => __('Easy Digital Downloads', 'easy-affiliate'),
      'deprecated' => false,
      'detectable' => true,
      'controller' => '\\EasyAffiliate\\Controllers\\EasyDigitalDownloadsCtrl',
    ),
    'wpforms' => array(
      'label' => __('WPForms', 'easy-affiliate'),
      'deprecated' => false,
      'detectable' => true,
      'controller' => '\\EasyAffiliate\\Controllers\\WPFormsCtrl',
    ),
    'formidable' => array(
      'label' => __('Formidable', 'easy-affiliate'),
      'deprecated' => false,
      'detectable' => true,
      'controller' => '\\EasyAffiliate\\Controllers\\FormidableCtrl',
    ),
    'paypal' => array(
      'label' => __('PayPal', 'easy-affiliate'),
      'deprecated' => false,
      'config' => ESAF_VIEWS_PATH . '/options/paypal_config.php',
    ),
  )
);
