<?php
if (!empty($_GET['do_cleanup_logs'])){
	$older_then = indeed_get_unixtimestamp_with_timezone() - $_GET['older_then'] * 24 * 60 * 60;
	Ihc_Db::delete_logs('user_logs', $older_then);
}
ihc_save_update_metas('user_reports');//save update metas
$data['metas'] = ihc_return_meta_arr('user_reports');//getting metas
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
do_action( "ihc_admin_dashboard_after_top_menu" );
?>
<form  method="post">
	<div class="ihc-stuffbox">
		<h3 class="ihc-h3"><?php esc_html_e('User Reports', 'ihc');?></h3>
		<div class="inside">

			<div class="iump-form-line">
				<h2><?php esc_html_e('Activate/Hold User Reports', 'ihc');?></h2>
				<label class="iump_label_shiwtch ihc-switch-button-margin">
					<?php $checked = ($data['metas']['ihc_user_reports_enabled']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_user_reports_enabled');" <?php echo $checked;?> />
					<div class="switch ihc-display-inline"></div>
				</label>
				<input type="hidden" name="ihc_user_reports_enabled" value="<?php echo $data['metas']['ihc_user_reports_enabled'];?>" id="ihc_user_reports_enabled" />
			</div>

			<?php $we_have_logs = Ihc_User_Logs::get_count_logs('user_logs');?>
			<?php if ($we_have_logs):?>
				<div class="iump-form-line">
					<?php esc_html_e('Clean Up Users Reports older then:', 'ihc');?>
					<select id="older_then_select">
						<option value="">...</option>
						<option value="1"><?php esc_html_e('One Day', 'ihc');?></option>
						<option value="7"><?php esc_html_e('One Week', 'ihc');?></option>
						<option value="30"><?php esc_html_e('One Month', 'ihc');?></option>
					</select>
					<div class="button button-primary button-large" onClick="ihcDoCleanUpLogs('<?php echo admin_url('admin.php?page=ihc_manage&tab=user_reports');?>');"><?php esc_html_e('Clean Up', 'ihc');?></div>
				</div>
				<div class="iump-form-line">
					<a href="<?php echo admin_url('admin.php?page=ihc_manage&tab=view_user_logs&type=user_logs');?>" target="_blank"><?php esc_html_e('View All User Reports', 'ihc');?></a>
				</div>
			<?php endif;?>

			<div class="ihc-wrapp-submit-bttn ihc-submit-form">
				<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
			</div>

		</div>
	</div>



</form>
