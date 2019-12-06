/* global wp */

import _ from 'lodash';
import classNames from 'classnames/bind';
import appStyles from 'css/app';
import { pluginData } from 'js/app';
import ToggleElement from 'js/shared/toggle-element';
import ConditionalImport from './partials/conditional-import';
import { unescapeString } from 'js/helpers/string-manipulations';
import Beacon from 'js/shared/helpscout-beacon';

const styles = classNames.bind( appStyles );
const { Component, Fragment } = wp.element;

/**
 * Configure import options
 */
export default class Configure extends Component {
	stepRouteId = 'step4Configure';

	state = {
		initialized: false,
		options: {
			processFeeds: {
				checked: false,
				feeds: [],
			},
			uploadFiles: {
				checked: false,
				disabled: true,
			},
			ignoreRequired: {
				checked: true,
			},
			overwritePostData: {
				checked: false,
				disabled: true,
			},
			useDefaultFieldValues: {
				checked: false,
			},
			ignoreErrors: {
				checked: true,
			},
			conditionalImport: {
				checked: false,
				conditions: null,
			},
			emailNotifications: {
				checked: false,
			},
			skipValidation: {
				checked: false,
			},
		},
	};

	/**
	 * Run on component mount
	 *
	 * @return {void}
	 */
	componentDidMount() {
		const { progress, routes, defaultRoute } = this.props.context;

		// Navigate to source selection if required data is not set
		if ( _.isEmpty( progress.stepData.step3MapFields ) ) {
			return this.props.history.push( routes[ defaultRoute ].path );
		}

		// Check if we're restarting interrupted import task and update state with previously saved progress data
		if ( ! _.isEmpty( progress.stepData[ this.stepRouteId ] ) ) {
			return this.setState( progress.stepData[ this.stepRouteId ] );
		}

		this.setState( {
			initialized: true,
		}, this.saveCurrentProgress );
	}

	/**
	 * Render option toggle element
	 *
	 * @return {ReactElement} JSX markup
	 * @param {string} option
	 * @param {string} title
	 * @param {string} description
	 * @param {Object} confirmation Object with confirmation modal content, confirm and cancel button labels
	 * @param {string} articleId ID of the Help Scout article to display in beacon
	 *
	 * @return {ReactElement} JSX markup
	 */
	renderToggleElement( option, title, description, confirmation, articleId ) {
		const { checked, disabled } = this.state.options[ option ];

		if ( disabled ) {
			return null;
		}

		const optionData = {
			option,
			title,
			description,
			checked,
			disabled,
			articleId,
			onChange: ( identifier, state ) => this.updateOption( identifier, state ),
		};

		if ( _.isObject( confirmation ) ) {
			const { content, confirmButton, cancelButton } = confirmation;

			optionData.confirmation = {
				displayModalDialog: this.props.context.displayModalDialog,
				dismissModalDialog: this.props.context.dismissModalDialog,
				content,
				confirmButton,
				cancelButton,
			};
		}

		return <ToggleElement { ...optionData } />;
	}

	/**
	 * Render option that allows feeds processing
	 *
	 * @return {ReactElement} JSX markup
	 */
	displayProcessFeedsOption() {
		const option = 'processFeeds';
		const { importData: { form } } = this.props.context;

		// Display feeds only for existing form and one that has feeds enabled
		return ( form.type !== 'new' && ! _.isEmpty( form.feeds ) ) && (
			<>
				{ this.renderToggleElement(
					option,
					pluginData.localization.configure.process_feeds.title, // Process feeds
					pluginData.localization.configure.process_feeds.description, // available form feeds will be executed for each entry
				) }

				{ this.state.options[ option ].checked && (
					<div className={ styles( 'box', 'process-feeds' ) }>
						<div className={ styles( 'columns' ) }>
							<div className={ styles( 'column' ) }>
								<span className={ styles( 'title', 'is-size-6' ) }>
									{ /* Run these actions for each entry: */ }
									{ pluginData.localization.configure.process_feeds.run_for_each_entry }
								</span>
							</div>
						</div>
						<div className={ styles( 'columns' ) }>
							<div className={ styles( 'column' ) }>
								{ _.map( form.feeds, ( feed, feedName ) => (
									<Fragment key={ `feed-${ feedName }` }>
										<div className={ styles( 'is-divider' ) } data-content={ feedName } />
										{ _.map( feed,
											( feedData ) => {
												const identifier = `feed-${ feedName }-${ feedData.id }`;

												const feedDescription = feedData.description ? `: ${ feedData.description }` : null;

												return (
													<div key={ identifier } className={ styles( 'field' ) }>
														<input
															type="checkbox"
															className={ styles( 'is-checkradio', 'is-success', 'is-circle' ) }
															id={ identifier }
															name={ feedData.id }
															checked={ _.includes( this.state.options[ option ].feeds, feedData.id ) }
															onChange={ ( e ) => {
																const { feeds } = this.state.options[ option ];
																const feedId = parseInt( e.target.name, 10 );

																if ( ! _.includes( feeds, feedId ) ) {
																	feeds.push( feedId );
																} else {
																	feeds.splice( feeds.indexOf( feedId ), 1 );
																}

																this.updateOption( option, { feeds } );
															} }
														/>
														<label htmlFor={ identifier }>
															{ feedData.name }{ feedDescription }
														</label>
													</div>
												);
											},
										) }
									</Fragment>
								) ) }
							</div>
						</div>
					</div>
				) }
			</>
		);
	}

