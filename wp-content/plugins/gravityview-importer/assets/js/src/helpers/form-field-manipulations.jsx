import _ from 'lodash';
import { pluginData } from 'js/app';
import { findByProperty } from 'js/helpers/string-manipulations';

/**
 * API's format for multi-input fields is TYPE[INDEX].INPUT
 *
 * @type {RegExp}
 */
export const multiInputTypeRegex = new RegExp( /(\w+)\[(\d+)\]\.(\w+)/, 'i' );

/**
 * Get and format field label
 *
 * @param {number} fieldId Field ID
 * @param {Object} formFields Object with form fields
 *
 * @return {string} Field label
 */
export const getFormFieldLabel = ( fieldId, formFields ) => {
	const field = getFormField( fieldId, formFields );

	return field.label ? field.label : pluginData.localization.shared.field_label_not_available;
};

/**
 * Get field object from an array of fields IDs or types
 *
 * @param {number} fieldId Field ID
 * @param {Object} formFields Object with form fields
 *
 * @return {Object} Form field data
 */
export const getFormField = ( fieldId, formFields ) => {
	const _fieldId = String( fieldId );
	let field;

	/**
	 * Field ID can take multiple forms:
	 *   1. single input field alphabetic (e.g, 'date_updated')
	 *   2. single input field numeric
	 *   3. multi-input field alphabetic/numeric (e.g., 'address.1', '3.1')
	 *   4. multi-input field formatted for API (e.g., 'address[0].1')
	 */
	if ( formFields[ _fieldId ] ) {
		field = formFields[ _fieldId ];
	} else if ( _fieldId.match( multiInputTypeRegex ) ) {
		const _id = multiInputTypeRegex.exec( _fieldId );

		field = findByProperty( formFields[ _id[ 1 ] ], 'id', parseInt( _id[ 3 ], 10 ) );
	} else if ( _fieldId.match( new RegExp( /\w+\.\d+/, 'i' ) ) ) {
		const _id = _fieldId.split( '.' );

		field = formFields[ _id[ 0 ] ].inputs[ _id[ 1 ] ] || formFields[ _id[ 0 ] ].inputs[ _fieldId ];
	} else {
		field = findByProperty( formFields, 'id', _fieldId );
	}

	if ( ! _.isObject( field ) ) {
		field = {};
	}

	return ! field.parent_id ? field : _.assign( {}, field, { parent_label: formFields[ field.parent_id ].label } );
};
