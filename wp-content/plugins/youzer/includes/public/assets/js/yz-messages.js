( function( $ ) {

	'use strict';

	$( document ).ready( function() {

		/**
		 * Load Emojis JS.
		 */
    	$( document ).on( 'click', '.yz-load-emojis', function() {
        	var form = $( this ).closest( 'form' );
	        $( this ).find( 'i' ).attr(  'class', 'fas fa-spin fa-spinner' );
        	$( this ).addClass( 'loading' );
	        $( '<link/>', { rel: 'stylesheet', href: Youzer.assets + 'css/emojionearea.min.css' } ).appendTo( 'head' );
	        $( '<script/>', { rel: 'text/javascript', src: Youzer.assets + 'js/emojionearea.min.js' } ).appendTo( 'head' );
	        $( '<script/>', { rel: 'text/javascript', src: Youzer.assets + 'js/yz-emoji.min.js' } ).appendTo( 'head' );
	    });

	});


})( jQuery );