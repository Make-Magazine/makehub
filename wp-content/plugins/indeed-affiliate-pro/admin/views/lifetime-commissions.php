			<form action="" method="post">
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('LifeTime Commissions', 'uap');?><span class="uap-admin-need-help"><i class="fa-uap fa-help-uap"></i><a href="https://help.wpindeed.com/ultimate-affiliate-pro/knowledge-base/lifetime-commissions/" target="_blank"><?php _e('Need Help?', 'uap');?></a></span></h3>
					<div class="inside">
						<div class="row">
						<div class="col-xs-5">
							<h3><?php _e('Activate/Hold LifeTime Commissions', 'uap');?></h3>
							<p><?php _e('You can activate this option to take place in your affiliate system.', 'uap');?></p>
							<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($data['metas']['uap_lifetime_commissions_enable']) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uapCheckAndH(this, '#uap_lifetime_commissions_enable');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="uap_lifetime_commissions_enable" value="<?php echo $data['metas']['uap_lifetime_commissions_enable'];?>" id="uap_lifetime_commissions_enable" />
						</div>
						</div>
						<div class="uap-line-break"></div>
						<div class="row">
						<div class="col-xs-8">

						<?php if (!empty($data['rank_list'])) :?>

							<h3><?php _e('LifeTime Amount For Each Rank', 'uap');?></h3>
							<p><?php _e('Set a special lifetime amount for each rank that will replace the default amount rank. This option will also become available in the "Rank Settings" page.', 'uap');?></p>
							<table class="uap-dashboard-inside-table">
								<tr>
									<th><?php _e('Rank Name', 'uap');?></th>
									<th><?php _e('Default Amount Rank', 'uap');?></th>
									<th><?php _e('LifeTime Amount', 'uap');?></th>
								</tr>
								<?php foreach ($data['rank_list'] as $id=>$label) :?>
									<tr>
										<td><?php echo $label;?></td>
										<td><?php echo $data['default_rank_amount_value_array'][$id] . ' ' . $data['amount_types'][$data['default_rank_amount_type_array'][$id]];?></td>
										<td>
											<?php $value = ($data['rank_amount_value_array'][$id]>-1) ? $data['rank_amount_value_array'][$id] : '';?>
											<input type="number" min="0" step='<?php echo uapInputNumerStep();?>' class="uap-input-number" value="<?php echo $value;?>" name="<?php echo "lifetime_ranks_value[$id]";?>" />
											<select name="<?php echo "lifetime_ranks_amount_type[$id]";?>"><?php
												foreach ($data['amount_types'] as $k=>$v):
													$selected = ($data['rank_amount_type_array'][$id]==$k) ? 'selected' : '';
													?>
													<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
													<?php
												endforeach;
											?></select>
										</td>
									</tr>
							<?php endforeach;?>
							</table>
							<p>
								<?php
								$offerType = get_option( 'uap_referral_offer_type' );
								if ( $offerType == 'biggest' ){
										$offerType = __( 'Biggest', 'uap' );
								} else {
										$offerType = __( 'Lowest', 'uap' );
								}
								echo __( 'If there are multiple Amounts set for the same action, like Ranks, Offers, Product or Category rate the ', 'uap' ) . '<strong>' . $offerType . '</strong> ' . __( 'will be taken in consideration. You may change that from', 'uap' ) . ' <a href="' . admin_url( 'admin.php?page=ultimate_affiliates_pro&tab=settings' ) . '" target="_blank">' . __( 'here.', 'uap' ) . '</a>';
								?>
							</p>
						<?php endif;?>
						</div>
						</div>
						<div class="uap-submit-form">
							<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>
					</div>
				</div>
			</form>


				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('Search Lifetime User-Affiliates', 'uap');?></h3>
					<div class="inside">
						<form action="" method="post">
							<div>
								<?php _e('Affiliate Username', 'uap');?> <input type="text" name="affiliate_username" value="<?php echo @$_POST['affiliate_username'];?>"/>
								<?php _e('Referrer Username', 'uap');?> <input type="text" name="username" value="<?php echo @$_POST['username'];?>" />
								<input type="submit" value="<?php _e('Search', 'uap');?>" name="search" class="button button-primary button-large" />
							</div>
						</form>
						<?php if (!empty($data['affiliate_referrals_table_data'])):?>
							<table class="uap-dashboard-inside-table">
								<tr>
									<th><?php _e('Affiliate Username', 'uap');?></th>
									<th><?php _e('Referral Username', 'uap');?></th>
									<th><?php _e('Actions', 'uap');?></th>
								</tr>
								<?php foreach ($data['affiliate_referrals_table_data'] as $id=>$item) : ?>
									<tr>
										<td><?php echo $item['affiliate_username'];?></td>
										<td><?php echo $item['referral_username'];?></td>
										<td><a href="<?php echo $data['edit_relation'] . '&id=' . $id;?>"><?php _e('Edit', 'uap');?></a> | <a href="<?php echo $data['current_url'] . '&delete=' . $id;?>"><?php _e('Delete', 'uap');?></a></td>
									</tr>
								<?php endforeach;?>
							</table>
						<?php endif;?>
					</div>
				</div>


				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('LifeTime Commissions - Relations', 'uap');?></h3>
						<div class="inside">
							<p><?php _e('You may assign a potential client as a referrer with an affiliate such that, affiliate will receive a lifetime commission based on referrer actions.', 'uap'); ?></p>
							<div><a href="<?php echo admin_url( 'admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=add_new_affiliate_referral_relation' );?>" class="uap-add-new-like-wp"><i class="fa-uap fa-add-uap"></i><span><?php _e( 'Add New Relation', 'uap' );?></span></a></div>
						</div>

				</div>
