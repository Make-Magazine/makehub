jQuery(function ($) {
  var $transactions_list_table = $('.wp_list_wafp_transactions');

  if($transactions_list_table.length) {
    if($.magnificPopup) {
      $transactions_list_table.on('click', '.esaf-view-transaction-details', function () {
        $.magnificPopup.open({
          mainClass: 'esaf-mfp',
          items: {
            src: $(this).closest('td').find('.esaf-view-transaction-details-popup'),
            type: 'inline'
          }
        });
      });
    }

    $transactions_list_table.on('click', '.esaf-delete-transaction', function () {
      if(!confirm(EsafTransactions.delete_are_you_sure)) {
        return;
      }

      var $icon = $(this),
        $row = $icon.closest('tr'),
        transaction_id = $icon.data('transaction-id');

      $.ajax({
        method: 'POST',
        url: EsafTransactions.ajax_url,
        dataType: 'json',
        data: {
          action: 'esaf_delete_transaction',
          _ajax_nonce: EsafTransactions.delete_transaction_nonce,
          transaction_id: transaction_id
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
          alert(EsafTransactions.error_deleting_transaction);
        }
      }).fail(function () {
        alert(EsafTransactions.error_deleting_transaction);
      });
    });
  }

  var $commissions = $('.wafp-commissions'),
    $add_commission = $('<div>').addClass('esaf-add-new-commission').hide(),
    $add_commission_button = $(EsafTransactions.add_commssion_level_button_html);

  $add_commission.append($add_commission_button).insertAfter($commissions);

  function set_add_commission_visibility() {
    if(EsafTransactions.commission_levels_enabled || get_commissions_count() === 0) {
      $add_commission.show();
    }
    else {
      $add_commission.hide();
    }
  }

  set_add_commission_visibility();

  function get_commissions_count() {
    return $commissions.find('.wafp-commissions-table').length;
  }

  function set_commission_level_visibility() {
    $commissions.toggleClass('wafp-has-multiple-commissions', get_commissions_count() > 1);
  }

  $add_commission_button.click(function() {
    $commissions.append(EsafTransactions.new_commission_form);
    EsafAdmin.setup_affiliate_search();
    set_add_commission_visibility();
    set_commission_level_visibility();
    jQuery('.wafp-commissions .wafp-commissions-table:last-child').slideDown('fast');
    return false;
  });

  function set_multi_commission_type_new(obj) {
    var ancestor = jQuery(obj).parent().parent().parent().parent(); // go to the table element
    if($(obj).val() === 'percentage') {
      ancestor.find('.commissions-currency-symbol').hide();
      ancestor.find('.commissions-percent-symbol').show();
    }
    else if($(obj).val() === 'fixed') {
      ancestor.find('.commissions-currency-symbol').show();
      ancestor.find('.commissions-percent-symbol').hide();
    }
  }

  function wafp_set_multi_commission_type(obj) {
    var data_id = jQuery(obj).attr('data-id');
    if($(obj).val() === 'percentage') {
      $('#'+data_id+'-currency-symbol').hide();
      $('#'+data_id+'-percent-symbol').show();
    }
    else if($(obj).val() === 'fixed') {
      $('#'+data_id+'-currency-symbol').show();
      $('#'+data_id+'-percent-symbol').hide();
    }
  }

  //Delete Commission AJAX
  jQuery('.wafp-commissions-delete a').click(function() {
    if(confirm('Are you sure you want to delete this Commission?')) {
      var i = jQuery(this).attr('data-id');
      var data = {
        action: 'wafp_delete_commission',
        id: i
      };

      jQuery.post(ajaxurl, data, function(response) {
        if(response == 'true') {
          jQuery('#wafp-commissions-' + i).fadeOut('slow', function () {
            jQuery('#wafp-commissions-' + i).remove();
            set_add_commission_visibility();
            set_commission_level_visibility();
          });
        } else {
          alert(response);
        }
      });
    }

    return false;
  });

  $('.wafp_multi_commission_type').each(function() {
    wafp_set_multi_commission_type(this);
  }).change(function() {
    wafp_set_multi_commission_type(this);
  });

  $commissions.on('click', '.wafp-commissions-delete-new', function() {
    jQuery(this).parent().slideUp('fast', function() {
      jQuery(this).remove();
      set_add_commission_visibility();
      set_commission_level_visibility();
    });
    return false;
  });

  $commissions.on('change', '.wafp_multi_commission_type_new', function() {
    set_multi_commission_type_new(this);
  });
});
