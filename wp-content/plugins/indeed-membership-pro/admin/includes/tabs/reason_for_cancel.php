<?php
ihc_save_update_metas('reason_for_cancel');//save update metas
$data['metas'] = ihc_return_meta_arr('reason_for_cancel');//getting metas
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
do_action( "ihc_admin_dashboard_after_top_menu" );

$reasonDbObject = new \Indeed\Ihc\Db\ReasonsForCancelDeleteLevels();
$count = $reasonDbObject->count();
$limit = 30;

$current_page = (empty($_GET['p'])) ? 1 : $_GET['p'];
if ( $current_page>1 ){
	$offset = ( $current_page - 1 ) * $limit;
} else {
	$offset = 0;
}
require_once IHC_PATH . 'classes/Ihc_Pagination.class.php';
$url  = admin_url( 'admin.php?page=ihc_manage&tab=reason_for_cancel' );
$pagination_object = new Ihc_Pagination(array(
											'base_url'             => $url,
											'param_name'           => 'p',
											'total_items'          => $count,
											'items_per_page'       => $limit,
											'current_page'         => $current_page,
));
$pagination = $pagination_object->output();
if ( $offset + $limit>$count ){
	$limit = $count - $offset;
}

$items= $reasonDbObject->get( $limit, $offset );
?>
<form  method="post">
	<div class="ihc-stuffbox">
		<h3 class="ihc-h3"><?php esc_html_e('Reason for Cancelling', 'ihc');?></h3>
		<div class="inside">

			<div class="iump-form-line">
				<h2><?php esc_html_e('Activate/Hold Reason for Cancelling', 'ihc');?></h2>
				<p><?php esc_html_e('You may activate this option in order to track the reason why a member wants to cancel their subscription or even delete it', 'ihc');?></p>
				<label class="iump_label_shiwtch ihc-switch-button-margin">
					<?php $checked = ($data['metas']['ihc_reason_for_cancel_enabled']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_reason_for_cancel_enabled');" <?php echo $checked;?> />
					<div class="switch ihc-display-inline"></div>
				</label>
				<input type="hidden" name="ihc_reason_for_cancel_enabled" value="<?php echo $data['metas']['ihc_reason_for_cancel_enabled'];?>" id="ihc_reason_for_cancel_enabled" />
			</div>

			<div class="iump-form-line">
					<label><?php esc_html_e('Predefined values', 'ihc');?></label>
					<div>
							<textarea class="ihc-custom-css-box" name="ihc_reason_for_cancel_resons"><?php echo stripslashes($data['metas']['ihc_reason_for_cancel_resons']);?></textarea>
					</div>
					<p><?php esc_html_e("Write values separated by comma ','.", 'ihc');?></p>
			</div>

			<div class="ihc-wrapp-submit-bttn ihc-submit-form">
				<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
			</div>

		</div>
	</div>

</form>

<?php if ( $items ):?>
	<div class="ihc-admin-user-data-list">
<table class="wp-list-table widefat fixed tags">
    <thead>
      <tr>
          <td><?php esc_html_e( 'Username', 'ihc' );?></td>
          <td><?php esc_html_e( 'Membership', 'ihc' );?></td>
          <td><?php esc_html_e( 'Action', 'ihc' );?></td>
          <td><?php esc_html_e( 'Reason', 'ihc' );?></td>
          <td><?php esc_html_e( 'Date', 'ihc' );?></td>
      </tr>
    </thead>
    <tbody class="ihc-alternate">
        <?php foreach ( $items as $itemData ):?>
            <tr>
                <td><?php echo $itemData->user_login;?></td>
                <td><?php echo \Ihc_Db::get_level_name_by_lid( $itemData->lid );?></td>
                <td><?php echo $itemData->action_type;?></td>
                <td><?php echo stripslashes($itemData->reason);?></td>
                <td><?php echo date( 'Y-m-d h:i:s', $itemData->action_date );?></td>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>
</div>
<?php endif;?>

<?php if ($pagination):?>
    <?php echo $pagination;?>
<?php endif;?>

<?php
