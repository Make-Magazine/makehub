<div class="uap-wrapper">

		<form action="" method="post">
			<div class="uap-stuffbox">
				<h3 class="uap-h3"><?php _e('Affiliate FlashBar', 'uap');?></h3>
				<div class="inside">
				<div class="row">
					<div class="col-xs-6">

					<div class="uap-form-line">
						<h2><?php _e('Activate/Hold Info Affiliate FlashBar', 'uap');?></h2>
                        <p><?php _e('The quickest and easiest way to link to any page.FlashBar will show up at the top of every page affiliate visits. To generate an affiliate link just go to the specific page and use the available options from FlashBar.', 'uap');?></p>
						<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
							<?php $checked = ($data['metas']['uap_info_affiliate_bar_enabled']) ? 'checked' : '';?>
							<input type="checkbox" class="uap-switch" onClick="uapCheckAndH(this, '#uap_info_affiliate_bar_enabled');" <?php echo $checked;?> />
							<div class="switch" style="display:inline-block;"></div>
						</label>
						<input type="hidden" name="uap_info_affiliate_bar_enabled" value="<?php echo $data['metas']['uap_info_affiliate_bar_enabled'];?>" id="uap_info_affiliate_bar_enabled" />
          </div>
          			</div>
                    </div>
                      <div class="row">
						<div class="col-xs-11">
						<img src="<?php echo UAP_URL;?>assets/images/flashbar-demo.png" style="margin: 0 auto; display: block; width:100%;"/>
                        </div>
						</div>
                    <div class="uap-line-break"></div>
					<div class="row">
					<div class="col-xs-6">
                    <h3><?php _e('FlashBar Logo', 'uap');?></h3>
								<p><?php _e('Personalize your FlashBar with your logo or leave the default one. The image will show up on the left side of the bar.', 'uap');?></p>
					<div class="form-group">
							<input type="text" class="form-control" style="max-width:80%; display: inline-block;" name="uap_info_affiliate_bar_logo" onClick="openMediaUp(this);" value="<?php echo $data['metas']['uap_info_affiliate_bar_logo'];?>" />
							<i class="fa-uap fa-trash-uap" onclick="jQuery('[name=uap_info_affiliate_bar_logo]').val('');" title="<?php _e('Remove Logo', 'uap');?>" style="display: inline-block;"></i>
					</div>
					</div>
                    </div>
                    <div class="uap-line-break"></div>
					<div class="row">
					<div class="col-xs-4">
							<h3><?php _e( 'Links Section', 'uap' );?></h3>
                            <p><?php _e('Will help affiliates to easily generates affiliates links with just 2 clicks. ', 'uap');?></p>
							<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($data['metas']['uap_info_affiliate_bar_links_section_enabled']) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uapCheckAndH(this, '#uap_info_affiliate_bar_links_section_enabled');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="uap_info_affiliate_bar_links_section_enabled" value="<?php echo $data['metas']['uap_info_affiliate_bar_links_section_enabled'];?>" id="uap_info_affiliate_bar_links_section_enabled" />
							<div class="input-group" style="margin-bottom:20px; margin-top:30px;">
									<span class="input-group-addon" id="basic-addon1"><?php _e( 'Section name', 'uap' );?></span>
									<input type="text" class="form-control" name="uap_info_affiliate_bar_links_label" value="<?php echo stripslashes( $data['metas']['uap_info_affiliate_bar_links_label'] );?>" />
							</div>
							<div class="input-group" style="margin-bottom:20px;">
									<span class="input-group-addon" id="basic-addon1"><?php _e( "'Get affiliate link' label", 'uap' );?></span>
									<input type="text" class="form-control" name="uap_info_affiliate_bar_links_get_label" value="<?php echo stripslashes( $data['metas']['uap_info_affiliate_bar_links_get_label'] );?>" />
							</div>
					</div>
                    </div>
                    <div class="uap-line-break"></div>

					<div class="row">
					<div class="col-xs-4">
							<h3><?php _e( 'Banner Section', 'uap' );?></h3>
							<p><?php _e('Based on current page Featured Image, affiliates may generated banners links. ', 'uap');?></p>
                            <label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($data['metas']['uap_info_affiliate_bar_banner_section_enabled']) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uapCheckAndH(this, '#uap_info_affiliate_bar_banner_section_enabled');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="uap_info_affiliate_bar_banner_section_enabled" value="<?php echo $data['metas']['uap_info_affiliate_bar_banner_section_enabled'];?>" id="uap_info_affiliate_bar_banner_section_enabled" />

							<div class="input-group" style="margin-bottom:20px; margin-top:30px;">
									<span class="input-group-addon" id="basic-addon1"><?php _e( 'Section name', 'uap' );?></span>
									<input type="text" class="form-control" name="uap_info_affiliate_bar_banner_label" value="<?php echo stripslashes( $data['metas']['uap_info_affiliate_bar_banner_label'] );?>" />
							</div>

							<div class="form-group">
									<h6><?php _e( 'Default Banner', 'uap' );?></h6>
									<input type="text" class="form-control" style="width: 80%;display: inline-block;" name="uap_info_affiliate_bar_banner_default_value" onClick="openMediaUp(this);" value="<?php echo $data['metas']['uap_info_affiliate_bar_banner_default_value'];?>" />
									<i class="fa-uap fa-trash-uap" onclick="jQuery('[name=uap_info_affiliate_bar_banner_default_value]').val('');" title="<?php _e('Remove Logo', 'uap');?>" style="display: inline-block;"></i>
							</div>

					</div>
                    </div>
                    <div class="uap-line-break"></div>

					<div class="row">
					<div class="col-xs-4">
							<h3><?php _e( 'Social Section', 'uap' );?></h3>
                            <p><?php _e('Place social Share icons with one generated Shortcode. We recommend "Social Share&Locker" plugin. ', 'uap');?></p>
							<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($data['metas']['uap_info_affiliate_bar_social_section_enabled']) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uapCheckAndH(this, '#uap_info_affiliate_bar_social_section_enabled');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="uap_info_affiliate_bar_social_section_enabled" value="<?php echo $data['metas']['uap_info_affiliate_bar_social_section_enabled'];?>" id="uap_info_affiliate_bar_social_section_enabled" />

							<div class="input-group" style="margin-bottom:20px; margin-top:30px;">
									<span class="input-group-addon" id="basic-addon1"><?php _e( 'Section name', 'uap' );?></span>
									<input type="text" class="form-control" name="uap_info_affiliate_bar_social_label" value="<?php echo stripslashes( $data['metas']['uap_info_affiliate_bar_social_label'] );?>" />
							</div>

							<div>
									<h3><?php _e( 'Shortcode', 'uap' );?></h3>
									<p><?php _e( 'You can generate the social share shortcode from the "Social Share &amp; Locker" dashboard and paste it here.' , 'uap' );?><strong><?php _e( '"Social Share&Locker" plugin is required.' , 'uap' );?></strong></p>
									<p><a href="<?php echo admin_url( 'admin.php?page=ism_manage&amp;tab=shortcode' );?>" target="_blank"><?php _e( 'Click here', 'uap' );?></a><?php _e( ' to grab a new shortcode.', 'uap' );?></p>
									<textarea name="uap_info_affiliate_bar_social_shortcode" style="width: 100%; height: 150px;"><?php echo stripslashes($data['metas']['uap_info_affiliate_bar_social_shortcode']);?></textarea>
							</div>

					</div>
                    </div>
                    <div class="uap-line-break"></div>

					<div class="row">
					<div class="col-xs-5">
							<h3><?php _e( 'Stats Section', 'uap' );?></h3>
                            <p><?php _e('Important stats and counts about current Page may show up into FlashBar so affiliate user will better understand the potential of that page.". ', 'uap');?></p>
							<h4><?php _e( 'Personal Stats', 'uap' );?></h4>
                            <p><?php _e('Counts about how many visits and referrals current page generated for logged affiliate user.". ', 'uap');?></p>
                            <label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($data['metas']['uap_info_affiliate_bar_stats_personal_section_enabled']) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uapCheckAndH(this, '#uap_info_affiliate_bar_stats_personal_section_enabled');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="uap_info_affiliate_bar_stats_personal_section_enabled" value="<?php echo $data['metas']['uap_info_affiliate_bar_stats_personal_section_enabled'];?>" id="uap_info_affiliate_bar_stats_personal_section_enabled" />

							<h4><?php _e( 'General Stats', 'uap' );?></h4>
                            <p><?php _e('Conversion Rate value is provided being calculated based on all affiliates performances related to current page.". ', 'uap');?></p>
							<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($data['metas']['uap_info_affiliate_bar_stats_general_section_enabled']) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uapCheckAndH(this, '#uap_info_affiliate_bar_stats_general_section_enabled');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="uap_info_affiliate_bar_stats_general_section_enabled" value="<?php echo $data['metas']['uap_info_affiliate_bar_stats_general_section_enabled'];?>" id="uap_info_affiliate_bar_stats_general_section_enabled" />

                            <h4><?php _e( 'Customizable Labels', 'uap' );?></h4>
							<div class="input-group" style="margin-bottom:20px;">
									<span class="input-group-addon" id="basic-addon1"><?php _e( 'Section name', 'uap' );?></span>
									<input type="text" class="form-control" name="uap_info_affiliate_bar_stats_label" value="<?php echo stripslashes( $data['metas']['uap_info_affiliate_bar_stats_label'] );?>" />
							</div>

							<div class="input-group" style="margin-bottom:20px;">
									<span class="input-group-addon" id="basic-addon1"><?php _e( 'Visits label', 'uap' );?></span>
									<input type="text" class="form-control" name="uap_info_affiliate_bar_visits_label" value="<?php echo stripslashes( $data['metas']['uap_info_affiliate_bar_visits_label'] );?>" />
							</div>

							<div class="input-group" style="margin-bottom:20px;">
									<span class="input-group-addon" id="basic-addon1"><?php _e( 'Referrals label', 'uap' );?></span>
									<input type="text" class="form-control" name="uap_info_affiliate_bar_referrals_label" value="<?php echo stripslashes( $data['metas']['uap_info_affiliate_bar_referrals_label'] );?>" />
							</div>

							<div class="input-group" style="margin-bottom:20px;">
									<span class="input-group-addon" id="basic-addon1"><?php _e( 'Insigts label', 'uap' );?></span>
									<input type="text" class="form-control" name="uap_info_affiliate_bar_insigts_label" value="<?php echo stripslashes( $data['metas']['uap_info_affiliate_bar_insigts_label'] );?>" />
							</div>

							<div class="input-group" style="margin-bottom:20px;">
									<span class="input-group-addon" id="basic-addon1"><?php _e( "'Conversion rate' label", 'uap' );?></span>
									<input type="text" class="form-control" name="uap_info_affiliate_bar_conversion_rate_label" value="<?php echo stripslashes( $data['metas']['uap_info_affiliate_bar_conversion_rate_label'] );?>" />
							</div>

							<div class="input-group" style="margin-bottom:20px;">
									<span class="input-group-addon" id="basic-addon1"><?php _e( "Perfomance label", 'uap' );?></span>
									<input type="text" class="form-control" name="uap_info_affiliate_bar_overall_performance_label" value="<?php echo stripslashes( $data['metas']['uap_info_affiliate_bar_overall_performance_label'] );?>" />
							</div>


					</div>
                    </div>
                    <div class="uap-line-break"></div>

					<div class="row">
					<div class="col-xs-4">
							<h3><?php _e( 'Menu Section', 'uap' );?></h3>
							<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($data['metas']['uap_info_affiliate_bar_menu_section_enabled']) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uapCheckAndH(this, '#uap_info_affiliate_bar_menu_section_enabled');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="uap_info_affiliate_bar_menu_section_enabled" value="<?php echo $data['metas']['uap_info_affiliate_bar_menu_section_enabled'];?>" id="uap_info_affiliate_bar_menu_section_enabled" />

							<div class="input-group" style="margin-bottom:20px;">
									<span class="input-group-addon" id="basic-addon1"><?php _e( 'Section name', 'uap' );?></span>
									<input type="text" class="form-control" name="uap_info_affiliate_bar_menu_label" value="<?php echo stripslashes( $data['metas']['uap_info_affiliate_bar_menu_label'] );?>" />
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

<?php
