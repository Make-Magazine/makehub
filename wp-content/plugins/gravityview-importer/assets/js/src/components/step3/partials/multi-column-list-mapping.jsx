/* global wp */

import _ from 'lodash';
import classNames from 'classnames/bind';
import ModalDialog from 'js/shared/modal-dialog';
import { pluginData } from 'js/app';
import { replacePlaceholders } from 'js/helpers/string-manipulations';

import appStyles from 'css/app';
import Beacon from 'js/shared/helpscout-beacon';

const styles = classNames.bind( appStyles );
const { Component } = wp.element;

export default class MultiColumnListMapping extends Component {
	csvHeader = this.getCSVHeader();

	state = {
		rowData: this.getRowData(),
		columnNames: [],
	};

	/**
	 * Get CSV header
	 *
	 * @return {Array} CSV column names (header)
	 */
	getCSVHeader() {
		const { importData } = this.props;

		return importData[ 0 ];
	}

	/**
	 * Run on component mount
	 *
	 * @return {void}
	 */
	componentDidMount() {
		const { selectedColumnFields, columnIndex: selectedColumnIndex } = this.props;
		const { rowData } = this.state;
		const { mappedColumnNames: columnNames } = selectedColumnFields[ selectedColumnIndex ]._data;

		let updatedColumnNames = this.getColumnNames( rowData );

		if ( columnNames && updatedColumnNames.length < columnNames.length ) {
			updatedColumnNames = columnNames.slice( 0, updatedColumnNames.length - columnNames.length );
		} else if ( columnNames && updatedColumnNames.length === columnNames.length ) {
			updatedColumnNames = columnNames;
		}

		this.setState( { columnNames: updatedColumnNames } );
	}

	/**
	 * Detect column count by checking for | in each associated row and return an array with column names
	 *
	 * @param {Array} rowData Column-to-row mapping data
	 *
	 * @return {Array} Column names data
	 */
	getColumnNames( rowData ) {
		const { importData, form } = this.props;

		const columnNames = [];
		let columnCount = 0;

		// Existing forms draw column names from the form itself
		if ( form.type !== 'new' ) {
			return columnNames;
		}

		_.each( rowData, ( data ) => {
			let rowColumnCount = data.row === '' ? 0 : ( importData[ 1 ][ data.row ].match( /\|/g ) || [] ).length;

			rowColumnCount = rowColumnCount ? rowColumnCount + 1 : rowColumnCount; // column count is always # of | characters + 1

			columnCount = rowColumnCount > columnCount ? rowColumnCount : columnCount;
		} );

		_.times( columnCount, ( columnIndex ) => {
			columnNames[ columnIndex ] = pluginData.localization.multi_column_lists.column.replace( '%s', columnIndex + 1 ); /* Column %s */
		} );

		return columnNames;
	}

	/**
	 * Detect row data or return an array with empty row
	 *
	 * @return {Array} Row data
	 */
	getRowData() {
		const { selectedColumnFields, columnIndex: selectedColumnIndex, form } = this.props;
		const { mappedColumns } = selectedColumnFields[ selectedColumnIndex ]._data;
		selectedColumnFields[ selectedColumnIndex ]._data = { ...selectedColumnFields[ selectedColumnIndex ]._data };

		if ( mappedColumns ) {
			return _.map( mappedColumns, ( row ) => ( { row } ) );
		}

		const noMatchReturnValue = [ { row: form.type === 'new' ? '' : selectedColumnIndex } ];
		const fieldName = RegExp( '^(.*?) \\d+$' ).exec( this.csvHeader[ selectedColumnIndex ] );

		if ( ! fieldName ) {
			return noMatchReturnValue;
		}

		// We can detect row data as GF exports it by appending row # to the field name (e.g., "Column 1", "Column 2", etc.)
		const matchedColumns = _.map( this.csvHeader, ( column, columnIndex ) => {
			const match = RegExp( `${ fieldName[ 1 ] } (\\d+)` ).exec( column );

			// Use temporary property if CSV columns are not in order
			return ( match ) ? { row: columnIndex, order: parseInt( match[ 1 ], 10 ) } : null;
		} ).filter( Boolean );

		return matchedColumns.length ? _.map( _.keyBy( matchedColumns, 'order' ), ( data ) => ( { row: data.row } ) ) : noMatchReturnValue;
	}

	/**
	 * Add new data row
	 */
	addRow() {
		const { rowData } = this.state;

		rowData.push( { row: '' } );

		this.setState( { rowData } );
	}

	/**
	 * Remove data row
	 *
	 * @param {number} rowIndex
	 */
	removeRow( rowIndex ) {
		const { rowData, columnNames } = this.state;

		rowData.splice( rowIndex, 1 );

		let updatedColumnNames = this.getColumnNames( rowData );

		updatedColumnNames = ( updatedColumnNames.length < columnNames.length ) ?
			columnNames.slice( 0, updatedColumnNames.length - columnNames.length ) :
			columnNames;

		this.setState( { rowData, columnNames: updatedColumnNames } );
	}

