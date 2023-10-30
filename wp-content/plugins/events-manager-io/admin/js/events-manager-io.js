jQuery(document).ready( function($){
	//source file and frequency
	$('#emio_source_type').on('change', function(){
		var attribute_val = $(this).val().replace(/[^a-zA-Z0-9_]/g,'-');
		var source_type = $('.emio-source-type-' + attribute_val);
		if( $(this).val().indexOf('file') == 0 || $(this).val() == 'file' ){
			$('.emio-source-text').hide();
			$('.emio-source-file').show();
			$('tbody.emio-frequency').hide();
		}else{
			$('.emio-source-file').hide();
			if( source_type.data('no-input') ){
				$('.emio-source-text').hide();
			}else{
				$('.emio-source-text').show().attr('placeholder', source_type.data('placeholder'));
			}
			$('tbody.emio-frequency').show();
		}
		$('.emio-source-type-text').hide();
		if( source_type.text() != '' ) source_type.show();
	}).trigger('change');
	if( $('button.emio-upload-again').length > 0 ){
		$('.emio-source-file, .emio-source-url, #emio_source_type').hide();
		$('button.emio-upload-again').on('click', function(){
			$('.emio-source-file-uploaded').hide();
			$('.emio-source-file, #emio_source_type').show();
		});
	}
	$('#emio_frequency').on('change', function(){
		if( $(this).val() == 0 ){
			$('.emio-frequency-option').hide();
		}else{
			$('.emio-frequency-option').show();
		}
	}).trigger('change');

	//fuzzy locations
	$('#emio_scope').on('change', function(){
		var scope = $(this).val();
		if( scope == 'events' ){
			$('.event-option').show();
			$('.location-option, tbody.emio-import-fuzzy-location').hide();
		}else if( scope == 'locations' ){
			$('.event-option, #fuzzy_location_default_row').hide();
			$('.location-option, tbody.emio-import-fuzzy-location').show();
		}else{
			$('.event-option, .location-option, #fuzzy_location_default_row, tbody.emio-import-fuzzy-location').show();
		}
	}).trigger('change');
	$('tbody.emio-import-fuzzy-location .emio-trigger-show, tbody.emio-import-fuzzy-location .emio-trigger-hide').on('click', function(){
		$('tbody.emio-import-fuzzy-location table.emio-import-fuzzy-location').toggle();
		$(this).toggle();
		$(this).siblings().toggle();
	});
	$("#emio-fuzzy-location-format" ).sortable({
		placeholder: "emio-fuzzy-location-format-ph",
		stop: function(){ $('#emio-fuzzy-location-format input').last().trigger('change'); }
	});
	$('#fuzzy_location_google_api').on('change', function(){
		if( this.value == 1 ){
			$('#fuzzy-location-non-default').hide();
		}else{
			$('#fuzzy-location-non-default').show();
		}
	}).trigger('change');
	$('#emio-fuzzy-location-format input, #fuzzy_location_delimiter').on('change', function(){
		var example = {name:'Empire State Building', address:'350 5th Ave', town:'New York', state:'NY', postcode:'10118', country:'United States', region:'Northeast Region'};
		var example_address = $('#emio-fuzzy-location-format input:checked, #emio-fuzzy-location-format input[type=hidden]').map( function(){
			return example[this.value];
		}).get();
		$('.emio-fuzzy-location-format-example').text( example_address.join($('#fuzzy_location_delimiter').val()) );
	}).last().trigger('change');

	//filter scope
	$('#emio_filter_scope').on('change', function(){
		if( $(this).val() == 'custom' ){
			$('#emio_filter_scope_range').show();
		}else{
			$('#emio_filter_scope_range').hide();
		}
	}).last().trigger('change');

	//select boxes
	$(".emio-select2").select2();

	//Google API key check
	if( !EMIO.google_api ){
		$('#fuzzy_location_google_api_row #fuzzy_location_google_api_no').prop('checked',true);
		$('#fuzzy_location_google_api_row input[name=fuzzy_location_google_api]').prop('disabled',true);
	}

	//form submission logic for imports
	var save_preview = function(){
		var button = $('#emio-editor.emio-editor-import #emio-editor-submit');
		button.val(button.data('save'));
	}
	$('#emio-editor.emio-editor-import').on('change', 'input,select', save_preview);
	$('#emio-editor.emio-editor-import').on('focus', 'input.hasDatepicker', save_preview);
	$('#emio-editor.emio-editor-import #emio-editor-submit').on('click', function(e){
		var button = $(this);
		if( button.val() == button.data('preview') ){
			//prevent default submit and just redirect for a preview
			e.preventDefault();
			var redirect = $('#emio-editor.emio-editor-import').attr('action') + '&tab=preview';
			window.location.href = redirect;
		}
	});

	//form submission logic for exports
	$('#emio-editor.emio-editor-export #emio-editor-submit-export').on('click', function(e){
		//change flag when export & save button is clicked to run the export
		$('#emio-editor-export-flag').val(1);
	});

	//preview section
	$('#emio-import-results').on('change', '#emio-import-preview .emio-import-item', function(){
		var cb = $(this);
		var button = $('#emio-import-button');
		if( $("input:checkbox:not(:checked).emio-import-item").length == 0 ){
			button.val( button.data('selected-all') );
			button.prop('disabled', false);
		}else{
			var checked = $("input:checkbox:checked.emio-import-item");
			button.val( button.data('selected').replace('%d', checked.length) );
			button.prop('disabled', checked.length == 0 );
		}
	});

	//Settings Page
	if( $('.events-manager-io-settings').length > 0 ){
		$(".postbox > h3").on('click', function(){ $(this).parent().toggleClass('closed'); });
		$(".postbox").addClass('closed');
		//Navigation Tabs
		$('.tabs-active .nav-tab-wrapper .nav-tab').on('click', function(){
			el = $(this);
			elid = el.attr('id');
			$('.emio-settings-group').hide();
			$('.'+elid).show();
			$(".postbox").addClass('closed');
		});
		$('.nav-tab-wrapper .nav-tab').on('click', function(){
			$('.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active').blur();
			$(this).addClass('nav-tab-active');
		});
		var navUrl = document.location.toString();
		if (navUrl.match('#')) { //anchor-based navigation
			var nav_tab = navUrl.split('#').pop().split('+');
			var current_tab = 'a#emio-settings-' + nav_tab[0];
			$(current_tab).trigger('click');
			if( nav_tab.length > 1 ){
				var section = $("#em-opt-"+nav_tab[1]);
				if( section.length > 0 ){
					section.children('h3').trigger('click');
					$('html, body').animate({ scrollTop: section.offset().top - 30 }); //sends user back to current section
				}
			}
		}else{
			//set to general tab by default, so we can also add clicked subsections
			document.location = navUrl+"#general";
			$('a#emio-settings-general').trigger('click');
		}
		$('.nav-tab-link').on('click', function(){ $($(this).attr('rel')).trigger('click'); }); //links to mimick tabs
		$('input[type="submit"]').on('click', function(){
			var el = $(this).parents('.postbox').first();
			var docloc = document.location.toString().split('#');
			var newloc = docloc[0];
			if( docloc.length > 1 ){
				var nav_tab = docloc[1].split('+');
				var tab_path = nav_tab[0];
				if( el.attr('id') ){
					tab_path = tab_path + "+" + el.attr('id').replace('em-opt-','');
				}
				newloc = newloc + "#" + tab_path;
			}
			//document.location = newloc;
			$(this).closest('form').append('<input type="hidden" name="tab_path" value="'+ tab_path +'" />');
		});
	}
});