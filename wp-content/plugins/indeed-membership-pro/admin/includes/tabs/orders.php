<?php
wp_enqueue_script( 'ihc-print-this' );

if ( isset( $_POST['save_edit_order'] ) && !empty( $_POST['ihc_admin_edit_order_nonce'] ) && wp_verify_nonce( $_POST['ihc_admin_edit_order_nonce'], 'ihc_admin_edit_order_nonce' ) ){
		$orderObject = new \Indeed\Ihc\Db\Orders();
		$orderData = $_POST;
		$orderObject->setData( $_POST )->setId( $_POST['id'] )->save();

		$orderData = $orderObject->fetch()->get();

		$orderMeta = new \Indeed\Ihc\Db\OrderMeta();
		$paymentGateway = $orderMeta->get( $_POST['id'], 'ihc_payment_type' );

		switch ( $_POST['status'] ){
				case 'pending':
					$args = [ 'manual' => true, 'expire_time' => '0000-00-00 00:00:00', 'payment_gateway' => $paymentGateway ];
					\Indeed\Ihc\UserSubscriptions::makeComplete( $orderData->uid, $orderData->lid, true, $args );
					\Indeed\Ihc\UserSubscriptions::updateStatus( $orderData->uid, $orderData->lid, 0 );
					do_action( 'ihc_action_after_cancel_subscription', $orderData->uid, $orderData->lid );
					break;
				case 'Completed':
						$levelData = \Indeed\Ihc\Db\Memberships::getOne( $orderData->lid );
						if (isset($levelData['access_trial_time_value']) && $levelData['access_trial_time_value'] > 0 && \Indeed\Ihc\UserSubscriptions::isFirstTime($orderData['uid'], $_POST['lid'])){
							/// CHECK FOR TRIAL
								\Indeed\Ihc\UserSubscriptions::makeComplete( $orderData->uid, $orderData->lid, true, [ 'manual' => true, 'payment_gateway' => $paymentGateway ] );
						} else {
								\Indeed\Ihc\UserSubscriptions::makeComplete( $orderData->uid, $orderData->lid, false, [ 'manual' => true, 'payment_gateway' => $paymentGateway ] );
						}
					break;
				case 'error':
					\Indeed\Ihc\UserSubscriptions::updateStatus( $orderData->uid, $orderData->lid, 0 );
					do_action( 'ihc_action_after_cancel_subscription', $orderData->uid, $orderData->lid );
					break;
				case 'refund':
					$deleteLevelForUser = apply_filters( 'ihc_filter_delete_level_for_user_on_payment_refund', true, $orderData['uid'], $_POST['lid'] );
			    do_action( 'ihc_action_payments_before_refund', $orderData->uid, $orderData->lid );
	        if ( $deleteLevelForUser ){
	            \Indeed\Ihc\UserSubscriptions::deleteOne( $orderData->uid, $orderData->lid );
	        }
	        do_action( 'ihc_action_payments_after_refund', $orderData->uid, $orderData->lid );
					break;
				case 'fail':
					\Indeed\Ihc\UserSubscriptions::deleteOne( $orderData->uid, $orderData->lid );
					break;
		}
}

////////////// create order manually
if (isset($_POST['save_order']) && !empty( $_POST['ihc_admin_add_new_order_nonce'] ) && wp_verify_nonce( $_POST['ihc_admin_add_new_order_nonce'], 'ihc_admin_add_new_order_nonce' ) ){
		require_once IHC_PATH . 'admin/classes/Ihc_Create_Orders_Manually.php';
		$Ihc_Create_Orders_Manually = new Ihc_Create_Orders_Manually($_POST);
		$Ihc_Create_Orders_Manually->process();
		if (!$Ihc_Create_Orders_Manually->get_status()){
				$create_order_message = '<div class="ihc-danger-box">' . $Ihc_Create_Orders_Manually->get_reason() . '</div>';
		} else {
				$create_order_message = '<div class="ihc-success-box">' . __('Order has been created!', 'ihc') . '</div>';
		}
}