	/**
	 * Handle changes to the import condition
	 *
	 * @param {number} rowIndex
	 * @param {number} columnIndex
	 */
	handleRowColumnAssociationChange( rowIndex, columnIndex ) {
		const { rowData, columnNames } = this.state;

		rowData[ rowIndex ] = { row: isNaN( columnIndex ) ? '' : columnIndex };

		let updatedColumnNames = this.getColumnNames( rowData );

		if ( updatedColumnNames.length < columnNames.length ) {
			updatedColumnNames = columnNames.slice( 0, updatedColumnNames.length - columnNames.length );
		} else if ( columnNames.length || updatedColumnNames.length > columnNames.length ) {
			_.each( columnNames, ( name, index ) => {
				updatedColumnNames[ index ] = name;
			} );
		} else if ( updatedColumnNames.length === columnNames.length ) {
			updatedColumnNames = columnNames;
		}

		this.setState( { rowData, columnNames: updatedColumnNames } );
	}

	/**
	 * Handle changes to the column name
	 *
	 * @param {number} columnIndex
	 * @param {string} columnName
	 */
	handleColumnNameChange( columnIndex, columnName ) {
		const { columnNames } = this.state;

		columnNames[ columnIndex ] = columnName;

		this.setState( { columnNames } );
	}

	/**
	 * Return modal window content
	 *
	 * @return {ReactElement} JSX markup
	 */
	getContent() {
		const { form } = this.props;
		const { columnNames } = this.state;
		const sortedRowData = _.keyBy( this.state.rowData, 'row' );

		return (
			<div className={ styles( 'multi-column-list-mapping' ) }>
				<div className={ styles( 'columns' ) }>
					<div className={ styles( 'column' ) }>
						{ form.type === 'new' && (
							<h3 className={ styles( 'is-size-6', 'has-text-weight-bold' ) }>
								{ /* If this List field will not have Mutliple Columns, click Cancel. */ }
								{ pluginData.localization.multi_column_lists.maybe_cancel }
							</h3>
						) }
						<p className={ styles( 'is-size-6' ) }>
							{ /* A list field can have multiple rows of data that are exported by Gravity Forms as separate columns in a CSV file. */ }
							{ pluginData.localization.multi_column_lists.list_field_description }

							<Beacon articleId={ ( form.type === 'new' ? '5d39382104286347867565e7' : '5d3933cd2c7d3a2ec4bf648e' ) } beaconType="sidebar" />
						</p>
					</div>
				</div>

				<div
					className={ styles( 'is-divider' ) }
					data-content={ pluginData.localization.multi_column_lists.list_rows } /* List Rows */
				/>

				<p className={ styles( 'is-size-6' ) }>
					{ pluginData.localization.multi_column_lists.select_rows }
				</p>

				<br />

				{ _.map( this.state.rowData, ( rowData, rowIndex ) => {
					return (
						<div key={ `row_${ rowIndex }` } className={ styles( 'columns' ) }>
							<div className={ styles( 'column' ) }>
								<div className={ styles( 'control', 'is-flex', 'center' ) }>

									<p className={ styles( 'is-size-6' ) }>
										{ /* Row %s */ }
										{ replacePlaceholders( pluginData.localization.multi_column_lists.row, [ `#<b>${ rowIndex + 1 }</b>` ] ) }
									</p>
									<div className={ styles( 'select', 'is-size-6' ) }>
										<select
											data-automation-id={ `row_column_mapping_${ rowIndex + 1 }` }
											value={ this.state.rowData[ rowIndex ].row }
											onChange={ ( e ) => this.handleRowColumnAssociationChange( rowIndex, parseInt( e.target.value, 10 ) ) }
										>
											<option value="">
												{ /* Select a CSV Column */ }
												{ pluginData.localization.multi_column_lists.select_csv_column }
											</option>
											{ _.map( this.csvHeader, ( column, columnIndex ) =>
												<option
													key={ `row-${ rowIndex }-column-${ columnIndex }` }
													value={ columnIndex }
													disabled={ sortedRowData[ columnIndex ] }
												>
													{ column }
												</option>,
											) }
										</select>
									</div>
									{ rowIndex === this.state.rowData.length - 1 && _.filter( sortedRowData, Boolean ).length < this.csvHeader.length && rowData.row !== '' && (
										<div
											role="button"
											tabIndex={ 0 }
											onClick={ () => this.addRow() }
											onKeyDown={ ( e ) => e.keyCode === 13 ? this.addRow() : null }
										>
											<figure className={ styles( 'image', 'icon-add' ) } />
										</div>
									) }

									{ ( rowIndex > 0 || this.state.rowData.length > 1 ) && (
										<div
											role="button"
											tabIndex={ 0 }
											onClick={ () => this.removeRow( rowIndex ) }
											onKeyDown={ ( e ) => e.keyCode === 13 ? this.removeRow( rowIndex ) : null }
										>
											<figure className={ styles( 'image', 'icon-remove' ) } />
										</div>
									) }
								</div>
							</div>
						</div>
					);
				} ) }

				{ form.type === 'new' && columnNames.length > 1 && (
					<>
						<div
							className={ styles( 'is-divider' ) }
							data-content={ pluginData.localization.multi_column_lists.list_columns } /* List Columns */
						/>

						<p className={ styles( 'is-size-6' ) }>
							{ /* Based on the associated rows, we detected that your list has %s columns. Please provide a label for each column: */ }
							{ replacePlaceholders( pluginData.localization.multi_column_lists.columns_detected, [ `<b>${ columnNames.length }</b>` ] ) }
						</p>

						<br />

						{ _.map( columnNames, ( columnName, columnIndex ) => {
							return (
								<div
									key={ `column_${ columnIndex + 1 }_name` }
									className={ styles( 'columns' ) }
								>
									<div className={ styles( 'column' ) }>
										<div className={ styles( 'control', 'is-flex', 'center' ) }>
											<p className={ styles( 'is-size-6' ) }>
												{ /* Column %s */ }
												{ replacePlaceholders( pluginData.localization.multi_column_lists.column, [ `#<b>${ columnIndex + 1 }</b>` ] ) }
											</p>
											<div className={ styles( 'is-size-6' ) }>
												<input
													id={ `column-${ columnIndex }` }
													type="text"
													placeholder={ pluginData.localization.multi_input_fields.column_name } /* Column Name */
													className={ styles( 'input' ) }
													data-automation-id={ `column_${ columnIndex + 1 }_name` }
													value={ columnName }
													onChange={ ( e ) => this.handleColumnNameChange( columnIndex, e.target.value ) }
												/>
											</div>
										</div>
									</div>
								</div>
							);
						} ) }
					</>
				) }
			</div>
		);
	}

