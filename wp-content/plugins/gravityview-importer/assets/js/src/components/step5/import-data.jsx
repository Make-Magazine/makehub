/* global wp */

import _ from 'lodash';
import { AJAX, API } from 'js/helpers/server-requests';
import { pluginData } from 'js/app';
import { addPeriodToString, i18nFormatNumber, joinJSXArray, replacePlaceholderLinks, replacePlaceholders } from 'js/helpers/string-manipulations';
import classNames from 'classnames/bind';
import appStyles from 'css/app';
import { Circle } from 'rc-progress';

const styles = classNames.bind( appStyles );
const { Component } = wp.element;

/**
 * Process import data
 */
export default class ImportData extends Component {
	stepRouteId = 'step5ImportData';

	state = {
		initialized: false,
		status: 'preparing',
		progress: {
			total: 0,
			processed: 0,
			skipped: 0,
			error: 0,
			percentCompleted: 0,
		},
		error: {},
		batchResponse: {},
		restartProcessing: false,
		errorReport: [],
	};

	/**
	 * Run on component mount
	 *
	 * @return {void}
	 */
	componentDidMount() {
		const { progress: { stepData }, routes, defaultRoute } = this.props.context;

		// Navigate to source selection if required data is not set
		if ( _.isEmpty( stepData.step4Configure ) ) {
			return this.props.history.push( routes[ defaultRoute ].path );
		}

		// Check if we're restarting interrupted import task and update state with previously saved progress data
		if ( ! _.isEmpty( stepData[ this.stepRouteId ] ) ) {
			return this.setState( stepData[ this.stepRouteId ], this.startImport );
		}

		this.setState( { initialized: true }, this.startImport );
	}

	/**
	 * Update import batch status, start processing and monitor progress
	 */
	startImport() {
		const { batchResponse } = this.state;

		if ( ! batchResponse.status === 'error' || batchResponse.status === 'done' ) {
			return;
		}

		this.saveCurrentProgress();

		// Step 1 - update batch status and set schema with mapped fields
		const step1UpdateBatch = () => {
			const { importData: { schema, batchId, options, conditions, useDefaultFieldValues } } = this.props.context;
			const { restartProcessing } = this.state;

			let updatedSchema = schema;

			if ( useDefaultFieldValues ) {
				updatedSchema = _.map( updatedSchema, ( field ) => ( { ...field, flags: [ 'default' ] } ) );
			}

			const requestData = ( ! restartProcessing ) ? {
				status: 'process',
				id: batchId,
				schema: updatedSchema,
				conditions,
				...options,
			} : {
				status: 'process',
				id: batchId,
			};

			API.update( {
				requestData,
				responseHandler: ( res ) => {
					const { status } = res;

					// Successful response must have status set to 'process'
					return ( status === 'process' );
				},
				successHandler: ( res ) => {
					if ( res.error ) {
						return errorHandler( {
							message: addPeriodToString( res.error ),
							batchResponse: res,
						} );
					}
					this.setState( { status: 'importing' }, () => {
						this.setState( { restartProcessing: false } );
						this.saveCurrentProgress();

						step2ProcessImportBatch();

						monitorProgress();
					} );
				},
				errorHandler,
			} );
		};

		// Step 2 - process data
		const step2ProcessImportBatch = () => {
			const { batchId } = this.props.context.importData;

			API.process( {
				requestData: {
					id: batchId,
				},
				successHandler: ( res ) => {
					const { status } = res;

					if ( status !== 'error' && status !== 'done' ) {
						return this.setState( { batchResponse: res, restartProcessing: true } );
					}

					this.setState( { progress: this.calculateImportProgress( res ), batchResponse: res }, this.saveCurrentProgress );
				},
				errorHandler,
			} );
		};

		// Monitor progress every 1.5 seconds
		const monitorProgress = () => {
			const { importData: { batchId } } = this.props.context;
			const { status: stateStatus, restartProcessing } = this.state;

			if ( restartProcessing ) {
				return step1UpdateBatch();
			}

			API.get( {
				requestData: {
					id: batchId,
				},
				successHandler: ( res ) => {
					const { status: responseStatus, error: responseError } = res;

					// Continue monitoring if the batch is still being imported/processed
					if ( stateStatus !== 'error' && ( responseStatus === 'importing' || responseStatus === 'process' || responseStatus === 'processing' ) ) {
						return this.setState( { progress: this.calculateImportProgress( res ), batchResponse: res }, () => setTimeout( monitorProgress, 1000 ) );
					}

					// If it is a newly created form, assign form ID to the global forms object
					this.maybeAssignIdToNewForm( res );

					// Process batch error
					if ( responseStatus === 'error' || responseStatus !== 'done' ) {
						return this.getRowErrorReport().then( ( errorReport ) => {
							return errorHandler( {
								message: addPeriodToString( responseError ),
								batchResponse: res,
								errorReport,
							} );
						} );
					}

					return this.setState( {
						status: 'done',
						progress: this.calculateImportProgress( res ),
						batchResponse: res,
					}, res.progress.processed === res.meta.rows ? this.props.context.clearLocalStorageState : this.saveCurrentProgress );
				},
				errorHandler,
			} );
		};

		// Handle server errors
		const errorHandler = ( err ) => {
			const { message, batchResponse: res = {}, errorReport = [] } = err;

			this.setState( {
				status: 'error',
				error: { message },
				batchResponse: res,
				errorReport,
			}, this.saveCurrentProgress );
		};

		step1UpdateBatch();
	}

