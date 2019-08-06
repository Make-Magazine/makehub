<div class="uap-ap-wrap">
  <?php if (!empty($data['title'])):?>
  	<h3><?php echo $data['title'];?></h3>
  <?php endif;?>
  <?php if (!empty($data['content'])):?>
  	<p><?php echo do_shortcode($data['content']);?></p>
  <?php endif;?>
  <div>
    <form method="post" action="">
      <div>
          <h2><?php _e('Show Info Affiliate Bar', 'uap');?></h2>
          <select name="iab_enable_bar" >
              <option value="0" <?php echo (!$data['settings']['iab_enable_bar']) ? 'selected' : '';?> >Off</option>
              <option value="1" <?php echo ($data['settings']['iab_enable_bar']) ? 'selected' : '';?> >On</option>
          </select>
      </div>


      <div>
          <?php
              $value = isset( $_POST['uap_info_affiliate_bar_hide'] ) ? $_POST['uap_info_affiliate_bar_hide'] : false;
              if ( $value===FALSE ){
                  $value = isset( $_COOKIE['uap_info_affiliate_bar_hide'] ) ? $_COOKIE['uap_info_affiliate_bar_hide'] : 0;
              }
          ?>
          <h2><?php _e('Temporary hide Info Affiliate Bar', 'uap');?></h2>
          <select name="uap_info_affiliate_bar_hide" >
              <option value="0" <?php echo ( empty( $value ) ) ? 'selected' : '';?> >Off</option>
              <option value="1" <?php echo ( !empty( $value ) ) ? 'selected' : '';?> >On</option>
          </select>
      </div>

      <div>
        <input type="submit" name="save" value="Save"  />
      </div>
    </form>

  </div>
</div>
