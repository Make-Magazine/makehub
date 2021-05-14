jQuery(document).ready(function($) {

    $(function() {

        // Add Color Picker to all inputs that have 'color-field' class
       $('.learndash-skin-color-picker').wpColorPicker({
           clear: function() {}
       });

       $('.learndash-skin-color-picker .wp-picker-clear').trigger('click');

    });


	// Show the current preview based on what's saved
	var current_lds_skin = $('#learndash-skin').val();
	$('#lds-'+current_lds_skin).show();

	if($('#lds_listing_style').val() == 'expanded') {
        $('#lds-expanded').show();
    }
    if($('#lds_listing_style').val() == 'grid-banner') {
        $('#lds-grid-banner').show();
    }
    if( $('#lds_listing_style').val() == 'enhanced' ) {
        $('#lds_show_leaderboard_settings').show();
    }

    if( $('#lds_listing_style').val() == 'grid-banner' ) {
        $('#lds-grid-columns-settings').show();
    }

    $('#lds_listing_style').change(function() {

        if( $(this).val() == 'grid-banner' ) {
            $('#lds-grid-columns-settings').show();
        } else {
            $('#lds-grid-columns-settings').hide();
        }

    });


	$('#learndash-skin').change(function() {

		var new_lds_skin = $(this).val();

		lds_load_defaults( new_lds_skin, '.', '' );

		$('.lds-theme-preview').hide();

		$('#lds-' + new_lds_skin).show();

	});

    $('#_customize-input-lds_skin').change(function() {

        console.log('run the color pickers!');

        var new_lds_skin = $(this).val();

        lds_load_defaults( new_lds_skin, '#customize-control-', ' .wp-color-picker' );

    });

	$('#lds_listing_style').change(function() {

		if( $(this).val() == 'expanded' ) {
			$('#lds-expanded').show();
		} else {
			$('#lds-expanded').hide();
		}

        if( $(this).val() == 'grid-banner' ) {
            $('#lds-grid-banner').show();
        } else {
            $('#lds-grid-banner').hide();
        }

        if( $(this).val() == 'enhanced' ) {
            $('#lds_show_leaderboard_settings').show();
        } else {
            $('#lds_show_leaderboard_settings').hide();
        }

	});

    $('.lds-clear-colors').click(function(e) {

        e.preventDefault();

        $('.color-picker-table .wp-picker-clear').trigger('click');

    });

    if( $('#_customize-input-lds_listing_style').val() != 'grid-banner' ) {
        $('#customize-control-lds_grid_columns').hide();
    }

    $('#_customize-input-lds_listing_style').change(function() {

        if( $(this).val() == 'grid-banner' ) {
            $('#customize-control-lds_grid_columns').show();
        } else {
            $('#customize-control-lds_grid_columns').hide();
        }

    });

	function lds_load_defaults( skin, location, selector ) {

        console.log( location + 'lds_row_bg' + selector );

		if(skin == 'modern') {

			$( location + 'lds_heading_bg' + selector ).wpColorPicker('color','#2f4050');
			$( location + 'lds_heading_txt' + selector ).wpColorPicker('color','#a7b1c2');
			$( location + 'lds_row_bg' + selector ).wpColorPicker('color','#f6f6f7');
			$( location + 'lds_row_bg_alt' + selector ).wpColorPicker('color','#efeff1');
			$( location + 'lds_row_txt' + selector ).wpColorPicker('color','#2f4050');
			$( location + 'lds_sub_row_bg' + selector ).wpColorPicker('color','#ffffff');
			$( location + 'lds_sub_row_bg_alt' + selector ).wpColorPicker('color','#ffffff');
			$( location + 'lds_sub_row_txt' + selector ).wpColorPicker('color','#2f4050');
			$( location + 'lds_button_bg' + selector ).wpColorPicker('color','#23c6c8');
			$( location + 'lds_button_txt' + selector ).wpColorPicker('color','#ffffff');
			$( location + 'lds_complete_button_bg' + selector ).wpColorPicker('color','#1ab394');
			$( location + 'lds_complete_button_txt' + selector ).wpColorPicker('color','#ffffff');
			$( location + 'lds_progress' + selector ).wpColorPicker('color','#1ab394');
			$( location + 'lds_links' + selector ).wpColorPicker('color','#2f4050');
			$( location + 'lds_widget_bg' + selector ).wpColorPicker('color','#f5f5f6');
			$( location + 'lds_widget_header_bg' + selector ).wpColorPicker('color','#2f4050');
			$( location + 'lds_widget_header_txt' + selector ).wpColorPicker('color','#a7b1c2');
			$( location + 'lds_widget_txt' + selector ).wpColorPicker('color','#444444');
			$( location + 'lds_checkbox_complete' + selector ).wpColorPicker('color','#1ab394');
			$( location + 'lds_checkbox_incomplete' + selector ).wpColorPicker('color','#2f4050');
			$( location + 'lds_arrow_complete' + selector ).wpColorPicker('color','#1ab394');
			$( location + 'lds_arrow_incomplete' + selector ).wpColorPicker('color','#2f4050');

			$('#lds_button_border_radius').val('0');
			$('#lds_icon_style').val('modern');

            if( wp.customize ) {

                wp.customize.control('lds_icon_style', function( control ) {
                    control.setting.set( 'modern' );
                });

                wp.customize.control('lds_button_border_radius', function( control ) {
                    control.setting.set( '0' );
                });

            }
		}

		if(skin == 'rustic') {

			$( location + 'lds_heading_bg' + selector ).wpColorPicker('color','#036564');
			$( location + 'lds_heading_txt' + selector ).wpColorPicker('color','#ffffff');
			$( location + 'lds_row_bg' + selector ).wpColorPicker('color','#f9f4ec');
			$( location + 'lds_row_bg_alt' + selector ).wpColorPicker('color','#f3efe9');
			$( location + 'lds_row_txt' + selector ).wpColorPicker('color','#333333');
			$( location + 'lds_sub_row_bg' + selector ).wpColorPicker('color','#fbfffd');
			$( location + 'lds_sub_row_bg_alt' + selector ).wpColorPicker('color','#fbfffd');
			$( location + 'lds_sub_row_txt' + selector ).wpColorPicker('color','#333');
			$( location + 'lds_button_bg' + selector ).wpColorPicker('color','#031634');
			$( location + 'lds_button_txt' + selector ).wpColorPicker('color','#ffffff');
			$( location + 'lds_complete_button_bg' + selector ).wpColorPicker('color','#036564');
            $( location + 'lds_complete_button_txt' + selector ).wpColorPicker('color','#ffffff');
			$( location + 'lds_progress' + selector ).wpColorPicker('color','#036564');
			$( location + 'lds_links' + selector ).wpColorPicker('color','#031634');
			$( location + 'lds_widget_bg' + selector ).wpColorPicker('color','#f9f4ec');
			$( location + 'lds_widget_header_bg' + selector ).wpColorPicker('color','#033649');
			$( location + 'lds_widget_header_txt' + selector ).wpColorPicker('color','#ffffff');
			$( location + 'lds_widget_txt' + selector ).wpColorPicker('color','#333333');
			$( location + 'lds_checkbox_complete' + selector ).wpColorPicker('color','#036564');
			$( location + 'lds_checkbox_incomplete' + selector ).wpColorPicker('color','#dddddd');
			$( location + 'lds_arrow_complete' + selector ).wpColorPicker('color','#85d18a');
			$( location + 'lds_arrow_incomplete' + selector ).wpColorPicker('color','#036564');

			$('#lds_button_border_radius').val('2');
			$('#lds_icon_style').val('chunky');

            if( wp.customize ) {

                wp.customize.control('lds_icon_style', function( control ) {
                    control.setting.set( 'chunky' );
                });

                wp.customize.control('lds_button_border_radius', function( control ) {
                    control.setting.set( '2' );
                });

                $('#customize-control-lds_button_border_radius .range-slider').val('2');
                $('#customize-control-lds_button_border_radius .lds-range-input').val('2');

            }

		}

		if(skin == 'classic') {

			$( location + 'lds_heading_bg' + selector ).wpColorPicker('color','#333');
			$( location + 'lds_heading_txt' + selector ).wpColorPicker('color','#fff');
			$( location + 'lds_row_bg' + selector ).wpColorPicker('color','#fafafa');
			$( location + 'lds_row_bg_alt' + selector ).wpColorPicker('color','#fafafa');
			$( location + 'lds_row_txt' + selector ).wpColorPicker('color','#333');
			$( location + 'lds_sub_row_bg' + selector ).wpColorPicker('color','#fff');
			$( location + 'lds_sub_row_bg_alt' + selector ).wpColorPicker('color','#fff');
			$( location + 'lds_sub_row_txt' + selector ).wpColorPicker('color','#333');
			$( location + 'lds_button_bg' + selector ).wpColorPicker('color','#333');
			$( location + 'lds_button_txt' + selector ).wpColorPicker('color','#fff');
			$( location + 'lds_complete_button_bg' + selector ).wpColorPicker('color','#FBB829');
			$( location + 'lds_complete_button_txt' + selector ).wpColorPicker('color','#fff');
			$( location + 'lds_progress' + selector ).wpColorPicker('color','#FBB829');
			$( location + 'lds_links' + selector ).wpColorPicker('color','#333');
			$( location + 'lds_widget_bg' + selector ).wpColorPicker('color','#fafafa');
			$( location + 'lds_widget_header_bg' + selector ).wpColorPicker('color','#333');
			$( location + 'lds_widget_header_txt' + selector ).wpColorPicker('color','#fff');
			$( location + 'lds_widget_txt' + selector ).wpColorPicker('color','#333');
			$( location + 'lds_checkbox_complete' + selector ).wpColorPicker('color','#FBB829');
			$( location + 'lds_checkbox_incomplete' + selector ).wpColorPicker('color','#dddddd');
			$( location + 'lds_arrow_complete' + selector ).wpColorPicker('color','#FBB829');
			$( location + 'lds_arrow_incomplete' + selector ).wpColorPicker('color','#006198');

			$('#lds_button_border_radius').val('0');
            $('#customize-control-lds_button_border_radius .range-slider').val('0');
            $('#customize-control-lds_button_border_radius .lds-range-input').val('0');

			$('#lds_icon_style').val('circles');

            if( wp.customize ) {

                wp.customize.control('lds_icon_style', function( control ) {
                    control.setting.set( 'circles' );
                });

                wp.customize.control('lds_button_border_radius', function( control ) {
                    control.setting.set( '0' );
                });

                $('#customize-control-lds_button_border_radius .range-slider').val('0');
                $('#customize-control-lds_button_border_radius .lds-range-input').val('0');

            }


		}

		if(skin == 'playful') {

			$( location + 'lds_heading_bg' + selector ).wpColorPicker('color','#1693A5');
			$( location + 'lds_heading_txt' + selector ).wpColorPicker('color','#ffffff');
			$( location + 'lds_row_bg' + selector ).wpColorPicker('color','#f9f9f9');
			$( location + 'lds_row_bg_alt' + selector ).wpColorPicker('color','#f1f1f1');
			$( location + 'lds_row_txt' + selector ).wpColorPicker('color','#1693A5');
			$( location + 'lds_sub_row_bg' + selector ).wpColorPicker('color','#fff');
			$( location + 'lds_sub_row_bg_alt' + selector ).wpColorPicker('color','#fff');
			$( location + 'lds_sub_row_txt' + selector ).wpColorPicker('color','#1693A5');
			$( location + 'lds_button_bg' + selector ).wpColorPicker('color','#1693A5');
			$( location + 'lds_button_txt' + selector ).wpColorPicker('color','#fff');
			$( location + 'lds_complete_button_bg' + selector ).wpColorPicker('color','#FF0066');
			$( location + 'lds_complete_button_txt' + selector ).wpColorPicker('color','#fff');
			$( location + 'lds_progress' + selector ).wpColorPicker('color','#FF0066');
			$( location + 'lds_links' + selector ).wpColorPicker('color','#1693A5');
			$( location + 'lds_widget_bg' + selector ).wpColorPicker('color','#f9f9f9');
			$( location + 'lds_widget_header_bg' + selector ).wpColorPicker('color','#1693A5');
			$( location + 'lds_widget_header_txt' + selector ).wpColorPicker('color','#fff');
			$( location + 'lds_widget_txt' + selector ).wpColorPicker('color','#473d13');
			$( location + 'lds_checkbox_complete' + selector ).wpColorPicker('color','#FF0066');
			$( location + 'lds_checkbox_incomplete' + selector ).wpColorPicker('color','#aaa');
			$( location + 'lds_arrow_complete' + selector ).wpColorPicker('color','#FF0066');
			$( location + 'lds_arrow_incomplete' + selector ).wpColorPicker('color','#aaa');

			$('#lds_button_border_radius').val('12');
			$('#lds_icon_style').val('playful');

            if( wp.customize ) {

                wp.customize.control('lds_icon_style', function( control ) {
                    control.setting.set( 'playful' );
                });

                wp.customize.control('lds_button_border_radius', function( control ) {
                    control.setting.set( '12' );
                });

                $('#customize-control-lds_button_border_radius .range-slider').val('12');
                $('#customize-control-lds_button_border_radius .lds-range-input').val('12');

            }



		}

		if(skin == 'default') {

            $('.color-picker-table .wp-picker-clear').trigger('click');

			$('#lds_button_border_radius').val('0');
            $('#customize-control-lds_button_border_radius .range-slider').val('0');
            $('#customize-control-lds_button_border_radius .lds-range-input').val('0');

			$('#lds_icon_style').val('default');

            if( wp.customize ) {

                wp.customize.control('lds_icon_style', function( control ) {
                    control.setting.set( 'default' );
                });

                wp.customize.control('lds_button_border_radius', function( control ) {
                    control.setting.set( '0' );
                });

                $('#customize-control-lds_button_border_radius .range-slider').val('0');
                $('#customize-control-lds_button_border_radius .lds-range-input').val('0');

            }

		}

		if(skin == 'upscale') {
            $( location + 'lds_heading_bg' + selector ).wpColorPicker('color','#F0D878');
            // $('.lds_heading_bg').wpColorPicker('color','#69514D');
			$( location + 'lds_heading_txt' + selector ).wpColorPicker('color','#333');
			$( location + 'lds_row_bg' + selector ).wpColorPicker('color','#333');
			$( location + 'lds_row_bg_alt' + selector ).wpColorPicker('color','#222');
			$( location + 'lds_row_txt' + selector ).wpColorPicker('color','#fff');
			$( location + 'lds_sub_row_bg' + selector ).wpColorPicker('color','#444');
			$( location + 'lds_sub_row_bg_alt' + selector ).wpColorPicker('color','#444');
			$( location + 'lds_sub_row_txt' + selector ).wpColorPicker('color','#fff');
			$( location + 'lds_button_bg' + selector ).wpColorPicker('color','#333');
			$( location + 'lds_button_txt' + selector ).wpColorPicker('color','#fff');
			$( location + 'lds_complete_button_bg' + selector ).wpColorPicker('color','#F0D878');
			$( location + 'lds_complete_button_txt' + selector ).wpColorPicker('color','#333');
			$( location + 'lds_progress' + selector ).wpColorPicker('color','#F0D878');
			$( location + 'lds_links' + selector ).wpColorPicker('color','#fff');
			$( location + 'lds_widget_bg' + selector ).wpColorPicker('color','#000');
			$( location + 'lds_widget_header_bg' + selector ).wpColorPicker('color','#F0D878');
			$( location + 'lds_widget_header_txt' + selector ).wpColorPicker('color','#333');
			$( location + 'lds_widget_txt' + selector ).wpColorPicker('color','#fff');
			$( location + 'lds_checkbox_complete' + selector ).wpColorPicker('color','#F0D878');
			$( location + 'lds_checkbox_incomplete' + selector ).wpColorPicker('color','#f1f1f1');
			$( location + 'lds_arrow_complete' + selector ).wpColorPicker('color','#F0D878');
			$( location + 'lds_arrow_incomplete' + selector ).wpColorPicker('color','#f1f1f1');

			$('#lds_button_border_radius').val('0');
			$('#lds_icon_style').val('circles');

            if( wp.customize ) {

                wp.customize.control('lds_icon_style', function( control ) {
                    control.setting.set( 'circles' );
                });

                wp.customize.control('lds_button_border_radius', function( control ) {
                    control.setting.set( '0' );
                });

                $('#customize-control-lds_button_border_radius .range-slider').val('0');
                $('#customize-control-lds_button_border_radius .lds-range-input').val('0');

            }

            $('#_customize-input-lds_icon_style').val('circles');


		}

	}

});
