<div class="uap_info_bar_banner_wrapp"><?php echo $data['banner'];?></div>
<div class="uap-info-bar-banner-size">
  <div class="uap-ap-label uap-special-label">
    <?php echo __('Banner Size', 'uap'); ?>
  </div>
  <select onChange="uapInfoAffiliateBarChangeBannerSize(this.value);" class="uap-public-form-control ">
      <option value="thumbnail"><?php echo __('Small', 'uap'); ?></option>
      <option value="medium"><?php echo __('Medium', 'uap'); ?></option>
      <option value="large"><?php echo __('Large', 'uap'); ?></option>
  </select>
</div>
<textarea id="uap_info_bar_banner_the_value" readonly class="uap-account-url" onclick="this.select()" onfocus="this.select()"><?php echo $data['banner'];?></textarea>