	/**
	 * Return modal window footer buttons
	 *
	 * @return {Object[]} Object with buttons data for use in the modal dialog
	 */
	getButtons() {
		const { onSave, onClose } = this.props;
		const { selectedColumnFields, columnIndex, defaultFieldId, fieldIdIdentifier, fieldId } = this.props;
		const { rowData, columnNames } = this.state;
		const mappedColumns = _.map( rowData, 'row' ).filter( ( row ) => row !== '' );
		const previouslyMappedColumns = selectedColumnFields[ columnIndex ]._data.mappedColumns;
		const updatedColumnFields = selectedColumnFields;

		return [
			{
				automationId: 'save_mapping',
				label: pluginData.localization.shared.save, // Save
				style: styles( 'is-link' ),
				action: () => {
					_.each( rowData, ( data ) => {
						// If the column was previously mapped to different column, remove it from that column's mapping object
						const mappedToColumnIndex = /^mapped_(\d+)/.exec( updatedColumnFields[ data.row ][ fieldIdIdentifier ] );

						if ( mappedToColumnIndex && parseInt( mappedToColumnIndex[ 1 ] ) !== columnIndex ) {
							updatedColumnFields[ mappedToColumnIndex[ 1 ] ]._data.mappedColumns = _.filter( updatedColumnFields[ mappedToColumnIndex[ 1 ] ]._data.mappedColumns, ( column ) => column === parseInt( parseInt( mappedToColumnIndex[ 1 ] ) ) );
							if ( ! updatedColumnFields[ mappedToColumnIndex[ 1 ] ]._data.mappedColumns.length ) {
								updatedColumnFields[ mappedToColumnIndex[ 1 ] ]._data.mappedColumns = null;
							}
						}

						// fieldId is the field ID/type that's selected before opening modal and passed through props
						if ( fieldId ) {
							updatedColumnFields[ columnIndex ][ fieldIdIdentifier ] = fieldId;
						}

						if ( data.row === columnIndex ) {
							updatedColumnFields[ columnIndex ]._data.mappedColumns = mappedColumns;
							updatedColumnFields[ columnIndex ]._data.mappedColumnNames = columnNames;
							updatedColumnFields[ columnIndex ].ignore = false;
						} else if ( data.row !== '' ) {
							updatedColumnFields[ data.row ][ fieldIdIdentifier ] = `mapped_${ columnIndex }`;
							updatedColumnFields[ data.row ].ignore = false;
						}
					} );

					const columnsNoLongerMapped = _.difference( previouslyMappedColumns, updatedColumnFields[ columnIndex ]._data.mappedColumns, _.isEqual );

					if ( columnsNoLongerMapped.length ) {
						_.each( columnsNoLongerMapped, ( column ) => {
							updatedColumnFields[ column ] = {
								[ fieldIdIdentifier ]: defaultFieldId,
								ignore: false,
								columnIndex: column,
								_data: {},
							};
						} );
					}

					return onSave( updatedColumnFields );
				},
				disabled: ! mappedColumns.length,
			},
			{
				label: pluginData.localization.modal.cancel, // Cancel
				action: onClose,
				dismissModal: true,
			},
		];
	}

	/**
	 * Render modal window with multi-column list field properties
	 *
	 * @return {ReactElement} JSX markup
	 */
	render() {
		const props = {
			content: this.getContent(),
			buttons: this.getButtons(),
			automationId: 'multi_column_list_mapping_modal',
		};

		return <ModalDialog { ...props } />;
	}
}
