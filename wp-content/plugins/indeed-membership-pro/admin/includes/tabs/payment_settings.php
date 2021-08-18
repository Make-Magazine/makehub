<div class="ihc-subtab-menu">
	<?php $items = ihc_list_all_payments();?>
	<?php foreach ( $items as $slug => $label ):?>
			<a class="ihc-subtab-menu-item <?php echo (isset( $_GET['subtab'] ) && $_GET['subtab'] ==$slug ) ? 'ihc-subtab-selected' : '';?>" href="<?php echo $url.'&tab='.$tab.'&subtab=' . $slug;?>"><?php echo $label;?></a>
	<?php endforeach;?>

	<a class="ihc-subtab-menu-item" href="<?php echo $url.'&tab=general&subtab=pay_settings';?>"><?php esc_html_e('Payment Settings', 'ihc');?></a>
	<div class="ihc-clear"></div>
</div>
<?php
echo ihc_inside_dashboard_error_license();

if (empty($_GET['subtab'])){
	//listing payment methods
	$pages = ihc_get_all_pages();//getting pages
	echo ihc_check_default_pages_set();//set default pages message
	echo ihc_check_payment_gateways();
	echo ihc_is_curl_enable();
	do_action( "ihc_admin_dashboard_after_top_menu" );
	?>
	<div class="iump-page-title">Ultimate Membership Pro -
		<span class="second-text">
			<?php esc_html_e('Payments Services', 'ihc');?>
		</span>
	</div>
	<div class="iump-payment-list-wrapper">
		<div class="iump-payment-box-wrap">
		<?php $pay_stat = ihc_check_payment_status('paypal'); ?>
		  <a href="<?php echo $url.'&tab='.$tab.'&subtab=paypal';?>">
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">PayPal Standard</div>
                <div class="iump-payment-box-type">OffSite payment solution</div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
		 </a>
		</div>
        <div class="iump-payment-box-wrap">
		   <?php $pay_stat = ihc_check_payment_status('paypal_express_checkout'); ?>
		   <a href="<?php echo $url.'&tab='.$tab.'&subtab=paypal_express_checkout';?>">
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">PayPal Express</div>
                <div class="iump-payment-box-type">PayPal Express Checkout - OffSite payment solution</div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
		   </a>
		</div>

		<div class="iump-payment-box-wrap">
		   <?php $pay_stat = ihc_check_payment_status('bank_transfer'); ?>
		   <a href="<?php echo $url.'&tab='.$tab.'&subtab=bank_transfer';?>">
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">Bank Transfer</div>
                <div class="iump-payment-box-type">OnSite payment solution</div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
		   </a>
		</div>
		<div class="iump-payment-box-wrap">
			 <?php $pay_stat = ihc_check_payment_status( 'stripe_checkout_v2' ); ?>
			 <a href="<?php echo $url.'&tab='.$tab.'&subtab=stripe_checkout_v2';?>">
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">Stripe Checkout</div>
								<div class="iump-payment-box-type"><?php esc_html_e( 'OffSite payment solution (3d secure ready)', 'ihc' );?></div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
			 </a>
		</div>
		<div class="iump-payment-box-wrap">
		   <?php $pay_stat = ihc_check_payment_status('twocheckout'); ?>
		   <a href="<?php echo $url.'&tab='.$tab.'&subtab=twocheckout';?>">
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">2Checkout</div>
                <div class="iump-payment-box-type">OffSite payment solution</div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
		   </a>
		</div>
		<div class="iump-payment-box-wrap">
		   <?php $pay_stat = ihc_check_payment_status('mollie'); ?>
		   <a href="<?php echo $url.'&tab='.$tab.'&subtab=mollie';?>">
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">Mollie</div>
                <div class="iump-payment-box-type">OffSite payment solution</div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
		   </a>
		</div>
        <div class="iump-payment-box-wrap">
		    <?php $pay_stat = ihc_check_payment_status('pagseguro'); ?>
		    <a href="<?php echo $url.'&tab='.$tab.'&subtab=pagseguro';?>">
					<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
						<div class="iump-payment-box-title">Pagseguro</div>
			          <div class="iump-payment-box-type">OffSite payment solution</div>
						<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
					</div>
				</a>
		</div>
		<div class="iump-payment-box-wrap">
		   <?php $pay_stat = ihc_check_payment_status('braintree'); ?>
		   <a href="<?php echo $url.'&tab='.$tab.'&subtab=braintree';?>">
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">Braintree</div>
                <div class="iump-payment-box-type">OnSite payment solution</div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
		   </a>
		</div>

		<div class="iump-payment-box-wrap">
		  <?php $pay_stat = ihc_check_payment_status('authorize'); ?>
		  <a href="<?php echo $url.'&tab='.$tab.'&subtab=authorize';?>">
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">Authorize.net</div>
                <div class="iump-payment-box-type">OffSite for OneTime payment & OnSite for Recurring Payment</div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
		 </a>
		</div>
		<div class="iump-payment-box-wrap">
		   <?php $pay_stat = ihc_check_payment_status('stripe'); ?>
		   <a href="<?php echo $url.'&tab='.$tab.'&subtab=stripe';?>">
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">Stripe Standard</div>
                <div class="iump-payment-box-type">OnSite payment solution</div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
		   </a>
		</div>

		<?php
				do_action( 'ihc_payment_gateway_box' );
				// @description
		?>


		<div class="ihc-clear"></div>
	</div>
	<?php
} else {
	switch ($_GET['subtab']){
		case 'paypal':
			if (isset($_POST['ihc_save']) && !empty($_POST['ihc-payment-settings-nonce']) && wp_verify_nonce( $_POST['ihc-payment-settings-nonce'], 'ihc-payment-settings-nonce' ) ){
					//ihc_save_update_metas('payment_paypal');//save update metas
					ihc_save_update_trimmed_metas('payment_paypal'); // save update metas without extra spaces
			}
			$meta_arr = ihc_return_meta_arr('payment_paypal');//getting metas
			$pages = ihc_get_all_pages();//getting pages
			echo ihc_check_default_pages_set();//set default pages message
			echo ihc_check_payment_gateways();
			echo ihc_is_curl_enable();
			do_action( "ihc_admin_dashboard_after_top_menu" );

			$siteUrl = site_url();
			$siteUrl = trailingslashit($siteUrl);
			?>
			<div class="iump-page-title">Ultimate Membership Pro -
				<span class="second-text">
					<?php esc_html_e('Payments Services', 'ihc');?>
				</span>
			</div>
			<form  method="post">
					<input type="hidden" name="ihc-payment-settings-nonce" value="<?php echo wp_create_nonce( 'ihc-payment-settings-nonce' );?>" />
					<div class="ihc-stuffbox">
						<h3><?php esc_html_e('PayPal Standard Activation:', 'ihc');?></h3>
						<div class="inside">
							<div class="iump-form-line">
								<h4><?php esc_html_e('Enable PayPal Standard', 'ihc');?> </h4>
								<p><?php esc_html_e('Once everything is properly set up, activate the payment gateway for further use.', 'ihc');?> </p>
                                <p><?php esc_html_e("PayPal Standard redirects customers to PayPal to enter their payment information", 'ihc');?></p>
                                <label class="iump_label_shiwtch ihc-switch-button-margin">
								<?php $checked = ($meta_arr['ihc_paypal_status']) ? 'checked' : '';?>
								<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_paypal_status');" <?php echo $checked;?> />
								<div class="switch ihc-display-inline"></div>
							</label>
							<input type="hidden" value="<?php echo $meta_arr['ihc_paypal_status'];?>" name="ihc_paypal_status" id="ihc_paypal_status" />
							</div>

							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>
						</div>
					</div>
					<div class="ihc-stuffbox">

						<h3><?php esc_html_e('PayPal Standard Settings:', 'ihc');?></h3>

						<div class="inside">
                         <div class="row ihc-row-no-margin">
                  			<div class="col-xs-6">
							<div class="iump-form-line input-group">
								<span class="input-group-addon" ><?php esc_html_e('PayPal Merchant Email:', 'ihc');?></span>
                                <input type="text" value="<?php echo $meta_arr['ihc_paypal_email'];?>" name="ihc_paypal_email" class="form-control"/>
							</div>
                            <div class="iump-form-line">
                            <p><?php esc_html_e("Please enter your PayPal Email address. This is required in order to take payments via PayPal.", 'ihc');?></p>
                            </div>

							<div class="iump-form-line input-group">
								<span class="input-group-addon" ><?php esc_html_e('Merchant account ID:', 'ihc');?></span>
                                <input type="text" value="<?php echo $meta_arr['ihc_paypal_merchant_account_id'];?>" name="ihc_paypal_merchant_account_id"  class="form-control" />

							</div>
                            <div class="iump-form-line input-group">
                            	<p><strong>Merchant account ID</strong> <?php esc_html_e(" is necessary for Subscriptions management especially.", 'ihc');?></p>

							</div>
							<div class="iump-form-line iump-no-border">
                           		<input type="checkbox" onClick="checkAndH(this, '#enable_sandbox');" <?php if($meta_arr['ihc_paypal_sandbox']) echo 'checked';?> />
								<label class="iump-labels"><?php esc_html_e(' Enable PayPal Sandbox', 'ihc');?></label>
								<input type="hidden" name="ihc_paypal_sandbox" value="<?php echo $meta_arr['ihc_paypal_sandbox'];?>" id="enable_sandbox" />
							</div>
                            <div class="iump-form-line">
                            <p><?php esc_html_e("PayPal sandbox mode can be used to testing purpose. A Sandbox merchant account and additional Sandbox buyer account is required. Sign up as a ", 'ihc');?><a target="_blank" href="https://developer.paypal.com/"><?php esc_html_e("developer account", 'ihc');?></a></p>
                            </div>

														<div class="iump-form-line">
														              <h4><?php esc_html_e("How to Setup A Sandobx Account", 'ihc');?></h4>
														                <p>1. <?php esc_html_e("Login in ", 'ihc'); ?> <a target="_blank" href="https://developer.paypal.com/">developer account </a> <?php esc_html_e("and go to Dashboard -> My Apps & Credentials and create an app.", 'ihc');?></p>
														                <p>2. <?php esc_html_e("In Sandbox -> Accounts, a 'Buyer' and a 'Merchant' account have been created.", 'ihc');?>
														                <p>3. <?php esc_html_e("You can find 'Merchant account ID' by loggin in to", 'ihc');?> <a target='_blank' href='https://www.sandbox.paypal.com/'>sandbox.paypal.com</a> <?php esc_html_e(" with your merchant account and click on Account Settings -> Business information -> PayPal Merchant ID.", 'ihc');?></p>
														                <p>4. <?php esc_html_e("Set ", 'ihc');?><b><?php echo $siteUrl . '?ihc_action=paypal'?></b> <?php esc_html_e("in Account Settings -> Website payments -> Instant payment notifications.",'ihc');?></p>

														</div>

														<div class="iump-form-line">
															<h4><?php esc_html_e('Checkout Page language:', 'ihc');?></h4>
															<select name="ihc_paypapl_locale_code"  class="form-control">
																	<?php
																			$locale = array(
																												'en_US' => 'English - US',
																												'ar_EG' => 'Arabic - Egipt',
																												'fr_XC' => 'France - Algeria',
																									 			'en_AU' => 'English - Australia',
																									   		'de_DE' => 'German - Germany',
																									  		'nl_NL' => 'Dutch - Netherlands',
																												'fr_FR' => 'French - France',
																												'pt_BR' => 'Portuguese - Brazil',
																												'fr_CA' => 'French - Canada',
																									    	'zh_CN' => 'Chinese - China',
																									   		'da_DK' => 'Danish - Denmark',
																										    'ru_RU' => 'Russian - Russia',
																										    'en_GB' => 'English - Grand Britain',
																										    'id_ID' => 'Indonesian - Indonesia',
																									   		'he_IL' => 'Hebrew - Israel',
																									    	'it_IT' => 'Italian - Italy',
																									   		'ja_JP' => 'Japanese - Japan',
																										    'no_NO' => 'Norwegian - Norway',
																										    'pl_PL' => 'Polish - Poland',
																										    'pt_PT' => 'Portuguese - Portugal',
																									      'sv_SE' => 'Swedish - Sweden',
																									      'zh_TW' => 'Chinese - Taiwan',
																									      'th_TH' => 'Thai - Thailand',
																									      'es_ES' => 'Spanish - Spain',

																			);
																	?>
																	<?php foreach ($locale as $k=>$country):?>
																			<option value="<?php echo $k;?>" <?php if ($k==$meta_arr['ihc_paypapl_locale_code']) echo 'selected';?> ><?php echo $country;?></option>
																	<?php endforeach;?>
															</select>
														</div>
							</div>
                 		 </div>
							<div class="iump-form-line iump-special-line">
                            <div class="row ihc-row-no-margin">
                  			<div class="col-xs-4">
								<h4><?php esc_html_e('Redirect Page after Payment:', 'ihc');?></h4>
								<select name="ihc_paypal_return_page" class="form-control">
									<option value="-1" <?php if($meta_arr['ihc_paypal_return_page']==-1)echo 'selected';?> >...</option>
									<?php
										if($pages){
											foreach($pages as $k=>$v){
												?>
													<option value="<?php echo $k;?>" <?php if ($meta_arr['ihc_paypal_return_page']==$k) echo 'selected';?> ><?php echo $v;?></option>
												<?php
											}
										}
									?>
								</select>
                             </div>
                             </div>

														 <div class="row ihc-row-no-margin">
                   			<div class="col-xs-4">
 								<h4><?php esc_html_e('Redirect Page after cancel Payment:', 'ihc');?></h4>
 								<select name="ihc_paypal_return_page_on_cancel" class="form-control">
 									<option value="-1" <?php if($meta_arr['ihc_paypal_return_page_on_cancel']==-1)echo 'selected';?> >...</option>
 									<?php
 										if($pages){
 											foreach($pages as $k=>$v){
 												?>
 													<option value="<?php echo $k;?>" <?php if ($meta_arr['ihc_paypal_return_page_on_cancel']==$k) echo 'selected';?> ><?php echo $v;?></option>
 												<?php
 											}
 										}
 									?>
 								</select>
                              </div>
                              </div>

							</div>
							<div class="iump-form-line">
                            <h4><?php esc_html_e("How to Setup", 'ihc');?></h4>
                            <p>1. <?php esc_html_e("Login with your credentials and go to 'Account Settings' (top-right of page)", 'ihc');?></p>
							<p>2. <?php esc_html_e("After that go to 'Notifications' and next Update the 'Instant payment notifications' ", 'ihc');?></p>
							<p>3. <?php esc_html_e('Setup your IPN in order to receive Payment confirmations as: ', 'ihc');?><a target="_blank" href="<?php echo $siteUrl . '?ihc_action=paypal';?>"><?php echo $siteUrl . '?ihc_action=paypal';?></a></p>
                            </div>

							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>
						</div>
					</div>

					<div class="ihc-stuffbox">
						<h3><?php esc_html_e('Extra Settings:', 'ihc');?></h3>
						<div class="inside">
                        <div class="row ihc-row-no-margin">
                  			<div class="col-xs-4">
							<div class="iump-form-line iump-no-border input-group">
								<span class="input-group-addon"><?php esc_html_e('Label:', 'ihc');?></span>
								<input type="text" name="ihc_paypal_label" value="<?php echo $meta_arr['ihc_paypal_label'];?>"  class="form-control"/>
							</div>

							<div class="iump-form-line iump-no-border input-group">
								<span class="input-group-addon"><?php esc_html_e('Order:', 'ihc');?></span>
								<input type="number" min="1" name="ihc_paypal_select_order" value="<?php echo $meta_arr['ihc_paypal_select_order'];?>"  class="form-control"/>
							</div>


						</div>

                        </div>
												<!-- developer -->
												  <div class="row ihc-row-no-margin">
												<div class="col-xs-4">
												<div class="input-group">
													 <h4><?php esc_html_e('Short Description', 'ihc');?></h4>
														 <textarea name="ihc_paypal_short_description" class="form-control" rows="2" cols="125" placeholder="<?php esc_html_e('write a short description', 'ihc');?>"><?php echo isset( $meta_arr['ihc_paypal_short_description'] ) ? stripslashes( $meta_arr['ihc_paypal_short_description'] ) : '';?></textarea>
												 </div>
											 </div>
										 </div>
												 <!-- end developer -->
												 <div class="ihc-wrapp-submit-bttn iump-submit-form">
													 <input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
												 </div>
                        </div>
					</div>

			</form>
			<?php
		break;

		case 'stripe':
			if (isset($_POST['ihc_save']) && !empty($_POST['ihc-payment-settings-nonce']) && wp_verify_nonce( $_POST['ihc-payment-settings-nonce'], 'ihc-payment-settings-nonce' ) ){
					//ihc_save_update_metas('payment_stripe');//save update metas
					ihc_save_update_trimmed_metas('payment_stripe'); // save update metas without extra spaces
			}

			$meta_arr = ihc_return_meta_arr('payment_stripe');//getting metas
			echo ihc_check_default_pages_set();//set default pages message
			echo ihc_check_payment_gateways();
			echo ihc_is_curl_enable();
			do_action( "ihc_admin_dashboard_after_top_menu" );
			?>
			<div class="iump-page-title">Ultimate Membership Pro -
				<span class="second-text">
					<?php esc_html_e('Payments Services', 'ihc');?>
				</span>
			</div>
			<form  method="post">
				<input type="hidden" name="ihc-payment-settings-nonce" value="<?php echo wp_create_nonce( 'ihc-payment-settings-nonce' );?>" />
			<div class="ihc-stuffbox">
						<h3><?php esc_html_e('Stripe  Standard Activation:', 'ihc');?></h3>
						<div class="inside">
							<div class="iump-form-line">
                            	<h4><?php esc_html_e('Enable Stripe Standard', 'ihc');?> </h4>
								<p><?php esc_html_e('Once all Settings are properly done, Activate the Payment Getway for further use.', 'ihc');?> </p>
								<label class="iump_label_shiwtch ihc-switch-button-margin">
								<?php $checked = ($meta_arr['ihc_stripe_status']) ? 'checked' : 'disabled';?>
								<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_stripe_status');" <?php echo $checked;?> />
								<div class="switch ihc-display-inline"></div>
							</label>
							<input type="hidden" value="<?php echo $meta_arr['ihc_stripe_status'];?>" name="ihc_stripe_status" id="ihc_stripe_status" />
							<div class="ihc-alert-warning"><?php echo esc_html__('We recommend to use', 'ihc'); ?> <a href="<?php echo $url.'&tab='.$tab.'&subtab=stripe_checkout';?>"><strong>Stripe Checkout</strong></a> <?php echo esc_html__('gateway instead being more stable and customizable. Stripe Standard will become deprecated soon.', 'ihc');?></div>
                            </div>
							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>
						</div>
					</div>
				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Stripe Standard Settings:', 'ihc');?></h3>
					<div class="inside">
                    	 <div class="row ihc-row-no-margin">
                  <div class="col-xs-6">
						<div class="iump-form-line input-group">
							<span class="input-group-addon"><?php esc_html_e('Publishable Key:', 'ihc');?></span>
							<input type="text" value="<?php echo $meta_arr['ihc_stripe_publishable_key'];?>" name="ihc_stripe_publishable_key" class="form-control"/>
						</div>
						<div class="iump-form-line input-group">
							<span class="input-group-addon"><?php esc_html_e('Secret Key:', 'ihc');?></span>
							<input type="text" value="<?php echo $meta_arr['ihc_stripe_secret_key'];?>" name="ihc_stripe_secret_key" class="form-control" />
						</div>
					</div>
                    </div>
						<div class="iump-form-line">
							<?php
								$site_url = site_url();
								$site_url = trailingslashit($site_url);
								$notify_url = add_query_arg('ihc_action', 'stripe', $site_url);
								?>
								<strong>Important:</strong> <?php esc_html_e(" set your 'Webhook' to: ");
								echo '<strong>' . $notify_url . '</strong>'; /// admin_url("admin-ajax.php") . "?action=ihc_twocheckout_ins"
							?>
						</div>

						<div class="ihc-payment-steps-wrapper">
							<ul class="ihc-info-list">
								<li><?php esc_html_e('1. Go to', 'ihc');?> <a href="http://stripe.com" target="_blank">http://stripe.com</a> <?php esc_html_e('and login with username and password.', 'ihc');?></li>
                                <li><?php esc_html_e('2. Complete your Account setup with all required information on ', 'ihc');?><a href="https://dashboard.stripe.com/settings/account" target="_blank">https://dashboard.stripe.com/settings/account</a></li>
								<li><?php esc_html_e('3. After that click on "Developers", and then select "API Keys".', 'ihc');?></li>
								<li><?php esc_html_e('4. You will find the "Publishable Key" and "Secret Key". If not, create them.', 'ihc');?></li>
								<li><?php esc_html_e('5. Go to "Webhooks" and press "Add endpoint".', 'ihc');?></li>
								<li><?php echo esc_html__("6. Set your Endpoint URL to: ", 'ihc') . '<strong>' . $notify_url . '</strong> and choose "receive all events"';?></li>
							</ul>
						</div>
						<div class="iump-form-line">
                         <h2><?php esc_html_e('Test Credentials', 'ihc');?></h2>
                        	<p><?php esc_html_e('For Test/Sandbox mode use the next credentials available:', 'ihc');?></p>
                        	<a href="https://stripe.com/docs/testing" target="_blank">https://stripe.com/docs/testing</a>
                            <p><?php esc_html_e('Example:', 'ihc');?></p>
                            <p><?php esc_html_e('Credit Card: ', 'ihc');?>4242424242424242</p>
                            <p><?php esc_html_e('Expire Time: ', 'ihc');?>1222</p>
                        </div>
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>
					</div>
				</div>

								<div class="ihc-stuffbox">
									<h3><?php esc_html_e('Additional Settings:', 'ihc');?></h3>
									<div class="inside">
                                    	<div class="row ihc-row-no-margin">
                                     	<div class="col-xs-4">
										<div class="iump-form-line iump-no-border">
											<h4><?php esc_html_e('Stripe popup Language:', 'ihc');?></h4>
											<select name="ihc_stripe_locale_code" class="form-control">
													<?php
															$locales = array(
																		'zh' => 'Simplified Chinese',
																		'da' => 'Danish',
																		'nl' => 'Dutch',
																		'en' => 'English',
																		'fi' => 'Finnish',
																		'fr' => 'French',
																		'de' => 'German',
																		'it' => 'Italian',
																		'ja' => 'Japanese',
																		'no' => 'Norwegian',
																		'es' => 'Spanish',
																		'sv' => 'Swedish',
															);
													?>
													<?php foreach ($locales as $k=>$v):?>
															<option value="<?php echo $k;?>" <?php if ($meta_arr['ihc_stripe_locale_code']==$k) echo 'selected';?> ><?php echo $v;?></option>
													<?php endforeach;?>
											</select>
										</div>

										<div class="iump-form-line iump-no-border  input-group">
											<span class="input-group-addon"><?php esc_html_e('Stripe popup Logo image:', 'ihc');?></span>
											<input type="text" onClick="openMediaUp(this);" name="ihc_stripe_popup_image" value="<?php echo $meta_arr['ihc_stripe_popup_image'];?>"  class="form-control"/>
										</div>

										<div class="iump-form-line iump-no-border input-group">
												<span class="input-group-addon"><?php esc_html_e('Stripe popup Button label:', 'ihc');?></span>
												<input type="text" name="ihc_stripe_bttn_value" value="<?php echo $meta_arr['ihc_stripe_bttn_value'];?>"  class="form-control"/>
										</div>

										<div class="ihc-wrapp-submit-bttn iump-submit-form">
											<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
										</div>
									</div>
                                    </div>
                                    </div>
								</div>

				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Extra Settings:', 'ihc');?></h3>
					<div class="inside">
                    	<div class="row ihc-row-no-margin">
                                     	<div class="col-xs-4">
						<div class="iump-form-line iump-no-border input-group">
							<span class="input-group-addon"><?php esc_html_e('Label:', 'ihc');?></span>
							<input type="text" name="ihc_stripe_label" value="<?php echo $meta_arr['ihc_stripe_label'];?>" class="form-control"/>
						</div>

						<div class="iump-form-line iump-no-border input-group">
							<span class="input-group-addon"><?php esc_html_e('Order:', 'ihc');?></span>
							<input type="number" min="1" name="ihc_stripe_select_order" value="<?php echo $meta_arr['ihc_stripe_select_order'];?>" class="form-control"/>
						</div>


                        </div>

                        </div>
												<!-- developer -->
													<div class="row ihc-row-no-margin">
												<div class="col-xs-4">
												<div class="input-group">
													 <h4><?php esc_html_e('Short Description', 'ihc');?></h4>
														 <textarea name="ihc_stripe_short_description" class="form-control" rows="2" cols="125" placeholder="<?php esc_html_e('write a short description', 'ihc');?>"><?php echo isset( $meta_arr['ihc_stripe_short_description'] ) ? stripslashes($meta_arr['ihc_stripe_short_description']) : '';?></textarea>
												 </div>
												</div>
												</div>
												 <!-- end developer -->
												 <div class="ihc-wrapp-submit-bttn iump-submit-form">
													 <input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
												 </div>
					</div>
				</div>

			</form>
			<?php
		break;

		case 'stripe_checkout_v2':
		if (isset($_POST['ihc_save']) && !empty($_POST['ihc-payment-settings-nonce']) && wp_verify_nonce( $_POST['ihc-payment-settings-nonce'], 'ihc-payment-settings-nonce' ) ){
			ihc_save_update_trimmed_metas('payment_stripe_checkout_v2'); // save update metas without extra spaces
				//ihc_save_update_metas('payment_stripe_checkout_v2');//save update metas

		}

		$meta_arr = ihc_return_meta_arr('payment_stripe_checkout_v2');//getting metas
		echo ihc_check_default_pages_set();//set default pages message
		echo ihc_check_payment_gateways();
		echo ihc_is_curl_enable();
		do_action( "ihc_admin_dashboard_after_top_menu" );
		?>
		<div class="iump-page-title">Ultimate Membership Pro -
			<span class="second-text">
				<?php esc_html_e('Payments Services', 'ihc');?>
			</span>
		</div>
		<form  method="post">
			<input type="hidden" name="ihc-payment-settings-nonce" value="<?php echo wp_create_nonce( 'ihc-payment-settings-nonce' );?>" />
		<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Stripe Checkout Activation', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line">
                            <h4><?php esc_html_e('Enable Stripe Checkout', 'ihc');?> </h4>
							<p><?php esc_html_e('Once all Settings are properly done, Activate the Payment Getway for further use.', 'ihc');?> </p>
							<label class="iump_label_shiwtch ihc-switch-button-margin">
							<?php $checked = ($meta_arr['ihc_stripe_checkout_v2_status']) ? 'checked' : '';?>
							<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_stripe_checkout_v2_status');" <?php echo $checked;?> />
							<div class="switch  ihc-display-inline"></div>
						</label>
						<input type="hidden" value="<?php echo $meta_arr['ihc_stripe_checkout_v2_status'];?>" name="ihc_stripe_checkout_v2_status" id="ihc_stripe_checkout_v2_status" />
						</div>
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>
					</div>
				</div>
			<div class="ihc-stuffbox">
				<h3><?php esc_html_e('Stripe Checkout Settings', 'ihc');?></h3>
				<div class="inside">
                <div class="row ihc-row-no-margin">
                  <div class="col-xs-6">
					<div class="iump-form-line input-group">
						<span class="input-group-addon" ><?php esc_html_e('Publishable Key:', 'ihc');?></span>
						<input type="text" value="<?php echo $meta_arr['ihc_stripe_checkout_v2_publishable_key'];?>" name="ihc_stripe_checkout_v2_publishable_key" class="form-control"/>
					</div>

                    <div class="iump-form-line input-group">
						<span class="input-group-addon" ><?php esc_html_e('Secret Key:', 'ihc');?></span>
						<input type="text" value="<?php echo $meta_arr['ihc_stripe_checkout_v2_secret_key'];?>" name="ihc_stripe_checkout_v2_secret_key"  class="form-control" />
					</div>

				  </div>
                  </div>

					<div class="iump-form-line">
						<?php
							$site_url = site_url();
							$site_url = trailingslashit($site_url);
							$notify_url = add_query_arg( 'ihc_action', 'stripe_checkout', $site_url );
							?>
							<strong>Important:</strong> <?php esc_html_e(" set your 'Webhook' to: ");
							echo '<strong>' . $notify_url . '</strong>'; /// admin_url("admin-ajax.php") . "?action=ihc_twocheckout_ins"
						?>
					</div>

					<div class="ihc-payment-steps-wrapper">
						<ul class="ihc-info-list">
							<li><?php esc_html_e('1. Go to', 'ihc');?> <a href="http://stripe.com" target="_blank">http://stripe.com</a> <?php esc_html_e('and login with username and password.', 'ihc');?></li>
              <li><?php esc_html_e('2. Complete your Account setup with all required information on ', 'ihc');?><a href="https://dashboard.stripe.com/settings/account" target="_blank">https://dashboard.stripe.com/settings/account</a></li>
							<li><?php esc_html_e('3. After that click on "Developers" and check the current API version. Be sure that the latest (2020-08-27) is chosen and set as Default', 'ihc');?></li>
							<li><?php esc_html_e('4. Then select "API Keys".', 'ihc');?></li>
							<li><?php esc_html_e('5. You will find the "Publishable Key" and "Secret Key". If have been created with an old API version, you have to delete and re-create them. If does not exist yet create them.', 'ihc');?></li>
							<li><?php esc_html_e('6. Go to "Webhooks" and press "Add endpoint".', 'ihc');?></li>
							<li><?php echo esc_html__("7. Set your Endpoint URL to: ", 'ihc') . '<strong>' . $notify_url . '</strong>';?> <?php esc_html_e('choosing "receive all events".', 'ihc');?><?php esc_html_e('Be sure that latest API version is used for webhooks.', 'ihc');?></li>
                            <li><?php echo esc_html__('8. Enable Email notifications from', 'ihc');?>  <strong>Manage payments that require 3D Secure</strong> <?php esc_html__(' on ', 'ihc');?> <a href="https://dashboard.stripe.com/account/billing/automatic" target="_blank">https://dashboard.stripe.com/account/billing/automatic</a></li>
                            <li><?php echo esc_html__("9. Customize Stripe Checkout page and Emails on ", 'ihc') . '<a href="https://dashboard.stripe.com/account/branding" target="_blank">https://dashboard.stripe.com/account/branding</a>';?></li>
						</ul>
					</div>
					<div class="iump-form-line">
                    <h2><?php esc_html_e('Test Credentials', 'ihc');?></h2>
												<p><?php esc_html_e('For Test/Sandbox mode use the next credentials available:', 'ihc');?></p>
												<a href="https://stripe.com/docs/testing" target="_blank">https://stripe.com/docs/testing</a>
													<p><?php esc_html_e('Example:', 'ihc');?></p>
													<p><?php esc_html_e('Credit Card: ', 'ihc');?>4000002500003155</p>
													<p><?php esc_html_e('Expire Time: ', 'ihc');?>1222</p>
											</div>
					<div class="ihc-wrapp-submit-bttn iump-submit-form">
						<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
					</div>
				</div>
			</div>

<?php
$pages = ihc_get_all_pages();
?>
							<div class="ihc-stuffbox">
								<h3><?php esc_html_e('Additional Settings', 'ihc');?></h3>
								<div class="inside">
									<div class="iump-form-line iump-no-border">
                                    <div class="row ihc-row-no-margin">
                                     <div class="col-xs-4">
										<h4><?php esc_html_e('Stripe Checkout page Language:', 'ihc');?></h4>
                                        <div>
										<select name="ihc_stripe_checkout_v2_locale_code" class="form-control">
												<?php
														$locales = array(
																	'zh' => 'Simplified Chinese',
																	'da' => 'Danish',
																	'nl' => 'Dutch',
																	'en' => 'English',
																	'fi' => 'Finnish',
																	'fr' => 'French',
																	'de' => 'German',
																	'it' => 'Italian',
																	'ja' => 'Japanese',
																	'no' => 'Norwegian',
																	'es' => 'Spanish',
																	'sv' => 'Swedish',
														);
												?>
												<?php foreach ($locales as $k=>$v):?>
														<option value="<?php echo $k;?>" <?php if ($meta_arr['ihc_stripe_checkout_v2_locale_code']==$k) echo 'selected';?> ><?php echo $v;?></option>
												<?php endforeach;?>
										</select>
                                      </div>
									</div>

								 </div>
                                 </div>

									<div class="iump-form-line iump-no-border">
                                    <div class="row ihc-row-no-margin">
                                     <div class="col-xs-4">
										<h4><?php esc_html_e('Success redirect page:', 'ihc');?></h4>
                                        <div>
										<select name="ihc_stripe_checkout_v2_success_page" class="form-control">
												<option value="-1" <?php if($meta_arr['ihc_stripe_checkout_v2_success_page']==-1)echo 'selected';?> >...</option>
												<?php
													if ($pages){
														foreach ($pages as $k=>$v){
															?>
																<option value="<?php echo $k;?>" <?php if($meta_arr['ihc_stripe_checkout_v2_success_page']==$k)echo 'selected';?> ><?php echo $v;?></option>
															<?php
														}
													}
												?>
										</select>
                                      </div>
									</div>

								 </div>
                                 </div>

									<div class="iump-form-line iump-no-border">
                                    <div class="row ihc-row-no-margin">
                                     <div class="col-xs-4">
										<h4><?php esc_html_e('Cancel redirect page:', 'ihc');?></h4>
                                        <div>
										<select name="ihc_stripe_checkout_v2_cancel_page" class="form-control">
												<option value="-1" <?php if($meta_arr['ihc_stripe_checkout_v2_cancel_page']==-1)echo 'selected';?> >...</option>
												<?php
													if ($pages){
														foreach ($pages as $k=>$v){
															?>
																<option value="<?php echo $k;?>" <?php if($meta_arr['ihc_stripe_checkout_v2_cancel_page']==$k)echo 'selected';?> ><?php echo $v;?></option>
															<?php
														}
													}
												?>
										</select>
                                      </div>
									</div>

								 </div>
                                 </div>

									<div class="iump-form-line">

                                    <div class="row ihc-row-no-margin">
                                     <div class="col-xs-4">
											<h4><?php esc_html_e( "Autocomplete Stripe Checkout Email Address with current user account.", 'ihc' );?></h4>
											<label class="iump_label_shiwtch ihc-switch-button-margin">
													<input type="checkbox" class="iump-switch" onclick="iumpCheckAndH(this, '#ihc_stripe_checkout_v2_use_user_email');" <?php if ( !empty( $meta_arr['ihc_stripe_checkout_v2_use_user_email'] ) ) echo 'checked';?> />
													<div class="switch ihc-display-inline"></div>
											</label>
											<input type="hidden" name="ihc_stripe_checkout_v2_use_user_email" id="ihc_stripe_checkout_v2_use_user_email" value="<?php echo $meta_arr['ihc_stripe_checkout_v2_use_user_email'];?>">
									</div>

								 </div>
                                 </div>
									<div class="ihc-wrapp-submit-bttn iump-submit-form">
										<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
									</div>
								</div>
							</div>

			<div class="ihc-stuffbox">
				<h3><?php esc_html_e('Extra Settings', 'ihc');?></h3>
				<div class="inside">
                <div class="row ihc-row-no-margin">
                  <div class="col-xs-3">
					<div class="iump-form-line iump-no-border input-group">
						<span class="input-group-addon"><?php esc_html_e('Label:', 'ihc');?></span>
						<input type="text" name="ihc_stripe_checkout_v2_label" class="form-control" value="<?php echo $meta_arr['ihc_stripe_checkout_v2_label'];?>" />
					</div>

					<div class="iump-form-line iump-no-border input-group">
						<span class="input-group-addon"><?php esc_html_e('Order:', 'ihc');?></span>
						<input type="number" min="1" name="ihc_stripe_checkout_v2_select_order" class="form-control" value="<?php echo $meta_arr['ihc_stripe_checkout_v2_select_order'];?>" />
					</div>
					</div>
            </div>
						<!-- developer -->
						  <div class="row ihc-row-no-margin">
						<div class="col-xs-4">
						<div class="input-group">
						   <h4><?php esc_html_e('Short Description', 'ihc');?></h4>
						     <textarea name="ihc_stripe_checkout_v2_short_description" class="form-control" rows="2" cols="125"  placeholder="<?php esc_html_e('write a short description', 'ihc');?>"><?php echo isset( $meta_arr['ihc_stripe_checkout_v2_short_description'] ) ? stripslashes($meta_arr['ihc_stripe_checkout_v2_short_description']) : '';?></textarea>
						 </div>
						</div>
						</div>
						 <!-- end developer -->
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>

				</div>
			</div>

		</form>
		<?php
			break;

		case 'authorize':
			if (isset($_POST['ihc_save']) && !empty($_POST['ihc-payment-settings-nonce']) && wp_verify_nonce( $_POST['ihc-payment-settings-nonce'], 'ihc-payment-settings-nonce' ) ){
					//ihc_save_update_metas('payment_authorize');//save update metas
					ihc_save_update_trimmed_metas('payment_authorize'); // save update metas without extra spaces
			}
			$meta_arr = ihc_return_meta_arr('payment_authorize');//getting metas
			echo ihc_check_default_pages_set();//set default pages message
			echo ihc_check_payment_gateways();
			echo ihc_is_curl_enable();
			do_action( "ihc_admin_dashboard_after_top_menu" );
			?>
			<div class="iump-page-title">Ultimate Membership Pro -
				<span class="second-text">
					<?php esc_html_e('Payments Services', 'ihc');?>
				</span>
			</div>
			<form  method="post">
				<input type="hidden" name="ihc-payment-settings-nonce" value="<?php echo wp_create_nonce( 'ihc-payment-settings-nonce' );?>" />
			<div class="ihc-stuffbox">
						<h3><?php esc_html_e('Authorize.net Activation:', 'ihc');?></h3>
						<div class="inside">
							<div class="iump-form-line">
										<h4><?php esc_html_e('Once all Settings are properly done, Activate the Payment Getway for further use.', 'ihc');?> </h4>
										<label class="iump_label_shiwtch ihc-switch-button-margin">
												<?php $checked = ($meta_arr['ihc_authorize_status']) ? 'checked' : '';?>
												<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_authorize_status');" <?php echo $checked;?> />
												<div class="switch ihc-display-inline"></div>
										</label>
									<input type="hidden" value="<?php echo $meta_arr['ihc_authorize_status'];?>" name="ihc_authorize_status" id="ihc_authorize_status" />
							</div>
							<p><?php esc_html_e('For recurring payments, the minimum time value is 7 days.', 'ihc');?></p>
							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>
						</div>
					</div>
				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Authorize.net Settings:', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line">
							<label class="iump-labels"><?php esc_html_e('Login ID:', 'ihc');?></label>
							<input type="text" value="<?php echo $meta_arr['ihc_authorize_login_id'];?>" name="ihc_authorize_login_id" class="ihc-input-limited-width" />
						</div>
						<div class="iump-form-line">
							<label class="iump-labels"><?php esc_html_e('Transaction Key:', 'ihc');?></label>
							<input type="text" value="<?php echo $meta_arr['ihc_authorize_transaction_key'];?>" name="ihc_authorize_transaction_key" class="ihc-input-limited-width" />
						</div>
						<div class="iump-form-line iump-no-border">
								<label class="iump-labels"><?php esc_html_e('Enable Sandbox', 'ihc');?></label> <input type="checkbox" onClick="checkAndH(this, '#enable_authorize_sandbox');" <?php if($meta_arr['ihc_authorize_sandbox']) echo 'checked';?> />
								<input type="hidden" name="ihc_authorize_sandbox" value="<?php echo $meta_arr['ihc_authorize_sandbox'];?>" id="enable_authorize_sandbox" />
						</div>

						<div class="iump-form-line">
							<?php
								$site_url = site_url();
								$site_url = trailingslashit($site_url);
								$notify_url = add_query_arg('ihc_action', 'authorize', $site_url);
								?>
								<strong>Important:</strong> <?php esc_html_e(" set your 'Silent Post URL' to: ");
								echo '<strong>' . $notify_url . '</strong>'; /// admin_url("admin-ajax.php") . "?action=ihc_twocheckout_ins"
							?>
						</div>

						<div class="ihc-payment-steps-wrapper">
							<ul class="ihc-info-list">
								<li><?php esc_html_e('1. Go to', 'ihc');?> <a href="http://authorize.net" target="_blank">http://authorize.net</a> <?php echo esc_html__(' (or ', 'ihc');?> <a href="https://sandbox.authorize.net/" target="_blank">https://sandbox.authorize.net/</a> <?php echo esc_html__('if you want to use sandbox) and login with username and password.', 'ihc');?></li>
								<li><?php esc_html_e('2. After that click on "Account". ', 'ihc');?></li>
								<li><?php echo esc_html__('3. In "Transaction Format Settings" you will find "Silent Post URL", "Response/Receipt URLs" and "Relay Response". Set them to: ', 'ihc'). '<strong>' . $notify_url . '</strong>';?></li>
								<li><?php esc_html_e('4. In the "Security Settings" section you will find following link: "API Credentials & Keys", click on it.', 'ihc');?></li>
								<li><?php esc_html_e('5. On this page you will find the "Login ID" and "Transaction Key".', 'ihc');?></li>
							</ul>
						</div>
						<div class="iump-form-line">
                        	<p><?php esc_html_e('For Test/Sandbox mode use the next credentials available:', 'ihc');?></p>
                        	<a href="https://developer.authorize.net/hello_world/testing_guide/" target="_blank">https://developer.authorize.net/hello_world/testing_guide/</a>
                            <p><?php esc_html_e('Example:', 'ihc');?></p>
                            <p><?php esc_html_e('Credit Card: ', 'ihc');?>370000000000002</p>
                            <p><?php esc_html_e('Expire Time: ', 'ihc');?>1222</p>
                        </div>
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>
					</div>
				</div>

				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Extra Settings:', 'ihc');?></h3>
					<div class="inside">
						<div class="row ihc-row-no-margin">
						<div class="col-xs-4">
						<div class="iump-form-line iump-no-border input-group">
							<span class="input-group-addon">Label:</span>
							<input type="text" name="ihc_authorize_label" value="<?php echo $meta_arr['ihc_authorize_label'];?>"  class="form-control" />
						</div>

						<div class="iump-form-line iump-no-border input-group">
							<span class="input-group-addon"><?php esc_html_e('Order:', 'ihc');?></span>
							<input type="number" min="1" name="ihc_authorize_select_order" value="<?php echo $meta_arr['ihc_authorize_select_order'];?>" class="form-control" />
						</div>
					</div>

					</div>
					<!-- developer -->
					<div class="row ihc-row-no-margin">
					<div class="col-xs-4">
					<div class="input-group">
						 <h4><?php esc_html_e('Short Description', 'ihc');?></h4>
							 <textarea name="ihc_authorize_short_description" class="form-control" rows="2" cols="125" placeholder="<?php esc_html_e('write a short description', 'ihc');?>"><?php echo isset( $meta_arr['ihc_authorize_short_description'] ) ? stripslashes($meta_arr['ihc_authorize_short_description']) : '';?></textarea>
					 </div>
					</div>
				</div>
					 <!-- end developer -->
					<div class="ihc-wrapp-submit-bttn iump-submit-form">
					 <input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
				 </div>
				</div>

			</form>
			<?php
		break;

		case 'twocheckout':
			if (isset($_POST['ihc_save']) && !empty($_POST['ihc-payment-settings-nonce']) && wp_verify_nonce( $_POST['ihc-payment-settings-nonce'], 'ihc-payment-settings-nonce' ) ){
					//ihc_save_update_metas('payment_twocheckout');//save update metas
					ihc_save_update_trimmed_metas('payment_twocheckout'); // save update metas without extra spaces
			}
			$meta_arr = ihc_return_meta_arr('payment_twocheckout');//getting metas
			echo ihc_check_default_pages_set();//set default pages message
			echo ihc_check_payment_gateways();
			echo ihc_is_curl_enable();
			do_action( "ihc_admin_dashboard_after_top_menu" );
			$pages = ihc_get_all_pages();
			?>
			<div class="iump-page-title">Ultimate Membership Pro -
				<span class="second-text">
					<?php esc_html_e('2Checkout Services', 'ihc');?>
				</span>
			</div>
			<form  method="post">
				<input type="hidden" name="ihc-payment-settings-nonce" value="<?php echo wp_create_nonce( 'ihc-payment-settings-nonce' );?>" />
				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('2Checkout Activation:', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line">
							<h4><?php esc_html_e('Once all Settings are properly done, Activate the Payment Getway for further use.', 'ihc');?> </h4>
							<label class="iump_label_shiwtch ihc-switch-button-margin">
								<?php $checked = ($meta_arr['ihc_twocheckout_status']) ? 'checked' : '';?>
								<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_twocheckout_status');" <?php echo $checked;?> />
								<div class="switch ihc-display-inline"></div>
							</label>
							<input type="hidden" value="<?php echo $meta_arr['ihc_twocheckout_status'];?>" name="ihc_twocheckout_status" id="ihc_twocheckout_status" />
						</div>
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>
					</div>
				</div>
				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('2Checkout Settings:', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line">
							<label class="iump-labels"><?php esc_html_e('API Username:', 'ihc');?></label>
							<input type="text" value="<?php echo $meta_arr['ihc_twocheckout_api_user'];?>" name="ihc_twocheckout_api_user" class="ihc-input-limited-width" />
						</div>
						<div class="iump-form-line">
							<label class="iump-labels"><?php esc_html_e('API Password:', 'ihc');?></label>
							<input type="text" value="<?php echo $meta_arr['ihc_twocheckout_api_pass'];?>" name="ihc_twocheckout_api_pass" class="ihc-input-limited-width" />
						</div>
						<div class="iump-form-line">
							<label class="iump-labels"><?php esc_html_e('API Private Key:', 'ihc');?></label>
							<input type="text" value="<?php echo $meta_arr['ihc_twocheckout_private_key'];?>" name="ihc_twocheckout_private_key" class="ihc-input-limited-width" />
						</div>
						<div class="iump-form-line">
							<label class="iump-labels"><?php esc_html_e('Merchant Code (Account Number):', 'ihc');?></label>
							<input type="text" value="<?php echo $meta_arr['ihc_twocheckout_account_number'];?>" name="ihc_twocheckout_account_number" class="ihc-input-limited-width" />
						</div>
						<div class="iump-form-line">
							<label class="iump-labels"><?php esc_html_e('Secret Word:', 'ihc');?></label>
							<input type="text" value="<?php echo $meta_arr['ihc_twocheckout_secret_word'];?>" name="ihc_twocheckout_secret_word" class="ihc-input-limited-width" />
						</div>
						<div class="iump-form-line">
							<label class="iump-labels"><?php esc_html_e('Enable Sandbox', 'ihc');?></label> <input type="checkbox" onClick="checkAndH(this, '#ihc_twocheckout_sandbox');" <?php if($meta_arr['ihc_twocheckout_sandbox']) echo 'checked';?> />
							<input type="hidden" name="ihc_twocheckout_sandbox" value="<?php echo $meta_arr['ihc_twocheckout_sandbox'];?>" id="ihc_twocheckout_sandbox" />
						</div>
						<div class="iump-form-line">
							<?php
								$site_url = site_url();
								$site_url = trailingslashit($site_url);
								$notify_url = add_query_arg('ihc_action', 'twocheckout', $site_url);
								?>
								<strong>Important:</strong> <?php esc_html_e(" set your 'Web Hook URL'(ISN) and Your 'Approved URL' to: ");
								echo '<strong>' . $notify_url . '</strong>'; /// admin_url("admin-ajax.php") . "?action=ihc_twocheckout_ins"
							?>
						</div>

						<div class="ihc-payment-steps-wrapper">
							<ul class="ihc-info-list">
								<li><?php esc_html_e('1. Go to ', 'ihc');?> <a target="_blank" href="https://www.2checkout.com/">2checkout.com</a><?php echo esc_html__(' and login with username and password.', 'ihc'); ?></li>
								<li><?php esc_html_e('2. Go to "Integrations" -> "Webhooks & API". You will find "Merchant Code", "Secret Key", "API Private Key", "Secret word".', 'ihc'); ?></li>
								<li><?php esc_html_e('3. In "Instant Notification System (INS)" section activate the service.', 'ihc'); ?></li>
								<li><?php esc_html_e('4. In "Redirect URL" section click "Enable return after sale" and set an approve URL.', 'ihc'); ?></li>
								<li><?php echo esc_html__('5. In "INS settings" add a new endpoint with', 'ihc') . " <b>" . $notify_url ."</b>"; ?></li>
								<li><?php echo esc_html__('6. In "IPN settings" add your IPN URL with', 'ihc') . " <b>" . $notify_url ."</b>"; ?></li>
							</ul>
						</div>

						<div class="iump-form-line">
			          <p><?php esc_html_e('For Test/Sandbox mode use the next credentials available:', 'ihc');?></p>
			          <a href="https://knowledgecenter.2checkout.com/Documentation/09Test_ordering_system/01Test_payment_methods" target="_blank">https://knowledgecenter.2checkout.com/Documentation/09Test_ordering_system/01Test_payment_methods</a>
			          <p><?php esc_html_e('Example:', 'ihc');?></p>
			          <p><?php esc_html_e('Credit Card: ', 'ihc');?>4111111111111111</p>
			          <p><?php esc_html_e('Expiration Month: ', 'ihc');?>12</p>
			          <p><?php esc_html_e('Expiration Year: ', 'ihc');?><?php echo date("Y") + 1;?></p>
								<p><?php esc_html_e('CVV: ', 'ihc');?>123</p>
								<p><?php esc_html_e('First Name: ', 'ihc');?>John</p>
								<p><?php esc_html_e('Last Name: ', 'ihc');?>Doe</p>
			      </div>

						<div class="row ihc-row-no-margin">
				 			<div class="col-xs-4">
									<label class="iump-labels-special"><?php esc_html_e('Redirect Page after Payment:', 'ihc');?></label>
									<select name="ihc_twocheckout_return_url" class="form-control">
									 <option value="-1" <?php if($meta_arr['ihc_twocheckout_return_url']==-1)echo 'selected';?> >...</option>
									 <?php
										 if($pages){
											 foreach($pages as $k=>$v){
												 ?>
													 <option value="<?php echo $k;?>" <?php if ($meta_arr['ihc_twocheckout_return_url']==$k) echo 'selected';?> ><?php echo $v;?></option>
												 <?php
											 }
										 }
									 ?>
									</select>
							 </div>
						 </div>

						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>
					</div>
				</div>

				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Extra Settings:', 'ihc');?></h3>
					<div class="inside">
						<div class="row ihc-row-no-margin">
							<div class="col-xs-4">
						<div class="iump-form-line iump-no-border input-group">
							<span class="input-group-addon"><?php esc_html_e('Label:', 'ihc');?></span>
							<input type="text" name="ihc_twocheckout_label" value="<?php echo $meta_arr['ihc_twocheckout_label'];?>" class="form-control" />
						</div>

						<div class="iump-form-line iump-no-border input-group">
							<span class="input-group-addon"><?php esc_html_e('Order:', 'ihc');?></span>
							<input type="number" min="1" name="ihc_twocheckout_select_order" value="<?php echo $meta_arr['ihc_twocheckout_select_order'];?>" class="form-control" />
						</div>


					</div>
				</div>
				<!-- developer -->
				  <div class="row ihc-row-no-margin">
				<div class="col-xs-4">
				<div class="input-group">
				   <h4><?php esc_html_e('Short Description', 'ihc');?></h4>
				     <textarea name="ihc_twocheckout_short_description" class="form-control" rows="2" cols="125"  placeholder="<?php esc_html_e('write a short description', 'ihc');?>"><?php echo isset( $meta_arr['ihc_twocheckout_short_description'] ) ? stripslashes($meta_arr['ihc_twocheckout_short_description']) : '';?></textarea>
				 </div>
				</div>
				</div>
				 <!-- end developer -->
				<div class="ihc-wrapp-submit-bttn iump-submit-form">
					<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
				</div>
				</div>
				</div>

			</form>

			<?php
			break;
		case 'braintree':
			if (isset($_POST['ihc_save']) && !empty($_POST['ihc-payment-settings-nonce']) && wp_verify_nonce( $_POST['ihc-payment-settings-nonce'], 'ihc-payment-settings-nonce' ) ){
					//ihc_save_update_metas('payment_braintree');//save update metas
					ihc_save_update_trimmed_metas('payment_braintree'); // save update metas without extra spaces
			}
			$meta_arr = ihc_return_meta_arr('payment_braintree');//getting metas
			echo ihc_check_default_pages_set();//set default pages message
			echo ihc_check_payment_gateways();
			echo ihc_is_curl_enable();
			do_action( "ihc_admin_dashboard_after_top_menu" );
			?>
				<div class="iump-page-title">Ultimate Membership Pro -
					<span class="second-text">
						<?php esc_html_e('Braintree Services', 'ihc');?>
					</span>
				</div>
				<form  method="post">
					<input type="hidden" name="ihc-payment-settings-nonce" value="<?php echo wp_create_nonce( 'ihc-payment-settings-nonce' );?>" />
					<div class="ihc-stuffbox">
						<h3><?php esc_html_e('Braintree Activation:', 'ihc');?></h3>
						<div class="inside">
							<div class="iump-form-line">
								<h4><?php esc_html_e('Once all Settings are properly done, Activate the Payment Getway for further use.', 'ihc');?> </h4>
								<label class="iump_label_shiwtch ihc-switch-button-margin">
									<?php $checked = ($meta_arr['ihc_braintree_status']) ? 'checked' : '';?>
									<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_braintree_status');" <?php echo $checked;?> />
									<div class="switch ihc-display-inline"></div>
								</label>
								<input type="hidden" value="<?php echo $meta_arr['ihc_braintree_status'];?>" name="ihc_braintree_status" id="ihc_braintree_status" />
							</div>
							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>
						</div>
					</div>

						<div class="ihc-stuffbox">
							<h3><?php esc_html_e('Braintree Settings:', 'ihc');?></h3>
							<div class="inside">

								<div class="iump-form-line iump-no-border">
									<label class="iump-labels"><?php esc_html_e('Sandbox', 'ihc');?></label>
									<label class="iump_label_shiwtch ihc-switch-button-margin">
										<?php $checked = ($meta_arr['ihc_braintree_sandbox']) ? 'checked' : '';?>
										<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_braintree_sandbox');" <?php echo $checked;?> />
										<div class="switch ihc-display-inline"></div>
									</label>
									<input type="hidden" value="<?php echo $meta_arr['ihc_braintree_sandbox'];?>" name="ihc_braintree_sandbox" id="ihc_braintree_sandbox" />
								</div>

								<div class="iump-form-line iump-no-border">
									<label class="iump-labels"><?php esc_html_e('Merchant ID:', 'ihc');?></label>
									<input type="text" name="ihc_braintree_merchant_id" value="<?php echo $meta_arr['ihc_braintree_merchant_id'];?>" />
								</div>

								<div class="iump-form-line iump-no-border">
									<label class="iump-labels"><?php esc_html_e('Public Key:', 'ihc');?></label>
									<input type="text" name="ihc_braintree_public_key" value="<?php echo $meta_arr['ihc_braintree_public_key'];?>" />
								</div>

								<div class="iump-form-line iump-no-border">
									<label class="iump-labels"><?php esc_html_e('Private Key:', 'ihc');?></label>
									<input type="text" name="ihc_braintree_private_key" value="<?php echo $meta_arr['ihc_braintree_private_key'];?>" />
								</div>
								<div class="iump-form-line">
									<?php
										$site_url = site_url();
										$site_url = trailingslashit($site_url);
										$notify_url = add_query_arg('ihc_action', 'braintree', $site_url);
										?>
										<strong>Important:</strong><?php esc_html_e(" set your Webhook to: ");
										echo '<strong>' . $notify_url . '</strong>'; /// IHC_URL . 'braintree_webhook.php'
									?>
								</div>

								<div class="ihc-payment-steps-wrapper">
									<ul class="ihc-info-list">
										<li><?php echo esc_html__("1. Go to ", 'ihc');?><a href="https://www.braintreepayments.com" target="_blank">https://www.braintreepayments.com</a> <?php echo esc_html__("(or ", 'ihc');?> <a href="https://www.braintreepayments.com/en-ro/sandbox" target="_blank">https://www.braintreepayments.com/en-ro/sandbox</a> <?php echo esc_html__("if you want to use sandbox version) and login with username and password.", 'ihc');?></li>
										<li><?php echo esc_html__('2. After you login go to "Account" section and select "My User". In this page click on "View Authorizations".', 'ihc');?></li>
										<li><?php echo esc_html__('3. In this page you will find the "Public Key", "Private Key" and "Merchant ID".', 'ihc');?></li>
										<li><?php echo esc_html__("4. After You copy and paste this keys You must set the webhook, to do that go to 'Settings' section and select 'Webhook'.", 'ihc');?></li>
										<li><?php echo esc_html__('5. Click on "Create new Webhook" and in the next page check all subscription options and set the "Destination URL" to ', 'ihc') . '<strong>' . $notify_url . '</strong>';?></li>
									</ul>
								</div>

								<div class="iump-form-line">
		               	<p><?php esc_html_e('For Test/Sandbox mode use the next credentials available:', 'ihc');?></p>
		               	<a href="https://developers.braintreepayments.com/guides/credit-cards/testing-go-live/php" target="_blank">https://developers.braintreepayments.com/guides/credit-cards/testing-go-live/php</a>
		                <p><?php esc_html_e('Example:', 'ihc');?></p>
		                <p><?php esc_html_e('Credit Card: ', 'ihc');?>4500600000000061</p>
		                <p><?php esc_html_e('Expiration Month: ', 'ihc');?>12</p>
		                <p><?php esc_html_e('Expiration Year: ', 'ihc');?><?php echo date("Y") + 1;?></p>
										<p><?php esc_html_e('CVV: ', 'ihc');?>123</p>
										<p><?php esc_html_e('First Name: ', 'ihc');?>John</p>
										<p><?php esc_html_e('Last Name: ', 'ihc');?>Doe</p>
		            </div>


								<div class="ihc-wrapp-submit-bttn iump-submit-form">
									<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
								</div>
							</div>
						</div>


					<div class="ihc-stuffbox">
						<h3><?php esc_html_e('Extra Settings:', 'ihc');?></h3>
						<div class="inside">
							<div class="row ihc-row-no-margin">
                <div class="col-xs-4">
							<div class="iump-form-line iump-no-border input-group">
								<span class="input-group-addon"><?php esc_html_e('Label:', 'ihc');?></span>
								<input type="text" name="ihc_braintree_label" value="<?php echo $meta_arr['ihc_braintree_label'];?>" class="form-control" />
							</div>

							<div class="iump-form-line iump-no-border input-group">
								<span class="input-group-addon"><?php esc_html_e('Order:', 'ihc');?></span>
								<input type="number" min="1" name="ihc_braintree_select_order" value="<?php echo $meta_arr['ihc_braintree_select_order'];?>" class="form-control" />
							</div>
						</div>
					</div>
					<!-- developer -->
					  <div class="row ihc-row-no-margin">
					<div class="col-xs-4">
					<div class="input-group">
					   <h4><?php esc_html_e('Short Description', 'ihc');?></h4>
					     <textarea name="ihc_braintree_short_description" class="form-control" rows="2" cols="125"  placeholder="<?php esc_html_e('write a short description', 'ihc');?>"><?php echo isset( $meta_arr['ihc_braintree_short_description'] ) ? stripslashes($meta_arr['ihc_braintree_short_description']) : '';?></textarea>
					 </div>
					</div>
					</div>
					 <!-- end developer -->
					<div class="ihc-wrapp-submit-bttn iump-submit-form">
						<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
					</div>
				</div>
			</div>
				</form>
				<?php
			break;
		case 'bank_transfer':
			if (isset($_POST['ihc_save']) && !empty($_POST['ihc-payment-settings-nonce']) && wp_verify_nonce( $_POST['ihc-payment-settings-nonce'], 'ihc-payment-settings-nonce' ) ){
					//ihc_save_update_metas('payment_bank_transfer');//save update metas
					ihc_save_update_trimmed_metas('payment_bank_transfer'); // save update metas without extra spaces

			}
			$meta_arr = ihc_return_meta_arr('payment_bank_transfer');//getting metas
			echo ihc_check_default_pages_set();//set default pages message
			echo ihc_check_payment_gateways();
			echo ihc_is_curl_enable();
			do_action( "ihc_admin_dashboard_after_top_menu" );
			?>
				<div class="iump-page-title">Ultimate Membership Pro -
					<span class="second-text">
						<?php esc_html_e('Payment Services', 'ihc');?>
					</span>
				</div>
			<form  method="post">
				<input type="hidden" name="ihc-payment-settings-nonce" value="<?php echo wp_create_nonce( 'ihc-payment-settings-nonce' );?>" />
				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Bank Transfer Activation:', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line">
							<h4><?php esc_html_e('Enable Bank Transfer', 'ihc');?> </h4>
                            <p><?php esc_html_e('Once all Settings are properly done, Activate the Payment Getway for further use.', 'ihc');?> </p>
                            <p><?php esc_html_e('Take payments in person via bank/wire transer', 'ihc');?> </p>
							<label class="iump_label_shiwtch ihc-switch-button-margin">
								<?php $checked = ($meta_arr['ihc_bank_transfer_status']) ? 'checked' : '';?>
								<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_bank_transfer_status');" <?php echo $checked;?> />
								<div class="switch ihc-display-inline"></div>
							</label>
							<input type="hidden" value="<?php echo $meta_arr['ihc_bank_transfer_status'];?>" name="ihc_bank_transfer_status" id="ihc_bank_transfer_status" />
						</div>
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>
					</div>
				</div>
				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Bank Transfer Instructions Message:', 'ihc');?></h3>

					<div class="inside">
                    	<div class="iump-form-line">
                    		<p><?php esc_html_e('Instructions will be provided to buyer via trank you page. Use available {constants} for a dynamic and complete description', 'ihc');?></p>
                        </div>
							<div class="ihc-payment-bank-editor">
								<?php wp_editor( stripslashes($meta_arr['ihc_bank_transfer_message']), 'ihc_bank_transfer_message', array('textarea_name'=>'ihc_bank_transfer_message', 'quicktags'=>TRUE) );?>
							</div>
							<div class="ihc-payment-bank-editor-constants">
								<div>{siteurl}</div>
								<div>{username}</div>
								<div>{first_name}</div>
								<div>{last_name}</div>
								<div>{user_id}</div>
								<div>{level_id}</div>
								<div>{level_name}</div>
								<div>{amount}</div>
								<div>{currency}</div>
							</div>
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>
					</div>
				</div>

				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Extra Settings', 'ihc');?></h3>
					<div class="inside">
                    <div class="row ihc-row-no-margin">
                  		<div class="col-xs-4">
						<div class="iump-form-line iump-no-border input-group">
							<span class="input-group-addon" ><?php esc_html_e('Label:', 'ihc');?></span>
							<input type="text" name="ihc_bank_transfer_label" value="<?php echo $meta_arr['ihc_bank_transfer_label'];?>"  class="form-control"/>
						</div>

						<div class="iump-form-line iump-no-border input-group">
							<span class="input-group-addon" ><?php esc_html_e('Order:', 'ihc');?></span>
							<input type="number" min="1" name="ihc_bank_transfer_select_order" value="<?php echo $meta_arr['ihc_bank_transfer_select_order'];?>"  class="form-control"/>
						</div>

											</div>
          					</div>
										<!-- developer -->
										  <div class="row ihc-row-no-margin">
										<div class="col-xs-4">
										<div class="input-group">
										   <h4><?php esc_html_e('Short Description', 'ihc');?></h4>
										     <textarea name="ihc_bank_transfer_short_description" class="form-control" rows="2" cols="125" placeholder="<?php esc_html_e('write a short description', 'ihc');?>"><?php echo isset( $meta_arr['ihc_bank_transfer_short_description'] ) ? stripslashes($meta_arr['ihc_bank_transfer_short_description']) : '';?></textarea>
										 </div>
										</div>
										</div>
										 <!-- end developer -->
								<div class="ihc-wrapp-submit-bttn iump-submit-form">
									<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
								</div>
          </div>
				</div>

			</form>

			<?php
			break;

		case 'mollie':
		if (isset($_POST['ihc_save']) && !empty($_POST['ihc-payment-settings-nonce']) && wp_verify_nonce( $_POST['ihc-payment-settings-nonce'], 'ihc-payment-settings-nonce' ) ){
				//ihc_save_update_metas('payment_mollie');//save update metas
				ihc_save_update_trimmed_metas('payment_mollie'); // save update metas without extra spaces

		}
		$pages = ihc_get_all_pages();//getting pages
		$meta_arr = ihc_return_meta_arr('payment_mollie');//getting metas
		echo ihc_check_default_pages_set();//set default pages message
		echo ihc_check_payment_gateways();
		echo ihc_is_curl_enable();
		do_action( "ihc_admin_dashboard_after_top_menu" );
		?>
			<div class="iump-page-title">Ultimate Membership Pro -
				<span class="second-text">
					<?php esc_html_e('Mollie Services', 'ihc');?>
				</span>
			</div>
			<form  method="post">
				<input type="hidden" name="ihc-payment-settings-nonce" value="<?php echo wp_create_nonce( 'ihc-payment-settings-nonce' );?>" />
				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Mollie Activation:', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line">
							<h4><?php esc_html_e('Once all Settings are properly done, Activate the Payment Getway for further use.', 'ihc');?> </h4>
							<label class="iump_label_shiwtch ihc-switch-button-margin">
								<?php $checked = ($meta_arr['ihc_mollie_status']) ? 'checked' : '';?>
								<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_mollie_status');" <?php echo $checked;?> />
								<div class="switch ihc-display-inline"></div>
							</label>
							<input type="hidden" value="<?php echo $meta_arr['ihc_mollie_status'];?>" name="ihc_mollie_status" id="ihc_mollie_status" />
						</div>
                <div class="ihc-error-global-dashboard-message">
										<p><?php esc_html_e( '1. Be sure you set into your Mollie dashboard Payment Methods at: ', 'ihc');?>  <strong>Credit Card.</strong><br/>
										<p><?php esc_html_e('We recommend CreditCard as main Payment Method. In order to manage Payment Methods into your Mollie account please access: Settings -> Website Profiles -> Payment Methods.', 'ihc');?></p>
										<p><?php esc_html_e('2. If no Payment Method is set into your mollie dashboard, the system will not work properly.', 'ihc');?> </p>
										<p><?php esc_html_e('3. Trial option works only with "Trial Period Price" set with a minimum 0.01 value.', 'ihc');?> </p>
										<p><?php esc_html_e('4. Coupons with 100% discounts are not accepted.', 'ihc');?> </p>
								</div>
								<div class="iump-form-line">
									<p><?php esc_html_e('Each payment method has a different minimum and maximum amount set by the banks. Check ', 'ihc')?> <a target="_blank" href="https://help.mollie.com/hc/en-us/articles/115000667365-What-are-the-minimum-and-maximum-amounts-per-payment-method-">minimum and maximum amounts per payment method</a>.</p>
									<p><?php esc_html_e('Check what currencies are supported by Mollie for payments in non-EUR in', 'ihc');?><a target="_blank" href="https://docs.mollie.com/payments/multicurrency"> Supported currencies</a>.</p>
								</div>

								<div class="iump-form-line iump-special-line">
	                            <div class="row ihc-row-no-margin">
	                  			<div class="col-xs-4">
									<label class="iump-labels-special"><?php esc_html_e('Redirect Page after Payment:', 'ihc');?></label>
									<select name="ihc_mollie_return_page" class="form-control">
										<option value="-1" <?php if($meta_arr['ihc_mollie_return_page']==-1)echo 'selected';?> >...</option>
										<?php
											if($pages){
												foreach($pages as $k=>$v){
													?>
														<option value="<?php echo $k;?>" <?php if ($meta_arr['ihc_mollie_return_page']==$k) echo 'selected';?> ><?php echo $v;?></option>
													<?php
												}
											}
										?>
									</select>
	                             </div>
	                             </div>
								</div>

						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>
					</div>
					 </div>
					<div class="ihc-stuffbox">
						<h3><?php esc_html_e('Mollie Settings:', 'ihc');?></h3>
						<div class="inside">

							<div class="iump-form-line iump-no-border">
								<label class="iump-labels"><?php esc_html_e('API key:', 'ihc');?></label>
								<input type="text" name="ihc_mollie_api_key" value="<?php echo $meta_arr['ihc_mollie_api_key'];?>" />
							</div>
                                <h4><?php esc_html_e('How to setup?', 'ihc'); ?></h4>
							<div class="ihc-payment-steps-wrapper">
									<ul class="ihc-info-list">
											<li>1. <?php esc_html_e('This payment service requires PHP > 5.6 and up-to-date OpenSSL (or other SSL/TLS toolkit). ', 'ihc');?></li>
											<li>2. <?php esc_html_e('Register at: ', 'ihc');?> <a href="https://www.mollie.com" target="_blank">https://www.mollie.com</a></li>
                                             <li>3. <?php esc_html_e('After you login with your username and password go to: ', 'ihc');?> <a href="https://www.mollie.com/dashboard/payments" target="_blank">https://www.mollie.com/dashboard/payments</a></li>
                                            <li>4. <?php esc_html_e('Go to \'Settings->Website profiles\' section and click on "Create a new website profile." ', 'ihc');?> </li>
											<li>5. <?php esc_html_e('Complete all required details for your current website. ', 'ihc');?> </li>
                                            <li>6. <?php esc_html_e('Go to \'Settings->Website profiles\' and click on "Payment methods" on your website section.', 'ihc');?> </li>
                                            <li>7. <?php esc_html_e('Activate and setup at least one of accepted Payment methods for recurring charges: \'Credit Card\'. ', 'ihc');?> </li>
                                            <li>8. <?php esc_html_e('\'Note:\' if no payment method is activated or an incompatible one payments can not be taken.', 'ihc');?> </li>
                                           <li>9. <?php esc_html_e('Go to \'Developers->API keys\' and copy the \'Test API\' or \'Live API\' key and paste it here.', 'ihc');?> </li>

									</ul>
								</div>

							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>
						</div>
					</div>

				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Extra Settings:', 'ihc');?></h3>
					<div class="inside">
						<div class="row ihc-row-no-margin">
                  			<div class="col-xs-4">
						<div class="iump-form-line iump-no-border input-group">
							<span class="input-group-addon"><?php esc_html_e('Label:', 'ihc');?></span>
							<input type="text" name="ihc_mollie_label" value="<?php echo $meta_arr['ihc_mollie_label'];?>" class="form-control"/>
						</div>

						<div class="iump-form-line iump-no-border input-group">
							<span class="input-group-addon"><?php esc_html_e('Order:', 'ihc');?></span>
							<input type="number" min="1" name="ihc_mollie_select_order" value="<?php echo $meta_arr['ihc_mollie_select_order'];?>" class="form-control"/>
						</div>


						</div>
					</div>
					<!-- developer -->
					  <div class="row ihc-row-no-margin">
					<div class="col-xs-4">
					<div class="input-group">
					   <h4><?php esc_html_e('Short Description', 'ihc');?></h4>
					     <textarea name="ihc_mollie_short_description" class="form-control" rows="2" cols="125"  placeholder="<?php esc_html_e('write a short description', 'ihc');?>"><?php echo isset( $meta_arr['ihc_mollie_short_description'] ) ? stripslashes($meta_arr['ihc_mollie_short_description'] ) : '';?></textarea>
					 </div>
					</div>
					</div>
					 <!-- end developer -->
					<div class="ihc-wrapp-submit-bttn iump-submit-form">
						<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>
				</div>
			</div>

			</form>
			<?php
			break;
		case 'paypal_express_checkout':
			if (isset($_POST['ihc_save']) && !empty($_POST['ihc-payment-settings-nonce']) && wp_verify_nonce( $_POST['ihc-payment-settings-nonce'], 'ihc-payment-settings-nonce' ) ){
					//ihc_save_update_metas('payment_paypal_express_checkout');//save update metas
					ihc_save_update_trimmed_metas('payment_paypal_express_checkout'); // save update metas without extra spaces

			}
			$meta_arr = ihc_return_meta_arr('payment_paypal_express_checkout');//getting metas
			$pages = ihc_get_all_pages();//getting pages
			echo ihc_check_default_pages_set();//set default pages message
			echo ihc_check_payment_gateways();
			echo ihc_is_curl_enable();
			do_action( "ihc_admin_dashboard_after_top_menu" );
			$siteUrl = site_url();
			$siteUrl = trailingslashit($siteUrl);
			?>
			<div class="iump-page-title">Ultimate Membership Pro -
				<span class="second-text">
					<?php esc_html_e('Payments Services', 'ihc');?>
				</span>
			</div>
			<form  method="post">
					<input type="hidden" name="ihc-payment-settings-nonce" value="<?php echo wp_create_nonce( 'ihc-payment-settings-nonce' );?>" />
					<div class="ihc-stuffbox">
						<h3><?php esc_html_e('PayPal Express Checkout Activation:', 'ihc');?></h3>
						<div class="inside">
							<div class="iump-form-line">
                            	<h4><?php esc_html_e('Enable PayPal Express Checkout', 'ihc');?> </h4>
								<p><?php esc_html_e('Once everything is properly set up, activate the payment gateway for further use.', 'ihc');?> </p>
								<label class="iump_label_shiwtch ihc-switch-button-margin">
								<?php $checked = ($meta_arr['ihc_paypal_express_checkout_status']) ? 'checked' : '';?>
								<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_paypal_express_checkout_status');" <?php echo $checked;?> />
								<div class="switch ihc-display-inline"></div>
							</label>
							<input type="hidden" value="<?php echo $meta_arr['ihc_paypal_express_checkout_status'];?>" name="ihc_paypal_express_checkout_status" id="ihc_paypal_express_checkout_status" />
							</div>

							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>
						</div>
					</div>
					<div class="ihc-stuffbox">

						<h3><?php esc_html_e('PayPal Express Checkout Settings:', 'ihc');?></h3>

						<div class="inside">
                        <div class="row ihc-row-no-margin">
                  			<div class="col-xs-6">
							<div class="iump-form-line input-group">
									<span class="input-group-addon"><?php esc_html_e('API Username:', 'ihc');?></span>
                                    <input type="text" value="<?php echo $meta_arr['ihc_paypal_express_checkout_user'];?>" name="ihc_paypal_express_checkout_user" class="form-control" />
							</div>

							<div class="iump-form-line input-group">
									<span class="input-group-addon"><?php esc_html_e('API Password:', 'ihc');?></span>
                                    <input type="text" value="<?php echo $meta_arr['ihc_paypal_express_checkout_password'];?>" name="ihc_paypal_express_checkout_password"  class="form-control"/>
							</div>

							<div class="iump-form-line input-group">
									<span class="input-group-addon"><?php esc_html_e('API Signature:', 'ihc');?></span>
                                    <input type="text" value="<?php echo $meta_arr['ihc_paypal_express_checkout_signature'];?>" name="ihc_paypal_express_checkout_signature"  class="form-control"/>
							</div>
							<div class="iump-form-line iump-no-border">
								<input type="checkbox" onClick="checkAndH(this, '#enable_sandbox');" <?php if($meta_arr['ihc_paypal_express_checkout_sandbox']) echo 'checked';?> />
								<input type="hidden" name="ihc_paypal_express_checkout_sandbox" value="<?php echo $meta_arr['ihc_paypal_express_checkout_sandbox'];?>" id="enable_sandbox" />
                                <label class="iump-labels"><?php esc_html_e('Enable PayPal Sandbox', 'ihc');?></label>
							</div>
                            <div class="iump-form-line">
                            <p><?php esc_html_e('PayPal sandbox mode can be used to testing purpose. A Sandbox merchant account and additional Sandbox buyer account is required.', 'ihc');?></p>
                            </div>
							<h4><?php esc_html_e( 'How to get required credentials', 'ihc' );?> </h4>
							<div class="iump-form-line iump-no-border">
								<p>1. <?php esc_html_e( 'Access the "Account Settings" section', 'ihc' );?></p>
								<p>2. <?php esc_html_e( 'Go to PayPal "My Profile" (top-right settings icon)', 'ihc' );?></p>
								<p>3. <?php esc_html_e( 'Find "API access" option into "Website Payments" section and click on "Update".', 'ihc' );?></p>
								<p>4. <?php esc_html_e( 'If you do not have one, create with "Request API signature" option', 'ihc' );?></p>
								<p>5. <?php esc_html_e( 'Copy credentials received on the next page (API Username, API Password, Signature)', 'ihc' );?></p>
								<p><?php esc_html_e( 'for sandbox', 'ihc' );?> <a target="_blank" href="https://www.sandbox.paypal.com/businessprofile/mytools/apiaccess/firstparty/signature">https://www.sandbox.paypal.com/businessprofile/mytools/apiaccess/firstparty/signature</a></p>
								<p><?php esc_html_e( 'for live environment', 'ihc' );?> <a target="_blank" href="https://www.paypal.com/businessprofile/mytools/apiaccess/firstparty/signature">https://www.paypal.com/businessprofile/mytools/apiaccess/firstparty/signature</a></p>
							</div>


                            <h4><?php esc_html_e( 'How to setup IPN', 'ihc' );?> </h4>
							<div class="iump-form-line iump-no-border">
                            	<p><?php esc_html_e('In order to use PayPal Express Checkout you must set your IPN. First go to: ', 'ihc');?><a href="https://www.paypal.com/signin" target="_blank">https://www.paypal.com/signin</a><?php esc_html_e(' or: ', 'ihc');?>
									<a href="https://www.sandbox.paypal.com/signin" target="_blank">https://www.sandbox.paypal.com/signin</a>
									<?php esc_html_e(' if you are using sandbox', 'ihc');?>
							</p>
							<p><?php esc_html_e("Login with your credentials and go to 'Account Settings' (top-right of page)", 'ihc');?></p>
							<p><?php esc_html_e("After that go to 'Notifications' and next Update the 'Instant payment notifications' ", 'ihc');?></p>
							<p><?php esc_html_e('Set your IPN at: ', 'ihc');?><a href="<?php echo $siteUrl . '?ihc_action=paypal_express_checkout_ipn';?>"><?php echo $siteUrl . '?ihc_action=paypal_express_checkout_ipn';?></a></p>
                            </div>

														<div class="row iump-form-line iump-no-border ihc-row-no-margin">
                  			<div class="col-xs-6">
								<h4><?php esc_html_e('Redirect Page after Payment:', 'ihc');?></h4>
								<select name="ihc_paypal_express_return_page" class="form-control">
									<option value="-1" <?php if($meta_arr['ihc_paypal_express_return_page']==-1)echo 'selected';?> >...</option>
									<?php
										if($pages){
											foreach($pages as $k=>$v){
												?>
													<option value="<?php echo $k;?>" <?php if ($meta_arr['ihc_paypal_express_return_page']==$k) echo 'selected';?> ><?php echo $v;?></option>
												<?php
											}
										}
									?>
								</select>
                             </div>
                             </div>

							<div class="row iump-form-line iump-no-border ihc-row-no-margin">
                   			<div class="col-xs-6">
 								<h4><?php esc_html_e('Redirect Page after cancel Payment:', 'ihc');?></h4>
 								<select name="ihc_paypal_express_return_page_on_cancel" class="form-control">
 									<option value="-1" <?php if($meta_arr['ihc_paypal_express_return_page_on_cancel']==-1)echo 'selected';?> >...</option>
 									<?php
 										if($pages){
 											foreach($pages as $k=>$v){
 												?>
 													<option value="<?php echo $k;?>" <?php if ($meta_arr['ihc_paypal_express_return_page_on_cancel']==$k) echo 'selected';?> ><?php echo $v;?></option>
 												<?php
 											}
 										}
 									?>
 								</select>
                              </div>
                              </div>

							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>
						</div>
                        </div>
                        </div>
					</div>

					<div class="ihc-stuffbox">
						<h3><?php esc_html_e('Extra Settings:', 'ihc');?></h3>
						<div class="inside">
                <div class="row ihc-row-no-margin">
                	<div class="col-xs-4">
							<div class="iump-form-line iump-no-border input-group">
								<span class="input-group-addon"><?php esc_html_e('Label:', 'ihc');?></span>
								<input type="text" name="ihc_paypal_express_checkout_label" value="<?php echo $meta_arr['ihc_paypal_express_checkout_label'];?>"  class="form-control"/>
							</div>

							<div class="iump-form-line iump-no-border input-group">
								<span class="input-group-addon"><?php esc_html_e('Order:', 'ihc');?></span>
								<input type="number" min="1" name="ihc_paypal_express_checkout_select_order" value="<?php echo $meta_arr['ihc_paypal_express_checkout_select_order'];?>"  class="form-control"/>
							</div>
									</div>
                  </div>
									<!-- developer -->
									  <div class="row ihc-row-no-margin">
									<div class="col-xs-4">
									<div class="input-group">
									   <h4><?php esc_html_e('Short Description', 'ihc');?></h4>
									     <textarea name="ihc_paypal_express_short_description" class="form-control" rows="2" cols="125"  placeholder="<?php esc_html_e('write a short description', 'ihc');?>"><?php echo isset( $meta_arr['ihc_paypal_express_short_description'] ) ? stripslashes($meta_arr['ihc_paypal_express_short_description']) : '';?></textarea>
									 </div>
									</div>
									</div>
									 <!-- end developer -->
			 							<div class="ihc-wrapp-submit-bttn iump-submit-form">
			 								<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
			 							</div>
            </div>
					</div>

			</form>
			<?php
			break;
		case 'pagseguro':
			if (isset($_POST['ihc_save']) && !empty($_POST['ihc-payment-settings-nonce']) && wp_verify_nonce( $_POST['ihc-payment-settings-nonce'], 'ihc-payment-settings-nonce' ) ){
					//ihc_save_update_metas('payment_pagseguro');//save update metas
					ihc_save_update_trimmed_metas('payment_pagseguro'); // save update metas without extra spaces

			}
			$meta_arr = ihc_return_meta_arr('payment_pagseguro');//getting metas
			$pages = ihc_get_all_pages();//getting pages
			echo ihc_check_default_pages_set();//set default pages message
			echo ihc_check_payment_gateways();
			echo ihc_is_curl_enable();
			do_action( "ihc_admin_dashboard_after_top_menu" );
			?>
			<div class="iump-page-title">Ultimate Membership Pro -
				<span class="second-text">
					<?php esc_html_e('Payments Services', 'ihc');?>
				</span>
			</div>
			<form  method="post">
				<input type="hidden" name="ihc-payment-settings-nonce" value="<?php echo wp_create_nonce( 'ihc-payment-settings-nonce' );?>" />
					<div class="ihc-stuffbox">
						<h3><?php esc_html_e('Pagseguro Activation:', 'ihc');?></h3>
						<div class="inside">
							<div class="iump-form-line">
								<h4><?php esc_html_e('Once everything is properly set up, activate the payment gateway for further use.', 'ihc');?> </h4>
								<label class="iump_label_shiwtch ihc-switch-button-margin">
								<?php $checked = ($meta_arr['ihc_pagseguro_status']) ? 'checked' : '';?>
								<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_pagseguro_status');" <?php echo $checked;?> />
								<div class="switch ihc-display-inline"></div>
							</label>
							<input type="hidden" value="<?php echo $meta_arr['ihc_pagseguro_status'];?>" name="ihc_pagseguro_status" id="ihc_pagseguro_status" />
							</div>
							<?php
									$siteUrl = site_url();
					        $siteUrl = trailingslashit($siteUrl);
							?>
							<p><?php esc_html_e( 'Use this payment gateway only in Brazil and set the currency type at Real(BRL).', 'ihc' );?></p>
                            <p><?php esc_html_e( 'Recurring Interval options are: WEEKLY (1 week), MONTHLY (1 month), BIMONTHLY (2 months), TRIMONTHLY (3 months), SEMIANNUALLY (6 months), YEARLY (1 year).', 'ihc' );?></p>
							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>
						</div>
					</div>
					<div class="ihc-stuffbox">

						<h3><?php esc_html_e('Pagseguro Express Checkout Settings:', 'ihc');?></h3>

						<div class="inside">
							<div class="iump-form-line">
									<label class="iump-labels"><?php esc_html_e('Pagseguro Account E-mail:', 'ihc');?></label> <input type="text" value="<?php echo $meta_arr['ihc_pagseguro_email'];?>" name="ihc_pagseguro_email" class="ihc-input-limited-width" />
							</div>

							<div class="iump-form-line">
									<label class="iump-labels"><?php esc_html_e('Token:', 'ihc');?></label> <input type="text" value="<?php echo $meta_arr['ihc_pagseguro_token'];?>" name="ihc_pagseguro_token" class="ihc-input-limited-width" />
							</div>
							<div class="iump-form-line iump-no-border">
								<p><?php esc_html_e('1. Login in to ', 'ihc'); ?><?php echo '<a target="_blank" href="https://pagseguro.uol.com.br/">pagseguro.uol.com.br.</a>'; ?></p>
								<p><?php esc_html_e('2. In email field set the email address used to register the account.', 'ihc'); ?></p>
								<p><?php esc_html_e('3. In Online Sale -> Integrations go to Use of API\'s and Generate token', 'ihc');?></p>
								<p><?php esc_html_e('4. In Transaction notification set the Notification URL as: ', 'ihc'); ?><a href="<?php echo $siteUrl . '?ihc_action=pagseguro';?>"><?php echo $siteUrl . '?ihc_action=pagseguro';?></a></p>
							</div>
							<div class="iump-form-line iump-no-border">
									<label class="iump-labels"><?php esc_html_e('Enable Sandbox', 'ihc');?></label> <input type="checkbox" onClick="checkAndH(this, '#ihc_pagseguro_sandbox');" <?php if($meta_arr['ihc_pagseguro_sandbox']) echo 'checked';?> />
									<input type="hidden" name="ihc_pagseguro_sandbox" value="<?php echo $meta_arr['ihc_pagseguro_sandbox'];?>" id="ihc_pagseguro_sandbox" />
							</div>
							<div class="iump-form-line iump-no-border">
								<p><?php esc_html_e('1. Login in to ', 'ihc'); ?> <?php echo  '<a target="_blank" href="https://sandbox.pagseguro.uol.com.br">sandbox.pagseguro.uol.com.br</a>';?></p>
								<p><?php esc_html_e('2. In Sandbox -> Test Buyer you can find the details in order to make test payments.', 'ihc'); ?></p>
							</div>

							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>
						</div>
					</div>

					<div class="ihc-stuffbox">
						<h3><?php esc_html_e('Extra Settings:', 'ihc');?></h3>
						<div class="inside">
							<div class="row ihc-row-no-margin">
                <div class="col-xs-4">
							<div class="iump-form-line iump-no-border input-group">
								<span class="input-group-addon"><?php esc_html_e('Label:', 'ihc');?></span>
								<input type="text" name="ihc_pagseguro_label" value="<?php echo $meta_arr['ihc_pagseguro_label'];?>" class="form-control"/>
							</div>

							<div class="iump-form-line iump-no-border input-group">
								<span class="input-group-addon"><?php esc_html_e('Order:', 'ihc');?></span>
								<input type="number" min="1" name="ihc_pagseguro_select_order" value="<?php echo $meta_arr['ihc_pagseguro_select_order'];?>" class="form-control"/>
							</div>


						</div>
					</div>
					<!-- developer -->
					  <div class="row ihc-row-no-margin">
					<div class="col-xs-4">
					<div class="input-group">
					   <h4><?php esc_html_e('Short Description', 'ihc');?></h4>
					     <textarea name="ihc_pagseguro_short_description" class="form-control" rows="2" cols="125"  placeholder="<?php esc_html_e('write a short description', 'ihc');?>"><?php echo isset( $meta_arr['ihc_pagseguro_short_description'] ) ? stripslashes($meta_arr['ihc_pagseguro_short_description']) : '';?></textarea>
					 </div>
					</div>
					</div>
					 <!-- end developer -->
					 <div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>
				</div>
			</div>
			</form>
			<?php
			break;
		default:

			do_action( 'ihc_payment_gateway_page', $_GET['subtab'] );
			// @description action on admin - dashboard , payment settings. @param type of payment

			break;
	}

}//end of switch
