/* global GV_IMPORT_ENTRIES, wp, jQuery, Beacon */
/**
 * Custom JS for Gravity Forms Import Entries plugin
 *
 * @package   GravityView Maps
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2019, Katz Web Services, Inc.
 *
 * @since 2.0
 */

import { HashRouter, Prompt, Redirect, Route, Switch } from 'react-router-dom';
import _ from 'lodash';
import { createHashHistory } from 'history';
import classNames from 'classnames/bind';
import { replacePlaceholders, unescapeString } from 'js/helpers/string-manipulations';
import dayjs from 'dayjs';

import styles from 'css/app';

import ProgressBar from 'js/components/progress-bar';
import Notification from 'js/shared/notification';
import Step1SelectSource from 'js/components/step1/select-source';
import Step1Upload from 'js/components/step1/upload';
import Step2SelectForm from 'js/components/step2/select-form';
import Step3MapFields from 'js/components/step3/map-fields';
import Step4Configure from 'js/components/step4/configure';
import Step5ImportData from 'js/components/step5/import-data';
import ModalDialog from 'js/shared/modal-dialog';
import ErrorNotice from 'js/shared/error-notice';
import StackTrace from 'stacktrace-js';

const { render, Component, createContext } = wp.element;
/**
 * CSS uses through the application
 */
export const appStyles = classNames.bind( styles );

/**
 * Global plugin data returned by the server; see GV\Import_Entries::enqueue_scripts()
 */
export const pluginData = GV_IMPORT_ENTRIES || {};

/**
 * Create a standalone history object as the wrapper App component does not have access to Router's history
 */
export const history = createHashHistory();

/**
 * Create context to manage state across components
 */
const AppContext = createContext();

/**
 * Main app container that holds state and renders children components
 */
class App extends Component {
	defaultRoute = 'step1Upload';
	routes = {
		step1SelectSource: {
			step: 1,
			path: `/step1/select-source`,
			component: Step1SelectSource,
			inactive: true,
		},
		step1Upload: {
			step: 1,
			path: `/step1/select-source/upload`,
			beaconSuggestions: pluginData.beacon.suggestions.upload_csv,
			component: Step1Upload,
		},
		step1Google: {
			step: 1,
			path: `/step1/select-source/google`,
			inactive: true,
		},
		step1Ftp: {
			step: 1,
			path: `/step1/select-source/ftp`,
			inactive: true,
		},
		step2SelectForm: {
			step: 2,
			path: `/step2/select-form`,
			beaconSuggestions: pluginData.beacon.suggestions.select_form,
			component: Step2SelectForm,
		},
		step3MapFields: {
			step: 3,
			path: `/step3/map-fields`,
			beaconSuggestions: pluginData.beacon.suggestions.map_fields,
			component: Step3MapFields,
		},
		step4Configure: {
			step: 4,
			path: `/step4/configure`,
			beaconSuggestions: pluginData.beacon.suggestions.configure_options,
			component: Step4Configure,
		},
		step5ImportData: {
			step: 5,
			path: `/step5/import-data`,
			beaconSuggestions: pluginData.beacon.suggestions.import_data,
			component: Step5ImportData,
		},
	};

	localStorageStateKey = 'gv_import_entries';

