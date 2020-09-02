/* global wp */

import _ from 'lodash';
import classNames from 'classnames/bind';
import { pluginData } from 'js/app';
import { replacePlaceholders } from 'js/helpers/string-manipulations';

import appStyles from 'css/app';

const styles = classNames.bind( appStyles );

const { Component } = wp.element;

export default class EntryNotesFilter extends Component {
	/**
	 * Validate and display the entry notes object
	 *
	 * @return {ReactElement} JSX markup
	 */
	render() {
		const { formatField: { value: entryNotes } = {} } = this.props;
		const truncateLength = 35;

		if ( 0 === entryNotes.length ) {
			return null;
		}

		let validEntryNotes = true;
		let displayMessage;

		try {
			const obj = JSON.parse( entryNotes );

			// Entry notes are an array of objects; verify that the first array contains "note_type" property
			if ( ! _.get( obj, '0.note_type' ) ) {
				throw false;
			}
			displayMessage = replacePlaceholders( pluginData.localization.entry_notes_filter.found_x_notes, [ `${ obj.length }` ] ); // %s entry note(s)
		} catch ( e ) {
			validEntryNotes = false;
			displayMessage = pluginData.localization.entry_notes_filter.invalid_notes; // Entry notes not recognized
		}

		return <>
			{ entryNotes.length > truncateLength ? `${ entryNotes.substring( 0, truncateLength ) }...` : entryNotes }
			{ ' ' }
			<span
				className={ styles( 'is-size-7', validEntryNotes ? 'has-text-grey' : 'has-text-danger' ) }
			>
				&rarr; { ` ${ displayMessage }` }
			</span>
		</>;
	}
}
