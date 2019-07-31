( function( $ ) {

	'use strict';

	$( document ).ready( function() {

		/**
		 * Woocommerce Add to cart with ajax.
		 */
		$( document ).on( 'click', '.yz-activate-addon-key', function (e) {

		    e.preventDefault();
		    
		    var button = $( this );

		    if ( button.hasClass( 'loading' ) ) {
	        console.log ( 'nanana' );
		    	return;
		    }

	        var parent = button.closest( '.yz-addon-license-area' ),
	        	title = button.text(),
	        	data = {
		        action: 'yz_save_addon_key_license',
		        license: button.prev( '.yz-addon-license-key' ).val(),
		        nounce : button.data( 'nounce' ),
		        // product_id : button.data( 'product-id' ),
		        product_name : button.data( 'product-name' ),
		        name: button.prev( 'input' ).attr( 'name' )
		    };
		    
		    $.ajax({
		        type: 'post',
		        url: Yz_Automatic_Updates.ajax_url,
		        data: data,
		        beforeSend: function (response) {
		        	button.addClass( 'loading' );
		            button.html( '<i class="fas fa-spin fa-spinner"></i>' );
		            parent.find( '.yz-addon-license-msg' ).remove();
		        },
		        complete: function (response) {
		            button.html( title );
		        	button.removeClass( 'loading' );
		        },
		        success: function (response) {
	            	
	            	// Get Response Data.
	            	var response = $.parseJSON( response );
	            	
		            if ( response.error ) {
		            	button.parent().append( '<div class="yz-addon-license-msg yz-addon-error-msg">' +  response.error + '</div>' );
		            } else {
		            	button.parent().hide( 100, function() {
		            		button.closest( '.yz-addon-license-area' ).append( '<div class="yz-addon-license-msg yz-addon-success-msg">' +  response.success + '</div>' );
		            		$( this ).remove();
		            	});
		            }
		        }
		    });

		    return false;
		});

	});

})( jQuery );