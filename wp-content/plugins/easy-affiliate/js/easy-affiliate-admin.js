var EsafAdmin = (function ($) {

  var admin = {

    initialize: function () {
      admin.setup_affiliate_search();
      admin.setup_commission_type();
      admin.setup_commission_sanitization();
      admin.setup_commission_override();
      admin.setup_tooltips();
      admin.setup_commission_levels_popup();
      admin.setup_toggle_boxes();
      admin.setup_resend_welcome_email();
      admin.setup_change_ssn();
      admin.setup_auto_generate_affiliate_agreement();
    },

    setup_affiliate_search: function () {
      if(!$.fn.suggest) {
        return;
      }

      $('#wafp-affiliate-referrer, .wafp-affiliate-referrer').suggest(
        EsafAdminL10n.ajax_url + '?action=wafp_affiliate_search', {
          delay: 500,
          minchars: 2
        }
      );
    },

    setup_commission_type: function () {
      admin.set_commission_type();
      $('#wafp_commission_type').on('change', admin.set_commission_type);
    },

    set_commission_type: function () {
      var type = $('#wafp_commission_type').val();

      if(type === 'percentage') {
        $('.wafp_commission_currency_symbol').hide();
        $('.wafp_commission_percentage_symbol').show();
      }
      else if(type === 'fixed') {
        $('.wafp_commission_currency_symbol').show();
        $('.wafp_commission_percentage_symbol').hide();
      }
    },

    setup_commission_sanitization: function () {
      $('#wafp_commission_levels').on('blur', 'input', function() {
        var new_val = 0.00;

        if( $(this).val().match(/\d+(\.\d+)?/) ) {
          new_val = $(this).val();
        }

        if($('#wafp_commission_type').val() === 'percentage' && parseInt(new_val) >= 100) {
          new_val = 100.00;
        }

        new_val = parseFloat(new_val).toFixed(2)

        $(this).val(new_val);
      });
    },

    setup_commission_override: function () {
      admin.set_commission_override();

      $('.esaf-show-commission-override').on('click', function () {
        admin.set_commission_override(true);
      });
    },

    set_commission_override: function (animate) {
      var checked = $('.esaf-show-commission-override').is(':checked');

      if(checked) {
        $('#wafp-commission-override')[animate ? 'slideDown' : 'show']();
        $('.esaf-affiliate-commissions')[animate ? 'slideUp' : 'hide']();
      }
      else {
        $('.esaf-affiliate-commissions')[animate ? 'slideDown' : 'show']();
        $('#wafp-commission-override')[animate ? 'slideUp' : 'hide']();
      }
    },

    setup_tooltips: function () {
      if(!window.tippy) {
        return;
      }

      $('.esaf-tooltip').each(function () {
        var $tooltip = $(this);

        tippy($tooltip[0], {
          content: $tooltip.find('.esaf-data-info').html(),
          interactive: true,
          allowHTML: true,
          theme: 'esaf'
        });
      });
    },

    setup_commission_levels_popup: function () {
      if(!$.magnificPopup) {
        return;
      }

      $('#esaf-commission-levels-upgrade').on('click', function () {
        $.magnificPopup.open({
          mainClass: 'esaf-mfp',
          items: {
            src: '#esaf-commission-levels-upgrade-popup',
            type: 'inline'
          }
        });
      });

      $('#esaf-commission-levels-install').on('click', function () {
        $.magnificPopup.open({
          mainClass: 'esaf-mfp',
          items: {
            src: '#esaf-commission-levels-install-popup',
            type: 'inline'
          }
        });
      });

      $('#esaf-commission-levels-install-cancel').on('click', function () {
        $.magnificPopup.close();
      });

      $('#esaf-commission-levels-install-action').on('click', admin.install_commission_levels_addon);
    },

    install_commission_levels_addon: function () {
      var $button = $(this),
        originalButtonHtml = $button.html(),
        originalButtonWidth = $button.width(),
        action = $button.data('action'),
        $content = $button.closest('.esaf-popup-content'),
        handleError = function (message) {
          $button.prop('disabled', false).removeClass('esaf-loading').html(originalButtonHtml).width('auto');
          $content.append($('<div class="notice notice-error">').append($('<p>').text(message)));
        };

      $content.find('.notice').remove();
      $button.prop('disabled', true).html('<i class="ea-icon ea-icon-spinner animate-spin" aria-hidden="true"></i>').addClass('esaf-loading').width(originalButtonWidth);

      $.ajax({
        type: 'POST',
        url: EsafAdminL10n.ajax_url,
        dataType: 'json',
        data: {
          _ajax_nonce: EsafAdminL10n.install_addon_nonce,
          action: action === 'activate' ? 'esaf_addon_activate' : 'esaf_addon_install',
          plugin: $button.data('plugin')
        }
      }).done(function(response) {
        if(!response || typeof response != 'object' || typeof response.success != 'boolean') {
          handleError(EsafAdminL10n.install_addon_failed);
        }
        else if(!response.success) {
          if(typeof response.data == 'object' && response.data[0] && response.data[0].code) {
            handleError(EsafAdminL10n.install_addon_failed);
          }
          else {
            handleError(response.data);
          }
        }
        else {
          $button.html('<i class="ea-icon ea-icon-ok" aria-hidden="true"></i>');
          $content.append($('<div class="notice notice-success">').append($('<p>').text(action === 'activate' ? EsafAdminL10n.activate_addon_success : EsafAdminL10n.install_addon_success)));
        }
      }).fail(function () {
        handleError(EsafAdminL10n.install_addon_failed);
      });
    },

    view_admin_affiliate_page: function (action, period, wafpage, search, show_loader) {
      if(!search) {
        search = '';
      }

      if(!show_loader) {
        show_loader = false;
      }

      if(show_loader) {
        $('.wafp-stats-loader').show();
      }

      $('.wafp-loading-image').css('display', 'inline-block');

      $.ajax({
        type: "POST",
        url: "index.php",
        data: "plugin=wafp&controller=reports&action=" + action + "&period=" + period + "&wafpage=" + wafpage + "&search=" + search,
        success: function (html) {
          $("#tooltip").remove(); // clear out the tooltip
          $('#wafp-admin-affiliate-panel').replaceWith(html);

          if(show_loader) {
            $('.wafp-stats-loader').hide();
          }

          $('.wafp-loading-image').css('display', 'none');
          $('.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
          $('.nav-tab-wrapper .' + action).addClass('nav-tab-active');
        }
      });
    },

    setup_toggle_boxes: function () {
      $('.esaf-toggle-checkbox').each(function() {
        var box = '.' + $(this).data('box');
        var reverse  = (typeof $(this).data('reverse') !== 'undefined');

        admin.toggle_checkbox_box(this, box, false, reverse);

        $(this).on('click', function() {
          admin.toggle_checkbox_box(this, box, true, reverse);
        });
      });

      $('.esaf-toggle-link').each(function() {
        var box = '.' + $(this).data('box');
        var reverse = (typeof $(this).data('reverse') !== 'undefined');

        reverse ? admin.show_box(box, false) : admin.hide_box(box, false);

        $(this).on('click', function(e) {
          e.preventDefault();
          admin.toggle_link_box(this, box, true);
        });
      });

      $('.esaf-toggle-select').each(function() {
        var boxes = {};

        $(this).find('option').each(function() {
          var value = $(this).val();
          if (typeof value !== 'undefined') {
            boxes[value] = value + '-box';
          }
        });

        admin.toggle_select_box(this, boxes, false);

        $(this).on('change', function(e) {
          admin.toggle_select_box(this, boxes, true);
        });
      });

      $('.esaf-toggle-radio').each(function () {
        var $radio = $(this);

        if($radio.is(':checked')) {
          admin.toggle_radio_box($radio, false);
        }

        $radio.on('change', function () {
          admin.toggle_radio_box($radio, true);
        });
      });
    },

    show_box: function (box, animate) {
      $(box).trigger('esaf_show_box');
      animate ? $(box).slideDown() : $(box).show();
    },

    hide_box: function (box, animate) {
      $(box).trigger('esaf_hide_box');
      animate ? $(box).slideUp() : $(box).hide();
    },

    toggle_checkbox_box: function(checkbox, box, animate, reverse) {
      if($(checkbox).is(':checked')) {
        reverse ? admin.hide_box(box, animate) : admin.show_box(box, animate);
      }
      else {
        reverse ? admin.show_box(box, animate) : admin.hide_box(box, animate);
      }
    },

    toggle_link_box: function(link, box, animate) {
      if($(box).is(':visible')) {
        admin.hide_box(box, animate);
      }
      else {
        admin.show_box(box, animate);
      }
    },

    toggle_select_box: function(select, boxes, animate) {
      var box = '';

      $.each(boxes, function(k,v) {
        box = '.' + v;
        admin.hide_box(box,animate);
      });

      if (typeof boxes[$(select).val()] !== undefined) {
        box = '.' + boxes[$(select).val()];
        admin.show_box(box,animate);
      }
    },

    toggle_radio_box: function ($radio, animate) {
      var $others = $('.esaf-toggle-radio').filter(function () {
        return $(this).attr('name') === $radio.attr('name');
      }).not($radio);

      $others.each(function () {
        var box = $(this).data('box');

        if(box) {
          admin.hide_box('.' + box, animate);
        }
      });

      var box = $radio.data('box');

      if(box) {
        admin.show_box('.' + box, animate);
      }
    },

    setup_resend_welcome_email: function () {
      $('.esaf-resend-welcome-email').on('click', function () {
        admin.resend_welcome_email($(this));
      });
    },

    resend_welcome_email: function ($button) {
      $button.prop('disabled', true);
      $('.esaf-resend-welcome-email-loader').show();

      $.ajax({
        type: 'POST',
        url: EsafAdminL10n.ajax_url,
        dataType: 'json',
        data: {
          _ajax_nonce: $button.data('nonce'),
          action: 'esaf_resend_welcome_email',
          uid: $button.data('user-id')
        }
      }).done(function (response) {
        if(response && typeof response == 'object' && typeof response.success == 'boolean') {
          $('.esaf-resend-welcome-email-message').text(response.data);
        }
      }).always(function () {
        $('.esaf-resend-welcome-email-loader').hide();
        $button.prop('disabled', false);
      });
    },

    setup_change_ssn: function () {
      $('#esaf-change-saved-ssn').on('click', function () {
        var $field = $(EsafAdminL10n.tax_id_field_html);
        $(this).replaceWith($field);
        $field.focus();
      });
    },

    setup_auto_generate_affiliate_agreement: function () {
      if(!$.magnificPopup) {
        return;
      }

      $('#esaf-generate-agreement').on('click', function () {
        $.magnificPopup.open({
          mainClass: 'esaf-mfp',
          items: {
            src: '#esaf-generate-agreement-popup',
            type: 'inline'
          }
        });
      });

      $('#esaf-generate-agreement-cancel').on('click', function () {
        $.magnificPopup.close();
      });

      $('#esaf-generate-agreement-generate').on('click', function () {
        admin.generate_from_template();
        $.magnificPopup.close();
      });
    },

    generate_from_template: function () {
      var company = $('#esaf-generate-agreement-company').val(),
        company_nickname = $('#esaf-generate-agreement-nickname').val(),
        government_law = $('#esaf-generate-agreement-law').val(),
        jurisdiction = $('#esaf-generate-agreement-jurisdiction').val(),
        t = $('#esaf-default-agreement-template').html();

      t = t.replace(/{{ company }}/g, company);
      t = t.replace(/{{ company_nickname }}/g, company_nickname);
      t = t.replace(/{{ goverment_law }}/g, government_law);
      t = t.replace(/{{ jurisdiction }}/g, jurisdiction);

      $('#wafp-affiliate-agreement-text').val(t);
    }

  };

  $(admin.initialize);

  return admin;

})(jQuery);
