/*
* Ultimate Membership Pro - Stripe Connect utilities
*/
"use strict";
var IhcStripeConnect = {
    formId                      : 'createuser',
    stripeObject                : null,
    card                        : null,
    elements                    : null,

    init                        : function( args ){

        var object = this;
        if ( window.ihcCheckoutIsRegister === '0' ){
            object.formId = 'checkout';
        }


        // saved cards
        if ( jQuery('[name=ihc_stripe_connect_payment_methods]').length > 0  ){
            jQuery( '[name=ihc_stripe_connect_payment_methods]' ).on( 'click', function(){
                jQuery( '.ihc-stripe-connect-saved-card-wrapper' ).removeClass( 'ihc-stripe-connect-saved-card-wrapper-selected' );
                if ( this.value === 'new' ){
                    // show stripe new card field
                    jQuery( '.ihc-js-stripe-connect-wrapp' ).removeClass( 'ihc-display-none' );
                } else {
                    // hide stripe new card field
                    jQuery( '.ihc-js-stripe-connect-wrapp' ).addClass( 'ihc-display-none' );
                }
                jQuery( this ).parent().addClass( 'ihc-stripe-connect-saved-card-wrapper-selected' );
            });
        }

        if ( jQuery('.ihc-js-connect-do-setup-intent').length == 0 && jQuery('.ihc-js-connect-do-payment-intent').length == 0 ){
            object.removePreventSubmit();
            return;
        }

        ihcAddAction( 'checkeout-payment-type-radio-change', function(){
        		var type = jQuery('[name=ihc_payment_gateway]').val();
            if ( type !== 'stripe_connect' ){
                object.removePreventSubmit();
            }
        }, 0 );

        ihcAddAction( 'checkeout-payment-type-select-change', function(){
            var type = jQuery('[name=ihc_payment_gateway]').val();
            if ( type !== 'stripe_connect' ){
                object.removePreventSubmit();
            }
        }, 0 );

        ihcAddAction( 'checkeout-payment-type-logos-change', function(){
            var type = jQuery('[name=ihc_payment_gateway]').val();
            if ( type !== 'stripe_connect' ){
                object.removePreventSubmit();
            }
        }, 0 );

        if ( jQuery( '[name=payment_selected]' ).val() === 'stripe_connect' ){
            self.IhcStripeConnect.initStripeObject();
            object.activatePreventSubmit();
        }

        // hook into indeed checkout object
        /*
        ihcAddAction( 'checkout-loaded', function(){
        		self.IhcStripeConnect.initStripeObject();
        }, 0 );
        */
        self.IhcStripeConnect.initStripeObject();

    },

    activatePreventSubmit           : function(){
        /// prevent form submit when stripe connect is selected, this will perform an extra ajax check to see if the payment fields are completed

        var theTarget = document.getElementById( self.IhcStripeConnect.formId );

        if ( typeof theTarget === 'undefined' || theTarget === null ){
            return;
        }
        theTarget.addEventListener( 'submit', self.IhcStripeConnect.preventFormSubmit, true );

    },

    removePreventSubmit              : function(){
        var theTarget = document.getElementById( self.IhcStripeConnect.formId );
        if ( typeof theTarget === 'undefined' || theTarget === null ){
            return;
        }
        theTarget.removeEventListener( 'submit', self.IhcStripeConnect.preventFormSubmit, true );
    },

    preventFormSubmit                 : function( evt ){
        evt.preventDefault();
        evt.stopPropagation();
        // here check the card and stuff

        return false;
    },

    initStripeObject                  : function(){
      // initiate stripe
      self.IhcStripeConnect.stripeObject = Stripe( window.ihcStripeConnectPublicKey, { stripeAccount: window.ihcStripeConnectAcctNumber, locale: window.ihcStripeConnectLang } );
      var clientSecret = jQuery('#ihc-js-stripe-connect-card-element').attr('data-client');

      self.IhcStripeConnect.elements = self.IhcStripeConnect.stripeObject.elements( );

      self.IhcStripeConnect.card = self.IhcStripeConnect.elements.create("card", {
        style: {
        base: {
          lineHeight: '50px',
          color: '#444444',
          fontWeight: '500',
          fontFamily: 'Montserrat, Arial, Helvetica',
          fontSize: '15px',
          fontSmoothing: 'antialiased',
          ':-webkit-autofill': {
            backgroundColor: '#fce883',
          },
          '::placeholder': {
            color: '#aaaaaa',
          },
        },
        invalid: {
          iconColor: '#dd3559',
          color: '#dd3559',
        },
      },
        hidePostalCode: true
      });
      self.IhcStripeConnect.card.mount( "#ihc-js-stripe-connect-card-element" );

    },

    preventFormSubmit             : function( evt ){
        evt.preventDefault();
        evt.stopPropagation();
        self.IhcStripeConnect.check();
        return false;
    },

    check                         : function(){
      if ( jQuery('[name=ihc_stripe_connect_payment_methods]').length > 0 && jQuery('input[name=ihc_stripe_connect_payment_methods]:checked').val() !== 'new'
            && jQuery('[name=ihc_stripe_connect_payment_methods]').val() !== '' ){
         // payment with old card
         var theTarget = document.getElementById( self.IhcStripeConnect.formId );
         self.IhcStripeConnect.removePreventSubmit();
         self.IhcStripeConnect.activateSpinner();
         theTarget.submit();
         return false; /// very important to stop the process here
      } else if ( jQuery('.ihc-js-connect-do-payment-intent').length > 0 ){
          // new card - payment intent
          var fullName = jQuery( '[name=ihc_stripe_connect_full_name]' ).val();
          self.IhcStripeConnect.stripeObject.createPaymentMethod({
            type              : 'card',
            card              : self.IhcStripeConnect.card,
            billing_details   : {
                                  name      : fullName,
            },
          }).then(function(result) {
              if ( jQuery( '#ihc_js_stripe_connect_card_error_message').length > 0 ){
                  jQuery( '#ihc_js_stripe_connect_card_error_message' ).remove();
              }
              if ( typeof result.error !== 'undefined' ){
                  jQuery( '#ihc_stripe_connect_payment_fields' ).append( '<div class="ihc-wrapp-the-errors" id="ihc_js_stripe_connect_card_error_message">' + result.error.message + '</div>' );
                  return;
              }

              if ( typeof result.paymentMethod.id !== 'undefined' ){
                  // send ajax to get the payment intent or setup intent
                  jQuery.ajax({
                       type 		: "post",
                       url 		: decodeURI(window.ihc_site_url) + '/wp-admin/admin-ajax.php',
                       data 		: {
                                  action							: "ihc_ajax_stripe_connect_generate_payment_intent",
                                  session             : jQuery( '.ihc-js-checkout-session' ).attr( 'data-value'),
                                  payment_method      : result.paymentMethod.id,

                       },
                       success	: function( responseJson ) {
                          var response = JSON.parse( responseJson );
                          if ( response.status === 0 ){
                              return false;
                          }
                          var fullName = jQuery( '[name=ihc_stripe_connect_full_name]' ).val();
                          jQuery( '[name=stripe_payment_intent]' ).val( response.payment_intent_id );
                          self.IhcStripeConnect.stripeObject.confirmCardPayment( response.client_secret, {
                                payment_method: {
                                    card: self.IhcStripeConnect.card,
                                    billing_details: {
                                      name: fullName
                                    }
                                }
                          }).then(function(result) {
                              if ( typeof result.error !== 'undefined' ){
                                  return false;
                              }
                              self.IhcStripeConnect.activateSpinner();
                              var theTarget = document.getElementById( self.IhcStripeConnect.formId );
                              self.IhcStripeConnect.removePreventSubmit();
                              theTarget.submit();
                          });
                       }
                  });
              }
          });
          return false;
      } else if ( jQuery('.ihc-js-connect-do-setup-intent').length > 0 ){
          // new card - setup intent
          var fullName = jQuery( '[name=ihc_stripe_connect_full_name]' ).val();
          self.IhcStripeConnect.stripeObject.createPaymentMethod({
            type              : 'card',
            card              : self.IhcStripeConnect.card,
            billing_details   : {
                                  name      : fullName,
            },
          }).then(function(result) {
              if ( jQuery( '#ihc_js_stripe_connect_card_error_message').length > 0 ){
                  jQuery( '#ihc_js_stripe_connect_card_error_message' ).remove();
              }
              if ( typeof result.error !== 'undefined' ){
                  jQuery( '#ihc_stripe_connect_payment_fields' ).append( '<div class="ihc-wrapp-the-errors" id="ihc_js_stripe_connect_card_error_message">' + result.error.message + '</div>' );
                  return;
              }

              if ( typeof result.paymentMethod.id !== 'undefined' ){
                  // send ajax to get the payment intent or setup intent
                  jQuery.ajax({
                       type 		: "post",
                       url 		: decodeURI(window.ihc_site_url) + '/wp-admin/admin-ajax.php',
                       data 		: {
                                  action							: "ihc_ajax_stripe_connect_generate_setup_intent",
                                  session             : jQuery( '.ihc-js-checkout-session' ).attr( 'data-value'),
                                  payment_method      : result.paymentMethod.id,

                       },
                       success	: function( responseJson ) {
                          var response = JSON.parse( responseJson );
                          if ( response.status === 0 ){
                              return false;
                          }
                          var fullName = jQuery( '[name=ihc_stripe_connect_full_name]' ).val();
                          jQuery( '[name=stripe_setup_intent]' ).val( response.setup_intent_id );
                          self.IhcStripeConnect.stripeObject.confirmCardSetup( response.client_secret, {
                                payment_method: {
                                    card: self.IhcStripeConnect.card,
                                    billing_details: {
                                      name: fullName
                                    }
                                }
                          }).then(function(result) {
                              if ( typeof result.error !== 'undefined' ){
                                  return false;
                              }
                              self.IhcStripeConnect.activateSpinner();
                              var theTarget = document.getElementById( self.IhcStripeConnect.formId );
                              self.IhcStripeConnect.removePreventSubmit();
                              theTarget.submit();
                          });
                       }
                  });
              }
          });
          return false;
      }

    },

    activateSpinner: function(){
        if ( jQuery( '.ihc-loading-purchase-button' ).length > 0 ){
          jQuery( '.ihc-complete-purchase-button' ).addClass( 'ihc-display-none' );
          jQuery('.ihc-loading-purchase-button').removeClass('ihc-display-none').addClass('ihc-display-block');
        }
    }

}

jQuery( document ).on( 'ready', function(){
		window.ihcStripeObject = IhcStripeConnect.init( [] );

    ihcAddAction( 'checkout-loaded', function(){
          window.ihcStripeObject = null;
          window.ihcStripeObject = IhcStripeConnect.init( [] );
    }, 0 );

});
