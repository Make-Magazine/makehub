jQuery(document).ready(function ($) {

  $(".mpgft-force-signup-checked").on("click", function (e) {
    let checkbox = $(this);
    if (!checkbox.is(":checked")) {
      e.preventDefault();
      return false;
    }
  });

  // $("body").on("change", "input[name='mpgft-signup-gift-checkbox']", function() {
  //   let form = $(this).closest(".mepr-signup-form");
  //   let settings = {
  //     data: {
  //       gift: $(this).is(':checked')
  //     }
  //   }
  //   window.meprUpdatePriceTerms(form, settings);
  // })

  // Hide Price, "Is this a gift?" checkbox and payment details if product is a gift
  var form = $(".mepr-signup-form");
  form.on("meprPriceStringUpdated", function(event, response) {
    if(response.is_gift){
      $(this).find('.mepr_price').hide();
      $(this).find('.mepr-payment-methods-wrapper').hide();
      $(this).find('.mepr-transaction-invoice-wrapper').hide();
      $(this).append('<input type="hidden" name="mepr_payment_methods_hidden" value="1">');
      $(this).find('input[name="mpgft-signup-gift-checkbox"]').closest('.mp-form-row').remove();
      if($('.mpgft_notice').length)  $('.mpgft_notice').remove();
    }
  });


});
