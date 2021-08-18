<div class="ihc-subtab-menu">
	<a class="ihc-subtab-menu-item" href="<?php echo admin_url('admin.php?page=ihc_manage&tab=invitation_code-add_new');?>"><?php esc_html_e('Add Single Invitation Code', 'ihc');?></a>
	<a class="ihc-subtab-menu-item" href="<?php echo admin_url('admin.php?page=ihc_manage&tab=invitation_code-add_new&multiple=true');?>"><?php esc_html_e('Add Bulk Invitation Codes', 'ihc');?></a>
	<a class="ihc-subtab-menu-item" href="<?php echo admin_url('admin.php?page=ihc_manage&tab=invitation_code');?>"><?php esc_html_e('Manage Invitation Codes', 'ihc');?></a>
	<div class="ihc-clear"></div>
</div>
<?php
if (!empty($_POST['add_new'])){
	Ihc_Db::invitation_code_add_new($_POST);
	ihc_do_write_into_htaccess();
} else if (!empty($_POST['delete_code'])){
	Ihc_Db::invitation_code_delete($_POST['delete_code']);
} else if (!empty($_POST['delete_multiple_codes'])){
	foreach ($_POST['delete_multiple_codes'] as $code){
		Ihc_Db::invitation_code_delete($code);
	}
}
ihc_save_update_metas('ihc_invitation_code');//save update metas
$data['metas'] = ihc_return_meta_arr('ihc_invitation_code');//getting metas

echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
do_action( "ihc_admin_dashboard_after_top_menu" );

$items = Ihc_Db::invitation_code_get_all();
?>
<div class="iump-wrapper">
<div class="iump-page-title">Ultimate Membership Pro -
				<span class="second-text">
					<?php  esc_html_e("Invitation Code", 'ihc');?>
				</span>
		</div>
<form  method="post">
	<div class="ihc-stuffbox">
		<h3 class="ihc-h3"><?php esc_html_e('Settings', 'ihc');?></h3>
		<div class="inside">

			<div class="iump-form-line">
				<h2><?php esc_html_e('Activate/Hold Section', 'ihc');?></h2>

				<label class="iump_label_shiwtch ihc-switch-button-margin">
					<?php $checked = ($data['metas']['ihc_invitation_code_enable']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_invitation_code_enable');" <?php echo $checked;?> />
					<div class="switch ihc-display-inline"></div>
				</label>
				<input type="hidden" name="ihc_invitation_code_enable" value="<?php echo $data['metas']['ihc_invitation_code_enable'];?>" id="ihc_invitation_code_enable" />
			</div>

			<div class="iump-form-line">
				<label><?php esc_html_e('Error Message', 'ihc');?></label>
				<input type="text" name="ihc_invitation_code_err_msg" value="<?php echo $data['metas']['ihc_invitation_code_err_msg'];?>" />
			</div>

			<div class="ihc-submit-form">
				<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
			</div>

		</div>
	</div>
</form>

<?php if ($items):?>
	<form method="post" id="delete_code_form">
		<input type="hidden" value="" name="delete_code" id="delete_code" />
		<div class="ihc-dashboard-form-wrap">
			<table class="wp-list-table widefat fixed tags ihc-admin-tables">
				<thead>
					<tr>
						<th class="manage-column" width="10px;"><input type="checkbox" onClick="ihcSelectAllCheckboxes(this, '.ihc-delete-code');" /></th>
						<th class="manage-column"><?php esc_html_e('Code', 'ihc');?></th>
						<th class="manage-column"><?php esc_html_e('Repeat', 'ihc');?></th>
						<th class="manage-column"><?php esc_html_e('Used', 'ihc');?></th>
						<th class="manage-column" width="80px;"><?php esc_html_e('Remove', 'ihc');?></th>
					</tr>
				</thead>
				<?php
				$i = 1;
				foreach ($items as $key=>$arr):?>
				<?php
				$done ='';
				if($arr['repeat_limit'] == $arr['submited']){
					 $done = 'ihc-invitationcode-disabled';
				}
				?>
				<tr class="<?php if ($i%2==0){
					 echo 'alternate';
				}
					echo $done;
					?>
					">
					<td><input type="checkbox" name="delete_multiple_codes[]" value="<?php echo $arr['id'];?>" class="ihc-delete-code" /></td>
					<td><?php echo $arr['code'];?></td>
					<td><?php echo $arr['repeat_limit'];?></td>
					<td><?php echo $arr['submited'];?></td>
					<td align="center">
						<i class="fa-ihc ihc-icon-remove-e ihc-js-admin-invitation-code-delete-code" data-id="<?php echo $arr['id'];?>"></i>
					</td>
				</tr>
				<?php
					$i++;
				endforeach;?>
			</table>
		</div>
		<div class="ihc-submit-form">
			<input type="submit" value="<?php esc_html_e('Remove', 'ihc');?>" name="delete" class="button button-primary button-large" />
		</div>
	</form>
<?php endif;?>
</div>
<?php
