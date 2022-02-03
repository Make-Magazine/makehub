jQuery(document).ready(function ($) {

  var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
  };

  var openSendGiftEmail = function openSendGiftEmail() {
    var action = getUrlParameter('action');
    var transaction = getUrlParameter('txn');

    if(action == "gifts" && $.isNumeric(transaction) && transaction > 0){
      var $gift = $("#mpgft-send-gift-"+transaction);
      if($gift.length > 0){
        $gift.parent().find('.mpgft-open-send-gift').click();
      }
    }
  };

  mepr_setup_clipboard();

  $('.mpgft-open-send-gift').magnificPopup({
    type: 'inline',
    preloader: false,
  });

  openSendGiftEmail()

  //Resend TXN Email JS
  $('.mpgft-send-gift-submit').click(function(event) {

    event.preventDefault();

    var $form = $(this).closest('.mpgft-send-gift-form');
    $form.find('img.mpgft-loader').show();

    var data = {
      action: 'mpgft_send_gift_email',
      gifter_name: $form.find('input[name="mpgft_gifter_name"]').val(),
      giftee_name: $form.find('input[name="mpgft_giftee_name"]').val(),
      giftee_email: $form.find('input[name="mpgft_giftee_email"]').val(),
      gift_note: $form.find('textarea[name="mpgft_gift_note"]').val(),
      txn_id: $form.find('input[name="mpgft_transaction_id"]').val(),
      security: $form.find('input[name="_wpnonce"]').val()
    };

    jQuery.post(MeprI18n.ajaxurl, data, function(response) {
      $('.cc-error').hide();
      $form.find('img.mpgft-loader').hide();

      if(response.success == true){
        $form.html(response.data);
      }
      else{
        $.each( response.data, function(key, value) {
          $('.mpgft_'+value+' .cc-error').show()
        });
      }
    });

    return false;
  });



});
