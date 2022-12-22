/* global wp */

import _ from 'lodash';
import { pluginData } from 'js/app';
import { getFormFieldLabel } from 'js/helpers/form-field-manipulations';
import classNames from 'classnames/bind';

import appStyles from 'css/app';

const styles = classNames.bind( appStyles );
const { Component } = wp.element;

/**
 * Map columns to multi-input fields (e.g., Address)
 */
export default class ConditionalImport extends Component {
	DOM = {};

	state = {
		conditions: this.props.conditions || [
			{
				column: '',
				op: '',
				value: '',
			},
		],
		group: this.props.group || 'and',
	};

	/**
	 * Handle changes to the import condition
	 *
	 * @param {number} conditionIndex
	 */
	handleConditionChange( conditionIndex ) {
		const { conditions } = this.state;

		conditions[ conditionIndex ] = {
			column: parseInt( this.DOM[ `condition-${ conditionIndex }-column` ].value, 10 ),
			op: this.DOM[ `condition-${ conditionIndex }-op` ].value,
			value: this.DOM[ `condition-${ conditionIndex }-value` ].value,
		};

		this.setState( { conditions }, () => this.handleParentChange() );
	}

	/**
	 * Handle changes to the import condition group
	 *
	 * @param {number} group
	 */
	handleConditionGroupChange( group ) {
		this.setState( { group }, () => this.handleParentChange() );
	}

	/**
	 * Add import condition
	 */
	addCondition() {
		const { conditions } = this.state;
		conditions.push( { column: '', op: '', value: '' } );

		this.setState( { conditions }, () => this.handleParentChange() );
	}

	/**
	 * Remove import condition
	 *
	 * @param {number} conditionIndex
	 */
	removeCondition( conditionIndex ) {
		const { conditions } = this.state;
		conditions.splice( conditionIndex, 1 );

		this.setState( { conditions }, () => this.handleParentChange() );
	}

	handleParentChange() {
		const { onChange } = this.props;
		const conditions = _.filter( this.state.conditions, ( condition ) => condition.value.trim() !== '' );

		return onChange( this.state.group, conditions );
	}

	/**
	 * Render import conditions selection area
	 *
	 * @return {ReactElement} JSX markup
	 */
	render() {
		const { form, columnFields } = this.props;

		return (
			<div className={ styles( 'box', 'conditional-import' ) }>
				<div className={ styles( 'columns' ) }>
					<div className={ styles( 'column' ) }>
						<div className={ styles( 'control', 'is-flex', 'baseline' ) }>
							<span className={ styles( 'title', 'is-size-6' ) }>
								{ /* Import the row if */ }
								{ pluginData.localization.configure.conditional_import.import_row_if }
							</span>
							<div className={ styles( 'select', 'is-size-6' ) }>
								<select
									defaultValue={ this.state.group }
									onChange={ ( e ) => this.handleConditionGroupChange( e.target.value ) }
								>
									<option value="and">
										{ /* all */ }
										{ pluginData.localization.configure.conditional_import.all }
									</option>
									<option value="or">
										{ /* any */ }
										{ pluginData.localization.configure.conditional_import.any }
									</option>
								</select>
							</div>
							<span className={ styles( 'title', 'is-size-6' ) }>
								{ /* of the following match: */ }
								{ pluginData.localization.configure.conditional_import.there_is_match }
							</span>
						</div>
						{ _.map( this.state.conditions, ( condition, conditionIndex ) => {
							return (
								<div className={ styles( 'columns' ) }>
									<div className={ styles( 'column' ) }>
										<div className={ styles( 'control', 'is-flex', 'center' ) }>
											<div className={ styles( 'select', 'is-size-6' ) }>
												<select
													ref={ ( el ) => this.DOM[ `condition-${ conditionIndex }-column` ] = el }
													defaultValue={ condition.column }
													name="column"
													onChange={ ( e ) => this.handleConditionChange( conditionIndex, e.target.name, e.target.value ) }
												>
													{ _.map( columnFields, ( columnField, fieldIndex ) =>
														<option
															key={ `condition-${ conditionIndex }-field-${ fieldIndex }` }
															value={ columnField.column }
														>
															{ form.type === 'new' ? columnField.name : getFormFieldLabel( columnField.field, form.fields ) }
														</option>,
													) }
												</select>
											</div>
											<div className={ styles( 'select', 'is-size-6' ) }>
												<select
													ref={ ( el ) => this.DOM[ `condition-${ conditionIndex }-op` ] = el }
													defaultValue={ condition.op }
													name="op"
													onChange={ ( e ) => this.handleConditionChange( conditionIndex, e.target.name, e.target.value ) }
												>
													<option value="eq">
														{ /* is */ }
														{ pluginData.localization.configure.conditional_import.is }
													</option>
													<option value="neq">
														{ /* is not */ }
														{ pluginData.localization.configure.conditional_import.is_not }
													</option>
													<option value="like">
														{ /* contains */ }
														{ pluginData.localization.configure.conditional_import.contains }
													</option>
													<option value="lt">
														{ /* less than */ }
														{ pluginData.localization.configure.conditional_import.less_than }
													</option>
													<option value="gt">
														{ /* less than */ }
														{ pluginData.localization.configure.conditional_import.greater_than }
													</option>
												</select>
											</div>
											<input
												ref={ ( el ) => this.DOM[ `condition-${ conditionIndex }-value` ] = el }
												type="text"
												autoFocus={ true }
												className={ styles( 'input', 'is-2' ) }
												value={ condition.value }
												placeholder={ pluginData.localization.configure.conditional_import.enter_value }
												onChange={ ( e ) => this.handleConditionChange( conditionIndex, e.target.name, e.target.value ) }
											/>
											<div
												role="button"
												tabIndex={ 0 }
												onClick={ () => this.addCondition() }
												onKeyDown={ ( e ) => e.keyCode === 13 ? this.addCondition() : null }
											>
												<figure className={ styles( 'image', 'icon-add' ) } />
											</div>
											{ conditionIndex > 0 && (
												<div
													role="button"
													tabIndex={ 0 }
													onClick={ () => this.removeCondition( conditionIndex ) }
													onKeyDown={ ( e ) => e.keyCode === 13 ? this.removeCondition( conditionIndex ) : null }
												>
													<figure className={ styles( 'image', 'icon-remove' ) } />
												</div>
											) }
										</div>
									</div>
								</div>
							);
						} ) }
					</div>
				</div>
			</div>
		);
	}
}
