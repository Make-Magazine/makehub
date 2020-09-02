/* global wp */

import reactStringReplace from 'react-string-replace';
import escapeStringRegexp from 'escape-string-regexp';
import _ from 'lodash';
import { pluginData } from 'js/app';
import classNames from 'classnames/bind';

import appStyles from 'css/app';
import Beacon from 'js/shared/helpscout-beacon';

const styles = classNames.bind( appStyles );
const { Component } = wp.element;

/**
 * Display form selection
 */
export default class SelectForm extends Component {
	stepRouteId = 'step2SelectForm';

	state = {
		initialized: false,
		form: {
			source: '',
			title: '',
			filter: '',
			isValidTitle: true,
		},
	};

	/**
	 * Run on component mount
	 *
	 * @return {void}
	 */
	componentDidMount() {
		const { progress: { stepData }, routes, defaultRoute, targetForm } = this.props.context;

		// Navigate to source selection if required data is not set
		if ( _.isEmpty( stepData.step1Upload ) ) {
			return this.props.history.push( routes[ defaultRoute ].path );
		}

		// If target form is already defined, move on to next step
		if ( targetForm ) {
			return this.goToNextStep( targetForm );
		}

		this.setState( { initialized: true } );
	}

	/**
	 * Handle form source (existing|new) change
	 *
	 * @param {string|null} source
	 */
	handleFormSourceChange( source ) {
		const { form } = this.state;

		form.source = source;

		this.setState( { form } );
	}

	/**
	 * Handle form filter
	 *
	 * @param {string} filter
	 */
	handleFormFilter( filter ) {
		const { form } = this.state;

		form.filter = filter;

		this.setState( { form } );
	}

	/**
	 * Handle new form title change
	 *
	 * @param {string} title Form title
	 */
	handleFormTitleChange( title ) {
		const { form } = this.state;

		form.title = title;
		form.isValidTitle = ! _.reject( pluginData.forms, ( existingForm ) => String( existingForm.title ).toLowerCase().trim() !== title.toLowerCase().trim() ).length;

		this.setState( { form } );
	}

	/**
	 * Add new form
	 */
	addForm() {
		const { form: { title, isValidTitle } } = this.state;

		if ( ! isValidTitle || ! title.trim().length > 0 ) {
			return;
		}

		this.goToNextStep(
			{
				title: title,
				type: 'new',
			},
		);
	}

	/**
	 * Proceed to field mapping step
	 *
	 * @param {Object} selectedForm
	 */
	goToNextStep( selectedForm ) {
		const { form } = this.state;
		const { importData, progress } = this.props.context;
		const { stepData: { step2SelectForm, step3MapFields } } = progress;

		// If form was previously selected and fields mapped and now a new form was chosen, unset step data for field mapping/etc.
		if ( step2SelectForm && step3MapFields && selectedForm.id !== step2SelectForm.form.id ) {
			progress.stepData = {
				...progress.stepData,
				step3MapFields: {},
				step4Configure: {},
				step5ImportData: {},
			};
		}

		importData.form = selectedForm;

		this.setState( { form: { ...form, ...selectedForm } }, () => {
			this.props.context.saveCurrentProgress( this.state, this.stepRouteId );

			this.props.context.setState( { importData, progress, targetForm: null }, () => this.props.history.push( this.props.context.routes.step3MapFields.path ) );
		} );
	}

	/**
	 * Display the name of uploaded CSV file
	 *
	 * @return {ReactElement} JSX markup
	 */
	displaySelectedImportSourceArea() {
		return (
			<section className={ styles( 'select-form', 'section' ) }>
				<div className={ styles( 'container' ) }>
					<div className={ styles( 'columns' ) }>
						<div className={ styles( 'column' ) }>
							<p className={ styles( 'title', 'is-4' ) }>
								{ pluginData.localization.select_form.import_source /* Import source */ }
							</p>
						</div>
					</div>

					<div className={ styles( 'columns' ) }>
						<div className={ styles( 'column' ) }>
							<div className={ styles( 'box' ) }>
								<div className={ styles( 'level' ) }>
									<div className={ styles( 'level-left' ) }>
										<div className={ styles( 'level-item' ) }>
											<p className={ styles( 'is-size-5' ) }>
												{ this.props.context.importData.file.name }
											</p>
										</div>
									</div>

									<div className={ styles( 'level-right' ) }>
										<div className={ styles( 'level-item' ) }>
											<figure className={ styles( 'import-source', 'image', 'is-24x24' ) } />
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		);
	}

