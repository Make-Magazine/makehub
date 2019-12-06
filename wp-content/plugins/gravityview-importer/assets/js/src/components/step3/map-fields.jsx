/* global wp */

import _ from 'lodash';
import { pluginData } from 'js/app';
import { AJAX, API } from 'js/helpers/server-requests';
import classNames from 'classnames/bind';
import MultiInputMapping, { formatMultiInputFieldType, isMultiInputField } from './partials/multi-input-mapping';
import DateFormatFilter, { transformDateFilterFormatForAPI } from './partials/date-format-filter';
import TimeFormatFilter, { transformTimeFilterFormatForAPI } from './partials/time-format-filter';
import EntryNotesFilter from './partials/entry-notes-filter';
import ListJsonFilter, { isValidListJson } from './partials/list-json-filter';
import MultiColumnListMapping from './partials/multi-column-list-mapping';
import ErrorNotice from 'js/shared/error-notice';
import ModalDialog from 'js/shared/modal-dialog';
import Beacon from 'js/shared/helpscout-beacon';
import { replacePlaceholderLinks, replacePlaceholders, unescapeString } from 'js/helpers/string-manipulations';
import { getFormField, getFormFieldLabel, multiInputTypeRegex } from 'js/helpers/form-field-manipulations';

import appStyles from 'css/app';

const styles = classNames.bind( appStyles );
const { createRef, Component } = wp.element;

const FIELD_FILTERS = {
	dateFormat: DateFormatFilter,
	timeFormat: TimeFormatFilter,
	entryNotes: EntryNotesFilter,
	listJson: ListJsonFilter,
};

/**
 * Map CSV data to form fields
 */
export default class MapFields extends Component {
	STEP_ROUTE_ID = 'step3MapFields';
	DEFAULT_COLUMN_FIELD = '';
	DEFAULT_NEW_FIELD_TYPE = 'text';
	IGNORE_FIELD_ID = 'do_not_import';
	DATA_PREVIEW_ROWS = 10;
	DOM = {
		addNewFormFieldButton: createRef(),
	};

	routeParameters = this.props.context.routeChangeParameters || {};

	state = {
		initialized: false,
		processing: {
			processed: false,
			step: 1,
			error: null,
		},
		selectedColumnFields: [], // array of objects with data on each column [{id, columnIndex, label}]
		importData: [], // array of objects with CSV preview data (excerpt)
		formFields: {},
		importBatchId: null,
		multiInputFields: {},
	};

	/**
	 * Run on component mount
	 *
	 * @return {void}
	 */
	componentDidMount() {
		const { importData: { form, schema }, progress: { stepData }, routes, defaultRoute } = this.props.context;

		// Navigate to form selection if required data is not set
		if ( _.isEmpty( stepData.step2SelectForm ) ) {
			return this.props.history.push( routes[ defaultRoute ].path );
		}

		// Check if we're restarting interrupted import task and update state with previously saved progress data
		if ( ! _.isEmpty( stepData[ this.STEP_ROUTE_ID ] ) && ! this.routeParameters.recreateBatch ) {
			const savedProgress = stepData[ this.STEP_ROUTE_ID ];

			// If we're returning from the "import data" step and the new form has been created, update column mapping to use field IDs instead of field types
			if ( _.isEmpty( savedProgress.formFields ) && ! _.isEmpty( form.fields ) ) {
				savedProgress.formFields = form.fields;
				savedProgress.selectedColumnFields = this.convertSchemaToSelectedColumnFields( schema, savedProgress.selectedColumnFields );
			}

			return this.setState( savedProgress, () => {
				this.saveCurrentProgress();

				if ( ! this.state.processing.processed ) {
					this.bootstrapFieldMapping();
				}
			} );
		}

		this.setState( {
			initialized: true,
		}, this.bootstrapFieldMapping );
	}

	/**
	 * Save state to local storage. This allows us to resume interrupted task.
	 */
	saveCurrentProgress() {
		this.props.context.saveCurrentProgress( this.state, this.STEP_ROUTE_ID );
	}

