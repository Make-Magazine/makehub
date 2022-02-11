<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Lib\BaseCtrl,
    EasyAffiliate\Models\Creative,
    EasyAffiliate\Models\Options;

/** Controller for dealing with Google Analytics UTM codes. */
class UtmCtrl extends BaseCtrl {
  public function load_hooks() {
    $options = Options::fetch();

    // Only filter if the utm_affiliate_links setting is checked
    if($options->utm_affiliate_links) {
      add_filter('esaf_affiliate_target_url', [$this, 'maybe_add_params'], 10, 3);
    }
  }

  /**
   * Maybe add UTM parameters to the target URL
   *
   * @param  string          $target_url   The original target_url
   * @param  integer         $affiliate_id The affiliate's ID
   * @param  integer|boolean $creative_id  The Creative ID OR false if not present
   * @return string
   */
  public function maybe_add_params($target_url, $affiliate_id, $creative_id) {
    $affiliate = new \WP_User($affiliate_id);

    // Set initial utms
    $utm = [
      'utm_source' => 'aff-' . sanitize_title(preg_replace('/@/','-',$affiliate->user_login)),
      'utm_medium' => 'aff-link'
    ];

    // Add creative-specific utms
    if(false !== $creative_id) {
      $creative = new Creative($creative_id);
      $utm['utm_content'] = $creative->post_title;

      $campaigns = $creative->campaigns;
      if(!empty($campaigns)) {
        $utm['utm_campaign'] = $campaigns[0]->slug;
      }
    }

    // Don't override whatever UTMs the user has set in the URL itself
    if(preg_match('/\?/', $target_url)) {
      $url_query = [];
      parse_str($_SERVER['QUERY_STRING'], $url_query);

      foreach($url_query as $arg => $v) {
        if(preg_match('/^utm_(source|medium|campaign|content)$/',$arg)) {
          unset($utm[$arg]);
        }
      }

      // If our utm array has been emptied out then no need for a separator
      $separator = empty($utm) ? '' : '&';
    }
    else {
      $separator = '?';
    }

    $utm = apply_filters('esaf_utm_params', $utm, $affiliate_id, $creative_id, $target_url);

    return $target_url . $separator . http_build_query($utm);
  }
}
