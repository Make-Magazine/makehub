jQuery( document ).ready( function( $ ) {

	var $table = $( "form#entry_form" );

	$table
		.on( 'change', '.toggle-all-revisions', function () {
			if( $( this ).is(":checked") ) {
				$( ".diff-deletedline input[type='radio']", $table ).trigger('click');
			} else {
				$( ".diff-addedline input[type='radio']", $table ).trigger('click');
			}
		})
		.on( "change", function ( e ) {

			var button_text = gvRevisions.restore['plural'];

			if ( 1 === $( ".diff-deletedline .revision_checkbox:checked", $table ).length ) {
				button_text = gvRevisions.restore['singular'];
			}

			// Show and hide the submit button based on whether any changes are selected
			if( $( ".diff-deletedline input[type='radio']", $table ).filter(':checked').length > 0 ) {
				$( '.button-primary', $table ).prop( 'value', button_text ).fadeIn( 150 );
			} else {
				$( '.button-primary', $table ).fadeOut( 150, function () {
					$( this ).prop( 'value', button_text );
				} );
			}

			// If all the revision radios are checked, check the "all" checkbox
			$( '.toggle-all-revisions').prop( 'checked', ( 0 === $( ".diff-deletedline input[type='radio']", $table ).not(':checked').length ) );
		})
		.on( 'submit', function( e ) {
			return confirm( gvRevisions.confirm );
		})
		.on ('click', 'td', function( e ) {
			if ( $( e.target ).is( 'td' ) ) {
				$( 'input[type="radio"]', e.target ).click();
			}
		})
		.on ('click', 'input[type="radio"]', function() {

			$( this ).prop( 'checked', true );

			// Only check the current <td> and its siblings
			$( this ).parents( 'tr' ).find( 'input[type="radio"]').each( function () {
				$( this ).parents('td').toggleClass( 'diff-enabled', this.checked );
			});
		});

	$( '.toggle-all-revisions', $table ).first().trigger( 'change' );
});