	/**
	 * Get form fields, create import batch and fetch first XX rows
	 */
	bootstrapFieldMapping() {
		// Step 1 - get form fields
		const step1GetFormFields = () => AJAX.post( {
			requestData: {
				action: pluginData.action_form_data,
				formId: this.props.context.importData.form.id,
			},
			responseHandler: ( res ) => {
				return res.success === true;
			},
			successHandler: ( res ) => {
				const { processing } = this.state;
				const { importData } = this.props.context;
				const { data: { form_feeds: formFeeds, form_fields: formFields } } = res;

				processing.step = 2;

				importData.form = _.extend( {}, importData.form, { fields: formFields, feeds: formFeeds } );

				this.props.context.setState( { importData } );

				this.setState( {
					formFields,
					processing,
				}, step2CreateImportBatch );
			},
			errorHandler,
		} );

		// Step 2 - create import batch
		const step2CreateImportBatch = () => {
			const requestData = {
				source: this.props.context.importData.file.serverLocation,
			};

			if ( this.props.context.importData.form.type === 'new' ) {
				requestData.form_title = this.props.context.importData.form.title;
			} else {
				requestData.form_id = this.props.context.importData.form.id;
			}

			API.create( {
				requestData,
				responseHandler: ( res ) => {
					return parseInt( res.id, 10 );
				},
				successHandler: ( res ) => {
					const processing = this.state.processing;
					processing.step = 3;

					// Update application state
					const { importData } = this.props.context;
					importData.batchId = res.id;
					this.props.context.setState( { importData } );

					this.setState( {
						importBatchId: res.id,
						processing,
					}, () => {
						this.saveCurrentProgress();

						step3ProcessImportBatch();
					} );
				},
				errorHandler,
			} );
		};

		// Step 3 - process batch, get column and excerpt data
		const step3ProcessImportBatch = () => API.process( {
			requestData: {
				id: this.state.importBatchId,
			},
			responseHandler: ( res ) => {
				return res.meta && res.meta.columns && res.meta.excerpt;
			},
			successHandler: ( res ) => {
				const { importData: { form, schema } } = this.props.context;
				const { processing } = this.state;
				const importData = _.slice( res.meta.excerpt, 0, this.DATA_PREVIEW_ROWS );
				const columnCount = _.size( res.meta.excerpt[ 0 ] );

				const fieldOrTypeIds = form.type === 'new' ?
					_.flatten( _.map( pluginData.field_types, ( type ) => type.inputs && type.virtual_id ? _.keys( type.inputs ) : String( type.id ) ) ) :
					_.flatten( _.map( this.state.formFields, ( field ) => field.inputs ? _.keys( field.inputs ) : String( field.id ) ) );

				const mappedIds = [];

				const selectedColumnFields = _.times( columnCount, ( columnIndex ) => {
					if ( form.type === 'new' ) {
						const type = res.meta.columns[ columnIndex ] && _.includes( fieldOrTypeIds, String( res.meta.columns[ columnIndex ].field ) ) ? String( res.meta.columns[ columnIndex ].field ) : this.DEFAULT_NEW_FIELD_TYPE;
						const labelShouldBeStripped = /(Address|Name) \((.*?)\)/.exec( importData[ 0 ][ columnIndex ] );
						const label = labelShouldBeStripped ? labelShouldBeStripped[ 1 ] : importData[ 0 ][ columnIndex ]; // strip Address and Name prefixes

						return {
							label,
							type,
							columnIndex,
							_data: getFormField( type, pluginData.field_types ),
						};
					}

					let id = res.meta.columns[ columnIndex ] && _.includes( fieldOrTypeIds, String( res.meta.columns[ columnIndex ].field ) ) ? String( res.meta.columns[ columnIndex ].field ) : this.DEFAULT_COLUMN_FIELD;

					// Do not allow columns to have duplicate IDs
					if ( id !== this.DEFAULT_COLUMN_FIELD && _.includes( mappedIds, id ) ) {
						id = this.DEFAULT_COLUMN_FIELD;
					}

					mappedIds.push( id );

					return {
						id,
						columnIndex,
						_data: getFormField( id, form.fields ),
					};
				} );

				processing.processed = true;

				const updateState = ( columnFields ) => {
					// Update state and save to local storage
					this.setState( {
						processing,
						importData,
						selectedColumnFields: columnFields,
					}, () => {
						this.saveCurrentProgress();
						if ( this.routeParameters.goToConfigureStep ) {
							this.goToNextStep();
						}
					} );
				};

				// When schema already exists, it means that mapping was previously done on a new form and we now need to assign proper IDs to each mapped field
				if ( form.type !== 'new' && ! _.isEmpty( schema ) ) {
					_.map( schema, ( data ) => {
						selectedColumnFields[ data.column ].id = data.field;
						selectedColumnFields[ data.column ]._data = getFormField( data.field, form.fields );
					} );

					// Update state and save to local storage
					return updateState( selectedColumnFields );
				}

				// Display warning message when one of the columns is automapped to an Entry ID
				if ( _.includes( mappedIds, 'id' ) ) {
					return this.props.context.displayModalDialog( {
						content:
							<>
								{ /* WARNING! One of the columns in your CSV file maps to a Entry ID. This will update values for existing entries that share the same Entry ID. Click Cancel to create new entries during import. */ }
								<span className={ styles( 'has-text-danger' ) }>{ pluginData.localization.map_fields.warning.toUpperCase() }!</span> { pluginData.localization.map_fields.entry_id_detected } { pluginData.localization.map_fields.entry_overwrite_warning }
							</>,
						buttons: [
							{
								label: pluginData.localization.modal.continue, // Continue
								style: styles( 'is-link' ),
								action: () => {
									this.props.context.dismissModalDialog();
									updateState( selectedColumnFields );
								},
							},
							{
								label: pluginData.localization.modal.cancel, // Cancel
								action: () => {
									const updatedSelectedColumnFields = _.map( selectedColumnFields, ( columnField ) => {
										return ( columnField.id !== 'id' ) ? columnField : {
											...columnField,
											id: this.DEFAULT_COLUMN_FIELD,
											data: {},
										};
									} );

									updateState( updatedSelectedColumnFields );
									this.props.context.dismissModalDialog();
								},
								dismissModal: true,
							},
						],
					} );
				}

				// Update state and save to local storage
				updateState( selectedColumnFields );
			},
			errorHandler,
		} );

		// Handle server errors
		const errorHandler = ( err ) => {
			const { processing } = this.state;

			processing.error = err.message;

			this.setState( { processing } );
		};

		if ( this.routeParameters.recreateBatch ) {
			// If batch is being recreated, update component state with form fields from app state and go straight to batch creation step
			const { importData: { form: { type: formType, fields: formFields } } } = this.props.context;

			if ( formType === 'new' ) {
				step2CreateImportBatch();
			} else if ( ! formFields ) {
				step1GetFormFields();
			} else {
				this.props.context.setState( { routeChangeParameters: {} } );

				this.setState( { formFields }, step2CreateImportBatch );
			}
		} else if ( ! this.state.processing.processed && this.state.processing.step === 3 ) {
			// If the batch is being processed (e.g., user is resuming interrupted API request) then jump straight to processing
			step3ProcessImportBatch();
		} else if ( this.props.context.importData.form.type !== 'new' ) {
			// Get form fields for form or proceed to batch creation for new form
			step1GetFormFields();
		} else {
			step2CreateImportBatch();
		}
	}

	/**
	 * Convert schema used for API requests to selected column fields object used to track column selection in field mapping
	 *
	 * @param {Object} schema
	 * @param {Object} selectedColumnFields
	 *
	 * @return {Object} Updated selected column fields object
	 */
	convertSchemaToSelectedColumnFields( schema, selectedColumnFields ) {
		const keyedSchema = _.keyBy( schema, 'column' );

		return _.map( selectedColumnFields, ( field ) => ( {
			id: keyedSchema[ field.columnIndex ] ? keyedSchema[ field.columnIndex ].field : '',
			columnIndex: field.columnIndex,
			ignore: false,
			_data: {},
		} ) );
	}

	/**
	 * Reset selected column to default field or ignore it altogether
	 *
	 * @param {number} columnIndex Column index
	 *
	 * @return {Object} Column data object
	 */
	clearSelectedColumn( columnIndex ) {
		const { selectedColumnFields, importData } = this.state;
		const selectedColumnField = selectedColumnFields[ columnIndex ];
		const updatedSelectedColumnField = ( this.props.context.importData.form.type === 'new' ) ? { type: this.IGNORE_FIELD_ID, label: importData[ 0 ][ columnIndex ] } : { id: this.DEFAULT_COLUMN_FIELD };

		return {
			...selectedColumnField,
			...updatedSelectedColumnField,
			ignore: false,
			_data: {},
		};
	}

	/**
	 * Update selected column data object
	 *
	 * @param {number|string} columnIndex Column index
	 * @param {number|string} id Form field ID
	 *
	 * @return {Object} Column data object
	 */
	updateSelectedColumn( columnIndex, id ) {
		const { importData: { form } } = this.props.context;

		return {
			id,
			columnIndex,
			ignore: id === this.IGNORE_FIELD_ID,
			_data: getFormField( id, form.fields ),
		};
	}

	/**
	 * Filter multi-input fields object by removing types (or inputs) that are no longer assigned to columns
	 *
	 * @param {Object} multiInputFields
	 * @param {Object} selectedColumnFields
	 *
	 * @return {Object} Filtered multi-input fields
	 */
	filterMultiInputFields( multiInputFields, selectedColumnFields ) {
		const _selectedColumnFields = JSON.stringify( selectedColumnFields );
		const filteredMultiInputFields = _( multiInputFields )
			.map( ( typeData, type ) => {
				const inputs = _.map( typeData, ( inputData, index ) => _selectedColumnFields.match( _.escapeRegExp( `${ type }[${ index }]` ) ) ? inputData : null );

				return ! _.isEmpty( _.pickBy( inputs, _.identity ) ) ? { [ type ]: inputs } : null;
			} )
			.pickBy( _.identity );

		return _.assign( {}, ...filteredMultiInputFields );
	}

