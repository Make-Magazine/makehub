/*
* Ultimate Membership Pro - Checkout Page
*/
"use strict";
var IhcCheckout = {

	init: function(args){
			var obj = this;

			jQuery(window).on('load', function(){

					// select payment - logos
          jQuery( '.ihc-js-select-payment' ).on( 'click', function( e, html ){
              var type = jQuery(e.target).attr( 'data-type' );
              var accessType = jQuery( '[name="subscription_access_type"]' ).val();
              obj.paymentSelect( type );
              obj.paymentUpdate( type, accessType );
          });

					// select payment - select
					jQuery( '.ihc-js-select-payment-service-select' ).on( 'change', function( e, html ){
							var type = jQuery(e.target).val();
							var accessType = jQuery( '[name="subscription_access_type"]' ).val();
							obj.paymentSelect( type );
							obj.paymentUpdate( type, accessType );
					});

					// select payment - radio
					jQuery( '.ihc-js-select-payment-service-radio' ).on( 'click', function( e, html ){
							var type = jQuery(e.target).val();
							var accessType = jQuery( '[name="subscription_access_type"]' ).val();
							obj.paymentSelect( type );
							obj.paymentUpdate( type, accessType );
					});

      });

	},

  paymentSelect: function( type ){
      jQuery('.ihc-payment-icon').removeClass('ihc-payment-select-img-selected');
      jQuery('#ihc_payment_icon_' + type ).addClass('ihc-payment-select-img-selected');
  },

  paymentUpdate: function( type, accessType ){
      jQuery('[name=ihc_payment_gateway]').val( type );
      jQuery('#ihc_authorize_payment_fields').fadeOut(200);
      jQuery('#ihc_braintree_payment_fields').fadeOut(200);
      switch ( type ){
        case 'stripe':
	      	jQuery('#ihc_submit_bttn').off('click');
          jQuery('#ihc_submit_bttn').on('click', function(e){
            e.preventDefault();
            var p = jQuery("#iumpfinalglobalp").val();
            if ((jQuery("#stripeToken").val() && jQuery("#stripeEmail").val()) || p==0){
              jQuery(".ihc-form-create-edit").submit();
              return true;
            }
            window.ihcStripeMultiply = parseInt( window.ihcStripeMultiply );
            p = p * window.ihcStripeMultiply;
            if ( window.ihcStripeMultiply== 100 && p<50 ){
              p = 50;
            }
            iump_stripe.open({
                    name: jQuery('#iump_site_name').val(),
                    description: jQuery('#iumpfinalglobal_ll').val(),
                    amount: p,
                    currency: jQuery('#iumpfinalglobalc').val(),
            });
          });
        break;
        case 'authorize':
          if ( accessType == 'regular_period' ){
						jQuery( '#ihc_authorize_payment_fields' ).fadeIn( 200 );
          }
        break;
        case 'braintree':
					jQuery( '#ihc_braintree_payment_fields' ).fadeId( 200 );
        break;
      }
  }

};
IhcCheckout.init();