if (!empty($_POST['submit_new_payment'])){
	unset($_POST['submit_new_payment']);
	$array = $_POST;
	if (empty($array['txn_id'])){
		/// set txn_id
		$array['txn_id'] = $_POST['uid'] . '_' . $_POST['order_id'] . '_' . indeed_get_unixtimestamp_with_timezone();
	}
	$array['message'] = 'success';


	/// THIS PIECE OF CODE ACT AS AN IPN SERVICE.
	$level_data = ihc_get_level_by_id($_POST['level']);
	if (isset($level_data['access_trial_time_value']) && $level_data['access_trial_time_value'] > 0 && \Indeed\Ihc\UserSubscriptions::isFirstTime($_POST['uid'], $_POST['level'])){
		/// CHECK FOR TRIAL
			\Indeed\Ihc\UserSubscriptions::makeComplete( $_POST['uid'], $_POST['level'], true, [ 'manual' => true ] );
	} else {
		  \Indeed\Ihc\UserSubscriptions::makeComplete( $_POST['uid'], $_POST['level'], false, [ 'manual' => true ] );
	}

	do_action( 'ihc_payment_completed', $_POST['uid'], $_POST['level'] );
	ihc_insert_update_transaction($_POST['uid'], $array['txn_id'], $array);

	Ihc_User_Logs::set_user_id($_POST['uid']);
	Ihc_User_Logs::set_level_id($_POST['level']);
	Ihc_User_Logs::write_log( __('Complete transaction.', 'ihc'), 'payments');

	unset($array);
}
$uid = (isset($_GET['uid'])) ? $_GET['uid'] : 0;

	$data['total_items'] = Ihc_Db::get_count_orders($uid);
	if ($data['total_items']){
		$url = admin_url('admin.php?page=ihc_manage&tab=orders');
		$limit = 25;
		$current_page = (empty($_GET['ihc_payments_list_p'])) ? 1 : $_GET['ihc_payments_list_p'];
		if ($current_page>1){
			$offset = ( $current_page - 1 ) * $limit;
		} else {
			$offset = 0;
		}
		include_once IHC_PATH . 'classes/Ihc_Pagination.class.php';
		$pagination = new Ihc_Pagination(array(
												'base_url' => $url,
												'param_name' => 'ihc_payments_list_p',
												'total_items' => $data['total_items'],
												'items_per_page' => $limit,
												'current_page' => $current_page,
		));
		if ($offset + $limit>$data['total_items']){
			$limit = $data['total_items'] - $offset;
		}
		$data['pagination'] = $pagination->output();
		$data['orders'] = Ihc_Db::get_all_order($limit, $offset, $uid);
	}
	$data['view_transaction_base_link'] = admin_url('admin.php?page=ihc_manage&tab=payments&details_id=');
	$data['add_new_transaction_by_order_id_link'] = admin_url('admin.php?page=ihc_manage&tab=new_transaction&order_id=');

	$payment_gateways = ihc_list_all_payments();
	$payment_gateways['woocommerce'] = __( 'WooCommerce', 'ihc' );

	$show_invoices = (ihc_is_magic_feat_active('invoices')) ? TRUE : FALSE;
	$invoiceShowOnlyCompleted = get_option('ihc_invoices_only_completed_payments');
	require_once IHC_PATH . 'classes/Orders.class.php';
	$Orders = new Ump\Orders();
?>

<?php
echo ihc_inside_dashboard_error_license();
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
do_action( "ihc_admin_dashboard_after_top_menu" );
?>
<div class="iump-wrapper">
<div class="iump-page-title">Ultimate Membership Pro -
	<span class="second-text"><?php _e('Orders List', 'ihc');?></span>
</div>
<a href="<?php echo admin_url('admin.php?page=ihc_manage&tab=add_new_order');?>" class="indeed-add-new-like-wp">
			<i class="fa-ihc fa-add-ihc"></i><?php _e('Add New Order', 'ihc');?></a>

<?php if (!empty($create_order_message)):?>
		<div style="margin-top: 10px;"><?php echo $create_order_message;?></div>
<?php endif;?>

<?php if (!empty($data['orders'])):?>
	<?php echo $data['pagination'];?>
		<div class="iump-rsp-table">
