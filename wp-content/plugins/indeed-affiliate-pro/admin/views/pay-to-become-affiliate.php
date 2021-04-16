<div class="uap-wrapper">

<div class="uap-inside-notification">
<?php _e('This module has a high level of complexity and requires proper knowledge about Ultimate Affiliate Pro system and may restrict registration process for new affiliate users. It is recommended only for advanced users.', 'uap');?>
</div>
		<form action="" method="post">
			<div class="uap-stuffbox">
				<h3 class="uap-h3"><?php _e('Account Page - Pay to become Affiliate', 'uap');?></h3>
				<div class="inside">

					<div class="uap-form-line">
						<h2><?php _e('Activate/Hold Pay to become affiliate', 'uap');?></h2>
						<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
							<?php $checked = ($data['metas']['uap_pay_to_become_affiliate_enabled']) ? 'checked' : '';?>
							<input type="checkbox" class="uap-switch" onClick="uapCheckAndH(this, '#uap_pay_to_become_affiliate_enabled');" <?php echo $checked;?> />
							<div class="switch" style="display:inline-block;"></div>
						</label>
						<input type="hidden" name="uap_pay_to_become_affiliate_enabled" value="<?php echo $data['metas']['uap_pay_to_become_affiliate_enabled'];?>" id="uap_pay_to_become_affiliate_enabled" />
                        <p><?php _e('Once this module is enabled new registered users will not be set as Affiliates until a specific purchase is confirmed. Based on Module settings, the system will check for current user if any complete Order with targeted products exist. ', 'uap');?></p>
                        <p><?php _e('For "Become an Affiliate" button dedicated for logged users the same restriction will apply. That button will not show up at all until current user will not complete the required payment.', 'uap');?></p>
					</div>

					<div class="uap-form-line">
							<h2><?php _e('Targeting', 'uap');?></h2>
							<p><?php _e('Based source of products', 'uap');?></p>
							<h4 style="margin-top:20px;"><?php _e('Source', 'uap');?></h4>
							<?php
							$services = uap_get_active_services();
							$possibleServicesAllowed = array( 'ump', 'woo' );
							?>
							<select <?php if (!$services) echo 'disabled';?> name="uap_pay_to_become_affiliate_target_product_group" id="the_source"  class="form-control m-bot15" onChange="uap_reset_autocomplete_fields();jQuery('#reference_search').autocomplete( 'option', { source: '<?php echo UAP_URL . 'admin/uap-offers-ajax-autocomplete.php';?>?source='+this.value } );"><?php
								if ( $services ):
									foreach ($services as $k=>$v){
										if ( !in_array($k, $possibleServicesAllowed) ) continue;
										$selected = ($data['metas']['uap_pay_to_become_affiliate_target_product_group']==$k) ? 'selected' : '';
										?>
										<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
										<?php
									}
								endif;
							?></select>
							<div>
									<h6><?php _e( 'All products', 'uap' );?></h6>
									<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
										<?php $checked = ($data['metas']['uap_pay_to_become_affiliate_target_all_products']) ? 'checked' : '';?>
										<input type="checkbox" class="uap-switch" onClick="uapCheckAndH(this, '#uap_pay_to_become_affiliate_target_all_products');uap_make_disable_if_checked(this, '#reference_search');uap_reset_autocomplete_fields();" <?php echo $checked;?> />
										<div class="switch" style="display:inline-block;"></div>
									</label>
									<input type="hidden" name="uap_pay_to_become_affiliate_target_all_products" value="<?php echo $data['metas']['uap_pay_to_become_affiliate_target_all_products'];?>" id="uap_pay_to_become_affiliate_target_all_products" />
							</div>
							<div class="input-group" style="margin-top:10px;">
								<span class="input-group-addon" id="basic-addon1"><?php _e('Products', 'uap');?></span>
								<input type="text" <?php if ($data['metas']['uap_pay_to_become_affiliate_target_all_products']) echo 'disabled';?> class="form-control" value="" name="" id="reference_search" />
							</div>
								<?php $value = (is_array($data['metas']['products'])) ? implode(',', $data['metas']['products']) : $data['metas']['products'];?>
								<input type="hidden" value="<?php echo $data['metas']['uap_pay_to_become_affiliate_target_products'];?>" name="uap_pay_to_become_affiliate_target_products" id="uap_pay_to_become_affiliate_target_products" />
								<div id="uap_reference_search_tags"><?php
									if (!empty($data['metas']['products'])){
										foreach ($data['metas']['products'] as $value){
											if ($value && !empty($data['products']['label'][$value])){
											$id = 'uap_reference_tag_' . $value;
											?>
											<div id="<?php echo $id;?>" class="uap-tag-item"><?php echo $data['products']['label'][$value];?><div class="uap-remove-tag" onclick="uapRemoveTag('<?php echo $value;?>', '#<?php echo $id;?>', '#uap_pay_to_become_affiliate_target_products');" title="Removing tag">x</div></div>
											<?php
											}
										}
									}
								?>
							</div>
					</div>


					<div class="uap-submit-form" style="margin-top: 20px;">
						<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
					</div>

				</div>
			</div>
		</form>
</div>

<script>
var uap_offer_source = jQuery('#the_source').val();

jQuery(document).ready(function(){
	jQuery('#the_source').on('change', function(){
		uap_offer_source = jQuery(this).val();
		jQuery('#uap_reference_search_tags').empty();
		jQuery('#reference_search_hidden').val('');
	});
});

function uap_split(v){
	if (v.indexOf(',')!=-1){
	    return v.split( /,\s*/ );
	} else if (v!=''){
		return [v];
	}
	return [];
}
function uap_extract(t) {
    return uap_split(t).pop();
}

jQuery(document).ready( function(){

    /// REFERENCE SEARCH
		jQuery( "#reference_search" ).bind( "keydown", function( event ) {
			if ( event.keyCode === jQuery.ui.keyCode.TAB &&
				jQuery( this ).autocomplete( "instance" ).menu.active ) {
			 	event.preventDefault();
			}
		}).autocomplete({
		focus: function( event, ui ){},
		minLength: 0,
		source: '<?php echo UAP_URL . 'admin/uap-offers-ajax-autocomplete.php';?>?source='+uap_offer_source,
		select: function( event, ui ) {
			var input_id = '#uap_pay_to_become_affiliate_target_products';
		 	var terms = uap_split(jQuery(input_id).val());//get items from input hidden
			var v = ui.item.id;
			var l = ui.item.label;
		 	if (!contains(terms, v)){
				terms.push(v);
			 	uapAutocompleteWriteTag(v, input_id, '#uap_reference_search_tags', 'uap_reference_tag_', l);// print the new shiny box
			 }
			var str_value = terms.join( "," );
		 	jQuery(input_id).val(str_value);//send to input hidden
			this.value = '';//reset search input
		 	return false;
		}
	});

});

function contains(a, obj) {
    return a.some(function(element){return element == obj;})
}

function uap_reset_autocomplete_fields()
{
		jQuery('#uap_pay_to_become_affiliate_target_products').val('')
		jQuery('#uap_reference_search_tags').html('')
}

function uap_make_disable_if_checked( checkSelector, target )
{
		if (jQuery(checkSelector).is(':checked')){
				jQuery(target).attr('disabled', 'disabled')
		} else {
				jQuery(target).removeAttr('disabled')
		}
}
</script>

<?php
