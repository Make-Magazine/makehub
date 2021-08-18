<?php
	$subtab = isset( $_GET['subtab'] ) ? $_GET['subtab'] : 'manage';
?>
<div class="ihc-subtab-menu">
	<a class="ihc-subtab-menu-item <?php echo ( $subtab =='add_edit') ? 'ihc-subtab-selected' : '';?>" href="<?php echo $url.'&tab='.$tab.'&subtab=add_edit';?>"><?php esc_html_e('Add Single Coupon', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ( $subtab =='multiple_coupons') ? 'ihc-subtab-selected' : '';?>" href="<?php echo $url.'&tab='.$tab.'&subtab=multiple_coupons';?>"><?php esc_html_e('Add Bulk Coupons', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ( $subtab =='manage' ) ? 'ihc-subtab-selected' : '';?>" href="<?php echo $url.'&tab='.$tab.'&subtab=manage';?>"><?php esc_html_e('Manage Coupons', 'ihc');?></a>
	<div class="ihc-clear"></div>
</div>
<?php
	echo ihc_check_default_pages_set();//set default pages message
	echo ihc_check_payment_gateways();
	echo ihc_is_curl_enable();
	do_action( "ihc_admin_dashboard_after_top_menu" );


	if ($subtab=='manage'){
		/// save
		if (isset($_POST['ihc_bttn'])  && !empty($_POST['ihc_admin_coupons_nonce']) && wp_verify_nonce( $_POST['ihc_admin_coupons_nonce'], 'ihc_admin_coupons_nonce' ) ){
			if (empty($_POST['id'])){
				//create
				ihc_create_coupon($_POST);
			} else {
				//update
				ihc_update_coupon($_POST);
			}
		}
		///print the coupons
		$coupons = ihc_get_all_coupons();
		if ($coupons){
			$base_edit_url = $url.'&tab='.$tab.'&subtab=add_edit';
			foreach ($coupons as $id => $coupon){
				ihc_generate_coupon_box($id, $coupon, $base_edit_url);
			}
		} else {
			?>
			<a href="<?php echo $url.'&tab='.$tab.'&subtab=add_edit';?>" class="indeed-add-new-like-wp"><i class="fa-ihc fa-add-ihc"></i><?php esc_html_e("Add New Coupon", 'ihc');?></a>
			<div class="iump-page-title">Ultimate Membership Pro - <span class="second-text"><?php esc_html_e("MemberShip Coupons", 'ihc');?></span>
			</div>
			<div class="ihc-warning-message"><?php esc_html_e(" No Coupons available! Please create your first Coupon.", "ihc");?></div>
			<?php
		}
	} else {
		$meta_arr = ihc_get_coupon_by_id((isset($_GET['id'])) ? $_GET['id'] : 0);
		?>

			<div class="iump-page-title"><?php  esc_html_e("Coupons", 'ihc');?></div>
			<form method="post" action="<?php echo $url.'&tab='.$tab.'&subtab=manage';?>">

				<input type="hidden" name="ihc_admin_coupons_nonce" value="<?php echo wp_create_nonce( 'ihc_admin_coupons_nonce' );?>" />

				<div class="ihc-stuffbox">
					<?php if (!empty($_GET['id'])){?>
					<h3><?php esc_html_e("Edit", 'ihc');?></h3>
					<input type="hidden" name="id" value="<?php echo $_GET['id'];?>" />
					<?php } else { ?>
					<h3><?php esc_html_e("Add New", 'ihc');?></h3>
					<?php } ?>
					<div class="inside">
						<?php
							if ($subtab=='multiple_coupons'){
								//////////////// MULTIPLE COUPONS ////////////
								?>
								<div class="iump-form-line">
									<label class="iump-labels-special"><?php esc_html_e("Code prefix", 'ihc');?></label>
									<input type="text" value="" name="code_prefix" />
								</div>
								<div class="iump-form-line">
									<label class="iump-labels-special"><?php esc_html_e("Length", 'ihc');?></label>
									<input type="number" min="2" value="10" name="code_length" />
								</div>
								<div class="iump-form-line">
									<label class="iump-labels-special"><?php esc_html_e("Number of Codes", 'ihc');?></label>
									<input type="number" min="2" value="2" name="how_many_codes" />
								</div>
								<?php
							} else {
								/////////////// ONE /////////////
								?>
								<div class="iump-form-line">
									<label class="iump-labels-special"><?php esc_html_e("Code", 'ihc');?></label>
									<input type="text" value="<?php echo $meta_arr['code'];?>" name="code" id="ihc_the_coupon_code" /> <span class="ihc-generate-coupon-button" onClick="ihcGenerateCode('#ihc_the_coupon_code', 10);"><?php esc_html_e("Generate Code", "ihc");?></span>
								</div>
								<?php
							}
						?>

						<div class="iump-form-line">
							<label class="iump-labels-special"><?php esc_html_e("Description", 'ihc');?></label>
							<textarea name="description" class="ihc-coupon-description"><?php echo (isset($meta_arr['description'])) ? $meta_arr['description'] : '';?></textarea>
						</div>

						<div class="iump-form-line">
							<label class="iump-labels-special"><?php esc_html_e("Type of discount", 'ihc');?></label>
							<select name="discount_type" onChange="ihcDiscountType(this.value);"><?php
								$arr = array('price' => esc_html__("Price", 'ihc'), 'percentage'=>"Percentage (%)");
								foreach ($arr as $k=>$v){
									$selected = ($meta_arr['discount_type']==$k) ? 'selected' : '';
									?>
										<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
									<?php
								}
							?></select>
						</div>
						<div class="iump-form-line">
							<label class="iump-labels-special"><?php esc_html_e("Discount Value", 'ihc');?></label>
							<input type="number" step="0.01" value="<?php echo $meta_arr['discount_value'];?>" name="discount_value"/>

							<span id="discount_currency" class="<?php if ($meta_arr['discount_type']=='price'){
								 echo 'ihc-display-inline';
							}else{
								 echo 'ihc-display-none';
							}
							?>">
								<?php echo get_option('ihc_currency');?>
							</span>
							<span id="discount_percentage" class="<?php if ($meta_arr['discount_type']=='percentage'){
								 echo 'ihc-display-inline';
							}else{
								 echo 'ihc-display-none';
							}
							?>">%</span>
						</div>
						<div class="iump-form-line">
							<label class="iump-labels-special"><?php esc_html_e("Period Type", 'ihc');?></label>
							<select name="period_type" onChange="ihcSelectShDiv(this, '#the_date_range', 'date_range');"><?php
								$arr = array('date_range' => esc_html__("Date Range", 'ihc'), 'unlimited'=>esc_html__("Unlimited", 'ihc'));
								foreach ($arr as $k=>$v){
									$selected = ($meta_arr['period_type']==$k) ? 'selected' : '';
									?>
										<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
									<?php
								}
							?></select>
						</div>
						<div class="iump-form-line" id="the_date_range" class="<?php if ($meta_arr['period_type']=='date_range'){
							 echo 'ihc-display-block';
						}else{
							 echo 'ihc-display-none';
						}
						?>">
							<label class="iump-labels-special"><?php esc_html_e("Date Range", 'ihc');?></label>
							<input type="text" name="start_time" id="ihc_start_time" value="<?php echo $meta_arr['start_time'];?>" /> - <input type="text" name="end_time" id="ihc_end_time" value="<?php echo $meta_arr['end_time'];?>" />
						</div>
						<div class="iump-form-line">
							<label class="iump-labels-special"><?php esc_html_e("Repeat", 'ihc');?></label>
							<input type="number" value="<?php echo $meta_arr['repeat'];?>" name="repeat" min="1" />
						</div>
						<div class="iump-form-line">
							<label class="iump-labels-special"><?php esc_html_e("Target Membership", 'ihc');?></label>
							<select name="target_level"><?php
								$levels = \Indeed\Ihc\Db\Memberships::getAll();
								if ($levels && count($levels)){
									$levels_arr[-1] = esc_html__("All", 'ihc');
									foreach ($levels as $k=>$v){
										$levels_arr[$k] = $v['name'];
									}
								}
								foreach ($levels_arr as $k=>$v){
									$selected = ($meta_arr['target_level']==$k) ? 'selected' : '';
									?>
										<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
									<?php
								}
							?></select>
						</div>
						<div class="iump-form-line">
							<label class="iump-labels-special"><?php esc_html_e("On Subscriptions with Billing Recurrence apply the Discount:", 'ihc');?></label>
							<select name="reccuring"><?php
								$arr = array(0 => esc_html__("Just Once", 'ihc'), 1 => esc_html__("Forever", 'ihc'));
								foreach ($arr as $k=>$v){
									$selected = ($meta_arr['reccuring']==$k) ? 'selected' : '';
									?>
										<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
									<?php
								}
							?></select>
						</div>
						<input type="hidden" name="box_color" value="<?php echo $meta_arr['box_color'];?>" />
						<div class="ihc-wrapp-submit-bttn">
							<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_bttn" class="button button-primary button-large" />
						</div>
					</div>
				</div>
			</form>
		<?php
	}
?>
