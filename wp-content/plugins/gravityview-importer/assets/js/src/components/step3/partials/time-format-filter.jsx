/* global wp */

import _ from 'lodash';
import dayjs from 'dayjs';
import classNames from 'classnames/bind';
import ModalDialog from 'js/shared/modal-dialog';
import { pluginData } from 'js/app';
import { replacePlaceholders } from 'js/helpers/string-manipulations';
import customParseFormat from 'dayjs/plugin/customParseFormat';

import XRegExp from 'xregexp';

import appStyles from 'css/app';

const styles = classNames.bind( appStyles );

const { Component } = wp.element;

dayjs.extend( customParseFormat );

export const DEFAULT_TIME_FORMAT = 'g:i a';

/**
 * Get an object with available formats: PHP time format as keys, description a values
 *
 * @return {Object} Time formats
 */
const getAvailableFormats = () => ( {
	'g:i a': 'hh:mm AM/PM',
	'H:i': 'hh:mm',
	custom: pluginData.localization.time_format_filter.custom_format,
} );

/**
 * Transform time format for API consumption
 *
 * @param {string} format
 *
 * @return {string} Format to be passed to the API as meta
 */
export const transformTimeFilterFormatForAPI = ( format ) => {
	if ( ! format ) {
		return DEFAULT_TIME_FORMAT;
	}

	if ( getAvailableFormats()[ format ] ) {
		return format;
	}

	return `regex:(${ btoa( getRegexFromFormat( format ).replace( '(?<', '(?P<' ) ) })`;
};

/**
 * Convert time format to regex
 *
 * @param {string} format
 * @return {string} Regex
 */
const getRegexFromFormat = ( format ) => {
	let parsedFormat = format;

	switch ( format ) {
		case 'g:i a':
			parsedFormat = '%H:%M %A';
			break;
		case 'H:i':
			parsedFormat = '%H:%M';
			break;
	}

	return parsedFormat
		.replace( /\^/g, '.*?' )
		.replace( /\~/g, '.' )
		.replace( /%H/, '(?<hour>\\d{1,2})' )
		.replace( /%M/, '(?<minute>\\d{2,})' )
		.replace( /%A/, '(?<period>[A|P]\\.?M\\.?)' );
};

export default class TimeFormatFilter extends Component {
	constructor( props ) {
		super( props );

		if ( this.props.formatField ) {
			return;
		}

		this.TIME_VALUE = this.getTimeValue();

		const _parsedTime = this.parseTime( this.TIME_VALUE, DEFAULT_TIME_FORMAT );

		this.state = {
			selectedFormat: '',
			customFormat: '%H:%M %A',
			originalFormatParsedTime: _parsedTime,
			selectedFormatParsedTime: _parsedTime,
		};
	}

	/**
	 * Run on component mount
	 *
	 * @return {void}
	 */
	componentDidMount() {
		const { formatField, field: { _data: { filterData } } = { _data: {} } } = this.props;

		if ( formatField || ! filterData ) {
			return;
		}

		// Update state with previously selected options
		if ( ! getAvailableFormats()[ filterData ] ) {
			const originalFormatParsedTime = this.parseTime( this.TIME_VALUE, filterData );

			this.setState( {
				selectedFormat: 'custom',
				customFormat: filterData,
				originalFormatParsedTime,
				selectedFormatParsedTime: originalFormatParsedTime,
			} );
		} else {
			const originalFormatParsedTime = this.parseTime( this.TIME_VALUE, filterData );
			this.setState( {
				selectedFormat: filterData,
				originalFormatParsedTime,
				selectedFormatParsedTime: originalFormatParsedTime,
			} );
		}
	}

	/**
	 * Get time field value from import data (extract)
	 *
	 * @return {string} Time field value
	 */
	getTimeValue() {
		const { importData, columnIndex } = this.props;

		// Import data is an array of rows with data for each column: [[column1value,column2value],...]
		// Destructure the object to get the first 2 rows
		const { 0: { [ columnIndex ]: timeValue1 = '' }, 1: { [ columnIndex ]: timeValue2 = '' } } = importData;

		// Check if the second row is empty or the first row is a header (i.e., does not contain a sequence of numbers separated by colon)
		const timeValue = ( ! timeValue2 || timeValue1.match( /\d[:.\s]\d/ ) ) ? timeValue1 : timeValue2;

		return ( timeValue.length > 20 ) ? `${ timeValue.substring( 0, 20 ) }...` : timeValue;
	}

