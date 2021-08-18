<?php $subtab = isset( $_GET['subtab'] ) ? $_GET['subtab'] : 'settings';?>
<div class="ihc-subtab-menu">
	<a class="ihc-subtab-menu-item <?php echo ( $subtab =='settings' ) ? 'ihc-subtab-selected' : '';?>" href="<?php echo $url.'&tab='.$tab.'&subtab=settings';?>"><?php esc_html_e('Checkout Showcase', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ( $subtab =='msg') ? 'ihc-subtab-selected' : '';?>" href="<?php echo $url.'&tab='.$tab.'&subtab=msg';?>"><?php esc_html_e('Custom Messages', 'ihc');?></a>
	<div class="ihc-clear"></div>
</div>
<?php
echo ihc_inside_dashboard_error_license();
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
do_action( "ihc_admin_dashboard_after_top_menu" );

$data = get_option('ihc_user_fields');

if ($data){
	$payment_select_key = ihc_array_value_exists($data, 'payment_select', 'name');
	$dynamic_price_key = ihc_array_value_exists($data, 'ihc_dynamic_price', 'name');
	$coupon_key = ihc_array_value_exists($data, 'ihc_coupon', 'name');
}

if ( isset($_POST['ihc_save'] ) && !empty($_POST['ihc_admin_checkout_settings_nonce']) && wp_verify_nonce( $_POST['ihc_admin_checkout_settings_nonce'], 'ihc_admin_checkout_settings_nonce' ) ){
    	ihc_save_update_metas('checkout-settings');

		$data[$payment_select_key][ 'display_public_reg' ] = $_POST['ihc-field-display-public-reg'.$payment_select_key];
		$data[$dynamic_price_key][ 'display_public_reg' ] = $_POST['ihc-field-display-public-reg'.$dynamic_price_key];
		$data[$coupon_key][ 'display_public_reg' ] = $_POST['ihc-field-display-public-reg'.$coupon_key];
		update_option('ihc_user_fields', $data);
}

$subtab = 'settings';
$meta_arr = ihc_return_meta_arr('checkout-settings');
$meta_arr_payment = ihc_return_meta_arr('payment');

if (isset($_REQUEST['subtab'])){
   $subtab = $_REQUEST['subtab'];
}
if ($subtab=='settings'){
?>
<form  method="post" >
  <input type="hidden" name="ihc_admin_checkout_settings_nonce" value="<?php echo wp_create_nonce( 'ihc_admin_checkout_settings_nonce' );?>" />
  <div class="ihc-stuffbox">
    <h3><?php esc_html_e('Checkout Page Settings', 'ihc');?></h3>

		<div class="inside">

			<div class="iump-form-line iump-no-border">
        <h2><?php esc_html_e("Activate Checkout Step", 'ihc');?></h2>
				<div>
				 <label class="iump_label_shiwtch ihc-switch-button-margin">
					 <?php $checked = ($meta_arr['ihc_checkout_enable ']) ? 'checked' : '';?>
					 <input type="checkbox" class="iump-switch" onclick="iumpCheckAndH(this, '#ihc_checkout_enable ');" <?php echo $checked;?> />
					 <div class="switch ihc-display-inline"></div>
				 </label>
			 </div>
				 <input type="hidden" name="ihc_checkout_enable " value="<?php echo $meta_arr['ihc_checkout_enable '];?>" id="ihc_checkout_enable " />
      </div>

      <div class="iump-special-line">
      <div class="iump-form-line iump-no-border">
        <h2><?php esc_html_e("Checkout Page Template", 'ihc');?></h2>
      </div>
					<?php esc_html_e('Select Checkout Template:', 'ihc');?> <select name="ihc_checkout_template" id="ihc_checkout_template">
						<?php
							$templates = array(
												'ihc_checkout_template_1'=>'(#1) '.esc_html__('Main Template', 'ihc'),
												);
							foreach($templates as $k=>$v){
								?>
									<option value="<?php echo $k;?>" <?php if ($k==$meta_arr['ihc_checkout_template']){
										 echo 'selected';
									}
									?>
									><?php echo $v;?></option>
								<?php
							}
						?>
					</select>

					<div>
						<input type="radio" name="ihc_checkout_column_structure" class="ihc-js-image-type-selector" value="1" <?php echo ($meta_arr['ihc_checkout_column_structure'] === '1') ? 'checked': ''; ?> >
						<label><?php esc_html_e('1 Column', 'ihc');?></label>
					</div>
					<div>
						<input type="radio" name="ihc_checkout_column_structure" class="ihc-js-image-type-selector" value="2" <?php echo ($meta_arr['ihc_checkout_column_structure'] === '2') ? 'checked': ''; ?> >
						<label><?php esc_html_e('2 Columns', 'ihc');?></label>
					</div>

    </div>

    <div class="iump-form-line iump-no-border">
      <h2><?php esc_html_e("Checkout Page Additional Settings", 'ihc');?></h2>
    </div>
    <div class="iump-form-line iump-no-border">
      <h4><?php esc_html_e("Customer Information", 'ihc');?></h4>
       <p>- Dropdown choose and add fields from Custom Fields section</p>
       <p>- A table with added fields for Checkout page</p>
			 <div>
			 <label class="iump_label_shiwtch ihc-switch-button-margin">
				 <?php $checked = ($meta_arr['ihc_checkout_customer_information']) ? 'checked' : '';?>
				 <input type="checkbox" class="iump-switch" onclick="iumpCheckAndH(this, '#ihc_checkout_customer_information');" <?php echo $checked;?> />
				 <div class="switch ihc-display-inline"></div>
			 </label>
			 <input type="hidden" name="ihc_checkout_customer_information" value="<?php echo $meta_arr['ihc_checkout_customer_information'];?>" id="ihc_checkout_customer_information" />
		 </div>
			 <p><?php echo esc_html__('To add or edit custom fields visit ', 'ihc'); ?><a href="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=register&subtab=custom_fields');?>" target="_blank"><?php esc_html_e( 'Registration Form Fields', 'ihc' );?></a></p>

		</div>

    <div class="iump-form-line iump-no-border">
      <h4><?php esc_html_e("Payment Method", 'ihc');?></h4>
       <p>- On/Off Button (meta from Custom Fields section is taken and used)</p>
       <p>- Dropdown choose Template (meta from Custom Fields section is taken and used)</p>
       <p>- any specific option that exist already for Payment Select</p>
       <p>- Dropdown with default payment selection (based on existent option from General Options)</p>
			 <div>
			 <label class="iump_label_shiwtch ihc-switch-button-margin">
				 <?php $checked = ($data[$payment_select_key]['display_public_reg']) ? 'checked' : '';?>
				 <input type="checkbox" class="iump-switch" onclick="iumpCheckAndH(this, '#ihc-field-display-public-reg<?php echo $payment_select_key;?>');" <?php echo $checked;?> />
				 <div class="switch ihc-display-inline"></div>
			 </label>
			 <input type="hidden" name="ihc-field-display-public-reg<?php echo $payment_select_key;?>" value="<?php echo $data[$payment_select_key]['display_public_reg'];?>" id="ihc-field-display-public-reg<?php echo $payment_select_key;?>" />
		 	</div>
    </div>

		<?php

			if ($data[$payment_select_key]['name']=='payment_select'){
				?>
				<div class="iump-form-line iump-no-border">
					<h2><?php esc_html_e("Template", 'ihc');?></h2>
					<p>Payment selection showcase</p>
					<select name="theme"><?php
						if (empty($data[$payment_select_key]['theme'])) $data[$payment_select_key]['theme'] = 'ihc-select-payment-theme-1';
						foreach (array('ihc-select-payment-theme-1' => 'RadioBox', 'ihc-select-payment-theme-2' => 'Logos', 'ihc-select-payment-theme-3' => 'DropDown') as $k=>$v){
							?>
							<option value="<?php echo $k;?>" <?php if ($k==$data[$payment_select_key]['theme']) echo 'selected';?> ><?php echo $v;?></option>
							<?php
						}
					?></select>
				</div>
				<?php
			}
			?>

				<div class="iump-form-line iump-no-border">
					<select class="iump-form-select ihc-form-element ihc-form-element-select ihc-form-select " name="ihc_payment_selected">
						<?php

							$payment_arr = ihc_list_all_payments();
							foreach($payment_arr as $k=>$v){

								$active = (ihc_check_payment_available($k)) ? esc_html__('Active', 'ihc') : esc_html__('Inactive', 'ihc');
								?>
								<option value="<?php echo $k?>" <?php if ($k==$meta_arr_payment['ihc_payment_selected']) echo 'selected';?> >
									<?php echo $v . ' - ' . $active;?>
								</option>
								<?php
							}
						?>
					</select>

							<p><?php echo esc_html__('Link to ', 'ihc'); ?><a href="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=level_restrict_payment');?>" target="_blank"><?php esc_html_e( 'Memberships vs Payments', 'ihc' );?></a></p>
				</div>


		<div class="iump-form-line iump-no-border">
      <h4><?php esc_html_e("Membership Price details", 'ihc');?></h4>
			<div>
			 <label class="iump_label_shiwtch ihc-switch-button-margin">
				 <?php $checked = ($meta_arr['ihc_checkout_membership_price_details']) ? 'checked' : '';?>
				 <input type="checkbox" class="iump-switch" onclick="iumpCheckAndH(this, '#ihc_checkout_membership_price_details');" <?php echo $checked;?> />
				 <div class="switch ihc-display-inline"></div>
			 </label>
			 <input type="hidden" name="ihc_checkout_membership_price_details" value="<?php echo $meta_arr['ihc_checkout_membership_price_details'];?>" id="ihc_checkout_membership_price_details" />
		 </div>
    </div>

    <div class="iump-form-line iump-no-border">
      <h4><?php esc_html_e("Dynamic Price box", 'ihc');?></h4>
       <p>- On/Off Button (meta from Custom Fields section is taken and used)</p>
			 <div>
			 <label class="iump_label_shiwtch ihc-switch-button-margin">
				 <?php $checked = ($data[$dynamic_price_key]['display_public_reg']) ? 'checked' : '';?>
				 <input type="checkbox" class="iump-switch" onclick="iumpCheckAndH(this, '#ihc-field-display-public-reg<?php echo $dynamic_price_key;?>');" <?php echo $checked;?> />
				 <div class="switch ihc-display-inline"></div>
			 </label>
			 <input type="hidden" name="ihc-field-display-public-reg<?php echo $dynamic_price_key;?>" value="<?php echo $data[$dynamic_price_key]['display_public_reg'];?>" id="ihc-field-display-public-reg<?php echo $dynamic_price_key;?>" />
		 	</div>
			 <p><?php echo esc_html__('Link to ', 'ihc'); ?><a href="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=level_dynamic_price');?>" target="_blank"><?php esc_html_e( 'Membership Dynamic Price', 'ihc' );?></a></p>

		</div>

    <div class="iump-form-line iump-no-border">
      <h4><?php esc_html_e("Discount Coupon box", 'ihc');?></h4>
       <p>- On/Off Button (meta from Custom Fields section is taken and used)</p>
			 <div>
			 <label class="iump_label_shiwtch ihc-switch-button-margin">
				 <?php $checked = ($data[$coupon_key]['display_public_reg']) ? 'checked' : '';?>
				 <input type="checkbox" class="iump-switch" onclick="iumpCheckAndH(this, '#ihc-field-display-public-reg<?php echo $coupon_key;?>');" <?php echo $checked;?> />
				 <div class="switch ihc-display-inline"></div>
			 </label>
			 <input type="hidden" name="ihc-field-display-public-reg<?php echo $coupon_key;?>" value="<?php echo $data[$coupon_key]['display_public_reg'];?>" id="ihc-field-display-public-reg<?php echo $coupon_key;?>" />
		 </div>
			 <p><?php echo esc_html__('Link to ', 'ihc'); ?> <a href="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=coupons');?>" target="_blank"><?php esc_html_e( 'Coupons', 'ihc' );?></a></p>

		</div>

    <div class="iump-form-line iump-no-border">
      <h4><?php esc_html_e("Taxes display section", 'ihc');?></h4>
			<p></p>
			<div>
			 <label class="iump_label_shiwtch ihc-switch-button-margin">
				 <?php $checked = ($meta_arr['ihc_checkout_taxes_display_section']) ? 'checked' : '';?>
				 <input type="checkbox" class="iump-switch" onclick="iumpCheckAndH(this, '#ihc_checkout_taxes_display_section');" <?php echo $checked;?> />
				 <div class="switch ihc-display-inline"></div>
			 </label>
			 <input type="hidden" name="ihc_checkout_taxes_display_section" value="<?php echo $meta_arr['ihc_checkout_taxes_display_section'];?>" id="ihc_checkout_taxes_display_section" />
		 </div>
			 <p><?php echo esc_html__('Link to ', 'ihc'); ?> <a href="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=taxes');?>" target="_blank"><?php esc_html_e( 'Taxes', 'ihc' );?></a></p>
    </div>

    <div class="iump-form-line iump-no-border">
      <h4><?php esc_html_e("Privacy Policy message", 'ihc');?></h4>
       <p></p>
			 <div>
			 <label class="iump_label_shiwtch ihc-switch-button-margin">
				 <?php $checked = ($meta_arr['ihc_checkout_privacy_policy_option']) ? 'checked' : '';?>
				 <input type="checkbox" class="iump-switch" onclick="iumpCheckAndH(this, '#ihc_checkout_privacy_policy_option');" <?php echo $checked;?> />
				 <div class="switch ihc-display-inline"></div>
			 </label>
			 <input type="hidden" name="ihc_checkout_privacy_policy_option" value="<?php echo $meta_arr['ihc_checkout_privacy_policy_option'];?>" id="ihc_checkout_privacy_policy_option" />
		 </div>


		<div class="iump-wp_editor">
		<?php wp_editor(stripslashes($meta_arr['ihc_checkout_privacy_policy_message']), 'ihc_checkout_privacy_policy_message', array('textarea_name'=>'ihc_checkout_privacy_policy_message', 'editor_height'=>200));?>
		</div>
	</div>


      <div class="ihc-wrapp-submit-bttn">
        <input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" id="ihc_submit_bttn" class="button button-primary button-large" />
      </div>
    </div>

  </div>

	<div class="ihc-stuffbox">
		<h3><?php esc_html_e('Additional Custom CSS', 'ihc');?></h3>
		<div class="inside">
			<div>
				<textarea name="ihc_checkout_custom_css" id="ihc_checkout_custom_css" class="ihc-dashboard-textarea-full"><?php
				echo stripslashes($meta_arr['ihc_checkout_custom_css']);
				?></textarea>
			</div>
			<div class="ihc-wrapp-submit-bttn">
				<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" id="ihc_submit_bttn" name="ihc_save" class="button button-primary button-large" />
			</div>
		</div>

	</div>
</form>
<?php }else{ ?>
  <form  method="post" >
    <input type="hidden" name="ihc_admin_checkout_settings_nonce" value="<?php echo wp_create_nonce( 'ihc_admin_checkout_settings_nonce' );?>" />
    <div class="ihc-stuffbox">
      <h3><?php esc_html_e('Checkout Page Messages', 'ihc');?></h3>
      <div class="inside">
        <div class="iump-form-line iump-no-border">
          <h2><?php esc_html_e("Customize predefined Strings and Messages", 'ihc');?></h2>
           <p>- Customer Information</p>
           <p>- Payment Method</p>
           <p>- Initial Payment</p>
           <p>- Then</p>
           <p>- Choose how much you wish to pay for it. </p>
           <p>- If you have a coupon code, please apply it below.</p>
           <p>- Apply</p>
           <p>- Taxes</p>
           <p>- Subtotal</p>
           <p>- Complete Purchase</p>
        </div>
      </div>
    </div>
  </form>
<?php } ?>
