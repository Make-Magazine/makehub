/* global wp */

import _ from 'lodash';
import ModalDialog from 'js/shared/modal-dialog';
import { pluginData } from 'js/app';
import { replacePlaceholders } from 'js/helpers/string-manipulations';
import { multiInputTypeRegex } from 'js/helpers/form-field-manipulations';

import classNames from 'classnames/bind';

import appStyles from 'css/app';

const styles = classNames.bind( appStyles );
const { Component } = wp.element;

/**
 * Return type formatted as per API's requirement
 *
 * @param {string} fieldType
 * @param {string} columnIndex
 * @param {string} inputId
 *
 * @return {string} TYPE[INDEX].INPUT
 */
export const formatMultiInputFieldType = ( fieldType, columnIndex, inputId ) => `${ fieldType }[${ columnIndex }].${ inputId }`;

/**
 * Check if field is multi-input
 *
 * @param {string} fieldType
 *
 * @return {boolean} True or false
 */
export const isMultiInputField = ( fieldType ) => ( fieldType.match( multiInputTypeRegex ) || _.get( pluginData.field_types, `${ fieldType }.inputs` ) || _.get( pluginData.field_types, `${ fieldType }.dynamic_inputs` ) );

/**
 * Map columns to multi-input fields (e.g., Address)
 */
export default class MultiInputMapping extends Component {
	multiInputField = this.getFieldDataFromType( this.props.column.type );

	state = {
		fieldLabel: this.multiInputField.label,
		selectedColumnFields: [ ...this.props.selectedColumnFields ],
		inputs: { ...this.multiInputField.inputs },
	};

	/**
	 * Run on component mount
	 *
	 * @return {void}
	 */
	componentDidMount() {
		// If this is not a new multi-input field, update the column that triggered field mapping
		if ( ! this.multiInputField.isNew ) {
			const { inputs, selectedColumnFields } = this.state;
			const { index, type } = this.props.column;

			// ID of 0 means that we're adding a new input
			if ( this.multiInputField.inputId === 0 ) {
				this.assignColumnFieldsToInputs( selectedColumnFields, inputs );
				return this.addInput();
			}

			const updatedSelectedColumnFields = _.map( selectedColumnFields, ( field ) => {
				// If another column is already configured with this field type, reset it
				if ( field.type === type ) {
					return this.resetColumnField( field );
				}

				return _.assign( {}, field, field.columnIndex === index && {
					type,
					multiInputField: {
						type: this.multiInputField.type,
						index: this.multiInputField.index,
					},
					ignore: false,
				} );
			} );

			if ( this.multiInputField.dynamicInputs ) {
				this.assignColumnFieldsToInputs( updatedSelectedColumnFields, inputs );
			}

			this.setState( { selectedColumnFields: updatedSelectedColumnFields, inputs } );
		}
	}

	/**
	 * Assign selected column fields' indices to inputs based on input IDs contained in fields' type
	 *
	 * @param {Object} selectedColumnFields Selected column fields
	 * @param {Object} fieldInputs Field inputs
	 */
	assignColumnFieldsToInputs( selectedColumnFields, fieldInputs ) {
		_.each( selectedColumnFields, ( field ) => {
			if ( ! field.multiInputField ) {
				return;
			}

			const fieldData = this.getFieldDataFromType( field.type );

			if ( fieldData.type === this.multiInputField.type && fieldData.index === this.multiInputField.index && fieldInputs[ fieldData.inputId ] ) {
				fieldInputs[ fieldData.inputId ].assignedColumn = field.columnIndex;
			}
		} );

		this.setState( { inputs: fieldInputs } );
	}

	/**
	 * Reset column data by setting default type
	 *
	 * @param {Object} column Column data
	 *
	 * @return {Object} Updated column data
	 */
	resetColumnField( column ) {
		const { defaultType } = this.props.column;

		return _.assign( {}, column, {
			type: defaultType,
			multiInputField: null,
			_data: {},
			ignore: false,
		} );
	}