	state = {
		appError: false,
		routeChangeParameters: {},
		targetForm: null,
		forms: pluginData.forms,
		defaultRoute: this.defaultRoute,
		routes: this.routes,
		progress: {
			step: this.routes[ this.defaultRoute ].step,
			stepRouteId: this.defaultRoute,
			stepData: {},
		},
		importData: {
			form: null,
			file: null,
			batchId: null,
			options: null,
			schema: null,
			conditions: null,
		},

		/**
		 * Update app state that's accessed via context by componetns
		 *
		 * @param {Object} state
		 * @param {null|callback} callback Callback that's executed after state is updated
		 *
		 */
		setState: ( state, callback ) => {
			this.setState( state, _.isFunction( callback ) ? callback : null );
		},

		/**
		 * Display global modal dialog window
		 *
		 * @param {Object|string} content String or React component
		 * @param {Object} buttons Action buttons
		 */
		displayModalDialog: ( { automationId, component, content, buttons } ) => {
			this.setState( {
				modalDialog: {
					automationId,
					component,
					content,
					buttons,
				},
			} );
		},

		/**
		 * Dismiss global modal dialog window
		 *
		 * @param {null|callback} callback Callback that's executed after modal is dismissed
		 */
		dismissModalDialog: ( callback ) => {
			this.setState( { modalDialog: null }, _.isFunction( callback ) ? callback : null );
		},

		/**
		 * Display global notification
		 *
		 * @param {Object} obj
		 * @param {string} obj.type Notification type (error, success, info)
		 * @param {string} obj.message Notification message
		 * @param {null|callback} callback Callback that's executed after notification is displayed
		 */
		displayNotification: ( { type, message }, callback ) => {
			this.setState(
				{
					notification: {
						type,
						message,
						onDismiss: () => this.state.dismissNotification(),
					},
				}, _.isFunction( callback ) ? callback : null,
			);
		},

		/**
		 * Dismiss global notification
		 *
		 * @param {null|callback} callback Callback that's executed after notification is dismissed
		 */
		dismissNotification: ( callback ) => {
			this.setState( { notification: null }, _.isFunction( callback ) ? callback : null );
		},

		/**
		 * Update app state with step data and then save state to local storage
		 *
		 *
		 * @param {Object} stepData Component state
		 * @param {string} stepRouteId Component route id
		 */
		saveCurrentProgress: ( stepData, stepRouteId ) => {
			const { progress } = this.state;
			progress.stepData[ stepRouteId ] = stepData;
			progress.stepRouteId = stepRouteId;

			this.setState( { progress }, this.state.saveStateToLocalStorage );
		},

		/**
		 * Save app state to local storage
		 */
		saveStateToLocalStorage: () => {
			const { progress, importData } = this.state;

			localStorage.setItem(
				this.localStorageStateKey,
				JSON.stringify( {
					progress,
					importData,
					date: dayjs().valueOf(),
				} ),
			);
		},

		/**
		 * Update app state from local storage object
		 *
		 * @param {Object} state Local storage state
		 * @param {null|callback} callback Callback that's executed after state is updated
		 */
		hydrateStateFromLocalStorage: ( state, callback ) => {
			const { progress, importData } = state;

			this.setState( { progress, importData }, _.isFunction( callback ) ? callback : null );
		},

		/**
		 * Get previously saved state from local storage object
		 *
		 * @return {Object} Local storage object
		 */
		getStateFromLocalStorage: () => {
			return JSON.parse( localStorage.getItem( this.localStorageStateKey ) );
		},

		/**
		 * Clear step progress data from application state
		 */
		clearCurrentProgress: () => {
			const { progress } = this.state;

			progress.stepData = {};
			progress.stepRouteId = null;

			this.setState( { progress }, this.state.clearLocalStorageState );
		},

		/**
		 * Delete local storage object
		 */
		clearLocalStorageState: () => {
			localStorage.removeItem( this.localStorageStateKey );
		},
	};

	/**
	 * Update state so the next render will show error message
	 *
	 * @return {Object} Empty error object
	 */
	static getDerivedStateFromError() {
		return { appError: {} };
	}

	/**
	 * Update state with error message/stack
	 *
	 * @param {Object} error JS error object
	 */
	componentDidCatch( error ) {
		StackTrace.fromError( error ).then( ( results ) => {
			const stack = _.map( results, ( result ) => `${ result.fileName.replace( 'webpack:///', '' ) }:${ result.lineNumber }:${ result.columnNumber } @ ${ result.functionName }` );
			this.setState( { appError: { message: error.message, stack } } );
		} );
	}

	/**
	 * Run before rendering component: check if target form is defined when IE is first run
	 *
	 * @param {Object} props
	 * @param {Object} state
	 *
	 * @return {null|Object} Null or updated state with target form that's used during form selection
	 */
	static getDerivedStateFromProps( props, state ) {
		const targetForm = /targetForm=(\d+)/.exec( window.location.hash );

		if ( targetForm ) {
			const targetFormObject = _.find( state.forms, { id: parseInt( targetForm[ 1 ], 10 ) } );

			return targetFormObject && _.assign( {}, state, { targetForm: targetFormObject } );
		}

		return null;
	}