	/**
	 * Parse time from time field value using custom format
	 *
	 * @param {string} time
	 * @param {string} format
	 *
	 * @return {string} Parsed and formatted time or an invalid time notice
	 */
	parseTime( time, format ) {
		if ( ! format || ! format.trim().length ) {
			return pluginData.localization.time_format_filter.invalid_time; // Invalid Time
		}

		const matchedTime = XRegExp.exec( time, XRegExp( getRegexFromFormat( format ), 'i' ) );

		if ( ! matchedTime ) {
			return pluginData.localization.time_format_filter.invalid_time; // Invalid Time
		} else if ( ! matchedTime.hour || ! matchedTime.minute ) {
			return pluginData.localization.time_format_filter.incomplete_time; // Time field must contain hour and minute values
		}

		const timePeriod = matchedTime.period ? ` ${ matchedTime.period.toUpperCase() }` : '';
		const parsedTime = dayjs( `${ matchedTime.hour }:${ matchedTime.minute }${ timePeriod }`, `H:mm${ timePeriod ? ' A' : '' }` ).format( 'HH:mm (A)' );

		return ! parsedTime.match( /invalid/i ) ? parsedTime : pluginData.localization.time_format_filter.invalid_time; // Invalid Time
	}

	/**
	 * Check if time format is custom or one of the available ones
	 *
	 * @param {string} format
	 *
	 * @return {boolean} True or false
	 */
	isCustomFormat( format ) {
		const { selectedFormat } = this.state;

		return format ? format === 'custom' : selectedFormat === 'custom';
	}

	/**
	 * Check if time is valid
	 *
	 * @param {string} time
	 *
	 * @return {boolean} True or false
	 */
	isValidTime( time ) {
		// Time can either be a formatted time or an invalid/incomplete time notice
		return time !== pluginData.localization.time_format_filter.invalid_time && time !== pluginData.localization.time_format_filter.incomplete_time;
	}

