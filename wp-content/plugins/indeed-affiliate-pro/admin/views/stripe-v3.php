<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Stripe v3 - Payouts', 'uap');?></h3>
		<div class="inside">
				<div class="row">
						<div class="col-xs-7">
							<h3><?php _e('Activate/Hold Stripe Gateway', 'uap');?></h3>
							<p><?php _e('Once activated you can process payments to your affiliate users via Stripe directly from the affiliate system.', 'uap');?></p>
							<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
							<?php $checked = ($data['metas']['uap_stripe_v3_enable']) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uapCheckAndH(this, '#uap_stripe_v3_enable');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="uap_stripe_v3_enable" value="<?php echo $data['metas']['uap_stripe_v3_enable'];?>" id="uap_stripe_v3_enable" />
						</div>
				</div>
				<div class="uap-line-break"></div>
				<div class="row">
					<div class="col-xs-6">


						<div class="uap-form-line">
							<label class="uap-label" style="text-align: left;"><?php _e('Live Secret Key', 'uap');?></label>
							<div>
								<input type="text" name="uap_stripe_v3_secret_key" value="<?php echo $data['metas']['uap_stripe_v3_secret_key'];?>" />
							</div>
						</div>
						<div class="uap-form-line">
							<label class="uap-label" style="text-align: left;"><?php _e('Live Publishable Key', 'uap');?></label>
							<div>
								<input type="text" name="uap_stripe_v3_publishable_key" value="<?php echo $data['metas']['uap_stripe_v3_publishable_key'];?>" />
							</div>
						</div>
						<div class="uap-form-line">
             				 <label class="uap-label" style="text-align: left;"><?php _e('Live mode client ID', 'uap');?></label>
             				 <div>
                			<input type="text" name="uap_stripe_v3_client_id" value="<?php echo $data['metas']['uap_stripe_v3_client_id'];?>" />
             				 </div>
           				 </div>
                         		</div>
				</div>
                <div class="uap-line-break"></div>
                <div class="row">
					<div class="col-xs-4">
						<h4><?php _e('Sandbox', 'uap');?></h4>
						<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
						<?php $checked = ($data['metas']['uap_stripe_v3_sandbox']) ? 'checked' : '';?>
						<input type="checkbox" class="uap-switch" onClick="uapCheckAndH(this, '#uap_stripe_v3_sandbox');" <?php echo $checked;?> />
						<div class="switch" style="display:inline-block;"></div>
						</label>
						<input type="hidden" name="uap_stripe_v3_sandbox" value="<?php echo $data['metas']['uap_stripe_v3_sandbox'];?>" id="uap_stripe_v3_sandbox" />
					</div>
				</div>
						<div class="row">
					<div class="col-xs-6">
                        <div class="uap-form-line">
							<label class="uap-label" style="text-align: left;"><?php _e('Sandbox Secret Key', 'uap');?></label>
							<div>
								<input type="text" name="uap_stripe_v3_sandbox_secret_key" value="<?php echo $data['metas']['uap_stripe_v3_sandbox_secret_key'];?>" />
							</div>
						</div>
						<div class="uap-form-line">
							<label class="uap-label" style="text-align: left;"><?php _e('Sandbox Publishable Key', 'uap');?></label>
							<div>
								<input type="text" name="uap_stripe_v3_sandbox_publishable_key" value="<?php echo $data['metas']['uap_stripe_v3_sandbox_publishable_key'];?>" />
							</div>
						</div>
						<div class="uap-form-line">
							<label class="uap-label" style="text-align: left;"><?php _e('Test mode client ID', 'uap');?></label>
							<div>
								<input type="text" name="uap_stripe_v3_sandbox_client_id" value="<?php echo $data['metas']['uap_stripe_v3_sandbox_client_id'];?>" />
							</div>
						</div>
					</div>
				</div>
				<div class="uap-line-break"></div>
				<div style="font-size: 11px; color: #333; padding-left: 10px;">
					<ul class="uap-info-list">
						<?php
							$notification_url = site_url();
							$notification_url = trailingslashit( $notification_url );
							$notification_url = add_query_arg( 'uap_act', 'stripe_v3_webhook', site_url() );
							$oauthRedirectBackPage = add_query_arg( 'uap_act', 'stripe_v3_auth', site_url() );
						?>
						<li><?php _e( '1. Go to http://stripe.com and login with username and password.', 'uap' );?></li>
						<li><?php _e( '2. Click on "Developers", and then select "API keys". Here you will find the "Secret Key" and "Publishable Key".', 'uap' );?></li>
						<li><?php _e( '3. Go to "Webhooks" and set Your <b>Webhook</b> at: ', 'uap' );?> <?php echo "<b>" . $notification_url . "</b>";?></li>
						<li><?php _e( '4. Be sure you use the same currency in the Stripe account and in the "Ultimate Affiliate Pro" settings.', 'uap' );?></li>
						<li><?php _e( '5. In order to get "Live mode client ID" or "Test mode client ID" you need to create a new Account application by clicking on "New Account" top left menu button into dashboard.', 'uap' );?></li>
						<li><?php _e( '6. Check all Account types and all Capabilities.', 'uap' );?></li>
						<li><?php _e( '7. Go to "Settings" > "Connect" > "Settings". From <b>Integration</b> section copy and paste "Live mode client ID" or "Test mode client ID" if <b>View test data</b> is ON.', 'uap' );?></li>
						<li><?php _e( '8. Enable "OAuth for Express accounts" and "OAuth for Standard accounts" from  OAuth settings.', 'uap' );?></li>
						<li><?php _e( '9. In "Redirects" add URI', 'uap' );?> <?php echo "<b>" . $oauthRedirectBackPage . "</b>";?></li>
						<li><?php _e( '10. If your account is in test mode go to "Settings" > "Connect Settings" and make sure that your country exist in "Transfers" and "Payments".', 'uap' );?></li>
						<li><?php _e( '11. Run some tests before switching to Live Transactions.', 'uap' );?></li>
				</ul>
				</div>

				<div class="inside">
					<h5 class="uap-h5"><?php _e('Important:', 'uap');?></h5>
					<p><?php _e('1. When Affiliate connects his Stripe Account with your merchant Stripe Account, Stripe will create a Sub Account for him where payout will be transferred.','uap'); ?></p>
					<p><?php _e('2. Affiliates may see all their Sub Accounts on Profile page from Stripe Dashboard.', 'uap');?></p>
					<p><?php _e('3. In order to run some tests before switching live, make sure that in Stripe balance you have the required amount to be in the same currency as the currency saved in UAP.', 'uap');?></p>
					<p><?php _e('4. In "Settings" > "Connect settings" make sure that you have configured <b>Accounts Types</b> and <b>Capabilities</b>.', 'uap');?></p>
				</div>

				<div class="uap-submit-form">
					<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
				</div>
		</div>
	</div>
</form>
