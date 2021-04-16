<?php
$i = 0;
if ( $data['settings']['uap_info_affiliate_bar_logo'] ) $i++;
if ( $data['settings']['uap_info_affiliate_bar_links_section_enabled'] ) $i++;
if ( $data['settings']['uap_info_affiliate_bar_banner_section_enabled'] && $data['banner'] ) $i++;
if ( $data['settings']['uap_info_affiliate_bar_social_section_enabled'] ) $i++;
if ( $data['settings']['uap_info_affiliate_bar_stats_general_section_enabled']  ||  $data['settings']['uap_info_affiliate_bar_stats_personal_section_enabled'] ) $i++;
if ( $data['settings']['uap_info_affiliate_bar_menu_section_enabled'] ) $i++;
$width = 99 / $i;
?>
<style>
.uap-info-affiliate-bar-item {
    width: calc(<?php echo $width;?>%-1px);
}
</style>
<div id="uap_info_affiliate_bar" class="uap-info-affiliate-bar-wrapper">
        <div class="uap-info-affiliate-bar-item uap-info-affiliate-bar-logo">
        <?php if ( $data['settings']['uap_info_affiliate_bar_logo'] ):?>
       	 	<img src="<?php echo $data['settings']['uap_info_affiliate_bar_logo'];?>" class="uap-iab-logo" />

    	<?php endif;?>
    	</div>

		<div class="uap-info-affiliate-bar-item uap-info-affiliate-bar-links">
        	<div class="uap-info-affiliate-bar-extralabel">
				<?php echo stripslashes($data['settings']['uap_info_affiliate_bar_links_get_label']); ?>
        	</div>
            <ul class="uap-info-affiliate-bar-getlinks">
    <?php if ( $data['settings']['uap_info_affiliate_bar_links_section_enabled'] ): ?>
            <li class="uap-info-affiliate-bar-btn uap-info-affiliate-bar-btn-default" id="uap_js_info_affiliate_bar_links_trigger"><i class="fa-uap fa-info_affiliate_bar-link"></i><span class="uap-info-affiliate-sublabel"><?php echo stripslashes($data['settings']['uap_info_affiliate_bar_links_label']);?></span></li>
        <script>
        jQuery( document ).ready( function(){
            jQuery( '#uap_js_info_affiliate_bar_links_trigger' ).popover({
                content       : <?php echo json_encode( $data['links_section'] );?>,
                interface     : 'popover',
                position      : 'bottom-left',
                trigger       : 'click',
                trigger_off   : 'click',
				        theme		      : 'uap-links-section',
				        title 		    : '<?php echo __('Simple Affiliate Link', 'uap'); ?>',
            })
        })
        </script>
    <?php endif;?>

    <?php //if ( $data['settings']['uap_info_affiliate_bar_banner_section_enabled'] && $data['banner'] ): ?>
    <?php if ( $data['settings']['uap_info_affiliate_bar_banner_section_enabled'] && $data['banner'] ): ?>
            <li class="uap-info-affiliate-bar-btn uap-info-affiliate-bar-btn-default" id="uap_info_affiliate_bar_banner_section"><i class="fa-uap fa-info_affiliate_bar-banner"></i><span class="uap-info-affiliate-sublabel"><?php echo stripslashes($data['settings']['uap_info_affiliate_bar_banner_label']);?></span></li>
            <div id="uap_info_affiliate_bar_banner_extra_info" data-affiliate_id="<?php echo $data['affiliate_id'];?>" data-uid="<?php echo $data['uid'];?>" ></div>
            <script>
            jQuery( document ).ready( function(){
                jQuery( '#uap_info_affiliate_bar_banner_section' ).popover({
                    content       : <?php echo json_encode($data['bannerSection']);?>,
                    interface     : 'popover',
                    position      : 'bottom-left',
                    trigger       : 'click',
                    trigger_off   : 'click',
					          theme		      : 'uap-banners-section',
					          title 		    : '<?php echo __('Banner Affiliate Link', 'uap'); ?>',
                })
            })
            </script>

    <?php endif;?>
    </ul>
    <div class="uap-clear"></div>
  </div>
    <?php if ( $data['settings']['uap_info_affiliate_bar_social_section_enabled'] ): ?>
        <div class="uap-info-affiliate-bar-item uap-info-affiliate-bar-social">
        	<span class="uap-info-affiliate-bar-extralabel uap-info-affiliate-bar-full-line">
				<?php echo stripslashes($data['settings']['uap_info_affiliate_bar_social_label']); ?>
        	</span>
            <?php echo $data['socialLinks'];?>
        </div>
    <?php endif;?>

    <div class="uap-info-affiliate-bar-item uap-info-affiliate-bar-menu">
    <?php if ( $data['settings']['uap_info_affiliate_bar_menu_section_enabled'] ):?>
          <div class="uap-info-affiliate-bar-btn uap-info-affiliate-bar-btn-default"
                      id="popover_for_iab_menu"
                      data-interface = "popover"
                      data-position = "bottom-right"
                      data-title = "<?php echo stripslashes($data['settings']['uap_info_affiliate_bar_menu_label']);?>"
                      data-theme="uap-menu-section"
                      data-content = "
                          <ul>
                              <li><a href='<?php echo $data['profile_permalink'];?>' target='_blank'><?php echo __('Your Profile', 'uap'); ?></a></li>
                              <li><a href='<?php echo $data['settings_permalink'];?>' target='_blank'><?php echo __('Extra Settings', 'uap'); ?></a></li>
                              <li><a href='<?php echo $data['tips_permalink'];?>'  target='_blank'><?php echo __('Learn more', 'uap'); ?></a></li>

                          </ul>
                          <div class='uap-pointer uap-affiliate-bar-temporary' onClick='uapDoHideInfoAffiliateBar( this );' ><?php echo __('Temporary Hide FlashBar', 'uap'); ?></div>
                          <div class='uap-affiliate-bar-info'><?php  _e('FlashBar will be disabled for 24hrs', 'uap'); ?></div>
                      "
                      data-shift = "-2"
                      data-defineClass = "bord"
                      data-trigger="click"
                      data-triggeroff="click"
          ><i class="fa-uap fa-info_affiliate_bar-menu"></i></div>
    <?php endif;?>
	 </div>


    <?php if ( $data['settings']['uap_info_affiliate_bar_stats_general_section_enabled']  ||  $data['settings']['uap_info_affiliate_bar_stats_personal_section_enabled'] ):?>
        <div class="uap-info-affiliate-bar-item uap-info-affiliate-bar-stats">
        	<span class="uap-info-affiliate-bar-extralabel uap-info-affiliate-bar-full-line">
				<?php echo stripslashes($data['settings']['uap_info_affiliate_bar_stats_label']);?>
        	</span>
            <?php if ( $data['settings']['uap_info_affiliate_bar_stats_general_section_enabled'] ):?>
            <div class="uap-info-affiliate-bar-stats-content">
            	<div class="uap-info-affiliate-bar-extralabel">
					<?php echo stripslashes($data['settings']['uap_info_affiliate_bar_insigts_label']); ?>
        		</div>
                <ul class="uap-info-affiliate-bar-stats-list">
                	<li><?php echo $data['personalVisits'];?> <?php echo stripslashes($data['settings']['uap_info_affiliate_bar_visits_label']); ?></li>
                    <li><?php echo $data['personalReferrals'];?> <?php echo stripslashes($data['settings']['uap_info_affiliate_bar_referrals_label']); ?></li>
                </ul>
    			<div class="uap-clear"></div>
            </div>
            <?php endif;?>
            <?php if ( $data['settings']['uap_info_affiliate_bar_stats_personal_section_enabled'] ):?>
            <div class="uap-info-affiliate-bar-stats-content">
            	<div class="uap-info-affiliate-bar-extralabel">
					<?php echo stripslashes($data['settings']['uap_info_affiliate_bar_overall_performance_label']); ?>
        		</div>
                <?php
                    if ( $data['generalReferrals'] == 0 || $data['generalVisits'] == 0 ){
                        $conversion = 0;
                    } else {
                        $conversion = $data['generalReferrals'] * 100 / $data['generalVisits'];
                    }
                ?>
                <ul class="uap-info-affiliate-bar-stats-list">
                	<li><?php echo $conversion.'%';?> <?php echo stripslashes($data['settings']['uap_info_affiliate_bar_conversion_rate_label']); ?></li>
                </ul>
    			<div class="uap-clear"></div>
            </div>
            <?php endif;?>
        </div>
    <?php endif;?>


    <div class="uap-clear"></div>
</div>
