<div class="uap-show-link-popup-wrapp">
    <div class="uap-the-popup">
        <div class="uap-popup-top">
						<div class="title"><?php _e('Product Link', 'uap');?></div>
						<div class="close-bttn uap-js-close-product-link-popup"></div>
						<div class="clear"></div>
        </div>
        <div class="uap-popup-content">
            <?php if (!empty($data['friendly_links'])):?>
              <div class="uap-ap-field">
                <label class="uap-ap-label uap-special-label"><?php _e("Friendly Links:", 'uap');?> </label>
                <select id="uap_show_link_friendly_link" class="uap-public-form-control ">
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
                <select id="uap_show_link_ref_type" class="uap-public-form-control ">
                  <option value="0"><?php echo $data['ref_type'];?></option>
                  <option value="1"><?php _e('Custom Affiliate Slug', 'uap');?></option>
                </select>
              </div>

            <?php endif;?>

            <div class="uap-ap-field uap-account-affiliatelinks-tab2">
              <div class="uap-ap-label"><?php _e("Your Affiliate Link", 'uap');?> </div>
              <span><?php _e('Copy the generated link and paste it into your website', 'uap');?></span>
              <textarea readonly class="uap-account-url uap-js-show-links-the-link" onclick="this.select()" onfocus="this.select()"><?php
              echo $data['url'];?></textarea>
            </div>

						<div id="uap_show_link_affiliate_id" data-affiliate_id="<?php echo $data['affiliate_id'];?>"></div>

        </div>
    </div>
</div>
<?php
