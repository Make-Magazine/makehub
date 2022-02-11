jQuery(function ($) {
  var $addonsContainer = $('#esaf-addons-container');

  if ($addonsContainer.length) {
    if (window.List) {
      var list = new List($addonsContainer[0], {
        valueNames: ['esaf-addon-name'],
        listClass: 'esaf-addons'
      });

      $('#esaf-addons-search').on('keyup', function () {
        list.search($(this).val());
      })
      .on('input', function () {
        // Used to detect click on HTML5 clear button
        if ($(this).val() === '') {
          list.search('');
        }
      });
    }

    if ($.fn.matchHeight) {
      $('.esaf-addon .esaf-addon-details').matchHeight({
        byRow: false
      });
    }

    var icons = {
      activate: '<i class="ea-icon ea-icon-toggle-on mp-flip-horizontal" aria-hidden="true"></i>',
      deactivate: '<i class="ea-icon ea-icon-toggle-on" aria-hidden="true"></i>',
      install: '<i class="ea-icon ea-icon-cloud-download" aria-hidden="true"></i>',
      spinner: '<i class="ea-icon ea-icon-spinner animate-spin" aria-hidden="true"></i>',
    };

    $(document).on('click', '.esaf-addon-action button', function () {
      var $button = $(this),
        $addon = $button.closest('.esaf-addon'),
        originalButtonHtml = $button.html(),
        originalButtonWidth = $button.width(),
        type = $button.data('type'),
        action,
        statusClass,
        statusText,
        buttonHtml,
        successText;

      if ($addon.hasClass('esaf-addon-status-active')) {
        action = 'esaf_addon_deactivate';
        statusClass = 'esaf-addon-status-inactive';
        statusText = EsafAddons.inactive;
        buttonHtml = icons.activate + EsafAddons.activate;
      } else if ($addon.hasClass('esaf-addon-status-inactive')) {
        action = 'esaf_addon_activate';
        statusClass = 'esaf-addon-status-active';
        statusText = EsafAddons.active;
        buttonHtml = icons.deactivate + EsafAddons.deactivate;
      } else if ($addon.hasClass('esaf-addon-status-download')) {
        action = 'esaf_addon_install';
        statusClass = 'esaf-addon-status-active';
        statusText = EsafAddons.active;
        buttonHtml = icons.deactivate + EsafAddons.deactivate;
      } else {
        return;
      }

      $button.prop('disabled', true).html(icons.spinner).addClass('esaf-loading').width(originalButtonWidth);

      var data = {
        action: action,
        _ajax_nonce: EsafAddons.nonce,
        plugin: $button.data('plugin'),
        type: type
      };

      var handleError = function (message) {
        $addon.find('.esaf-addon-actions').append($('<div class="esaf-addon-message esaf-addon-message-error">').text(message));
        $button.html(originalButtonHtml);
      };

      $.ajax({
        type: 'POST',
        url: EsafAddons.ajax_url,
        dataType: 'json',
        data: data
      })
      .done(function (response) {
        if (!response || typeof response != 'object' || typeof response.success != 'boolean') {
          handleError(type === 'plugin' ? EsafAddons.plugin_install_failed : EsafAddons.install_failed);
        } else if (!response.success) {
          if (typeof response.data == 'object' && response.data[0] && response.data[0].code) {
            handleError(type === 'plugin' ? EsafAddons.plugin_install_failed : EsafAddons.install_failed);
          } else {
            handleError(response.data);
          }
        } else {
          if (action === 'esaf_addon_install') {
            $button.data('plugin', response.data.basename);
            successText = response.data.message;

            if (!response.data.activated) {
              statusClass = 'esaf-addon-status-inactive';
              statusText = EsafAddons.inactive;
              buttonHtml = icons.activate + EsafAddons.activate;
            }
          } else {
            successText = response.data;
          }

          $addon.find('.esaf-addon-actions').append($('<div class="esaf-addon-message esaf-addon-message-success">').text(successText));

          $addon.removeClass('esaf-addon-status-active esaf-addon-status-inactive esaf-addon-status-download')
                .addClass(statusClass);

          $addon.find('.esaf-addon-status-label').text(statusText);

          $button.html(buttonHtml);
        }
      })
      .fail(function () {
        handleError(type === 'plugin' ? EsafAddons.plugin_install_failed : EsafAddons.install_failed);
      })
      .always(function () {
        $button.prop('disabled', false).removeClass('esaf-loading').width('auto');

        // Automatically clear add-on messages after 3 seconds
        setTimeout(function() {
          $addon.find('.esaf-addon-message').remove();
        }, 3000);
      });
    });
  }
});
