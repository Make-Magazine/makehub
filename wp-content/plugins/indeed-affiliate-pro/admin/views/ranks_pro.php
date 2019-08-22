
<div class="uap-wrapper">
<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Ranks PRO options', 'uap');?></h3>
		<div class="inside">
        	<div class="uap-inside-item">
			<div class="row">
				<div class="col-xs-7">
				<h2><?php _e('Activate/Hold Ranks PRO', 'uap');?></h2>
				<p><?php _e('A dynamic way to update Ranks periodically based on affiliate performance for a more interactive workflow. Despite the default Rank achievement process, where an affiliate will keep the rank forever once is assigned, with Ranks Pro module, affiliates may be downgraded if their performances where slowed down.', 'uap');?></p>
				<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['uap_ranks_pro_enabled']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uapCheckAndH(this, '#uap_ranks_pro_enabled');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="uap_ranks_pro_enabled" value="<?php echo $data['metas']['uap_ranks_pro_enabled'];?>" id="uap_ranks_pro_enabled" />
				</div>
			</div>
            </div>
			<div class="uap-line-break"></div>
			<div class="uap-inside-item">
			<div class="row">
				<div class="col-xs-8">
				<h3><?php _e('Achievements Calculation', 'uap');?></h3>
                <p><?php _e('Ranks are calculated and assigned 2 times per day via Cron jobs or it can be manually triggered from Affiliates section.', 'uap');?></p>
                <p><?php _e('If is set an <strong>Unlimited</strong> period affiliates can receive a <strong>higher rank</strong> only if the achievements are accomplished.', 'uap');?></p>
				 <p><?php _e('For <strong>Limited</strong> calculation time, will be taken in consideration for achievement verification only Referrals from a specific period. Affiliates who did not achieved at least the current rank requirements may receive a <strong>lower rank</strong>.', 'uap');?></p>
            	</div>
            </div>
            </div>
            <div class="uap-inside-item">
			<div class="row">
				<div class="col-xs-4">
				<select name="uap_default_achieve_calculation" class="form-control m-bot15" onChange="uapHideDivIfValue( this.value, 'unlimited', '#uap_achieve_period_div_wrapp');"><?php
				$referral_format = array('unlimited' => 'Unlimited (default)', 'limited'=>'Limited back in Time');
				foreach ($referral_format as $k=>$v){
					$selected = ($data['metas']['uap_default_achieve_calculation']==$k) ? 'selected' : '';
					?>
					<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
					<?php
				}
				?></select>

				</div>
			</div>
			</div>

      <div class="uap-inside-item">
					<div class="row" id="uap_achieve_period_div_wrapp" <?php if ($data['metas']['uap_default_achieve_calculation']=='unlimited') echo "style='display: none;'";?> >
						<div class="col-xs-4">
							<div class="input-group">
								<span class="input-group-addon" id="basic-addon1"><?php _e('Period of :', 'uap');?></span>
								<input type="number" min="1" id="uap_achieve_period" class="form-control" value="<?php echo $data['metas']['uap_achieve_period'];?>" name="uap_achieve_period"/>
								<div class="input-group-addon"> <?php _e("days", 'uap');?></div>
							</div>
						</div>
					</div>
			</div>
            <div class="uap-line-break"></div>
			<div class="uap-inside-item">
				<div class="row">
				<div class="col-xs-7">
				<h2><?php _e('Reset Ranks', 'uap');?></h2>
				<p><?php _e('Reset all affiliates ranks to the Basic one monthly. Choose the desired date of the month', 'uap');?></p>
				<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['uap_ranks_pro_reset']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uapCheckAndH(this, '#uap_ranks_pro_reset');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="uap_ranks_pro_reset" value="<?php echo $data['metas']['uap_ranks_pro_reset'];?>" id="uap_ranks_pro_reset" />
				</div>
			</div>
			</div>
			<div class="uap-inside-item">
			<div class="row">
				<div class="col-xs-4">
				<div class="input-group">
					<span class="input-group-addon" id="basic-addon1"><?php _e('Reset on day', 'uap');?></span>
					<input type="number" min="1" max="30" class="form-control" value="<?php echo $data['metas']['uap_ranks_pro_reset_day'];?>" name="uap_ranks_pro_reset_day"/>
					<div class="input-group-addon"> <?php _e("of every month", 'uap');?></div>
				</div>

				</div>
			</div>
			</div>
			<div class="uap-submit-form" style="margin-top: 20px;">
				<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>

		</div>
	</div>

</form>



</div>
