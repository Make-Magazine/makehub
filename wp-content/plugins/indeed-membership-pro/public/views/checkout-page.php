<?php
  wp_enqueue_style( 'ihc_templates_style', IHC_URL . 'assets/css/templates.min.css', array(), 1.1 );
  wp_enqueue_script( 'ihc_checkout', IHC_URL . 'assets/js/checkout.js', [], false, false );
 ?>
<div class="ihc-checkout-page-wrapp" id="ihc_checkout_page_wrapp">
  <div class="ihc-checkout-page-left-side">

    <input type="hidden" name="subscription_access_type" value="<?php echo $data['levelData']['access_type'];?>" />

    <div class="ihc-checkout-page-box-wrapper ihc-checkout-page-customer-form-wrapper">
      <div class="ihc-checkout-page-box-title"><?php esc_html_e( 'Customer Information', 'ihc' );?></div>
      <div class="ihc-checkout-page-customer-form">
        <div class="ihc-register-14">

          <?php if ( $data['uid'] ):?>
            <form method="post" enctype="multipart/form-data">
          <?php endif;?>

              <input type="hidden" name="lid" value="<?php echo $data['lid'];?>" />

              <?php if ( $data['showUserDetails'] ): ?>
                  <div class="iump-form-line-register iump-form-text" id="ihc_reg_text_9177">
                    <label class="iump-labels-register">
                      <span class="ihc-required-sign">*</span><?php esc_html_e( 'Email', 'ihc' );?>
                    </label>
                    <input type="text" name="user_email" value="" placeholder="" />
                  </div>

                  <div class="iump-form-line-register iump-form-text" id="ihc_reg_text_531">
                      <label class="iump-labels-register"><?php esc_html_e( 'First Name', 'ihc' );?></label>
                      <input type="text" name="first_name" value="" placeholder="">
                  </div>

                  <div class="iump-form-line-register iump-form-text" id="ihc_reg_text_3584">
                    <label class="iump-labels-register"><?php esc_html_e( 'Last Name', 'ihc' );?></label>
                    <input type="text" name="last_name" value="" placeholder="" >
                  </div>

                  <div class="iump-form-line-register iump-form-textarea" id="ihc_reg_textarea_1249">
                      <label class="iump-labels-register"><?php esc_html_e( 'Address 1', 'ihc' );?></label>
                      <textarea name="addr1" class="iump-form-textarea "></textarea>
                  </div>

                  <div class="iump-form-line-register iump-form-number" id="ihc_reg_number_495">
                      <label class="iump-labels-register"><?php esc_html_e( 'Phone', 'ihc' );?></label>
                      <input type="number" name="phone" value="" min="" max="" />
                  </div>

                  <div class="iump-form-line-register iump-form-text" id="ihc_reg_text_1050">
                    <label class="iump-labels-register"><?php esc_html_e( 'City', 'ihc' );?></label>
                    <input type="text" name="city" value="" placeholder="" />
                  </div>

                  <div class="iump-form-line-register iump-form-text" id="ihc_reg_text_3412">
                    <label class="iump-labels-register"><?php esc_html_e( 'Zip', 'ihc' );?></label>
                    <input type="text" name="zip" value="" placeholder="" />
                  </div>

              <input type="hidden" name="ihcaction" value="update" />
              <div class="iump-submit-form">
                <div class="iump-clear"></div>
              </div>
            <?php endif;?>


          <?php if ( $data['uid'] ):?>
              </form>
          <?php endif;?>

        </div>
      </div>
    </div>

    <?php if ( !empty( $data['fields']['payment_select'] ) ): ?>
        <div class="ihc-checkout-page-box-wrapper ihc-checkout-page-payment-selection-wrapper">
          <div class="ihc-checkout-page-box-title"><?php
            echo isset( $data['paymentSelectSettings']['label'] ) ? $data['paymentSelectSettings']['label'] : esc_html__( 'Payment Method', 'ihc' );
          ?></div>
          <!-- List of available Payment methods for current Membership -->
          <div class="ihc-checkout-page-payment-selection">

            <?php if ( $data['paymentSelectSettings']['theme'] === 'ihc-select-payment-theme-1' ):?>
                <div class="ihc-select-payment-theme-1">
                    <div class="iump-form-paybox">
                        <?php foreach ( $data['paymentServices'] as $paymentSlug => $paymentLabel ):?>

                          <div class="iump-form-paybox" >
                            <?php $checked = $data['defaultPayment'] === $paymentSlug ? 'checked' : '';?>
                            <input type="radio" class="ihc-js-select-payment-service-radio" <?php echo $checked;?> value="<?php echo $paymentSlug;?>" /> <?php echo $paymentLabel;?>
                          </div>

                        <?php endforeach;?>
                    </div>
                </div>
            <?php elseif ( $data['paymentSelectSettings']['theme'] === 'ihc-select-payment-theme-2' ): ?>

                <div class="ihc-select-payment-theme-2 ">

                  <?php foreach ( $data['paymentServices'] as $paymentSlug => $paymentLabel ):?>
                    <div class="iump-form-paybox" >
                      <?php $extraClass = $data['defaultPayment'] === $paymentSlug ? 'ihc-payment-select-img-selected' : '';?>
                      <img src="<?php echo IHC_URL . 'assets/images/'.$paymentSlug.'.png';?>" data-type="<?php echo $paymentSlug;?>" class="ihc-payment-icon <?php echo $extraClass;?> ihc-js-select-payment" id="ihc_payment_icon_<?php echo $paymentSlug;?>" />
                    </div>
                  <?php endforeach;?>

                </div>

            <?php elseif ( $data['paymentSelectSettings']['theme'] === 'ihc-select-payment-theme-3' ):?>
                <div class="ihc-select-payment-theme-3">
                    <select name="" class="ihc-js-select-payment-service-select">
                      <?php foreach ( $data['paymentServices'] as $paymentSlug => $paymentLabel ):?>
                          <?php $selected = $data['defaultPayment'] === $paymentSlug ? 'selected' : '';?>
                          <option value="<?php echo $paymentSlug;?>" <?php echo $selected;?> ><?php echo $paymentLabel;?></option>
                      <?php endforeach;?>
                    </select>
                </div>
            <?php endif;?>

          </div>
          <!-- Inline fields from Braintree, Authorize, etc -->
          <div class="ihc-checkout-page-payment-onsite-fields">

              <?php if ( isset( $data['paymentServices']['authorize'] ) && $data['level_data']['access_type'] === 'regular_period' ):?>
                  <div id="ihc_authorize_payment_fields" >
                      <div class="ihc_payment_details"><?php esc_html_e('Complete Payment with Authorize', 'ihc');?></div>
                      <?php
                          if (!class_exists('ihcAuthorizeNet')){
                    				require_once IHC_PATH . 'classes/PaymentGateways/ihcAuthorizeNet.class.php';
                    			}
                    			$authorizeObject = new \ihcAuthorizeNet();
                          echo $authorizeObject->payment_fields();
                      ?>
                  </div>
              <?php endif;?>

              <?php if ( isset( $data['paymentServices']['braintree'] ) ):?>
                  <?php
                      require_once IHC_PATH . 'classes/PaymentGateways/Ihc_Braintree.class.php';
              		    $braintree = new \Ihc_Braintree();
                  ?>
                  <div id="ihc_braintree_payment_fields" >
                      <div class="ihc_payment_details"><?php esc_html_e('Complete Payment with Braintree', 'ihc');?></div>
                      <?php echo $braintree->get_form();?>
                  </div>
              <?php endif;?>

              <input type="hidden" name="ihc_payment_gateway" value="<?php echo $data['defaultPayment'];?>" />

          </div>
        </div>
    <?php endif;?>

  </div>

  <div class="ihc-checkout-page-right-side">

    <?php include IHC_PATH . 'public/views/checkout-subscription-details.php';?>

    <div class="ihc-checkout-page-box-extra-options">

      <!-- DYNAMIC PRICE -->
      <?php if ( !empty( $data['fields']['ihc_dynamic_price'] ) ): ?>
          <div class="ihc-checkout-page-box-wrapper ihc-dynamic-price-wrapper">
            <div class="ihc-checkout-page-additional-info"><?php esc_html_e( 'Choose how much you wish to pay for it.', 'ihc' );?></div>
            <div class="ihc-checkout-page-input-left">
              <input class="ihc-checkout-page-input" type="text" value=""/>
            </div>
            <div class="ihc-checkout-page-apply-right">
              <button type="submit" class="ihc-checkout-page-apply" name="apply_dynamic_price" value="Apply">Apply</button>
            </div>
            <div class="ihc-clear"></div>
          </div>
      <?php endif;?>

      <!-- COUPON -->
      <?php if ( !empty( $data['fields']['ihc_coupon'] ) ): ?>
          <div class="ihc-checkout-page-box-wrapper ihc-discount-wrapper">
            <div class="ihc-checkout-page-additional-info"><?php esc_html_e( 'If you have a coupon code, please apply it below.', 'ihc' );?></div>
            <div class="ihc-checkout-page-input-left">
              <input class="ihc-checkout-page-input" type="text" value=""/>
            </div>
            <div class="ihc-checkout-page-apply-right">
              <button type="submit" class="ihc-checkout-page-apply" name="apply_discount" value="Apply"><?php esc_html_e( 'Apply', 'ihc' );?></button>
            </div>
            <div class="ihc-clear"></div>
          </div>
      <?php endif;?>

    </div>

    <?php include IHC_PATH . 'public/views/checkout-subtotal.php';?>

    <?php if ( $data['showPrivacyPolicy'] ):?>
        <div class="ihc-checkout-page-box-wrapper ihc-terms-wrapper">
          <p><?php echo $data['privacyPolicyMessage'];?></p>
        </div>
    <?php endif;?>

    <div class="ihc-checkout-page-box-wrapper ihc-purchase-wrapper">
      <input type="submit"  name="ihc-complete-purchase" value="<?php esc_html_e( 'Complete Purchase', 'ihc' );?>" class="ihc-complete-purchase-button" />
    </div>
  </div>

</div>
