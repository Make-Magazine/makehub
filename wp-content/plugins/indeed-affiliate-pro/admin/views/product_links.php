<div class="uap-wrapper">

		<form action="" method="post">
			<div class="uap-stuffbox">
				<h3 class="uap-h3"><?php _e('Product Links', 'uap');?></h3>
				<div class="inside">
				<div class="row">
					<div class="col-xs-6">


					<div class="uap-form-line">
						<h2><?php _e('Activate/Hold Product Links', 'uap');?></h2>
                        <p><?php _e('Affiliates can easily search for products and analyze them for generating custom affiliate links. The module is ready and compatible with WooCommerce, Ultimate Learning Pro and Easy Digital Downloads.', 'uap');?></p>
						<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
							<?php $checked = ($data['metas']['uap_product_links_enabled']) ? 'checked' : '';?>
							<input type="checkbox" class="uap-switch" onClick="uapCheckAndH(this, '#uap_product_links_enabled');" <?php echo $checked;?> />
							<div class="switch" style="display:inline-block;"></div>
						</label>
						<input type="hidden" name="uap_product_links_enabled" value="<?php echo $data['metas']['uap_product_links_enabled'];?>" id="uap_product_links_enabled" />

                        <p><?php _e('Once the Module is enabled an extra sub-tab will be available on Account Page under "Marketing" main tab.', 'uap');?></p>
                       </div>
              </div>
						</div>
                    <div class="uap-line-break"></div>
					<div class="row">
					<div class="col-xs-4">
					<div class="uap-form-line">
						<h3><?php _e('Show Reward calculation', 'uap');?></h3>
                        <p><?php _e('Besides the Product price, affiliates may see how actually the receives if the product is promoted. The system is able to take in consideration any available Offers for each product if a such value is set', 'uap');?></p>
						<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
							<?php $checked = ($data['metas']['uap_product_links_reward_calculation']) ? 'checked' : '';?>
							<input type="checkbox" class="uap-switch" onClick="uapCheckAndH(this, '#uap_product_links_reward_calculation');" <?php echo $checked;?> />
							<div class="switch" style="display:inline-block;"></div>
						</label>
						<input type="hidden" name="uap_product_links_reward_calculation" value="<?php echo $data['metas']['uap_product_links_reward_calculation'];?>" id="uap_product_links_reward_calculation" />
         </div>
         </div>
						</div>
                    <div class="uap-line-break"></div>
					<div class="row">
					<div class="col-xs-4">

					<div class="uap-form-line">
						<h3><?php _e( 'Products Source', 'uap' );?></h3>
						<?php $services = uap_get_active_services();?>
						<?php if ( isset( $services['ump'] ) ) unset( $services['ump'] );?>
						<select name="uap_product_links_source" >
								<option value="">...</option>
								<?php foreach ( $services as $serviceSlug => $serviceName ):?>
										<option value="<?php echo $serviceSlug;?>" <?php if ( $serviceSlug == $data['metas']['uap_product_links_source'] ) echo 'selected';?> ><?php echo $serviceName;?></option>
								<?php endforeach;?>
						</select>
          </div>
          </div>
						</div>
                    <div class="uap-line-break"></div>
			<div>
            <p><?php _e('You may place the showcase into any other place than is by default by copying the available shortcode.', 'uap');?></p>
          <div class="uap-admin-shortcode-wrap">[uap-product-links]</div>
			</div>
					<div class="uap-submit-form" style="margin-top: 20px;">
						<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
					</div>

				</div>
			</div>
		</form>
</div>

<?php
