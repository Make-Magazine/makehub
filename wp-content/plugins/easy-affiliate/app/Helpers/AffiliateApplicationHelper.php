<?php

namespace EasyAffiliate\Helpers;

use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\AffiliateApplication;

class AffiliateApplicationHelper {
  public static function get_application_from_request() {
    if( isset($_REQUEST['application']) &&
        (!!($app = AffiliateApplication::get_one_by_uuid(sanitize_text_field($_REQUEST['application'])))) ) {
      return $app;
    }

    return false;
  }

  /**
   * Get the links to help admins audit a list of websites
   *
   * @param string $websites
   * @param bool $include_domain_stats
   * @return string
   */
  public static function get_website_audit_links_html($websites, $include_domain_stats = true) {
    if(empty($websites)) {
      return '';
    }

    $websites = array_map('trim', explode("\n", $websites));
    $filtered_websites = [];

    foreach($websites as $website) {
      if(!empty($website) && Utils::is_url($website)) {
        $filtered_websites[] = $website;
      }
    }

    if(!count($filtered_websites)) {
      return '';
    }

    $output = '<div class="esaf-application-websites">';

    foreach($filtered_websites as $filtered_website) {
      $output .= '<div class="esaf-application-website">';

      $output .= sprintf(
        '<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
        esc_url($filtered_website),
        esc_url($filtered_website)
      );

      if($include_domain_stats) {
        $output .= sprintf(
          ' | <a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
          esc_url('https://web.archive.org/web/*/' . $filtered_website),
          esc_html__('Internet Archive', 'easy-affiliate')
        );

        $host = parse_url($filtered_website, PHP_URL_HOST);

        if(!empty($host)) {
          $output .= sprintf(
            ' | <a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
            esc_url('https://www.alexa.com/siteinfo/' . $host),
            esc_html__('Alexa', 'easy-affiliate')
          );
        }
      }

      $output .= '</div>';
    }

    return $output;
  }
}
