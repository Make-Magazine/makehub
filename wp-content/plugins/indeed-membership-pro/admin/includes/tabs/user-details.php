<?php
    $uid = isset( $_GET['uid'] ) ? $_GET['uid'] : 0;
    $MemberAddEdit = new \Indeed\Ihc\Admin\MemberAddEdit();
    $data = $MemberAddEdit->setUid( $uid )->getUserData();
    $data['avatar'] = ihc_get_avatar_for_uid( $uid );
    $data['order'] = \Ihc_Db::get_all_order( 100, 0, $uid);
    $data['full_name'] = \Ihc_Db::getUserFulltName( $uid );
    $data['verified_email'] =  get_user_meta( $uid, 'ihc_verification_status', true );
    $data['allow_show_public_profile'] = get_user_meta( $uid, 'ihc_membership_accept', true );
    $data['user_roles'] = \Ihc_Db::getUserRole( $uid );
    $availableRoles = ihc_get_wp_roles_list();
    $registerFields = ihc_get_user_reg_fields();
    $key = ihc_array_value_exists( $registerFields, 'ihc_memberlist_accept', 'name' );
    $showMemberlist = false;
    if ( !empty( $registerFields[$key]['display_public_reg'] ) ){
    	$showMemberlist = true;
    }
    $data['gifts'] = \Ihc_Db::get_gifts_by_uid( $uid );
    $reasonDbObject = new \Indeed\Ihc\Db\ReasonsForCancelDeleteLevels();
    $data['reasons_for_cancel'] = $reasonDbObject->getForUser( $uid );
    $data['notification_logs'] = \Indeed\Ihc\Db\NotificationLogs::getMany( $uid, 5, 0, true  );
    $data['orders'] = \Ihc_Db::get_all_order( 10, 0, $uid);
    $payment_gateways = ihc_list_all_payments();
    $payment_gateways['woocommerce'] = __( 'WooCommerce', 'ihc' );

    $data['subscriptions'] = \Indeed\Ihc\UserSubscriptions::getAllForUser( $uid, false );
    $taxesOn = ihc_is_magic_feat_active( 'taxes' );
    $userFields = ihc_get_user_reg_fields();

wp_enqueue_script( 'ihc-print-this' );