	/**
	 * Run on component mount: check if import is being resumed
	 */
	componentDidMount() {
		// Initialize current URL on load (handleRouteChange is otherwise only called during navigation)
		this.handleRouteChange( { pathname: window.location.href.split( '#' )[ 1 ] } );

		// Resume interrupted import
		this.maybeResumeImport();
	}

	/**
	 * Detect and resume interrupted import
	 *
	 * @return {void}
	 */
	maybeResumeImport() {
		const localStorageState = this.state.getStateFromLocalStorage();
		const excludeStepsFromResume = [ 'step1SelectSource', 'step1Upload', 'step1Google', 'step1Ftp', 'step2SelectForm' ];

		if ( ! localStorageState || _.includes( excludeStepsFromResume, _.get( localStorageState, 'progress.stepRouteId' ) ) ) {
			return;
		}

		const { date, importData } = localStorageState;
		const { last_batch: lastBatch = {} } = pluginData;

		const batchData = {
			formattedDate: dayjs( date ).format( 'MMMM D, YYYY' ),
			hoursSinceLastImport: dayjs().diff( dayjs( date ), 'hour' ),
			filename: _.get( localStorageState, 'importData.file.name' ),
		};

		// Get localized batch last update date
		if ( lastBatch && lastBatch.id === importData.batchId ) {
			batchData.date = lastBatch.date;
			batchData.hoursSinceLastImport = dayjs().diff( dayjs.unix( lastBatch.timestamp ), 'hour' );
		}

		// Do not resume if more than 2 hours elapsed since last attempt
		if ( ! batchData.filename || batchData.hoursSinceLastImport > 2 ) {
			return this.state.clearLocalStorageState();
		}

		// Offer to resume previously interrupted import task
		this.state.displayModalDialog( {
			automationId: 'resume_upload_modal',
			content: replacePlaceholders(
				pluginData.localization.app.previous_import_detected, // It appears that you never finished importing %s that you started on %s. Do you want to resume import or start anew?,
				[
					`<strong>${ batchData.filename }</strong>`,
					`<strong>${ batchData.formattedDate }</strong>`,
				],
			),
			buttons: [
				{
					automationId: 'resume_upload',
					label: pluginData.localization.app.resume, // Resume
					style: appStyles( 'is-link' ),
					action: () => this.state.hydrateStateFromLocalStorage( localStorageState, () => {
						// Update state, dismiss modal window and redirect to the last saved step
						this.state.dismissModalDialog( () => history.push( this.state.routes[ localStorageState.progress.stepRouteId ].path ) );
					} ),
				},
				{
					label: pluginData.localization.app.start_new, // Start New Import
					action: () => this.state.dismissModalDialog( this.state.clearLocalStorageState ),
					dismissModal: true,
				},
			],
		} );
	}

	/**
	 * Perform actions during route change
	 *
	 * @param {Object} location Location object with information about the current URL
	 *
	 * @return {boolean} Return true to continue changing route
	 */
	handleRouteChange = ( location ) => {
		const stepData = _.find( this.state.routes, { path: location.pathname } ) || {};

		const updatedState = {};

		// Update progress step
		if ( stepData.step ) {
			updatedState.progress = this.state.progress;
			updatedState.progress.step = stepData.step;
		}

		// Re-initialize HS Beacon suggestions
		if ( stepData.beaconSuggestions && Beacon ) {
			Beacon( 'suggest', stepData.beaconSuggestions );
		}

		// Clear any notifications
		if ( this.state.notification ) {
			updatedState.notification = null;
		}

		if ( ! _.isEmpty( updatedState ) ) {
			this.setState( updatedState );
		}

		return true;
	};

