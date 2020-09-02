import ReactHtmlParser from 'react-html-parser';
import reactStringReplace from 'react-string-replace';
import { pluginData } from 'js/app';
import _ from 'lodash';

/**
 * Replace %s placeholders in a string with data
 *
 * @param {string} string String with "%s" placeholders
 * @param {Array} placeholders Array with placeholder data
 *
 * @return {string} String with replaced placeholders
 */
export const replacePlaceholders = ( string, placeholders ) => {
	const replacements = _.isArray( placeholders ) ? placeholders : [];

	return ReactHtmlParser(
		reactStringReplace(
			string,
			/(%s)/,
			( match ) => {
				const replacement = replacements.shift();

				return replacement ? replacement : match;
			} ).join( '' ),
	);
};

/**
 * Decode HTML entities
 *
 * @param {string} string
 *
 * @return {string} String with HTML entities decoded
 */
export const unescapeString = ( string ) => {
	const el = document.createElement( 'textarea' );

	el.innerHTML = string;

	return el.value;
};

/**
 * Replace [link]XYZ[/link] placeholders in a string with links
 *
 * @param {string} string String with "[link]XYZ[/link]" placeholders
 * @param {Array} placeholders Array with placeholder data
 *
 * @return {string} String with replaced placeholders
 */
export const replacePlaceholderLinks = ( string, placeholders ) => {
	const replacements = _.isArray( placeholders ) ? placeholders : [];

	return reactStringReplace(
		string,
		/\[link\](.*?)\[\/link\]/,
		( match ) => {
			const replacementData = replacements.shift();

			if ( ! _.isObject( replacementData ) ) {
				return match;
			}

			return replacementData.onClick ?
				<button
					key={ match }
					className={ 'button-link' }
					tabIndex={ 0 }
					onClick={ () => replacementData.onClick() }
					onKeyDown={ ( e ) => e.keyCode === 13 ? replacementData.onClick() : null }
				>
					{ match }
				</button> :
				<a key={ match } { ...replacementData }>{ match }</a>;
		},
	);
};

/**
 * Add period to the end of the string when it's missing one
 *
 * @param {string} string String with or without a period
 *
 * @return {string} Original string or one with a period added
 */
export const addPeriodToString = ( string ) => {
	if ( _.isEmpty( string ) ) {
		return '';
	}

	return /\.$/.test( string.trim() ) ? string : `${ string }.`;
};

/**
 * Extract JSON object from a string
 *
 * @param {string} str String with JSON
 *
 * @return {Object} Empty object or JSON
 */
export const extractJSON = ( str ) => {
	let extractedJSON;
	try {
		extractedJSON = JSON.parse( str );
	} catch ( exception ) {}

	if ( ! _.isObject( extractedJSON ) ) {
		try {
			extractedJSON = JSON.parse( ( str.match( /(.|\n)({".*?)$/ ) || [] )[ 0 ] );
		} catch ( exception ) {}
	}

	if ( ! _.isObject( extractedJSON ) ) {
		extractedJSON = {};
	}

	return extractedJSON;
};

/**
 * Join array of JSX elements using separator
 *
 * @param {Array} array
 * @param {string} interimSeparator
 * @param {string} endSeparator
 *
 * @return {ReactElement} JSX markup
 */
export const joinJSXArray = ( array, interimSeparator, endSeparator ) => {
	return array.length > 0 ? array.reduce( ( result, item, i ) => <>{ result }{ i < ( array.length - 1 ) || ! endSeparator ? interimSeparator : endSeparator }{ item }</> ) : null;
};

/**
 * Deep search object by property. Adopted from: https://codereview.stackexchange.com/a/73755
 *
 * @param {Object} object
 * @param {string} property
 * @param {value} value
 *
 * @return {void|Object} Search result
 */
export const findByProperty = ( object, property, value ) => {
	if ( _.get( object, property ) === value ) {
		return object;
	}

	let result;

	for ( const p in object ) {
		if ( _.isObject( object[ p ] ) ) {
			result = findByProperty( object[ p ], property, value );
			if ( result ) {
				return result;
			}
		}
	}

	return result;
};

export const i18nFormatNumber = ( number ) => new Intl.NumberFormat( pluginData.locale || 'en-US' ).format( number );

