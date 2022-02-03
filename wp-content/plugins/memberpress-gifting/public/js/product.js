jQuery(document).ready(function ($) {
  var mpgft_hide_gift_checkbox = function (value = '') {
    let price = this.value || value;

    if (!price || price <= 0) {
      $("input[name='mpgft_allow_gifting']").prop("checked", false);
      $("#mepr-product-allow-gifting-fields-wrap").hide();
    }
    else{
      $("#mepr-product-allow-gifting-fields-wrap").show();
    }
  }
  $('#_mepr_product_price').change(mpgft_hide_gift_checkbox);
  if($('#_mepr_product_price').length){
    let price = $('#_mepr_product_price').val();
    mpgft_hide_gift_checkbox(price);
  }
});