	/**
	 * Return modal window content
	 *
	 * @return {ReactElement} JSX markup
	 */
	getContent() {
		const { selectedFormat, customFormat, selectedFormatParsedTime, originalFormatParsedTime } = this.state;

		return (
			<>
				<div className={ styles( 'columns' ) }>
					<div className={ styles( 'column' ) }>
						<p className={ styles( 'is-size-6' ) }>
							{ /* This column contains a time value. */ }
							{ this.isValidTime( originalFormatParsedTime ) ?
								replacePlaceholders( pluginData.localization.time_format_filter.time_recognized, [ `<b>${ this.TIME_VALUE }</b>`, `<b>${ originalFormatParsedTime }</b>` ] ) : // We recognized %s as %s. If this is incorrect, please select one of the available formats or specify your own:
								replacePlaceholders( pluginData.localization.time_format_filter.time_unrecognized, [ `<b>${ this.TIME_VALUE }</b>`, `<b>${ getAvailableFormats()[ DEFAULT_TIME_FORMAT ] }</b>` ] ) // We couldn't recognize %s using the default %s format. Please select one of the other possible formats or specify your own:
							}
						</p>
					</div>
				</div>
				<div className={ styles( 'columns' ) }>
					<div className={ styles( 'column' ) }>

						<div className={ styles( 'control' ) }>
							<div className={ styles( 'select' ) }>
								<select
									value={ selectedFormat }
									onChange={ ( e ) => {
										const { target: { value } } = e;
										const format = ( ! this.isCustomFormat( value ) ) ? value : customFormat;

										this.setState( { selectedFormat: value, selectedFormatParsedTime: this.parseTime( this.TIME_VALUE, format ) } );
									} }
								>
									<option disabled value="">
										{ /* Select Time Format: */ }
										{ pluginData.localization.time_format_filter.select_time_format }
									</option>
									{ _.map( getAvailableFormats(), ( description, format ) => (
										<option key={ format } value={ format }>
											{ description }
										</option>
									) ) }
								</select>
							</div>
						</div>
					</div>
				</div>

				{ this.isCustomFormat() && (
					<div className={ styles( 'columns' ) }>
						<div className={ styles( 'column', 'is-12' ) }>
							<div className={ styles( 'control' ) }>
								<input
									className={ styles( 'input' ) }
									type="text"
									placeholder={ pluginData.localization.time_format_filter.custom_time_format_placeholder } // Custom Time Format
									defaultValue={ customFormat }
									onChange={ ( e ) => {
										const { target: { value } } = e;

										this.setState( { customFormat: value, selectedFormatParsedTime: this.parseTime( this.TIME_VALUE, value ) } );
									} }
								/>
							</div>
							<p className={ styles( 'help' ) }>
								{ replacePlaceholders(
									pluginData.localization.time_format_filter.custom_format_hint, // Use %s for hour, %s for minute, %s for time period, %s to skip a single and and %s to skip multiple characters
									[ '<b><i>%H</i></b>', '<b><i>%M</i></b>', '<b><i>%A</i></b>', '<b><i>~</i></b>', '<b><i>^</i></b>' ],
								) }
							</p>
						</div>
					</div>
				) }

				{ this.state.selectedFormat && (
					<div className={ styles( 'columns' ) }>
						<div className={ styles( 'column' ) }>
							<span className={ styles( 'label', 'is-size-7', 'is-uppercase', 'has-text-weight-normal', 'has-text-grey' ) }>
								{ pluginData.localization.time_format_filter.time_live_preview }
							</span>
							<p
								className={ styles( 'is-size-6' ) }
								title={ replacePlaceholders( pluginData.localization.time_format_filter.time_live_preview_hint, [ `${ this.TIME_VALUE }`, `${ selectedFormatParsedTime }` ] ) } // %s will be interpreted as %s
							>
								<b className={ styles( 'has-text-link' ) }>{ this.TIME_VALUE }</b>
								{ ' ' }
								&rarr;
								{ ' ' }
								{ this.isValidTime( selectedFormatParsedTime ) ?
									<b className={ styles( 'has-text-success' ) }>{ selectedFormatParsedTime }</b> :
									<b className={ styles( 'has-text-danger' ) }>{ selectedFormatParsedTime }</b>
								}
							</p>
						</div>
					</div>
				) }
			</>
		);
	}

	/**
	 * Return modal window footer buttons
	 *
	 * @return {Object[]} Object with buttons data for use in the modal dialog
	 */
	getButtons() {
		const { selectedFormat, customFormat, selectedFormatParsedTime } = this.state;
		const { onClose, onSave } = this.props;

		return [
			{
				label: pluginData.localization.shared.update, // Update
				style: styles( 'is-link' ),
				action: () => {
					const filterData = ( selectedFormat !== 'custom' ) ? selectedFormat : customFormat;

					onSave( { filterData } );
				},
				disabled: ! this.isValidTime( selectedFormatParsedTime ) || ! selectedFormat ? 'disabled' : null,
			},
			{
				label: pluginData.localization.modal.cancel, // Cancel
				action: () => onClose(),
				dismissModal: true,
			},
		];
	}

	/**
	 * Format field value for display in the table
	 *
	 * @param {string} value
	 * @param {string} format
	 *
	 * @return {ReactElement} JSX markup
	 */
	formatField( { value, format } ) {
		const parsedTime = this.parseTime( value, format || DEFAULT_TIME_FORMAT );

		return <>
			{ value }
			{ ' ' }
			<span
				className={ styles( 'is-size-7', this.isValidTime( parsedTime ) ? 'has-text-grey' : 'has-text-danger' ) }
				title={ this.isValidTime( parsedTime ) ? replacePlaceholders( pluginData.localization.time_format_filter.time_live_preview_hint, [ `${ value }`, `${ parsedTime }` ] ) : pluginData.localization.time_format_filter.invalid_time } // %s will be interpreted as %s
			>
				&rarr; { ` ${ parsedTime }` }
			</span>
		</>;
	}

	/**
	 * Render modal window with time formatting options
	 *
	 * @return {ReactElement} JSX markup
	 */
	render() {
		const { formatField } = this.props;

		if ( formatField ) {
			return ( formatField.value ) ? this.formatField( formatField ) : null;
		}

		const props = {
			content: this.getContent(),
			buttons: this.getButtons(),
		};

		return <ModalDialog { ...props } />;
	}
}
