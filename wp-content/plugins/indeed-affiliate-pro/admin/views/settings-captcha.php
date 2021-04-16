<div class="uap-wrapper">
<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3">ReCaptcha</h3>
		<div class="inside">
			<div>
				<?php _e( 'Recaptcha version:', 'uap' );?>
				<select name="uap_recaptcha_version" >
							<?php
									if ( empty( $data['metas']['uap_recaptcha_version'] ) ){
											$data['metas']['uap_recaptcha_version'] = 'v2';
									}
							?>
							<option value="v2" <?php if ( $data['metas']['uap_recaptcha_version'] == 'v2' ) echo 'selected';?> >V2</option>
							<option value="v3" <?php if ( $data['metas']['uap_recaptcha_version'] == 'v3' ) echo 'selected';?> >V3</option>
				</select>
			</div>

			<h5><?php _e( 'V2 verion:', 'uap' );?></h5>
			<div class="uap-form-line">
				<label class="uap-labels-special"><?php _e('Public Key:', 'uap');?></label> <input type="text" name="uap_recaptcha_public" value="<?php echo $data['metas']['uap_recaptcha_public'];?>" class="uap-deashboard-middle-text-input" />
			</div>
			<div class="uap-form-line">
				<label class="uap-labels-special"><?php _e('Private Key:', 'uap');?></label> <input type="text" name="uap_recaptcha_private" value="<?php echo $data['metas']['uap_recaptcha_private'];?>" class="uap-deashboard-middle-text-input" />
			</div>
			<div class=""><?php _e('Get Public and Private Keys from', 'uap');?> <a href="https://www.google.com/recaptcha/admin#list" target="_blank"><?php _e('here', 'uap');?></a>.</div>

			<h5><?php _e( 'V3 verion:', 'uap');?></h5>
			<div class="uap-form-line">
				<label class="uap-labels-special"><?php _e('Site Key:', 'uap');?></label> <input type="text" name="uap_recaptcha_public_v3" value="<?php echo $data['metas']['uap_recaptcha_public_v3'];?>" class="uap-deashboard-middle-text-input" />
			</div>
			<div class="uap-form-line">
				<label class="uap-labels-special"><?php _e('Secret Key:', 'uap');?></label> <input type="text" name="uap_recaptcha_private_v3" value="<?php echo $data['metas']['uap_recaptcha_private_v3'];?>" class="uap-deashboard-middle-text-input" />
			</div>
			<div class="">
					<?php _e('Get Public and Private Keys from', 'uap');?> <a href="https://www.google.com/recaptcha/admin#list" target="_blank"><?php _e('here', 'uap');?></a>.
			</div>

			<div style="margin-top: 15px;">
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" onClick="" class="button button-primary button-large" />
			</div>
		</div>
	</div>
</form>
</div>
