<div class="uap-wrapper uap-affiliate-userprofile">
    <div class="uap-stuffbox">
        <div class="uap-h3"><?php _e( 'Affiliate Profile', 'uap' );?></div>
        <div class="inside">
            <?php if ( empty($data) ):?>
                <h4><?php _e( 'No details for this Affiliate user.', 'uap' );?></h4>
            <?php else:?>
				<div class="row">
                	<div class="col-xs-12">
                    	<div class="uap-userprofile-mainname-wrapper">
                          <div class="row">
                           <div class="col-xs-1">
                           <?php if ( $data['ranking_possition'] > -1 ):?>
                           		<div class="uap-userprofile-top-wrapper">
                                		<img src="<?php echo UAP_URL;?>assets/images/uap_trophy.png"/>
                                        <div class="uap-userprofile-top-position">
                                        	#<?php echo $data['ranking_possition'];?>
                                        </div>
                                        <div class="uap-userprofile-top-details">
                                        <?php _e( "based on performance", 'uap' );?>
                                        </div>
                                </div>
                            <?php endif;?>
                           </div>
                            <div class="col-xs-5">
                        	<div class="uap-userprofile-mainname">
                        	 <?php if ( !empty( $data['user_meta']['first_name']) && !empty( $data['user_meta']['last_name'] ) ):?>
                   				 <?php echo $data['user_meta']['first_name'].' '.$data['user_meta']['last_name'].' ('.$data['user_meta']['nickname'].')';?>
               				 <?php endif;?>
                       		</div>
                            <div class="uap-userprofile-links">
                            	<span class="uap-userprofile-links-label"><?php _e( "Affiliate Link", 'uap' );?>: </span><a href="<?php echo $data['affiliate_link'];?>" target="_blank"><?php echo $data['affiliate_link'];?></a> <span class="js-uap-copy uap-userprofile-copyclipboard"><?php _e( 'Copy to Clipboard', 'uap' );?></span> <div class="div-notice"><?php _e( '(for testing purpose use the affiliate link into a fresh incognito browser window)', 'uap' );?></div>
                            </div>
                            <div class="uap-userprofile-links">
                            	<span class="uap-userprofile-links-label"><?php _e( "Affiliate ID", 'uap' );?>: </span><?php echo $data['affiliate_id']; ?>
                            </div>
                            <?php if ( $data['custom_slug'] ):?>
                                <div class="uap-userprofile-links">
                                	<span class="uap-userprofile-links-label"><?php _e( "Custom Slug", 'uap' );?>: </span><?php echo $data['custom_slug'];?>
                                </div>
                            <?php endif;?>

                           </div>
                           <div class="col-xs-6">
                           	<div class="row" style="position:relative;">
                            	<div class="col-xs-5">
                                	<div class="uap-userprofile-rank uap-currentrank" style="background-color:#f1505b">
                                    	<div class="uap-userprofile-rank-title"><?php echo $data['current_rank_data']['label'];?>
                                         (<?php if ( $data['current_rank_data']['amount_type'] == 'percentage' ):?><?php echo $data['current_rank_data']['amount_value'];?>%
                                         <?php else :?> <?php echo uap_format_price_and_currency($data['currency'], $data['current_rank_data']['amount_value']);?>
                                              <?php endif;?>
											  <?php _e( 'reward', 'uap' );?>)
                                              </div>
                                        <ul>
                                            <?php if ( $data['bonus_enabled'] && !empty( $data['current_rank_data']['bonus'] ) ):?>
                                                <li><?php _e( 'Achievement Bonus', 'uap' );?>: <?php echo uap_format_price_and_currency($data['currency'], $data['current_rank_data']['bonus']);?></li>
                                            <?php endif;?>

                                            <?php if ( $data['sign_up_enabled'] ):?>

                                                <?php if ( empty( $data['current_rank_data']['sign_up_amount_value'] ) || $data['current_rank_data']['sign_up_amount_value'] < 0 ):?>
                                                    <li><?php _e( 'SignUp Referrals', 'uap' );?>: <?php echo uap_format_price_and_currency( $data['currency'], $data['default_sign_up_referrals'] ) . __( '(default value)', 'uap');?></li>
                                                <?php else : ?>
                                                    <li><?php _e( 'SignUp Referrals', 'uap' );?>: <?php echo uap_format_price_and_currency( $data['currency'], $data['current_rank_data']['sign_up_amount_value'] );?></li>
                                                <?php endif;?>

                                            <?php endif;?>

                                            <?php if ( $data['lifetime_commission'] ):?>
                                                <li>

                                                    <?php _e( 'LifeTime Comission', 'uap' );?>:
                                                    <?php if ( !empty( $data['current_rank_data']['lifetime_amount_value'] ) && $data['current_rank_data']['lifetime_amount_value'] > -1.00 ):?>
                                                        <?php if ( $data['current_rank_data']['lifetime_amount_type'] == 'percentage' ):?>
                                                            <?php echo $data['current_rank_data']['lifetime_amount_value'];?>%
                                                        <?php else :?>
                                                            <?php echo uap_format_price_and_currency( $data['currency'], $data['current_rank_data']['lifetime_amount_value'] );?>
                                                        <?php endif;?>
                                                    <?php else :?>
                                                      <?php if ( $data['current_rank_data']['amount_type'] == 'percentage' ):?>
                                                          <?php echo $data['current_rank_data']['amount_value'];?>%
                                                      <?php else :?>
                                                          <?php echo uap_format_price_and_currency( $data['currency'], $data['current_rank_data']['amount_value'] );?>
                                                      <?php endif;?>
                                                      <?php _e( '(Rank amount)', 'uap' );?>
                                                    <?php endif;?>

                                                </li>
                                            <?php endif;?>

                                            <?php if ( $data['reccuring_referrals'] ):?>
                                                <li>

                                                    <?php _e( 'Reccurring Referrals', 'uap' );?>:
                                                    <?php if ( !empty( $data['current_rank_data']['reccuring_amount_value'] ) && $data['current_rank_data']['reccuring_amount_value'] > -1.00 ):?>
                                                        <?php if ( $data['current_rank_data']['reccuring_amount_type'] == 'percentage' ):?>
                                                            <?php echo $data['current_rank_data']['reccuring_amount_value'];?>%
                                                        <?php else :?>
                                                            <?php echo uap_format_price_and_currency( $data['currency'], $data['current_rank_data']['reccuring_amount_value'] );?>
                                                    <?php endif;?>

                                                    <?php else :?>
                                                        <?php if ( $data['current_rank_data']['amount_type'] == 'percentage' ):?>
                                                            <?php echo $data['current_rank_data']['amount_value'];?>%
                                                        <?php else :?>
                                                            <?php echo uap_format_price_and_currency( $data['currency'], $data['current_rank_data']['amount_value'] );?>
                                                        <?php endif;?>
                                                        <?php _e( '(Rank amount)', 'uap' );?>
                                                    <?php endif;?>

                                                </li>
                                            <?php endif;?>

                                            <?php if ( $data['pay_per_click'] && !empty( $data['current_rank_data']['pay_per_click'] ) && $data['current_rank_data']['pay_per_click'] > -1.00 ):?>
                                                <li><?php _e( 'PPC Amount', 'uap' );?>: <?php echo uap_format_price_and_currency( $data['currency'], $data['current_rank_data']['pay_per_click'] );?></li>
                                            <?php endif;?>

                                            <?php if ( $data['cpm_commission'] && !empty( $data['current_rank_data']['cpm_commission'] ) && $data['current_rank_data']['cpm_commission'] > -1.00 ):?>
                                                <li><?php _e( 'CPM Amount', 'uap' );?>: <?php echo uap_format_price_and_currency( $data['currency'], $data['current_rank_data']['cpm_commission'] );?></li>
                                            <?php endif;?>

                                        </ul>
                                    </div>
                                </div>

                                <?php if ( $data['next_rank_data'] ):?>

                                <div class="uap-achievenextrank">
                                        	<?php _e( 'Next Rank', 'uap' );?>
                                            <div class="uap-achievement-condition">
                                            <div><?php _e('Achievement condition', 'uap');?></div>
                                            <?php
                                            $achieve_types= array(-1=>'...', 'referrals_number'=>'Number of Referrals', 'total_amount'=>'Total Amount');
