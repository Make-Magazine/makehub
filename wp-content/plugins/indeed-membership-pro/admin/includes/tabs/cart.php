<?php
echo ihc_inside_dashboard_error_license();
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
do_action( "ihc_admin_dashboard_after_top_menu" );
if ( isset($_POST['ihc_save'] ) && !empty($_POST['ihc_admin_cart_settings_nonce']) && wp_verify_nonce( $_POST['ihc_admin_cart_settings_nonce'], 'ihc_admin_cart_settings_nonce' ) ){
    ihc_save_update_metas('cart-settings');
}
$meta_arr = ihc_return_meta_arr('cart-settings');
?>
<form action="" method="post" >
  <input type="hidden" name="ihc_admin_cart_settings_nonce" value="<?php echo wp_create_nonce( 'ihc_admin_cart_settings_nonce' );?>" />
  <div class="ihc-stuffbox">
    <h3><?php _e('Cart Settings', 'ihc');?></h3>
    <div class="inside">
      <div style="display: inline-block; width: 45%;">

      </div>


      <div class="ihc-wrapp-submit-bttn">
        <input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
      </div>
    </div>
  </div>
</form>
