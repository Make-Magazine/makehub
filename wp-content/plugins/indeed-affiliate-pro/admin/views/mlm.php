			<form action="" method="post">
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('Multi-Level Marketing', 'uap');?><span class="uap-admin-need-help"><i class="fa-uap fa-help-uap"></i><a href="https://help.wpindeed.com/ultimate-affiliate-pro/knowledge-base/mlm-workflow-example/" target="_blank"><?php _e('Need Help?', 'uap');?></a></span></h3>
					<div class="inside">
					<div class="row">
						<div class="col-xs-7">
							<h3><?php _e('Activate/Hold Multi-Level Marketing', 'uap');?></h3>
							<p><?php _e('You can activate this option to take in place into your Affiliate system.', 'uap');?></p>
							<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($data['metas']['uap_mlm_enable']) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uapCheckAndH(this, '#uap_mlm_enable');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="uap_mlm_enable" value="<?php echo $data['metas']['uap_mlm_enable'];?>" id="uap_mlm_enable" />
						</div>
						</div>
						<div class="uap-line-break"></div>
						<div class="row">
							<div class="col-xs-5">
								<h3><?php _e('MLM Matrix', 'uap');?></h3>
								<p><?php _e('There are multiple ways the MLM system may work.', 'uap');?></p>
								<select name="uap_mlm_matrix_type" onChange="uapMatrixTypeCondition(this.value);"  class="form-control m-bot15"><?php
								foreach ($data['matrix_types'] as $k=>$v):
									$selected = ($data['metas']['uap_mlm_matrix_type']==$k) ? 'selected' : '';
									?>
									<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
									<?php
								endforeach;
							?></select>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-5">
								<h4><?php _e('Matrix Options', 'uap');?></h4>
								<?php $display = ($data['metas']['uap_mlm_matrix_type']=='unilevel') ? 'none' : 'table';?>
								<div class="input-group" id="children_limit_div" style="display: <?php echo $display;?>;">
									<span class="input-group-addon" id="basic-addon1"><?php _e('Children Limit', 'uap');?></span>
									<input type="number" class="form-control" id="uap_mlm_child_limit" name="uap_mlm_child_limit" value="<?php echo $data['metas']['uap_mlm_child_limit'];?>" min="1" />
								</div>
								<div class="input-group" style="margin:30px 0 15px 0;">
									<span class="input-group-addon" id="basic-addon1"><?php _e('Matrix Depth(Levels)', 'uap');?></span>
									<input type="number" class="form-control" name="uap_mlm_matrix_depth" onChange="uapMlmUpdateTbl(this.value);" value="<?php echo $data['metas']['uap_mlm_matrix_depth'];?>" min="1"/>
								</div>
							</div>
						</div>
						<div class="uap-line-break"  style="display:none;"></div>
						<div class="row" style="display:none;">
							<div class="col-xs-10">
								<h3><?php _e('MLM Worflow Concept', 'uap');?></h3>
                                <p><?php _e('By default, a MLM system rewards the uplines affiliates based on the main affiliate referral amount, no matter the source of the Referral. An additional workflow is available and let you relate the MLM rewards directly to Product price/Order amount when the main Referral is based on a purchased from integrated Systems (WooCommerce, Ultimate Membership Pro, EDD)', 'uap');?></p>
								<div class="uap-form-line col-xs-6" style="padding-left: 0px;">
									<select name="uap_mlm_use_amount_from" id="uap_mlm_use_amount_from"  class="form-control m-bot15"><?php
										$types = array(
														'child_referral' => __('Child Referral amount', 'uap'),
														'product_price' => __('Product Price', 'uap'),
										);
										foreach ($types as $k=>$v):
											$selected = ($data['metas']['uap_mlm_use_amount_from']==$k) ? 'selected' : '';
											?>
												<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
											<?php
										endforeach;
									?></select>
								</div>
							</div>
						</div>

						<div class="uap-line-break"></div>
						<div class="row">
							<div class="col-xs-5">
								<h3><?php _e('Default Amount', 'uap');?></h3>
								<p><?php _e('Set the default amount that will be used when no special amount is set for a certain rank.', 'uap');?></p>
								<div class="uap-form-line">
									<div class="form-group">
									<input type="number" class="form-control" step="0.01" id="uap_mlm_default_amount_value" name="uap_mlm_default_amount_value" value="<?php echo $data['metas']['uap_mlm_default_amount_value'];?>" min="0.01"/>
									</div>
									<select name="uap_mlm_default_amount_type" id="uap_mlm_default_amount_type"><?php
										foreach ($data['amount_types'] as $k=>$v):
											$selected = ($data['metas']['uap_mlm_default_amount_type']==$k) ? 'selected' : '';
											?>
												<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
											<?php
										endforeach;
									?></select>
								</div>
							</div>
						</div>
						<div class="uap-line-break"></div>
						<div class="row">
							<div class="col-xs-6">

							<h3><?php _e('Amount For Each Level', 'uap');?></h3>
							<p><?php _e('Set a special MLM amount for each level of your ranks. This option will also become available in the "Rank Settings" page.', 'uap');?></p>

						<table class="uap-dashboard-inside-table" id="mlm-amount-for-each-level">
							<thead>
								<th><?php _e('#', 'uap');?></th>
								<th><?php _e('Value', 'uap');?></th>
							</thead>
							<?php
								for ($i=1; $i<=$data['metas']['uap_mlm_matrix_depth']; $i++):
									?>
									<tr data-tr="<?php echo $i;?>" id="uap_mlm_level_<?php echo $i;?>">
										<td><?php echo __('Level', 'uap') . ' ' . $i;?></td>
										<td>
											<input type="number" step="0.01" min="0" class="uap-input-number" value="<?php echo @$data['metas']['mlm_amount_value_per_level'][$i];?>" name="<?php echo "mlm_amount_value_per_level[$i]";?>" />
											<select name="<?php echo "mlm_amount_type_per_level[$i]";?>"><?php
												foreach ($data['amount_types'] as $k=>$v):
													$selected = (!empty($data['metas']['mlm_amount_type_per_level'][$i]) && $data['metas']['mlm_amount_type_per_level'][$i]==$k) ? 'selected' : '';
													?>
													<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
													<?php
												endforeach;
											?></select>
										</td>
									</tr>
									<?php
								endfor;
							?>
						</table>
						</div>
						</div>
						<div class="uap-submit-form">
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>
					</div>
				</div>
			</form>


						<!-- for js -->
						<table id="uap_mlm_model" style="display: none;">
							<tr data-tr="{{i}}" id="uap_mlm_level_{{i}}">
								<td><?php echo __('Level', 'uap') . ' {{i}}';?></td>
								<td>
									<input type="number" step="0.01" min="0" class="uap-input-number" value="" name="mlm_amount_value_per_level[{{i}}]" />
									<select name="<?php echo "mlm_amount_type_per_level[{{i}}]";?>"><?php
									foreach ($data['amount_types'] as $k=>$v):
										?>
										<option value="<?php echo $k;?>"><?php echo $v;?></option>
										<?php
									endforeach;
									?></select>
								</td>
							</tr>
						</table>
						<!-- /for js -->

		<div class="uap-stuffbox">
			<h3 class="uap-h3"><?php _e('Search MLM Relations', 'uap');?></h3>
			<div class="inside">
				<form action="<?php echo $data['search_submit_url'];?>" method="post">
					<div>
						<?php _e('Affiliate Username', 'uap');?> <input type="text" id="affiliate_name" name="affiliate_name"/>
						<input type="hidden" id="affiliate_id_hidden" name="search_aff_id" value=""/>
						<input type="submit" value="<?php _e('Search', 'uap');?>" name="search" class="button button-primary button-large" />
					</div>
				</form>
			</div>
		</div>

        <div class="uap-stuffbox">
			<h3 class="uap-h3"><?php _e('How it works?', 'uap');?></h3>
			<div class="inside">
            <?php _e('		<p>For a better understanding of how the MLM system works and provides rewards to affiliates, a common&nbsp;scenario is listed below:</p>
<h5><span id="mlm-settings"><strong>MLM Settings</strong></span></h5>
<p><strong>Matrix Depth(Levels)</strong> : <span style="color: #000000;"><strong>3</strong></span></p>
<p><strong>Amount For Each Level</strong></p>
<ul>
<li>Level 1: 20%</li>
<li>Level 2: 10%</li>
<li>Level 3: 5%</li>
</ul>
<h5><span id="john-mlm-matrix-sub-affiliates"><span style="color: #4e6e9c;"><strong>John</strong></span> MLM Matrix sub-affiliates:</span></h5>
<ul>
<li><span style="color: #cc5a5a;"><strong>Geo</strong></span>&nbsp;– Level 1</li>
<li><span style="color: #cc5a5a;"><strong>Bob</strong>&nbsp;–&nbsp;</span>Level 2</li>
<li><span style="color: #cc5a5a;"><strong>Max</strong>&nbsp;–&nbsp;</span>Level 3</li>
</ul>
<h5><span id="actions">Actions:</span></h5>
<p><strong>Max</strong> has Rank “Premium” with Amount: <strong>%25</strong><br>
<strong>Max</strong> refers a user that purchases a product of: <strong>$400</strong><br>
Rank Reward:<br>
<strong>Max</strong> gets a referral of: <strong>$100</strong></p>
<h5><span id="mlm-rewards">MLM Rewards:</span></h5>
<ul>
<li>Bob gets: $20 (20% of $100)</li>
<li>Geo gets: $10 (10% of $100)</li>
<li>John gets: $5 (5% of $100)</li>
</ul>

		'); ?>
            </div>
       	</div>

<script>
jQuery(function() {

	/// USERNAME SEARCH
	jQuery( "#affiliate_name" ).bind( "keydown", function( event ) {
		if ( event.keyCode === jQuery.ui.keyCode.TAB &&
			jQuery( this ).autocomplete( "instance" ).menu.active ) {
		 	event.preventDefault();
		}
	}).autocomplete({
		minLength: 0,
		source: '<?php echo UAP_URL . 'admin/Uap_Coupons_Ajax_Autocomplete.php';?>?users=true',
		focus: function() {},
		select: function( event, ui ) {
			var l = ui.item.label;
			jQuery('#affiliate_name').val(l);
		 	return false;
		}
	});

});

</script>
