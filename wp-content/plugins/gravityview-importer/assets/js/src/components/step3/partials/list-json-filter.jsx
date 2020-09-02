/* global wp */

import _ from 'lodash';
import classNames from 'classnames/bind';
import { pluginData } from 'js/app';
import { replacePlaceholders } from 'js/helpers/string-manipulations';

import appStyles from 'css/app';

const styles = classNames.bind( appStyles );

const { Component } = wp.element;

/**
 * Check if list data is a valid JSON object
 *
 * @param {string} json
 *
 * @return {boolean} Return true or false if JSON object is valid
 */
export const isValidListJson = ( json ) => {
	try {
		const obj = JSON.parse( json );

		// List data must be an array
		if ( ! _.isObject( obj ) ) {
			throw false;
		}

		return true;
	} catch ( e ) {
		return false;
	}
};

export default class ListJsonFilter extends Component {
	/**
	 * Validate and display list field object
	 *
	 * @return {ReactElement} JSX markup
	 */
	render() {
		const { formatField: { value: list } = {} } = this.props;
		const truncateLength = 35;

		if ( 0 === list.length ) {
			return null;
		}

		const validList = isValidListJson( list );
		const obj = JSON.parse( list );
		let displayMessage;

		if ( validList ) {
			displayMessage = ( _.isObject( obj[ 0 ] ) ) ?
				replacePlaceholders( pluginData.localization.list_json_filter.found_x_columns_rows, [ `${ _.size( obj[ 0 ] ) }`, `${ obj.length }` ] ) : // %s row(s); %s column(s)
				replacePlaceholders( pluginData.localization.list_json_filter.found_x_rows, [ `${ obj.length }` ] ); // %s row(s)
		} else {
			displayMessage = pluginData.localization.list_json_filter.invalid_list; // List data not recognized
		}

		return <>
			{ list.length > truncateLength ? `${ list.substring( 0, truncateLength ) }...` : list }
			{ ' ' }
			<span
				className={ styles( 'is-size-7', validList ? 'has-text-grey' : 'has-text-danger' ) }
			>
				&rarr; { ` ${ displayMessage }` }
			</span>
		</>;
	}
}