;                                           $achieve = json_decode( $data['next_rank_data']['achieve'], TRUE);
                                            if ($achieve):
                                            for ($i=1; $i<=$achieve['i']; $i++):?>
                                              <div class="uap-admin-listing-ranks-achieve">
                                                <div><strong><?php echo $achieve_types[$achieve['type_' . $i]];?></strong></div>
                                                <div><?php echo __('From: ', 'uap');
                                                  if ($achieve['type_' . $i]=='total_amount'){
                                                    echo uap_format_price_and_currency( $data['currency'], $achieve['value_' . $i] );
                                                  } else {
                                                      echo $achieve['value_' . $i] . '';
                                                  }
                                                  ?></div>
                                              </div>
                                            <?php
                                              endfor;
                                            else:
                                              ?>
                                              <div class="uap-admin-listing-ranks-achieve">
                                                <?php _e('None', 'uap');?>
                                              </div>
                                              <?php
                                            endif;
                                            ?>
                                          </div>
                                        </div>
                                <div class="col-xs-2">

                                </div>
                                <div class="col-xs-5">
                                    <div class="uap-userprofile-rank uap-nextrank">
                                    	<div class="uap-userprofile-rank-title"><?php echo $data['next_rank_data']['label'];?>
                                        (<?php if ( $data['next_rank_data']['amount_type'] == 'percentage' ):?><?php echo $data['next_rank_data']['amount_value'];?>%
                                        <?php else :?><?php echo uap_format_price_and_currency( $data['currency'], $data['next_rank_data']['amount_value'] );?>
                                            <?php endif;?>
											  <?php _e( 'reward', 'uap' );?>)
                                        </div>
                                        <ul>

                                          <?php if ( $data['bonus_enabled'] && !empty( $data['next_rank_data']['bonus'] ) ):?>
                                              <li><?php _e( 'Achievement Bonus', 'uap' );?>: <?php echo uap_format_price_and_currency( $data['currency'], $data['next_rank_data']['bonus'] );?></li>
                                          <?php endif;?>

                                          <?php if ( $data['sign_up_enabled'] ):?>
                                              <?php if ( !empty( $data['next_rank_data']['sign_up_amount_value'] ) && $data['next_rank_data']['sign_up_amount_value'] > -1.00 ):?>
                                                  <li><?php _e( 'SignUp Referrals', 'uap' );?>: <?php echo uap_format_price_and_currency( $data['currency'], $data['next_rank_data']['sign_up_amount_value'] );?></li>
                                              <?php else :?>
                                                  <li><?php _e( 'SignUp Referrals', 'uap' );?>: <?php echo uap_format_price_and_currency( $data['currency'], $data['next_rank_data']['amount_value'] );?></li>
                                              <?php endif;?>
                                          <?php endif;?>

                                          <?php if ( $data['lifetime_commission'] ):?>
                                              <li>

                                                  <?php _e( 'LifeTime Comission', 'uap' );?>:
                                                  <?php if ( !empty( $data['next_rank_data']['lifetime_amount_value'] ) && $data['next_rank_data']['lifetime_amount_value'] > -1.00 ):?>
                                                      <?php if ( $data['next_rank_data']['lifetime_amount_type'] == 'percentage' ):?>
                                                          <?php echo $data['next_rank_data']['lifetime_amount_value'];?>%
                                                      <?php else :?>
                                                          <?php echo uap_format_price_and_currency( $data['currency'], $data['next_rank_data']['lifetime_amount_value'] );?>
                                                      <?php endif;?>
                                                  <?php else :?>
                                                      <?php if ( $data['next_rank_data']['amount_type'] == 'percentage' ):?>
                                                          <?php echo $data['next_rank_data']['amount_value'];?>%
                                                      <?php else :?>
                                                          <?php echo uap_format_price_and_currency( $data['currency'], $data['next_rank_data']['amount_value'] );?>
                                                      <?php endif;?>
                                                      <?php _e( '(Rank amount)', 'uap' );?>
                                                  <?php endif;?>

                                              </li>
                                          <?php endif;?>

                                          <?php if ( $data['reccuring_referrals'] ):?>
                                              <li>
                                                  <?php _e( 'Reccurring Referrals', 'uap' );?>:
                                                  <?php if ( !empty( $data['next_rank_data']['reccuring_amount_value'] ) && $data['current_rank_data']['reccuring_amount_value'] > -1.00 ):?>
                                                      <?php if ( $data['next_rank_data']['reccuring_amount_type'] == 'percentage' ):?>
                                                          <?php echo $data['next_rank_data']['reccuring_amount_value'];?>%
                                                      <?php else :?>
                                                          <?php echo uap_format_price_and_currency( $data['currency'], $data['next_rank_data']['reccuring_amount_value'] );?>
                                                      <?php endif;?>
                                                  <?php else :?>
                                                      <?php if ( $data['next_rank_data']['amount_type'] == 'percentage' ):?>
                                                          <?php echo $data['next_rank_data']['amount_value'];?>%
                                                      <?php else :?>
                                                          <?php echo uap_format_price_and_currency( $data['currency'], $data['next_rank_data']['amount_value'] );?>
                                                      <?php endif;?>
                                                      <?php _e( '(Rank amount)', 'uap' );?>
                                                  <?php endif;?>
                                              </li>
                                          <?php endif;?>

                                          <?php if ( $data['pay_per_click'] && !empty( $data['next_rank_data']['pay_per_click'] ) && $data['next_rank_data']['pay_per_click'] > -1.00 ):?>
                                              <li><?php _e( 'PPC Amount', 'uap' );?>: <?php echo uap_format_price_and_currency( $data['currency'], $data['next_rank_data']['pay_per_click'] );?></li>
                                          <?php endif;?>

                                          <?php if ( $data['cpm_commission'] && !empty( $data['next_rank_data']['cpm_commission'] ) && $data['next_rank_data']['cpm_commission'] > -1.00 ):?>
                                              <li><?php _e( 'CPM Amount', 'uap' );?>: <?php echo uap_format_price_and_currency( $data['currency'], $data['next_rank_data']['cpm_commission'] );?></li>
                                          <?php endif;?>


                                        </ul>
                                    </div>

                                </div>
                                <?php endif;?>

                            </div>
                           </div>
                         </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                	<div class="col-xs-8">
                    	<div class="uap-userprofile-profiledetails">
                        	<div class="uap-userprofile-sectiontitle"><?php _e( 'Profile details', 'uap' );?></div>
                            <div class="row">
                            <div class="col-xs-3">
                                  <div class="uap-userprofile-avatar">
                                  <?php if ( !empty( $data['avatar'] ) ):?>
                                      <img src="<?php echo $data['avatar'];?>" class="uap-avatar" />
                                  <?php endif;?>
                                  </div>
                            </div>
                            <div class="col-xs-9">
                        	<table class="form-table">
                            	<tbody>
                                	<tr class="form-field">
                                    	<th><?php _e( 'Username', 'uap' );?>:</th>
                                        <td> <?php echo $data['user_name'];?></td>
                                    </tr>
                                    <tr class="form-field">
                                    	<th><?php _e( 'Email', 'uap' );?>:</th>
                                        <td> <?php echo $data['user_email'];?></td>
                                    </tr>
                                    <tr class="form-field">
                                    	<th><?php _e( 'First Name', 'uap' );?>:</th>
                                        <td> <?php echo $data['user_meta']['first_name'];?></td>
                                    </tr>
                                    <tr class="form-field">
                                    	<th><?php _e( 'Last Name', 'uap' );?>:</th>
                                        <td> <?php echo $data['user_meta']['last_name'];?></td>
                                    </tr>
                                    <tr class="form-field">
                                    	<th><?php _e( 'WP Role', 'uap' );?>:</th>
                                        <td> <?php echo $data['role'];?></td>
                                    </tr>
                                    <tr class="form-field">
                                    	<th><?php _e( 'Member Since', 'uap' );?>:</th>
                                        <td> <?php echo $data['member_since'];?></td>
                                    </tr>
                                    <tr class="form-field">
                                    	<th><?php _e( 'Nickname', 'uap' );?>:</th>
                                        <td> <?php echo $data['user_meta']['nickname'];?></td>
                                    </tr>
                                    <tr class="form-field">
                                    	<th><?php _e( 'Biographical Info', 'uap' );?>:</th>
                                        <td> <?php echo $data['description'];?></td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-4">
                    	<div class="uap-userprofile-sectiontitle"><?php _e( 'Overall Perfromance', 'uap' );?></div>
                         <div class="uap-userprofile-stats">
                         		<table class="form-table">
                            	<tbody>
                                	<tr class="form-field">
                                    	<th>
                                        	<div><?php _e( 'Referrals', 'uap' );?></div>
                                          <?php $totalAmount = $data['referrals_stats']['verified_referrals_amount'] + $data['referrals_stats']['unverified_referrals_amount'];?>
                                        	<div> <a href="<?php echo admin_url( 'admin.php?page=ultimate_affiliates_pro&tab=referrals&affiliate_id=' . $data['affiliate_id'] );?>" target="_blank"><?php echo $data['stats']['referrals'];?> (<?php echo uap_format_price_and_currency( $data['currency'], $totalAmount );?>)</a></div>
                                        </th>
                                        <td>
                                        	<div><?php _e( 'Verified:', 'uap' );?>: <?php echo $data['referrals_stats']['verified_referrals'];?> (<?php echo uap_format_price_and_currency( $data['currency'], $data['referrals_stats']['verified_referrals_amount'] );?>)</div>
                                        	<div><?php _e( 'UnVerified:', 'uap' );?>: <?php echo $data['referrals_stats']['unverified_referrals'];?> (<?php echo uap_format_price_and_currency( $data['currency'], $data['referrals_stats']['unverified_referrals_amount'] );?>)</div>
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <?php $totalEarnings = $data['stats']['total_paid']  + $data['stats']['total_unpaid'];?>
                                        <th>
                                        	<div><?php _e( 'Earnings', 'uap' );?></div>
                                        	<div> <?php echo uap_format_price_and_currency( $data['currency'], $totalEarnings );?></div>
                                        </th>
                                        <td>
                                        	<div><?php _e( 'Paid:', 'uap' );?>: <a href="<?php echo admin_url( 'admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=paid_referrals&affiliate=' . $data['affiliate_id'] );?>" target="_blank"><?php echo uap_format_price_and_currency( $data['currency'], $data['stats']['total_paid'] );?></a></div>
                                        	<div><?php _e( 'Unpaid:', 'uap' );?>: <a href="<?php echo admin_url( 'admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=unpaid&affiliate=' . $data['affiliate_id'] );?>" target="_blank"><?php echo uap_format_price_and_currency( $data['currency'], $data['stats']['total_unpaid'] );?></a></div>
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                    	<th>
                                        	<div><?php _e( 'Visits', 'uap' );?></div>
                                        	<div> <a href="<?php echo admin_url( 'admin.php?page=ultimate_affiliates_pro&tab=visits&affiliate_id=' . $data['affiliate_id'] );?>" target="_blank"><?php echo $data['stats']['visits'];?></a></div>
                                        </th>
                                        <td>
                                        	<div><?php _e( 'Conversion:', 'uap' );?>: <?php echo $data['stats']['conversions'];?></div>
                                        	<div><?php _e( 'Success Rate:', 'uap' );?>: <?php echo $data['stats']['success_rate'];?>%</div>
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                    	<th>
                                        	<div><?php _e( 'Payout', 'uap' );?></div>
                                        	<div> <?php echo uap_format_price_and_currency( $data['currency'], $data['payment_stats']['paid_payments_value'] );?></div>
                                        </th>
                                        <td>
                                        	<div><?php _e( 'Completed Transactions:', 'uap' );?>: <?php echo $data['count_payments_completed'];?></div>
                                        	<div><?php _e( 'Pending Transactions:', 'uap' );?>: <?php echo $data['count_payments_pending'];?></div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                         </div>

                         <?php if ( !empty( $data['statsForLast30'] ) ):?>
                            <div class="uap-userprofile-chart-wrapper" ><canvas id="chart-1" ></canvas></div>
                         <?php endif;?>
                    </div>
                </div>
                <div class="row" style="padding-top:25px;">

                	<div class="col-xs-8">
                  	<div class="uap-userprofile-sectiontitle"><?php _e( 'Payout Details', 'uap' );?></div>
                  	<table class="form-table">
                      <tbody>
                          <tr class="form-field">
                              <th><?php _e( 'Payout Method', 'uap' );?>:</th>
                              <td>
                                  <?php switch( $data['payments_settings']['uap_affiliate_payment_type'] ){
                                          case 'bt':?>
                                          <?php _e( 'Bank Transfer', 'uap' );?>
                                        <?php break;
                                          case 'paypal':?>
                                          <?php _e( 'PayPal', 'uap' );?>
                                        <?php break;
                                          case 'stripe':?>
                                          <?php _e( 'Stripe', 'uap' );?>
                                        <?php break;
                                          case 'stripe_v2':?>
                                            <?php _e( 'Stripe V2', 'uap' );?>
                                        <?php break;?>
                                        <?php case 'stripe_v3':?>
                                        <?php _e( 'Stripe V3', 'uap' );?>
                                      <?php break;?>
                                  <?php }?>
                              </td>
                          </tr>
                          <tr class="form-field">
                              <?php switch( $data['payments_settings']['uap_affiliate_payment_type'] ){
                                      case 'bt':?>
                                      <th><?php _e( 'Account:', 'uap' );?></th>
                                      <td><?php echo $data['payments_settings']['uap_affiliate_bank_transfer_data'];?></td>
                                    <?php break;
                                      case 'paypal':?>
                                      <th><?php _e( 'E-mail address:', 'uap' );?></th>
                                      <td><?php echo $data['payments_settings']['uap_affiliate_paypal_email'];?></td>
                                    <?php break;
                                      case 'stripe':?>
                                      <th><?php _e( 'Stripe Name:', 'uap' );?></th>
                                      <td><?php echo $data['payments_settings']['uap_affiliate_stripe_name'];?></td>
                                      </tr>
                                      <tr class="form-field">
                                        <th><?php _e( 'Card number:', 'uap' );?></th>
                                        <td><?php echo $data['payments_settings']['uap_affiliate_stripe_card_number'];?></td>
                                      </tr>
                                      <tr class="form-field">
                                        <th><?php _e( 'Expiration month:', 'uap' );?></th>
                                        <td><?php echo $data['payments_settings']['uap_affiliate_stripe_expiration_month'];?></td>
                                      </tr>
                                      <tr class="form-field">
                                        <th><?php _e( 'Expiration year:', 'uap' );?></th>
                                        <td><?php echo $data['payments_settings']['uap_affiliate_stripe_expiration_year'];?></td>
                                      </tr>
                                      <tr class="form-field">
                                        <th><?php _e( 'Card type:', 'uap' );?></th>
                                        <td><?php echo $data['payments_settings']['uap_affiliate_stripe_card_type'];?></td>
                                    <?php break;?>
                                    <?php case 'stripe_v2':?>
                                      <th></th>
                                      <td></td>
                                    <?php break;?>
                                    <?php case 'stripe_v3':
                                        ?>
                                        <th><?php _e( 'Stripe Account:', 'uap' );?></th>
                                        <td>
                                        <?php
                                        $accountId = get_user_meta( $data['uid'], 'uap_stripe_v3_user_account_id', true );
                            						if ( $accountId != false && $accountId != '' ):?>
                            							<div class="uap-payment-details-do-payment">
                            									<a href="https://dashboard.stripe.com/test/connect/accounts/<?php echo $accountId;?>" target="_blank"><?php
                            									_e( 'View Stripe Affiliate Account', 'uap');
                            							?></a></div>
                            					<?php else :?>
                            						<div class="uap-payment-details-do-payment"><?php _e('Incomplete Payment Settings', 'uap');?></div>
                                      <?php endif;?>
                                      </td>
                                    <?php break;?>
                              <?php }?>
                          </tr>
                      </tbody>
                    </table>
                  </div>


                    <?php if ( !empty( $data['campaigns'] ) ):?>
                      <div class="col-xs-4">
                    	<div class="uap-userprofile-sectiontitle"><?php _e( 'Campaigns', 'uap' );?></div>
                        <table class="form-table">
                          <tbody>
                              <?php foreach ( $data['campaigns'] as $campaignObject ):?>
                                  <tr class="form-field">
                                      <th width="40%" style="text-align:center;"><?php echo $campaignObject->name;?></th>
                                      <td width="20%"><?php _e('Visists: ', 'uap');?><?php echo $campaignObject->visit_count;?></td>
                                      <td width="20%"><?php _e('Unique visits: ', 'uap');?><?php echo $campaignObject->unique_visits_count;?></td>
                                      <td width="20%"><?php _e('Referrals: ', 'uap');?><?php echo $campaignObject->referrals;?></td>
                                  </tr>
                              <?php endforeach;?>
                          </tbody>
                        </table>
                	  </div>
                    <?php endif;?>
                </div>
 				<div class="row uap-userprofile-specialrow" style="padding-top:25px;">

                  <?php if ( $data['coupons'] ):?>
                      <div class="col-xs-4 uap-userprofile-box">
                          <div class="uap-userprofile-sectiontitle"><?php _e( 'Assigned Coupons', 'uap' );?> <span>(<a href="<?php echo admin_url( 'admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=coupons' );?>" target="_blank"><?php _e( 'check settings', 'uap' );?></a>)</span></div>
                          <ul class="uap-userprofile-list">
                              <?php foreach ( $data['coupons'] as $couponData ):?>
                                  <?php $couponSettings = unserialize( $couponData['settings'] );?>
                                  <li><?php echo $couponData['code'];?>
                                      <?php if ( isset( $couponSettings['amount_type'] ) && $couponSettings['amount_type'] == 'percentage' ):?>
                                          (<?php echo $couponSettings['amount_value'];?>%) -
                                      <?php else :?>
                                          (<?php echo uap_format_price_and_currency( $data['currency'], $couponSettings['amount_value'] );?>) -
                                      <?php endif;?>
                                  <a href="<?php echo admin_url( 'admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=coupons&add_edit=' . $couponData['code'] );?>" target="_blank"><?php _e( 'Edit', 'uap' );?></a> |
                                  <span class='uap-js-delete-coupons-link uap-delete-span' data-id='<?php echo $couponData['id'];?>' ><?php _e( 'Delete', 'uap' );?></span>
                                  </li>
                              <?php endforeach;?>
                              <li><a href="<?php echo admin_url( 'admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=coupons&add_edit=0' );?>" target="_blank"><?php _e( 'Add New', 'uap' );?></a></li>
                          </ul>
                      </div>
                  <?php endif;?>

                  <?php if ( $data['referrer_links'] ):?>
                      <div class="col-xs-4 uap-userprofile-box">
                            	<div class="uap-userprofile-sectiontitle"><?php _e( 'Referrer Links', 'uap' );?> <span>(<a href="<?php echo admin_url( 'admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=simple_links' );?>" target="_blank"><?php _e( 'check settings', 'uap' );?></a>)</span></div>
                              <ul class="uap-userprofile-list">
                                <?php foreach ( $data['referrer_links'] as $referrerLinks ):?>
                                     <li><a href="<?php echo $referrerLinks['url'];?>" target="_blank"><?php echo $referrerLinks['url'];?></a> - <span class='uap-js-delete-referrals-link uap-delete-span' data-id='<?php echo $referrerLinks['id'];?>' ><?php _e( 'Delete', 'uap' );?></span></li>
                                <?php endforeach;?>
                              </ul>
                      </div>
                  <?php endif;?>

                  <?php if ( $data['landing_pages'] ):?>
                      <div class="col-xs-4 uap-userprofile-box">
                          	<div class="uap-userprofile-sectiontitle"><?php _e( 'Landing Page', 'uap' );?> <span>(<a href="<?php echo admin_url( 'admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=landing_pages' );?>" target="_blank"><?php _e( 'check settings', 'uap' );?></a>)</span></div>
                            <ul class="uap-userprofile-list">
                              <?php foreach ( $data['landing_pages'] as $landingPage ):?>
                            	     <li><a href="<?php echo get_permalink($landingPage->ID);?>" target="_blank"><?php echo $landingPage->post_title;?></a> - <a href="<?php echo admin_url('post.php?post='.$landingPage->ID.'&action=edit');?>" target="_blank"><?php _e( 'Edit', 'uap' );?></a> |
                                        <span class='uap-js-delete-landing-page uap-delete-span' data-id='<?php echo $landingPage->ID;?>' ><?php _e( 'Delete', 'uap' );?></span>
                                   </li>
                              <?php endforeach;?>
                            </ul>
                      </div>
                  <?php endif;?>

          </div>


			


            <?php endif;?>
        </div>
    </div>

    <?php if ( $data['mlm'] ):?>
        <?php echo $data['mlm'];?>
    <?php endif;?>
