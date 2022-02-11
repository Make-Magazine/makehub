<?php

namespace EasyAffiliate\Helpers;

use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Creative;
use EasyAffiliate\Models\CustomLink;
use EasyAffiliate\Models\Options;
use EasyAffiliate\Models\User;

class CreativesHelper {
  public static function link_types_dropdown($selected=false) {
    ?>
    <select name="link_type">
      <option value=""><?php esc_html_e('Any', 'easy-affiliate'); ?></option>
      <?php
        $obj = new Creative();
        foreach($obj->link_types as $link_type):
          ?>
          <option value="<?php echo esc_attr($link_type); ?>" <?php selected($selected, $link_type); ?>><?php echo esc_html(CreativesHelper::readable_link_type($link_type)); ?></option>
          <?php
        endforeach;
      ?>
    </select>
    <?php
  }

  public static function readable_link_type($link_type) {
    switch($link_type) {
      case 'banner':
        return __('Banner', 'easy-affiliate');
      case 'text':
        return __('Text Link', 'easy-affiliate');
    }
  }

  public static function dashboard_active_view($view = 'text-links') {
    $active = isset($_REQUEST['view']) && is_string($_REQUEST['view']) ? sanitize_text_field(wp_unslash($_REQUEST['view'])) : 'text-links';

    if($view == $active) {
      return ' class="esaf-creatives-nav-active"';
    }

    return '';
  }

  public static function dashboard_my_affiliate_link($default_affiliate_url, $show_close_button = false) {
    ?>
    <div id="esaf-dashboard-my-affiliate-link">
      <div class="esaf-dashboard-my-affiliate-link-label"><?php esc_html_e('My Affiliate Link:', 'easy-affiliate'); ?></div>
      <div class="esaf-dashboard-my-affiliate-link-input">
        <form class="esaf-form">
          <input type="text" id="esaf-dashboard-my-affiliate-link-field" readonly="readonly" value="<?php echo esc_attr($default_affiliate_url); ?>" />
        </form>
        <span class="esaf-copy-clipboard" data-clipboard-target="#esaf-dashboard-my-affiliate-link-field"><i class="ea-icon ea-icon-docs"></i></span>
      </div>
      <?php if ($show_close_button) : ?>
        <i class="ea-icon ea-icon-cancel"></i>
      <?php endif; ?>
    </div>
    <?php
  }

  public static function show_showcase_url($showcase_url) {
    $options = Options::fetch();
    ?>
    <div id="esaf-dashboard-showcase-url">
      <?php if ( ! empty( $options->showcase_url_title ) ) : ?>
       <div class="esaf-dashboard-my-affiliate-link-label"><?php esc_html_e($options->showcase_url_title, 'easy-affiliate'); ?></div>
     <?php endif; ?>
      <div class="esaf-dashboard-my-affiliate-link-input">
        <form class="esaf-form">
          <input type="text" id="esaf-dashboard-showcase-url-field" readonly="readonly" value="<?php echo esc_attr($showcase_url); ?>" />
        </form>
        <span class="esaf-copy-clipboard" data-clipboard-target="#esaf-dashboard-showcase-url-field"><i class="ea-icon ea-icon-docs"></i></span>
      </div>
    </div>
    <?php
  }

  public static function dashboard_sub_nav() {
    global $current_user;
    Utils::get_currentuserinfo();
    $affiliate_id = $current_user->ID;
    $text_links_link = Utils::dashboard_url(['action' => 'creatives', 'view' => 'text-links']);
    $banners_link = Utils::dashboard_url(['action' => 'creatives', 'view' => 'banners']);
    $coupons_link = Utils::dashboard_url(['action' => 'creatives', 'view' => 'coupons']);
    $custom_links_link = Utils::dashboard_url(['action' => 'creatives', 'view' => 'custom-links']);

    $text_links_count = Creative::get_all_visible('text', false, true);
    $banners_count = Creative::get_all_visible('banner', false, true);
    $coupons_count = apply_filters('esaf_dashboard_coupon_count', 0);
    $custom_links_count = CustomLink::get_count(['affiliate_id' => $affiliate_id]);

    $text_links_label = sprintf(__('Text Links (%s)', 'easy-affiliate'), number_format_i18n($text_links_count));
    $banners_label = sprintf(__('Banners (%s)', 'easy-affiliate'), number_format_i18n($banners_count));
    $coupons_label = sprintf(__('Coupons (%s)', 'easy-affiliate'), number_format_i18n($coupons_count));
    $custom_links_label = sprintf(__('Custom Links (%s)', 'easy-affiliate'), number_format_i18n($custom_links_count));
    ?>
    <ul class="esaf-creatives-nav">
      <li<?php echo self::dashboard_active_view(); ?>><a href="<?php echo esc_url($text_links_link); ?>"><?php echo esc_html($text_links_label); ?></a></li>
      <li<?php echo self::dashboard_active_view('banners'); ?>><a href="<?php echo esc_url($banners_link); ?>"><?php echo esc_html($banners_label); ?></a></li>
      <?php if($coupons_count > 0) : ?>
        <li<?php echo self::dashboard_active_view('coupons'); ?>><a href="<?php echo esc_url($coupons_link); ?>"><?php echo esc_html($coupons_label); ?></a></li>
      <?php endif; ?>
      <li<?php echo self::dashboard_active_view('custom-links'); ?>><a href="<?php echo esc_url($custom_links_link); ?>"><?php echo esc_html($custom_links_label); ?></a></li>
      <?php do_action('esaf-affiliate-dashboard-creatives-nav'); ?>
    </ul>
    <?php
  }