	/**
	 * Handle changes to the column field for new form
	 *
	 * @param {number|string} columnIndex Column index
	 *
	 * @return {void}
	 */
	handleNewFormColumnChange( columnIndex ) {
		const { selectedColumnFields, multiInputFields, importData } = this.state;
		const label = this.DOM[ `column-${ columnIndex }-label` ].value || '';
		const type = this.DOM[ `column-${ columnIndex }-type` ].value;
		const ignore = type === this.IGNORE_FIELD_ID;
		const multiInputField = multiInputTypeRegex.exec( type );

		const updateColumnsFieldSelection = () => {
			const { field_types: fieldTypes } = pluginData;
			const updatedSelectedColumnFields = this.processMappedField( selectedColumnFields[ columnIndex ].type, columnIndex, selectedColumnFields );

			updatedSelectedColumnFields[ columnIndex ] = {
				label: ( ignore ) ? updatedSelectedColumnFields[ columnIndex ].label : label,
				type,
				columnIndex,
				ignore,
				multiInputField: multiInputField ?
					{ type: multiInputField[ 1 ], index: parseInt( multiInputField[ 2 ], 10 ) } :
					null,
				_data: getFormField( type, { ...fieldTypes } ),
			};

			if ( type === 'list' && isValidListJson( importData[ 1 ][ columnIndex ] ) ) {
				updatedSelectedColumnFields[ columnIndex ]._data = {
					...updatedSelectedColumnFields[ columnIndex ]._data,
					with_properties: false,
					filter: 'listJson',
				};
			}

			return this.setState( { selectedColumnFields: updatedSelectedColumnFields, multiInputFields: this.filterMultiInputFields( multiInputFields, updatedSelectedColumnFields ) }, this.saveCurrentProgress );
		};

		if ( ! isMultiInputField( type ) ) {
			return updateColumnsFieldSelection();
		}

		// Handle multi-input field types
		const props = {
			column: {
				type,
				index: columnIndex,
				defaultType: this.DEFAULT_NEW_FIELD_TYPE,
			},
			multiInputFields,
			selectedColumnFields,
			onSave: ( { updatedMultiInputFields, updatedSelectedColumnFields } ) => {
				this.setState( {
					selectedColumnFields: updatedSelectedColumnFields,
					multiInputFields: this.filterMultiInputFields( updatedMultiInputFields, updatedSelectedColumnFields ),
				}, () => {
					this.props.context.dismissModalDialog();
					this.saveCurrentProgress();
				} );
			},
			onClose: () => {
				this.props.context.dismissModalDialog();
			},
		};

		// Unset the change as it is handled in the modal window
		this.DOM[ `column-${ columnIndex }-type` ].value = selectedColumnFields[ columnIndex ].type;

		this.props.context.displayModalDialog( {
			component: <MultiInputMapping { ...props } />,
		} );
	}

	/**
	 * Handle changes to the new form column field label
	 *
	 * @param {number|string} columnIndex Column index
	 */
	handleNewFormColumnLabelChange( columnIndex ) {
		const { selectedColumnFields } = this.state;
		const label = this.DOM[ `column-${ columnIndex }-label` ].value || '';

		selectedColumnFields[ columnIndex ].label = label;

		this.setState( { selectedColumnFields }, this.saveCurrentProgress );
	}

	processMappedField( field, columnIndex, selectedColumnFields ) {
		const updatedSelectedColumnFields = [ ...selectedColumnFields ];
		const mappedToColumnIndex = /^mapped_(\d+)/.exec( field );

		if ( mappedToColumnIndex ) {
			updatedSelectedColumnFields[ mappedToColumnIndex[ 1 ] ]._data.mappedColumns = _.filter( updatedSelectedColumnFields[ mappedToColumnIndex[ 1 ] ]._data.mappedColumns, ( row ) => row !== columnIndex );

			if ( ! updatedSelectedColumnFields[ mappedToColumnIndex[ 1 ] ]._data.mappedColumns.length ) {
				updatedSelectedColumnFields[ mappedToColumnIndex[ 1 ] ]._data.mappedColumns = null;
			}
		}

		if ( updatedSelectedColumnFields[ columnIndex ]._data.mappedColumns ) {
			// Unassign previously assigned column + any other columns mapped to it
			_.each( selectedColumnFields[ columnIndex ]._data.mappedColumns, ( mappedColumnIndex ) => {
				updatedSelectedColumnFields[ mappedColumnIndex[ 1 ] ] = this.clearSelectedColumn( mappedColumnIndex );
			} );
		}
		return updatedSelectedColumnFields;
	}