</div>


<script>
jQuery(document).ready(function(){
    jQuery('.uap-js-delete-referrals-link').on( 'click', function(){
        jQuery.ajax({
            type : 'post',
            url : window.uap_url + '/wp-admin/admin-ajax.php',
            data : {
                       action: 'uap_delete_referrer_link_for_affiliate',
                       id: jQuery(this).attr('data-id'),
                   },
            success: function (response) {
                location.reload();
            }
       });
    });
    jQuery('.uap-js-delete-landing-page').on( 'click', function(){
        jQuery.ajax({
            type : 'post',
            url : window.uap_url + '/wp-admin/admin-ajax.php',
            data : {
                       action: 'uap_delete_landing_page_for_affiliate',
                       id: jQuery(this).attr('data-id'),
                   },
            success: function (response) {
                location.reload();
            }
       });
    });
    jQuery('.uap-js-delete-coupons-link').on( 'click', function(){
        jQuery.ajax({
            type : 'post',
            url : window.uap_url + '/wp-admin/admin-ajax.php',
            data : {
                       action: 'uap_delete_coupons_for_affiliate',
                       id: jQuery(this).attr('data-id'),
                   },
            success: function (response) {
                location.reload();
            }
       });
    });

    jQuery('.js-uap-copy').on( 'click', function(){
        var url = jQuery( '.uap-userprofile-links a' ).attr( 'href' );
        const el = document.createElement('textarea');
        el.value = url;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        swal({
          title: '<?php _e( 'Copied to clipboard.', 'uap' );?>',
          text: "",
          type: "success",
          showConfirmButton: false,
          confirmButtonClass: "btn-success",
          confirmButtonText: "OK",
          timer: 1000
        });
    });


});
</script>

