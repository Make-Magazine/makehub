<div class="uap-wrapper">
<form action="" method="post">

	<input type="hidden" name="uap_admin_forms_nonce" value="<?php echo wp_create_nonce( 'uap_admin_forms_nonce' );?>" />
	
	<div class="uap-stuffbox">
		<h3 class="uap-h3">ReCaptcha</h3>
		<div class="inside">
			<div>
				<?php _e( 'Recaptcha version:', 'uap' );?>
				<select name="uap_recaptcha_version" class="js-uap-change-recaptcha-version" >
							<?php
									if ( empty( $data['metas']['uap_recaptcha_version'] ) ){
											$data['metas']['uap_recaptcha_version'] = 'v2';
									}
							?>
							<option value="v2" <?php if ( $data['metas']['uap_recaptcha_version'] == 'v2' ) echo 'selected';?> ><?php _e( 'reCAPTCHA v2', 'uap');?></option>
							<option value="v3" <?php if ( $data['metas']['uap_recaptcha_version'] == 'v3' ) echo 'selected';?> ><?php _e( 'reCAPTCHA v3', 'uap');?></option>
				</select>
			</div>
			<div class="js-uap-recaptcha-v2-wrapp" style="<?php if ( $data['metas']['uap_recaptcha_version'] == 'v3' ) echo 'display: none;';?>" >
			<h4><?php _e( 'reCAPTCHA v2', 'uap');?></h4>
			<div class="uap-form-line">
            	<div class="input-group" style="margin:0px 0 15px 0; width: 50%;">
				<span class="input-group-addon"><?php _e('SITE KEY:', 'uap');?></span>
                <input type="text" name="uap_recaptcha_public" value="<?php echo $data['metas']['uap_recaptcha_public'];?>" class="form-control uap-deashboard-middle-text-input" />
               </div>
				<div class="input-group" style="margin:0px 0 15px 0; width: 50%;">
				<span class="input-group-addon"><?php _e('SECRET KEY:', 'uap');?></span>
                <input type="text" name="uap_recaptcha_private" value="<?php echo $data['metas']['uap_recaptcha_private'];?>" class="form-control uap-deashboard-middle-text-input" />
                </div>
			<div class="">
											<p><strong><?php _e('How to setup:', 'uap');?></strong></p>
                                            <p>	<?php _e('1. Get Public and Private Keys from', 'uap');?> <a href="https://www.google.com/recaptcha/admin#list" target="_blank"><?php _e('here', 'uap');?></a>.</p>
                                            <p>	<?php _e('2. Click on "Create" button.', 'uap');?></p>
                                            <p>	<?php _e('3. Choose "reCAPTCHA v2" with "Im not a robot" Checkbox.', 'uap');?></p>
                                            <p>	<?php _e('4. Add curent WP website main domain', 'uap');?></p>
                                            <p> <?php _e('5. Accept terms and conditions and Submit', 'uap');?></p>
										</div>
			</div>
			</div>

            <div class="js-uap-recaptcha-v3-wrapp" style="<?php if ( $data['metas']['uap_recaptcha_version'] == 'v2' ) echo 'display: none;';?>" >

            <h4><?php _e( 'reCAPTCHA v3', 'uap');?></h4>

			<div class="uap-form-line">
            <div class="input-group" style="margin:0px 0 15px 0; width: 50%;">
				<span class="input-group-addon"><?php _e('SITE KEY:', 'uap');?></span>
                <input type="text" name="uap_recaptcha_public_v3" value="<?php echo $data['metas']['uap_recaptcha_public_v3'];?>" class="form-control uap-deashboard-middle-text-input" />
			</div>
			<div class="input-group" style="margin:0px 0 15px 0; width: 50%;">
				<span class="input-group-addon"><?php _e('SECRET KEY:', 'uap');?></span>
                <input type="text" name="uap_recaptcha_private_v3" value="<?php echo $data['metas']['uap_recaptcha_private_v3'];?>" class="form-control uap-deashboard-middle-text-input" />
			</div>
			<div class="">
                                        	<p><strong><?php _e('How to setup:', 'uap');?></strong></p>
											<p> <?php _e('1. Get Public and Private Keys from', 'uap');?> <a href="https://www.google.com/recaptcha/admin#list" target="_blank"><?php _e('here', 'uap');?></a>.</p>
                                            <p>	<?php _e('2. Click on "Create" button.', 'uap');?></p>
                                            <p>	<?php _e('3. Choose "reCAPTCHA v3".', 'uap');?></p>
                                            <p>	<?php _e('4. Add curent WP website main domain', 'uap');?></p>
                                            <p> <?php _e('5. Accept terms and conditions and Submit', 'uap');?></p>
										</div>
			</div>
            </div>
			<div style="margin-top: 15px;">
				<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" onClick="" class="button button-primary button-large" />
			</div>
		</div>
	</div>
</form>
<script>
							jQuery( '.js-uap-change-recaptcha-version' ).on( 'change', function( evt ){
									if ( this.value == 'v2' ){
											jQuery( '.js-uap-recaptcha-v2-wrapp' ).css( 'display', 'block' );
											jQuery( '.js-uap-recaptcha-v3-wrapp' ).css( 'display', 'none' );
									} else {
											jQuery( '.js-uap-recaptcha-v2-wrapp' ).css( 'display', 'none' );
											jQuery( '.js-uap-recaptcha-v3-wrapp' ).css( 'display', 'block' );
									}
							});
					</script>
</div>