	/**
	 * Handle changes to the column field for existing form
	 *
	 * @param {number} columnIndex Column index
	 * @param {Object} newFieldId New column value
	 *
	 * @return {void}
	 */
	handleExistingFormColumnChange( columnIndex, newFieldId ) {
		const { selectedColumnFields, importData } = this.state;

		const previousFieldId = selectedColumnFields[ columnIndex ].id || this.DEFAULT_COLUMN_FIELD;

		const updateColumnFieldsSelection = () => {
			const updatedSelectedColumnFields = this.processMappedField( previousFieldId, columnIndex, selectedColumnFields );

			updatedSelectedColumnFields[ columnIndex ] = this.updateSelectedColumn( columnIndex, newFieldId );

			if ( updatedSelectedColumnFields[ columnIndex ]._data.type === 'list' && isValidListJson( importData[ 1 ][ columnIndex ] ) ) {
				updatedSelectedColumnFields[ columnIndex ]._data = {
					...updatedSelectedColumnFields[ columnIndex ]._data,
					with_properties: false,
					list_choices: false,
					filter: 'listJson',
				};
			}

			return this.setState( { selectedColumnFields: updatedSelectedColumnFields }, () => {
				if ( updatedSelectedColumnFields[ columnIndex ]._data.list_choices ) {
					this.displayMultiColumnListModal( columnIndex );
					this.saveCurrentProgress();
				}
			} );
		};

		// Find column that's configured with the same field and display duplicate field warning message
		const alreadyAssignedColumnField = _.reject( selectedColumnFields, ( field ) => field.id !== newFieldId )[ 0 ] || null;

		if ( alreadyAssignedColumnField && newFieldId !== this.DEFAULT_COLUMN_FIELD && newFieldId !== this.IGNORE_FIELD_ID ) {
			return this.props.context.displayModalDialog( {
				content: pluginData.localization.map_fields.duplicate_column_field, // This field is already assigned to another column. Do you want to re-assign it?
				buttons: [
					{
						label: pluginData.localization.map_fields.reassign, // Re-assign
						style: styles( 'is-link' ),
						action: () => {
							// Reset previously assigned column + any other columns that are mapped to it
							const updatedSelectedColumnFields = _.map( selectedColumnFields, ( columnField ) => {
								return ( columnField.columnIndex === alreadyAssignedColumnField.columnIndex || columnField.id === `mapped_${ alreadyAssignedColumnField.columnIndex }` ) ?
									this.clearSelectedColumn( columnField.columnIndex ) :
									columnField;
							} );

							updatedSelectedColumnFields[ columnIndex ] = this.updateSelectedColumn( columnIndex, newFieldId );

							if ( updatedSelectedColumnFields[ columnIndex ]._data.type === 'list' && isValidListJson( importData[ 1 ][ columnIndex ] ) ) {
								updatedSelectedColumnFields[ columnIndex ]._data = {
									...updatedSelectedColumnFields[ columnIndex ]._data,
									with_properties: false,
									list_choices: false,
									filter: 'listJson',
								};
							}

							this.setState( { selectedColumnFields: updatedSelectedColumnFields }, () => {
								this.props.context.dismissModalDialog();

								this.saveCurrentProgress();

								if ( updatedSelectedColumnFields[ columnIndex ]._data.list_choices ) {
									this.displayMultiColumnListModal( columnIndex );
								}
							} );
						},
					},
					{
						label: pluginData.localization.modal.cancel, // Cancel
						action: () => {
							this.DOM[ `column-${ columnIndex }` ].value = previousFieldId;
							this.props.context.dismissModalDialog();
						},
						dismissModal: true,
					},
				],
			} );
		}

		// This is a virtual option that should not be selected
		if ( newFieldId === 'add_form_field' ) {
			this.DOM[ `column-${ columnIndex }` ].value = previousFieldId;

			return this.setState( {
				newField: {
					label: importData[ 0 ][ columnIndex ],
					type: this.DEFAULT_NEW_FIELD_TYPE,
					columnIndex,
				},
			} );
		}

		// Display warning message when Entry ID field is selected
		if ( newFieldId === 'id' ) {
			return this.props.context.displayModalDialog( {
				content:
					<>
						{ /* WARNING! This will update values for existing entries that share the same Entry ID. Click Cancel to create new entries during import. */ }
						<span className={ styles( 'has-text-danger' ) }>{ pluginData.localization.map_fields.warning.toUpperCase() }!</span> { pluginData.localization.map_fields.entry_overwrite_warning }
					</>,
				buttons: [
					{
						label: pluginData.localization.modal.continue, // Continue
						style: styles( 'is-link' ),
						action: () => {
							this.props.context.dismissModalDialog();
							updateColumnFieldsSelection();
						},
					},
					{
						label: pluginData.localization.modal.cancel, // Cancel
						action: () => {
							this.DOM[ `column-${ columnIndex }` ].value = previousFieldId;
							this.props.context.dismissModalDialog();
						},
						dismissModal: true,
					},
				],
			} );
		}

		updateColumnFieldsSelection();
	}

	/**
	 * Handle changes to the new field label/type
	 *
	 * @param {string} key
	 * @param {string} value
	 */
	handleNewFormFieldChange( key, value ) {
		const newField = this.state.newField || {};
		newField[ key ] = value;

		this.setState( { newField } );
	}

	/**
	 * Add new form field
	 *
	 * @param {Object} event DOM event
	 */
	addFormField( event ) {
		// Persist synthetic event so that it could be accessed in the AJAX promise
		event.persist();

		event.target.classList.add( styles( 'is-loading' ) );

		let { type } = this.state.newField;
		let inputId;

		if ( type.match( /\./ ) ) {
			inputId = type.split( '.' )[ 1 ];
			type = type.split( '.' )[ 0 ];
		}
		AJAX.post( {
			requestData: {
				action: pluginData.action_add_form_field,
				formId: this.props.context.importData.form.id,
				fieldLabel: this.state.newField.label,
				fieldType: type,
			},
			responseHandler: ( res ) => {
				return res.success === true;
			},
			successHandler: ( res ) => {
				const columnIndex = this.state.newField.columnIndex;

				event.target.classList.remove( styles( 'is-loading' ) );

				// Update the list of available form fields, configure column with the new field and dismiss the dialog window
				this.setState( {
					formFields: res.data.form_fields,
					newField: null,
				}, () => {
					const { importData } = this.props.context;
					const newFieldId = ( inputId ) ? `${ res.data.field_id }.${ inputId }` : res.data.field_id;

					importData.form.fields = res.data.form_fields;

					this.props.context.setState( { importData } );

					this.handleExistingFormColumnChange( columnIndex, newFieldId );
				} );
			},
			errorHandler: ( err ) => {
				this.props.context.displayNotification( {
					type: 'error',
					message: err.message,
				} );

				event.target.classList.remove( styles( 'is-loading' ) );

				this.setState( { newField: null } );
			},
		} );
	}

	/**
	 * Change import source by redirecting to source selection step
	 */
	changeImportSource() {
		this.props.history.push( this.props.context.routes[ this.props.context.defaultRoute ].path );
	}

	/**
	 * Go to import options configuration step
	 */
	goToNextStep() {
		const { importData, progress } = this.props.context;
		const { form } = importData;
		const { selectedColumnFields, multiInputFields } = this.state;

		if ( form.type === 'new' ) {
			// Schema for new forms should not contain field ID but rather field type
			importData.schema = _.chain( selectedColumnFields )
				.map( ( field ) => {
					let fieldName = field.label;

					if ( ! field.label || field.ignore || /^mapped_/.test( field.type ) ) {
						return null;
					}

					const meta = {};

					if ( field.multiInputField ) {
						const [ , type, index, inputId ] = multiInputTypeRegex.exec( field.type );

						// Multi-input fields with dynamic inputs (e.g., checkboxes) will have "inputs" property with data for each input (ID and label)
						fieldName = ( multiInputFields[ type ][ index ].inputs ) ? multiInputFields[ type ][ index ].inputs[ inputId ].label : fieldName;

						meta.parent_name = multiInputFields[ field.multiInputField.type ][ field.multiInputField.index ].label;
					}

					if ( field._data.is_meta ) {
						meta.is_meta = true;
					}

					if ( field._data.filter === 'dateFormat' ) {
						meta.datetime_format = transformDateFilterFormatForAPI( field._data.filterData );
					}

					if ( field._data.filter === 'timeFormat' ) {
						meta.datetime_format = transformTimeFilterFormatForAPI( field._data.filterData );
					}

					if ( field._data.mappedColumns ) {
						meta.list_rows = field._data.mappedColumns;
						meta.list_cols = field._data.mappedColumnNames;
					}

					return {
						column: field.columnIndex,
						field: field.type,
						name: fieldName,
						meta,
					};
				} )
				.compact()
				.value();
		} else {
			importData.schema = _.chain( selectedColumnFields )
				.map( ( field ) => {
					if ( ! field.id || field.ignore || field.id === this.DEFAULT_COLUMN_FIELD || /^mapped_/.test( field.id ) ) {
						return null;
					}

					const meta = {};

					if ( field._data.is_meta ) {
						meta.is_meta = true;
					}

					if ( field._data.filter === 'dateFormat' ) {
						meta.datetime_format = transformDateFilterFormatForAPI( field._data.filterData );
					}

					if ( field._data.filter === 'timeFormat' ) {
						meta.datetime_format = transformTimeFilterFormatForAPI( field._data.filterData );
					}

					if ( field._data.mappedColumns ) {
						meta.list_rows = field._data.mappedColumns;
					}

					return {
						column: field.columnIndex,
						field: field.id,
						meta,
					};
				} )
				.compact()
				.value();
		}

		importData.batchId = this.state.importBatchId;

		progress.stepData.step5ImportData = null;

		this.props.context.setState( { importData, progress } );

		this.props.history.push( this.props.context.routes.step4Configure.path );
	}