	/**
	 * Save state to local storage. This allows us to resume interrupted task.
	 */
	saveCurrentProgress() {
		this.props.context.saveCurrentProgress( this.state, this.stepRouteId );
	}

	/**
	 * In case of a new form, assign its ID from the API batch request to the app state forms object and update schema
	 *
	 * @param {Object} batchResponse API batch response
	 */
	maybeAssignIdToNewForm( batchResponse ) {
		const { form_id: newFormId, schema: updatedSchema, progress, meta } = batchResponse;
		const { importData: { form }, forms, importData } = this.props.context;

		if ( form.type === 'new' && ! form.id && newFormId ) {
			AJAX.post( {
				requestData: {
					action: pluginData.action_form_data,
					formId: newFormId,
				},
				responseHandler: ( res ) => {
					return res.success === true;
				},
				successHandler: ( res ) => {
					const { form_fields: fields, form_feeds: feeds } = res.data;

					importData.form = {
						id: parseInt( newFormId, 10 ),
						title: form.title,
						type: null,
						fields,
						feeds,
					};

					importData.schema = updatedSchema;

					forms.push( {
						id: importData.form.id,
						title: importData.form.title,
					} );

					this.props.context.setState( { importData, forms }, () => {
						// Clear local storage state if import is done
						return progress.processed === meta.rows ? this.props.context.clearLocalStorageState() : this.saveCurrentProgress();
					} );
				},
				errorHandler: ( err ) => {
					const { message } = err;

					this.setState( {
						status: 'error',
						error: { message },
					}, this.saveCurrentProgress );
				},
			} );
		}
	}

	/**
	 * Update state with import progress from API request
	 *
	 * @param {Object} res API response
	 * @return {Object} Object with total, processed, skipped, error and % completed metrics
	 */
	calculateImportProgress( res ) {
		const { meta: { rows: total = 0 }, progress: { processed = 0, skipped = 0, error = 0 } } = res;

		return {
			total,
			processed,
			skipped,
			error,
			percentCompleted: Math.round( ( processed + skipped + error ) / total * 100 ),
		};
	}

	/**
	 * Reset state and restart data mapping process
	 */
	restartImportData() {
		this.setState( {
			status: 'preparing',
			progress: {
				total: null,
				processed: null,
				percentCompleted: null,
			},
			error: {},
			batchResponse: {},
		}, () => this.startImport() );
	}

	/**
	 * Reset import data in application state and redirect to import source selection
	 */
	startNewImportJob() {
		const { progress } = this.props.context;

		this.props.context.setState( { importData: {}, progress: { stepData: {}, step: progress.step } }, () => {
			this.props.context.clearLocalStorageState();
			this.props.history.push( this.props.context.routes[ this.props.context.defaultRoute ].path );
		} );
	}

	/**
	 * Display information while import task is being started
	 *
	 * @return {ReactElement} JSX markup
	 */
	displayStartingState() {
		return (
			<>
				<p className={ styles( 'title', 'is-4' ) }>
					{ /* Preparing to import your data. */ }
					{ pluginData.localization.import_data.preparing_to_import }
				</p>
				<p className={ styles( 'is-size-5' ) }>
					{ /* Do not navigate away from this page. */ }
					{ pluginData.localization.import_data.do_not_navigate }
					<span className={ styles( 'spinner', 'spinner-custom' ) } />
				</p>
			</>
		);
	}