	/**
	 * Display form source selection (i.e., existing or new form)
	 *
	 * @return {ReactElement} JSX markup
	 */
	displayFormSourceSelectionArea() {
		return (
			<section data-automation-id="form-source-selection-container" className={ styles( 'section', 'form-source-selection-container' ) }>
				<div className={ styles( 'container' ) }>
					<div className={ styles( 'columns' ) }>
						<div className={ styles( 'column' ) }>
							<p className={ styles( 'title', 'is-4' ) }>
								{ pluginData.localization.select_form.where_to_import /* Where would you like to import the entries? */ }
								<Beacon articleId="5d37c9d20428634786755686" />
							</p>
						</div>
					</div>

					<div className={ styles( 'columns' ) }>
						{ pluginData.forms.length ?
							/* Import into existing form (when forms exist) */
							<>
								<div className={ styles( 'column', 'is-3' ) }>
									<div
										className={ styles( 'box', 'is-flex', 'is-link' ) }
										data-automation-id="existing_form_selection"
										role="button"
										tabIndex={ 0 }
										onClick={ () => this.handleFormSourceChange( 'existing' ) }
										onKeyDown={ ( e ) => e.keyCode === 13 ? this.handleFormSourceChange( 'existing' ) : null }
									>
										<figure className={ styles( 'form-source--existing', 'image', 'is-64x64' ) } />
										<p className={ styles( 'form-source-label', 'is-size-5' ) }>
											{ pluginData.localization.select_form.existing_form /* Existing Form */ }
										</p>
									</div>
								</div>
							</> : null
						}

						<div className={ styles( 'column', 'is-3' ) }>
							<div
								className={ styles( 'box', 'is-flex' ) }
								data-automation-id="new_form_selection"
								role="button"
								tabIndex={ 0 }
								onClick={ () => this.handleFormSourceChange( 'new' ) }
								onKeyDown={ ( e ) => e.keyCode === 13 ? this.handleFormSourceChange( 'new' ) : null }
							>
								<figure className={ styles( 'form-source--new', 'image', 'is-64x64' ) } />
								<p className={ styles( 'form-source-label', 'is-size-5' ) }>
									{ pluginData.localization.select_form.new_form /* New Form */ }
								</p>
							</div>
						</div>
					</div>
				</div>
			</section>
		);
	}

	/**
	 * Display selected form source (i.e., existing or new form)
	 *
	 * @return {ReactElement} JSX markup
	 */
	displaySelectedFormSourceArea() {
		return (
			<section className={ styles( 'section' ) }>
				<div className={ styles( 'container' ) }>
					<div className={ styles( 'columns' ) }>
						<div className={ styles( 'column' ) }>
							<p className={ styles( 'title', 'is-4' ) }>
								{ pluginData.localization.select_form.where_to_import /* Where would you like to import the entries? */ }
								<Beacon articleId="5d37c9d20428634786755686" />
							</p>
						</div>
					</div>

					<div className={ styles( 'columns' ) }>
						<div className={ styles( 'column', 'is-4' ) }>
							<div className={ styles( 'box' ) }>
								<div className={ styles( 'level' ) }>
									<div className={ styles( 'level-left' ) }>
										<div className={ styles( 'level-item' ) }>
											<p className={ styles( 'is-size-5' ) }>
												{ this.state.form.source === 'new' ? pluginData.localization.select_form.new_form : pluginData.localization.select_form.existing_form /* An Existing Form | New Form */ }
											</p>
										</div>
									</div>

									<div className={ styles( 'level-right' ) }>
										<div className={ styles( 'level-item' ) }>
											<button
												className={ styles( 'is-size-6', 'button-link' ) }
												tabIndex={ 0 }
												onClick={ () => this.handleFormSourceChange( null ) }
												onKeyDown={ ( e ) => e.keyCode === 13 ? this.handleFormSourceChange( null ) : null }
											>
												{ pluginData.localization.select_form.change_form /* Change */ }
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		);
	}

	/**
	 * Display new form input
	 *
	 * @return {ReactElement} JSX markup
	 */
	displayAddFormArea() {
		const { form: { title, isValidTitle } } = this.state;

		return (
			<section className={ styles( 'section', 'form-add-container' ) }>
				<div className={ styles( 'container' ) }>
					<div className={ styles( 'columns' ) }>
						<div className={ styles( 'column' ) }>
							<p className={ styles( 'title', 'is-4' ) }>
								{ pluginData.localization.select_form.add_new_form /* Add a new form */ }
							</p>
						</div>
					</div>

					<div className={ styles( 'box' ) }>
						<div className={ styles( 'columns' ) }>
							<div className={ styles( 'column', 'is-4' ) }>
								<input
									autoFocus={ true }
									type="text"
									className={ styles( 'input', 'is-size-5', { 'is-danger': ! isValidTitle } ) }
									data-automation-id="new_form_name"
									value={ this.state.form.title }
									onChange={ ( e ) => this.handleFormTitleChange( e.target.value ) }
									onKeyDown={ ( e ) => e.keyCode === 13 && isValidTitle ? this.addForm() : null }
									placeholder={ pluginData.localization.select_form.add_new_form_placeholder /* New form name */ }
								/>
							</div>

							<div className={ styles( 'column', 'is-4' ) }>
								<button
									className={ styles( 'button', 'is-link', 'is-primary', 'is-medium' ) }
									data-automation-id="continue_with_import"
									tabIndex={ 0 }
									disabled={ title.trim().length > 0 && isValidTitle ? null : 'disabled' }
									onClick={ () => this.addForm() }
									onKeyDown={ ( e ) => e.keyCode === 13 ? this.addForm() : null }
								>
									{ pluginData.localization.shared.continue_with_import /* Continue With Import */ }
								</button>
							</div>
						</div>
						<p className={ styles( 'help', 'is-size-5', 'is-danger', { 'is-hidden': isValidTitle } ) }>
							{ pluginData.localization.select_form.title_not_unique /*  A form with the same title already exists. Please select a unique title. */ }
						</p>
					</div>
				</div>
			</section>
		);
	}