	/**
	 * Reset state and restart data mapping process
	 */
	restartDataMapping() {
		this.setState( {
			processing: {
				processed: false,
				step: 1,
				error: null,
			},
			selectedColumnFields: [],
			formFields: {},
			importData: [],
			importBatchId: null,
			multiInputFields: {},
		}, this.bootstrapFieldMapping );
	}

	/**
	 * Check if any columns have been configured by rejecting fields where id (for existing form) or label (for new form) is empty
	 *
	 * @return {boolean} True or false
	 */
	areColumnsConfigured() {
		return ( this.props.context.importData.form.type === 'new' ) ?
			!! _.reject( this.state.selectedColumnFields, ( field ) => ! field.label || field.ignore ).length :
			!! _.reject( this.state.selectedColumnFields, ( field ) => ! field.id || field.ignore ).length;
	}

	/**
	 * Render processing status and error messages
	 *
	 * @return {ReactElement} JSX markup
	 */
	displayImportDataProcessingStatus() {
		const processingSteps = [
			pluginData.localization.map_fields.getting_form_fields,
			pluginData.localization.map_fields.creating_import_task,
			pluginData.localization.map_fields.getting_data,
		];

		return (
			<section className={ styles( 'section', 'processing-status' ) }>
				<div className={ styles( 'container' ) }>
					<div className={ styles( 'hero', 'is-medium' ) }>
						<div className={ styles( 'hero-body' ) }>
							<div className={ styles( 'container', 'has-text-centered' ) }>
								<div>
									<p className={ styles( 'title', 'is-4' ) }>
										{ pluginData.localization.map_fields.processing_import_data }
									</p>
									<p className={ styles( 'is-size-5' ) }>
										{ `${ this.state.processing.step }/${ processingSteps.length } - ${ processingSteps[ this.state.processing.step - 1 ] }` }
										<span className={ styles( 'spinner', 'spinner-custom' ) } />
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		);
	}

	/**
	 * Render new field entry form
	 *
	 * @return {ReactElement} JSX markup
	 */
	displayNewFieldForm() {
		const { newField } = this.state;
		const { field_types: fieldTypes } = pluginData;

		if ( ! newField ) {
			return null;
		}

		const props = {
			content: (
				<div className={ styles( 'columns' ) }>
					<div className={ styles( 'column', 'is-6' ) }>
						<div className={ styles( 'field' ) }>
							<span className={ styles( 'label' ) }>
								{ /* Field Label */ }
								{ pluginData.localization.map_fields.field_label }
							</span>
							<div className={ styles( 'control' ) }>
								<input
									type="text"
									autoFocus={ true }
									className={ styles( 'input' ) }
									defaultValue={ newField.label }
									onChange={ ( e ) => this.handleNewFormFieldChange( 'label', e.target.value ) }
									onKeyDown={ ( e ) => e.keyCode === 13 ? this.DOM.addNewFormFieldButton.current.click() : null }
								/>
							</div>
						</div>

						<div className={ styles( 'field' ) }>
							<span className={ styles( 'label' ) }>
								{ /* Field Type */ }
								{ pluginData.localization.map_fields.field_type }
							</span>
							<div className={ styles( 'select', 'is-fullwidth' ) }>
								<select
									defaultValue={ newField.type }
									onChange={ ( e ) => this.handleNewFormFieldChange( 'type', e.target.value ) }
								>
									{ _.map( fieldTypes, ( field ) => field.multi ?
										<optgroup
											key={ `new-field-type-${ field.id }-optgroup` }
											label={ field.label }
										>
											{ _.map( field.inputs, ( input ) =>
												<option
													key={ `new-field-type-${ field.id }-field-${ input.id }` }
													value={ field.virtualGroup ? input.id : `${ field.id }.${ input.id }` }
												>
													{ input.label }
												</option>,
											) }
										</optgroup> :
										<option
											key={ `new-field-type-${ field.id }` }
											value={ field.id }
										>
											{ field.label }
										</option>,
									) }
								</select>
							</div>
						</div>
					</div>
				</div>
			),
			buttons: [
				{
					ref: this.DOM.addNewFormFieldButton,
					label: pluginData.localization.map_fields.add_form_field, // Add Form Field
					disabled: newField.label.trim() ? null : 'disabled',
					style: styles( 'is-link' ),
					action: ( e ) => this.addFormField( e ),
				},
				{
					label: pluginData.localization.modal.cancel, // Cancel
					action: () => this.setState( { newField: null } ),
					dismissModal: true,
				},
			],
		};

		return <ModalDialog { ...props } />;
	}

	/**
	 * Clear field selection
	 *
	 * @param {string} columnIndex
	 *
	 * @return {void}
	 */
	handleFieldClearClick( columnIndex ) {
		if ( this.props.context.importData.form.type === 'new' ) {
			this.DOM[ `column-${ columnIndex }-type` ].value = this.IGNORE_FIELD_ID;
			this.handleNewFormColumnChange( columnIndex );
		} else {
			this.handleExistingFormColumnChange( columnIndex, this.DEFAULT_COLUMN_FIELD );
		}
	}

	/**
	 * Perform action when field properties icon is clicked
	 *
	 * @param {string} columnIndex
	 *
	 * @return {void}
	 */
	handleFieldPropertiesClick( columnIndex ) {
		const { selectedColumnFields } = this.state;

		if ( selectedColumnFields[ columnIndex ]._data.filter ) {
			return this.displayFieldFilterModal( columnIndex );
		} else if ( selectedColumnFields[ columnIndex ]._data.list_choices || selectedColumnFields[ columnIndex ].type === 'list' ) {
			return this.displayMultiColumnListModal( columnIndex, selectedColumnFields[ columnIndex ].type || selectedColumnFields[ columnIndex ].id );
		}
	}