	/**
	 * Display progress as import is being executed
	 *
	 * @return {ReactElement} JSX markup
	 */
	displayInProgressState() {
		const { progress: { processed, skipped, error, total, percentCompleted } } = this.state;

		return (
			<>
				<p className={ styles( 'title', 'is-4' ) }>
					{ /* Please wait while your data is being imported. Do not navigate away from this page. */ }
					{ pluginData.localization.import_data.importing_data } { pluginData.localization.import_data.do_not_navigate }
				</p>

				<div className={ styles( 'uploadProgress' ) }>
					<Circle percent={ percentCompleted } strokeWidth="5" strokeColor="#46B450" />
				</div>

				<p className={ styles( 'is-size-5' ) }>
					{ /* Processed X of Y records */ }
					{ replacePlaceholders(
						pluginData.localization.import_data.processed_x_of_y_records, [
							`<strong>${ i18nFormatNumber( processed + skipped + error ) }</strong>`,
							`<strong>${ i18nFormatNumber( total ) }</strong>`,
						],
					) }
				</p>
			</>
		);
	}

	/**
	 * Display final statistics and call to action
	 *
	 * @return {ReactElement} JSX markup
	 */
	displayFinishedState() {
		const { batchResponse: { progress, meta } } = this.state;
		const { importData: { form } } = this.props.context;

		const viewImportedRecordsButton = {
			automationId: 'view_imported_records',
			label: pluginData.localization.import_data.view_imported_records, /* View Imported Records */
			action: () => window.location.href = `${ pluginData.gf_entries_url }&id=${ form.id }`,
		};
		const startnewImportButton = {
			automationId: 'start_new_import',
			label: pluginData.localization.import_data.start_new_import, /* Start New Import */
			action: () => this.startNewImportJob(),
		};
		const changeFieldMappingButton = {
			automationId: 'change_field_mapping',
			label: pluginData.localization.shared.change_field_mapping, /* Change Field Mapping */
			action: () => this.changeFieldMapping( {
				recreateBatch: true,
			} ),
		};
		const modifyImportConfigurationButton = {
			automationId: 'change_field_mapping',
			label: pluginData.localization.import_data.modify_import_configuration, /* Modify Import Configuration */
			action: () => this.changeFieldMapping( {
				recreateBatch: true,
				goToConfigureStep: true,
			} ),
		};
		let buttons;

		if ( progress.processed === meta.rows ) {
			// All records were imported
			buttons = [ viewImportedRecordsButton, startnewImportButton ];
		} else if ( progress.skipped === meta.rows || progress.error === meta.rows ) {
			// All records were skipped or rejected
			buttons = [ changeFieldMappingButton, modifyImportConfigurationButton, startnewImportButton ];
		} else {
			// Some fields were imported
			buttons = [ viewImportedRecordsButton, modifyImportConfigurationButton, changeFieldMappingButton, startnewImportButton ];
		}

		return (
			<>
				<p data-automation-id={ ! progress.error ? 'import_finished' : 'import_finished_with_errors' } className={ styles( 'title', 'is-4' ) }>
					{ /* Import has finished.|Import has finished with errors. */ }
					{ ( ! progress.error ) ? pluginData.localization.import_data.import_finished : pluginData.localization.import_data.import_finished_with_errors }
				</p>
				<p className={ styles( 'stats', 'is-size-5' ) }>
					{ this.getBatchFinalProgressReport() }
				</p>

				<div className={ styles( 'buttons', 'has-text-centered', 'block' ) }>
					{ buttons.map( ( button ) =>
						<span
							key={ `button-${ button.label }` }
							className={ styles( 'button', 'is-link' ) }
							data-automation-id={ button.automationId }
							role="button"
							tabIndex={ 0 }
							onClick={ button.action }
							onKeyDown={ ( e ) => e.keyCode === 13 ? button.action() : null }
						>
							{ button.label }
						</span>,
					) }
				</div>
			</>
		);
	}

	/**
	 * Display network, API or batch errors
	 *
	 * @return {ReactElement} JSX markup
	 */
	displayErrorState() {
		const { batchResponse = {} } = this.state;
		const { error: batchError } = batchResponse;

		const changeFieldMappingButton = {
			label: pluginData.localization.shared.change_field_mapping, /* Change Field Mapping */
			action: () => this.changeFieldMapping( {
				recreateBatch: true,
			} ),
		};
		const tryAgainButton = {
			label: pluginData.localization.shared.try_again, // Try Again
			action: () => this.restartImportData(),
		};

		// If batch has an error set, allow only to modify fields (requires recreating the batch)
		const buttons = ( _.isEmpty( batchResponse ) || batchError ) ? [ changeFieldMappingButton ] : [ tryAgainButton, changeFieldMappingButton ];

		return (
			<>
				<p className={ styles( 'title', 'is-4' ) }>
					{ /* Import Failed */ }
					{ pluginData.localization.import_data.failed_to_import_data }
				</p>
				<p className={ styles( 'stats', 'is-size-5' ) }>
					{ this.getBatchFinalProgressReport() }
				</p>

				<div className={ styles( 'buttons', 'has-text-centered', 'block' ) }>
					{ buttons.map( ( button ) =>
						<span
							key={ `button-${ button.label }` }
							className={ styles( 'button', 'is-link' ) }
							role="button"
							tabIndex={ 0 }
							onClick={ button.action }
							onKeyDown={ ( e ) => e.keyCode === 13 ? button.action() : null }
						>
							{ button.label }
						</span>,
					) }
				</div>
			</>
		);
	}

