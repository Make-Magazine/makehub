<div class="form-row form-row-full options">

    <h4><?php _e( 'Ultimate Affiliate Pro - Static Coupon', 'uap' );?></h4>

    <p class="form-field">
        <label><?php _e( 'Target Affiliate Username: ', 'uap' );?></label>
        <input class="short" type="text" name="uap_affiliate_username" value="<?php echo isset( $data['uap_affiliate_username'] ) ? $data['uap_affiliate_username'] : '';?>" />
    </p>

    <p class="form-field">
        <label><?php _e('Referral Rate Type', 'uap');?></label>
          <select name="uap_amount_type" class="select short">
              <?php if ( $data['types'] ):?>
                  <?php foreach ( $data['types'] as $key => $value ):?>
                      <option value="<?php echo $key;?>" <?php if ( $data['uap_amount_type'] == $key ) echo 'selected';?> ><?php echo $value;?></option>
                  <?php endforeach;?>
              <?php endif;?>
          </select>
    </p>

    <p class="form-field">
        <label><?php _e('Referral Value', 'uap');?></label>
        <input type="number" step="0.01" min="0" class="short" name="uap_amount_value" value="<?php echo $data['uap_amount_value'];?>" />
    </p>

    <p class="form-field">
        <label><?php _e('Enable', 'uap');?></label>
        <select name="uap_status" class="select short">
            <option value="0" <?php if ($data['uap_status'] == 0 ) echo 'selected';?> ><?php _e( 'Off', 'uap' );?></option>
            <option value="1" <?php if ($data['uap_status'] == 1 ) echo 'selected';?> ><?php _e( 'On', 'uap' );?></option>
        </select>
    </p>

    <input type="hidden" name="uap_static_coupon_id" value="<?php echo $data['id'];?>" />

</div>
