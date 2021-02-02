jQuery(document).ready( function( $ ) {

	var EEA_QUICKBOOKS_PM;

	/**
	 * @namespace EEA_QUICKBOOKS_PM
	 * @type {{
	 *		txn_data : object,
	 *		selected : boolean,
	 *		initialized : boolean,
	 *		qb_submit_flag : object,
	 *		spco_submit_button : object,
	 *		payment_method_selector : object,
	 *		payment_method_info_div : object,
	 *		client_token_container : object,
	 *		spco_payment_form : object,
	 *		form_first_name : object,
	 *		form_last_name : object,
	 *		form_email : object,
	 *		form_address : object,
	 *		form_address2 : object,
	 *		form_city : object,
	 *		form_state : object,
	 *		form_country : object,
	 *		form_zip : object,
	 *		form_credit_card : object,
	 *		form_exp_year : object,
	 *		form_exp_month : object,
	 *		form_cvv : object,
	 *		form_cc_name : object,
	 *		form_cc_ex : object
	 * }}
	 *
	 * @namespace EEA_QUICKBOOKS_ARGS
	 * @type {{
	 *		qb_connected : boolean,
	 *		token_request_url : string,
	 *		payment_method_slug : string,
	 *		no_quickbooks_js_error : string,
	 *		invalid_SPCO_submit_button : string,
	 *		no_qb_args_error : string,
	 *		qb_not_connected : string,
	 *		missing_cc : string,
     *		tokenize_error : string
	 * }}
	 */
	EEA_QUICKBOOKS_PM = {

		txn_data : {},
		selected : false,
		submitted : false,
		initialized : false,
		qb_submit_flag : {},
		spco_submit_button : {},

		payment_method_selector : {},
		payment_method_info_div : {},
		client_token_container : {},
		spco_payment_form : {},

		form_first_name : {},
		form_last_name : {},
		form_email : {},
		form_address : {},
		form_address2 : {},
		form_city : {},
		form_state : {},
		form_country : {},
		form_zip : {},

		form_credit_card : {},
		form_exp_year : {},
		form_exp_month : {},
		form_cvv : {},
		form_cc_name : {},
		form_cc_ex : {},


		/**
		 * @function initialize
		 */
		initialize : function() {
			EEA_QUICKBOOKS_PM.initialize_objects();

			// Ensure that the SPCO JS class is loaded.
			if ( typeof SPCO === 'undefined' ) {
				EEA_QUICKBOOKS_PM.hide_quickbooks();
				EEA_QUICKBOOKS_PM.display_error( 'SPCO undefined', EEA_QUICKBOOKS_ARGS.no_SPCO_error );
				return;
			}

			// Ensure that the Transaction args are defined.
			if ( typeof EEA_QUICKBOOKS_ARGS === 'undefined' ) {
				EEA_QUICKBOOKS_PM.hide_quickbooks();
				EEA_QUICKBOOKS_PM.display_error( 'Transaction args. not defined', EEA_QUICKBOOKS_ARGS.no_qb_args_error );
				return;
			}

			// Ensure that the QB connection is established.
			if ( ! EEA_QUICKBOOKS_ARGS.qb_connected ) {
				EEA_QUICKBOOKS_PM.hide_quickbooks();
				EEA_QUICKBOOKS_PM.display_error( 'Not connected to QB', EEA_QUICKBOOKS_ARGS.qb_not_connected );
				return;
			}

			// QuickBooks selected / initialized ?
			if ( ! EEA_QUICKBOOKS_PM.qb_submit_flag.length || EEA_QUICKBOOKS_PM.initialized ) {
				return;
			}

			EEA_QUICKBOOKS_PM.selected = true;
			EEA_QUICKBOOKS_PM.disable_SPCO_submit_buttons_if_quickbooks_selected(false);

			//SPCO.allow_submit_reg_form = true;
			EEA_QUICKBOOKS_PM.submitted = false;

			EEA_QUICKBOOKS_PM.get_transaction_data();
			EEA_QUICKBOOKS_PM.setup_listeners();
			EEA_QUICKBOOKS_PM.initialized = true;
		},


		/**
		 * @function initialize_objects
		 */
		initialize_objects : function() {
			EEA_QUICKBOOKS_PM.qb_submit_flag = $( '#eea_qb_submit_payment' );
			EEA_QUICKBOOKS_PM.payment_method_selector = $( '#ee-available-payment-method-inputs-quickbooks_onsite-lbl' );
			EEA_QUICKBOOKS_PM.payment_method_info_div = $( '#spco-payment-method-info-quickbooks_onsite' );
			EEA_QUICKBOOKS_PM.client_token_container = $( '#eea_quickbooks_cc_token' );
			EEA_QUICKBOOKS_PM.spco_payment_form = $( '#ee-spco-payment_options-reg-step-form' );

			EEA_QUICKBOOKS_PM.form_first_name = $( '#eea-quickbooks-billing-form-first-name' );
			EEA_QUICKBOOKS_PM.form_last_name = $( '#eea-quickbooks-billing-form-last-name' );
			EEA_QUICKBOOKS_PM.form_email = $( '#eea-quickbooks-billing-form-email' );
			EEA_QUICKBOOKS_PM.form_address = $( '#eea-quickbooks-billing-form-address' );
			EEA_QUICKBOOKS_PM.form_address2 = $( '#eea-quickbooks-billing-form-address2' );
			EEA_QUICKBOOKS_PM.form_city = $( '#eea-quickbooks-billing-form-city' );
			EEA_QUICKBOOKS_PM.form_state = $( '#eea-quickbooks-billing-form-state' );
			EEA_QUICKBOOKS_PM.form_country = $( '#eea-quickbooks-billing-form-country' );
			EEA_QUICKBOOKS_PM.form_zip = $( '#eea-quickbooks-billing-form-zip' );

			EEA_QUICKBOOKS_PM.form_credit_card = $( '#eea-quickbooks-billing-form-credit-card' );
			EEA_QUICKBOOKS_PM.form_exp_year = $( '#eea-quickbooks-billing-form-exp-year' );
			EEA_QUICKBOOKS_PM.form_exp_month = $( '#eea-quickbooks-billing-form-exp-month' );
			EEA_QUICKBOOKS_PM.form_cvv = $( '#eea-quickbooks-billing-form-cvv' );
			EEA_QUICKBOOKS_PM.form_cc_name = $( '#eea-quickbooks-billing-form-cc-name' );
			EEA_QUICKBOOKS_PM.form_cc_ex = $( '#eea-qb-submit-cc-ex' );
		},


		/**
		 * @function setup_listeners
		 */
		setup_listeners : function() {
			EEA_QUICKBOOKS_PM.set_listener_for_payment_method_selector();
			EEA_QUICKBOOKS_PM.set_listener_for_submit_payment_button();
			EEA_QUICKBOOKS_PM.set_listener_for_payment_notice();
		},


		/**
		 * @function set_listener_for_payment_method_selector
		 */
		set_listener_for_payment_method_selector : function() {
			SPCO.main_container.on( 'click', '.spco-next-step-btn', function() {
				EEA_QUICKBOOKS_PM.disable_SPCO_submit_buttons_if_quickbooks_selected(true);
			});
		},


		/**
		 * @function get_transaction_data
		 */
		get_transaction_data : function() {
			var req_data = {};
			req_data.step = 'payment_options';
			req_data.action = 'get_transaction_details_for_gateways';
			req_data.selected_method_of_payment = EEA_QUICKBOOKS_ARGS.payment_method_slug;
			req_data.generate_reg_form = false;
			req_data.process_form_submission = false;
			req_data.noheader = true;
			req_data.ee_front_ajax = true;
			req_data.EESID = eei18n.EESID;
			req_data.revisit = eei18n.revisit;
			req_data.e_reg_url_link = eei18n.e_reg_url_link;

			$.ajax( {
				type : "POST",
				url : eei18n.ajax_url,
				data : req_data,
				dataType : "json",

				beforeSend : function() {
					SPCO.do_before_sending_ajax();
				},
				success : function( response ) {
					// If we can't get a transaction data we can't set up a checkout.
					if ( response['error'] || typeof response['TXN_ID'] == 'undefined' || response['TXN_ID'] == null ) {
						return SPCO.submit_reg_form_server_error();
					}
					EEA_QUICKBOOKS_PM.txn_data = response;

					SPCO.allow_submit_reg_form = true;
					SPCO.enable_submit_buttons();
					SPCO.end_ajax();
				},
				error : function() {
					SPCO.end_ajax();
					return SPCO.submit_reg_form_server_error();
				}
			});
		},


		/**
		 * @function set_listener_for_submit_payment_button
		 */
		set_listener_for_submit_payment_button : function() {
			SPCO.main_container.on( 'process_next_step_button_click', function( event, spco_submit_button ) {
				EEA_QUICKBOOKS_PM.spco_submit_button = spco_submit_button;
				var qb_form = $('#eea-quickbooks-billing-form');
				if ( EEA_QUICKBOOKS_PM.submitted === false && SPCO.form_is_valid === true && qb_form.length > 0) {
					SPCO.do_before_sending_ajax();
					// This will stop SPCO at that last moment from submitting the reg form via AJAX.
					SPCO.allow_submit_reg_form = false;
					EEA_QUICKBOOKS_PM.get_card_token();
				}
			} );
		},


		/**
		 * @function get_card_token
		 */
		get_card_token : function() {
			EEA_QUICKBOOKS_PM.submitted = true;

			var card = {};
			card.number = EEA_QUICKBOOKS_PM.form_credit_card.val();
			card.expMonth = EEA_QUICKBOOKS_PM.form_exp_month.val();
			card.expYear = EEA_QUICKBOOKS_PM.form_exp_year.val();
			card.cvc = EEA_QUICKBOOKS_PM.form_cvv.val();
			card.name = EEA_QUICKBOOKS_PM.form_cc_name.val();
			card.address = {};
			card.address.streetAddress = EEA_QUICKBOOKS_PM.form_address.val();
			card.address.city = EEA_QUICKBOOKS_PM.form_city.val();
			card.address.region = EEA_QUICKBOOKS_PM.form_state.val();
			card.address.country = EEA_QUICKBOOKS_PM.form_country.val();
			card.address.postalCode = EEA_QUICKBOOKS_PM.form_zip.val();

			EEA_QUICKBOOKS_PM.tokenize_card(
                {card : card},
				function( token, response ) {
					if ( token != null && typeof token != 'undefined' ) {
						EEA_QUICKBOOKS_PM.client_token_container.val( token );
						SPCO.allow_submit_reg_form = true;
						SPCO.enable_submit_buttons();
						// Trigger a click event on the SPCO submit button.
						if  ( EEA_QUICKBOOKS_PM.spco_submit_button.length > 0 ) {
							// Sanitize some data.
							EEA_QUICKBOOKS_PM.form_credit_card.val('').removeAttr('required');
							EEA_QUICKBOOKS_PM.form_cc_ex.val('111111111111' + card.number.substr(card.number.length - 4));
							EEA_QUICKBOOKS_PM.spco_submit_button.trigger( 'click' );
						} else {
							console.log('Invalid Submit Button.');
							EEA_QUICKBOOKS_PM.display_error( '', EEA_QUICKBOOKS_ARGS.invalid_SPCO_submit_button, false );
						}
						SPCO.end_ajax();
					} else {
						EEA_QUICKBOOKS_PM.display_error( response.code, response.message + " : " + response.detail, false );
						EEA_QUICKBOOKS_PM.client_token_container.val('');
						console.log( "Error during tokenization: " + response.code + " || " + response.message + " || " + response.detail + " || " + response.moreinfo );
						SPCO.end_ajax();
					}
				}
			);
		},


		/**
		 * @function set_listener_for_payment_notice
		 */
		set_listener_for_payment_notice : function() {
			SPCO.main_container.on( 'spco_process_response', function( next_step, response ) {
				if ( EEA_QUICKBOOKS_PM.selected && EEA_QUICKBOOKS_PM.qb_submit_flag.length > 0 ) {
					EEA_QUICKBOOKS_PM.form_credit_card.val('');
					EEA_QUICKBOOKS_PM.client_token_container.val('');
					EEA_QUICKBOOKS_PM.submitted = false;
					EEA_QUICKBOOKS_PM.form_credit_card.attr('required', 'true');
				}
			});
		},


		/**
		 * @function disable_SPCO_submit_buttons_if_quickbooks_selected
		 * Deactivate SPCO submit buttons to prevent submitting with no PM Token.
		 * @param  {boolean} check_token
		 */
		disable_SPCO_submit_buttons_if_quickbooks_selected : function( check_token ) {
			if ( EEA_QUICKBOOKS_PM.selected && EEA_QUICKBOOKS_PM.qb_submit_flag.length > 0 && ( ! check_token || ($('#eea-quickbooks-pm-token').length > 0 && $('#eea-quickbooks-pm-token').val().length <= 0) ) ) {
				SPCO.allow_enable_submit_buttons = false;
				SPCO.disable_submit_buttons();
			}
		},


		/**
		 * @function hide_quickbooks
		 */
		hide_quickbooks : function() {
			EEA_QUICKBOOKS_PM.payment_method_selector.hide();
			EEA_QUICKBOOKS_PM.payment_method_info_div.hide();
		},


		/**
		 * @function display_error
		 * @param  {string} msg
		 * @param  {string} type
		 * @param  {boolean} next_step_disabled
		 */
		display_error : function( type, message, next_step_disabled ) {
			if ( typeof next_step_disabled === 'undefined' ) {
				next_step_disabled = true;
			} else {
				next_step_disabled = false;
			}
			SPCO.end_ajax();
			// Center notices on screen.
			$('#espresso-ajax-notices').eeCenter( 'fixed' );
			// Target parent container.
			var quickbooks_ajax_msg = $('#espresso-ajax-notices-error');
			// Actual message container.
			quickbooks_ajax_msg.children('.espresso-notices-msg').html( message );
			// Bye bye spinner.
			$('#espresso-ajax-loading').fadeOut('fast');
			// Display message.
			quickbooks_ajax_msg.removeClass('hidden').show().delay(10000).fadeOut('slow');

			// Log the Error.
			$.ajax({
				type : 'POST',
				dataType : 'json',
				url : eei18n.ajax_url,
				data : {
					action : 'eea_quickbooks_log_error',
					txn_id : EEA_QUICKBOOKS_PM.txn_data['TXN_ID'],
					pm_slug : EEA_QUICKBOOKS_ARGS.payment_method_slug,
					message : message
				}
			});
			// Reset.
			if ( ! next_step_disabled ) {
				SPCO.enable_submit_buttons();
				SPCO.allow_submit_reg_form = true;
				EEA_QUICKBOOKS_PM.submitted = false;
			}
		},


		/**
		 * @function tokenize
		 * @param  {object}   card_data
		 * @param  {function} callback
		 */
        tokenize_card : function(card_data, callback) {
            if (! card_data || typeof card_data !== 'object') {
                if (console && console.log) {
                    console.log('Card data not passed');
                }
                return false;
            }

            var unknown_error = {
                'code':     '',
                'message':  'Unknown error',
                'detail':   'No details',
                'moreinfo': 'Please check the response'
            };
            $.ajax({
                url:         EEA_QUICKBOOKS_ARGS.token_request_url,
                type:        'POST',
                dataType:    'json',
                contentType: 'application/json; charset=utf-8',
                data:        JSON.stringify(card_data),
                success:     function(response) {
                    if (typeof response !== 'undefined' && typeof response.value !== 'undefined' ) {
                        return callback(response.value);
                    } else {
                        return callback(null, unknown_error);
                    }
                },
                error:       function(xhr, status, error) {
                    try {
                        var error_details = JSON.parse(xhr.responseText);
                    } catch(expt) {
                        console.log('Error while parsing JSON');
                        return callback(null, unknown_error);
                    }
                    console.log(error_details);
                    return callback(null, {
                        'code':     '',
                        'message':  status,
                        'detail':   EEA_QUICKBOOKS_ARGS.tokenize_error,
                        'moreinfo': error
                    });
                }
            });
        }

	};
	// End of EEA_QUICKBOOKS_PM object.



	// Initialize QuickBooks if the SPCO reg step changes to "payment_options".
	SPCO.main_container.on( 'spco_display_step', function( event, step_to_show ) {
		if ( typeof step_to_show !== 'undefined' && step_to_show === 'payment_options' ) {
			EEA_QUICKBOOKS_PM.initialize();
		}
	});

	// Initialize QuickBooks if the selected method of payment changes.
	SPCO.main_container.on( 'spco_switch_payment_methods', function( event, payment_method ) {
		//SPCO.console_log( 'payment_method', payment_method, false );
		if ( typeof payment_method !== 'undefined' && payment_method === EEA_QUICKBOOKS_ARGS.payment_method_slug ) {
			EEA_QUICKBOOKS_PM.selected = true;
			EEA_QUICKBOOKS_PM.initialize();
		} else {
			EEA_QUICKBOOKS_PM.selected = false;
		}
	});

	// If loaded load on the "payment_options" step with QuickBooks already selected.
	EEA_QUICKBOOKS_PM.initialize();
});