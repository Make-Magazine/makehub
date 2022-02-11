var Esaf = (function($) {

  var Esaf = {

    loading_icon: '<i class="ea-icon ea-icon-spinner animate-spin" aria-hidden="true"></i>',

    initialize: function () {
      Esaf.setup_clipboard($('body'));
      Esaf.setup_affiliate_agreement_toggle();
      Esaf.setup_close_default_link_button();
      Esaf.setup_text_links_infinite_scroller();
      Esaf.setup_banner_links_infinite_scroller();
      Esaf.setup_affiliate_payments_infinite_scroller();
      Esaf.setup_text_links_popup();
      Esaf.setup_coupons_popup();
      Esaf.setup_banner_links_popup();
      Esaf.setup_custom_links();
      Esaf.setup_account_form_logic();
      Esaf.setup_responsive_menus();
    },

    setup_affiliate_agreement_toggle: function () {
      $('#wafp_agreement_agree').click(function() {
        $('#wafp_signup_agreement_text').slideToggle();
        return false;
      });
    },

    setup_close_default_link_button: function () {
      var $my_affiliate_link = $('#esaf-dashboard-my-affiliate-link');

      $my_affiliate_link.find('.ea-icon-cancel').on('click', function () {
        $my_affiliate_link.hide();
      });
    },

    setup_clipboard: function ($container) {
      if(!window.ClipboardJS || !$container.length) {
        return;
      }

      $container.find('.esaf-copy-clipboard').each(function () {
        var $trigger = $(this),
          clipboard = new ClipboardJS($trigger[0], {
            container: $container[0]
          });

        clipboard.on('success', function (e) {
          e.clearSelection();
          Esaf.show_tooltip($trigger, EsafL10n.copied);
        });

        clipboard.on('error', function () {
          if(/iPhone|iPad/i.test(navigator.userAgent)){
            Esaf.show_tooltip($trigger, EsafL10n.no_support);
          }
          else if(/Mac/i.test(navigator.userAgent)){
            Esaf.show_tooltip($trigger, EsafL10n.press_to_copy.replace('%s', '⌘-C'));
          }
          else{
            Esaf.show_tooltip($trigger, EsafL10n.press_to_copy.replace('%s', 'Ctrl-C'));
          }
        });

        $trigger.on('blue mouseleave', function () {
          Esaf.clear_tooltip($trigger);
        });
      });
    },

    show_tooltip: function ($element, message) {
      $element.addClass('tooltipped tooltipped-n tooltipped-no-delay').attr('aria-label', message);
    },

    clear_tooltip: function ($element) {
      $element.removeClass('tooltipped tooltipped-n tooltipped-no-delay').removeAttr('aria-label');
    },

    setup_infinite_scroller: function ($observer, callback) {
      if(!window.IntersectionObserver) {
        return;
      }

      var observer = new IntersectionObserver(function(entries) {
        if(entries[0].isIntersecting) {
          callback(observer);
        }
      });

      observer.observe($observer[0]);
    },

    setup_text_links_infinite_scroller: function () {
      var $table = $('#esaf-creatives-text-links-table');

      if(!$table.length) {
        return;
      }

      var page = 1,
        fetching = false,
        $observer = $('<div class="esaf-infinite-scroll-observer">').insertAfter($table),
        $spinner = $(Esaf.loading_icon).hide().appendTo($observer),
        handler = function (observer) {
          if(fetching) {
            return;
          }

          fetching = true;
          $spinner.show();

          $.ajax({
            method: 'GET',
            url: EsafL10n.ajax_url,
            dataType: 'json',
            data: {
              action: 'esaf_get_text_links',
              offset: ++page
            }
          }).done(function(response) {
            if(response && typeof response == 'object' && response.success) {
              if(response.data.length) {
                $.each(response.data, function (i, text_link) {
                  $table.find('> tbody').append(text_link);
                });
              }
              else {
                // There are no more links
                observer.disconnect();
              }
            }
          }).always(function() {
            $spinner.hide();
            fetching = false;
          });
        };

      Esaf.setup_infinite_scroller($observer, handler);
    },

    setup_banner_links_infinite_scroller: function () {
      var $grid = $('#esaf-creatives-banners-grid');

      if(!$grid.length) {
        return;
      }

      var page = 1,
        fetching = false,
        $observer = $('<div class="esaf-infinite-scroll-observer">').insertAfter($grid),
        $spinner = $(Esaf.loading_icon).hide().appendTo($observer),
        handler = function (observer) {
          if(fetching) {
            return;
          }

          fetching = true;
          $spinner.show();

          $.ajax({
            method: 'GET',
            url: EsafL10n.ajax_url,
            dataType: 'json',
            data: {
              action: 'esaf_get_banner_links',
              offset: ++page
            }
          }).done(function(response) {
            if(response && typeof response == 'object' && response.success) {
              if(response.data.length) {
                $.each(response.data, function (i, link) {
                  $grid.append(link);
                });
              }
              else {
                // There are no more links
                observer.disconnect();
              }
            }
          }).always(function() {
            $spinner.hide();
            fetching = false;
          });
        };

      Esaf.setup_infinite_scroller($observer, handler);
    },

    setup_affiliate_payments_infinite_scroller: function () {
      var $table = $('#esaf-affilate-payments-table');

      if(!$table.length) {
        return;
      }

      var page = 1,
        fetching = false,
        $observer = $('<div class="esaf-infinite-scroll-observer">').insertAfter($table),
        $spinner = $(Esaf.loading_icon).hide().appendTo($observer),
        handler = function (observer) {
          if(fetching) {
            return;
          }

          fetching = true;
          $spinner.show();

          $.ajax({
            method: 'GET',
            url: EsafL10n.ajax_url,
            dataType: 'json',
            data: {
              action: 'esaf_get_affiliate_payments',
              offset: ++page
            }
          }).done(function(response) {
            if(response && typeof response == 'object' && response.success) {
              if(response.data.length) {
                $.each(response.data, function (i, text_link) {
                  $table.find('> tbody').append(text_link);
                });
              }
              else {
                // There are no more links
                observer.disconnect();
              }
            }
          }).always(function() {
            $spinner.hide();
            fetching = false;
          });
        };

      Esaf.setup_infinite_scroller($observer, handler);
    },

    setup_text_links_popup: function () {
      var $table = $('#esaf-creatives-text-links-table');

      if(!$table.length || !$.magnificPopup) {
        return;
      }

      Esaf.setup_clipboard($('#esaf-text-link-get-html-code-popup'));

      $table.on('click', '.esaf-text-link-get-html-code', function (e) {
        e.preventDefault();

        var $link = $(this),
          html_code = $link.data('html-code') || '',
          url_only = $link.data('url-only') || '';

        $('#esaf-text-link-get-html-code-field').val(html_code);
        $('#esaf-text-link-get-html-code-url-only').val(url_only);

        $.magnificPopup.open({
          items: {
            src: '#esaf-text-link-get-html-code-popup',
            type: 'inline'
          }
        });
      });
    },

    setup_coupons_popup: function () {
      var $table = $('#esaf-mepr-coupons-table');

      if(!$table.length || !$.magnificPopup) {
        return;
      }

      Esaf.setup_clipboard($('#esaf-coupon-get-html-code-popup'));

      $table.on('click', '.esaf-coupon-get-html-code', function (e) {
        e.preventDefault();

        var $link = $(this),
          html_code = $link.data('html-code') || '',
          url_only = $link.data('url-only') || '';

        $('#esaf-coupon-get-html-code-field').val(html_code);
        $('#esaf-coupon-get-html-code-url-only').val(url_only);

        $.magnificPopup.open({
          items: {
            src: '#esaf-coupon-get-html-code-popup',
            type: 'inline'
          }
        });
      });
    },

    setup_banner_links_popup: function () {
      var $grid = $('#esaf-creatives-banners-grid');

      if(!$grid.length || !$.magnificPopup) {
        return;
      }

      Esaf.setup_clipboard($('#esaf-banner-link-get-html-code-popup'));

      $grid.on('click', '.esaf-banner-link-get-html-code', function (e) {
        e.preventDefault();

        var $link = $(this),
          html_code = $link.data('html-code') || '',
          url_only = $link.data('url-only') || '',
          title = $link.data('banner-id') || '',
          height = $link.data('banner-height') || '',
          width = $link.data('banner-width') || '',
          image_url = $link.attr('src') || '';

        $('#esaf-banner-link-get-html-code-field').val(html_code);
        $('#esaf-banner-link-information').attr('src', image_url);
        $('#esaf-banner-link-get-html-code-url-only').val(url_only);
        $('#esaf-banner-link-get-html-id').html(title);
        $('#esaf-banner-link-information-width').html(width);
        $('#esaf-banner-link-information-height').html(height);

        $.magnificPopup.open({
          items: {
            src: '#esaf-banner-link-get-html-code-popup',
            type: 'inline'
          }
        });
      });
    },

    setup_custom_links: function () {
      $('#esaf-dashboard-custom-links-form').on('submit', Esaf.create_custom_link);
      $('#esaf-dashboard-custom-links-table').on('click', '.esaf-dashboard-custom-link-edit', Esaf.edit_custom_link);
      $('#esaf-dashboard-custom-links-update-form').on('submit', Esaf.update_custom_link);
    },

    create_custom_link: function (e) {
      e.preventDefault();

      var $button = $('#esaf-dashboard-custom-link-create'),
        original_button_html = $button.html(),
        original_button_width = $button.width(),
        $url = $('#esaf-dashboard-custom-link-url-field'),
        url = $url.val();

      if (!url || !url.length || Esaf.working) {
        return;
      }

      Esaf.working = true;
      $button.width(original_button_width).html(Esaf.loading_icon);
      $('#esaf-dashboard-custom-links-form').find('.esaf-error-text').remove();

      $.ajax({
        method: 'POST',
        url: EsafL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'esaf_create_custom_link',
          destination_url: url
        }
      }).done(function(response) {
        if(response && typeof response == 'object' && typeof response.success == 'boolean') {
          if(response.success) {
            var $row = $(response.data);
            $('#esaf-dashboard-custom-links-table-body').prepend($row);
            $url.val('');
            Esaf.setup_clipboard($row);
            $('#esaf-dashboard-custom-links-table').css('display', 'table');
          }
          else {
            Esaf.create_custom_link_error(response.data);
          }
        }
        else {
          Esaf.create_custom_link_error();
        }
      }).fail(function () {
        Esaf.create_custom_link_error();
      }).always(function() {
        Esaf.working = false;
        $button.html(original_button_html).width('auto');
      });
    },

    create_custom_link_error: function (message) {
      $('<span class="esaf-error-text">')
        .text(message || EsafL10n.error_creating_custom_link)
        .insertAfter('#esaf-dashboard-custom-link-create');
    },

    edit_custom_link: function () {
      var $row = $(this).closest('tr');

      $('#esaf-dashboard-custom-link-url-update-field').val($row.data('destination-link'));
      $('#esaf-dashboard-custom-link-id').val($row.data('custom-link-id'));
      $('#esaf-dashboard-custom-links-form').hide();
      $('#esaf-dashboard-custom-links-update-form').show();
    },

    update_custom_link: function (e) {
      e.preventDefault();

      var $button = $('#esaf-dashboard-custom-link-update'),
        original_button_html = $button.html(),
        original_button_width = $button.width(),
        url = $('#esaf-dashboard-custom-link-url-update-field').val(),
        id = $('#esaf-dashboard-custom-link-id').val();

      if (!url || !url.length || Esaf.working) {
        return;
      }

      Esaf.working = true;
      $button.width(original_button_width).html(Esaf.loading_icon);
      $('#esaf-dashboard-custom-links-update-form').find('.esaf-error-text').remove();

      $.ajax({
        method: 'POST',
        url: EsafL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'esaf_update_custom_link',
          destination_url: url,
          link_id: id
        }
      }).done(function(response) {
        if(response && typeof response == 'object' && typeof response.success == 'boolean') {
          if(response.success) {
            var $row = $(response.data);
            $('#esaf-dashboard-custom-link-row-id-' + id).replaceWith($row);
            Esaf.setup_clipboard($row);
            $('#esaf-dashboard-custom-link-url-field').val('');
            $('#esaf-dashboard-custom-links-update-form').hide();
            $('#esaf-dashboard-custom-links-form').show();
          }
          else {
            Esaf.update_custom_link_error(response.data);
          }
        }
        else {
          Esaf.update_custom_link_error();
        }
      }).fail(function () {
        Esaf.update_custom_link_error();
      }).always(function() {
        Esaf.working = false;
        $button.html(original_button_html).width('auto');
      });
    },

    update_custom_link_error: function (message) {
      $('<span class="esaf-error-text">')
        .text(message || EsafL10n.error_updating_custom_link)
        .insertAfter('#esaf-dashboard-custom-link-update');
    },

    setup_account_form_logic: function () {
      var $form = $('.esaf-account-form');

      if(!$form.length) {
        return;
      }

      var $country = $form.find('#esaf-dashboard-address-country'),
        $tax_id_us = $form.find('.esaf_tax_id_us').hide(),
        $tax_id_int = $form.find('.esaf_tax_id_int').hide();

      $country.on('change', function () {
        $tax_id_us[$country.val() === 'US' ? 'show' : 'hide']();
        $tax_id_int[$country.val() === 'US' ? 'hide' : 'show']();
      }).triggerHandler('change');
    },

    setup_responsive_menus: function () {
      $('.esaf-dashboard-responsive-nav-toggle').on('click', function () {
        var $nav = $('.esaf-dashboard-nav-wrapper'),
          cb = $.noop;

        if($nav.is(':visible')) {
          cb = function () {
            $nav.css('display', '');
          };
        }

        $nav.animate({ height: 'toggle' }, 400, cb);
      });

      $('.esaf-pro-dashboard-responsive-nav-toggle').on('click', function () {
        var $nav = $('.esaf-pro-dashboard-menu'),
          cb = $.noop;

        if($nav.is(':visible')) {
          cb = function () {
            $nav.css('display', '');
          };
        }

        $nav.animate({ height: 'toggle' }, 400, cb);
      });
    }

  };

  $(Esaf.initialize);

  return Esaf;

})(jQuery);