?>
<style>
<?php if ( isset( $data['ihc_user_custom_banner_src'] ) && $data['ihc_user_custom_banner_src'] != '' ) :?>
.ihc-admin-user-profile-wrapper .ihc-admin-user-page-top-ap-background {
    background-image: url( '<?php echo $data['ihc_user_custom_banner_src'];?>' );
}
<?php endif;?>
</style>
<script>
var ihcPrintThisOptions = {
			importCSS: true,
      importStyle: true,
      loadCSS:"<?php echo IHC_URL . 'admin/assets/css/style.css';?>",
      debug: false,
      printContainer: true,
      pageTitle: "",
      removeInline: false,
      printDelay: 333,
      header: null,
      formValues: false,
      base: true
};
jQuery(document).ready(function(){
      // print page
			jQuery(".ihc-js-print-page").on("click", function(e){
					jQuery( "#ihc_js_print_this" ).printThis( ihcPrintThisOptions );
			});

      // show more details about member
      jQuery( '.ihc-js-show-more' ).on( 'click', function( e ){
        var show = jQuery( '.ihc-js-user-extra-fields' ).css( 'display' );
        if ( show == 'block' ){
            jQuery( '.ihc-js-user-extra-fields' ).css( 'display', 'none' );
            jQuery( '.ihc-js-show-more' ).html( '<?php _e( 'Show More', 'ihc' );?>' );
        } else {
            jQuery( '.ihc-js-user-extra-fields' ).css( 'display', 'block' );
            jQuery( '.ihc-js-show-more' ).html( '<?php _e( 'Show Less', 'ihc' );?>' );
        }
      });
});
</script>
<div class="ihc-stuffbox">
    <h3><?php _e( 'Member Details', 'ihc');?></h3>
    <div class="inside">
      <div class="ihc-js-print-page ihc-print-buttton-wrapper"><i class="fa-ihc fa-print-ihc"></i> Print</div>
      <div class="ihc-admin-user-profile-wrapper" id="ihc_js_print_this" >
        <div class="ihc-admin-user-page-top-ap-background "></div>
        <div class="ihc-admin-user-details-wrapper">
          <div class="ihc-admin-left-side">
            <?php
                if ( isset( $data['avatar'] ) ){
                    $avatar = $data['avatar'];
                } else {
                    $avatar = 'https://secure.gravatar.com/avatar/1cc31b08528740e0d8519581e6bf1b04?s=96&amp;d=mm&amp;r=g';
                }
            ?>
            <img src="<?php echo $avatar;?>" />
            <div class="ihc-admin-user-edit-profile">
              <a href="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=users&ihc-edit-user=' . $uid );?>" target="_blank" class="button button-primary button-large">
                <?php _e( 'Edit Member Profile', 'ihc' );?>
              </a>
            </div>
            <div class="ihc-admin-user-profile-status">
              <span><?php _e( 'Member since:', 'ihc');?></span> <?php echo ihc_convert_date_to_us_format( $data['user_registered'] );?>
            </div>
            <div class="ihc-admin-user-profile-status">
              <!-- Pending WP role with extra style  -->
              <span><?php _e( 'WordPress Role:', 'ihc');?></span>
              <?php if ( !empty( $data['user_roles'] ) ):?>
                  <?php foreach ( $data['user_roles'] as $roleKey => $roleIndex ):?>
                      <?php $style = $roleKey == 'pending_user' ? 'color: #9b4449; font-weight:600;' : '';?>
                      <div style="<?php echo $style;?>"><?php echo $availableRoles[$roleKey];?></div>
                  <?php endforeach;?>
              <?php endif;?>
            </div>
            <?php if ( get_option( 'ihc_register_double_email_verification' ) && ($data['verified_email'] == 1 || $data['verified_email'] == -1) ):?>
                <div class="ihc-admin-user-profile-status">
                  <!-- Show only if Double Email Verification is different than -  -->
                      <span><?php _e( 'Email Verification:', 'ihc');?></span>
                      <?php if ( $data['verified_email'] == 1 ):?>
                          <?php _e( 'Pending', 'ihc' );?>
                      <?php elseif ( $data['verified_email'] == -1 ) :?>
                          <?php _e( 'Unapproved', 'ihc' );?>
                      <?php endif;?>
                </div>
            <?php endif;?>
            <?php if ( get_option( 'ihc_register_opt-in' ) ):?>
                <div class="ihc-admin-user-profile-status">
                  <!-- Show only if ihc_optin_accept is activated: Accepted/Denied  -->
                      <span><?php _e( 'Subscribe to Newsletter list:', 'ihc');?></span>
                      <?php if ( empty( $data['ihc_optin_accept'] ) ):?>
                          <?php _e( 'Denied', 'ihc' );?>
                      <?php else :?>
                          <?php _e( 'Accepted', 'ihc' );?>
                      <?php endif;?>
                </div>
            <?php endif;?>
              <?php if ( $showMemberlist ):?>
                  <div class="ihc-admin-user-profile-status">
                    <!-- Show only if ihc_membership_accept is activated: Accepted/Denied  -->
                    <span><?php _e( 'Show Profile on Public:', 'ihc');?></span>
                    <?php if ( empty( $data['allow_show_public_profile'] ) ):?>
                        <?php _e( 'Denied', 'ihc' );?>
                    <?php else:?>
                        <?php _e( 'Accepted', 'ihc' );?>
                    <?php endif;?>
                  </div>
              <?php endif;?>
              <?php if ( get_option( 'ihc_individual_page_enabled' ) && !empty( $data['ihc_individual_page'] ) ) :?>
                  <div class="ihc-admin-user-profile-status">
                    <!-- Show only if Individual Page module is enabled and user has this page created  -->
                    <?php $url = admin_url( 'post.php?post=' . $data['ihc_individual_page'] . '&action=edit'  );?>
                    <div class="level-type-list ihc_small_yellow_button" style="margin: 0;"> <a href="<?php echo $url;?>" target="_blank" style="color: #fff;"><?php _e( 'Individual Page', 'ihc');?></a></div>
                  </div>
              <?php endif;?>
          </div>

          <div class="ihc-admin-middle-side">
            <div class="ihc-admin-user-main-name">
                <?php echo $data['full_name'];?>
            </div>
            <div class="ihc-admin-user-data">
              <table>
                <tbody>
              <tr>
								<th class="ihc-admin-user-data-label"><?php _e( 'Username:', 'ihc' );?></th>
								<td class="ihc-admin-user-data-content"><?php
                    if ( isset( $data['user_login'] ) ){
                        echo $data['user_login'];
                    } else {
                        echo '-';
                    }
                ?></td>
							</tr>
							<tr>
								<th class="ihc-admin-user-data-label"><?php _e( 'Email:', 'ihc' );?></th>
								<td class="ihc-admin-user-data-content"><a class="" href="mailto:<?php echo $data['user_email'];?>"><?php
                if ( isset( $data['user_email'] ) ){
                    echo $data['user_email'];
                } else {
                    echo '-';
                }
                ?></a></td>
							</tr>
							<tr>
								<th class="ihc-admin-user-data-label"><?php _e( 'First Name:', 'ihc' );?></th>
								<td class="ihc-admin-user-data-content"><?php
                    if ( isset( $data['first_name'] ) ){
                        echo $data['first_name'];
                    } else {
                        echo '-';
                    }
                ;?></td>
							</tr>
							<tr>
								<th class="ihc-admin-user-data-label"><?php _e( 'Last Name:', 'ihc' );?></th>
								<td class="ihc-admin-user-data-content"><?php
                if ( isset( $data['last_name'] ) ){
                    echo $data['last_name'];
                } else {
                    echo '-';
                }?></td>
							</tr>
							<tr>
								<th class="ihc-admin-user-data-label"><?php _e( 'Biography', 'ihc' );?></th>
								<td class="ihc-admin-user-data-content"><?php  if ( isset( $data['description'] ) ){
                    echo $data['description'];
                } else {
                    echo '-';
                };?></td>
							</tr>
							<tr>
								<th class="ihc-admin-user-data-label"><?php _e( 'Gender:', 'ihc' );?></th>
								<td class="ihc-admin-user-data-content"><?php echo isset( $data['gender'] ) ? $data['gender'] : '-';?></td>
							</tr>
							<tr>
								<th class="ihc-admin-user-data-label"><?php _e( 'Website (URL):', 'ihc' );?></th>
								<td class="ihc-admin-user-data-content"><?php echo isset( $data['user_url'] ) ? $data['user_url'] : '-';?></td>
							</tr>

						</tbody>
              </table>

              <!-- SHOW MORE USER DETAILS -->
              <?php $showMore = false;?>
                  <table class="ihc-js-user-extra-fields" style="display: none;">
                          <tbody>
                          <?php if ( $userFields ):?>
                              <?php $exclude = [ 'tos',
                                                 'confirm_email',
                                                 'ihc_optin_accept',
                                                 'ihc_memberlist_accept',
                                                 'recaptcha',
                                                 'ihc_invitation_code_field',
                                                 'ihc_dynamic_price',
                                                 'ihc_coupon',
                                                 'payment_select',
                                                 'ihc_avatar',
                              ];?>
                              <?php foreach ( $userFields as $userField ):?>
                                  <?php
                                    $slug = $userField['name'];
                                    if ( $userField['native_wp'] || !isset( $data[ $slug ] ) || $data[ $slug ] == '' || in_array( $slug, $exclude) ) continue;
                                    $showMore = true;
                                  ?>
                                  <tr>
                                      <th class="ihc-admin-user-data-label"><?php echo $userField['label'] . ':';?></th>
                                      <td class="ihc-admin-user-data-content">
                                      <?php
                                        switch ( $userField['type'] ){
                                            case 'ihc_country':
                                              $countries = ihc_get_countries();
                                              echo isset( $countries[ $data[ $slug ] ] ) ? $countries[ $data[ $slug ] ] : $data[ $slug ];
                                              break;
                                            case 'ihc_state':
                                              echo $data[ $slug ];
                                              break;
                                            case 'select':
                                              if ( isset( $userField['values'][$data[ $slug ]] ) ){
                                                  echo $userField['values'][$data[ $slug ]];
                                              }
                                              break;
                                            default:
                                              if ( is_array( $data[ $slug ] ) ){
                                                  echo implode( ',', $data[$slug] );
                                              } else {
                                                  echo $data[ $slug ];
                                              }
                                              break;
                                        }
                                      ?>
                                      </td>
                                  </tr>
                              <?php endforeach;?>
                          <?php endif;?>
                        </tbody>
                      </table>
                <?php if ( $showMore ):?>
                    <div class="ihc-pointer ihc-js-show-more ihc-user-details-showmore-fields"><?php _e( 'Show More', 'ihc' );?></div>
                <?php endif;?>
              <!-- END OF SHOW MORE USER DETAILS -->

            </div>
          </div>
          <div class="ihc-clear"></div>

          <?php if ( !empty( $data['subscriptions'] ) ):?>
              <div class="ihc-admin-user-data-list" style="position:relative;">
                <!-- Show only if User has any Membership assigned  -->
                <h2><?php _e( 'Membership Plans', 'ihc');?></h2>
                <p><?php _e( 'Member signed memberships list', 'ihc');?></p>
                <div style="position:absolute; top:70px; right:0px;">
                <a href="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=users&ihc-edit-user=' . $uid .'#ihc_membeship_select_wrapper' );?>" target="_blank">
                  <i class="fa-ihc ihc-icon-edit-e"></i>
                </a>
              </div>
                <table class="wp-list-table widefat fixed tags" >
                    <thead>
                        <tr>
                            <th><?php _e( 'Membership', 'ihc');?></th>
                            <th><?php _e( 'Plan Details', 'ihc');?></th>
                            <th><?php _e( 'Amount', 'ihc');?></th>
                            <th><?php _e( 'Payment Service', 'ihc');?></th>
                            <th><?php _e( 'Trial Period<', 'ihc');?>/th>
                            <th><?php _e( 'Grace Period', 'ihc');?></th>
                            <th><?php _e( 'Next Payment Due', 'ihc');?></th>
                            <th><?php _e( 'Starts On', 'ihc');?></th>
                            <th><?php _e( 'Expires On', 'ihc');?></th>
                            <th style="width:50px"><?php _e( 'Status', 'ihc');?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $data['subscriptions'] as $subscription ):?>
                            <?php
                                $subscriptionMetas = \Indeed\Ihc\Db\UserSubscriptionsMeta::getAllForSubscription( $subscription['id'] );
                                $membershipData = \Indeed\Ihc\Db\Memberships::getOne( $subscription['level_id'] );
                                $accessType = ihcGetValueFromTwoPossibleArrays( $subscriptionMetas, $membershipData, 'access_type' );
                                //$orderId = \Indeed\Ihc\UserSubscriptions::getIdForUserSubscription( $subscription['user_id'], $subscription['level_id'] );
                                $orderId = \Ihc_Db::getLastOrderIdByUserAndLevel( $subscription['user_id'], $subscription['level_id'] );
                                $orderMeta = new \Indeed\Ihc\Db\OrderMeta();
                            ?>
                            <tr>
                              <td class="ihc-levels-table-name" style="color: #21759b; font-weight:bold; width:120px;font-size: 14px;"><?php echo \Indeed\Ihc\Db\Memberships::getMembershipLabel( $subscription['level_id'] );?></td>
                              <td><?php
                                  switch ( $accessType ){
                                      case 'regular_period':
                                        $accessRegularTimeType = ihcGetValueFromTwoPossibleArrays( $subscriptionMetas, $membershipData, 'access_regular_time_type' );
                                        _e( 'Subscription - ', 'ihc' );
                                        switch ( $accessRegularTimeType ){
                                            case 'D':
                                              _e( 'Daily', 'ihc' );
                                              break;
                                            case 'W':
                                              _e( 'Weekly', 'ihc' );
                                              break;
                                            case 'M':
                                              _e( 'Monthly', 'ihc' );
                                              break;
                                            case 'Y':
                                              _e( 'Yearly', 'ihc' );
                                              break;
                                        }
                                        break;
                                      case 'unlimited':
                                        _e( 'LifeTime', 'ihc' );
                                        break;
                                      case 'limited':
                                        _e( 'Limited Time', 'ihc' );
                                        break;
                                      case 'date_interval':
                                        _e( 'Date Range', 'ihc' );
                                        break;
                                  }
                              ?></td>
                              <td><?php echo ihcPaymentPlanDetailsAdmin( $uid, $subscription['level_id'], $subscription['id'] );?></td>
                              <td>
                                  <?php if ( isset( $subscriptionMetas['payment_gateway'] ) && isset( $payment_gateways[$subscriptionMetas['payment_gateway']] ) ):?>
                                      <?php echo  $payment_gateways[$subscriptionMetas['payment_gateway']];?>
                                  <?php else :
                                      $paymentService = $orderMeta->get( $orderId, 'ihc_payment_type' );
                                      echo isset( $payment_gateways[ $paymentService ] ) ? $payment_gateways[ $paymentService ] : '-';
                                  endif;?>
                              </td>
                              <td><?php
                                  $isTrial = ihcGetValueFromTwoPossibleArrays( $subscriptionMetas, $membershipData, 'is_trial' );
                                  $expireTrialTime = ihcGetValueFromTwoPossibleArrays( $subscriptionMetas, $membershipData, 'expire_trial_time' );
                                  if ( $isTrial ){
                                      _e( 'Yes', 'ihc' );
                                      if ( isset( $isTrial ) && $isTrial && $expireTrialTime !==false && strtotime( $expireTrialTime )  < indeed_get_unixtimestamp_with_timezone() ){
                                          echo __( ' - until ', 'ihc' ) . ihc_convert_date_time_to_us_format( $expireTrialTime );
                                      }
                                  } else {
                                     _e( 'No', 'ihc' );
                                  }
                              ?>
                              </td>
                              <td><?php if ( isset( $subscriptionMetas['grace_period'] ) && $subscriptionMetas['grace_period'] != ''): ?>
                                  <?php echo __( 'Yes - ', 'ihc') . $subscriptionMetas['grace_period'] . ihcGetTimeTypeByCode( 'D', $subscriptionMetas['grace_period'] ) . __(' after expires', 'ihc' );?>
                                  <?php else:?>
                                    <?php
                                        $gracePeriod = \Indeed\Ihc\Db\Memberships::getMembershipGracePeriod( $subscription['level_id'] );
                                        if ( $gracePeriod ):?>
                                      <?php echo __( 'Yes - ', 'ihc') . $gracePeriod . ihcGetTimeTypeByCode( 'D', $gracePeriod ) .  __(' after expires', 'ihc' );?>
                                    <?php endif;?>
                              <?php endif;?>
                              </td>
                              <td><?php if ( isset( $subscriptionMetas['payment_due_time'] ) && $subscriptionMetas['payment_due_time'] !='' ){
                                      echo ihc_convert_date_time_to_us_format( $subscriptionMetas['payment_due_time'] );
                              }?></td>
                              <td><?php echo ihc_convert_date_time_to_us_format( $subscription['start_time'] );?></td>
                              <td><?php echo ihc_convert_date_time_to_us_format( $subscription['expire_time'] );?></td>
                              <td><?php
                                  if ( strtotime( $subscription['expire_time'] ) > indeed_get_unixtimestamp_with_timezone() ){
                                      _e( 'Active', 'ihc' );
                                  } else if ( strtotime( $subscription['expire_time'] ) < indeed_get_unixtimestamp_with_timezone() ){
                                      _e( 'Expired', 'ihc' );
                                  } else {
                                      _e( 'Pending', 'ihc' );
                                  }
                              ?></td>
                            </tr>
                        <?php endforeach;?>
                      </tbody>
                </table>
              </div>
          <?php endif;?>

          <?php if ( !empty( $data['orders'] ) ):?>
            <?php
                $orderMeta = new \Indeed\Ihc\Db\OrderMeta();
                require_once IHC_PATH . 'classes/Orders.class.php';
                $Orders = new Ump\Orders();
            ?>
              <div class="ihc-admin-user-data-list">
                <!-- Show only if User has any Order created. Taxes and Net Amount shows only when Taxes module is enabled  -->
                <h2><?php _e( 'Payment History', 'ihc');?></h2>
                <p><?php _e( 'All payments registered for current Member until now', 'ihc');?></p>
                <table class="wp-list-table widefat fixed tags ">
                  <thead>
                      <tr>
                          <th><?php _e( 'Membership', 'ihc');?></th>
                          <th><?php _e( 'Code', 'ihc');?></th>
                          <?php if ( $taxesOn ):?>
                              <th><?php _e( 'Net Amount', 'ihc');?></th>
                              <th><?php _e( 'Taxes', 'ihc');?></th>
                          <?php endif;?>
                          <th><?php _e( 'Total Amount', 'ihc');?></th>
                          <th><?php _e( 'Payment Method', 'ihc');?></th>
                          <th><?php _e( 'Charging Type', 'ihc');?></th>
                          <th><?php _e( 'Coupon', 'ihc');?></th>
                          <th><?php _e( 'Transaction', 'ihc');?></th>
                          <th><?php _e( 'Date', 'ihc');?></th>
                          <th style="width:60px"><?php _e( 'Status', 'ihc');?></th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php foreach ( $data['orders'] as $orderData ):?>
                          <?php
                          $taxes = $orderMeta->get( $orderData['id'], 'taxes_amount' );
                          ?>
                          <tr>
                            <td><?php echo $orderData['level'];?></td>
                            <td><?php echo isset( $orderData['metas']['code'] ) ? $orderData['metas']['code'] : '-';?></td>
                            <?php if ( $taxesOn ):?>
                                <td>
                                  <?php $value = $orderMeta->get( $orderData['id'], 'base_price' );?>
                          				<?php if ( $value !== null ):?>
                          						<?php echo $value . ' ' . $orderData['amount_type'];?>
                          				<?php elseif ( $taxes != false ):?>
                          						<?php $netAmount = $orderData['amount_value'] - $taxes;?>
                          						<?php echo $netAmount . ' ' . $orderData['amount_type'];?>
                          				<?php else :?>
                          						<?php echo $orderData['amount_value'] . ' ' . $orderData['amount_type'];?>
                          				<?php endif;?>
                                </td>
                                <td>
                            				<?php
                                        $value = $orderMeta->get( $orderData['id'], 'base_price' );
                                    ?>
                            				<?php if ( $value !== null ):?>
                            						<?php echo $value . ' ' . $orderData['amount_type'];?>
                            				<?php elseif ( $taxes != false ):?>
                            						<?php $netAmount = $orderData['amount_value'] - $taxes;?>
                            						<?php echo $netAmount . ' ' . $orderData['amount_type'];?>
                            				<?php else :?>
                            						<?php echo $orderData['amount_value'] . ' ' . $orderData['amount_type'];?>
                            				<?php endif;?>
                                </td>
                            <?php endif;?>
                            <td><?php echo $orderData['amount_value'] . ' ' . $orderData['amount_type'];?></td>
                            <td><?php
                                if (empty($orderData['metas']['ihc_payment_type'])):
                        					echo '-';
                        				else:
                        					if (!empty($orderData['metas']['ihc_payment_type'])){
                        						$gateway_key = $orderData['metas']['ihc_payment_type'];
                        						echo isset( $payment_gateways[$gateway_key] ) ? $payment_gateways[$gateway_key] : '-';
                        						 $payment_gateway = $payment_gateways[$gateway_key];
                        					}
                        				endif;
                            ?></td>
                            <td><?php
                              $isRecurring = $orderMeta->get( $orderData['id'], 'is_recurring' );;
                              if ( $isRecurring ):?>
                                  <?php _e( 'Recurrent', 'ihc' );?>
                              <?php else :?>
                                  <?php _e( 'Single payment', 'ihc' );?>
                              <?php endif;?>
                            </td>
                            <td><?php
                      					$coupon = $Orders->get_meta_by_order_and_name( $orderData['id'], 'coupon_used' );
                      					if ($coupon) echo $coupon;
                      					else echo '-';
                      			?></td>
                            <td><?php
                              $transactionId = $orderMeta->get( $orderData['id'], 'transaction_id' );
                              echo !empty( $transactionId ) ? $transactionId : '-';
                            ?></td>
                            <td><?php echo ihc_convert_date_time_to_us_format($orderData['create_date']);?></td>
                            <td><?php
                                switch ($orderData['status']){
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
                      							echo $orderData['status'];
                      							break;
                      					}
                            ?></td>
                          </tr>
                      <?php endforeach;?>
                  </tbody>
                </table>
                <a href="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=orders&uid=' . $uid );?>" target="_blank" style="text-decoration: underline;"><?php _e( 'Show more', 'ihc');?></a>
              </div>
          <?php endif;?>

          <?php if ( get_option( 'ihc_gifts_enabled' ) && !empty( $data['gifts'] ) && is_array( $data['gifts'] ) ):?>
              <div class="ihc-admin-user-data-list">
                <!-- Show only if Membership Gifts module is enabled and user received any Gift  -->
                <h2><?php _e( 'Membership Gifts', 'ihc');?></h2>
                <p><?php _e( 'Gift codes received by current Member', 'ihc');?></p>
                <table class="wp-list-table widefat fixed tags ">
                  <thead>
                      <tr>
                          <th><?php _e( 'Gift Code', 'ihc');?></th>
                          <th><?php _e( 'Discount Value', 'ihc');?></th>
                          <th><?php _e( 'Discount for Membership', 'ihc');?></th>
                          <th style="width:50px"><?php _e( 'Status', 'ihc');?></th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php foreach ( $data['gifts'] as $gift ):?>
                          <tr>
                            <td><?php echo $gift['code'];?></td>
                            <td><?php
                    				if ($gift['discount_type']=='price'){
                    					echo ihc_format_price_and_currency( get_option( 'ihc_currency' ) , $gift['discount_value']);
                    				} else {
                    					echo $gift['discount_value'] . '%';
                    				}
                    			?></td>
                            <td><?php echo \Indeed\Ihc\Db\Memberships::getMembershipLabel( $gift['target_level'] );?></td>
                            <td><?php
                                if ($gift['is_active']):
					                          _e('Unused', 'ihc');
                        				else :
                        					  _e('Used', 'ihc');
                        				endif;
                            ?></td>
                          </tr>
                      <?php endforeach;?>
                  </tbody>
                </table>
              </div>
          <?php endif;?>

          <?php if ( !empty( $data['notification_logs'] ) ):?>
              <?php
              $notifications = new \Indeed\Ihc\Notifications();
              $notification_arr = $notifications->getAllNotificationNames();
              $adminNotifications = $notifications->getAdminCases();
              ?>
              <div class="ihc-admin-user-data-list">
                <!-- Show only if User has any Notification stored in Logs  -->
                <h2><?php _e( 'All Notifications sent to current Member by now', 'ihc');?></h2>
                <p><?php _e( 'All Notifications sent to current Member by now', 'ihc');?></p>
                <table class="wp-list-table widefat fixed tags">
                  <thead>
                      <tr>
                          <th style="width: 250px;"><?php _e( 'Notification Type', 'ihc');?></th>
                          <th><?php _e( 'Email', 'ihc');?></th>
                          <th style="width: 120px;"><?php _e( 'Sent On', 'ihc');?></th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php foreach ( $data['notification_logs'] as $notification ):?>
                        <tr>
                          <td><?php echo isset( $notification_arr[$notification->notification_type] ) ? $notification_arr[$notification->notification_type] : $notification->notification_type;?></td>
                          <td>
                            <div style="font-weight:700; padding-bottom:10px;"><?php echo $notification->subject;?></div>
                            <div class="ihc-notification-logs-message"><p></p>
                            <?php echo $notification->message;?>
                          </div>
                          </td>
                          <td><?php echo ihc_convert_date_time_to_us_format( $notification->create_date );?></td>
                        </tr>
                      <?php endforeach;?>
                  </tbody>
                </table>
                <a href="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=notification-logs&uid=' . $uid );?>" target="_blank"  style="text-decoration: underline;"><?php _e( 'Show more', 'ihc' );?></a>
              </div>
          <?php endif;?>

          <?php if ( get_option( 'ihc_reason_for_cancel_enabled' ) && !empty( $data['reasons_for_cancel'] ) ):?>
              <div class="ihc-admin-user-data-list">
                <!-- Show only if Reason for Cancelling module is enabled and user has a reason submitted  -->
                <h2><?php _e( 'Reason for Cancelling', 'ihc');?></h2>
                <p><?php _e( 'Reasons submitted by Member during cancelling process', 'ihc');?></p>
                <table class="wp-list-table widefat fixed tags ">
                  <thead>
                      <tr>
                        <th style="width: 150px;"><?php _e( 'Membership', 'ihc');?></th>
                        <th><?php _e( 'Reason', 'ihc');?></th>
                        <th style="width: 120px;"><?php _e( 'Date', 'ihc');?></th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php foreach ( $data['reasons_for_cancel'] as $reason ):?>
                          <tr>
                            <td><?php echo \Ihc_Db::get_level_name_by_lid( $reason->lid );?></td>
                            <td><?php echo $reason->reason;?></td>
                            <td><?php echo date( 'Y-m-d h:i:s', $reason->action_date );?></td>
                          </tr>
                      <?php endforeach;?>
                  </tbody>
                </table>
              </div>
          <?php endif;?>

        </div>
      </div>

    </div>
</div>
