/* global wp */

import _ from 'lodash';
import dayjs from 'dayjs';
import classNames from 'classnames/bind';
import ModalDialog from 'js/shared/modal-dialog';
import { pluginData } from 'js/app';
import { replacePlaceholders } from 'js/helpers/string-manipulations';
import XRegExp from 'xregexp';

import appStyles from 'css/app';

const styles = classNames.bind( appStyles );

const { Component } = wp.element;

export const DEFAULT_DATE_FORMAT = 'm/d/Y';

/**
 * Get an object with available formats: PHP date format as keys, description as values
 *
 * @return {Object} Date formats
 */
const getAvailableFormats = () => {
	const month = pluginData.localization.date_format_filter.mm; // MM
	const day = pluginData.localization.date_format_filter.dd; // DD
	const year = pluginData.localization.date_format_filter.yyyy; // YYYY
	const hour = pluginData.localization.time_format_filter.hh; // HH
	const minute = pluginData.localization.time_format_filter.mm; // MM

	return {
		'm/d/Y': `${ month }/${ day }/${ year }`,
		'd/m/Y': `${ day }/${ month }/${ year }`,
		'd-m-Y': `${ day }-${ month }-${ year }`,
		'd.m.Y': `${ day }.${ month }.${ year }`,
		'Y/m/d': `${ year }/${ month }/${ day }`,
		'Y-m-d': `${ year }-${ month }-${ day }`,
		'Y.m.d': `${ year }.${ month }.${ day }`,
		'Y-m-d G:i': `${ year }-${ month }-${ day } ${ hour }:${ minute }`,
		custom: pluginData.localization.date_format_filter.custom_format,
	};
};

/**
 * Transform date format for API consumption
 *
 * @param {string} format
 *
 * @return {string} Format to be passed to the API as meta
 */
export const transformDateFilterFormatForAPI = ( format ) => {
	if ( ! format ) {
		return DEFAULT_DATE_FORMAT;
	}

	if ( getAvailableFormats()[ format ] ) {
		return format;
	}

	return `regex:(${ btoa( getRegexFromFormat( format ).replace( '(?<', '(?P<' ) ) })`;
};

/**
 * Convert date format to regex
 *
 * @param {string} format
 * @return {string} Regex
 */
const getRegexFromFormat = ( format ) => {
	let parsedFormat = format;

	switch ( format ) {
		case 'm/d/Y':
			parsedFormat = '%M/%D/%YYYY';
			break;
		case 'd/m/Y':
			parsedFormat = '%D/%M/%YYYY';
			break;
		case 'd-m-Y':
			parsedFormat = '%D-%M-%YYYY';
			break;
		case 'd.m.Y':
			parsedFormat = '%D\\.%M\\.%YYYY';
			break;
		case 'Y/m/d':
			parsedFormat = '%YYYY/%M/%D';
			break;
		case 'Y-m-d':
			parsedFormat = '%YYYY-%M-%D';
			break;
		case 'Y.m.d':
			parsedFormat = '%YYYY\\.%M\\.%D';
			break;
		case 'Y-m-d G:i':
			parsedFormat = '%YYYY-%M-%D \\d+:\\d+';
	}

	return parsedFormat
		.replace( /\^/g, '.*?' )
		.replace( /\~/g, '.' )
		.replace( /%M/, '(?<month>\\d{1,2}|jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)' )
		.replace( /%D/, '(?<day>\\d{1,2})' )
		.replace( /%YYYY/, '(?<year>\\d{4})' )
		.replace( /%Y/, '(?<year>\\d{2}(?:\\d{2})?)' );
};

