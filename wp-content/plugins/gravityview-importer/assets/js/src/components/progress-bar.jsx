/* global wp */

import { history, pluginData } from 'js/app';
import classNames from 'classnames/bind';

import appStyles from 'css/app';

const styles = classNames.bind( appStyles );
const { Component } = wp.element;

/**
 * Display progress bar
 *
 * @return {ReactElement} JSX markup
 */
export default class ProgressBar extends Component {
	stepsLabels = [
		pluginData.localization.progress.select_source,
		pluginData.localization.progress.select_form,
		pluginData.localization.progress.map_fields,
		pluginData.localization.progress.configure,
		pluginData.localization.progress.import,
	];

	/**
	 * Clear current progress and redirect to form selection step
	 */
	handleFormChange() {
		history.push( this.props.context.routes.step2SelectForm.path );
	}

	/**
	 * Clear current progress and redirect to source selection step
	 */
	handleSourceChange() {
		history.push( this.props.context.routes[ this.props.context.defaultRoute ].path );
	}

	/**
	 * Redirect to field mapping step
	 */
	handleFieldMappingChange() {
		history.push( this.props.context.routes.step3MapFields.path );
	}

	/**
	 * Render progress indicator
	 *
	 * @return {ReactElement} JSX markup
	 */
	render() {
		return (
			<section className={ styles( 'progress-bar', 'section' ) }>
				<ul className={ styles( 'steps', 'step-bar', 'is-small', 'is-centered', 'has-gaps', 'has-content-centered' ) }>
					{ this.stepsLabels.map( ( label, index ) => (
						<li key={ `step-${ index }` } className={ styles( 'steps-segment', { 'is-active': this.props.context.progress.step === index + 1 } ) }>
							<span className={ styles( 'steps-marker' ) } />
							<div className={ styles( 'steps-content' ) }>
								<p className={ styles( 'is-size-4' ) }>
									{ label }
								</p>
								{ index === 0 && this.props.context.importData.file && ( this.props.context.progress.step > 1 && this.props.context.progress.step < this.stepsLabels.length ) &&
								<button
									className={ styles( 'button-link', 'is-size-6' ) }
									tabIndex={ 0 }
									onClick={ () => this.handleSourceChange() }
									onKeyDown={ ( e ) => e.keyCode === 13 ? this.handleSourceChange() : null }
								>
									{ /* Change Source */ }
									{ pluginData.localization.shared.change_source }
								</button>
								}
								{ index === 1 && this.props.context.importData.form && ( this.props.context.progress.step > 2 && this.props.context.progress.step < this.stepsLabels.length ) &&
								<button
									className={ styles( 'button-link', 'is-size-6' ) }
									tabIndex={ 0 }
									onClick={ () => this.handleFormChange() }
									onKeyDown={ ( e ) => e.keyCode === 13 ? this.handleFormChange() : null }
								>
									{ /* Change Form */ }
									{ pluginData.localization.progress.change_form }
								</button>
								}
								{ index === 2 && this.props.context.importData.form && ( this.props.context.progress.step > 3 && this.props.context.progress.step < this.stepsLabels.length ) &&
								<button
									className={ styles( 'button-link', 'is-size-6' ) }
									tabIndex={ 0 }
									onClick={ () => this.handleFieldMappingChange() }
									onKeyDown={ ( e ) => e.keyCode === 13 ? this.handleFieldMappingChange() : null }
								>
									{ /* Change Field Mapping */ }
									{ pluginData.localization.shared.change_field_mapping }
								</button>
								}
							</div>
						</li>
					) ) }
				</ul>
			</section>
		);
	}
}
