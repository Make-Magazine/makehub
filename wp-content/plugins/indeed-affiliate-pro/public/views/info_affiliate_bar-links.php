

<?php if (!empty($data['friendly_links'])):?>
<div class="uap-ap-field">
  <label class="uap-ap-label uap-special-label"><?php _e("Friendly Links:", 'uap');?> </label>
  <select id="uap_affiliate_bar_friendly_links" onChange="uapInfoAffiliateBarUpdateLink();" class="uap-public-form-control ">
    <option value="0"><?php _e('Off', 'uap');?></option>
    <option value="1"><?php _e('On', 'uap');?></option>
  </select>
</div>
<?php endif;?>

<?php if (!empty($data['custom_affiliate_slug']) && !empty($data['the_slug'])):?>
  <?php
    $ref_type = ($data['uap_default_ref_format']=='username') ? __('Username', 'uap') : 'Id';
  ?>
<div class="uap-ap-field">
  <label class="uap-ap-label uap-special-label"><?php _e("Referrence Type:", 'uap');?> </label>
  <select id="ap_affiliate_bar_ref_type" onChange="uapInfoAffiliateBarUpdateLink();" class="uap-public-form-control ">
    <option value="0"><?php echo $data['ref_type'];?></option>
    <option value="1"><?php _e('Custom Affiliate Slug', 'uap');?></option>
  </select>
</div>
<div id="uap_info_affiliate_bar_extra_info" data-affiliate_id="<?php echo $data['affiliate_id'];?>"></div>

<?php endif;?>

<div class="uap-ap-field uap-account-affiliatelinks-tab2">
  <div class="uap-ap-label"><?php _e("Your Affiliate Link", 'uap');?> </div>
  <span><?php _e('Copy the generated link and paste it into your website', 'uap');?></span>
<textarea readonly class="uap-account-url uap-js-iab-affiliate-link" onclick="this.select()" onfocus="this.select()">
 <?php echo $data['url'];?>
</textarea>


</div>
