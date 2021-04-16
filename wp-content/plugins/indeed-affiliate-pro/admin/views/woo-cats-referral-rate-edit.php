<tr class="form-field">
    <td><?php _e( 'Ultimate Affiliate Pro - Rate', 'uap');?></td>
</tr>
<tr class="form-field">
    <td><label><?php _e('Referral Rate Type', 'uap');?></label></td>
    <td>
        <select name="uap_referral_type" class="select short">
              <?php if ( $data['types'] ):?>
                  <?php foreach ( $data['types'] as $key => $value ):?>
                      <option value="<?php echo $key;?>" <?php if ( $data['uap_referral_type'] == $key ) echo 'selected';?> ><?php echo $value;?></option>
                  <?php endforeach;?>
              <?php endif;?>
        </select>
    </td>
</tr>
<tr class="form-field">
      <td><label><?php _e('Referral Value', 'uap');?></label></td>
      <td><input type="number" step="0.01" min="0" class="short" name="uap_referral_value" value="<?php echo $data['uap_referral_value'];?>" /></td>
</tr>
<tr class="form-field">
    <td></td>
    <td>
      <?php
      $offerType = get_option( 'uap_referral_offer_type' );
      if ( $offerType == 'biggest' ){
      		$offerType = __( 'Biggest', 'uap' );
      } else {
      		$offerType = __( 'Lowest', 'uap' );
      }
      echo __( 'If there are multiple Amounts set for the same action, like Ranks, Offers, Product or Category rate the ', 'uap' ) . '<strong>' . $offerType . '</strong> ' . __( 'will be taken in consideration. You may change that from', 'uap' ) . ' <a href="' . admin_url( 'admin.php?page=ultimate_affiliates_pro&tab=settings' ) . '" target="_blank">' . __( 'here.', 'uap' ) . '</a>';
      ?>
    </td>
</tr>
