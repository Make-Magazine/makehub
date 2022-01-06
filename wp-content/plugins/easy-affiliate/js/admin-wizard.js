var EsafWizard = (function ($) {
  var wizard = {

    current_step: 1,
    verifying_license_key: false,
    verified_license_key: false,
    working: false,
    creative_media_frame: null,

    initialize: function () {
      $('.esaf-wizard-next-step').on('click', function () {
        wizard.go_to_step(wizard.current_step + 1);
      });

      $('.esaf-wizard-progress-step').on('click', function () {
        var clicked_step = $(this).index() + 1;

        if(clicked_step < wizard.current_step) {
          wizard.go_to_step(clicked_step);
        }
      });

      wizard.setup_license_key_events();
      wizard.setup_migrate_events();
      wizard.setup_ecommerce_events();
      wizard.setup_business_events();
      wizard.setup_affiliate_registration_events();
      wizard.setup_commissions_payouts_events();
      wizard.setup_creatives_events();
    },

    go_to_step: function (step) {
      wizard.current_step = step;
      $('.esaf-wizard-step').hide();
      $('.esaf-wizard-step-' + step).show();
    },

    setup_license_key_events: function () {
      var $license_key = $('#esaf-wizard-license-key');

      $license_key.on('blur', function () {
        wizard.verify_license_key($license_key.val());
      }).on('paste', function () {
        setTimeout(function () {
          $license_key.triggerHandler('blur');
        }, 0);
      });
    },

    verify_license_key: function (license_key) {
      if(wizard.verifying_license_key || wizard.verified_license_key) {
        return;
      }

      if(!license_key) {
        $('#esaf-wizard-license-key-success').hide();
        $('#esaf-wizard-license-key-verified').hide();
        return;
      }

      wizard.verifying_license_key = true;
      $('#esaf-wizard-license-key-loading').show();
      $('#esaf-wizard-license-key').prop('readonly', true);
      $('.esaf-wizard-box-content-license').find('.esaf-error-text').remove();

      $.ajax({
        method: 'POST',
        url: EsafWizardL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'esaf_verify_license_key',
          _ajax_nonce: EsafWizardL10n.verify_license_key_nonce,
          license_key: license_key
        }
      }).done(function(response) {
        if(response && typeof response == 'object' && typeof response.success == 'boolean') {
          if(response.success) {
            wizard.verified_license_key = true;
            $('#esaf-wizard-license-key-success').show();
            $('#esaf-wizard-license-key-verified').slideToggle();
          }
          else {
            wizard.verify_license_key_error(response.data);
          }
        }
        else {
          wizard.verify_license_key_error();
        }
      }).fail(function () {
        wizard.verify_license_key_error();
      }).always(function() {
        $('#esaf-wizard-license-key').prop('readonly', false);
        $('#esaf-wizard-license-key-loading').hide();
        wizard.verifying_license_key = false;
      });
    },

    verify_license_key_error: function (message) {
      $('<div class="esaf-error-text">')
        .text(message || EsafWizardL10n.error_verifying_license_key)
        .insertBefore('#esaf-wizard-license-key-verified');
    },

    setup_migrate_events: function () {
      $('#esaf-wizard-migrate-affiliatewp').on('click', wizard.migrate_affiliatewp);
      $('#esaf-wizard-migrate-affiliate-royale').on('click', wizard.migrate_affiliate_royale);
    },

    migrate_affiliatewp: function () {
      $('#esaf-wizard-migrate-affiliatewp').prop('disabled', true).hide();
      $('#esaf-wizard-migrating-affiliatewp').show().find('.esaf-determinate-progress-bar-inner').css('width', 0);
      $('.esaf-wizard-box-content-migrate').find('.esaf-error-text').remove();
      $('#esaf-wizard-migrate-save-and-continue').prop('disabled', true);

      window.onbeforeunload = function () {
        return EsafWizardL10n.migration_leave_are_you_sure;
      };

      wizard.migrate_affiliatewp_request({ step: 'settings' });
    },

    migrate_affiliatewp_request: function (data) {
      $.ajax({
        method: 'POST',
        url: EsafWizardL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'esaf_migrate_affiliatewp',
          _ajax_nonce: EsafWizardL10n.migrate_affiliatewp_nonce,
          data: JSON.stringify(data)
        }
      }).done(function(response) {
        if(response && typeof response == 'object' && typeof response.success == 'boolean') {
          if(response.success) {
            var $container = $('#esaf-wizard-migrating-affiliatewp');

            if(response.data.progress) {
              $container.find('.esaf-determinate-progress-bar-inner').css('width', response.data.progress + '%');
            }

            if(response.data.status === 'complete') {
              $('#esaf-wizard-migrate-affiliatewp')
                .prop('disabled', true)
                .text(EsafWizardL10n.migration_complete)
                .prepend('<i class="ea-icon ea-icon-ok-circle" aria-hidden="true"></i>')
                .addClass('esaf-complete')
                .show();

              $container.hide();
              $('#esaf-wizard-migrate-save-and-continue').prop('disabled', false);
              window.onbeforeunload = null;

              $('input[name="wafp-integration-type[]"]').prop('checked', false);

              if(response.data.integration) {
                $.each(response.data.integration, function (i, integration) {
                  $('#esaf-payment-integration-' + integration).prop('checked', true);
                });
              }

              if(response.data.registration_type) {
                $('input[name="wafp-registration-type"]').filter(function () {
                  return $(this).val() === response.data.registration_type;
                }).prop('checked', true);
              }

              if(response.data.commission_levels_html) {
                var $commission_levels = $('#wafp_commission_levels');

                $commission_levels.html(response.data.commission_levels_html);

                if($commission_levels.children().length > 1) {
                  $commission_levels.addClass('wafp-has-multiple-commission-levels');
                }
                else {
                  $commission_levels.removeClass('wafp-has-multiple-commission-levels');
                }
              }

              if(response.data.commission_type) {
                $('#wafp_commission_type').val(response.data.commission_type).trigger('change');
              }
            }
            else {
              $container.find('.esaf-wizard-migrating-affiliatewp-text').text(response.data.status_text);
              wizard.migrate_affiliatewp_request(response.data);
            }
          }
          else {
            wizard.migrate_affiliatewp_error(response.data);
          }
        }
        else {
          wizard.migrate_affiliatewp_error();
        }
      }).fail(function () {
        wizard.migrate_affiliatewp_error();
      });
    },

    migrate_affiliatewp_error: function (message) {
      var $container = $('#esaf-wizard-migrating-affiliatewp');

      $('#esaf-wizard-migrate-affiliatewp').prop('disabled', false).show();
      $container.hide().find('.esaf-wizard-migrating-affiliatewp-text').text(EsafWizardL10n.migrating_settings);
      $container.find('.esaf-determinate-progress-bar-inner').css('width', 0);
      $('#esaf-wizard-migrate-save-and-continue').prop('disabled', false);
      window.onbeforeunload = null;

      $('<div class="esaf-error-text">')
        .text(message || EsafWizardL10n.error_migrating_affiliatewp)
        .insertAfter('#esaf-wizard-migrate-affiliatewp');
    },

    migrate_affiliate_royale: function () {
      $('#esaf-wizard-migrate-affiliate-royale').prop('disabled', true).hide();
      $('#esaf-wizard-migrating-affiliate-royale').show();
      $('.esaf-wizard-box-content-migrate').find('.esaf-error-text').remove();
      $('#esaf-wizard-migrate-save-and-continue').prop('disabled', true);

      $.ajax({
        method: 'POST',
        url: EsafWizardL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'esaf_migrate_affiliate_royale',
          _ajax_nonce: EsafWizardL10n.migrate_affiliate_royale_nonce
        }
      }).done(function(response) {
        if(response && typeof response == 'object' && typeof response.success == 'boolean') {
          if(response.success) {
            $('#esaf-wizard-migrate-affiliate-royale')
              .prop('disabled', true)
              .text(EsafWizardL10n.migration_complete)
              .prepend('<i class="ea-icon ea-icon-ok-circle" aria-hidden="true"></i>')
              .addClass('esaf-complete')
              .show();
          }
          else {
            wizard.migrate_affiliate_royale_error(response.data);
          }
        }
        else {
          wizard.migrate_affiliate_royale_error();
        }
      }).fail(function () {
        wizard.migrate_affiliate_royale_error();
      }).always(function() {
        $('#esaf-wizard-migrating-affiliate-royale').hide();
        $('#esaf-wizard-migrate-save-and-continue').prop('disabled', false);
      });
    },

    migrate_affiliate_royale_error:  function (message) {
      $('#esaf-wizard-migrate-affiliate-royale').prop('disabled', false).show();

      $('<div class="esaf-error-text">')
        .text(message || EsafWizardL10n.error_migrating_affiliatewp)
        .insertAfter('#esaf-wizard-migrate-affiliate-royale');
    },

    setup_ecommerce_events: function () {
      $('#esaf-wizard-ecommerce-save-and-continue').on('click', wizard.save_ecommerce_setup);
    },

    save_ecommerce_setup: function () {
      if(wizard.working) {
        return;
      }

      wizard.working = true;

      var data = {
        integrations: [],
        'wafp-woocommerce-order-status': $('#wafp-woocommerce-order-status').val()
      };

      if($('#esaf-payment-integration-memberpress').is(':checked')) {
        data.integrations.push('memberpress');
      }

      if($('#esaf-payment-integration-woocommerce').is(':checked')) {
        data.integrations.push('woocommerce');
      }

      if($('#esaf-payment-integration-easy_digital_downloads').is(':checked')) {
        data.integrations.push('easy_digital_downloads');
      }

      if($('#esaf-payment-integration-wpforms').is(':checked')) {
        data.integrations.push('wpforms');
      }

      if($('#esaf-payment-integration-formidable').is(':checked')) {
        data.integrations.push('formidable');
      }

      var $button = $('#esaf-wizard-ecommerce-save-and-continue'),
        original_button_html = $button.html(),
        original_button_width = $button.width();

      $button.width(original_button_width).html('<i class="ea-icon ea-icon-spinner animate-spin" aria-hidden="true"></i>');

      $('.esaf-wizard-box-content-ecommerce').find('.esaf-error-text').remove();

      $.ajax({
        method: 'POST',
        url: EsafWizardL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'esaf_wizard_save_ecommerce_setup',
          _ajax_nonce: EsafWizardL10n.save_ecommerce_setup_nonce,
          data: JSON.stringify(data)
        }
      }).done(function(response) {
        if(response && typeof response == 'object' && typeof response.success == 'boolean') {
          if(response.success) {
            wizard.go_to_step(wizard.current_step + 1);
          }
          else {
            wizard.save_ecommerce_setup_error(response.data);
          }
        }
        else {
          wizard.save_ecommerce_setup_error();
        }
      }).fail(function () {
        wizard.save_ecommerce_setup_error();
      }).always(function() {
        wizard.working = false;
        $button.html(original_button_html).width('auto');
      });
    },

    save_ecommerce_setup_error: function (message) {
      $('<div class="esaf-error-text">')
        .text(message || EsafWizardL10n.error_saving_ecommerce_setup)
        .insertAfter('#esaf-wizard-ecommerce-save-and-continue');
    },

    setup_business_events: function () {
      $('#esaf-wizard-business-save-and-continue').on('click', wizard.save_business_information);
    },

    save_business_information: function () {
      if(wizard.working) {
        return;
      }

      wizard.working = true;
      $('.esaf-wizard-box-content-business').find('.esaf-error-text').remove();

      var has_error = false,
        fields = [
          { id: 'wafp-business-name', required: true },
          { id: 'wafp-business-address-one', required: true },
          { id: 'wafp-business-address-two', required: false },
          { id: 'wafp-business-address-city', required: true },
          { id: 'wafp-business-address-state', required: true },
          { id: 'wafp-business-address-zip', required: true },
          { id: 'wafp-business-address-country', required: true },
          { id: 'wafp-business-address-tax-id', required: false }
        ],
        data = {};

      $.each(fields, function (i, field) {
        var $field = $('#' + field.id);

        if($field.length) {
          var value = $field.val();

          data[field.id] = value;

          if(field.required) {
            if(!value || !value.length) {
              has_error = true;

              $('<div class="esaf-error-text">')
                .text(EsafWizardL10n.this_field_is_required)
                .insertAfter($field);
            }
          }
        }
      });

      if(has_error) {
        wizard.working = false;
        return;
      }

      var $button = $('#esaf-wizard-business-save-and-continue'),
        original_button_html = $button.html(),
        original_button_width = $button.width();

      $button.width(original_button_width).html('<i class="ea-icon ea-icon-spinner animate-spin" aria-hidden="true"></i>');

      $.ajax({
        method: 'POST',
        url: EsafWizardL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'esaf_wizard_save_business_information',
          _ajax_nonce: EsafWizardL10n.save_business_information_nonce,
          data: JSON.stringify(data)
        }
      }).done(function(response) {
        if(response && typeof response == 'object' && typeof response.success == 'boolean') {
          if(response.success) {
            wizard.go_to_step(wizard.current_step + 1);
          }
          else {
            wizard.save_business_information_error(response.data);
          }
        }
        else {
          wizard.save_business_information_error();
        }
      }).fail(function () {
        wizard.save_business_information_error();
      }).always(function() {
        wizard.working = false;
        $button.html(original_button_html).width('auto');
      });
    },

    save_business_information_error: function (message) {
      $('<div class="esaf-error-text">')
        .text(message || EsafWizardL10n.error_saving_business_information)
        .insertAfter('#esaf-wizard-business-save-and-continue');
    },

    setup_affiliate_registration_events: function() {
      $('#esaf-wizard-affiliate-registration-save-and-continue').on('click', wizard.save_affiliate_registration_information);
    },

    save_affiliate_registration_information: function () {
      if(wizard.working) {
        return;
      }

      wizard.working = true;

      var $button = $('#esaf-wizard-affiliate-registration-save-and-continue'),
        original_button_html = $button.html(),
        original_button_width = $button.width(),
        data = {
          'wafp-registration-type': $('input[name="wafp-registration-type"]:checked').val(),
          'wafp-show-address-fields': $('#wafp-show-address-fields').is(':checked'),
          'wafp-show-address-fields-account': $('#wafp-show-address-fields-account').is(':checked'),
          'wafp-require-address-fields': $('#wafp-require-address-fields').is(':checked'),
          'wafp-show-tax-id-fields': $('#wafp-show-tax-id-fields').is(':checked'),
          'wafp-show-tax-id-fields-account': $('#wafp-show-tax-id-fields-account').is(':checked'),
          'wafp-require-tax-id-fields': $('#wafp-require-tax-id-fields').is(':checked'),
          'wafp-affiliate-agreement-enabled': $('#wafp-affiliate-agreement-enabled').is(':checked'),
          'wafp-affiliate-agreement-text': $('#wafp-affiliate-agreement-text').val()
        };

      $button.width(original_button_width).html('<i class="ea-icon ea-icon-spinner animate-spin" aria-hidden="true"></i>');

      $.ajax({
        method: 'POST',
        url: EsafWizardL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'esaf_wizard_save_affiliate_registration_information',
          _ajax_nonce: EsafWizardL10n.save_affiliate_registration_information_nonce,
          data: JSON.stringify(data)
        }
      }).done(function(response) {
        if(response && typeof response == 'object' && typeof response.success == 'boolean') {
          if(response.success) {
            wizard.go_to_step(wizard.current_step + 1);
          }
          else {
            wizard.save_affiliate_registration_information_error(response.data);
          }
        }
        else {
          wizard.save_affiliate_registration_information_error();
        }
      }).fail(function () {
        wizard.save_affiliate_registration_information_error();
      }).always(function() {
        wizard.working = false;
        $button.html(original_button_html).width('auto');
      });
    },

    save_affiliate_registration_information_error: function (message) {
      $('<div class="esaf-error-text">')
        .text(message || EsafWizardL10n.error_saving_affiliate_registration_information)
        .insertAfter('#esaf-wizard-affiliate-registration-save-and-continue');
    },

    setup_commissions_payouts_events: function() {
      $('#esaf-wizard-commissions-payouts-save-and-continue').on('click', wizard.save_commissions_payouts);
    },

    save_commissions_payouts: function () {
      if(wizard.working) {
        return;
      }

      wizard.working = true;

      var $button = $('#esaf-wizard-commissions-payouts-save-and-continue'),
        original_button_html = $button.html(),
        original_button_width = $button.width(),
        $commission_levels = $('#wafp_commission_levels'),
        $commission_level_fields = $commission_levels.find('input[name="wafp-commission[]"]'),
        errors = [],
        commissions = [],
        data = {
          'wafp-commission-type': $('#wafp_commission_type').val(),
          'wafp-subscription-commissions': $('input[name="wafp-subscription-commissions"]:checked').val(),
          'wafp-payment-type': $('input[name="wafp-payment-type"]:checked').val(),
          'wafp-paypal-client-id': $('#wafp-paypal-client-id').val(),
          'wafp-paypal-secret-id': $('#wafp-paypal-secret-id').val()
        };

      $('.esaf-wizard-box-content-commissions-payouts').find('.esaf-error-text').remove();

      if($commission_level_fields.length) {
        $commission_level_fields.each(function (i) {
          var value = $(this).val(),
            level = i + 1;

          if(typeof value != 'string' || value === '' || !wizard.is_number(value) || value < 0) {
            errors.push({
              id: '#wafp_commission_levels',
              message: EsafWizardL10n.commission_must_be_number.replace('%d', level)
            });
          }
          else if(data['wafp-commission-type'] === 'percentage' && value > 100) {
            errors.push({
              id: '#wafp_commission_levels',
              message: EsafWizardL10n.commission_percentage_range.replace('%d', level)
            });
          }
          else {
            commissions.push($(this).val());
          }
        });
      }
      else {
        errors.push({
          id: '#wafp_commission_levels',
          message: EsafWizardL10n.commission_must_be_number.replace('%d', level)
        });
      }

      data['wafp-commission'] = commissions;

      if(data['wafp-payment-type'] === 'paypal-1-click') {
        if(typeof data['wafp-paypal-client-id'] != 'string' || data['wafp-paypal-client-id'] === '') {
          errors.push({
            id: '#wafp-paypal-client-id',
            message: EsafWizardL10n.paypal_client_id_required
          });
        }

        if(typeof data['wafp-paypal-secret-id'] != 'string' || data['wafp-paypal-secret-id'] === '') {
          errors.push({
            id: '#wafp-paypal-secret-id',
            message: EsafWizardL10n.paypal_secret_key_required
          });
        }
      }

      if(errors.length) {
        $.each(errors, function (i, error) {
          $('<div class="esaf-error-text">')
            .text(error.message)
            .insertAfter(error.id);
        });

        wizard.working = false;
        return;
      }

      $button.width(original_button_width).html('<i class="ea-icon ea-icon-spinner animate-spin" aria-hidden="true"></i>');

      $.ajax({
        method: 'POST',
        url: EsafWizardL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'esaf_wizard_save_commissions_payouts',
          _ajax_nonce: EsafWizardL10n.save_commissions_payouts_nonce,
          data: JSON.stringify(data)
        }
      }).done(function(response) {
        if(response && typeof response == 'object' && typeof response.success == 'boolean') {
          if(response.success) {
            wizard.go_to_step(wizard.current_step + 1);
          }
          else {
            wizard.save_commissions_payouts_error(response.data);
          }
        }
        else {
          wizard.save_commissions_payouts_error();
        }
      }).fail(function () {
        wizard.save_commissions_payouts_error();
      }).always(function() {
        wizard.working = false;
        $button.html(original_button_html).width('auto');
      });
    },

    save_commissions_payouts_error: function (message) {
      $('<div class="esaf-error-text">')
        .text(message || EsafWizardL10n.error_saving_commissions_payouts)
        .insertAfter('#esaf-wizard-commissions-payouts-save-and-continue');
    },

    is_number: function (n) {
      return !isNaN(parseFloat(n)) && isFinite(n);
    },

    setup_creatives_events: function () {
      if(!$.magnificPopup) {
        return;
      }

      $('#esaf-wizard-creatives-add-creative').on('click', wizard.open_upload_creative_popup);

      $('input[name="esaf_wizard_creative_type"]').on('change', function () {
        var value = $(this).val();

        $('#esaf-wizard-creative-form-banner-row')[value === 'banner' ? 'show' : 'hide']();
        $('#esaf-wizard-creative-form-text-row')[value === 'banner' ? 'hide' : 'show']();
      });

      $('#esaf-wizard-creative-form-banner-button').on('click', wizard.open_creative_media_frame);

      $('#esaf-wizard-creative-form-remove-banner-button').on('click', function () {
        $('#esaf-wizard-creative-form-banner-preview').removeData('esaf-image').html('').hide();
        $('#esaf-wizard-creative-form-banner').show();
        $('#esaf-wizard-creative-form-remove-banner').hide();
      });

      $('#esaf-wizard-creatives-create-creative').on('click', wizard.create_creative);
    },

    open_upload_creative_popup: function () {
      // Reset the Upload Creative form
      $('#esaf-wizard-creative-form-name').val('');
      $('#esaf-wizard-creative-form-url').val('');
      $('#esaf-wizard-creative-type-banner').prop('checked', true).triggerHandler('change');
      $('#esaf-wizard-creative-form-banner-preview').removeData('esaf-image').html('').hide();
      $('#esaf-wizard-creative-form-banner').show();
      $('#esaf-wizard-creative-form-remove-banner').hide();
      $('#esaf-wizard-creative-form-text').val('');

      $.magnificPopup.open({
        mainClass: 'esaf-mfp',
        closeOnBgClick: false,
        items: {
          src: '#esaf-wizard-creatives-popup',
          type: 'inline'
        }
      });
    },

    open_creative_media_frame: function () {
      if(!window.wp || !wp.media) {
        return;
      }

      if(wizard.creative_media_frame) {
        wizard.creative_media_frame.open();
        return;
      }

      wizard.creative_media_frame = wp.media({
        title: EsafWizardL10n.choose_or_upload_banner,
        button: {
          text: EsafWizardL10n.use_this_image
        },
        library: {
          type: 'image'
        }
      });

      wizard.creative_media_frame.on('select', wizard.on_select_media_item);
      wizard.creative_media_frame.open();
    },

    on_select_media_item: function () {
      var image = wizard.creative_media_frame.state().get('selection').first().toJSON();

      if(!image || !image.url || !image.url.length) {
        return;
      }

      $('#esaf-wizard-creative-form-banner-preview')
        .data('esaf-image', image)
        .html($('<img>').attr('src', image.url))
        .show();

      $('#esaf-wizard-creative-form-banner').hide();
      $('#esaf-wizard-creative-form-remove-banner').show();
    },

    create_creative: function () {
      if(wizard.working) {
        return;
      }

      wizard.working = true;
      $('#esaf-wizard-creatives-popup').find('.esaf-error-text').remove();

      var $button = $('#esaf-wizard-creatives-create-creative'),
        original_button_html = $button.html(),
        original_button_width = $button.width(),
        errors = [],
        data = {
          name: $('#esaf-wizard-creative-form-name').val(),
          url: $('#esaf-wizard-creative-form-url').val(),
          type: $('input[name="esaf_wizard_creative_type"]:checked').val()
        };

      if(typeof data.name != 'string' || data.name === '') {
        errors.push({
          id: '#esaf-wizard-creative-form-name',
          message: EsafWizardL10n.this_field_is_required
        });
      }

      if(typeof data.url != 'string' || data.url === '') {
        errors.push({
          id: '#esaf-wizard-creative-form-url',
          message: EsafWizardL10n.this_field_is_required
        });
      }

      if(data.type === 'banner') {
        data.image = $('#esaf-wizard-creative-form-banner-preview').data('esaf-image');

        if(!data.image) {
          errors.push({
            id: '#esaf-wizard-creative-form-remove-banner',
            message: EsafWizardL10n.a_banner_image_is_required
          });
        }
      }
      else {
        data.text = $('#esaf-wizard-creative-form-text').val();

        if(typeof data.text != 'string' || data.text === '') {
          errors.push({
            id: '#esaf-wizard-creative-form-text',
            message: EsafWizardL10n.this_field_is_required
          });
        }
      }

      if(errors.length) {
        $.each(errors, function (i, error) {
          $('<div class="esaf-error-text">')
            .text(error.message)
            .insertAfter(error.id);
        });

        wizard.working = false;
        return;
      }

      $button.width(original_button_width).html('<i class="ea-icon ea-icon-spinner animate-spin" aria-hidden="true"></i>');

      $.ajax({
        method: 'POST',
        url: EsafWizardL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'esaf_wizard_add_creative',
          _ajax_nonce: EsafWizardL10n.add_creative_nonce,
          data: JSON.stringify(data)
        }
      }).done(function(response) {
        if(response && typeof response == 'object' && typeof response.success == 'boolean') {
          if(response.success) {
            var $uploaded = $('#esaf-wizard-creatives-uploaded'),
              $tbody = $uploaded.find('> table > tbody'),
              $rows = $tbody.children('tr');

            if($rows.length > 5) {
              $rows.last().remove();
            }

            $(response.data.row).insertAfter($rows.first());
            $uploaded.show();
            $('#esaf-wizard-creatives-skip-and-continue').html(EsafWizardL10n.save_and_continue);
            $.magnificPopup.close();
          }
          else {
            wizard.add_creative_error(response.data);
          }
        }
        else {
          wizard.add_creative_error();
        }
      }).fail(function () {
        wizard.add_creative_error();
      }).always(function() {
        wizard.working = false;
        $button.html(original_button_html).width('auto');
      });
    },

    add_creative_error: function (message) {
      $('<div class="esaf-error-text">')
        .text(message || EsafWizardL10n.error_saving_commissions_payouts)
        .insertAfter('#esaf-wizard-creatives-create-creative');
    }

  };

  $(wizard.initialize);

  return wizard;

})(jQuery);