export default class DateFormatFilter extends Component {
	constructor( props ) {
		super( props );

		if ( this.props.formatField ) {
			return;
		}

		this.DATE_VALUE = this.getDateValue();

		const _parsedDate = this.parseDate( this.DATE_VALUE, DEFAULT_DATE_FORMAT );

		this.state = {
			selectedFormat: '',
			customFormat: '%M/%D/%Y',
			originalFormatParsedDate: _parsedDate,
			selectedFormatParsedDate: _parsedDate,
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
		const originalFormatParsedDate = this.parseDate( this.DATE_VALUE, filterData );

		if ( ! getAvailableFormats()[ filterData ] ) {
			this.setState( {
				selectedFormat: 'custom',
				customFormat: filterData,
				originalFormatParsedDate,
				selectedFormatParsedDate: originalFormatParsedDate,
			} );
		} else {
			this.setState( {
				selectedFormat: filterData,
				originalFormatParsedDate,
				selectedFormatParsedDate: originalFormatParsedDate,
			} );
		}
	}

	/**
	 * Get date field value from import data (extract)
	 *
	 * @return {string} Date field value
	 */
	getDateValue() {
		const { importData, columnIndex } = this.props;
		// Import data is an array of rows with data for each column: [[column1value,column2value],...]
		// Destructure the object to get the first 2 rows
		const { 0: { [ columnIndex ]: dateValue1 = '' }, 1: { [ columnIndex ]: dateValue2 = '' } } = importData;

		// Check if the second row is empty or the first row is a header (i.e., does not contain a sequence of numbers)
		return ( ! dateValue2 || dateValue1.match( /\d\d/ ) ) ? dateValue1 : dateValue2;
	}

	/**
	 * Parse date from date field value using custom format
	 *
	 * @param {string} date
	 * @param {string} format
	 *
	 * @return {string} Parsed and formatted date or an invalid date notice
	 */
	parseDate( date, format ) {
		if ( ! format || ! format.trim().length ) {
			return pluginData.localization.date_format_filter.invalid_date; // Invalid Date
		}

		const matchedDate = XRegExp.exec( date, XRegExp( getRegexFromFormat( format ), 'i' ) );

		// Get numeric month from the month name value
		if ( matchedDate && matchedDate.month && ! parseInt( matchedDate.month, 10 ) ) {
			matchedDate.month = [ 'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec' ].indexOf( matchedDate.month.toLowerCase() ) + 1;
		}

		if ( ! matchedDate ) {
			return pluginData.localization.date_format_filter.invalid_date; // Invalid Date
		} else if ( ! matchedDate.year || ! matchedDate.month || ! matchedDate.day ) {
			return pluginData.localization.date_format_filter.incomplete_date; // Date field must contain day, month and year values
		}

		const parsedDate = dayjs( `${ matchedDate.month }/${ matchedDate.day }/${ matchedDate.year }` ).format( 'MMMM DD, YYYY' );

		return ! date.match( /invalid/i ) ? parsedDate : pluginData.localization.date_format_filter.invalid_date; // Invalid Date
	}

	/**
	 * Conditionally truncate date value
	 *
	 * @param {string} dateValue
	 *
	 * @return {string} Truncated or original date value
	 */
	truncateDateValue( dateValue ) {
		return dateValue.length > 20 ? `${ dateValue.substring( 0, 20 ) }...` : dateValue;
	}

	/**
	 * Check if date format is custom or one of the available ones
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
	 * Check if date is valid
	 *
	 * @param {string} date
	 *
	 * @return {boolean} True or false
	 */
	isValidDate( date ) {
		// Date can either be a formatted date or an invalid/incomplete date notice
		return date !== pluginData.localization.date_format_filter.invalid_date && date !== pluginData.localization.date_format_filter.incomplete_date;
	}

	/**
	 * Return modal window content
	 *
	 * @return {ReactElement} JSX markup
	 */
	getContent() {
		const { selectedFormat, customFormat, selectedFormatParsedDate, originalFormatParsedDate } = this.state;

		return (
			<>
				<div className={ styles( 'columns' ) }>
					<div className={ styles( 'column' ) }>
						<p className={ styles( 'is-size-6' ) }>
							{ /* This column contains a date. */ }
							{ this.isValidDate( originalFormatParsedDate ) ?
								replacePlaceholders( pluginData.localization.date_format_filter.date_recognized, [ `<b>${ this.truncateDateValue( this.DATE_VALUE ) }</b>`, `<b>${ originalFormatParsedDate }</b>` ] ) : // We recognized %s as %s. If this is incorrect, please select one of the available formats or specify your own:
								replacePlaceholders( pluginData.localization.date_format_filter.date_unrecognized, [ `<b>${ this.truncateDateValue( this.DATE_VALUE ) }</b>`, `<b>${ getAvailableFormats()[ DEFAULT_DATE_FORMAT ] }</b>` ] ) // We couldn't recognize %s using the default %s format. Please select one of the other possible formats or specify your own:
							}
						</p>
					</div>
				</div>
				<div className={ styles( 'columns' ) }>
					<div className={ styles( 'column' ) }>

						<div className={ styles( 'control' ) }>
							<div className={ styles( 'select' ) }>
								<select
									data-automation-id="date_format_selection"
									value={ selectedFormat }
									onChange={ ( e ) => {
										const { target: { value } } = e;
										const format = ( ! this.isCustomFormat( value ) ) ? value : customFormat;

										this.setState( { selectedFormat: value, selectedFormatParsedDate: this.parseDate( this.DATE_VALUE, format ) } );
									} }
								>
									<option disabled value="">
										{ /* Select Date Format: */ }
										{ pluginData.localization.date_format_filter.select_date_format }
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
						<div className={ styles( 'column', 'is-8' ) }>
							<div className={ styles( 'control' ) }>
								<input
									className={ styles( 'input' ) }
									data-automation-id="custom_data_format_input"
									type="text"
									placeholder={ pluginData.localization.date_format_filter.custom_date_format_placeholder } // Custom Date Format
									defaultValue={ customFormat }
									onChange={ ( e ) => {
										const { target: { value } } = e;

										this.setState( { customFormat: value, selectedFormatParsedDate: this.parseDate( this.DATE_VALUE, value ) } );
									} }
								/>
							</div>
							<p className={ styles( 'help' ) }>
								{ replacePlaceholders(
									pluginData.localization.date_format_filter.custom_format_hint, // Use %s for day, %s for month, %s for year, %s to skip a single and and %s to skip multiple characters. Day, month, and year are all required by Gravity Forms.
									[ '<b><i>%D</i></b>', '<b><i>%M</i></b>', '<b><i>%Y</i></b>', '<b><i>~</i></b>', '<b><i>^</i></b>' ],
								) }
							</p>
						</div>
					</div>
				) }

				{ this.state.selectedFormat && (
					<div className={ styles( 'columns' ) }>
						<div className={ styles( 'column' ) }>
							<span className={ styles( 'label', 'is-size-7', 'is-uppercase', 'has-text-weight-normal', 'has-text-grey' ) }>
								{ pluginData.localization.date_format_filter.date_live_preview /* Live Preview: */ }
							</span>
							<p
								className={ styles( 'is-size-6' ) }
								title={ replacePlaceholders( pluginData.localization.date_format_filter.date_live_preview_hint, [ `${ this.truncateDateValue( this.DATE_VALUE ) }`, `selectedFormatParsedDate` ] ) } // %s is recognized as %s
								data-automation-id="live_preview"
							>
								<b className={ styles( 'has-text-success' ) }>{ this.truncateDateValue( this.DATE_VALUE ) }</b>
								{ ' ' }
								&rarr;
								{ ' ' }
								{ this.isValidDate( selectedFormatParsedDate ) ?
									<b className={ styles( 'has-text-success' ) }>{ selectedFormatParsedDate }</b> :
									<b className={ styles( 'has-text-danger' ) }>{ selectedFormatParsedDate }</b>
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
		const { selectedFormat, customFormat, selectedFormatParsedDate } = this.state;
		const { onClose, onSave } = this.props;

		return [
			{
				automationId: 'update_format',
				label: pluginData.localization.shared.update, // Update
				style: styles( 'is-link' ),
				action: () => {
					const filterData = ( selectedFormat !== 'custom' ) ? selectedFormat : customFormat;

					onSave( { filterData } );
				},
				disabled: ! this.isValidDate( selectedFormatParsedDate ) || ! selectedFormat ? 'disabled' : null,
			},
			{
				automationId: 'close_modal',
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
		const parsedDate = this.parseDate( value, format || DEFAULT_DATE_FORMAT );

		return <>
			{ value }
			{ ' ' }
			<span
				className={ styles( 'is-size-7', this.isValidDate( parsedDate ) ? 'has-text-grey' : 'has-text-danger' ) }
				title={ this.isValidDate( parsedDate ) ? replacePlaceholders( pluginData.localization.date_format_filter.date_live_preview_hint, [ `${ value }`, `${ parsedDate }` ] ) : pluginData.localization.date_format_filter.invalid_date } // %s will be interpreted as %s
			>
				&rarr; { ` ${ parsedDate }` }
			</span>
		</>;
	}

	/**
	 * Render modal window with date formatting options
	 *
	 * @return {ReactElement} JSX markup
	 */
	render() {
		const { formatField } = this.props;

		if ( formatField ) {
			return ( formatField.value ) ? this.formatField( formatField ) : null;
		}

		const props = {
			automationId: 'date_filter_modal',
			content: this.getContent(),
			buttons: this.getButtons(),
		};

		return <ModalDialog { ...props } />;
	}
}