<table class="wp-list-table widefat fixed tags ihc-admin-tables" style="margin-top:20px;">
	<thead>
		<tr>
			<th class="manage-column check-column" style="width:60px;">
				<span><?php _e('ID', 'ihc');?></span>
			</th>
			<th class="manage-column column-primary">
				<span><?php _e('Code', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php _e('Customer', 'ihc');?></span>
			</th>
			<th class="manage-column" style="width: 200px;">
				<span><?php _e('Memberships', 'ihc');?></span>
			</th>
			<?php if ( ihc_is_magic_feat_active( 'taxes' ) ):?>
			<th class="manage-column">
					<span><?php _e('Net Amount', 'ihc');?></span>
			</th>
			<th class="manage-column">
					<span><?php _e('Taxes', 'ihc');?></span>
			</th>
			<?php endif;?>
			<th class="manage-column">
				<span><?php _e('Total Amount', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php _e('Payment method', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php _e('Date', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php _e('Coupon', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php _e('Transaction', 'ihc');?></span>
			</th>
			<?php if ($show_invoices):?>
				<th class="manage-column">
					<span><?php _e('Invoices', 'ihc');?></span>
				</th>
			<?php endif;?>
			<th class="manage-column">
				<span><?php _e('Status', 'ihc');?></span>
			</th>
			<th class="manage-column" style="width:120px;">
				<span><?php _e('Actions', 'ihc');?></span>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th class="manage-column check-column">
				<span><?php _e('ID', 'ihc');?></span>
			</th>
			<th class="manage-column column-primary">
				<span><?php _e('Code', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php _e('Customer', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php _e('Items', 'ihc');?></span>
			</th>
			<?php if ( ihc_is_magic_feat_active( 'taxes' ) ):?>
			<th class="manage-column">
					<span><?php _e('Net Amount', 'ihc');?></span>
			</th>
			<th class="manage-column">
					<span><?php _e('Taxes', 'ihc');?></span>
			</th>
			<?php endif;?>
			<th class="manage-column">
				<span><?php _e('Total Amount', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php _e('Payment method', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php _e('Date', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php _e('Coupon', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php _e('Transaction', 'ihc');?></span>
			</th>
			<?php if ($show_invoices):?>
				<th class="manage-column">
					<span><?php _e('Invoice', 'ihc');?></span>
				</th>
			<?php endif;?>
			<th class="manage-column">
				<span><?php _e('Status', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php _e('Actions', 'ihc');?></span>
			</th>
		</tr>
	</tfoot>

	<?php
	$i = 1;
	$orderMeta = new \Indeed\Ihc\Db\OrderMeta();
	foreach ($data['orders'] as $array):?>
		<?php
				$taxes = $orderMeta->get( $array['id'], 'taxes_amount' );
				if ( $taxes == null ){
						$taxes = $orderMeta->get( $array['id'], 'tax_value' );
				}
				if ( $taxes == null ){
						$taxes = $orderMeta->get( $array['id'], 'taxes' );
				}
		?>
		<tr  class="<?php if($i%2==0) echo 'alternate';?>">
			<th class="check-column"><?php echo $array['id'];?></th>
			<td class="column-primary"><?php
				if (!empty($array['metas']['code'])){
					echo '<a href="' . admin_url( '/admin.php?page=ihc_manage&tab=order-edit&order_id=' . $array['id'] ) . '" target="_blank" >' . $array['metas']['code'] . '</a>';
				} else {
					echo '-';
				}
			?>
			<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
		</td>
			<td><span style="color: #21759b; color: #222; font-size: 13px; font-weight:bold;"><a target="_blank" href="<?php echo ihcAdminUserDetailsPage( $array['uid'] );?>"><?php echo $array['user'];?></a></span></td>
			<td><div  style="background:none; color: #6eaf0f; font-weight: bold; font-size: 13px;"><?php echo $array['level'];?></div></td>
			<?php if ( ihc_is_magic_feat_active( 'taxes' ) ):?>
			<td>
				<?php $value = $orderMeta->get( $array['id'], 'base_price' );?>
				<?php if ( $value !== null ):?>
						<?php echo $value . ' ' . $array['amount_type'];?>
				<?php elseif ( $taxes != false ):?>
						<?php $netAmount = $array['amount_value'] - $taxes;?>
						<?php echo $netAmount . ' ' . $array['amount_type'];?>
				<?php else :?>
						<?php echo $array['amount_value'] . ' ' . $array['amount_type'];?>
				<?php endif;?>
			</td>
			<td>
				<?php if ( $taxes !== null ):?>
						<?php echo $taxes . ' ' . $array['amount_type'];?>
				<?php endif;?>
			</td>
			<?php endif;?>
			<td><span class="order-total-amount"><?php echo $array['amount_value'] . ' ' . $array['amount_type'];?></span></td>
			<td><?php
				$payment_gateway = "";
				if (empty($array['metas']['ihc_payment_type'])):
					echo '-';
				else:
					if (!empty($array['metas']['ihc_payment_type'])){
						$gateway_key = $array['metas']['ihc_payment_type'];
						echo isset( $payment_gateways[$gateway_key] ) ? $payment_gateways[$gateway_key] : '-';
						 $payment_gateway = $payment_gateways[$gateway_key];
					}
				endif;
			?></td>
			<td><?php echo ihc_convert_date_time_to_us_format($array['create_date']);?></td>
			<td><?php
					$coupon = $Orders->get_meta_by_order_and_name($array['id'], 'coupon_used');
					if ($coupon) echo $coupon;
					else echo '-';
			?></td>
			<td><?php
								$transactionId = $orderMeta->get( $array['id'], 'transaction_id' );

								if ( $transactionId == '' )
								echo '-';
								else{
									switch ( $array['metas']['ihc_payment_type'] ){
											case 'paypal':
												if ( get_option( 'ihc_paypal_sandbox' ) ){
													$transactionLink = 'https://www.sandbox.paypal.com/activity/payment/' . $transactionId;
												} else {
													$transactionLink = 'https://www.paypal.com/activity/payment/' . $transactionId;
												}
												break;
											case 'paypal_express_checkout':
													if ( get_option( 'ihc_paypal_express_checkout_sandbox' ) ){
														$transactionLink = 'https://www.sandbox.paypal.com/activity/payment/' . $transactionId;
													} else {
														$transactionLink = 'https://www.paypal.com/activity/payment/' . $transactionId;
													}
													break;
											case 'stripe':

												break;
											case 'stripe_checkout_v2':
												$key = get_option( 'ihc_stripe_checkout_v2_publishable_key' );
												if ( strpos( $key, 'pk_test' ) !== false ){
													$transactionLink = 'https://dashboard.stripe.com/test/payments/' . $transactionId;
												} else {
													$transactionLink = 'https://dashboard.stripe.com/payments/' . $transactionId;
												}
												break;
											case 'mollie':
												$transactionLink = 'https://www.mollie.com/dashboard/payments/' . $transactionId;
												break;
											case 'twocheckout':
												if ( strpos( $transactionId, '_' ) !== false ){
														$temporaryTransactionId = explode( '_', $transactionId );
														$transactionId = isset( $temporaryTransactionId[1] ) ? $temporaryTransactionId[1] : $transactionId;
												}
												$transactionLink = 'https://secure.2checkout.com/cpanel/order_info.php?refno=' . $transactionId;
												break;
									}
								}
			?><a target="_blank" title="<?php _e('Check Transaction on '.$payment_gateway.'', 'ihc'); ?>" href="<?php echo $transactionLink;?>"><?php echo $transactionId;?></a></td>
			<?php if ($show_invoices):?>
				<?php if ( !empty( $invoiceShowOnlyCompleted ) && $array['status'] !== 'Completed' ):?>
					<td data-title="<?php _e('Level', 'ihc');?>">-</td>
				<?php else:?>
					<td><i class="fa-ihc fa-invoice-preview-ihc iump-pointer" onClick="iumpGenerateInvoice(<?php echo $array['id'];?>);"></i></td>
				<?php endif;?>
			<?php endif;?>
			<td style="font-weight:700;">
				<?php
					//echo ucfirst($array['status']);
					switch ($array['status']){
						case 'Completed':
							_e('Completed', 'ihc');
							break;
						case 'pending':
							echo '<div>' . __('Pending', 'ihc') . '</div>';

							break;
						case 'fail':
						case 'failed':
							_e('Fail', 'ihc');
							break;
						case 'error':
							_e('Error', 'ihc');
							break;
						default:
							echo $array['status'];
							break;
					}
				?>
			</td>
			<td class="column ihc-order-actions" style="width:60px; text-align:center;">

					<?php if ( $array['status'] == 'pending' ):?>
								<span class="ihc-js-make-order-completed ihc-pointer" data-id="<?php echo $array['id'];?>" ><i  title="<?php _e( 'Make Completed', 'ihc' );?>" class="fa-ihc ihc-icon-completed-e"></i></span>
					<?php endif;?>

					<a title="<?php _e( 'Edit', 'ihc' );?>" href="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=order-edit&order_id=' . $array['id'] );?>" >
						<i class="fa-ihc ihc-icon-edit-e"></i>
					</a>
					<?php if ( isset( $array['metas']['ihc_payment_type'] )
								&& in_array( $array['metas']['ihc_payment_type'], [ 'stripe', 'paypal', 'paypal_express_checkout', 'stripe_checkout_v2', 'mollie', 'twocheckout' ] ) ) :?>
						<?php
						$chargingPlan = '';
						$refundLink = '';
						$subscriptionId = $orderMeta->get( $array['id'], 'subscription_id' );
						switch ( $array['metas']['ihc_payment_type'] ){
								case 'paypal':
									if ( get_option( 'ihc_paypal_sandbox' ) ){
										if ( $subscriptionId != '' ){
												$chargingPlan = 'https://www.sandbox.paypal.com/billing/subscriptions/' . $subscriptionId;
										}
					          $refundLink = 'https://www.sandbox.paypal.com/activity/actions/refund/edit/' . $transactionId;
					        } else {
										if ( $subscriptionId != '' ){
												$chargingPlan = 'https://www.paypal.com/billing/subscriptions/' . $subscriptionId;
										}
					          $refundLink = 'https://www.paypal.com/activity/actions/refund/edit/' . $transactionId;
					        }
									break;
								case 'paypal_express_checkout':
									if ( get_option( 'ihc_paypal_express_checkout_sandbox' ) ){
										if ( $subscriptionId != '' ){
												$chargingPlan = 'https://www.sandbox.paypal.com/billing/subscriptions/' . $subscriptionId;
										}
										$refundLink = 'https://www.sandbox.paypal.com/activity/actions/refund/edit/' . $transactionId;
									} else {
										if ( $subscriptionId != '' ){
												$chargingPlan = 'https://www.paypal.com/billing/subscriptions/' . $subscriptionId;
										}
										$refundLink = 'https://www.paypal.com/activity/actions/refund/edit/' . $transactionId;
									}
									break;
								case 'stripe':

									break;
								case 'stripe_checkout_v2':
									$key = get_option( 'ihc_stripe_checkout_v2_publishable_key' );
									if ( strpos( $key, 'pk_test' ) !== false ){
										if ( $subscriptionId != '' ){
												$chargingPlan = 'https://dashboard.stripe.com/test/subscriptions/' . $subscriptionId;
										}
										$refundLink = 'https://dashboard.stripe.com/test/payments/' . $transactionId;
									} else {
										if ( $subscriptionId != '' ){
												$chargingPlan = 'https://dashboard.stripe.com/subscriptions/' . $subscriptionId;
										}
										$refundLink = 'https://dashboard.stripe.com/payments/' . $transactionId;
									}
									break;
								case 'mollie':
									$customerId = $orderMeta->get( $array['id'], 'customer_id' );
									if ( $customerId != '' ){
											$chargingPlan = 'https://www.mollie.com/dashboard/customers/' . $customerId;
									}
									$refundLink = 'https://www.mollie.com/dashboard/payments/' . $transactionId;
									break;
								case 'twocheckout':
									if ( $subscriptionId != '' ){
											$chargingPlan = 'https://secure.2checkout.com/cpanel/license_info.php?refno=' . $subscriptionId;
									}
									break;
						}
						if ( $refundLink != '' ):?>
							<a title="<?php _e( 'Refund', 'ihc' );?>" href="<?php echo $refundLink;?>" target="_blank" ><i class="fa-ihc ihc-icon-refund-e"></i></a>
						<?php endif;?>

						<?php if ( $chargingPlan != '' ):?>
							<a title="<?php _e( 'Check Charging plan on '.$payment_gateway.'', 'ihc' );?>" href="<?php echo  $chargingPlan;?>" target="_blank" ><i class="fa-ihc ihc-icon-plan-e"></i></a>
						<?php endif;?>

					<?php endif;?>

							<span class="ihc-pointer ihc-js-delete-order" data-id="<?php echo $array['id'];?>" title="<?php _e( 'Remove', 'ihc' );?>" >
									<i class="fa-ihc ihc-icon-remove-e"></i>
							</span>
			</td>
		</tr>
	<?php
		$i++;
	 endforeach;?>

</table>
</div>
<?php endif;?>
</div>

<script>
jQuery( '.ihc-js-delete-order' ).on( 'click', function(){
		var orderId = jQuery( this ).attr( 'data-id' );
		swal({
			title: "<?php _e( 'Are you sure that you want to delete this order?', 'ihc' );?>",
			text: "",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-danger",
			confirmButtonText: "OK",
			closeOnConfirm: true
		},
		function(){
				jQuery.ajax({
						type : 'post',
						url : decodeURI(window.ihc_site_url)+'/wp-admin/admin-ajax.php',
						data : {
											 action: 'ihc_admin_delete_order',
											 id:			orderId,
									 },
						success: function (response) {
								location.reload();
						}
			 });
	 });
});
jQuery( document ).ready(function(){
	jQuery( '.ihc-js-make-order-completed' ).on( 'click', function(){
			var orderId = jQuery( this ).attr( 'data-id' );
			jQuery.ajax({
					type : 'post',
					url : decodeURI(window.ihc_site_url)+'/wp-admin/admin-ajax.php',
					data : {
										 action: 'ihc_admin_make_order_completed',
										 id:			orderId,
								 },
					success: function (response) {
							location.reload();
					}
		 });
	});
});

</script>

<style>
.btn-default {
  color: #333;
  background-color: #fff;
  border-color: #ccc;
}
.btn-default:focus,
.btn-default.focus {
  color: #333;
  background-color: #e6e6e6;
  border-color: #8c8c8c;
}
.btn-default:hover {
  color: #333;
  background-color: #e6e6e6;
  border-color: #adadad;
}
.btn-default:active,
.btn-default.active,
.open > .dropdown-toggle.btn-default {
  color: #333;
  background-color: #e6e6e6;
  border-color: #adadad;
}
.btn-default:active:hover,
.btn-default.active:hover,
.open > .dropdown-toggle.btn-default:hover,
.btn-default:active:focus,
.btn-default.active:focus,
.open > .dropdown-toggle.btn-default:focus,
.btn-default:active.focus,
.btn-default.active.focus,
.open > .dropdown-toggle.btn-default.focus {
  color: #333;
  background-color: #d4d4d4;
  border-color: #8c8c8c;
}
.btn-default:active,
.btn-default.active,
.open > .dropdown-toggle.btn-default {
  background-image: none;
}
.btn-default.disabled,
.btn-default[disabled],
fieldset[disabled] .btn-default,
.btn-default.disabled:hover,
.btn-default[disabled]:hover,
fieldset[disabled] .btn-default:hover,
.btn-default.disabled:focus,
.btn-default[disabled]:focus,
fieldset[disabled] .btn-default:focus,
.btn-default.disabled.focus,
.btn-default[disabled].focus,
fieldset[disabled] .btn-default.focus,
.btn-default.disabled:active,
.btn-default[disabled]:active,
fieldset[disabled] .btn-default:active,
.btn-default.disabled.active,
.btn-default[disabled].active,
fieldset[disabled] .btn-default.active {
  background-color: #fff;
  border-color: #ccc;
}
.btn-danger:hover{
    color: #fff;
    background-color: #ac2925;
    border-color: #761c19;
}
.btn {
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: normal;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
}
.btn-danger {
    color: #fff;
    background-color: #d9534f;
    border-color: #d43f3a;
}
.btn-lg, .btn-group-lg > .btn {
    padding: 10px 16px;
    font-size: 18px;
    line-height: 1.3333333;
    border-radius: 6px;
}
.btn-default {
    color: #333;
    background-color: #fff;
    border-color: #ccc;
}

</style>