  public static function text_link_row($text_link, $affiliate_id) {
    ob_start();
    ?>
    <tr>
      <td class="esaf-td-id"><?php echo esc_html($text_link->ID); ?></td>
      <td class="esaf-td-description"><?php echo esc_html($text_link->post_title); ?></td>
      <td class="esaf-td-example"><?php echo $text_link->link_code($affiliate_id, '_blank'); ?></td>
      <td class="esaf-td-modified"><?php echo esc_html(get_post_modified_time(Utils::get_date_format(), false, $text_link->ID, true)); ?></td>
      <td class="esaf-td-actions"><a href="#" class="esaf-text-link-get-html-code" data-html-code="<?php echo esc_attr($text_link->link_code($affiliate_id)); ?>" data-url-only="<?php echo esc_attr($text_link->display_url($affiliate_id)); ?>"><?php esc_html_e('Get Link', 'easy-affiliate'); ?></a></td>
    </tr>
    <?php

    return ob_get_clean();
  }

  public static function banner_grid_item(Creative $creative, $affiliate_id) {
    ob_start();
    ?>
    <div class="esaf-col-xs-6 esaf-col-md-3 esaf-creatives-banner">
      <img class="esaf-banner-link-get-html-code" src="<?php echo esc_url($creative->image); ?>"
           data-html-code="<?php echo esc_attr($creative->link_code($affiliate_id)); ?>"
           data-url-only="<?php echo esc_attr($creative->display_url($affiliate_id)); ?>"
           data-banner-height="<?php echo esc_attr($creative->image_height); ?>"
           data-banner-width="<?php echo esc_attr($creative->image_width); ?>"
           data-banner-id="<?php echo esc_attr($creative->ID . ' - ' . $creative->post_title); ?>"
           alt=""
      />
    </div>
    <?php

    return ob_get_clean();
  }

  public static function custom_link_row(CustomLink $custom_link, User $user) {
    $username = is_email($user->user_login) ? $user->ID : $user->user_login;
    $affiliate_link = add_query_arg('aff', urlencode($username), $custom_link->destination_link);

    if(function_exists('prli_get_pretty_link_url') && !empty($custom_link->pretty_link_id)) {
      $pretty_link_url = prli_get_pretty_link_url($custom_link->pretty_link_id);

      if(!empty($pretty_link_url)) {
        $affiliate_link = $pretty_link_url;
      }
    }

    ob_start();
    ?>
    <tr
      id="esaf-dashboard-custom-link-row-id-<?php echo esc_attr($custom_link->id); ?>"
      data-custom-link-id="<?php echo esc_attr($custom_link->id); ?>"
      data-destination-link="<?php echo esc_attr($custom_link->destination_link); ?>"
    >
      <td>
        <a href="<?php echo esc_url($affiliate_link); ?>" id="esaf-dashboard-custom-link-url-<?php echo esc_html($custom_link->id); ?>"><?php echo esc_url($affiliate_link); ?></a>
        <span class="esaf-copy-clipboard" data-clipboard-target="#esaf-dashboard-custom-link-url-<?php echo esc_html($custom_link->id); ?>"><i class="ea-icon ea-icon-docs"></i></span>
      </td>
      <td class="esaf-dashboard-custom-links-td-destination-url"><a href="<?php echo esc_url($custom_link->destination_link); ?>"><?php echo esc_url($custom_link->destination_link); ?></a></td>
      <td><?php echo esc_html(Utils::format_date($custom_link->created_at)); ?><i class="ea-icon ea-icon-pencil esaf-dashboard-custom-link-edit"></i></td>
    </tr>
    <?php

    return ob_get_clean();
  }
}
