<?php

namespace EasyAffiliate\Helpers;

use EasyAffiliate\Models\Campaign;

class CampaignsHelper {
  public static function campaigns_dropdown($selected=false) {
    $all_campaigns = Campaign::get_all('tx.count DESC', '', array('tx.count > 0'));

    if(!empty($all_campaigns)):
      ?>
        <select name="campaign">
          <option value=""><?php esc_html_e('Any', 'easy-affiliate'); ?></option>
          <?php
            foreach($all_campaigns as $campaign):
              ?>
              <option value="<?php echo esc_attr($campaign->slug); ?>" <?php selected($campaign->slug, $selected); ?>><?php echo esc_html("{$campaign->name} ({$campaign->count})"); ?></option>
              <?php
            endforeach;
          ?>
        </select>
      <?php
    endif;
  }
}

