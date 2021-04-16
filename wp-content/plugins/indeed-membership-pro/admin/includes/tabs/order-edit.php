<?php
$orderId = isset( $_GET['order_id'] ) ? $_GET['order_id'] : 0;
$orderObject = new \Indeed\Ihc\Db\Orders();
$orderData = $orderObject->setId( $orderId )
                         ->fetch()
                         ->get();
$orderMetaObject = new \Indeed\Ihc\Db\OrderMeta();
$orderMeta = $orderMetaObject->getAllByOrderId( $orderId );
$uid = isset( $orderData->uid ) ? $orderData->uid : 0;
$lid = isset( $orderData->lid ) ? $orderData->lid : 0;

if ( !$orderId || !$orderData ):?>
    <h5><?php _e( "No order details available!", 'ihc' );?></h5>
<?php else :?>

<form action="<?php echo admin_url('admin.php?page=ihc_manage&tab=orders');?>" method="post">

	<input type="hidden" name="ihc_admin_edit_order_nonce" value="<?php echo wp_create_nonce( 'ihc_admin_edit_order_nonce' );?>" />

  <input type="hidden" name="id" value="<?php echo $orderId;?>" />

	<div class="ihc-stuffbox">
		<h3><?php _e('Edit Order', 'ihc');?></h3>
		<div class="inside">
      <div class="ihc-order-edit-leftside">
      <div class="row" style="margin-left:0px;">
      		<div class="col-xs-5">
      		    <div class="input-group" style="margin:30px 0 5px 0;">
          				<span class="input-group-addon" id="basic-addon1" style="min-width:170px; text-align:right;"><?php _e('Username:', 'ihc');?></span>
                  <input type="text" name="username" value="<?php echo \Ihc_Db::get_username_by_wpuid( $orderData->uid );?>"  />
      				</div>
      		</div>
      </div>


            <div class="row" style="margin-left:0px;">
            		<div class="col-xs-5">
            		    <div class="input-group" style="margin:30px 0 5px 0;">
                				<span class="input-group-addon" id="basic-addon1" style="min-width:170px; text-align:right;"><?php _e('Amount:', 'ihc');?></span>
                        <input type="number" min=0 name="amount_value" step="0.01" value="<?php echo $orderData->amount_value;?>" />
            				</div>
            		</div>
            </div>

            <div class="row" style="margin-left:0px;">
      				<div class="col-xs-5">
      					<div class="input-group" style="margin:30px 0 5px 0;">
          					<span class="input-group-addon" id="basic-addon1" style="min-width:170px; text-align:right;"><?php _e('Currency:', 'ihc');?></span>
                    <select name="amount_type">
                      <?php
        								$currency_arr = ihc_get_currencies_list('all');
        								$custom_currencies = ihc_get_currencies_list('custom');
                        $ihc_currency = get_option('ihc_currency');
        								foreach ($currency_arr as $k=>$v){
        									?>;?>
        									<option value="<?php echo $k?>" <?php if ($k==$orderData->amount_type) echo 'selected';?> >
        										<?php echo $v;?>
        										<?php if (is_array($custom_currencies) && in_array($v, $custom_currencies))  _e(" (Custom Currency)");?>
        									</option>
        									<?php
        								}
        							?>
                    </select>
      					</div>
      				</div>
      			</div>

            <div class="row" style="margin-left:0px;">
      				<div class="col-xs-5">
      					<div class="input-group" style="margin:30px 0 5px 0;">
          					<span class="input-group-addon" id="basic-addon1" style="min-width:170px; text-align:right;"><?php _e('Created Date:', 'ihc');?></span>
                    <input type="text" id="created_date_ihc" name="create_date" value="<?php echo $orderData->create_date;?>" />
      					</div>
      				</div>
      			</div>

      <div class="row" style="margin-left:0px;">
				<div class="col-xs-5">
					<div class="input-group" style="margin:30px 0 5px 0;">
					<span class="input-group-addon" id="basic-addon1" style="min-width:170px; text-align:right;"><?php _e('Payment Status:', 'ihc');?></span>
          <select name="status">
            <?php
                $status = [
                            'pending'   => __( 'Pending', 'ihc' ),
                            'Completed' => __( 'Completed', 'ihc' ),
                            'error'     => __( 'Error', 'ihc' ),
                            'refund'    => __( 'Refund', 'ihc' ),
                            'fail'      => __( 'Failed', 'ihc' ),
                ];
  							foreach ($status as $k=>$v):
                  $selected = ( $k == $orderData->status ) ? 'selected' : '';
  								?>
  								<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
  								<?php
  							endforeach;
  					?>
          </select>
					</div>
				</div>
			</div>

			<div style="margin-top: 15px;" class="ihc-wrapp-submit-bttn">
				<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="save_edit_order" class="button button-primary button-large" />
			</div>
    </div>
    <div class="ihc-order-edit-rightside">
      <div class="ihc-order-edit-box">
      <h4><?php _e('Customer', 'ihc');?></h4>
        <div class="ihc-order-edit-box-title"><?php
          $fullName = \Ihc_Db::getUserFulltName( $uid );
          if ( $fullName != '' ){
              echo $fullName;
          } else {
              echo '-';
          }
          ?></div>
        <div><?php echo \Ihc_Db::user_get_email( $uid );?></div>
      </div>
      <div class="ihc-order-edit-box">
      <h4><?php _e('Membership Plan', 'ihc');?></h4>
        <div class="ihc-order-edit-box-title"><?php echo \Indeed\Ihc\Db\Memberships::getMembershipLabel( $lid );?>
          <?php if ( empty( $orderMeta['is_recurring'] ) ) :?>
              <?php _e( '(One Time)', 'ihc' );?>
          <?php else :?>
              <?php _e( '(Recurring)', 'ihc' );?>
          <?php endif;?>
        </div>
        <div><?php echo \Indeed\Ihc\Db\Memberships::getMembershipShortDescription( $lid );?></div>
      </div>
      <div class="ihc-order-edit-box">
      <h4><?php _e('Charging details', 'ihc');?></h4>
        <div>Payment via <span style="color:#444; font-weight: 400;">
          <?php
          $payment_gateway = "";
          $payment_gateways = ihc_list_all_payments();
          $payment_gateways['woocommerce'] = __( 'WooCommerce', 'ihc' );
          if ( empty( $orderMeta['ihc_payment_type'] ) ):
  					  echo '-';
  				else:
  						echo isset( $payment_gateways[$orderMeta['ihc_payment_type']] ) ? $payment_gateways[$orderMeta['ihc_payment_type']] : '-';
              $payment_gateway =  $payment_gateways[$orderMeta['ihc_payment_type']];
  				endif;
          ?>
          <?php
              if ( isset( $orderMeta['transaction_id'] ) && $orderMeta['transaction_id'] != '' ){
                  $transactionId = $orderMeta['transaction_id'];
                  switch ( $orderMeta['ihc_payment_type'] ){
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
                            if ( strpos( $orderMeta['transaction_id'], '_' ) !== false ){
                                $temporaryTransactionId = explode( '_', $transactionId );
                                $transactionId = isset( $temporaryTransactionId[1] ) ? $temporaryTransactionId[1] : $transactionId;
                            }
                            $transactionLink = 'https://secure.2checkout.com/cpanel/order_info.php?refno=' . $transactionId;
                            break;
                    }
                    ?>
                    (<a target="_blank" title="<?php _e('Check Transaction on '.$payment_gateway.'', 'ihc'); ?>" href="<?php echo $transactionLink;?>"><?php echo $transactionId;?></a>)
                    <?php
                }
          ?></span></div>
        <div>
          <?php if ( empty( $orderMeta['is_recurring'] ) ) :?>
              <?php _e( 'Single Charge', 'ihc' );?>
          <?php else :?>
              <?php if ( empty( $orderMeta['is_trial'] ) ):?>
                  <?php _e( 'Recurrent', 'ihc' );?>
              <?php else :?>
                  <?php _e( 'Trial/Intial Payment', 'ihc' );?>
              <?php endif;?>
          <?php endif;?>
        </div>
        <?php if ( isset( $orderMeta['taxes_amount'] ) && $orderMeta['taxes_amount'] != '' && isset( $orderMeta['currency'] ) ):?>
            <div><?php _e( 'Taxes Included:', 'ihc' );?> <?php echo $orderMeta['taxes_amount'] . $orderMeta['currency'];?></div>
        <?php endif;?>
            <?php if ( isset( $orderMeta['coupon_used'] ) && $orderMeta['coupon_used'] != '' ) :?>
                <div>
                    <?php _e( 'Coupon applied:', 'ihc' );?>
                    <?php echo $orderMeta['coupon_used'];?>
                </div>
            <?php endif;?>
        <div class="ihc-order-edit-box-links">
          <?php if ( isset( $orderMeta['ihc_payment_type'] )
                && in_array( $orderMeta['ihc_payment_type'], [ 'stripe', 'paypal', 'paypal_express_checkout', 'stripe_checkout_v2', 'mollie', 'twocheckout' ] ) ) :?>
            <?php
            $chargingPlan = '';
            $refundLink = '';
            $transactionId = isset( $orderMeta['transaction_id'] ) ? $orderMeta['transaction_id'] : '';
            switch ( $orderMeta['ihc_payment_type'] ){
                case 'paypal':
                  if ( get_option( 'ihc_paypal_sandbox' ) ){
                    if ( !empty( $orderMeta['is_recurring'] ) && isset( $orderMeta['subscription_id'] ) && $orderMeta['subscription_id'] != '' ){
                        $chargingPlan = 'https://www.sandbox.paypal.com/billing/subscriptions/' . $orderMeta['subscription_id'];
                    }
                    $refundLink = 'https://www.sandbox.paypal.com/activity/actions/refund/edit/' . $transactionId;
                  } else {
                    if ( !empty( $orderMeta['is_recurring'] ) && isset( $orderMeta['subscription_id'] ) && $orderMeta['subscription_id'] != '' ){
                        $chargingPlan = 'https://www.paypal.com/billing/subscriptions/' . $orderMeta['subscription_id'];
                    }
                    $refundLink = 'https://www.paypal.com/activity/actions/refund/edit/' . $transactionId;
                  }
                  break;
                case 'paypal_express_checkout':
                  if ( get_option( 'ihc_paypal_express_checkout_sandbox' ) ){
                    if ( !empty( $orderMeta['is_recurring'] ) && isset( $orderMeta['subscription_id'] ) && $orderMeta['subscription_id'] != '' ){
                        $chargingPlan = 'https://www.sandbox.paypal.com/billing/subscriptions/' . $orderMeta['subscription_id'];
                    }
                    $refundLink = 'https://www.sandbox.paypal.com/activity/actions/refund/edit/' . $transactionId;
                  } else {
                    if ( !empty( $orderMeta['is_recurring'] ) && isset( $orderMeta['subscription_id'] ) && $orderMeta['subscription_id'] != '' ){
                        $chargingPlan = 'https://www.paypal.com/billing/subscriptions/' . $orderMeta['subscription_id'];
                    }
                    $refundLink = 'https://www.paypal.com/activity/actions/refund/edit/' . $transactionId;
                  }
                  break;
                case 'stripe':

                  break;
                case 'stripe_checkout_v2':
                  $key = get_option( 'ihc_stripe_checkout_v2_publishable_key' );
                  if ( strpos( $key, 'pk_test' ) !== false ){
                    if ( !empty( $orderMeta['is_recurring'] ) && isset( $orderMeta['subscription_id'] ) && $orderMeta['subscription_id'] != '' ){
                        $chargingPlan = 'https://dashboard.stripe.com/test/subscriptions/' . $orderMeta['subscription_id'];
                    }
                    $refundLink = 'https://dashboard.stripe.com/test/payments/' . $transactionId;
                  } else {
                    if ( !empty( $orderMeta['is_recurring'] ) && isset( $orderMeta['subscription_id'] ) && $orderMeta['subscription_id'] != '' ){
                        $chargingPlan = 'https://dashboard.stripe.com/subscriptions/' . $orderMeta['subscription_id'];
                    }
                    $refundLink = 'https://dashboard.stripe.com/payments/' . $transactionId;
                  }
                  break;
                case 'mollie':
                  if ( !empty( $orderMeta['is_recurring'] ) && isset( $orderMeta['customer_id'] ) && $orderMeta['customer_id'] != '' ){
                      $chargingPlan = 'https://www.mollie.com/dashboard/customers/' . $orderMeta['customer_id'];
                  }
                  $refundLink = 'https://www.mollie.com/dashboard/payments/' . $transactionId;
                  break;
                case 'twocheckout':
                  if ( !empty( $orderMeta['is_recurring'] ) && isset( $orderMeta['subscription_id'] ) && $orderMeta['subscription_id'] != '' ){
                      $chargingPlan = 'https://secure.2checkout.com/cpanel/license_info.php?refno=' . $orderMeta['subscription_id'];
                  }
                  break;
            }
            if ( $refundLink != '' ):?>
              <span>
                  <a title="<?php _e( 'Refund', 'ihc' );?>" href="<?php echo $refundLink;?>" target="_blank" ><?php _e('Refund', 'ihc');?></a>
              </span>
            <?php endif;?>

            <?php if ( $chargingPlan != '' ):?>
              <span>
                  <a title="<?php _e( 'Check charging plan on '.$payment_gateway.'', 'ihc' );?>" href="<?php echo  $chargingPlan;?>" target="_blank" ><?php _e('Check Charging Plan', 'ihc');?></a>
              </span>
            <?php endif;?>

          <?php endif;?>
              <span>
                  <a target="_blank" href="<?php echo admin_url( '/admin.php?page=ihc_manage&tab=order-details&order_id=' . $orderId );?>"><?php _e('Order Details', 'ihc');?></a>
              </span>
        </div>
      </div>
    </div>
    <div class="ihc-clear"></div>
		</div>

	</div>
</form>

<script>
  jQuery(document).ready(function() {
      jQuery('#created_date_ihc').datepicker({
          dateFormat : 'yy-mm-dd',
          onSelect: function(datetext){
              var d = new Date();
              datetext = datetext+" "+d.getHours()+":"+ihcAddZero(d.getMinutes())+":"+ihcAddZero(d.getSeconds());
              jQuery(this).val(datetext);
          }
      });
  });
</script>

<?php endif;?>