	/**
	 * Get multi-input field data from field type (e.g., "address" or "address[0].3")
	 *
	 * @param {string} fieldType
	 *
	 * @return {Object|null} Null or object with multi-input field data
	 */
	getFieldDataFromType( fieldType ) {
		const multiInputField = multiInputTypeRegex.exec( fieldType );

		if ( ! multiInputField && ! _.get( pluginData.field_types, `${ fieldType }.inputs` ) && ! _.get( pluginData.field_types, `${ fieldType }.dynamic_inputs` ) ) {
			return null;
		}

		if ( multiInputField ) {
			const [ , type, index, inputId ] = multiInputField;
			const dynamicInputs = pluginData.field_types[ type ].dynamic_inputs === true;

			return {
				type,
				index: parseInt( index, 10 ),
				inputId: parseInt( inputId, 10 ),
				label: this.props.multiInputFields[ type ][ index ].label,
				dynamicInputs,
				inputs: ! dynamicInputs ? pluginData.field_types[ type ].inputs : this.props.multiInputFields[ type ][ index ].inputs,
			};
		}

		const dynamicInputs = pluginData.field_types[ fieldType ].dynamic_inputs === true;

		return {
			type: fieldType,
			isNew: true,
			label: pluginData.field_types[ fieldType ].label,
			index: ( this.props.multiInputFields[ fieldType ] || [] ).length,
			dynamicInputs,
			inputs: ! dynamicInputs ? pluginData.field_types[ fieldType ].inputs :
				{
					1: {
						id: 1,
						label: `${ pluginData.localization.multi_input_fields.input } 1`, // Input X
					},
				},
		};
	}

	/**
	 * Add new input
	 *
	 * @param {number} id Input ID
	 */
	addInput() {
		const { inputs } = this.state;

		const id = parseInt( _.findLastKey( inputs ), 10 ) + 1;

		inputs[ id ] = {
			id,
			label: `${ pluginData.localization.multi_input_fields.input } ${ id }`, // Input X
		};

		this.setState( { inputs } );
	}

	/**
	 * Remove input and recalculate IDs
	 *
	 * @param {number} id Input ID
	 */
	removeInput( id ) {
		const { inputs, selectedColumnFields } = this.state;
		const newInputs = {};
		let newId = 1;

		// Remove input and make IDs sequential
		_.each( inputs, ( input ) => {
			const inputLabelAssigned = input.assignedColumn || input.assignedColumn === 0;

			if ( input.id === id ) {
				if ( inputLabelAssigned ) {
					selectedColumnFields[ input.assignedColumn ] = this.resetColumnField( selectedColumnFields[ input.assignedColumn ] );
				}
				return;
			}

			newInputs[ newId ] = {
				id: newId,
				// If it's a default label, update it with the proper ID :)
				label: input.label === `${ pluginData.localization.multi_input_fields.input } ${ input.id }` ? `${ pluginData.localization.multi_input_fields.input } ${ newId }` : input.label, // Input X
			};

			if ( inputLabelAssigned ) {
				newInputs[ newId ].assignedColumn = input.assignedColumn;
			}

			newId++;
		} );

		this.setState( { inputs: newInputs, selectedColumnFields } );
	}

	/**
	 * Update input label
	 *
	 * @param {number} id Input ID
	 * @param {string} label Input label
	 */
	handleFieldInputLabelChange( { id, label } ) {
		const { inputs } = this.state;

		inputs[ id ].label = label;

		this.setState( inputs );
	}

	/**
	 * Handle changes to the column-input mapping
	 *
	 * @param {Object} event DOM event
	 *
	 * @return {void}
	 */
	handleColumnInputMappingChange( event ) {
		const { inputs, selectedColumnFields } = this.state;
		const columnIndex = parseInt( event.target.querySelector( ':checked' ).getAttribute( 'data-column-index' ), 10 );
		const inputId = event.target.getAttribute( 'data-input-id' );
		const columnType = formatMultiInputFieldType( this.multiInputField.type, this.multiInputField.index, inputId );
		const updatedSelectedColumnFields = _.map( selectedColumnFields, ( field ) => {
			// If another column is already configured with this field type, unset it
			if ( field.type === columnType ) {
				// If the previous value is a multi-type field but with a different type/index, use that instead of a default field type
				return this.props.selectedColumnFields[ field.columnIndex ].multiInputField && this.props.selectedColumnFields[ field.columnIndex ].multiInputField.type !== this.multiInputField.type ?
					this.props.selectedColumnFields[ field.columnIndex ] :
					this.resetColumnField( field );
			}

			return _.assign( {}, field, field.columnIndex === columnIndex && {
				type: columnType,
				multiInputField: {
					type: this.multiInputField.type,
					index: this.multiInputField.index,
				},
				ignore: false,
			} );
		} );

		inputs[ inputId ].assignedColumn = columnIndex;

		this.setState( { selectedColumnFields: updatedSelectedColumnFields, inputs } );
	}