	/**
	 * Render option that allows file upload
	 *
	 * @return {ReactElement} JSX markup
	 */
	displayUploadFilesOption() {
		const option = 'uploadFiles';
		const { importData: { form, schema } } = this.props.context;

		const isFileUploadFieldSelected = _.filter( schema, ( column ) => _.get( form.fields, `_${ column.field }.type` ) === 'fileupload' ).length || null;

		// Display option only if one of the selected fields is a file upload
		return isFileUploadFieldSelected && (
			<>
				{ this.renderToggleElement(
					option,
					pluginData.localization.configure.upload_files.title, // Upload Files
					_.unescape( pluginData.localization.configure.upload_files.description ), // upload files mapped to "File Upload" fields
				) }
			</>
		);
	}

	/**
	 * Render option that allows ignoring required fields
	 *
	 * @return {ReactElement} JSX markup
	 */
	displayIgnoreRequiredOption() {
		const option = 'ignoreRequired';
		const { importData: { form, schema } } = this.props.context;

		const areRequiredFieldsSelected = _.filter( schema, ( column ) => _.get( form.fields, `${ column.field }.required` ) ).length || null;

		// Display option only when required fields are selected
		return areRequiredFieldsSelected && (
			<>
				{ this.renderToggleElement(
					option,
					pluginData.localization.configure.ignore_required.title, // Ignore Required
					pluginData.localization.configure.ignore_required.description, // entry will be imported even if it is missing required fields
				) }
			</>
		);
	}

	/**
	 * Render option that allows overwriting post data
	 *
	 * @return {ReactElement} JSX markup
	 */
	displayOverwritePostDataOption() {
		const option = 'overwritePostData';
		const { importData: { form, schema } } = this.props.context;

		const arePostFieldsSelected = _.filter( schema, ( column ) => ( _.get( form.fields, `_${ column.field }.type` ) || '' ).match( /^post_/ ) ).length || null;

		// Display option only if one of the selected fields is a post field
		return arePostFieldsSelected && (
			<>
				{ this.renderToggleElement(
					option,
					pluginData.localization.configure.overwrite_post_data.title, // Overwrite Post Data
					pluginData.localization.configure.overwrite_post_data.description, // existing post content will be overwritten by the imported data
				) }
			</>
		);
	}

	/**
	 * Render option that allows using default values for empty fields
	 *
	 * @return {ReactElement} JSX markup
	 */
	displayUseDefaultFieldsValuesDataOption() {
		const option = 'useDefaultFieldValues';
		const { importData: { form, schema } } = this.props.context;

		const areFieldsWithDefaultValuesSelected = _.filter( schema, ( column ) => _.get( form.fields, `${ column.field }.default` ) ).length || null;

		// Display option only when fields with default values are selected
		return areFieldsWithDefaultValuesSelected && (
			<>
				{ this.renderToggleElement(
					option,
					pluginData.localization.configure.use_default_values.title, // Use Default Field Values
					pluginData.localization.configure.use_default_values.description, // empty fields will be populated with default values
				) }
			</>
		);
	}

	/**
	 * Render option that allows specifying import conditions
	 *
	 * @return {ReactElement} JSX markup
	 */
	displayConditionalImportOption() {
		const option = 'conditionalImport';
		const { options } = this.state;
		const { importData: { form, schema } } = this.props.context;

		return (
			<>
				{ this.renderToggleElement(
					option,
					pluginData.localization.configure.conditional_import.title, // Conditional Import
					pluginData.localization.configure.conditional_import.description, // only import rows if they match certain conditions
				) }

				{ options.conditionalImport.checked && (
					<ConditionalImport
						group={ options[ option ].conditions ? options[ option ].conditions.op : null }
						conditions={ options[ option ].conditions ? options[ option ].conditions.value : null }
						form={ form }
						columnFields={ schema }
						onChange={ ( group, conditions ) => {
							this.updateOption( option, {
								conditions: conditions.length ? {
									op: group,
									value: conditions,
								} : null,
							} );
						} }
					/>
				) }
			</>
		);
	}

