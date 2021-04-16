<?php
$responseNumber = isset($_GET['response']) ? $_GET['response'] : false;
if ( !empty($_GET['token'] ) && $responseNumber == 1 ){
		$ElCheck = new \Indeed\Uap\ElCheck();
		$responseNumber = $ElCheck->responseFromGet();
}
if ( $responseNumber !== false ){
		$ElCheck = new \Indeed\Uap\ElCheck();
		$responseMessage = $ElCheck->responseCodeToMessage( $responseNumber, 'uap-danger-box', 'uap-success-box', 'uap' );
}
$license = get_option('uap_license_set');
?>
<div class="uap-wrapper">
<div class="uap-page-title">Ultimate Affiliate Pro - <span class="second-text"><?php _e('Help', 'uap');?></span></div>

<div class="uap-stuffbox">
	<h3 class="uap-h3">
		<?php _e('Activate Ultimate Affiliate Pro', 'uap');?>
	</h3>

	<form method="post" action="">
		<div class="inside">
			<?php if ($disabled):?>
				<div class="uap-form-line uap-no-border" style="font-weight: bold; color: red;"><?php _e("cURL is disabled. You need to enable if for further activation request.", 'uap');?></div>
			<?php endif;?>
			<div class="uap-form-line uap-no-border" style="width:10%; float:left; box-sizing:border-box; text-align:right; font-weight:bold;">
				<label for="tag-name" class="uap-labels" style="padding-right:0px; line-height:30px;"><?php _e('Your Purchase Code:', 'uap');?></label>
			</div>
			<div class="uap-form-line uap-no-border" style="width:70%; float:left; box-sizing:border-box;">
				<input name="uap_envato_code" type="password" value="<?php echo $data['uap_envato_code'];?>" style="width:100%;"/>
			</div>
			<div class="uap-stuffbox-submit-wrap uap-license-button" style="width:20%; float:right; box-sizing:border-box;">
				<?php if ( $license ):?>
                	<div class="uap-revoke-license uap-js-revoke-license"><?php _e( 'Revoke License', 'uap' );?></div>
                <?php else: ?>
                	<input type="submit" value="<?php _e('Activate License', 'uap');?>" name="uap_save_licensing_code" <?php echo $disabled;?> class="button button-primary button-large" style="width:150px;" />
                <?php endif;?>
			</div>

			<div class="uap-clear"></div>
			<div class="uap-license-status">
        	<?php
						if ( $responseNumber !== false ){
								echo $responseMessage;
						} else if ( !empty( $_GET['revoke'] ) ){
								?>
								<div class="uap-success-box"><?php _e( 'You have just revoke your license for Ultimate Affiliate Pro plugin.', 'uap' );?></div>
								<?php
						} else if ( $license ){ ?>
									<div class="uap-success-box"><?php _e( 'Your license for Ultimate Affiliate Pro is currently Active.', 'uap' );?></div>
          <?php } ?>
      </div>

					<div class="uap-license-status">
								<?php
						if ( isset($_GET['extraCode']) && isset( $_GET['extraMess'] ) && $_GET['extraMess'] != '' ){
								$_GET['extraMess'] = stripslashes($_GET['extraMess']);
								if ( $_GET['extraCode'] > 0 ){
										// success
										?>
										<div class="uap-success-box"><?php echo urldecode( $_GET['extraMess'] );?></div>
										<?php
								} else if ( $_GET['extraCode'] < 0 ){
										// errors
										?>
										<div class="uap-danger-box"><?php echo urldecode( $_GET['extraMess'] );?></div>
										<?php
								} else if ( $_GET['extraCode'] == 0 ){
										// warning
										?>
										<div class="uap-warning-box"><?php echo urldecode( $_GET['extraMess'] );?></div>
										<?php
								}
						}
					?>
					</div>

      </div>

			<div style="padding:0 60px;">
				<p><?php _e('A valid purchase code Activate the Full Version of', 'uap');?><strong> Ultimate Affiliate Pro</strong> <?php _e('plugin and provides access on support system. A purchase code can only be used for ', 'uap');?><strong><?php _e('ONE', 'uap');?></strong> Ultimate Affiliate Pro <?php _e('for WordPress installation on', 'uap');?> <strong><?php _e('ONE', 'uap');?></strong> <?php _e('WordPress site at a time. If you previosly activated your purchase code on another website, then you have to get a', 'uap');?> <a href="https://codecanyon.net/item/ultimate-affiliate-pro-wordpress-plugin/16527729?ref=azzaroco" target="_blank"><?php _e('new Licence', 'uap');?></a>.</p>
				<h4><?php _e('Where can I find my Purchase Code?', 'uap');?></h4>
				<a href="https://codecanyon.net/item/ultimate-affiliate-pro-wordpress-plugin/16527729?ref=azzaroco" target="_blank">
					<img src="<?php echo UAP_URL;?>assets/images/purchase_code.jpg" style="margin: 0 auto; display: block;"/>
					</a>
				</div>
			</div>
	</form>
</div>

<div class="uap-stuffbox">
		<h3 class="uap-h3">
			<label style="text-transform: uppercase; font-size:16px;">
				<?php _e('Contact Support', 'uap');?>
			</label>
		</h3>
		<div class="inside">
			<div class="submit" style="float:left; width:80%;">
				<?php _e('In order to contact Indeed support team you need to create a ticket providing all the necessary details via our support system:', 'uap');?> support.wpindeed.com
			</div>
			<div class="submit" style="float:left; width:20%; text-align:center;">
				<a href="http://support.wpindeed.com/open.php?topicId=19" target="_blank" class="button button-primary button-large"> <?php _e('Submit Ticket', 'uap');?></a>
			</div>
			<div class="clear"></div>
		</div>
	</div>

	<div class="uap-stuffbox">
		<h3 class="uap-h3">
			<label style="text-transform: uppercase; font-size:16px;">
		    	<?php _e('Documentation', 'uap');?>
		    </label>
		</h3>
		<div class="inside">
			<iframe src="https://affiliate.wpindeed.com/documentation/" width="100%" height="1000px" ></iframe>
		</div>
	</div>

</div>

<script>
jQuery( document ).ready(function(){
		jQuery( '[name=uap_save_licensing_code]' ).on( 'click', function(){
				jQuery.ajax({
							type : "post",
							url : window.uap_url + '/wp-admin/admin-ajax.php',
							data : {
											 action						: "uap_el_check_get_url_ajax",
											 purchase_code		: jQuery('[name=uap_envato_code]').val(),
											 nonce						: '<?php echo wp_create_nonce('uap_license_nonce');?>',
						  },
							success: function (data) {
									if ( data ){
											window.location.href = data;
									} else {
											alert( 'Error!' );
									}
							}
				});
				return false;
		});
		jQuery( '.uap-js-revoke-license' ).on( 'click', function(){
				jQuery.ajax({
							type : "post",
							url : window.uap_url + '/wp-admin/admin-ajax.php',
							data : {
											 action						: "uap_revoke_license",
											 nonce						: '<?php echo wp_create_nonce('uap_license_nonce');?>',
							},
							success: function (data) {
									window.location.href = '<?php echo admin_url('admin.php?page=ultimate_affiliates_pro&tab=help&revoke=true');?>';
							}
				});
		});
});
</script>
<?php