	/**
	 * Download CSV with failed import records
	 *
	 * @return {Promise} Resolved promise
	 */
	downloadFailedRecordsCSV() {
		const { importData: { batchId } } = this.props.context;

		return new Promise( ( resolve ) => {
			API.getErrorReport( {
				csv: true,
				requestData: {
					id: batchId,
				},
				successHandler: ( res ) => {
					const element = document.createElement( 'a' );
					const csv = new Blob( [ res ], {
						type: 'text/csv;charset=utf8;',
					} );

					document.body.appendChild( element );

					element.setAttribute( 'download', `${ replacePlaceholders( pluginData.localization.import_data.error_report_filename, [ batchId ] ) }.csv` ); // import_error_report-%s.csv
					element.setAttribute( 'href', window.URL.createObjectURL( csv ) );
					element.style.display = '';
					element.click();

					document.body.removeChild( element );

					resolve();
				},
				errorHandler: ( err ) => {
					this.props.context.displayNotification( {
						type: 'error',
						message: err.message,
					} );

					resolve();
				},
			} );
		} );
	}

	/**
	 * Get error report from the API
	 *
	 * @return {Promise<Array>} Resolved promise with error report (array) or nothing
	 */
	getRowErrorReport() {
		const { errorReport } = this.state;
		const { importData: { batchId } } = this.props.context;

		return new Promise( ( resolve ) => {
			if ( errorReport && errorReport.length ) {
				return resolve( errorReport );
			}

			API.getErrorReport( {
				requestData: {
					id: batchId,
				},
				responseHandler: ( res ) => {
					// Response must be in [{data:[],error,number},{...}] format
					return _.get( res, '[0][error]' );
				},
				successHandler: ( res ) => {
					this.setState( { errorReport: res }, () => {
						resolve( res );
					} );
				},
				errorHandler: ( err ) => {
					this.props.context.displayNotification( {
						type: 'error',
						message: err.message,
					} );

					resolve();
				},
			} );
		} );
	}

	/**
	 * Display a modal window with batch error log
	 */
	displayErrorLogModalWindow() {
		this.getRowErrorReport().then( ( errorReport ) => {
			if ( ! errorReport || ! errorReport.length ) {
				return;
			}

			this.props.context.displayModalDialog( {
				content: (
					<div>
						<ul className={ styles( 'ul-disc' ) }>
							{ _.map( errorReport, ( row ) => (
								<li key={ `row-error-${ row.number }` }>
									<span className={ styles( 'has-text-danger', 'has-text-weight-semibold' ) }>
										{ `${ replacePlaceholders( pluginData.localization.import_data.row, [ row.number ] ) }` }
									</span>
									{ ` ${ row.error }` }
								</li>
							) ) }
						</ul>
					</div>
				),
				buttons: [
					{
						label: pluginData.localization.import_data.download_failed_records, // Download Failed Records
						style: styles( 'is-link' ),
						action: ( event ) => {
							// Persist synthetic event so that it could be accessed in the promise
							event.persist();

							event.target.classList.add( styles( 'is-loading' ) );

							this.downloadFailedRecordsCSV().finally( () => {
								event.target.classList.remove( styles( 'is-loading' ) );

								this.props.context.dismissModalDialog();
							} );
						},
					},
					{
						label: pluginData.localization.modal.close, // Close
						dismissModal: true,
						action: () => {
							this.props.context.dismissModalDialog();
						},
					},
				],
			} );
		} );
	}

