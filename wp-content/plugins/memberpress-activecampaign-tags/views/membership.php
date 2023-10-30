<div id="mepr-activecampaign-tags" class="mepr-product-adv-item">
  <input type="checkbox" name="mepractivecampaign_add_tags" id="mepractivecampaign_add_tags" data-account="<?php echo $this->account(); ?>" data-apikey="<?php echo $this->apikey(); ?>" <?php checked($add_tags); ?> />
  <label for="mepractivecampaign_add_tags"><?php _e('ActiveCampaign tags for this Membership', 'memberpress-activecampaigntags'); ?></label>

  <?php MeprAppHelper::info_tooltip('mepractivecampaign-add-tags',
    __('Enable Membership ActiveCampaign Tags', 'memberpress-activecampaigntags'),
    __('If this is set and tags are added to this Membership then anyone who is active will have these tags applied. If the user becomes inactive on this membership then they will be removed from their contact record. Note: Changing the tag values here after some are applied to contact records in ActiveCampaign, will prevent them from being removed later from those records if they become inactive.', 'memberpress-activecampaigntags'));
  ?>

  <div id="mepractivecampaign_tags_area" class="mepr-hidden product-options-panel">
    <label><?php _e('ActiveCampaign Tags: ', 'memberpress-activecampaigntags'); ?></label>
    <input type="text" name="mepractivecampaign_tags" id="mepractivecampaign_tags" class="mepr-text-input form-field" size="75" value="<?php echo stripslashes($tags); ?>" />
  </div>
</div>

