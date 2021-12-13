<?php
if (isset($_POST['ihc_save']) && !empty( $_POST['ihc_admin_inside_page_nonce'] ) && wp_verify_nonce( $_POST['ihc_admin_inside_page_nonce'], 'ihc_admin_inside_page_nonce' ) ){
		ihc_save_update_metas('individual_page');//save update metas
}
$data['metas'] = ihc_return_meta_arr('individual_page');//getting metas
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
do_action( "ihc_admin_dashboard_after_top_menu" );
$pages = ihc_get_all_pages();//getting pages
?>
<form method="post" id="individual_page_form">
	<input type="hidden" name="ihc_admin_inside_page_nonce" value="<?php echo wp_create_nonce( 'ihc_admin_inside_page_nonce' );?>" />
	<div class="ihc-stuffbox">
		<h3><?php esc_html_e('Individual Page', 'ihc');?></h3>
		<div class="inside">

			<div class="iump-form-line">
				<h2><?php esc_html_e('Activate/Hold', 'ihc');?></h2>
				<label class="iump_label_shiwtch ihc-switch-button-margin">
					<?php $checked = ($data['metas']['ihc_individual_page_enabled']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_individual_page_enabled');" <?php echo $checked;?> />
					<div class="switch ihc-display-inline"></div>
				</label>
				<input type="hidden" name="ihc_individual_page_enabled" value="<?php echo $data['metas']['ihc_individual_page_enabled'];?>" id="ihc_individual_page_enabled" />
			</div>

			<div class="iump-form-line">
				<h2><?php esc_html_e('Parent Page', 'ihc');?></h2>
				<select name="ihc_individual_page_parent">
					<option value="-1" <?php if($data['metas']['ihc_individual_page_parent']==-1){
						echo 'selected';
					}
					?> >...</option>
					<?php
						if ($pages){
							foreach ($pages as $k=>$v){
							?>
								<option value="<?php echo $k;?>" <?php if($data['metas']['ihc_individual_page_parent']==$k){
									echo 'selected';
								}
								?>
								><?php echo $v;?></option>
							<?php
							}
						}
					?>
				</select>
				<?php echo ihc_general_options_print_page_links($data['metas']['ihc_individual_page_parent']);?>
			</div>

			<div class="iump-form-line">
				<h2><?php esc_html_e('Page Title Prefix', 'ihc');?></h2>
			    <input type="text" value="<?php echo $data['metas']['ihc_individual_page_title'];?>" name="ihc_individual_page_title" />
			</div>

			<div class="iump-form-line">
				<h2><?php esc_html_e('Page Slug Prefix', 'ihc');?></h2>
			    <input type="text" value="<?php echo $data['metas']['ihc_individual_page_slug_prefix'];?>" name="ihc_individual_page_slug_prefix" />
			</div>

			<div class="iump-form-line">
				<h2><?php esc_html_e('Default Content', 'ihc');?></h2>
			</div>
			<div>
				<?php $data['metas']['ihc_individual_page_default_content'] = stripslashes($data['metas']['ihc_individual_page_default_content']);?>
				<?php wp_editor( $data['metas']['ihc_individual_page_default_content'], 'ihc_individual_page_default_content', array('textarea_name'=>'ihc_individual_page_default_content', 'quicktags'=>TRUE) );?>
			</div>
			<div >
				<?php esc_html_e('You can add specific user shortcodes which can be found ', 'ihc');?>
				<a href="<?php echo admin_url('admin.php?page=ihc_manage&tab=user_shortcodes');?>" target="_blank"><?php esc_html_e('here', 'ihc');?></a>
			</div>

			<div class="iump-form-line">
				<h2><?php esc_html_e('Generate Pages for existing Users', 'ihc');?></h2>
				<div class="button button-primary button-large" onClick="ihcDoBuiltInvidualPages();"><?php esc_html_e('Build Users Pages', 'ihc');?></div>
				<span class="spinner" id="ihc_loading"></span>
			</div>
			<h4><?php esc_html_e('Link to Individual Page (Shortcode):', 'ihc');?> </h4>
			<div class="ihc-user-list-shortcode-wrapp">
				<div class="content-shortcode">
					<span class="the-shortcode"> [ihc-individual-page-link]</span>
				</div>
			</div>

			<div class="ihc-wrapp-submit-bttn iump-submit-form">
				<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large">
			</div>

		</div>
	</div>
</form>

<?php
