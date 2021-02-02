jQuery(document).ready(function ($) {

    var EEA_QUICKBOOKS_OAUTH;

    /**
     * @namespace EEA_QUICKBOOKS_OAUTH
     * @type {{
     *		oauth_ok_btn : object,
     *		oauth_x_btn : object,
     *		oauth_version_selector : object,
     *		oauth_btn : object,
     *		oauth_dv : object,
     *		ee_ajax_loader : object,
     *		txn_data : object,
     *		processing_pm : object,
     *		oauth_window : object,
     *		oauth_window_timer : number,
     *		oauth_version : string
     * }}
     *
     * @namespace EEA_QUICKBOOKS_OAUTH_ARGS
     * @type {{
     *		espresso_default_styles : string,
     *		wp_stylesheet : string,
     *		qb_blocked_popups_notice : string,
     *		qb_token_error : string,
     *		unknown_container : string,
     *		'v1a_pub_key_title' : string,
     *		'v1a_secret_key_title' : string,
     *		'v2_pub_key_title' : string,
     *		'v2_secret_key_title' : string
     * }}
     */
    EEA_QUICKBOOKS_OAUTH = {

        oauth_ok_btn: {},
        oauth_x_btn: {},
        oauth_version_selector: {},
        oauth_btn: {},
        oauth_dv: {},
        ee_ajax_loader: {},
        txn_data: {},
        oauth_window: null,
        oauth_window_timer: 0,
        processing_pm: {},
        oauth_version: null,


        /**
         * @method initialize
         */
        initialize: function () {
            EEA_QUICKBOOKS_OAUTH.initialize_objects();
            // QuickBooks selected / initialized ?
            if (!EEA_QUICKBOOKS_OAUTH.oauth_btn.length || EEA_QUICKBOOKS_OAUTH.initialized) {
                return;
            }

            EEA_QUICKBOOKS_OAUTH.setup_listeners();

            EEA_QUICKBOOKS_OAUTH.initialized = true;
        },


        /**
         * @method initialize_objects
         */
        initialize_objects: function () {
            EEA_QUICKBOOKS_OAUTH.oauth_ok_btn = $('#eea_quickbooks_oauth_ok');
            EEA_QUICKBOOKS_OAUTH.oauth_x_btn = $('#eea_quickbooks_oauth_x');
            EEA_QUICKBOOKS_OAUTH.oauth_version_selector = $('#eea_quickbooks_oauth_version');
            EEA_QUICKBOOKS_OAUTH.oauth_btn = $('.eea-qb-oauth-btn');
            EEA_QUICKBOOKS_OAUTH.oauth_ok_dv = $('#eea_quickbooks_oauth_ok_dv');
            EEA_QUICKBOOKS_OAUTH.oauth_x_dv = $('#eea_quickbooks_oauth_x_dv');
            EEA_QUICKBOOKS_OAUTH.ee_ajax_loader = $('#espresso-ajax-loading');
            EEA_QUICKBOOKS_OAUTH.oauth_reconnect_btn = $('#eea_qb_oauth_reconnect');
            EEA_QUICKBOOKS_OAUTH.oauth_disconnect_btn = $('#eea_qb_oauth_disconnect');
        },


        /**
         * @method setup_listeners
         */
        setup_listeners: function () {
            EEA_QUICKBOOKS_OAUTH.set_listener_for_oauth_btns();
            EEA_QUICKBOOKS_OAUTH.set_listener_for_oauth_selector();
            EEA_QUICKBOOKS_OAUTH.set_listener_for_form_submit();
        },


        /**
         * @method validate_form_inputs
         */
        validate_form_inputs: function (submitted_form) {
            var goodToGo = true;
            var cntr = 0;

            $(submitted_form).find('.required, .ee-required').each(function () {
                if ($(this).val() === '' || $(this).val() === 0) {
                    $(this).addClass('requires-value').siblings('.validation-notice-dv').fadeIn();
                    goodToGo = false;
                    cntr++;
                }
                $(this).on('change', function () {
                    if ($(this).val() !== '' || $(this).val() !== 0) {
                        $(this).removeClass('requires-value').siblings('.validation-notice-dv').fadeOut('fast');
                    }
                });
                if (cntr >= 1) {
                    var thisPos = $(this).offset();
                    $('html,body').scrollTop(thisPos.top - 500);
                }
            });
            return goodToGo;
        },


        /**
         * @method set_listener_for_oauth_btns
         */
        set_listener_for_oauth_btns: function () {
            // oAuth'enticate.
            EEA_QUICKBOOKS_OAUTH.oauth_x_btn.on('click', function (e) {
                e.preventDefault();
                // Check if window already open.
                if (EEA_QUICKBOOKS_OAUTH.oauth_window) {
                    EEA_QUICKBOOKS_OAUTH.oauth_window.focus();
                    return;
                }
                // New window stuff.
                // Need to open it now to prevent browser pop-up blocking.
                var wind_width = screen.width / 2;
                wind_width = wind_width > 350 ? 695 : wind_width;
                wind_width = wind_width < 200 ? 295 : wind_width;
                var wind_height = screen.height / 2;
                wind_height = wind_height > 500 ? 680 : wind_height;
                wind_height = wind_height < 220 ? 380 : wind_height;
                var parameters = [
                    'location=0',
                    'height=' + wind_height,
                    'width=' + wind_width,
                    'top=' + (screen.height - wind_height) / 2,
                    'left=' + (screen.width - wind_width) / 2,
                    'centered=true'
                ];
                EEA_QUICKBOOKS_OAUTH.oauth_window = window.open('', 'oAuthQuickBooksPopupWindow', parameters.join());
                setTimeout(function () {
                    $(EEA_QUICKBOOKS_OAUTH.oauth_window.document.body).html(
                        '<html><head>' +
                        '<title>QuickBooks authentication</title>' +
                        '<link rel="stylesheet" type="text/css" href="' + EEA_QUICKBOOKS_OAUTH_ARGS.espresso_default_styles + '">' +
                        '<link rel="stylesheet" type="text/css" href="' + EEA_QUICKBOOKS_OAUTH_ARGS.wp_stylesheet + '">' +
                        '</head><body>' +
                        '<div id="espresso-ajax-loading" class="ajax-loading-grey">' +
                        '<span class="ee-spinner ee-spin">' +
                        '</div></body></html>'
                    );
                    var win_loader = EEA_QUICKBOOKS_OAUTH.oauth_window.document.getElementById('espresso-ajax-loading');
                    win_loader.style.display = 'inline-block';
                    win_loader.style.top = '40%';
                }, 100);
                // Check in case the popup window was blocked.
                if (!EEA_QUICKBOOKS_OAUTH.oauth_window
                    || typeof EEA_QUICKBOOKS_OAUTH.oauth_window === 'undefined'
                    || EEA_QUICKBOOKS_OAUTH.oauth_window.closed
                    || typeof EEA_QUICKBOOKS_OAUTH.oauth_window.closed === 'undefined'
                ) {
                    EEA_QUICKBOOKS_OAUTH.oauth_window = null;
                    alert(EEA_QUICKBOOKS_OAUTH_ARGS.qb_blocked_popups_notice);
                    console.log(EEA_QUICKBOOKS_OAUTH_ARGS.qb_blocked_popups_notice);
                    return;
                }
                // Try to display the OAuth page.
                var submitted_form = $(this).closest('form');
                var submitted_pm = submitted_form.attr('id').split('_settings_form')[0];
                if (EEA_QUICKBOOKS_OAUTH.validate_form_inputs(submitted_form)) {
                    // Get request token from Intuit.
                    EEA_QUICKBOOKS_OAUTH.oauth_send_request(submitted_form[0], submitted_pm, 'eea_get_qb_request_token');
                }
            });

            // oAuth Reconnect.
            EEA_QUICKBOOKS_OAUTH.oauth_reconnect_btn.on('click', function () {
                var submitted_form = $(this).closest('form');
                var submitted_pm = submitted_form.attr('id').split('_settings_form')[0];
                if (EEA_QUICKBOOKS_OAUTH.validate_form_inputs(submitted_form)) {
                    EEA_QUICKBOOKS_OAUTH.oauth_send_request(submitted_form[0], submitted_pm, 'eea_qb_oauth_reconnect');
                }
            });

            // oAuth Disconnect.
            EEA_QUICKBOOKS_OAUTH.oauth_disconnect_btn.on('click', function () {
                var submitted_form = $(this).closest('form');
                var submitted_pm = submitted_form.attr('id').split('_settings_form')[0];
                if (EEA_QUICKBOOKS_OAUTH.validate_form_inputs(submitted_form)) {
                    EEA_QUICKBOOKS_OAUTH.oauth_send_request(submitted_form[0], submitted_pm, 'eea_qb_oauth_disconnect');
                }
            });
        },


        /**
         * @method set_listener_for_oauth_selector
         */
        set_listener_for_oauth_selector: function() {
            EEA_QUICKBOOKS_OAUTH.oauth_version_selector.on('change', function (event) {
                var submitting_form = $(this).parents('form').eq(0)[0];
                if (submitting_form) {
                    var oauth_version_selector = submitting_form.querySelector('select[name*=oauth_version]');
                    var oauth_version = oauth_version_selector.value;
                    var pm_slug = oauth_version_selector.dataset.pmSlug;
                    if (oauth_version && typeof oauth_version !== 'undefined' && pm_slug) {
                        var pub_key = submitting_form.querySelector('label[id*='+ pm_slug +'][id*=consumer-key]');
                        var secret_key = submitting_form.querySelector('label[id*='+ pm_slug +'][id*=shared-secret]');
                        if (
                            pub_key
                            && secret_key
                            && typeof oauth_version !== 'undefined'
                            && typeof oauth_version !== 'undefined'
                        ) {
                            if (oauth_version === 'v1a') {
                                $(pub_key).html(EEA_QUICKBOOKS_OAUTH_ARGS.v1a_pub_key_title + '*');
                                $(secret_key).html(EEA_QUICKBOOKS_OAUTH_ARGS.v1a_secret_key_title + '*');
                            } else if (oauth_version === 'v2') {
                                $(pub_key).html(EEA_QUICKBOOKS_OAUTH_ARGS.v2_pub_key_title + '*');
                                $(secret_key).html(EEA_QUICKBOOKS_OAUTH_ARGS.v2_secret_key_title + '*');
                            }
                        }
                    }
                } else {
                    console.log(EEA_QUICKBOOKS_OAUTH_ARGS.unknown_container);
                }
            });
        },


        /**
         * @method set_listener_for_form_submit
         */
        set_listener_for_form_submit: function () {
            // If QB settings form was submitted and not yet app connected then auto start the OAuth process.
            var payment_method = EEA_QUICKBOOKS_OAUTH.get_parameter_by_name('payment_method');
            var is_safari = navigator.userAgent.indexOf('Safari') > -1;
            if (payment_method
                && (payment_method.indexOf('quickbooks_onsite') >= 0)
                && EEA_QUICKBOOKS_OAUTH.oauth_x_dv.is(':visible')
                && !is_safari
            ) {
                setTimeout(function () {
                    EEA_QUICKBOOKS_OAUTH.oauth_x_btn.trigger('click');
                }, 100);
            }
        },


        /**
         * @method oauth_send_request
         */
        oauth_send_request: function (submitted_form, submitted_pm, request_action) {
            EEA_QUICKBOOKS_OAUTH.processing_pm = submitted_pm;
            // Try getting the OAuth version.
            var oauth_version_selector = submitted_form.querySelector('select[name*=oauth_version]');
            var oauth_version = oauth_version_selector.dataset.oauthVersion;
            EEA_QUICKBOOKS_OAUTH.oauth_version = oauth_version;
            var req_data = {};
            req_data.action = request_action;
            // If OAuth 2.0 is used - use the appropriate methods.
            if (oauth_version && typeof oauth_version !== 'undefined' && oauth_version === 'v2') {
                req_data.action += '_v2';
            }
            req_data.submitted_pm = submitted_pm;
            $.ajax({
                type: 'POST',
                url: eei18n.ajax_url,
                data: req_data,
                dataType: 'json',

                beforeSend: function () {
                    window.do_before_admin_page_ajax();
                },
                success: function (response) {
                    if (
                        response === null
                        || response['qb_error']
                        || typeof response['qb_success'] === 'undefined'
                        || response['qb_success'] === null
                    ) {
                        var qb_error = 'oAuth request Error.';
                        EEA_QUICKBOOKS_OAUTH.ee_ajax_loader.fadeOut('fast');
                        if (response['qb_error'])
                            qb_error = response['qb_error'];
                        console.log(qb_error);

                        // Reset if Token rejected.
                        if (qb_error.toLowerCase().indexOf('270') >= 0 && qb_error.toLowerCase().indexOf('oauth token rejected') >= 0) {
                            if (
                                confirm('Your OAuth Token was rejected ! \n\n Would you like to reset this connection settings to be able to establish a new connection ?')
                            ) {
                                EEA_QUICKBOOKS_OAUTH.oauth_reset_connection(submitted_pm);
                            }
                        }

                        if (EEA_QUICKBOOKS_OAUTH.oauth_window) {
                            EEA_QUICKBOOKS_OAUTH.oauth_window.document.getElementById('espresso-ajax-loading').style.display = 'none';
                            $(EEA_QUICKBOOKS_OAUTH.oauth_window.document.body).html(qb_error);
                            EEA_QUICKBOOKS_OAUTH.oauth_window = null;
                        }

                        return false;
                    }
                    EEA_QUICKBOOKS_OAUTH.ee_ajax_loader.fadeOut('fast');

                    switch (request_action) {
                        case 'eea_get_qb_request_token': // If all is fine open a new window for OAuth process.
                            EEA_QUICKBOOKS_OAUTH.popup_oauth_window(response['handle_return_url']);
                            break;
                        case 'eea_qb_oauth_reconnect': // Reconnect.
                            console.log('Reconnect OK');
                            EEA_QUICKBOOKS_OAUTH.update_oauth_status();
                            break;
                        case 'eea_qb_oauth_disconnect': // Disconnect.
                            console.log('Disconnect OK');
                            EEA_QUICKBOOKS_OAUTH.update_oauth_status();
                            break;
                    }
                },
                error: function (response, error, description) {
                    var qb_error = EEA_QUICKBOOKS_OAUTH_ARGS.qb_token_error;
                    if (description)
                        qb_error = qb_error + ': ' + description;
                    if (EEA_QUICKBOOKS_OAUTH.oauth_window) {
                        EEA_QUICKBOOKS_OAUTH.oauth_window.document.getElementById('espresso-ajax-loading').style.display = 'none';
                        $(EEA_QUICKBOOKS_OAUTH.oauth_window.document.body).html(qb_error);
                        EEA_QUICKBOOKS_OAUTH.oauth_window = null;
                    }
                    EEA_QUICKBOOKS_OAUTH.ee_ajax_loader.fadeOut('fast');
                    console.log(qb_error);
                }
            });
        },


        /**
         * @method oauth_reset_connection
         */
        oauth_reset_connection: function (submitted_pm) {
            var req_data = {};
            req_data.action = 'eea_qb_oauth_reset_oathsettings';
            req_data.submitted_pm = submitted_pm;
            $.ajax({
                type: 'POST',
                url: eei18n.ajax_url,
                data: req_data,
                dataType: 'json',

                beforeSend: function () {
                    window.do_before_admin_page_ajax();
                },
                success: function (response) {
                    if (
                        response === null
                        || response['qb_error']
                        || typeof response['qb_success'] === 'undefined'
                        || response['qb_success'] === null
                    ) {
                        var qb_error = 'oAuth request Error.';
                        EEA_QUICKBOOKS_OAUTH.ee_ajax_loader.fadeOut('fast');
                        if (response['qb_error'])
                            qb_error = response['qb_error'];
                        console.log(qb_error);
                        return false;
                    }
                    EEA_QUICKBOOKS_OAUTH.ee_ajax_loader.fadeOut('fast');

                    console.log('Reset OK');
                    EEA_QUICKBOOKS_OAUTH.update_oauth_status();
                },
                error: function (response, error, description) {
                    var qb_error = 'Error trying to reset.';
                    if (description)
                        qb_error = qb_error + ': ' + description;
                    EEA_QUICKBOOKS_OAUTH.ee_ajax_loader.fadeOut('fast');
                    console.log(qb_error);
                }
            });
        },


        /**
         * @method popup_oauth_window
         */
        popup_oauth_window: function (handle_return_url) {
            if (EEA_QUICKBOOKS_OAUTH.oauth_window
                && EEA_QUICKBOOKS_OAUTH.oauth_window.location.href.indexOf('about:blank') > -1
            ) {
                EEA_QUICKBOOKS_OAUTH.oauth_window.location = handle_return_url;
                // Update the OAuth status on OAuth window closed.
                EEA_QUICKBOOKS_OAUTH.oauth_window_timer = setInterval(EEA_QUICKBOOKS_OAUTH.check_oauth_child, 500);
            } else if (EEA_QUICKBOOKS_OAUTH.oauth_window) {
                EEA_QUICKBOOKS_OAUTH.oauth_window.focus();
            }
        },


        /**
         * @method check_oauth_child
         */
        check_oauth_child: function () {
            if (EEA_QUICKBOOKS_OAUTH.oauth_window && EEA_QUICKBOOKS_OAUTH.oauth_window.closed) {
                clearInterval(EEA_QUICKBOOKS_OAUTH.oauth_window_timer);
                EEA_QUICKBOOKS_OAUTH.update_oauth_status();
                EEA_QUICKBOOKS_OAUTH.oauth_window = null;
            }
        },


        /**
         * @method update_oauth_status
         */
        update_oauth_status: function () {
            var req_data = {};
            req_data.action = 'eea_qb_update_oauth_status';
            // If OAuth 2.0 is used - use the appropriate methods.
            if (EEA_QUICKBOOKS_OAUTH.oauth_version === 'v2') {
                req_data.action += '_v2';
            }
            req_data.submitted_pm = EEA_QUICKBOOKS_OAUTH.processing_pm;
            $.ajax({
                type: 'POST',
                url: eei18n.ajax_url,
                data: req_data,
                dataType: 'json',

                beforeSend: function () {
                    window.do_before_admin_page_ajax();
                },
                success: function (response) {
                    EEA_QUICKBOOKS_OAUTH.ee_ajax_loader.fadeOut('fast');
                    if (response['oauthed'] === true) {
                        EEA_QUICKBOOKS_OAUTH.oauth_ok_dv.show();
                        EEA_QUICKBOOKS_OAUTH.oauth_x_dv.hide();
                    } else {
                        EEA_QUICKBOOKS_OAUTH.oauth_ok_dv.hide();
                        EEA_QUICKBOOKS_OAUTH.oauth_x_dv.show();
                    }
                }
            });
        },


        /**
         * @method get_parameter_by_name
         */
        get_parameter_by_name: function (name, url) {
            if (!url)
                url = window.location.href;
            url = url.toLowerCase();
            name = name.replace(/[\[\]]/g, "\\$&").toLowerCase();
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"), results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        }
    };


    EEA_QUICKBOOKS_OAUTH.initialize();
});