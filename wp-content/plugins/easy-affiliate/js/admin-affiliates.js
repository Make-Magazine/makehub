var EsafAffiliates = (function ($) {
  var affiliates = {

    working: false,

    initialize: function () {
      $('.wp_list_wafp_affiliates .column-col_notes i').on('click', affiliates.show_notes_popup);
      $('#esaf-affiliate-notes-save').on('click', affiliates.save_notes);
    },

    show_notes_popup: function () {
      if(!$.magnificPopup) {
        return;
      }

      var $icon = $(this),
        $popup = $('#esaf-affiliate-notes-popup');

      $popup.find('h2').text($icon.data('heading'));
      $popup.find('textarea').val($icon.data('notes'));
      $popup.find('input[type="hidden"]').val($icon.data('affiliate-id'));

      $.magnificPopup.open({
        mainClass: 'esaf-mfp',
        closeOnBgClick: false,
        items: {
          src: '#esaf-affiliate-notes-popup',
          type: 'inline'
        },
        focus: 'textarea'
      });
    },

    save_notes: function () {
      if(affiliates.working) {
        return;
      }

      affiliates.working = true;

      var $popup = $('#esaf-affiliate-notes-popup'),
        $button = $('#esaf-affiliate-notes-save'),
        original_button_width = $button.width(),
        original_button_html = $button.html(),
        data = {
          affiliate_id: $popup.find('input[type="hidden"]').val(),
          notes: $popup.find('textarea').val()
        };

      $button.width(original_button_width).html('<i class="ea-icon ea-icon-spinner animate-spin" aria-hidden="true"></i>');
      $popup.find('.notice').remove();

      $.ajax({
        method: 'POST',
        url: EsafAdminL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'esaf_save_affiliate_notes',
          _ajax_nonce: EsafAffiliatesL10n.save_affiliate_notes_nonce,
          data: JSON.stringify(data)
        }
      }).done(function(response) {
        if(response && typeof response == 'object' && typeof response.success == 'boolean') {
          if(response.success) {
            $('#record_' + data.affiliate_id)
              .find('.column-col_notes i')
              .data('notes', data.notes)
              .toggleClass('esaf-has-notes', data.notes && data.notes.length > 0);

            $.magnificPopup.close();
          }
          else {
            affiliates.save_notes_error(response.data);
          }
        }
        else {
          affiliates.save_notes_error();
        }
      }).fail(function () {
        affiliates.save_notes_error();
      }).always(function() {
        $button.html(original_button_html).width('auto');
        affiliates.working = false;
      });
    },

    save_notes_error: function (message) {
      $('<div class="notice notice-error">').append(
        $('<p>').text(message || EsafAffiliatesL10n.error_saving_notes)
      ).insertAfter('#esaf-affiliate-notes-popup textarea');
    }

  };

  $(affiliates.initialize);

  return affiliates;

})(jQuery);