	/**
	 * Render app
	 *
	 * @return {ReactElement} JSX markup
	 */
	render() {
		const { appError } = this.state;

		if ( appError ) {
			const props = {
				error: pluginData.localization.application_error.error_occured, // An Error Has Occured
				description:
					<>
						<p className={ appStyles( 'is-size-5' ) }>
							{ pluginData.localization.application_error.cannot_continue /* The importer encountered an error that prevents it from continuing: */ }
						</p>
						<br />
						<article className={ appStyles( 'message', 'is-marginless', 'is-danger' ) }>
							<div className={ appStyles( 'message-body', 'is-family-monospace' ) }>
								{ appError.stack ?
									<>
										<strong>{ appError.message }</strong>
										<div className={ appStyles( 'has-text-left' ) }>
											<br />
											{ _.map( appError.stack, ( line, index ) => <>{ ++index }. { line }<br /></> ) }
										</div>
									</> :
									<>
										{ unescapeString( pluginData.localization.application_error.processing_log ) /* Processing error log&hellip; */ } <span className={ appStyles( 'spinner', 'spinner-custom' ) } />
									</>
								}
							</div>
						</article>
						<br />
						<p className={ appStyles( 'is-size-5' ) }>
							{ unescapeString( pluginData.localization.application_error.help_troubleshoot ) /* We&rsquo;re here to help! Share this error with support by clicking the "%s" button. */ }
						</p>
					</>,
				buttons: [
					{
						label: pluginData.localization.application_error.contact_support, // Contact Support
						action: () => {
							window.Beacon( 'session-data', {
								...pluginData.beacon.session_data,
								Error: JSON.stringify( appError ),
							} );
							window.Beacon( 'prefill', {
								subject: `Import Entries ${ pluginData.beacon.session_data[ 'Plugin Version' ] } - application error`,
								text: `Please note that an error has occured:\n\n${ appError.message }`,
							} );
							window.Beacon( 'open' );
							window.Beacon( 'navigate', '/ask/message/' );
						},
					},
				],
			};
			return <ErrorNotice { ...props } />;
		}

		return (
			<AppContext.Provider value={ this.state }>
				<HashRouter>
					<>
						{ withContext( ProgressBar )() }

						{ /* Display global notification */ }
						{ this.state.notification && <Notification { ...this.state.notification } /> }

						<Switch>
							{ _.map( this.state.routes, ( route ) => ! route.inactive && <Route exact key={ route.path } path={ route.path } render={ withContext( route.component ) } /> ) }
							<Redirect from="*" to={ this.state.routes[ this.defaultRoute ].path } />
						</Switch>

						<Prompt message={ this.handleRouteChange } />

						{ /* Display global modal dialog */ }
						{ this.state.modalDialog && ( this.state.modalDialog.component || <ModalDialog { ...this.state.modalDialog } /> ) }
					</>
				</HashRouter>
			</AppContext.Provider>
		);
	}
}

/**
 * Higher order component to pass context and props to child component
 *
 * @param {ReactElement} ChildComponent React component
 *
 * @return {ReactElement} Wrapped React component
 */
const withContext = ( ChildComponent ) => {
	return ( props ) =>
		<AppContext.Consumer>
			{ ( context ) => {
				return <ChildComponent { ...props } context={ context } />;
			} }
		</AppContext.Consumer>;
};

/**
 * Render app
 */
const appContainer = document.getElementById( 'gv-import-entries' );

if ( appContainer && ! _.isEmpty( pluginData ) ) {
	render( <App />, appContainer );
}

/**
 * Force scrollbars to be always visible in macOS.
 */
( function() {
	window.addEventListener( 'load', function() {
		const scrollableElem = document.createElement( 'div' );
		const innerElem = document.createElement( 'div' );

		scrollableElem.style.width = '30px';
		scrollableElem.style.height = '30px';
		scrollableElem.style.overflow = 'scroll';
		scrollableElem.style.borderWidth = '0';

		innerElem.style.width = '30px';
		innerElem.style.height = '60px';

		scrollableElem.appendChild( innerElem );

		document.body.appendChild( scrollableElem );

		const diff = scrollableElem.offsetWidth - scrollableElem.clientWidth;

		document.body.removeChild( scrollableElem );

		if ( ! diff ) {
			document.body.classList.add( appStyles( 'macos-force-show-scrollbars' ) );
		}
	} );
}( jQuery ) );