<?php if ( !empty( $data['statsForLast30'] ) ):?>
<?php wp_enqueue_script( 'uap-moment.js', UAP_URL . 'assets/js/moment.min.js', [], false );?>
<?php wp_enqueue_script( 'uap-chart.js', UAP_URL . 'assets/js/chart.min.js', [], false );?>

<script>
var uapDates = [<?php foreach( $data['statsForLast30'] as $date => $referrals ) { $date = uap_convert_date_to_us_format($date);echo "'$date', "; }?>];

jQuery(document).ready(function(){

  var options = {
    maintainAspectRatio: false,
    spanGaps: false,
    elements: {
      line: {
        tension: 0.01
      }
    },
    plugins: {
      filler: {
        propagate: false
      }
    },
    scales: {
      x: {
        ticks: {
          autoSkip: false,
          maxRotation: 0
        },
      },
      xAxes: [ { gridLines: { display: false } } ],
      yAxes: [{
             ticks: {
                 beginAtZero: true,
                 userCallback: function(label, index, labels) {
                     // when the floored value is the same as the value we have a whole number
                     if (Math.floor(label) === label) {
                         return label;
                     }

                 },
             }
      }],
      scaleShowVerticalLines: false,
    }
  };
  new Chart('chart-1', {
    type    : 'line',
    data    : {
      labels    : [<?php
                      $i=1;
                      foreach( $data['statsForLast30'] as $date => $referralNumber ) {
                          if ( $i%2==0 )continue;
                          $temporaryDate = explode( '-', $date);
                          $day = isset($temporaryDate[2]) ? $temporaryDate[2] : $date;
                          echo "'$day'" . ",";
                      }?>],
      datasets  : [{
        backgroundColor   : '#edf2f6',
        borderColor       : '#799edb',
        data              : [<?php foreach( $data['statsForLast30'] as $date => $referralNumber ) echo $referralNumber . ",";?>], //referrals counts
        label             : '<?php _e('Referrals', 'uap');?>',
        fill              : 'start'
      }]
    },
    options : Chart.helpers.merge(options, {
              legend: {
                    display: false
              },
              title: {
                text    : '<?php _e( 'Referrals for the last 30 days.', 'uap');?>',
                display : true
              },
              tooltips: {
                    intersect		: false,
                    position		: 'nearest',
                    callbacks: {
                      title: function(tooltipItem, data) {
                        string = '';
                        window.uapDates.forEach(function( val, i ){
                            if ( i == tooltipItem[0].index ){
                              string = val;
                            }
                        });
                        return string;
                      },
                    }
                }
    })
  });


});
</script>

<?php endif;?>
