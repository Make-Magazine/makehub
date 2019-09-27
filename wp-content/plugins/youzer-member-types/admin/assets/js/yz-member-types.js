( function( $ ) {

	'use strict';

	$( document ).ready( function() {

		/**
		 * Add New Type.
		 */
		$( document ).on( 'click', '#yz-add-member-type' , function( e ) {

			e.preventDefault();

			// Get Data.
			var	name_selector = $( '.yz-member-type-name span' ),
				member_types_form = $( '#yz-member-types-form' ),
				fieldName	  = 'yz_member_types[yz_member_type_' + yz_nextMType + ']',
				type 	  	  = $.yz_getAddData( member_types_form, 'yz_member_type' ),
				member_type_args = {
					value	: type['name'],
					form 	: member_types_form,
					selector: name_selector,
					type	: 'text',
					type_id : type['id'],
					type_icon : type['icon'],
					type_name : type['name'],
					type_slug  : type['slug'],
					type_active  : type['active'],
					type_singular : type['singular'],
					type_register : type['register'],
				};

			// Validate Data.
			if ( ! $.validate_member_types_data( member_type_args ) ) {
				return false;
			}

			// Add Item.
			$( '#yz_member_types' ).prepend(
				'<li class="yz-member-type-item" data-member-type-name="yz_member_type_'+ yz_nextMType +'">'+
				'<h2 class="yz-member-type-name">'+
				'<i class="yz-member-type-icon '+ type['icon'] +'"></i>'+
				'<span>' + type['name'] + '</span>'+
				'</h2>' +
				'<input type="hidden" name="' + fieldName +'[id]" value="' + type['id'] + '" >'+
				'<input type="hidden" name="' + fieldName +'[icon]" value="' + type['icon'] + '" >'+
				'<input type="hidden" name="' + fieldName +'[name]" value="' + type['name'] + '" >'+
				'<input type="hidden" name="' + fieldName +'[singular]" value="' + type['singular'] + '" >'+
				'<input type="hidden" name="' + fieldName +'[active]" value="' + type['active'] + '" >'+
				'<input type="hidden" name="' + fieldName +'[slug]" value="' + type['slug'] + '" >'+
				'<input type="hidden" name="' + fieldName +'[left_color]" value="' + type['left_color'] + '" >'+
				'<input type="hidden" name="' + fieldName +'[right_color]" value="' + type['right_color'] + '" >'+
				'<input type="hidden" name="' + fieldName +'[show_in_md]" value="' + type['show_in_md'] + '" >'+
				'<input type="hidden" name="' + fieldName +'[register]" value="' + type['register'] + '" >'+
				'<a class="yz-edit-item yz-edit-member-type"></a>' +
				'<a class="yz-delete-item yz-delete-member-type"></a>' +
				'</li>'
			);

			// Hide Modal
			$.yz_HideModal( member_types_form );

			// Increase ID Number
			yz_nextMType++;

		});

		/**
		 * Edit Member Type.
		 */
		$( document ).on( 'click', '.yz-edit-member-type' , function( e )	{

			// Get Data.
			var member_type_item  = $( this ).closest( '.yz-member-type-item' ),
				member_types_form = $( '#yz-member-types-form' );

			// Get Form Values
			$.yz_EditForm( {
				button_id	: 'yz-update-member-type',
				form_title	: Youzer_MT.update_member_type,
				form 		: member_types_form,
				item 		: member_type_item
			});

		});

		/**
		 * Save Member Type.
		 */
		$( document ).on( 'click', '#yz-update-member-type' , function( e )	{

			e.preventDefault();

			// Set Up Variables.
			var type_name = '.yz-member-type-name span',
				member_types_form = $( '#yz-member-types-form' ),
				member_type_item  = $.yz_getItemObject( member_types_form ),
				type = $.yz_getNewData( member_types_form, 'keyToVal' ),
				member_type_args = {
					old_name 	: member_type_item.find( type_name ).text(),
					value		: type['name'],
					form 		: member_types_form,
					selector 	: $( type_name ),
					type		: 'text', 
					type_id   	: type['id'],
					type_icon   : type['icon'],
					type_name   : type['name'],
					type_slug   : type['slug'],
					type_active : type['active'],
					type_singular : type['singular'],
					type_register : type['register'],
				};

			// Validate Tab Data.
			if ( ! $.validate_member_types_data( member_type_args ) ) {
				return false;
			}

			// Update Data.
			$.yz_updateFieldsData( member_types_form );

		});

		/**
		 * Validate Member Type Data.
		 */
		$.validate_member_types_data = function( options ) {

			// O = Options
			var o = $.extend( {}, options );

			if ( o.type_name == null || $.trim( o.type_name ) == '' ) {
				// Show Error Message
                $.ShowPanelMessage( {
                    msg  : Youzer_MT.mtype_name_empty,
                    type : 'error'
                } );
                return false;
			}

			// Check if type Exist or not
			var nameAlreadyeExist = $.yz_isAlreadyExist( {
				old_title 	: o.old_name,
				selector 	: o.selector,
				value		: o.value,
				type		: 'text'
			});

			if ( nameAlreadyeExist ) {
				// Show Error Message
                $.ShowPanelMessage( {
                    msg  : Youzer_MT.name_exist,
                    type : 'error'
                });
                return false;
			}

			if ( o.type_singular == null || $.trim( o.type_singular ) == '' ) {
				// Show Error Message
				$.ShowPanelMessage( {
					msg  : Youzer_MT.mtype_singular_empty,
					type : 'error'
				} );
				return false;
			}


			if ( o.type_slug == null || $.trim( o.type_slug ) == '' ) {
				// Show Error Message
				$.ShowPanelMessage( {
					msg  : Youzer_MT.mtype_slug_empty,
					type : 'error'
				} );
				return false;
			}

			return true;
		}

	});

})( jQuery );