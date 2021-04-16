<div class="uap-ap-wrap">

<?php if (!empty($data['title'])):?>
	<h3><?php echo $data['title'];?></h3>
<?php endif;?>
<?php if (!empty($data['message'])):?>
	<p><?php echo do_shortcode($data['message']);?></p>
<?php endif;?>

	<form method="post" action="" id="uap_campaign_form">

		<input type="hidden" name="uap_campaign_nonce" value="<?php echo wp_create_nonce( 'uap_campaign_nonce' );?>" />

        <div class="uap-profile-box-wrapper">
    	<div class="uap-profile-box-title"><span><?php _e('Add New Campaign', 'uap');?></span></div>
        <div class="uap-profile-box-content">
        	<div class="uap-row ">
            	<div class="uap-col-xs-8">
                <?php _e("Campaigns will help you to better promote your marketing strategy. Those are private and individual for each affiliate account.", 'uap');?>
                </div>
            </div>
        </div>
        <div class="uap-profile-box-content">
            <div class="uap-row ">
            	<div class="uap-col-xs-6">
                    <div class="uap-ap-field">
                        <div class="uap-account-title-label"><?php _e('Campaing unique Slug', 'uap');?></div>
                        <input type="text" name="campaign_name" value="" class="uap-public-form-control "/>
                        <div class="uap-account-notes"><?php echo __("Slug must be unique and based on only lowercase characters without extra symbols or spaces.", 'uap');?></div>
                    </div>
                    <div class="uap-ap-field">
                        <input type="submit" name="save" value="<?php _e('Add New', 'uap');?>" class="button button-primary button-large uap-js-submit-campaign" />
                    </div>
               </div>
            </div>
         </div>
         </div>

		<?php if (!empty($data['campaigns'])) : ?>
		<div class="uap-profile-box-wrapper">
    	<div class="uap-profile-box-title"><span><?php _e('Your own Campaigns', 'uap');?></span></div>
        <div class="uap-profile-box-content">
        	<div class="uap-row ">
            	<div class="uap-col-xs-8">
		<div class="uap-account-campaign-list-wrapper">
        	<div class="uap-account-campaign-list-header">
        		<div style="width:80%; display:inline-block;"><?php _e('Campaign slug', 'uap');?></div>
        		<div style="width:18%; display:inline-block; text-align:center;"><?php _e('Remove', 'uap');?></div>
            </div>
		<?php foreach ($data['campaigns'] as $campaignId => $value ) : ?>
			<div class="uap-account-campaign-list">
				<div style="width:80%; display:inline-block; color: #21759b; font-weight:bold;"><?php echo $value;?></div><div style="width:20%; display:inline-block; text-align:center;"><i class="fa-uap fa-trash-uap" onClick="jQuery('#uap_delete_campaign').val('<?php echo $campaignId;?>');jQuery('#uap_campaign_form').submit();"></i></div>
			</div>
		<?php endforeach;?>
		</div>
			<input type="hidden" value="" name="uap_delete_campaign" id="uap_delete_campaign"/>
               </div>
            </div>
         </div>
         </div>
		<?php endif;?>
		<br/>

	</form>
</div>

<script>
jQuery('.uap-js-submit-campaign').on( 'click', function(e){
		e.preventDefault();
		jQuery.ajax({
				type : "post",
				url : decodeURI(ajax_url),
				data : {
									 action						: "uap_ajax_save_campaign",
									 campaignName			: jQuery('[name=campaign_name]').val(),
							 },
				success: function (response) {
						location.reload();
				}
	 });
})
</script>