	/**
	 * Display modal window with field formatting options
	 *
	 * @param {string} columnIndex
	 * @param {string} fieldId Field ID or type
	 *
	 * @return {void}
	 */
	displayMultiColumnListModal( columnIndex, fieldId ) {
		const { selectedColumnFields, importData } = this.state;
		const { form } = this.props.context.importData;

		const props = {
			selectedColumnFields,
			form,
			fieldId,
			fieldIdIdentifier: form.type === 'new' ? 'type' : 'id',
			importData,
			columnIndex,
			defaultFieldId: form.type === 'new' ? this.DEFAULT_NEW_FIELD_TYPE : this.DEFAULT_NEW_FIELD_TYPE,
			onClose: () => {
				this.props.context.dismissModalDialog();
			},
			onSave: ( updatedSelectedColumnFields ) => {
				this.setState( { selectedColumnFields: updatedSelectedColumnFields }, () => {
					this.saveCurrentProgress();
					this.props.context.dismissModalDialog();
				} );
			},
		};

		this.props.context.displayModalDialog( {
			component: <MultiColumnListMapping { ...props } />,
		} );
	}

	/**
	 * Display modal window with field formatting options
	 *
	 * @param {string} columnIndex
	 *
	 * @return {void}
	 */
	displayFieldFilterModal( columnIndex ) {
		const { selectedColumnFields, importData } = this.state;
		const field = selectedColumnFields[ columnIndex ];
		const { _data: { filter } } = field;

		const FilterComponent = FIELD_FILTERS[ filter ];

		const props = {
			field,
			importData,
			columnIndex,
			onClose: () => {
				this.props.context.dismissModalDialog();
			},
			onSave: ( { filterData } ) => {
				selectedColumnFields[ columnIndex ]._data.filterData = filterData;

				this.setState( { selectedColumnFields }, () => {
					this.props.context.dismissModalDialog();
					this.saveCurrentProgress();
				} );
			},
		};

		this.props.context.displayModalDialog( {
			component: <FilterComponent { ...props } />,
		} );
	}

