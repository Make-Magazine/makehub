<?php
if (!empty($_POST['new_currency_code']) && !empty($_POST['new_currency_name'])){
	$data = get_option('ihc_currencies_list');
	if (empty($data[$_POST['new_currency_code']])){
		$data[$_POST['new_currency_code']] = $_POST['new_currency_name'];
	}
	update_option('ihc_currencies_list', $data);
}
$basic_currencies = ihc_get_currencies_list('custom');
?>
<div class="iump-wrapper">
<form  method="post">
		<div class="ihc-stuffbox">
			<h3><?php esc_html_e('Add new Currency', 'ihc');?></h3>
			<div class="inside">
			<h2><?php esc_html_e('Custom Currency', 'ihc');?></h2>
		<p><?php esc_html_e('Add new currencies (with custom symbols) alongside the predefined list', 'ihc');?></p>

				<div class="iump-form-line">
					<label class="iump-labels-special"><?php esc_html_e('Code:', 'ihc');?></label>
					<input type="test" value="" name="new_currency_code" />
					<p><?php esc_html_e('Insert a valid Currency Code, ex: ', 'ihc');?><span><strong><?php esc_html_e('USD, EUR, CAD.', 'ihc');?></strong></span></p>
				</div>
				<div class="iump-form-line">
					<label class="iump-labels-special"><?php esc_html_e('Name:', 'ihc');?></label>
					<input type="text" value="" name="new_currency_name" />
				</div>
				<div class="ihc-wrapp-submit-bttn iump-submit-form">
					<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
				</div>
			</div>
		</div>
		<?php if ($basic_currencies!==FALSE && count($basic_currencies)>0){?>
		<div class="ihc-dashboard-form-wrap">
			<table class="wp-list-table widefat fixed tags ihc-admin-tables">
				<thead>
					<tr>
						<th class="manage-column">Code</th>
						<th class="manage-column">Name</th>
						<th class="manage-column" width="80px;">Delete</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($basic_currencies as $code=>$name){
					?>
					<tr id="ihc_div_<?php echo $code;?>">
						<td><?php echo $code;?></td>
						<td><?php echo $name;?></td>
						<td><i class="fa-ihc ihc-icon-remove-e" onClick="ihcRemoveCurrency('<?php echo $code;?>');"></i></td>
					</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>
	<?php }?>
</form>
</div>
