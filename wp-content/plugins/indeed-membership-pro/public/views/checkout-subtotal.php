<?php if ( $data['showTaxes'] ): ?>
    <div class="ihc-checkout-page-box-wrapper ihc-taxes-wrapper">
      <div class="ihc-checkout-page-box-title"><?php esc_html_e( 'Taxes', 'ihc' );?></div>
      <table class="ihc-product-details-table">
        <tr>
          <td><div class="ihc-tax-label">VAT</div></td>
          <td><div class="ihc-tax-price">19%</div></td>
        </tr>
        <tr>
          <td><div class="ihc-tax-label">Extra Tax</div></td>
          <td><div class="ihc-tax-price">5%</div></td>
        </tr>
      </table>
    </div>
<?php endif;?>

<div class="ihc-checkout-page-box-wrapper ihc-subtotal-wrapper">
  <div class="ihc-checkout-page-box-title"><?php esc_html_e( 'Subtotal', 'ihc' );?></div>
  <table class="ihc-product-details-table">
    <tr>
      <td><div class="ihc-product-trial-fee-label"><?php esc_html_e( 'Initial Payment', 'ihc' );?></div></td>
      <td><div class="ihc-product-price"><span id="ihc-subtotal-initial-payment-price">Free</span> <span class="ihc-product-price-detail"> for 15 days</span></div></td>
    </tr>
    <tr>
      <td><div class="ihc-product-main-fee-label"><?php esc_html_e( 'Then', 'ihc' );?></div>
      </td><td><div class="ihc-product-price"><span id="ihc-subtotal-product-price">$24</span> <span class="ihc-product-price-detail"> every month</span></div></td>
    </tr>
  </table>
</div>