	/**
	 * Render table with import data
	 *
	 * @return {ReactElement} JSX markup
	 */
	displayDataTable() {
		const { selectedColumnFields, formFields } = this.state;
		const { field_types: fieldTypes } = pluginData;

		const { form } = this.props.context.importData;

		const getMappedToColumnId = ( columnIndex ) => {
			const fieldIdIdentifier = form.type === 'new' ? 'type' : 'id';
			const mappedToColumnIndex = /^mapped_(\w+)/.exec( selectedColumnFields[ columnIndex ][ fieldIdIdentifier ] );

			if ( ! mappedToColumnIndex ) {
				return null;
			}

			return ( fieldIdIdentifier === 'type' ) ? mappedToColumnIndex[ 1 ] : selectedColumnFields[ mappedToColumnIndex[ 1 ] ][ fieldIdIdentifier ];
		};

		return (
			<section data-automation-id="field_mapping_container" className={ styles( 'section' ) }>
				<div className={ styles( 'container' ) }>
					<div className={ styles( 'columns' ) }>
						<div className={ styles( 'column' ) }>
							<h3 className={ styles( 'title', 'is-4' ) }>
								{ /* Map CSV columns to %s form fields | Create a form named %s. */ }
								{ replacePlaceholders( form.type !== 'new' ? pluginData.localization.map_fields.map_columns_to_fields : pluginData.localization.map_fields.create_fields_and_map_columns_to_fields, [ `<em>${ this.props.context.importData.form.title }</em>` ] ) }
								<Beacon articleId={ ( form.type === 'new' ? '5d36314804286347867546b9' : '5d38e9782c7d3a2ec4bf62ad' ) } beaconType="modal" />
							</h3>
							<h4 className={ styles( 'is-5' ) }>
								{ /* You can skip columns by selecting "Do Not Import". To create a new form field, select "Add Form Field". | Choose which CSV columns will be turned into form fields. Set the field type to match the data. */ }
								{ form.type !== 'new' ? unescapeString( pluginData.localization.map_fields.map_columns_to_fields_desc ) : pluginData.localization.map_fields.create_fields_and_map_columns_to_fields_desc }
							</h4>
						</div>
					</div>
					<div className={ styles( 'columns' ) }>
						<div className={ styles( 'column', 'is-full' ) }>
							<div className={ styles( 'table-container' ) }>
								<table className={ styles( 'data-mapping', 'table', 'is-striped', 'is-bordered' ) }>
									<thead>
										<tr>
											{ _.times( selectedColumnFields.length, ( columnIndex ) => {
												const mappedToColumnId = getMappedToColumnId( columnIndex );
												const multiInputFieldDetails = multiInputTypeRegex.exec( selectedColumnFields[ columnIndex ].type );
												let multiInputFieldLabel = '';

												if ( multiInputFieldDetails ) {
													const _type = multiInputFieldDetails[ 1 ];
													const _index = parseInt( multiInputFieldDetails[ 2 ], 10 );

													if ( _type !== 'address' && _type !== 'name' ) {
														multiInputFieldLabel = this.state.multiInputFields[ _type ][ _index ].label;
													}
												}

												return (
													<th key={ `column-${ columnIndex }` }>
														{ form.type === 'new' ?
															/* Header for new form */
															<div className={ styles( 'columns' ) }>
																<div className={ styles( 'column' ) }>
																	<div className={ styles( 'field' ) }>
																		<label className={ styles( 'label', 'is-size-7', 'is-uppercase', 'has-text-weight-normal', 'has-text-grey' ) } htmlFor={ `column-${ columnIndex }-label` }>
																			{ pluginData.localization.map_fields.field_label }
																		</label>
																		<div className={ styles( 'control' ) }>
																			<input
																				type="text"
																				className={ styles( 'input', 'has-text-weight-semibold' ) }
																				disabled={ multiInputFieldLabel || selectedColumnFields[ columnIndex ].ignore || mappedToColumnId !== null }
																				ref={ ( el ) => this.DOM[ `column-${ columnIndex }-label` ] = el }
																				value={ multiInputFieldLabel || selectedColumnFields[ columnIndex ].label }
																				id={ `column-${ columnIndex }-label` }
																				placeholder={ pluginData.localization.map_fields.add_new_field_label }
																				onChange={ () => this.handleNewFormColumnLabelChange( columnIndex ) }
																			/>
																		</div>
																	</div>
																	<div className={ styles( 'field-type' ) }>
																		<label className={ styles( 'label', 'is-fullwidth', 'is-size-7', 'is-uppercase', 'has-text-weight-normal', 'has-text-grey' ) } htmlFor={ `column-${ columnIndex }-type` }>
																			{ /* Field Type */ }
																			{ pluginData.localization.map_fields.field_type }
																		</label>
																		<div className={ styles( 'control', 'new-form', { 'with-properties': selectedColumnFields[ columnIndex ]._data.with_properties, 'with-clear': selectedColumnFields[ columnIndex ].type !== this.DEFAULT_COLUMN_FIELD && selectedColumnFields[ columnIndex ].type !== this.IGNORE_FIELD_ID } ) }>
																			<div className={ styles( 'select', 'is-fullwidth' ) }>
																				<select
																					ref={ ( el ) => this.DOM[ `column-${ columnIndex }-type` ] = el }
																					id={ `column-${ columnIndex }-type` }
																					className={ styles( 'has-text-weight-semibold' ) }
																					value={ ( () => {
																						if ( mappedToColumnId !== null ) {
																							return selectedColumnFields[ mappedToColumnId ].type;
																						}

																						return selectedColumnFields[ columnIndex ].type || this.DEFAULT_COLUMN_FIELD;
																					} )() }
																					onChange={ () => this.handleNewFormColumnChange( columnIndex ) }
																				>
																					{ /* Multi-input fields */ }
																					{ _.map( this.state.multiInputFields, ( multiInputField, multiInputFieldType ) => _.map( multiInputField, ( field, fieldIndex ) => field &&
																						<optgroup
																							key={ `column-${ columnIndex }-multi-${ multiInputFieldType }-${ fieldIndex }` }
																							label={ field.label }
																						>
																							{ _.map( field.inputs || fieldTypes[ multiInputFieldType ].inputs, ( input ) => {
																								const columnType = formatMultiInputFieldType( multiInputFieldType, fieldIndex, input.id );

																								return (
																									<option
																										key={ `${ columnIndex }-${ columnType }` }
																										value={ columnType }
																									>
																										{ input.label }
																									</option>
																								);
																							} ) }
																							{ field.inputs && _.size( field.inputs ) < _.size( selectedColumnFields ) && (
																								<>
																									<option disabled="disabled">
																										--------
																									</option>
																									<option value={ formatMultiInputFieldType( multiInputFieldType, fieldIndex, 0 ) }>
																										{ /* Add New Input */ }
																										{ pluginData.localization.multi_input_fields.add_new_input }
																									</option>
																								</>
																							) }
																						</optgroup>,
																					) ) };
																					{ /* All other field types */ }
																					{ _.map( fieldTypes, ( field ) => field.virtualGroup ?
																						<optgroup
																							key={ `column-${ columnIndex }-optgroup-${ field.id }` }
																							label={ field.label }
																						>
																							{ _.map( field.inputs, ( input ) =>
																								<option
																									key={ `column-${ columnIndex }-field-${ input.id }` }
																									value={ input.id }
																								>
																									{ getFormFieldLabel( input.id, fieldTypes ) }
																								</option>,
																							) }
																						</optgroup> :
																						<option
																							key={ `column-${ columnIndex }-type-${ field.id }` }
																							value={ field.id }
																							disabled={ ( () => {
																								if ( mappedToColumnId !== null ) {
																									return field.id === selectedColumnFields[ mappedToColumnId ].type;
																								}

																								return false;
																							} )() }
																						>
																							{ /* Mapped */ }
																							{ mappedToColumnId !== null && field.id === selectedColumnFields[ mappedToColumnId ].type ? `(${ pluginData.localization.map_fields.mapped }) ${ selectedColumnFields[ mappedToColumnId ].label } - ${ field.label }` : field.label }
																						</option>,
																					) }
																					<option disabled="disabled">
																						--------
																					</option>
																					<option value={ this.IGNORE_FIELD_ID }>
																						{ pluginData.localization.map_fields.do_not_import /* Do Not Import */ }
																					</option>
																				</select>
																			</div>
																			{ selectedColumnFields[ columnIndex ].type !== this.DEFAULT_COLUMN_FIELD && selectedColumnFields[ columnIndex ].type !== this.IGNORE_FIELD_ID && (
																				<button
																					aria-label={ pluginData.localization.map_fields.clear_field }
																					title={ pluginData.localization.map_fields.clear_field }
																					className={ styles( 'button-link', 'clear' ) }
																					data-automation-id={ `column_${ columnIndex }_field_clear` }
																					onClick={ () => this.handleFieldClearClick( columnIndex ) }
																				/>
																			) }
																			{ selectedColumnFields[ columnIndex ]._data.with_properties && ! mappedToColumnId && (
																				<button
																					aria-label={ pluginData.localization.map_fields.field_properties }
																					title={ pluginData.localization.map_fields.field_properties }
																					className={ styles( 'button-link', 'properties', 'icon-cog' ) }
																					data-automation-id={ `column_${ columnIndex }_field_properties` }
																					onClick={ () => this.handleFieldPropertiesClick( columnIndex ) }
																				/>
																			) }
																		</div>
																	</div>
																</div>
															</div> :
															/* Header for existing form */
															<>
																<div className={ styles( 'columns', 'is-mobile' ) }>
																	<div className={ styles( 'column', 'is-6' ) }>
																		<p className={ styles( 'label', 'is-size-7', 'is-uppercase', 'has-text-weight-normal', 'has-text-grey' ) }>
																			{ pluginData.localization.map_fields.data_header }
																		</p>
																		<p
																			className={ styles( 'csv-header-data', 'is-size-6' ) }
																			title={ this.state.importData[ 0 ][ columnIndex ] }
																		>
																			{ this.state.importData[ 0 ][ columnIndex ] }
																		</p>
																	</div>
																	<div className={ styles( 'column' ) }>
																		<div className={ styles( 'field', 'field-type' ) }>
																			<label className={ styles( 'label', 'is-fullwidth', 'is-size-7', 'is-uppercase', 'has-text-weight-normal', 'has-text-grey' ) } htmlFor={ `column-${ columnIndex }` }>
																				{ unescapeString( pluginData.localization.map_fields.import_to ) /* Import to... */ }
																			</label>
																			<div className={ styles( 'control', { 'with-properties': selectedColumnFields[ columnIndex ]._data.with_properties, 'with-clear': selectedColumnFields[ columnIndex ].id !== this.DEFAULT_COLUMN_FIELD && selectedColumnFields[ columnIndex ].id !== this.IGNORE_FIELD_ID } ) }>
																				<div className={ styles( 'select' ) }>
																					<select
																						ref={ ( el ) => this.DOM[ `column-${ columnIndex }` ] = el }
																						id={ `column-${ columnIndex }` }
																						data-automation-id={ `existing-form-field-selection-column-${ columnIndex }` }
																						value={ ( () => {
																							if ( mappedToColumnId !== null ) {
																								return mappedToColumnId;
																							}

																							return selectedColumnFields[ columnIndex ] ? selectedColumnFields[ columnIndex ].id : this.DEFAULT_COLUMN_FIELD;
																						} )() }
																						onChange={ ( e ) => this.handleExistingFormColumnChange( columnIndex, e.target.value ) }
																					>
																						<option value={ this.DEFAULT_COLUMN_FIELD }>
																							{ pluginData.localization.map_fields.select_form_field /* Select Form Field */ }
																						</option>
																						{ _.map( formFields, ( field ) => field.inputs ?
																							<optgroup
																								key={ `column-${ columnIndex }-optgroup-${ field.id }` }
																								label={ field.label }
																							>
																								{ _.map( field.inputs, ( input ) =>
																									<option
																										key={ `column-${ columnIndex }-field-${ input.id }` }
																										value={ input.id }
																									>
																										{ getFormFieldLabel( input.id, formFields ) }
																									</option>,
																								) }
																							</optgroup> :
																							<option
																								key={ `column-${ columnIndex }-field-${ field.id }` }
																								value={ field.id }
																								disabled={ ( () => {
																									if ( mappedToColumnId !== null ) {
																										return String( field.id ) === String( mappedToColumnId );
																									}

																									return false;
																								} )() }
																							>
																								{ /* Mapped */ }
																								{ mappedToColumnId !== null && String( field.id ) === String( mappedToColumnId ) ? `(${ pluginData.localization.map_fields.mapped }) ` : '' }{ getFormFieldLabel( field.id, formFields ) }
																							</option>,
																						) }
																						<option disabled=" disabled">
																							&mdash;&mdash;&mdash;&mdash;
																						</option>
																						<option value="add_form_field">
																							{ pluginData.localization.map_fields.add_form_field /* Add Form Field */ }
																						</option>
																						<option value={ this.IGNORE_FIELD_ID }>
																							{ pluginData.localization.map_fields.do_not_import /* Do Not Import */ }
																						</option>
																					</select>
																				</div>
																				{ selectedColumnFields[ columnIndex ].id !== this.DEFAULT_COLUMN_FIELD && selectedColumnFields[ columnIndex ].id !== this.IGNORE_FIELD_ID && (
																					<button
																						aria-label={ pluginData.localization.map_fields.clear_field }
																						title={ pluginData.localization.map_fields.clear_field }
																						className={ styles( 'button-link', 'clear' ) }
																						data-automation-id={ `column_${ columnIndex }_field_clear` }
																						onClick={ () => this.handleFieldClearClick( columnIndex ) }
																					/>
																				) }
																				{ selectedColumnFields[ columnIndex ]._data.with_properties && (
																					<button
																						aria-label={ pluginData.localization.map_fields.field_properties }
																						title={ pluginData.localization.map_fields.field_properties }
																						className={ styles( 'button-link', 'properties' ) }
																						data-automation-id={ `column_${ columnIndex }_field_properties` }
																						onClick={ () => this.handleFieldPropertiesClick( columnIndex ) }
																					/>
																				) }
																			</div>
																		</div>
																	</div>
																</div>
															</>
														}
													</th>
												);
											} ) }
										</tr>
									</thead>

									<tbody>
										{ _.map( this.state.importData, ( row, rowIndex ) => rowIndex === 0 ?
											null : // skip the first row
											<tr key={ `row-${ rowIndex }` }>
												{ _.map( row, ( rowData, columnIndex ) => {
													const field = selectedColumnFields[ columnIndex ];

													// field can be empty when the number of elements in a row != the number of elements in the first row (i.e., header)
													if ( ! field ) {
														return null;
													}

													const { _data: { filter, filterData = '' } = {} } = field;

													const FilterComponent = FIELD_FILTERS[ filter ];

													const formattedData = ( filter ) ?
														<FilterComponent formatField={ { value: rowData, format: filterData } } /> :
														rowData;

													const isDisabled = selectedColumnFields[ columnIndex ].ignore || ( form.type !== 'new' && selectedColumnFields[ columnIndex ].id === this.DEFAULT_COLUMN_FIELD );

													return (
														<td
															data-automation-id={ `column_${ columnIndex }_row_${ rowIndex }_data` }
															key={ `column_${ columnIndex }_row_${ rowIndex }_data` }
															className={ styles( { 'has-text-grey-light': isDisabled } ) }
														>
															{ formattedData }
														</td>
													);
												} ) }
											</tr>,
										) }
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</section>
		);
	}

