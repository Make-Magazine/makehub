(function ($) {

    var tooltipster = function () {
        $('.mo-autoresponder-modal-view-tags').tooltipster({
            functionInit: function (instance, helper) {
                var content = $('#mo-autoresponder-tooltipster-content');
                instance.content(content);
            },
            theme: 'tooltipster-light',
            trigger: 'click',
            side: 'right',
            contentAsHTML: true,
            interactive: true
        });
    };

    var tinymce_event_handler = function () {
        tinymce.get('mo_autoresponder_message').on('keyup change undo redo SetContent', function () {
            this.save();
        });
    };

    var close_modal = function () {
        $('.mo-autoresponder-modal-overlay').remove();
    };

    window.onclick = function (event) {
        var cache = $('.mo-autoresponder-modal-overlay');
        if (event.target === cache[0]) {
            cache.remove();
        }
    };

    $(document).on('click', '#mo-configure-autoresponder', function (e) {
        e.preventDefault();
        var template = wp.template('mo-autores-config');
        var data = _.extend(
            JSON.parse($('.mo-autoresponder-settings-field').val()),
            {
                'firstname_tag': '{{first_name}}',
                'lastname_tag': '{{last_name}}',
                'email_tag': '{{email}}',
                'site_name': '{{site_name}}'
            }
        );

        $(document.body).prepend(template(data));

        $('#mo_autoresponder_message').mo_wp_editor();

        $('.mo-autoresponder-modal-overlay').show();
        tooltipster();
        tinymce_event_handler();
    });

    $(document).on('click', '.mo-autoresponder-modal-close', function (e) {
        e.preventDefault();
        close_modal();
    });

    $(document).on('click', '#mo-autoresponder-modal-send-test-mail', function (e) {
        e.preventDefault();
        var _this = this;
        $('#mo-autoresponder-send-test-mail-spinner').toggleClass('mo-visible');

        $.post(ajaxurl, {
            action: 'mailoptin_send_test_autoresponder_email',
            subject: $('#mo-autoresponder-subject').val(),
            message: $('#mo_autoresponder_message').val(),
            security: $("input[data-customize-setting-link*='[ajax_nonce]']").val()

        }, function (response) {
            $('#mo-autoresponder-send-test-mail-spinner').toggleClass('mo-visible');
            $(_this).blur();
            if (typeof response.success !== 'undefined' && response.success === true) {
                return _.delay(function () {
                    window.alert(autoresponder_globals.test_email_success_msg + ' ' + response.email);
                }, 500);
            }

            console.warn('Failed to send test email');
            window.alert(autoresponder_globals.test_email_error_msg);
        });
    });

    $(document).on('click', '#mo-autoresponder-modal-save', function (e) {
        e.preventDefault();
        var is_active = document.getElementById('mo-autoresponder-activate').checked;
        $('.mo-autoresponder-settings-field').val(
            JSON.stringify({
                active: is_active,
                subject: $('#mo-autoresponder-subject').val(),
                message: $('#mo_autoresponder_message').val()
            })
        ).trigger('change');

        // update config btn font icon
        if (is_active === true) {
            $('#mo-autores-btn-icon-no').hide();
            $('#mo-autores-btn-icon-yes').show();
        }
        else {
            $('#mo-autores-btn-icon-no').show();
            $('#mo-autores-btn-icon-yes').hide();
        }

        close_modal();
    });

})(jQuery);