	/**
	 * Format data to be passed back to the field mapping component
	 *
	 * @return {Object} Object with updated multi-input fields and selected column fields data
	 */
	handleSave() {
		const { multiInputFields } = this.props;
		const { selectedColumnFields, inputs } = this.state;

		if ( ! multiInputFields[ this.multiInputField.type ] ) {
			multiInputFields[ this.multiInputField.type ] = [];
		}
		if ( ! this.areColumnsConfigured() ) {
			multiInputFields[ this.multiInputField.type ][ this.multiInputField.index ] = null;

			return {
				updatedMultiInputFields: multiInputFields,
				updatedSelectedColumnFields: selectedColumnFields,
			};
		}

		multiInputFields[ this.multiInputField.type ][ this.multiInputField.index ] = { label: this.state.fieldLabel };

		if ( this.multiInputField.dynamicInputs ) {
			const updatedInputs = {};

			let newId = 1;

			// Remove unassigned inputs and make IDs sequential
			_.each( inputs, ( input ) => {
				if ( ! input.assignedColumn && input.assignedColumn !== 0 ) {
					return;
				}

				updatedInputs[ newId ] = {
					id: newId,
					// If it's a default label, update it with the proper ID :)
					label: input.label === `${ pluginData.localization.multi_input_fields.input } ${ input.id }` ? `${ pluginData.localization.multi_input_fields.input } ${ newId }` : input.label, // Input X
				};

				// Update the selected column with new ID
				selectedColumnFields[ input.assignedColumn ].type = formatMultiInputFieldType( this.multiInputField.type, this.multiInputField.index, newId );

				newId++;
			} );

			multiInputFields[ this.multiInputField.type ][ this.multiInputField.index ].inputs = updatedInputs;
		}

		return {
			updatedMultiInputFields: multiInputFields,
			updatedSelectedColumnFields: selectedColumnFields,
		};
	}

	/**
	 * Check if any columns have been configured
	 *
	 * @return {boolean} True or false
	 */
	areColumnsConfigured() {
		return _.filter( this.state.selectedColumnFields, ( column ) => column.multiInputField && column.multiInputField.index === this.multiInputField.index ).length;
	}

	/**
	 * Check condition for disabling save/update action button
	 *
	 * @return {boolean} True or false
	 */
	shouldSaveBeDisabled() {
		// Label must always exist and columns must be configured
		if ( ! this.state.fieldLabel.trim() || ! this.areColumnsConfigured() ) {
			return true;
		}

		// Multi-input fields with dynamic inputs must have labels set
		if ( this.multiInputField.dynamicInputs ) {
			return _.filter( this.state.inputs, ( input ) => ! input.label.trim() ).length;
		}

		return false;
	}

