( function( $ ) {

	$( function() {

		$( '.icp' ).iconpicker().on( 'iconpickerUpdated', function() {
			$( this ).trigger( 'change' );
		} );

		var sections = [
			'lds_visual_customizer_colors',
			'lds_visual_customizer_content_list',
			'lds_visual_customizer_fonts',
			'lds_icons',
			'lds_visual_customizer_buttons',
			'lds_visual_customizer_widgets',
			'lds_visual_customizer_quiz'
		];

		var ldvc_button = '<li class="customize-control customize-control-color"><button class="lds-vc-clear">Reset Settings</button></li>';

		sections.forEach(function(element) {
			$('#sub-accordion-section-' + element ).find('.customize-section-description-container').after( ldvc_button );
		});

	} );

} )( jQuery );
