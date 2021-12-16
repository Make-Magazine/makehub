<?php
ihc_save_update_metas('prorate_subscription');//save update metas
$data['metas'] = ihc_return_meta_arr('prorate_subscription');//getting metas
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
do_action( "ihc_admin_dashboard_after_top_menu" );
?>
<form  method="post">
	<div class="ihc-stuffbox">
		<h3 class="ihc-h3"><?php esc_html_e('Pro-rate Subscription', 'ihc');?></h3>
		<div class="inside">

			<div class="iump-form-line">
					<h2><?php esc_html_e('Activate/Hold Pro-rate Subscription', 'ihc');?></h2>
					<p></p>

				<label class="iump_label_shiwtch ihc-switch-button-margin">
					<?php $checked = ($data['metas']['ihc_prorate_subscription_enabled']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_prorate_subscription_enabled');" <?php echo $checked;?> />
					<div class="switch ihc-display-inline"></div>
				</label>
				<input type="hidden" name="ihc_prorate_subscription_enabled" value="<?php echo $data['metas']['ihc_prorate_subscription_enabled'];?>" id="ihc_prorate_subscription_enabled" />
			</div>

      <div class="iump-form-line">
					<h2><?php esc_html_e('Reset Billing Period', 'ihc');?></h2>
					<p></p>

				<label class="iump_label_shiwtch ihc-switch-button-margin">
					<?php $checked = ($data['metas']['ihc_prorate_subscription_reset_billing_period']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_prorate_subscription_reset_billing_period');" <?php echo $checked;?> />
					<div class="switch ihc-display-inline"></div>
				</label>
				<input type="hidden" name="ihc_prorate_subscription_reset_billing_period" value="<?php echo $data['metas']['ihc_prorate_subscription_reset_billing_period'];?>" id="ihc_prorate_subscription_reset_billing_period" />
			</div>


			<div class="ihc-wrapp-submit-bttn ihc-submit-form">
				<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
			</div>

		</div>
	</div>
</form>