	/**
	 * Render field mapping area
	 *
	 * @return {ReactElement} JSX markup
	 */
	render() {
		// Component is initialized when data is validated. See componentDidMount();
		if ( ! this.state.initialized ) {
			return null;
		}

		// Display status/error during data processing
		if ( ! this.state.processing.processed ) {
			return ! this.state.processing.error ?
				this.displayImportDataProcessingStatus() :
				<ErrorNotice
					error={ pluginData.localization.map_fields.failed_to_process_import_data } // We were unable to process your import data.
					description={ `${ this.state.processing.error } ${ pluginData.localization.shared.try_again_or_contact_support }` }
					buttons={ [
						{
							label: pluginData.localization.shared.try_again, // Try Again
							action: () => this.restartDataMapping(),
						},
					] }
				/>;
		}

		// Display notice when no rows were found
		if ( ! this.state.importData.length ) {
			return <ErrorNotice
				automationId="csv_is_empty_notice"
				error={ pluginData.localization.map_fields.no_import_data_found } // We could not find any data to import.
				description={ pluginData.localization.map_fields.no_import_data_found_explanation } // This can be because your CSV file is malformed, empty or due to a server error. Please try again or select a different import source.
				buttons={ [
					{
						label: pluginData.localization.shared.change_source, // Change Source
						action: () => this.changeImportSource(),
					},
					{
						label: pluginData.localization.shared.try_again, // Try Again
						action: () => this.restartDataMapping(),
					},
				] }
			/>;
		}

		// Display notice when only a header row was detected
		if ( this.state.importData.length === 1 ) {
			const description = replacePlaceholderLinks(
				pluginData.localization.map_fields.only_header_is_found_explanation,
				[ { href: 'https://docs.gravityview.co/article/257-formatting-guide-csv-import' } ],
			);

			return <ErrorNotice
				automationId="csv_with_only_header_notice"
				error={ pluginData.localization.map_fields.only_header_is_found } // Only a header row was detected.
				description={ description } // GravityView requires a header row and at least one data row. Please [link]refer to our guide[/link] for CSV file formatting tips.
				buttons={ [
					{
						automationId: 'change_source',
						label: pluginData.localization.shared.change_source, // Change Source
						action: () => this.changeImportSource(),
					},
				] }
			/>;
		}

		return (
			<div className={ styles( 'map-fields' ) }>
				{ this.displayDataTable() }

				{ this.displayNewFieldForm() }

				<section className={ styles( 'section' ) }>
					<div className={ styles( 'container' ) }>
						<div className={ styles( 'columns' ) }>
							<div className={ styles( 'column' ) }>
								<div className={ styles( 'level' ) }>
									<div className={ styles( 'level-left' ) }>
										<button data-automation-id="continue_with_import" className={ styles( 'button', 'is-link', 'is-primary', 'is-medium' ) } disabled={ this.areColumnsConfigured() ? null : 'disabled' } onClick={ ( e ) => e.target.getAttribute( 'disabled' ) === null && this.goToNextStep() }>
											{ /* Continue With Import */ }
											{ pluginData.localization.shared.continue_with_import }
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
		);
	}
}
