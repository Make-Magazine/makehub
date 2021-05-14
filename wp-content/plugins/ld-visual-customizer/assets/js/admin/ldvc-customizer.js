jQuery(document).ready(function($) {

    /**
     * Template and theme changes
     * @type {[type]}
     */

     $('body').on( 'change', '#_customize-input-lds_listing_style', function() {

         if( $(this).val() == 'grid-banner' ) {

             $('#customize-control-lds_grid_columns').show();
             $('#customize-control-lds_skin').hide();
             $('#_customize-input-lds_skin').val('default');

             ldsSetBaseTheme('default');

         } else if( $(this).val() == 'expanded' || $(this).val() == 'stacked' ) {

             $('#customize-control-lds_skin').hide();

             $('#customize-control-lds_grid_columns').hide();

             ldsSetBaseTheme('default');


         } else {
             $('#customize-control-lds_grid_columns').hide();
             $('#customize-control-lds_skin').show();
         }

     });

     $('body').on( 'click', '.lds-vc-clear', function(e) {

         e.preventDefault();

         var thisParent = $(this).parents('.control-section');

         $(thisParent).find('input[type="text"]').val('').trigger('change');

         $(thisParent).find('input[type="range"]').each(function() {

             var def = $(this).attr('default');

             $(this).siblings('.lds-range-input').val(def);

             $(this).val(def).trigger('change');

         });


         $(thisParent).find('select').prop( 'selectedIndex', 0 ).trigger('change');
         $(thisParent).find('.wp-picker-clear').trigger('click');

     });


     if( wp.customize ) {

         wp.customize.bind( 'ready', function() {

             if( $('#_customize-input-lds_listing_style').val() == 'grid-banner' ) {
                 $('#customize-control-lds_grid_columns').show();
                 $('#customize-control-lds_skin').hide();
             } else if( $('#_customize-input-lds_listing_style').val() == 'expanded' || $('#_customize-input-lds_listing_style').val() == 'stacked' ) {
                 $('#customize-control-lds_skin').hide();
                 $('#customize-control-lds_grid_columns').hide();
             } else {
                 $('#customize-control-lds_grid_columns').hide();
             }

         });

     }



     $('body').on( 'change', '#_customize-input-lds_skin', function() {

         if( typeof ldvc_themes !== 'undefined' ) {

             var theme = $(this).val();

             if( typeof ldvc_themes[ theme ] !== 'undefined' ) {
                 ldsSetBaseTheme( ldvc_themes[ theme ] );
             } else {
                 alert( 'Error, this this was not found');
             }

         } else {
             alert( 'No themes found' );
         }

     });

    /**
     * Theme Schema
     *
     * ldvc.modern
     *      .controls
     *          .controlname = value
     *      .hide
     *          .element
     *              .classname
     *
     *

    if( wp.customize ) {

        wp.customize.control('lds_icon_style', function( control ) {
            control.setting.set( 'modern' );
        });

        wp.customize.control('lds_button_border_radius', function( control ) {
            control.setting.set( '0' );
        });

    } */

});

function ldsSetBaseTheme( theme ) {

    if( wp.customize ) {

        jQuery.each( ldvc_themes.reset, function( index, setting ) {

            wp.customize.control( setting, function(control) {
                control.setting.set('');
            });

        });

        jQuery.each( theme.controls, function( setting, value ) {

            wp.customize.control( setting, function(control) {
                control.setting.set(value);
            });

        });

    }

}