	/**
	 * Display list of forms and allow filtering and selection
	 *
	 * @return {ReactElement} JSX markup
	 */
	displayFormListArea() {
		// Return an array of all forms or those that match filter
		const forms = _.chain( pluginData.forms )
			.map( ( form ) => {
				const filter = this.state.form.filter.trim();
				const isFiltered = filter.length;
				let title = form.title;
				let titleMatchesFilter = false;
				let idMatchesFilter = false;

				if ( isFiltered ) {
					const formTitleFilterRegex = new RegExp( '(' + escapeStringRegexp( filter ) + ')', 'i' );
					titleMatchesFilter = title && title.match( formTitleFilterRegex );
					idMatchesFilter = form.id === parseInt( filter.replace( /[^0-9]+/g, '' ), 10 );
					// Highlight the matched part
					title = reactStringReplace( title, formTitleFilterRegex, ( match, i ) =>
						<span key={ `form-${ form.id }-match-${ i }` } className={ styles( 'search-highlight' ) }>
							{ match }
						</span> );
				}

				return ( ! isFiltered || titleMatchesFilter || idMatchesFilter ) &&
					<div
						key={ `form-${ form.id }` }
						data-automation-id={ `form-${ form.id }` }
						className={ styles( 'columns' ) }
						tabIndex={ 0 }
						role="button"
						onClick={ () => this.goToNextStep( form ) }
						onKeyDown={ ( e ) => e.keyCode === 13 ? this.goToNextStep( form ) : null }
						title={ `#${ form.id } ${ form.title }` }
					>
						<div className={ styles( 'column' ) }>
							<div className={ styles( 'level-left', 'result' ) }>
								<div className={ styles( 'level-item', 'is-hidden-mobile' ) }>
									<p className={ styles( 'is-size-6' ) }>
										{ idMatchesFilter ?
											<span className={ styles( 'search-highlight' ) }>
												#{ form.id }
											</span> :
											`#${ form.id }`
										}
									</p>
								</div>
								<div className={ styles( 'level-item' ) }>
									<p className={ styles( 'is-size-5' ) }>
										{ title }
									</p>
								</div>
							</div>
						</div>
					</div>;
			} )
			.compact()
			.value();

		return (
			<section className={ styles( 'section', 'form-search-container' ) }>
				<div className={ styles( 'container' ) }>
					<div className={ styles( 'columns' ) }>
						<div className={ styles( 'column' ) }>
							<p className={ styles( 'title', 'is-4' ) }>
								{ /* Choose a form for this import */ }
								{ pluginData.localization.select_form.choose_form }
								<Beacon
									beaconType="inline"
									articleId="5d373da82c7d3a2ec4bf4d08"
									href="https://docs.gravityview.co/article/607-c"
								/>
							</p>
						</div>
					</div>

					<div className={ styles( 'box' ) }>
						{ /* Search form */ }
						<div className={ styles( 'columns' ) }>
							<div className={ styles( 'column', 'search-area' ) }>
								<input
									autoFocus={ true }
									type="text"
									className={ styles( 'input', 'is-size-5' ) }
									data-automation-id="form_search_bar"
									onChange={ ( e ) => this.handleFormFilter( e.target.value ) }
									placeholder={ pluginData.localization.select_form.search_forms_placeholder } // Type a form name to search
								/>
								<div className={ styles( 'columns', 'results-wrapper' ) }>
									<div className={ styles( 'column' ) }>
										{ /* Display list of forms or a notice if none are found */ }
										{ forms.length ?
											forms :
											<p className={ styles( 'is-size-5' ) }>
												{ /* Form matching search criteria is not found */ }
												{ pluginData.localization.select_form.form_not_found }
											</p>
										}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		);
	}

	/**
	 * Render source selection area
	 *
	 * @return {ReactElement} JSX markup
	 */
	render() {
		const { initialized, form: { source } } = this.state;

		// Component is initialized when data is validated. See componentDidMount();
		return initialized && (
			<div className={ styles( 'select-form' ) }>
				{ this.displaySelectedImportSourceArea() }

				{ source ?
					<>
						{ this.displaySelectedFormSourceArea() }

						{ source === 'existing' ? this.displayFormListArea() : this.displayAddFormArea() }
					</> :
					this.displayFormSourceSelectionArea()
				}
			</div>
		);
	}
}