	/**
	 * Return progress report string with processed/skipped/rejected stats
	 *
	 * @return {ReactElement} JSX markup
	 */
	getBatchFinalProgressReport() {
		const { batchResponse = {}, errorReport } = this.state;
		const { progress, meta, flags, status } = batchResponse;
		const { importData: { overwriteEntryData } } = this.props.context;
		const actionType = ( overwriteEntryData ) ? pluginData.localization.import_data.updated : pluginData.localization.import_data.imported; // updated|imported

		// "Continue processing" is turned off and processing failed
		if ( status === 'error' && ! _.includes( flags, 'soft' ) ) {
			const errorMessage = replacePlaceholders(
				// %s records: %s were %s before the import encountered an error
				pluginData.localization.import_data.processed_x_before_failed, [
					`<strong>${ i18nFormatNumber( meta.rows ) }</strong>`,
					`<strong>${ i18nFormatNumber( progress.processed ) }</strong>`,
					actionType,
				],
			);

			return ( errorReport ) ?
				<>
					{ errorMessage }:
					<br /><br />
					{ /* Row #%s */ }
					{ replacePlaceholders( pluginData.localization.import_data.row, [ i18nFormatNumber( errorReport[ 0 ].number ) ] ) }: <strong>{ errorReport[ 0 ].error }</strong>
				</> :
				<>{ errorMessage }.</>;
		}

		// No records were processed
		if ( _.isEmpty( batchResponse ) || ( ! progress.processed && ( progress.skipped !== meta.rows && progress.error !== meta.rows ) ) ) {
			return pluginData.localization.import_data.no_records_processed_error;
		}

		// All records were processed
		if ( meta.rows && progress.processed === meta.rows ) {
			return replacePlaceholders(
				// We have processed and imported|updated all %s records.
				pluginData.localization.import_data.processed_and_imported_x_records, [
					`<strong>${ i18nFormatNumber( progress.processed ) }</strong>`,
					actionType,
					`<strong>${ i18nFormatNumber( progress.processed ) }</strong>`,
				],
			);
		}

		// Some records were processed
		const rowsProcessed = progress.processed + progress.skipped + progress.error;
		const stats = [];
		const rowsProcessedMessage = replacePlaceholders(
			// We have processed %s records:
			pluginData.localization.import_data.processed_x_records, [
				`<strong>${ i18nFormatNumber( rowsProcessed ) }</strong>`,
			],
		);

		if ( progress.processed ) {
			// %s imported|updated
			stats.push( <><span className={ styles( 'imported' ) }>{ i18nFormatNumber( progress.processed ) }</span> { actionType }</> );
		}

		if ( progress.skipped ) {
			// %s skipped
			stats.push( <><span className={ styles( 'imported' ) }>{ i18nFormatNumber( progress.skipped ) }</span> { pluginData.localization.import_data.skipped /* skipped */ }</> );
		}

		if ( progress.error ) {
			// %s rejected due to an error ([link]view log[/link])
			const rejectedMessage = replacePlaceholderLinks( pluginData.localization.import_data.rejected,
				[
					{
						onClick: () => {
							this.displayErrorLogModalWindow();
						},
					},
				],
			);

			stats.push( <><span className={ styles( 'error' ) }>{ i18nFormatNumber( progress.error ) }</span> { rejectedMessage }</> );
		}

		return (
			<>{ rowsProcessedMessage } { joinJSXArray( stats, ', ', ` ${ pluginData.localization.import_data.and } ` ) }.</>
		);
	}

	/**
	 * Navigate to field mapping step
	 *
	 * @param {Object} routeChangeParameters Optional parameters that are interpreted by the field mapping step
	 */
	changeFieldMapping( routeChangeParameters ) {
		const { progress, routes } = this.props.context;

		progress.stepData = {
			...progress.stepData,
			step5ImportData: {},
		};

		progress.step = 'step3MapFields';

		this.props.context.setState( {
			progress,
			routeChangeParameters,
		}, () => this.props.history.push( routes.step3MapFields.path ) );
	}

	/**
	 * Render import status screen
	 *
	 * @return {ReactElement} JSX markup
	 */
	render() {
		const { initialized, status } = this.state;

		// Component is initialized when data is validated. See componentDidMount();
		if ( ! initialized ) {
			return null;
		}

		return (
			<section className={ styles( 'import-data', 'section' ) }>
				<div className={ styles( 'container' ) }>
					<div className={ styles( 'hero', 'is-medium' ) }>
						<div className={ styles( 'hero-body' ) }>
							<div className={ styles( 'container', 'has-text-centered' ) }>
								{ status === 'preparing' && this.displayStartingState() }

								{ status === 'importing' && this.displayInProgressState() }

								{ status === 'done' && this.displayFinishedState() }

								{ status === 'error' && this.displayErrorState() }
							</div>
						</div>
					</div>
				</div>
			</section>
		);
	}
}