	/**
	 * Render option that allows continuing import despite errors
	 *
	 * @return {ReactElement} JSX markup
	 */
	displayIgnoreErrorsOption() {
		const option = 'ignoreErrors';

		return (
			<>
				{ this.renderToggleElement(
					option,
					pluginData.localization.configure.ignore_errors.title, // Ignore Errors
					pluginData.localization.configure.ignore_errors.description, // import will not interrupt when errors are encountered
					null,
					'5d38abd62c7d3a2ec4bf5f97',
				) }
			</>
		);
	}

	/**
	 * Render option that allows sending email notification for each imported record
	 *
	 * @return {ReactElement} JSX markup
	 */
	displayEmailNotificationsOption() {
		const option = 'emailNotifications';

		return (
			<>
				{ this.renderToggleElement(
					option,
					pluginData.localization.configure.email_notifications.title, // Email Notifications
					pluginData.localization.configure.email_notifications.description, // receive email notification for each imported record
				) }
			</>
		);
	}

	/**
	 * Render option that allows skipping field validation
	 *
	 * @return {ReactElement} JSX markup
	 */
	skipValidationOption() {
		const option = 'skipValidation';
		const { importData: { form } } = this.props.context;

		// Display option only for existing forms
		return form.type !== 'new' && (
			<>
				{ this.renderToggleElement(
					option,
					pluginData.localization.configure.skip_validation.title, // Skip Field Validation
					unescapeString( pluginData.localization.configure.skip_validation.description ), // disregard field's validation logic
					null,
					'5d770c4204286364bc8ee89e',
				) }
			</>
		);
	}

	/**
	 * Update state with new option settings
	 *
	 * @param {string} option
	 * @param {*} data
	 */
	updateOption( option, data ) {
		const options = _.assign( {}, this.state.options );
		options[ option ] = _.assign( {}, options[ option ], data );

		this.setState( { options }, this.saveCurrentProgress );
	}

	/**
	 * Save state to local storage. This allows us to resume interrupted task.
	 */
	saveCurrentProgress() {
		this.props.context.saveCurrentProgress( this.state, this.stepRouteId );
	}

	/**
	 * Go to import data step
	 */
	goToNextStep() {
		const { importData, progress } = this.props.context;
		const { options } = this.state;

		importData.options = { flags: [] };

		if ( options.processFeeds.checked ) {
			importData.options.feeds = _.flatten( options.processFeeds.feeds );
		}

		if ( options.ignoreErrors.checked ) {
			importData.options.flags.push( 'soft' );
		}

		if ( ! options.ignoreRequired.checked ) {
			importData.options.flags.push( 'require' );
		}

		if ( options.conditionalImport.checked && options.conditionalImport.conditions ) {
			importData.conditions = options.conditionalImport.conditions;
		}

		if ( options.emailNotifications.checked ) {
			importData.options.flags.push( 'notify' );
		}

		if ( options.skipValidation.checked ) {
			importData.options.flags.push( 'valid' );
		}

		importData.useDefaultFieldValues = options.useDefaultFieldValues.checked === true;

		progress.stepData.step5ImportData = null;

		this.props.context.setState( { importData, progress } );

		this.props.history.push( this.props.context.routes.step5ImportData.path );
	}

	/**
	 * Render import configuration screen
	 *
	 * @return {ReactElement} JSX markup
	 */
	render() {
		// Component is initialized when data is validated. See componentDidMount();
		return this.state.initialized && (
			<div data-automation-id="configure_options" className={ styles( 'configure-options' ) }>
				<section className={ styles( 'section' ) }>
					<div className={ styles( 'container' ) }>
						<div className={ styles( 'columns' ) }>
							<div className={ styles( 'column' ) }>
								<p className={ styles( 'title', 'is-4' ) }>
									{ /* Configure Import Options */ }
									{ pluginData.localization.configure.configure_options }
									<Beacon articleId="5d36928304286347867548e0" beaconType="sidebar" />
								</p>
							</div>
						</div>

						{ this.displayProcessFeedsOption() }

						{ this.displayUploadFilesOption() }

						{ this.displayIgnoreRequiredOption() }

						{ this.displayIgnoreErrorsOption() }

						{ this.displayOverwritePostDataOption() }

						{ this.displayUseDefaultFieldsValuesDataOption() }

						{ this.displayConditionalImportOption() }

						{ this.displayEmailNotificationsOption() }

						{ this.skipValidationOption() }

					</div>
				</section>

				<section className={ styles( 'section' ) }>
					<div className={ styles( 'container' ) }>
						<div className={ styles( 'columns' ) }>
							<div className={ styles( 'column' ) }>
								<div className={ styles( 'level' ) }>
									<div className={ styles( 'level-left' ) }>
										<button data-automation-id="continue_with_import" className={ styles( 'button', 'is-link', 'is-primary', 'is-medium' ) } onClick={ ( e ) => this.goToNextStep( e ) }>
											{ /* Create Form And Continue With Import|Continue With Import */ }
											{ this.props.context.importData.form.type === 'new' ? pluginData.localization.configure.create_and_continue : pluginData.localization.shared.continue_with_import }
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