	/**
	 * Return modal window content
	 *
	 * @return {ReactElement} JSX markup
	 */
	getContent() {
		const { inputs, selectedColumnFields, fieldLabel } = this.state;
		const dynamicInputs = this.multiInputField.dynamicInputs;
		const numberOfInputs = _.size( inputs );
		const numberOfColumns = _.size( selectedColumnFields );
		const lastInputId = parseInt( _.findLastKey( inputs ), 10 );

		return (
			<div className={ styles( 'multi-input-mapping' ) }>
				<div className={ styles( 'columns' ) }>
					<div className={ styles( 'column' ) }>
						<p className={ styles( 'title', 'is-5' ) }>
							{ /* The %s field type contains multiple inputs that can be mapped to your import data. */ }
							{ replacePlaceholders(
								pluginData.localization.multi_input_fields.field_contains_multiple_inputs, [
									`<em>${ pluginData.field_types[ this.multiInputField.type ].label }</em>`,
								],
							) }
						</p>
					</div>
				</div>

				<div className={ styles( 'field', 'is-horizontal' ) }>
					<div className={ styles( 'field-body' ) }>
						<div className={ styles( 'field' ) }>
							<p className={ styles( 'control', 'is-expanded' ) }>
								<input
									className={ styles( 'input' ) }
									type="text"
									placeholder={ pluginData.localization.map_fields.field_label } /* Field Label */
									value={ fieldLabel }
									onChange={ ( e ) => this.setState( { fieldLabel: e.target.value } ) }
								/>
							</p>
						</div>
					</div>
				</div>

				<div
					className={ styles( 'is-divider' ) }
					data-content={ pluginData.localization.map_fields.field_inputs } /* Field Inputs */
				/>

				{ _.map( inputs, ( input ) => {
					const columnValue = formatMultiInputFieldType( this.multiInputField.type, this.multiInputField.index, input.id );

					return (
						<div
							key={ `input-${ input.id }` }
							className={ styles( 'columns', 'is-mobile' ) }
						>
							{ dynamicInputs ?
								<div className={ styles( 'column', 'is-4' ) }>
									<label htmlFor={ `input-${ input.id }` } className={ styles( 'label', 'is-small' ) }>
										{ /* Field Input Label */ }
										{ pluginData.localization.multi_input_fields.input_label }
									</label>
									<input
										id={ `input-${ input.id }` }
										className={ styles( 'input' ) }
										type="text"
										placeholder={ pluginData.localization.multi_input_fields.input_label } /* Field Label */
										value={ input.label }
										onChange={ ( e ) => this.handleFieldInputLabelChange( { id: input.id, label: e.target.value } ) }
									/>
								</div> :
								<div className={ styles( 'column' ) }>
									<label htmlFor={ input.id } className={ styles( 'label' ) }>
										{ input.label }
									</label>
								</div>
							}
							<div className={ styles( 'column', 'is-6' ) }>
								{ dynamicInputs && <label htmlFor={ `input-column-${ input.id }` } className={ styles( 'label', 'is-small' ) }>CSV column</label> }
								<div className={ styles( 'select', 'is-fullwidth' ) }>
									<select
										className={ styles( 'is-fullwidth' ) }
										id={ `input-column-${ input.id }` }
										onChange={ ( e ) => this.handleColumnInputMappingChange( e ) }
										value={ columnValue }
										data-input-id={ input.id }
									>
										<option value={ this.props.column.defaultType }>
											{ /* Select Import Column */ }
											{ pluginData.localization.map_fields.select_import_column }
										</option>

										{ _.map( selectedColumnFields, ( column ) => (
											<option
												key={ `input-${ input.id }-column-${ column.columnIndex }` }
												data-column-index={ column.columnIndex }
												value={ column.type }
											>
												{ column.label }
											</option>
										) ) }
									</select>
								</div>
							</div>
							{ dynamicInputs && (
								<div className={ styles( 'column', 'controls' ) }>
									<div className={ styles( 'field', 'is-grouped' ) }>
										{ numberOfInputs < numberOfColumns && input.id === lastInputId && (
											<div className={ styles( 'control', 'is-expanded' ) }>
												<div
													role="button"
													tabIndex={ 0 }
													onClick={ () => this.addInput() }
													onKeyDown={ ( e ) => e.keyCode === 13 ? this.addInput() : null }
												>
													<figure className={ styles( 'image', 'icon-add' ) } />
												</div>
											</div>
										) }
										{ numberOfInputs > 1 && (
											<div className={ styles( 'control' ) }>
												<div
													role="button"
													tabIndex={ 0 }
													onClick={ () => this.removeInput( input.id ) }
													onKeyDown={ ( e ) => e.keyCode === 13 ? this.removeInput( input.id ) : null }
												>
													<figure className={ styles( 'image', 'icon-remove' ) } />
												</div>
											</div>
										) }
									</div>
								</div>
							) }
						</div>
					);
				} ) }
			</div>
		);
	}

	/**
	 * Return modal window footer buttons
	 *
	 * @return {Object[]} Object with buttons data for use in the modal dialog
	 */
	getButtons() {
		return [
			{
				label: ( this.multiInputField.isNew ) ? pluginData.localization.shared.save : pluginData.localization.shared.update, // Save|Update
				style: styles( 'is-link' ),
				action: () => this.props.onSave( this.handleSave() ),
				disabled: this.shouldSaveBeDisabled() ? 'disabled' : null,
			},
			{
				label: pluginData.localization.modal.cancel, // Cancel
				action: () => this.props.onClose(),
				dismissModal: true,
			},
		];
	}

	/**
	 * Render modal window with multi-input field mapping logic
	 *
	 * @return {ReactElement} JSX markup
	 */
	render() {
		const props = {
			content: this.getContent(),
			buttons: this.getButtons(),
		};

		return <ModalDialog { ...props } />;
	}
}
