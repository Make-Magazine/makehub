// Easy Affiliate Options JS

/* globals jQuery, EsafOptionsL10n */

var EsafOptions = (function ($) {

  var options = {

    working: false,
    pro_dashboard_logo_media_frame: null,

    initialize: function () {
      options.setup_nav();
      options.setup_license();
      options.setup_dashboard_page_selector();
      options.setup_color_pickers();
      options.setup_pro_dashboard_logo_uploader();
      options.setup_edit_email();
      options.setup_insert_email_variable();
      options.setup_send_test_email();
      options.setup_reset_email();
      options.setup_integration_settings();
    },

    setup_license: function () {
      var $license = $('#esaf-license');

      $license.on('blur', '#wafp-mothership-license', function () {
        options.activate_license($(this).val());
      }).on('paste', '#wafp-mothership-license', function () {
        var $field = $(this);

        setTimeout(function () {
          $field.triggerHandler('blur');
        }, 0);
      });

      $license.on('click', '#esaf-license-deactivate button', options.deactivate_license);
    },

    activate_license: function (license_key) {
      if(options.working) {
        return;
      }

      if(!license_key) {
        return;
      }

      var $license = $('#esaf-license');

      options.working = true;
      $license.find('#esaf-options-license-key-loading').show();
      $license.find('#wafp-mothership-license').prop('readonly', true);
      $license.find('.notice').remove();

      $.ajax({
        method: 'POST',
        url: EsafOptionsL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'esaf_options_activate_license',
          _ajax_nonce: EsafOptionsL10n.activate_license_nonce,
          license_key: license_key
        }
      }).done(function(response) {
        if(response && typeof response == 'object' && typeof response.success == 'boolean') {
          if(response.success) {
            $license.hide().html(response.data).slideToggle();
          }
          else {
            options.activate_license_error(response.data);
          }
        }
        else {
          options.activate_license_error();
        }
      }).fail(function () {
        options.activate_license_error();
      }).always(function() {
        $('#wafp-mothership-license').prop('readonly', false);
        $('#esaf-options-license-key-loading').hide();
        options.working = false;
      });
    },

    activate_license_error: function (message) {
      $('<div class="notice notice-error">').append(
        $('<p>').text(message || EsafOptionsL10n.error_activating_license)
      ).prependTo('#esaf-license');
    },

    deactivate_license: function () {
      if(options.working) {
        return;
      }

      if(!confirm(EsafOptionsL10n.deactivate_license_are_you_sure)) {
        return;
      }

      options.working = true;

      var $license = $('#esaf-license'),
        $button = $license.find('#esaf-license-deactivate button'),
        original_button_html = $button.html(),
        original_button_width = $button.width();

      $button.width(original_button_width).html('<i class="ea-icon ea-icon-spinner animate-spin" aria-hidden="true"></i>');
      $license.find('.notice').remove();

      $.ajax({
        method: 'POST',
        url: EsafOptionsL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'esaf_options_deactivate_license',
          _ajax_nonce: EsafOptionsL10n.deactivate_license_nonce
        }
      }).done(function (response) {
        if(response && typeof response == 'object' && typeof response.success == 'boolean') {
          if(response.success) {
            $license.hide().html(response.data).slideToggle();
          }
          else {
            options.deactivate_license_error(response.data);
          }
        }
        else {
          options.deactivate_license_error();
        }
      }).fail(function () {
        options.deactivate_license_error();
      }).always(function () {
        options.working = false;
        $button.html(original_button_html).width('auto');
      });
    },

    deactivate_license_error: function (message) {
      $('<div class="notice notice-error">').append(
        $('<p>').text(message || EsafOptionsL10n.error_deactivating_license)
      ).prependTo('#esaf-license');
    },

    setup_nav: function () {
      $('.esaf-settings-nav > a').on('click', function () {
        $('.esaf-settings-saved').remove();
        options.set_page($(this).data('id'))
      });

      if(window.location.hash && /^#esaf-/.test(window.location.hash)) {
        options.set_page(window.location.hash.replace(/^#esaf-/, ''));
      }
    },

    set_page: function (page) {
      var $page = $('#esaf-page-' + page);

      if($page.length) {
        var hash = '#esaf-' + page,
          $link = $('.esaf-settings-nav > a').filter(function () {
            return $(this).data('id') === page;
          });

        $('.esaf-settings-nav').find('.esaf-active').removeClass('esaf-active');
        $link.addClass('esaf-active');
        $('.esaf-page').hide();
        $page.show();
        window.location.hash = hash;
      }
    },

    setup_dashboard_page_selector: function () {
      var pages = EsafOptionsL10n.pages;
      var pagesselected_json = $("#wafp-data-selected").text();
      var pagesselected = pagesselected_json ? JSON.parse(pagesselected_json) : [];

      if( pagesselected != null && pagesselected != undefined && pagesselected.length > 0 ) {
        for(var i=0; i < pagesselected.length; i++) {
          $("ol#wafp-dash-pages").append( options.get_pages_dropdown( i, pages, pagesselected[i] ) );
          $('#wafp_remove_nav_pages').show();
        }
      }

      $('#wafp_add_nav_pages').click( function() {
        $("ol#wafp-dash-pages").append( options.get_pages_dropdown( $("ol#wafp-dash-pages").attr('data-index'), pages, '' ) );
        $('#wafp_remove_nav_pages').show();
      });

      $('#wafp_remove_nav_pages').click( function() {
        var index = $("ol#wafp-dash-pages").attr('data-index');
        $("#wafp-nav-page-" + index).remove();
        index = (options.intval(index)-1);
        $("ol#wafp-dash-pages").attr('data-index',index);
        if(index <= 0)
          $('#wafp_remove_nav_pages').hide();
      });
    },

    intval: function (mixed_var, base) {
      // Get the integer value of a variable using the optional base for the conversion
      //
      // version: 1109.2015
      // discuss at:     // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // +   improved by: stensi
      // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // +   input by: Matteo
      // +   bugfixed by: Brett Zamir (http://brett-zamir.me)    // +   bugfixed by: Rafał Kukawski (http://kukawski.pl)
      // *     example 1: intval('Kevin van Zonneveld');
      // *     returns 1: 0
      // *     example 2: intval(4.2);
      // *     returns 2: 4    // *     example 3: intval(42, 8);
      // *     returns 3: 42
      // *     example 4: intval('09');
      // *     returns 4: 9
      // *     example 5: intval('1e', 16);    // *     returns 5: 30
      var tmp;

      var type = typeof(mixed_var);
      if (type === 'boolean') {
        return +mixed_var;
      } else if (type === 'string') {
        tmp = parseInt(mixed_var, base || 10);
        return (isNaN(tmp) || !isFinite(tmp)) ? 0 : tmp;
      } else if (type === 'number' && isFinite(mixed_var)) {
        return mixed_var | 0;
      } else {
        return 0;
      }
    },

    get_pages_dropdown: function ( index, pages, selected ) {
      index = (options.intval(index)+1);
      $("ol#wafp-dash-pages").attr('data-index',index);
      var dropdown = '<li id="wafp-nav-page-' + index + '"><select name="wafp-dash-nav[]">';

      for( var i=0; i < pages.length; i++ ) {
        if( pages[i]['ID'] == selected )
          dropdown += '<option value="' + pages[i]['ID'] + '" selected="selected">' + pages[i]['title'] + '</option>';
        else
          dropdown += '<option value="' + pages[i]['ID'] + '">' + pages[i]['title'] + '</option>';
      }

      dropdown += '</select></li>';

      return dropdown;
    },

    setup_color_pickers: function () {
      if(typeof $.fn.wpColorPicker !== 'function') {
        return;
      }

      $('.esaf-color-picker').wpColorPicker();
    },

    setup_pro_dashboard_logo_uploader: function () {
      $('#esaf-pro-dashboard-logo-choose-button').on('click', options.open_pro_dashboard_logo_media_frame);

      $('#esaf-pro-dashboard-logo-remove-button').on('click', function () {
        $('#esaf-pro-dashboard-logo-preview').html('').hide();
        $('#wafp-pro-dashboard-logo-url').val('');
        $('#esaf-pro-dashboard-logo-choose').show();
        $('#esaf-pro-dashboard-logo-remove').hide();
      });
    },

    open_pro_dashboard_logo_media_frame: function () {
      if(!window.wp || !wp.media) {
        return;
      }

      if(options.pro_dashboard_logo_media_frame) {
        options.pro_dashboard_logo_media_frame.open();
        return;
      }

      options.pro_dashboard_logo_media_frame = wp.media({
        title: EsafOptionsL10n.choose_or_upload_an_image,
        button: {
          text: EsafOptionsL10n.use_this_image
        },
        library: {
          type: 'image'
        }
      });

      options.pro_dashboard_logo_media_frame.on('select', options.on_select_pro_dashboard_logo_media_item);
      options.pro_dashboard_logo_media_frame.open();
    },

    on_select_pro_dashboard_logo_media_item: function () {
      var image = options.pro_dashboard_logo_media_frame.state().get('selection').first().toJSON();

      if(!image || !image.url || !image.url.length) {
        return;
      }

      $('#esaf-pro-dashboard-logo-preview')
        .html($('<img>').attr('src', image.url))
        .show();

      $('#wafp-pro-dashboard-logo-url').val(image.url);

      $('#esaf-pro-dashboard-logo-choose').hide();
      $('#esaf-pro-dashboard-logo-remove').show();
    },

    setup_edit_email: function () {
      $('.esaf-edit-email-toggle').each(function () {
        var $button = $(this),
          edit_text = $button.data('edit-text'),
          cancel_text = $button.data('cancel-text'),
          box = '.' + $button.data('box');

        $button.on('click', function () {
          $(box).toggle('fast', function () {
            if($(box).is(':hidden')) {
              $button.text(edit_text);
            }
            else {
              $button.text(cancel_text);
            }
          });
        });
      });
    },

    setup_insert_email_variable: function () {
      $('button.esaf-insert-email-var').on('click', function(e) {
        e.preventDefault();

        var varid = $(this).data('variable-id');
        var edid  = $(this).data('textarea-id');

        var varstr = $('#'+varid).val();

        var editor = tinyMCE.get(edid);
        if(editor && editor instanceof tinyMCE.Editor && !editor.isHidden()) {
          editor.execCommand('mceInsertContent',false,varstr);
        }
        else {
          var body_selector = '#'+edid;

          var get_cursor_position = function () {
            var el = $(body_selector).get(0);
            var pos = 0;

            if('selectionStart' in el) {
              pos = el.selectionStart;
            } else if ('selection' in document) {
              el.focus();
              var Sel = document.selection.createRange();
              var SelLength = document.selection.createRange().text.length;
              Sel.moveStart('character', -el.value.length);
              pos = Sel.text.length - SelLength;
            }

            return pos;
          }

          var position = get_cursor_position();
          var content = $(body_selector).val();
          var newContent = content.substr(0, position) + varstr + content.substr(position);

          $(body_selector).val(newContent);
        }
      });
    },

    setup_send_test_email: function () {
      $('button.esaf-send-test-email').on('click', function(e) {
        e.preventDefault();

        var $button = $(this),
          button_text = $button.text(),
          button_width = $button.width(),
          subject_id = $button.data('subject-id'),
          body_id = $button.data('body-id'),
          use_template_id = $button.data('use-template-id'),
          subject_selector = '#' + subject_id,
          subject = $(subject_selector).val(),
          use_template_selector = '#' + use_template_id,
          use_template = $(use_template_selector).is(':checked'),
          body = '';

        if($button.data('sending')) {
          return;
        }

        $button.data('sending', true);

        var get_body_from_textarea = function() {
          var body_selector = '#' + body_id;
          body_selector = body_selector.replace(/([\[\]])/g, '\\$1'); // Escape Brackets
          return $(body_selector).val();
        };

        if(typeof tinyMCE !== 'undefined') {
          var editor = tinyMCE.get(body_id);

          if(editor && editor instanceof tinyMCE.Editor && !editor.isHidden() && editor.isDirty()) {
            editor.save();
          }
        }

        body = get_body_from_textarea();

        $button.width(button_width).html('<i class="ea-icon ea-icon-spinner animate-spin" aria-hidden="true"></i>');

        $.ajax({
          method: 'POST',
          url: EsafOptionsL10n.ajax_url,
          dataType: 'json',
          data: {
            action: 'esaf_send_test_email',
            _ajax_nonce: EsafOptionsL10n.send_test_email_nonce,
            s: subject,
            b: body,
            t: use_template
          }
        }).done(function (response) {
          if(response && typeof response.success == 'boolean' && response.success) {
            $button.html('<i class="ea-icon ea-icon-ok esaf-success-text" aria-hidden="true"></i>');
          }
          else {
            $button.html('<i class="ea-icon ea-icon-cancel esaf-error-text" aria-hidden="true"></i>');
          }
        }).fail(function () {
          $button.html('<i class="ea-icon ea-icon-cancel esaf-error-text" aria-hidden="true"></i>');
        }).always(function () {
          setTimeout(function () {
            $button.text(button_text).width('auto');
            $button.data('sending', false);
          }, 2000);
        });
      });
    },

    setup_reset_email: function () {
      $('button.esaf-reset-email').on('click', function(e) {
        e.preventDefault();

        var $button = $(this),
          button_text = $button.text(),
          button_width = $button.width(),
          subject_id = $button.data('subject-id'),
          body_id = $button.data('body-id'),
          option_id = $button.data('option-id'),
          use_template_id = $button.data('use-template-id');

        if($button.data('resetting')) {
          return;
        }

        $button.data('resetting', true);

        $button.width(button_width).html('<i class="ea-icon ea-icon-spinner animate-spin" aria-hidden="true"></i>');

        $.ajax({
          method: 'POST',
          url: EsafOptionsL10n.ajax_url,
          dataType: 'json',
          data: {
            action: 'esaf_set_email_defaults',
            e: option_id,
            _ajax_nonce: EsafOptionsL10n.set_email_defaults_nonce
          }
        }).done(function (response) {
          if(response && typeof response.success == 'boolean' && response.success) {
            var subject_selector = '#' + subject_id;
            $(subject_selector).val(response.data.subject);

            if(typeof tinyMCE != "undefined") {
              var editor = tinyMCE.get(body_id);
              if(editor && editor instanceof tinyMCE.Editor && !editor.isHidden()) {
                editor.setContent(response.data.body);
              }

              var textarea_selector = 'textarea#' + body_id;
              textarea_selector = textarea_selector.replace(/([\[\]])/g, '\\$1'); // Escape Brackets
              $(textarea_selector).val(response.data.body);
            }

            // Always defaults to true for now
            $('#' + use_template_id).prop('checked', true);

            $button.html('<i class="ea-icon ea-icon-ok esaf-success-text" aria-hidden="true"></i>');
          }
          else {
            $button.html('<i class="ea-icon ea-icon-cancel esaf-error-text" aria-hidden="true"></i>');
          }
        }).fail(function () {
          $button.html('<i class="ea-icon ea-icon-cancel esaf-error-text" aria-hidden="true"></i>');
        }).always(function () {
          setTimeout(function () {
            $button.text(button_text).width('auto');
            $button.data('resetting', false);
          }, 2000);
        });
      });
    },

    setup_integration_settings: function () {
      var $popup = $('#esaf-integration-upgrade-popup'),
        $popup_title = $popup.find('h3'),
        $popup_text = $popup.find('p'),
        $popup_button = $popup.find('a'),
        popup_title = $popup_title.text(),
        popup_text = $popup_text.text(),
        popup_button = $popup_button.text();

      $('.esaf-integration-header').on('click', function () {
        var $integration = $(this).closest('.esaf-integration');

        if($integration.hasClass('esaf-integration-upgrade')) {
          $popup_title.text(
            popup_title.replace('%1$s', $integration.data('name')).replace('%2$s', $integration.data('plan'))
          );

          $popup_text.text(
            popup_text.replace('%1$s', $integration.data('name')).replace('%2$s', $integration.data('plan'))
          );

          $popup_button.text(popup_button.replace('%s', $integration.data('plan')));

          if($.magnificPopup) {
            $.magnificPopup.open({
              mainClass: 'esaf-mfp',
              items: {
                src: '#esaf-integration-upgrade-popup',
                type: 'inline'
              }
            });
          }
        }
        else {
          var $settings = $integration.find('.esaf-integration-settings'),
            $arrow = $integration.find('.esaf-integration-logo > i');

          if($settings.is(':visible')) {
            $settings.slideUp();
            $arrow.removeClass('ea-icon-angle-down').addClass('ea-icon-angle-right');
          }
          else {
            $settings.slideDown();
            $arrow.removeClass('ea-icon-angle-right').addClass('ea-icon-angle-down');
          }
        }
      });
    }

  };

  $(options.initialize);

  return options;

})(jQuery);
