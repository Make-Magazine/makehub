var EsafPayments = (function ($) {

  var payments = {

    working: false,

    initialize: function () {
      payments.setup_pay_commissions_button();
      payments.setup_payouts();
      payments.setup_delete_payment();
      payments.setup_confirm_delete_payments();
    },

    setup_pay_commissions_button: function () {
      payments.maybe_enable_pay_commissions_button();

      $('body').on('change', '.esaf-payouts-checkbox', function () {
        payments.maybe_enable_pay_commissions_button();
      });
    },

    maybe_enable_pay_commissions_button: function () {
      if($('.esaf-payouts-checkbox:checked').length) {
        $('#esaf-pay-selected-commissions').prop('disabled', false);
      }
      else {
        $('#esaf-pay-selected-commissions').prop('disabled', true);
      }
    },

    setup_payouts: function () {
      if(!$('#esaf-admin-payouts-table').length) {
        return;
      }

      $('#esaf-paypal-bulk-payment').on('click', payments.paypal_mass_payment_file_payout);
      $('#esaf-paypal-1-click-bulk-payment').on('click', payments.paypal_one_click_payout);
      $('#esaf-auto-paypal-fail-try-again').on('click', payments.paypal_one_click_payout);
      $('#esaf-auto-paypal-fail-pay-with-mass-file').on('click', payments.paypal_one_click_failed_do_mass_payment_file_payout);
      $('#esaf-manual-bulk-payment').on('click', payments.manual_payout);
    },

    paypal_mass_payment_file_payout: function () {
      if(payments.working) {
        return;
      }

      payments.working = true;

      var $button = $(this),
        button_text = $button.text(),
        button_width = $button.width(),
        $payouts_table = $('#esaf-admin-payouts-table');

      $button.width(button_width).html('<i class="ea-icon ea-icon-spinner animate-spin" aria-hidden="true"></i>');

      $.ajax({
        method: 'POST',
        url: EsafPaymentsL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'esaf_payout_paypal_mass_payment_file',
          payouts: $payouts_table.data('payouts'),
          period: $payouts_table.data('period'),
          batch_id: $payouts_table.data('batch-id'),
          _ajax_nonce: $payouts_table.data('nonce')
        }
      }).done(function (response) {
        if(response && typeof response.success == 'boolean') {
          if(response.success) {
            $button.html('<i class="ea-icon ea-icon-ok" aria-hidden="true"></i>');
            $('#esaf-download-manual-paypal-pay-file-link').attr('href', response.data.download_file_url);
            $('#esaf-download-manual-paypal-mass-pay-file-link').attr('href', response.data.download_mass_pay_file_url);

            $.magnificPopup.open({
              mainClass: 'esaf-mfp',
              items: {
                src: '#esaf-success-popup-manual-paypal-payment',
                type: 'inline'
              }
            });

            $button.prop('disabled', true);
          }
          else {
            payments.paypal_mass_payment_file_payout_failed(response.data);
            $button.html('<i class="ea-icon ea-icon-cancel" aria-hidden="true"></i>');
          }
        }
        else {
          payments.paypal_mass_payment_file_payout_failed(EsafPaymentsL10n.manual_payout_request_error);
          $button.html('<i class="ea-icon ea-icon-cancel" aria-hidden="true"></i>');
        }
      }).fail(function () {
        payments.paypal_mass_payment_file_payout_failed(EsafPaymentsL10n.manual_payout_request_error);
        $button.html('<i class="ea-icon ea-icon-cancel" aria-hidden="true"></i>');
      }).always(function () {
        setTimeout(function () {
          $button.text(button_text).width('auto');
          payments.working = false;
        }, 2000);
      });
    },

    paypal_mass_payment_file_payout_failed: function (message) {
      $.magnificPopup.open({
        mainClass: 'esaf-mfp',
        items: {
          src: '#esaf-fail-popup-payment',
          type: 'inline'
        }
      });

      $('#esaf-fail-popup-payment').find('.notice p.esaf-hidden').text(message).show();
    },

    paypal_one_click_payout: function () {
      if(payments.working) {
        return;
      }

      payments.working = true;

      var $button = $(this),
        button_text = $button.text(),
        button_width = $button.width(),
        $payouts_table = $('#esaf-admin-payouts-table');

      $button.width(button_width).html('<i class="ea-icon ea-icon-spinner animate-spin" aria-hidden="true"></i>');

      $.ajax({
        method: 'POST',
        url: EsafPaymentsL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'esaf_payout_paypal_one_click',
          payouts: $payouts_table.data('payouts'),
          period: $payouts_table.data('period'),
          batch_id: $payouts_table.data('batch-id'),
          _ajax_nonce: $payouts_table.data('nonce')
        }
      }).done(function (response) {
        if(response && typeof response.success == 'boolean') {
          if(response.success) {
            $button.html('<i class="ea-icon ea-icon-ok" aria-hidden="true"></i>');
            $('#esaf-download-auto-paypal-pay-file-link').attr('href', response.data.download_file_url);

            $.magnificPopup.open({
              mainClass: 'esaf-mfp',
              items: {
                src: '#esaf-success-popup-auto-paypal-payment',
                type: 'inline'
              }
            });

            $button.prop('disabled', true);
          }
          else {
            $button.html('<i class="ea-icon ea-icon-cancel" aria-hidden="true"></i>');
            payments.paypal_one_click_payout_failed(response.data);
          }
        }
        else {
          $button.html('<i class="ea-icon ea-icon-cancel" aria-hidden="true"></i>');
          payments.paypal_one_click_payout_failed(EsafPaymentsL10n.paypal_one_click_request_error);
        }
      }).fail(function () {
        $button.html('<i class="ea-icon ea-icon-cancel" aria-hidden="true"></i>');
        payments.paypal_one_click_payout_failed(EsafPaymentsL10n.paypal_one_click_request_error);
      }).always(function () {
        setTimeout(function () {
          $button.text(button_text).width('auto');
          payments.working = false;
        }, 1000);
      });
    },

    paypal_one_click_payout_failed: function (message) {
      payments.working = false;

      $.magnificPopup.open({
        mainClass: 'esaf-mfp',
        items: {
          src: '#esaf-fail-popup-auto-paypal-payment',
          type: 'inline'
        }
      });

      $('#esaf-fail-popup-auto-paypal-payment').find('.notice p.esaf-hidden').text(message).show();
    },

    paypal_one_click_failed_do_mass_payment_file_payout: function () {
      if(payments.working) {
        return;
      }

      payments.working = true;

      var $button = $(this),
        button_text = $button.text(),
        button_width = $button.width(),
        $payouts_table = $('#esaf-admin-payouts-table');

      $button.width(button_width).html('<i class="ea-icon ea-icon-spinner animate-spin" aria-hidden="true"></i>');

      $.ajax({
        method: 'POST',
        url: EsafPaymentsL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'esaf_payout_paypal_mass_payment_file',
          payouts: $payouts_table.data('payouts'),
          period: $payouts_table.data('period'),
          batch_id: $payouts_table.data('batch-id'),
          _ajax_nonce: $payouts_table.data('nonce')
        }
      }).done(function (response) {
        if(response && typeof response.success == 'boolean') {
          if(response.success) {
            $('#esaf-download-manual-paypal-pay-file-link').attr('href', response.data.download_file_url);
            $('#esaf-download-manual-paypal-mass-pay-file-link').attr('href', response.data.download_mass_pay_file_url);

            $.magnificPopup.open({
              mainClass: 'esaf-mfp',
              items: {
                src: '#esaf-success-popup-manual-paypal-payment',
                type: 'inline'
              }
            });
          }
          else {
            $button.html('<i class="ea-icon ea-icon-cancel" aria-hidden="true"></i>');
            payments.paypal_mass_payment_file_payout_failed(response.data);
          }
        }
        else {
          $button.html('<i class="ea-icon ea-icon-cancel" aria-hidden="true"></i>');
          payments.paypal_mass_payment_file_payout_failed(EsafPaymentsL10n.manual_payout_request_error);
        }
      }).fail(function () {
        $button.html('<i class="ea-icon ea-icon-cancel" aria-hidden="true"></i>');
        payments.paypal_mass_payment_file_payout_failed(EsafPaymentsL10n.manual_payout_request_error);
      }).always(function () {
        setTimeout(function () {
          $button.text(button_text).width('auto');
          payments.working = false;
        }, 1000);
      });
    },

    manual_payout: function () {
      if(payments.working) {
        return;
      }

      payments.working = true;

      var $button = $(this),
        button_text = $button.text(),
        button_width = $button.width(),
        $payouts_table = $('#esaf-admin-payouts-table');

      $button.width(button_width).html('<i class="ea-icon ea-icon-spinner animate-spin" aria-hidden="true"></i>');

      $.ajax({
        method: 'POST',
        url: EsafPaymentsL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'esaf_payout_manual',
          payouts: $payouts_table.data('payouts'),
          period: $payouts_table.data('period'),
          batch_id: $payouts_table.data('batch-id'),
          _ajax_nonce: $payouts_table.data('nonce')
        }
      }).done(function (response) {
        if(response && typeof response.success == 'boolean') {
          if(response.success) {
            $button.html('<i class="ea-icon ea-icon-ok" aria-hidden="true"></i>');
            $('#esaf-download-manual-pay-file-link').attr('href', response.data.download_file_url);

            $.magnificPopup.open({
              mainClass: 'esaf-mfp',
              items: {
                src: '#esaf-success-popup-manual-payment',
                type: 'inline'
              }
            });

            $button.prop('disabled', true);
          }
          else {
            $button.html('<i class="ea-icon ea-icon-cancel" aria-hidden="true"></i>');
            payments.manual_payout_failed(response.data);
          }
        }
        else {
          $button.html('<i class="ea-icon ea-icon-cancel" aria-hidden="true"></i>');
          payments.manual_payout_failed(EsafPaymentsL10n.manual_payout_request_error);
        }
      }).fail(function () {
        $button.html('<i class="ea-icon ea-icon-cancel" aria-hidden="true"></i>');
        payments.manual_payout_failed(EsafPaymentsL10n.manual_payout_request_error);
      }).always(function () {
        setTimeout(function () {
          $button.text(button_text).width('auto');
          payments.working = false;
        }, 2000);
      });
    },

    manual_payout_failed: function (message) {
      $.magnificPopup.open({
        mainClass: 'esaf-mfp',
        items: {
          src: '#esaf-fail-popup-payment',
          type: 'inline'
        }
      });

      $('#esaf-fail-popup-payment').find('.notice p.esaf-hidden').text(message).show();
    },

    setup_delete_payment: function () {
      var $payments_list_table = $('.wp_list_wafp_payments');

      if(!$payments_list_table.length) {
        return;
      }

      $payments_list_table.on('click', '.esaf-delete-payment', function () {
        if(!confirm(EsafPaymentsL10n.confirm_delete_payment)) {
          return;
        }

        var $icon = $(this),
          $row = $icon.closest('tr'),
          payment_id = $icon.data('payment-id');

        $.ajax({
          method: 'POST',
          url: EsafPaymentsL10n.ajax_url,
          dataType: 'json',
          data: {
            action: 'esaf_delete_payment',
            _ajax_nonce: EsafPaymentsL10n.delete_payment_nonce,
            payment_id: payment_id
          }
        }).done(function (response) {
          if(response && typeof response == 'object' && typeof response.success == 'boolean') {
            if(response.success) {
              $row.fadeOut('slow');
            }
            else {
              alert(response.data);
            }
          }
          else {
            alert(EsafPaymentsL10n.error_deleting_payment);
          }
        }).fail(function () {
          alert(EsafPaymentsL10n.error_deleting_payment);
        });
      });
    },

    setup_confirm_delete_payments: function () {
      $('#esaf-payout-history-table').on('click', '#doaction, #doaction2', function (e) {
        var action = $('#bulk-action-selector-top').val();

        if(action === 'delete' && !confirm(EsafPaymentsL10n.confirm_delete_payments)) {
          e.preventDefault();
        }
      });
    }

  };

  $(payments.initialize);

  return payments;

})(jQuery);
