<?php

namespace EasyAffiliate\Lib;

use EasyAffiliate\Controllers\UpdateCtrl;
use EasyAffiliate\Models\Options;

class AddonUpdates {
  public $slug, $main_file, $installed_version, $name, $description;

  public function __construct($slug, $main_file, $installed_version, $name, $description) {
    $this->slug = $slug;
    $this->main_file = $main_file;
    $this->installed_version = $installed_version;
    $this->name = $name;
    $this->description = $description;

    add_filter('pre_set_site_transient_update_plugins', [$this, 'queue_update']);
    add_filter('plugins_api', [$this, 'plugin_info'], 11, 3);
  }

  public function queue_update($transient) {
    if(empty($transient->checked)) {
      return $transient;
    }

    $update_info = get_site_transient('esaf_update_info_' . $this->slug);

    if(!is_array($update_info)) {
      $options = Options::fetch();
      $args = [];

      if($options->edge_updates || (defined('EASY_AFFILIATE_EDGE') && EASY_AFFILIATE_EDGE)) {
        $args['edge'] = 'true';
      }

      if(empty($options->mothership_license)) {
        // Just here to query for the current version
        try {
          $version_info = UpdateCtrl::send_mothership_request("/versions/latest/{$this->slug}", $args);
          $curr_version = $version_info['version'];
          $download_url = '';
        }
        catch(\Exception $e) {
          if(isset($transient->response[$this->main_file])) {
            unset($transient->response[$this->main_file]);
          }

          return $transient;
        }
      }
      else {
        try {
          $args['domain'] = urlencode(Utils::site_domain());

          $license_info = UpdateCtrl::send_mothership_request("/versions/info/{$this->slug}/{$options->mothership_license}", $args);
          $curr_version = $license_info['version'];
          $download_url = $license_info['url'];
        }
        catch(\Exception $e) {
          try {
            // Just here to query for the current version
            $version_info = UpdateCtrl::send_mothership_request("/versions/latest/{$this->slug}", $args);
            $curr_version = $version_info['version'];
            $download_url = '';
          } catch (\Exception $e) {
            if(isset($transient->response[$this->main_file])) {
              unset($transient->response[$this->main_file]);
            }

            return $transient;
          }
        }
      }

      set_site_transient(
        'esaf_update_info_' . $this->slug,
        compact('curr_version', 'download_url'),
        Utils::hours(12)
      );
    }
    else {
      $curr_version = isset($update_info['curr_version']) ? $update_info['curr_version'] : $this->installed_version;
      $download_url = isset($update_info['download_url']) ? $update_info['download_url'] : '';
    }

    if(isset($curr_version) && version_compare($curr_version, $this->installed_version, '>')) {
      global $wp_version;

      $transient->response[$this->main_file] = (object) [
        'id' => $this->main_file,
        'slug' => $this->slug,
        'plugin' => $this->main_file,
        'new_version' => $curr_version,
        'url' => 'https://easyaffiliate.com/',
        'package' => $download_url,
        'tested' => $wp_version
      ];
    }
    else {
      unset($transient->response[$this->main_file]);

      // Enables the "Enable auto-updates" link
      $transient->no_update[$this->main_file] = (object) [
        'id' => $this->main_file,
        'slug' => $this->slug,
        'plugin' => $this->main_file,
        'new_version' => $this->installed_version,
        'url' => 'https://easyaffiliate.com/',
        'package' => ''
      ];
    }

    return $transient;
  }

  public function plugin_info($api, $action, $args) {
    global $wp_version;

    if(!isset($action) || $action != 'plugin_information') {
      return $api;
    }

    if(!isset($args->slug) || $args->slug != $this->slug) {
      return $api;
    }

    $options = Options::fetch();
    $args = [];

    if($options->edge_updates || (defined('EASY_AFFILIATE_EDGE') && EASY_AFFILIATE_EDGE)) {
      $args['edge'] = 'true';
    }

    if(empty($options->mothership_license)) {
      try {
        // Just here to query for the current version
        $version_info = UpdateCtrl::send_mothership_request("/versions/latest/{$this->slug}", $args);
        $curr_version = $version_info['version'];
        $version_date = $version_info['version_date'];
        $download_url = '';
      }
      catch(\Exception $e) {
        return $api;
      }
    }
    else {
      try {
        $args['domain'] = urlencode(Utils::site_domain());

        $license_info = UpdateCtrl::send_mothership_request("/versions/info/{$this->slug}/{$options->mothership_license}", $args);
        $curr_version = $license_info['version'];
        $version_date = $license_info['version_date'];
        $download_url = $license_info['url'];
      }
      catch(\Exception $e) {
        try {
          // Just here to query for the current version
          $version_info = UpdateCtrl::send_mothership_request("/versions/latest/{$this->slug}", $args);
          $curr_version = $version_info['version'];
          $version_date = $version_info['version_date'];
          $download_url = '';
        }
        catch(\Exception $e) {
          return $api;
        }
      }
    }

    return (object) [
      'slug' => $this->slug,
      'name' => esc_html($this->name),
      'author' => ESAF_AUTHOR,
      'author_profile' => ESAF_AUTHOR_URI,
      'contributors' => [
        'caseproof' => [
          'profile' => ESAF_AUTHOR_URI,
          'avatar' => 'https://secure.gravatar.com/avatar/762b61e36276ff6dc0d7b03b8c19cfab?s=96&d=monsterid&r=g',
          'display_name' => ESAF_AUTHOR_NAME
        ]
      ],
      'homepage' => 'https://easyaffiliate.com/',
      'version' => $curr_version,
      'new_version' => $curr_version,
      'requires' => '5.2',
      'requires_php' => ESAF_MINIMUM_PHP_VERSION,
      'tested' => $wp_version,
      'compatibility' => [$wp_version => [$curr_version => [100, 0, 0]]],
      'rating' => '100.00',
      'num_ratings' => '1',
      'added' => '2012-12-02',
      'last_updated' => $version_date,
      'tags' => [
        'affiliate' => 'affiliate',
        'affiliate program' => 'affiliate program',
      ],
      'sections' => [
        'description' => '<p>' . $this->description . '</p>',
        'faq' => '<p>' . sprintf(esc_html__('You can access in-depth information about Easy Affiliate at %1$sthe Easy Affiliate User Manual%2$s.', 'easy-affiliate'), '<a href="https://easyaffiliate.com/docs/">', '</a>') . '</p>',
        'changelog' => '<p>' . sprintf(esc_html__('See the %1$sEasy Affiliate changelog%2$s.', 'easy-affiliate'), '<a href="https://easyaffiliate.com/change-log/">', '</a>') . '</p>'
      ],
      'download_link' => $download_url
    ];
  }
}
