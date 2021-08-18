<div class="ihc-checkout-page-box-wrapper ihc-product-details-wrapper">
  <div class="ihc-product-details">
    <!-- One Time Membership -->
    <!--table class="ihc-product-details-table">
      <tr>
        <td>
          <div class="ihc-product-name">Premium Membership</div>
          <div class="ihc-product-description">One time Payment</div>
        </td>
        <td>
          <div class="ihc-product-price">$20</div>
        </td>
      </tr>

      <tr id="ihc-main-payment-discount">
        <td><div class="ihc-product-fee-label-extra">Discount (15%)</div></td>
        <td><div class="ihc-product-price-extra">-$5</div></td>
      </tr>
      <tr id="ihc-main-payment-taxes">
        <td colspan="2">
            <table class="ihc-product-extra-details-table">
              <tr>
                <td><div class="ihc-product-fee-label-extra">VAT (19%)</div></td>
                <td><div class="ihc-product-price-extra">$9</div></td>
              </tr>
              <tr>
                <td><div class="ihc-product-fee-label-extra">Extra Tax (5%)</div></td>
                <td><div class="ihc-product-price-extra">$2</div></td>
              </tr>
            </table>
        </td>
      </tr>

    </table-->
    <!-- Standard Recurring -->
    <!--table class="ihc-product-details-table">
      <tr>
        <td>
          <div class="ihc-product-name">Simple Subscriptions</div>
          <div class="ihc-product-description">Full access on all content</div>
        </td>
        <td><div class="ihc-product-price"><span id="ihc-product-price">$20</span> <span class="ihc-product-price-detail"> every month</span></div></td>
      </tr>

      <tr id="ihc-main-payment-discount">
        <td><div class="ihc-product-fee-label-extra">Discount (15%)</div></td>
        <td><div class="ihc-product-price-extra">-$5</div></td>
      </tr>
      <tr id="ihc-main-payment-taxes">
        <td colspan="2">
            <table class="ihc-product-extra-details-table">
              <tr>
                <td><div class="ihc-product-fee-label-extra">VAT (19%)</div></td>
                <td><div class="ihc-product-price-extra">$9</div></td>
              </tr>
              <tr>
                <td><div class="ihc-product-fee-label-extra">Extra Tax (5%)</div></td>
                <td><div class="ihc-product-price-extra">$2</div></td>
              </tr>
            </table>
        </td>
      </tr>

    </table-->

    <!-- Recurring with Trial-->
    <table class="ihc-product-details-table">
      <tr>
        <td>
          <div class="ihc-product-name"><?php echo $data['levelData']['label'];?></div>
          <div class="ihc-product-description"><?php echo $data['levelData']['description'];?></div>
        </td>
        <td></td>
      </tr>
      <tr>
        <td>
          <div class="ihc-product-trial-fee-label"><?php esc_html_e( 'Initial Payment', 'ihc' );?></div>
          <div class="ihc-price-description"></div>
        </td>
        <td><div class="ihc-product-price" id="ihc-initial-payment-price"><span id="ihc-initial-payment-price">Free</span> <span class="ihc-product-price-detail"> for 15 days</span></div></td>
      </tr>

      <tr id="ihc-initial-payment-discount">
        <td><div class="ihc-product-fee-label-extra"><?php esc_html_e( 'Discount (15%)', 'ihc' );?></div></td>
        <td><div class="ihc-product-price-extra">-$5</div></td>
      </tr>
      <tr id="ihc-initial-payment-taxes">
        <td colspan="2">
            <table class="ihc-product-extra-details-table">
              <tr>
                <td><div class="ihc-product-fee-label-extra">VAT (19%)</div></td>
                <td><div class="ihc-product-price-extra">$9</div></td>
              </tr>
              <tr>
                <td><div class="ihc-product-fee-label-extra">Extra Tax (5%)</div></td>
                <td><div class="ihc-product-price-extra">$2</div></td>
              </tr>
            </table>
        </td>
      </tr>

      <tr>
        <td>
          <div class="ihc-product-main-fee-label">Then</div>
          <div class="ihc-price-description"></div>
        </td>
        <td><div class="ihc-product-price"><span id="ihc-product-price">$20</span> <span class="ihc-product-price-detail"> every month</span></div></td>
      </tr>

      <tr id="ihc-main-payment-discount">
        <td><div class="ihc-product-fee-label-extra">Discount (15%)</div></td>
        <td><div class="ihc-product-price-extra">-$5</div></td>
      </tr>


      <?php if ( $data['showTaxes'] ): ?>
          <tr id="ihc-main-payment-taxes">
            <td colspan="2">
                <table class="ihc-product-extra-details-table">
                  <tr>
                    <td><div class="ihc-product-fee-label-extra">VAT (19%)</div></td>
                    <td><div class="ihc-product-price-extra">$9</div></td>
                  </tr>
                  <tr>
                    <td><div class="ihc-product-fee-label-extra">Extra Tax (5%)</div></td>
                    <td><div class="ihc-product-price-extra">$2</div></td>
                  </tr>
                </table>
            </td>
          </tr>
      <?php endif;?>

    </table>
  </div>
</div>
